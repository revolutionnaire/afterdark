<?php get_header(); ?>
<main>
  <section>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article>
        <header class="title-card" >
  <?php if ( has_post_thumbnail() ) : ?>
          <div class="featured-image-container">
    <?php the_post_thumbnail( $post->ID, array( 'class' => 'image-center-fit' ) ); ?>
          </div>
  <?php endif; ?>
          <div class="wrapper">
            <h1><?php the_title(); ?></h1>
            <p>Written and Photographed by <?php the_author(); ?></p>
            <p>Published on <?php the_date(); ?></p>
          </div>
        </header>
        <div class="wrapper">
          <div id="content">
  <?php the_content(); ?>
  <?php ad_social_media_links( $post ); ?>
  <?php ad_categories_as_breadcrumbs( $post->ID ); ?>
          </div>
  <?php get_sidebar(); ?>
        </div>
      </article>
<?php endwhile; else : ?>
      <article>
        <p>Sorry, no post was found!</p>
      </article>
<?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>
