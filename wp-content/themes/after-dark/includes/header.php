<?php
/**
 * ------------------------------------------------------------------------
 * Theme's Header
 * ------------------------------------------------------------------------
 * This file is for helper and customization functions that will be used
 * on the navigation bar of the theme.
 */

// Register the custom walker for navigation menu
function ad_nav_menu_walker( $args ) {
  return array_merge( $args, array(
    'walker' => new AD_Walker_Nav_Menu(),
  ));
}

// Register the header's navigation menu
function ad_add_main_navigation() {
  register_nav_menu( 'main-menu',__( 'Main' ) );
}

// Helper function for creating an instance of the main menu
function ad_create_main_menu() {
  // Pass the array to load main menu
  return array (
    'theme_location' => 'main-menu',
    'menu_class' => 'nav-list',
    'menu_id' => null,
    'container' => 'nav',
    'link_before' => '<h2>',
    'link_after' => '</h2>',
    'walker' => new AD_Walker_Nav_Menu()
  );
}
