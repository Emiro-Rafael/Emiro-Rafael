<?php
function country_custom_post_type()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Countries', 'Post Type General Name' ),
        'singular_name'       => _x( 'Country', 'Post Type Singular Name' ),
        'menu_name'           => __( 'Countries' ),
        'parent_item_colon'   => __( 'Parent Country' ),
        'all_items'           => __( 'All Countries' ),
        'view_item'           => __( 'View Country' ),
        'add_new_item'        => __( 'Add New Country' ),
        'add_new'             => __( 'Add New' ),
        'edit_item'           => __( 'Edit Country' ),
        'update_item'         => __( 'Update Country' ),
        'search_items'        => __( 'Search Country' ),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );
    
    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'countries' ),
        'description'         => __( 'Countries for individual purchase' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'geography' ),
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
    register_post_type( 'country', $args );
}
add_action( 'init', 'country_custom_post_type', 0 );

add_filter('manage_country_posts_columns', function($columns) {
	return array_merge($columns, ['verified' => __('Visible', 'textdomain')]);
});

add_action('manage_country_posts_custom_column', function($column_key, $post_id) {
	if ($column_key == 'verified') {
        $snack = new CountryModel($post_id);
		$verified = $snack->checkForCompletion();
		$bg = $verified ? 'green' : 'red';
        echo '<p style="background-color:'.$bg.';width:16px;height:16px;border-radius:8px;">&nbsp;</p>';
	}
}, 10, 2);