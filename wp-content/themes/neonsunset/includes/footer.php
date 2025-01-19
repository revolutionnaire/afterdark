<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Footer
 * ------------------------------------------------------------------------
 * This file is for footer related functions
 *
 * @package NeonSunset
 */

/**
 * Register the footer's navigation menu.
 */
function ns_add_footer_navigation() {
	register_nav_menu( 'footer-menu', __( 'Footer' ) );
}

/**
 * Helper function for creating an instance of the footer menu.
 */
function ns_create_footer_menu() {
	return array(
		'theme_location' => 'footer-menu',
		'menu_class'     => 'nav',
		'container'      => null,
		'items_wrap'     => '%3$s',
	);
}
