<?php
/**
 * ------------------------------------------------------------------------
 * Site's Archive Pages
 * ------------------------------------------------------------------------
 * This file is for helper functions used in archive pages. Currently, the
 * theme uses index.php for the archive pages.
 *
 * @package NeonSunset
 */

/**
 * Helper function to get category ID by $page_slug that is the same as the category slug.
 *
 * @param string $page_slug The slug of the page you want to get the category ID of.
 * @return category ID
 */
function ns_get_category_id_by_slug( $page_slug ) {
	$category = get_category_by_slug( $page_slug );

	return get_cat_ID( $category->name );
}

/**
 * Helper class for getting the link to the category page by passing the $category_slug.
 *
 * @param string $category_slug The slug of the category you want to get the URL of.
 * @return the URL of the category link
 */
function ns_get_category_link( $category_slug ) {
	$category = get_category_by_slug( $category_slug );

	if ( ! empty( $category ) ) :
		return esc_url( get_category_link( $category->term_id ) );
	else :
		return esc_attr( 'The category slug does not exist' );
	endif;
}
