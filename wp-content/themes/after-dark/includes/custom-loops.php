<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Custom Loops
 * ------------------------------------------------------------------------
 * This file is for custom loops to fetch posts and child categories
 */

// Helper function to get the  home page loop
function ad_home_page_loop() {
  $posts_count = get_option( 'posts_per_page' );
  $args = array(
    'post_type' => 'guide', // Set the post type to 'guide'
    'posts_per_page' => $post_count, // Display 7 posts (must always be an odd number to evenly distribute the posts)
    'orderby' => 'date', // Order posts by date
    'order'   => 'DESC', // Display posts in descending order (newest to oldest)
  );

  return new WP_Query( $args );
}

// Helper function to get all posts by category
function ad_get_posts_by_category( $category_slug, $number_of_posts = 3 ) {
  $category_posts = new WP_Query(array(
    'category_name' => $category_slug,
    'posts_per_page' => $number_of_posts
  ));

  if ( ! empty( $category_posts ) ) :
    return $category_posts;
  else :
    return 'Category slug does not exits';
  endif;
}

// Helper function to accept a page and returns the child categories based on the page name
function ad_get_child_categories( $current_page ) {
  // Assign the page slug as a category
  $parent_category = get_category_by_slug( $current_page->post_name );

  // Get child categories
  $child_categories = get_terms( array(
    'taxonomy' => 'category',
    'child_of' => $parent_category->term_id,
    'meta_key' => 'category_thumbnail',
  ));

  return $child_categories;
}
