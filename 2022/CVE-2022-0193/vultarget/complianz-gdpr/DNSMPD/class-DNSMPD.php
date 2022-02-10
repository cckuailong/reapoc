<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! class_exists( "cmplz_DNSMPD" ) ) {
	class cmplz_DNSMPD {
		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_ajax_cmplz_send_dnsmpd_request', array( $this, 'send_dnsmpd_request' ) );
			add_action( 'wp_ajax_nopriv_cmplz_send_dnsmpd_request', array( $this, 'send_dnsmpd_request' ) );
			add_action( 'activated_plugin', array( $this, 'update_db_check' ), 10, 2 );
			add_action( 'admin_init', array( $this, 'update_db_check' ), 10 );
			add_action( 'cmplz_admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'process_delete' ) );
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));

		}

		static function this() {
			return self::$_this;
		}

		public function admin_menu() {
			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			if ( ! cmplz_dnsmpi_required()
			) {
				return;
			}

			add_submenu_page(
				'complianz',
				__( 'DNSMPI', 'complianz-gdpr' ),
				__( 'DNSMPI', 'complianz-gdpr' ),
				'manage_options',
				'cmplz_dnsmpd',
				array( $this, 'removed_users_overview' )
			);
		}

		public function removed_users_overview() {
			include( dirname( __FILE__ ) . '/class-DNSMPD-table.php' );

			$customers_table = new cmplz_DNSMPD_Table();
			$customers_table->prepare_items();
			?>
			<div class="wrap">
				<h1 class="wp-heading-inline"><?php _e( 'Do Not Sell My Personal Info Requests', 'complianz-gdpr' ); ?>
				<?php //do_action( 'edd_customers_table_top' );
				?>
				<a href="<?php echo esc_url_raw( cmplz_url
				                                 . "DNSMPD/csv.php?nonce="
				                                 . wp_create_nonce( 'cmplz_csv_nonce' ) ) ?>"
				   target="_blank" class="button button-primary"><?php _e("Export", "complianz-gdpr")?></a>
				</h1>
				<form id="cmplz-dnsmpd-filter" method="get"
				      action="<?php echo admin_url( 'admin.php?page=cmplz_dnsmpd' ); ?>">
					<?php
					$customers_table->search_box( __( 'Search Customers',
						'complianz-gdpr' ), 'cmplz_dnsmpd' );
					$customers_table->display();
					?>
					<input type="hidden" name="page" value="cmplz_dnsmpd"/>
				</form>
				<?php //do_action( 'edd_customers_table_bottom' );
				?>
			</div>
			<?php
		}


		public function get_users( $args ) {
			global $wpdb;
			$sql        = "SELECT * from {$wpdb->prefix}cmplz_dnsmpd";
			$search_sql = '';
			if ( isset( $args['email'] ) && ! empty( $args['email'] )
			     && is_email( $args['email'] )
			) {
				$sql = $wpdb->prepare( "%s WHERE email like %s", $sql,
					"%" . sanitize_text_field( $args['email'] ) . "%" );
			}

			if ( isset( $args['name'] ) && ! empty( $args['name'] ) ) {
				$search_sql = " WHERE name like '%"
				              . sanitize_text_field( $args['name'] ) . "%'";
			}
			$sql .= $search_sql . " ORDER BY "
			        . sanitize_title( $args['orderby'] ) . " "
			        . sanitize_title( $args['order'] );

			if ( isset( $args['number'] ) ) {
				$sql .= " LIMIT " . intval( $args['number'] ) . " OFFSET "
				        . intval( $args["offset"] );
			}
			$users = $wpdb->get_results( $sql );

			return $users;
		}

		/**
		 * Count number of users
		 * @param $args
		 *
		 * @return int
		 */

		public function count_users( $args ) {
			unset( $args['number'] );
			$users = $this->get_users( $args );
			return count( $users );
		}

		public function send_dnsmpd_request() {

			//check honeypot
			$error = false;
			if ( isset( $_POST['firstname'] )
			     && ! empty( $_POST['firstname'] )
			) {
				$error   = true;
				$message = __( "Sorry, it looks like you're a bot",
					'complianz-gdpr' );
			}

			if ( ! isset( $_POST['email'] ) || ! is_email( $_POST['email'] ) ) {
				$error   = true;
				$message = __( "Please enter a valid email address.",
					'complianz-gdpr' );
			}

			if ( ! isset( $_POST['name'] ) || strlen( $_POST['name'] ) == 0 ) {
				$error   = true;
				$message = __( "Please enter your name", 'complianz-gdpr' );
			}

			if ( ! isset( $_POST['name'] ) || strlen( $_POST['name'] ) > 100 ) {
				$error = true;
				$message
				       = __( "That's a long name you got there. Please try to shorten the name.",
					'complianz-gdpr' );
			}

			if ( ! $error ) {
				$email = sanitize_email( $_POST['email'] );
				$name  = sanitize_text_field( $_POST['name'] );
				//check if this email address is already registered:
				global $wpdb;
				$count
					= $wpdb->get_var( $wpdb->prepare( "SELECT count(*) from {$wpdb->prefix}cmplz_dnsmpd WHERE email = '%s'",
					$email ) );
				if ( $count == 0 ) {
					$wpdb->insert( $wpdb->prefix . 'cmplz_dnsmpd',
						array(
							'name'         => $name,
							'email'        => $email,
							'request_date' => time()
						)
					);
					$this->send_confirmation_mail( $email, $name );
					$message
						= __( "Your request has been processed successfully!",
						'complianz-gdpr' );
				} else {
					$message = __( "Your email address was already registered!",
						'complianz-gdpr' );
				}
			}

			$data     = array(
				'success' => ! $error,
				'message' => $message
			);
			$response = json_encode( $data );
			header( "Content-Type: application/json" );
			echo $response;
			exit;
		}

		/**
		 * Handle ajax delete request
		 */

		public function process_delete() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'cmplz_dnsmpd' )
			     && isset( $_GET['action'] )
			     && $_GET['action'] == 'delete'
			     && isset( $_GET['id'] )
			) {
				global $wpdb;
				$wpdb->delete( $wpdb->prefix . 'cmplz_dnsmpd',
					array( 'ID' => intval( $_GET['id'] ) ) );
				$paged = isset( $_GET['paged'] ) ? 'paged='
				                                   . intval( $_GET['paged'] )
					: '';
				wp_redirect( admin_url( 'admin.php?page=cmplz_dnsmpd' . $paged ) );
			}
		}

		/**
		 * Enqueue back-end assets
		 * @param $hook
		 */
		public function admin_enqueue($hook){
			if (!isset($_GET['page']) || $_GET['page'] !== 'cmplz_dnsmpd' ) return;
			$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
			wp_register_style('cmplz-posttypes', cmplz_url . "assets/css/posttypes$min.css", false, cmplz_version);
			wp_enqueue_style('cmplz-posttypes');
		}

		/**
		 * Enqueue front-end assets
		 * @param $hook
		 */
		public function enqueue_assets( $hook ) {
			if ( ! cmplz_has_region( 'us' )
			     || ! cmplz_sells_personal_data()
			) {
				return;
			}
			$dnsmpd_page_id
				= COMPLIANZ::$document->get_shortcode_page_id( 'cookie-statement',
				'us' );
			if ( ! $dnsmpd_page_id ) {
				return;
			}

			global $post;
			if ( $post && $post->ID != $dnsmpd_page_id ) {
				return;
			}

			wp_enqueue_script( 'cmplz-dnsmpd', cmplz_url . "DNSMPD/dnsmpd.js",
				array( 'jquery' ), cmplz_version, true );
			wp_localize_script(
				'cmplz-dnsmpd',
				'cmplz_dnsmpd',
				array(
					'url' => admin_url( 'admin-ajax.php' ),
				)
			);

		}


		public function update_db_check() {
			if ( get_option( 'cmplz_dnsmpd_db_version' ) != cmplz_version ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				$this->create_user_table();
				update_option( 'cmplz_dnsmpd_db_version', cmplz_version );
			}
		}


		/*
		 *
		 *
		 *
		 * */

		public function create_user_table() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . 'cmplz_dnsmpd';
			$sql        = "CREATE TABLE $table_name (
              `ID` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `request_date` int(11) NOT NULL,
              PRIMARY KEY  (ID)
            ) $charset_collate;";

			dbDelta( $sql );
		}


		private function send_confirmation_mail( $email, $name ) {
			$message = cmplz_get_value( 'notification_email_content' );
			$subject = cmplz_get_value( 'notification_email_subject' );

			$message = str_replace( '{name}', $name, $message );
			$message = str_replace( '{blogname}', get_bloginfo( 'name' ),
				$message );
			$this->send_mail( $email, $subject, $message );
		}


//        private function send_admin_notification($email, $name){
//            $message = cmplz_get_value('notification_email_content');
//            $subject = cmplz_get_value('notification_email_subject');
//
//            $message = str_replace('{name}', $name, $message);
//            $message = str_replace('{blogname}', get_bloginfo('name'), $message);
//            $this->send_mail($email, $subject, $message);
//        }

		private function send_mail( $email, $subject, $message ) {
			$headers = array();

			$from_name  = get_bloginfo( 'name' );
			$from_email = cmplz_get_value( 'notification_from_email' );

			add_filter( 'wp_mail_content_type', function ( $content_type ) {
				return 'text/html';
			} );

			if ( ! empty( $from_email ) ) {
				$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>'
				             . "\r\n";
			}
			$success = true;

			if ( wp_mail( $email, $subject, $message, $headers ) === false ) {
				$success = false;
			}

			// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

			return $success;
		}


	}
} //class closure
