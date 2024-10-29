<?php 
/*
Template Name: 404
*/

get_header(); 
?>
 
 <section id="primary" class="content-area mb-5">    
    <div class="404 py-3 pt-lg-5">
        <div class="container text-white d-flex flex-column align-items-center">
            <h1 class="display-4 display-3-lg font-weight-bold">404</h1>
            <h2 class="display-5 display-4-lg font-weight-bold text-center">No snacks here, playa.</h2>

            <img alt="an empty SnackCrate box frowning" class="img-fluid my-3 my-lg-5" src="<?php echo get_bloginfo('url')?>/wp-content/uploads/2021/08/box_404_new.png">

            <div class="d-flex flex-column align-items-center">
                <p class="h5-lg text-white mb-lg-4">But since you've made it this far, we might as well tell you what our favorite snacks are:</p>

                <ul class="h5-lg mb-lg-4">
                    <li>Matcha Kit Kats from Japan</li>
                    <li>Old Dutch Ketchup Chips from Canada</li>
                    <li>Cadbury Double Deckers from the UK</li>
                    <li>Ahlgrens Bilar from Sweden</li>
                    <li>Stroopwafels from the Netherlands</li>
                </ul>

                <p class="h5-lg text-white">We hope you find the page you're looking for!</p>
            </div>

        </div> <!-- container -->

    </div><!-- 404 -->
</section><!-- #primary -->

<?php get_footer(); ?>