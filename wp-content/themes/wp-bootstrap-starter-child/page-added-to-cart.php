<?php
/* Template Name: Added to Cart */
get_header();
if(empty($_POST))
    echo '<script>window.location.href = "' . get_site_url() . '";</script>';
    
$snack_id = $_POST['snack_id'];
$quantity = $_POST['quantity'];
$crate_size = array_key_exists('crate_size', $_POST) ? $_POST['crate_size'] : null;

switch( get_post_type($snack_id) )
{
    case 'snack':
        $data = new SnackModel($snack_id);
        $thumbnail_img = $data->getThumbnail('small');
        break;
    
    case 'country':
        $data = new CountryModel($snack_id);
        $thumbnail_img = $data->getFeaturedImage();
        break;

    case 'collection':
        $data = new CollectionModel($snack_id);
        $thumbnail_img = $data->getFeaturedImage();
        break;
}
$cart = new Cart();
?>

    <section id="primary" class="content-area mb-5">    
		<div class="added-to-cart py-3 py-md-4">
            <div class="container">
                <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="javascript:history.back()" style="width: fit-content;"><span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> Continue Shopping</a>

                <div class="added-to-cart-container d-flex flex-column align-items-center justify-content-center p-2 p-lg-3 p-xl-5 mt-3">
                    <div class="added-item d-flex flex-column align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center justify-content-center flex-md-row">
                            <h1 class="h6 h5-lg h4-xl text-success font-weight-bold">Added to Cart</h1>

                            <div class="added-item-img mb-2 mx-md-3 mx-xl-4 position-relative w-100">
                                <img class="img-fluid" src="<?php echo $thumbnail_img;?>">
                                <div class="checkmark h3 h2-lg h1-xl position-absolute text-success"><i class="fas fa-check"></i></div>
                            </div>
                        </div>

                        <p class="added-item-price d-sm-none h6 h5-lg h4-xl font-weight-medium text-gray mb-0">$<span><?php echo Cart::getItemTotal($snack_id, $quantity, $crate_size);?></span></p>
                    </div>

                    <div class="go-to-cart bg-white d-flex align-items-center justify-content-between justify-content-sm-center p-2">
                        <div class="d-flex flex-column align-items-start flex-sm-row align-item-sm-center justify-content-sm-start">
                            <h3 class="h6 h5-lg h4-xl font-weight-medium mb-1 mb-sm-0">Subtotal &#40;<span><?php echo $quantity;?></span> items&#41;:</h3>
                            <h2 class="h6 h5-lg h4-xl font-weight-bold mb-0 ml-sm-2">$<span><?php echo number_format(Cart::getItemTotal($snack_id, $quantity, $crate_size), 2);?></span></h2>
                        </div>

                        <a class="btn btn-secondary text-white h7 h6-lg h5-xl px-4 px-xl-5 ml-sm-4 ml-xl-5" href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) ) ;?>">Go to Cart</a>
                    </div>
                </div>

            
                <div class="w-100 border-bottom">
                    <div class="progress-bar-container border-top py-3 px-md-3 pb-lg-4 p-xl-5 mx-auto d-flex flex-column align-items-center">

                        <?php if( $cart->getShippingMinimum() > $cart->getSubtotal() ):?>
                            <p class="h7 h6-md h5-lg h4-xl text-center font-weight-medium mb-4">Get FREE shipping when you spend another <span class="font-weight-bold">$<?php echo number_format( max( 0, ($cart->getShippingMinimum() - $cart->getSubtotal()) ), 2 );?></span> at the CandyBar</p>
                        <?php else:?>
                            <p class="h7 h6-md h5-lg h4-xl text-center font-weight-medium mb-4">Your order is eligible for <span class="font-weight-bold">FREE</span> shipping.</p>
                        <?php endif;?>
                        <div class="progress-bar bg-gray-light rounded-sm align-self-start">
                            <div class="progress h-100 rounded-sm position-relative" style="width:<?php echo max( 18, $cart->getShippingProgress() );?>%;">
                                <img class="shipping-truck position-absolute" alt="a blue shipping truck that shows how close you are to free shipping" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/shipping-truck.png">
                            </div>
                        </div>
                    </div>
                </div>
            

                <?php if(get_post_type($snack_id) === 'snack'):?>
                    <!-- related items carousel -->
                    <div class="my-3 my-lg-4 my-xl-5">
                        <?php 
                        $terms = $data->getTerms();
                        if(!empty($terms['snack_types']))
                        {
                            get_template_part( 
                                'partials/more-from-country-carousel', 
                                'related-items', 
                                array(
                                    'title' => 'Related to this item', 
                                    'type' => 'post', 
                                    'identifier' => 'related', 
                                    'snack_type' => $data->getSnackType(),
                                    'brand' => $data->getBrand(),
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
                    <div class="my-3 my-lg-4 my-xl-5">
                        <?php

                        get_template_part( 
                            'partials/more-from-country-carousel', 
                            'more-from-country', 
                            array(
                                'title' => 'More from this country', 
                                'type' => 'post', 
                                'identifier' => 'country', 
                                'country' => $data->getCountryName(),
                                'max' => 6,
                                'show_titles' => true,
                                'show_ratings' => true,
                                'show_flags' => true,
                                'swiper_class' => 'more-from-country'
                            )
                        );
                        ?>
                        
                    </div>
                <?php endif;?>
            </div> <!-- container -->

		</div><!-- added-to-cart -->
	</section><!-- #primary -->
<?php 
get_footer();
?>