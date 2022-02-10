<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VillaTheme_Support' ) ) {

	/**
	 * Class VillaTheme_Support
	 * 1.0.12
	 */
	class VillaTheme_Support {
		protected $plugin_base_name;

		public function __construct( $data ) {
			$this->data               = array();
			$this->data['support']    = $data['support'];
			$this->data['docs']       = $data['docs'];
			$this->data['review']     = $data['review'];
			$this->data['css_url']    = $data['css'];
			$this->data['images_url'] = $data['image'];
			$this->data['slug']       = $data['slug'];
			$this->data['menu_slug']  = $data['menu_slug'];
			$this->data['version']    = isset( $data['version'] ) ? $data['version'] : '1.0.0';
			$this->data['pro_url']    = isset( $data['pro_url'] ) ? $data['pro_url'] : '';
			$this->plugin_base_name   = "{$this->data['slug']}/{$this->data['slug']}.php";
			add_action( 'villatheme_support_' . $this->data['slug'], array( $this, 'villatheme_support' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'admin_notices', array( $this, 'review_notice' ) );
			add_action( 'admin_init', array( $this, 'hide_review_notice' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9999 );
			add_filter( 'plugin_action_links_' . $this->plugin_base_name, array( $this, 'link_to_pro' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			/*Admin notices*/
			if ( ! get_transient( 'villatheme_call' ) || get_transient( 'villatheme_call' ) == $this->data['slug'] ) {
				set_transient( 'villatheme_call', $this->data['slug'], 86400 );
				/*Hide notices*/
				add_action( 'admin_init', array( $this, 'hide_notices' ) );

				add_action( 'admin_notices', array( $this, 'form_ads' ) );

				/*Admin dashboard*/
				add_action( 'wp_dashboard_setup', array( $this, 'dashboard' ) );
			}
		}

		/**Add link to Documentation, Support and Reviews
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( $this->plugin_base_name === $file ) {
				$row_meta = array(
					'support' => '<a href="' . $this->data['support'] . '" target="_blank" title="' . esc_attr( 'VillaTheme Support' ) . '">' . esc_html( 'Support' ) . '</a>',
					'review'  => '<a href="' . $this->data['review'] . '" target="_blank" title="' . esc_attr( 'Rate this plugin' ) . '">' . esc_html( 'Reviews' ) . '</a>',
				);
				if ( ! empty( $this->data['docs'] ) ) {
					$row_meta['docs'] = '<a href="' . $this->data['docs'] . '" target="_blank" title="' . esc_attr( 'Plugin Documentation' ) . '">' . esc_html( 'Docs' ) . '</a>';
				}

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * @param $links
		 *
		 * @return mixed
		 */
		public function link_to_pro( $links ) {
			if ( ! empty( $this->data['pro_url'] ) ) {
				$link = '<a class="villatheme-button-upgrade" href="' . $this->data['pro_url'] . '" target="_blank" title="' . esc_attr( 'Upgrade plugin to premium version' ) . '">' . esc_html( 'Upgrade' ) . '</a>';
				array_unshift( $links, $link );
			}

			return $links;
		}

		/**wp_remote_get
		 *
		 * @param $url
		 *
		 * @return array
		 */
		public function wp_remote_get( $url ) {
			$return  = array(
				'status' => '',
				'data'   => '',
			);
			$request = wp_remote_get( $url, array(
				'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
				'timeout'    => 10,
			) );

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$return['status'] = 'success';
				$return['data']   = $request['body'];
			} else {
				$return['status'] = 'error';
				$return['data']   = $request->get_error_message();
			}

			return $return;
		}

		/**
		 * Add Extension page
		 */
		function admin_menu() {
			add_submenu_page( $this->data['menu_slug'], esc_html( 'Extensions' ), esc_html( 'Extensions' ), 'manage_options', $this->data['slug'] . '-extensions', array(
				$this,
				'page_callback'
			) );
			if ( $this->data['menu_slug'] && $this->data['pro_url'] ) {
				global $submenu;
				$submenu[ $this->data['menu_slug'] ][] = array(
					esc_html( 'Try Premium Version' ),
					'manage_options',
					$this->data['pro_url']
				);
			}
		}

		/**
		 * Extensions page
		 * @return bool
		 */
		public function page_callback() { ?>
			<div class="villatheme-extension-page">
				<div class="villatheme-extension-top">
					<h2><?php echo esc_html( 'THE BEST PLUGINS FOR WOOCOMMERCE' ) ?></h2>
					<p><?php echo esc_html( 'Our plugins are constantly updated and thanks to your feedback. We add new features on a daily basis. Try our live demo and start increasing the conversions on your ecommerce right away.' ) ?></p>
				</div>
				<div class="villatheme-extension-content villatheme-dashboad">
					<?php
					$feeds = get_transient( 'villatheme_ads' );
					$ads   = '';
					if ( ! $feeds ) {
						$request_data = $this->wp_remote_get( 'https://villatheme.com/wp-json/info/v1' );
						if ( $request_data['status'] === 'success' ) {
							$ads = $request_data['data'];
						}
						set_transient( 'villatheme_ads', $ads, 86400 );
					} else {
						$ads = $feeds;
					}
					if ( $ads ) {
						$ads = json_decode( $ads );
						$ads = array_filter( $ads );
					} else {
						return false;
					}
					if ( is_array( $ads ) && count( $ads ) ) {
						foreach ( $ads as $ad ) {
							?>
							<div class="villatheme-col-3">
								<?php if ( $ad->image ) { ?>
									<div class="villatheme-item-image">
										<img src="<?php echo esc_url( $ad->image ) ?>">

									</div>
								<?php } ?>
								<?php if ( $ad->title ) { ?>
									<div class="villatheme-item-title">
										<?php if ( @$ad->link ) { ?>
										<a target="_blank"
										   href="<?php echo esc_url( $ad->link ) ?>">
											<?php } ?>
											<?php echo esc_html( $ad->title ) ?>
											<?php if ( @$ad->link ) { ?>
										</a>
									<?php } ?>

									</div>
								<?php } ?>
								<div class="villatheme-item-controls">
									<div class="villatheme-item-controls-inner">
										<?php if ( @$ad->link ) { ?>
											<a class="button button-primary" target="_blank"
											   href="<?php echo esc_url( $ad->link ) ?>"><?php echo esc_html( 'Download' ) ?></a>
										<?php }
										if ( @$ad->demo_url ) { ?>
											<a class="button" target="_blank"
											   href="<?php echo esc_url( $ad->demo_url ) ?>"><?php echo esc_html( 'Demo' ) ?></a>
										<?php }
										if ( @$ad->free_url ) { ?>
											<a class="button" target="_blank"
											   href="<?php echo esc_url( $ad->free_url ) ?>"><?php echo esc_html( 'Trial' ) ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php }
					} ?>
				</div>
			</div>
		<?php }

		/**
		 * Hide notices
		 */
		public function hide_review_notice() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$_villatheme_nonce = isset( $_GET['_villatheme_nonce'] ) ? wp_unslash( sanitize_text_field( $_GET['_villatheme_nonce'] ) ) : '';
			if ( empty( $_villatheme_nonce ) ) {
				return;
			}
			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_dismiss_notices' ) ) {
				update_option( $this->data['slug'] . '_dismiss_notices', 1 );
			}
			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_hide_notices' ) ) {
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 2592000 );
			}
			if ( wp_verify_nonce( $_villatheme_nonce, $this->data['slug'] . '_wp_reviewed' ) ) {
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 2592000 );
				update_option( $this->data['slug'] . '_wp_reviewed', 1 );
				ob_start();
				ob_end_clean();
				wp_redirect( $this->data['review'] );
				die;
			}
		}

		/**
		 * Show review wordpress
		 */
		public function review_notice() {
			if ( get_option( $this->data['slug'] . '_dismiss_notices', 0 ) ) {
				return;
			}
			if ( get_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices' ) ) {
				return;
			}
			$name         = str_replace( '-', ' ', $this->data['slug'] );
			$name         = ucwords( $name );
			$check_review = get_option( $this->data['slug'] . '_wp_reviewed', 0 );
			$check_start  = get_option( $this->data['slug'] . '_start_use', 0 );
			if ( ! $check_start ) {
				update_option( $this->data['slug'] . '_start_use', 1 );
				set_transient( $this->data['slug'] . $this->data['version'] . '_hide_notices', 1, 259200 );

				return;
			}
			if ( $check_review && ! $this->data['pro_url'] ) {
				return;
			}
			?>

			<div class="villatheme-dashboard updated" style="border-left: 4px solid #ffba00">
				<div class="villatheme-content">
					<form action="" method="get">
						<?php if ( ! $check_review ) { ?>
							<p><?php echo esc_html( 'Hi there! You\'ve been using ' ) . '<strong>' . esc_html( $name ) . '</strong>' . esc_html( ' on your site for a few days - I hope it\'s been helpful. If you\'re enjoying my plugin, would you mind rating it 5-stars to help spread the word?' ) ?></p>
						<?php } else { ?>
							<p><?php echo esc_html( 'Hi there! You\'ve been using ' ) . '<strong>' . esc_html( $name ) . '</strong>' . esc_html( ' on your site for a few days - I hope it\'s been helpful. Would you want get more features?' ) ?></p>
						<?php } ?>
						<p>
							<a href="<?php echo esc_url( wp_nonce_url( @add_query_arg( array() ), $this->data['slug'] . '_hide_notices', '_villatheme_nonce' ) ); ?>"
							   class="button"><?php echo esc_html( 'Thanks, later' ) ?></a>
							<?php if ( ! $check_review ) { ?>
								<button class="button button-primary"><?php echo esc_html( 'Rate Now' ) ?></button>
								<?php wp_nonce_field( $this->data['slug'] . '_wp_reviewed', '_villatheme_nonce' ) ?>
							<?php } ?>
							<?php if ( $this->data['pro_url'] ) { ?>
								<a target="_blank" href="<?php echo esc_url( $this->data['pro_url'] ); ?>"
								   class="button button-primary"><?php echo esc_html( 'Try Premium Version' ) ?></a>
							<?php } ?>
							<a target="_self"
							   href="<?php echo esc_url( wp_nonce_url( @add_query_arg( array() ), $this->data['slug'] . '_dismiss_notices', '_villatheme_nonce' ) ); ?>"
							   class="button notice-dismiss vi-button-dismiss"><?php echo esc_html( 'Dismiss' ) ?></a>
						</p>
					</form>
				</div>

			</div>
		<?php }

		/**
		 * Dashboard widget
		 */
		public function dashboard() {
			global $current_user;
			$hide = get_transient( 'villatheme_hide_notices_' . $current_user->ID );
			if ( $hide ) {
				return;
			}
			wp_add_dashboard_widget( 'villatheme_dashboard_status', esc_html( 'VillaTheme News' ), array(
				$this,
				'widget'
			) );
		}

		public function widget() {

			$default = array(
				'heading'     => '',
				'description' => '',
				'link'        => ''
			);
			$data    = get_transient( 'villatheme_notices' );
			$called  = get_transient( 'villatheme_called' );

			if ( ! $data && ! $called ) {

				$request_data = $this->wp_remote_get( 'https://villatheme.com/notices.php' );
				if ( $request_data['status'] === 'success' ) {
					@$data = json_decode( $request_data['data'], true );
				}
				set_transient( 'villatheme_notices', $data, 86400 );
			}
			if ( ! $called ) {
				set_transient( 'villatheme_called', 1, 86400 );
			}
			if ( ! is_array( $data ) ) {

				return;
			}
			$data = wp_parse_args( $data, $default );
			if ( ! $data['heading'] && ! $data['description'] ) {
				echo esc_html( 'There are no events yet.' );

				return;
			} ?>
			<div class="villatheme-dashboard">
				<div class="villatheme-content">
					<?php if ( $data['heading'] ) { ?>
						<h3><?php echo esc_html( $data['heading'] ) ?></h3>
					<?php } ?>

					<?php if ( $data['description'] ) { ?>
						<p><?php echo esc_html( $data['description'] ) ?></p>
					<?php } ?>
					<p>
						<?php if ( $data['link'] ) { ?>
							<a target="_blank" href="<?php echo esc_url( $data['link'] ); ?>"
							   class="button button-primary"><?php echo esc_html( 'Get Your Gift' ) ?></a>
						<?php } ?>

					</p>
				</div>

			</div>

		<?php }

		/**
		 * Hide notices
		 */
		public function hide_notices() {
			global $current_user;

			$_villatheme_nonce = isset( $_GET['_villatheme_nonce'] ) ? wp_unslash( sanitize_text_field( $_GET['_villatheme_nonce'] ) ) : '';
			$hide_notice       = isset( $_GET['villatheme-hide-notice'] ) ? wp_unslash( sanitize_text_field( $_GET['villatheme-hide-notice'] ) ) : '';
			if ( empty( $_villatheme_nonce ) && empty( $hide_notice ) ) {
				return;
			}
			if ( wp_verify_nonce( $_villatheme_nonce, 'hide_notices' ) ) {
				if ( $hide_notice == 1 ) {
					set_transient( 'villatheme_hide_notices_' . $current_user->ID, 1, 86400 );
				} else {
					set_transient( 'villatheme_hide_notices_' . $current_user->ID, 1, 86400 * 30 );

				}
			}
		}

		/**
		 * Show Notices
		 */
		public function form_ads() {
			global $current_user;
			$hide = get_transient( 'villatheme_hide_notices_' . $current_user->ID );
			if ( $hide ) {
				return;
			}
			$default = array(
				'heading'     => '',
				'description' => '',
				'link'        => ''
			);

			$data   = get_transient( 'villatheme_notices' );
			$called = get_transient( 'villatheme_called' );

			if ( ! $data && ! $called ) {
				$request_data = $this->wp_remote_get( 'https://villatheme.com/notices.php' );
				if ( $request_data['status'] === 'success' ) {
					@$data = json_decode( $request_data['data'], true );
				}
				set_transient( 'villatheme_notices', $data, 86400 );
			}
			if ( ! $called ) {
				set_transient( 'villatheme_called', 1, 86400 );
			}

			if ( ! is_array( $data ) ) {
				return;
			}
			$data = wp_parse_args( $data, $default );
			if ( ! $data['heading'] && ! $data['description'] ) {
				return;
			}
			ob_start(); ?>
			<div class="villatheme-dashboard updated">
				<div class="villatheme-content">

					<?php if ( $data['heading'] ) { ?>
						<h3><?php echo esc_html( $data['heading'] ) ?></h3>
					<?php } ?>

					<?php if ( $data['description'] ) { ?>
						<p><?php echo esc_html( $data['description'] ) ?></p>
					<?php } ?>
					<p>
						<a target="_self"
						   href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'villatheme-hide-notice', '2' ), 'hide_notices', '_villatheme_nonce' ) ); ?>"
						   class="button notice-dismiss vi-button-dismiss"><?php echo esc_html( 'Dismiss' ) ?></a>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'villatheme-hide-notice', '1' ), 'hide_notices', '_villatheme_nonce' ) ); ?>"
						   class="button"><?php echo esc_html( 'Thanks, later.' ) ?></a>
						<?php if ( $data['link'] ) { ?>
							<a target="_blank" href="<?php echo esc_url( $data['link'] ); ?>"
							   class="button button-primary"><?php echo esc_html( 'Get Your Gift' ) ?></a>
						<?php } ?>

					</p>

				</div>

			</div>

			<?php $html = ob_get_clean();
			$html       = apply_filters( 'form_ads_data', $html );
			echo $html;
		}

		/**
		 * Init script
		 */
		public function scripts() {
			wp_enqueue_style( 'villatheme-support', $this->data['css_url'] . 'villatheme-support.css' );
		}

		/**
		 *
		 */
		public function villatheme_support() { ?>

			<div id="villatheme-support" class="vi-ui form segment">
				<h3><?php echo esc_html( 'MAYBE YOU LIKE' ) ?>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<a class="vi-ui button labeled icon" target="_blank"
					   href="<?php echo esc_url( $this->data['docs'] ) ?>">
						<i class="book icon"></i>
						<?php echo esc_html( 'Documentation' ) ?>
					</a>
					<a class="vi-ui button inverted labeled icon orange" target="_blank"
					   href="<?php echo esc_url( $this->data['review'] ) ?>">
						<i class="star icon"></i>
						<?php echo esc_html( 'Review' ) ?>
					</a>
					<a class="vi-ui  button labeled icon green" target="_blank"
					   href="<?php echo esc_url( $this->data['support'] ) ?>">
						<i class="users icon"></i>
						<?php echo esc_html( 'Request Support' ) ?>
					</a>
				</h3>
				<div class="fields">

					<?php $items = $this->get_data( $this->data['slug'] );
					if ( is_array( $items ) && count( $items ) ) {
						shuffle( $items );
						$items = array_slice( $items, 0, 4 );
						foreach ( $items as $k => $item ) { ?>
							<div class="four wide field">
								<div class="villatheme-item">
									<a target="_blank" href="<?php echo esc_url( $item->link ) ?>">
										<img src="<?php echo esc_url( $item->image ) ?>" />
									</a>
								</div>
							</div>
						<?php }
						?>

					<?php } ?>
				</div>

			</div>
		<?php }

		/**
		 * Get data from server
		 * @return array
		 */
		protected function get_data( $slug = false ) {
			$feeds   = get_transient( 'villatheme_ads' );
			$results = array();
			$ads     = '';
			if ( ! $feeds ) {
				$request_data = $this->wp_remote_get( 'https://villatheme.com/wp-json/info/v1' );
				if ( $request_data['status'] === 'success' ) {
					@$ads = $request_data['data'];
				}
				set_transient( 'villatheme_ads', $ads, 86400 );
			} else {
				$ads = $feeds;
			}
			if ( $ads ) {
				$ads = json_decode( $ads );
				$ads = array_filter( $ads );
			} else {
				return false;
			}
			if ( is_array( $ads ) && count( $ads ) ) {
				$theme_select = null;
				foreach ( $ads as $ad ) {
					if ( $slug ) {
						if ( $ad->slug == $slug ) {
							continue;
						}
					}
					$item        = new stdClass();
					$item->title = $ad->title;
					$item->link  = $ad->link;
					$item->thumb = $ad->thumb;
					$item->image = $ad->image;
					$item->desc  = $ad->description;
					$results[]   = $item;
				}
			} else {
				return false;
			}
			if ( is_array( $results ) && count( $results ) ) {
				return $results;
			} else {
				return false;
			}
		}
	}
}
