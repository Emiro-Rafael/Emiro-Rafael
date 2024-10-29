<?php
/**
 * The template for displaying Countries
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Countries
 */
get_header();
?>

<section id="primary" class="content-area mb-5">    
    <div class="collections py-3 py-md-4">
        <div class="container">
            <!-- breadcrumb nav -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb px-0 py-0 bg-transparent align-items-center mb-md-4 mb-xl-5">
                    <li class="breadcrumb-item"><a class="text-gray" href="<?php echo get_site_url()?>">Home</a></li>
                    <li class="d-flex"><i class="fas fa-chevron-right text-gray px-1"></i></li>
                    <li class="breadcrumb-item text-gray" aria-current="page"><?php the_title();?></li>
                </ol>
            </nav>

            <h1 class="h4 h3-md h1-xl font-weight-bold mb-3 mb-md-4 mb-xl-5">Countries</h1>

            <div class="collections-container w-100">

                <?php foreach(CountryModel::getAllCountries() as $country) :?>
                    <?php $country_object = new CountryModel($country->ID);?>
                    <a class="single-collection p-3 p-sm-0 mb-sm-2 mb-md-0" href="<?php echo get_permalink( $country->ID );?>">
                        <img class="collection-img w-100 img-fluid" alt="" src="<?php echo $country_object->getFeaturedImage();?>">
                        <h2 class="h6 h5-sm h4-md h3-lg h2-xl font-weight-bold mb-0 mt-3 mt-lg-4"><?php echo $country->post_title;?></h2>
                    </a>
                <?php endforeach;?>

            </div> <!-- collections-container -->

        </div> <!-- container -->

    </div><!-- collections -->
</section><!-- #primary -->

<?php get_footer();?>