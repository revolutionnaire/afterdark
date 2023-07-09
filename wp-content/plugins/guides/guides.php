<?php
/**
 * Plugin Name: Guides
 * Description: Adds the custom post type Guides and support for a featured image in categories.
 * Version: 0.1-beta
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 **/

define( 'GUIDE_POSTS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GUIDE_POSTS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once( GUIDE_POSTS__PLUGIN_DIR . 'class.category-featured-image.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'class.guides.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'class.guides-widget.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'class.guides-rest-api.php' );

add_action( 'init', array( 'Category_Featured_Image', 'init' ) );
add_action( 'init', array( 'Guides', 'init' ) );
add_action( 'widgets_init', 'guides_register_related_guides_widget' );
add_action( 'init', array( 'Guides_REST_API', 'init_hook' ) );
add_action( 'rest_api_init', array( 'Guides_REST_API', 'init' ) );
