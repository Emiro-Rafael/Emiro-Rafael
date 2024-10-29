<?php
function collection_custom_post_type()
{
    
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Collections', 'Post Type General Name' ),
        'singular_name'       => _x( 'Collection', 'Post Type Singular Name' ),
        'menu_name'           => __( 'Collections' ),
        'parent_item_colon'   => __( 'Parent Collection' ),
        'all_items'           => __( 'All Collections' ),
        'view_item'           => __( 'View Collection' ),
        'add_new_item'        => __( 'Add New Collection' ),
        'add_new'             => __( 'Add New' ),
        'edit_item'           => __( 'Edit Collection' ),
        'update_item'         => __( 'Update Collection' ),
        'search_items'        => __( 'Search Collection' ),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );
    
    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'collections' ),
        'description'         => __( 'Collections for individual purchase' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'countries', 'collections', 'geography', 'collection_types' ),
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
        'rewrite' => array('slug' => 'collections')
    );
    
    // Registering your Custom Post Type
    register_post_type( 'collection', $args );
}
add_action( 'init', 'collection_custom_post_type', 0 );

add_filter('manage_collection_posts_columns', function($columns) {
	return array_merge($columns, ['verified' => __('Visible', 'textdomain')]);
});

add_action('manage_collection_posts_custom_column', function($column_key, $post_id) {
	if ($column_key == 'verified') {
        $collection = new CollectionModel($post_id);
		
        $verified = $collection->checkForCompletion();
        $bg = $verified ? 'green' : 'red';
        echo '<p style="background-color:'.$bg.';width:16px;height:16px;border-radius:8px;">&nbsp;</p>';
	}
}, 10, 2);