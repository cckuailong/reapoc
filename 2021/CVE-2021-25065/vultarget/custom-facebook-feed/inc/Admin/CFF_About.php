<?php
/**
 * About Sbi admin page class.
 *
 * @since 2.4/5.5
 */
namespace CustomFacebookFeed\Admin;

class CFF_About {

	/**
	 * Admin menu page slug.
	 *
	 * @since 2.4/5.5
	 *
	 * @var string
	 */
	const SLUG = 'cff-about';

	/**
	 * Default view for a page.
	 *
	 * @since 2.4/5.5
	 *
	 * @var string
	 */
	const DEFAULT_TAB = 'about';

	/**
	 * Array of license types, that are considered being top level and has no features difference.
	 *
	 * @since 2.4/5.5
	 *
	 * @var array
	 */
	public static $licenses_top = array( 'pro', 'agency', 'ultimate', 'elite' );

	/**
	 * List of features that licenses are different with.
	 *
	 * @since 2.4/5.5
	 *
	 * @var array
	 */
	public static $licenses_features = array();

	/**
	 * The current active tab.
	 *
	 * @since 2.4/5.5
	 *
	 * @var string
	 */
	public $view;

	/**
	 * The core views.
	 *
	 * @since 2.4/5.5
	 *
	 * @var array
	 */
	public $views = array();

	/**
	 * Primary class constructor.
	 *
	 * @since 2.4/5.5
	 */
	public function __construct() {

		// In old PHP we can't define this elsewhere.
		self::$licenses_features = array(
			'entries'      => esc_html__( 'Media Display', 'custom-facebook-feed' ),
			//'fields'       => esc_html__( 'Layouts', 'custom-facebook-feed' ),
			// 'templates'    => esc_html__( 'Post Display', 'custom-facebook-feed' ),
			//'conditionals' => esc_html__( 'Image and Video Display', 'custom-facebook-feed' ),
			'addons' => esc_html__( 'Post Source', 'custom-facebook-feed' ),
			'addons1' => esc_html__( 'Number of Posts', 'custom-facebook-feed' ),
			'addons2' => esc_html__( 'Filtering', 'custom-facebook-feed' ),

			//'marketing'    => esc_html__( 'Filtering', 'custom-facebook-feed' ),
			//'marketing'     => esc_html__( 'Instagram Stories', 'custom-facebook-feed' ),
			'payments'      => esc_html__( 'Feed Layout', 'custom-facebook-feed' ),
			'surveys'     => esc_html__( 'Post Information', 'custom-facebook-feed' ),
			'advanced'       => esc_html__( 'Comments', 'custom-facebook-feed' ),
			'extensions'       => esc_html__( 'Extensions', 'custom-facebook-feed' ),
			'support'      => esc_html__( 'Customer Support', 'custom-facebook-feed' ),
		);

		// Maybe load tools page.
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 2.4/5.5
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// Only load if we are actually on the settings page.
		if ( self::SLUG !== $page ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );

		/*
		 * Define the core views for the our tab.
		 */
		$this->views = apply_filters(
			'cff_admin_about_views',
			array(
				esc_html__( 'About Us', 'custom-facebook-feed' )        => array( 'about' ),
				esc_html__( 'Getting Started', 'custom-facebook-feed' ) => array( 'getting-started' ),
			)
		);

		$license = $this->get_license_type();

		if (
			(
				$license === 'pro' ||
				! in_array( $license, self::$licenses_top, true )
			)
			//cff_debug()
		) {
			$vs_tab_name = sprintf( /* translators: %1$s - current license type, %2$s - suggested license type. */
				esc_html__( '%1$s vs %2$s', 'custom-facebook-feed' ),
				ucfirst( $license ),
				$this->get_next_license( $license )
			);

			$this->views[ $vs_tab_name ] = array( 'versus' );
		}

		// Determine the current active settings tab.
		$this->view = ! empty( $_GET['view'] ) ? esc_html( $_GET['view'] ) : self::DEFAULT_TAB;

		// If the user tries to load an invalid view - fallback to About Us.
		if (
			! in_array( $this->view, call_user_func_array( 'array_merge', array_values( $this->views ) ), true ) &&
			! has_action( 'cff_admin_about_display_tab_' . sanitize_key( $this->view ) )
		) {
			$this->view = self::DEFAULT_TAB;
		}

		add_action( 'cff_admin_page', array( $this, 'output' ) );

		// Hook for addons.
		do_action( 'cff_admin_about_init' );
	}

	/**
	 * Enqueue assets for the the page.
	 *
	 * @since 2.4/5.5
	 */
	public function enqueues() {

		wp_enqueue_script(
			'jquery-matchheight',
			CFF_PLUGIN_URL . 'assets/js/jquery.matchHeight-min.js',
			array( 'jquery' ),
			'0.7.0',
			false
		);
	}

	/**
	 * Output the basic page structure.
	 *
	 * @since 2.4/5.5
	 */
	public function output() {

		$show_nav = false;
		foreach ( $this->views as $view ) {
			if ( in_array( $this->view, (array) $view, true ) ) {
				$show_nav = true;
				break;
			}
		}
		?>

		<div id="cff-admin-about" class="wrap cff-admin-wrap">

			<?php
			if ( $show_nav ) {
				$license      = $this->get_license_type();
				$next_license = $this->get_next_license( $license );
				echo '<ul class="cff-admin-tabs">';
				foreach ( $this->views as $label => $view ) {
					$class = in_array( $this->view, $view, true ) ? 'active' : '';
					echo '<li>';
					printf(
						'<a href="%s" class="%s">%s</a>',
						esc_url( admin_url( 'admin.php?page=' . self::SLUG . '&view=' . sanitize_key( $view[0] ) ) ),
						esc_attr( $class ),
						esc_html( $label )
					);
					echo '</li>';
				}
				echo '</ul>';
			}
			?>

			<h1 class="cff-h1-placeholder"></h1>

			<?php
			switch ( $this->view ) {
				case 'about':
					$this->output_about();
					break;
				case 'getting-started':
					$this->output_getting_started();
					break;
				case 'versus':
					$this->output_versus();
					break;
				default:
					do_action( 'cff_admin_about_display_tab_' . sanitize_key( $this->view ) );
					break;
			}
			?>

		</div>

		<?php
	}

	/**
	 * Display the About tab content.
	 *
	 * @since 2.4/5.5
	 */
	protected function output_about() {

		$this->output_about_info();
		$this->output_about_addons();
	}

	/**
	 * Display the General Info section of About tab.
	 *
	 * @since 1.5.8
	 */
	protected function output_about_info() {

		?>

		<div class="cff-admin-about-section cff-admin-columns">

			<div class="cff-admin-about-text" style="min-height: 340px;">
				<h3>
					<?php esc_html_e( 'Hello and welcome to the Custom Facebook Feed plugin, the most customizable, clean, and simple Facebook feed plugin in the world. At Smash Balloon, we build software that helps you create beautiful responsive social media feeds for your website in minutes.', 'custom-facebook-feed' ); ?>
				</h3>

				<p>
					<?php esc_html_e( 'Smash Balloon is a fun-loving WordPress plugin development company birthed into existence in early 2013. We specialize in creating plugins that are not only intuitive and simple to use, but also designed to integrate seamlessly into your website and allow you to display your social media content in powerful and unique ways. Over 1 million awesome people have decided to actively use our plugins, which is an incredible honor that we don’t take lightly. This compels us to try to provide the quickest and most effective customer support that we can, blowing users away with the best customer service they’ve ever experienced.', 'custom-facebook-feed' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'We’re a small, but dedicated, team based in Minnesota in the USA.', 'custom-facebook-feed' ); ?>
				</p>

			</div>

			<div class="cff-admin-about-image cff-admin-column-last">
				<figure>
					<img src="<?php echo CFF_PLUGIN_URL; ?>admin/assets/img/about/team.jpg" alt="<?php esc_attr_e( 'The Sbi Team photo', 'custom-facebook-feed' ); ?>">
					<figcaption>
						<?php esc_html_e( 'The Smash Balloon Team', 'custom-facebook-feed' ); ?><br>
					</figcaption>
				</figure>
			</div>

		</div>
		<?php
	}

	/**
	 * Display the Addons section of About tab.
	 *
	 * @since 1.5.8
	 */
	protected function output_about_addons() {

		if ( ! current_user_can( 'manage_custom_facebook_feed_options' )
             || version_compare( PHP_VERSION,  '5.3.0' ) <= 0
		     || version_compare( get_bloginfo('version'), '4.6' , '<' ) ){
			return;
		}

		$all_plugins = get_plugins();
		$am_plugins  = $this->get_am_plugins();

		?>
		<div id="cff-admin-addons">
			<div class="addons-container">
                <h3><?php echo __( 'Our Other Plugins', 'custom-facebook-feed' ); ?></h3>
				<?php
				foreach ( $am_plugins as $plugin => $details ) :

					$plugin_data = $this->get_plugin_data( $plugin, $details, $all_plugins );

				if ( $plugin === 'wpforms-lite/wpforms.php' ) {
				    echo '<h3>' .__( 'Plugins We Recommend', 'custom-facebook-feed' ). '</h3>';
                }

					?>
					<div class="addon-container">
						<div class="addon-item">
							<div class="details cff-clear">
								<img src="<?php echo esc_url( $plugin_data['details']['icon'] ); ?>">
								<h5 class="addon-name">
									<?php echo esc_html( $plugin_data['details']['name'] ); ?>
								</h5>
								<p class="addon-desc">
									<?php echo wp_kses_post( $plugin_data['details']['desc'] ); ?>
								</p>
							</div>
							<div class="actions cff-clear">
								<div class="status">
									<strong>
										<?php
										printf(
										/* translators: %s - addon status label. */
											esc_html__( 'Status: %s', 'custom-facebook-feed' ),
											'<span class="status-label ' . esc_attr( $plugin_data['status_class'] ) . '">' . wp_kses_post( $plugin_data['status_text'] ) . '</span>'
										);
										?>
									</strong>
								</div>
								<div class="action-button">
									<button class="<?php echo esc_attr( $plugin_data['action_class'] ); ?>" data-plugin="<?php echo esc_attr( $plugin_data['plugin_src'] ); ?>" data-type="plugin">
										<?php echo wp_kses_post( $plugin_data['action_text'] ); ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get AM plugin data to display in the Addons section of About tab.
	 *
	 * @since 1.5.8
	 *
	 * @param string $plugin      Plugin slug.
	 * @param array  $details     Plugin details.
	 * @param array  $all_plugins List of all plugins.
	 *
	 * @return array
	 */
	protected function get_plugin_data( $plugin, $details, $all_plugins ) {

		$have_pro = ( ! empty( $details['pro'] ) && ! empty( $details['pro']['plug'] ) );
		$show_pro = false;

		$plugin_data = array();

		if ( $have_pro ) {
			if ( array_key_exists( $plugin, $all_plugins ) ) {
				if ( is_plugin_active( $plugin ) ) {
					$show_pro = true;
				}
			}
			if ( array_key_exists( $details['pro']['plug'], $all_plugins ) ) {
				$show_pro = true;
			}
			if ( $show_pro ) {
				$plugin  = $details['pro']['plug'];
				$details = $details['pro'];
			}
		}

		if ( array_key_exists( $plugin, $all_plugins ) ) {
			if ( is_plugin_active( $plugin ) ) {
				// Status text/status.
				$plugin_data['status_class'] = 'status-active';
				$plugin_data['status_text']  = esc_html__( 'Active', 'custom-facebook-feed' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary disabled';
				$plugin_data['action_text']  = esc_html__( 'Activated', 'custom-facebook-feed' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			} else {
				// Status text/status.
				$plugin_data['status_class'] = 'status-inactive';
				$plugin_data['status_text']  = esc_html__( 'Inactive', 'custom-facebook-feed' );
				// Button text/status.
				$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-secondary';
				$plugin_data['action_text']  = esc_html__( 'Activate', 'custom-facebook-feed' );
				$plugin_data['plugin_src']   = esc_attr( $plugin );
			}
		} else {
			// Doesn't exist, install.
			// Status text/status.
			$plugin_data['status_class'] = 'status-download';
			if ( isset( $details['act'] ) && 'go-to-url' === $details['act'] ) {
				$plugin_data['status_class'] = 'status-go-to-url';
			}
			$plugin_data['status_text'] = esc_html__( 'Not Installed', 'custom-facebook-feed' );
			// Button text/status.
			$plugin_data['action_class'] = $plugin_data['status_class'] . ' button button-primary';
			$plugin_data['action_text']  = esc_html__( 'Install Plugin', 'custom-facebook-feed' );
			$plugin_data['plugin_src']   = esc_url( $details['url'] );
		}

		$plugin_data['details'] = $details;

		return $plugin_data;
	}

	/**
	 * Display the Getting Started tab content.
	 *
	 * @since 2.4/5.5
	 */
	protected function output_getting_started() {

		$license = $this->get_license_type();
		?>

		<div class="cff-admin-about-section cff-admin-about-section-first-form" style="display:flex;">

			<div class="cff-admin-about-section-first-form-text">

				<h2>
					<?php esc_html_e( 'Creating Your First Feed', 'custom-facebook-feed' ); ?>
				</h2>

				<p>
					<?php esc_html_e( 'Want to get started creating your first feed with Custom Facebook Feed? By following the step by step instructions in this walkthrough, you can easily publish your first feed on your site.', 'custom-facebook-feed' ); ?>
				</p>

				<p>
					<?php esc_html_e( 'Navigate to Facebook Feed in the admin sidebar to go the Configure page.', 'custom-facebook-feed' ); ?>
				</p>

				<p>
					<?php esc_html_e( 'Click on the large blue button to connect your Facebook account.', 'custom-facebook-feed' ); ?>
				</p>

                <p>
					<?php esc_html_e( 'Once you connect a Facebook account, you can display your feed on any post, page or widget using the shortcode [custom-facebook-feed]. You can also use the Custom Facebook Feed Gutenberg block if your site has the WordPress block editor enabled.', 'custom-facebook-feed' ); ?>
                </p>

				<ul class="list-plain">
					<li>
						<a href="https://smashballoon.com/can-display-feeds-multiple-facebook-pages/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=multiplefeeds" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Display Multiple Feeds', 'custom-facebook-feed' ); ?>
						</a>
					</li>
					<li>
						<a href="https://smashballoon.com/using-shortcode-options-customize-facebook-feeds/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=shortcode" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Shortcode Settings Guide', 'custom-facebook-feed' ); ?>
						</a>
					</li>
					<li>
						<a href="https://smashballoon.com/find-facebook-page-group-id/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=pageid" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Find My Page ID', 'custom-facebook-feed' ); ?>
						</a>
					</li>
				</ul>

			</div>

			<div class="cff-admin-about-section-first-form-video">
				<iframe src="https://www.youtube-nocookie.com/embed/0gykYq3JSrY?rel=0" width="540" height="304" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
			</div>

		</div>

		<?php if ( ! in_array( $license, self::$licenses_top, true ) ) { ?>
			<div class="cff-admin-about-section cff-admin-about-section-hero">

				<div class="cff-admin-about-section-hero-main">
					<h2>
						<?php esc_html_e( 'Get Custom Facebook Feed Pro and Unlock all the Powerful Features', 'custom-facebook-feed' ); ?>
					</h2>

					<p class="bigger">
						<?php
						echo wp_kses(
							__( 'Thanks for being a loyal Custom Facebook Feed Lite user. <strong>Upgrade to Custom Facebook Feed Pro</strong> to unlock all the awesome features and experience<br>why Custom Facebook Feed is the most popular Facebook feed plugin.', 'custom-facebook-feed' ),
							array(
								'br'     => array(),
								'strong' => array(),
							)
						);
						?>
					</p>

					<p>
						<?php
						printf(
							wp_kses(
							/* translators: %s - stars. */
								__( 'We know that you will truly love Custom Facebook Feed. It has over <strong>1000+ five star ratings</strong> (%s) and is active on over 200,000 websites.', 'custom-facebook-feed' ),
								array(
									'strong' => array(),
								)
							),
							'<i class="fa fa-star" aria-hidden="true"></i>' .
							'<i class="fa fa-star" aria-hidden="true"></i>' .
							'<i class="fa fa-star" aria-hidden="true"></i>' .
							'<i class="fa fa-star" aria-hidden="true"></i>' .
							'<i class="fa fa-star" aria-hidden="true"></i>'
						);
						?>
					</p>
				</div>

				<div class="cff-admin-about-section-hero-extra">
					<div class="cff-admin-columns">
						<div class="cff-admin-column-50">
							<ul class="list-features list-plain">
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Display photos, videos, and albums in your posts.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Choose which content type to display; posts, photos, albums, videos, links, or events.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Pop-up lightbox to view images and watch videos.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Show comments, likes, shares, reactions and comment replies.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Filter posts by hashtag or words.', 'custom-facebook-feed' ); ?>
								</li>
							</ul>
						</div>
						<div class="cff-admin-column-50 cff-admin-column-last">
							<ul class="list-features list-plain">
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Infinitely load more posts.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'HD, 360 degree, and Live video support.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Support for Facebook Groups.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Multiple post layout options.', 'custom-facebook-feed' ); ?>
								</li>
								<li>
									<i class="fa fa-check" aria-hidden="true"></i>
									<?php esc_html_e( 'Priority Pro support from our team of experts.', 'custom-facebook-feed' ); ?>
								</li>
							</ul>
						</div>
					</div>

					<hr />

					<h3 class="call-to-action">
						<?php
						if ( 'lite' === $license ) {
							echo '<a href="https://smashballoon.com/custom-facebook-feed/pricing/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=profeaturescompare" target="_blank" rel="noopener noreferrer">';
						} else {
							echo '<a href="https://smashballoon.com/custom-facebook-feed/pricing/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=profeaturescompare" target="_blank" rel="noopener noreferrer">';
						}
						esc_html_e( 'Get Custom Facebook Feed Pro Today and Unlock all the Powerful Features', 'custom-facebook-feed' );
						?>
						</a>
					</h3>

					<?php if ( 'lite' === $license ) { ?>
						<p>
							<?php
							echo wp_kses(
								__( 'Bonus: Custom Facebook Feed Lite users get <span class="price-20-off">50% off regular price</span>, automatically applied at checkout.', 'custom-facebook-feed' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							);
							?>
						</p>
					<?php } ?>
				</div>

			</div>
		<?php } ?>


        <div class="cff-admin-about-section cff-admin-about-section-squashed cff-admin-about-section-post cff-admin-columns">
            <div class="cff-admin-column-20">
                <img src="<?php echo CFF_PLUGIN_URL; ?>admin/assets/img/about/steps.png" alt="">
            </div>
            <div class="cff-admin-column-80">
                <h2>
					<?php esc_html_e( 'Detailed Step-By-Step Guide', 'custom-facebook-feed' ); ?>
                </h2>

                <p>
					<?php esc_html_e( 'View detailed steps with related images on our website. We have a comprehensive guide to getting up and running with Custom Facebook Feed.', 'custom-facebook-feed' ); ?>
                </p>

                <a href="https://smashballoon.com/custom-facebook-feed/docs/free/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=readsetup" target="_blank" rel="noopener noreferrer" class="cff-admin-about-section-post-link">
					<?php esc_html_e( 'Read Documentation', 'custom-facebook-feed' ); ?><i class="fa fa-external-link" aria-hidden="true"></i>
                </a>
            </div>
        </div>

		<div class="cff-admin-about-section cff-admin-about-section-squashed cff-admin-about-section-post cff-admin-columns">
			<div class="cff-admin-column-20">
				<img src="<?php echo CFF_PLUGIN_URL; ?>admin/assets/img/about/api-error.png" alt="">
			</div>
			<div class="cff-admin-column-80">
				<h2>
					<?php esc_html_e( 'Troubleshoot Connection and API Errors', 'custom-facebook-feed' ); ?>
				</h2>

				<p>
					<?php esc_html_e( 'Are you having trouble displaying your feed due to an error connecting an account or a Facebook API error? We have several articles to help you troubleshoot issues and help you solve them.', 'custom-facebook-feed' ); ?>
				</p>

				<a href="https://smashballoon.com/custom-facebook-feed/docs/errors/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=readerrordoc" target="_blank" rel="noopener noreferrer" class="cff-admin-about-section-post-link">
					<?php esc_html_e( 'Read Documentation', 'custom-facebook-feed' ); ?><i class="fa fa-external-link" aria-hidden="true"></i>
				</a>
			</div>
		</div>

		<?php
	}

	/**
	 * Get the next license type. Helper for Versus tab content.
	 *
	 * @since 1.5.5
	 *
	 * @param string $current Current license type slug.
	 *
	 * @return string Next license type slug.
	 */
	protected function get_next_license( $current ) {

	    return 'Pro';
		$current       = ucfirst( $current );
		$license_pairs = array(
			'Lite'  => 'Pro',
			'Basic' => 'Pro',
			'Plus'  => 'Pro',
			'Pro'   => 'Elite',
		);

		return ! empty( $license_pairs[ $current ] ) ? $license_pairs[ $current ] : 'Elite';
	}

	/**
	 * Display the Versus tab content.
	 *
	 * @since 2.4/5.5
	 */
	protected function output_versus() {

		//$license      = $this->get_license_type();
		//$next_license = $this->get_next_license( $license );
		$license      = 'lite';
		$next_license = 'pro';
		?>

		<div class="cff-admin-about-section cff-admin-about-section-squashed">
			<h1 class="centered">
				<strong><?php echo esc_html( ucfirst( $license ) ); ?></strong> vs <strong><?php echo esc_html( ucfirst( $next_license ) ); ?></strong>
			</h1>

			<p class="centered">
				<?php esc_html_e( 'Get the most out of your Custom Facebook Feeds by upgrading to Pro and unlocking all of the powerful features.', 'custom-facebook-feed' ); ?>
			</p>
		</div>

		<div class="cff-admin-about-section cff-admin-about-section-squashed cff-admin-about-section-hero cff-admin-about-section-table">

			<div class="cff-admin-about-section-hero-main cff-admin-columns">
				<div class="cff-admin-column-33">
					<h3 class="no-margin">
						<?php esc_html_e( 'Feature', 'custom-facebook-feed' ); ?>
					</h3>
				</div>
				<div class="cff-admin-column-33">
					<h3 class="no-margin">
						<?php echo esc_html( ucfirst( $license ) ); ?>
					</h3>
				</div>
				<div class="cff-admin-column-33">
					<h3 class="no-margin">
						<?php echo esc_html( ucfirst( $next_license ) ); ?>
					</h3>
				</div>
			</div>
			<div class="cff-admin-about-section-hero-extra no-padding cff-admin-columns">

				<table>
					<?php
					foreach ( self::$licenses_features as $slug => $name ) {
						$current = $this->get_license_data( $slug, $license );
						$next    = $this->get_license_data( $slug, strtolower( $next_license ) );

						if ( empty( $current ) || empty( $next ) ) {
							continue;
						}
						?>
						<tr class="cff-admin-columns">
							<td class="cff-admin-column-33">
								<p><?php echo esc_html( $name ); ?></p>
							</td>
							<td class="cff-admin-column-33">
								<?php if ( is_array( $current ) ) : ?>
									<p class="features-<?php echo esc_attr( $current['status'] ); ?>">
										<?php echo wp_kses_post( implode( '<br>', $current['text'] ) ); ?>
									</p>
								<?php endif; ?>
							</td>
							<td class="cff-admin-column-33">
								<?php if ( is_array( $current ) ) : ?>
									<p class="features-full">
										<?php echo wp_kses_post( implode( '<br>', $next['text'] ) ); ?>
									</p>
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>

			</div>

		</div>

		<div class="cff-admin-about-section cff-admin-about-section-hero">
			<div class="cff-admin-about-section-hero-main no-border">
				<h3 class="call-to-action centered">
					<?php
					if ( 'lite' === $license ) {
						echo '<a href="https://smashballoon.com/custom-facebook-feed/pricing/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=profeaturescompare" target="_blank" rel="noopener noreferrer">';
					} else {
						echo '<a href="https://smashballoon.com/custom-facebook-feed/pricing/?utm_campaign=facebook-free&utm_source=gettingstarted&utm_medium=profeaturescompare" target="_blank" rel="noopener noreferrer">';
					}
					printf( /* translators: %s - next license level. */
						esc_html__( 'Get Custom Facebook Feed Pro Today and Unlock all the Powerful Features', 'custom-facebook-feed' ),
						esc_html( $next_license )
					);
					?>
					</a>
				</h3>

				<?php if ( 'lite' === $license ) { ?>
                    <p class="centered">
						<?php
						echo wp_kses(
							__( 'Bonus: Custom Facebook Feed Lite users get <span class="price-20-off">50% off regular price</span>, automatically applied at checkout.', 'custom-facebook-feed' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						);
						?>
                    </p>
				<?php } ?>
			</div>
		</div>

		<?php
	}

	/**
	 * List of AM plugins that we propose to install.
	 *
	 * @since 2.4/5.5
	 *
	 * @return array
	 */
	protected function get_am_plugins() {

		$images_url = CFF_PLUGIN_URL . 'admin/assets/img/about/';

		return array(
			'instagram-feed/instagram-feed.php' => array(
				'icon' => $images_url . 'plugin-if.png',
				'name' => esc_html__( 'Instagram Feed', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Instagram Feed is a clean and beautiful way to add your Instagram posts to your website. Grab your visitors attention and keep them engaged with your site longer.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
				'pro'  => array(
					'plug' => 'instagram-feed-pro/instagram-feed.php',
					'icon' => $images_url . 'plugin-if.png',
					'name' => esc_html__( 'Instagram Feed Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'Instagram Feed is a clean and beautiful way to add your Instagram posts to your website. Grab your visitors attention and keep them engaged with your site longer.', 'custom-facebook-feed' ),
					'url'  => 'https://smashballoon.com/instagram-feed/?utm_campaign=facebook-free&utm_source=cross&utm_medium=sbiinstaller',
					'act'  => 'go-to-url',
				),
			),

			'custom-twitter-feeds/custom-twitter-feed.php' => array(
				'icon' => $images_url . 'plugin-tw.jpg',
				'name' => esc_html__( 'Custom Twitter Feeds', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
				'pro'  => array(
					'plug' => 'custom-twitter-feeds-pro/custom-twitter-feed.php',
					'icon' => $images_url . 'plugin-tw.jpg',
					'name' => esc_html__( 'Custom Twitter Feeds Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'custom-facebook-feed' ),
					'url'  => 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=facebook-free&utm_source=cross&utm_medium=ctfinstaller',
					'act'  => 'go-to-url',
				),
			),

			'feeds-for-youtube/youtube-feed.php' => array(
				'icon' => $images_url . 'plugin-yt.png',
				'name' => esc_html__( 'Feeds for YouTube', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Feeds for YouTube is a simple yet powerful way to display videos from YouTube on your website. Increase engagement with your channel while keeping visitors on your website.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
				'pro'  => array(
					'plug' => 'youtube-feed-pro/youtube-feed.php',
					'icon' => $images_url . 'plugin-yt.png',
					'name' => esc_html__( 'Feeds for YouTube Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'Feeds for YouTube is a simple yet powerful way to display videos from YouTube on your website. Increase engagement with your channel while keeping visitors on your website.', 'custom-facebook-feed' ),
					'url'  => 'https://smashballoon.com/youtube-feed/?utm_campaign=facebook-free&utm_source=cross&utm_medium=sbyinstaller',
					'act'  => 'go-to-url',
				),
			),

            'wpforms-lite/wpforms.php' => array(
                'icon' => $images_url . 'plugin-wpforms.png',
                'name' => esc_html__( 'WPForms', 'custom-facebook-feed' ),
                'desc' => esc_html__( 'The most beginner friendly drag & drop WordPress forms plugin allowing you to create beautiful contact forms, subscription forms, payment forms, and more in minutes, not hours!', 'custom-facebook-feed' ),
                'url'  => 'https://downloads.wordpress.org/plugin/wpforms-lite.zip',
                'pro'  => array(
                    'plug' => 'wpforms/wpforms.php',
                    'icon' => $images_url . 'plugin-wpforms.png',
                    'name' => esc_html__( 'WPForms', 'custom-facebook-feed' ),
                    'desc' => esc_html__( 'The most beginner friendly drag & drop WordPress forms plugin allowing you to create beautiful contact forms, subscription forms, payment forms, and more in minutes, not hours!', 'custom-facebook-feed' ),
                    'url'  => 'https://wpforms.com/lite-upgrade/?utm_source=WordPress&utm_campaign=liteplugin&utm_medium=cff-about-page',
                    'act'  => 'go-to-url',
                ),
            ),

			'google-analytics-for-wordpress/googleanalytics.php' => array(
				'icon' => $images_url . 'plugin-mi.png',
				'name' => esc_html__( 'MonsterInsights', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'MonsterInsights makes it “effortless” to properly connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
				'pro'  => array(
					'plug' => 'google-analytics-premium/googleanalytics-premium.php',
					'icon' => $images_url . 'plugin-mi.png',
					'name' => esc_html__( 'MonsterInsights Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'MonsterInsights makes it “effortless” to properly connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.', 'custom-facebook-feed' ),
					'url'  => 'https://www.monsterinsights.com/?utm_source=proplugin&utm_medium=cff-about-page&utm_campaign=pluginurl&utm_content=7%2E0%2E0',
					'act'  => 'go-to-url',
				),
			),

			'optinmonster/optin-monster-wp-api.php' => array(
				'icon' => $images_url . 'plugin-om.png',
				'name' => esc_html__( 'OptinMonster', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Our high-converting optin forms like Exit-Intent® popups, Fullscreen Welcome Mats, and Scroll boxes help you dramatically boost conversions and get more email subscribers.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
			),

			'wp-mail-smtp/wp_mail_smtp.php'         => array(
				'icon' => $images_url . 'plugin-smtp.png',
				'name' => esc_html__( 'WP Mail SMTP', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Make sure your website\'s emails reach the inbox. Our goal is to make email deliverability easy and reliable. Trusted by over 1 million websites.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
				'pro'  => array(
					'plug' => 'wp-mail-smtp-pro/wp_mail_smtp.php',
					'icon' => $images_url . 'plugin-smtp.png',
					'name' => esc_html__( 'WP Mail SMTP Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'Make sure your website\'s emails reach the inbox. Our goal is to make email deliverability easy and reliable. Trusted by over 1 million websites.', 'custom-facebook-feed' ),
					'url'  => 'https://wpmailsmtp.com/pricing/',
					'act'  => 'go-to-url',
				),
			),

			'rafflepress/rafflepress.php'           => array(
				'icon' => $images_url . 'plugin-rp.png',
				'name' => esc_html__( 'RafflePress', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Turn your visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with powerful viral giveaways & contests.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
				'pro'  => array(
					'plug' => 'rafflepress-pro/rafflepress-pro.php',
					'icon' => $images_url . 'plugin-rp.png',
					'name' => esc_html__( 'RafflePress Pro', 'custom-facebook-feed' ),
					'desc' => esc_html__( 'Turn your visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with powerful viral giveaways & contests.', 'custom-facebook-feed' ),
					'url'  => 'https://rafflepress.com/pricing/',
					'act'  => 'go-to-url',
				),
			),

			'all-in-one-seo-pack/all_in_one_seo_pack.php'           => array(
				'icon' => $images_url . 'plugin-seo.png',
				'name' => esc_html__( 'All In One SEO Pack', 'custom-facebook-feed' ),
				'desc' => esc_html__( 'Out-of-the-box SEO for WordPress. Features like XML Sitemaps, SEO for custom post types, SEO for blogs or business sites, SEO for ecommerce sites, and much more. More than 50 million downloads since 2007.', 'custom-facebook-feed' ),
				'url'  => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
			),
		);
	}

	/**
	 * Get the array of data that compared the license data.
	 *
	 * @since 2.4/5.5
	 *
	 * @param string $feature Feature name.
	 * @param string $license License type to get data for.
	 *
	 * @return array|false
	 */
	protected function get_license_data( $feature, $license ) {

		$data = array(
			'entries'      => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'User, Hashtag, and Tagged Feeds', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Complete Entry Management inside WordPress', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Display photos and videos in your posts', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'fields'       => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Access to all Standard and Fancy Fields', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Address, Phone, Website URL, Date/Time, Password, File Upload, HTML, Pagebreaks, Section Dividers, Ratings, and Hidden Field', 'custom-facebook-feed' ),
					),
				),
				'plus'  => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Access to all Standard and Fancy Fields', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Address, Phone, Website URL, Date/Time, Password, File Upload, HTML, Pagebreaks, Section Dividers, Ratings, and Hidden Field', 'custom-facebook-feed' ),
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Grid, highlight, masonry, and carousel layouts', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'conditionals' => array(
				'lite'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Image, carousel, and video thumbnails', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Powerful Form Logic for Building Smart Forms', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Powerful Form Logic for Building Smart Forms', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Display images, swipe through carousel posts, and play videos in a pop-up lightbox', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'marketing'    => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Limited Marketing Integration', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Constant Contact only', 'custom-facebook-feed' ),
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( '6 Email Marketing Integrations', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Constant Contact, Mailchimp, AWeber, GetResponse, Campaign Monitor, and Drip', 'custom-facebook-feed' ),
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Display images and play videos in a pop-up lightbox', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'payments'     => array(
				'lite'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Default post layout', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Choose from thumbnail, half-width or full-width post layouts, and grid layout for photo, album, and video feeds.', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'surveys'      => array(
				'lite'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Basic post information', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Display the number of likes, shares, comments, and reactions below each post', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'advanced'     => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Limited Advanced Features', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Multi-page Forms, File Upload Forms, Multiple Form Notifications, Conditional Form Confirmation', 'custom-facebook-feed' ),
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Limited Advanced Features', 'custom-facebook-feed' ) . '</strong>',
						esc_html__( 'Multi-page Forms, File Upload Forms, Multiple Form Notifications, Conditional Form Confirmation', 'custom-facebook-feed' ),
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Display comments below each post and in the pop-up lightbox', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'addons'       => array(
				'lite'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Show feeds from a timeline', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Custom Captcha Addon included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Email Marketing Addons included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Timeline feeds, Photo Grids, Album feeds, Video feeds, Event feeds', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'addons1'       => array(
				'lite'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Choose how many posts to display', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Custom Captcha Addon included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Email Marketing Addons included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Choose to dynamically load more posts using the Load More button', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'addons2'       => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Custom Captcha Addon included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Email Marketing Addons included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Filter posts in your feed based on a particular hashtag, word, or phrase', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'extensions'       => array(
				'lite'  => array(
					'status' => 'none',
					'text'   => array(
						'<strong>' . esc_html__( 'Not available', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic' => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Custom Captcha Addon included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'  => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Email Marketing Addons included', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Supports all add-on extensions; Reviews, Multifeed, Date Range, Album Embed, Featured Post, and Carousel.', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'support'      => array(
				'lite'     => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Limited support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'basic'    => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Standard Support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'     => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( 'Standard Support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'      => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Priority support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'elite'    => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Premium Support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'ultimate' => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Premium Support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'agency'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Premium Support', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
			'sites'        => array(
				'basic'    => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( '1 Site', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'plus'     => array(
					'status' => 'partial',
					'text'   => array(
						'<strong>' . esc_html__( '3 Sites', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'pro'      => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( '5 Sites', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'elite'    => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Unlimited Sites', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'ultimate' => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Unlimited Sites', 'custom-facebook-feed' ) . '</strong>',
					),
				),
				'agency'   => array(
					'status' => 'full',
					'text'   => array(
						'<strong>' . esc_html__( 'Unlimited Sites', 'custom-facebook-feed' ) . '</strong>',
					),
				),
			),
		);

		// Wrong feature?
		if ( ! isset( $data[ $feature ] ) ) {
			return false;
		}

		// Is a top level license?
		$is_licenses_top = in_array( $license, self::$licenses_top, true );

		// Wrong license type?
		if ( ! isset( $data[ $feature ][ $license ] ) && ! $is_licenses_top ) {
			return false;
		}

		// Some licenses have partial data.
		if ( isset( $data[ $feature ][ $license ] ) ) {
			return $data[ $feature ][ $license ];
		}

		// Top level plans has no feature difference with `pro` plan in most cases.
		return $is_licenses_top ? $data[ $feature ]['pro'] : $data[ $feature ][ $license ];
	}

	/**
	 * Get the current installation license type (always lowercase).
	 *
	 * @since 2.4/5.5
	 *
	 * @return string
	 */
	protected function get_license_type() {

		//$type = cff_setting( 'type', '', 'cff_license' );

		//if ( empty( $type ) || ! cff()->pro ) {
			$type = 'free';
		//}

		return strtolower( $type );
	}
}
