<?php
/**
 * ------------------------------------------------------------------------
 * Site's Login Page
 * ------------------------------------------------------------------------
 * This file is for customization function that will be used on the
 * to change the logo on the login page.
 *
 * @package AfteDark
 */

/**
 * Replace the logo on the login page.
 */
function ad_replace_login_logo() {
	$logo = get_template_directory_uri() . '/assets/images/Logo-Stroked-Moon.svg';

	// Output the custom logo image.
	echo '<style type="text/css">
		.login h1 a {
			background-image: url(' . esc_url( $logo ) . ') !important;
			background-size: contain !important;
			height: 100px !important;
			width: 100% !important;
		}
	</style>';
}
