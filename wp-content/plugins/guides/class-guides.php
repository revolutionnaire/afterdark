<?php
/**
 * ------------------------------------------------------------------------
 * Guides
 * ------------------------------------------------------------------------
 * This is the main class for the plugin which handles registering and
 * management of custom posts and plugin activation and deactivation
 * actions.
 *
 * @class Guides
 * @package Guides
 */
class Guides {

  	/**
   	 * Check if the object has been initiated.
   	 *
   	 * @var boolean
   	 */
	public static $initiated = false;

  	/**
   	 * Initialize object.
   	 */
	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

  	/**
   	 * Initialize WordPress hooks.
   	 */
	private static function init_hooks() {
		self::$initiated = true;

		// Add locations to the built in meta boxes of the custom post type.
		add_action( 'add_meta_boxes', array( 'Guides', 'add_guide_locations_meta_boxes' ) );

		// Enqueue JS and CSS for the block editor.
		add_action( 'enqueue_block_editor_assets', array( 'Guides', 'enqueue_block_editor_assets' ) );

		// Allow iframe tags in the content.
		add_filter( 'wp_kses_allowed_html', array( 'Guides', 'allow_iframe_tags' ), 10, 1 );

		// Restrict contributors to guides.
		add_action( 'admin_menu', array( 'Guides', 'hide_posts_contributor_admin_page' ) );

		// Add custom post type to the default WordPress loop.
		add_action( 'pre_get_posts', array( 'Guides', 'include_guides_in_loop' ) );

		// Add the locations to the content of the custom post type when being rendered.
		add_filter( 'the_content', array( 'Guides', 'add_locations_to_content' ), 20 );
	}

  	/**
   	 * Plugin activation actions.
   	 */
	public static function activate_plugin() {
		self::register_guides();

		// Grant role permissions.
		$editor = get_role( 'editor' );
		$editor->add_cap( 'manage_categories' );

		$contributor = get_role( 'contributor' );
		$contributor->add_cap( 'upload_files' );
	}

  	/**
   	 * Plugin deactivation actions.
   	 */
	public static function deactivate_plugin() {
		// Unregister Guides.
		unregister_post_type( 'guides' );

		// Clear the permalinks to remove the post type's rules from the database.
		flush_rewrite_rules();

		// Restore role permissions.
		$editor = get_role( 'editor' );
		$editor->remove_cap( 'manage_categories' );

		$contributor = get_role( 'contributor' );
		$contributor->remove_cap( 'upload_files' );
	}

  	/**
   	 * Register custom post type.
   	 */
	public static function register_guides() {
		// Setup custom post.
		$labels = array(
			'name'          => 'Guides',
			'singular_name' => 'Guide',
			'menu_name'     => 'Guides',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'guide' ),
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
			'taxonomies'         => array( 'category' ),
			'menu_icon'          => 'dashicons-location',
			'show_in_rest'       => true,
		);

		// Register the custom post type.
		register_post_type( 'guide', $args );
	}

  	/**
   	 * Add location form to the custom post.
   	 */
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

  	/**
   	 * Include custom post type in the WordPress loop.
   	 *
   	 * @param WP_Query $query The WordPress query of the loop.
   	 */
	public static function include_guides_in_loop( $query ) {
		if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_archive() || $query->is_search() || $query->is_category() || $query->is_tag() ) ) :
			$query->set( 'post_type', array( 'post', 'guide' ) );
		endif;
	}

  	/**
   	 * Enqueue assets when loading the block editor.
   	 */
	public static function enqueue_block_editor_assets() {
		// Check if it's a Guide custom post.
		if ( get_post_type() === 'guide' ) :
			// Enqueue media editor.
			wp_enqueue_media();

			// Enqueue the Guide post location editor JavaScript file.
			wp_enqueue_script(
				'guide-posts',
				GUIDE_POSTS__PLUGIN_URL . 'assets/js/guide-locations.js',
				array(),
				false,
				array(
					'strategy'  => 'defer',
					'in_footer' => false,
				)
			);

			// Enqueue location form styles.
			wp_enqueue_style( 'location-styles',  GUIDE_POSTS__PLUGIN_URL . 'assets/css/guide-locations.css' );
		endif;
	}

  	/**
   	 * Allow `iframe` tag in posts.
   	 *
   	 * @param array $allowedposttags The array of tag permissions.
   	 */
	public static function allow_iframe_tags( $allowedposttags ) {
		$allowedposttags['iframe'] = array(
			'src'         => true,
			'width'       => true,
			'height'      => true,
			'frameborder' => true,
			'allow'       => true,
		);

		return $allowedposttags;
	}

  	/**
   	 * Hide posts in administration pages to contributors.
   	 */
	public static function hide_posts_contributor_admin_page() {
		if ( current_user_can( 'contributor' ) ) :
			remove_menu_page( 'edit.php' );
		endif;
	}

  	/**
   	 * Add the locations to the content of the post if it's a guide.
   	 *
   	 * @param string $content The string containing the markup of the contents.
   	 * @return string $content The modified content with the locations.
   	 */
	public static function add_locations_to_content( $content ) {
		// Check if it's a guide.
		if ( is_singular( 'guide' ) ) :
			// Get the value of the custom field.
			$locations = get_post_meta( get_the_ID(), 'guide_locations', true );

			// Append the locations to the content.
			if ( ! empty( $locations ) ) :
				foreach ( $locations as $location ) :
					$content .= '<h4 class="location-name">' . esc_html( $location['name'] ) . '</h4>';
					$content .= wp_kses_post( do_shortcode( $location['description'] ) );
					$content .= '<ul class="location-details">';
					$content .= '<li class="location-wifi">Wi-Fi is <b>';
					$content .= ( true === $location['wifi'] ) ? esc_html( 'available' ) : esc_html( 'not available' );
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

  	/**
   	 * Add location form to the block editor.
   	 *
   	 * @param WP_Post $post The object of the current post.
   	 */
	public static function render_guide_locations_meta_box( $post ) {
		$locations = get_post_meta( $post->ID, 'guide_locations', true );
		wp_nonce_field( 'wp_rest', 'wp_rest' );

		include 'parts/locations.php';
	}
}
