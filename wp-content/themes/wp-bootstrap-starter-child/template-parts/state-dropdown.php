<?php
// common state dropdown. pass in element name to use for form posting, and what state should be selected by default (if any)
$element_name = $args[0];
$user_state = $args[1];
?>
<label class="sr-only" for="<?php echo $element_name;?>">State</label>
<select class="form-control state-select" id="<?php echo $element_name;?>" name="<?php echo $element_name;?>" <?php echo $element_name == 'billing_state' ? '' : 'required';?>>
    <option value="" disabled selected>Select State</option>
    <?php 
    foreach( Address::getStates() as $abbreviation => $state )
    {
        if( !empty($user_state) && ( $state == $user_state || $abbreviation == $user_state ) )
        {
            echo "<option value='{$state}' selected>{$state}</option>";
        }
        else
        {
            echo "<option value='{$state}'>{$state}</option>";
        }
    }
    ?>
</select>