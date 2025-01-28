<?php
/**
 * ------------------------------------------------------------------------
 * Title Card
 * ------------------------------------------------------------------------
 * Part for displaying post title information in a card format
 *
 * @package NeonSunset
 */

?>
<header class="image-card" >
<?php
if ( has_post_thumbnail() ) :
	$thumbnail_alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	?>
	<figure class="featured-image-container">
	<?php
	the_post_thumbnail(
		$post->ID,
		array(
			'class' => 'image-center-fit',
			'alt'   => $thumbnail_alt,
		)
	);

	$caption = wp_get_attachment_caption( get_post_thumbnail_id() );
	if ( ! empty( $caption ) ) :
		?>
		<figcaption class="text-center"><?php echo wp_kses_post( $caption ); ?></figcaption>
	<?php endif; ?>
	</figure>
<?php endif; ?>
</header>
