<?php
/**
 * ------------------------------------------------------------------------
 * Category Card
 * ------------------------------------------------------------------------
 * Part for displaying categories in a card format.
 *
 * @package NeonSunset
 */

$category = $args['child'];
?>
<article class="category-card category-card-<?php echo esc_attr( $category->term_id ); ?>">
	<header>
	<?php
	$thumbnail_id = $args['thumbnail-id'];
	if ( ! empty( $args['thumbnail-id'] ) ) :
		?>
		<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
		<img class="featured-image image-center-fit" src="<?php echo esc_attr( wp_get_attachment_image_url( $thumbnail_id, 'large' ) ); ?>" alt="<?php echo esc_attr( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>" />
		</a>
	<?php endif; ?>
		<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
			<h3><?php echo esc_html( $category->name ); ?></h3>
		</a>
	</header>
</article>
