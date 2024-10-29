const loadingGif = `<div class="loading d-flex align-items-center justify-content-center mx-auto">    
<img style="max-width: 125px;" src="/wp-content/themes/wp-bootstrap-starter-child/loading.gif">
</div>`;
const ajaxUrl = '/wp-admin/admin-ajax.php';

jQuery(document).ready(function(){
    const containers = jQuery("[data-ajaxcontainer]");
    const clickables = jQuery("[data-ajaxclickparent]");
    const lazyloaders = jQuery("[data-lazyload='1']");

    jQuery(containers).each(function(index, container){
        var params;
        if( container.dataset.ajaxparams == '' )
        {
            params = {
                'action': container.dataset.ajaxcontainer
            };
        }
        else
        {
            params = {
                'action': container.dataset.ajaxcontainer,
                'params': JSON.parse(container.dataset.ajaxparams)
            };
        }

        let buttonLoad = 'buttonlazyload' in container.dataset;
        if( 'lazyload' in container.dataset )
        {
            initializeLazyLoader(container, container.dataset.ajaxcontainer, buttonLoad);
        }
        
        sendLoadRequest( container, params );
    });

    jQuery(clickables).each(function(index, parent){
        jQuery(parent).on('click', function(){
            var params;
            let id = jQuery(this).data('ajaxclickparent');

            if( !document.getElementById( id ) )
            {
                return false;
            }

            const container = document.getElementById( id );
            
            let buttonLoad = 'buttonlazyload' in container.dataset;

            if( 'lazyload' in container.dataset )
            {
                initializeLazyLoader(container, container.dataset.ajaxclickcontainer, buttonLoad);
            }

            if(!jQuery(container).hasClass('nodata'))
            {
                if( typeof container.dataset.ajaxheader != 'undefined')
                {
                    document.getElementById(container.dataset.ajaxheader).style.display = 'block';
                }

                if( container.innerHTML.trim() == '' )
                {
                    if( container.dataset.ajaxparams == '' )
                    {
                        params = {
                            'action': container.dataset.ajaxclickcontainer
                        };
                    }
                    else
                    {
                        params = {
                            'action': container.dataset.ajaxclickcontainer,
                            'params': JSON.parse(container.dataset.ajaxparams)
                        };
                    }
                
                    sendLoadRequest( container, params );
                }
            }
            else if( typeof container.dataset.ajaxheader != 'undefined')
            {
                document.getElementById(container.dataset.ajaxheader).style.display = 'none';
            }
            
            // hide load more btn when accordion is collapsed
            if ( jQuery(container).hasClass('show') ) {
                jQuery(container).next().removeClass('d-flex').addClass('d-none');
            } else {
                jQuery(container).next().removeClass('d-none').addClass('d-flex');
            }
        });
    });
});

function initializeLazyLoader( container, action, buttonLoad = false )
{
    if(buttonLoad)
    {
        let btnId = "loadMore_" + action + "_" + container.id;
        if(!document.getElementById(btnId))
        {
            jQuery(container).after("<div class='d-flex align-items-center justify-content-center px-3 pb-4'><button id='"+btnId+"' class='load-more-btn btn btn-sm btn-secondary text-white font-weight-semibold text-capitalize mb-4 w-100'>Load More</button></div>");
            jQuery("#"+btnId).click(function(e){
                jQuery("#"+btnId).attr('disabled','disabled');
                let currentCount = jQuery(container).children('[data-lazyelement]').length;
                let params = {
                    'count': currentCount,
                    'action': action,
                    'params': container.dataset.ajaxparams == '' ? null : JSON.parse(container.dataset.ajaxparams)
                }

                jQuery(container).addClass('lazyloading');
                sendLazyLoadRequest( container, params );

                e.stopPropagation();
            });
        }
    }
    else
    {
        jQuery(window).scroll(function(){
            if(jQuery(container).hasClass('show') && !jQuery(container).hasClass('lazyloading') && !jQuery(container).hasClass('lazyloadingcomplete'))
            {
                let currentCount = jQuery(container).children('[data-lazyelement]').length;
                let scrollPosition = jQuery(window).scrollTop() + jQuery(window).height();
                let elementPosition = jQuery(container).offset().top + jQuery(container).height();
    
                if( scrollPosition > elementPosition )
                {
                    let params = {
                        'count': currentCount,
                        'action': action,
                        'params': container.dataset.ajaxparams == '' ? null : JSON.parse(container.dataset.ajaxparams)
                    }

                    jQuery(container).addClass('lazyloading');
                    sendLazyLoadRequest( container, params );
                }
            }
        });
    }
}

function sendLazyLoadRequest( container, params )
{
    container.innerHTML += loadingGif;

    jQuery.ajax({
        url: ajaxUrl,
        type: "POST",
        beforeSend: function(xhr){
            xhr.setRequestHeader('csrf_token', jQuery('meta[name="csrf-token"]').attr('content'));
        },
        data: params,
        dataType: 'json',
        success: function(response) {
            container.innerHTML = container.innerHTML.replace(loadingGif, '');
            container.innerHTML += response.data.content;

            jQuery(container).removeClass('lazyloading');

            let btnId = "loadMore_" + params.action + "_" + container.id;
            jQuery("#"+btnId).removeAttr('disabled');
            if( response.data.content.trim() == '' )
            {
                jQuery(container).addClass('lazyloadingcomplete'); // nothing left to load
                jQuery("#"+btnId).hide();//.attr('disabled', 'disabled').attr('title', 'No more content to load.');
            }
        },
        error: function(response) {
            console.error('Error: ', response);
        },
    });
}

function sendLoadRequest( container, params )
{
    container.innerHTML = loadingGif;

    jQuery.ajax({
        url: ajaxUrl,
        type: "POST",
        beforeSend: function(xhr){
            xhr.setRequestHeader('csrf_token', jQuery('meta[name="csrf-token"]').attr('content'));
        },
        data: params,
        dataType: 'json',
        success: function(response) {
            container.innerHTML = response.data.content;
            container.style.height = 'auto';

            if( typeof container.dataset.ajaxheader != 'undefined')
            {
                if(response.data.content.trim() == '' )
                {
                    document.getElementById(container.dataset.ajaxheader).style.display = 'none';
                    jQuery(container).addClass("nodata");
                }
                else
                {
                    document.getElementById(container.dataset.ajaxheader).style.display = 'block';
                }
            }
            
            let event = new CustomEvent("containerLoaded", {detail: container.dataset});
            document.body.dispatchEvent(event);
        },
        error: function(response) {
            console.error('Error: ', response);
        },
    });
}