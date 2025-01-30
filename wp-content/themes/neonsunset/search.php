<?php
/**
 * ------------------------------------------------------------------------
 * Search Page Template
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

get_header();
?>
	<form id="search" class="search-bar" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		<div class="wrapper">
			<input type="search" class="input-text" name="s" placeholder="<?php echo ( ! empty( get_search_query() ) ? get_search_query() : 'Search for a location' ); ?>" autocomplete="off">
		</div>
	</form>
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
