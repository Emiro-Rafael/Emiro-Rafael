const freeShippingMinimum = 35;
const shippingCost = 4.99;
jQuery(document).ready(function() {
    var $ = jQuery;

    if(document.body.dataset.namespace == 'shopping-cart')
    {
        if( sessionStorage.getItem("cart") == null )
        {
            fixSession();
        }

        $(".ajax_form").on('submit', function(event){
            if($(this).attr('id') == 'removeFromCart')
            {
                let snackId = $(this).find("input[name='snack_id']").val();
                $("#cartQty_" + snackId).remove();
            }
        });

        $(".cartQty, .mobileCartQty").on("change", function(e){
            let newQty = e.currentTarget.value;
            let singlePrice = e.currentTarget.dataset.price;
            let snackId = e.currentTarget.dataset.snackid;

            if( parseInt(newQty) > parseInt(e.currentTarget.max) )
            {
                newQty = e.currentTarget.max;
                jQuery("#mobileCartQty_" + snackId).val(newQty);
                jQuery("#cartQty_" + snackId).val(newQty);
            }

            let oldTotalSnackPrice = parseFloat( $("#totalPrice_" + snackId).text().replace(',','') ).toFixed(2);
            let newTotalSnackPrice = parseFloat(newQty * singlePrice).toFixed(2);

            $("#totalPrice_" + snackId).text( newTotalSnackPrice );
            $("#mobileTotalPrice_" + snackId).text( newTotalSnackPrice );

            let difference = oldTotalSnackPrice - newTotalSnackPrice;

            let oldSubTotal = parseFloat( $("#subTotal").text().replace(',','') ).toFixed(2);
            let oldGrandTotal = parseFloat( $("#grandTotal").text().replace(',','') ).toFixed(2);
            
            $("#subTotal").text( ( oldSubTotal - difference ).toFixed(2) );
            $("#grandTotal").text( ( oldGrandTotal - difference ).toFixed(2) );
            

            if( oldSubTotal >= freeShippingMinimum && ( oldSubTotal - difference ) < freeShippingMinimum )
            {
                $("#grandTotal").text( parseFloat( oldGrandTotal - difference + shippingCost ).toFixed(2) );
                $("#shippingTotal").text( '$' + shippingCost );
            }
            else if( oldSubTotal < freeShippingMinimum && ( oldSubTotal - difference ) >= freeShippingMinimum )
            {
                $("#grandTotal").text( parseFloat( oldGrandTotal - difference - shippingCost ).toFixed(2) );
                $("#shippingTotal").text( 'FREE' );
            }
        

            let cartItemsCount = 0;
            $(".cartQty").each(function(i, e){
                cartItemsCount += parseInt(e.value);
            });
            $("#cart-counter > p").text(cartItemsCount);
        
            let newCart = {};

            $(".cartQty").each(function(i, e){
                switch (e.dataset.posttype)
                {
                    case 'collection':
                    case 'country':
                        newCart[e.dataset.snackid] = {};
                        newCart[e.dataset.snackid][e.dataset.cratesize] = parseInt(e.value);
                        break;
                    default:
                        newCart[e.dataset.snackid] = parseInt(e.value);
                        break;
                }
            });
        
            sessionStorage.setItem( "cart", JSON.stringify( newCart ) );
            
            let actionUrl = "/wp-admin/admin-ajax.php";
            let postData = {
                'action': 'sc_update_cart',
                'cart': JSON.stringify( newCart )
            };

            jQuery.ajax({
                async: false,
                url: actionUrl,
                type: "POST",
                beforeSend: function(xhr){
                    xhr.setRequestHeader('csrf_token', jQuery('meta[name="csrf-token"]').attr('content'));
                },
                data: postData,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {

                    Object.keys(error).forEach( function( index ){
                        console.log(index, error[index]);
                    });

                    
                }
            });
        
        });
        
    }
});

function addToCart(snackId, quantity)
{
    let cartString = window.sessionStorage.getItem('cart');
    let cart = JSON.parse(cartString);

    if(cart == null)
    {
        cart = {};
    }
    
    if(cart.hasOwnProperty(snackId))
    {
        cart[snackId].qty += parseInt(quantity);
    }
    else
    {
        cart[snackId] = {qty: parseInt(quantity)};
    }

    window.sessionStorage.setItem('cart', JSON.stringify(cart));
}

function getCart()
{
    let cartString = window.sessionStorage.getItem('cart');
    let cart = JSON.parse(cartString);
}

function checkout()
{

}

function fixSession()
{
    let newCart = {};
    $(".cartQty").each(function(i, e){
        switch (e.dataset.posttype)
        {
            case 'collection':
            case 'country':
                newCart[e.dataset.snackid] = {};
                newCart[e.dataset.snackid][e.dataset.cratesize] = parseInt(e.value);
                break;
            default:
                newCart[e.dataset.snackid] = parseInt(e.value);
                break;
        }
    });

    sessionStorage.setItem( "cart", JSON.stringify(newCart) );
}