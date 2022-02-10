<?php
defined( 'ABSPATH' ) or die( );

if ( ! class_exists( "cmplz_admin" ) ) {
	class cmplz_admin {
		private static $_this;
		public $error_message = "";
		public $success_message = "";

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'admin_menu', array( $this, 'register_admin_page' ), 20 );

			$plugin = cmplz_plugin;
			add_filter( "plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ) );

			add_action( "in_plugin_update_message-{$plugin}", array( $this, 'plugin_update_message'), 10, 2 );
			add_filter( "auto_update_plugin", array( $this, 'override_auto_updates'), 99, 2 );

			//multisite
			add_filter( "network_admin_plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ) );

			add_action( 'admin_init', array( $this, 'process_reset_action' ), 10, 1 );
			add_action('cmplz_fieldvalue', array($this, 'filter_cookie_domain'), 10, 2);
			add_action( 'wp_ajax_cmplz_dismiss_warning', array( $this, 'dismiss_warning' ) );
			add_action( 'wp_ajax_cmplz_load_warnings', array( $this, 'ajax_load_warnings' ) );
			add_action( 'wp_ajax_cmplz_load_gridblock', array( $this, 'ajax_load_gridblock' ) );
		}

		static function this() {
			return self::$_this;
		}

		/**
		 * Check if current day falls within required date range.
		 *
		 * @return bool
		 */
		public function is_bf(){
			if ( defined("cmplz_premium" ) ) {
				return false;
			}
			$start_day = 22;
			$end_day = 29;
			$current_year = date("Y");//e.g. 2021
			$current_month = date("n");//e.g. 3
			$current_day = date("j");//e.g. 4

			if ( $current_year == 2021 &&
				 $current_month == 11 &&
				 $current_day >=$start_day &&
				 $current_day <= $end_day
			) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Hooked into ajax call to dismiss a warning
		 * @hooked wp_ajax_cmplz_dismiss_warning
		 */

		public function dismiss_warning() {
			$error   = false;

			if ( ! is_user_logged_in() ) {
				$error = true;
			}

			if ( !isset($_POST['id']) ) {
				$error = true;
			}

			if ( !$error ) {
				$warning_id = sanitize_title($_POST['id']);
				$dismissed_warnings = get_option( 'cmplz_dismissed_warnings', array() );
				if ( !in_array($warning_id, $dismissed_warnings) ) {
					$dismissed_warnings[] = $warning_id;
				}
				update_option('cmplz_dismissed_warnings', $dismissed_warnings );
			}

			$out = array(
					'success' => ! $error,
			);

			die( json_encode( $out ) );
		}

		/**
		 * Hooked into ajax call to load the warnings
		 * @hooked wp_ajax_cmplz_load_warnings
		 */

		public function ajax_load_warnings() {
			$error   = false;
			$html = '';
			$remaining_count = $all_count = 0;
			if ( ! is_user_logged_in() ) {
				$error = true;
			}

			if ( !isset($_GET['status']) ) {
				$error = true;
			}

			if (!$error) {
				$all_count = count( $this->get_warnings(array( 'cache' => false ) ) );
				$remaining_count = count( $this->get_warnings(array(
						'cache' => false,
						'status' => array('urgent', 'open'),
				) ) );

				$html = cmplz_get_template('dashboard/progress.php');
			}


			$out = array(
					'success' => ! $error,
					'html' => $html,
					'count_all' => $all_count,
					'count_remaining' => $remaining_count,
			);

			die( json_encode( $out ) );
		}


		/**
		 *
		 */
		public function ajax_load_gridblock() {
			$error   = false;
			$html = '';
			if ( ! is_user_logged_in() ) {
				$error = true;
			}

			if (!isset($_GET['template'])) {
				$error = true;
			}

			if (!$error) {
				$template = sanitize_title($_GET['template']);
				$html = cmplz_get_template("dashboard/$template.php");
			}

			$out = array(
					'success' => ! $error,
					'html' => $html,
			);

			die( json_encode( $out ) );
		}

		/**
		 * Sanitize the cookiedomain
		 * @param string $fieldname
		 * @param string $fieldvalue
		 *
		 * @return string|string[]
		 */

		public function filter_cookie_domain( $fieldvalue, $fieldname ){
			if (!current_user_can('manage_options')) return $fieldvalue;
			//sanitize the cookie domain
			if ( ( $fieldname === 'cmplz_cookie_domain' && strlen($fieldvalue)>0 )
			) {
				$fieldvalue = str_replace(array("https://", "http://", "www."), "", $fieldvalue);
			}

			return $fieldvalue;
		}

		/**
		 * process the reset
		 */

		public function process_reset_action() {

			if ( ! isset( $_POST['cmplz_reset_settings'] ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( ! isset( $_POST['cmplz_nonce'] )
			     || ! wp_verify_nonce( $_POST['cmplz_nonce'],
					'complianz_save' )
			) {
				return;
			}

			$options = array(
				'cmplz_activation_time',
				'cmplz_review_notice_shown',
				"cmplz_wizard_completed_once",
				'complianz_options_settings',
				'complianz_options_wizard',
				'complianz_options_dataleak',
				'complianz_options_processing',
				'complianz_active_policy_id',
				'complianz_scan_token',
				'cmplz_license_notice_dismissed',
				'cmplz_license_key',
				'cmplz_license_status',
				'cmplz_changed_cookies',
				'cmplz_plugins_changed',
				'cmplz_detected_stats',
				'cmplz_deleted_cookies',
				'cmplz_reported_cookies',
				'cmplz_sync_cookies_complete',
				'cmplz_sync_services_complete',
				'cmplz_detected_social_media',
				'cmplz_detected_thirdparty_services',
				'cmplz_run_cdb_sync_once',

			);


			foreach ( $options as $option_name ) {
				delete_option( $option_name );
				delete_site_option( $option_name );
			}

			global $wpdb;
			$table_names = array(
				$wpdb->prefix . 'cmplz_statistics',
				$wpdb->prefix . 'cmplz_cookies',
				$wpdb->prefix . 'cmplz_services'
			);

			foreach ( $table_names as $table_name ) {
				if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" )
				     === $table_name
				) {
					$wpdb->query( "TRUNCATE TABLE $table_name" );
				}
			}

			$banners = cmplz_get_cookiebanners( array( 'status' => 'all' ) );
			foreach ( $banners as $banner ) {
				$banner = new CMPLZ_COOKIEBANNER( $banner->ID );
				$banner->delete( true );
			}


			$this->success_message = __( 'Data successfully cleared',
				'complianz-gdpr' );
		}

		/**
		 * Add a major changes notice to the plugin updates message
		 * @param $plugin_data
		 * @param $response
		 */
		public function plugin_update_message($plugin_data, $response){
			if ( strpos($response->slug , 'complianz') !==false && $response->new_version === '6.0.0' ) {
				echo '<br><b>' . '&nbsp'.sprintf(__("Important: Please %sread about%s Complianz 6.0 before updating. This is a major release and includes changes and new features that might need your attention.").'</b>','<a target="_blank" href="https://complianz.io/upgrade-to-complianz-6-0/">','</a>');
			}
		}

		/**
		 * If this update is to 6, don't auto update
		 *
		 * @param $update
		 * @param $item
		 *
		 * @return false|mixed
		 */
		public function override_auto_updates( $update, $item ) {
			if ( strpos($item->slug , 'complianz') !==false && version_compare($item->new_version, '6.0.0', '>=') ) {
				return false;
			}
			return $update;
		}

		/**
		 * Enqueue some assets
		 *
		 * @param $hook
		 */
		public function enqueue_assets( $hook ) {
			if ( ( strpos( $hook, 'complianz' ) === false )
			     && strpos( $hook, 'cmplz' ) === false
			) {
				return;
			}

			$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_register_style( 'cmplz', trailingslashit( cmplz_url ) . "assets/css/admin$minified.css", "", cmplz_version );
			wp_enqueue_style( 'cmplz' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'cmplz-ace', cmplz_url . "assets/ace/ace.js", array(), cmplz_version, false );
			wp_enqueue_script( 'cmplz-dashboard', cmplz_url . "assets/js/dashboard$minified.js", array( 'jquery' ), cmplz_version, true );
			wp_enqueue_script( 'cmplz-admin', cmplz_url . "assets/js/admin$minified.js", array( 'jquery', 'wp-color-picker' ), cmplz_version, true );
			$sync_progress = COMPLIANZ::$cookie_admin->get_sync_progress();
			$progress      = COMPLIANZ::$cookie_admin->get_progress_count();
			wp_localize_script(
				'cmplz-admin',
				'complianz_admin',
				array(
					'admin_url'    => admin_url( 'admin-ajax.php' ),
					'progress'     => $progress,
					'syncProgress' => $sync_progress,
				)
			);
		}

		/**
		 * Add custom link to plugins overview page
		 *
		 * @hooked plugin_action_links_$plugin
		 *
		 * @param array $links
		 *
		 * @return array $links
		 */

		public function plugin_settings_link( $links ) {
			$settings_link = '<a href="'
			                 . admin_url( "admin.php?page=complianz" )
			                 . '" class="cmplz-settings-link">'
			                 . __( "Settings", 'complianz-gdpr' ) . '</a>';
			array_unshift( $links, $settings_link );

			$support_link = defined( 'cmplz_free' )
				? "https://wordpress.org/support/plugin/complianz-gdpr"
				: "https://complianz.io/support";
			$faq_link     = '<a target="_blank" href="' . $support_link . '">'
			                . __( 'Support', 'complianz-gdpr' ) . '</a>';
			array_unshift( $links, $faq_link );

			if ( ! defined( 'cmplz_premium' ) ) {
				$upgrade_link
					= '<a style="color:#2DAAE1;font-weight:bold" target="_blank" href="https://complianz.io/l/pricing">'
					  . __( 'Upgrade to premium', 'complianz-gdpr' ) . '</a>';
				array_unshift( $links, $upgrade_link );
			}

			return $links;
		}



		/**
		 * get a list of applicable warnings.
		 *
		 * @param array $args
		 *
		 * @return array
		 */

		public function get_warnings( $args = array() ) {
			$defaults = array(
				'cache' => true,
				'status' => 'all',
				'plus_ones' => false,
				'progress_items_only' => false,
			);
			$args = wp_parse_args($args, $defaults);
			$cache = $args['cache'];
			if (isset($_GET['page']) && ($_GET['page']==='complianz' || strpos($_GET['page'],'cmplz') !== false ) ) {
				$cache = false;
			}

			$warnings = $cache ? get_transient( 'complianz_warnings' ) : false;
			//re-check if there are no warnings, or if the transient has expired
			if ( ! $warnings ) {

				$warning_type_defaults = array(
					'plus_one' => false,
					'warning_condition' => '_true_',
					'success_conditions' => array(),
					'relation' => 'OR',
					'status' => 'open',
					'include_in_progress' => false,
				);

				$warning_types = COMPLIANZ::$config->warning_types;
				foreach ($warning_types as $id => $warning_type) {
					$warning_types[$id] = wp_parse_args($warning_type, $warning_type_defaults );
				}

				$dismissed_warnings = get_option('cmplz_dismissed_warnings', array() );
				foreach ( $warning_types as $id => $warning ) {
					if ( in_array( $id, $dismissed_warnings) ) {
						continue;
					}

					$show_warning = $this->validate_function($warning['warning_condition']);
					if ( !$show_warning ) {
						continue;
					}

					$relation = $warning['relation'];
					if ( $relation === 'AND' ) {
						$success = TRUE;
					} else {
						$success = FALSE;
					}
					foreach ( $warning[ 'success_conditions']  as $func) {
						$condition = $this->validate_function($func);
						if ( $relation === 'AND' ) {
							$success = $success && $condition;
						} else {
							$success = $success || $condition;
						}
					}

					if ( !$success ) {
						if ( isset( $warning['open']) ) {
							$warning['message'] = $warning['open'];
							$warning['status'] = 'open';
							$warnings[$id] = $warning;
						} else if (isset( $warning['urgent']) ) {
							$warning['message'] = $warning['urgent'];
							$warning['status'] = 'urgent';
							$warnings[$id] = $warning;
						}
					} else {
						if (isset( $warning['completed']) ) {
							$warning['message'] = $warning['completed'];
							$warning['status'] = 'completed';
							$warning['plus_one'] = false;
							$warnings[$id] = $warning;
						}
					}
				}
				set_transient( 'complianz_warnings', $warnings, HOUR_IN_SECONDS );
			}

			//filtering outside cache if, to make sure all warnings are saved for the cache.
			//filter by status
			if ($args['status'] !== 'all' ) {
				$filter_statuses = is_array($args['status']) ? $args['status'] : array($args['status']);
				foreach ($warnings as $id => $warning ) {
					if ( !in_array( $warning['status'], $filter_statuses) ) {
						unset( $warnings[$id] );
					}
				}
			}

			//filter by plus ones
			if ($args['plus_ones']) {
				//if notifications disabled, we return an empty array when the plus ones are requested.
				if ( cmplz_get_value( 'disable_notifications' ) ) {
					return array();
				}

				foreach ($warnings as $id => $warning ) {
					//prevent notices on upgrade to 5.0
					if ( !isset( $warning['plus_one'])) continue;

					if ( !$warning['plus_one'] ){
						unset($warnings[$id]);
					}
				}
			}

			//filter for progress bar
			if ($args['progress_items_only']) {
				foreach ($warnings as $id => $warning ) {
					//prevent notices on upgrade to 5.0
					if ( !isset( $warning['include_in_progress'])) continue;

					if ( !$warning['include_in_progress'] ){
						unset($warnings[$id]);
					}
				}
			}

			//sort so warnings are on top
			$completed = array();
			$open = array();
			$urgent = array();
			foreach ($warnings as $key => $warning){
				//prevent notices on upgrade to 5.0
				if ( !isset( $warning['status'])) continue;

				if ($warning['status']==='urgent') {
					$urgent[$key] = $warning;
				} else if ($warning['status']==='open') {
					$open[$key] = $warning;
				} else {
					$completed[$key] = $warning;
				}
			}
			$warnings = $urgent + $open + $completed;

			return $warnings;
		}


		/**
		 * Get output of function, in format 'function', or 'class()->sub()->function'
		 * We can pass one variable to the function
		 * @param string $func
		 * @return string|bool
		 */

		private function validate_function( $func ){
			$invert = false;
			if (strpos($func, 'NOT ') !== FALSE ) {
				$func = str_replace('NOT ', '', $func);
				$invert = true;
			}

			if ( empty($func) ) {
				return true;
			}

			if ( strpos($func, 'get_option_') !== false ) {
				$field  = str_replace( 'get_option_', '', $func );
				$output = get_option( $field );
			} else if ( preg_match( '/get_value_(.*)==(.*)/i', $func, $matches)) {
				$fieldname = $matches[1];
				$value = $matches[2];
				$output = cmplz_get_value( $fieldname ) === $value;
			} else if ( $func === '_true_') {
				$output = true;
			} else if ( $func === '_false_' ) {
				$output = false;
			} else {
				if ( preg_match( '/(.*)->(.*)/i', $func, $matches)) {
					if (preg_match( '/(.*)->(.*)\((.*)\)/i', $func, $sub_matches )) {
						$class = $sub_matches[1];
						$function = $sub_matches[2];
						$variable = $sub_matches[3];
						$output = COMPLIANZ::${$class}->$function($variable);
					} else {
						$class = $matches[1];
						$function = $matches[2];
						$output = COMPLIANZ::${$class}->$function();
					}
				} else if ( preg_match( '/(.*)\((.*)\)/i', $func, $matches ) ) {
					$func = $matches[1];
					$variable = $matches[2];
					$output = $func($variable);
				} else{
					$output = $func();
				}
			}

			if ( $invert ) {
				$output = !$output;
			}

			return $output;
		}


		/**
		 * Register our menu's
		 */

		public function register_admin_page() {
			if ( ! cmplz_user_can_manage() ) {
				return;
			}
			$warnings = $this->get_warnings( array(
					'plus_ones' => true,
			) );
			$warning_count = count( $warnings );
			$warning_title = esc_attr( sprintf( '%d plugin warnings', $warning_count ) );
			$menu_label    = sprintf( __( 'Complianz %s', 'complianz-gdpr' ),
				"<span class='update-plugins count-$warning_count' title='$warning_title'><span class='update-count'>"
				. number_format_i18n( $warning_count ) . "</span></span>" );


			global $cmplz_admin_page;
			$cmplz_admin_page = add_menu_page(
				__( 'Complianz', 'complianz-gdpr' ),
				$menu_label,
				'manage_options',
				'complianz',
				array( $this, 'dashboard' ),
				cmplz_url . 'assets/images/menu-icon.svg',
				CMPLZ_MAIN_MENU_POSITION
			);

			add_submenu_page(
				'complianz',
				__( 'Dashboard', 'complianz-gdpr' ),
				__( 'Dashboard', 'complianz-gdpr' ),
				'manage_options',
				'complianz',
				array( $this, 'dashboard' )
			);

			add_submenu_page(
				'complianz',
				__( 'Wizard', 'complianz-gdpr' ),
				__( 'Wizard', 'complianz-gdpr' ),
				'manage_options',
				'cmplz-wizard',
				array( $this, 'wizard_page' )
			);

			do_action( 'cmplz_cookiebanner_menu' );

			do_action( 'cmplz_integrations_menu' );

			add_submenu_page(
				'complianz',
				__( 'Settings' ),
				__( 'Settings' ),
				'manage_options',
				"cmplz-settings",
				array( $this, 'settings' )
			);

			do_action( 'cmplz_admin_menu' );

			if ( defined( 'cmplz_free' ) && cmplz_free ) {
				global $submenu;
				$class                  = 'cmplz-submenu';
				$highest_index = count($submenu['complianz']);
				$submenu['complianz'][] = array(
						__( 'Upgrade to premium', 'complianz-gdpr' ),
						'manage_options',
						'https://complianz.io/l/pricing'
				);
				if ( isset( $submenu['complianz'][$highest_index] ) ) {
					if (! isset ($submenu['complianz'][$highest_index][4])) $submenu['complianz'][$highest_index][4] = '';
					$submenu['complianz'][$highest_index][4] .= ' ' . $class;
				}
			}

		}

		/**
		 * Show the wizard page
		 */
		public function wizard_page() {
			?>
			<div class="wrap">
				<?php if ( apply_filters( 'cmplz_show_wizard_page', true ) ) {
					COMPLIANZ::$wizard->wizard( 'wizard' );
				} else {
					$link = '<a href="'.add_query_arg(array('page'=>'cmplz-settings#license'), admin_url('admin.php')).'">';
					cmplz_admin_notice( sprintf(__( 'Your license needs to be %sactivated%s to unlock the wizard', 'complianz-gdpr' ), $link, '</a>' ));
				} ?>
			</div>
			<?php
		}

		/**
		 * Get status link for plugin, depending on installed, or premium availability
		 * @param $item
		 *
		 * @return string
		 */

		public function get_status_link($item){
			if (!defined($item['constant_free']) && !defined($item['constant_premium'])) {
				$link = admin_url() . "plugin-install.php?s=".$item['search']."&tab=search&type=term";
				$text = __('Install', 'complianz-gdpr');
				$status = "<a href=$link>$text</a>";
			} elseif ($item['constant_free'] == 'wpsi_plugin' || defined($item['constant_premium'] ) ) {
				$status = __("Installed", "complianz-gdpr");
			} elseif (defined($item['constant_free']) && !defined($item['constant_premium'])) {
				$link = $item['website'];
				$text = __('Upgrade to pro', 'complianz-gdpr');
				$status = "<a href=$link>$text</a>";
			}
			return $status;
		}

		/**
		 * Generate the dashboard page
		 */

		public function dashboard() {
			$all_count = count( $this->get_warnings(array( 'cache' => false ) ) );
			$remaining_count = count( $this->get_warnings(array(
					'cache' => false,
					'status' => array('urgent', 'open'),
					) ) );
			$tasks = '<span class="cmplz-task active" href="'.add_query_arg( array('page' => 'complianz'), admin_url('admin.php') ).'">'
					. sprintf(__("All tasks (%s)", "complianz-gdpr"), '<span class="cmplz-task-count cmplz-all">'.$all_count.'</span>')
					. '</span><span class="cmplz-task" href="'.add_query_arg( array('page' => 'complianz', 'cmplz-status' => 'remaining'), admin_url('admin.php') ).'">'
					. sprintf(__("Remaining tasks (%s)", "complianz-gdpr"), '<span class="cmplz-task-count cmplz-remaining">'.$remaining_count .'</span>')
					. '</span>';
			$grid_items =
				array(
					array(
                        'name'  => 'progress',
                        'header' => __("Your progress", "complianz-gdpr"),
						'class' => '',
						'page' => 'dashboard',
						'controls' => $tasks,
					),
					array(
                        'name'  => 'documents',
                        'header' => __("Documents", "complianz-gdpr"),
						'class' => 'small',
						'page' => 'dashboard',
						'controls' => __("Last update", "complianz-gdpr"),
					),

					array(
                        'name'  => 'tools',
                        'header' => __("Tools", "complianz-gdpr"),
						'class' => 'small',
						'page' => 'dashboard',
						'controls' => '',
					),

					array(
							'name'  => 'tips-tricks',
							'header' => __("Tips & Tricks", "complianz-gdpr"),
							'class' => 'half-height',
							'page' => 'dashboard',
							'controls' => '',
					),

					array(
						'name'  => 'other-plugins',
						'header' => __("Other plugins", "complianz-gdpr"),
						'class' => 'half-height',
						'page' => 'dashboard',
						'controls' => '<a href="https://really-simple-plugins.com/" target="_blank">
										<img src="'.cmplz_url.'/assets/images/really-simple-plugins.svg" alt="Really Simple Plugins">
										</a>',
					),

				);
			$grid_items = apply_filters('cmplz_grid_items', $grid_items);
			//give each item the key as index
			array_walk($grid_items, function(&$a, $b) { $a['index'] = $b; });

			$grid_html = '';
			foreach ($grid_items as $index => $grid_item) {
				$grid_html .= cmplz_grid_element($grid_item);
			}
			$args = array(
				'page' => 'dashboard',
				'content' => cmplz_grid_container($grid_html),
			);
			echo cmplz_get_template('admin_wrap.php', $args );

		}

		/**
		 * Get dropdown for grid
		 * @param string $name
		 * @param array $options
		 *
		 * @return string
		 */

		public function grid_dropdown($name, $options, $default) {
			$selected = false;
			if (isset($_GET[$name])) {
				$selected = sanitize_title($_GET[$name]);
			}

			if (!$selected) $selected = $default;

			$html = '<select class="cmplz-grid-selector" id="'.$name.'">';
			foreach ( $options as $value => $option ) {
				$sel = $selected === $value ? 'selected' : '';
				$html .= '<option value="'.$value.'" '.$sel.'>'.$option;
			}
			$html .= '</select>';
			return $html;
		}


		/**
		 * Get the html output for a help tip
		 *
		 * @param $str
		 */

		public function get_help_tip( $str ) {
			?>
			<span class="cmplz-tooltip-right tooltip-right"
			      data-cmplz-tooltip="<?php echo $str ?>">
              <span class="dashicons dashicons-editor-help"></span>
            </span>
			<?php
		}

		/**
		 * General settings page
		 *
		 */

		function settings() {
              // Grid
            $grid_items = array(
                'general' => array(
                    'page' => 'settings',
                    'name' => 'general',
                    'header' => __('General', 'complianz-gdpr'),
                    'class' => 'big',
                    'index' => '11',
                    'controls' => '',
                ),
                'data' => array(
                    'page' => 'settings',
                    'name' => 'data',
                    'header' => __('Data', 'complianz-gdpr'),
                    'class' => 'medium',
                    'index' => '12',
                    'controls' => '',
                ),
                'cookie-blocker' => array(
                    'page' => 'settings',
                    'name' => 'cookie-blocker',
                    'header' => __('Cookies', 'complianz-gdpr'),
                    'class' => 'medium',
                    'index' => '13',
                    'controls' => '',
                ),
                'document-styling' => array(
                    'page' => 'settings',
                    'name' => 'document-styling',
                    'header' => __('Document Styling', 'complianz-gdpr'),
                    'class' => 'big condition-check-1',
                    'index' => '14',
                    'controls' => '',
                    'conditions' => 'data-condition-question-1="use_custom_document_css" data-condition-answer-1="1"',
                ),
            );

			$grid_items = apply_filters( 'cmplz_settings_items', $grid_items);

			echo cmplz_grid_container_settings(__( "Settings", 'complianz-gdpr' ), $grid_items);
        }
	}
} //class closure
