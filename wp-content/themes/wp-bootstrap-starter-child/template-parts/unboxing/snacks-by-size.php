<section class="snacks-by-size mb-5 mx-2 mx-lg-3 mx-xxl-auto pt-4 py-xl-5">
    <div class="snacks-by-size-text mb-4">
        <h2 class="text-center font-weight-bolder h4 h1-md display-4-xl">Let's get to the good stuff</h2>
        <p class="text-center h6 h4-md h3-xl mb-0 mx-auto">Our SnackSheets are now paperless! Explore your crate using this page, and drop a rating so we know your favorites!</p>
    </div>

    <?php foreach( PageModel::SIZES as $lc_size => $size ): ?>
        <div class="accordion size-accordion w-100" id="<?php echo $lc_size;?>Accordion">
            <div class="w-100" id="<?php echo $lc_size;?>Heading">
                <button 
                    class="size-btn <?php echo $lc_size;?>-btn" 
                    id="<?php echo $lc_size;?>AccordionBtn" 
                    type="button" data-toggle="collapse" 
                    data-target="#<?php echo $lc_size;?>Collapse" 
                    aria-expanded="false" 
                    aria-controls="#<?php echo $lc_size;?>Collapse" 
                    data-size="<?php echo $lc_size;?>"
                    data-ajaxclickparent="<?php echo $lc_size;?>Collapse"
                    >
                    <?php echo $size;?> <span class="h8 h6-md h5-xl ml-1 align-middle"><i class="fas fa-plus"></i></span>
                </button>
            </div>
        
            <div 
                id="<?php echo $lc_size;?>Collapse" 
                class="<?php echo $lc_size;?>-collapse collapse mx-n2 mx-lg-n3 mx-xxl-auto" 
                aria-labelledby="<?php echo $lc_size;?>Heading" 
                data-parent="#<?php echo $lc_size;?>Accordion"
                data-ajaxclickcontainer="snacks_by_size"
                data-ajaxparams='<?php echo json_encode( array( 'size' => $lc_size, 'post_id' => $args->getId() ) );?>'
                >
            </div>
        </div>
        
    <?php endforeach;?>

    <?php 
    $drink = current( $args->getSnacksBySize('drink') );
    if( !empty($drink) ):
    ?>
        <div class="drink-upgrade mx-auto mt-md-4 mt-lg-5">
            <h3>Drink Upgrade</h3>

            <div class="drink-upgrade-content d-flex justify-content-center align-items-center py-4 py-md-5 pr-4 pr-md-5">
                <img class="img-fluid" alt="can of <?php echo get_post_meta( $drink->ID, 'user-friendly-name', true );?>" src="<?php echo get_the_post_thumbnail_url( $drink->ID, 'full' );?>">
                <div class="d-flex flex-column">
                    <h4 class="h6 h5-md h4-xl text-primary font-weight-bolder text-uppercase text-center"><?php echo get_post_meta( $drink->ID, 'user-friendly-name', true );?></h4>
                    <div class="drink-desc h7 h6-md h5-xl text-center"><?php echo trim($drink->post_content);?></div>
                    <div class="add-to-cart mb-2">
                        <?php get_template_part( 'template-parts/add-to-cart', get_post_format(), $drink->ID ); ?>
                    </div>

                    <?php if( User::checkHasSubscription() && !empty( User::getDrinklessSubscriptionData() ) ) :?>
                        <!-- open drink-upgrade-modal -->
                        <a class="h8 h6-md font-weight-semibold text-center text-dark mx-auto mt-3 mt-md-4" id="drinkUpgradeModalLink" type="button" data-toggle="modal" data-target="#drinkUpgradeModal">Add Monthly Drink Upgrade</a>
                    <?php endif;?>

                    <p class="h8 h6-md font-weight-semibold text-center text-dark mx-auto mt-3 mt-md-4 mb-0 d-none" id="drinkUpgradeAddedText">Drink Upgrade Added</p>
                </div>
            </div>

            <?php 
            if( User::checkHasSubscription() )
            {
                if( SnackModel::userCanReview( get_current_user_id(), $drink->ID ) )
                {
                    get_template_part( 'template-parts/unboxing/review-stars', null, $drink->ID );
                }
                else
                {
                    echo '<div class="review-stars-container">';
                        get_template_part( 'template-parts/star-rating', get_post_format(), SnackModel::getUserSnackRating( get_current_user_id(), $drink->ID ) );
                    echo '</div>';
                }
            }
            ?>

        </div> <!-- drink-upgrade -->
    <?php endif;?>
</section>