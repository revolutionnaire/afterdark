<?php
/**
 * ------------------------------------------------------------------------
 * Category Featured Image Form
 * ------------------------------------------------------------------------
 * This contains the form for adding a featured image in the category.
 *
 * @package Guides
 */

?>
<tr class="form-field">
	<th scope="row" valign="top">
	</th>
	<td>
		<input id="image-id" type="hidden" name="image-id" value="<?php echo esc_attr( $category_thumbnail_id ); ?>" />
		<input id="cfi-nonce" type="hidden" name="cfi-nonce" value="<?php echo esc_attr( wp_create_nonce( 'category-featured-image-nonce' ) ); ?>" />
		<div id="image-preview" class="<?php echo 0 === intval( $category_thumbnail_id ) ? esc_attr( 'bg-white' ) : ''; ?>">
			<label for="category-image"><?php esc_html_e( 'Featured Image', 'text-domain' ); ?></label>
			<?php if ( $category_thumbnail_id > 0 ) : ?>
			<img src="<?php echo esc_html( wp_get_attachment_image_url( $category_thumbnail_id, 'large' ) ); ?>" alt="<?php echo esc_attr( get_post_meta( $category_thumbnail_id, '_wp_attachment_image_alt', true ) ); ?>" style="max-width: 100%;" />
		<?php endif; ?>
		</div>
		<?php if ( $category_thumbnail_id > 0 ) : ?>
		<button id="button-image-upload" class="button"><?php esc_html_e( 'Change Image', 'text-domain' ); ?></button>
		<?php else : ?>
		<button id="button-image-upload" class="button"><?php esc_html_e( 'Upload Image', 'text-domain' ); ?></button>
		<?php endif; ?>
		<button id="button-image-remove" class="button"><?php esc_html_e( 'Remove Image', 'text-domain' ); ?></button>
		<p class="description"><?php esc_html_e( 'Upload or select an image from the media library.', 'text-domain' ); ?></p>
	</td>
</tr>
