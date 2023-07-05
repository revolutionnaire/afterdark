<?php
/**
 * Plugin Name: Guide Posts
 * Description: Adds the custom post type Guides and support for a featured image in categories.
 * Version: 0.01-alpha
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 **/

define( 'GUIDE_POSTS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GUIDE_POSTS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once( GUIDE_POSTS__PLUGIN_DIR . 'includes/category-featured-image.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'includes/scripts-and-styles-enqueueing.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'includes/guide-posts-custom-post.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'includes/guide-posts-rest-api.php' );
require_once( GUIDE_POSTS__PLUGIN_DIR . 'includes/guide-posts-widget.php' );
