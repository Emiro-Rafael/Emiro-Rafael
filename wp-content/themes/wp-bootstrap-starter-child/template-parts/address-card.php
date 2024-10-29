<?php
/**
 * Template part address card. Pass in address data as it would appear in address table
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$address = $args[0];
$default_address_id = $args[1];
?>
<div class="p-2 card-container swiper-slide <?php echo $default_address_id == $address->id ? 'selected' : '';?>" data-addressid="<?php echo $address->id;?>">
    <div class="card equalHeight p-3 px-md-4 p-xl-4">
        <p class="font-weight-bold mb-2 mt-xl-0"><?php echo $address->shipping_name;?></p>

        <div>
            <p class="mb-0"><?php echo $address->address_1;?></p>
            <?php if( !empty($address->address_2) ):?>
                <p class="mb-0"><?php echo $address->address_2;?></p>
            <?php endif;?>
            <p class="mb-0"><?php echo $address->city;?>, <?php echo $address->state;?> <?php echo $address->zipcode;?>, <?php echo str_replace('United States of America', 'USA', $address->country);?></p>
            <p class="mb-0"><?php echo $address->phone;?></p>
        </div>

        <!-- <div class="my-2"><span data-toggle="modal" data-target="#editShippingAddressModal-<?php echo $address->id;?>" class="text-gray">Edit</span></div> -->
    </div>
    <i class="selected-check fas fa-check"></i>
</div>