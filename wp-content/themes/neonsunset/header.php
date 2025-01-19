<?php
/**
 * ------------------------------------------------------------------------
 * Theme Header
 * ------------------------------------------------------------------------
 *
 * @package NeonSunset
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php bloginfo( 'name' ); ?> &#8250; <?php is_front_page() ? bloginfo( 'description' ) : wp_title( '' ); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="robots" content="<?php echo is_search() ? 'noindex' : 'index'; ?>">
	<meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
	<meta property="og:title" content="<?php the_title(); ?>">
	<meta property="og:type" content="<?php echo is_singular( 'page' ) ? esc_attr( 'website' ) : esc_attr( 'article' ); ?>">
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
	<meta property="og:image" content="<?php echo esc_url( ns_seo_image() ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( ns_seo_description() ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php the_title(); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( ns_seo_description() ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( ns_seo_image() ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="canonical" href="<?php echo esc_url( get_permalink() ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="navigation">
		<div class="background"></div>
		<div class="wrapper">
			<header>
				<?php if ( is_front_page() ) : ?>
					<h1>
						<?php get_template_part( 'parts/logo', 'logo' ); ?>
					</h1>
				<?php else : ?>
					<h2>
						<?php get_template_part( 'parts/logo', 'logo' ); ?>
					</h2>
				<?php endif; ?>
			</header>
			<form id="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<input type="search" class="input-text" name="s" placeholder="<?php echo ( ! empty( get_search_query() ) ? get_search_query() : 'Search for a neighborhood or city' ); ?>" autocomplete="off">
			</form>
			<?php
			if ( has_nav_menu( 'main-menu' ) ) :
				wp_nav_menu( ns_create_main_menu() );
			endif;
			?>
			<?xml version="1.0" ?>
			<svg id="down" height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"><path d="M14.83 16.42l9.17 9.17 9.17-9.17 2.83 2.83-12 12-12-12z"/></svg>
		</div>
	</div>
