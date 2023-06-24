<?php if ( is_active_sidebar( 'default-sidebar' ) && is_active_sidebar( 'guides-sidebar' ) ) : ?>
  <aside id="sidebar" class="widget-area" role="complementary">
  <?php global $post; if ( $post->post_type === 'guide' ) : dynamic_sidebar( 'guides-sidebar' ); else : dynamic_sidebar( 'default-sidebar' ); endif; ?>
  </aside>
<?php endif; ?>
