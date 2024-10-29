<?php
$show_optin = !$args;
?>
<div class="container p-1" id="optin_block">
    <div class="row">
        <div class="mb-2 styled-check col-12 pl-xxl-0">
            <input type="checkbox" name="billing_same" id="billing_same" class="input" checked />
            <label class="text-gray col-11" for="billing_same">My billing and delivery information are the same.</label>
        </div>

        <?php if($show_optin):?>
            <div class="mb-2 styled-check col-12 pl-xxl-0">
                <input type="checkbox" name="optin" id="optin" class="input" checked />
                <label class="text-gray col-11" for="optin">Yes, I would like to receive emails about product updates, exclusive sales, and more.</label>
            </div>
        <?php endif;?>
    </div>
</div>