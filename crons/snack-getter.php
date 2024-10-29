<?php
require_once __DIR__ . "/../wp-load.php";
$posts = get_posts([
    'post_type' => 'snack',
    'post_status' => 'publish',
    'numberposts' => -1,
]);

foreach( $posts as $post )
{
    $replace_strings = array(
        '<!-- wp:paragraph -->', '<p>', '</p>', '<!-- /wp:paragraph -->'
    );

    $data = array(
        'sap_code' => get_post_meta( $post->ID, 'internal-id-code', true ),
        'title' => get_post_meta( $post->ID, 'user-friendly-name', true ),
	'description' => '"' . trim(str_replace($replace_strings, '', $post->post_content)) . '"',
        'link' => 'https://candybar.snackcrate.com/snack/' . $post->post_name,
        'image_link' => wp_get_attachment_url( get_post_meta( $post->ID, 'medium-thumbnail', true ) ),

        'availability' => empty( get_post_meta( $post->ID, 'in-stock', true ) ) ? 'out_of_stock' : 'in_stock',

        'price' => get_post_meta( $post->ID, 'price', true ) . ' USD',

        'product_type' => get_the_terms($post->ID, 'snack_types')[0]->name,

        'brand' => get_the_terms($post->ID, 'brands')[0]->name,
    );
    
    echo implode(',', $data) . "\n";
}
