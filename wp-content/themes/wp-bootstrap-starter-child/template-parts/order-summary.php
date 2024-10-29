<?php
$cart = new Cart();
?>
<div class="order-summary bg-gray-light rounded-sm p-3 p-xl-4 w-100">
    <h2 class="h4 h3-md h2-xl font-weight-bold mb-3">Order Summary</h2>

    <p class="h7 h6-md font-weight-medium mb-0"><span><?php echo Cart::getCartNumber();?></span> items in cart</p>

    <?php 
    foreach( $cart->getCartItems() as $post_id => $quantity ) : 
        $cart_items = $cart->arrangeCartItem($post_id, $quantity);
        foreach($cart_items as $cart_item) :
    ?>

            <div class="single-cart-item d-flex align-items-center justify-content-start border-bottom py-3">
                <div class="cart-item-img mr-2">
                    <img class="img-fluid" alt="image of <?php echo $cart_item->name;?>" src="<?php echo $cart_item->thumbnail;?>">
                </div>
                <div class="d-flex align-items-end justify-content-between w-100">
                    <div class="d-flex flex-column justify-content-start">
                        <h4 class="h7 h6-md font-weight-semibold mb-0"><?php echo $cart_item->name;?></h4>
                        <p class="h7 h6-md text-dark font-weight-medium mb-0">Qty: <span><?php echo $cart_item->quantity;?></span></p>
                    </div>

                    <h3 class="h6 text-gray font-weight-medium mb-0">$<span><?php echo number_format(Cart::getItemTotal($post_id, $cart_item->quantity, $cart_item->crate_size), 2);?></span></h3>
                </div>
            </div> <!-- single-cart-item -->
        <?php endforeach;?>
    <?php endforeach;?>

    <div class="cost-breakdown d-flex align-items-center justify-content-between py-3">
        <div class="d-flex flex-column align-items-start mr-5">
            <h3 class="h6 text-dark font-weight-semibold">Shipping</h3>
            <?php if($cart->getTaxes() > 0):?>
                <h3 class="h6 text-gray font-weight-medium">Taxes</h3>
            <?php endif;?>
            <h3 class="h6 text-dark font-weight-semibold mb-0 mt-3 mt-lg-4">Total &#40;USD&#41;</h3>
        </div>

        <div class="d-flex flex-column align-items-end">
            <h3 class="h6 font-weight-medium text-uppercase text-gray"><span><?php $cart->displayShippingTotal();?></span></h3>
            <?php if($cart->getTaxes() > 0):?>
                <h3 class="h6 font-weight-medium text-uppercase text-gray"><span><?php $cart->displayTaxes();?></span></h3>
            <?php endif;?>
            <h3 class="h6 font-weight-semibold text-uppercase text-dark mb-0 mt-3 mt-lg-4">$<span><?php echo number_format($cart->getTotal(), 2);?></span></h3>
        </div>
    </div> <!-- cost-breakdown -->

    <?php 
    if( strpos( get_permalink(), 'checkout-confirm-shipping' ) === false && strpos( get_permalink(), 'checkout-guest' ) === false ):

        if( !empty($_SESSION['checkout']['address']['shipping']) ): 
            $user_address_1 = $_SESSION['checkout']['address']['shipping']['address_1'];
            $user_address_2 = $_SESSION['checkout']['address']['shipping']['address_2'];
            $user_city = $_SESSION['checkout']['address']['shipping']['city'];
            $user_state = $_SESSION['checkout']['address']['shipping']['state'];
            $user_zip = $_SESSION['checkout']['address']['shipping']['zip'];

            $shipping_name = $_SESSION['checkout']['address']['shipping']['shipping_name'];
            $first_name = substr( $shipping_name, 0, strpos($shipping_name, ' ') );
            $last_name = substr( $shipping_name, strpos($shipping_name, ' ') );
    ?>
        <div class="shipping-address py-3 border-top">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="h6 font-weight-bold">Shipping Address</h3>
                <!-- <p class="h6 font-weight-medium text-uppercase mb-2 edit-address-btn text-primary" type="button" data-toggle="modal" data-target="#editShippingAddressModal-shipping">edit</p> -->
            </div>
            <p class="h6 text-gray font-weight-medium"><span data-shippingfield="first_name"><?php echo $first_name;?></span> <span data-shippingfield="last_name"><?php echo $last_name;?></span></p>
            <p class="h6 text-gray font-weight-medium" data-shippingfield="address_1"><?php echo $user_address_1;?></p>
            
            <p class="h6 text-gray font-weight-medium <?php echo empty($user_address_2) ? 'd-none' : '' ;?>" data-shippingfield="address_2"><?php echo $user_address_2;?></p>
            
            <p class="h6 text-gray font-weight-medium mb-0"><span data-shippingfield="city"><?php echo $user_city;?></span>, <span data-shippingfield="state"><?php echo $user_state;?></span> <span data-shippingfield="zip"><?php echo $user_zip;?></span></p>
        </div> <!-- shipping-address -->
    <?php 
        endif;
    
        if( !empty($_SESSION['checkout']['address']['billing']) ):
    ?>
            <div class="billing-address py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="h6 font-weight-bold">Billing Address</h3>
                    <!-- <p class="h6 font-weight-medium text-uppercase mb-2 edit-address-btn text-primary" type="button" data-toggle="modal" data-target="#editShippingAddressModal-billing">edit</p> -->
                </div>
                <p class="h6 text-gray font-weight-medium"><span data-billingfield="shipping_name"><?php echo $_SESSION['checkout']['address']['billing']['shipping_name'];?></span></p>
                <p class="h6 text-gray font-weight-medium" data-billingfield="address_1"><?php echo $_SESSION['checkout']['address']['billing']['address_1'];?></p>
                
                <p class="h6 text-gray font-weight-medium <?php echo empty($user_address_2) ? 'd-none' : '' ;?>" data-billingfield="address_2"><?php echo $_SESSION['checkout']['address']['billing']['address_2'];?></p>
                
                <p class="h6 text-gray font-weight-medium mb-0"><span data-billingfield="city"><?php echo $_SESSION['checkout']['address']['billing']['city'];?></span>, <span data-billingfield="state"><?php echo $_SESSION['checkout']['address']['billing']['state'];?></span> <span data-billingfield="zip"><?php echo $_SESSION['checkout']['address']['billing']['zip'];?></span></p>
            </div> <!-- shipping-address -->
    <?php 
        endif;

    endif;    
    ?>

    <div class="pt-3 d-flex align-items-start border-top d-md-none">
        <p class="text-gray small">
            By clicking Place Order you agree to the <a class="text-gray text-decoration-underline" href="<?php echo get_bloginfo('url')?>/terms-of-use/" target="_blank">Terms &amp; Conditions</a>
        </p>
    </div>
    <!--
    <p class="h7 text-gray font-weight-medium pt-3 mb-0" style="opacity: 0;">Your plan will be billed on the 5th of each month and shipped before the end of the month. You will be able to adjust your plan after checkout.</p>
    <div class="mt-3" style="opacity: 0;">
        <h4 class="h6 font-weight-semibold">Pause, skip, or cancel anytime.</h4>
        <p class="h8 text-gray font-weight-medium">By clicking “Place Order,” you agree you are purchasing a continuous subscription and will receive deliveries billed to your designated payment method until you cancel. You may pause or cancel your subscription at any time, based on the date for your next delivery on your account page.</p>
    </div>
        -->
</div> <!-- order-summary -->