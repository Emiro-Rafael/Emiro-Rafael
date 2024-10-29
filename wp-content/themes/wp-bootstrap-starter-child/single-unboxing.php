<?php
/* Template Name: unboxing */
get_header();
$model = new UnboxingModel( get_the_ID() );
$hero = $model->getSinglePostMetaByKey('video-preview');
$heroExt = pathinfo(wp_get_attachment_url( $hero ), PATHINFO_EXTENSION);
?>

<!-- chantal font link -->
<link rel="stylesheet" href="https://use.typekit.net/tjd6rif.css">

<section id="primary" class="content-area">
    <div class="unboxing-wrapper">
        <section class="unboxing-hero position-relative d-flex flex-column justify-content-center"
        <?php if( in_array( $heroExt, ['webp','png', 'jpg', 'jpeg'] ) ): ?>
            style="
                background-image: url('<?php echo wp_get_attachment_url( $hero );?>');
                background-size: cover;
                background-position: center bottom;
                background-repeat: no-repeat";
            <?php endif;?>
        >
            <?php if( $heroExt == 'mp4' ) : ?>
                <video class="country-loop position-absolute" id="countryLoop" preload="auto" playsinline autoplay muted loop>
                    <source src="<?php echo wp_get_attachment_url( $hero );?>" type="video/mp4">
                </video>
            <?php endif;?>
            
            <div class="hero-content position-relative mb-5 ml-3 ml-md-5 pl-2 pl-md-4 pl-xl-5">
                <?php if( User::checkLoggedIn() ) :?>
                    <h3 class="sub-greeting font-weight-bold font-italic h6 h5-md h4-xl">Hey <?php echo User::getSnackCrateUserData()->firstname;?>,</h3>
                <?php endif;?>

                <?php if( empty( $model->getSinglePostMetaByKey('greeting') ) ) :?>
                    <h3 class="font-weight-bolder h5 h4-md h1-xl">Welcome to</h3>
                <?php else:?>
                    <h3 class="font-weight-bolder h5 h4-md h1-xl"><?php echo $model->getSinglePostMetaByKey('greeting');?></h3>
                <?php endif;?>
                
                <h1 class="font-weight-bolder mb-4 <?php echo get_the_title() == 'Colombia' ? 'colombia-text' : ''?>"><?php echo str_replace( ' ', '<br>', get_the_title() );?></h1>
                
                <?php if( !empty( $model->getSinglePostMetaByKey('hero-video') ) ) :?>
                    <button type="button" data-toggle="modal" data-target="#unboxVidModal" class="btn btn-secondary text-white h8 h6-md h5-xl">Watch Our Video Tour <span><i class="fas fa-play ml-2"></i></i></span></button>
                <?php endif;?>
            </div>
        </section>

        <!-- learn about section -->
        <?php 
            if( $model->crate_type == 'country' && !empty( $model->getSinglePostMetaByKey('fun-facts_opening-text') ) ) 
                get_template_part( 'template-parts/unboxing/learn-about', null, $model );
        ?>

        <!-- snacks by size section -->
        <?php get_template_part( 'template-parts/unboxing/snacks-by-size', null, $model );?>

        <!-- upsell section -->
        <?php // get_template_part( 'template-parts/unboxing/upsell-uk', null, $model ); ?>

        <!-- recipe section -->
        <?php 
            if( $model->crate_type == 'country' && !empty( $model->getSinglePostMetaByKey('recipe_opening-text') ) )
                get_template_part( 'template-parts/unboxing/recipe', null, $model  );
        ?>

        <?php if( !empty($model->getSinglePostMetaByKey('trivia_questions')) ):?>

            <section class="subscribers-only">
                <img class="w-100 img-fluid border-secondary light-blue-curve" alt="decorative blue curve" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/blue-wave-curve.svg">
                <div class="subscribers-only-wrap">
                    <div class="subscribers-only-header d-flex flex-column align-items-center px-5 py-4">
                        <h2 class="h4 h1-md display-4-xl mb-lg-3 text-center font-weight-bolder">Exclusive Extras for <span>You</span></h2>

                        <?php if( !User::checkHasSubscription() ) :?>
                        <p class="h8 h6-md h5-xl mb-0 mb-lg-4 text-center font-weight-semibold">Take your unboxing experience up a notch with our exclusive extras for active subscribers only.</p>
                        <button class="signin-btn btn btn-secondary text-white w-100 h8 h6-md h5-xl mt-3" data-toggle="modal" data-target="#signinModal">Login to View</button>
                        <?php endif;?>
                    </div> <!-- subscribers-only-header -->
                    
                    <section class="trivia-activities mx-lg-3 mx-xxl-auto py-5<?php echo !User::checkHasSubscription() ? ' subscribers-only-blur' : '';?>">
                        <!-- trivia section -->
                        <?php get_template_part( 'template-parts/unboxing/trivia', null, $model  );?>
                        <!-- activities section -->
                        <?php get_template_part( 'template-parts/unboxing/activities', null, $model  );?>
                    </section>

                    <?php get_template_part( 'template-parts/unboxing/snack-poll', null, $model );?>
                    
                </div> <!-- subscribers-only-wrap -->
            </section> <!-- subscribers-only -->

        <?php endif;?>
        <?php if( empty( $model->getSinglePostMetaByKey('still-hungry-image') ) ):?>
            <section class="stock-up p-5 d-flex flex-column align-items-center my-lg-4">
                <h2 class="h6 h5-md h3-xl text-center font-weight-bolder mb-4" style="max-width: 500px;">Want some more of your favorites? Check out the CandyBar to stay stocked!</h2>
                <a class="btn btn-secondary text-white font-weight-semibold h7 h6-md h5-xl w-100" href="/shop-all?c=<?php echo $model->country_slug;?>">Stock Up!</a>
            </section>
        <?php else: ?>
            <section class="stock-up d-flex flex-column flex-md-row align-items-center justify-content-md-center mb-n3 mt-lg-4 position-relative">
                <h2 class="h4 h1-md display-4-xl text-center font-weight-bolder mt-5 mb-0 d-md-none">Still hungry?</h2>

                <img class="img-fluid big-tee mx-auto w-100 mb-n3 mx-md-0" alt="<?php echo get_post_meta( $model->getSinglePostMetaByKey('still-hungry-image'), '_wp_attachment_image_alt', true);?>" src="<?php echo wp_get_attachment_url( $model->getSinglePostMetaByKey('still-hungry-image') );?>">

                <div class="stock-up-bottom w-100 d-flex flex-column align-items-center pb-5 d-md-none">
                    <h3 class="h6 h5-md h3-xl text-center font-weight-bolder my-4 mx-auto">We've got you covered. Check out the CandyBar to stay stocked!</h3>
                    <a class="btn btn-secondary text-white font-weight-semibold h7 h6-md h5-xl w-100 mx-auto mb-lg-4" href="/shop-all?c=<?php echo $model->country_slug;?>">Stock Up!</a>
                </div>

                <div class="desktop-only d-none d-md-flex flex-column align-items-center">
                    <h2 class="h4 h1-md display-4-xl text-center font-weight-bolder mb-0">Still hungry?</h2>
                    <h3 class="h6 h5-md h3-xl text-center font-weight-bolder my-4 mx-auto">We've got you covered. Check out the CandyBar to stay stocked!</h3>
                    <a class="btn btn-secondary text-white font-weight-semibold h7 h6-md h5-xl w-100 mx-auto mb-lg-4" href="/shop-all?c=<?php echo $model->country_slug;?>">Stock Up!</a>
                </div>

                <div class="light-blue-wave position-absolute">
                    <img class="w-100 img-fluid border-secondary" alt="decorative blue curve" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/blue-wave-curve.svg">
                    <div class="color-fill"></div>
                </div>

                <div class="dark-blue-wave position-absolute">
                    <img class="w-100 img-fluid border-secondary" alt="decorative blue curve" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/blue-wave-curve-2.svg">
                </div>
            </section>
        <?php endif;?>
    </div> <!-- unboxing-wrapper -->
</section>

<!-- unboxing video modal -->
<?php get_template_part( 'modals/unboxing/unboxing-video-modal', null, $model );?>

<!-- snack poll modal -->
<?php get_template_part( 'modals/unboxing/snack-poll-modal', null, $model );?>

<!-- drink upgrade modal -->
<?php get_template_part( 'modals/unboxing/drink-upgrade-modal', null, $model );?>

<?php get_footer();?>