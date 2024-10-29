<?php

if( empty($args) )
{
    $first_name = '';
    $last_name = '';
    $user_address_1 = '';
    $user_address_2 = '';
    $user_city = '';
    $user_state = '';
    $user_zip = '';
    $user_phone = '';

    $id = 0;
}
elseif( $args == 'shipping' || $args == 'billing' ) // this is one of the modals on confirm payment page
{
    $key = $args;

    $first_name = substr( $_SESSION['checkout']['address'][$key]['shipping_name'], 0, strpos( $_SESSION['checkout']['address'][$key]['shipping_name'], ' ') );
    $last_name = substr( $_SESSION['checkout']['address'][$key]['shipping_name'], strpos( $_SESSION['checkout']['address'][$key]['shipping_name'], ' ') );
    $user_address_1 = $_SESSION['checkout']['address'][$key]['address_1'];
    $user_address_2 = $_SESSION['checkout']['address'][$key]['address_2'];
    $user_city = $_SESSION['checkout']['address'][$key]['city'];
    $user_state = $_SESSION['checkout']['address'][$key]['state'];
    $user_zip = $_SESSION['checkout']['address'][$key]['zip'];
    $user_phone = $_SESSION['checkout']['address'][$key]['phone'];

    $id = $args;
}
else
{
    $first_name = substr( $args->shipping_name, 0, strpos( $args->shipping_name, ' ') );
    $last_name = substr( $args->shipping_name, strpos( $args->shipping_name, ' ') );
    $user_address_1 = $args->address_1;
    $user_address_2 = $args->address_2;
    $user_city = $args->city;
    $user_state = $args->state;
    $user_zip = $args->zipcode;
    $user_phone = $args->phone;

    $id = $args->id;
}
?>
<!-- Edit Shipping Address Modal -->
<div class="edit-shipping-address-modal modal fade" id="editShippingAddressModal-<?php echo $id;?>" tabindex="-1"
                aria-labelledby="editShippingAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 p-lg-4 d-flex flex-column align-items-center">
            <div class="modal-header p-0 w-100 align-items-center">
                <h5 class="modal-title mb-3 h5 h4-lg font-weight-bold" id="editShippingAddressModalLabel">Add New Address</h5>
                <button type="button" class="close p-0 mb-0 mr-0" data-dismiss="modal" aria-label="Close">
                    <span class="h2 h1-lg text-secondary" aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="ajax_form" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                <div class="modal-body mt-3 p-0">
                    <div class="row mb-2">
                        <div class="col-12 col-md-6 pr-md-0">
                            <label class="d-none d-md-block" for="userFirstNameEdit">First Name</label>
                            <input id="userFirstNameEdit" class="form-control" name="first_name" type="text" value="<?php echo $first_name;?>" placeholder="First Name" required>
                        </div>

                        <div class="col-12 col-md-6 pl-md-2">
                            <label class="d-none d-md-block" for="userLastNameEdit">Last Name</label>
                            <input id="userLastNameEdit" class="form-control" name="last_name" type="text" value="<?php echo $last_name;?>" placeholder="Last Name" required>
                        </div>

                        <div class="col-12 col-md-8 pr-md-0">
                            <label class="d-none d-md-block" for="userStreetEdit">Street Address</label>
                            <input id="userStreetEdit" class="form-control" name="address_1" type="text" value="<?php echo $user_address_1;?>" placeholder="Street Address" required>
                        </div>

                        <div class="col-12 col-md-4 pl-md-2">
                            <label class="d-none d-md-block" for="userStreet2Edit">Apt/Suite</label>
                            <input id="userStreet2Edit" class="form-control" name="address_2" type="text" value="<?php echo $user_address_2;?>" placeholder="Apt/Suite">
                        </div>

                        <div class="col-12 col-md-4 pr-md-0">
                            <label class="d-none d-md-block" for="userCityEdit">City</label>
                            <input id="userCityEdit" class="form-control" name="city" type="text" value="<?php echo $user_city;?>" placeholder="City" required>
                        </div>

                        <div class="col-12 col-md-4 pr-md-0 pl-md-2">
                            <label class="d-none d-md-block" for="state">State</label>
                            <?php get_template_part( 'template-parts/state-dropdown', get_post_format(), array( "state", $user_state ) );?>
                            
                        </div>

                        <div class="col-12 col-md-4 pl-md-2">
                            <label class="d-none d-md-block" for="userZipEdit">Zip</label>
                            <input id="userZipEdit" class="form-control" name="zip" type="text" value="<?php echo $user_zip;?>" placeholder="Zip" required>
                        </div>

                        <div class="col-12 col-md-4 my-3">
                            <span class="font-weight-bold">Country:</span> USA
                        </div>

                        <div class="col-12 col-md-8 pl-md-2">
                            <label class="d-none d-md-block" for="userPhoneEdit">Phone</label>
                            <input id="userPhoneEdit" class="form-control" name="phone" type="text" value="<?php echo $user_phone;?>" placeholder="Phone" required>
                        </div>

                    </div>
                </div> <!-- end modal-body -->

                <div class="modal-footer d-flex flex-column align-items-center p-0 border-0 w-100">

                    <p class="h8 text-center text-gray">You may be charged local sales tax or VAT, if applicable.</p>

                    <!-- saves shipping address changes & closes the modal -->
                    <button id="saveAddressChange" type="submit" class="h6 h5-lg mx-0 mt-3 mb-2 btn btn-secondary text-white font-weight-semibold w-100">
                        Save
                    </button>

                    <input type="hidden" name="address_id" value="<?php echo $id;?>" />
                    <?php if($args == 'shipping' || $args == 'billing') :?>
                        <input type="hidden" name="action" value="confirm_pay_shipping_edit" />
                    <?php else:?>
                        <input type="hidden" name="action" value="member_shipping_edit" />
                    <?php endif;?>
                </div>
            </form>
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end edit-shipping-address-modal -->