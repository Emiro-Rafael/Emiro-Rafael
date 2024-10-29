<?php
$items = PageModel::getCategoricalData($args);
if(count($items) > 0) :
?>
    <h3 class="h7 h6-sm h5-lg h4-xl font-weight-bold text-left mb-3 mb-lg-4"><?php echo $args['title'];?></h3>
    <div class="snack-swiper container position-relative px-0 px-md-3">
        <div class="snack-swiper-container swiper-container <?php echo $args['swiper_class'];?>-swiper px-1 py-1 px-md-3 mx-auto">
            <div class="swiper-wrapper">

                <?php foreach($items as $item) :?>

                    <?php 
                    $snack = new SnackModel($item->ID);
                    ?>

                    <div class="swiper-slide">
                        <div class="single-snack ml-lg-0">
                            <a class="snack-btn" href="<?php echo $item->link;?>">
                                <img class="flag-img img-fluid" alt="<?php echo $snack->getCountryName();?> Flag" src="<?php echo $snack->getCountryFlag()?>">

                                <img class="img-fluid" alt="Image of <?php echo $item->name;?>" src="<?php echo $item->thumbnail; ?>" />
                                <?php if( $snack->getStock() <= $snack->getMinimumStock() ) :?>
                                    <!-- SOLD OUT OVERLAY IMAGE HERE -->
                                    <img alt="out of stock" class="position-absolute img-fluid" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/OOS_205x130_new-min.png">
                                <?php endif;?>
                            </a>

                            <h3 class="snack-title equalHeight"><?php echo $item->name;?></h3>

                            <?php if ( $snack->hasReviews() && $snack->getRatingsCount() >= 3 ): ?>
                                <?php get_template_part( 'template-parts/star-rating', get_post_format(), $item->rating ); ?>
                            <?php else: ?>
                            
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach;?>
            
            </div>  <!-- swiper-wrapper -->
        </div> <!-- snack-swiper-container -->

        <div class="d-none d-md-flex swiper-button-prev prev-<?php echo $args['identifier'];?>-item"></div>
        <div class="d-none d-md-flex swiper-button-next next-<?php echo $args['identifier'];?>-item"></div>
    </div>
<?php endif;?>