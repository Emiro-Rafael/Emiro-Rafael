<?php 
/*
Template Name: Legal Doc
*/

get_header(); 
?>
 
<section id="primary" class="content-area">
	<div class="legal-doc text-primary container mx-auto">

		<div class="inner-content px-3 px-xl-0 pb-5">
			<h1 class="display-5-lg my-4 mt-lg-5"><strong><?php echo the_title() ?></strong></h1>
				<?php
					the_content(); 
				?>
			<span class="text-dark">
				&copy; <?php echo date('Y') ?> <?php echo bloginfo( 'name' ) ?>. All Rights Reserved.
			</span>
		</div>

	</div>
</section>

<?php get_footer(); ?>