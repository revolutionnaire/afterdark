<?php
/**
 * ------------------------------------------------------------------------
 * Post Template
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
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
		<?php ( has_post_thumbnail( $post->ID ) ) ? get_template_part( 'parts/image-card', 'image-card' ) : null;  ?>
		<div class="wrapper">
			<section id="content">
				<h1><?php the_title(); ?></h1>
				<ul class="byline">
					<li>Written by <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></li>
					<li>Published on <?php the_date(); ?></li>
				</ul>
				<?php the_content(); ?>
			</section>
			<?php get_sidebar(); ?>
			<footer>
			<?php ns_social_media_links( $post ); ?>
			<?php ns_categories_as_breadcrumbs( $post->ID ); ?>
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
