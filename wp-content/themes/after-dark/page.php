<?php get_header(); ?>
<main>
  <section>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article class="wrapper">
      <header>
      <h1><?php the_title(); ?></h1>
      </header>
      <?php the_content(); ?>
    </article>
  <?php endwhile; else : ?>
    <article>
      <h2>>Sorry, the page wasn't found!</h2>
    </article>
  <?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>
