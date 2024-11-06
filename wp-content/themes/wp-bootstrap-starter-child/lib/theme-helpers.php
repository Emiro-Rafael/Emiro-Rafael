<?php
// Hide wordpress user bar on frontend for non-admins
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
    else 
    {
        show_admin_bar(true);
    }
}

// Allow SVG
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
  
    $filetype = wp_check_filetype( $filename, $mimes );
  
    return array(
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    );
  
}, 10, 4 );

function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
    echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action( 'admin_head', 'fix_svg' );
// END Allow SVG

function set_global_user_data() {
    global $user_data;
    if(isset($_COOKIE['snackcrate_user']))
    {
        $user_data = json_decode( stripslashes($_COOKIE['snackcrate_user']) );

        if( empty( get_current_user_id() ) )
        {
            $user_obj = new User($user_data->email);
            $user_obj->login(
                array(
                    'pwd' => wp_generate_password()
                )
            );
        }

        setcookie( 'snackcrate_user', stripslashes($_COOKIE['snackcrate_user']), time() + 3600, "/", SCModel::getDomain() ); // extend 1 hour
    } elseif(is_user_logged_in()) {
        $user = get_user_by('id', get_current_user_id());
        if(!current_user_can('administrator') && !in_array('warehouse', $user->roles)){
            wp_logout();
        }
    }
}
add_action( 'after_setup_theme', 'set_global_user_data' );

function start_session_wp() 
{
    $session_handler = new Session();
    $session_handler->checkCartExpiry();
}
add_action('init', 'start_session_wp', 1);


// backend redirect for countries that are out of stock
function check_stock()
{
    global $post;
    if($post->post_type == 'country')
    {
        $country_model = new CountryModel( $post->ID );
        if( $country_model->checkInStock() == 0 && !$country_model->checkIfPreorder() )
        {
            $url = add_query_arg( 
                    'c', 
                    get_post_meta( 
                        $post->ID, 
                        'country-taxonomy', 
                        true 
                    ), 
                    get_permalink( get_page_by_path( 'shop-all' ) ) 
                );

            wp_redirect( $url );
        }
    }
}
add_action('template_redirect', 'check_stock', 1);


/**
 * predictive search with ajax
 */
function ajax_fetch() {
?>
    <script type="text/javascript">
        function fetch2(){//i hate you...
            if( jQuery('#keyword').val().length >= 3 )
            {
                jQuery('#predictive-search').show();
                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: { action: 'data_fetch', keyword: jQuery('#keyword').val() },
                    success: function(data) {
                        jQuery('#predictive-search').html( data );
                    }
                });

            }
            else
            {
                jQuery('#predictive-search').hide();
                jQuery('#predictive-search').empty();
            }
        }
    </script>

<?php
}
// add the ajax fetch js
add_action( 'wp_footer', 'ajax_fetch' );


function data_fetch(){

    $the_query = new WP_Query( 
        array( 
            'posts_per_page' => -1, 
            's' => esc_attr( $_POST['keyword'] ), 
            'post_type' => 'snack',
            'post_status' => 'publish'
        )
    );
    
    $i = 0;
    if( $the_query->have_posts() ) :
        while( $the_query->have_posts() ): $the_query->the_post(); 
            $i++;
        ?>

            <div class="py-2 w-100"><a class="w-100 d-inline-block" href="<?php echo esc_url( post_permalink() ); ?>"><?php echo get_post_meta(get_the_ID(), 'user-friendly-name', true);?></a></div>

        <?php 
            if($i == 5) break;
        endwhile;
        wp_reset_postdata();
    else:
        ?>
            <div class="py-2">Nothing matches your search...</div>
            <!--
            <script type="text/javascript">
                jQuery('#predictive-search').hide();
            </script>
            -->
        <?php
    endif;

    die();
}
// the ajax function
add_action('wp_ajax_data_fetch' , 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch','data_fetch');


add_action('transition_comment_status', 'my_approve_comment_callback', 10, 3);
function my_approve_comment_callback($new_status, $old_status, $comment) {
    if($old_status != $new_status) {
        if($new_status == 'approved' && $comment->comment_type == 'snack-review') {
            // Your code here
            $snack = new SnackModel( $comment->comment_post_ID );
            $snack->approveReview( $comment );
        }
    }
}

function remove_posts_from_sitemap( $args, $post_type ) {
    if ( 'page' !== $post_type ) {
         return $args;
    }
    $args['post__not_in'] = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array(); 
    $args['post__not_in'][] = 2605; // Replace 100 with a specific post's ID
    return $args;
}

add_filter( 'wp_sitemaps_posts_query_args', 'remove_posts_from_sitemap', 10, 2 );



function add_picker_user_role()
{
    $subscriber_capabilities = get_role('subscriber')->capabilities;
    
    add_role( 'fulfillment', 'Fulfillment', array( 'read' => true, 'level_0' => true ) );
    add_role( 'warehouse', 'warehouse', array( 'read' => true, 'level_0' => true ) );
}
add_action( 'init', 'add_picker_user_role' );

function logout_fulfiller()
{
    if( !empty($_SESSION) )
    {
        if( !empty($_SESSION['allow_fulfill']) )
        {
            unset($_SESSION['allow_fulfill']);
        }
        
        if( !empty($_SESSION['fulfiller_user_id']) )
        {
            unset($_SESSION['fulfiller_user_id']);
        }
    }
}
add_action( 'wp_logout', 'logout_fulfiller' );


function site_scripts()
{
    wp_enqueue_script( 'main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery') );

    wp_localize_script(
        'main-js', 
        'global_script_vars', 
        array(
            'template_uri' => get_stylesheet_directory_uri()
        )
    );
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);