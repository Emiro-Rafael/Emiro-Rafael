<?php
/**
 * The template for displaying Guest Checkout
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Checkout-guest
 */

get_header();
?>

<section id="primary" class="content-area mb-5">    
    <div class="checkout-guest py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) ) ;?>" style="max-width: fit-content;">
                <span class="text-primary mb-0 mr-1 mr-md-2"><i class="fas fa-arrow-circle-left"></i></span> Back to Cart
            </a>

            <a role="button" data-toggle="modal" data-target="#signinModal" class="signInBtn h7 d-block d-lg-none text-uppercase my-4 font-weight-semibold">
                    Sign in and checkout faster
            </a>
        </div>
        <div class="container main-container">

            <div class="d-lg-flex justify-content-between">
                <div class="form-container col-lg-7 mx-auto mx-lg-0 mt-md-4 px-0 pl-lg-3">
                    <form name="guestCheckoutForm" class="guest-checkout-form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                        <h3 class="h4 h3-md h2-xl  text-gray-dark mb-md-3 font-weight-bold">Shipping Address</h3>
                        
                        <span class="form-error-1"></span>
                        <span class="form-error-2"></span>

                        <div id="shipping_block" class="row mb-md-5 my-3">
                            <div class="mb-2 mb-md-1 col-12 col-md-6">
                                <label class="sr-only" for="firstname">First Name</label>
                                <input type="text" name="firstname" id="firstname" class="input" value="" placeholder="First Name" required>
                            </div>

                            <div class="mb-2 mb-md-1 col-12 col-md-6">
                                <label class="sr-only" for="lastname">Last Name</label>
                                <input type="text" name="lastname" id="lastname" class="input" value="" placeholder="Last Name" required>
                            </div>

                            <div class="mb-2 mb-md-1 col-12">
                                <label class="sr-only" for="address_1">Address Line 1</label>
                                <input type="text" name="address_1" id="address_1" class="input" value="" placeholder="Address Line 1" required>
                            </div>

                            <div class="mb-2 mb-md-1 col-12">
                                <label class="sr-only" for="address_2">Address Line 2</label>
                                <input type="text" name="address_2" id="address_2" class="input" value="" placeholder="Address Line 2">
                            </div>

                            <div class="mb-2 mb-md-1 col-12 col-md-6">
                                <label class="sr-only" for="city">City</label>
                                <input type="text" name="city" id="city" class="input" value="" placeholder="City" required>
                            </div>

                            <div class="mb-2 mb-md-1 col-12 col-md-6">

                                <?php get_template_part( 'template-parts/state-dropdown', get_post_format(), array( "state", '' ) );?>
                                
                            </div>

                            <div class="mb-2 mb-md-1 col-12 col-md-6">
                                <label class="sr-only" for="zipcode">Postal</label>
                                <input type="text" name="zipcode" id="zipcode" class="input" value="" placeholder="Postal" required>
                            </div>
                            
                            <div class="mb-2 mb-md-1 col-12 col-md-6">
                                <label class="sr-only" for="country">Country</label>
                                <input type="text" readonly name="country" id="country" class="input" value="United States of America" placeholder="Country" required>
                            </div>
                        </div>

                        <div id="contact_block" class="mb-md-5 my-3">
                            <!-- Contact Info Block -->
                            <h3 class="h4 h3-md h2-xl  text-gray-dark mb-md-3 font-weight-bold">Contact Details</h3>
                            <p class="text-gray mb-3">We'll use this information to keep you informed about your delivery.</p>

                            <span class="form-error-3"></span>
                            <span class="form-error-4"></span>
                            
                            <div class="row">
                                <div class="d-none col-12 text-danger email-exists-error"></div>
                                <div class="mb-2 mb-md-1 col-12 col-md-6">
                                    <label class="sr-only" for="email">Email</label>
                                    <input type="text" name="email" id="email" class="input" value="" placeholder="Email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-2 mb-md-1 col-12 col-md-6">
                                    <label class="sr-only" for="phone">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="input" value="" placeholder="Phone Number" required>
                                </div>
                            </div>
                        </div>

                        <?php get_template_part( 'template-parts/billing-address-block', get_post_format() );?>

                        <?php get_template_part( 'template-parts/optin-block', get_post_format() );?>

                        <div class="my-3">
                            <div class="mb-2 col-md-6 p-0 guest-submit">
                                <button type="submit" class="btn btn-secondary d-block text-white w-100">Review and Pay</button>
                                <input type="hidden" name="action" value="sc_checkout_guest" />
                            </div>
                        </div>

                    </form>
                </div>

                <div class="d-none d-lg-block col-lg-5 sign-in-summary">
                    <div class="p-4 card my-4" id="guestCheckoutSignin">
                        <h3 class="h4 h3-md h2-xl font-weight-semibold mb-0">Sign In</h3>
                        <?php get_template_part( 'partials/login-form' );?>
                    </div>

                    <?php get_template_part( 'template-parts/order-summary', get_post_format(), '' );?>
                </div>

            </div>
            <!-- the old stuff -->
            <?php
            /*
            <div class="form-container mx-auto mt-4">
                
                <?php if(@$_GET['expired'] == 1):?>
                    <p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">You have been logged out due to inactivity. Your cart is saved, and you can continue as a guest or log back in.</p>
                <?php endif;?>

                <form name="guestCheckoutForm" class="guest-checkout-form" action="<?php echo get_permalink( get_page_by_path(' checkout/checkout-address ') );?>" method="POST">

                    <h1 class="h6 font-weight-medium text-gray-dark mb-md-3">Continue Manually:</h1>

                    <span class="form-error-1"></span>
                    <span class="form-error-2"></span>

                    <div class="guest-name d-flex align-items-center justify-content-between mb-2">
                        <div class="p-0 mr-3">
                            <label class="sr-only" for="user_first_name">First Name</label>
                            <input type="text" name="user_first_name" id="user_first_name" class="input" value="" placeholder="First Name">
                        </div>

                        <div class="p-0">
                            <label class="sr-only" for="user_last_name">Last Name</label>
                            <input type="text" name="user_last_name" id="user_last_name" class="input" value="" placeholder="Last Name">
                        </div>
                    </div>

                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_email">Email Address</label>
                        <input type="text" name="user_email" id="user_email" class="input" value="" placeholder="Email Address">
                    </div>
                    
                    <div class="guest-submit">
                        <input type="submit" name="wp-submit" id="wp-submit" class="h6-md py-md-2 btn btn-sm btn-secondary font-weight-semibold text-white w-100" value="Next: Shipping">
                        <!--<input type="hidden" name="action" value="sc_checkout_guest">-->
                    </div>
                    
                </form>
            </div> <!-- form-container -->
            */
            ?>
        </div> <!-- container -->

    </div><!-- checkout-guest -->
</section><!-- #primary -->

<?php get_footer();?>