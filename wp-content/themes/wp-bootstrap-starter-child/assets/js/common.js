const email = $('#user_email');
const password = $('#user_password');
const ccNumber = $('#ccNumber');
const ccExpMonth = $('#ccExpMonth');
const ccExpYear = $('#ccExpYear');
const ccCvv = $('#ccCvv');
const errorText1 = $('.form-error-1');
const errorText2 = $('.form-error-2');
const errorText3 = $('.form-error-3');
const errorText4 = $('.form-error-4');

jQuery(window).load(function(){
    jQuery("[type='submit']").each(function(){
        jQuery(this).css('pointer-events', 'auto').css('opacity', 1);
    });

    // take the select to change as a parameters
    function updateSelect(select) {
        // get the index of the selected option
        const index = select.selectedIndex;
        // check if the first option is selected (index 0)
        if (index === 0) {
        // if selected, add 'grey' class to change text color
        select.classList.remove('text-dark');
        } else {
        // if not selected, remove 'grey' class
        select.classList.add('text-dark');
        }
    }
    
    // Event listener fo the select 'change' event
    function change(event) {
        // get the changed select from the event object
        const select = event.target;
        // update this select
        updateSelect(select);
    }
    
    // find all the 'select' elements
    const selects = document.querySelectorAll('select');
    // for each select, add change event listener and update them
    selects.forEach((select) => {
        // add change listener
        select.addEventListener('change', change);
        // update select
        updateSelect(select);
    });

    // adjust position of shipping-truck based on width of progress
    if( $(window).width() < 992) {
        if ( $('.progress').width() < $('.shipping-truck').width() - 15 ) {
            $('.shipping-truck').css('left', '0');
        } else {
            $('.shipping-truck').css('left', 'calc(100% - 47px)');
        }

        if( $('.progress').width() < $('.progress-bar').width()) {
            $('.progress-bar').css('width', '100%');
        } else {
            $('.progress-bar').css('width', 'calc(100% - 15px)');
        }
    } else {
        if ( $('.progress').width() < $('.shipping-truck').width() - 22 ) {
            $('.shipping-truck').css('left', '0');
        } else {
            $('.shipping-truck').css('left', 'calc(100% - 70px)');
        }

        if( $('.progress').width() < $('.progress-bar').width() ) {
            $('.progress-bar').css('width', '100%');
        } else {
            $('.progress-bar').css('width', 'calc(100% - 22px)');
        }
    }
});

jQuery(document).ready(function() {
    var $ = jQuery;

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(function () {
        $('[data-toggle="popover"]').popover({html:true})
    })

    // close predictive search window if a click outside of that element occurs
    $(document).click(function(event) { 
        var $target = $(event.target);
        if( !$target.closest('#predictive-search').length && $('#predictive-search').is(":visible") ) 
        {
            $('#predictive-search').hide();
        }        
    });
    $(".appleid-signin").on('click', function(e){
        $("#appleid-signin").click();
    });

    // add back-to-top button on every page after #primary section
    $('#primary').after('<a style="z-index: 100;" aria-label="click to scroll back to top" id="back-to-top" href="#" class="btn btn-primary btn-lg-md back-to-top text-white shadow-sm" role="button"><i class="fas fa-chevron-up"></i></a>');

    // fade back-to-top btn in and out based on user scroll
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });

    initializeAjaxForms();

    document.body.addEventListener('containerLoaded', function(e) {
        initializeAjaxForms();
    });
});

function initializeAjaxForms()
{
    $(".ajax_form").unbind('submit');
    $(".ajax_form").on('submit', function(e){            
        e.preventDefault();
        let doAction = true;
        let submitButton = jQuery(this).find('[type="submit"]').first()
        let originalText = jQuery(submitButton).text();


        jQuery(this).find('[type="submit"]').first().attr('disabled', 'disabled');
        jQuery(this).find('[type="submit"]').first().text('');
        jQuery(this).find('[type="submit"]').first().html(`<div class="spinner-border text-light" role="status">
        <span class="sr-only">Loading...</span>
      </div>`);

        // credit card input validation
        if ( ccNumber.val() != undefined && ccExpMonth.val() != undefined && ccExpYear.val() != undefined && ccCvv.val() != undefined && $(this).attr('id') !== 'guestCheckout' ) {
            doAction = validateCreditCardInputs(ccNumber, ccExpMonth, ccExpYear, ccCvv, errorText1, errorText2, errorText3, errorText4);
        }

        // create account input validation 
        if ( email.val() != undefined && password.val() != undefined && $(this).attr('id') !== 'guestCheckout' ) {
            doAction = validateCreateAccountInputs(email, password, errorText1, errorText2, errorText3);
        }

        if ( doAction ) {
            let actionUrl = $(this).attr('action');
            $(".form-error").empty();
            var postData = {};
            for(const field of $(this).serializeArray())
            {
                postData[field.name] = field.value;
            }
    
            submitAjaxForm(actionUrl, postData, originalText, submitButton);
        }
        else
        {
            jQuery(submitButton).removeAttr('disabled');
            jQuery(submitButton).html('');
            jQuery(submitButton).text(originalText)
        }
    });
}

function disableSubmit(submitBtn)
{
    jQuery(submitBtn).attr('disabled','disabled');
    jQuery(submitBtn).html(`<div class="spinner-border text-light" role="status">
        <span class="sr-only">Loading...</span>
        </div>`);
}

function submitAjaxForm(actionUrl, postData, originalText, submitButton)
{
    jQuery.ajax({
        url: actionUrl,
        type: "POST",
        async: false,
        beforeSend: function(xhr){
            xhr.setRequestHeader('csrf_token', jQuery('meta[name="csrf-token"]').attr('content'));
        },
        data: postData,
        dataType: 'json',
        success: function(response) {
            console.log('Success: ', response);

            if(typeof response.data === 'object' && 'callback' in response.data)
            {
                let args = new Array();
                if('callbackArguments' in response.data)
                {
                    args = response.data.callbackArguments;
                    // callbackArguments MUST be passed as an array from PHP. 
                    // And as an array of arrays if there are multiple variables needed.
                }
                runFunction(response.data.callback, args);
            }
            else
            {
                console.log('Nothing left to do.');
            }
            jQuery('.loading_wrapper').fadeOut();
        },
        error: function(response) {
            console.error(response);
            //jQuery(".form-error").html(`<p class="mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">${response.responseJSON.data}</p>`);

            if( 
                typeof response.responseJSON.data === 'object' && 
                'show_message' in response.responseJSON.data && 
                'message' in response.responseJSON.data && 
                response.responseJSON.data.show_message === true
            )
            {
                alert(response.responseJSON.data.message);
                jQuery(submitButton).removeAttr('disabled');
            }
            else
            {
                jQuery(".form-error").html(`<p class="mw-100 text-center mb-2 mb-md-3 bg-danger font-weight-semibold rounded-sm text-white p-2">An error occurred while processing your request.</p>`);
                var errorElement = document.getElementById('card-errors');
                var loggedInError = document.querySelector('.loggedInCardErrors')
                errorElement.textContent = response.responseJSON.data;
                loggedInError.style.backgroundColor = '#DC3545'
                loggedInError.textContent = response.responseJSON.data;
            }

            if( document.getElementById("packError"))
            {
                jQuery("#packError").text(response.responseJSON.data);
            }
            
            jQuery('.loading_wrapper').fadeOut();
        },
        complete: function() {
            jQuery(submitButton).removeAttr('disabled');
            jQuery(submitButton).html(originalText);
        },
    });
}

function runFunction(name, args)
{
    var fn = window[name];
    if(typeof fn !== 'function')
        return;

    fn.apply(window, args);
}

function loginSuccess(args)
{
    if( args.redirect_link != '' )
    {
        straightRedirect(args);
    }
    else
    {
        window.location.reload();
    }
    
}

function updateCartCounter(quantity)
{
    if(document.getElementById("cart-counter"))
    {
        let oldQty = document.getElementById("cart-counter").textContent;
        let newQty = parseInt(oldQty) + parseInt(quantity);
        document.getElementById("cart-counter").textContent = newQty;
        if(newQty === 0)
        {
            jQuery("#cart-counter").remove();
        }
    }
    else
    {
        jQuery("#userCart").append("<div id='cart-counter'>"+quantity+"</div>");
    }
}

function cartDirectUpdateSuccess(args)
{
    sessionStorage.setItem("cart", args.cart);
}

function cartSuccess(args)
{
    updateCartCounter(args.quantity);

    sessionStorage.setItem("cart", args.cart);

    jQuery('<form action="/added-to-cart" method="POST"/>')
        .append(jQuery('<input type="hidden" name="snack_id">').val(args.snack_id))
        .append(jQuery('<input type="hidden" name="crate_size">').val(args.crate_size))
        .append(jQuery('<input type="hidden" name="quantity">').val(args.quantity))
        .appendTo(jQuery(document.body))
        .submit();
}

function cartRemovalSuccess(args)
{
    updateCartCounter(args.quantity * -1);

    sessionStorage.setItem("cart", args.cart);

    window.location.reload();
}

function fulfillmentSuccess()
{
    window.location.reload();
}

function printLabelSuccess(args)
{
    let i = 0;
    args.labels.forEach( function( label ){
        i++;
        window.open(label, '_blank');
    } ); 
}

function printedScanform(args)
{
    window.open(args.link, '_blank');
    window.location.reload();
}

function invoiceGenerationSuccess(args)
{
    args.invoices.forEach( function( invoice ) {
        window.open(invoice, '_blank');
    } ); 

    window.location.reload();
}

function updateShippingSuccess(args)
{
    window.location.href = window.location.href.split('?')[0] + "?selected_address=" + args.id;
}

function updatePaymentSuccess(args)
{
    jQuery('.modal').modal('hide');
    window.location.href = window.location.href.split('?')[0] + "?p=" + args.card_id;
}

function updateOrderShippingSuccess(args)
{
    jQuery( "input[name='shipping_address']" ).val( args.data );

    let values = JSON.parse(args.data);
    for( const index in values )
    {
        let value = values[index];
        console.log(value, index);
        console.log( jQuery( "[data-shippingfield='" + index + "']") )
        jQuery( "[data-shippingfield='" + index + "']").text( value );

        if( index === 'address_2' && value.length == 0 )
        { 
            jQuery( "[data-shippingfield='address_2']" ).addClass("d-none");
        }
        else if( index === 'address_2' && value.length > 0 )
        {
            jQuery( "[data-shippingfield='address_2']" ).removeClass("d-none");
        }
    }

    jQuery('.modal').modal('hide');
}

function checkoutUser(args)
{
    jQuery('<form action="'+ args.submit_link +'" method="POST"/>')
        .append(jQuery('<input type="hidden" name="email">').val(args.email))
        .append(jQuery('<input type="hidden" name="first_name">').val(args.first_name))
        .append(jQuery('<input type="hidden" name="last_name">').val(args.last_name))
        .append(jQuery('<input type="hidden" name="user_address_1">').val(args.user_address_1))
        .append(jQuery('<input type="hidden" name="user_address_2">').val(args.user_address_2))
        .append(jQuery('<input type="hidden" name="user_city">').val(args.user_city))
        .append(jQuery('<input type="hidden" name="user_state">').val(args.user_state))
        .append(jQuery('<input type="hidden" name="user_zip">').val(args.user_zip))
        .append(jQuery('<input type="hidden" name="user_country">').val(args.user_country))
        .appendTo(jQuery(document.body))
        .submit();
}

function checkoutGuestSubmitted(args)
{
    jQuery('<form action="'+ args.submit_link +'" method="POST"/>')
        .append(jQuery('<input type="hidden" name="email">').val(args.email))
        .append(jQuery('<input type="hidden" name="first_name">').val(args.first_name))
        .append(jQuery('<input type="hidden" name="last_name">').val(args.last_name))
        .appendTo(jQuery(document.body))
        .submit();
}

function checkoutConfirmPaymentGuest(args)
{
    jQuery('<form action="'+ args.submit_link +'" method="POST"/>')
        .append(jQuery('<input type="hidden" name="data">').val(args.data))
        .appendTo(jQuery(document.body))
        .submit();
}

function straightRedirect(args)
{
    window.location.href = args.redirect_link;
}

function straightReload()
{
    window.location.reload();
}

function triviaAnswered(args)
{
    Object.values(args.percentages).forEach((percentage, idx) => {
        let letter = Object.keys(args.percentages)[idx];
        jQuery("#triviaQuestion" + args.question_key).find("button[data-answer='"+ letter +"'] .user-percentage").css("width", percentage + "%");
    });
}

function superlativeSaved(args)
{
    $('.' + args.type + '-picks').addClass('d-flex').removeClass('d-none');
    $('.' + args.type + '-snack').addClass('d-none').removeClass('d-flex');

    $('#snackPollModal').modal('hide');

    $('[data-elementrole="' + args.type + '-user-image"]').attr("alt", "image of " + decodeHTMLEntities(args.user_pick.name) ).attr("src", args.user_pick.image);
    $('[data-elementrole="' + args.type + '-user-name"]').text( decodeHTMLEntities(args.user_pick.name) );

    $('[data-elementrole="' + args.type + '-popular-image"]').attr("alt", "image of " + decodeHTMLEntities(args.highest_rated.name) ).attr("src", args.highest_rated.image);
    $('[data-elementrole="' + args.type + '-popular-name"]').text( decodeHTMLEntities(args.highest_rated.name) );

    getStarsHTML( args.user_pick.rating, '.' + args.type + '-picks .your-pick .review-stars' );
    getStarsHTML( args.highest_rated.rating, '.' + args.type + '-picks .popular-pick .review-stars' );
}

function getStarsHTML( rating, element )
{
    rating = (rating == '') ? 0 : rating;
    $(element).html('');
    var url;
    var stars = '';
    for( var i = 1; i <= 5; i++)
    {
        if( rating >= i )
        {
            url = global_script_vars.template_uri + '/template-parts/full-star-svg.php';
        }
        else if( rating >= (i - .75) )
        {
            url = global_script_vars.template_uri + '/template-parts/half-star-svg.php';
        }
        else
        {
            url = global_script_vars.template_uri + '/template-parts/empty-star-svg.php';
        }

        $.ajax({
            method: "GET",
            url: url,
            async: false,
            dataType: 'html',
            success: function (data) {
                $(element).append( data );
                stars += data;
            }
        });
    }
}

function addDrinkSuccess()
{
    jQuery('#addDrinkUpgrade')
        .html('')
        .removeClass('btn-secondary')
        .addClass('btn-success')
        .text('Drink Upgrade Added');
    jQuery('#drinkUpgradeModalLink').addClass('d-none');
    jQuery('#drinkUpgradeAddedText').removeClass('d-none');

    jQuery('#drinkUpgradeModal').modal('hide');
}

function decodeHTMLEntities(text)
{
    var textArea = document.createElement('textarea');
    textArea.innerHTML = text;
    return textArea.value;
}