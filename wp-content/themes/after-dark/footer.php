<?php
// Define HTML structure of the Footer Menu
$args = array (
  'theme_location' => 'footer-menu',
  'menu_class' => 'nav',
  'container' => null,
  'items_wrap' => '<ul id="%1$s" class="%2$s list-links">%3$s</ul>'
);
?>
    <footer>
      <div class="wrapper">
      <p id="colophon">Copyright &copy; <?php echo date("Y"); ?> Around After Dark. All rights reserved.</p>
<?php if ( has_nav_menu( 'footer-menu' ) ) : wp_nav_menu( $args ); endif; ?>
      </div>
    </footer>
<?php wp_footer(); ?>
  </body>
</html>
