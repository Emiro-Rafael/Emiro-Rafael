<?php
function unboxing_custom_post_type() 
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Unboxings', 'Post Type General Name' ),
        'singular_name'       => _x( 'Unboxing', 'Post Type Singular Name' ),
        'menu_name'           => __( 'Unboxings' ),
        'parent_item_colon'   => __( 'Parent Unboxing' ),
        'all_items'           => __( 'All Unboxings' ),
        'view_item'           => __( 'View Unboxing' ),
        'add_new_item'        => __( 'Add New Unboxing' ),
        'add_new'             => __( 'Add New' ),
        'edit_item'           => __( 'Edit Unboxing' ),
        'update_item'         => __( 'Update Unboxing' ),
        'search_items'        => __( 'Search Unboxing' ),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label'               => __( 'unboxing' ),
        'description'         => __( 'Monthly digital unboxing experience' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'countries' ),
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
    register_post_type( 'unboxing', $args );
}
add_action( 'init', 'unboxing_custom_post_type', 0 );
