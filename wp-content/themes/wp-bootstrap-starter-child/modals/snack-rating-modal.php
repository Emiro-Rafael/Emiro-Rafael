<?php
// pass in SnackModel object
?>
<!-- Snack Rating Modal -->
<div class="snack-rating-modal modal fade" id="snackRatingModal" tabindex="-1" aria-labelledby="snackRatingModalLabel" aria-hidden="true">
    <div class="modal-dialog mx-auto">
        <div class="modal-content p-3">
            <div class="modal-header border-0 p-0">
                <div class="modal-title d-flex align-items-center justify-content-start">
                    <div class="review-stars">
                        <?php get_template_part( 'template-parts/star-rating', get_post_format(), get_post_meta(get_the_ID(), 'average_rating', true) ); ?>
                    </div>
                    <p id="snackRatingModalLabel" class="h5-md font-weight-bold mb-0 mt-1 mt-md-2 ml-2 ml-md-3">
                        <?php if($args->hasReviews()) :?>
                            <span>
                                <?php echo get_post_meta(get_the_ID(), 'average_rating', true);?>
                            </span> out of 5
                        <?php endif; ?>
                    </p>
                </div> <!-- modal-title -->

                <button type="button" class="close p-2" data-dismiss="modal" aria-label="Close">
                <span class="h2 h1-lg" aria-hidden="true">&times;</span>
                </button>

            </div> <!-- modal-header -->
            <div class="modal-body p-0">
                <!-- number of reviews should match number on single-snack page -->
                <p class="h8 h6-md text-gray font-weight-medium my-3"><span><?php echo $args->getRatingsCount();?></span> global reviews</p>

                <?php for($i = 5; $i > 0; $i--) :?>
                    <div class="review-breakdown d-flex align-items-center justify-content-between mb-3">
                        <p class="num-stars"><?php echo $i;?> Stars</p>

                        <div class="percentage-bar-container">
                            <!-- percentage-bar width === star-percentage value -->
                            <div class="percentage-bar h-100" style="width: <?php echo $args->getStarPct($i);?>%;"></div>
                        </div>

                        <p class="star-percentage"><?php echo $args->getStarPct($i);?>%</p>
                    </div>
                <?php endfor; ?>

            </div> <!-- modal-body -->

        </div> <!-- modal-content -->
    </div> <!-- modal-dialog -->
</div> <!-- snack-rating-modal -->