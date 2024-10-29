<?php
$lc_size = $_REQUEST['params']['size'];
$args = new UnboxingModel($_REQUEST['params']['post_id']);
?>
<div class="<?php echo $lc_size;?>-size-swiper size-swiper">
    <div class="swiper-wrapper">

        <?php foreach( $args->getSnacksBySize($lc_size) as $snack ):?>
            <div class="swiper-slide">
                <div class="slide-content">
                    <div class="content-wrap">
                        <div class="d-flex flex-column p-3 p-xl-4">
                            <img class="snack-img" alt="image of <?php echo get_post_meta( $snack->ID, 'user-friendly-name', true );?>" src="<?php echo wp_get_attachment_url( get_post_meta( $snack->ID, 'medium-thumbnail', true ) );?>">
                            <h3 class="text-uppercase text-primary text-left font-weight-bolder h6 h5-md h4-xl"><?php echo get_post_meta( $snack->ID, 'user-friendly-name', true );?></h3>
                            <div class="text-dark text-left h7 h6-md h5-xl"><?php echo trim($snack->post_content);?></div>
                        </div>
                        <?php 
                        if( User::checkHasSubscription() ) 
                        {
                            if( SnackModel::userCanReview( get_current_user_id(), $snack->ID ) )
                            {
                                get_template_part( 'template-parts/unboxing/review-stars', null, $snack->ID );
                            }
                            else
                            {
                                echo '<div class="review-stars-container">';
                                    get_template_part( 'template-parts/star-rating', get_post_format(), SnackModel::getUserSnackRating( get_current_user_id(), $snack->ID ) );
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                    <div class="add-to-cart">
                        <?php get_template_part( 'template-parts/add-to-cart', get_post_format(), array('snack_id' => $snack->ID, 'include_price' => true) ); ?>
                    </div>
                </div> <!-- slide-content -->

            </div> <!-- swiper-slide -->
        <?php endforeach;?>
    </div> <!-- swiper-wrapper -->

    <div class="prev-<?php echo $lc_size;?>-snack swiper-button-prev"></div>
    <div class="next-<?php echo $lc_size;?>-snack swiper-button-next"></div>
</div> <!-- size-swiper -->