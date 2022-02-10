<?php
/**
 * The Template for displaying header for wizard.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo sprintf( __( 'TI Wishlist &rsaquo; %s', 'ti-woocommerce-wishlist' ), $title ); // WPCS: xss ok. ?></title>
	<?php do_action( 'admin_print_styles' ); ?>
	<?php //do_action( 'admin_head' ); ?>
</head>
<body class="tinvwl-wizard wp-core-ui">
<div class="tinvwl-logo">
	<i class="wizard_logo"></i>
	<h2>ti.Wishlist</h2>
</div>
<div class="tinvwl-progress">
	<ul>
		<?php
		$current_step = filter_input( INPUT_GET, 'step', FILTER_VALIDATE_INT, array( 'default'   => 0,
																					 'min_range' => 0
		) );
		foreach ( $list_steps as $step => $step_name ) {
			$class = 'active';
			if ( $step > $current_step ) {
				$class = '';
			}
			if ( $step == $current_step ) { // WPCS: loose comparison ok.
				$class = 'active last';
			}
			?>
			<li class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $step_name ); ?></li>
		<?php } ?>
	</ul>
</div>
