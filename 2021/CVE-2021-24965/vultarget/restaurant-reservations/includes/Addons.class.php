<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbAddons' ) ) {
/**
 * Class to handle the addons page for Restaurant Reservations
 *
 * @since 1.3
 */
class rtbAddons {

	public function __construct( ) {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 100 );

		// Add a newsletter subscription prompt above the addons
		add_action( 'rtb_addons_pre', array( $this, 'add_subscribe_pompt' ) );
	}

	/**
	 * Add the addons page to the admin menu
	 */
	public function add_menu_page() {

		add_submenu_page(
			'rtb-bookings',
			_x( 'Addons', 'Title of addons page', 'restaurant-reservations' ),
			_x( 'Addons', 'Title of addons page in the admin menu', 'restaurant-reservations' ),
			'manage_options',
			'rtb-addons',
			array( $this, 'show_admin_addons_page' )
		);

	}

	/**
	 * Display the addons page
	 */
	public function show_admin_addons_page() {

		// Set campaign parameters for addon URLs
		$url_params = '?utm_source=Plugin&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations';
		?>

		<div class="wrap">
			<h1><?php _e( 'Addons for Restaurant Reservations', 'restaurant-reservations' ); ?></h1>
			<?php do_action( 'rtb_addons_pre' ); ?>
			<div class="rtb-addons">
				<div class="addon addon-custom-fields">
					<a href="https://themeofthecrop.com/plugins/restaurant-reservations/custom-fields/<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/custom-fields.png'; ?>">
					</a>
					<h3><?php esc_html_e( 'Custom Fields', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'Plan your dinner service better by asking for special seating requests, dietary needs and more when customers book online.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/plugins/restaurant-reservations/custom-fields/<?php echo $url_params; ?>" class="button button-primary" target="_blank">
								<?php esc_html_e( 'Learn More', 'restaurant-reservations' ); ?>
							</a>
						</div>
					</div>
				</div>
				<div class="addon addon-export-bookings">
					<a href="https://themeofthecrop.com/plugins/restaurant-reservations/export-bookings/<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/export-bookings.png'; ?>">
					</a>
					<h3><?php esc_html_e( 'Export Bookings', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'Easily print your bookings in a PDF or export them to an Excel/CSV file so you can analyze patterns, cull customer data and import bookings into other services.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/plugins/restaurant-reservations/export-bookings/<?php echo $url_params; ?>" class="button button-primary" target="_blank">
								<?php esc_html_e( 'Learn More', 'restaurant-reservations' ); ?>
							</a>
						</div>
					</div>
				</div>
				<div class="addon addon-email-templates">
					<a href="https://themeofthecrop.com/plugins/restaurant-reservations/email-templates/<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/email-templates.png'; ?>">
					</a>
					<h3><?php esc_html_e( 'Email Templates', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'Send beautiful email notifications with your own logo and brand colors when your customers make a reservation.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/plugins/restaurant-reservations/email-templates/<?php echo $url_params; ?>" class="button button-primary" target="_blank">
								<?php esc_html_e( 'Learn More', 'restaurant-reservations' ); ?>
							</a>
						</div>
					</div>
				</div>
				<div class="addon addon-mailchimp">
					<a href="https://themeofthecrop.com/plugins/restaurant-reservations/mailchimp/<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/mailchimp.png'; ?>">
					</a>
					<h3><?php esc_html_e( 'MailChimp', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'Subscribe requests to your MailChimp mailing list and watch your subscription rates grow effortlessly.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/plugins/restaurant-reservations/mailchimp/<?php echo $url_params; ?>" class="button button-primary" target="_blank">
								<?php esc_html_e( 'Learn More', 'restaurant-reservations' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div><?php /*
			<h2>Recommended Themes</h2>
			<p>The following restaurant themes integrate beautifully with Restaurant Reservations, providing a clean, stylized booking form that matches your site's design.</p>
			<div class="rtb-addons">
				<div class="addon addon-themes">
					<a href="https://themeofthecrop.com/themes/augustan<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-augustan.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Augustan', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A traditionally elegant theme for high-class restaurants, with simple setup and powerful features.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/themes/augustan<?php echo $url_params; ?>" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://themeofthecrop.com/<?php echo $url_params; ?>">Theme of the Crop</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://themeofthecrop.com/themes/luigi<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-luigi.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Luigi', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A smart theme for upscale bistros and fine Italian restaurants. Get up and running quickly.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/themes/luigi<?php echo $url_params; ?>" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://themeofthecrop.com/<?php echo $url_params; ?>">Theme of the Crop</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://themeofthecrop.com/themes/the-spot<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-the-spot.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'The Spot', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A vibrant theme for bars, pubs and destination restaurants with an attention-grabbing homepage.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/themes/the-spot<?php echo $url_params; ?>" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://themeofthecrop.com/<?php echo $url_params; ?>">Theme of the Crop</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://themeofthecrop.com/themes/plate-up<?php echo $url_params; ?>">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-plate-up.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Plate Up', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A refined theme for sophisticated, modern restaurants to drive customers to your booking form.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themeofthecrop.com/themes/plate-up<?php echo $url_params; ?>" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://themeofthecrop.com/<?php echo $url_params; ?>">Theme of the Crop</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://themebeans.com/themes/plate?utm_source=totc_addons_plate&utm_medium=banner&utm_campaign=TOTC%20Addons%20Link%2C%20Plate">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-plate.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Plate', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A delightfully beautiful WordPress theme designed to help you build a stunning restaurant website.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://themebeans.com/themes/plate?utm_source=totc_addons_plate&utm_medium=banner&utm_campaign=TOTC%20Addons%20Link%2C%20Plate" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://themebeans.com?utm_source=totc_addons_plate&utm_medium=banner&utm_campaign=TOTC%20Addons%20Link%2C%20Plate">ThemeBeans</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://wordpress.org/themes/auberge/">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-auberge.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Auberge', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'Display a menu of your restaurant, café or bar stylishly with this free mobile-friendly WordPress theme.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://wordpress.org/themes/auberge/" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://www.webmandesign.eu/">Webman Design</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="http://www.anarieldesign.com/themes/restaurant-bar-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-liber.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Liber', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A responsive theme optimized for restaurants and bars supporting features these websites need.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="http://www.anarieldesign.com/themes/restaurant-bar-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="http://www.anarieldesign.com/">Anariel Design</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="https://wordpress.org/themes/brasserie/">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-brasserie.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Brasserie', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A delightfully simple to use and beautifully crafted free theme for any food establishment.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="https://wordpress.org/themes/brasserie/" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="https://www.templateexpress.com/">Template Express</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="http://www.anarieldesign.com/themes/food-blog-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-veggie.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Veggie', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A food blogging and restaurant theme with modern, easy-to-read typography and minimalist design.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="http://www.anarieldesign.com/themes/food-blog-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="http://www.anarieldesign.com/">Anariel Design</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="http://www.anarieldesign.com/themes/wine-and-winery-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-good-ol-wine.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( "Good Ol' Wine", 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A beautiful responsive theme that is suitable for wine enthusiasts, wineries and wine bars.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="http://www.anarieldesign.com/themes/wine-and-winery-wordpress-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="http://www.anarieldesign.com/">Anariel Design</a>
							</span>
						</div>
					</div>
				</div>
				<div class="addon addon-themes">
					<a href="http://www.anarieldesign.com/themes/simple-and-fresh-blogging-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations">
						<img src="<?php echo RTB_PLUGIN_URL . '/assets/img/theme-healthy-living.jpg'; ?>">
					</a>
					<h3><?php esc_html_e( 'Healthy Living', 'restaurant-reservations' ); ?></h3>
					<div class="details">
						<div class="description">
							<?php esc_html_e( 'A modern, clean healthy food blogging theme that can be used for a restaurant as well.', 'restaurant-reservations' ); ?>
						</div>
						<div class="action">
							<a href="http://www.anarieldesign.com/themes/simple-and-fresh-blogging-theme/?utm_source=Theme%20of%20the%20Crop&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations" class="button" target="_blank">
								<?php esc_html_e( 'View Theme', 'restaurant-reservations' ); ?>
							</a>
							<span class="rtb-by">
								by <a href="http://www.anarieldesign.com/">Anariel Design</a>
							</span>
						</div>
					</div>
				</div>
			</div>*/ ?>
			<?php do_action( 'rtb_addons_post' ); ?>
		</div>

		<?php
	}

	/**
	 * Add a prompt for users to subscribe to the Theme of the Crop mailing list
	 * below the addons list.
	 *
	 * @since 0.1
	 */
	public function add_subscribe_pompt() {

		?>

		<p>
			<?php
				echo sprintf(
					esc_html_x( 'Find out when new addons are available by subscribing to the %smonthly newsletter%s, liking %sTheme of the Crop%s on Facebook, or following %sTheme of the Crop%s on Twitter.', 'restaurant-reservations' ),
					'<a target="_blank" href="https://themeofthecrop.com/about/mailing-list/?utm_source=Plugin&utm_medium=Addon%20List&utm_campaign=Restaurant%20Reservations">',
					'</a>',
					'<a target="_blank" href="https://www.facebook.com/themeofthecrop/">',
					'</a>',
					'<a target="_blank" href="http://twitter.com/themeofthecrop">',
					'</a>'
				);
			?>
		</p>

		<?php
	}

}
} // endif;
