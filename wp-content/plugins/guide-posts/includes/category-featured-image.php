<?php
// Add custom field for category image
function gp_category_thumbnail( $term ) {
  $category_thumbnail_id = ( isset( $term->term_id ) && $term->term_id ) ? get_term_meta( $term->term_id, 'category_thumbnail', true ) : 0;
?>
  <tr class="form-field">
    <th scope="row" valign="top">
    </th>
    <td>
      <input id="image-id" type="hidden" name="image-id" value="<?php echo esc_attr( $category_thumbnail_id ); ?>" />
      <div id="image-preview"<?php if ( $category_thumbnail_id == 0  ) : ?> class="bg-white"<?php endif; ?>>
        <label for="category-image"><?php esc_html_e( 'Featured Image', 'text-domain' ); ?></label>
      <?php if ( $category_thumbnail_id > 0  ) : ?>
        <img src="<?php echo wp_get_attachment_image_url( $category_thumbnail_id, 'large' ); ?>" alt="<?php echo esc_html( get_post_meta( $category_thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>" style="max-width: 100%;" />
      <?php endif; ?>
      </div>
    <?php if ( $category_thumbnail_id > 0  ) : ?>
      <button id="button-image-upload" class="button"><?php esc_html_e( 'Change Image', 'text-domain' ); ?></button>
    <?php else : ?>
      <button id="button-image-upload" class="button"><?php esc_html_e( 'Upload Image', 'text-domain' ); ?></button>
    <?php endif; ?>
      <button id="button-image-remove" class="button"><?php esc_html_e( 'Remove Image', 'text-domain' ); ?></button>
      <p class="description"><?php esc_html_e( 'Upload or select an image from the media library.', 'text-domain' ); ?></p>
    </td>
  </tr>
<?php
}

// Display the category thumbnail field on the category edit form
add_action( 'category_edit_form_fields', 'gp_category_thumbnail', 10, 2 );

// Display the category thumbnail field on the category add form
add_action( 'category_add_form_fields', 'gp_category_thumbnail', 10, 2 );

// Save custom field value
function gp_save_category_thumbnail( $term_id ) {
  if ( isset( $_POST['image-id'] ) ) :
    update_term_meta( $term_id, 'category_thumbnail', intval( $_POST['image-id'] ) );
  endif;
}

// Save custom field value when a category is created
add_action( 'create_category', 'gp_save_category_thumbnail', 10, 2 );

// Save custom field value when a category is updated
add_action( 'edited_category', 'gp_save_category_thumbnail', 10, 2 );
