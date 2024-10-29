const actionUrl = "/wp-admin/admin-ajax.php";
const confirmPaymentText = "Place Order";

jQuery(document).ready(function() {
    var $ = jQuery;

    if(document.body.dataset.namespace == 'checkout-guest' || document.body.dataset.namespace == 'checkout-confirm-shipping')
    {
        $("#billing_same").on( 'change', function() {

            if( $(this).is(':checked') )
            {
                $("#billing_block").addClass("d-none");
            }
            else
            {
                $("#billing_block").removeClass("d-none");
            }

        });

        $("[data-addressid]").on('click', function(){
            let selected_address = $(this).data('addressid');
            if( selected_address != 0)
            {
                $("input[name='shipping_address']").val(selected_address);
                $(this).siblings().removeClass('selected');
                $("[data-addressid="+selected_address+"]").addClass('selected');
            } 
        });

        const emailErrorText = $('.email-exists-error');
        $("#email").on('change', function(){
            $.ajax({
                url: actionUrl,
                type: "POST",
                data: {
                    email: $("#email").val(),
                    action: 'sc_check_guest_email'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.data === true)
                    {   
                        emailErrorText.html('<p class="mb-2 mb-md-3 text-danger">Oops! It looks like an account with that email already exists. Please <a id="scrollToLogin" class="text-danger text-decoration-underline" href=" #guestCheckoutSignin">Sign In</a> or try a different email address.</p>');
                        $(emailErrorText).removeClass("d-none");

                        $("#scrollToLogin").on('click', function(evt){
                            evt.preventDefault();

                            if( $("#guestCheckoutSignin:visible").length == 0)
                            {
                                $("#userAccount").click();
                            }
                            else
                            {
                                $("#guestCheckoutSignin").addClass("border border-danger");
                                $([document.documentElement, document.body]).animate({
                                    scrollTop: $("#guestCheckoutSignin").offset().top - 80
                                }, 500);
                            }
                        });

                        return false;
                    }
                    else
                    {
                        $(emailErrorText).addClass("d-none");
                    }
                }
            });
        });
    }
    else if( document.body.dataset.namespace == 'checkout-confirm-pay' )
    {
        let style = {
            base: {
                fontSize: '16px',
                color: "#32325d",
            }
        };

        var stripe;
        if (window.location.href.indexOf('local') !== -1 || window.location.href.indexOf('snackbar') !== -1 || window.location.href.indexOf('candybar-dev') !== -1) {
            stripe = window.Stripe('pk_test_n3rrzyh2ClIZlnCntL9X8IRx');
        }
        else
        {
            stripe = window.Stripe('pk_live_3L8cvOKHqpOpDF3JXfZqZTd2');
        }
        
        let elements = stripe.elements();

        let card = elements.create('card', {
            'hidePostalCode': true,
            'style': style
        });

        card.mount('#card-element');

        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) 
            {
                displayError.textContent = event.error.message;
            }
            else 
            {
                displayError.textContent = '';
            }
        });

        if( $("[data-card]").length > 0 )
        {
            $("[data-card]").on('click', function(){
                let selected_source = $(this).data('card');
                
                if(selected_source != 0)
                {
                    $("input[name='source']").val(selected_source);
                    $(this).siblings().removeClass('selected');
                    $("[data-card="+selected_source+"]").addClass('selected');
                }
            });

            $("#newCustomerCard").on('submit', function(event){
                event.preventDefault();
                
                let submitBtn = $(this).find('[type="submit"]').first();
                disableSubmit(submitBtn);

                stripe.createToken(card)
                    .then(function(result){
                        if(result.error)
                        {
                            jQuery(submitBtn).removeAttr("disabled").text("Save");
                            // Inform the customer that there was an error.
                            var errorElement = document.getElementsByClassName('form-error')[0];
                            errorElement.textContent = result.error.message;
                            $('#signup-customer').removeClass('avoid-clicks');
                        }
                        else
                        {
                            let postData = {
                                'name': $("#ccNameUpdate").val(),
                                'action': 'sc_update_payment_method',
                                'token': result.token.id,
                            };

                            submitAjaxForm(actionUrl, postData, "Save", submitBtn);
                        }
                    });
            });

            $("#confirmPaymentForm").on('submit', function(event){
                event.preventDefault();

                let submitBtn = $(this).find('[type="submit"]').first();

                let postData = {
                    'action': 'sc_confirm_payment',
                    'source': $("[name='source']").val(),
                };

                disableSubmit(submitBtn);
                
                submitAjaxForm(actionUrl, postData, confirmPaymentText, submitBtn);
            });
        }
        else
        {
            $("#confirmPaymentForm").on('submit', function(event){
                event.preventDefault();

                let submitBtn = $(this).find('[type="submit"]').first();
                disableSubmit(submitBtn);

                runStripe(stripe, card, submitBtn);
            });
        }
    }
});


function runStripe(stripe, card, submitBtn) {
    APev = false;
    CCev = false;

    stripe.createToken(card)
        .then(function(result){
            if(result.error)
            {
                jQuery(submitBtn).removeAttr("disabled").text(confirmPaymentText);
                // Inform the customer that there was an error.
                var errorElement = document.getElementById('card-errors');
                var loggedInError = document.querySelector('.loggedInCardErrors')
                errorElement.textContent = result.error.message;
                loggedInError.style.backgroundColor = '#DC3545'
                loggedInError.textContent = result.error.message;

                $('#signup-customer').removeClass('avoid-clicks');
            }
            else
            {
                document.querySelector('.placeOrderCheckout').textContent = "Processing..."
                let postData = {
                    'action': 'sc_confirm_payment',
                    'payment_method': 'card',
                    'token': result.token.id,
                };
                
                submitAjaxForm(actionUrl, postData, confirmPaymentText, submitBtn);
            }
        });
}