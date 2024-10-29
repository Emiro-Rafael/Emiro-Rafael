<?php
/**
 * Template part for displaying snack price.
 * pass in snack object
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$snack = $args; 
global $user_data;
if( !empty($user_data) )
{
    $has_subscription = get_user_meta( get_user_by('email', $user_data->email)->ID, 'has_subscription', true );
}
else
{
    $has_subscription = false;
}
?>

<div class="prices h7 h6-lg mb-0">
    <span <?php echo $has_subscription ? 'style="text-decoration:line-through;"' : '';?>>$<?php echo $price = get_post_meta( $snack->getId(), 'price', true );?></span> 

    
    <?php if($has_subscription) : ?>
        <span>$<?php echo $snack->getDiscount($price);?></span>
    <?php endif;?>
</div>