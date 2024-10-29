<?php
function create_snacktype_nonhierarchical_taxonomy() {
    $labels = array(
        'name' => _x( 'Snack Types', 'taxonomy general name' ),
        'singular_name' => _x( 'Snack Type', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Snack Types' ),
        'popular_items' => __( 'Popular Snack Types' ),
        'all_items' => __( 'All Snack Types' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Snack Type' ), 
        'update_item' => __( 'Update Snack Type' ),
        'add_new_item' => __( 'Add New Snack Type' ),
        'new_item_name' => __( 'New Snack Type Name' ),
        'separate_items_with_commas' => __( 'Separate Snack Types with commas' ),
        'add_or_remove_items' => __( 'Add or remove Snack Types' ),
        'choose_from_most_used' => __( 'Choose from the most used Snack Types' ),
        'menu_name' => __( 'Snack Types' ),
    ); 

    // Now register the non-hierarchical taxonomy like tag
    register_taxonomy('snack_types','snack',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'snack-type' ),
    ));
}
add_action( 'init', 'create_snacktype_nonhierarchical_taxonomy', 0 );


function add_custom_tax_field_onedit( $term ){
    $term_meta = get_option( "snack_types_{$term->term_id}" );
    $value = empty($term_meta['thumbnail']) ? 0 : $term_meta['thumbnail'];
    ?>
    
    <tr>
        <th><label for='<?php echo $term->term_id;?>_field'>Thumbnail</label></th>
        <td>
            <div id="<?php echo $term->term_id;?>_thumbnail" style='border-radius:4px;display:inline-block;padding:5px;margin:5px 10px 0 0;border:1px solid #8c8f94;width:80px;height:80px;'>
                <?php 
                    if($value)
                        echo wp_get_attachment_image( $value, 'thumbnail' );
                    else
                        echo '<img src="#" class="attachment-thumbnail size-thumbnail" alt="no image chosen" loading="lazy" width="80" height="80">';
                ?>
            </div>
            
            <button style="vertical-align: bottom;" class="set_custom_images button"><?php echo ($value) ? 'Replace' : 'Add';?> Image</button>

            <button <?php echo $value ? '' : 'disabled';?> onClick="removeImageValue('<?php echo $term->term_id;?>');" style="float:right;" type="button" class="components-button is-link is-destructive" id="<?php echo $term->term_id;?>_rmv">Remove image</button>  

            <input type="hidden" class="regular-text process_custom_images" value="<?php echo $value;?>" id="<?php echo $term->term_id;?>_field" name="thumbnail" >
        </td>
    </tr>
    <?php
}
add_action( 'snack_types_edit_form_fields', 'add_custom_tax_field_onedit'  );
add_action( 'snack_types_add_form_fields', 'add_custom_tax_field_onedit'  );


function save_snack_types_field( $term_id )
{
    if ( isset( $_POST['thumbnail'] ) ) 
    {
        $term = get_term( $term_id );
    
		// get options from database - if not a array create a new one
		$term_meta = get_option( "{$term->taxonomy}_{$term_id}" );

		if ( !is_array( $term_meta ))
			$term_meta = array();

		// get value and save it into the database - maybe you have to sanitize your values (urls, etc...)
		$term_meta['thumbnail'] = isset( $_POST['thumbnail'] ) ? $_POST['thumbnail'] : '';

		update_option( "{$term->taxonomy}_{$term_id}", $term_meta );
	}
}
add_action( 'create_snack_types', 'save_snack_types_field' );
add_action( 'edited_snack_types', 'save_snack_types_field' );