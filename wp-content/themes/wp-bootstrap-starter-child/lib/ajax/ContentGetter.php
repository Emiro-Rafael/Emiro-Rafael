<?php

require_once('SCAjax.php');
require_once('ContentLoader.php');

class ContentGetter extends SCAjax
{
    private static $instance = null;
    protected $action = 'loader';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'snacks_by_size' => 'snacksBySize'
            ]
        );
    }

    private static function _sendContent( $path, $params_exist = false )
    {
        try
        {
            $loader = new ContentLoader();
            $loader->setPartialPath( $path );
            if( $params_exist )
            {
                $data = array_map( 'esc_attr', $_REQUEST['params'] );
                $loader->setQueryParameters( $data );
            }
            $content = $loader->getContent();
            $send_data = array(
                "content" => $content,
            );
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
        wp_die();
    }

    public static function snacksBySize()
    {
        self::_sendContent( get_stylesheet_directory() . '/template-parts/ajax/snacks-by-size.php' );
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new ContentGetter();
        }

        return self::$instance;
    }
}

ContentGetter::getInstance();