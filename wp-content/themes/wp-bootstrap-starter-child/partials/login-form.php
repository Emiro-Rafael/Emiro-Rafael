<form name="signInForm" class="ajax_form w-100" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                
    <div class="modal-body mt-2 w-100 px-0">
        <p class="form-error"></p>

        <div class="sign-in-email mb-2">
            <label class="sr-only" for="user_login">Email Address</label>
            <input type="text" name="email" id="user_login" class="input" value="" placeholder="Email Address">
        </div>

        <div id="signIn" class="sign-in-password position-relative mb-2">
            <label class="sr-only" for="password">Password</label>
            <input type="password" name="pwd" id="password" class="input" value="" placeholder="Password">
            <div class="password-toggle text-gray position-absolute">
                <label id="showPassword">
                    <i class="fas fa-eye"></i>
                    <input type="checkbox" class="d-none" aria-label="Checkbox for toggling password visibility">
                </label>
            </div>
        </div>
        
        <div class="justify-content-between mb-4">
            <a class="h7 h6-md text-gray-dark font-weight-medium" target="_blank" href="https://snackcrate.com/password-recovery/">Forgot your password?</a>
            
            <!--
            <div class="sign-in-keep my-4">
                <input name="keepSignedIn" type="checkbox" id="keepSignedIn" value="">
                <label for="keepSignedIn" class="text-capitalize position-relative ml-1"> Keep me signed in</label>
            </div>
            -->
        </div>
    </div>
    
    <div class="modal-footer align-items-center p-0 border-0 w-100">
        <div class="container p-0 mx-auto w-100">
            <div class="sign-in-submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="h6-md py-md-2 btn btn-sm btn-secondary font-weight-semibold text-white w-100" value="Sign In">
                <input type="hidden" name="action" value="sc_login">
            </div>
        </div>
    </div>
</form>
<div class="thirdpartylogins w-100 mx-0 my-4">
    <div class="alert callout emptymessage" id="thirdpartynotification" style="display:none;"></div>
    <button 
        type="button"
        class="w-100 appleid-signin"
    >
        <i class="fab fa-apple"></i>
        Sign in with Apple
    </button>
</div>