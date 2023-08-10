<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Sidebar
 * ------------------------------------------------------------------------
 * This file is for customization functions that will be used on the
 * sidebar bar of the theme.
 *
 * @package AfterDark
 */

/**
 * Register two sidebars one for posts and the other for guides.
 */
function ad_add_widget_support() {
	// Widget area for related guides.
	register_sidebar(
		array(
			'name'          => __( 'Guides Sidebar', 'custom-theme' ),
			'id'            => 'guides-sidebar',
			'description'   => __( 'Widget area for displaying related guides.', 'custom-theme' ),
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Widget area for default.
	register_sidebar(
		array(
			'name'          => __( 'Default Sidebar', 'custom-theme' ),
			'id'            => 'default-sidebar',
			'description'   => __( 'Default widget area for other cases.', 'custom-theme' ),
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
