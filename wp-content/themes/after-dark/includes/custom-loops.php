<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Custom Loops
 * ------------------------------------------------------------------------
 * This file is for custom loops to fetch posts and child categories
 *
 * @package AfterDark
 */

/**
 * Helper function to get the  home page loop.
 *
 * @return WP_Query
 */
function ad_home_page_loop() {
	$post_count = get_option( 'posts_per_page' );
	$args       = array(
		'post_type'      => 'guide', // Set the post type to 'guide'.
		'posts_per_page' => $post_count, // Use settings for home page loop.
		'orderby'        => 'date', // Order posts by date.
		'order'          => 'DESC', // Display posts in descending order (newest to oldest).
	);

	return new WP_Query( $args );
}

/**
 * Helper function to get all posts by category.
 *
 * @param string  $category_slug The slug of the category of the posts you want to retrieve.
 * @param integer $number_of_posts The number of how many posts you want to display. Default: 3.
 * @return WP_Query
 */
function ad_get_posts_by_category( $category_slug, $number_of_posts = 3 ) {
	$category_posts = new WP_Query(
		array(
			'category_name'  => $category_slug,
			'posts_per_page' => $number_of_posts,
		)
	);

	if ( ! empty( $category_posts ) ) :
		return $category_posts;
	else :
		return 'Category slug does not exits';
	endif;
}

/**
 * Helper function to accept a page and returns the child categories based on the page name.
 *
 * @param string $current_page The slug of the current page.
 * @return WP_Term
 */
function ad_get_child_categories( $current_page ) {
	// Assign the page slug as a category.
	$parent_category = get_category_by_slug( $current_page->post_name );

	// Get child categories.
	$child_categories = get_terms(
		array(
			'taxonomy' => 'category',
			'child_of' => $parent_category->term_id,
			'meta_key' => 'category_thumbnail',
		)
	);

	return $child_categories;
}
