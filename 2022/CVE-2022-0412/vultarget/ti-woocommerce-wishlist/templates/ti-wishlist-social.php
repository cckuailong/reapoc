<?php
/**
 * The Template for displaying social buttons.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-social.php.
 *
 * @version             1.21.5
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="social-buttons">
	<?php if ( ! empty( $share_on ) ) : ?>
		<span><?php echo wp_kses_post( $share_on ) ?></span>
	<?php endif; ?>
	<ul>
		<?php foreach ( $social as $social_name => $social_url ) {
			?>
			<li><a href="<?php echo esc_url( $social_url ); ?>"
				   class="social social-<?php echo esc_attr( $social_name ) . ' ' . esc_attr( tinv_get_option( 'social', 'icon_style' ) ); ?>"
				   title="<?php echo( $social_titles[ $social_name ] ); ?>"><i
							class="ftinvwl ftinvwl-<?php echo esc_attr( $social_name ); ?>"></i></a></li>
		<?php } ?>
	</ul>
</div>
