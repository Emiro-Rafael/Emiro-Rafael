<?php
function create_brand_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Brands', 'taxonomy general name' ),
        'singular_name' => _x( 'Brand', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Brands' ),
        'popular_items' => __( 'Popular Brands' ),
        'all_items' => __( 'All Brands' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Brand' ), 
        'update_item' => __( 'Update Brand' ),
        'add_new_item' => __( 'Add New Brand' ),
        'new_item_name' => __( 'New Brand Name' ),
        'separate_items_with_commas' => __( 'Separate Brands with commas' ),
        'add_or_remove_items' => __( 'Add or remove Brands' ),
        'choose_from_most_used' => __( 'Choose from the most used Brands' ),
        'menu_name' => __( 'Brands' ),
    ); 

    // Now register the non-hierarchical taxonomy like tag
    register_taxonomy('brands','snack',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'brand' ),
    ));
}
add_action( 'init', 'create_brand_nonhierarchical_taxonomy', 0 );


add_action( 'brands_edit_form_fields', 'add_custom_tax_field_onedit'  );

add_action( 'create_brands', 'save_snack_types_field' );
add_action( 'edited_brands', 'save_snack_types_field' );