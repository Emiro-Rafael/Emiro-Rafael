jQuery(document).ready(function() {
    var $ = jQuery;

    const email = $('#email');
    const userFirst = $('#firstname');
    const userLast = $('#lastname');
    const userAddress1 = $('#address_1');
    const userCity = $('#city');
    const userZip = $('#zipcode');
    const userPhone = $('#phone');
    const errorText1 = $('.form-error-1');
    const errorText2 = $('.form-error-2');
    const errorText3 = $('.form-error-3');
    const errorText4 = $('.form-error-4');
    const emailErrorText = $('.email-exists-error');

    // guestCheckoutForm validation
    $(".guest-checkout-form").on('submit', function(e){

        $(emailErrorText).addClass("d-none");
        e.preventDefault();
        var submitBtn = $(".guest-checkout-form").find('[type="submit"]').first();
        var originalText = jQuery(submitBtn).text();

        jQuery(submitBtn).attr('disabled', 'disabled');
        jQuery(submitBtn).text('');
        jQuery(submitBtn).html(`<div class="spinner-border text-light" role="status">
        <span class="sr-only">Loading...</span>
      </div>`);
        
        let submitForm = true;
        
        // check if any inputs were left blank
        if( userFirst.val().length === 0 || userLast.val().length === 0 || email.val().length === 0 || userAddress1.val().length === 0 || userCity.val().length === 0 || userZip.val().length === 0 || userPhone.val().length === 0) {
            submitForm = false;
            errorText1.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">One or more fields were left blank</p>');
        } else {
            errorText1.html('');
        }

        // check if userZip is at least 5 digits
        function checkZipcode(zipcode) {
            var regex = /^\d{5}$/;
            return regex.test(zipcode);
        }

        if(!checkZipcode(userZip.val())) {
            submitForm = false;
            errorText2.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Zipcode must be 5 digits</p>');
        } else {
            errorText2.html('');
        }

        // check if userPhone is at least 10 digits
        function checkPhone(phoneNum) {
            var regex = /^\d{10}$/;
            return regex.test(phoneNum);
        }

        if(!checkPhone(userPhone.val())) {
            submitForm = false;
            errorText4.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Phone number must be 10 digits</p>');
        } else {
            errorText4.html('');
        }
        
        // check if email address is valid
        function checkEmail(emailAddress) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,6})+$/;
            return regex.test(emailAddress);
        }

        if(!checkEmail(email.val())) {
            submitForm = false;
            errorText3.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Please enter a valid email address</p>');
        } else {
            errorText3.html('');
        }

        if( submitForm )
        {
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: "POST",
                async: false,
                data: {
                    email: email.val(),
                    action: 'sc_check_guest_email'
                },
                dataType: 'json',
                success: function(response) {
                    console.log(e);
                    if(response.data === true)
                    {   
                        emailErrorText.html('<p class="mb-2 mb-md-3 text-danger">Oops! It looks like an account with that email already exists. Please <a class="text-danger text-decoration-underline" href="https://account.snackcrate.com" target="_blank">Sign In</a> or try a different email address.</p>');
                        $(emailErrorText).removeClass("d-none");

                        return false;
                    }
                    else
                    {
                        let postData = {};
                        for(const field of $(".guest-checkout-form").serializeArray())
                        {
                            postData[field.name] = field.value;
                        }
                        submitAjaxForm('/wp-admin/admin-ajax.php', postData, originalText, submitBtn);
                    }
                },
                error: function(error) {
    
                }
            });
        }
        else
        {
            jQuery(this).find('[type="submit"]').first().removeAttr('disabled');
            jQuery(this).find('[type="submit"]').first().html('');
            jQuery(this).find('[type="submit"]').first().text(originalText);
        }
    });
});

var $ = jQuery;
let doAction;

function validateCreditCardInputs (number, month, year, cvv, error1, error2, error3, error4) {
    // check if any inputs were left blank
    if( number.val().length === 0 || month.val().length === 0 || year.val().length === 0 || cvv.val().length === 0) {
        doAction = false;
        error1.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">One or more fields were left blank</p>');
    } else {
        error1.html('');
        doAction = true;
    }

    // check if ccNumber is 13-16 digits
    function checkCCNumber(ccNum) {
        var regex = /^\b\d{13,16}\b$/;
        return regex.test(ccNum);
    }

    if(!checkCCNumber(number.val())) {
        doAction = false;
        error2.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Credit card number must be between 13 - 16 digits</p>');
    } else {
        error2.html('');
        doAction = true;
    }

    // check if ccExpMonth and ccExpYear are 2 digits
    function checkExpDate(ccDate) {
        var regex = /^\d{2}$/
        return regex.test(ccDate);
    }

    if(!checkExpDate(month.val()) || !checkExpDate(year.val())) {
        doAction = false;
        error3.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Expiration month and year must each be 2 digits</p>');
    } else {
        error3.html('');
        doAction = true;
    }

    // check if CVV are 3-4 digits
    function checkCvv(ccCvv) {
        var regex = /^\b\d{3,4}\b$/;
        return regex.test(ccCvv);
    }

    if(!checkCvv(cvv.val())) {
        doAction = false;
        error4.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">CVV must be 3 - 4 digits</p>');
    } else {
        error4.html('');
        doAction = true;
    }

    return doAction;
}

function validateCreateAccountInputs (email, password, error1, error2, error3) {
    // check if any inputs were left blank
    if( email.val().length === 0 || password.val().length === 0 ) {
        doAction = false;
        error1.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">One or more fields were left blank</p>');
    } else {
        error1.html('');
        doAction = true;
    }

    // check if email address is valid
    function checkEmail(emailAddress) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,6})+$/;
        return regex.test(emailAddress);
    }

    if(!checkEmail(email.val())) {
        doAction = false;
        error2.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Please enter a valid email address</p>');
    } else {
        error2.html('');
        doAction = true;
    }

    // check if password is valid
    function checkPassword(password) {
        var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/;
        return regex.test(password);
    }

    if(!checkPassword(password.val())) {
        doAction = false;
        error3.html('<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">Passwords must be longer than eight characters and include at least one uppercase letter, one lowercase letter, and one number</p>');
    } else {
        error3.html('');
        doAction = true;
    }

    return doAction;
}