<?php
function create_geography_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Geography', 'taxonomy general name' ),
        'singular_name' => _x( 'Geography', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Geography' ),
        'popular_items' => __( 'Popular Geography' ),
        'all_items' => __( 'All Geography' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Geography' ), 
        'update_item' => __( 'Update Geography' ),
        'add_new_item' => __( 'Add New Geography' ),
        'new_item_name' => __( 'New Geography Name' ),
        'separate_items_with_commas' => __( 'Separate geography with commas' ),
        'add_or_remove_items' => __( 'Add or remove geography' ),
        'choose_from_most_used' => __( 'Choose from the most used geography' ),
        'menu_name' => __( 'Geography' ),
    ); 

    // Now register the non-hierarchical taxonomy like tag

    register_taxonomy('geography',['country'],array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'geography' ),
        'show_in_rest' => true,
    ));
}
add_action( 'init', 'create_geography_nonhierarchical_taxonomy', 0 );