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

  // Load the theme's style last
  wp_enqueue_style( 'afterdark-style', get_stylesheet_uri(), array(), null, 'all' );

  // Load main JavaScript file
  wp_enqueue_script( 'afterdark-script' , get_template_directory_uri() . '/main.js' );
}

// Hook to the scripts and style enqueueing
add_action( 'wp_enqueue_scripts', 'ad_add_styles_and_scripts', 99 );

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

// Replace post ID with page slugs in the classes added to the body element
function ad_body_class($classes) {
  // Remove existing page or post ID class
  $classes = array_filter($classes, function($class) {
    return !preg_match('/^(page-id|postid)-\d+$/i', $class);
  });

  // Get the current post or page
  $post = get_queried_object();

  if ($post instanceof WP_Post) {
    // Check if it's a page or post
    $slug_class = $post->post_type === 'page' ? 'page-' : 'post-';
    $slug_class .= $post->post_name;

    // Add the post or page slug as a class with the appropriate prefix
    $classes[] = $slug_class;
  }

  return $classes;
}

// Customize the classes of the body
add_filter('body_class', 'ad_body_class');

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

// Helper function to get the  home page loop
function ad_home_page_loop( $post_count ) {
  $args = array(
    'post_type' => 'guide', // Set the post type to 'guide'
    'posts_per_page' => $post_count, // Display 7 posts (must always be an odd number to evenly distribute the posts)
    'orderby' => 'date', // Order posts by date
    'order'   => 'DESC', // Display posts in descending order (newest to oldest)
  );

  return new WP_Query( $args );
}

// Helper function to get category id by page slug that is the same as the  category slug
function ad_get_category_id_by_slug( $page_slug ) {
  $category = get_category_by_slug( $page_slug );

  return get_cat_ID( $category->name );
}

// Helper function that adds  categories as  breadcrumbs by the post's ID or the category ID on the category page
function ad_categories_as_breadcrumbs( $id, $bar = false ) {
  // Check if ID is a post ID or a category ID
  if ( ! empty( get_the_category( $id ) ) ) :
    $categories = get_the_category( $id );
  else:
    $categories = array( get_category( $id ) );
  endif;

  // Check if there are any categories
  if ( ! empty( $categories ) ) :
    echo '<nav id="breadcrumbs" class="' . ( $bar === true ? ' breadcrumbs-bar' : '' ) . '">';
    if ( $bar === true ) :
      echo '<div class="wrapper">';
    endif;
    echo '<ul class="breadcrumbs">';

    // Filter out the category that doesn't have a child when it is a post
    if ( is_single() ) {
      $categories = array_filter( $categories, function( $category ) {
        return ! empty( get_ancestors( $category->term_id, 'category' ) );
      } );
    }

    foreach ( $categories as $category ) :
      $ancestors = get_ancestors( $category->term_id, 'category' );
      $ancestors = array_reverse( $ancestors );

      if ( ! empty( $ancestors ) ) :
        // Output the ancestor categories as roots
        foreach ( $ancestors as $ancestor ) :
          $taxonomy = get_category( $ancestor );
          // Check if the parent category has a page that displays child categories
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

      // Output the current category as a link if it's a child
      if ( ! empty( $ancestors ) && is_single() ) :
        echo '<li class="breadcrumbs-child-category"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></li>';
      elseif ( ! empty( $ancestors ) ) :
        echo '<li class="breadcrumbs-child-category">' . esc_html( $category->name ) . '</li>';
      endif;
    endforeach;
    echo '</ul>';
    if ( $bar === true ) :
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

// Helper class for getting the link to the category page by passing the category slug
function ad_get_category_link( $category_slug ) {
  $category = get_category_by_slug( $category_slug );

  if ( ! empty( $category ) ):
    return get_category_link( $category->term_id );
  else :
    return 'The category slug does not exist';
  endif;
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

// Helper function for creating an instance of the footer menu
function ad_create_footer_menu() {
  return array (
    'theme_location' => 'footer-menu',
    'menu_class' => 'nav',
    'container' => null,
    'items_wrap' => '<ul id="%1$s" class="%2$s list-links">%3$s</ul>'
  );
}
