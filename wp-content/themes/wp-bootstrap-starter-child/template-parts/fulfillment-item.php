<?php
// Template partial for individual item in an order that is to be fulfilled
$item = $args;
?>

<div class="col-6">
    <?php echo stripslashes($item->item_name);?>
    <?php if(isset($item->is_addon) && $item->is_addon == 1): ?>
        <div style="color: green; display: inline-block;">*Addon</div>
    <?php endif; ?>
</div>
<div class="col-3">
    <?php echo $item->code;?>
</div>
<div class="col-3 text-right" data-qty="<?php echo $item->quantity;?>">
    <?php echo $item->quantity;?>
</div>