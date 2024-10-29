<?php

require_once('SCAjax.php');

class UnboxingAjax extends SCAjax
{
    private static $instance = null;
    protected $action = 'unboxing';

    public function __construct()
    {
        parent::__construct(
            $this->actions = [
                'add-rating' => 'addRating',
                'answered-trivia' => 'answeredTrivia',
                'save_snack_superlative' => 'saveSnackSuperlative'
            ]
        );
    }

    public static function addRating()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $model = new SnackModel($data['snack_id']);
            $model->addRatingFromUnboxing( $data['rating'] );
            
            wp_send_json_success($user_id);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function answeredTrivia()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $model = new UnboxingModel($data['post_id']);
            $percentages = $model->addTriviaAnswer( $data );

            $send_data = array(
                "callback" => "triviaAnswered",
                "callbackArguments" => array(
                    array(
                        'percentages' => $percentages,
                        'question_key' => $data['question_key']
                    )
                )
            );
            
            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function saveSnackSuperlative()
    {
        try
        {
            $data = array_map( 'esc_attr', $_POST );

            $model = new UnboxingModel( $data['post_id'] );
            $superlative_data = $model->saveUserSnackSuperlative( $data );

            $send_data = array(
                "callback" => "superlativeSaved",
                "callbackArguments" => array(
                    array(
                        "type" => $data['type'],
                        "user_pick" => $superlative_data['user_pick'],
                        "highest_rated" => $superlative_data['highest_rated']
                    )
                )
            );

            wp_send_json_success($send_data);
        }
        catch(Exception $e)
        {
            wp_send_json_error($e->getMessage(), 500);
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new UnboxingAjax();
        }

        return self::$instance;
    }
}

UnboxingAjax::getInstance();