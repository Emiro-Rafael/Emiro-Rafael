<!-- Billing address block. Hidden by default -->
<div id="billing_block" class="d-none row">
                        
    <h3 class="h4 h3-md h2-xl font-weight-bold mb-3 mb-md-4 col-12">Billing Address</h3>
    
    <div class="mb-2 mb-md-1 col-12 col-md-6">
        <label class="sr-only" for="billing_firstname">First Name</label>
        <input type="text" name="billing_firstname" id="billing_firstname" class="input" value="" placeholder="First Name">
    </div>

    <div class="mb-2 mb-md-1 col-12 col-md-6">
        <label class="sr-only" for="billing_lastname">Last Name</label>
        <input type="text" name="billing_lastname" id="billing_lastname" class="input" value="" placeholder="Last Name">
    </div>

    <div class="mb-2 mb-md-1 col-12">
        <label class="sr-only" for="billing_address_1">Address Line 1</label>
        <input type="text" name="billing_address_1" id="billing_address_1" class="input" value="" placeholder="Address Line 1">
    </div>

    <div class="mb-2 mb-md-1 col-12">
        <label class="sr-only" for="billing_address_2">Address Line 2</label>
        <input type="text" name="billing_address_2" id="billing_address_2" class="input" value="" placeholder="Address Line 2">
    </div>

    <div class="mb-2 mb-md-1 col-12 col-md-6">
        <label class="sr-only" for="billing_city">City</label>
        <input type="text" name="billing_city" id="billing_city" class="input" value="" placeholder="City">
    </div>

    <div class="mb-2 mb-md-1 col-12 col-md-6">
        
        <?php get_template_part( 'template-parts/state-dropdown', get_post_format(), array( "billing_state", $user_state ) );?>
        
    </div>

    <div class="mb-2 mb-md-1 col-12 col-md-6">
        <label class="sr-only" for="billing_zipcode">Postal</label>
        <input type="text" name="billing_zipcode" id="billing_zipcode" class="input" value="" placeholder="Postal">
    </div>
    
    <div class="mb-2 mb-md-1 col-12 col-md-6">
        <label class="sr-only" for="country">Country</label>
        <input type="text" readonly name="country" id="country" class="input" value="United States of America" placeholder="Country">
    </div>
</div>