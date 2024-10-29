jQuery(document).ready(function() {
    var $ = jQuery;

    if ($( window ).width() >= 768) { 
        $('#holidayHomeVid').html(`<video class="position-absolute country-loop" preload="auto" playsinline autoplay muted loop>
            <source src="/wp-content/uploads/2022/11/HolidayMastHead_CandyBar_1280x720_v6.mp4" type="video/mp4">
        </video>`);
    };
});