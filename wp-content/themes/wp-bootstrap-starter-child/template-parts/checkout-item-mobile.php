<?php
/**
 * Template part for cart line item
 * Pass in snack post id and quantity
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$snack_id = $args[0];
$post_type = get_post_type($snack_id);
switch($post_type)
{
    case 'snack':
        $data = new SnackModel($snack_id);
        $thumbnail = $data->getThumbnail('large');
        $quantity = $args[1];
        $crate_size = null;
        $name = get_post_meta($snack_id, 'user-friendly-name', true);
        $internal_code = $data->meta['internal-id-code'][0];
        break;
    case 'country':
        $data = new CountryModel($snack_id);
        $thumbnail = $data->getFeaturedImage();
        $quantity = $args[1];
        $crate_size = $args[2];
        $name = get_post_meta($snack_id, 'user-friendly-name', true) . ' ' . CountryModel::$pretty_names[$crate_size];
        $internal_code = get_post_meta($snack_id, 'country-code', true) . $crate_size;
        break;
    case 'collection':
        $data = new CollectionModel($snack_id);
        $thumbnail = $data->getFeaturedImage();
        $quantity = $args[1];
        $crate_size = $args[2];
        $name = get_post_meta($snack_id, 'user-friendly-name', true);
        $internal_code = get_post_meta($snack_id, 'country-code', true) . $crate_size;
        break;
}
?>
<div class="cart-item-container bg-gray-light border-top">
    <div class="single-cart-item w-100 border-bottom">
        <div class="container py-4 d-flex align-items-start">
            <div class="cart-item-img mr-2">
                <img alt="image of <?php echo $name;?>" class="img-fluid" src="<?php echo $thumbnail;?>">
            </div>

            <div class="d-flex flex-column w-100">
                <div class="border-bottom d-flex align-items-start justify-content-between w-100">
                    <div class="d-flex flex-column justify-content-start">
                        <h4 class="h6 font-weight-semibold mb-1"><?php echo $name;?></h4>
                        <p class="h8 text-gray-dark font-weight-medium text-uppercase mb-1"> Item # <span><?php echo $internal_code;?></span></p>
                    </div>

                    <h3 class="h6 text-dark font-weight-semibold">$<span id="mobileTotalPrice_<?php echo $snack_id;?>"><?php echo number_format(Cart::getItemTotal($snack_id, $quantity, $crate_size),2);?></span></h3>
                </div>

                <div class="d-flex align-items-center justify-content-between w-100 mt-1">
                    <!-- <p class="h8 text-gray-dark font-weight-medium mb-0">Recurring Monthly</p> -->

                    <div class="d-flex align-items-center justify-content-center">
                        <label class="h8 font-weight-semibold text-gray-dark mb-0 mr-2" for="mobileCartQty_<?php echo $snack_id;?>">QTY:</label>
                        <input 
                            class="border-0 bg-gray-light h8 text-gray-dark text-center mobileCartQty" 
                            name="qty" 
                            id="mobileCartQty_<?php echo $snack_id;?>" 
                            type="number" 
                            min="1" 
                            max="<?php echo get_post_meta( $snack_id, 'in-stock', true ); ?>" 
                            data-posttype="<?php echo $post_type;?>"
                            data-snackid="<?php echo $snack_id;?>" 
                            data-price="<?php echo Cart::getItemTotal($snack_id, 1, $crate_size);?>"
                            value="<?php echo $quantity;?>">
                    </div>

                    <div>
                        <?php if($post_type == 'snack'):?>
                            <?php get_template_part( 'template-parts/remove-from-cart', get_post_format(), $snack_id );?>
                        <?php else: ?>
                            <?php get_template_part( 'template-parts/remove-from-cart-crate', get_post_format(), array($snack_id, $crate_size) );?>
                        <?php endif;?>
                    </div>
                </div>
            </div>

        </div>
    </div> <!-- single-cart-item -->

</div> <!-- cart-item-container -->