<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Settings_Email_Notifications class
 *
 * This file used to define the settings for the email notifications. User can 
 * enable/disable emails receiving for Admin/HR/Applicant.
 * 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.3.2   Added Filter for HR Email Address
 * @since       2.4.0   Revised Inputs & Outputs Sanitization & Escaping
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/settings
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Settings_Email_Notifications {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct() {

        // Filter -> Add Settings Email Notifications Tab
        add_filter('sjb_settings_tab_menus', array($this, 'sjb_add_settings_tab'), 70);

        // Action -> Add Settings Email Notifications Section 
        add_action('sjb_settings_tab_section', array($this, 'sjb_add_settings_section'), 70);

        // Action -> Save Settings Email Notifications Section 
        add_action('sjb_save_setting_sections', array($this, 'sjb_save_settings_section'));
    }

    /**
     * Add Settings Email Notifications Tab.
     *
     * @since    2.2.3
     * 
     * @param    array  $tabs  Settings Tab
     * @return   array  $tabs  Merge array of Settings Tab with "Email Notification" Tab.
     */
    public function sjb_add_settings_tab($tabs) {
        
        $tabs['email_notifications'] = esc_html__( 'Email Notifications', 'simple-job-board' );
        return $tabs;
    }

    /**
     * Add Settings Email Notifications Section.
     *
     * @since    2.2.3
     */
    public function sjb_add_settings_section() {
        
        ?>
        <!-- Notification -->
        <div data-id="settings-email_notifications" class="sjb-admin-settings tab">
            
            <?php
            /**
             * Action -> Add new section before notifications settings .  
             * 
             * @since 2.2.0 
             */
            do_action('sjb_notifications_settings_before');

            $hr_email = ( FALSE !== get_option('settings_hr_email') ) ? get_option('settings_hr_email') : '';
            $admin_email = ( FALSE !== get_option( 'settings_admin_email' ) ) ? get_option( 'settings_admin_email' ) : get_option( 'admin_email' );
            ?>
            <h4 class="first"><?php _e('Enable Email Notification', 'simple-job-board'); ?></h4>
            <form method="post" id="email_notification_form">
                <div class="sjb-section">
                    <div class="sjb-content-email-notify">
                        
                        <?php
                        /**
                         * Action -> Add new fields at the start of notification section.  
                         * 
                         * @since 2.2.0 
                         */
                        do_action('sjb_email_notifications_settings_start');
                        ?>
                        <div class="sjb-form-group">
                            <label><?php echo esc_html__('HR Email:', 'simple-job-board'); ?><input type="hidden" name="empty_form_check" value="empty_form_submitted"></label>
                            <?php $HR = '<input type="email" name="hr_email" value="' . esc_attr( $hr_email ) . '" size="30">';
                                echo apply_filters( 'sjb_settings_hr_email', $HR );
                            ?>
                        </div>
                        <div class="sjb-form-group right-align">
                            <input type="checkbox" name="email_notification[]" id="enable-hr-email" value="hr_email" <?php if ('yes' === get_option('job_board_hr_notification')) echo 'checked="checked"'; ?>/>
                            <label for="enable-hr-email"><?php echo esc_html__('Enable the HR Email Notification', 'simple-job-board'); ?></label>
                        </div>
                        <div class="sjb-form-group">
                            <label><?php echo esc_html__('Admin Email:', 'simple-job-board'); ?></label>
                            <?php
                                $ADMIN = '<input type="email" name="admin_email" value="' . esc_attr( $admin_email ) . '" size="30">';
                                echo apply_filters('sjb_settings_admin_email', $ADMIN);
                            ?>
                        </div>
                        <div class="sjb-form-group right-align">
                            <input type="checkbox" name="email_notification[]" id="enable-admin-email" value="admin_email" <?php if ('yes' === get_option('job_board_admin_notification')) echo 'checked="checked"'; ?> />
                            <label for="enable-admin-email"><?php echo esc_html__('Enable the Admin Email Notification', 'simple-job-board'); ?></label>
                        </div>
                        <div class="sjb-form-group right-align">
                            <input type="checkbox" name="email_notification[]" id="enable-applicant-email" value="applicant_email" <?php if ('yes' === get_option('job_board_applicant_notification')) echo 'checked="checked"'; ?>/>
                            <label for="enable-applicant-email"><?php echo esc_html__('Enable the Applicant Email Notification', 'simple-job-board'); ?></label>
                        </div>
                        
                        <?php
                        /**
                         * Action -> Add new fields at the end of notification section.  
                         * 
                         * @since 2.2.0 
                         */
                        do_action('sjb_notifications_settings_end');
                        ?>                        
                    </div>
                </div>
                
                <?php
                /**
                 * Action -> Add new section after notifications settings .  
                 * 
                 * @since 2.2.0 
                 */
                do_action('sjb_notifications_settings_after');
                ?>
                
                <input type="hidden" value="1" name="admin_notices" />
                <input type="submit" name="job_email_notification" id="job_email_notification" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'simple-job-board'); ?>" />
            </form>
        </div>
        <?php
    }

    /**
     * Save Settings Email Notification Section.
     * 
     * This function is used to save the email notifications settings. User can 
     * enable/disable the notifications for Admin/HR/Applicant. 
     *
     * @since    2.2.3
     */
    public function sjb_save_settings_section() {
        $notifications_status = filter_input( INPUT_POST, 'email_notification', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $empty_form_check = filter_input( INPUT_POST, 'empty_form_check' );
        
        if ( ( isset( $notifications_status ) ) || NULL != $empty_form_check ) {
            
            // Empty checkboxes status
            $hr_email_status = $admin_email_status = $applicant_email_status = 'no';
            
            // Save Notifications Settings
            if ( !empty( $notifications_status ) ) {
                foreach ( $notifications_status as $notification ) {
                    if ( 'hr_email' === $notification ) {
                        update_option('job_board_hr_notification', 'yes');
                        $hr_email_status = 'yes';
                    } elseif ( 'admin_email' === $notification ) {
                        update_option('job_board_admin_notification', 'yes');
                        $admin_email_status = 'yes';
                    } elseif ( 'applicant_email' === $notification ) {
                        update_option('job_board_applicant_notification', 'yes');
                        $applicant_email_status = 'yes';
                    }
                }
            }

            // HR Email
            $hr_email = filter_input( INPUT_POST, 'hr_email' );
            
            // Admin Email
            $admin_email = filter_input(INPUT_POST, 'admin_email');
            
            if ( !empty( $hr_email ) ) {
                ( false !== get_option('settings_hr_email') ) ? update_option('settings_hr_email', sanitize_email( $hr_email ) ) : add_option('settings_hr_email', sanitize_email( $hr_email ) );
            } elseif(isset($hr_email) && '' === $hr_email) {
                update_option('settings_hr_email', '' );
            }
            
            if (!empty($admin_email)) {
                ( false !== get_option('settings_admin_email') ) ? update_option('settings_admin_email', sanitize_email($admin_email)) : add_option('settings_admin_email', sanitize_email($admin_email));
            } elseif ( isset($admin_email) && '' === $admin_email) {
                update_option('settings_admin_email', '' );
            }
            

            // Disable HR Notification
            if ( 'no' === $hr_email_status ) {
                update_option('job_board_hr_notification', 'no');
            }

            // Disable Admin Notification
            if ( 'no' === $admin_email_status ) {
                update_option('job_board_admin_notification', 'no');
            }

            // Disable Applicant Notification
            if ( 'no' === $applicant_email_status ) {
                update_option('job_board_applicant_notification', 'no');
            }
        } 
    }
}