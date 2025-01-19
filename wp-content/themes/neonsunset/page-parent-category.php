<?php
/**
 * ------------------------------------------------------------------------
 * Template Name: Parent Category Page
 * ------------------------------------------------------------------------
 * Description: Page for displaying the child categories of a parent category specified by the page slug
 *
 * @package NeonSunset
 */

get_header();
global $post;
ns_categories_as_breadcrumbs( ns_get_category_id_by_slug( $post->post_name ), true );
?>
<main class="wrapper category-card-wrapper">
	<?php
	$children = ns_get_child_categories( get_queried_object() );
	if ( $children ) :
		foreach ( $children as $child ) :
			$thumbnail_id = get_term_meta(
				$child->term_id,
				'category_thumbnail',
				true
			);

			get_template_part(
				'parts/category-card',
				'content-card',
				array(
					'child'        => $child,
					'thumbnail-id' => $thumbnail_id,
				)
			);
		endforeach;
	else :
		?>
	<article>
		<section>
			<h2>Sorry, no <?php echo esc_html( get_post_field( 'post_name', get_queried_object_id() ) ); ?> found</h2>
		</section>
	</article>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
