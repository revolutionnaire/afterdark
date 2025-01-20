<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Body Element
 * ------------------------------------------------------------------------
 * This file is for customization functions for the body element
 *
 * @package NeonSunset
 */

/**
 * Filter to replace post ID with page slugs in the classes added to the body element.
 *
 * @param string $classes The string of the class attribute.
 * @return string
 */
function ns_body_class( $classes ) {
	// Remove existing page or post ID class.
	$classes = array_filter(
		$classes,
		function( $class ) {
			return ! preg_match( '/^(page-id|postid)-\d+$/i', $class );
		}
	);

	// Get the current post or page.
	$post = get_queried_object();

	if ( $post instanceof WP_Post ) {
		// Check if it's a page or post.
		$slug_class  = 'page' === $post->post_type ? 'page-' : 'post-';
		$slug_class .= $post->post_name;

		// Add the post or page slug as a class with the appropriate prefix.
		$classes[] = $slug_class;
	}

	return $classes;
}
