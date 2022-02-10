<?php
/**
 * The Template for displaying footer for wizard.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'admin_print_footer_scripts' );
if ( 'intro' !== $page ) :
	?>
	<div class="tinvwl-return-to-dash">
		<a class="tinvwl-btn white w-icon md-icon round" href="<?php echo esc_url( admin_url() ); ?>"><i
					class="ftinvwl ftinvwl-arrow-left"></i><?php esc_html_e( 'Return to the WordPress Dashboard', 'ti-woocommerce-wishlist' ); ?>
		</a>
	</div>
<?php endif; ?>
</body></html>
