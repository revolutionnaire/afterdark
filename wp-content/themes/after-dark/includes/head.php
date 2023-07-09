<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Head Element
 * ------------------------------------------------------------------------
 * This file is for helper functions that will be used on the head
 * element.
 */

// Add default styles and necessary JavaScript for all the pages
function ad_add_styles_and_scripts() {
  // Use Inter from Google Fonts
  wp_enqueue_style( 'google-fonts', "https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap" );

  // Load the theme's style last
  wp_enqueue_style( 'afterdark-style', get_stylesheet_uri(), array(), null, 'all' );

  // Load main JavaScript file
  wp_enqueue_script( 'afterdark-script' , get_template_directory_uri() . '/main.js' );
}

// Helper function for SEO description
function ad_seo_description() {
  global $post;

  // Check if it has an excerpt and pass the blog description if not
  $content = ( !empty( get_the_excerpt( $post ) ) ) ? get_the_excerpt( $post ) : bloginfo( 'description' );

  return $content;
}

// Helper function for SEO image
function ad_seo_image() {
  global $post;

  // Check if the post or page has a featured image and if not pass the default image
  $content = ( !empty( get_the_post_thumbnail_url( $post ) ) ) ? get_the_post_thumbnail_url( $post ) : get_template_directory_uri() . '/assets/images/Default-SEO-Image.jpg';

  return $content;
}
