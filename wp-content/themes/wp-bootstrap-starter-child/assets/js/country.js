jQuery(document).ready(function() {
    var $ = jQuery;
    let boxSizeBtns = $(".box-size-btns button");

    let miniSnackBox = $('#crateSize4Snack');
    let ogSnackBox = $('#crateSize8Snack');
    let famSnackBox = $('#crateSize16Snack');

    let snackQtyMini = $('#snackQty4Snack');
    let snackQtyOg = $('#snackQty8Snack');
    let snackQtyFam = $('#snackQty16Snack');

    $("#addDrink").attr( 'disabled', 'disabled' );
    $("#addDrink").prop( "checked", false );
    console.log('stock spot')

    if( $("#itemQuantity").is("[data-stock]") )
    {
        populateSelector( $("#itemQuantity").data("stock"), $("#itemQuantity") );
    }

    $(boxSizeBtns).on('click', function(e){
        $(".country-price").hide();
        $(".box-size-btns button").removeClass("btn-active");

        let sizeSelected = e.currentTarget.dataset.box;
        $("#"+sizeSelected+"_prices").show();

        e.currentTarget.classList.add("btn-active");

        $("#addToCart").removeAttr('disabled');

        if( e.currentTarget.dataset.drinkstock > 0 || e.currentTarget.dataset.preorder > 0 )
        {
            $("#addDrink").removeAttr('disabled');
            $(".add-drink").removeClass('d-none').addClass('d-flex');
        }
        else
        {
            $("#addDrink").attr( 'disabled', 'disabled' );
            $(".add-drink").removeClass('d-flex').addClass('d-none');
        }

        $("#crate_type").val($(this).data("box"));

        populateSelector( e.currentTarget.dataset.stock, e.currentTarget );

        if ($(ogSnackBox).hasClass('btn-active')) {
            $("#freeShippingIcon").removeClass('opacity-0').addClass('opacity-1');
            $(snackQtyMini).addClass('d-none');
            $(snackQtyOg).removeClass('d-none');
            $(snackQtyFam).addClass('d-none');
        } else if ($(famSnackBox).hasClass('btn-active')) {
            $("#freeShippingIcon").removeClass('opacity-0').addClass('opacity-1');
            $(snackQtyMini).addClass('d-none');
            $(snackQtyOg).addClass('d-none');
            $(snackQtyFam).removeClass('d-none');
        } else if ($(miniSnackBox).hasClass('btn-active')) {
            $("#freeShippingIcon").removeClass('opacity-1').addClass('opacity-0');
            $(snackQtyMini).removeClass('d-none');
            $(snackQtyOg).addClass('d-none');
            $(snackQtyFam).addClass('d-none');
        }
    });


    $("#addDrink").on('change', function(element){
        let drinkAddonValue = element.currentTarget.checked ? 1 : 0;
        let priceDiffMultiplier = drinkAddonValue ? 1 : -1;
        let drinkPrice = parseFloat( $("#drink_price").val() );
        $("#drink_addon").val(drinkAddonValue);
    
        $(".country-price").each(function(i,e){
            let oldPrice = parseFloat( $(e).text().replace( '$', '' ) );
            let newPrice = parseFloat( oldPrice + ( drinkPrice * priceDiffMultiplier ) ).toFixed(2);
            $(e).text( '$' + newPrice );
        });

        if( $(element.target).is(":checked") && !$(miniSnackBox).hasClass('btn-active'))
        {
            $("#freeShippingIcon").removeClass('opacity-0').addClass('opacity-1');
        }
    });
});

function populateSelector( maxStock, element )
{
    if($(element).is("[data-preorder]"))
    {
        return false;
    }

    maxStock = Math.min(10, maxStock);
    $("#itemQuantity").empty();
    if(maxStock <= 0)
    {
        console.log(maxStock)
        $("#addToCart").attr("disabled","disabled").text("Out of Stock");
        $("#itemQuantity").parent().hide();
    }
    else
    {
        $("#itemQuantity").parent().show();
        $("#addToCart").removeAttr("disabled").text("Add to Cart");
        let optionHtml = '';

        for(let i = 1; i <= maxStock; i++)
        {
            optionHtml += `<option value="${i}">${i}</option>`;
        }

        $("#itemQuantity").html( optionHtml );
    }
}