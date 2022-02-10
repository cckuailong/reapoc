<?php
/**
 * Original Author: danieliser
 * Original Author URL: https://danieliser.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Ppc_Install_Permissions
 *
 * This class adds a message inviting to install Permissions
 */
if( !class_exists('Ppc_Install_Permissions') ) {
	class Ppc_Install_Permissions {

		/**
		 * Tracking API Endpoint.
		 *
		 * @var string
		 */
		public static $api_url = '';

		/**
		 *
		 */
		public static function init() {
			if (!isset( $_GET['pp-after-click'])) {
				self::hooks();

				add_action( 'wp_ajax_ppc_permissions_action', array( __CLASS__, 'ajax_handler' ) );
			}
		}

		/**
		 * Hook into relevant WP actions.
		 */
		public static function hooks() {
			if ( is_admin() && current_user_can( 'edit_posts' ) ) {
				self::installed_on();
				add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
				add_action( 'network_admin_notices', array( __CLASS__, 'admin_notices' ) );
				add_action( 'user_admin_notices', array( __CLASS__, 'admin_notices' ) );
			}
		}

		/**
		 * Get the install date for comparisons. Sets the date to now if none is found.
		 *
		 * @return false|string
		 */
		public static function installed_on() {
			$installed_on = get_option( 'ppc_permissions_installed_on', false );

			if ( ! $installed_on ) {
				$installed_on = current_time( 'mysql' );
				update_option( 'ppc_permissions_installed_on', $installed_on );
			}

			return $installed_on;
		}

		/**
		 *
		 */
		public static function ajax_handler() {
			$args = wp_parse_args( $_REQUEST, array(
				'group'  => self::get_trigger_group(),
				'code'   => self::get_trigger_code(),
				'pri'    => self::get_current_trigger( 'pri' ),
				'reason' => 'maybe_later',
			) );

			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'ppc_permissions_action' ) ) {
				wp_send_json_error();
			}

			try {
				$user_id = get_current_user_id();

				$dismissed_triggers                   = self::dismissed_triggers();
				$dismissed_triggers[ $args['group'] ] = $args['pri'];
				update_user_meta( $user_id, '_ppc_permissions_dismissed_triggers', $dismissed_triggers );
				update_user_meta( $user_id, '_ppc_permissions_last_dismissed', current_time( 'mysql' ) );

				switch ( $args['reason'] ) {
					case 'maybe_later':
						update_user_meta( $user_id, '_ppc_permissions_last_dismissed', current_time( 'mysql' ) );
						break;
					case 'am_now':
					case 'already_did':
						self::already_did( true );
						break;
				}

				wp_send_json_success();

			} catch ( Exception $e ) {
				wp_send_json_error( $e );
			}
		}

		/**
		 * @return int|string
		 */
		public static function get_trigger_group() {
			static $selected;

			if ( ! isset( $selected ) ) {

				$dismissed_triggers = self::dismissed_triggers();

				$triggers = self::triggers();

				foreach ( $triggers as $g => $group ) {
					foreach ( $group['triggers'] as $t => $trigger ) {
						if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
							$selected = $g;
							break;
						}
					}

					if ( isset( $selected ) ) {
						break;
					}
				}
			}

			return $selected;
		}

		/**
		 * @return int|string
		 */
		public static function get_trigger_code() {
			static $selected;

			if ( ! isset( $selected ) ) {

				$dismissed_triggers = self::dismissed_triggers();

				foreach ( self::triggers() as $g => $group ) {
					foreach ( $group['triggers'] as $t => $trigger ) {
						if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
							$selected = $t;
							break;
						}
					}

					if ( isset( $selected ) ) {
						break;
					}
				}
			}

			return $selected;
		}

		/**
		 * @param null $key
		 *
		 * @return bool|mixed|void
		 */
		public static function get_current_trigger( $key = null ) {
			$group = self::get_trigger_group();
			$code  = self::get_trigger_code();

			if ( ! $group || ! $code ) {
				return false;
			}

			$trigger = self::triggers( $group, $code );

			if(empty($key)){
                $return = $trigger;
            }elseif(isset($trigger[$key])){
                 $return = $trigger[$key];
            }else {
               $return = false;
            }

            return $return;
		}

		/**
		 * Returns an array of dismissed trigger groups.
		 *
		 * Array contains the group key and highest priority trigger that has been shown previously for each group.
		 *
		 * $return = array(
		 *   'group1' => 20
		 * );
		 *
		 * @return array|mixed
		 */
		public static function dismissed_triggers() {
			$user_id = get_current_user_id();

			$dismissed_triggers = get_user_meta( $user_id, '_ppc_permissions_dismissed_triggers', true );

			if ( ! $dismissed_triggers ) {
				$dismissed_triggers = array();
			}

			return $dismissed_triggers;
		}

		/**
		 * Returns true if the user has opted to never see this again. Or sets the option.
		 *
		 * @param bool $set If set this will mark the user as having opted to never see this again.
		 *
		 * @return bool
		 */
		public static function already_did( $set = false ) {
			$user_id = get_current_user_id();

			if ( $set ) {
				update_user_meta( $user_id, '_ppc_permissions_already_did', true );

				return true;
			}

			return (bool) get_user_meta( $user_id, '_ppc_permissions_already_did', true );
		}

		/**
		 * Gets a list of triggers.
		 *
		 * @param null $group
		 * @param null $code
		 *
		 * @return bool|mixed|void
		 */
		public static function triggers( $group = null, $code = null ) {
			static $triggers;

			if ( ! isset( $triggers ) ) {

				$time_message = __( 'Do you want to control permissions for specific posts and pages?', 'capsman-enhanced' );

				$triggers = apply_filters( 'ppc_permissions_triggers', array(
					'time_installed' => array(
						'triggers' => array(
	                        'one_week'     => array(
								'message'    => sprintf( $time_message, __( '1 week', 'capsman-enhanced' ) ),
								'conditions' => array(
									strtotime( self::installed_on() . ' +1 week' ) < time(),
								),
								'link'       => admin_url( 'plugin-install.php?s=publishpress+permissions+control+access&tab=search&type=term&pp-after-click' ),
								'pri'        => 10,
							),
						),
						'pri'      => 10,
					),
				) );

				// Sort Groups
				uasort( $triggers, array( __CLASS__, 'rsort_by_priority' ) );

				// Sort each groups triggers.
				foreach ( $triggers as $k => $v ) {
					uasort( $triggers[ $k ]['triggers'], array( __CLASS__, 'rsort_by_priority' ) );
				}
			}

			if ( isset( $group ) ) {
				if ( ! isset( $triggers[ $group ] ) ) {
					return false;
				}

				if (!isset($code)) {
                    $return = $triggers[$group];
                } elseif (isset($triggers[$group]['triggers'][$code])) {
                    $return = $triggers[$group]['triggers'][$code];
                } else {
                    $return = false;
                }

				return $return;
			}

			return $triggers;
		}

		/**
		 * Render admin notices if available.
		 */
		public static function admin_notices() {
			if ( self::hide_notices() ) {
				return;
			}

			$group  = self::get_trigger_group();
			$code   = self::get_trigger_code();
			$pri    = self::get_current_trigger( 'pri' );
			$tigger = self::get_current_trigger();

			// Used to anonymously distinguish unique site+user combinations in terms of effectiveness of each trigger.
			$uuid = wp_hash( home_url() . '-' . get_current_user_id() );

			?>

			<script type="text/javascript">
				(function ($) {
					var trigger = {
						group: '<?php echo $group; ?>',
						code: '<?php echo $code; ?>',
						pri: '<?php echo $pri; ?>'
					};

					function dismiss(reason) {
						$.ajax({
							method: "POST",
							dataType: "json",
							url: ajaxurl,
							data: {
								action: 'ppc_permissions_action',
								nonce: '<?php echo wp_create_nonce( 'ppc_permissions_action' ); ?>',
								group: trigger.group,
								code: trigger.code,
								pri: trigger.pri,
								reason: reason
							}
						});

						<?php if ( ! empty( self::$api_url ) ) : ?>
						$.ajax({
							method: "POST",
							dataType: "json",
							url: '<?php echo self::$api_url; ?>',
							data: {
								trigger_group: trigger.group,
								trigger_code: trigger.code,
								reason: reason,
								uuid: '<?php echo $uuid; ?>'
							}
						});
						<?php endif; ?>
					}

					$(document)
						.on('click', '.ppc-notice .ppc-dismiss', function (event) {
							var $this = $(this),
								reason = $this.data('reason'),
								notice = $this.parents('.ppc-notice');

							notice.fadeTo(100, 0, function () {
								notice.slideUp(100, function () {
									notice.remove();
								});
							});

							dismiss(reason);
						})
						.ready(function () {
							setTimeout(function () {
								$('.ppc-notice button.notice-dismiss').click(function (event) {
									dismiss('maybe_later');
								});
							}, 1000);
						});
				}(jQuery));
			</script>

			<div class="notice notice-success is-dismissible ppc-notice">

				<p>
					<?php echo $tigger['message']; ?>
				</p>
				<p>
	                <a class="button button-primary ppc-dismiss" target="_blank" href="<?php echo admin_url( 'plugin-install.php?s=publishpress+permissions+control+access&tab=search&type=term&pp-after-click' ) ?>" data-reason="am_now">
	                    <strong><?php _e( 'Install PublishPress Permissions', 'capsman-enhanced' ); ?></strong>
	                </a>
				</p>

			</div>

			<?php
		}

		/**
		 * Checks if notices should be shown.
		 *
		 * @return bool
		 */
		public static function hide_notices() {
			$conditions = array(
				self::already_did(),
				self::last_dismissed() && strtotime( self::last_dismissed() . ' +2 weeks' ) > time(),
				empty( self::get_trigger_code() ),
			);

			return in_array( true, $conditions );
		}

		/**
		 * Gets the last dismissed date.
		 *
		 * @return false|string
		 */
		public static function last_dismissed() {
			$user_id = get_current_user_id();

			return get_user_meta( $user_id, '_ppc_permissions_last_dismissed', true );
		}

		/**
		 * Sort array by priority value
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		public static function sort_by_priority( $a, $b ) {
			if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
				return 0;
			}

			return ( $a['pri'] < $b['pri'] ) ? - 1 : 1;
		}

		/**
		 * Sort array in reverse by priority value
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		public static function rsort_by_priority( $a, $b ) {
			if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
				return 0;
			}

			return ( $a['pri'] < $b['pri'] ) ? 1 : - 1;
		}

	}

	Ppc_Install_Permissions::init();
}
