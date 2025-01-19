<?php
/**
 * ------------------------------------------------------------------------
 * AD Walker Nav Menu
 * ------------------------------------------------------------------------
 * Extends the Walker_Nav_Menu class to provide custom formatting and
 * output for navigation menu items. It adds a class to each menu item
 * based on its title and modifies the output attributes and HTML structure
 * of each menu item.
 *
 * @class NS_Walker_Nav_Menu
 * @package AfteDark
 */
class NS_Walker_Nav_Menu extends Walker_Nav_Menu {
	/**
 	 * Start the element (menu item) output.
 	 *
 	 * This function is called by WordPress for each menu item in the navigation menu.
 	 *
 	 * @param string $output  Used to append additional content (HTML) to the output.
 	 * @param object $item    The current menu item being processed.
 	 * @param int    $depth   Depth of the current menu item (0 for top-level items).
 	 * @param array  $args    An array of arguments passed to wp_nav_menu() function.
 	 * @param int    $id      ID of the current menu item.
 	 *
 	 * @return void
 	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_classes   = $item->classes;
		$item_title     = strtolower( $item->title );
		$item_classes[] = 'menu-item-' . $item_title;
		$classes        = empty( $item_classes ) ? array() : (array) $item_classes;

		$attributes = '';

		if ( ! empty( $item->attr_title ) ) :
			$attributes .= ' title="' . esc_attr( $item->attr_title ) . '"';
		endif;

		if ( ! empty( $item->target ) ) :
			$attributes .= ' target="' . esc_attr( $item->target ) . '"';
		endif;

		if ( ! empty( $item->xfn ) ) :
			$attributes .= ' rel="' . esc_attr( $item->xfn ) . '"';
		endif;

		if ( ! empty( $item->url ) ) :
			$attributes .= ' href="' . esc_attr( $item->url ) . '"';
		endif;

		$item_output = '<div class="' . implode( ' ', $classes ) . '">';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= '</div>';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
