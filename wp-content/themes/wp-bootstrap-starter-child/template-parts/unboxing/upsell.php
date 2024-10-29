<?php
$args = [
    'post_type'      => 'snack',
    'posts_per_page' => 1,
    'post_name__in'  => ['morinaga-hi-chew-peach-40g']
];
$q = get_posts( $args );
$post_id = $q[0]->ID;
?>
<section class="upsell position-relative mx-2 mx-lg-3 mx-xxl-auto mb-5 p-lg-5 d-flex align-items-center justify-content-start">
    <video class="upsell-vid position-absolute" id="countryLoop" preload="auto" playsinline autoplay muted loop>
        <source src="/wp-content/uploads/2022/10/Mast_Head_Final_Web_NoText-min.mp4" type="video/mp4">
    </video>

    <div class="upsell-text d-flex flex-column justify-content-center align-items-start ml-5 my-5 w-50">
        <span class="text-uppercase text-white font-italic mb-1 h9 h8-sm h7-md h4-xl h3-xxl">Limited Edition:</span>    
        <h3 class="text-uppercase text-white font-weight-bolder mb-0 h3 display-6-sm display-4-md display-3-lg display-2-xl">Hi-Chew</h3>
        <h3 class="text-uppercase text-white font-weight-bolder mb-0 h3 display-6-sm display-4-md display-3-lg display-2-xl">Peach</h3>
        <?php get_template_part( 'template-parts/add-to-cart', get_post_format(), $post_id ); ?>
    </div>

</section>