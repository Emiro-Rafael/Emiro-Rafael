<?php
/**
 * Template part for reusing remove from cart button. Requires passing in country's post id and box size
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
?>
<form class="ajax_form" id="removeFromCartCrate" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
    <button class="btn text-danger h8 h7-md h6-lg font-weight-medium text-uppercase p-0" type="submit">
        Remove
    </button>
    <input type="hidden" name="action" value="remove_from_cart_crate" />
    <input type="hidden" name="country_id" value="<?php echo $args[0];?>" />
    <input type="hidden" name="crate_size" value="<?php echo $args[1];?>" />
</form>