var cardSwiper;
var addressSwiper;


const relatedItemsSwiper = new Swiper('.related-items-swiper', {
            grabCursor: true,
            centeredSlides: false,
			loop: false,
            initialSlide: 0,

            navigation: {
                nextEl: '.next-related-item',
                prevEl: '.prev-related-item',
            },

            keyboard: {
                enabled: true,
            },

            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 3.5,
                    spaceBetween: 10,
                },

                // when window width is >= 576px
                576: {
                    slidesPerView: 4,
                    spaceBetween: 16,
                    centeredSlides: false,
                  },

                // when window width is >= 768px
                768: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                    centeredSlides: false,
                },

                // when window width is >= 992px
                992: {
                  slidesPerView: 5,
                  spaceBetween: 36,
                  centeredSlides: false,
                },
            }
		});

const moreFromCountrySwiper = new Swiper('.more-from-country-swiper', {
            grabCursor: true,
			centeredSlides: false,
			loop: true,
            initialSlide: 2,

            navigation: {
                nextEl: '.next-country-item',
                prevEl: '.prev-country-item',
            },

            keyboard: {
                enabled: true,
            },

            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 3.5,
                    spaceBetween: 10,
                },

                // when window width is >= 576px
                576: {
                    slidesPerView: 4,
                    spaceBetween: 16,
                    centeredSlides: true,
                  },

                // when window width is >= 768px
                768: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                    centeredSlides: true,
                },

                // when window width is >= 992px
                992: {
                  slidesPerView: 5,
                  spaceBetween: 36,
                  centeredSlides: true,
                },
            }
		});

const userReviewsSwiper = new Swiper('.user-reviews-container', {
    grabCursor: true,
    centeredSlides: true,
    loop: true,

    navigation: {
        nextEl: '.next-user-review',
        prevEl: '.prev-user-review',
    },

    keyboard: {
        enabled: true,
    },

    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 16,
        },

        // when window width is >= 576px
        576: {
            slidesPerView: 2,
            spaceBetween: 16,
        },

        // when window width is >= 768px
        768: {
            slidesPerView: 2,
            spaceBetween: 24,
        },

        // when window width is >= 992px
        992: {
            slidesPerView: 3,
            spaceBetween: 36,
        },
    }
});

const enableSnackSwiper = function(size) {
    // snacks-by-size swipers
    let sizeSwiper = new Swiper(`.${size}-size-swiper`, {
        grabCursor: true,
        centeredSlides: true,
        loop: true,
        slidesPerView: 1.5,

    
        navigation: {
            nextEl: `.next-${size}-snack`,
            prevEl: `.prev-${size}-snack`,
        },
    
        keyboard: {
            enabled: true,
        },
    
        breakpoints: {
            // // when window width is >= 576px
            576: {
                slidesPerView: 2.5,
            },
    
            // // when window width is >= 768px
            768: {
                slidesPerView: 2.5,
                spaceBetween: 10,
            },
    
            // // when window width is >= 992px
            992: {
                slidesPerView: 3.75,
                spaceBetween: 15,
            },
        }
    });
    equalHeight(true);

    jQuery(`.${size}-size-swiper .add_to_cart`).css('opacity', 1);
}
document.body.addEventListener('containerLoaded', function(e) {
    if(e.detail.ajaxclickcontainer == 'snacks_by_size')
    {
        let params = JSON.parse(e.detail.ajaxparams);
        enableSnackSwiper(params.size);
    }
});

// trivia swiper
const triviaSwiper = new Swiper('.trivia-swiper', {
    centeredSlides: true,
    slidesPerView: 1,
    allowTouchMove: false,

    pagination: {
        el: '.trivia-pagination',
        type: 'bullets',
    },

    navigation: {
        nextEl: '.next-question',
        prevEl: '.prev-question',
    },

    keyboard: {
        enabled: true,
    },
});

// Equal Heights

(function () {
    equalHeight(false);
})();
  
window.onresize = function(){
    equalHeight(true);
}

jQuery('.size-accordion .collapse').on('show.bs.collapse', function (e) {
    equalHeight(true);
});
  
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

const enableCardSwiper = function() {
    if ( cardSwiper !== undefined ) cardSwiper.destroy;
    if ( addressSwiper !== undefined ) addressSwiper.destroy;

    cardSwiper = new Swiper('.card-swiper', {
        grabCursor: true,
        centeredSlides: false,
        loop: false,
        initialSlide: 0,
    
        navigation: {
            nextEl: '.next-card-item',
            prevEl: '.prev-card-item',
        },
    
        keyboard: {
            enabled: true,
        },
    
        breakpoints: {
           // when window width is >= 320px
            320: {
                slidesPerView: 'auto',
                spaceBetween: 0,
            },

            // when window width is >= 576px
            576: {
                slidesPerView: 'auto',
                spaceBetween: 12,
                centeredSlides: true,
            },
    
            // when window width is >= 768px
            768: {
                slidesPerView: 1.5,
                spaceBetween: 24,
            },
    
            // when window width is >= 992px
            992: {
                slidesPerView: 1.5,
                spaceBetween: 36,
            },
        }
    });

    addressSwiper = new Swiper('.address-swiper', {
        grabCursor: true,
        centeredSlides: false,
        loop: false,
        initialSlide: 0,

        navigation: {
            nextEl: '.next-address-item',
            prevEl: '.prev-address-item',
        },

        keyboard: {
            enabled: true,
        },

        breakpoints: {
            // when window width is >= 320px
            320: {
                slidesPerView: 'auto',
                spaceBetween: 0,
            },

            // when window width is >= 576px
            576: {
                slidesPerView: 'auto',
                spaceBetween: 12,
                centeredSlides: true,
            },

            // when window width is >= 768px
            768: {
                slidesPerView: 1.5,
                spaceBetween: 24,
                centeredSlides: true,
            },

            // when window width is >= 992px
            992: {
                slidesPerView: 1.5,
                spaceBetween: 36,
                centeredSlides: true,
            },
        }
    });
}

const breakpoint = window.matchMedia( '(min-width:768px)' );
const breakpointChecker = function() {
    
    // if larger viewport and multi-row layout needed
    if ( breakpoint.matches === true ) 
    {
        // clean up old instances and inline styles when available
        if ( cardSwiper !== undefined ) 
        {
            if ( cardSwiper.length > 1 ) cardSwiper = cardSwiper[0];
            cardSwiper.destroy;
        }

        if ( addressSwiper !== undefined )
        {
            if ( addressSwiper.length > 1 ) addressSwiper = addressSwiper[0];
            addressSwiper.destroy;
        }

        jQuery(".card-swiper .swiper-wrapper").removeAttr('style');
        jQuery(".address-swiper .swiper-wrapper").removeAttr('style');
        // or/and do nothing
        return;

    // else if a small viewport and single column layout needed
    } 
    else if ( breakpoint.matches === false ) 
    {
        // fire small viewport version of swiper
        return enableCardSwiper();
    }

};


breakpointChecker();
jQuery( window ).resize(function() {
    breakpointChecker();
    jQuery(".card-swiper .swiper-wrapper").removeAttr('style');
    jQuery(".address-swiper .swiper-wrapper").removeAttr('style');
});