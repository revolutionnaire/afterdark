<?php
/**
 * ------------------------------------------------------------------------
 * Site's Author Pages
 * ------------------------------------------------------------------------
 * This file is for helper functions used in author pages. Currently, the
 * theme uses index.php for the author pages.
 *
 * @package AfterDark
 */

/**
 * Helper function to fetch and display author details.
 *
 * @param integer $author_id The ID of the author.
 */
function ad_contributor_card( $author_id ) {
	$user = get_userdata( $author_id );

	echo '<div class="wrapper contributor-card">';
	echo wp_kses_post( get_avatar( $author_id, 64, get_option( 'avatar_default' ), get_the_author_meta( 'display_name', $author_id ) . '\'s Profile Picture' ) );
	echo '<h3 class="contributor">' . esc_html( get_the_author_meta( 'display_name', $author_id ) ) . '</h3>';
	if ( ! empty( get_the_author_meta( 'user_url', $authord_id ) ) ) :
		echo '<a class="contributor-link" href="' . esc_url( get_the_author_meta( 'user_url', $authord_id ) ) . '" target="_blank">';
		echo '<?xml version="1.0" ?><svg class="bi bi-link-45deg" fill="currentColor" height="16" viewBox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg>';
		echo '</a>';
	endif;
	echo '<p><small>' . esc_html( ucwords( $user->roles[0] ) ) . '</small></p>';
	if ( ! empty( get_the_author_meta( 'description', $author_id ) ) ) :
		echo '<p>' . wp_kses_post( get_the_author_meta( 'description', $author_id ) ) . '</p>';
	endif;
	echo '</div>';
}
