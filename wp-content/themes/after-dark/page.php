<?php
/**
 * ------------------------------------------------------------------------
 * Page Template
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
		<article class="wrapper">
			<header>
				<h1><?php the_title(); ?></h1>
			</header>
			<section>
				<?php the_content(); ?>
			</section>
		</article>
				<?php
			endwhile;
		else :
			?>
		<article class="wrapper">
			<h2>Sorry, the page wasn't found!</h2>
		</article>
		<?php endif; ?>
	</main>
<?php get_footer(); ?>
