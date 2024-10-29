<section class="activities mx-2 mx-lg-0 pb-4 pb-md-0">
    <a class="activity-link" href="#snackPoll">
        <h4>Rate my<br>Snacks</h4>
        <img class="img-fluid" alt="an orange emoji smiling sticking its tongue out overlapping with a blue emoji frowning surrounded by 3 orange stars" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/unboxing-rate-snacks.png">
    </a>

    <!-- link to coloring page pdf -->
    <a class="activity-link" target="_blank" href="<?php echo $args->getSinglePostMetaByKey('coloring-pages');?>">
        <h4><?php the_title();?><br>Coloring Pages</h4>
        <img class="img-fluid" alt="pink, orange, blue, and green crayons pointing diagonally down and to the left on top of a blue circle in the background" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/unboxing-coloring.png">
    </a>

    <!-- link to spotify playlist -->
    <a class="activity-link" target="_blank" href="<?php echo $args->getSinglePostMetaByKey('playlist');?>">
        <h4><?php the_title();?><br>Spotify Playlist</h4>
        <img class="img-fluid" alt="a blue version of the Spotify logo with pink and orange music notes in the bottom left corner" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/unboxing-playlist.png">
    </a>

    <!-- link to word search pdf -->
    <a class="activity-link" target="_blank" href="<?php echo $args->getSinglePostMetaByKey('word-search');?>">
        <h4><?php the_title();?><br>Word Search</h4>
        <img class="img-fluid" alt="an orange wordsearch letter grid with a magnifying glass in the bottom left corner" src="/wp-content/themes/wp-bootstrap-starter-child/assets/images/unboxing-wordsearch.png">
    </a>
</section>