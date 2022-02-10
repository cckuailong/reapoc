<?php
/**
 * Provide a admin area view for the getting started.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$lp_pro_installed = get_option( '_lp_pro_installed' );
$lp_accept_terms  = get_option( 'lp_accept_terms' );
?>
<div class="clear"></div>
<div class="wrap">
	<div class="postbox lp-accept-terms">
		<input type="hidden" name="redirect_url" value="<?php echo esc_url( admin_url() . 'admin.php?page=lp-getting-started' ); ?>">
			<h3 class="hndle myLabel-head"  style="cursor:pointer; padding:7px 10px; font-size:20px;"> <?php esc_attr_e( 'Getting Started', 'wplegalpages' ); ?> </h3>
		<div class="lp_accept_terms_content">
			<h4 class="myLabel-head"><?php esc_attr_e( '1. Accept the terms of use', 'wplegalpages' ); ?></h4>
			<div class="lp_accept_terms">
				<p>
					<?php
					if ( function_exists( 'wp_nonce_field' ) ) {
						wp_nonce_field( 'lp-accept-terms' );
					}
					?>
					<input 
					<?php
					if ( '1' === $lp_accept_terms ) :
						echo 'checked ';
endif;
					?>
					type="checkbox" name="lp_accept_terms" value="1" onclick="jQuery('#lp_accept_submit').toggle();"/> <?php esc_attr_e( 'By using WPLegalPages', 'wplegalpages' ); ?>
	<?php
	if ( '1' === $lp_pro_installed ) :
		echo esc_attr__( ' Pro, ', 'wplegalpages' );
endif;
	?>
<?php esc_attr_e( 'you accept the ', 'wplegalpages' ); ?><a href="https://wplegalpages.com/product-terms-of-use/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=terms-of-use" target="_blank"><?php esc_attr_e( 'terms of use', 'wplegalpages' ); ?></a>.
				</p>
				<?php if ( '1' !== $lp_accept_terms ) : ?>
					<input type="button" name="lp_accept_submit" class="button button-primary" id="lp_accept_submit" style="display:none;" value="Accept">
				<?php endif; ?>
			</div>
		</div>
		<div class="lp_accept_terms_other_content" 
		<?php
		if ( '1' !== $lp_accept_terms ) :
			echo 'style="display:none;"';
endif;
		?>
		>
			<h4 class="myLabel-head"><?php esc_attr_e( '2. Setup your website details', 'wplegalpages' ); ?></h4>
			<div>
				<p><?php esc_attr_e( 'WPLegalPages generates personalized legal pages for your website. To do this it needs to know a few details about your website. Please take a couple of minutes to set up your business details before you can generate a policy page for this website.', 'wplegalpages' ); ?></p>
				<a href="<?php menu_page_url( 'legal-pages', true ); ?>" target="_blank"><?php esc_attr_e( 'Configure details &raquo;', 'wplegalpages' ); ?></a>
			</div>
			<h4 class="myLabel-head"><?php esc_attr_e( '3. Create a legal page', 'wplegalpages' ); ?></h4>
			<div>
				<p><?php esc_attr_e( 'Generate a personalized legal policy page for this website.', 'wplegalpages' ); ?></p>
				<a href="<?php menu_page_url( 'lp-create-page', true ); ?>" target="_blank"><?php esc_attr_e( 'Create Policy Page &raquo;', 'wplegalpages' ); ?></a>
			</div>
			<h4 class="myLabel-head"><?php esc_attr_e( 'About WPLegalPages', 'wplegalpages' ); ?></h4>
			<div>
				<p>
					<?php
					echo sprintf(
						/* translators: %s: Product feature link */
						esc_html__( 'WPLegalPages is a privacy policy and terms & conditions generator for WordPress. With just a few clicks you can generate %s for your WordPress website. These policy pages are vetted by experts and are constantly updated to keep up with the latest regulations such as GDPR, CCPA, CalOPPA and many others.', 'wplegalpages' ),
						sprintf(
							/* translators: %s: Product feature link */
							'<a href="%s" target="_blank">25+ policy pages</a>',
							esc_url( 'https://club.wpeka.com/product/wplegalpages/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=25-policy-pages#wplegalpages-policy-templates' )
						)
					);
					?>
				</p>
			</div>
			<h4 class="myLabel-head"><?php esc_attr_e( 'Help & Support', 'wplegalpages' ); ?></h4>
			<div>
				<ul>
					<li><a href="https://docs.wpeka.com/wp-legal-pages/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=documentation" target="_blank"><?php esc_attr_e( 'Documentation &raquo;', 'wplegalpages' ); ?></a></li>
					<li><a href="https://docs.wpeka.com/wp-legal-pages/faq/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=faq" target="_blank"><?php esc_attr_e( 'FAQs &raquo;', 'wplegalpages' ); ?></a></li>
					<?php if ( '1' === $lp_pro_installed ) : ?>
						<li><a href="https://club.wpeka.com/my-account/orders/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=support" target="_blank"><?php esc_attr_e( 'Support &raquo;', 'wplegalpages' ); ?></a></li>
					<?php else : ?>
						<li><a href="https://wordpress.org/support/plugin/wplegalpages/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=forums" target="_blank"><?php esc_attr_e( 'Forums &raquo;', 'wplegalpages' ); ?></a></li>
						<li><a href="https://club.wpeka.com/product/wplegalpages/?utm_source=wplegalpages&utm_medium=getting-started&utm_campaign=link&utm_content=upgrade-to-wplegalpages-pro" target="_blank"><?php esc_attr_e( 'Upgrade to WPLegalPages Pro &raquo;', 'wplegalpages' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
