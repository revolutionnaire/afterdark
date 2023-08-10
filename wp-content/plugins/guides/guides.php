<?php
/**
 * Plugin Name: Guides
 * Description: Adds the custom post type Guides and support for a featured image in categories.
 * Version: 0.1-beta
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Guides
 **/

// Define paths.
define( 'GUIDE_POSTS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GUIDE_POSTS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include classes.
require_once GUIDE_POSTS__PLUGIN_DIR . 'class-category-featured-image.php';
require_once GUIDE_POSTS__PLUGIN_DIR . 'class-guides.php';
require_once GUIDE_POSTS__PLUGIN_DIR . 'class-related-guides-widget.php';
require_once GUIDE_POSTS__PLUGIN_DIR . 'class-guides-rest-api.php';

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, array( 'Guides', 'activate_plugin' ) );
register_deactivation_hook( __FILE__, array( 'Guides', 'deactivate_plugin' ) );

// Hook intialization and registration functions.
add_action( 'init', array( 'Category_Featured_Image', 'init' ) );
add_action( 'init', array( 'Guides', 'init' ) );
add_action( 'init', array( 'Guides', 'register_guides' ) );
add_action( 'widgets_init', array( 'Related_Guides_Widget', 'register' ) );
add_action( 'init', array( 'Guides_REST_API', 'init_hook' ) );
add_action( 'rest_api_init', array( 'Guides_REST_API', 'init' ) );
