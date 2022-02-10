<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_mec extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_db
     */
    public $db;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * @var MEC_notifications
     */
    public $notifications;
    public $settings;
    public $page;
    public $PT;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Import MEC DB
        $this->db = $this->getDB();

        // Import MEC Main
        $this->main = $this->getMain();

        // Import MEC Notifications
        $this->notifications = $this->getNotifications();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    /**
     * Initialize calendars feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        $this->factory->action('admin_menu', array($this, 'menus'));
        $this->factory->action('admin_menu', array($this, 'support_menu'), 21);
        $this->factory->action('init', array($this, 'register_post_type'));
        $this->factory->action('add_meta_boxes', array($this, 'register_meta_boxes'), 1);

        $this->factory->action('parent_file', array($this, 'mec_parent_menu_highlight'));
        $this->factory->action('submenu_file', array($this, 'mec_sub_menu_highlight'));

        $this->factory->action('current_screen', array($this, 'booking_badge'));
        $this->factory->action('current_screen', array($this, 'events_badge'));

        // Google recaptcha
        $this->factory->filter('mec_grecaptcha_include', array($this, 'grecaptcha_include'));

        // Google Maps API
        $this->factory->filter('mec_gm_include', array($this, 'gm_include'));

        $this->factory->filter('manage_mec_calendars_posts_columns', array($this, 'filter_columns'));
        $this->factory->action('manage_mec_calendars_posts_custom_column', array($this, 'filter_columns_content'), 10, 2);

        $this->factory->action('save_post', array($this, 'save_calendar'), 10);

        // BuddyPress Integration
        $this->factory->action('mec_booking_confirmed', array($this->main, 'bp_add_activity'), 10);
        $this->factory->action('mec_booking_verified', array($this->main, 'bp_add_activity'), 10);
        $this->factory->action('bp_register_activity_actions', array($this->main, 'bp_register_activity_actions'), 10);
        $this->factory->action('bp_setup_nav', array($this->main, 'bp_add_profile_menu'));

        // Mailchimp Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'mailchimp_add_subscriber'), 10);

        // Campaign Monitor Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'campaign_monitor_add_subscriber'), 10);

        // MailerLite Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'mailerlite_add_subscriber'), 10);

        // Constant Contact Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'constantcontact_add_subscriber'), 10);

        // Active Campaign Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'active_campaign_add_subscriber'), 10);

        // AWeber Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'aweber_add_subscriber'), 10);

        // MailPoet Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'mailpoet_add_subscriber'), 10);

        // Sendfox Integration
        $this->factory->action('mec_booking_verified', array($this->main, 'sendfox_add_subscriber'), 10);

        // MEC Notifications
        $this->factory->action('mec_booking_completed', array($this->notifications, 'email_verification'), 10);
        $this->factory->action('mec_booking_completed', array($this->notifications, 'booking_notification'), 11);
        $this->factory->action('mec_booking_completed', array($this->notifications, 'admin_notification'), 12);
        $this->factory->action('mec_booking_confirmed', array($this->notifications, 'booking_confirmation'), 10, 2);
        $this->factory->action('mec_booking_canceled', array($this->notifications, 'booking_cancellation'), 12);
        $this->factory->action('mec_booking_rejected', array($this->notifications, 'booking_rejection'), 12);
        $this->factory->action('mec_fes_added', array($this->notifications, 'new_event'), 50, 2);
        $this->factory->action('mec_after_publish_admin_event', array($this->notifications, 'new_event'), 10, 2);
        $this->factory->action('mec_event_published', array($this->notifications, 'user_event_publishing'), 10, 3);
        $this->factory->action('mec_event_soldout', array($this->notifications, 'event_soldout'), 10, 2);

        $this->page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'MEC-settings';

        // MEC Post Type Name
        $this->PT = $this->main->get_main_post_type();

        // Disable Block Editor
        $gutenberg_status = (!isset($this->settings['gutenberg']) or (isset($this->settings['gutenberg']) and $this->settings['gutenberg'])) ? true : false;
        if($gutenberg_status)
        {
            $this->factory->filter('gutenberg_can_edit_post_type', array($this, 'gutenberg'), 10, 2);
            $this->factory->filter('use_block_editor_for_post_type', array($this, 'gutenberg'), 10, 2);
        }

        // Export Settings
        $this->factory->action('wp_ajax_download_settings', array($this, 'download_settings'));

        // Import Settings
        $this->factory->action('wp_ajax_import_settings', array($this, 'import_settings'));

        // License Activation
        $this->factory->action('wp_ajax_activate_license', array($this, 'activate_license'));

        // Close Notification
        $this->factory->action('wp_ajax_close_notification', array($this, 'close_notification'));

        // Close Custom Text Notification
        $this->factory->action('wp_ajax_close_cmsg_notification', array($this, 'close_cmsg_notification'));
        $this->factory->action('wp_ajax_close_cmsg_2_notification', array($this, 'close_cmsg_2_notification'));

        // Occurences Dropdown
        $this->factory->action('wp_ajax_mec_occurrences_dropdown', array($this, 'dropdown'));

        // Close Custom Text Notification
        $this->factory->action('wp_ajax_report_event_dates', array($this, 'report_event_dates'));

        // Scheduler Cronjob
        $schedule = $this->getSchedule();
        $this->factory->action('mec_scheduler', array($schedule, 'cron'));

        $syncSchedule = $this->getSyncSchedule();
        $this->factory->action('mec_syncScheduler', array($syncSchedule, 'sync'));

        // Dashborad Metaboxes
        $this->factory->action('wp_dashboard_setup', array($this, 'dashboard_widgets'));

        // Dashborad Metabox Total Bookingajax
        $this->factory->action('wp_ajax_total-booking-get-reports',array($this, 'dashboard_widget_total_booking_ajax_handler'));

        // Custom Capability Map
        $this->factory->filter('map_meta_cap', array($this, 'map_meta_cap'), 10, 4);

        // Protected Content Shortcode
        if($this->getPRO()) $this->factory->shortcode('mec-only-booked-users', array($this, 'only_booked_users_content'));

        // Assets Per Page
        $this->factory->filter('mec_include_frontend_assets', array($this, 'assets_per_page'));
        if(isset($this->settings['assets_per_page_status']) and $this->settings['assets_per_page_status'])
        {
            $this->factory->action('add_meta_boxes', array($this, 'register_assets_per_page_meta_boxes'), 1);
            $this->factory->action('save_post', array($this, 'assets_per_page_save_page'), 10, 2);
        }

        // SEO Title
        $this->factory->filter('pre_get_document_title', array($this, 'page_title'), 1000);
    }

    /* Activate License */
    public function activate_license()
    {
        // Current User is not Permitted
        if(!current_user_can('manage_options')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        if(!wp_verify_nonce($_REQUEST['nonce'], 'mec_settings_nonce'))
        {
            exit();
        }

        $options = get_option('mec_options');

        $options['product_name'] = $_REQUEST['content']['LicenseTypeJson'];
        $options['purchase_code'] = $_REQUEST['content']['PurchaseCodeJson'];
        update_option( 'mec_options', $options);

        $verify = NULL;
        if($this->getPRO())
        {
            $envato = $this->getEnvato();
            $verify = $envato->get_MEC_info('dl');
        }

        if(!is_null($verify))
        {
            $LicenseStatus = 'success';
            update_option( 'mec_license_status', 'active');
        }
        else
        {
            $LicenseStatus = __('Activation failed. Please check your purchase code or license type.<br><b>Note: Your purchase code should match your licesne type.</b>', 'modern-events-calendar-lite') . '<a style="text-decoration: underline; padding-left: 7px;" href="https://webnus.net/dox/modern-events-calendar/auto-update-issue/" target="_blank">'  . __('Troubleshooting', 'modern-events-calendar-lite') . '</a>';
            update_option( 'mec_license_status', 'faild');
        }

        echo $LicenseStatus;
        wp_die();
    }

    /* Download MEC settings */
    public function download_settings()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        if(!wp_verify_nonce($_REQUEST['nonce'], 'mec_settings_download'))
        {
            exit();
        }

        $content = get_option('mec_options');
        $content = json_encode($content, true);

        header('Content-type: application/txt');
        header('Content-Description: MEC Settings');
        header('Content-Disposition: attachment; filename="mec_options_backup_' . date( 'd-m-Y' ) . '.json"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        print_r($content);
        wp_die();
    }

    /* Close addons notification */
    public function close_notification()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));
        if(!wp_verify_nonce( $_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        update_option('mec_addons_notification_option', 'open');
        wp_die();
    }

    /* Close addons notification */
    public function close_cmsg_2_notification()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));
        if(!wp_verify_nonce( $_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        update_option('mec_custom_msg_2_close_option', 'open');
        wp_die();
    }

    /* Close addons notification */
    public function close_cmsg_notification()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));
        if(!wp_verify_nonce( $_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        update_option('mec_custom_msg_close_option', 'open');
        wp_die();
    }


    /* Report Event Dates */
    public function report_event_dates()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_report')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));
        if(!wp_verify_nonce($_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        $event_id = $_POST['event_id'];
        $feature_class = new MEC_feature_mec();

        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        $bookings_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0;

        if($event_id != 'none')
        {
            $dates = $feature_class->db->select("SELECT `tstart`, `tend` FROM `#__mec_dates` WHERE `post_id`='".$event_id."' LIMIT 100");
            $occurrence = reset($dates)->tstart;

            $date_format = (isset($this->settings['booking_date_format1']) and trim($this->settings['booking_date_format1'])) ? $this->settings['booking_date_format1'] : 'Y-m-d';
            if(get_post_meta($event_id, 'mec_repeat_type', true) === 'custom_days') $date_format .= ' '.get_option('time_format');

            echo '<select name="mec-report-event-dates" class="mec-reports-selectbox mec-reports-selectbox-dates" onchange="mec_event_attendees('.$event_id.', this.value);">';
            echo '<option value="none">'.esc_html__( "Select Date" , "mec").'</option>';

            if($bookings_all_occurrences)
            {
                echo '<option value="all">'.esc_html__( "All" , "mec").'</option>';
            }

            foreach($dates as $date)
            {
                $start = array(
                    'date' => date('Y-m-d', $date->tstart),
                    'hour' => date('h', $date->tstart),
                    'minutes' => date('i', $date->tstart),
                    'ampm' => date('A', $date->tstart),
                );

                $end = array(
                    'date' => date('Y-m-d', $date->tend),
                    'hour' => date('h', $date->tend),
                    'minutes' => date('i', $date->tend),
                    'ampm' => date('A', $date->tend),
                );

                echo '<option value="'.$date->tstart.'" '.($occurrence == $date->tstart ? 'class="selected-day"' : '').'>'.strip_tags($this->main->date_label($start, $end, $date_format, ' - ', false)).'</option>';
            }

            echo '</select>';
        }
        else
        {
            echo '';
        }

        wp_die();
    }

    /* Import MEC settings */
    public function import_settings()
    {
        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->main->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));
        if(!wp_verify_nonce($_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        $options = $_REQUEST['content'];
        if($options == 'No-JSON')
        {
            echo '<div class="mec-message-import-error">' . esc_html__('Your option is not in JSON format. Please insert correct options in this field and try again.', 'modern-events-calendar-lite') . '</div>';
            exit();
        }
        else
        {
            if(empty($options))
            {
                echo '<div class="mec-message-import-error">' . esc_html__('Your options field can not be empty!', 'modern-events-calendar-lite') . '</div>';
                exit;
            }
            else
            {
                update_option('mec_options', $options);
                echo '<div class="mec-message-import-success">' . esc_html__('Your options imported successfuly.', 'modern-events-calendar-lite') . '</div>';
            }
        }

        wp_die();
    }

    /**
     * highlighting menu when click on taxonomy
     * @author Webnus <info@webnus.biz>
     * @param string $parent_file
     * @return string
     */
    public function mec_parent_menu_highlight($parent_file)
    {
        global $current_screen;

        $taxonomy = $current_screen->taxonomy;
        $post_type = $current_screen->post_type;

        // Don't do amything if the post type is not our post type
        if(!in_array($post_type, array($this->PT, $this->main->get_email_post_type()))) return $parent_file;

        // Email Post Type
        if($post_type == $this->main->get_email_post_type()) return 'mec-intro';

        // Tag Taxonomy
        $tag_taxonomy = apply_filters('mec_taxonomy_tag', '');

        switch($taxonomy)
        {
            case 'mec_category':
            case $tag_taxonomy:
            case 'mec_label':
            case 'mec_location':
            case 'mec_organizer':
            case 'mec_speaker':

                $parent_file = 'mec-intro';
                break;

            default:
                //nothing
                break;
        }

        return $parent_file;
    }

    public function mec_sub_menu_highlight($submenu_file)
    {
        global $current_screen;

        $taxonomy = $current_screen->taxonomy;
        $post_type = $current_screen->post_type;

        // Don't do amything if the post type is not our post type
        if(!in_array($post_type, array($this->PT, $this->main->get_email_post_type()))) return $submenu_file;

        // Email Post Type
        if($post_type == $this->main->get_email_post_type()) return 'edit.php?post_type=mec-emails';

        // Tag Taxonomy
        $tag_taxonomy = apply_filters('mec_taxonomy_tag', '');

        switch($taxonomy)
        {
            case 'mec_category':

                $submenu_file = 'edit-tags.php?taxonomy=mec_category&post_type='.$this->PT;
                break;
            case $tag_taxonomy:

                $submenu_file = 'edit-tags.php?taxonomy='.$tag_taxonomy.'&post_type='.$this->PT;
                break;
            case 'mec_label':

                $submenu_file = 'edit-tags.php?taxonomy=mec_label&post_type='.$this->PT;
                break;
            case 'mec_location':

                $submenu_file = 'edit-tags.php?taxonomy=mec_location&post_type='.$this->PT;
                break;
            case 'mec_organizer':

                $submenu_file = 'edit-tags.php?taxonomy=mec_organizer&post_type='.$this->PT;
                break;
            case 'mec_speaker':

                $submenu_file = 'edit-tags.php?taxonomy=mec_speaker&post_type='.$this->PT;
                break;
            default:
                //nothing
                break;
        }

        return $submenu_file;
    }

    /**
     * Add the support menu
     * @author Webnus <info@webnus.biz>
     */
    public function support_menu()
    {
        add_submenu_page('mec-intro', __('MEC - Support', 'modern-events-calendar-lite'), __('Support', 'modern-events-calendar-lite'), 'manage_options', 'MEC-support', array($this, 'support_page'));
    }

    /**
     * Add the calendars menu
     * @author Webnus <info@webnus.biz>
     */
    public function menus()
    {
        global $submenu;
        unset($submenu['mec-intro'][2]);

        remove_menu_page('edit.php?post_type=mec-events');
        remove_menu_page('edit.php?post_type=mec_calendars');
        do_action('before_mec_submenu_action');

        add_submenu_page('mec-intro', __('Add Event', 'modern-events-calendar-lite'), __('Add Event', 'modern-events-calendar-lite'), 'edit_posts', 'post-new.php?post_type='.$this->PT);
        add_submenu_page('mec-intro', __('Tags', 'modern-events-calendar-lite'), __('Tags', 'modern-events-calendar-lite'), 'edit_others_posts', 'edit-tags.php?taxonomy='.apply_filters('mec_taxonomy_tag', '').'&post_type='.$this->PT);
        add_submenu_page('mec-intro', esc_html($this->main->m('taxonomy_categories', __('Categories', 'modern-events-calendar-lite'))), esc_html($this->main->m('taxonomy_categories', __('Categories', 'modern-events-calendar-lite'))), 'edit_others_posts', 'edit-tags.php?taxonomy=mec_category&post_type='.$this->PT);
        add_submenu_page('mec-intro', esc_html($this->main->m('taxonomy_labels', __('Labels', 'modern-events-calendar-lite'))), esc_html($this->main->m('taxonomy_labels', __('Labels', 'modern-events-calendar-lite'))), 'edit_others_posts', 'edit-tags.php?taxonomy=mec_label&post_type='.$this->PT);
        add_submenu_page('mec-intro', esc_html($this->main->m('taxonomy_locations', __('Locations', 'modern-events-calendar-lite'))), esc_html($this->main->m('taxonomy_locations', __('Locations', 'modern-events-calendar-lite'))), 'edit_others_posts', 'edit-tags.php?taxonomy=mec_location&post_type='.$this->PT);
        add_submenu_page('mec-intro', esc_html($this->main->m('taxonomy_organizers', __('Organizers', 'modern-events-calendar-lite'))), esc_html($this->main->m('taxonomy_organizers', __('Organizers', 'modern-events-calendar-lite'))), 'edit_others_posts', 'edit-tags.php?taxonomy=mec_organizer&post_type='.$this->PT);

        // Speakers Menu
        if(isset($this->settings['speakers_status']) and $this->settings['speakers_status'])
        {
            add_submenu_page('mec-intro', esc_html($this->main->m('taxonomy_speakers', __('Speakers', 'modern-events-calendar-lite'))), esc_html($this->main->m('taxonomy_speakers', __('Speakers', 'modern-events-calendar-lite'))), 'edit_others_posts', 'edit-tags.php?taxonomy=mec_speaker&post_type='.$this->PT);
        }

        $capability = (current_user_can('administrator') ? 'manage_options' : 'mec_shortcodes');
        add_submenu_page('mec-intro', __('Shortcodes', 'modern-events-calendar-lite'), __('Shortcodes', 'modern-events-calendar-lite'), $capability, 'edit.php?post_type=mec_calendars');

        // Auto Email Menu
        if(isset($this->settings['auto_emails_module_status']) and $this->settings['auto_emails_module_status'])
        {
            $capability = 'manage_options';
            add_submenu_page('mec-intro', __('Emails', 'modern-events-calendar-lite'), __('Emails', 'modern-events-calendar-lite'), $capability, 'edit.php?post_type=mec-emails');
        }

        $capability = (current_user_can('administrator') ? 'manage_options' : 'mec_settings');
        add_submenu_page('mec-intro', __('MEC - Settings', 'modern-events-calendar-lite'), __('Settings', 'modern-events-calendar-lite'), $capability, 'MEC-settings', array($this, 'page'));

        add_submenu_page('mec-intro', __('MEC - Addons', 'modern-events-calendar-lite'), __('Addons', 'modern-events-calendar-lite'), 'manage_options', 'MEC-addons', array($this, 'addons'));
        add_submenu_page('mec-intro', __('MEC - Wizard', 'modern-events-calendar-lite'), __('Wizard', 'modern-events-calendar-lite'), 'manage_options', 'MEC-wizard', array($this, 'setup_wizard'));

        if(isset($this->settings['booking_status']) and $this->settings['booking_status'])
        {
            add_submenu_page('mec-intro', __('MEC - Report', 'modern-events-calendar-lite'), __('Report', 'modern-events-calendar-lite'), 'mec_report', 'MEC-report', array($this, 'report'));
        }

        if(!$this->getPRO()) add_submenu_page('mec-intro', __('MEC - Go Pro', 'modern-events-calendar-lite'), __('Go Pro', 'modern-events-calendar-lite'), 'manage_options', 'MEC-go-pro', array($this, 'go_pro'));
        do_action('after_mec_submenu_action');
    }

    
    /**
     * Get Wizard page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function setup_wizard()
    {
        $this->display_wizard();
    }

    /**
     * Show Wizard page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function display_wizard()
    {
        $path = MEC::import('app.features.mec.wizard', true, true);
        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Register post type of calendars/custom shortcodes
     * @author Webnus <info@webnus.biz>
     *
     */
    public function register_post_type()
    {
        $elementor = class_exists('MEC_Shortcode_Builder') && did_action('elementor/loaded') ? true : false;

        register_post_type('mec_calendars',
            array(
                'labels'=>array
                (
                    'name'=>__('Shortcodes', 'modern-events-calendar-lite'),
                    'singular_name'=>__('Shortcode', 'modern-events-calendar-lite'),
                    'add_new'=>__('Add Shortcode', 'modern-events-calendar-lite'),
                    'add_new_item'=>__('Add New Shortcode', 'modern-events-calendar-lite'),
                    'not_found'=>__('No shortcodes found!', 'modern-events-calendar-lite'),
                    'all_items'=>__('All Shortcodes', 'modern-events-calendar-lite'),
                    'edit_item'=>__('Edit shortcodes', 'modern-events-calendar-lite'),
                    'not_found_in_trash'=>__('No shortcodes found in Trash!', 'modern-events-calendar-lite')
                ),
                'public'=>$elementor,
                'show_in_nav_menus'=>false,
                'show_in_admin_bar'=>$elementor,
                'show_ui'=>true,
                'has_archive'=>false,
                'exclude_from_search'=>true,
                'publicly_queryable'=>$elementor,
                'show_in_menu'=>'mec-intro',
                'supports'=>array('title'),
            )
        );

        do_action('mec_register_post_type');
    }

    /**
     * Filter columns of calendars/custom shortcodes
     * @author Webnus <info@webnus.biz>
     * @param array $columns
     * @return array
     */
    public function filter_columns($columns)
    {
        $columns['shortcode'] = __('Shortcode', 'modern-events-calendar-lite');
        return $columns;
    }

    /**
     * Filter column content of calendars/custom shortcodes
     * @author Webnus <info@webnus.biz>
     * @param string $column_name
     * @param int $post_id
     */
    public function filter_columns_content($column_name, $post_id)
    {
        if($column_name == 'shortcode')
        {
            echo '[MEC id="'.$post_id.'"]';
        }
    }

    /**
     * Register meta boxes of calendars/custom shortcodes
     * @author Webnus <info@webnus.biz>
     */
    public function register_meta_boxes()
    {
        // Fix conflict between Ultimate GDPR and niceSelect
        $screen = get_current_screen();
        if ( $screen->id == 'mec_calendars' ) remove_all_actions('acf/input/admin_head');

		add_meta_box('mec_calendar_display_options', __('Display Options', 'modern-events-calendar-lite'), array($this, 'meta_box_display_options'), 'mec_calendars', 'normal', 'high');
        add_meta_box('mec_calendar_filter', __('Filter Options', 'modern-events-calendar-lite'), array($this, 'meta_box_filter'), 'mec_calendars', 'normal', 'high');
        add_meta_box('mec_calendar_shortcode', __('Shortcode', 'modern-events-calendar-lite'), array($this, 'meta_box_shortcode'), 'mec_calendars', 'side');
        add_meta_box('mec_calendar_search_form', __('Search Form', 'modern-events-calendar-lite'), array($this, 'meta_box_search_form'), 'mec_calendars', 'side');
    }

    /**
     * Save calendars/custom shortcodes
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return void
     */
    public function save_calendar($post_id)
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_calendar_nonce'])) return;

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['mec_calendar_nonce']), 'mec_calendar_data')) return;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

        $terms = isset($_POST['mec_tax_input']) ? $_POST['mec_tax_input'] : array();

        $categories = (isset($terms['mec_category']) and is_array($terms['mec_category'])) ? implode(',', $terms['mec_category']) : '';
        $locations = (isset($terms['mec_location']) and is_array($terms['mec_location'])) ? implode(',', $terms['mec_location']) : '';
        $organizers = (isset($terms['mec_organizer']) and is_array($terms['mec_organizer'])) ? implode(',', $terms['mec_organizer']) : '';
        $labels = (isset($terms['mec_label']) and is_array($terms['mec_label'])) ? implode(',', $terms['mec_label']) : '';
        $tags = (isset($terms['mec_tag'])) ? explode(',', trim($terms['mec_tag'])) : '';
        $authors = (isset($terms['mec_author']) and is_array($terms['mec_author'])) ? implode(',', $terms['mec_author']) : '';

        // Fix tags
        if(is_array($tags) and count($tags) == 1 and trim($tags[0]) == '') $tags = array();
        if(is_array($tags))
        {
            $tags = array_map('trim', $tags);
            $tags = implode(',', $tags);
        }

        update_post_meta($post_id, 'label', $labels);
        update_post_meta($post_id, 'category', $categories);
        update_post_meta($post_id, 'location', $locations);
        update_post_meta($post_id, 'organizer', $organizers);
        update_post_meta($post_id, 'tag', $tags);
        update_post_meta($post_id, 'author', $authors);

        do_action('mec_shortcode_filters_save', $post_id, $terms );

        $mec = (isset($_POST['mec']) ? $_POST['mec'] : array());
        
        $skin = (isset($mec['skin']) ? $mec['skin'] : '');
        $start_date_type = ((isset($mec['sk-options'][$skin]) and isset($mec['sk-options'][$skin]['start_date_type'])) ? $mec['sk-options'][$skin]['start_date_type'] : 'today');
        
        $ongoing = ((isset($mec['show_only_ongoing_events']) and $mec['show_only_ongoing_events']) ? 1 : 0);

        // Set start date to Today because of showing ongoing events
        if($ongoing and in_array($skin, array('list', 'grid', 'agenda', 'timeline'))) $mec['sk-options'][$skin]['start_date_type'] = 'today';
        // Enable "Show Past Events" option since the start date is past
        elseif(in_array($start_date_type, array('yesterday', 'start_last_year', 'start_last_month', 'start_last_week'))) $mec['show_past_events'] = 1;

        // Set date filter type to dropdown because of skin
        if(!in_array($skin, array('list', 'grid', 'agenda', 'timeline', 'map')) and $mec['sf-options'][$skin]['month_filter']['type'] == 'date-range-picker') $mec['sf-options'][$skin]['month_filter']['type'] = 'dropdown';

        foreach($mec as $key=>$value) update_post_meta($post_id, $key, $value);
    }

    /**
     * Show content of filter meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_filter($post)
    {
        $path = MEC::import('app.features.mec.meta_boxes.filter', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of shortcode meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_shortcode($post)
    {
        $path = MEC::import('app.features.mec.meta_boxes.shortcode', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of search form meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_search_form($post)
    {
        $path = MEC::import('app.features.mec.meta_boxes.search_form', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of display options meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_display_options($post)
    {
        $path = MEC::import('app.features.mec.meta_boxes.display_options', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of skin options meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_skin_options($post)
    {
        $path = MEC::import('app.features.mec.meta_boxes.skin_options', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Get Addons page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function go_pro()
    {
        $this->display_go_pro();
    }

    /**
     * Show go_pro page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function display_go_pro()
    {
        $path = MEC::import('app.features.mec.go-pro', true, true);
        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Get Addons page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function addons()
    {
        $this->display_addons();
    }

    /**
     * Show Addons page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function display_addons()
    {
        $path = MEC::import('app.features.mec.addons', true, true);
        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Get Report page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function report()
    {
        $this->display_report();
    }

    /**
     * Show report page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function display_report()
    {
        $path = MEC::import('app.features.mec.report', true, true);

        ob_start();
        include $path;
        do_action('mec_display_report_page', $path);
        echo $output = ob_get_clean();
    }

    /**
     * Show support page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function display_support()
    {
        $path = MEC::import('app.features.mec.support-page', true, true);
        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * support page
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function support_page()
    {
        $this->display_support();
    }

    /**
     * Show content settings menu
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function page()
    {
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'MEC-settings';

        if($tab == 'MEC-customcss') $this->styles();
        elseif($tab == 'MEC-ie') $this->import_export();
        elseif($tab == 'MEC-notifications') $this->notifications();
        elseif($tab == 'MEC-messages') $this->messages();
        elseif($tab == 'MEC-styling') $this->styling();
        elseif($tab == 'MEC-single') $this->single();
        elseif($tab == 'MEC-booking') $this->booking();
        elseif($tab == 'MEC-modules') $this->modules();
        elseif($tab == 'MEC-integrations') $this->integrations();
        elseif (apply_filters('mec_is_custom_settings',false,$tab)){
        	do_action('mec_display_settings_page',$tab);
		}
        else $this->settings();
    }

    /**
     * Show content of settings tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function settings()
    {
        $path = MEC::import('app.features.mec.settings', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of styles tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function styles()
    {
        $path = MEC::import('app.features.mec.styles', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of styling tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function styling()
    {
        $path = MEC::import('app.features.mec.styling', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of single tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function single()
    {
        $path = MEC::import('app.features.mec.single', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of booking tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function booking()
    {
        $path = MEC::import('app.features.mec.booking', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of modules tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function modules()
    {
        $path = MEC::import('app.features.mec.modules', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of import/export tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function import_export()
    {
        $path = MEC::import('app.features.mec.ie', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of notifications tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function notifications()
    {
        $path = MEC::import('app.features.mec.notifications', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of messages tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function messages()
    {
        $path = MEC::import('app.features.mec.messages', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Show content of integrations tab
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function integrations()
    {
        $path = MEC::import('app.features.mec.integrations', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Whether to include google recaptcha library
     * @author Webnus <info@webnus.biz>
     * @param boolean $grecaptcha_include
     * @return boolean
     */
    public function grecaptcha_include($grecaptcha_include)
    {
        // Don't include the library if google recaptcha is not enabled
        if(!$this->main->get_recaptcha_status()) return false;

        return $grecaptcha_include;
    }

    /**
     * Whether to include google map library
     * @author Webnus <info@webnus.biz>
     * @param boolean $gm_include
     * @return boolean
     */
    public function gm_include($gm_include)
    {
        // Don't include the library if google Maps API is set to don't load
        if(isset($this->settings['google_maps_dont_load_api']) and $this->settings['google_maps_dont_load_api']) return false;

        return $gm_include;
    }

    /**
     * Single Event Display Method
     * @param string $skin
     * @param int $value
     * @param int $image_popup
     * @return string
     */
    public function sed_method_field($skin, $value = 0, $image_popup = 0)
    {
        $image_popup_html = '<div class="mec-form-row mec-image-popup-wrap mec-switcher">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_image_popup">'.__('Display content\'s images as Popup', 'modern-events-calendar-lite').'</label>
            </div>
            <div class="mec-col-4">
                <input type="hidden" name="mec[sk-options]['.$skin.'][image_popup]" value="0" />
                <input type="checkbox" name="mec[sk-options]['.$skin.'][image_popup]" id="mec_skin_'.$skin.'_image_popup" value="1"';

            if($image_popup == 1) $image_popup_html .= 'checked="checked"';

            $image_popup_html .= '/><label for="mec_skin_'.$skin.'_image_popup"></label>
            </div>
        </div>';

        return '<div class="mec-form-row mec-sed-method-wrap">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_sed_method">'.__('Single Event Display Method', 'modern-events-calendar-lite').'</label>
            </div>
            <div class="mec-col-8">
                <input type="hidden" name="mec[sk-options]['.$skin.'][sed_method]" value="'.$value.'" id="mec_skin_'.$skin.'_sed_method_field" />
                <ul class="mec-sed-methods" data-for="#mec_skin_'.$skin.'_sed_method_field">
                    <li data-method="0" class="'.(!$value ? 'active' : '').'">'.__('Current Window', 'modern-events-calendar-lite').'</li>
                    <li data-method="new" class="'.($value === 'new' ? 'active' : '').'">'.__('New Window', 'modern-events-calendar-lite').'</li>
                    <li data-method="m1" class="'.($value === 'm1' ? 'active' : '').'">'.__('Modal Popup', 'modern-events-calendar-lite').'</li>
                    <li data-method="no" class="'.($value === 'no' ? 'active' : '').'">'.__('Disable Link', 'modern-events-calendar-lite').'</li>
                </ul>
            </div>
        </div>' . $image_popup_html;
    }

    public function booking_button_field($skin, $value = 0)
    {
        $booking_status = (!isset($this->settings['booking_status']) or (isset($this->settings['booking_status']) and !$this->settings['booking_status'])) ? false : true;
        if(!$booking_status) return '';

        return '<div class="mec-form-row mec-booking-button-wrap mec-switcher">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_booking_button">'.__('Booking Button / Icon', 'modern-events-calendar-lite').'</label>
            </div>
            <div class="mec-col-4">
                <input type="hidden" name="mec[sk-options]['.$skin.'][booking_button]" value="0" />
                <input type="checkbox" name="mec[sk-options]['.$skin.'][booking_button]" id="mec_skin_'.$skin.'_booking_button" value="1" '.($value == '1' ? 'checked="checked"' : '').' /><label for="mec_skin_'.$skin.'_booking_button"></label>
            </div>
        </div>';
    }

    public function display_organizer_field($skin, $value = 0)
    {
        return '<div class="mec-form-row mec-display-organizer-wrap mec-switcher">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_display_organizer">'.__('Display Organizers', 'modern-events-calendar-lite').'</label>
            </div>
            <div class="mec-col-4">
                <input type="hidden" name="mec[sk-options]['.$skin.'][display_organizer]" value="0" />
                <input type="checkbox" name="mec[sk-options]['.$skin.'][display_organizer]" id="mec_skin_'.$skin.'_display_organizer" value="1" '.($value == '1' ? 'checked="checked"' : '').' /><label for="mec_skin_'.$skin.'_display_organizer"></label>
            </div>
        </div>';
    }

    public function display_custom_data_field($skin, $value = 0)
    {
        return '<div class="mec-form-row mec-display-organizer-wrap mec-switcher">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_custom_data">'.__('Display Custom Fields', 'modern-events-calendar-lite').'</label>
            </div>
            <div class="mec-col-4">
                <input type="hidden" name="mec[sk-options]['.$skin.'][custom_data]" value="0" />
                <input type="checkbox" name="mec[sk-options]['.$skin.'][custom_data]" id="mec_skin_'.$skin.'_custom_data" value="1" '.($value == '1' ? 'checked="checked"' : '').' /><label for="mec_skin_'.$skin.'_custom_data"></label>
            </div>
        </div>';
    }

    public function display_detailed_time_field($skin, $value = 0)
    {
        return '<div class="mec-form-row mec-switcher">
            <div class="mec-col-4">
                <label for="mec_skin_'.$skin.'_detailed_time">'.__('Detailed Time', 'modern-events-calendar-lite').'</label>
                <span>'.__('For Multiple Day Events', 'modern-events-calendar-lite').'</span>
            </div>
            <div class="mec-col-4">
                <input type="hidden" name="mec[sk-options]['.$skin.'][detailed_time]" value="0" />
                <input type="checkbox" name="mec[sk-options]['.$skin.'][detailed_time]" id="mec_skin_'.$skin.'_detailed_time" value="1" '.($value == '1' ? 'checked="checked"' : '').' /><label for="mec_skin_'.$skin.'_detailed_time"></label>
            </div>
        </div>';
    }

    /**
     * Disable Gutenberg Editor for MEC Post Types
     * @param boolean $status
     * @param string $post_type
     * @return bool
     */
    public function gutenberg($status, $post_type)
    {
        if(in_array($post_type, array($this->PT, $this->main->get_book_post_type(), $this->main->get_shortcode_post_type()))) return false;
        return $status;
    }

    /**
     * Show Booking Badge.
     * @param object $screen
     * @return void
     */
    public function booking_badge($screen)
    {
        $user_id = get_current_user_id();
        $user_last_view_date = get_user_meta($user_id, 'user_last_view_date', true);
        $count = 0;

        if(!trim($user_last_view_date))
        {
            update_user_meta($user_id, 'user_last_view_date', date('YmdHis', current_time('timestamp', 0)));
            return;
        }

        $args = array(
            'post_type' => $this->main->get_book_post_type(),
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'mec_book_date_submit',
                    'value' => $user_last_view_date,
                    'compare' => '>=',
                ),
            ),
        );

        $query = new WP_Query($args);
        if($query->have_posts())
        {
            while($query->have_posts())
            {
                $query->the_post();
                $count += 1;
            }
        }

        if($count != 0)
        {
            if(isset($screen->id) and $screen->id == 'edit-mec-books')
            {
                update_user_meta($user_id, 'user_last_view_date', date('YmdHis', current_time('timestamp', 0)));
                return;
            }

            // Append Booking Badge To Booking Menu.
            global $menu;

            $badge = ' <span class="update-plugins count-%%count%%"><span class="plugin-count">%%count%%</span></span>';
            $menu_item = wp_list_filter($menu, array(2 =>'edit.php?post_type='.$this->main->get_book_post_type()));
            if(is_array($menu_item) and count($menu_item))
            {
                $menu[key($menu_item)][0] .= str_replace('%%count%%', esc_attr($count), $badge);
            }
        }
    }

    /**
     * Show Events Badge.
     * @param object $screen
     * @return void
     */
    public function events_badge($screen)
    {
        if(!current_user_can('administrator') and !current_user_can('editor')) return;

        $user_id = get_current_user_id();
        $user_last_view_date_events = get_user_meta($user_id, 'user_last_view_date_events', true);
        $count = 0;

        if(!trim($user_last_view_date_events))
        {
            update_user_meta($user_id, 'user_last_view_date_events', date('YmdHis', current_time('timestamp', 0)));
            return;
        }

        $args = array(
            'post_type' => $this->main->get_main_post_type(),
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'mec_event_date_submit',
                    'value' => $user_last_view_date_events,
                    'compare' => '>=',
                ),
            ),
        );

        $query = new WP_Query($args);
        if($query->have_posts())
        {
            while($query->have_posts())
            {
                $query->the_post();
                $count += 1;
            }
        }

        if($count != 0)
        {
            if(isset($screen->id) and $screen->id == 'edit-mec-events')
            {
                update_user_meta($user_id, 'user_last_view_date_events', date('YmdHis', current_time('timestamp', 0)));
                return;
            }

            // Append Events Badge To Event Menu.
            global $menu;

            $badge = ' <span class="update-plugins count-%%count%%"><span class="plugin-count">%%count%%</span></span>';
            $menu_item = wp_list_filter($menu, array(2 =>'mec-intro'));
            if(is_array($menu_item) and count($menu_item))
            {
                $menu[key($menu_item)][0] .= str_replace('%%count%%', esc_attr($count), $badge);
            }
        }
    }

    /**
     * Add MEC metaboxes in WordPress dashboard
     * @author Webnus <info@webnus.biz>
     */
    public function dashboard_widgets()
    {
        wp_add_dashboard_widget(
            'mec_widget_news_features',
            __('Modern Events Calendar', 'modern-events-calendar-lite'),
            array($this, 'widget_news')
        );

        if($this->getPRO() and current_user_can('mec_settings') and isset($this->settings['booking_status']) and $this->settings['booking_status'])
        {
            wp_add_dashboard_widget(
                'mec_widget_total_bookings',
                __('Total Bookings', 'modern-events-calendar-lite'),
                array($this, 'widget_total_bookings')
            );
        }
    }

    /**
     * MEC render metabox in WordPress dashboard
     * @author Webnus <info@webnus.biz>
     */
    public function widget_news()
    {
        // Head Section
        echo '<div class="mec-metabox-head-wrap">
            <div class="mec-metabox-head-version">
                <img src="'.plugin_dir_url(__FILE__ ) . '../../assets/img/ico-mec-vc.png" />
                <p>'.($this->getPRO() ? __('Modern Events Calendar', 'modern-events-calendar-lite') : __('Modern Events Calendar (Lite)', 'modern-events-calendar-lite')).'</p>
                <a href="'.esc_html__(admin_url( 'post-new.php?post_type=mec-events' )).'" class="button"><span aria-hidden="true" class="dashicons dashicons-plus"></span> Create New Event</a>
            </div>
            <div class="mec-metabox-head-button"></div>
            <div style="clear:both"></div>
        </div>';

        // Upcoming Events
        $upcoming_events = $this->main->get_upcoming_events(3);
        echo '<div class="mec-metabox-upcoming-wrap"><h3 class="mec-metabox-feed-head">'.esc_html__('Upcoming Events', 'modern-events-calendar-lite').'</h3><ul>';
        foreach($upcoming_events as $date => $content)
        {
            foreach($content as $array_id => $event)
            {
                $location_id = $this->main->get_master_location_id($event);

                $event_title = $event->data->title;
                $event_link = $event->data->permalink;
                $event_date = $this->main->date_i18n(get_option('date_format'), $event->date['start']['date']);
                $location = get_term($location_id, 'mec_location');

                $locationName = '';
                if(isset($location->name)) $locationName = $location->name;

                echo '<li>
                    <span aria-hidden="true" class="dashicons dashicons-calendar-alt"></span>
                    <div class="mec-metabox-upcoming-event">
                        <a href="'.$event_link.'" target="">'.$event_title.'</a>
                        <div class="mec-metabox-upcoming-event-location">'.$locationName.'</div>
                    </div>
                    <div class="mec-metabox-upcoming-event-date">'.$event_date.'</div>
                    <div style="clear:both"></div>
                </li>';
            }
        }

        echo '</ul></div>';

        $mec_get_webnus_news_time = get_option('mec_get_webnus_news_time');
        if(!isset($mec_get_webnus_news_time) || !$mec_get_webnus_news_time)
        {
            $data_url = wp_remote_get( 'https://webnus.net/wp-json/wninfo/v1/posts', ['user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36']);
            $obj = ((is_array($data_url) and isset($data_url['body'])) ? json_decode($data_url['body']) : '');

            update_option('mec_get_webnus_news_time', date("Y-m-d"));
            update_option('mec_get_webnus_news_html', $obj);
        }
        else
        {
            if(strtotime(date("Y-m-d")) > strtotime($mec_get_webnus_news_time))
            {
                $data_url = wp_remote_get( 'https://webnus.net/wp-json/wninfo/v1/posts', ['user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36']);
                $obj = ((is_array($data_url) and isset($data_url['body'])) ? json_decode($data_url['body']) : '');

                update_option('mec_get_webnus_news_time', date("Y-m-d"));
                update_option('mec_get_webnus_news_html', $obj);
            }
            else
            {
                $obj = get_option('mec_get_webnus_news_html');
            }
        }

        // News
        if(!empty($obj))
        {
            echo '<h3 class="mec-metabox-feed-head">'.esc_html__('News & Updates', 'modern-events-calendar-lite').'</h3><div class="mec-metabox-feed-content"><ul>';
            foreach($obj as $key => $value)
            {
                echo '<li>
                    <a href="'.$value->link.'" target="_blank">'.$value->title.'</a>
                    <p>'.$value->content.'</p>
                </li>';
            }

            echo '</ul></div>';
        }

        // Links
        echo '<div class="mec-metabox-footer"><a href="https://webnus.net/blog/" target="_blank">'.esc_html__('Blog', 'modern-events-calendar-lite').'<span aria-hidden="true" class="dashicons dashicons-external"></span></a><a href="https://webnus.net/dox/modern-events-calendar/" target="_blank">'.esc_html__('Help', 'modern-events-calendar-lite').'<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
        if($this->getPRO()) echo '<a href="https://webnus.net/mec-purchase" target="_blank">'.esc_html__('Go Pro', 'modern-events-calendar-lite').'<span aria-hidden="true" class="dashicons dashicons-external"></span></a>';
        echo '</div>';
    }       

    public function dashboard_widget_total_booking_ajax_handler()
    {
        $start = isset($_REQUEST['start']) ? sanitize_text_field($_REQUEST['start']) : date('Y-m-d', strtotime('-15 days'));
        $end = isset($_REQUEST['end']) ? sanitize_text_field($_REQUEST['end']) : date('Y-m-d');
        $type = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'daily';
        $chart = isset($_REQUEST['chart']) ? sanitize_text_field($_REQUEST['chart']) : 'bar';
        
        ob_start();
        $this->display_total_booking_chart($start, $end, $type, $chart);
        $r = ob_get_clean();

        wp_send_json($r);
    }

    public function display_total_booking_chart($start, $end, $type = 'daily', $chart = 'bar')
    {
        $start = (!empty($start) ? $start : date('Y-m-d', strtotime('-15 days')));
        $end = (!empty($end) ? $end : date('Y-m-d'));

        $periods = $this->main->get_date_periods($start, $end, $type);

        $stats = '';
        $labels = '';
        foreach($periods as $period)
        {
            $post_type = $this->main->get_book_post_type();
            $posts_ids = $this->db->select("SELECT `ID` FROM `#__posts` WHERE `post_type`='".$post_type."' AND `post_date`>='".$period['start']."' AND `post_date`<='".$period['end']."'", 'loadColumn');

            if(count($posts_ids)) $total_sells = $this->db->select("SELECT SUM(`meta_value`) FROM `#__postmeta` WHERE `meta_key`='mec_price' AND `post_id` IN (".implode(',', $posts_ids).")", 'loadResult');
            else $total_sells = 0;

            $labels .= '"'.$period['label'].'",';
            $stats .= $total_sells.',';
        }

        $currency = $this->main->get_currency_sign();

        echo '<canvas id="mec_total_bookings_chart" width="600" height="300"></canvas>';
        echo '<script type="text/javascript">
            jQuery(document).ready(function()
            {
                var ctx = document.getElementById("mec_total_bookings_chart");
                var mecSellsChart = new Chart(ctx,
                {
                    type: "'.$chart.'",
                    data:
                    {
                        labels: ['.trim($labels, ', ').'],
                        datasets: [
                        {
                            label: "'.esc_js(sprintf(__('Total Sales (%s)', 'modern-events-calendar-lite'), $currency)).'",
                            data: ['.trim($stats, ', ').'],
                            backgroundColor: "rgba(159, 216, 255, 0.3)",
                            borderColor: "#36A2EB",
                            borderWidth: 1
                        }]
                    }
                });
            });
        </script>';
    }

    public function widget_total_bookings()
    {
        $current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

        wp_enqueue_script('mec-chartjs-script', $this->main->asset('js/chartjs.min.js'));
        wp_enqueue_script('mec-total-booking-reports-script', $this->main->asset('js/total-booking-reports.js'));
        wp_localize_script('mec-total-booking-reports-script','mec_ajax_data', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
        ?>
        <div class="w-row <?php echo (($current_page == 'dashboard') ? 'mec-dashboard-widget-total-bookings' : ''); ?>">
            <div class="w-col-sm-12">
                <div class="w-box total-bookings">
                    <div class="w-box-head">
                        <?php echo esc_html__('Total Bookings', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <ul>
                            <li class="mec-chart-this-month"><a href="<?php echo add_query_arg(array(
                                'sort' => 'this_month',
                                'start' => date('Y-m-01'),
                                'end' => date('Y-m-t'),
                                'type' => 'daily',
                            )); ?>"><?php _e('This Month', 'modern-events-calendar-lite'); ?></a></li>
                            <li class="mec-chart-last-month"><a href="<?php echo add_query_arg(array(
                                'sort' => 'last_month',
                                'start' => date('Y-m-01', strtotime('-1 Month')),
                                'end' => date('Y-m-t', strtotime('-1 Month')),
                                'type' => 'daily',
                            )); ?>"><?php _e('Last Month', 'modern-events-calendar-lite'); ?></a></li>
                            <li class="mec-chart-this-year"><a href="<?php echo add_query_arg(array(
                                'sort' => 'this_year',
                                'start' => date('Y-01-01'),
                                'end' => date('Y-12-31'),
                                'type' => 'monthly',
                            )); ?>"><?php _e('This Year', 'modern-events-calendar-lite'); ?></a></li>
                            <li class="mec-chart-last-year"><a href="<?php echo add_query_arg(array(
                                'sort' => 'last_year',
                                'start' => date('Y-01-01', strtotime('-1 Year')),
                                'end' => date('Y-12-31', strtotime('-1 Year')),
                                'type' => 'monthly',
                            )); ?>"><?php _e('Last Year', 'modern-events-calendar-lite'); ?></a></li>
                        </ul>
                        <script>                        
                        </script>
                        <?php
                            $start = date('Y-m-d', strtotime('-15 days'));
                            $end = date('Y-m-d');
                            $type = 'daily';
                            $chart = 'bar';
                        ?>
                        <form class="mec-sells-filter" method="GET" action="">
                            <?php if($current_page != 'dashboard'): ?><input type="hidden" name="page" value="mec-intro" /><?php endif; ?>
                            <input type="text" class="mec_date_picker" name="start" placeholder="<?php esc_attr_e('Start Date', 'modern-events-calendar-lite'); ?>" value="<?php echo $start; ?>" />
                            <input type="text" class="mec_date_picker" name="end" placeholder="<?php esc_attr_e('End Date', 'modern-events-calendar-lite'); ?>" value="<?php echo $end; ?>" />
                            <select name="type">
                                <option value="daily" <?php echo $type == 'daily' ? 'selected="selected"' : ''; ?>><?php _e('Daily', 'modern-events-calendar-lite'); ?></option>
                                <option value="monthly" <?php echo $type == 'monthly' ? 'selected="selected"' : ''; ?>><?php _e('Monthly', 'modern-events-calendar-lite'); ?></option>
                                <option value="yearly" <?php echo $type == 'yearly' ? 'selected="selected"' : ''; ?>><?php _e('Yearly', 'modern-events-calendar-lite'); ?></option>
                            </select>
                            <select name="chart">
                                <option value="bar" <?php echo $chart == 'bar' ? 'selected="selected"' : ''; ?>><?php _e('Bar', 'modern-events-calendar-lite'); ?></option>
                                <option value="line" <?php echo $chart == 'line' ? 'selected="selected"' : ''; ?>><?php _e('Line', 'modern-events-calendar-lite'); ?></option>
                            </select>
                            <button type="submit"><?php _e('Filter', 'modern-events-calendar-lite'); ?></button>
                        </form>
                        <div id="mec-total-booking-report">
                            <?php
                                $this->display_total_booking_chart($start,$end,$type,$chart);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function widget_print()
    {
        $start_year = $min_start_year = $this->db->select("SELECT MIN(cast(meta_value as unsigned)) AS date FROM `#__postmeta` WHERE `meta_key`='mec_start_date'", 'loadResult');
        $end_year = $max_end_year = $this->db->select("SELECT MAX(cast(meta_value as unsigned)) AS date FROM `#__postmeta` WHERE `meta_key`='mec_end_date'", 'loadResult');
        $current_month = current_time('m');
        ?>
        <div class="w-row">
            <div class="w-col-sm-12">
                <div class="w-box total-bookings print-events">
                    <div class="w-box-head">
                        <?php echo esc_html__('Print Calendar', 'modern-events-calendar-lite'); ?>
                    </div>
                    <div class="w-box-content">
                        <form method="GET" action="<?php echo home_url(); ?>" target="_blank">
                            <input type="hidden" name="method" value="mec-print">
                            <select name="mec-year" title="<?php esc_attr('Year', 'modern-events-calendar-lite'); ?>">
                                <?php for($i = $start_year; $i <= $end_year; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($i == date('Y', current_time('timestamp', 0))) ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="mec-month" title="<?php esc_attr('Month', 'modern-events-calendar-lite'); ?>">
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo ($i < 10 ? '0'.$i : $i); ?>" <?php echo ($current_month == $i ? 'selected="selected"' : ''); ?>><?php echo $this->main->date_i18n('F', mktime(0, 0, 0, $i, 10)); ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit"><?php _e('Display Events', 'modern-events-calendar-lite'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function map_meta_cap($caps, $cap, $user_id, $args)
    {
        if('mec_bookings' == $cap) $caps = array('mec_bookings');
        return $caps;
    }

    public function only_booked_users_content($atts, $content = '')
    {
        // Current User
        $user_id = get_current_user_id();

        // Guest User
        if(!$user_id) return '';

        // Event
        global $mec_current_event;

        // Invalid Event
        if(!$mec_current_event or ($mec_current_event and !isset($mec_current_event->ID))) return '';

        // Date
        $date = (isset($mec_current_event->date) ? $mec_current_event->date : array());
        $start_timestamp = (isset($date['start']) and isset($date['start']['timestamp'])) ? $date['start']['timestamp'] : NULL;
        $end_timestamp = (isset($date['end']) and isset($date['end']['timestamp'])) ? $date['end']['timestamp'] : NULL;

        // Invalid Date
        if(!$start_timestamp or !$end_timestamp) return '';

        // Not Booked
        if(!$this->main->is_user_booked($user_id, $mec_current_event->ID, $start_timestamp)) return '';

        // Booked
        return $content;
    }

    public function register_assets_per_page_meta_boxes()
    {
        add_meta_box('mec_metabox_app', __('Include MEC Assets', 'modern-events-calendar-lite'), array($this, 'meta_box_assets_per_page'), 'page', 'side', 'low');
    }

    public function meta_box_assets_per_page($post)
    {
        $mec_include_assets = get_post_meta($post->ID, 'mec_include_assets', true);
        ?>
        <div class="mec-assets-per-page-metabox">
            <label for="mec_include_assets">
                <input type="hidden" name="mec_include_assets" value="0" />
                <input type="checkbox" name="mec_include_assets" id="mec_include_assets" <?php echo ($mec_include_assets ? 'checked="checked"' : ''); ?> value="1" />
                <?php _e('Include Modern Events Calendar Assets (CSS, JavaScript, etc files.)', 'modern-events-calendar-lite'); ?>
            </label>
        </div>
        <?php
    }

    public function assets_per_page_save_page($post_id, $post)
    {
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

        // Not a Page
        if($post->post_type != 'page') return;

        if(isset($_POST['mec_include_assets']))
        {
            $mec_include_assets = sanitize_text_field($_POST['mec_include_assets']);
            update_post_meta($post_id, 'mec_include_assets', $mec_include_assets);
        }
    }

    public function assets_per_page($status)
    {
        // Turned Off
        if(!isset($this->settings['assets_per_page_status']) or (isset($this->settings['assets_per_page_status']) and !$this->settings['assets_per_page_status'])) return $status;
        // Turned On
        else
        {
            global $post;

            $status_per_page = 1;
            if($post->post_type === 'page')
            {
                $status_per_page = get_post_meta($post->ID, 'mec_include_assets', true);
                if(trim($status_per_page) == '') $status_per_page = 0;
            }

            $status = (boolean) $status_per_page;
        }

        return $status;
    }

    public function page_title($title)
    {
        // Occurrences Status
        $occurrences_status = (isset($this->settings['per_occurrences_status']) and $this->settings['per_occurrences_status'] and $this->getPRO());

        if(is_singular($this->main->get_main_post_type()) and $occurrences_status)
        {
            global $post;

            $timestamp = ((isset($_GET['time']) and $_GET['time']) ? $_GET['time'] : NULL);

            $occurrence = (isset($_GET['occurrence']) ? $_GET['occurrence'] : NULL);
            if(!$timestamp and $occurrence) $timestamp = strtotime($occurrence) + (int) get_post_meta($post->ID, 'mec_start_day_seconds', true);

            if(!$timestamp)
            {
                $render = $this->getRender();
                $dates = $render->dates($post->ID, NULL, 1, date('Y-m-d', strtotime('Yesterday')));

                if(isset($dates[0]) and isset($dates[0]['start']) and isset($dates[0]['start']['timestamp'])) $timestamp = $dates[0]['start']['timestamp'];
            }

            $title = MEC_feature_occurrences::param($post->ID, $timestamp, 'title', $title);
        }

        return $title;
    }

    public function dropdown()
    {
        // Check if our nonce is set.
        if(!isset($_POST['_wpnonce'])) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'mec_occurrences_dropdown')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        // Date is invalid!
        if(!trim($date) or !trim($id)) $this->main->response(array('success'=>0, 'code'=>'DATE_OR_ID_IS_INVALID'));

        $dates = explode(':', $date);

        $limit = 100;
        $now = $dates[0];
        $_6months_ago = strtotime('-6 Months', $now);

        $occ = new MEC_feature_occurrences();
        $occurrences = $occ->get_dates($id, $now, $limit);

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $datetime_format = $date_format.' '.$time_format;

        $success = 0;
        $html = '<option class="mec-load-occurrences" value="'.$_6months_ago.':'.$_6months_ago.'">'.__('Previous Occurrences', 'modern-events-calendar-lite').'</option>';

        $i = 1;
        foreach($occurrences as $occurrence)
        {
            $success  = 1;
            $html .= '<option value="'.$occurrence->tstart.':'.$occurrence->tend.'" '.($i === 1 ? 'selected="selected"' : '').'>'.(date_i18n($datetime_format, $occurrence->tstart)).'</option>';
            $i++;
        }

        if(count($occurrences) >= $limit and isset($occurrence)) $html .= '<option class="mec-load-occurrences" value="'.$occurrence->tstart.':'.$occurrence->tend.'">'.__('Next Occurrences', 'modern-events-calendar-lite').'</option>';

        $this->main->response(array('success'=>$success, 'html'=>$html));
    }
}
