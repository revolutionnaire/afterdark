<?php
/**
 * ------------------------------------------------------------------------
 * Content Card
 * ------------------------------------------------------------------------
 * Part for displaying summary of posts in a card format.
 *
 * @package NeonSunset
 */

global $post;
$featured_article = array_key_exists( 'featured-article', $args ) ? $args['featured-article'] : false;
?>
<article class="content-card content-card-<?php the_ID( $post->ID ); ?> <?php echo $featured_article ? esc_attr( 'content-card-featured' ) : esc_attr( '' ); ?>">
	<header>
	<?php
	$has_thumbnail = has_post_thumbnail( $post->ID );
	if ( $has_thumbnail ) :
		?>
		<a href="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" class="featured-image-container" title="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">
		<?php
		the_post_thumbnail(
			$post->ID,
			array(
				'class' => 'image-center-fit',
				'alt'   => get_post_meta( get_post_meta( $post->ID, '_thumbnail_id', true ), '_wp_attachment_image_alt', true ),
			)
		);
		?>
		</a>
	<?php endif; ?>
		<div class="wrapper">
			<h2><?php echo esc_html( get_the_title( $post->ID ) ); ?></h2>
			<a class="button" href="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" title="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">Read now &#8250;</a>
		</div>
	</header>
</article>
