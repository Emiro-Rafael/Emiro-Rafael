<?php
$user_choices = get_user_meta( get_current_user_id(), 'snack_superlatives_' . $args->getId(), true );
$popular_choices = $args->getMaxSuperlatives();

$user_choice_data = array(
    'best' => empty($user_choices['best']) ? null : $args->getSuperlativeSnackData( $user_choices['best'] ),
    'worst' => empty($user_choices['worst']) ? null : $args->getSuperlativeSnackData( $user_choices['worst'] ),
    'weird' => empty($user_choices['weird']) ? null : $args->getSuperlativeSnackData( $user_choices['weird'] ),
);

$popular_choice_data = array(
    'best' => empty($popular_choices['best']) ? null : $args->getSuperlativeSnackData( $popular_choices['best'] ),
    'worst' => empty($popular_choices['worst']) ? null : $args->getSuperlativeSnackData( $popular_choices['worst'] ),
    'weird' => empty($popular_choices['weird']) ? null : $args->getSuperlativeSnackData( $popular_choices['weird'] ),
);
?>
<section class="snack-poll<?php echo !User::checkHasSubscription() ? ' subscribers-only-blur' : '';?>" id="snackPoll">
    <div class="snack-poll-content">
        <div class="snack-poll-header">
            <h2 class="h4 h1-md display-4-xl text-center font-weight-bolder">What did you think?</h2>
            <p class="h6 h5-md h3-xl text-center">Let us know your favorites (and least favorites) so we can find the best snacks for you next time!</p>
        </div> <!-- snack-poll-header -->

        <div class="snack-poll-qs">
            <div class="poll-q-wrap">
                <div class="single-poll-q best-snack <?php echo !empty($user_choices['best']) ? 'd-none' : 'd-flex';?>">
                    <div class="q-header">
                        <h4>Best Snack?</h4>
                    </div>

                    <img class="img-fluid" alt="an orange emoji smiling sticking its tongue" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/best-snack-icon.png">

                    <div class="q-footer">
                        <button class="best-btn poll-modal-trigger" type="button" data-toggle="modal" data-target="#snackPollModal" >Select</button>
                    </div>
                </div> <!-- single-poll-q -->

                <div class="user-picks best-picks <?php echo empty($user_choices['best']) ? 'd-none' : 'd-flex';?>">
                    <!-- reopen modal is user wants to re-pick -->
                    <button type="button" data-toggle="modal" data-target="#snackPollModal" class="user-picks-header best-btn poll-modal-trigger">Best Snack</button>
                    <div class="snack-picks">
                        <div class="single-pick your-pick">
                            <div class="pick-header">
                                <h5>Your pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="best-user-image" class="img-fluid" alt="image of <?php echo $user_choice_data['best']['name'];?>" src="<?php echo $user_choice_data['best']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="best-user-name"><?php echo $user_choice_data['best']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($user_choice_data['best']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->

                        <div class="single-pick popular-pick">
                            <div class="pick-header">
                                <h5>Most popular pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="best-popular-image" class="img-fluid" alt="image of <?php echo $popular_choice_data['best']['name'];?>" src="<?php echo $popular_choice_data['best']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="best-popular-name"><?php echo $popular_choice_data['best']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($popular_choice_data['best']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->
                    </div> <!-- snack-picks -->
                </div> <!-- user-picks -->
            </div> <!-- poll-q-wrap -->

            <div class="poll-q-wrap">
                <div class="single-poll-q worst-snack <?php echo !empty($user_choices['worst']) ? 'd-none' : 'd-flex';?>">
                    <div class="q-header">
                        <h4>Worst Snack?</h4>
                    </div>

                    <img class="img-fluid" alt="a blue emoji frowning with its eyes squeezed shut" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/worst-snack-icon.png">

                    <div class="q-footer">
                        <button class="worst-btn poll-modal-trigger" type="button" data-toggle="modal" data-target="#snackPollModal">Select</button>
                    </div>
                </div> <!-- single-poll-q -->

                <div class="user-picks worst-picks <?php echo empty($user_choices['worst']) ? 'd-none' : 'd-flex';?>">
                    <!-- reopen modal is user wants to re-pick -->
                    <button type="button" data-toggle="modal" data-target="#snackPollModal" class="user-picks-header worst-btn poll-modal-trigger">Worst Snack</button>
                    <div class="snack-picks">
                        <div class="single-pick your-pick">
                            <div class="pick-header">
                                <h5>Your pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="worst-user-image" class="img-fluid" alt="image of <?php echo $user_choice_data['worst']['name'];?>" src="<?php echo $user_choice_data['worst']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="worst-user-name"><?php echo $user_choice_data['worst']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($user_choice_data['worst']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->

                        <div class="single-pick popular-pick">
                            <div class="pick-header">
                                <h5>Most popular pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="worst-popular-image" class="img-fluid" alt="image of <?php echo $popular_choice_data['worst']['name'];?>" src="<?php echo $popular_choice_data['worst']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="worst-popular-name"><?php echo $popular_choice_data['worst']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($popular_choice_data['worst']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->
                    </div> <!-- snack-picks -->
                </div> <!-- user-picks -->
            </div> <!-- poll-q-wrap -->

            <div class="poll-q-wrap">
                <div class="single-poll-q weird-snack <?php echo !empty($user_choices['weird']) ? 'd-none' : 'd-flex';?>">
                    <div class="q-header">
                        <h4>Weirdest Snack?</h4>
                    </div>

                    <img class="img-fluid" alt="a purple emoji looking surprised with dark purple swirls in its eyes" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/weirdest-snack-icon.png">

                    <div class="q-footer">
                        <button class="weird-btn poll-modal-trigger" type="button" data-toggle="modal" data-target="#snackPollModal">Select</button>
                    </div>
                </div> <!-- single-poll-q -->
                <div class="user-picks weird-picks <?php echo empty($user_choices['weird']) ? 'd-none' : 'd-flex';?>">
                    <!-- reopen modal is user wants to re-pick -->
                    <button type="button" data-toggle="modal" data-target="#snackPollModal" class="user-picks-header weird-btn poll-modal-trigger">Weirdest Snack</button>
                    <div class="snack-picks">
                        <div class="single-pick your-pick">
                            <div class="pick-header">
                                <h5>Your pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="weird-user-image" class="img-fluid" alt="image of <?php echo $user_choice_data['weird']['name'];?>" src="<?php echo $user_choice_data['weird']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="weird-user-name"><?php echo $user_choice_data['weird']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($user_choice_data['weird']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->

                        <div class="single-pick popular-pick">
                            <div class="pick-header">
                                <h5>Most popular pick</h5>
                            </div>

                            <div class="pick-img-wrap">
                                <img data-elementRole="weird-popular-image" class="img-fluid" alt="image of <?php echo $popular_choice_data['weird']['name'];?>" src="<?php echo $popular_choice_data['weird']['image'];?>">
                            </div>

                            <div class="mt-auto">
                                <h6 class="equalHeight" data-elementRole="weird-popular-name"><?php echo $popular_choice_data['weird']['name'];?></h6>

                                <p>Average rating</p>

                                <div class="pick-footer">
                                    <?php get_template_part( 'template-parts/star-rating', get_post_format(), ($popular_choice_data['weird']['rating'] ?? 0) ); ?>
                                </div>
                            </div>
                        </div> <!-- popular-pick -->
                    </div> <!-- snack-picks -->
                </div> <!-- user-picks -->
            </div> <!-- poll-q-wrap -->
        </div> <!-- snack-poll-qs -->
    </div> <!-- snack-poll-content -->
</section>