<?php
/**
 * The template for displaying Shop All page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: Shop All
 */
get_header(); 
$data = new shopAllModel(get_the_ID(), array('countries', 'snack_types'));
$search = empty($_GET['search']) ? null : $_GET['search'];
$page = new PageModel(get_the_ID());
?>

<div class="container shop-all py-3 py-md-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb px-0 py-0 bg-transparent align-items-center">
            <li class="breadcrumb-item"><a class="text-gray" href="<?php echo get_site_url()?>">Home</a></li>
            <li class="d-flex"><i class="fas fa-chevron-right text-gray px-1"></i></li>
            <li class="breadcrumb-item"><a class="text-gray" href="javascript:history.back()">Previous</a></li>
            <li class="d-flex"><i class="fas fa-chevron-right text-gray px-1"></i></li>
            <li class="breadcrumb-item text-gray" aria-current="page"><?php the_title();?></li>
        </ol>
    </nav>
    <div class="shop-all--wrap">

        <div class="d-flex align-items-center justify-content-between d-md-none">
            <button type="button" class="filterBtn d-flex align-items-center justify-content-start d-md-none pl-0 text-dark" id="filterBtn">
                <div>Filter & Sort</div>
                <svg class="icon icon-icon-filter">
                    <use xlink:href="#icon-icon-filter"></use>
                    <symbol id="icon-icon-filter" viewBox="0 0 57 32">
                        <path d="M55.11 21.334h-37.333v-1.773c0-0.979-0.794-1.773-1.773-1.773v0h-3.545c-0.979 0-1.773 0.794-1.773 1.773v0 1.773h-8.914c-0.979 0-1.773 0.794-1.773 1.773v0 3.545c0 0.979 0.794 1.773 1.773 1.773v0h8.893v1.773c0 0.979 0.794 1.773 1.773 1.773v0h3.545c0.979 0 1.773-0.794 1.773-1.773v0-1.773h37.354c0.979 0 1.773-0.794 1.773-1.773v0-3.545c0-0.979-0.794-1.773-1.773-1.773v0zM55.11 3.557h-8.887v-1.773c0-0.979-0.794-1.773-1.773-1.773v0h-3.545c-0.979 0-1.773 0.794-1.773 1.773v0 1.773h-37.359c-0.979 0-1.773 0.794-1.773 1.773v0 3.545c0 0.979 0.794 1.773 1.773 1.773v0h37.339v1.773c0 0.979 0.794 1.773 1.773 1.773h3.545c0.979 0 1.773-0.794 1.773-1.773v0-1.773h8.89c0.979 0 1.773-0.794 1.773-1.773v0-3.545c0-0.004 0-0.008 0-0.012 0-0.973-0.784-1.763-1.754-1.773h-0.001z"></path>
                    </symbol>
                </svg>
            </button>
            <a class="h7" href="<?php echo get_site_url()?>/shop-all/"><?php echo empty($search) ? '' : 'Clear Search'?></a>
        </div>

        <div class="filter-wrap mt-3 mx-auto position-relative">
            <section id="search-filter" class="d-flex align-items-start justify-content-between flex-md-column">
                <?php get_template_part( 'template-parts/search-filter', 'search-filter', $data );?>
            </section>
        </div>

        <section id="primary" class="content-area">
            <div id="main" class="site-main" role="main">
                <div class="items my-4 mx-auto w-100">
                    <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center mb-3 mb-lg-4">
                        <h2 class="h5 h4-md h3-lg h2-xl font-weight-semibold mb-lg-0" id="filter-display"><?php echo empty($search) ? '' : 'Search results for: '. $search;?></h2>
                        <a class="h7 h6-lg d-none d-md-block" href="<?php echo get_site_url()?>/shop-all/"><?php echo empty($search) ? '' : 'Clear Search'?></a>
                    </div>
                    
                    <div class="taxWrap items">
                    <?php 
                        $snacks_loop = $data->getSnacks( $search );
                        if($snacks_loop->have_posts()) :
                    
                            while($snacks_loop->have_posts()) : $snacks_loop->the_post();
                                get_template_part( 'template-parts/snack-block', 'snack-block' );
                            endwhile;
                        
                        endif;  

                        $out_of_stock_loop = $data->getOutOfStockSnacks( $search );
                        if($out_of_stock_loop->have_posts()) :
                    
                            while($out_of_stock_loop->have_posts()) : $out_of_stock_loop->the_post();
                                get_template_part( 'template-parts/snack-block', 'snack-block' );
                            endwhile;
                        
                        endif; 
                    ?>
                    
                    </div>
                    <?php if( $snacks_loop->post_count + $out_of_stock_loop->post_count == 0 ) :?>
                        <p class="h5 mt-2">This search has no results.</p>    
                    <?php endif;?>
                    <p id="filterMsg" class="h5 mt-2" style="display: none;">These filters have no results.</p>  
                </div>
            </div><!-- #main -->
        </section><!-- #primary -->
    </div>
</div>

<?php
get_footer();
?>
