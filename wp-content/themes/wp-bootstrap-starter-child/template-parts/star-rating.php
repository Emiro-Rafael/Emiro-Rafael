<?php
// pass in rating
$rating = $args;
?>
<div class="review-stars">
<?php 
for( $i = 1; $i <= 5; $i++ )
{
    if($rating >= $i)
    {
        get_template_part( 'template-parts/full-star-svg', get_post_format() );
    }
    elseif($rating >= $i - 0.75)
    {
        get_template_part( 'template-parts/half-star-svg', get_post_format() );
    }
    else
    {
        get_template_part( 'template-parts/empty-star-svg', get_post_format() );
    }
}
?>
</div>