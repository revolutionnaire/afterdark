<?php
/**
 * ------------------------------------------------------------------------
 * Post Template
 * ------------------------------------------------------------------------
 *
 * @package AfterDark
 */

get_header();
?>
<main>
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
	<article>
		<?php get_template_part( 'parts/title-card', 'title-card' ); ?>
		<div class="wrapper">
			<section id="content">
			<?php the_content(); ?>
			</section>
			<?php get_sidebar(); ?>
			<footer>
			<?php ad_social_media_links( $post ); ?>
			<?php ad_categories_as_breadcrumbs( $post->ID ); ?>
			</footer>
		</div>
	</article>
		<?php
	endwhile;
else :
	?>
	<article class="wrapper">
		<h2>Sorry, no post was found!</h2>
	</article>
<?php endif; ?>
</main>
<?php get_footer(); ?>
