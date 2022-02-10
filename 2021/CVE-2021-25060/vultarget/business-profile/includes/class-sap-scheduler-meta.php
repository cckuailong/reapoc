<?php
/**
 * Extend the Simple Admin Pages Scheduler component so that it can be used
 * in the location custom post type editing screen.
 *
 * @see lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.1
 */

defined( 'ABSPATH' ) || exit;

require_once BPFWP_PLUGIN_DIR . '/lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php';

if ( ! class_exists( 'bpfwpSAPSchedulerMeta', false ) && class_exists( 'sapAdminPageSettingScheduler_2_6_3' ) ) :

	/**
	 * Class to extend the Simple Admin Pages Scheduler component for use on
	 * the location custom post type editing screen
	 *
	 * @since 1.1
	 */
	class bpfwpSAPSchedulerMeta extends sapAdminPageSettingScheduler_2_6_3 {

		/**
		 * Generate an option input field name. The default component appends
		 * an admin page slug to the input field name. We only want the
		 * object ID, which will be the post meta key.
		 *
		 * @since  1.1
		 * @access public
		 * @return string $id The input ID.
		 */
		public function get_input_name() {
			return esc_attr( $this->id );
		}
	}
endif;
