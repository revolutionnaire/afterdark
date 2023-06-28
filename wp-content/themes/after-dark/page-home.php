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
<?php if ( $loop->have_posts() ) : $loop->the_post(); ?>
    <section>
      <?php get_template_part( 'parts/content-card', 'content-card', array( 'post' => get_post(), 'featured-article' => true ) ); ?>
      <div class="wrapper">
        <div class="content-card-wrapper">
  <?php while ( $loop->have_posts() ) : ?>
  <?php $loop->the_post(); ?>
  <?php get_template_part( 'parts/content-card', 'content-card', array( 'post' => get_post() ) ); ?>
  <?php endwhile; ?>
        </div>
      </div>
    </section>
<?php else : ?>
    <section>
      <article class="wrapper">
        <h2>Sorry, no posts were found!</h2>
      </article>
    </section>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
  </main>
<?php get_footer(); ?>
