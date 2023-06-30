<?php
/*
 * Custom class that turns the class names of the list items of the navigation menu's  walker to use slugs instead of numbers
 */

class AD_Walker_Nav_Menu extends Walker_Nav_Menu {
  // Override the start_el method
  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    $item_classes = $item->classes;
    $item_title = strtolower( $item->title );
    $item_classes[] = 'menu-item-' . $item_title;
    $classes = empty( $item_classes ) ? array() : ( array ) $item_classes;

    $output .= '<li class="' . implode( ' ', $classes ) . '">';

    $attributes = '';

    if (!empty($item->attr_title ) ) :
      $attributes .= ' title="' . esc_attr( $item->attr_title ) . '"';
    endif;

    if (!empty( $item->target ) ) :
      $attributes .= ' target="' . esc_attr( $item->target ) . '"';
    endif;

    if ( !empty( $item->xfn ) ) :
      $attributes .= ' rel="' . esc_attr( $item->xfn ) . '"';
    endif;

    if ( !empty( $item->url ) ) :
      $attributes .= ' href="' . esc_attr( $item->url ) . '"';
    endif;

    $item_output = $args->before;
    $item_output .= '<a' . $attributes . '>';
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}
