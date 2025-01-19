<?php
/**
 * ------------------------------------------------------------------------
 * About Page
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

get_header();
?>
<main>
	<article class="wrapper">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
		<header>
			<h3 class="text-center">Welcome</h3>
		</header>
		<section id="content">
			<?php the_content(); ?>
		</section>
		<aside id="sidebar" role="complementary">
			<?php dynamic_sidebar('about-sidebar'); ?>
		</aside>
	<?php endwhile; else : ?>
		<section class="wrapper">
			<h2>Sorry, the page wasn't found!</h2>
		</section>
	<?php endif; ?>
	</article>
</main>
<?php get_footer(); ?>
