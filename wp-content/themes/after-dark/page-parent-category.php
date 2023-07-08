<?php
/*
Template Name: Parent Category Page
Description: Page for displaying the child categories of a parent category specified by the page slug
*/
get_header();
global $post; ad_categories_as_breadcrumbs(ad_get_category_id_by_slug( $post->post_name ), true );
?>
<main>
  <section class="wrapper category-card-wrapper">
<?php $children = ad_get_child_categories( get_queried_object() ); if ( $children ) : foreach ($children as $child) : $thumbnail_id = get_term_meta( $child->term_id, 'category_thumbnail', true ); ?>
  <?php get_template_part( 'parts/category-card', 'content-card', array( 'child' => $child, 'thumbnail-id' => $thumbnail_id ) ); ?>
<?php endforeach; else : ?>
    <article>
      <header>
        <h2>Sorry, no <?php echo get_post_field( 'post_name', get_queried_object_id() ); ?> found</h2>
      </header>
    </article>
  <?php endif; ?>
  </section>
</main>
<?php get_footer(); ?>
