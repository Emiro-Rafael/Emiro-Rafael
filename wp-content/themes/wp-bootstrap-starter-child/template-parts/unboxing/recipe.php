<section class="recipe mx-2 mx-lg-3 mx-xxl-auto mb-5 d-flex flex-column">
    <div class="recipe-header p-3 mt-2 mt-md-4 mt-xl-5 mx-auto">
        <h2 class="text-center font-weight-bolder h4 h1-md display-4-xl mb-2">Let's get cooking!</h2>
        <p class="text-center h6 h4-md h3-xl mb-0"><?php echo $args->getSinglePostMetaByKey('recipe_opening-text');?></p>
    </div>

    <div class="recipe-video px-3 pb-3 mt-2 my-md-4 my-xl-5 mb-3">
        <!-- .thumbnail background url dynamic -->
        <div class="thumbnail position-relative mx-auto" id="thumbnail" style="background: no-repeat center/cover url('<?php echo wp_get_attachment_url( $args->getSinglePostMetaByKey('recipe_video-thumbnail') );?>');">
            <button class="btn position-absolute open-recipe-vid" id="openRecipeVid" aria-label="Open recipe video"><span class="text-white h1 display-4-md display-3-xl"><i class="fas fa-play"></i></span></button>

            <button class="btn position-absolute close-recipe-vid p-0" id="closeRecipeVid" aria-label="Close recipe video"><span>&times;</span></button>

            <div class="embed mx-auto" id="embedVid">
                <!-- iframe src dynamic -->
                <iframe title="Recipe How To Video" id="recipeVideo" class="responsive-iframe w-100 h-100" src="<?php echo $args->getSinglePostMetaByKey('recipe_video');?>"></iframe>
            </div>
        </div>
    </div> <!-- recipe-video -->

    <div class="accordion recipe-accordion w-100" id="recipeAccordion">
        <div id="recipeCollapse" class="collapse" aria-labelledby="recipeHeading" data-parent="#recipeAccordion">
            <div class="recipe-description px-4 px-md-5 mb-4">
                <h3 class="text-center font-weight-bolder h4 h3-md h2-xl mb-0"><?php echo $args->getSinglePostMetaByKey('recipe_local-name');?></h3>
                <p class="text-center font-weight-semibold mb-2 h5-md h4-xl"><?php echo $args->getSinglePostMetaByKey('recipe_english-name');?></p>
                <p class="text-dark text-md-center h5-md h4-xl mb-0 mx-auto"><?php echo $args->getSinglePostMetaByKey('recipe_description');?></p>
            </div>

            <div class="recipe-stats bg-gray-light p-3 p-xl-4 mx-2 mx-md-4 mb-4 d-md-flex align-items-center">
                <div class="time mb-2 mb-md-0">
                    <p class="stat-label">Total time</p>
                    <p class="stat"><?php echo $args->getSinglePostMetaByKey('recipe_total-time');?></p>
                </div>

                <div class="serves mb-2 mb-md-0">
                    <p class="stat-label">Serves</p>
                    <p class="stat"><?php echo $args->getSinglePostMetaByKey('recipe_serves');?></p>
                </div>

                <div class="difficulty">
                    <p class="stat-label">Difficulty</p>
                    <p class="stat"><?php echo $args->getSinglePostMetaByKey('recipe_difficulty');?></p>
                </div>
            </div> <!-- recipe-stats -->

            <div class="recipe-ingredients px-4 d-md-flex align-items-start justify-content-start pt-2 pt-lg-4 pt-xl-5 mb-4">
                <img loading="lazy" class="img-fluid d-none d-md-block mr-4 mr-lg-5 pr-xl-3" alt="image of ingredients listed below spread out on a white table" src="<?php echo wp_get_attachment_url( $args->getSinglePostMetaByKey('recipe_ingredients-image') );?>?>">
                <div class="ingredients-list">
                    <?php echo $args->getSinglePostMetaByKey('recipe_ingredients');?>
                </div> <!-- ingredients-list -->
            </div> <!-- recipe-ingredients -->

            <div class="recipe-instructions px-4 pt-3">
                <h3 class="font-weight-bold h4 h3-md h1-xl mb-4 pb-1">Instructions</h3>

                <div class="instruction-grid">
                    <?php 
                    $step_words = array(
                        1  => 'One',
                        2  => 'Two',
                        3  => 'Three',
                        4  => 'Four',
                        5  => 'Five',
                        6  => 'Six',
                        7  => 'Seven',
                        8  => 'Eight',
                        9  => 'Nine',
                        10 => 'Ten',
                    );
                    foreach( $args->getSinglePostMetaByKey('recipe_steps') as $i => $step ): 
                        $step_number = $i+1;
                        ?>
                        <div class="single-instruction">
                            <img loading="lazy" class="img-fluid" alt="image of current step being described in text below" src="<?php echo wp_get_attachment_url( $step['image'] );?>">
                            <div class="step-num-wrap">
                                <div class="step-number">
                                    <p><?php echo $step_number;?></p>
                                </div>
                                <h4>Step <?php echo $step_words[$step_number];?></h4>
                            </div>

                            <p class="instruction-text"><?php echo $step['description'];?></p>
                        </div> <!-- single-instruction -->
                    <?php endforeach;?>
                </div> <!-- instruction-grid -->
            </div> <!-- recipe-instructions -->

            <div class="w-100 d-flex justify-content-center">
                <!-- link to download recipe pdf -->
                <a class="download-recipe d-flex align-items-center btn btn-secondary text-white font-weight-semibold mx-auto mb-3 my-md-4 h8 h6-md h5-xl" target="_blank" href="<?php echo $args->getSinglePostMetaByKey('recipe_downloadable-version');?>">Download Printable Recipe</a>
            </div>

        </div> <!-- recipeCollapse -->

        <div class="w-100 d-flex" id="recipeHeading">
            <button class="view-full-recipe btn btn-secondary text-white w-100 h8 h6-md h5-xl mx-auto mb-4 mb-xl-5 py-1 py-md-2" id="viewFullRecipeBtn" type="button" data-toggle="collapse" data-target="#recipeCollapse" aria-expanded="false" aria-controls="#recipeCollapse">View Full Recipe</button>
        </div>
    </div> <!-- recipe-accordion -->

    <button class="recipe-view-less btn text-center text-dark bg-gray-light py-2 h7 h6-md h5-xl mt-4 d-none" id="viewLessRecipeBtn" type="button" data-toggle="collapse" data-target="#recipeCollapse" aria-expanded="true" aria-controls="#recipeCollapse">
        View less <span class="h9 h7-xl ml-1 align-middle"><i class="fas fa-minus"></i></span>
    </button>
</section>