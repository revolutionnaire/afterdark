<?php
/**
 * ------------------------------------------------------------------------
 * Theme Footer
 * ------------------------------------------------------------------------
 *
 * @package AfterDark
 */

?>
	<footer>
		<div class="wrapper">
			<p id="colophon">Copyright &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Around After Dark. All rights reserved.</p>
			<?php
			if ( has_nav_menu( 'footer-menu' ) ) :
				wp_nav_menu( ad_create_footer_menu() );
			endif;
			?>
		</div>
	</footer>
	<?php wp_footer(); ?>
</body>
</html>
