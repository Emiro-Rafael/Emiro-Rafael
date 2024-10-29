<?php
/* Template Name: unboxing */
get_header();
?>

<!-- chantal font link -->
<link rel="stylesheet" href="https://use.typekit.net/tjd6rif.css">

<section id="primary" class="content-area">
    <div class="unboxing-wrapper">
        <section class="unboxing-hero position-relative d-flex flex-column justify-content-center">
            <video class="country-loop position-absolute" id="countryLoop" preload="auto" playsinline autoplay muted loop>
                <source src="https://www.snackcrate.com/wp-content/themes/snackcrate_redesign/assets/video/Holiday_Banner4K.mp4" type="video/mp4">
            </video>

            <div class="hero-content position-relative mb-5 ml-4 ml-md-5 pl-2 pl-md-4 pl-xl-5">
                <h3 class="sub-greeting font-weight-bold font-italic h6 h5-md h4-xl">Hey Lisa,</h3>
                <h3 class="font-weight-bolder h5 h4-md h1-xl">Welcome to</h3>
                <h1 class="font-weight-bolder mb-4">South<br>Korea</h1>
                <button type="button" data-toggle="modal" data-target="#unboxVidModal" class="btn btn-secondary text-white h8 h6-md h5-xl">Watch Our Video Tour <span><i class="fas fa-play ml-2"></i></span></button>
            </div>
        </section>

        <!-- learn about section -->
        <?php get_template_part( 'template-parts/unboxing/learn-about' );?>

        <!-- snacks by size section -->
        <?php get_template_part( 'template-parts/unboxing/snacks-by-size' );?>

        <!-- recipe section -->
        <?php get_template_part( 'template-parts/unboxing/recipe' );?>

        <section class="subscribers-only pt-xl-5">
            <img class="w-100 img-fluid border-secondary mb-n1" alt="decorative blue curve" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/blue-wave-curve.svg">
            <div class="subscribers-only-wrap">
                <div class="subscribers-only-header d-flex flex-column align-items-center px-5 py-4">
                    <h2 class="h4 h1-md display-4-xl mb-lg-3 text-center font-weight-bolder">Exclusive Extras for <span>You</span></h2>

                    <?php if( empty($user_data) ):?>
                        <p class="h8 h6-md h5-xl mb-lg-4 text-center font-weight-semibold">Take your unboxing experience up a notch with our exclusive extras for subscribers only.</p>
                        <button class="signin-btn btn btn-secondary text-white w-100 h8 h6-md h5-xl" data-toggle="modal" data-target="#signinModal">Login to View</button>
                    <?php else:?>

                    <?php endif;?>
                </div> <!-- subscribers-only-header -->

                <section class="trivia-activities mx-lg-3 mx-xxl-auto py-5">
                    <!-- trivia section -->
                    <?php get_template_part( 'template-parts/unboxing/trivia' );?>
                    <!-- activities section -->
                    <?php get_template_part( 'template-parts/unboxing/activities' );?>
                </section>

                <!-- snack-poll section -->
                <?php get_template_part( 'template-parts/unboxing/snack-poll' );?>
                
            </div> <!-- subscribers-only-wrap -->
        </section> <!-- subscribers-only -->

        <section class="stock-up p-5 d-flex flex-column align-items-center my-lg-4">
            <h2 class="h6 h5-md h3-xl text-center font-weight-bolder mb-4">Want some more of your favorites? Check out the CandyBar to stay stocked!</h2>
            <a class="btn btn-secondary text-white font-weight-semibold h7 h6-md h5-xl w-100" href="/shop-all">Stock Up!</a>
        </section>
    </div> <!-- unboxing-wrapper -->
</section>

<!-- unboxing video modal -->
<?php get_template_part( 'modals/unboxing/unboxing-video-modal' );?>

<!-- snack poll modal -->
<?php get_template_part( 'modals/unboxing/snack-poll-modal' );?>

<?php get_footer();?>