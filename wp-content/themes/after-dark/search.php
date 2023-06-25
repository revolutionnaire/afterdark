<?php get_header(); ?>
<form id="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="search-bar">
  <div class="wrapper">
    <input type="search" class="input-text" name="s" placeholder="Search for a location" autocomplete="off">
  </div>
</form>
<main id="content" class="wrapper">
  <section class="content-card-wrapper">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php $post = get_post(); get_template_part( 'parts/content-card', 'content-card', array( 'post' => $post ) ); ?>
<?php endwhile; else : ?>
    <article class="content-card-message">
      <h2>Sorry, no posts were found!</h2>
    </article>
<?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>

