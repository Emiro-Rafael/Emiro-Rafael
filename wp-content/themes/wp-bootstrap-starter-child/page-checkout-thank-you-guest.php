<?php
/* Template Name: Checkout-thank-you-guest */

get_header();
get_template_part( 'template-parts/facebook-checkout-pixel' );
?>

<section id="primary" class="content-area"><section>
	<div class="thank-you mt-4 mt-xl-5">
		<!-- background images mobile -->
		<div class="bg-mobile d-md-none position-absolute w-100">
			<div class="position-relative w-100 h-100">
				<span class="position-absolute h-100"></span>
				<span class="position-absolute w-100 h-100"></span>
				<span class="position-absolute w-100 h-100"></span>
			</div>
		</div>

		<!-- background images desktop -->
		<div class="bg-desktop d-none d-md-block position-absolute w-100">
			<div class="position-relative w-100 h-100">
				<span class="position-absolute w-100 h-100"></span>
				<span class="position-absolute w-100 h-100"></span>
			</div>
		</div>

		<div class="container mx-auto d-flex flex-column align-items-center justify-content-between text-dark">
			<div class="d-flex flex-column align-items-center px-2 py-md-2 mt-2 mt-md-0 mb-auto">
				<div class="candybar-logo mb-4 w-100">
					<img class="w-100" alt="CandyBar logo" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo/candybar-logo.svg">
				</div>
				<h1 class="h1 display-5-md display-4-xl text-center text-uppercase font-weight-bold mx-auto mb-3">Thank you!</h1>

				<p class="h6 h5-md h4-xl text-center mx-auto font-weight-medium mb-4 mb-md-5">Your order has been placed. We just sent you an email confirmation, and you’ll receive another email once your order has shipped.</p>

				<a class="h6 h5-md h4-xl text-center text-secondary font-weight-semibold mx-auto mb-3 mb-md-4 w-100 viewAccount" href="<?php echo get_permalink( get_page_by_path( 'create-account' ) );?>">Create Account</a>
			</div>

			<div class="social-links d-flex flex-column align-items-center flex-md-row justify-content-md-center mt-auto mb-md-5 text-dark">
				<h6 class="text-center font-weight-semibold mb-0 mr-md-3">Follow Us:</h6>
				<div>
					<a class="display-5 text-dark mx-2 text-decoration-none" target="_blank" href="https://instagram.com/snackcrate">
						<i class="fab fa-instagram"></i>
					</a>
					<a class="display-5 text-dark mx-2 text-decoration-none" target="_blank" href="https://www.facebook.com/snackcrate">
						<i class="fab fa-facebook"></i>
					</a>
				</div>
			</div>

		</div>

	</div>
</section>

<?php get_footer() ?>