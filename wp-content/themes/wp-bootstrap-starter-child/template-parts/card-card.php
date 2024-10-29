<?php
/**
 * Template part address card. Pass in address data as it would appear in address table
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$card = $args[0];
$default_card_id = $args[1];
?>

<div class="p-2 card-container float-left swiper-slide <?php echo $default_card_id == $card->id ? 'selected' : '';?>" data-card="<?php echo $card->id;?>">
    <div class="card p-2 equalHeight px-3 p-xl-4 justify-content-between">
        
        <img class="mt-2" style="max-width: 25%;" alt="<?php echo $card->brand;?> logo" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/<?php echo $card->brand;?>.png">
        <div class="my-2">
            <div class="font-weight-bold"><?php echo $card->name;?>&nbsp;</div>
            <div class="font-weight-bold">XXXX XXXX XXXX <?php echo $card->last4;?></div>
            <div class="text-gray">Valid thru: <?php echo $card->exp_month;?>/<?php echo substr($card->exp_year, 2);?></div>
        </div>
        <!-- <div class="mt-2"><span data-toggle="modal" data-target="#editPaymentModal-<?php echo $card->id;?>" class="text-gray">Edit</span></div> -->

    </div>
    <i class="selected-check fas fa-check"></i>
</div>
