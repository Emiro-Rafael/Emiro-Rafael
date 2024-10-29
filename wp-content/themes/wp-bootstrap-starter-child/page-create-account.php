<?php
/**
 * The template for displaying Create Account
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Create-account
 */

get_header();
?>
<section id="primary" class="content-area mb-5">
    <div class="create-account py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg h4-xl font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_bloginfo('url')?>">
                <span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> 
                Back to CandyBar
            </a>

            <div class="form-container mx-auto mt-4">
                
                <h3 class="h4 h3-md h1-xl font-weight-bold">Create Account</h3>
                
                <p class="mb-4 text-gray">Keep up with all of you CandyBar orders and make checkout for all future orders even faster.</p>

                <form name="guestCheckoutForm" class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                    
                    <div class="sign-in-email my-4">
                        <h5 class="h5 text-bold">Email Address:</h5>
                        <div class="text-gray"><?php echo $_SESSION['checkout']['user_info']['email'];?></div>
                    </div>
                    
                    <div class="sign-in-pwd my-4">
                        <h5 class="h5 text-bold">Create Password:</h5>
                        <input class="input" type="password" name="pwd" />
                    </div>

                    <div class="sign-in-btn mb-2">
                        <button type="submit" class="btn btn-secondary text-white">Create Account</button>

                        <input type="hidden" name="action" value="sc_create_account" />
                        <input type="hidden" name="customer_id" value="<?php echo $_SESSION['checkout']['user_info']['customer_id'];?>" />
                        <input type="hidden" name="email" value="<?php echo $_SESSION['checkout']['user_info']['email'];?>" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>