<?php
/**
 * ------------------------------------------------------------------------
 * Home Page
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

get_header();
?>
	<main>
	<?php
	$loop = ns_home_page_loop();
	if ( $loop->have_posts() ) :
		$loop->the_post();
		get_template_part( 'parts/content-card', 'content-card', array( 'featured-article' => true ) );
		?>
		<div class="wrapper content-card-wrapper">
			<?php
			while ( $loop->have_posts() ) :
				$loop->the_post();
				get_template_part( 'parts/content-card', 'content-card' );
			endwhile;
			?>
		</div>
	<?php else : ?>
		<article class="wrapper">
			<h2>Sorry, no posts were found!</h2>
		</article>
		<?php
	endif;
	wp_reset_postdata();
	?>
	</main>
<?php get_footer(); ?>
