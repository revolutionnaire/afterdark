/**
 * Global JS
 * The JS file that controls all global elements like the navigation bar
 *
 * @package AfterDark
 */

import { NavigationBar } from './navigation.js';

document.addEventListener(
	'DOMContentLoaded',
	function() {
		const navigationBar = new NavigationBar( '#navigation' );
	}
);
