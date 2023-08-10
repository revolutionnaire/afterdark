<?php
/**
 * ------------------------------------------------------------------------
 * Site's Posts
 * ------------------------------------------------------------------------
 * This file is for customization and helper functions that will be used
 * on the post template.
 *
 * @package AfterDark
 */

/**
 * Helper function to add links to share the current post or page.
 *
 * @param WP_Post $post the post for the links.
 */
function ad_social_media_links( $post ) {
	// Get the current post URL.
	$post_url = rawurlencode( get_permalink( $post->ID ) );

	// Generate the social media sharing links.
	$facebook_link = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
	$twitter_link  = 'https://twitter.com/intent/tweet?url=' . $post_url;
	$mastodon_link = 'https://mastodonshare.com/?url=' . $post_url;

	// Output the social media sharing links.
	echo '<ul class="social-media-links list-links">';
	echo '<li><small>Share on</small></li>';
	echo '<li><a href="' . esc_url( $facebook_link ) . '" target="_blank" rel="nofollow">Facebook</a></li>';
	echo '<li><a href="' . esc_url( $twitter_link ) . '" target="_blank" rel="nofollow">Twitter</a></li>';
	echo '<li><a href="' . esc_url( $mastodon_link ) . '" target="_blank" rel="nofollow">Mastodon</a></li>';
	echo '</ul>';
}
