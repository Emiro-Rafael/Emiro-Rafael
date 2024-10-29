<section class="learn-about mt-n5 mb-5 mx-2 mx-lg-3 mx-xxl-auto">
    <div class="semi-circle mx-auto w-100">
    </div>
    <div class="learn-about-content d-flex flex-column align-items-center">
        <img class="crate-img img-fluid mx-auto mb-lg-0" alt="A blue open SnackCrate from <?php echo get_the_title();?> overflowing with snacks" src="<?php echo $args->country_model->getFeaturedImage();?>">

        <div class="learn-about-title d-flex flex-column align-items-center mb-3 mb-md-4 mb-lg-5 px-4">
            <h2 class="text-center font-weight-bolder mb-0 h4 h1-md display-4-xl">Let's learn about</h2>
            <h2 class="text-center font-weight-bolder mb-0 h4 h1-md display-4-xl"><?php echo get_the_title() == 'United Kingdom' ? 'the ' : '';?><?php echo get_the_title();?></h2>
        </div> <!-- learn-about-title -->

        <div class="learn-about-text d-xl-flex justify-content-around px-4 px-md-5 pb-2 pb-md-4 mx-xxl-5 pt-xxl-4">
            <p class="desc-text mb-4 mb-lg-5 pr-xl-5 h6 h4-md h2-xl"><?php echo $args->getSinglePostMetaByKey('fun-facts_opening-text');?></p>

            <div class="fun-facts ml-xxl-5">
                <?php foreach($args->getSinglePostMetaByKey('fun-facts_facts') as $fact):?>
                    <div class="single-fact d-flex align-items-center justify-content-start mb-4 mb-lg-5">
                        <img class="fact-icon mr-4 mr-md-5 mr-xl-4" alt="blue icon describing the fun fact described on the right" src="<?php echo wp_get_attachment_url($fact['icon']);?>">
                        <p class="fact-text mb-0 ml-xl-2 font-weight-semibold h6 h4-md h3-xl"><?php echo $fact['fact'];?></p>
                    </div>
                <?php endforeach;?>
            </div>
        </div> <!-- learn-about-text -->

        <div class="accordion read-more-accordion w-100" id="readMoreAccordion">
            <div id="readMoreCollapse" class="collapse" aria-labelledby="readMoreHeading" data-parent="#readMoreAccordion">
                <p class="h6 h5-md h4-xl px-4 px-md-5 mb-4 mb-md-5 mx-xxl-5">
                    <?php echo $args->getSinglePostMetaByKey('fun-facts_closing-text');?>
                </p>
            </div>

            <?php if( !empty( $args->getSinglePostMetaByKey('fun-facts_closing-text') ) ): ?>
            <div class="w-100" id="readMoreHeading">
                <button class="btn text-center text-dark w-100 bg-gray-light py-2 h7 h6-md h5-xl" id="readMoreAccordionBtn" type="button" data-toggle="collapse" data-target="#readMoreCollapse" aria-expanded="false" aria-controls="#readMoreCollapse">
                    Read more <span class="h9 h7-xl ml-1 align-middle"><i class="fas fa-plus"></i></span>
                </button>
            </div>
            <?php endif;?>
        </div> <!-- read-more-accordion -->
    </div> <!-- learn-about-content -->
</section>