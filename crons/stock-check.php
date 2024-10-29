<?php
require_once __DIR__ . "/../wp-load.php";

$posts = get_posts([
    'post_type' => 'snack',
    'post_status' => 'publish',
    'numberposts' => -1,
]);

foreach($posts as $post)
{
    $meta = get_post_meta( $post->ID );

    //die('<pre>'.print_r($meta,1));

    echo "{$post->post_title}, {$meta['internal-id-code'][0]}, {$meta['in-stock'][0]}\n";
}