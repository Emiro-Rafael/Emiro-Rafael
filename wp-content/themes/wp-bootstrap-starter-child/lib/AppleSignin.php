<?php
require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/wp-load.php');

try
{
    if(array_key_exists('error', $_POST))
    {
        throw new Exception($_POST['error']);
    }
    $code = $_POST['authorization']['code'];
    $token = $_POST['authorization']['id_token'];
    $state = $_POST['authorization']['state'];

    if($state == $_SESSION['csrf_token'])
    {
        if(array_key_exists('user', $_POST) && array_key_exists('email', $_POST['user']))
        {
            $user = $_POST['user'];
            $options['email'] = $user['email'];
            $options['firstname'] = $user['name']['firstName'];
            $options['lastname'] = $user['name']['lastName'];
            $options['method'] = "AppleID";
            $options['signup_position'] = 1;

            /**
             * BEGIN
             */
            
            wp_send_json_success($options);
            wp_die();
            /**
             * END
             */
        }
        else
        {
            $url = 'https://appleid.apple.com/auth/token';
            // build the urlencoded data

            $fields = [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => get_stylesheet_directory_uri() . '/lib/AppleSignin.php',
                'client_id' => 'com.snackcrate.service',
                'client_secret' => $_ENV['apple_secret'],
                'scope' => 'name email'
            ];
            $postvars = http_build_query($fields);
        
            // open connection
            $ch = curl_init();
        
            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'User-Agent: curl', # Apple requires a user agent header at the token endpoint
              ]);        
            // execute post
            $result = curl_exec($ch);
            $res = json_decode($result);

            $claims = explode('.', $res->id_token)[1];
            $claims = json_decode(base64_decode($claims));

            if($claims->email_verified == true)
            {
                $options['firstname'] = $claims->name['firstName'];
                $options['lastname'] = $claims->name['lastName'];
                $options['email'] = $claims->email;
                $options['method'] = 'AppleID';
                
                wp_send_json_success($options);
                wp_die();
            }
            else
            {
                wp_send_json_error("Login failed.", 500);
                wp_die();
                throw new Exception('Login failed.');
            }
        }
    }
    else
    {
        wp_send_json_error("Invalid request.", 500);
        wp_die();
        throw new Exception('Invalid request.');
    }
}
catch(Exception $e)
{
    wp_send_json_error($e->getMessage(), 500);
    wp_die();
}