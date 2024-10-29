document.addEventListener('AppleIDSignInOnSuccess', (data) => {
    let detail = data.detail;
    detail.origin = 'login';

    jQuery.ajax({
        url: "/wp-content/themes/wp-bootstrap-starter-child/lib/AppleSignin.php",
        type: "POST",
        beforeSend: function(xhr){xhr.setRequestHeader('csrf_token', jQuery('meta[name="csrf-token"]').attr('content'));},
        data: detail ,
        dataType: "json",
        async: true,
        success: function(response) {
            let r = (Math.random() + 1).toString(36).substring(2);

            let actionUrl = "/wp-admin/admin-ajax.php";
            let postData = {
                'pwd': r,
                'email': response.data.email,
                'method': 'AppleID',
                'action': 'sc_login',
            };
            submitAjaxForm(actionUrl, postData, '');
        },
        error: function(error) {
            console.error('Error: ', error);
            jQuery(".form-error").show().text(error.responseJSON.data);
        }
    });
});
//Listen for authorization failures
document.addEventListener('AppleIDSignInOnFailure', (error) => {
    console.error(error);
});

var CCev = false;
var APev = false;
jQuery(document).ready(function() {

    if(document.body.dataset.namespace == 'checkout-confirm-pay')
    {
        var stripeAP;
        let total = parseInt( Math.round(document.getElementsByName('total_amount')[0].value * 100) );
        if (window.location.href.indexOf('local') !== -1 || window.location.href.indexOf('snackbar') !== -1 || window.location.href.indexOf('candybar-dev') !== -1) {
            stripeAP = Stripe('pk_test_n3rrzyh2ClIZlnCntL9X8IRx');
        }
        else
        {
            stripeAP = window.Stripe('pk_live_3L8cvOKHqpOpDF3JXfZqZTd2');
        }

        var paymentRequestAP = stripeAP.paymentRequest({
            country: 'US',
            currency: 'usd',
            total: {
                label: "SnackCrate",
                amount: total,
            },
            requestPayerName: false,
            requestPayerEmail: false,
            requestShipping: false,
        });

        var elements = stripeAP.elements();
        var prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequestAP,
            style: {
                paymentRequestButton: {
                    type: 'default', // default: 'default'
                    theme: 'dark', // default: 'dark'
                    height: '56px', // default: '40px', the width is always '100%'
                },
            },
        });
    
        // Check the availability of the Payment Request API first.
        paymentRequestAP.canMakePayment().then(function(result) {
            if (result) {
                prButton.mount('#payment-request-button-applepay');
                document.getElementById('apple-pay-window').style.display = 'block';
            } else {
                document.getElementById('apple-pay-window').style.display = 'none';
            }
        });
    
        paymentRequestAP.on('token', function(ev) {
            // Send the token to your server to charge it!
            console.log(ev)
            ev.complete('success');
            CCev = false;
            APev = ev;
            
            let response = (APev) ? APev : CCev;

            let actionUrl = "/wp-admin/admin-ajax.php";
            let postData = {
                'action': 'sc_confirm_payment',
                'payment_method': response.methodName,
                'token': response.token.id,
            };
            
            submitAjaxForm(actionUrl, postData, '');
        });
        paymentRequestAP.on('cancel', function(ev) {
            // Send the token to your server to charge it!
            console.log('fail');
            ev.complete('fail');
        });
    }    
});
