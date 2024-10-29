let currentUrl = new URL(window.location.href);
const baseUrl = currentUrl.origin + currentUrl.pathname;
let urlParams = new URLSearchParams(currentUrl.search);

document.addEventListener('DOMContentLoaded', function() {
    if(document.body.dataset.namespace == 'shop-all')
    {
        let filters = {};
        let filterMsg = document.getElementById("filterMsg");
        let snacks = document.getElementsByClassName("taxItem");
        
        if(urlParams.has('c'))
        {
            let countrySlug = urlParams.get('c');
            if( document.getElementById(countrySlug + "_countries_filter") )
            {
                filters['countries'] = countrySlug;
                updateFilters(filters, 'countries', snacks);
                document.getElementById(countrySlug + "_countries_filter").checked = true;
                jQuery('#countriesRemoveFilter').prop('disabled', false);
            }
            else
            {
                updateHistoryState('countries', 'remove');
            }

            // show filterMsg on load if filters are applied
            if (jQuery('.taxItem:visible').length == 0) {
                filterMsg.style.display = "block";
            } else {
                filterMsg.style.display = "none";
            }
        }
        if(urlParams.has('t'))
        {
            let typeSlug = urlParams.get('t');
            if( document.getElementById(typeSlug + "_snack_types_filter") )
            {
                filters['snack_types'] = typeSlug;
                updateFilters(filters, 'snack_types', snacks);
                document.getElementById(typeSlug + "_snack_types_filter").checked = true;
                jQuery('#snack_typesRemoveFilter').prop('disabled', false);
            }
            else
            {
                updateHistoryState('snack_types', 'remove');
            }

            // show filterMsg on load if filters are applied
            if (jQuery('.taxItem:visible').length == 0) {
                filterMsg.style.display = "block";
            } else {
                filterMsg.style.display = "none";
            }
        }

        document.body.addEventListener('change', function (e) {
            let target = e.target;
            if(target.getAttribute("name").includes("_filter"))
            {
                let filterTarget = target.id.split("_")[0];
                let filterType = target.name.replace("_filter", "");
                filters[filterType] = filterTarget;

                if(filterType == 'snack_types' && urlParams.has('search'))
                {
                    window.location.href = window.location.href.split('?')[0] + "?t=" + filterTarget;
                    jQuery(`#${filterType}RemoveFilter`).prop('disabled', false);
                }
                else
                {
                    updateFilters(filters, filterType, snacks);
                    jQuery(`#${filterType}RemoveFilter`).prop('disabled', false);
                }

                updateHistoryState(filterType, "add", filterTarget);
            }

            // show filterMsg if no snacks visible
            if (jQuery('.taxItem:visible').length == 0) {
                filterMsg.style.display = "block";
            } else {
                filterMsg.style.display = "none";
            }
        });
        document.body.addEventListener('removeFilter', function (e) {
            let type = e.detail.dataset.taxonomy;
            delete filters[type];
            let target = null;
            let filterType = null;
            if(Object.keys(filters).length > 0)
            {
                if(type == 'snack_types')
                {
                    target = document.querySelector('input[name="countries_filter"]:checked');
                    filterType = 'countries';
                }
                else
                {
                    target = document.querySelector('input[name="snack_types_filter"]:checked');
                    filterType = 'snack_types';
                }
            }
            updateFilters(filters, filterType, snacks);
            filterMsg.style.display = "none";
        });
    } 
});

    // querySelector returns the first element it finds with the correct selector
    // so it needs a unique class name or you're only grabbing one element
jQuery(document).ready(function() {
    if(document.body.dataset.namespace == 'shop-all')
    {
        document.querySelector('#filterBtn').addEventListener('click', function(e) {
            // from https://www.jamestease.co.uk/blether/add-remove-or-toggle-classes-using-vanilla-javascript
            // querySelectorAll returns a nodeList, so map to to an array and BOOM
            // if there's only one element to toggle, you can skip the array
            // and grab it with a simple querySelector
            [].map.call(document.querySelectorAll('.filter-wrap'), function(el) {
                // classList supports 'contains', 'add', 'remove', and 'toggle'
                el.classList.toggle('toggled');
            });
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    if(document.body.dataset.namespace == 'shop-all')
    {
        // Gradient & Filter for Shop All
        jQuery( ".taxonomy_container .term_container:nth-child(6)").addClass("linGradient");
        jQuery( ".taxonomy_container .term_container:nth-child(n+7)").addClass("d-none");


        let seeMoreCat = document.getElementsByClassName('seeMoreCat');
        let seeMoreIcon = document.getElementsByClassName('seeMoreIcon');
        let seeMoreCatText = document.getElementsByClassName('seeMoreCatText');
        let linGradient = document.querySelectorAll('.taxonomy_container .term_container:nth-child(6)');

        let searchDiv = document.querySelectorAll('#search-filter div');
        let showContent = false;


        for(var i=0; i < seeMoreCat.length; i++) {
            (function(index) {
                seeMoreCat[index].addEventListener("click", function(e) {
                    let target = e.currentTarget;
                    let parent = target.parentElement.id;
                    let linGradientItem = document.querySelectorAll('#' + parent + '.taxonomy_container .term_container:nth-child(n+7)');

                    showContent = !showContent;

                    if (showContent === true) {
                        seeMoreCat[index].classList.add('active');
                        seeMoreCatText[index].innerHTML = "Show Less";
                        linGradient[index].classList.remove('linGradient');
                        linGradientItem.forEach(el => el.classList.remove('d-none'));
                        seeMoreIcon[index].classList.add('fa-minus');
                        seeMoreIcon[index].classList.remove('fa-plus');
                    } else {
                        seeMoreCat[index].classList.remove('active');
                        seeMoreCatText[index].innerHTML = "Show More";
                        linGradient[index].classList.add('linGradient');
                        linGradientItem.forEach(el => el.classList.add('d-none'));
                        seeMoreIcon[index].classList.remove('fa-minus');
                        seeMoreIcon[index].classList.add('fa-plus');
                    }
                })
            })(i);
        }
    }
});

// Equal Heights
(function () {
    equalHeight(false);
})();

window.onresize = function(){
    equalHeight(true);
}


function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function updateFilters(filters, filterType, snacks)
{
    let filtersArr = Object.values(filters);

    if( filterType === null )
    {
        document.getElementById("filter-display").innerHTML = '';
    }
    else
    {
        if('snack_types' in filters && 'countries' in filters)
        {
            document.getElementById("filter-display").innerHTML =  toTitleCase(filters['snack_types'].replace('-',' ')) + ' &#x2192; ' + toTitleCase(filters['countries'].replace('-',' '));
        }
        else
        {
            document.getElementById("filter-display").innerHTML = toTitleCase(filtersArr[0].replace('-',' '));
        }

    }
    
    for (var i = 0; i < snacks.length; i++) 
    {
        let taxonomies = [
            snacks[i].dataset.type,
            JSON.parse(snacks[i].dataset.countries)
        ].flat();
        
        let intersection = taxonomies.filter(x => filtersArr.includes(x));

        if(filtersArr.length <= intersection.length)
        {
            snacks[i].style.display = "block";
        }
        else
        {
            snacks[i].style.display = "none";
        }
    }
}

function equalHeight(resize) {
    var elements = document.getElementsByClassName("equalHeight"),
    allHeights = [],
    i = 0;
    if(resize === true){
        for(i = 0; i < elements.length; i++){
            elements[i].style.height = 'auto';
        }
    }
    for(i = 0; i < elements.length; i++){
        var elementHeight = elements[i].clientHeight;
        allHeights.push(elementHeight);
    }
    for(i = 0; i < elements.length; i++){
        elements[i].style.height = Math.max.apply( Math, allHeights) + 'px';
        // Optional: Add show class to prevent FOUC
        if(resize === false){
            elements[i].className = elements[i].className + " show";
        }
    }
}

function updateHistoryState(taxonomy, action = "remove", term = null)
{
    switch( taxonomy )
    {
        case 'snack_types':
            if(action == "remove" && urlParams.has('t'))
            {
                urlParams.delete('t');
            }
            else if(action = "add")
            {
                urlParams.delete('t');
                urlParams.append('t', term);
            }
            break;
        case 'countries':
            if(action == "remove" && urlParams.has('c'))
            {
                urlParams.delete('c');
            }
            else if(action = "add")
            {
                urlParams.delete('c');
                urlParams.append('c', term);
            }
            break;
    }

    let newUrl = baseUrl + "?" + urlParams.toString();
    newUrl = newUrl.replace(/\?\s*$/, ""); // remove trailing ?
    window.history.replaceState({}, document.title, newUrl);
}

function removeFilter(term)
{
    if(document.querySelectorAll("input[name='"+term.dataset.taxonomy+"_filter']:checked").length)
    {
        document.querySelectorAll("input[name='"+term.dataset.taxonomy+"_filter']:checked")[0].checked = false;
    }

    updateHistoryState(term.dataset.taxonomy, 'remove');

    let event = new CustomEvent("removeFilter", {detail: term});
    document.body.dispatchEvent(event);
    jQuery(`#${term.dataset.taxonomy}RemoveFilter`).prop('disabled', true);
}
