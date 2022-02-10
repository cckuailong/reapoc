<?php
/**
 * The Template for displaying admin empty this plugin.
 * This template is displayed when the desired admin template was not found.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<h1><?php esc_html_e( 'Error 404', 'ti-woocommerce-wishlist' ); // WPCS: xss ok. ?></h1>
<h2><?php
	$path = array( @$_type, @$_template_name ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
	$path = implode( DIRECTORY_SEPARATOR, $path );
	printf( __( 'Template "%s" not found!', 'ti-woocommerce-wishlist' ), $path ); // WPCS: xss ok.
	?></h2>
<!-- <?php echo self::file( @$_template_name, @$_type ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged ?> -->
