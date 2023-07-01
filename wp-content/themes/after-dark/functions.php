<?php
// Include the custom walker class
require_once get_template_directory() . '/includes/nav-walker-menu.php';

// Replace the logo on the login page
function ad_replace_login_logo() {
    $logo = get_template_directory_uri() . '/assets/images/Logo-Stroked-Moon.svg';

    // Output the custom logo image
    echo '<style type="text/css">
        .login h1 a {
            background-image: url(' . esc_url( $logo ) . ') !important;
            background-size: contain !important;
            height: 100px !important;
            width: 100% !important;
        }
    </style>';
}

// Hook the function to the 'login_head' action
add_action( 'login_head', 'ad_replace_login_logo' );

// Add default styles and necessary JavaScript for all the pages
function ad_add_styles_and_scripts() {
  // Use Inter from Google Fonts
  wp_enqueue_style( 'google-fonts', "https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap" );

  // Load main JavaScript file
  wp_enqueue_script( 'afterdark-navigation' , get_template_directory_uri() . '/main.js' );
}

// Hook to the scripts and style enqueueing
add_action( 'wp_enqueue_scripts', 'ad_add_styles_and_scripts' );

// Support featured images for posts
add_theme_support( 'post-thumbnails' );

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

// Register the custom walker for navigation menu
function ad_nav_menu_walker( $args ) {
  return array_merge( $args, array(
    'walker' => new AD_Walker_Nav_Menu(),
  ));
}

// Customize the arguments passed to wp_nav_menu
add_filter( 'wp_nav_menu_args', 'ad_nav_menu_walker' );

// Register the header's navigation menu
function ad_add_main_navigation() {
  register_nav_menu( 'main-menu',__( 'Main' ) );
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'ad_add_main_navigation' );

// Helper function that adds  categories as  breadcrumbs
function ad_categories_as_breadcrumbs( $id, $bar = false ) {
  // Check if ID is a post ID or a category ID
  if ( !empty( get_the_category( $id ) ) ) :
    $categories = get_the_category( $id );
  else:
    $categories = array( get_category( $id ) );
  endif;

  // Check if there are any categories
  if ( ! empty( $categories ) ) :
    echo '<nav id="breadcrumbs" class="' . ( $bar === true ? ' breadcrumbs-bar' : '' ) . '">';
    if ( $bar === true) :
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
    if ( $bar === true) :
      echo '</div>';
    endif;
    echo '</nav>';
  endif;
}

// Helper function to add links to share the current post or page
function ad_social_media_links( $post ) {
    // Get the current post URL
    $post_url = urlencode( get_permalink( $post->ID ) );

    // Generate the social media sharing links
    $facebook_link = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
    $twitter_link = 'https://twitter.com/intent/tweet?url=' . $post_url;

    // Output the social media sharing links
    echo '<ul class="social-media-links list-links">';
    echo '<li><small>Share on</small></li>';
    echo '<li><a href="' . esc_url( $facebook_link ) . '" target="_blank" rel="nofollow">Facebook</a></li>';
    echo '<li><a href="' . esc_url( $twitter_link ) . '" target="_blank" rel="nofollow">Twitter</a></li>';
    echo '</ul>';
}

// Register two sidebars one for posts and the other for guides
function ad_add_widget_support() {
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
add_action( 'widgets_init', 'ad_add_widget_support' );

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

// Register the footer's navigation menu
function ad_add_footer_navigation() {
  register_nav_menu( 'footer-menu',__( 'Footer' ) );
}

// Hook to the init action hook, run the header menu function
add_action( 'init', 'ad_add_footer_navigation' );
