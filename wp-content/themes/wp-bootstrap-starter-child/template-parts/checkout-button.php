<?php $cart = new Cart(); ?>

<form class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
    <button class="checkout-btn btn btn-secondary text-white font-weight-semibold h7 h6-md h5-lg" type="submit" <?php echo count($cart->getCartItems()) === 0 ? 'disabled' : '';?>>
        Checkout
    </button>
    <input type="hidden" name="action" value="checkout" />
</form>