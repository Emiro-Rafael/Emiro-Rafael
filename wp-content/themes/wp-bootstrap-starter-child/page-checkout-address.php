<?php
/**
 * The template for displaying Checkout Address
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Checkout-address
 */

get_header();
?>

<section id="primary" class="content-area mb-5">    
    <div class="checkout-address py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) ) ;?>"><span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> Back to Cart</a>

            <div class="form-container mx-auto mt-4">
                <h1 class="h4 h3-md h1-xl font-weight-bold mb-3 mb-md-4">Address</h1>

                <form name="checkoutAddressForm" class="checkout-address-form" action="<?php echo get_permalink( get_page_by_path( 'checkout/checkout-confirm-pay' ) );?>" method="POST">

                    <span class="form-error-1"></span>
                    <span class="form-error-2"></span>

                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_address_1">Address Line 1</label>
                        <input type="text" name="user_address_1" id="user_address_1" class="input" value="" placeholder="Address Line 1">
                    </div>

                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_address_2">Address Line 2</label>
                        <input type="text" name="user_address_2" id="user_address_2" class="input" value="" placeholder="Address Line 2">
                    </div>
                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_city">City</label>
                        <input type="text" name="user_city" id="user_city" class="input" value="" placeholder="City">
                    </div>
                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_state">State</label>
                        <select name="user_state" id="user_state" required>
                            <option value="" disabled selected hidden>State</option>
                            <option value="AL">AL</option>
                            <option value="AK">AK</option>
                            <option value="AR">AR</option>	
                            <option value="AZ">AZ</option>
                            <option value="CA">CA</option>
                            <option value="CO">CO</option>
                            <option value="CT">CT</option>
                            <option value="DC">DC</option>
                            <option value="DE">DE</option>
                            <option value="FL">FL</option>
                            <option value="GA">GA</option>
                            <option value="HI">HI</option>
                            <option value="IA">IA</option>	
                            <option value="ID">ID</option>
                            <option value="IL">IL</option>
                            <option value="IN">IN</option>
                            <option value="KS">KS</option>
                            <option value="KY">KY</option>
                            <option value="LA">LA</option>
                            <option value="MA">MA</option>
                            <option value="MD">MD</option>
                            <option value="ME">ME</option>
                            <option value="MI">MI</option>
                            <option value="MN">MN</option>
                            <option value="MO">MO</option>	
                            <option value="MS">MS</option>
                            <option value="MT">MT</option>
                            <option value="NC">NC</option>	
                            <option value="NE">NE</option>
                            <option value="NH">NH</option>
                            <option value="NJ">NJ</option>
                            <option value="NM">NM</option>			
                            <option value="NV">NV</option>
                            <option value="NY">NY</option>
                            <option value="ND">ND</option>
                            <option value="OH">OH</option>
                            <option value="OK">OK</option>
                            <option value="OR">OR</option>
                            <option value="PA">PA</option>
                            <option value="RI">RI</option>
                            <option value="SC">SC</option>
                            <option value="SD">SD</option>
                            <option value="TN">TN</option>
                            <option value="TX">TX</option>
                            <option value="UT">UT</option>
                            <option value="VT">VT</option>
                            <option value="VA">VA</option>
                            <option value="WA">WA</option>
                            <option value="WI">WI</option>	
                            <option value="WV">WV</option>
                            <option value="WY">WY</option>
                        </select>
                    </div>
                    <div class="guest-email mb-2">
                        <label class="sr-only" for="user_zip">Zipcode</label>
                        <input type="text" name="user_zip" id="user_zip" class="input" value="" placeholder="Zipcode">
                    </div>
                    
                    <div class="guest-submit">
                        <input type="submit" name="wp-submit" id="wp-submit" class="h6-md py-md-2 btn btn-sm btn-secondary font-weight-semibold text-white w-100" value="Next: Confirm and Pay">
                        <input type="hidden" name="action" value="sc_checkout_address">
                        <input type="hidden" name="email" value="<?php echo $_POST['user_email'];?>">
                        <input type="hidden" name="first_name" value="<?php echo $_POST['user_first_name'];?>">
                        <input type="hidden" name="last_name" value="<?php echo $_POST['user_last_name'];?>">
                    </div>
                    
                </form>
            </div> <!-- form-container -->

        </div> <!-- container -->

    </div><!-- checkout-guest -->
</section><!-- #primary -->

<?php get_footer();?>