<?php
class Category_Featured_Image {

  private static $initiated = false;

  public static function init() {
    if ( ! self::$initiated ) {
      self::init_hooks();
    }
  }

	/**
	 * Initializes WordPress hooks
	 */
  private static function init_hooks() {
    self::$initiated = true;

    // Enqueue JS and CSS files for all the admin pages
    add_action( 'admin_enqueue_scripts', array( 'Category_Featured_Image', 'enqueue_assets' ) );

    // Save custom field value when a category is created
    add_action( 'create_category', array( 'Category_Featured_Image', 'save_category_thumbnail' ), 10, 2 );

    // Save custom field value when a category is updated
    add_action( 'edited_category', array( 'Category_Featured_Image', 'save_category_thumbnail' ), 10, 2 );

    // Display the category thumbnail field on the category edit form
    add_action( 'category_edit_form_fields', array( 'Category_Featured_Image', 'category_thumbnail' ), 10, 2 );

    // Display the category thumbnail field on the category add form
    add_action( 'category_add_form_fields', array( 'Category_Featured_Image', 'category_thumbnail' ), 10, 2 );
  }

  public static function enqueue_assets() {
    $screen = get_current_screen();

    // Check if it's any of the Category admin pages or the one for widgets
    if ( $screen->id === 'edit-category' || $screen->id === 'category' ) :
      // Enqueue media editor
      wp_enqueue_media();

      // Enqueue category featured image JavaScript
      wp_enqueue_script( 'category-featured-image', GUIDE_POSTS__PLUGIN_URL . 'assets/js/category-featured-image.js', array(), '1.0.0', true );

      // Enqueue category featured image styles
      wp_enqueue_style( 'featured-image-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/category-featured-image.css');
    endif;
  }

  public static function category_thumbnail( $term ) {
    $category_thumbnail_id = ( isset( $term->term_id ) && $term->term_id ) ? get_term_meta( $term->term_id, 'category_thumbnail', true ) : 0;

    include 'parts/category-featured-image.php';
  }

  public static function save_category_thumbnail( $term_id ) {
    if ( isset( $_POST['image-id'] ) ) :
      update_term_meta( $term_id, 'category_thumbnail', intval( $_POST['image-id'] ) );
    endif;
  }
}
