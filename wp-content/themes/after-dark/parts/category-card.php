<?php $category = $args['child']; ?>
<article class="category-card category-card-<?php echo $category->term_id; ?>">
  <header>
<?php $thumbnail_id = $args['thumbnail-id']; ?>
    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
<?php if ( ! empty( $args['thumbnail-id'] ) ) : ?>
      <img src="<?php echo wp_get_attachment_image_url( $thumbnail_id, 'large' ); ?>" class="image-center-fit" alt="<?php echo esc_attr( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>" />
<?php endif; ?>
      <h2><?php echo esc_html( $category->name ); ?></h2>
    </a>
  </header>
</article>
