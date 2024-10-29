<?php
/**
 * Template part for reusing add crate to cart button.
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

global $post;
$include_price = ( $post->post_type == 'unboxing' );
?>

<form class="ajax_form" id="addToCartCrate" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
    <div class="d-flex align-items-center">
        <div class="position-relative select-quantity mr-3 mr-xl-5">
            <label class="h8 h6-lg font-weight-bold mb-0 position-absolute" for="itemQuantity">Quantity</label>
            <select 
                class="custom-select" 
                name="itemQuantity" 
                id="itemQuantity" 
                <?php echo get_post_type() == 'collection' ? 'data-stock="'.$args->getStock( $args->getCrateSize(), false ).'"' : '';?>
                <?php echo get_post_type() == 'collection' && $args->checkIfPreorder() ? 'data-preorder="1"' : '';?>
            >
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <!-- <option value="10">10</option> -->
            </select>
        </div>
        <!-- adds selected snackcrate to cart, is disabled until a crate size (mini, reg, family) is selected -->
        <?php if( $args->checkInStock() == 0 && !$args->checkIfPreorder() ):?>
            <button class="add_to_cart btn btn-sm h6-md h5-lg text-white px-4" type="button" id="outOfStock" disabled>Out Of Stock</button>
        <?php else:?>
            <button class="add_to_cart btn btn-sm bg-secondary h6-md h5-lg text-white px-4" type="submit" id="addToCart" <?php echo get_post_type() == 'country' ? 'disabled' : '';?>>Add to Cart<?php echo $include_price ? " ($".$args->getPrice().")" : ""; ?></button>
            <?php endif;?>
        <input type="hidden" name="action" value="add_to_cart_crate" />
        <input type="hidden" id="drink_addon" name="drink_addon" value="0" />
        <input 
            type="hidden" 
            name="crate_type" 
            id="crate_type" 
            value="<?php echo get_post_type( $args->getId() ) == 'collection' ? $args->getCrateSize() : '';?>" 
        />
        <input type="hidden" name="crate_country" id="crate_country" value="<?php echo $args->getId();?>" />
        <input type="hidden" id="drink_price" value="<?php echo CountryModel::$drink_price;?>" />
    </div>
</form>