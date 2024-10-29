<?php 
// make sure we can use wordpress functions in all of these files
require_once ABSPATH . "/wp-load.php";
require_once ABSPATH . "/../keys.php";

// vendor files
require_once get_stylesheet_directory() . '/vendor/stripe-php/init.php';
\Stripe\Stripe::setApiKey($_ENV['stripe_api_key']);

require_once get_stylesheet_directory() . '/vendor/easypost-php/lib/easypost.php';
\EasyPost\EasyPost::setApiKey($_ENV['easypost_api_key']);

require_once get_stylesheet_directory() . '/vendor/php-sapb1/sap_autoloader.php';

require_once get_stylesheet_directory() . '/vendor/php-barcode-generator/vendor/autoload.php';
require_once get_stylesheet_directory() . '/vendor/fpdf181/fpdf.php';
require_once get_stylesheet_directory() . '/vendor/FPDI/src/autoload.php';


// sap specific files
require_once get_stylesheet_directory() . '/lib/sap/sap-master.class.php';
require_once get_stylesheet_directory() . '/lib/sap/sap-item.class.php';
require_once get_stylesheet_directory() . '/lib/sap/sap-invoice.class.php';
require_once get_stylesheet_directory() . '/lib/sap/sap-country.class.php';

// file with common functionality related to custom post types and taxonomies
require_once get_stylesheet_directory() . '/lib/custom-fields/common.php';

// files which create custom post types
require_once get_stylesheet_directory() . '/lib/post-types/snack-custom-posttype.php';
require_once get_stylesheet_directory() . '/lib/post-types/country-custom-posttype.php';
require_once get_stylesheet_directory() . '/lib/post-types/collection-custom-posttype.php';
require_once get_stylesheet_directory() . '/lib/post-types/unboxing-custom-posttype.php';

// files which create custom taxonomies
require_once get_stylesheet_directory() . '/lib/taxonomies/collection-custom-taxonomy.php';
require_once get_stylesheet_directory() . '/lib/taxonomies/country-custom-taxonomy.php';
require_once get_stylesheet_directory() . '/lib/taxonomies/snack-type-custom-taxonomy.php';
require_once get_stylesheet_directory() . '/lib/taxonomies/geography-custom-taxonomy.php';
require_once get_stylesheet_directory() . '/lib/taxonomies/brand-custom-taxonomy.php';

// include model classes for different pages/entities
require_once get_stylesheet_directory() . '/lib/page-models/page.model.php';
require_once get_stylesheet_directory() . '/lib/page-models/shopall.model.php';
require_once get_stylesheet_directory() . '/lib/page-models/country.model.php';
require_once get_stylesheet_directory() . '/lib/page-models/collection.model.php';
require_once get_stylesheet_directory() . '/lib/page-models/snack.model.php';
require_once get_stylesheet_directory() . '/lib/page-models/unboxing.model.php';

require_once get_stylesheet_directory() . '/lib/custom-endpoints.php';

// general classes
require_once get_stylesheet_directory() . '/lib/model.php'; // Database inializer

require_once get_stylesheet_directory() . '/lib/general.class.php'; // parent class for address, cart, and user
require_once get_stylesheet_directory() . '/lib/user.class.php'; // Login stuff
require_once get_stylesheet_directory() . '/lib/cart.class.php'; // Checkout/Cart functionality
require_once get_stylesheet_directory() . '/lib/address.class.php'; // address stuff

require_once get_stylesheet_directory() . '/lib/stripe-helper.class.php'; // Helper class to do specific Stripe calls
require_once get_stylesheet_directory() . '/lib/easypost-helper.class.php'; // Helper class for easypost things
require_once get_stylesheet_directory() . '/lib/tax.class.php'; // tax getter
require_once get_stylesheet_directory() . '/lib/email.class.php'; // email sender
require_once get_stylesheet_directory() . '/lib/klaviyo-helper.class.php'; // klaviyo helper
require_once get_stylesheet_directory() . '/lib/inventree.php';


require_once get_stylesheet_directory() . '/lib/fulfillment.class.php'; // fulfillment logic
require_once get_stylesheet_directory() . '/lib/warehouse.class.php';
require_once get_stylesheet_directory() . '/lib/invoice-generator.class.php'; 

// include ajax handler
//require_once get_stylesheet_directory() . '/lib/ajax-handler.php';
require_once get_stylesheet_directory() . '/lib/ajax/WarehouseAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/CartAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/CheckoutAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/FulfillmentAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/UserAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/UnboxingAjax.php';
require_once get_stylesheet_directory() . '/lib/ajax/ContentGetter.php';

require_once get_stylesheet_directory() . '/lib/session.class.php';

// theme hooks and actions
require_once get_stylesheet_directory() . '/lib/theme-helpers.php';

// gallery metabox
require_once get_stylesheet_directory() . '/lib/gallery-metabox/gallery.php';

// set URL of sync posts target site
$host_url = parse_url( get_site_url(), PHP_URL_HOST );
$account_site_url = '';
if ($host_url === 'candybar.snackcrate.com') {
    $account_site_url = 'account.snackcrate.com';
} elseif ($host_url === 'candybar-dev.snackcrate.com') {
    $account_site_url = 'account-dev.snackcrate.com';
} elseif ($host_url === 'candybar-staging.snackcrate.com') {
    $account_site_url = 'account-staging.snackcrate.com';
}

// sync posts with account site
function sync_single_posts_arr_with_account($posts) {
    // Create a new cURL resource
    try{
        // Create a new cURL resource
        $curl = curl_init();

        // Set the URL for the POST request
        global $account_site_url;
        error_log("accoutnsite:https://$account_site_url/wp-json/custom-api/v1/save-posts/");
        curl_setopt($curl, CURLOPT_URL, "https://$account_site_url/wp-json/custom-api/v1/save-posts/");

        // Set the HTTP method to POST
        curl_setopt($curl, CURLOPT_POST, 1);

        // Set the POST data to the array/chunk of posts
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($posts));

        // Set the content type to JSON
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Return the response instead of printing it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
            error_log('cURL Error: ' . 'Error: ' . curl_error($curl));
        }else {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            error_log('HTTP Status Code: ' . $http_code);
            error_log('Response: ' . $response);
        }

        // Close cURL resource
        curl_close($curl);
    }catch ( Exception $e){
        error_log("Error in single:". $e->getMessage());
    }

}

add_action( 'save_post', 'callback_sync_single_post_with_account', 20, 2 );
function callback_sync_single_post_with_account($post_id, $post) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'country' !== $post->post_type && 'snack' !== $post->post_type && 'collection' !== $post->post_type ) {
        return;
    }

    sync_post_assets($post);

    sync_single_posts_arr_with_account(array($post));
}

add_action( 'delete_post', 'sync_delete_post_from_account' );

function sync_delete_post_from_account($post_id) {
    // Create a new cURL resource
    $curl = curl_init();

    // Set the URL for the POST request
    global $account_site_url;
    curl_setopt($curl, CURLOPT_URL, "https://$account_site_url/wp-json/custom-api/v1/save-posts/");

    // Set the HTTP method to POST
    curl_setopt($curl, CURLOPT_POST, 1);

    // Set the POST data
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_id));

    // Set the content type to JSON
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Return the response instead of printing it
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute the POST request
    curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        echo 'Error: ' . curl_error($curl);
    }

    // Close cURL resource
    curl_close($curl);

}

function sync_post_assets($post_obj) {
    $meta_data = get_post_meta($post_obj->ID);
    unset($meta_data['_cdp_origin']);
    unset($meta_data['_cdp_origin_site']);
    unset($meta_data['_cdp_origin_title']);
    unset($meta_data['_cdp_counter']);

    $attachments = get_attached_media('', $post_obj);

    $taxonomies = get_post_taxonomies($post_obj);
    $send_terms = array();
    
    $check_attachments = ['_thumbnail_id', 'featured-image', 'small-thumbnail', 'medium-thumbnail', 'nutrition-label', 'hero-image', 'hero-video'];
    
    foreach ($check_attachments as $attachment_name){
        if ((isset($meta_data[$attachment_name]) && !empty($meta_data[$attachment_name])) && (!isset($attachments[$meta_data[$attachment_name][0]]) || empty($attachments[$meta_data[$attachment_name][0]]))) {
            if(!is_int($meta_data[$attachment_name][0]) && !ctype_digit($meta_data[$attachment_name][0])){
                continue;
            }
            $attachment_post = get_post($meta_data[$attachment_name][0]);
            if(!$attachment_post){
                continue;
            }
            $attachments[$attachment_post->ID] = $attachment_post;
        }
    }
    
    foreach ($taxonomies as $tax) {
        $terms = wp_get_post_terms($post_obj->ID, $tax);
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                unset($term->term_id);
                unset($term->term_group);
                unset($term->term_taxonomy_id);
                unset($term->parent);
                unset($term->count);
                unset($term->filter);

                $send_terms[$tax][] = $term;
            }
        }
    }

    $post_obj->postobj_meta_data = $meta_data;
    $post_obj->postobj_attachments = $attachments;
    $post_obj->postobj_taxonomies = $send_terms;

    return $post_obj;
}

function sync_all_posts_to_account_site()
{
    // Prepare query arguments
    $post_types = array( 'country', 'snack', 'collection' );
    $args = array(
        'post_type'      => $post_types, // Array of post types
        'post_status' => 'any',
        'posts_per_page' => -1,          // Retrieve all posts
    );

    // Get all posts
    $posts = get_posts( $args );

    // Chunk posts into arrays of 30 posts each
    $chunked_posts = array_chunk($posts, 30);

    // Loop through each chunk and send it via POST request
    foreach ($chunked_posts as $chunk) {

        $posts_arr = array();
        foreach ($chunk as $post_obj) {
            $pwa = sync_post_assets($post_obj);
            $posts_arr[] = $pwa;
        }

        sync_single_posts_arr_with_account($posts_arr);
    }
}

// add "sync all posts" button to admin bar
add_action('admin_bar_menu', 'add_sync_with_account_button', 100);
function add_sync_with_account_button() {
    global $wp_admin_bar;
    $form_html = '<form method="post"><button name="sync_with_account_button" class="button-link" style="color: #f0f0f1; text-decoration: none; border: none; background: none" type="submit" title="Sync all posts with account site">Sync with account</button></form>';

    $args = array(
        'id' => 'sync_with_account_form',
        'title' => $form_html,
        'parent' => false,
    );

    $wp_admin_bar->add_node($args);
}

add_action('init', function() {
    if (isset($_POST['sync_with_account_button']) && !wp_next_scheduled('sync_with_account_hook')) {
        wp_schedule_single_event( time(), 'sync_with_account_hook' );
    }
});

add_action( 'sync_with_account_hook', 'sync_all_posts_to_account_site' );

function get_current_user_role() {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    $user = get_user_by( 'id', $user_id );
    $user_vars = get_object_vars( $user );

    return $user_vars['roles'][0];
}
