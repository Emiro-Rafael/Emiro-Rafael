<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */
get_header();
$snack = new SnackModel( get_the_ID() );
if( !$snack->checkForCompletion() )
{
    echo '<script> window.location.href = "' . get_permalink( get_page_by_path( 'shop-all' ) ) . '";</script>';
}
?>

	<section id="primary" class="content-area mb-5">
		<div class="single-snack py-3 py-md-4">
			<div class="container single-snack-container mb-5">
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

				<div class="single-snack-content d-md-flex align-items-start">
					<div>
						<div class="snack-country d-flex align-items-center justify-content-start">
							<img class="flag-img img-fluid mr-2 mr-lg-3" alt="<?php echo $snack->getCountryName();?> Flag" src="<?php echo $snack->getCountryFlag();?>"/>
							<div class="d-flex flex-column align-items-start justify-content-center">
								<h6 class="font-weight-bold h7 h5-md h4-lg mb-0"><?php echo $snack->getCountryName();?></h6>
								<a class="h8 h7-md h6-lg text-primary" href="/country/<?php echo $snack->getCountryNameLink();?>">View Country</a>
							</div>
						</div>
						<div class="snack-img d-flex flex-column align-items-center m-4 mx-xl-5 position-relative">
							<img class="img-fluid" alt="Image of <?php echo get_post_meta(get_the_ID(), 'user-friendly-name', true);?>" src="<?php echo $snack->getThumbnail('large');?>">
                            <?php /* if( $snack->meta['in-stock'] == 0 || empty($snack->meta['in-stock']) ) :?>
                                <!-- SOLD OUT OVERLAY IMAGE HERE -->
                                <img alt="out of stock" class="position-absolute img-fluid" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/09/OOS_482x482_new-min.png">
                            <?php endif; */?>
						</div>
					</div>

					<div class="snack-info mt-xl-5 ml-xxl-5 pl-xl-4"> 
                        <div class="border-bottom mb-3 my-md-0">

							<div class="d-flex flex-md-column align-items-end align-items-md-start justify-content-between">
								<div>
									<h2 class="h6 h5-md h3-xl font-weight-medium mb-1"><?php echo get_post_meta(get_the_ID(), 'brand', true);?></h2>

									<h1 class="h4 h3-md h1-xl font-weight-bold mb-0 mb-xl-2"><?php the_title();?></h1>

									<a class="specs-link h8 d-md-none font-weight-medium text-gray" href="#" role="button" data-toggle="modal" data-target="#viewSpecsModal" tabindex="0">View Nutrition Facts</a>
								</div>

                                <!-- only show review-stars if snack has reviews -->
                                <?php if ( $snack->hasReviews() && $snack->getRatingsCount() >= 3 ): ?>
                                    <div class="review-stars d-flex align-items-center justify-content-end flex-wrap
                                    " type="button" data-toggle="modal" data-target="#snackRatingModal" tabindex="0">
                                        <?php get_template_part( 'template-parts/star-rating', get_post_format(), get_post_meta(get_the_ID(), 'average_rating', true) ); ?>

                                        <div class="d-flex align-items-center">
                                            <span class="text-primary font-weight-medium h7 h6-lg h4-xl ml-1 ml-lg-2 pt-1"><?php echo $snack->getRatingsCount();?></span>

                                            <span><i class="fas fa-chevron-down text-gray h8 h7-lg h5-xl ml-1 mt-2"></i></span>
                                        </div>
                                        
                                    </div> <!-- review-stars -->

								    <?php get_template_part( 'modals/snack-rating-modal', get_post_format(), $snack ); ?>

                                <?php else: ?>

                                <?php endif; ?>
							</div>

                            <div class="h5-xl font-weight-medium text-gray-dark mt-3 mb-4 mb-md-0"><?=get_the_content();?></div>

							<a class="specs-link d-none d-md-inline h7-md h6-xl font-weight-medium text-gray" href="#" role="button" data-toggle="modal" data-target="#viewSpecsModal" tabindex="0">View Nutrition Facts</a>

							<p class="mb-2 mb-xl-3 mt-md-4">
                            <span class="snack-price h6 h5-lg h4-xl font-weight-bold mr-lg-1" id="snack_price">$<?php echo $snack->getDiscount();?> / Member price</span>
                                <?php /* if( get_user_meta( get_current_user_id(), 'has_subscription', true ) ):?>
                                    <span class="snack-price h6 h4-xl font-weight-bold mr-lg-1" id="snack_price">$<?php echo $snack->getDiscount();?></span>
                                <?php else:?>
                                    <span class="snack-price h6 h4-xl font-weight-bold mr-lg-1" id="snack_price">$<?php echo number_format(get_post_meta(get_the_ID(), 'price', true), 2);?></span>
                                <?php endif; */?>
                            </p>
                        </div>


                        <div class="add-to-cart my-2 my-xl-3">
                            <div class="discount-info d-flex align-items-center justify-content-between justify-content-sm-start mb-xl-3">
                                <a class="h7 h6-lg text-gray-dark mr-sm-4 mr-xl-5 font-weight-medium mb-3" role="button" data-toggle="popover" data-trigger="focus" 
                                data-placement="right"
                                data-content="Monthly subscribers of SnackCrate get 30%
off all individual snacks on the CandyBar. </br> </br> <a href='https://www.snackcrate.com/landing/' target='_blank'>Sign up</a> to save." tabindex="0">
                                        $<?php echo number_format(get_post_meta(get_the_ID(), 'price', true), 2);?>
                                        <span class="h7 h6-lg text-gray-dark">
                                            without SnackCrate Subscription
                                        </span>
                                        <svg class="mb-lg-1" xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="24px" fill="#646464"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                </a>
                        
                            </div>

                            <!-- for SnackCrate subscribers, adds the selected item to their crate -->
                            <!-- <div class="add-to-crate d-flex align-items-center mb-4 pl-2 pl-lg-1">
                                <input class="mr-1 mr-lg-2" type="checkbox" id="addToMyCrate" name="addToMyCrate">
                                <label class="h8 h6-lg text-gray-dark mb-0 position-relative" for="addToMyCrate">Add this item to my SnackCrate every month</label>
                            </div> -->

                            <?php if($snack->getStock( null, false) <= $snack->getStockAlertLevel() && (int)$snack->getStock( null, false ) > $snack->getMinimumStock()) :?>
    							<p class="text-success font-weight-semibold mb-4 pb-xl-2" id="quantityAlert">Only <?php echo $snack->getStock() - $snack->getMinimumStock();?> left!</p>
                            <?php endif;?>

							<?php get_template_part( 'template-parts/add-to-cart', get_post_format(), get_the_ID() ); ?>
                        </div>

                        <?php if (!empty($snack->getShippingDate())):?>
                            <p class="text-gray-dark font-weight-semibold mt-3">Preorder ships <?php echo $snack->getShippingDate();?></p>
                        <?php endif;?>
                    </div> <!-- end country-box -->
				</div> <!-- end snack-info-content -->

				<!-- user reviews carousel -->
                <?php if($snack->hasReviews() && count( $snack->getReviews() ) >= 3) :?>
                    <div class="my-5">
                        <div class="reviews-stats mb-3 mb-lg-4 d-md-flex align-items-center justify-content-start">
                            <h3 class="h4 font-weight-bold text-left mr-md-2"><span id="snackScore"><?php echo get_post_meta(get_the_ID(), 'average_rating', true);?></span> out of 5</h3>
                            <h4 class="h6 h5-md font-weight-medium text-left">&#40;<span><?php echo $snack->getRatingsCount();?></span> reviews&#41;</h4>
                        </div>
                        <?php get_template_part( 'partials/user-reviews-carousel', get_post_format(), $snack ); ?>
                    </div>
                <?php endif; ?>

				<!-- related items carousel -->
				<div class="my-5">
					<?php 
                    $terms = $snack->getTerms();
                    if(!empty($terms['snack_types']))
                    {
                        get_template_part( 
                            'partials/more-from-country-carousel', 
                            'related-items', 
                            array(
                                'title' => 'Related to this item', 
                                'type' => 'post', 
                                'identifier' => 'related', 
                                'snack_type' => $snack->getSnackType(),
                                'brand' => $snack->getBrand(),
                                'max' => 20,
                                'show_titles' => true,
                                'show_ratings' => true,
                                'show_flags' => true,
                                'swiper_class' => 'related-items'
                            )
                        );
                    }
                    
                    ?>
				</div>

				<!-- more-from-this-country carousel -->
				<div class="my-5">
					<?php 
                    
                    get_template_part( 
                        'partials/more-from-country-carousel', 
                        'more-from-country', 
                        array(
                            'title' => 'More from this country', 
                            'type' => 'post', 
                            'identifier' => 'country', 
                            'country' => $snack->getCountryName(),
                            'max' => 20,
                            'show_titles' => true,
                            'show_ratings' => true,
                            'show_flags' => true,
                            'swiper_class' => 'more-from-country'
                        )
                    );
                    ?>
                    
				</div>


			</div> <!-- end single-snack-container -->

		<?php
		while ( have_posts() ) : the_post();

			// get_template_part( 'template-parts/content', get_post_format() );

			// the_post_navigation();

			// echo '<pre>'.print_r($snack->getTerms(),1).'</pre>';
			
			// echo '<pre>'.print_r($snack->getMeta(),1).'</pre>';

            // if(is_user_logged_in() && $snack->userCanReview())
            //     get_template_part( 'template-parts/snack-review', get_post_format() );

		endwhile; // End of the loop.

        // get_template_part( 'template-parts/add-to-cart', get_post_format(), get_the_ID() );
		 ?>
        
		</div><!-- end single-snack -->
	</section><!-- #primary -->

    <?php get_template_part( 'modals/view-snack-specs-modal', get_post_format(), $snack ); ?>
<?php
get_footer();
?>