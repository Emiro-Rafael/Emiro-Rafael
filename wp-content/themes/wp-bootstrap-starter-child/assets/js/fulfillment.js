jQuery(document).ready(function(){
    if(document.body.dataset.namespace == 'fulfillment')
    {
        jQuery("#addBox").on('click', function(){
            let lastBox = jQuery(".box-block").last();
            let boxCount = parseInt( jQuery(".box-block").last().data("blockcount") ) + 1;

            let newBox = jQuery(".box-block").last().clone();

            jQuery( newBox ).data("blockcount", boxCount);

            jQuery( newBox ).find(".size-select").last().attr( "name", "boxsize["+boxCount+"]" );
            jQuery( newBox ).find(".lb-select").last().attr( "name", "weight_lb["+boxCount+"]" ).val(0);
            jQuery( newBox ).find(".oz-select").last().attr( "name", "weight_oz["+boxCount+"]" ).val(0);

            jQuery( newBox ).find(".remove-box").last().data('block', boxCount).addClass('d-block').removeClass('d-none');

            jQuery( lastBox ).after( newBox );
        });

        jQuery("body").on('click', '.remove-box', function(elem){
            jQuery( elem.currentTarget ).closest(".box-block").remove();
        });

        /**
         * BEGIN
         * Fulfillment Item picker steps
         */

        jQuery(".fulfillment-item-row").on('click', function(event){
            if( !jQuery(this).hasClass("bg-success") )
            {
                let itemQty = parseInt( jQuery(this).find('[data-qty]').data('qty') );
                let confirmation = true;
                if( itemQty > 1 )
                {
                    confirmation = window.confirm("Please confirm that you have taken " + itemQty + " of this item.");
                }

                if( confirmation )
                {
                    jQuery(this).addClass("bg-success text-white").removeClass("bg-gray-light");
                }
            }
            else
            {
                jQuery(this).removeClass("bg-success text-white");
                if( jQuery(this).hasClass('odd') )
                {
                    jQuery(this).addClass("bg-gray-light");
                }
            }

            if( jQuery(".fulfillment-item-row").length == jQuery(".fulfillment-item-row.bg-success").length)
            {
                jQuery("#print_label").removeAttr('disabled');
            }
            else
            {
                jQuery("#print_label").attr('disabled', 'disabled');
            }
        });
    }
});

function nextOrder(setFulfilled)
{
    let orderIds = jQuery( "input[name='ids']" ).val();

    let postData = {
        ids: orderIds,
        setFulfilled: setFulfilled,
        action: 'next_order_fulfillment',
    };
    
    submitAjaxForm("/wp-admin/admin-ajax.php", postData, '');
}