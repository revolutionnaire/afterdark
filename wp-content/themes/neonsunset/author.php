<?php
/**
 * ------------------------------------------------------------------------
 * Author Template
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

get_header();
?>
	<main class="wrapper content-card-wrapper">
	<?php if ( have_posts() ) : the_post(); ?>
		<div class="author-card text-center">
		<?php echo get_avatar( get_the_author_meta( 'ID' ) ); ?>
			<h1><?php  the_author_meta( 'display_name' ); ?></h1>
			<h4><small><?php esc_html( ns_the_author_role() ); ?></small></h4>
		<?php if ( get_the_author_meta( 'description' ) !== '' ) : ?>
			<p><?php esc_html( the_author_meta( 'description' ) ); ?></p>
		<?php endif; ?>
		<?php if ( get_the_author_meta( 'user_url' ) !== '' ) : ?>
			<a class="button button-foreground" href="<?php esc_url( the_author_meta( 'user_url' ) ); ?>">Website &#8250;</a>
		<?php endif; ?>
		</div>
	<?php rewind_posts(); while ( have_posts() ) : the_post();
		if ( $post->post_type === 'guide' )
			get_template_part( 'parts/content-card', 'content-card' );
		endwhile;
	else :
	?>
		<article class="content-card-message">
			<h2>Sorry, no posts were found!</h2>
		</article>
	<?php endif; ?>
	</main>
<?php get_footer(); ?>
