<?php
/**
 * Template part for reusing remove from cart button. Requires passing in snack's post id
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
?>
<form class="ajax_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
    <button class="btn text-danger h8 h7-md h6-lg font-weight-medium text-uppercase p-0" type="submit">
        Remove
    </button>
    <input type="hidden" name="action" value="remove_from_cart" />
    <input type="hidden" name="snack_id" value="<?php echo $args;?>" />
</form>