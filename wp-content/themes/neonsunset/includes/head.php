<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Head Element
 * ------------------------------------------------------------------------
 * This file is for helper functions that will be used on the head
 * element.
 *
 * @package NeonSunset
 */

/**
 * Add default styles and necessary JavaScript for all the pages.
 */
function ns_add_styles_and_scripts() {
	// Use Inter from Google Fonts.
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap' );
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Oi&display=swap' );

	// Load the theme's style.
	wp_enqueue_style( 'afterdark-style', get_stylesheet_uri(), array(), '1.0', 'screen' );

	// Load the editor's style last.
	wp_enqueue_style( 'editor-style', get_template_directory_uri() . '/editor-style.css', array(), false, 'screen' );

	// Load main JavaScript file.
	wp_enqueue_script(
		'afterdark-main-script',
		get_template_directory_uri() . '/assets/js/global.js',
		array(),
		false,
		array(
			'strategy'  => 'defer',
			'in_footer' => false,
		)
	);
}

/**
 * Load theme's editor style for the classic editor.
 */
function ns_custom_editor_styles() {
	// Use Inter from Google Fonts on the classic editor.
	add_editor_style( 'https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap' );

	// Load theme's editor styles.
	add_editor_style( 'editor-style.css' );
}

/**
 * Load theme's editor style for the block editor.
 */
function ns_block_editor_styles() {
	// Use Inter from Google Fonts on the block editor.
	wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap' );

	// Load theme's editor styles.
	wp_enqueue_style( 'editor-style', get_theme_file_uri( 'editor-style.css' ), array(), false, 'screen' );
}

/**
 * Helper function for SEO description.
 *
 * @return string
 */
function ns_seo_description() {
	global $post;

	// Check if it has an excerpt and pass the blog description if not.
	$content = ! empty( get_the_excerpt( $post ) ) ? get_the_excerpt( $post ) : bloginfo( 'description' );

	return $content;
}

/**
 * Helper function for SEO image.
 *
 * @return string
 */
function ns_seo_image() {
	global $post;

	// Check if the post or page has a featured image and if not pass the default image.
	$content = ! empty( get_the_post_thumbnail_url( $post ) ) ? get_the_post_thumbnail_url( $post ) : get_template_directory_uri() . '/assets/images/Default-SEO-Image.jpg';

	return $content;
}
