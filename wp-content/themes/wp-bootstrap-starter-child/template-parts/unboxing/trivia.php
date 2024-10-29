<section class="trivia mx-2 ml-lg-0 mb-4 mb-md-0">
    <div class="trivia-pagination d-flex justify-content-center align-items-center"></div>
    <div class="trivia-swiper">
        <h3><?php the_title();?> Trivia</h3>
        <div class="swiper-wrapper">
            <?php foreach( $args->getSinglePostMetaByKey('trivia_questions') as $i => $question ) : ?>
                <div class="swiper-slide trivia-q">
                    <h4><?php echo $question['question'];?></h4>
                    <?php if( $previous_answer = get_user_meta( get_current_user_id() , "trivia_" . $args->getId() . "_{$i}", true ) ) :
                        $percentages = $args->calculateAnswerPercentages($i);
                        ?>
                        <div class="answers">
                            <?php foreach( array('a','b','c','d') as $answer ) :
                                $answer_class = $args->getAnswerClass( $answer, $question['correct-answer'], $previous_answer );
                            ?>
                                <button type="button" class="single-answer<?php echo $answer_class;?>">
                                    <p class="answer-letter"><?php echo strtoupper($answer);?></p>
                                    <p class="answer-text"><?php echo $question["answer-{$answer}"];?></p>
                                    <div class="user-percentage d-block<?php echo $answer_class;?>" style="width:<?php echo $percentages[$answer];?>%"></div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <form class="ajax_form w-100" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST" id="triviaQuestion<?php echo $i;?>">
                            <div class="answers">
                                <?php foreach( array('a','b','c','d') as $answer ) :?>
                                    <button type="button" class="single-answer<?php echo $question['correct-answer'] == $answer ? ' correct' : '';?>" data-answer="<?php echo $answer;?>">
                                        <p class="answer-letter"><?php echo strtoupper($answer);?></p>
                                        <p class="answer-text"><?php echo $question["answer-{$answer}"];?></p>
                                        <div class="user-percentage d-none"></div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="post_id" value="<?php echo $args->getId();?>" />
                            <input type="hidden" name="question_key" value="<?php echo $i;?>" />
                            <input type="hidden" name="answer" value="" />
                            <input type="hidden" name="action" value="answered-trivia" />
                        </form>
                    <?php endif; ?>
                    <?php if ($i === 0): ?>
                    <button class="next-question">Next Question</button>
                    <?php elseif ($i > 0 && $i < 4): ?>
                    <div class="controls-wrap">
                        <button class="prev-question">Back</button>
                        <button class="next-question">Next Question</button>
                    </div>
                    <?php elseif ($i === 4): ?>
                    <button class="prev-question">Back</button>
                    <?php endif; ?>
                </div> <!-- trivia-q -->
            <?php endforeach;?>
        </div> <!-- swiper-wrapper -->
    </div> <!-- trivia-swiper -->
</section>