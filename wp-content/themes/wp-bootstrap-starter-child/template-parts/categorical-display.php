<?php
/**
 * Template part for displaying various category items
 * 
 * Requires an array of data with (in order) Title of the block, Taxonomy to list, Max number of items to display
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
extract($args);
if(!isset($show_titles)) $show_titles = true;

$items = PageModel::getCategoricalData($args);
?>
<div class="categories container mt-5 mt-md-3 pt-3 pb-xl-3">
    <h2 class="text-left mb-2 mb-lg-3 mb-xl-4"><?php echo $title;?></h2>

    <div class="categories-container ml-n1 ml-lg-0">
        <div class="categories-options pr-2 pr-lg-0">
            
            <?php foreach($items as $item): ?>
                <?php 
                    $snack = new SnackModel($item->ID);
                ?>
                <div class="single-snack ml-lg-0">
                    
                    <a class="snack-btn" href="<?php echo $item->link;?>">
                        <?php if(!empty($item->flag)) :?>
                            <img class="flag-img img-fluid" alt="<?php echo $snack->getCountryName();?> Flag" src="<?php echo $snack->getCountryFlag()?>" />
                        <?php endif; ?>

                        <img class="img-fluid" alt="Image of <?php echo $item->name;?>" src="<?php echo $item->thumbnail; ?>">
                    </a>

                    <?php if($show_titles) :?>
                        <h3 class="snack-title equalHeight"><?php echo $item->name;?></h3>
                    <?php endif; ?>

                    <?php if ( $snack->hasReviews() && $snack->getRatingsCount() >= 3 ): ?>
                        <?php get_template_part( 'template-parts/star-rating', get_post_format(), $item->rating ); ?>
                    <?php else: ?>
                    
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        
        </div> <!-- end pop-cat-options -->
    </div> <!-- end pop-cat-container -->
    
</div> <!-- end popular categories -->