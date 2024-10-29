<?php
function rate_snack(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );

        $snack = new SnackModel($data['postId']);
        $user = new User($data['email']);
        $user_id = $user->getWordpressUserId();
        $result = $snack->addReview($user_id, $data['comment'], $data['rating'], $data['email']);

        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'rate-snack', array(
        'methods'  => 'POST',
        'callback' => 'rate_snack',
        'permission_callback' => '__return_true',
    ));
});


function user_can_rate(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );

        $user = new User($data['email']);
        $user_id = $user->getWordpressUserId();
        $result = SnackModel::userCanReview($user_id, $data['postId']);
        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'user-can-rate', array(
        'methods'  => 'POST',
        'callback' => 'user_can_rate',
        'permission_callback' => '__return_true',
    ));
});

function get_snack_info(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );

        switch( get_post_type($data['postId']) )
        {
            case 'snack':
                $snack = new SnackModel($data['postId']);
                $user = new User($data['email']);
                
                if($user->getHasSubscription())
                {
                    $price = $snack->getDiscount();
                }
                else
                {
                    $price = get_post_meta( $data['postId'], 'price', true );
                }

                $name = get_post_meta( $data['postId'], 'user-friendly-name', true );
                $post_slug = get_post_field( 'post_name', $data['postId'] );
                $thumbnail = $snack->getFeaturedImage();
                break;

            case 'country':
                $country = new CountryModel( $data['postId'] );
                $price = $country->getPrice($data['crate_size']);
                $name = get_the_title( $data['postId'] ) . ' ' . CountryModel::$pretty_names[$data['crate_size']];
                $post_slug = get_post_field( 'post_name', $data['postId'] );
                $thumbnail = $country->getFeaturedImage();
                break;

            case 'collection':
                $collection = new CollectionModel($data['postId']);
                $price = get_post_meta( $data['postId'], 'cost', true );
                $name = get_post_meta( $data['postId'], 'user-friendly-name', true );
                $post_slug = get_post_field( 'post_name', $data['postId'] );
                $thumbnail = $collection->getFeaturedImage();
                break;
        }
        
        
        
        $result = (object)array(
            'thumbnail' => $thumbnail,
            'price' => $price,
            'name' => $name,
            'post_slug' => $post_slug,
            'post_type' => get_post_type( $data['postId'] ),
        );

        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'get-snack-info', array(
        'methods'  => 'POST',
        'callback' => 'get_snack_info',
        'permission_callback' => '__return_true',
    ));
});


function get_cart_info(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );

        $serialized_items = Session::getInstance()->fetchUserSession($data['user_id']);
        
        $return = array();
        foreach(unserialize($serialized_items) as $post_id => $details)
        {
            switch( get_post_type($post_id) )
            {
                case "snack":
                    array_push( 
                        $return, 
                        (object)array(
                            "name" => get_post_meta( $post_id, 'user-friendly-name', true ),
                            "quantity" => $details
                        )
                    );
                    break;
                case "country":
                case "collection":
                    foreach($details as $crate_size => $quantity)
                    {
                        array_push($return, array(
                            "name" => get_post_meta( $post_id, 'user-friendly-name', true ) . " " . CountryModel::$pretty_names[$crate_size],
                            "quantity" => $quantity
                        ));
                    }
                    break;
            }
        }

        return $return;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'get-cart-info', array(
        'methods'  => 'POST',
        'callback' => 'get_cart_info',
        'permission_callback' => '__return_true',
    ));
});

function get_country_image(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );
        
        $page = get_page_by_title( $data['country'], OBJECT, 'country' );
        
        if( is_object($page) )
        {
            $country = new CountryModel($page->ID);

            return $country->getFeaturedImage();
        }
        else 
        {
            $page = get_page_by_title( $data['country'], OBJECT, 'collection' );

            if( is_object($page) )
            {
                $collection = new CollectionModel($page->ID);

                return $collection->getFeaturedImage();
            }
        }

        return get_stylesheet_directory_uri() . "/assets/default/MysteryCrateHero-min.png";
    }
    catch(Exception $e)
    {
        return get_stylesheet_directory_uri() . "/assets/default/MysteryCrateHero-min.png";
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'get-country-image', array(
        'methods'  => 'POST',
        'callback' => 'get_country_image',
        'permission_callback' => '__return_true',
    ));
});

function get_collection_info(WP_REST_Request $request)
{
    try
    {
        $return_object = new stdClass();

        $data = array_map( 'esc_attr', $request->get_params() );

        $posts = get_posts(array(
            'name' => $data['collection'],
            'posts_per_page' => 1,
            'post_type' => $data['type'],
            'post_status' => ['publish','draft']
        ));

        $page = current($posts);

        if( !is_object($page) )
        {
            $posts = get_posts(array(
                'name' => $data['collection'],
                'posts_per_page' => 1,
                'post_type' => 'country',
                'post_status' => ['publish','draft']
            ));

            $page = current($posts);

            $return_object->crate_type = '';

            if( !is_object($page) )
            {
                throw new Exception('Page not found.');
            }
        }
        else 
        {
            $return_object->crate_type = get_post_meta( $page->ID, 'crate-size', true );
        }

        $return_object->post_id = $page->ID;
        $return_object->preorder_date = get_post_meta( $page->ID, 'preorder-shipping-date', true );
        $return_object->sap_code = $data['type'] == 'snack' ? get_post_meta( $page->ID, 'internal-id-code', true ) : get_post_meta( $page->ID, 'country-code', true );
        $return_object->image = wp_get_attachment_url( get_post_meta( $page->ID, 'featured-image', true ) );
        $stock = get_post_meta( $page->ID, 'in-stock', true );

        $fulfillment_name = get_post_meta( $page->ID, 'fulfillment-name', true );
        $return_object->fulfillment_name = empty($fulfillment_name) ? $page->post_title : $fulfillment_name;

        $return_object->stock = $stock;
        return $return_object;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'get-collection-info', array(
        'methods'  => 'POST',
        'callback' => 'get_collection_info',
        'permission_callback' => '__return_true',
    ));
});

function add_to_cart_account(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );
        $page = get_page_by_title( $data['collection'], OBJECT, 'collection' );
        $crate_type = get_post_meta( $page->ID, 'crate-size', true );
        $cart = new Cart();
        $cart->addCrateToCart($page->ID, 1, $crate_type);

        return array(
            'link' => get_permalink( get_page_by_path('shopping-cart') ),
            'token' => $_SESSION['cart_token'],
        );
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'add-to-cart', array(
        'methods'  => 'POST',
        'callback' => 'add_to_cart_account',
        'permission_callback' => '__return_true',
    ));
});

function get_post_id(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );
        
        $post_types = array('snack', 'country', 'collection');

        $page = get_page_by_title( $data['title'], OBJECT, $post_types );

        if( is_object($page) )
        {
            return $page->ID;
        }
        else 
        {
            return 0;
        }
    }
    catch(Exception $e)
    {
        return 0;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'get-post-id', array(
        'methods'  => 'POST',
        'callback' => 'get_post_id',
        'permission_callback' => '__return_true',
    ));
});




function update_stock(WP_REST_Request $request)
{
    try
    {
        $data = array_map( 'esc_attr', $request->get_params() );

        $current_stock = get_post_meta($data['post_id'], 'in-stock', true);

        if( is_array($current_stock) ) 
            $current_stock[ $data['size'] ] = $current_stock[ $data['size'] ] - $data['quantity'];
        else
            $current_stock = $current_stock - $data['quantity'];

        update_post_meta($data['post_id'], 'in-stock', $current_stock);

        return true;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'update-stock', array(
        'methods'  => 'POST',
        'callback' => 'update_stock',
        'permission_callback' => '__return_true',
    ));
});


function check_preorder_status(WP_REST_Request $request)
{
    try
    {
        $data = $request->get_params();
        $result = General::checkPreorderStatus( $data['postIds'] );

        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}
add_action('rest_api_init', function () {
    register_rest_route( 'candybar-api/v1', 'check-preorder-status', array(
        'methods'  => 'POST',
        'callback' => 'check_preorder_status',
        'permission_callback' => '__return_true',
    ));
});

function addons_decrease_inventory(WP_REST_Request $request)
{
    try
    {    
        $data = array_map( 'esc_attr', $request->get_params() );

        $result = [];

        $item_ids = explode(',', $data['item_ids']);

        foreach($item_ids as $item_id)
        {
            $inStock = get_post_meta( $item_id, 'in-stock', true );
            update_post_meta( $item_id, 'in-stock', $inStock - 1, $inStock );

        }

        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'candybar-api/v1', 'addons-decrease-inventory', array(
        'methods'  => 'GET',
        'callback' => 'addons_decrease_inventory',
        'permission_callback' => '__return_true',
    ) );
});

function addons_increase_inventory(WP_REST_Request $request)
{
    try
    {    
        $data = array_map( 'esc_attr', $request->get_params() );

        $result = [];

        $item_ids = explode(',', $data['item_ids']);

        foreach($item_ids as $item_id)
        {
            $inStock = get_post_meta( $item_id, 'in-stock', true );
            update_post_meta( $item_id, 'in-stock', $inStock + 1, $inStock );

        }

        return $result;
    }
    catch(Exception $e)
    {
        return false;
    }
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'candybar-api/v1', 'addons-increase-inventory', array(
        'methods'  => 'GET',
        'callback' => 'addons_increase_inventory',
        'permission_callback' => '__return_true',
    ) );
});