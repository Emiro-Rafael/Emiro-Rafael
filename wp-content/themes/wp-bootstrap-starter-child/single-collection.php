<?php
/**
 * The template for displaying single collections
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: single-collection
 */
get_header();
$data = new CollectionModel( get_the_ID() );
?>
    <!-- collections colors set here -->
    <style>
        /* collections primary bg color */
        .collection-template-default {
            background-color: <?php echo $data->getBackgroundColor();?>;
        }

        #addToCartCrate label {
            background-color: <?php echo $data->getBackgroundColor();?>;
        }
    </style>

	<section id="primary" class="content-area pb-5" style="background-color: <?php echo $data->getBackgroundColor();?>;">
		<div class="single-collection">

            <!-- collection hero image -->
            <div class="jumbotron jumbotron-fluid country-tour-video" 
                style="background-image: url(<?php echo $data->getHero(); ?>);
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-position: <?php echo $data->getHeroBackgroundPosition();?>;
                    background-color: <?php echo $data->getBackgroundColor();?>;"
            >
            </div>

            <section class="container country-box-container mb-5">
                <!-- breadcrumb nav -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb px-0 py-0 bg-transparent align-items-center">
                        <li class="breadcrumb-item"><a class="text-white" href="<?php echo get_site_url()?>">Home</a></li>
                        <li class="d-flex"><i class="fas fa-chevron-right text-white px-1"></i></li>
                        <li class="breadcrumb-item"><a class="text-white" href="javascript:history.back()">Previous</a></li>
                        <li class="d-flex"><i class="fas fa-chevron-right text-white px-1"></i></li>
                        <li class="breadcrumb-item text-white" aria-current="page"><span><?php the_title();?></span> Collection</li>
                    </ol>
                </nav>

                <div class="country-box-content d-md-flex align-items-center">
                    <!-- country box placeholder image tablet -->
                    <div class="country-box-img d-none d-md-flex d-lg-none flex-column align-items-center my-5 mr-5">
                        <img alt="Image of an open <?php echo the_title();?> collection box overflowing with snacks"  class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">
                    </div>

                    <!-- country box placeholder image desktop -->
                    <div class="country-box-img d-none d-lg-flex flex-column align-items-center my-4 mr-5">
                        <img alt="Image of an open <?php echo the_title();?> collection box overflowing with snacks"  class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">
                    </div>

                    <div class="country-box"> 
                        <!-- country-info content will be populated dynamically -->
                        <div class="country-info mt-4 mb-md-4 mt-md-0">

                            <h1 class="h2 h1-md display-4-xl font-weight-bold text-white mb-0 mb-md-2"><span><?php the_title();?></span> Collection</h1>

                            <div class="text-white d-none d-md-block"><?php the_content();?></div>

                        </div>

                        <!-- country box placeholder image mobile -->
                        <div class="country-box-img d-flex flex-column align-items-center d-md-none">
                            <img alt="Image of an open <?php echo the_title();?> collection box overflowing with snacks" class="img-fluid" src="<?php echo $data->getFeaturedImage();?>">

                            <div class="text-white mb-3"><?php the_content();?></div>
                        </div>

                        <div class="select-box-size border-bottom d-flex align-items-center justify-content-between">
                            
                          <!-- <p class="h6 h5-md h4-xl font-weight-bold text-white mb-2 mb-md-1 <?php echo $data->getPrice() != 25 ? ' d-none' : ' d-block' ?>"><s>39.99</s></p> -->
                            
                            <p class="h6 h5-md h4-xl font-weight-bold text-white mb-2 mb-md-1 mr-5">$<?php echo $data->getPrice();?>
                                <!--
                                <span class="h6"><a class="font-weight-medium text-white" href="https://www.snackcrate.com/landing/">With SnackCrate Subscription</a></span><svg class="mr-1" xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="24px" fill="#fff"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                -->
                            </p>

                            <p class="free-shipping h7 h6-lg text-white text-uppercase mb-2 mb-md-1 <?php echo $data->getPrice() < 35 ? 'd-none' : 'd-block' ?>"><i class="fas fa-truck mr-2 "></i>Free Shipping</p>
                        </div>

                        <div class="add-to-cart my-2">
                            <div class="discount-info d-flex align-items-center justify-content-between justify-content-sm-start pb-2 mb-2">
                                <!-- <p class="h7 h6-lg text-white mr-sm-4 mb-0">Reg $<span>30</span> &#40;<span>30%</span> OFF&#41;</p>
                                <p class="h7 h6-lg text-white text-uppercase mb-0"><i class="fas fa-truck mr-2"></i>Free Shipping</p> -->
                            </div>

                            <!-- for SnackCrate subscribers, adds the selected item to their crate -->
                            <!-- <div class="add-to-crate d-flex align-items-center mb-4 pl-2 pl-lg-1">
                                <input class="mr-1 mr-lg-2" type="checkbox" id="addToMyCrate" name="addToMyCrate">
                                <label class="h7 h6-lg text-gray-dark mb-0 position-relative" for="addToMyCrate">Add this item to my SnackCrate every month</label>
                            </div> -->

                            <?php get_template_part( 'template-parts/add-to-cart-crate', get_post_format(), $data );?>
                            
                        </div>

                        <?php if (!empty($data->getShippingDate())):?>
                            <p class="text-white font-weight-semibold mt-3">Preorder ships <?php echo $data->getShippingDate();?></p>
                        <?php endif;?>
                    </div> <!-- end country-box -->
                </div> <!-- end country-box-content -->
            </section> <!-- end country-box-container -->
            
            <?php 
                $loop = $data->getLoop();
            ?>

            <?php if($loop->have_posts()): ?>
            <section class="container country-all-snacks mb-5">
                <h2 class="h4 h2-md font-weight-bold text-white">All <?php the_title();?> Snacks</h2>

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

<?php
get_footer();
?>