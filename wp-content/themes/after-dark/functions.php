<?php
// Add default styles and necessary JavaScript for all the pages
function aftedark_add_styles_and_scripts() {
  // Use Inter from Google Fonts
  wp_enqueue_style( 'google-fonts', "https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap" );

  // Navigation bar related JavaScript
  wp_enqueue_script( 'afterdark-navigation' , get_template_directory_uri() . '/assets/js/navigation.js' );
}

// Hook to the scripts and style enqueueing
add_action( 'wp_enqueue_scripts', 'aftedark_add_styles_and_scripts' );

// Support featured images for posts
add_theme_support( 'post-thumbnails' );

// Add a custom class to manipulate class names in wp_nav_menu to use names instead of numbers
class After_Dark_Walker_Nav_Menu extends Walker_Nav_Menu {
  // Override the start_el method
  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $item_classes = $item->classes;
    $item_title = strtolower( $item->title );
    $item_classes[] = 'menu-item-' . $item_title;
    $classes = empty( $item_classes ) ? array() : ( array ) $item_classes;

    $output .= '<li class="' . implode( ' ', $classes ) . '">';

    $attributes = '';

    if (!empty($item->attr_title ) ) :
      $attributes .= ' title="' . esc_attr( $item->attr_title ) . '"';
    endif;

    if (!empty( $item->target ) ) :
      $attributes .= ' target="' . esc_attr( $item->target ) . '"';
    endif;

    if ( !empty( $item->xfn ) ) :
      $attributes .= ' rel="' . esc_attr( $item->xfn ) . '"';
    endif;

    if ( !empty( $item->url ) ) :
      $attributes .= ' href="' . esc_attr( $item->url ) . '"';
    endif;

    $item_output = $args->before;
    $item_output .= '<a' . $attributes . '>';
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}

// Register the custom walker for wp_nav_menu
function afterdark_nav_menu_walker( $args ) {
  return array_merge( $args, array(
  'walker' => new After_Dark_Walker_Nav_Menu(),
  ));
}

// Customize the arguments passed to wp_nav_menu
add_filter( 'wp_nav_menu_args', 'afterdark_nav_menu_walker' );

// Register the header's navigation menu
function afterdark_add_main_navigation() {
  register_nav_menu( 'main-menu',__( 'Main' ) );
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'afterdark_add_main_navigation' );

// Add category breadcrumbs after the content
function afterdark_categories_as_breadcrumbs( $id, $wrapper = false ) {
  // Check if ID is post ID or category ID
  if ( !empty( get_the_category( $id ) ) ) :
    $categories = get_the_category( $id );
  else:
    $categories = array( get_category( $id ) );
  endif;

  // Check if there are any categories
  if ( ! empty( $categories ) ) :
    echo '<nav id="breadcrumbs">';
    if ( $wrapper === true) :
      echo '<div class="wrapper">';
    endif;
    echo '<ul class="breadcrumbs">';
    foreach ( $categories as $category ) :
      $ancestors = get_ancestors( $category->term_id, 'category' );
      $ancestors = array_reverse( $ancestors );

      // Output the ancestor categories as roots
      foreach ( $ancestors as $ancestor ) :
        $taxonomy = get_category( $ancestor );
        // Check if the parent category has a page that displays child categories
        $link = ( ! empty( get_permalink( get_page_by_path( $taxonomy->slug ) ) ) ) ? get_permalink( get_page_by_path( $taxonomy->slug ) ) : get_category_link( $taxonomy->term_id );
        echo '<li class="breadcrumbs-ancestor-category"><a href="' . esc_url( $link ) . '">' . esc_html( $taxonomy->name ) . '</a></li>';
      endforeach;

      // Output the current category as a link if it's a child
      if ( ! empty( $ancestors )  && is_single () ) :
        echo '<li class="breadcrumbs-child-category"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></li>';
      elseif ( ! empty( $ancestors ) ) :
        echo '<li class="breadcrumbs-child-category">' . esc_html( $category->name ) . '</a></li>';
      endif;
    endforeach;
    echo '</ul>';
    if ( $wrapper === true) :
      echo '</div>';
    endif;
    echo '</nav>';
  endif;
}

// Register two sidebars one for posts and the other for guides
function afterdark_add_widget_support() {
  // Widget area for related guides
  register_sidebar( array(
    'name' => __( 'Guides Sidebar', 'custom-theme' ),
    'id' => 'guides-sidebar',
    'description' => __( 'Widget area for displaying related guides.', 'custom-theme' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );

  // Widget area for default
  register_sidebar( array(
      'name' => __( 'Default Sidebar', 'custom-theme' ),
      'id' => 'default-sidebar',
      'description' => __( 'Default widget area for other cases.', 'custom-theme' ),
      'before_widget' => '<div class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h3 class="widget-title">',
      'after_title' => '</h3>',
  ) );
}

// Hook the widget initiation and run our function
add_action( 'widgets_init', 'afterdark_add_widget_support' );

// Register the footer's navigation menu
function afterdark_add_footer_navigation() {
  register_nav_menu( 'footer-menu',__( 'Footer' ) );
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'afterdark_add_footer_navigation' );
