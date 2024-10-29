<?php
/* Template Name: holiday-landing */
get_header();
// $data = new CountryModel(17729);
$data = new CollectionModel(4091);
?>

<style>
    .lil-blurb {
        color: #FFF;
        font-family: Ridley Grotesk;
        font-size: 12px!important;
        font-style: normal;
        font-weight: 400!important;
        line-height: normal;
    }
    @media(max-width: 768px) {
        .holiday-hero-content {
            margin-top: 3rem!important;
        }
    }
</style>

	<section id="primary" class="content-area mb-5">

		<div class="holiday-landing">
            <section class="holiday-hero d-flex flex-column justify-content-start flex-md-row align-items-md-center justify-content-md-start position-relative">
                <div class="holiday-hero-wrap container d-md-flex mx-auto my-lg-5 py-xl-4">
                    <div class="holiday-hero-content d-flex flex-column align-items-center mb-5">
                        <h1 class="font-weight-bolder text-center text-white text-shadow mb-4">Give the gift of<br>SnackCrate</h1>

                        <h6 class="h6 h5-md font-weight-semibold text-center text-white text-shadow mb-4">Gift SnackCrate to everyone on your list for six months, and they'll receive a FREE holiday crate and a bonus 6-month drink package!</h6>
                        <a class="btn btn-white font-weight-semibold py-3 w-100 h5 mb-3" target="_blank" href="https://www.snackcrate.com/holiday-promo/">Gift SnackCrate</a>

                        <!-- <a class="text-white text-shadow-sm h7" href="https://www.snackcrate.com/give-gift/" style="text-decoration: underline;">Want a regular gift?</a> -->
                    </div>
                </div>
                <!-- <img class="white-curve position-absolute w-100" alt="decorative white curve" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/holiday/wave-white-top.svg"> -->
            </section>

            <section class="holiday-video bg-white">
                <div class="container my-5 py-4">
                    <div id="holidayLandingVid" class="video-wrap position-relative overflow-hidden rounded mb-3 mb-lg-4 mx-auto">
                        <video class="position-absolute rounded" preload="auto" playsinline autoplay muted loop>
                            <source src="/wp-content/uploads/2022/11/HolidayMastHead_CandyBar_1080x1350-center-min.mp4" type="video/mp4">
                        </video>
                    </div>

                    <div class="holiday-video-content mx-auto d-flex flex-column align-items-center justify-content-center">
                        <h2 class="h2 h1-md display-5-xl font-weight-bolder mb-4 text-center">Start your own SnackCrate adventure</h2>

                        <h6 class="text-center font-weight-normal mb-4">Don’t worry, we won’t tell you decided to keep SnackCrate all to yourself. Start your own SnackCrate adventure today!</h6>

                        <a class="holiday-2023-button" target="_blank" href="https://www.snackcrate.com/subscribe-new/">Get SnackCrate</a>
                    </div>
                </div>
            </section>

            <!-- <section class="holiday-how-it-works mb-5 pb-4">
                <div class="container">
                    <div class="how-it-works-wrap d-flex flex-column flex-lg-row align-items-center align-items-lg-start justify-content-center mx-auto">
                        <div class="step">
                            <h2>A Tasty Gift That<br>Keeps On Giving...</h2>
                            <img alt="a green Holiday crate with a gold bow being handed to you on a red background" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/holiday/holiday-circle-one.png">
                            <h6>Gift a <strong>6-Month Subscription</strong> to an <strong>Original SnackCrate w/ Drink Upgrade</strong> for <strong>$199</strong> and we'll send them a <strong>Belgium Crate</strong> for <strong>FREE</strong>.</h6>
                        </div>

                        <div class="step mx-lg-4">
                            <h2>Their Free Crate<br>Ships Out Today</h2>
                            <img alt="an open green Holiday crate overflowing with snacks on a green background" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/holiday/holiday-circle-two.png">
                            <h6>We'll ship the <strong>Belgium Crate</strong> directly to the recipient of your awesome gift as the perfect intro to global snacking adventures.</h6>
                        </div>

                        <div class="step">
                            <h2>Let The Snack<br>Adventures Begin!</h2>
                            <img alt="multiple holiday snacks spilling out of a red and white stocking on a red background" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/holiday/holiday-circle-three.png">
                            <h6>Starting in January, we’ll send them <strong>10-12 full-sized snacks &amp; 1 drink</strong> from a <strong>new country</strong> each month until their 6-month subscription ends.</h6>
                        </div>

                    </div>
                </div>
            </section> -->
            <img class="white-curve w-100" alt="decorative white curve" src="https://candybar.snackcrate.com/wp-content/uploads/2023/10/holidat-top.png" style="margin-top: -1px;">

            <section class="holiday-collection">
                <!-- <img class="white-curve position-absolute w-100" alt="decorative white curve" src="https://candybar.snackcrate.com/wp-content/uploads/2023/10/holidat-top.png" style="margin-top: -1px;"> -->

                <div class="country-box-content d-md-flex align-items-center container pt-5 pb-2 pb-md-0">

                    <!-- country box placeholder image desktop -->
                    <div class="country-box-img d-none d-md-flex flex-column align-items-center my-5 mb-lg-4 mr-5">
                        <img alt="Image of an open holiday box collection box overflowing with snacks"  class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">
                    </div>

                    <div class="country-box py-5 my-md-4"> 
                        <!-- country-info content will be populated dynamically -->
                        <div class="country-info mt-4 mb-md-4 mt-md-0">

                            <h2 class="h2 h1-md display-4-xl font-weight-bold text-white text-center text-md-left mb-0 mb-md-2">Holiday Collection</h2>

                            <div class="text-white d-none d-md-block">‘Tis the season to indulge in delicious holiday-themed snacks! This limited collection from SnackCrate brings together 14 goodies from 7 different countries, making this the perfect gift for celebrating the festive season.</div>

                        </div>
                        <!-- country box image mobile -->
                        <div class="country-box-img d-flex flex-column align-items-center d-md-none">
                            <img alt="Image of an open Holiday Crate collection box overflowing with snacks" class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">

                            <div class="text-white mb-3"><?php echo get_post_field('post_content', 4091);?></div>
                        </div>
                        <p class="h6 h5-md h4-xl font-weight-bold text-white mb-2 mb-md-1 mr-5">$<?php echo $data->getPrice();?></p>
                        <div class="select-box-size border-bottom d-flex align-items-center justify-content-between">
                            <!-- <p class="h6 h5-md h4-xl font-weight-bold text-white mb-2 mb-md-1 mr-5">$<?php echo $data->getPrice();?></p> -->

                            <!-- <div class="select-box-size border-bottom">
                            <h3 class="h6 h5-md text-white font-weight-bold mb-3">$35.99</h3>

                            <div class="box-size-btns mb-3">
                                <?php 
                                foreach( CountryModel::$button_names as $size => $name ) :
                                    $available_stock = $data->getStock($size, false);
                                    $drink_stock = $data->getStock($size.'W', false);
                                    if( $available_stock > 0 || $data->checkIfPreorder() ) : 
                                ?>
                                        <button 
                                            data-box="<?php echo $size;?>" 
                                            data-stock="<?php echo $available_stock;?>"
                                            data-drinkstock="<?php echo $drink_stock;?>"
                                            data-preorder="<?php echo $data->checkIfPreorder() ? 1 : 0;?>"
                                            class="btn btn-sm mr-1 mr-lg-2 text-white border-white" 
                                            id="crateSize<?php echo $size;?>">
                                            <?php echo $name;?>
                                        </button>
                                    <?php else: ?>
                                        <a
                                            class="d-inline-block"
                                            role="button"
                                            data-toggle="popover"
                                            data-placement="right" 
                                            data-trigger="click focus"
                                            data-content="Out of Stock"
                                            tabindex="0"
                                        >
                                            <button 
                                                data-box="<?php echo $size;?>" 
                                                class="btn btn-sm mr-1 mr-lg-2 text-white border-white" 
                                                id="crateSize<?php echo $size;?>"
                                                disabled>
                                                <?php echo $name;?>
                                            </button>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </div>

                            <p id="snackQty8Snack" class="font-weight-medium text-white mb-3">Contains between 10-12 full-sized snacks.</p>
                            <p id="snackQty16Snack" class="font-weight-medium text-white mb-3 d-none">Contains between 20-24 full-sized snacks.</p>


                            <div class="add-drink d-flex align-items-center mb-4 pl-2 pl-lg-1">
                                <input class="mr-1 mr-lg-3" type="checkbox" id="addDrink" name="addDrink">
                                <label class="h7 h6-lg text-white font-weight-semibold mb-0 position-relative" for="addDrink">Add a drink? &#40;$<?php echo CountryModel::$drink_price;?>&#41;</label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <p class="">
                                    <?php foreach(CountryModel::$price as $size => $cost):?>
                                        <span class="country-price h6 h5-md h4-xl font-weight-bold text-white mb-2 mb-md-1 mr-5" id="<?php echo $size;?>_prices">$<?php echo $cost;?></span>
                                    <?php endforeach;?>
                                    <span>
                                        <a class="text-gray-dark font-weight-medium" href="https://www.snackcrate.com/landing/">With SnackCrate Subscription</a>
                                    </span>
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="24px" fill="#646464"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg> -->
                                <!-- </p>
                                <p id="freeShippingIcon" class="free-shipping h7 h6-lg text-uppercase text-white mb-2 mb-md-1 opacity-0"><i class="fas fa-truck mr-2"></i>Free Shipping</p>
                            </div> -->

                        <!-- </div> --> 

                        <div class="add-to-cart my-2">
                            <div class="discount-info d-flex align-items-center justify-content-between justify-content-sm-start pb-2 mb-2">
                                <!-- <p class="h7 h6-lg text-white mr-sm-4 mb-0">Reg $<span>30</span> &#40;<span>30%</span> OFF&#41;</p>
                                <p class="h7 h6-lg text-white text-uppercase mb-0"><i class="fas fa-truck mr-2"></i>Free Shipping</p> -->
                            </div>

                            <?php get_template_part( 'template-parts/add-to-cart-crate', get_post_format(), $data );?>
                            
                        </div>

                        <!-- <?php if (!empty($data->getShippingDate())):?>
                            <p class="text-white font-weight-semibold mt-3">Preorder ships <?php echo $data->getShippingDate();?></p>
                        <?php endif;?> -->
                    </div> <!-- end country-box -->
                    <?php if (!empty($data->getShippingDate())):?>
                            <p class="text-white font-weight-semibold mt-3">Preorder ships <?php echo $data->getShippingDate();?></p>
                    <?php endif;?>
                            <p class="lil-blurb">Product shown is for illustration purposes only. Actual products may vary based on availability.</p>
                </div> <!-- end country-box-content -->

            </section>
            <img class="white-curve position-absolute w-100" alt="decorative white curve" src="https://candybar.snackcrate.com/wp-content/uploads/2023/10/holiday-bottom.png" style="margin-top: -1px;">


		</div> <!-- end holiday-landing -->
		
		
	</section> <!-- end #primary -->

<?php
get_footer();
?>