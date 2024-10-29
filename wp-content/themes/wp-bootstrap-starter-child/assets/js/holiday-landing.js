jQuery(document).ready(function() {
    var $ = jQuery;

    

    if ($( window ).width() >= 768) { 
        $('#holidayLandingVid').html(`<video class="position-absolute rounded" preload="auto" playsinline autoplay muted loop>
            <source src="/wp-content/uploads/2022/11/HolidayMastHead_CandyBar_1280x720-center-min.mp4" type="video/mp4">
        </video>`);
    };
});