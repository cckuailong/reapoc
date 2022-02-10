<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bpfwpDashboard' ) ) {
/**
 * Class to handle plugin dashboard
 *
 * @since 2.0.0
 */
class bpfwpDashboard {

	public $message;
	public $status = true;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_dashboard_to_menu' ), 99 );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
	}

	public function add_dashboard_to_menu() {
		global $menu, $submenu;

		add_submenu_page( 
			'bpfwp-business-profile', 
			'Dashboard', 
			'Dashboard', 
			'manage_options', 
			'bpfwp-dashboard', 
			array($this, 'display_dashboard_screen') 
		);

		if ( ! isset( $submenu['bpfwp-business-profile'] ) or ! is_array( $submenu['bpfwp-business-profile'] ) ) { return; }

		// Create a new sub-menu in the order that we want
		$new_submenu = array();
		$menu_item_count = 5;
		foreach ( $submenu['bpfwp-business-profile'] as $key => $sub_item ) {
			if ( $sub_item[0] == 'Dashboard' ) { $new_submenu[0] = $sub_item; }
			elseif ( $sub_item[0] == 'Locations' ) { $new_submenu[1] = $sub_item; }
			elseif ( $sub_item[0] == 'Business Profile' ) { $new_submenu[2] = ''; }
			elseif ( $sub_item[0] == 'Schemas' ) { $new_submenu[3] = $sub_item; }
			elseif ( $sub_item[0] == 'Settings' ) { $new_submenu[4] = $sub_item; }
			else {
				$new_submenu[$menu_item_count] = $sub_item;
				$menu_item_count++;
			}
		}
		ksort($new_submenu);
		
		$submenu['bpfwp-business-profile'] = $new_submenu;
		
		if ( isset( $dashboard_key ) ) {
			$submenu['bpfwp-business-profile'][0] = $submenu['bpfwp-business-profile'][$dashboard_key];
			unset($submenu['bpfwp-business-profile'][$dashboard_key]);
		}
	}

	// Enqueues the admin script so that our hacky sub-menu opening function can run
	public function enqueue_scripts() {
		$currentScreen = get_current_screen();
		if ( $currentScreen->id == 'business-profile_page_bpfwp-dashboard' ) {
			wp_enqueue_style( 'bpfwp-admin', BPFWP_PLUGIN_URL . '/assets/css/admin.css', array(), BPFWP_VERSION );
			wp_enqueue_script( 'bpfwp-admin-js', BPFWP_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), BPFWP_VERSION, true );
		}
	}

	public function display_dashboard_screen() { 
		global $bpfwp_controller;

		$permission = $bpfwp_controller->permissions->check_permission( 'premium' );

		?>
		<div id="bpfwp-dashboard-content-area">

			<div id="bpfwp-dashboard-content-left">
		
				<?php if ( ! $permission or get_option("BPFWP_Trial_Happening") == "Yes") {
					$premium_info = '<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full">';
					$premium_info .= '<div class="bpfwp-dashboard-new-widget-box-top">';
					$premium_info .= sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to upgrade to premium.'), 'https://www.fivestarplugins.com/premium-upgrade-instructions/' );
					$premium_info .= '</div>';
					$premium_info .= '</div>';

					$premium_info = apply_filters( 'fsp_dashboard_top', $premium_info, 'BPFWP', 'https://www.fivestarplugins.com/license-payment/?Selected=BPFWP&Quantity=1' );

					echo wp_kses(
						$premium_info,
						apply_filters( 'fsp_dashboard_top_kses_allowed_tags', wp_kses_allowed_html( 'post' ) )
					);
				} ?>
		
				<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full" id="bpfwp-dashboard-support-widget-box">
					<div class="bpfwp-dashboard-new-widget-box-top">Get Support<span id="bpfwp-dash-mobile-support-down-caret">&nbsp;&nbsp;&#9660;</span><span id="bpfwp-dash-mobile-support-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="bpfwp-dashboard-new-widget-box-bottom">
						<ul class="bpfwp-dashboard-support-widgets">
							<li>
								<a href="https://www.youtube.com/watch?v=Mq089tgCxkQ&list=PLEndQUuhlvSoOidQF7iRvstiKjOT4tX71" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-youtube.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-support-widgets-text">YouTube Tutorials</div>
								</a>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/business-profile/#faq" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-faqs.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-support-widgets-text">Plugin FAQs</div>
								</a>
							</li>
							<li>
								<a href="http://doc.fivestarplugins.com/plugins/business-profile/" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-documentation.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-support-widgets-text">Documentation</div>
								</a>
							</li>
							<li>
								<a href="https://www.fivestarplugins.com/support-center/" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-forum.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-support-widgets-text">Get Support</div>
								</a>
							</li>
						</ul>
					</div>
				</div>
		
				<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full" id="bpfwp-dashboard-optional-table">
					<div class="bpfwp-dashboard-new-widget-box-top">Location Summary<span id="bpfwp-dash-optional-table-down-caret">&nbsp;&nbsp;&#9660;</span><span id="bpfwp-dash-optional-table-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="bpfwp-dashboard-new-widget-box-bottom">
						<table class='bpfwp-overview-table wp-list-table widefat fixed striped posts'>
							<thead>
								<tr>
									<th><?php _e("Location", 'business-profile'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$args = array(
										'post_type' => 'location',
										'orderby' => 'date',
										'order' => 'DESC',
										'posts_per_page' => 10
									);

									$Dashboard_Locations_Query = new WP_Query($args);
									$Dashboard_Locations = $Dashboard_Locations_Query->get_posts();

									if (sizeOf($Dashboard_Locations) == 0) {echo "<tr><td colspan='3'>" . __("No Locations to display yet. Create a location for it to be displayed here.", 'business-profile') . "</td></tr>";}
									else {
										foreach ($Dashboard_Locations as $Dashboard_Location) { ?>
											<tr>
												<td><a href='post.php?post=<?php echo esc_url( $Dashboard_Location->ID );?>&action=edit'><?php echo esc_html( $Dashboard_Location->post_title ); ?></a></td>
											</tr>
										<?php }
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
		
				<?php /*<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full">
					<div class="bpfwp-dashboard-new-widget-box-top">What People Are Saying</div>
					<div class="bpfwp-dashboard-new-widget-box-bottom">
						<ul class="bpfwp-dashboard-testimonials">
							<?php $randomTestimonial = rand(0,2);
							if($randomTestimonial == 0){ ?>
								<li id="bpfwp-dashboard-testimonial-one">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-testimonial-title">"Awesome. Just Awesome."</div>
									<div class="bpfwp-dashboard-testimonial-author">- @shizart</div>
									<div class="bpfwp-dashboard-testimonial-text">Thanks for this very well-made plugin. This works so well out of the box, I barely had to do ANYTHING to create an amazing FAQ accordion display... <a href="https://wordpress.org/support/topic/awesome-just-awesome-11/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 1){ ?>
								<li id="bpfwp-dashboard-testimonial-two">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-testimonial-title">"Absolutely perfect with great support"</div>
									<div class="bpfwp-dashboard-testimonial-author">- @isaac85</div>
									<div class="bpfwp-dashboard-testimonial-text">I tried several different FAQ plugins and this is by far the prettiest and easiest to use... <a href="https://wordpress.org/support/topic/absolutely-perfect-with-great-support/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 2){ ?>
								<li id="bpfwp-dashboard-testimonial-three">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="bpfwp-dashboard-testimonial-title">"Perfect FAQ Plugin"</div>
									<div class="bpfwp-dashboard-testimonial-author">- @muti-wp</div>
									<div class="bpfwp-dashboard-testimonial-text">Works great! Easy to configure and to use. Thanks! <a href="https://wordpress.org/support/topic/perfect-faq-plugin/" target="_blank">read more</a></div>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div> */ ?>
		
				<?php /* if($hideReview != 'Yes' and $Ask_Review_Date < time()){ ?>
					<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-one-third">
						<div class="bpfwp-dashboard-new-widget-box-top">Leave a review</div>
						<div class="bpfwp-dashboard-new-widget-box-bottom">
							<div class="bpfwp-dashboard-review-ask">
								<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
								<div class="bpfwp-dashboard-review-ask-text">If you enjoy this plugin and have a minute, please consider leaving a 5-star review. Thank you!</div>
								<a href="https://wordpress.org/plugins/ultimate-faqs/#reviews" class="bpfwp-dashboard-review-ask-button">LEAVE A REVIEW</a>
								<form action="admin.php?page=EWD-UFAQ-Options" method="post">
									<input type="hidden" name="hide_ufaq_review_box_hidden" value="Yes">
									<input type="submit" name="hide_ufaq_review_box_submit" class="bpfwp-dashboard-review-ask-dismiss" value="I've already left a review">
								</form>
							</div>
						</div>
					</div>
				<?php } */ ?>
		
				<?php if ( ! $permission or get_option("BPFWP_Trial_Happening") == "Yes" ) { ?>
					<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full" id="bpfwp-dashboard-guarantee-widget-box">
						<div class="bpfwp-dashboard-new-widget-box-top">
							<div class="bpfwp-dashboard-guarantee">
								<div class="bpfwp-dashboard-guarantee-title">14-Day 100% Money-Back Guarantee</div>
								<div class="bpfwp-dashboard-guarantee-text">If you're not 100% satisfied with the premium version of our plugin - no problem. You have 14 days to receive a FULL REFUND. We're certain you won't need it, though.</div>
							</div>
						</div>
					</div>
				<?php } ?>
		
			</div> <!-- left -->
		
			<div id="bpfwp-dashboard-content-right">
			
				<?php if ( ! $permission or get_option("BPFWP_Trial_Happening") == "Yes" ) { ?>
					<div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full" id="bpfwp-dashboard-get-premium-widget-box">
						<div class="bpfwp-dashboard-new-widget-box-top">Get Premium</div>

						<?php if ( get_option("BPFWP_Trial_Happening") == "Yes" ) { do_action( 'fsp_trial_happening', 'BPFWP' ); } ?>
						
						<div class="bpfwp-dashboard-new-widget-box-bottom">
							<div class="bpfwp-dashboard-get-premium-widget-features-title"<?php echo ( get_option("BPFWP_Trial_Happening") == "Yes" ? "style='padding-top: 20px;'" : ""); ?>>GET FULL ACCESS WITH OUR PREMIUM VERSION AND GET:</div>
							<ul class="bpfwp-dashboard-get-premium-widget-features">
								<li>WooCommerce Integration</li>
								<li>Google Rich Snippets</li>
								<li>Schema Default Helpers</li>
								<li>+ More</li>
							</ul>
							<a href="https://www.fivestarplugins.com/license-payment/?Selected=BPFWP&Quantity=1" class="bpfwp-dashboard-get-premium-widget-button" target="_blank">UPGRADE NOW</a>
								
							<?php if ( ! get_option( "BPFWP_Trial_Happening" ) ) { 
								$trial_info = sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to get a free 7-day trial of the premium plugin.'), 'https://www.fivestarplugins.com/premium-upgrade-instructions/' );
						
								echo apply_filters( 'fsp_trial_button', $trial_info, 'BPFWP' );
							} ?>
						</div>

					</div>
				<?php } ?>
			</div>
		
				<!-- <div class="bpfwp-dashboard-new-widget-box ewd-widget-box-full">
					<div class="bpfwp-dashboard-new-widget-box-top">Other Plugins by Etoile</div>
					<div class="bpfwp-dashboard-new-widget-box-bottom">
						<ul class="bpfwp-dashboard-other-plugins">
							<li>
								<a href="https://wordpress.org/plugins/ultimate-product-catalogue/" target="_blank"><img src="<?php echo plugins_url( '../images/ewd-upcp-icon.png', __FILE__ ); ?>"></a>
								<div class="bpfwp-dashboard-other-plugins-text">
									<div class="bpfwp-dashboard-other-plugins-title">Product Catalog</div>
									<div class="bpfwp-dashboard-other-plugins-blurb">Enables you to display your business's products in a clean and efficient manner.</div>
								</div>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/ultimate-reviews/" target="_blank"><img src="<?php echo plugins_url( '../images/ewd-urp-icon.png', __FILE__ ); ?>"></a>
								<div class="bpfwp-dashboard-other-plugins-text">
									<div class="bpfwp-dashboard-other-plugins-title">Ultimate Reviews</div>
									<div class="bpfwp-dashboard-other-plugins-blurb">Let visitors submit reviews and display them right in the tabbed page layout!</div>
								</div>
							</li>
						</ul>
					</div>
				</div> -->
		
			</div> <!-- right -->	
		
		</div> <!-- bpfwp-dashboard-content-area -->
		
		<?php if ( ! $permission or get_option("BPFWP_Trial_Happening") == "Yes" ) { ?>
			<div id="bpfwp-dashboard-new-footer-one">
				<div class="bpfwp-dashboard-new-footer-one-inside">
					<div class="bpfwp-dashboard-new-footer-one-left">
						<div class="bpfwp-dashboard-new-footer-one-title">What's Included in Our Premium Version?</div>
						<ul class="bpfwp-dashboard-new-footer-one-benefits">
							<li>WooCommerce Integration</li>
							<li>Full Product Schema Automatically Applied</li>
							<li>Automatically-integrated schema for posts</li>
							<li>Default Schema Helpers</li>
							<li>Quickly add schema for any page/element using our custom list of defaults</li>
						</ul>
					</div>
					<div class="bpfwp-dashboard-new-footer-one-buttons">
						<a class="bpfwp-dashboard-new-upgrade-button" href="https://www.fivestarplugins.com/license-payment/?Selected=BPFWP&Quantity=1" target="_blank">UPGRADE NOW</a>
					</div>
				</div>
			</div> <!-- bpfwp-dashboard-new-footer-one -->
		<?php } ?>	
		<div id="bpfwp-dashboard-new-footer-two">
			<div class="bpfwp-dashboard-new-footer-two-inside">
				<img src="<?php echo plugins_url( '../assets/img/fivestartextlogowithstar.png', __FILE__ ); ?>" class="bpfwp-dashboard-new-footer-two-icon">
				<div class="bpfwp-dashboard-new-footer-two-blurb">
					At Five Star Plugins, we build powerful, easy-to-use WordPress plugins with a focus on the restaurant, hospitality and business industries. With a modern, responsive look and a highly-customizable feature set, Five Star Plugins can be used as out-of-the-box solutions and can also be adapted to your specific requirements.
				</div>
				<ul class="bpfwp-dashboard-new-footer-two-menu">
					<li>SOCIAL</li>
					<li><a href="https://www.facebook.com/fivestarplugins/" target="_blank">Facebook</a></li>
					<li><a href="https://twitter.com/fivestarplugins" target="_blank">Twitter</a></li>
					<li><a href="https://www.fivestarplugins.com/category/blog/" target="_blank">Blog</a></li>
				</ul>
				<ul class="bpfwp-dashboard-new-footer-two-menu">
					<li>SUPPORT</li>
					<li><a href="https://www.youtube.com/watch?v=Mq089tgCxkQ&list=PLEndQUuhlvSoOidQF7iRvstiKjOT4tX71" target="_blank">YouTube Tutorials</a></li>
					<li><a href="http://doc.fivestarplugins.com/plugins/business-profile/" target="_blank">Documentation</a></li>
					<li><a href="https://www.fivestarplugins.com/support-center/" target="_blank">Get Support</a></li>
					<li><a href="https://wordpress.org/plugins/business-profile/#faq" target="_blank">FAQs</a></li>
				</ul>
			</div>
		</div> <!-- bpfwp-dashboard-new-footer-two -->
		
	<?php }

	public function get_term_from_array($terms, $term_id) {
		foreach ($terms as $term) {if ($term->term_id == $term_id) {return $term;}}

		return array();
	}

	public function display_notice() {
		if ( $this->status ) {
			echo "<div class='updated'><p>" . esc_html( $this->message ) . "</p></div>";
		}
		else {
			echo "<div class='error'><p>" . esc_html( $this->message ) . "</p></div>";
		}
	}
}
} // endif
