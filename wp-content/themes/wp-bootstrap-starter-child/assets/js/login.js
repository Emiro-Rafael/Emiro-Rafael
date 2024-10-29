jQuery( document ).ready(function() {
    jQuery('#signIn input[type="checkbox"]').on('change', function () {
        jQuery(this).prev().toggleClass('fa-eye').toggleClass('fa-eye-slash');
        let inputType =
            jQuery('#signIn #password').prop('type') === 'password'
                ? 'text'
                : 'password';
        jQuery('#signIn #password').prop('type', inputType);
    });
});