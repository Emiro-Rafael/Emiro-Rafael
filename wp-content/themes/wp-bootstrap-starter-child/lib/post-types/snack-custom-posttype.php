<?php
function snack_custom_post_type() 
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Snacks', 'Post Type General Name' ),
        'singular_name'       => _x( 'Snack', 'Post Type Singular Name' ),
        'menu_name'           => __( 'Snacks' ),
        'parent_item_colon'   => __( 'Parent Snack' ),
        'all_items'           => __( 'All Snacks' ),
        'view_item'           => __( 'View Snack' ),
        'add_new_item'        => __( 'Add New Snack' ),
        'add_new'             => __( 'Add New' ),
        'edit_item'           => __( 'Edit Snack' ),
        'update_item'         => __( 'Update Snack' ),
        'search_items'        => __( 'Search Snack' ),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'snacks' ),
        'description'         => __( 'Snacks for individual purchase' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'countries', 'collections', 'snack_types', 'brands' ),
        /* 
        * A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,
    );

    // Registering your Custom Post Type
    register_post_type( 'snack', $args );
}
add_action( 'init', 'snack_custom_post_type', 0 );


add_filter('manage_snack_posts_columns', function($columns) {
	return array_merge($columns, ['verified' => __('Visible', 'textdomain')], ['included_in' => __('Included In', 'textdomain')] );
});

add_action('manage_snack_posts_custom_column', function($column_key, $post_id) {
    switch($column_key)
    {
        case 'verified':
            $snack = new SnackModel($post_id);
            $verified = $snack->checkForCompletion();
            $bg = $verified ? 'green' : 'red';
            echo '<p style="background-color:'.$bg.';width:16px;height:16px;border-radius:8px;">&nbsp;</p>';
            break;

        case 'included_in':
            echo get_post_meta( $post_id, 'included-in', true );
            break;
    }
}, 10, 2);