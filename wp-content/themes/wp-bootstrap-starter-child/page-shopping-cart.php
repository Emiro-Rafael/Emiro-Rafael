<?php
/* Template Name: Shopping Cart */
get_header();
$cart = new Cart();
global $user_data;
?>
    <?php if( $_GET['session_expired'] ) :?>
        <div class="text-white bg-danger font-weight-semibold mb-2 mb-md-3 p-2 text-center">Your Session has expired and your cart is now empty.</div>
    <?php endif;?>
    <section id="primary" class="content-area mb-5">    
		<div class="shopping-cart py-3 py-md-4">
            <div class="top-container container">
                <div class="d-flex align-items-center justify-content-between mb-4 mb-lg-5 mb-md-0">
                <a class="h6 h5-lg font-weight-semibold text-gray-dark d-flex align-items-center" href="/shop-all/"><span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span> Continue Shopping</a>

                    <?php if( empty($user_data) ):?>
                        <div class="checkout-btn-top d-md-flex align-items-center justify-content-end">
                            
                            <div class="d-none d-md-flex align-items-center justify-content-center">
                                <a role="button" data-toggle="modal" data-target="#signinModal" class="text-uppercase signInBtn font-weight-semibold h6 h5-xl">
                                    Sign in and checkout faster
                                </a>
                            </div>
                            
                        </div>
                    <?php endif;?>
                </div>
                
                <div class="d-flex align-items-center justify-content-between my-4 my-lg-5">
                    <h1 class="h4 h2-md h1-lg display-5-xl font-weight-bold mb-0">Shopping Cart</h1>
                    <div class="checkout-btn-top d-md-flex align-items-center justify-content-end">
                        <div class="d-none d-md-flex align-items-center justify-content-center mr-3 mr-lg-4 mr-xl-5">
                            <h2 class="h6 h4-md h3-lg h2-xl font-weight-bold text-uppercase mb-0 mr-2 mr-lg-3">Subtotal:</h2>
                            <h2 class="h6 h4-md h3-lg h2-xl font-weight-bold text-uppercase mb-0">$<span id="subTotal"><?php echo number_format($cart->getSubTotal(), 2, '.', '');?></span></h2>
                        </div>
                        <?php get_template_part( 'template-parts/checkout-button', get_post_format() ); ?>
                    </div>
                </div>
            </div> <!-- top-container -->


            <!-- mobile -->
            <div class="cart-items-mobile d-md-none w-100">
                <div class="d-flex align-items-center justify-content-between container">
                    <h3 class="h8 text-gray font-weight-semibold ml-5 mb-1">Name/QTY</h3>
                    <h3 class="h8 text-gray font-weight-semibold mb-1">Total</h3>
                </div>
                <?php if(count($cart->getCartItems()) === 0): ?>
                    <div class="cart-item-container bg-gray-light border-top">
                        <div class="single-cart-item w-100 border-bottom">
                            <div class="container py-5 d-flex align-items-start">
                                <h5 class="font-weight-semibold mb-0">Your cart is empty.</h5>
                            </div>
                        </div> <!-- single-cart-item -->

                    </div> <!-- cart-item-container -->
                <?php endif; ?>
                <?php 
                    foreach($cart->getCartItems() as $post_id => $item) 
                    {
                        if( get_post_type($post_id) == 'snack' )
                        {
                            get_template_part( 'template-parts/checkout-item-mobile', get_post_format(), array($post_id, $item) );
                        }
                        else
                        {
                            foreach($item as $size => $quantity)
                            {
                                get_template_part( 'template-parts/checkout-item-mobile', get_post_format(), array($post_id, $quantity, $size) );
                            }
                        }
                    }
                ?>
            </div> <!-- cart-items-mobile -->

            <!-- tablet/desktop -->
            <div class="cart-items-desktop d-none d-md-block w-100 mx-auto">

                <table class="cart-table w-100">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <h3 class="h7 h6-lg h5-xl text-gray font-weight-semibold mb-1 mb-lg-3 mb-xl-4">Name</h3>
                            </th>
                            <th>
                                <h3 class="h7 h6-lg h5-xl text-gray font-weight-semibold mb-1 mb-lg-3 mb-xl-4">Price</h3>
                            </th>
                            <th></th>
                            <th>
                                <h3 class="h7 h6-lg h5-xl text-gray font-weight-semibold mb-1 mb-lg-3 mb-xl-4">Quantity</h3>
                            </th>
                            <th></th>
                            <th>
                                <h3 class="h7 h6-lg h5-xl text-gray font-weight-semibold mb-1 mb-lg-3 mb-xl-4">Total</h3>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-gray-light border-top">
                    <?php if(count($cart->getCartItems()) === 0): ?>
                        <tr class="single-cart-item border-bottom">
                            <td class="py-5 text-center" colspan="7">
                                <h5 class="h4-lg h3-xl font-weight-semibold mb-0 py-xl-4">Your cart is empty.</h5>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php 
                        foreach($cart->getCartItems() as $post_id => $item) 
                        {
                            if( get_post_type($post_id) == 'snack' )
                            {
                                get_template_part( 'template-parts/checkout-item-desktop', get_post_format(), array($post_id, $item) );
                            }
                            else
                            {
                                foreach($item as $size => $quantity)
                                {
                                    get_template_part( 'template-parts/checkout-item-desktop', get_post_format(), array($post_id, $quantity, $size) );
                                }
                            }
                        }
                    ?>
                    </tbody>
                </table> <!-- cart-table -->
                
            </div> <!-- cart-items-desktop -->


            <div class="total-cost">
                <div class="total-cost-container container py-3 py-lg-5">
                    <div class="d-flex justify-content-end">
                        <div class="cost-breakdown d-flex align-items-center justify-content-end border-bottom">
                            <div class="d-flex flex-column align-items-start mr-5">
                                <h3 class="h6 h5-md h4-lg h3-xl text-dark font-weight-semibold">Shipping</h3>
                                <?php if($cart->getTaxes() > 0):?>
                                    <h3 class="h6 h5-md h4-lg h3-xl text-gray-dark font-weight-medium">Taxes</h3>
                                <?php endif;?>
                            </div>

                            <div class="d-flex flex-column align-items-end">
                                <h3 class="h6 h5-md h4-lg h3-xl font-weight-medium text-uppercase text-gray-dark" id="shippingTotal"><?php echo count($cart->getCartItems()) === 0 ? '--' : $cart->displayShippingTotal();?></h3>
                                <?php if($cart->getTaxes() > 0):?>
                                    <h3 class="h6 h5-md h4-lg h3-xl font-weight-medium text-uppercase text-gray-dark"><?php $cart->displayTaxes();?></h3>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end mt-2">
                        <div class="d-flex flex-column align-items-start mr-2">
                            <h2 class="h6 h5-md h4-lg h3-xl font-weight-bold text-uppercase mb-0 mr-5">Total</h2>
                        </div>

                        <div class="d-flex flex-column align-items-end">
                            <h2 class="h6 h5-md h4-lg h3-xl font-weight-bold text-uppercase mb-0">$<span id="grandTotal"><?php echo count($cart->getCartItems()) === 0 ? '0.00' : number_format($cart->getTotal(), 2, '.', '');?></span></h2>
                        </div>
                    </div>
 
                </div> <!-- total-cost-container -->
            </div> <!-- total-cost -->

                <div class="checkout-btn-bottom container d-flex justify-content-center justify-content-md-end pt-3">
                    <?php get_template_part( 'template-parts/checkout-button', get_post_format() ); ?>
                </div>
            </div>

		</div><!-- shopping-cart -->
	</section><!-- #primary -->

<?php 
get_footer();
?>