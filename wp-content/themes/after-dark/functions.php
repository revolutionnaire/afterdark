<?php
// Add default styles and necessary JavaScript for all the pages
function aftedark_add_styles_and_scripts() {
  // Use Inter from Google Fonts
  wp_enqueue_style( 'google-fonts', "https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap");
  // Navigation bar related JavaScript
  wp_enqueue_script('afterdark-navigation', get_template_directory_uri() . '/assets/js/navigation.js');
}

// Hook to the scripts and style enqueueing
add_action('wp_enqueue_scripts', 'aftedark_add_styles_and_scripts');

// Support featured images for posts
add_theme_support('post-thumbnails');

// Add a custom class to manipulate class names in wp_nav_menu to use names instead of numbers
class After_Dark_Walker_Nav_Menu extends Walker_Nav_Menu {
  // Override the start_el method
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_classes = $item->classes;
    $item_title = strtolower($item->title);
    $item_classes[] = 'menu-item-' . $item_title;
    $classes = empty($item_classes) ? array() : (array) $item_classes;

    $output .= '<li class="' . implode(' ', $classes) . '">';

    $attributes = '';

    if (!empty($item->attr_title)) :
      $attributes .= ' title="' . esc_attr($item->attr_title) . '"';
    endif;

    if (!empty($item->target)) :
      $attributes .= ' target="' . esc_attr($item->target) . '"';
    endif;

    if (!empty($item->xfn)) :
      $attributes .= ' rel="' . esc_attr($item->xfn) . '"';
    endif;

    if (!empty($item->url)) :
      $attributes .= ' href="' . esc_attr($item->url) . '"';
    endif;

    $item_output = $args->before;
    $item_output .= '<a' . $attributes . '>';
    $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
  }
}

// Register the custom walker for wp_nav_menu
function afterdark_nav_menu_walker($args) {
  return array_merge($args, array(
  'walker' => new After_Dark_Walker_Nav_Menu(),
  ));
}

// Customize the arguments passed to wp_nav_menu
add_filter('wp_nav_menu_args', 'afterdark_nav_menu_walker');

// Register the header's navigation menu
function afterdark_add_main_navigation() {
  register_nav_menu('main-menu',__( 'Main' ));
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'afterdark_add_main_navigation' );

// Register the footer's navigation menu
function afterdark_add_footer_navigation() {
  register_nav_menu('footer-menu',__( 'Footer' ));
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'afterdark_add_footer_navigation' );
