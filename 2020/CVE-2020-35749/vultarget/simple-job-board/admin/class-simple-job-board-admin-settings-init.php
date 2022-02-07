<?php if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Settings_Init Class
 * 
 * This is used to define job settings. This file contains following settings
 * 
 * - General
 * - Appearance
 * - Job Features
 * - Application Form Fields
 * - Filters
 * - Email Notifications
 * - Upload File Extensions
 * 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.7.2 Added prefix to the main wrapper class
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin
 * @author      PressTigers <support@presstigers.com> 
 */

class Simple_Job_Board_Settings_Init
{

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct()
    {

        /**
         * The class responsible for defining all the plugin general settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-general.php';

        // Check if General Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_General')) {

            // Initialize General Settings class           
            new Simple_Job_Board_Settings_General();
        }

        /**
         * The class responsible for defining all the plugin appearance settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-appearance.php';

        // Check if  Appearance Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Appearance')) {

            // Initialize Appearance Settings Class           
            new Simple_Job_Board_Settings_Appearance();
        }

        /**
         * The class responsible for defining all the plugin job features settings that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-job-features.php';

        // Check if  Job Features Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Job_Features')) {

            // Initialize Job Features Class           
            new Simple_Job_Board_Settings_Job_Features();
        }

        /**
         * The class responsible for defining all the plugin job application form settings that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-application-form-fields.php';

        // Check if Job Application Form Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Application_Form_Fields')) {

            // Initialize Job Application Form Settings Class           
            new Simple_Job_Board_Settings_Application_Form_Fields();
        }

        /**
         * The class responsible for defining all the plugin job filters settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-filters.php';

        // Check if Job Filters Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Filters')) {

            // Initialize Job Filters Settings Class           
            new Simple_Job_Board_Settings_Filters();
        }

        /**
         * The class responsible for defining all the plugin email notifications settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-email-notifications.php';

        // Check if Email Notifications Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Email_Notifications')) {

            // Initialize Email Notifications Settings Class           
            new Simple_Job_Board_Settings_Email_Notifications();
        }

        /**
         * The class responsible for defining all the plugin uplaod file extensions settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-upload-file-extensions.php';

        // Check if Upload File Extension Settings Class Exists
        if (class_exists('Simple_Job_Board_Settings_Upload_File_Extensions')) {

            // Initialize Upload File Extension Settings Class           
            new Simple_Job_Board_Settings_Upload_File_Extensions();
        }

        /**
         * The class responsible for defining all the plugin uplaod file extensions settings that occur in the frontend area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings/class-simple-job-board-settings-privacy.php';

        // Check if Simple_Job_Board_GDPR_Settings Class Exists
        if (class_exists('Simple_Job_Board_Privacy')) {

            // Initialize Simple_Job_Board_GDPR_Settings Class           
            new Simple_Job_Board_Privacy();
        }

        // Action - Add Settings Menu
        add_action('admin_menu', array($this, 'sjb_admin_menu'), 12);

        // Action - Add Wizard Menu
        add_action('admin_menu', array($this, 'sjb_admin_menu_wizard'), 12);

        // Action - Save Settings
        add_action('admin_notices', array($this, 'sjb_save_settings'));
    }

    /**
     * Add Wizard Page Under Job Board Menu.
     * 
     * @since   2.0.0
     */
    public function sjb_admin_menu_wizard()
    {
        add_submenu_page('edit.php?post_type=jobpost', esc_html__('Wizard', 'simple-job-board'), esc_html__('Wizard', 'simple-job-board'), 'manage_options', 'job-board-wizard', array($this, 'sjb_wizard_tab_menu'));
    }

    /**
     * Add Settings Page Under Job Board Menu.
     * 
     * @since   2.0.0
     */
    public function sjb_admin_menu()
    {
        add_submenu_page('edit.php?post_type=jobpost', esc_html__('Settings', 'simple-job-board'), esc_html__('Settings', 'simple-job-board'), 'manage_options', 'job-board-settings', array($this, 'sjb_settings_tab_menu'));
    }

    /**
     * Add Wizard Tab Menu.
     * 
     * @Since   2.8.4
     */
    public function sjb_wizard_tab_menu()
    {
        wp_enqueue_script('wp-easing');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('sjb-wizard-script');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        
        
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );  
        $site_url = site_url();
        $settings_url = $site_url . '/wp-admin/edit.php?post_type=jobpost&page=job-board-settings';
        $wizard_url = $site_url . '/wp-admin/edit.php?post_type=jobpost&page=job-board-wizard';

        ?>
        <section class="sjb-wizard">
            <?php
            if(isset($_GET['get_ready']) && $_GET['get_ready'] == 'yes'){
                $get_ready ='active';
                $get_started = '';
                ?>
                <form id="sjb-wiz-id" action="<?php echo $action_url ?>;" method="post">
                    <?php
                    /**
                    * This template is responsible to display top tabs.
                    */
                    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-tabs.php';
                    ?>
                    <fieldset>
                        <?php
                        /**
                        * This template is responsible to display get ready section in wizard
                        */
                        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-get-ready.php';
                        ?>
                    </fieldset>
                </form>
                <?php
            }
            else{
                $get_ready ='';
                $get_started = 'active';
                ?>
                <form id="sjb-wiz-id" action="<?php echo $wizard_url;?>&get_ready=yes"  method="post">
                    <?php
                    /**
                    * This template is responsible to display top tabs.
                    */
                    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-tabs.php';
                    ?>
                    <!-- fieldsets -->
                    <fieldset>
                        <?php
                        /**
                        * This template is responsible to display get started section in wizard
                        */
                        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-get-started.php';
                        ?>
                    </fieldset>
                    <fieldset>
                        <?php
                        /**
                        * This template is responsible to display appearance section in wizard
                        */
                        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-job-data.php';
                        ?>
                    </fieldset>
                    <fieldset>
                        <?php
                        /**
                        * This template is responsible to display job application data section in wizard
                        */
                        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/wizard/wizard-job-application-data.php';
                        ?>
                    </fieldset>
                </form>
                <?php
            }
            ?>
        </section>
    <?php
    }

    /**
     * Add Settings Tab Menu.
     * 
     * @Since   2.0.0
     * @Since   2.7.2 Renamed classname 'wrap' to 'sjb-tabs-wrap' to avoid from conflict
     */
    public function sjb_settings_tab_menu()
    {
    ?>
        <div class="sjb-tabs-wrap">
            <h1><?php esc_html_e('Settings', 'simple-job-board'); ?></h1>
            <h2 class="nav-tab-wrapper">

                <?php
                /**
                 * Filter the Settings Tab Menus. 
                 * 
                 * @since 2.2.0 
                 * 
                 * @param array (){
                 *     @type array Tab Id => Settings Tab Name
                 * }
                 */
                $settings_tabs = apply_filters('sjb_settings_tab_menus', array());

                $count = 1;
                foreach ($settings_tabs as $key => $tab_name) {
                    $active_tab = (1 === $count) ? 'nav-tab-active' : '';
                    echo '<a href="#" data-id="settings-' . sanitize_key($key) . '" class="nav-tab ' . sanitize_html_class($active_tab) . ' ">' . esc_attr($tab_name) . '</a>';
                    $count++;
                }
                ?>

            </h2>

            <?php
            /**
             * Action -> Display Settings Sections.  
             * 
             * @since 2.2.3 
             */
            do_action('sjb_settings_tab_section');
            ?>

        </div>

        <?php
    }

    /**
     * Save Settings.
     * 
     * @since   2.2.3
     */
    public function sjb_save_settings()
    {
        /**
         * Action -> Save Setting Sections.
         * 
         * @since   2.2.3 
         */
        do_action('sjb_save_setting_sections');

        if(!isset($_POST['sjb-wiz-id'])){
            // Admin Notices
            if ((NULL != filter_input(INPUT_POST, 'admin_notices'))) {
            ?>
                <div class="updated">
                    <p><?php echo apply_filters('sjb_saved_settings_notification', esc_html__('Settings have been saved.', 'simple-job-board')); ?></p>
                </div>
            <?php
            }
        }
    }
}

new Simple_Job_Board_Settings_Init();
