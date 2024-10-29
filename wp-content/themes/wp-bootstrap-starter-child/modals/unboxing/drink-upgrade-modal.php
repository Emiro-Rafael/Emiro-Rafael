<?php
$subscription_data = User::getDrinklessSubscriptionData();
$currency_data = User::getCurrencyData();
$drink_price_string = $currency_data->unit . round($currency_data->drink_cost / 100, 2);

$pretty_names = array(
    '4Snack' => 'Mini',
    '8Snack' => 'Original',
    '16Snack' => 'Family'
);
?>
<!-- Drink Upgrade Modal -->
<div class="modal fade drink-upgrade-modal" id="drinkUpgradeModal" tabindex="-1" role="dialog" aria-labelledby="drinkUpgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3" role="document">
        <div class="modal-content d-flex flex-column w-100 my-0 mx-auto">
            <div class="modal-header position-relative border-bottom border-gray d-flex align-items-center justify-content-between p-0 mx-3">
                <h5 class="h6 modal-title font-weight-bold" id="drinkUpgradeModalLabel">Secure Checkout</h5>

                <button type="button" class="close-modal btn text-gray mr-n3 mb-0 h4" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="vid-container d-flex w-100 mb-3"> 
                    <video class="mx-auto w-100" id="countryLoop" preload="auto" playsinline autoplay muted loop>
                        <source src="/wp-content/uploads/2022/11/Drink_Checkout_1280_720_1.mp4" type="video/mp4">
                    </video>
                </div>

                <div class="upgrade-info border-bottom border-gray">
                    <div class="upgrade-title d-flex align-items-center justify-content-between mb-3 mx-auto">
                        <h6 class="h7 font-weight-bold text-dark mb-0">Monthly Drink Upgrade</h6>
                        <h6 class="h7 font-weight-medium text-gray mb-0"><?php echo $drink_price_string;?></h6>
                    </div>

                    <p class="h7 text-center mx-auto">This will be a recurring monthly upgrade to your current subscription. This will begin with your next order.</p>

                    <div class="form-error"></div>
                </div>
            </div> <!-- modal-body -->


            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary text-white font-weight-semibold h7 w-100 mb-3 mx-auto py-2" id="addDrinkUpgrade">Add Drink Upgrade</button>
                <input type="hidden" value="<?php echo $subscription_data->id;?>" name="drinkless_subscription_id" />
                <input type="hidden" value="<?php echo $subscription_data->plan;?>" name="drinkless_subscription_plan" />
                <input type="hidden" value="<?php echo $currency_data->currency_code;?>" name="user_currency_code" />
                <input type="hidden" value="<?php echo $subscription_data->subscription_id;?>" name="drinkless_subscription_stripe_id" />

                <p class="h9 font-weight-medium text-gray text-center mx-4 mx-md-auto">By clicking "Add Drink Upgrade" you agree that all future orders will be billed an additional <?php echo $drink_price_string;?> on the 5th of each month and you agree to our <a class="text-gray" target="_blank" href="/terms-of-use/">Terms of Use</a> policy</p>
            </div> <!-- modal-footer -->
        </div> <!-- modal-content -->
    </div> <!-- modal-dialog -->
</div>