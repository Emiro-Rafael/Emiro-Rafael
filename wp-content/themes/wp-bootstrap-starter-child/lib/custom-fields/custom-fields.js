jQuery(document).ready(function() {
    var $ = jQuery;
    
    setCustomImages();

    $("#discounts_Fixed").on('change', function(e){
        let price = $("#price").val();
        let newFixedDiscount = e.currentTarget.value;
        let newPctDiscount = parseFloat(100 * newFixedDiscount / price).toFixed(3);
        $("#discounts_Percentage").val(newPctDiscount);
    });

    $("#discounts_Percentage").on('change', function(e){
        let price = $("#price").val();
        let newPctDiscount = e.currentTarget.value;
        let newFixedDiscount = parseFloat(price * newPctDiscount / 100).toFixed(2);
        $("#discounts_Fixed").val(newFixedDiscount);
    });
});

function setCustomImages()
{
    if ($('.set_custom_images').length > 0) {
        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            $('.set_custom_images').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var id = button.prev();
                wp.media.editor.send.attachment = function(props, attachment) {
                    id[0].children[0].setAttribute('src',attachment.url);
                    let inputId = id.attr('id').replace('_thumbnail', '_field');
                    document.getElementById(inputId).value = attachment.id;
                    button[0].innerHTML = 'Replace Image';

                    let rmvBtnId = id.attr('id').replace('_thumbnail', '_rmv');
                    document.getElementById(rmvBtnId).removeAttribute('disabled');
                };
                wp.media.editor.open(button);
                
                return false;
            });
        }
    }
}

function removeImageValue(field_id)
{
    document.getElementById(field_id + '_thumbnail').children[0].setAttribute('src', '');
    document.getElementById(field_id + '_thumbnail').children[0].setAttribute('alt', 'no image chosen');
    document.getElementById(field_id + '_thumbnail').children[0].removeAttribute('srcset');
    document.getElementById(field_id + '_field').value = '';
    document.getElementById(field_id + '_rmv').setAttribute('disabled','disabled');
}

function addExtendableRow(btn)
{
    let group = jQuery(btn).prev();
    let markup =  jQuery(group).prop('outerHTML');
    
    let field = jQuery(btn).prev().data("field");
    let subFields = jQuery(btn).prev().data("subfields").split(",");

    let oldKey = jQuery(btn).prev().data("position");
    let newKey = oldKey+1;
    
    let newMarkup = markup;

    let count = jQuery("input[name=" + field + "_count]").val();
    count++;
    jQuery("input[name=" + field + "_count]").val(count);

    let fieldParts = field.split("_");
    
    subFields.forEach( function(subfield){
        let textToChange = fieldParts[0] + "[" + fieldParts[1] + "][" + oldKey + "][" + subfield + "]";
        let newText = fieldParts[0] + "[" + fieldParts[1] + "][" + newKey + "][" + subfield + "]";

        newMarkup = newMarkup.replaceAll( textToChange, newText );
    } );

    jQuery( group ).after( newMarkup );
    let newGroup = jQuery(group).next();
    jQuery( newGroup ).attr('data-position', newKey);
    jQuery( newGroup ).find("button.remove-row").attr('disabled', false);

    jQuery( newGroup ).find("input").val('');

    jQuery( newGroup ).find("button[onclick^='removeImageValue']").click();
    jQuery( newGroup ).find(".set_custom_images").text("Add Image");
    setCustomImages();
}


function removeExtendableRow(btn)
{
    let group = jQuery(btn).parent();
    let field = jQuery(group).data("field");
    jQuery(group).remove();
}

function clearSize(termId)
{
    $(`input[type='radio'][name='included-in[${termId}]']`).prop('checked', false);
}