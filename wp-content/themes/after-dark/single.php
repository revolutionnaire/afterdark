<?php get_header(); ?>
<main>
  <section>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article>
  <?php get_template_part( 'parts/title-card', 'title-card' ); ?>
        <div class="wrapper">
          <div id="content">
  <?php the_content(); ?>
          </div>
  <?php get_sidebar(); ?>
        </div>
        <footer class="wrapper">
  <?php ad_social_media_links( $post ); ?>
  <?php ad_categories_as_breadcrumbs( $post->ID ); ?>
        </footer>
      </article>
<?php endwhile; else : ?>
      <article class="wrapper">
        <h2>Sorry, no post was found!</h2>
      </article>
<?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>
