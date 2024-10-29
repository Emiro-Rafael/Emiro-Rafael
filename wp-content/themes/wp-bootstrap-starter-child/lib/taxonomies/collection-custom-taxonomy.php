<?php
function create_collections_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Collections', 'taxonomy general name' ),
        'singular_name' => _x( 'Collection', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Collections' ),
        'popular_items' => __( 'Popular Collections' ),
        'all_items' => __( 'All Collections' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Collection' ), 
        'update_item' => __( 'Update Collection' ),
        'add_new_item' => __( 'Add New Collection' ),
        'new_item_name' => __( 'New Collection Name' ),
        'separate_items_with_commas' => __( 'Separate collections with commas' ),
        'add_or_remove_items' => __( 'Add or remove collections' ),
        'choose_from_most_used' => __( 'Choose from the most used collections' ),
        'menu_name' => __( 'Collections' ),
    ); 

    // Now register the non-hierarchical taxonomy like tag
    register_taxonomy('collections','snack',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'collections' ),
    ));
}
add_action( 'init', 'create_collections_nonhierarchical_taxonomy', 0 );
