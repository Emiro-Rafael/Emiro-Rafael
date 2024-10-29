<?php 
$picker_number = $args->getPickers();
?>

<section id="tertiary" class="">
    
    <form class="ajax_form mx-auto mt-5" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>" >
        <input type="text" name="pickers" value="<?php echo $picker_number;?>" />
        <input type="hidden" name="action" value="picker_number" />
        <button type="submit" class="btn btn-sm btn-secondary text-white">Update Pickers</button>
    </form>

    <form class="ajax_form mx-auto mt-5" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>" >
        <label>I am picker #:</label>
        <?php for($i = 0; $i < $picker_number; $i++):?>
            <div>
                <input type="radio" name="user_number" value="<?php echo $i;?>" <?php echo $_SESSION['picker_user'] == $i ? 'checked' : '';?>/> <?php echo $i+1;?>
            </div>
        <?php endfor;?>
        <input type="hidden" name="action" value="user_picker_number" />
        <button type="submit" class="btn btn-sm btn-primary text-white">Set #</button>
    </form>

</section>