<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
$data = new CountryModel(get_the_ID());
?>

	<section id="primary" class="content-area mb-5">
		<div class="single-country">

            <!-- country hero image -->
            <div class="jumbotron jumbotron-fluid country-tour-video" 
                style="background-image: url(<?php echo $data->getHero(); ?>);
                    <?php if ( strpos($_SERVER['REQUEST_URI'], "world-tour") == true ): ?>
                    background-position: center;
                    <?php else: ?>
                    background-position: bottom center;
                    <?php endif;?>
                    background-repeat: no-repeat;
                    background-size: cover;">

            </div>

            <div class="container country-box-container mb-5">
                <!-- breadcrumb nav -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb px-0 py-0 bg-transparent align-items-center">
                        <li class="breadcrumb-item"><a class="text-gray" href="<?php echo get_site_url()?>">Home</a></li>
                        <li class="d-flex"><i class="fas fa-chevron-right text-gray px-1"></i></li>
                        <li class="breadcrumb-item"><a class="text-gray" href="javascript:history.back()">Previous</a></li>
                        <li class="d-flex"><i class="fas fa-chevron-right text-gray px-1"></i></li>
                        <li class="breadcrumb-item text-gray" aria-current="page"><?php the_title();?></li>
                    </ol>
                </nav>

                <div class="country-box-content d-md-flex align-items-center">
                    <!-- country box placeholder image tablet -->
                    <div class="country-box-img d-none d-md-flex d-lg-none flex-column align-items-center my-5 mr-5">
                        <img alt="Image of an open <?php echo the_title();?> SnackCrate overflowing with snacks" class="img-fluid" src="<?php echo $data->getFeaturedImage(337);?>">
                    </div>

                    <!-- country box placeholder image desktop -->
                    <div class="country-box-img d-none d-lg-flex flex-column align-items-center my-4 mr-5 pt-xxl-3">
                        <img alt="Image of an open <?php echo the_title();?> SnackCrate overflowing with snacks" class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">
                    </div>

                    <div class="country-box"> 
                        <!-- country-info content will be populated dynamically -->
                        <div class="country-info mt-4 mt-md-0">

                            <h1 class="h2 h1-md display-4-xl font-weight-bold"><?php the_title();?></h1>

                            <div class="text-gray-dark d-none d-md-block"><?php the_content();?></div>

                        </div>

                        <!-- country box placeholder image mobile -->
                        <div class="country-box-img d-flex flex-column align-items-center d-md-none">
                            <img alt="Image of an open <?php echo the_title();?> SnackCrate overflowing with snacks" class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">

                            <div class="text-gray-dark mb-3"><?php the_content();?></div>
                        </div>

                        <div class="select-box-size border-bottom">
                            <h3 class="h6 h5-md font-weight-bold mb-3"><?php the_title();?> SnackCrate</h3>

                            <div class="box-size-btns mb-3">
                                <?php 
                                foreach( array_filter(CountryModel::$button_names, function($item, $key) {
                                    return $key === 'Ultimate';
                                }, ARRAY_FILTER_USE_BOTH) as $size => $name ) :
                                    $available_stock = $data->getStock($size, false);
                                    $drink_stock = $data->getStock($size.'W', false);
                                    if( $available_stock > 0 || $data->checkIfPreorder() ) : 
                                ?>
                                        <button 
                                            data-box="<?php echo $size;?>" 
                                            data-stock="<?php echo $available_stock;?>"
                                            data-drinkstock="<?php echo $drink_stock;?>"
                                            data-preorder="<?php echo $data->checkIfPreorder() ? 1 : 0;?>"
                                            class="btn btn-sm mr-1 mr-lg-2" 
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
                                                class="btn btn-sm mr-1 mr-lg-2" 
                                                id="crateSize<?php echo $size;?>"
                                                disabled>
                                                <?php echo $name;?>
                                            </button>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach;?>
                            </div>
                            <p id="snackQty4Snack" class="font-weight-medium text-gray-dark mb-3 d-none">Contains between 5-6 full-sized snacks.</p>
                            <p id="snackQty8Snack" class="font-weight-medium text-gray-dark mb-3 d-none">Contains between 10-12 full-sized snacks.</p>
                            <p id="snackQty16Snack" class="font-weight-medium text-gray-dark mb-3 d-none">Contains between 20-24 full-sized snacks.</p>


                            <div class="add-drink d-none align-items-center mb-4 pl-2 pl-lg-1">
                                <input class="mr-1 mr-lg-3" type="checkbox" id="addDrink" name="addDrink">
                                <label class="h7 h6-lg text-dark font-weight-semibold mb-0 position-relative" for="addDrink">Add a drink? &#40;$<?php echo CountryModel::$drink_price;?>&#41;</label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <p class="">
                                    <?php foreach(CountryModel::$price as $size => $cost):?>
                                        <span class="country-price h6 h5-md h4-xl font-weight-bold mb-2 mb-md-1 mr-5" id="<?php echo $size;?>_prices">$<?php echo $cost;?></span>
                                    <?php endforeach;?>
                                    <!-- <span>
                                        <a class="text-gray-dark font-weight-medium" href="https://www.snackcrate.com/landing/">With SnackCrate Subscription</a>
                                    </span>
                                    <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="24px" fill="#646464"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg> -->
                                </p>
                                <p id="freeShippingIcon" class="free-shipping h7 h6-lg text-uppercase mb-2 mb-md-1 opacity-0"><i class="fas fa-truck mr-2"></i>Free Shipping</p>
                            </div>

                        </div>

                        <div class="add-to-cart my-2">
                            <div class="discount-info d-flex align-items-center justify-content-between justify-content-sm-start mb-4">
                                <!-- <p class="h8 h6-lg text-gray-dark mr-sm-4">Reg $<span>2.99</span> &#40;30% OFF&#41;</p> -->
                            </div>

                            <?php get_template_part( 'template-parts/add-to-cart-crate', get_post_format(), $data );?>
                                
                        </div>

                        <?php if ( $data->checkIfPreorder() ):?>
                            <p class="font-weight-semibold mt-3">Preorder ships <?php echo $data->getShippingDate();?></p>
                        <?php endif;?>
                    </div> <!-- end country-box -->
                </div> <!-- end country-box-content -->
            </div> <!-- end country-box-container -->

    <?php 
                $loop = $data->getLoop();
            ?>

            <?php if($loop->have_posts()): ?>
            <section class="container country-all-snacks mb-5">
                <h2 class="h4 h2-md font-weight-bold text-black">All <?php the_title();?> Snacks</h2>

                <div class="all-snacks-grid my-4 mx-auto w-100">
                    <?php 
                        if($loop->have_posts()) {
                            while($loop->have_posts()) : $loop->the_post();
                                get_template_part( 'template-parts/snack-block', get_post_format(), $data );
                            endwhile;
                        }
                        else
                        {
                            echo 'No snacks here.';
                        }
                    ?> 

                </div> <!-- end all-snacks-grid -->
            </section> <!-- end country-all-snacks -->
            <?php endif; ?>

            <!-- customers also viewed swiper -->
            <!-- <section class="container also-viewed">
                <h2 class="h4 h2-md font-weight-bold">Customers also viewed:</h2>
                <p>a swiper widget of snacks customers also viewed will go here</p>
            </section> -->


		</div><!-- end single-country -->
	</section><!-- end #primary -->
    <script>
        jQuery(document).ready(function(){
            if(jQuery('#crateSizeUltimate')) {
                jQuery('#crateSizeUltimate').click();
            }
        });
    </script>
<?php
get_footer();
?>