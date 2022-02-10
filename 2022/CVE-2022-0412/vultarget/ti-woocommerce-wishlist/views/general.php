<?php
/**
 * The Template for displaying admin page this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'tinvwl_view_header', $_header );
?>
<div class="wrap" style="margin-left: 58px;"></div>
<div class="<?php echo esc_attr( sprintf( '%s-content', self::$_name ) ); ?>">
	<?php self::view( $_template_name, $_data ); ?>
</div>
<?php do_action( 'tinvwl_view_footer', $_footer ); ?>
