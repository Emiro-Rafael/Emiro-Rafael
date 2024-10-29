<?php
/**
 * The template for displaying Confirm & Pay
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Checkout-confirm-pay
 */
get_header();
if( empty($_SESSION) || empty($_SESSION['cart']) || empty($_SESSION['checkout']) || empty($_SESSION['checkout']['user_info']) || empty($_SESSION['checkout']['user_info']['email']) )
{
    echo '<script>window.location.href = "' . get_permalink( get_page_by_path( 'shopping-cart' ) ) . '?session_expired=1";</script>';
}

$email = $_SESSION['checkout']['user_info']['email'];
$cart = new Cart();
$user = new User($email);

$default_payment_id = $user->getDefaultPaymentId();
if( @$_GET['p'] )
{
    $default_payment_id = base64_decode( $_GET['p'] );
}
?>

<section id="primary" class="content-area mb-5">
    <div class="confirm-pay py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_permalink( get_page_by_path( 'checkout/checkout-confirm-shipping' ) ) ;?>" style="width: fit-content;"><span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> Back to Shipping</a>
            
                <div class="confirm-pay-container d-md-flex flex-column flex-lg-row align-items-center align-items-lg-start justify-content-between mx-auto mt-4">
                    <div class="payment-container mr-md-4 mr-lg-5 w-100">
                        <h1 class="h4 h3-md h2-xl font-weight-bold mb-0 mb-md-3">Payment Method</h1>

                        <p class="h7 h6-lg text-gray d-none d-md-block mb-4 mb-xl-5">
                            All transactions are safe and secure.
                        </p>
                        <form id="confirmPaymentForm" class="" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                            <div class="quick-checkout">
                                <div id="apple-pay-window">
                                    <div id="payment-request-button-applepay" class="my-4 my-lg-5">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                
                                    <div class="d-flex align-items-center justify-content-between w-100 my-2 my-md-3 my-lg-5">
                                        <div class="border-bottom w-100"></div>
                                        <p class="h7 px-2 bg-white text-gray font-weight-medium mb-0">Or</p>
                                        <div class="border-bottom w-100"></div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!$user->isStripeCustomer()):?>

                                <!-- guest-pay should be visible if user is not signed into an existing account -->
                                <div class="guest-pay mb-3">

                                    <span class="form-error"></span>
                                    <span class="form-error-1"></span>
                                    <span class="form-error-2"></span>
                                    <span class="form-error-3"></span>
                                    <span class="form-error-4"></span>

                                    <div class="mt-3 mb-2 mb-lg-3 d-flex justify-content-end">
                                        <img alt="Visa, Mastercard, American Express, and Discover credit card logos
                                        " class="w-50 credit-cards-img " src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/credit-cards.png">
                                    </div>

                                    <div class="credit-card-input d-block">
                                        
                                        <div id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                        <!-- Used to display Element errors. -->
                                        <div id="card-errors" class="text-white bg-danger px-3 py-1" role="alert"></div>

                                    </div>

                                    <div class="norton-logo mx-auto mt-3 mb-4 d-flex justify-content-center d-md-none">
                                        <img alt="Norton Secured" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/norton-secured-pbd.svg">
                                    </div>
                                </div> <!-- guest-pay -->

                            <?php 
                                else:
                                
                                    $user->setPaymentsInfo();
                                    $cards = $user->getPaymentsInfo();
                                    $cart->checkForLogout();
                            ?>

                                <div class="card-swiper container position-relative px-0 mb-md-3 mb-xl-5">
                                    <div class="card-swiper-container swiper-container container px-1 py-4 p-md-0 mx-auto">
                                        <div class="card-swiper">
                                            <div class="swiper-wrapper cards row px-md-2 pl-1">
                                                <?php 
                                                foreach( $cards as $card )
                                                {
                                                    get_template_part( 'template-parts/card-card', get_post_format(), array( $card, $default_payment_id ) );
                                                }
                                                ?>

                                                <div class="card-container p-2 swiper-slide" data-card="0">
                                                    <div class="card p-2 equalHeight px-3 p-xl-4 justify-content-between" data-toggle="modal" data-target="#editPaymentModal-0" >
                                                        <div class="font-weight-bold">New Card</div>
                                                        <div class="my-2">
                                                            <div class="">&nbsp;</div>
                                                            <div>&nbsp;</div>
                                                            <div>&nbsp;</div>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray h6">
                                                                <i class="fas fa-plus"></i>
                                                            </span>
                                                        </div>

                                                        <i class="selected-check fas fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php /*
                                <!-- customer-pay should be visible if user is signed into an existing account -->
                                <div class="customer-pay d-md-flex justify-content-between align-items-end">
                                    <div class="d-flex flex-column align-items-center align-items-md-start my-3 mt-md-0">
                                        <h3 class="h6 font-weight-bold text-gray mb-md-3">Your Card:</h3>
                                        <div class="customer-card d-flex align-items-center justify-content-center mb-2">
                                            <div class="card-icon h5 mb-0">
                                                <i class="fas fa-credit-card"></i>
                                            </div>

                                            <!-- customer card from their account -->
                                            <p class="h6 font-weight-semibold mb-0 mx-3"><span><?php echo $card->brand;?></span> <span><?php echo $card->last4;?></span></p>

                                            <div class="checkmark text-success">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <p class="h6 font-weight-medium text-gray mb-0">Or <span class="edit-payment-btn text-primary" type="button" data-toggle="modal" data-target="#editPaymentModal">Add new card</span></p>
                                    </div>

                                    <div class="norton-logo mx-auto mt-3 mb-4 d-flex justify-content-center d-md-block mx-md-0 mb-md-3">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/norton-secured-pbd.svg">
                                    </div>

                                    <input type="hidden" name="shipping_address" value="" />
                                </div> <!-- customer-pay -->
                                    */?>
                            <?php endif;?>

                            <?php if($user->isStripeCustomer()):?>
                                <div class="loggedInCardErrors text-white px-3 py-1"></div>

                                <?php endif;?>
                            
                                <div class="place-order bg-white d-flex flex-md-column align-items-center justify-content-between justify-content-sm-center p-2 px-md-0">
                                    <div class="d-flex d-md-none flex-column align-items-start flex-sm-row align-item-sm-center justify-content-sm-start my-1">
                                        <h5 class="h7 h5-lg h4-xl font-weight-medium mb-1 mb-sm-0">Total &#40;<span><?php echo Cart::getCartNumber();?></span> items&#41;:</h5>
                                        <h6 class="h7 h5-lg h4-xl font-weight-bold mb-0 ml-sm-2">$<span><?php echo $cart->getTotal();?></span></h6>
                                    </div>
                                
                                    <button class="btn btn-secondary text-white h7 h6-md h5-xl px-4 px-xl-5 ml-sm-4 ml-md-0 placeOrderCheckout" type="submit">Place Order</button>
                                    <input type="hidden" name="total_amount" value="<?php echo $cart->getTotal();?>" />
                                    <input type="hidden" name="action" value="sc_confirm_payment" />
                                    <input type="hidden" name="source" value="<?php echo $default_payment_id;?>" />
                                
                                    <p class="text-gray small d-none d-md-block mt-3 align-self-start">
                                        By clicking Place Order you agree to the <a class="text-gray text-decoration-underline" href="<?php echo get_bloginfo('url')?>/terms-of-use/" target="_blank">Terms &amp; Conditions</a>
                                    </p>
                                    <div class="norton-logo mx-auto mt-3 mb-4 d-md-flex justify-content-center d-none">
                                        <img alt="Norton Secured" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/norton-secured-pbd.svg">
                                    </div>
                                </div> <!-- place-order -->
                        </form>
                        <!-- <div class="mt-3" style="opacity: 0;">
                            <h4 class="h6 font-weight-semibold">Pause, skip, or cancel anytime.</h4>
                            <p class="h8 text-gray font-weight-medium">By clicking “Place Order,” you agree you are purchasing a continuous subscription and will receive deliveries billed to your designated payment method until you cancel. You may pause or cancel your subscription at any time, based on the date for your next delivery on your account page.</p>
                        </div> -->
                    </div>

                    <?php get_template_part( 'template-parts/order-summary', get_post_format(), '' );?>

                    
                </div> <!-- confirm-pay-container -->
        </div> <!-- container -->

    </div><!-- confirm-pay -->
</section><!-- #primary -->

<?php 
if( !empty($cards) )
{
    /*
    foreach( $cards as $card )
    {
        get_template_part( 'modals/edit-payment-modal', get_post_format(), $card->id );
    }
    */
    get_template_part( 'modals/edit-payment-modal', get_post_format(), 0 );
}
/*
get_template_part( 'modals/edit-shipping-address-modal', get_post_format(), 'shipping' );
if( !empty($_SESSION['checkout']['address']['billing']) )
{
    get_template_part( 'modals/edit-shipping-address-modal', get_post_format(), 'billing' );
}
*/

get_footer();
?>