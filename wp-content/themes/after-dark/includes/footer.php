<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Footer
 * ------------------------------------------------------------------------
 * This file is for footer related functions
 *
 * @package AfterDark
 */

/**
 * Register the footer's navigation menu.
 */
function ad_add_footer_navigation() {
	register_nav_menu( 'footer-menu', __( 'Footer' ) );
}

/**
 * Helper function for creating an instance of the footer menu.
 */
function ad_create_footer_menu() {
	return array(
		'theme_location' => 'footer-menu',
		'menu_class'     => 'nav',
		'container'      => null,
		'items_wrap'     => '<ul id="%1$s" class="%2$s list-links">%3$s</ul>',
	);
}
