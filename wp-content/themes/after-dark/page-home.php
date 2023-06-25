<?php get_header(); ?>
  <main>
<?php
// Define how the loop will fetch posts
$args = array(
  'post_type' => 'guide', // Set the post type to 'guide'
  'posts_per_page' => 7, // Display 7 posts (must always be an odd number to evenly distribute the posts)
  'orderby' => 'date', // Order posts by date
  'order'   => 'DESC', // Display posts in descending order (newest to oldest)
);

$loop = new WP_Query( $args );
?>
<?php if ( $loop->have_posts() ) : ?>
  <?php $loop->the_post(); ?>
    <section>
      <?php $post = get_post(); get_template_part( 'parts/content-card', 'content-card', array( 'post' => $post, 'featured-article' => true ) ); ?>
      <div class="wrapper">
        <div class="content-card-wrapper">
  <?php while ( $loop -> have_posts() ) : ?>
  <?php $loop->the_post(); ?>
  <?php $post = get_post(); get_template_part( 'parts/content-card', 'content-card', array( 'post' => $post ) ); ?>
  <?php endwhile; ?>
        </div>
      </div>
    </section>
<?php else : ?>
    <section class="wrapper">
      <center>
        <p>Sorry, no posts were found!</p>
      </center>
    </section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
  </main>
<?php get_footer(); ?>
