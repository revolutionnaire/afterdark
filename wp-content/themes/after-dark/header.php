<?php
// Define HTML structure of the Header Menu
$args = array (
  'theme_location' => 'main-menu',
  'menu_class' => 'nav-list',
  'menu_id' => null,
  'container' => 'nav',
  'link_before' => '<h2>',
  'link_after' => '</h2>',
  'walker' => new AD_Walker_Nav_Menu()
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <title><?php bloginfo('name'); ?> &#8250; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
<?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <div id="navigation">
      <div class="wrapper">
        <header>
          <?php if ( is_front_page() ) : ?>
            <h1>
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <svg id="logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.11 122.11">
                  <defs><style>.moon{stroke:#000;stroke-miterlimit:10;}</style></defs>
                  <path class="moon" d="m96.69,15.5c-1.03-.81-2.47-.85-3.55-.1-1.08.75-1.55,2.1-1.16,3.36,2.9,9.36,3.35,19.38,1.3,28.99-6.62,31.04-37.25,50.9-68.29,44.28-1.58-.34-3.21-.76-4.97-1.3-1.25-.38-2.61.09-3.35,1.16-.74,1.08-.7,2.51.1,3.55,8.22,10.55,19.94,17.9,33.02,20.69,3.99.85,7.97,1.26,11.9,1.26,26.29,0,49.98-18.35,55.68-45.07,4.59-21.5-3.34-43.27-20.68-56.81Z"/>
                  <path class="star"d="m65.33,15.55c-.2-.62-.78-1.04-1.43-1.04h-10.86l-3.36-10.33c-.2-.62-.78-1.04-1.43-1.04s-1.23.42-1.43,1.04l-3.36,10.33h-10.86c-.65,0-1.23.42-1.43,1.04-.2.62.02,1.29.54,1.68l8.79,6.38-3.36,10.33c-.2.62.02,1.29.54,1.68.26.19.57.29.88.29s.62-.1.88-.29l8.79-6.38,8.79,6.38c.53.38,1.24.38,1.76,0,.53-.38.75-1.06.54-1.68l-3.36-10.33,8.79-6.38c.53-.38.75-1.06.54-1.68Z"/>
                  <path class="star" d="m28.02,38.34h-7.94l-2.45-7.55c-.2-.62-.78-1.04-1.43-1.04s-1.23.42-1.43,1.04l-2.45,7.55h-7.94c-.65,0-1.23.42-1.43,1.04-.2.62.02,1.29.54,1.68l6.42,4.67-2.45,7.55c-.2.62.02,1.29.54,1.68.53.38,1.24.38,1.76,0l6.42-4.67,6.42,4.67c.26.19.57.29.88.29s.62-.1.88-.29c.53-.38.75-1.06.54-1.68l-2.45-7.55,6.42-4.67c.53-.38.75-1.06.54-1.68-.2-.62-.78-1.04-1.43-1.04Z"/>
                </svg>
                <b class="invisible"><?php bloginfo('name'); ?></b>
              </a>
            </h1>
          <?php else : ?>
            <h2>
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <svg id="logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.11 122.11">
                  <defs><style>.moon{stroke:#000;stroke-miterlimit:10;}</style></defs>
                  <path class="moon" d="m96.69,15.5c-1.03-.81-2.47-.85-3.55-.1-1.08.75-1.55,2.1-1.16,3.36,2.9,9.36,3.35,19.38,1.3,28.99-6.62,31.04-37.25,50.9-68.29,44.28-1.58-.34-3.21-.76-4.97-1.3-1.25-.38-2.61.09-3.35,1.16-.74,1.08-.7,2.51.1,3.55,8.22,10.55,19.94,17.9,33.02,20.69,3.99.85,7.97,1.26,11.9,1.26,26.29,0,49.98-18.35,55.68-45.07,4.59-21.5-3.34-43.27-20.68-56.81Z"/>
                  <path class="star"d="m65.33,15.55c-.2-.62-.78-1.04-1.43-1.04h-10.86l-3.36-10.33c-.2-.62-.78-1.04-1.43-1.04s-1.23.42-1.43,1.04l-3.36,10.33h-10.86c-.65,0-1.23.42-1.43,1.04-.2.62.02,1.29.54,1.68l8.79,6.38-3.36,10.33c-.2.62.02,1.29.54,1.68.26.19.57.29.88.29s.62-.1.88-.29l8.79-6.38,8.79,6.38c.53.38,1.24.38,1.76,0,.53-.38.75-1.06.54-1.68l-3.36-10.33,8.79-6.38c.53-.38.75-1.06.54-1.68Z"/>
                  <path class="star" d="m28.02,38.34h-7.94l-2.45-7.55c-.2-.62-.78-1.04-1.43-1.04s-1.23.42-1.43,1.04l-2.45,7.55h-7.94c-.65,0-1.23.42-1.43,1.04-.2.62.02,1.29.54,1.68l6.42,4.67-2.45,7.55c-.2.62.02,1.29.54,1.68.53.38,1.24.38,1.76,0l6.42-4.67,6.42,4.67c.26.19.57.29.88.29s.62-.1.88-.29c.53-.38.75-1.06.54-1.68l-2.45-7.55,6.42-4.67c.53-.38.75-1.06.54-1.68-.2-.62-.78-1.04-1.43-1.04Z"/>
                </svg>
                <b class="invisible"><?php bloginfo('name'); ?></b>
              </a>
            </h2>
          <?php endif; ?>
        </header>
        <form id="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
          <input type="search" class="input-text" name="s" placeholder="Search for a neighborhood or city" autocomplete="off">
        </form>
        <?php if ( has_nav_menu( 'main-menu' ) ) : wp_nav_menu( $args ); endif; ?>
        <svg id="hamburger" xmlns="http://www.w3.org/2000/svg" fill="none" height="24" stroke-width="1.5" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
          <path class="bar" d="M3 5H21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
          <path class="bar" d="M3 12H21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
          <path class="bar" d="M3 19H21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
