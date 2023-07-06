<header class="title-card" >
<?php if ( has_post_thumbnail() ) : $alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); ?>
  <div class="featured-image-container">
  <?php the_post_thumbnail( $post->ID, array( 'class' => 'image-center-fit', 'alt' => esc_attr( $alt ) ) ); ?>
  </div>
<?php endif; ?>
  <div class="wrapper">
    <h1><?php the_title(); ?></h1>
    <p>Written and Photographed by <?php the_author(); ?></p>
    <p>Published on <?php the_date(); ?></p>
  </div>
</header>
