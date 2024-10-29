<?php
/* Template Name: candybar-home */
get_header();
?>

<style>
	@media (max-width: 7687px) {
		.hero-content {
			margin-top: 15rem!important;
		}
	}
	/* CSS for desktop */
	.desktop-video {
		display: block; 
	}
	.mobile-video {
		display: none; 
	}

	/* Media query for small mobile screens */
	@media (max-width: 767px) {
		.desktop-video {
			display: none; 
		}
		.mobile-video {
			display: block; 
		}
	}
</style>
<!-- test comment-1 -->
	<section id="primary" class="content-area mb-5">

		<div class="home pb-lg-3 pb-xl-5">

			<div class="unboxing-wrapper">
				<section class="unboxing-hero position-relative d-flex flex-column justify-content-center">
 					<video class="country-loop position-absolute desktop-video" id="countryLoop" preload="auto" playsinline autoplay muted loop>
						<source src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2023_Skittles_Masthead_DESKTOP.webm" type="video/webm">
						<source src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2023_Skittles_Masthead_DESKTOP.mp4" type="video/mp4">
					</video>
					<video class="country-loop position-absolute mobile-video" id="countryLoopMobile" preload="auto" playsinline autoplay muted loop>
						<source src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2023_Skittles_Masthead_MOBILE.webm" type="video/webm">
						<source src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2023_Skittles_Masthead_MOBILE.mp4" type="video/mp4">
					</video>
 					<div class="hero-content position-relative mb-5 ml-4 ml-md-5 pl-2 pl-md-4 pl-xl-5">
 						<h3 class="text-uppercase text-white text-left font-weight-bolder mb-1 h5 h4-md h1-xl">Limited Edition:</h3>
 						<h1 class="colombia-text font-weight-bolder mb-4 text-left ml-n1">Skittles<br>Crate</h1>
 						<a href="/collections/british-skittles/" class="btn btn-secondary text-white h8 h6-md h5-xl d-flex justify-content-center align-items-center px-4 w-100">Shop Now</a>
			</div>
			</section>
		</div>

		<div class="shop-now world-tour container py-2 my-4 my-lg-5 d-md-flex align-items-center align-items-xl-end">

				<div class="shop-now-hero-container position-relative">

					<a class="d-block" href="/snack/uk-elite-upgrade-5pack/">
						<img class="shop-now-hero img-fluid" alt="Cadbury Image" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/SC_WEB_CandyBar_UK_Hero Card_50-min.png">
					</a>

					<a class="d-none btn btn-sm btn-secondary shadow text-white py-md-2 py-xl-3 mb-1 mx-auto w-100 h9 h7-sm h6-lg" href="/collections/british-skittles/">Shop Now</a>
				</div>

				<div class="shop-now-options-container ml-md-4 ml-xxl-5 mr-md-n2 mb-xl-n2">
					<div class="shop-now-text world-tour-text d-flex flex-column align-items-start justify-content-center pl-2 mb-2 mb-xxl-4">
						<h2 class="font-weight-bold mb-1 mb-xl-0 mt-3 mt-md-0">UK Elite Collection</h2>
						<a class="shop-now-link font-weight-semibold h8 h6-sm h4-xl" href="/snack/uk-elite-upgrade-5pack/">Shop Now</a>
					</div>
					<div class="shop-now-options mt-3 mt-md-0">
						<div class="single-shop-now-container">
							<!-- <a class="single-shop-now single-cd" href="/snack/cadbury-snack-pack/"> -->
							<a class="single-cd" href="/snack/doritos-chilli-heatwave-pack-2-2/">
								<!-- <img class="flag-img" alt="Flag of Hong Kong" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/HongKongWavyFlag.svg"> -->

								<!-- <img class="shop-now-img img-fluid" alt="Doritos Heatwave" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_UK_BOM_Doritos_HeatWave_50-min.png"> -->
								<img class="img-fluid rounded" alt="Doritos Heatwave" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_UK_BOM_Doritos_HeatWave_50-min.png">
								<!-- <p class="shop-now-title">Cadbury Dairy Milk Oreo Sandwich</p> -->
							</a>
						</div>

						<div class="single-shop-now-container">
							<!-- <a class="single-shop-now single-cd" href="/snack/crunchie-40g/"> -->
							<a class="single-cd" href="/snack/doritos-triple-cheese-pizza-pack-2/">
								<!-- <img class="flag-img" alt="Polish Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/07/PolandWavyFlag.svg"> -->

								<!-- <img class="shop-now-img img-fluid" alt="Doritos Cheese" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_UK_BOM_DoritosCheese_50-min.png"> -->
								<img class="img-fluid rounded" alt="Doritos Cheese" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_UK_BOM_DoritosCheese_50-min.png">

							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-cd" href="/snack/pringles-prawn-2-pack/">
								<!-- <img class="flag-img" alt="Egyptian Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/11/EgyptWavyFlag.svg"> -->

								<img class="img-fluid rounded" alt="Pringles Prawn Cocktail" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_UK_BOM_PringlesPrawnCocktail_50-min.png">

							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-cd" href="/snack/chewits-blue-raspberry-pack-5/">
								<!-- <img class="flag-img" alt="French Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/FranceWavyFlag.svg"> -->

								<img class="img-fluid rounded" alt="ChewIts PDP" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/UK_ChewIts_PDP_50-min.png">

							</a>
						</div>
					</div>
				</div>

			</div> <!-- end UK Elite Collection -->

			<div class="trending-brands container my-4 my-lg-5">
				<h2 class="text-left mb-lg-3 mb-xl-4">Trending Crates</h2>

				<div class="brand-icons d-flex flex-column flex-md-row justify-content-between align-items-center">
					<div class="brands-row">
						<a class="brand-btn" href="/collections/cadbury/">
							<img class="brand-logo img-fluid rounded" alt="cadbury Logo" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_Cadbury-min.png">
						</a> 

						<a class="brand-btn mx-3 mx-lg-4" href="/country/united-kingdom/">
							<img class="brand-logo img-fluid rounded" alt="UK pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_UK_min.png">
						</a>
						
						<a class="brand-btn" href="/country/france/">
							<img class="brand-logo img-fluid rounded" alt="France pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_France-min.png">
						</a>
						
					</div>
				</div>

			</div> <!-- end trending-brands -->

			<div class="shop-now world-tour container py-2 my-4 my-lg-5 d-md-flex flex-row-reverse align-items-center align-items-xl-end">

				<div class="shop-now-hero-container position-relative">

					<a class="d-block" href="/collections/british-skittles">
						<img class="shop-now-hero img-fluid" alt="British Skittles" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/Skittles_Preview Card_50-min.png">
					</a>

					<a class="d-none btn btn-sm btn-secondary shadow text-white py-md-2 py-xl-3 mb-1 mx-auto w-100 h9 h7-sm h6-lg" href="/collections/world-tour/">Shop Now</a>
				</div>

				<div class="shop-now-options-container mr-md-4 mr-xxl-5 ml-md-n2 mb-xl-n2">
					<div class="shop-now-text world-tour-text d-flex flex-column align-items-start justify-content-center pl-2 mb-2 mb-xxl-4">
						<h2 class="font-weight-bold mb-1 mb-xl-0 mt-3 mt-md-0">British Skittles</h2>
						<a class="shop-now-link font-weight-semibold h8 h6-sm h4-xl" href="/collections/british-skittles">Shop Now</a>
					</div>
					<div class="shop-now-options mt-3 mt-md-0">
						<div class="single-shop-now-container">
							<a class="single-cd" href="/snack/skittles-chewies-5-pack/">
								<!-- <img class="flag-img" alt="Flag of Hong Kong" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/HongKongWavyFlag.svg"> -->

								<img class="rounded img-fluid" alt="Skittles Chewies Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/Skittles_Chewies_PDP-min.png">

								<!-- <p class="shop-now-title">Cadbury Delight Pack</p> -->
							</a>
						</div>

						<div class="single-shop-now-container">
							<a class="single-cd" href="/snack/skittles-squishy-cloudz/">
								<!-- <img class="flag-img" alt="Polish Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/07/PolandWavyFlag.svg"> -->

								<img class="rounded img-fluid" alt="Skittles Squishy Fruit Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/Skittles_Squishy Fruit_PDP-min.png">

								<!-- <p class="shop-now-title">Crunchie – 5 Pack</p> -->
							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-cd" href="/snack/skittles-sour-giants-5-pack/">
								<!-- <img class="flag-img" alt="Egyptian Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/11/EgyptWavyFlag.svg"> -->

								<img class="rounded img-fluid" alt="Skittles Sour Giants Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/Skittles_Giant Sour_PDP-min.png">

								<!-- <p class="shop-now-title">Cadbury Dairy Milk Oreo Sandwich</p> -->
							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-cd" href="/snack/skittles-giants-5-pack/">
								<!-- <img class="flag-img" alt="French Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/FranceWavyFlag.svg"> -->

								<img class="rounded img-fluid" alt="Skittles Fruit Giants Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/Skittles_Giant Fruit_PDP-min.png">

								<!-- <p class="shop-now-title">Double Decker - 5 Pack</p> -->
							</a>
						</div>
					</div>
				</div>

			</div> <!-- end British Skittles -->

			<div class="categories container mt-n3 mt-md-3 pt-3 position-relative d-none">
				<h2 class="text-left mb-2 mb-lg-3 mb-xl-4">Shop Popular Categories</h2>

				<div class="categories-container ml-n1 ml-lg-0">
					<div class="categories-options pr-2 pr-lg-0">

						<div class="single-pop-cat ml-lg-0">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=biscuits">
								<img class="img-fluid" alt="A stack of thin crumbly biscuits" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/crackers-biscuits-crop.png">
							</a>

							<h3 class="cat-title">Biscuits</h3>
						</div>

						<div class="single-pop-cat">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=chips">
								<img class="img-fluid" alt="A pile of cripsy potato chips" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/chips-crop.png">
							</a>

							<h3 class="cat-title">Chips</h3>
						</div>

						<div class="single-pop-cat">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=chocolates">
								<img class="img-fluid" alt="A pile of square shaped chocolate pieces" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/bars-crop.png">
							</a>

							<h3 class="cat-title">Chocolates</h3>
						</div>

						<div class="single-pop-cat">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=cookies">
								<img class="img-fluid" alt="A stack of chocolate chip cookies" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/cookies-crop.png">
							</a>

							<h3 class="cat-title">Cookies</h3>
						</div>

						<div class="single-pop-cat">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=gummies">
								<img class="img-fluid" alt="A pile of colorful gummy candy covered in sugar" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/gummies-crop.png">
							</a>

							<h3 class="cat-title">Gummies</h3>
						</div>

						<div class="single-pop-cat mr-lg-0">
							<a class="pop-cat-btn p-2" href="/shop-all/?t=wafers">
								<img class="img-fluid" alt="A pile of buttery shortbread pastries" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/wafers-crop.png">
							</a>

							<h3 class="cat-title">Wafers</h3>
						</div>

					</div> <!-- end pop-cat-options -->
				</div> <!-- end pop-cat-container -->
				
			</div> <!-- end popular categories -->

			<!-- top-rated -->
			<div class="categories container mt-n3 mt-md-3 pt-3 pb-xl-3">
				<h2 class="text-left mb-2 mb-lg-3 mb-xl-4">Top Rated</h2>

				<div class="categories-container ml-n1 ml-lg-0">
					<div class="categories-options pr-2 pr-lg-0">
						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/lutti-koala-3-pack/">
								<!-- <img class="flag-img img-fluid" alt="Canada Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/CanadaWavyFlag.svg"> -->

								<img class="img-fluid" alt="Lutti Koala - 3 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0224_Candybar_France_LuttiKoala-min.png">
							</a>

							<h3 class="snack-title equalHeight">Lutti Koala - 3 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4.5); ?>
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/mentos-spice-it-up-5-pack/">
								<!-- <img class="flag-img img-fluid" alt="Portugal Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/05/PortugalWavyFlag-01.svg"> -->

								<img class="img-fluid" alt="Mentos Spice it Up - 5 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2-mentos-spiceitup-5-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Mentos Spice it Up - 5 Pack</h3>	
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4.5); ?>						
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/kitkat-chunky-lotus-3pack/">
								<!-- <img class="flag-img img-fluid" alt="South Korea Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/08/SouthKoreaWavyFlag-1.svg"> -->

								<img class="img-fluid" alt="Kitkat Chunky Lotus - 3 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/3-kitkat-chunky-lotus-3-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Kitkat Chunky Lotus - 3 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4.5); ?>
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/double-decker-5-pack/">
								<!-- <img class="flag-img img-fluid" alt="United Kingdom Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdomWavyFlag.svg"> -->

								<img class="img-fluid" alt="Double Decker - 5 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/4-double-decker-5-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Double Decker - 5 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/orangina-pik-3-pack/">
								<!-- <img class="flag-img img-fluid" alt="Belgium Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/10/BelgiumWavyFlag.svg"> -->

								<img class="img-fluid" alt="Orangina Pik - 3 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/5-orangina-pik-3pack.png">
							</a>

							<h3 class="snack-title equalHeight">Orangina Pik - 3 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/oreo-wafer-roll-4-pack/">
								<!-- <img class="flag-img img-fluid" alt="Colombia Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/12/ColombiaWavyFlag.svg"> -->

								<img class="img-fluid" alt="Oreo Wafer Roll - 4 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/6-oreo-wafer-roll-4-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Oreo Wafer Roll - 4 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>
						</div>

					</div> <!-- end top-rated-options -->
				</div> <!-- end top-rated-container -->
				
			</div> <!-- end top-rated -->

			<div class="shop-now road-trip container py-2 my-4 my-lg-5 d-md-flex flex-row-reverse align-items-center align-items-xl-end">

				<div class="shop-now-hero-container position-relative">

					<a class="d-block" href="/collections/cadbury/">
						<img class="shop-now-hero img-fluid" alt="Red, white, and blue text that reads 'SnackCrate Road Trip' with a background collage of famous American landmarks such as the Statue of Liberty, the Route 66 highway sign, and the Welcome to Las Vegas sign." src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_Cadbury_HeroCard_50-min.jpg">
					</a>

					<a class="d-none btn btn-sm btn-secondary shadow text-white py-md-2 py-xl-3 mb-1 mx-auto w-100 h9 h7-sm h6-lg" href="/country/road-trip/">Shop Now</a>
					
				</div>

				<div class="shop-now-options-container mr-md-4 mr-xxl-5 ml-md-n2 mb-xl-n2">
					<div class="shop-now-text d-flex flex-column align-items-start justify-content-center text-shadow-sm w-100 pl-2 mb-2 mb-xxl-4">
						<h2 class="font-weight-bold mb-1 mb-xl-0 mt-3 mt-md-0">Cadbury</h2>
						<a class="shop-now-link font-weight-semibold h8 h6-sm h4-xl" href="/collections/cadbury/">Shop Now</a>
					</div>
					<div class="shop-now-options mt-3 mt-md-0">
						<div class="single-shop-now-container">
							<a class="single-road-trip" href="/snack/twirl-orange-5/">
								<img class="rounded  img-fluid" alt="Cadbury Orange Twirl" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0124_CandyBar_Cadbury_OrangeTwirl-min.png">

								<!-- <p class="shop-now-title">Boulder Canyon Chips</p> -->
							</a>
						</div>

						<div class="single-shop-now-container">
							<a class="single-road-trip" href="/snack/wispa-gold-5/">
								<img class="rounded  img-fluid" alt="Cadbury Wispa Gold" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_Cadbury_WispaGold-min.jpg">

								<!-- <p class="shop-now-title">Abba Zabba</p> -->
							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-road-trip" href="/snack/cadbury-dairy-milk-oreo-sandwich-92g/">
								<img class="rounded  img-fluid" alt="Cadbury Dairy Milk Oreo" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0324_CandyBar_Cadbury_MilkOreo-min.jpg">

								<!-- <p class="shop-now-title">Elk Jerky</p> -->
							</a>
						</div>

						<div class="single-shop-now-container d-none d-md-block">
							<a class="single-road-trip" href="/snack/crunchie-40g/">
								<img class="rounded  img-fluid" alt="Cadbury Crunchie" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/0124_Candybar_Cadbury_SampleBundle-min.png">

								<!-- <p class="shop-now-title">Aunt Sally's Pralines</p> -->
							</a>
						</div>

					</div>
				</div> <!-- end shop-now-options-container -->

			</div> <!-- end shop-now -->
			<!-- TOP RATED OLD POSITION -->
			

			<div class="shop-now gummie-crate container py-2 my-4 my-lg-5 d-md-flex-DISPLAY-DISABLED flex-row-reverse align-items-center align-items-xl-end d-none">

				<div class="shop-now-hero-container position-relative">
					<a class="d-block" href="/collection/gummie-crate/">
						<img class="shop-now-hero img-fluid" alt="SnackCrate branded Gummie Crate text logo along with various gummy snacks" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/12/CB_CCHome_GC_1000-min.png">
					</a>

					<a class="d-none btn btn-sm btn-secondary shadow text-white py-md-2 py-xl-3 mb-1 mx-auto w-100 h9 h7-sm h6-lg" href="/collections/gummie-crate/">Shop Now</a>
				</div>

				<div class="shop-now-options-container mr-md-4 mr-xxl-5 ml-md-n2 mb-xl-n2">
					<div class="shop-now-text d-none d-xl-flex flex-column align-items-start justify-content-center text-shadow-sm pl-2 mb-2 mb-xxl-4">
						<h2 class="font-weight-bold mb-1 mb-xl-0">Gummie&nbsp;Crate</h2>
						<a class="shop-now-link font-weight-semibold h8 h6-sm h4-xl" href="/shop-all/?t=gummies">Shop Now</a>
					</div>
					<div class="shop-now-options mt-3 mt-md-0">

						<div class="single-shop-now-container">
							<a class="single-shop-now single-gm" href="/snack/drumstick-squashies-sour-cherry-and-apple-160g/">
								<img class="flag-img" alt="Flag of the United Kingdom" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdomWavyFlag.svg">
								<img class="shop-now-img  img-fluid" alt="A packet of Drumstick Squashies gummy candy" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdom_Drumstick_Sour_482x482_.png">

								<p class="shop-now-title">Drumstick Squashies</p>
							</a>
						</div>

						<div class="single-shop-now-container">
							<a class="single-shop-now single-gm" href="/snack/zozole-mieszko-zelki-zozole-rainbow-75g/">
								<img class="flag-img" alt="Flag of Poland" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/07/PolandWavyFlag.svg">
								<img class="shop-now-img  img-fluid" alt="Packet of Zozole gummy snacks" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/11/Poland_Zozole_482x482_.png">

								<p class="shop-now-title">Zozole</p>
							</a>
						</div>

						<div class="single-shop-now-container">
							<a class="single-shop-now single-gm" href="/snack/millions-strawberry-45g/">
								<img class="flag-img" alt="Flag of the United Kingdom" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdomWavyFlag.svg">
								<img class="shop-now-img  img-fluid" alt="Packet of Millions Strawberry gummy snacks" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdom_Strawberry_Millions_482x482_.png">

								<p class="shop-now-title">Millions Strawberry</p>
							</a>
						</div>

						<div class="single-shop-now-container">
							<a class="single-shop-now single-gm" href="/snack/haribo-orangina-pik-120g/">
								<img class="flag-img" alt="Flag of France" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/FranceWavyFlag.svg">
								<img class="shop-now-img  img-fluid" alt="Bag of Orangina Pik gummy candy" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/11/France_Haribo_OranginaPik_482x482_.png">

								<p class="shop-now-title">Orangina Pik</p>
							</a>
						</div>
					</div>
				</div> <!-- end shop-now-options-container -->

			</div> <!-- end shop-now -->

			<div class="categories container mt-n3 mt-md-3 pt-3 pb-xl-3">
				<h2 class="text-left mb-2 mb-lg-3 mb-xl-4">Best-Selling Items</h2>

				<div class="categories-container ml-n1 ml-lg-0">
					<div class="categories-options pr-2 pr-lg-0">

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/skittles-sour-squishy-cloudz-5-pack/">
								<!-- <img class="flag-img img-fluid" alt="Czechia Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/05/CzechWavyFlag-01.svg"> -->

								<img class="img-fluid" alt="Skittles Sour Squishy Cloudz - 5 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/1-skittles-sour-squishy-cloudz-5-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Skittles Sour Squishy Cloudz - 5 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>
						</div>

						<div class="single-snack ml-lg-0">
							<a class="snack-btn" href="/snack/cadbury-snack-pack/">
								<!-- <img class="flag-img img-fluid" alt="Philippines Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/07/PhilippinesWavyFlag.svg"> -->

								<img class="img-fluid" alt="Cadbury Delight Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/2-cadbury-delight-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Cadbury Delight Pack</h3>	
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>						
						</div>

						<div class="single-snack">
							<a class="snack-btn" href="/snack/doritos-combo-pack-2-uk/">
								<!-- <img class="flag-img img-fluid" alt="Belgium Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/10/BelgiumWavyFlag.svg"> -->

								<img class="img-fluid" alt="British Doritos Combo Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/3-british-doritos-combo-pack.png">
							</a>

							<h3 class="snack-title equalHeight">British Doritos Combo Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>						
						</div>

						<div class="single-snack">
							<a class="snack-btn" href="/snack/pringles-smokey-bacon-2-pack/">
								<!-- <img class="flag-img img-fluid" alt="Belgium Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2022/10/BelgiumWavyFlag.svg"> -->

								<img class="img-fluid" alt="Pringles Smokey Bacon - 2 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/4-pringles-smokey-bacon-2-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Pringles Smokey Bacon - 2 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>						
						</div>

						<div class="single-snack">
							<a class="snack-btn" href="/snack/walkers-cheese-onion-4-pack/">
								<!-- <img class="flag-img img-fluid" alt="United Kingdom Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/UnitedKingdomWavyFlag.svg"> -->

								<img class="img-fluid" alt="Walkers Cheese & Onion - 4 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads//2024/04/5-walkers-cheese-onions-4-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Walkers Cheese & Onion - 4 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>						
						</div>

						<div class="single-snack mr-lg-0">
							<a class="snack-btn" href="/snack/brets-chips-5-pack/">
								<!-- <img class="flag-img img-fluid" alt="Canada Flag" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/CanadaWavyFlag.svg"> -->

								<img class="img-fluid" alt="Bret's Chips - 5 Pack" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2024/04/6-brets-chips-5-pack.png">
							</a>

							<h3 class="snack-title equalHeight">Bret's Chips - 5 Pack</h3>
							<?php get_template_part( 'template-parts/star-rating', get_post_format(), 4); ?>						
						</div>

					</div> <!-- end best-sellers-options -->
				</div> <!-- end best-sellers-container -->
				
			</div> <!-- end best-sellers -->

		</div> <!-- end home -->
		
	</section> <!-- end #primary -->

<?php
get_footer();
?>

