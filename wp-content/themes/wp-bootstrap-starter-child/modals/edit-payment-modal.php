<!-- Edit Payment Modal -->
<div class="edit-payment-modal modal fade" id="editPaymentModal-<?php echo $args;?>" tabindex="-1"
                aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 p-lg-4 d-flex flex-column align-items-center">
            <div class="modal-header p-0 w-100 align-items-center">
                <h5 class="modal-title mb-3 h5 h4-lg font-weight-bold" id="editPaymentModalLabel">Add New Payment</h5>
                <button type="button" class="close p-0 mb-0 mr-0" data-dismiss="modal" aria-label="Close">
                    <span class="h2 h1-lg text-secondary" aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="" id="newCustomerCard" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                <div class="modal-body w-100 px-0">
                    <div class="align-items-center justify-content-between container mx-auto w-100 p-0">
                        <div class="row">
                            <span class="form-error"></span>
                            <span class="form-error-1"></span>
                            <span class="form-error-2"></span>
                            <span class="form-error-3"></span>
                            <span class="form-error-4"></span>
                            <div class="mt-3 mb-2 mb-lg-3 d-flex justify-content-end col-12">
                                <img alt="Visa, Mastercard, American Express, and Discover credit card logos
                                " class="w-50 credit-cards-img " src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/credit-cards.png">
                            </div>
                            <div class="col-12 pl-md-2">
                                <label class="sr-only d-none d-md-block" for="name">Name</label>
                                <input 
                                    id="ccNameUpdate" 
                                    class="position-relative form-control col-12" 
                                    name="name" 
                                    type="text" 
                                    placeholder="Full Name" 
                                    required 
                                    value="<?php echo Cart::getBillingAddress()['shipping_name'];?>"
                                    >
                            </div>

                            <!--
                            <div class="col-12 pl-md-2">
                                <label class="sr-only d-none d-md-block" for="card_number">Card Number</label>
                                <input id="ccNumberUpdate" class="position-relative form-control" name="card_number" type="text" placeholder="Card Number" required>
                            </div>

                            <div class="col-6 pl-md-2 text-nowrap">
                                <label class="sr-only d-none d-md-block" for="expiration_month">Expiration Month</label>
                                <input id="ccExpMUpdate" class="form-control col-6 mx-0 d-inline" name="expiration_month" type="text" placeholder="MM" required>
                            
                                <label class="sr-only d-none d-md-block" for="expiration_year">Expiration Year</label>
                                <input id="ccExpYUpdate" class="form-control col-6 mx-0 d-inline" name="expiration_year" type="text" placeholder="YY" required>
                            </div>

                            <div class="col-6 pl-md-2">
                                <label class="sr-only d-none d-md-block" for="cvv">CVV</label>
                                <input id="ccCvvUpdate" class="form-control" name="cvv" type="text" placeholder="CVV" required>
                            </div>
                            -->

                            <div class="col-12 pl-md-2 credit-card-input d-block">
                                        
                                <div id="card-element">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <div id="card-errors" class="text-white bg-danger px-3 py-1" role="alert"></div>
                                
                            </div>
                        </div>
                    </div>
                </div> <!-- end modal-body -->

                <div class="modal-footer align-items-center p-0 border-0 w-100">
                    <div class="container mx-auto w-100">
                        <div class="row">
                            <!-- saves payment changes & closes the modal -->
                            <button id="savePaymentChange" type="submit" class="h6 h5-lg mx-0 mt-2 mb-2 btn btn-secondary text-white float-left font-weight-semibold w-100 col-6">
                                Save
                            </button>

                            <button href="#" type="button" class="btn h6 h5-lg close p-0 mb-0 mr-0 col-6 text-right" data-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="card_id" value="<?php echo $args;?>" />
                    <input type="hidden" name="action" value="sc_update_payment_method" />
                </div>
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end edit-payment-modal -->