<div class="modal fade snack-poll-modal" id="snackPollModal" tabindex="-1" role="dialog" aria-labelledby="snackPollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered m-0 w-100 px-3" role="document">
        <div class="modal-content d-flex flex-column w-100 my-0 mx-auto border-0">
            <div class="modal-header position-relative border-0 bg-primary justify-content-center">
                <h4 class="h4 h3-md h2-xl text-white text-center font-weight-bolder mb-0">Pick the <span id="snackPollCategory">Best</span> Snack</h4>
                <button type="button" class="close-snack-poll-modal position-absolute p-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> <!-- modal-header -->
            <form class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" id="snackSuperlativeForm" method="POST">
                <div class="modal-body p-4">
                    <?php foreach( $args->getSnacksBySize() as $snack ) : ?>
                        <button type="button" class="single-snack" data-snackid="<?php echo $snack->ID;?>">
                            <img loading="lazy" class="img-fluid" alt="image of <?php echo get_post_meta($snack->ID, 'user-friendly-name', true);?>" src="<?php echo get_the_post_thumbnail_url( $snack->ID, 'full' );?>">
                            <h5><?php echo get_post_meta($snack->ID, 'user-friendly-name', true);?></h5>
                        </button>
                    <?php endforeach;?>
                </div> <!-- modal-body -->
                <input type="hidden" name="action" value="save_snack_superlative" />
                <input type="hidden" name="snack_id" value="" />
                <input type="hidden" name="post_id" value="<?php echo $args->getId();?>" />
                <input type="hidden" name="type" value="" />
            </form>
        </div> <!-- modal-content -->
    </div> <!-- modal-dialog -->
</div> <!-- snack-poll-modal -->