<?php
/**
 * Template part for displaying snacks in a block
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
?>

<form class="ajax_form" id="review_form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
    <div>
        <div>
            <label for="review_text">Leave a review...</label>
        </div>
        <div>
            <textarea name="review_text"></textarea>
        </div>
    </div>
    <div>
        <div>
            <label for="review_rating">Leave a rating...</label>
        </div>
        <div>
            <select name="review_rating">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
    </div>
    <div>
        <button id="submit_review" class="btn btn-primary">Submit Review</button>
    </div>
    <input type="hidden" name="post_id" value="<?php echo get_the_ID();?>"/>
    <input type="hidden" name="user_id" value="<?php echo get_current_user_id();?>"/>
    <input type="hidden" name="action" value="add_review" />
</form>
