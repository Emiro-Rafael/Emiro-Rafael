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
        $max_stock = $data->checkIfPreorder() ? 1000 : $data->getStock();
        break;
    case 'country':
        $data = new CountryModel($snack_id);
        $thumbnail = $data->getFeaturedImage();
        $quantity = $args[1];
        $crate_size = $args[2];
        $name = get_post_meta($snack_id, 'user-friendly-name', true) . ' ' . CountryModel::$pretty_names[$crate_size];
        $internal_code = get_post_meta($snack_id, 'country-code', true) . $crate_size;
        $max_stock = $data->checkIfPreorder() ? 1000 : $data->getStock( $crate_size );
        break;
    case 'collection':
        $data = new CollectionModel($snack_id);
        $thumbnail = $data->getFeaturedImage();
        $quantity = $args[1];
        $crate_size = $args[2];
        $name = get_post_meta($snack_id, 'user-friendly-name', true);
        $internal_code = get_post_meta($snack_id, 'country-code', true) . $crate_size;
        $max_stock = $data->checkIfPreorder() ? 1000 : $data->getStock( $crate_size );
        break;
}
?>
<tr class="single-cart-item border-bottom" data-rowtype="<?php echo $post_type;?>">
                            
    <td class="py-4 pl-3 pl-lg-4 pl-xl-5">
        <div class="cart-item-img mr-2 mr-lg-3">
            <img alt="image of <?php echo $name;?>" class="img-fluid" src="<?php echo $thumbnail;?>">
        </div>
    </td>

    <td>
        <div class="d-flex flex-column justify-content-start my-lg-3">
            <h4 class="h5 h4-lg font-weight-semibold mb-1 mb-lg-2"><?php echo $name;?></h4>
            <p class="h7 h6-lg text-dark font-weight-medium text-uppercase mb-0"> Item # <span><?php echo $internal_code;?></span></p>
            <!-- <p class="h7 h6-lg text-gray-dark font-weight-medium mb-0">Recurring Monthly</p> -->
        </div>
    </td>
    <td>
        <h3 class="h6 h5-lg h4-xl mb-0 text-dark font-weight-medium">$<span><?php echo number_format(Cart::getItemTotal($snack_id, 1, $crate_size),2);?></span></h3>
    </td>

    <td>
        <div class="h6 h5-lg h4-xl text-gray mx-3 mb-0"><i class="fas fa-times d-none"></i></div>
    </td>

    <td>
        <div class="d-flex align-items-center">
            <label class="h6 h5-lg h4-xl font-weight-semibold text-gray-dark mb-0 mr-3 sr-only" for="cartQty_<?php echo $snack_id;?>">QTY:</label>
            <div class="d-flex align-items-center">
                <input 
                    class="border-0 bg-gray-light h6 h5-lg h4-xl text-gray-dark mb-0 text-center cartQty" 
                    type="number" 
                    name="qty" 
                    id="cartQty_<?php echo $snack_id;?>" 
                    data-snackid="<?php echo $snack_id;?>" 
                    data-posttype="<?php echo $post_type;?>"
                    data-price="<?php echo Cart::getItemTotal($snack_id, 1, $crate_size);?>"
                    <?php echo $post_type == 'snack' ? '' : "data-cratesize=\"{$crate_size}\"";?>
                    min="1" 
                    max="<?php echo $max_stock; ?>" 
                    value="<?php echo $quantity;?>" 
                />
            </div>
        </div>
    </td>

    <td>
        <div class="h6 h5-lg h4-xl text-gray mx-3 mb-0"><i class="fas fa-equals d-none"></i></div>
    </td>

    <td>
        <h3 class="h6 h5-lg h4-xl mb-0 text-dark font-weight-medium">$<span id="totalPrice_<?php echo $snack_id;?>"><?php echo number_format(Cart::getItemTotal($snack_id, $quantity, $crate_size), 2);?></span></h3>
    </td>

    <td class="pr-3 pr-lg-5" style="text-align: right;">
        <div>
            <?php if($post_type == 'snack'):?>
                <?php get_template_part( 'template-parts/remove-from-cart', get_post_format(), $snack_id );?>
            <?php else: ?>
                <?php get_template_part( 'template-parts/remove-from-cart-crate', get_post_format(), array($snack_id, $crate_size) );?>
            <?php endif;?>
        </div>
    </td>
</tr>
