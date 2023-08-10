<?php
/**
 * ------------------------------------------------------------------------
 * Category Card
 * ------------------------------------------------------------------------
 * Part for displaying categories in a card format.
 *
 * @package AfterDark
 */

$category = $args['child'];
?>
<article class="category-card category-card-<?php echo esc_attr( $category->term_id ); ?>">
	<header>
	<?php
	$thumbnail_id = $args['thumbnail-id'];
	if ( ! empty( $args['thumbnail-id'] ) ) :
		?>
		<figure class="featured-image-container">
		<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
			<img src="<?php echo esc_attr( wp_get_attachment_image_url( $thumbnail_id, 'large' ) ); ?>" class="image-center-fit" alt="<?php echo esc_attr( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>" />
		</a>
		<?php
		$caption = wp_get_attachment_caption( $thumbnail_id );
		if ( ! empty( $caption ) ) :
			?>
		<figcaption><?php echo wp_kses_post( $caption ); ?></figcaption>
		<?php endif; ?>
		</figure>
	<?php endif; ?>
		<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
			<h2><?php echo esc_html( $category->name ); ?></h2>
		</a>
	</header>
</article>
