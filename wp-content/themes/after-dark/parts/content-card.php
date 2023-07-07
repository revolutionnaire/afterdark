<?php $post = $args['post'];  if ( array_key_exists( 'featured-article', $args ) ) : $featured_article = $args['featured-article']; else : $featured_article = false; endif; ?>
<article class="content-card content-card-<?php the_ID( $post->ID ); ?><?php if ( $featured_article == true ) : echo ' content-card-featured'; endif; ?>">
  <header>
<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
    <a href="<?php echo get_permalink( $post->ID ); ?>" class="featured-image-container" title="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">
  <?php the_post_thumbnail( $post->ID, array( 'class' => 'image-center-fit', 'alt' => esc_attr( get_post_meta( get_post_meta( $post->ID, '_thumbnail_id', true ), '_wp_attachment_image_alt', true ) ) ) ); ?>
    </a>
<?php endif; ?>
    <div class="wrapper text-center">
      <h2><?php echo get_the_title( $post->ID ); ?></h2>
      <p><a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">Read now</a>&nbsp;&#8250;</p>
    </div>
  </header>
</article>
