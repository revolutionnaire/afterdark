<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Header
 * ------------------------------------------------------------------------
 * This file is for helper and customization functions that will be used
 * on the navigation bar of the theme.
 *
 * @package NeonSunset
 */

/**
 * Register the custom walker for navigation menu.
 *
 * @param array $args The default arguments for the navigation menu.
 * @return array
 */
function ns_nav_menu_walker( $args ) {
	return array_merge(
		$args,
		array(
			'walker' => new NS_Walker_Nav_Menu(),
		)
	);
}

/**
 * Register the header's navigation menu.
 */
function ns_add_main_navigation() {
	register_nav_menu( 'main-menu', __( 'Main' ) );
}

/**
 * Helper function for creating an instance of the main menu.
 *
 * @return array
 */
function ns_create_main_menu() {
	// Pass the array to load main menu.
	return array(
		'theme_location' => 'main-menu',
		'menu_id'        => null,
		'container'      => 'nav',
		'items_wrap'      => '%3$s',
		'link_before'    => '<h3>',
		'link_after'     => '</h3>',
		'walker'         => new NS_Walker_Nav_Menu(),
	);
}
