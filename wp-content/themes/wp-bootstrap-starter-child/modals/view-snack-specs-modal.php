<?php
/**
 * Nutrional lable modal
 * Pass in snack model object
 */
?>
<!-- View Snack Specs Modal -->
<div class="view-snack-specs-modal modal fade" id="viewSpecsModal" tabindex="-1"
                aria-labelledby="viewSpecsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 p-lg-4 d-flex flex-column align-items-center mt-5">
            <div class="modal-header p-0 w-100 align-items-center border-0">
                <button type="button" class="close p-0 pt-2 mr-0" data-dismiss="modal" aria-label="Close">
                    <span class="h2 h1-lg" aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body w-100 px-0 d-flex justify-content-center">
                <img class="img-fluid" alt="snack nutrional info" src="<?php echo $args->getNutritionalLabel();?>">
            </div> <!-- end modal-body -->

        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end edit-payment-modal -->