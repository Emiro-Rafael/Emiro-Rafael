document.addEventListener('DOMContentLoaded', function() {
    jQuery('[type="search"]').on('keyup', function(e){
        if(e.originalEvent.which == 13)
        {
            runSearch(e.currentTarget.value);
        }
    });
    jQuery("#searchSubmit").on('click', function(e){
        e.preventDefault();
        runSearch(jQuery('[type="search"]').val());
    });
});

function runSearch(str)
{
    window.location.href = '/shop-all?search=' + str;
}