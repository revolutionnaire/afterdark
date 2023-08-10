<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Breadcrumbs
 * ------------------------------------------------------------------------
 * This file is for breadcrumbs related functions
 *
 * @package AfterDark
 */

/**
 * Helper function that adds  categories as  breadcrumbs by the post's ID or the category ID on the category page.
 *
 * @param integer $id Either the post ID or category ID.
 * @param boolean $bar `true` if the breadcrumbs will be displayed in a bar. Default: `false`.
 */
function ad_categories_as_breadcrumbs( $id, $bar = false ) {
	// Check if ID is a post ID or a category ID.
	if ( ! empty( get_the_category( $id ) ) ) :
		$categories = get_the_category( $id );
	else :
		$categories = array( get_category( $id ) );
	endif;

	// Check if there are any categories.
	if ( ! empty( $categories ) ) :
		echo '<nav id="breadcrumbs"' . ( true === $bar ? ' class="breadcrumbs-bar' : '' ) . '">';
		if ( true === $bar ) :
			echo '<div class="wrapper">';
		endif;
		echo '<ul class="breadcrumbs">';

		// Filter out the category that doesn't have a child when it is a post.
		if ( is_single() ) :
			$categories = array_filter(
				$categories,
				function( $category ) {
					return ! empty( get_ancestors( $category->term_id, 'category' ) );
				}
			);
		endif;

		foreach ( $categories as $category ) :
			$ancestors = get_ancestors( $category->term_id, 'category' );
			$ancestors = array_reverse( $ancestors );

			if ( ! empty( $ancestors ) ) :
				// Output the ancestor categories as roots.
				foreach ( $ancestors as $ancestor ) :
					$taxonomy = get_category( $ancestor );
					// Check if the parent category has a page that displays child categories.
					$link = ( ! empty( get_permalink( get_page_by_path( $taxonomy->slug ) ) ) ) ? get_permalink( get_page_by_path( $taxonomy->slug ) ) : get_category_link( $taxonomy->term_id );
					echo '<li class="breadcrumbs-ancestor-category"><a href="' . esc_url( $link ) . '">' . esc_html( $taxonomy->name ) . '</a></li>';
				endforeach;
			else :
				if ( is_single() ) :
					echo '<li class="breadcrumbs-ancestor-category"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></li>';
				else :
					echo '<li class="breadcrumbs-ancestor-category text-emphasis">' . esc_html( $category->name ) . '</li>';
				endif;
			endif;

			// Output the current category as a link if it's a child.
			if ( ! empty( $ancestors ) && is_single() ) :
				echo '<li class="breadcrumbs-child-category"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></li>';
			elseif ( ! empty( $ancestors ) ) :
				echo '<li class="breadcrumbs-child-category">' . esc_html( $category->name ) . '</li>';
			endif;
		endforeach;
		echo '</ul>';
		if ( true === $bar ) :
			echo '</div>';
		endif;
		echo '</nav>';
	endif;
}
