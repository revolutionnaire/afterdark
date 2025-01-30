<?php
/**
 * ------------------------------------------------------------------------
 * Related Guides Widget
 * ------------------------------------------------------------------------
 * This feature adds a Guides Widget that displays guides under the same
 * category to the Widgets available in the administration pages.
 *
 * @class RelatedGuidesWidget
 * @package Guides
 */
class Related_Guides_Widget extends WP_Widget {

	/**
 	 * Object Constructor.
 	 */
	public function __construct() {
		parent::__construct(
			'related_guides_widget',
			'Related Guides',
			array( 'description' => 'Displays guides in the same neighborhood or city' )
		);

		add_action( 'admin_head-widgets.php', array( $this, 'css' ) );
	}

	/**
 	 * Inline Styles Injector.
 	 */
	public function css() {
		include 'parts/widget-style.php';
	}

	/**
 	 * Widget Constructor.
 	 *
 	 * @param array $args The arguments passed to the Widget.
 	 * @param array $instance The instance to be passed to the widget which includes the settings.
 	 */
	public function widget( $args, $instance ) {
		global $post;

		if ( ! empty( $post ) ) :
			// Get child categories of the current post.
			$child_categories = get_the_terms( $post->ID, 'category' );

			if ( ! empty( $child_categories ) && ! is_wp_error( $child_categories ) ) :
    			// Get first child category.
    			$first_child_category = $child_categories[0];

    			// Query posts with the same child category.
    			$query_args = array(
					'post_type'      => 'guide',
					'posts_per_page' => $instance['number_of_posts'],
					'category_name'  => $first_child_category->slug,
					'post__not_in'   => array( $post->ID ),
  				);

  				$posts_query = new WP_Query( $query_args );

  				if ( $posts_query->have_posts() ) :
					echo wp_kses_post( $args['before_widget'] );
					echo wp_kses_post( $args['before_title'] ) . esc_html( 'Related Guides ' ) . wp_kses_post( $args['after_title'] );

					while ( $posts_query->have_posts() ) :
						$posts_query->the_post();
						echo wp_kses_post( '<article class="content-card content-card-' . esc_attr( $post->ID ) . '">' );
						echo wp_kses_post( '<header>' );
						if ( has_post_thumbnail() ) :
							echo wp_kses_post( '<a href="' . esc_attr( get_permalink( $post->ID ) ) . '" class="featured-image-container" title="' . esc_attr( get_the_title( $post->ID ) ) . '">' );
							echo the_post_thumbnail(
								$post->ID,
								array(
									'class' => 'image-center-cover',
									'alt'   => esc_attr( get_post_meta( get_post_meta( $post->ID, '_thumbnail_id', true ), '_wp_attachment_image_alt', true ) ),
								)
							);
							echo wp_kses_post( '</a>' );
						endif;
						echo wp_kses_post( '<div class="wrapper">' );
						echo wp_kses_post( '<h3>' . esc_html( get_the_title() ) . '</h3>' );
						echo wp_kses_post( '<a class="button button-foreground" href="' . esc_url( get_permalink() ) . '">Read now&nbsp;&#8250;</a>' );
						echo wp_kses_post( '</div>' );
						echo wp_kses_post( '</header>' );
						echo wp_kses_post( '</article>' );
					endwhile;

					echo wp_kses_post( $args['after_widget'] );
  				else :
					echo wp_kses_post( $args['before_widget'] );
					echo wp_kses_post( $args['before_title'] ) . 'No related guides found' . wp_kses_post( $args['after_title'] );
					echo wp_kses_post( $args['after_widget'] );
				endif;
				// Restore the global post data.
				wp_reset_postdata();
			endif;
		endif;
	}

	/**
 	 * Display the widget form in the WordPress administration panel.
 	 *
 	 * This function is responsible for rendering the form fields and user interface
 	 * elements for configuring the widget instance. It includes a separate template
 	 * file to keep the HTML markup organized and separated from the PHP code.
 	 *
 	 * @param array $instance The current settings for the widget instance.
 	 */
	public function form( $instance ) {
		$number_of_posts = ! empty( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;

		include 'parts/widget-form.php';
	}

	/**
 	 * Update widget instance settings when the widget is saved.
 	 *
 	 * This function is responsible for updating the widget instance settings with
 	 * the values provided by the user when the widget is saved in the WordPress
 	 * administration panel.
 	 *
 	 * @param array $new_instance The new settings for the widget instance.
 	 * @param array $old_instance The previous settings for the widget instance.
 	 * @return array The updated widget instance settings.
 	 */
	public function update( $new_instance, $old_instance ) {
		$instance                    = array();
		$instance['number_of_posts'] = ! empty( $new_instance['number_of_posts'] ) ? absint( $new_instance['number_of_posts'] ) : 5;
		return $instance;
	}

	/**
 	 * Function that registers the widget.
 	 */
	public static function register() {
		register_widget( 'Related_Guides_Widget' );
	}
}
