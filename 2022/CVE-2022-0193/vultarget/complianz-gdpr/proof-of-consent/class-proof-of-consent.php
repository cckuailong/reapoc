<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! class_exists( "cmplz_proof_of_consent" ) ) {
	class cmplz_proof_of_consent {
		private static $_this;
		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}
			self::$_this = $this;

			if ( cmplz_get_value('records_of_consent') !== 'yes' || defined('cmplz_free') ) {
				add_action( 'cmplz_admin_menu', array( $this, 'menu_item' ), 10 );
				add_action( 'wp_ajax_cmplz_delete_snapshot', array( $this, 'ajax_delete_snapshot' ) );
			}

			add_action( 'admin_init', array( $this, 'force_snapshot_generation' ) );
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));

		}

		static function this() {
			return self::$_this;
		}

		/**
		 * Enqueue back-end assets
		 * @param $hook
		 */
		public function admin_enqueue($hook){
			if (!isset($_GET['page']) || $_GET['page'] !== 'cmplz-proof-of-consent' ) return;
			$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
			wp_register_style('cmplz-posttypes', cmplz_url . "assets/css/posttypes$min.css", false, cmplz_version);
			wp_enqueue_style('cmplz-posttypes');
		}

		/**
		 * Get start or end date timestamp
		 * @param int    $year
		 * @param int    $month
		 * @param string $type
		 *
		 * @return int
		 */
		public function get_time_stamp_for_date( $year, $month, $type = 'start_date' ){
			if ( $year != 0 && $month != 0 ) {
				$day = '1';
				$month = intval($month);
				$year = intval($year) ;
				$t = '00:00:00';

				if ( $type === 'end_date' ) {
					$month = $month + 1;
				}
				return DateTime::createFromFormat("Y-m-d H:i:s", "$year-$month-$day $t")->getTimestamp();
			}
			return 0;
		}

		/**
		 * Get list of cookie statement snapshots
		 * @param array $args
		 *
		 * @return array|false
		 */

		public function get_cookie_snapshot_list( $args = array() ) {
			$defaults   = array(
				'number' => 10,
				'region' => false,
				'offset' => 0,
				'order'  => 'DESC',
				'start_date'    => 0,
				'end_date'      => 9999999999999,
			);
			$args       = wp_parse_args( $args, $defaults );
			$uploads    = wp_upload_dir();
			$upload_dir = $uploads['basedir'];
			$upload_url = $uploads['baseurl'];
			$path       = $upload_dir . '/complianz/snapshots/';
			$url        = $upload_url . '/complianz/snapshots/';
			$filelist   = array();
			$extensions = array( "pdf" );
			$index = 0;
			if ( file_exists( $path ) && $handle = opendir( $path ) ) {
				while ( false !== ( $file = readdir( $handle ) ) ) {
					if ( $file != "." && $file != ".." ) {
						$file = $path . $file;
						$ext  = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
						if ( is_file( $file ) && in_array( $ext, $extensions ) ) {

							if ( $args['region'] && strpos(basename($file), $args['region'].'-proof-of-consent') === false ) {
								continue;
							}

							if ( empty( $args['search'] ) || strpos( $file, $args['search'] ) !== false) {
								$index++;
								if ($args['start_date'] < filemtime( $file ) && filemtime( $file ) < $args['end_date'] ) {
									$filelist[filemtime($file) . $index]["path"] = $file;
									$filelist[filemtime($file) . $index]["url"]  = trailingslashit($url).basename($file);
									$filelist[filemtime($file) . $index]["file"] = basename($file);
									$filelist[filemtime($file) . $index]["time"] = filemtime($file);
								}
							}
						}
					}
				}
				closedir( $handle );
			}

			if ( $args['order'] === 'DESC' ) {
				krsort( $filelist );
			} else {
				ksort( $filelist );
			}

			if ( empty( $filelist ) ) {
				return false;
			}

			$page       = (int) $args['offset'];
			$total      = count( $filelist ); //total items in array
			$limit      = $args['number'];
			$totalPages = ceil( $total / $limit ); //calculate total pages
			$page       = max( $page, 1 ); //get 1 page when $_GET['page'] <= 0
			$page       = min( $page, $totalPages ); //get last page when $_GET['page'] > $totalPages
			$offset     = ( $page - 1 ) * $limit;
			if ( $offset < 0 ) {
				$offset = 0;
			}

			$filelist = array_slice( $filelist, $offset, $limit );

			if ( empty( $filelist ) ) {
				return false;
			}

			return $filelist;

		}


		/**
		 * Forces generation of a snapshot for today, triggered by the button
		 *
		 */

		public function force_snapshot_generation() {
			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			if ( isset( $_POST["cmplz_generate_snapshot"] )
			     && isset( $_POST["cmplz_nonce"] )
			     && wp_verify_nonce( $_POST['cmplz_nonce'],
					'cmplz_generate_snapshot' )
			) {
				COMPLIANZ::$proof_of_consent->generate_cookie_policy_snapshot( $force = true );
			}
		}

		/**
		 * Delete a snapshot
		 */

		public function ajax_delete_snapshot() {

			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			if ( isset( $_POST['snapshot_id'] ) ) {
				$this->delete_snapshot( $_POST['snapshot_id'] );
				$response   = json_encode( array(
					'success' => true,
				) );
				header( "Content-Type: application/json" );
				echo $response;
				exit;
			}
		}

		/**
		 * @param string $filename
		 */

		public function delete_snapshot( $filename ){
			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			$uploads    = wp_upload_dir();
			$upload_dir = $uploads['basedir'];
			$path       = $upload_dir . '/complianz/snapshots/';
			$success    = unlink( $path . sanitize_file_name( $filename ) );
		}

		/**
		 * Add submenu items
		 */

		public function menu_item() {
			//if (!cmplz_user_can_manage()) return;
			add_submenu_page(
				'complianz',
				__( 'Proof of consent', 'complianz-gdpr' ),
				__( 'Proof of consent', 'complianz-gdpr' ),
				'manage_options',
				"cmplz-proof-of-consent",
				array( $this, 'cookie_statement_snapshots' )
			);
		}

		/**
		 * Render proof of consent table
		 */

		public function cookie_statement_snapshots() {
			include( cmplz_path . 'proof-of-consent/class-cookiestatement-snapshot-table.php' );
			$snapshots_table = new cmplz_CookieStatement_Snapshots_Table();
			$snapshots_table->prepare_items();
			?>
			<script>
				jQuery(document).ready(function ($) {
					$(document).on('click', '.cmplz-delete-snapshot', function (e) {

						e.preventDefault();
						var btn = $(this);
						btn.closest('tr').css('background-color', 'red');
						var delete_snapshot_id = btn.data('id');
						$.ajax({
							type: "POST",
							url: '<?php echo admin_url( 'admin-ajax.php' )?>',
							dataType: 'json',
							data: ({
								action: 'cmplz_delete_snapshot',
								snapshot_id: delete_snapshot_id
							}),
							success: function (response) {
								if (response.success) {
									btn.closest('tr').remove();
								}
							}
						});

					});
				});
			</script>
			<div class="wrap">
			<div id="cookie-policy-snapshots" class="wrap cookie-snapshot">
				<form id="cmplz-cookiestatement-snapshot-generate" method="POST" action="">
					<h1 class="wp-heading-inline"><?php _e( "Proof of consent", 'complianz-gdpr' ) ?></h1>
					<?php echo wp_nonce_field( 'cmplz_generate_snapshot',
						'cmplz_nonce' ); ?>
					<input type="submit" class="button button-primary cmplz-header-btn"
					       name="cmplz_generate_snapshot"
					       value="<?php _e( "Generate now",
						       "complianz-gdpr" ) ?>"/>
					<a href="https://complianz.io/definitions/what-is-proof-of-consent/" target="_blank" class="button button-default cmplz-header-btn"><?php _e( "Read more", "complianz-gdpr" ) ?></a>
				</form>
				<?php
				if ( isset( $_POST['cmplz_generate_snapshot_error'] ) ) {
					cmplz_notice( __( "Proof of consent generation failed. Check your write permissions in the uploads directory",
							"complianz-gdpr" ), 'warning' );
				}
				?>
				<form id="cmplz-cookiestatement-snapshot-filter" method="get"
				      action="">

					<?php
					$snapshots_table->search_box( __( 'Filter', 'complianz-gdpr' ), 'cmplz-cookiesnapshot' );
					$snapshots_table->date_select();
					$snapshots_table->display();
					?>
					<input type="hidden" name="page" value="cmplz-proof-of-consent"/>
				</form>
				<?php do_action( 'cmplz_after_cookiesnapshot_list' ); ?>
			</div>
			</div>
			<?php
		}

		/**
		 * Generate the cookie policy snapshot
		 * @param bool $force
		 */

		public function generate_cookie_policy_snapshot( $force = false ) {
			if ( ! $force
			     && ! get_option( 'cmplz_generate_new_cookiepolicy_snapshot' )
			) {
				return;
			}

			$regions = cmplz_get_regions();
			foreach ( $regions as $region => $label ) {
				$banner_id = cmplz_get_default_banner_id();
				$banner    = new CMPLZ_COOKIEBANNER( $banner_id );
				$settings  = $banner->get_settings_array();
				$settings['privacy_link_us '] = COMPLIANZ::$document->get_page_url( 'privacy-statement', 'us' );
				$settings_html = '';
				$skip          = array(
					'categorie',
					'use_custom_cookie_css',
					'custom_css_amp',
					'static',
					'set_cookies',
					'hide_revoke',
					'position',
					'theme',
					'version',
					'banner_version',
					'a_b_testing',
					'title',
					'privacy_link',
					'nonce',
					'url',
					'current_policy_id',
					'type',
					'layout',
					'use_custom_css',
					'custom_css',
					'banner_width',
                    'colorpalette_background',
                    'colorpalette_text',
                    'colorpalette_toggles',
                    'colorpalette_border_radius',
                    'border_width',
                    'colorpalette_button_accept',
                    'colorpalette_button_deny',
                    'colorpalette_button_settings',
                    'buttons_border_radius',
				);
				$cats_pattern = '/data-category="(.*?)"/i';
				if (isset($settings['categories'])) {
					if ( preg_match_all( $cats_pattern, $settings['categories'],
						$matches )
					) {
						$categories = $matches[1];
						foreach($categories as $index => $category ) {
							$category = str_replace('cmplz_', '', $category);
							if (is_numeric(intval($category))) {
								$category = 'Custom event ' . $category;
							}
							$categories[$index] = $category;
						}
						$settings['categories'] =  implode(', ', $categories);
					}
				}

				unset( $settings["readmore_url"] );
				$settings = apply_filters( 'cmplz_cookie_policy_snapshot_settings' ,$settings );

				foreach ( $settings as $key => $value ) {

					if ( in_array( $key, $skip ) ) {
						continue;
					}
					if (is_array($value)) $value = implode(',', $value);
					$settings_html .= '<li>' . $key . ' => ' . esc_html( $value ) . '</li>';
				}

				$settings_html = '<div><h1>' . __( 'Cookie consent settings', 'complianz-gdpr' ) . '</h1><ul>' . ( $settings_html ) . '</ul></div>';
				$intro         = '<h1>' . __( "Proof of Consent",
						"complianz-gdpr" ) . '</h1>
                     <p>' . sprintf( __( "This document was generated to show efforts made to comply with privacy legislation.
                            This document will contain the Cookie Policy and the cookie consent settings to proof consent
                            for the time and region specified below. For more information about this document, please go
                            to %shttps://complianz.io/consent%s.",
						"complianz-gdpr" ),
						'<a target="_blank" href="https://complianz.io/consent">',
						"</a>" ) . '</p>';
				COMPLIANZ::$document->generate_pdf( 'cookie-statement', $region, false, true, $intro, $settings_html );
				do_action('cmplz_after_proof_of_consent_generation', get_option( 'cmplz_generate_new_cookiepolicy_snapshot') );
			}


			update_option( 'cmplz_generate_new_cookiepolicy_snapshot', false );
		}
	}
} //class closure
