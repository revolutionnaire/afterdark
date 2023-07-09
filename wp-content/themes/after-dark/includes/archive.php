<?php
/**
 * ------------------------------------------------------------------------
 * Site's Archive Pages
 * ------------------------------------------------------------------------
 * This file is for helper classes used in archive pages. Currently, the
 * theme uses index.php for the archive pages.
 */

// Helper function to get category id by page slug that is the same as the  category slug
function ad_get_category_id_by_slug( $page_slug ) {
  $category = get_category_by_slug( $page_slug );

  return get_cat_ID( $category->name );
}

// Helper class for getting the link to the category page by passing the category slug
function ad_get_category_link( $category_slug ) {
  $category = get_category_by_slug( $category_slug );

  if ( ! empty( $category ) ):
    return get_category_link( $category->term_id );
  else :
    return 'The category slug does not exist';
  endif;
}
