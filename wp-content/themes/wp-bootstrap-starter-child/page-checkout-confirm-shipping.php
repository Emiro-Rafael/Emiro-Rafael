<?php
/**
 * The template for displaying Confirm & Pay
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Checkout-confirm-pay
 */
get_header();
global $user_data;

if( empty($user_data) )
{
    echo '<script>window.location.href = "' . get_permalink( get_page_by_path( 'checkout/checkout-guest' ) ) . '"</script>';
}
$user = new User( $user_data->email );
$user->setAddressData();
$preferences = $user->getUserNotificationPreferences();
$default_address = $user->getAddressData();

if( @$_GET['selected_address'] )
{
    $default_address_id = $_GET['selected_address'];
}
elseif( !empty($default_address) )
{
    // check if we already have this address in the new Address table, add it if we don't
    $default_address_id = Address::addAddress($default_address, $user->getStripeCustomerId(), '', 1); 
}
$addresses = $user->getAllAddresses( $default_address_id );
?>

<section id="primary" class="content-area mb-5">
    <div class="confirm-shipping py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) ) ;?>" style="max-width: fit-content;">
                <span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> 
                Back to Cart
            </a>
            <div class="row justify-content-between">
                <div class="col-12 col-lg-7 pr-lg-5 shipping-container">
                    

                    <div class="bg-primary text-white rounded-sm p-3 my-4 mt-lg-3">
                        <h3 class="h6 h5-lg text-uppercase">Welcome back, <?php echo $user_data->firstname;?>!</h3>
                        <p class="h7 h6-lg mb-0">We&rsquo;re happy to see you &mdash; Let&rsquo;s get this order wrapped up for you&hellip;</p>
                    </div>

                    <h1 class="h4 h3-md h2-xl font-weight-bold mb-0 py-xl-3">Shipping Address</h1>
                    <form id="confirmShippingForm" class="ajax_form" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                        <div class="cards mb-3">
                            <div class="container position-relative px-0">
                                <div class="address-swiper-container swiper-container pl-1 py-3 px-md-0 mx-auto">
                                    <div class="address-swiper">
                                        <div class="swiper-wrapper row clearfix px-md-2 pl-1">
                                            <?php 
                                            foreach($addresses as $address) 
                                            {
                                                get_template_part( 'template-parts/address-card', get_post_format(), array( $address, $default_address_id ) );
                                            }
                                            ?>
                                            <div class="p-2 card-container swiper-slide" data-addressid="0">
                                                <div class="card equalHeight px-3 px-md-4 swiper-slide p-2 justify-content-between" role="button" data-toggle="modal" data-target="#editShippingAddressModal-0">
                                                    <div class="font-weight-bold my-2">New Address</div>

                                                    <div class="my-2"><span class="text-gray"><i class="fas fa-plus"></i></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="h6 h5-xl mb-0">
                                <span class="font-weight-bold">Email:</span>
                                <?php echo $user_data->email;?>
                            </h6>
                        </div>

                        <?php get_template_part( 'template-parts/billing-address-block', get_post_format() );?>

                        <?php 
                            get_template_part( 'template-parts/optin-block', get_post_format(), ( !empty($preferences) && $preferences->marketing_email == 1 ) );
                        ?>

                        <div class="my-4">
                            
                                <button type="submit" class="btn btn-secondary text-white">Review and Pay</button>
                                <input type="hidden" name="shipping_address" value="<?php echo $default_address_id;?>" />
                                <input type="hidden" name="action" value="shipping_confirmed" />
                            
                        </div>
                    </form>
                </div>

                <div class="d-none d-lg-block col-12 col-lg-5 my-4 order-summary-container mt-lg-3 mr-xxl-2">
                    <?php get_template_part( 'template-parts/order-summary', get_post_format(), '' );?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php 

foreach($addresses as $address)
{
    get_template_part( 'modals/edit-shipping-address-modal', get_post_format(), $address ); 
} 
get_template_part( 'modals/edit-shipping-address-modal', get_post_format() ); 

get_footer();?>