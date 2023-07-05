<?php
// Enqueue scripts and styles
function gp_scripts_and_styles() { // Enqueue media editor
  $screen = get_current_screen();

  // Check if it's any of the Category admin pages or the one for widgets
  if ( $screen->id === 'edit-category' || $screen->id === 'category' ) :
    // Enqueue media editor
    wp_enqueue_media();

    // Enqueue category featured image JavaScript
    wp_enqueue_script( 'category-featured-image', GUIDE_POSTS__PLUGIN_URL . 'assets/js/category-featured-image.js', array(), '1.0.0', true );

    // Enqueue category featured image styles
    wp_enqueue_style( 'featured-image-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/category-featured-image.css');
  elseif ( $screen->id === 'widgets' ) :
    // Load widget styles
    wp_enqueue_style( 'widget-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/widget-style.css' );
  endif;
}

// Hook the function to the action for all the admin pages
add_action( 'admin_enqueue_scripts', 'gp_scripts_and_styles' );

// Enqueue block editor assets
function gp_block_editor_assets() {
  // Check if it's a Guide custom post
  if ( get_post_type() === 'guide' ) :
    // Enqueue media editor
    wp_enqueue_media();

    // Enqueue the Guide post location editor JavaScript file
    wp_enqueue_script( 'guide-posts', GUIDE_POSTS__PLUGIN_URL . 'assets/js/guide-locations.js', array(), '1.0', true );

    // Enqueue location form styles
    wp_enqueue_style( 'location-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/guide-locations.css');

    // Add inline script to pass the nonce value
    wp_add_inline_script( 'guide-posts', '
      var guidePostsREST = {
        restURL: "' . esc_js( rest_url( 'guide-posts/v1' ) ) . '",
        nonce: "' . esc_js( wp_create_nonce( 'wp_rest' ) ) . '"
      }'
    );
  endif;
}

// Hook the function to the action after block assets have been enqueued for the editing interface
add_action( 'enqueue_block_editor_assets', 'gp_block_editor_assets' );
