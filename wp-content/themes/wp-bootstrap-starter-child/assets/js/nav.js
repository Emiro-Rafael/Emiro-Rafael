jQuery(document).ready(function() {
    var $ = jQuery;

    $('.countries-menu-mobile li.geography > a').on('click', function (event) {    
        $(this).parent().parent().css('transform','translate3d(-100vw, 0px, 0px)');
        $(this).parent().children('ul.sub-menu-mobile').addClass('show-menu');
        return false;
    });

    $('.menu-back').on('click', function (event) {
        $(this).parent().parent().parent().css('transform','translate3d(0%, 0px, 0px)');
        $(this).parent().parent().children('ul.sub-menu-mobile').removeClass('show-menu');
        return false;
    });

    // add background overlay when mobile nav is open
    $( '.navbar-toggler' ).unbind( "click" );
    $('.navbar-toggler').click(function (e) {
        $('.site-content').css('transition', 'none');
        $('.site-content').css('padding-top', '34px');
        $('body').addClass('noscroll');
        $('#header').css('z-index', 'unset');
        $('#header').css('margin-top', '-34px');
        $('#header').removeClass('fixed-top');
        $('#header').addClass('position-relative');
        $('#secondary-header').css('z-index', 'unset');
        $('body').append('<div class="overlay fixed-top fixed-bottom"></div>');
    });

    // remove background overlay when mobile nav is closed
    $('#collapse-close').on('click', function () {
        $('.site-content').css('padding-top', '131px');
        $('body').removeClass('noscroll');
        $('#header').css('z-index', 1050);
        $('#header').css('margin-top', 'unset');
        $('#header').removeClass('position-relative');
        $('#header').addClass('fixed-top');
        $('#secondary-header').css('z-index', 1050);
        $('.overlay').remove();
        setTimeout(() => {$('.site-content').css('transition', 'all .3s ease')}, 100);
    });

    // close mobile nav when user clicks login and signinModal is open
    $('#signInTrigger').on('click', function () {
        $('.site-content').css('padding-top', '131px');
        $('body').removeClass('noscroll');
        $('#navbarToggler').removeClass('show');
        $('#navbarToggler').removeClass('collapse');
        $('#navbarToggler').addClass('collapsing');
        $('#navbarToggler').removeClass('collapsing');
        $('#navbarToggler').addClass('collapse');
        $('#header').css('margin-top', 'unset');
        $('#header').removeClass('position-relative');
        $('#header').addClass('fixed-top');
        $('.overlay').remove();
    });

    // change header z-index when modal is shown so it appears behind modal backdrop
    let modalArray = ['#viewSpecsModal', '#editShippingAddressModal-0', '#editPaymentModal-0', '#snackRatingModal', '#signinModal', '#snackPollModal', '#unboxVidModal', '#drinkUpgradeModal'];

    modalArray.forEach(function(modal) {
        $(modal).on('show.bs.modal', function () {
            $('#header').css('z-index', 'unset');
            $('#secondary-header').css('z-index', 'unset');
        });

        $(modal).on('hidden.bs.modal', function () {
            $('#header').css('z-index', 1050);
            $('#secondary-header').css('z-index', 1050);
        });
    });

    // remove overlay if user increases browser width > 992px, add overlay if user descreases browser width < 992px while navbar-collapse is shown
    var resizeTimer;

    $(window).on('resize', function() {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {

            if ($( window ).width() > 992) {
                $('.overlay').remove();
            } 
            else if ($( window ).width() < 992 && $('.navbar-collapse').hasClass('show') && $('.overlay').length < 1) {
                $('body').append('<div class="overlay fixed-top fixed-bottom"></div>');
            }
                    
        }, 100);
    });

    // hide nav on scroll down, show nav on scroll up
    const headerElem  = $('#header');
    const headerElemHeight = headerElem.outerHeight();
    const secHeaderHeight = $('#secondary-header').outerHeight();
    const countriesMenuDialog = $('#countriesMenuModalDialog');

    // set top of countries-menu = to header + secondary-header height
    $(countriesMenuDialog).css('top', `calc(${secHeaderHeight}px + ${headerElemHeight}px)`);

    if ($(document).scrollTop() <= 0 ) {
        $(headerElem).css('top', `${secHeaderHeight}px`);
        $(countriesMenuDialog).css('top', `calc(${secHeaderHeight}px + ${headerElemHeight}px)`);
        $('#content').css('padding-top', `${headerElemHeight}px`);
    } else {
        $(headerElem).css('top', '0');
        $(countriesMenuDialog).css('top', `${headerElemHeight}px`);
        $('#content').css('padding-top', `${headerElemHeight - secHeaderHeight}px`);
    }

    $(window).on('scroll', function() {
        const headerElemHeight = headerElem.outerHeight();
        const secHeaderHeight = $('#secondary-header').outerHeight();

        // secondary-header only visible at top of page, adjust countries-menu top if user tries to open on scroll up before top of page
        if ($(document).scrollTop() <= 0 ) {
            $(headerElem).css('top', `${secHeaderHeight}px`);
            $(countriesMenuDialog).css('top', `calc(${secHeaderHeight}px + ${headerElemHeight}px)`);
            $('#content').css('padding-top', `${headerElemHeight}px`);
        } else {
            $(headerElem).css('top', '0');
            $(countriesMenuDialog).css('top', `${headerElemHeight}px`);
            $('#content').css('padding-top', `${headerElemHeight - secHeaderHeight}px`);
        }

        // Catch false equivelance glitch
        if ( this.oldScroll === this.scrollY ) {
            return null;
        } else if ( this.oldScroll >= this.scrollY || this.scrollY < 0 ) {
            $(headerElem).removeClass('hide');
        } else if ( this.scrollY >= headerElemHeight ) {
            $(headerElem).addClass('hide');
        }

        this.oldScroll = this.scrollY;
    })

    $(window).on('resize', function() {
        const headerElemHeight = headerElem.outerHeight();
        const secHeaderHeight = $('#secondary-header').outerHeight();

        // set top of header = to secondary-header-height on resize
        $(headerElem).css('top', `${secHeaderHeight}px`);
        $('#content').css('padding-top', `${headerElemHeight}px`);

        // set top of countries-menu = to header + secondary-header height
        if ($(document).scrollTop() == 0 ) {
            $(countriesMenuDialog).css('top', `calc(${secHeaderHeight}px + ${headerElemHeight}px)`);
        } else {
            $(countriesMenuDialog).css('top', `${headerElemHeight}px`);
        }
    });
});