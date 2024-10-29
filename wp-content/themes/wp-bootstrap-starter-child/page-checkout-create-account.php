<?php
/**
 * The template for displaying Create Account
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Checkout-create-account
 */

get_header();
$data = json_decode( stripslashes($_POST['data']) );
?>

<section id="primary" class="content-area mb-5">    
    <div class="create-account py-3 py-md-4">
        <div class="container">

            <div class="form-container mx-auto mt-4">
                <h1 class="h4 h3-md h1-xl font-weight-bold mb-4 mb-md-5">Create Account</h1>
                
                <button 
                    id="appleid-signin" 
                    class="appleid conitnue h6-md btn btn-sm text-white w-100 mb-2 mb-md-3"
                    title="Continue with Apple"
                    >
                    
                    <i class="fab fa-apple mr-2"></i>Continue with Apple
                </button>
                <script>
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            for (var i = 0; i < mutation.addedNodes.length; i++) {
                                jQuery('#appleid-signin').html(`
                                                <i class="fab fa-apple" style="font-size: 14px;"></i>
                                                Continue with Apple
                                            `);
                                observer.disconnect();
                            }
                        });
                    });
                    observer.observe(document.getElementById("appleid-signin"), {
                        childList: true,
                        subtree: true
                    });
                </script>
                <div class="d-flex align-items-center justify-content-between w-100 mb-2 mb-md-3">
                    <div class="border-bottom w-100"></div>
                    <p class="h7 px-2 bg-white text-gray font-weight-medium mb-0">Or</p>
                    <div class="border-bottom w-100"></div>
                </div>
                <form name="createAccountForm" class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                    <span class="form-error"></span>
                    <span class="form-error-1"></span>
                    <span class="form-error-2"></span>
                    <span class="form-error-3"></span>

                    <div class="create-account-email mb-4">
                        <label class="" for="user_login">Email Address:</label>
                        <input type="text" name="log" id="user_email" class="input" value="<?php echo $data->email;?>" placeholder="Email Address">
                    </div>

                    <div class="create-account-password position-relative mb-3 mb-md-4">
                        <label class="" for="password">Create Password:</label>
                        <input type="text" name="pwd" id="user_password" class="input" value="" placeholder="Password">
                    </div>
                    
                    
                    <div class="create-account-submit">
                        <input type="submit" name="" id="" class="h6-md py-md-2 btn btn-sm btn-secondary font-weight-semibold text-white w-100" value="Create Account">
                        <input type="hidden" name="action" value="sc_create_account_checkout">
                        <input type="hidden" name="data" value="<?php echo htmlentities( stripslashes( $_POST['data'] ), ENT_QUOTES) ;?>">
                    </div>
                    
                </form>
                
                <hr />

                <form class="ajax_form" id="guestCheckout" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                    <div class="create-account-submit">
                        <input type="submit" name="" id="" class="h6-md py-md-2 btn btn-sm btn-primary font-weight-semibold text-white w-100" value="Checkout As Guest">
                        <input type="hidden" name="action" value="sc_final_checkout_guest">
                        <input type="hidden" name="data" value="<?php echo htmlentities( stripslashes( $_POST['data'] ), ENT_QUOTES) ;?>">
                    </div>
                </form>
            </div> <!-- form-container -->

        </div> <!-- container -->

    </div><!-- create-account -->
</section><!-- #primary -->

<?php get_footer();?>