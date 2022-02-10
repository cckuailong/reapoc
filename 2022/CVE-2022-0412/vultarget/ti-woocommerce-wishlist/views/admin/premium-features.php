<?php
/**
 * The Template for displaying admin premium features notice this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<section class="tinvwl-premium-feat tinvwl-panel w-shadow w-bg">
	<div class="container-fluid">
		<div class="row">
			<div class="tinvwl-pic-col col-lg-4">
				<a href="https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN;// WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM;// WPCS: xss ok. ?>&utm_content=premium_explore_logo&partner=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>">
					<i class="premium_adv"></i>
				</a>
				<h2><?php esc_html_e( 'Premium version', 'ti-woocommerce-wishlist' ) ?></h2>
				<p><?php esc_html_e( 'benefit from all the features', 'ti-woocommerce-wishlist' ) ?></p>
				<a href="https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN;// WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM;// WPCS: xss ok. ?>&utm_content=premium_explore&partner=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>"
				   class="tinvwl-btn white round"><?php esc_html_e( 'check premium options', 'ti-woocommerce-wishlist' ) ?></a>
			</div>
			<div class="tinvwl-feat-col col-lg-4">

				<div class="half-containers rate">
					<h2>
						<a href="https://wordpress.org/support/plugin/ti-woocommerce-wishlist/reviews/"><?php esc_html_e( 'Rate us please', 'ti-woocommerce-wishlist' ) ?></a>
					</h2>
					<p><?php esc_html_e( 'We’d really appreciate if you could spend a few minutes to', 'ti-woocommerce-wishlist' ) ?>
						<br>
						<a href="https://wordpress.org/support/plugin/ti-woocommerce-wishlist/reviews/"><?php esc_html_e( 'leave a review', 'ti-woocommerce-wishlist' ) ?></a>.
					</p>
				</div>
				<div class="half-containers subscribe">
					<h2><?php esc_html_e( 'We love making new friends', 'ti-woocommerce-wishlist' ) ?></h2>
					<p><?php esc_html_e( 'sign up for emails to get updates and instant discount', 'ti-woocommerce-wishlist' ) ?></p>
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
						<form
							action="https://templateinvaders.us14.list-manage.com/subscribe/post?u=e41c4138bfe744af05e6e3e4c&amp;id=7ef8ec2b94"
							method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form"
							class="validate" target="_blank" novalidate>
							<div id="mc_embed_signup_scroll">

								<div class="mc-field-group">
									<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
									<input type="submit" value="<?php __( 'Subscribe', 'ti-woocommerce-wishlist' ); ?>"
										   name="subscribe"
										   id="mc-embedded-subscribe" class="tinvwl-btn">
								</div>
								<div id="mce-responses" class="clear">
									<div class="response" id="mce-error-response" style="display:none"></div>
									<div class="response" id="mce-success-response" style="display:none"></div>
								</div>
								<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
								<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text"
																										  name="b_e41c4138bfe744af05e6e3e4c_7ef8ec2b94"
																										  tabindex="-1"
																										  value="">
								</div>

							</div>
						</form>
					</div>

					<!--End mc_embed_signup-->
				</div>

			</div>
			<div class="tinvwl-sup-col col-lg-4">
				<div class="half-containers money-back">
					<a href="https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/?utm_source=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN;// WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM;// WPCS: xss ok. ?>&utm_content=money_back&partner=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>">
						<span>Money Back Guarantee</span>
					</a>
					<p><?php esc_html_e( '100% No-Risk 14-Days Money Back Guarantee', 'ti-woocommerce-wishlist' ) ?></p>
				</div>
				<div class="half-containers customization">
					<h2><?php esc_html_e( 'Need customization?', 'ti-woocommerce-wishlist' ) ?></h2>
					<p><?php esc_html_e( 'Highly skilled WordPress experts are ready to satisfy your needs', 'ti-woocommerce-wishlist' ) ?></p>
					<a href="https://templateinvaders.com/customization/?utm_source=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>&utm_campaign=<?php echo TINVWL_UTM_CAMPAIGN;// WPCS: xss ok. ?>&utm_medium=<?php echo TINVWL_UTM_MEDIUM;// WPCS: xss ok. ?>&utm_content=customization&partner=<?php echo TINVWL_UTM_SOURCE;// WPCS: xss ok. ?>"
					   class="tinvwl-btn gray round"><?php esc_html_e( 'get started now', 'ti-woocommerce-wishlist' ) ?></a>
				</div>
			</div>
		</div>
	</div>
</section>
