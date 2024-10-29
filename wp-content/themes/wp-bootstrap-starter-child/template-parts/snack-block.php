<?php
/**
 * Template part for displaying snacks in a block
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$snack = new SnackModel(get_the_ID());
if(!$snack->checkForCompletion())
    return false; // snack needs more data for us to show it here
?>
<div class="taxItem" data-type="<?php echo $snack->getSnackType('slug');?>" data-countries='<?php echo $snack->getAllCountries('slug');?>'>
    <a href="<?php echo get_permalink();?>" class="snack-block-container" data-equalizer="resizeElement">
        <div class="snack-block border-0 rounded-sm p-2 p-lg-3 p-xl-4 position-relative">
            <img loading="lazy" class="flag-img img-fluid position-absolute" alt="<?php echo $snack->getCountryName();?> Flag" src="<?php echo $snack->getCountryFlag();?>"/>
            <div class="snack-img d-flex flex-column align-items-center mt-4 mt-lg-5 mb-3 mx-2 mx-lg-auto position-relative">
                <img loading="lazy" class="img-fluid" alt="Image of <?php echo get_post_meta(get_the_ID(), 'user-friendly-name', true);?>" src="<?php echo $snack->getThumbnail('medium');?>" />
                <?php if( $snack->getStock() <= $snack->getMinimumStock() ) :?>
                    <!-- SOLD OUT OVERLAY IMAGE HERE -->
                    <img alt="out of stock" class="position-absolute img-fluid" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/OOS_233x189_new-min.png">
                <?php endif;?>
            </div>

            <div class="equalHeight">
                <h3 id="snackTitle" class="snack-title h7 h5-lg h3-xl font-weight-bold mb-2"><?php echo get_post_meta(get_the_ID(), 'user-friendly-name', true);?></h3>
                <h4 id="snackBrand" class="snack-brand h8 h6-lg h4-xl"><?php echo $snack->getBrand();?></h4>
            </div>

            <!-- only show review-stars if snack has reviews -->
            <?php if ( $snack->hasReviews() && $snack->getRatingsCount() >= 3): ?>
                <div class="review-stars d-flex align-items-center justify-content-start mb-0 mb-xl-1">
                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), get_post_meta(get_the_ID(), 'average_rating', true) ); ?>
                </div>
            <?php else: ?>
                <div class="stars-placeholder mb-0 mb-xl-1">

                </div>
            <?php endif; ?>

            <?php get_template_part( 'template-parts/snack-price', get_post_format(), $snack) ;?>

        </div>
    </a>
</div>