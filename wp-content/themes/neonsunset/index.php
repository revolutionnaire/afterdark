<?php
/**
 * ------------------------------------------------------------------------
 * Author Template
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

?>
<?php get_header(); ?>
<?php
if ( is_category() ) :
	ns_categories_as_breadcrumbs( $cat, true );
endif;
?>
	<main class="wrapper">
		<div class="content-card-wrapper">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'parts/content-card', 'content-card' );
			endwhile;
		else :
			?>
			<article class="content-card-message">
				<h2>Sorry, no posts were found!</h2>
			</article>
		<?php endif; ?>
		</div>
	</main>
<?php get_footer(); ?>
