<?php
// pass in SnackModel object
?>
<div class="user-reviews-swiper container position-relative">
    <div class="user-reviews-container swiper-container px-1 py-1 px-md-3 py-md-2 mx-auto">
        <div class="swiper-wrapper">

            <?php foreach($args->getReviews() as $review): ?>
                <div class="swiper-slide">
                    <div class="single-user-review">
                        <div class="review-stars">
                            <?php 
                                get_template_part( 'template-parts/star-rating', get_post_format(), $review['rating'] );
                            ?>
                        </div>

                        <p class="user-review-text">
                            &ldquo;<?php echo $review['comment'];?>&rdquo;
                        </p>

                        <h5 class="user-review-name">-<span><?php echo $review['user_name'];?></span></h5>

                    </div>
                </div>
            <?php endforeach; ?>
            
        </div> <!-- swiper-wrapper -->

        
    </div> <!-- user-reviews-swiper -->

    <div class="swiper-button-prev prev-user-review"></div>
    <div class="swiper-button-next next-user-review"></div>
</div> <!-- user-reviews-container -->
