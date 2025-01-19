/**
 * Navigation Bar
 * Adds toggling functionality to the navigation bar's menu in mobile phones and resets the position based on the WordPress administration bar
 *
 * @class NavigationBar
 * @package AfterDark
 */

class NavigationBar {
	constructor( element ) {
		this.navigationBar = document.querySelector( element );
		const down    = this.navigationBar.querySelector( '#down' );

		down.addEventListener( 'click', this.toggleMenu.bind( this ) );

		document.addEventListener(
			'DOMContentLoaded',
			() => {
				this.moveNavigationBar();
			}
		);

		window.addEventListener(
			'scroll',
			() => {
				this.moveNavigationBar();
			}
		);
	}

	toggleMenu() {
		this.navigationBar.classList.toggle( 'show' );
	}

	moveNavigationBar() {
		var viewport   = window.innerWidth || document.documentElement.clientWidth;
		const iPadMini = 768; // Smallest supported tablet.

		if ( document.body.classList.contains( 'logged-in' ) === true && viewport < iPadMini ) {
			var navigationHeight = this.navigationBar.offsetHeight;
			var wpAdminBarHeight = document.getElementById( 'wpadminbar' ).offsetHeight;
			var scrollTop        = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

			if ( scrollTop >= wpAdminBarHeight ) {
				this.navigationBar.classList.add( 'scrolling' );
			} else {
				this.navigationBar.classList.remove( 'scrolling' );
			}
		}
	}
}

export { NavigationBar };
