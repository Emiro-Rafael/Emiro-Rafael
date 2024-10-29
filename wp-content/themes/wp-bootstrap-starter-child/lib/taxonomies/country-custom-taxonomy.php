<?php
function create_countries_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Countries', 'taxonomy general name' ),
        'singular_name' => _x( 'Country', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Countries' ),
        'popular_items' => __( 'Popular Countries' ),
        'all_items' => __( 'All Countries' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Country' ), 
        'update_item' => __( 'Update Country' ),
        'add_new_item' => __( 'Add New Country' ),
        'new_item_name' => __( 'New Country Name' ),
        'separate_items_with_commas' => __( 'Separate countries with commas' ),
        'add_or_remove_items' => __( 'Add or remove countries' ),
        'choose_from_most_used' => __( 'Choose from the most used countries' ),
        'menu_name' => __( 'Countries' ),
    ); 

    // Now register the non-hierarchical taxonomy like tag
    register_taxonomy('countries','snack',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'country' ),
    ));
}
add_action( 'init', 'create_countries_nonhierarchical_taxonomy', 0 );