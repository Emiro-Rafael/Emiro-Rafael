<?php
/**
 * The template for displaying Sign In
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Sign-in
 */

get_header();
?>

<section id="primary" class="content-area mb-5">    
    <div class="sign-in py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) ) ;?>"><span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> Back to Cart</a>

            <div class="form-container mx-auto mt-4">
                <h1 class="h4 h3-md h1-xl font-weight-bold mb-1">Sign-In</h1>
                <p class="h7 h6-md text-gray font-weight-medium mb-4">Already a customer?</p>

                <form name="signInForm" class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                    <p class="form-error"></p>

                    <div class="sign-in-email mb-2">
                        <label class="sr-only" for="user_login">Email Address</label>
                        <input type="text" name="log" id="user_login" class="input" value="" placeholder="Email Address">
                    </div>

                    <div id="signIn" class="sign-in-password position-relative mb-2">
                        <label class="sr-only" for="password">Password</label>
                        <input type="password" name="pwd" id="password" class="input" value="" placeholder="Password">
                        <div class="password-toggle text-gray position-absolute">
                            <label id="showPassword">
                                <i class="fas fa-eye"></i>
                                <input type="checkbox" class="d-none" aria-label="Checkbox for toggling password visibility">
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="sign-in-keep">
                            <input name="keepSignedIn" type="checkbox" id="keepSignedIn" value="">
                            <label for="keepSignedIn" class="text-capitalize position-relative ml-1"> Keep me signed in</label>
                        </div>

                        <a class="h7 h6-md text-gray font-weight-medium" href="#">Forgot password?</a>
                    </div>
                    
                    <div class="sign-in-submit">
                        <input type="submit" name="wp-submit" id="wp-submit" class="h6-md py-md-2 btn btn-sm btn-secondary font-weight-semibold text-white w-100" value="Sign In">
                        <input type="hidden" name="action" value="sc_login">
                    </div>

                    <div class="d-flex align-items-center justify-content-between w-100 mt-3 mb-2">
                        <div class="border-bottom w-100"></div>
                        <p class="h7 px-2 bg-white text-gray font-weight-medium mb-0">Or</p>
                        <div class="border-bottom w-100"></div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a class="checkout-guest h6 font-weight-semibold text-primary text-center mx-auto w-100" href="#">Checkout as Guest</a>
                    </div>
                    <input type="hidden" name="action" value="sc_login">
                </form>
            </div> <!-- form-container -->

        </div> <!-- container -->

    </div><!-- sign-in -->
</section><!-- #primary -->

<?php get_footer();?>