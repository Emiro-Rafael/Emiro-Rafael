<?php

/**
 * The SCAjax class is intended for having a one-to-many
 * relationship when defining ajax requests via WP.
 *
 * Before:
 *         add_action('wp_ajax_send_email', array($this, 'sendEmail'));
 *         add_action('wp_ajax_nopriv_send_email', array($this, 'sendEmail'));
 *
 * After:
 *         $this->actions = [
 *             'send_email' => 'sendEmail',
 *             ...
 *         ]
 */
class SCAjax {

    protected $action;

    protected $actions;

    const SC_NONCE = "handleAjax_nonce";

    public function __construct($_actions = [])
    {
        $this->actions = $_actions;

        $this->setup_wpajax();
    }

    public function setup_wpajax($public = TRUE)
    {
        $className = $this->getClassName();

        foreach ($this->actions as $action_name => $method_name) {
            add_action("wp_ajax_{$action_name}", array($className, $method_name));
            if($public) {
                add_action("wp_ajax_nopriv_{$action_name}", array($className, $method_name));
            }
        }
    }

    public function getClassName()
    {
        return get_called_class();
    }

    public function getActionName()
    {
        return $this->action;
    }

    /**
     * Get the AJAX data that WordPress needs to output.
     *
     * @return array
     */
    protected function get_ajax_data()
    {
        return array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'action' => $this->getActionName(),
            '_ajax_nonce' => wp_create_nonce(self::SC_NONCE)
        );
    }

    protected function verify_request() {
        // Returns -1 if invalid
        check_ajax_referer('handleAjax_nonce', 'nonce');
    }

    protected function get_request_data($to_json = false) {
        $data = array_map( 'esc_attr', $_REQUEST );

        # @TODO Handle processing of data in here
        # @TODO Validate data with absint(), esc_*(), etc.
        return ($to_json) ? wp_send_json_success(json_encode($data)) : $data;
    }
}