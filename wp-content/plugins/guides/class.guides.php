<?php
class Guides {

  public static $initiated = false;

  public static function init() {
    if ( ! self::$initiated ) {
      $labels = array(
        'name' => 'Guides',
        'singular_name' => 'Guide',
        'menu_name' => 'Guides',
      );

      $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'guide' ),
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies' => array( 'category' ),
        'menu_icon' => 'dashicons-location',
        'show_in_rest' => true,
      );

      // Register the custom post type
      register_post_type( 'guide', $args );

      self::init_hooks();
    }
  }

	/**
	 * Initializes WordPress hooks
	 */

  private static function init_hooks() {
    self::$initiated = true;

    // Add locations to the built in meta boxes of the custom post type
    add_action( 'add_meta_boxes', array( 'Guides', 'add_guide_locations_meta_boxes' ) );

    // Enqueue JS and CSS for the block editor
    add_action( 'enqueue_block_editor_assets', array( 'Guides', 'enqueue_block_editor_assets' ) );

    // Allow iframe tags in the content
    add_filter( 'wp_kses_allowed_html', array( 'Guides', 'allow_iframe_tags'), 10, 1 );

    // Add custom post type to the default WordPress loop
    add_action( 'pre_get_posts', array( 'Guides', 'include_guides_in_loop' ) );

    // Add the locations to the content of the custom post type when being rendered
    add_filter( 'the_content', array( 'Guides', 'add_locations_to_content' ), 20 );
  }

  public static function add_guide_locations_meta_boxes() {
    add_meta_box(
      'guide_locations_meta_box',
      'Locations',
      array( 'Guides', 'render_guide_locations_meta_box' ),
      'guide',
      'normal',
      'high'
    );
  }

  public static function include_guides_in_loop( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_archive() || $query->is_search() || $query->is_category() || $query->is_tag() ) ) :
      $query->set( 'post_type', array( 'post', 'guide' ) );
    endif;
  }

  public static function enqueue_block_editor_assets() {
    // Check if it's a Guide custom post
    if ( get_post_type() === 'guide' ) :
      // Enqueue media editor
      wp_enqueue_media();

      // Enqueue the Guide post location editor JavaScript file
      wp_enqueue_script( 'guide-posts', GUIDE_POSTS__PLUGIN_URL . 'assets/js/guide-locations.js', array(), '1.0', true );

      // Enqueue location form styles
      wp_enqueue_style( 'location-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/guide-locations.css');
    endif;
  }

  public static function allow_iframe_tags( $allowedposttags ) {
    $allowedposttags['iframe'] = array(
        'src' => true,
        'width' => true,
        'height' => true,
        'frameborder' => true,
        'allow' => true,
    );

    return $allowedposttags;
  }

  public static function add_locations_to_content( $content ) {
    // Check if it's a guide
    if ( is_singular( 'guide' ) ) :
      // Get the value of the custom field
      $locations = get_post_meta( get_the_ID(), 'guide_locations', true );

      // Append the locations to the content
      if ( ! empty( $locations ) ) :
        foreach( $locations as $location ) :
          $content .= '<h3 class="location-name">' . esc_html ( $location['name'] ) . '</h3>';
          $content .= wp_kses_post( do_shortcode( $location['description'] ) );
          $content .= '<ul class="location-details">';
          $content .= '<li class="location-wifi">Wi-Fi is <b>';
          $content .= ( $location['wifi'] === true ) ? esc_html( 'available' ) : esc_html( 'not available' );
          $content .= '</b></li>';
          if ( ! empty( $location['price'] ) ) :
            $content .= '<li class="location-price">Price is <b>';
            $content .= esc_html( $location['price'] );
            $content .= '</b></li>';
          endif;
          if ( ! empty( $location['hours'] ) ) :
            $content .= '<li class="location-hours">Place open <b>';
            $content .= esc_html( $location['hours'] );
            $content .= '</b></li>';
          endif;
          $content .= '</ul>';
          $content .= wp_kses_post( $location['map'] );
        endforeach;
      endif;
    endif;

    return $content;
  }

  public static function render_guide_locations_meta_box( $post ) {
    $locations = get_post_meta( $post->ID, 'guide_locations', true );
    wp_nonce_field( 'wp_rest', 'wp_rest' );

    include 'parts/locations.php';
  }
}
