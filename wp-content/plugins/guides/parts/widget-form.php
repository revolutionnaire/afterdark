<?php
/**
 * ------------------------------------------------------------------------
 * Related Guides Widget Form
 * ------------------------------------------------------------------------
 * This contains the form for managing the Related Guides Widget.
 *
 * @package Guides
 */

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>">Number of posts to display:</label>
	<input class="widefat" type="number" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" value="<?php echo esc_attr( $number_of_posts ); ?>" min="1" max="10">
</p>
