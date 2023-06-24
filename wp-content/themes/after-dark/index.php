<?php get_header(); ?>
<?php if ( is_category() ) : afterdark_categories_as_breadcrumbs( $cat, true ); endif; ?>
<main id="content" class="wrapper">
  <section class="content-card-wrapper">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php $post = get_post(); get_template_part( 'parts/content-card', 'content-card', array( 'post' => $post ) ); ?>
<?php endwhile; else : ?>
      <article>
        <p>Sorry, no posts were found!</p>
      </article>
<?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>
