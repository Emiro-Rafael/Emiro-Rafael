<?php
/**
 * Template part for displaying cart
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$cart = new Cart();
?>

<?php foreach($cart->getCartItems() as $post_id => $item) : ?>
    <?php if(get_post_type($post_id) == 'snack') :?>
        <div class="snack-row">
            <span class="snack-name"><?php echo get_post_meta( $post_id, 'user-friendly-name', true );?></span>
            <span class="snack-quantity"><?php echo $item;?></span>
            <span class="snack-total-price"><?php echo Cart::getItemTotal($post_id, $item);?></span>
            <?php get_template_part( 'template-parts/remove-from-cart', get_post_format(), $post_id );?>
        </div>
    <?php elseif(get_post_type($post_id) == 'country' || get_post_type($post_id) == 'collection') :?>
        <?php foreach($item as $box_type => $quantity) :?>
            <div class="snack-row">
                <span class="snack-name"><?php echo get_the_title($post_id) ." ". ucwords($box_type);?></span>
                <span class="snack-quantity"><?php echo $quantity;?></span>
                <span class="snack-total-price"><?php echo Cart::getItemTotal($post_id, $quantity, $box_type);?></span>
                <?php get_template_part( 'template-parts/remove-from-cart-crate', get_post_format(), array($post_id, $box_type) );?>
            </div>
        <?php endforeach;?>
    <?php endif;?>
<?php endforeach;?>
