<?php
// Create a custom widget to create a widget that displays the guides with the same child category
class Related_Guides_Widget extends WP_Widget {

  public function __construct() {
    parent::__construct(
      'related_guides_widget',
      'Related Guides',
      array( 'description' => 'Displays guides in the same neighborhood or city' )
    );

    if ( is_active_widget( false, false, $this->id_base ) ) :
      add_action( 'wp_head', array( $this, 'css' ) );
    endif;
  }

  public function css() {
    include 'parts/widget-style.php';
  }

  public function widget( $args, $instance ) {
    global $post;

    if ( ! empty ( $post ) ) :
      // Get child categories of the current post
      $child_categories = get_the_terms( $post->ID, 'category' );

      if ( ! empty( $child_categories ) && ! is_wp_error( $child_categories ) ) :
        // Get first child category
        $first_child_category = $child_categories[0];

        // Query posts with the same child category
        $query_args = array(
          'post_type' => 'guide',
          'posts_per_page' => $instance['number_of_posts'],
          'category_name' => $first_child_category->slug,
          'post__not_in' => array( $post->ID ),
        );

        $posts_query = new WP_Query( $query_args );

        if ( $posts_query->have_posts() ) :
          echo $args['before_widget'];
          echo $args['before_title'] . esc_html( 'Other Guides About ' ) . $first_child_category->name . $args['after_title'];

          while ( $posts_query->have_posts() ) :
            $posts_query->the_post();
            echo wp_kses_post( '<article class="content-card content-card-' . esc_attr( $post->ID ) . '">' );
              echo wp_kses_post( '<header>' );
              if ( has_post_thumbnail() ) :
                echo wp_kses_post( '<div class="featured-image-container">' );
                the_post_thumbnail( $post->ID, array ( 'class' => 'image-center-fit', 'alt' => esc_attr( get_post_meta( get_post_meta( $post->ID, '_thumbnail_id', true ), '_wp_attachment_image_alt', true ) ) ) );
                echo wp_kses_post( '</div>' );
              endif;
                echo wp_kses_post( '<div class="wrapper">' );
                  echo wp_kses_post( '<p class="text-center">' . esc_html( get_the_title() ) . '</p>' );
                  echo wp_kses_post( '<p class="text-center"><a href="' . esc_url( get_permalink() ) . '">Read now</a>&nbsp;&#8250;</p>' );
                echo wp_kses_post( '</div>' );
              echo wp_kses_post( '</header>' );
            echo wp_kses_post( '</article>' );
            echo wp_kses_post( '</div>' );
          endwhile;
          echo wp_kses_post( $args['after_widget'] );
        else :
          echo wp_kses_post( $args['before_widget'] );
          echo wp_kses_post( $args['before_title'] ) . 'No related guides found' . wp_kses_post( $args['after_title'] );
        endif;
        // Restore the global post data
        wp_reset_postdata();
      endif;
    endif;
  }

  public function form( $instance ) {
    $number_of_posts = ! empty( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : 5;
?>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>">Number of posts to display:</label>
        <input class="widefat" type="number" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" value="<?php echo esc_attr( $number_of_posts ); ?>" min="1" max="10">
    </p>
<?php
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['number_of_posts'] = ( ! empty( $new_instance['number_of_posts'] ) ) ? absint( $new_instance['number_of_posts'] ) : 5;
    return $instance;
  }
}

// Registered custom widget
function guides_register_related_guides_widget() {
  register_widget( 'Related_Guides_Widget' );
}
