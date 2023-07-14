<?php
// Include custom functions and classes
require_once get_template_directory() . '/includes/nav-walker-menu.php';
require_once get_template_directory() . '/includes/login.php';
require_once get_template_directory() . '/includes/head.php';
require_once get_template_directory() . '/includes/body.php';
require_once get_template_directory() . '/includes/header.php';
require_once get_template_directory() . '/includes/custom-loops.php';
require_once get_template_directory() . '/includes/breadcrumbs.php';
require_once get_template_directory() . '/includes/archive.php';
require_once get_template_directory() . '/includes/single.php';
require_once get_template_directory() . '/includes/sidebar.php';
require_once get_template_directory() . '/includes/footer.php';

// Support featured images for posts
add_theme_support( 'editor-style' );
add_theme_support( 'post-thumbnails' );

// Hook functions and filters
add_action( 'init', 'ad_add_main_navigation' );
add_action( 'init', 'ad_add_footer_navigation' );
add_filter( 'wp_nav_menu_args', 'ad_nav_menu_walker' );
add_action( 'admin_enqueue_scripts', 'ad_custom_editor_styles' );
add_action( 'enqueue_block_editor_assets', 'ad_block_editor_styles' );
add_action( 'wp_enqueue_scripts', 'ad_add_styles_and_scripts', 99 );
add_filter( 'body_class', 'ad_body_class' );
add_action( 'login_head', 'ad_replace_login_logo' );
add_action( 'widgets_init', 'ad_add_widget_support' );
