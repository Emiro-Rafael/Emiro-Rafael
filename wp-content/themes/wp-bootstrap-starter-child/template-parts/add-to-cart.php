<?php
/**
 * Template part for reusing add to cart button. Requires passing in snack's post id
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
if( is_array($args) )
{
    $snack = new SnackModel($args['snack_id']);
    $include_price = $args['include_price'];
    $snack_id = $args['snack_id'];
}
else
{
    $snack = new SnackModel($args);
    global $post;
    $include_price = ( $post->post_type == 'unboxing' );
    $snack_id = $args;
}
if( $snack->getStock(null,false) > $snack->getMinimumStock() || ( !empty($snack->getShippingDate()) && $snack->getShippingDate() > date('Y-m-d') ) ) : // decide on a threshold here
?>
<form class="ajax_form d-flex align-items-center" id="addToCart" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">

    <div class="position-relative select-quantity mr-3 mr-lg-4 mr-xl-5">
        <label class="h8 h6-lg font-weight-bold mb-0 position-absolute bg-white text-gray-dark" for="cartQty">Quantity</label>
        <select class="bg-white custom-select" name="qty" id="cartQty" type="number">
            <?php 
            for($i = 1; $i <= 10; $i++):
                if($i > $snack->getStock(null,false))
                {
                    break;
                }
                ?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
            <?php endfor;?>
        </select>
    </div>

    <button class="add_to_cart btn btn-sm btn-secondary h6-md h5-lg text-white px-4" type="submit">Add to Cart<?php echo $include_price ? " ({$snack->getCurrentUserPrice()})" : ""; ?></button>
    <input type="hidden" name="action" value="add_to_cart" />
    <input type="hidden" name="snack_id" value="<?php echo $snack_id;?>" />
</form>
<?php elseif($snack->getStock(null,true) > $snack->getMinimumStock()) : // check if current user has added current snack to cart, but cannot add anymore ?>
    <div class="text-danger font-weight-semibold mb-4 pb-xl-2">Cannot add anymore of this snack.</div>
<?php elseif(!$include_price) : ?>
    <div class="text-danger font-weight-semibold mb-4 pb-xl-2">OUT OF STOCK</div>
<?php else : ?>
    <div class="button-height-equalizer">&nbsp;</div>
<?php endif; ?>