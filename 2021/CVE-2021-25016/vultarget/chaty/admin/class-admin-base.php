<?php

namespace CHT\admin;

use CHT\includes\CHT_Widget;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CHT_Admin_Base
 * @since 1.0
 */

require_once 'class-social-icons.php';

class CHT_Admin_Base
{
    public $page;
    public $socials;
    public $fa_icons;
    public $widget_index = '';
    public $colors;
    protected $token;
    protected static $response = null;
    protected static $checked_token = false;
    protected $upgrade_slug;


    public function __construct()
    {
        $plugin = CHT_Widget::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->friendly_name = $plugin->get_name();
        $this->socials = CHT_Social_Icons::get_instance()->get_icons_list();
        $this->colors = CHT_Social_Icons::get_instance()->get_colors();
        $this->token = $this->get_token();
        $this->upgrade_slug = $this->plugin_slug . '-upgrade';

        if (is_admin()) { // admin actions
            add_action('admin_menu', array($this, 'cht_admin_setting_page'));
            // Adds all of the options for the administrative settings
            add_action('admin_init', array($this, 'cht_register_inputs'));
            add_action('admin_head', array($this, 'cht_inline_css_admin'));
        }
//        add_action('updated_option', array($this, 'cht_clear_all_caches'));

        /* Send message to owner */
        add_action('wp_ajax_wcp_admin_send_message_to_owner', array($this, 'wcp_admin_send_message_to_owner'));

        /* ADD Upgrade link to plugin */
        add_filter('plugin_action_links_' . CHT_PLUGIN_BASE, [$this, 'plugin_action_links']);

        add_action('admin_footer', array($this, 'add_deactivate_modal'));
        add_action('wp_ajax_chaty_plugin_deactivate', array($this, 'chaty_plugin_deactivate'));

	    add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'), 99);

	    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 99);

        add_action("wp_ajax_chaty_update_status", array($this, 'chaty_update_status'));

        /* load language files */
        add_action( 'plugins_loaded', array( $this, 'chaty_text' ) );

        add_action("wp_ajax_update_popup_status", array($this, 'update_popup_status'));
        add_action("wp_ajax_update_channel_setting", array($this, 'update_channel_setting'));
    }

    public function plugin_action_links($links)
    {
        $links['need_help'] = '<a target="_blank" href="https://premio.io/help/chaty/?utm_source=pluginspage" >'.__( 'Need help?', CHT_OPT ).'</a>';
        $links['go_pro'] = '<a style="color: #FF5983; font-weight: bold; display: inline-block; border: solid 1px #FF5983; border-radius: 4px; padding: 0 5px;" href="' . CHT_PRO_URL . '" class="chaty-plugins-gopro">' . esc_attr__('Upgrade', CHT_OPT) . '</a>';
        return $links;
    }

    /* chaty_plugin_deactivate start */
    public function chaty_plugin_deactivate()
    {
        $postData = $_POST;
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 1;
        $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
        if (empty($reason)) {
            $errorCounter++;
            $response['message'] = "Please provide reason";
        } else if (empty($nonce)) {
            $response['message'] = esc_attr__("Your request is not valid", CHT_OPT);
            $errorCounter++;
            $response['valid'] = 0;
        } else if(!current_user_can("manage_options")) {
            $response['message'] = esc_attr__("Your request is not valid", CHT_OPT);
            $errorCounter++;
            $response['valid'] = 0;
        } else {
            if (!wp_verify_nonce($nonce, 'chaty_deactivate_nonce')) {
                $response['message'] = esc_attr__("Your request is not valid", CHT_OPT);
                $errorCounter++;
                $response['valid'] = 0;
            }
        }
        if ($errorCounter == 0) {
            global $current_user;
            $email = "none@none.none";

            if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                $email = $postData['email_id'];
            }
            $domain = site_url();
            $user_name = $current_user->first_name . " " . $current_user->last_name;

            $response['status'] = 1;

	        /* sending message to Crisp */
	        $post_message = array();

	        $message_data = array();
	        $message_data['key'] = "Plugin";
	        $message_data['value'] = "Chaty";
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Plugin Version";
	        $message_data['value'] = CHT_VERSION;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Domain";
	        $message_data['value'] = $domain;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Email";
	        $message_data['value'] = $email;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "WordPress Version";
	        $message_data['value'] = esc_attr(get_bloginfo('version'));
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "PHP Version";
	        $message_data['value'] = PHP_VERSION;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Message";
	        $message_data['value'] = $reason;
	        $post_message[] = $message_data;

	        $api_params = array(
		        'domain' => $domain,
		        'email' => $email,
		        'url' => site_url(),
		        'name' => $user_name,
		        'message' => $post_message,
		        'plugin' => "Chaty",
		        'type' => "Uninstall",
	        );

	        /* Sending message to Crisp API */
	        $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

	        if (is_wp_error($crisp_response)) {
		        wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
	        }
        }
        echo json_encode($response);
        wp_die();
    }

    /* function to sanitize input values */
    public static function chaty_sanitize_options($value)
    {
        $value = stripslashes($value);
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $value = htmlspecialchars($value);
        return $value;
    }

    // add popup model html on Plugin List page
    public function add_deactivate_modal()
    {
        if(current_user_can("manage_options")) {
            global $pagenow;

            if ('plugins.php' !== $pagenow) {
                return;
            }

            include CHT_DIR . '/views/admin/chaty-deactivate-form.php';
        }
    }

    public function del_space($text)
    {
        return str_replace('_', ' ', $text);
    }

    public function cht_inline_css_admin()
    {
        ob_start();
        ?>
        <style>
            #toplevel_page_chaty-app img:hover, #toplevel_page_chaty-app img {                
                opacity: 0 !important;
              } 
            #toplevel_page_chaty-app:hover .dashicons-before{
                background-color: #00b9eb; 
                } 
            #toplevel_page_chaty-app .dashicons-before{
                background-color: #A0A3A8;              
                -webkit-mask: url('<?php echo esc_url(plugins_url('chaty/admin/assets/images/chaty.svg')) ?>') no-repeat center;
                mask: url('<?php echo esc_url(plugins_url('chaty/admin/assets/images/chaty.svg')) ?>') no-repeat center;
            }
            .current#toplevel_page_chaty-app .dashicons-before{
                background-color: #fff; 
            }
         </style>
        <?php
        echo ob_get_clean();
    }

    /* admin css files */
    public function enqueue_styles($page)
    {
//        echo $page; die;
	    if($page == 'toplevel_page_chaty-app' || $page == 'chaty_page_chaty-contact-form-feed' || $page == 'chaty_page_widget-analytics' || $page == "chaty_page_chaty-upgrade") {

		    $query_args = array(
			    'family' => 'Rubik:400,700|Oswald:400,600',
			    'subset' => 'latin,latin-ext'
		    );
		    wp_enqueue_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
		    wp_enqueue_style( $this->plugin_slug . 'spectrum', plugins_url( '../admin/assets/css/spectrum.min.css', __FILE__ ), array(), CHT_VERSION );
		    wp_enqueue_style( $this->plugin_slug . 'intlTelInput', plugins_url( '../admin/assets/css/intlTelInput.min.css', __FILE__ ), array(), CHT_VERSION );
		    /* WP change this */

		    if ($page == 'chaty_page_chaty-contact-form-feed' ) {
			    wp_enqueue_style( 'jquery-ui-css', plugins_url( '../admin/assets/css/datepicker.min.css', __FILE__ ), array(), CHT_VERSION );
		    }

		    wp_enqueue_style( $this->plugin_slug, plugins_url( '../admin/assets/css/cht-style.min.css', __FILE__ ), array(), CHT_VERSION );
	    }
    }

    public function enqueue_pricing_styles() {
        wp_enqueue_style($this->plugin_slug."-select2", plugins_url('../admin/assets/css/select2.min.css', __FILE__), array(), CHT_VERSION);
        wp_enqueue_style($this->plugin_slug."-pricing", plugins_url('../admin/assets/css/admin-setting.css', __FILE__), array(), CHT_VERSION);
    }

    /* admin js files */
    public function enqueue_scripts($page) {
        if($page == 'chaty_page_widget-analytics' || $page == "chaty_page_chaty-contact-form-feed") {
            wp_enqueue_script('jquery-ui-datepicker');
            return;
        }

	    $is_shown = get_option("chaty_update_message");
	    if($is_shown === false) {
	        return;
	    }

	    if($page != "toplevel_page_chaty-app") {
		    return;
	    }

        wp_enqueue_script($this->plugin_slug . 'fileinput', plugins_url('../admin/assets/js/fileinput.min.js', __FILE__), array('jquery'), CHT_VERSION);
        wp_enqueue_script($this->plugin_slug . 'spectrum', plugins_url('../admin/assets/js/spectrum.min.js', __FILE__), array('jquery'), CHT_VERSION);
        wp_enqueue_script($this->plugin_slug . 'pop', plugins_url('../admin/assets/js/popper.min.js', __FILE__), array('jquery'), CHT_VERSION);
        wp_enqueue_script($this->plugin_slug . 'intlTelInput', plugins_url('../admin/assets/js/intlTelInput.min.js', __FILE__), array('jquery'), CHT_VERSION);

        /* WP change this */
        wp_enqueue_editor();
        wp_enqueue_script($this->plugin_slug. 'chaty', plugins_url('../admin/assets/js/cht-scripts.min.js', __FILE__), array('jquery', 'wp-color-picker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable'), CHT_VERSION);
        wp_localize_script($this->plugin_slug . 'chaty', 'cht_nonce_ajax',
            array(
                'cht_nonce' => wp_create_nonce('cht_nonce_ajax')
            )
        );$whatsapp_settings = array();
        foreach($this->socials as $social) {
            $whatsapp_settings[$social['slug']] = "";
        }
        wp_localize_script($this->plugin_slug . 'chaty', 'cht_settings',
            array(
                'plugin_url' => CHT_PLUGIN_URL,
                'channel_settings' => $whatsapp_settings
            )
        );

    }

    public function cht_admin_setting_page()
    {
        if (current_user_can('manage_options')) {
            $this->page = add_menu_page(
                esc_attr__('Chaty', CHT_OPT),
                esc_attr__('Chaty', CHT_OPT),
                'manage_options',
                $this->plugin_slug,
                array($this, 'display_cht_admin_page'),
                plugins_url('chaty/admin/assets/images/chaty.svg')
            );

            add_submenu_page(
                $this->plugin_slug,
                esc_attr__('Dashboard', CHT_OPT),
                esc_attr__('Dashboard', CHT_OPT),
                'manage_options',
                $this->plugin_slug,
                array($this, 'display_cht_admin_page')
            );

            $widget_page = add_submenu_page(
                $this->plugin_slug,
                esc_attr__('Settings Admin', CHT_OPT),
                esc_attr__('+ Create New Widget', CHT_OPT),
                'manage_options',
                "chaty-upgrade",
                array($this, "chaty_widget_page")
            );

            /* creating admin sub menu for chaty */
            $upgrade_page = add_submenu_page(
                $this->plugin_slug,
                esc_attr__('Widget Analytics', CHT_OPT),
                esc_attr__('Widget Analytics', CHT_OPT),
                'manage_options',
                'widget-analytics',
                array($this, 'display_cht_admin_widget_analytics')
            );
            add_action('admin_print_styles-' . $upgrade_page, array($this, 'enqueue_styles'));

            /* creating admin sub menu for chaty */
            $feed_page = add_submenu_page(
                $this->plugin_slug,
                esc_attr__('Contact form leads', CHT_OPT),
                esc_attr__('Contact form leads', CHT_OPT),
                'manage_options',
                "chaty-contact-form-feed",
                array($this, 'chaty_contact_form_feed')
            );
	        add_action('admin_print_styles-' . $feed_page, array($this, 'enqueue_styles')); /* creating admin sub menu for chaty */

            $getData = filter_input_array(INPUT_GET);
            if(isset($getData['hide_chaty_recommended_plugin']) && isset($getData['nonce'])) {
                if(current_user_can('manage_options')) {
                    $nonce = $getData['nonce'];
                    if(wp_verify_nonce($nonce, "chaty_recommended_plugin")) {
                        update_option('hide_chaty_recommended_plugin',true);
                    }
                }
            }

            $recommended_plugin = get_option("hide_chaty_recommended_plugin");
            if($recommended_plugin === false) {
                add_submenu_page(
                    $this->plugin_slug,
                    esc_html__('Recommended Plugins', CHT_OPT),
                    esc_html__('Recommended Plugins', CHT_OPT),
                    'manage_options',
                    'recommended-chaty-plugins',
                    array($this, 'recommended_plugins')
                );
            }

            $upgrade_page = add_submenu_page(
                $this->plugin_slug,
                esc_attr__('Upgrade to Pro', CHT_OPT),
                esc_attr__('Upgrade to Pro', CHT_OPT),
                'manage_options',
                $this->upgrade_slug,
                array($this, 'display_cht_admin_upgrade_page')
            );
            add_action('admin_print_styles-' . $upgrade_page, array($this, 'enqueue_pricing_styles'));
        }
        // Load public-facing style sheet and JavaScript.
        add_action('admin_print_styles-' . $this->page, array($this, 'enqueue_styles'));
    }


    public function chaty_contact_form_feed() {
        include_once CHT_DIR . '/views/admin/contact-form-feed.php';
    }

    public function recommended_plugins() {
        include_once CHT_DIR . '/views/admin/recommended-plugins.php';
    }

    public function display_cht_admin_widget_analytics() {
        include_once CHT_DIR . '/views/admin/pro_analytics.php';
    }

    /* returns upgrade menu item url */
    public function getUpgradeMenuItemUrl()
    {
        return CHT_PRO_URL;
    }

    public function chaty_widget_page()
    {
        include_once CHT_DIR . '/views/admin/chaty_widget.php';
    }

    /* chaty admin page for settings */
    public function display_cht_admin_page()
    {
        $is_shown = get_option("chaty_update_message");
        if($is_shown === false) {
            include_once CHT_DIR . '/views/admin/update.php';
        } else {
            $status = get_option("cht_active");
            delete_option("cht_is_default_deleted");
            if($status === false || isset($_GET['widget'])) {
	            $channel_class = "";
	            $fonts         = self::get_font_list();
	            include_once CHT_DIR . '/views/admin/admin.php';
            } else {
	            include_once CHT_DIR . '/views/admin/dashboard.php';
            }
	        $popup_status = get_option( "chaty_intro_popup" );
	        if ( $popup_status == "show" ) {
		        include_once CHT_DIR . '/views/admin/chaty-popup.php';
	        }
        }

	    if(isset($_REQUEST['show_message']) && $_REQUEST['show_message'] == 1) { ?>
            <div class="toast-message">
                <div class="toast-close-btn"><a href="javascript:;"></a></div>
                <div class="toast-message-title">Settings Updated</div>
                <div class="toast-message-body">Your settings has been saved</div>
            </div>
	    <?php }
    }

    public function display_cht_admin_upgrade_page()
    {
        wp_enqueue_script($this->plugin_slug . 'select2-js', plugins_url('../admin/assets/js/select2.min.js', __FILE__), array('jquery'), CHT_VERSION);
        include_once CHT_DIR . '/views/admin/upgrade.php';
    }

    /* returns EDD token */
    protected function get_token()
    {
        return get_option('cht_license_key');
    }

    // this site domain
    public function get_site()
    {
        $permalink = get_home_url();
        return $permalink;
    }

    public function is_pro($token = '')
    {
        return false;
    }

    public function data_has_expired()
    {
        return strtotime(date('Y-m-d')) > strtotime(date('Y-m-d', strtotime($this->active_license())));
    }

    /* checking for license is expired or not */
    public function active_license()
    {
        if (!$this->is_pro()) return;
        $body = null;
        if (empty(self::$response->errors)) {
            $body = json_decode(self::$response['body']);
        }
        if (isset($body->expired_date)) return $body->expired_date->date;
    }

    /* checking for license key */
    public function data_check()
    {
        if ($this->is_pro()) {
            return false;
        };
    }

    /* get current color for widget */
    public function get_current_color()
    {
        $def_color = get_option('cht_color');
        $custom_color = get_option('cht_custom_color');
        if (!$def_color) {
            $color = $custom_color;
        } else {
            $color = $def_color;
        }

        $color = strtoupper($color);
        return $color;
    }

    /* checking for widget position */
    public function get_position_style()
    {
        $position = get_option('cht_position');

        if ($position === 'custom') {
            $pos_side = get_option('positionSide');
            $bot = (get_option('cht_bottom_spacing')) ? get_option('cht_bottom_spacing') : '25';
            $side = (get_option('cht_side_spacing')) ? get_option('cht_side_spacing') : '25';
            if ($pos_side === 'right') {
                $pos_style = 'left: auto; bottom: ' . $bot . 'px; right: ' . $side . 'px';
            } else {
                $pos_style = 'left: ' . $side . 'px; bottom: ' . $bot . 'px; right: auto';
            }
        } elseif ($position === 'right') {
            $pos_style = 'left: auto; bottom: 25px; right: 25px';
        } else {
            $pos_style = 'left: 25px; bottom: 25px; right: auto';
        }

        return $pos_style;
    }

    public function cht_register_inputs()
    {
        if(isset($_GET['page']) && $_GET['page'] == "chaty-upgrade") {
	        $cht_active = get_option('cht_active');
	        if($cht_active === false) {
	            wp_redirect(admin_url("admin.php?page=chaty-app"));
	            exit;
	        }
        }
        if(current_user_can("manage_options")) {

            global $wpdb;
            $table_name = $wpdb->prefix . 'chaty_contact_form_leads';
            $postData = filter_input_array(INPUT_POST);
            if(isset($postData['remove_chaty_leads'])) {
                if(wp_verify_nonce($postData['remove_chaty_leads'], "remove_chaty_leads")) {
                    if(isset($postData['chaty_leads']) && !empty($postData['chaty_leads'])) {
                        if(isset($postData['action']) && $postData['action'] == "delete_message") {
                            if(is_array($postData['chaty_leads'])) {
                                $chaty_leads = $postData['chaty_leads'];
                                $chaty_leads = implode(",", $chaty_leads);
                            } else {
                                $chaty_leads = $postData['chaty_leads'];
                            }
                            if($chaty_leads) {
                                $delete = $wpdb->query("DELETE FROM {$table_name} WHERE id IN(".$chaty_leads.")");
	                            $paged = isset($postData['paged'])&&!empty($postData['paged'])&&is_numeric($postData['paged'])&&$postData['paged']>0?$postData['paged']:1;
	                            $search = isset($postData['search'])&&!empty($postData['search'])?$postData['search']:"";
	                            $url = admin_url("admin.php?page=chaty-contact-form-feed");
	                            if(intval($paged) > 1) {
		                            $url .= "&paged=".$paged;
	                            }
	                            if(!empty($search)) {
		                            $url .= "&search=".$search;
	                            }
	                            wp_redirect($url);
	                            exit;
                            }
                        }
                    }
                }
            }
            $postData = filter_input_array(INPUT_GET);
            if(isset($postData['remove_chaty_leads'])) {
                if(wp_verify_nonce($postData['remove_chaty_leads'], "remove_chaty_leads")) {
                    if(isset($postData['chaty_leads']) && !empty($postData['chaty_leads'])) {
                        if(isset($postData['action']) && $postData['action'] == "delete_message") {
                            if(is_array($postData['chaty_leads'])) {
                                $chaty_leads = $postData['chaty_leads'];
                                $chaty_leads = implode(",", $chaty_leads);
                            } else {
                                $chaty_leads = $postData['chaty_leads'];
                            }
                            if(!empty($chaty_leads)) {
                                if($chaty_leads == "remove-all") {
                                    $delete = $wpdb->query("TRUNCATE TABLE {$table_name}");
                                } else {
                                    $delete = $wpdb->query("DELETE FROM {$table_name} WHERE id IN(" . $chaty_leads . ")");
                                }
	                            $paged = isset($postData['paged'])&&!empty($postData['paged'])&&is_numeric($postData['paged'])&&$postData['paged']>0?$postData['paged']:1;
	                            $search = isset($postData['search'])&&!empty($postData['search'])?$postData['search']:"";
	                            $url = admin_url("admin.php?page=chaty-contact-form-feed");
	                            if(intval($paged) > 1) {
		                            $url .= "&paged=".$paged;
	                            }
	                            if(!empty($search)) {
		                            $url .= "&search=".$search;
	                            }
	                            wp_redirect($url);
	                            exit;
                            }
                        }
                    }
                }
            }

            $postData = filter_input_array(INPUT_GET);
            if(isset($postData['download_chaty_file']) && $postData['download_chaty_file'] == "chaty_contact_leads" && isset($postData['nonce'])) {
                if(wp_verify_nonce($postData['nonce'], "download_chaty_contact_leads")) {
                    $file = "chaty_contact_leads.csv";
                    $fp = fopen($file, "w")or die("Error Couldn't open {$file} for writing!");

                    global $wpdb;
                    $contact_lists_table = $wpdb->prefix.'chaty_contact_form_leads';
                    $results = $wpdb->get_results( "SELECT * FROM ".$contact_lists_table." ORDER BY ID DESC");
                    $all_data = '';
                    foreach ($results as $res) {
                        if($res->widget_id == 0) {
                            $widget_name = "Default";
                        } else {
                            $widget_name = get_option("cht_widget_title_".$res->widget_id);
                            if(empty($widget_name)) {
                                $widget_name = "Widget #".($res->widget_id+1);
                            }
                        }
                        $fields = array(
                            $res->id,
                            $widget_name,
                            $res->name,
                            $res->email,
                            nl2br($res->message),
                            $res->created_on,
                            $res->ref_page,
                        );

                        fputcsv($fp, $fields);
                    }
                    fclose($fp);

                    $file_content = file_get_contents($file);
                    header("Content-Disposition: attachment; filename=".basename($file));
                    header("Content-Length: " . filesize($file));
                    header("Content-Type: application/octet-stream;");
                    readfile($file);
                    exit;
                }
            }

            /**
             * Adding settings fields
             */
            // Section One
            foreach ($this->socials as $social) {
                add_settings_field(
                    'cht_social_' . $social['slug'],
                    ucfirst($social['slug']),
                    '',
                    $this->plugin_slug
                );
            }

            // Section Two
            add_settings_field('cht_devices', 'Devices', '', $this->plugin_slug);
            add_settings_field('cht_color', 'Color', '', $this->plugin_slug);
            add_settings_field('cht_custom_color', 'Color', '', $this->plugin_slug);
            add_settings_field('cht_position', 'Position', '', $this->plugin_slug);
            add_settings_field('cht_widget_font', 'Position', '', $this->plugin_slug);
            add_settings_field('positionSide', 'PositionSide', '', $this->plugin_slug);
            add_settings_field('cht_bottom_spacing', 'Bottom spacing', '', $this->plugin_slug);
            add_settings_field('cht_side_spacing', 'Side spacing', '', $this->plugin_slug);
            add_settings_field('cht_cta', 'CTA', '', $this->plugin_slug);
            add_settings_field('cht_cta_switcher', 'CTA switcher', '', $this->plugin_slug);
            add_settings_field('chaty_attention_effect', 'CTA effect', '', $this->plugin_slug);
            add_settings_field('chaty_default_state', 'CTA state', '', $this->plugin_slug);
            add_settings_field('chaty_trigger_on_time', 'CTA trigger on time', '', $this->plugin_slug);
            add_settings_field('chaty_trigger_time', 'CTA time to trigger', '', $this->plugin_slug);
            add_settings_field('chaty_trigger_on_exit', 'CTA exit intent', '', $this->plugin_slug);
            add_settings_field('chaty_trigger_on_scroll', 'CTA trigger on scroll', '', $this->plugin_slug);
            add_settings_field('chaty_trigger_on_page_scroll', 'CTA page scroll', '', $this->plugin_slug);
            add_settings_field('cht_close_button', 'CTA close button', '', $this->plugin_slug);
            add_settings_field('cht_close_button_text', 'CTA close button text', '', $this->plugin_slug);
            add_settings_field('chaty_icons_view', 'Icon view', '', $this->plugin_slug);
            add_settings_field('cht_created_on', 'Created time', '', $this->plugin_slug);
            add_settings_field('cht_widget_title', 'Chaty title', '', $this->plugin_slug);

            // section three
            add_settings_field('cht_active', 'Active', '', $this->plugin_slug);
            add_settings_field('cht_cta_action', 'CTA Action', '', $this->plugin_slug);
            add_settings_field('cht_cta_text_color', 'CTA Text Color', '', $this->plugin_slug);
            add_settings_field('cht_cta_bg_color', 'CTA BG Color', '', $this->plugin_slug);
            add_settings_field('cht_pending_messages', 'CTA Pending message', '', $this->plugin_slug);
            add_settings_field('cht_number_of_messages', 'CTA Number of Messages', '', $this->plugin_slug);
            add_settings_field('cht_number_color', 'CTA Color', '', $this->plugin_slug);
            add_settings_field('cht_number_bg_color', 'CTA BG Color', '', $this->plugin_slug);

            // token
            add_settings_field('cht_license_key', 'Token', '', $this->plugin_slug);

            // slug
            add_settings_field('cht_numb_slug', 'Numb', '', $this->plugin_slug);

	        add_settings_field('chaty_updated_on', 'Updated time', '', $this->plugin_slug);

            /**
             * Registering settings fields
             */

            $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

            /*check for nonce*/
            if (!empty($nonce) && wp_verify_nonce($nonce, "chaty_plugin_nonce")) {

                // register field section one
                foreach ($this->socials as $social) {
                    register_setting($this->plugin_slug, 'cht_social_' . $social['slug']);
                }

                if (isset($_POST['cht_4_cta'])) {
                    $_POST['cht_cta'] = $_POST['cht_4_cta'];
                }

                // register field section two
                register_setting($this->plugin_slug, 'cht_devices', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_color', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_custom_color', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_position', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_widget_font', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'positionSide', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_bottom_spacing', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_side_spacing', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_cta', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_cta_switcher', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_widget_size', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_widget_img', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'widget_icon', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_attention_effect', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_default_state', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_trigger_on_time', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_trigger_time', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_trigger_on_exit', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_trigger_on_scroll', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_trigger_on_page_scroll', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_close_button', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_close_button_text', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'chaty_icons_view', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_created_on', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_widget_title', 'chaty_sanitize_options');

//                add_action('pre_update_option_cht_widget_img', array($this, 'uploadCustomWidget'), 10, 3);

                // register field section three
                register_setting($this->plugin_slug, 'cht_active', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_cta_action', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_cta_text_color', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_cta_bg_color', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_pending_messages', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_number_of_messages', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_number_color', 'chaty_sanitize_options');
                register_setting($this->plugin_slug, 'cht_number_bg_color', 'chaty_sanitize_options');

                // register field section token
                register_setting($this->plugin_slug, 'cht_license_key', 'chaty_sanitize_options');

                // register field section slug
                register_setting($this->plugin_slug, 'cht_numb_slug', 'chaty_sanitize_options');

	            register_setting($this->plugin_slug, 'chaty_updated_on', 'chaty_sanitize_options');

                cht_clear_all_caches();
            }
        }
    }

    /* checking for devices desktop/mobile */
    public function device()
    {
        return 'desktop_active mobile_active';
    }

    /* return custom widget URL if uploaded */
    public function getCustomWidgetImg()
    {
        $value = get_option('cht_widget_img');
        return isset($value['url']) ? $value['url'] : '';
    }

    /* uploads custom widget image */
    public function uploadCustomWidget($value, $old_value, $option)
    {
        $option = !empty($option) ? $option : 'cht_widget_img';
        $allowed_ext = ['jpeg', 'png', 'jpg', 'svg'];
        if (!function_exists('wp_handle_upload'))
            require_once(ABSPATH . 'wp-admin/includes/file.php');

        if(isset($_FILES[$option])) {
	        $file = &$_FILES[ $option ];
	        $type = wp_check_filetype( $file['name'] );

	        if ( ! in_array( $type['ext'], $allowed_ext ) ) {
		        return $old_value;
	        }

	        if ( isset( $old_value['file'] ) ) {
		        wp_delete_file( $old_value['file'] );
	        }

	        $overrides = array( 'test_form' => false );
	        $movefile  = wp_handle_upload( $file, $overrides );
	        if ( $movefile && empty( $movefile['error'] ) ) {
		        return array(
			        'file' => $movefile['file'],
			        'url'  => $movefile['url']
		        );
	        }
        }
        return array();
    }

    /* returns CTA text */
    public function getCallToAction()
    {
        if (get_option('cht_cta')) {
            $res = nl2br(get_option('cht_cta'));
            $res = str_replace(array("\n", "\r"), "", $res);
            return $res;
        }
        return '';
    }

    public static function get_font_list(){
        return array(
            // System fonts.
	        "System Stack" => 'Default',
            'Arial' => 'Default',
            'Tahoma' => 'Default',
            'Verdana' => 'Default',
            'Helvetica' => 'Default',
            'Times New Roman' => 'Default',
            'Trebuchet MS' => 'Default',
            'Georgia' => 'Default',

            // Google Fonts (last update: 23/10/2018).
            'ABeeZee' => 'Google Fonts',
            'Abel' => 'Google Fonts',
            'Abhaya Libre' => 'Google Fonts',
            'Abril Fatface' => 'Google Fonts',
            'Aclonica' => 'Google Fonts',
            'Acme' => 'Google Fonts',
            'Actor' => 'Google Fonts',
            'Adamina' => 'Google Fonts',
            'Advent Pro' => 'Google Fonts',
            'Aguafina Script' => 'Google Fonts',
            'Akronim' => 'Google Fonts',
            'Aladin' => 'Google Fonts',
            'Aldrich' => 'Google Fonts',
            'Alef' => 'Google Fonts',
            'Alef Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Alegreya' => 'Google Fonts',
            'Alegreya SC' => 'Google Fonts',
            'Alegreya Sans' => 'Google Fonts',
            'Alegreya Sans SC' => 'Google Fonts',
            'Alex Brush' => 'Google Fonts',
            'Alfa Slab One' => 'Google Fonts',
            'Alice' => 'Google Fonts',
            'Alike' => 'Google Fonts',
            'Alike Angular' => 'Google Fonts',
            'Allan' => 'Google Fonts',
            'Allerta' => 'Google Fonts',
            'Allerta Stencil' => 'Google Fonts',
            'Allura' => 'Google Fonts',
            'Almendra' => 'Google Fonts',
            'Almendra Display' => 'Google Fonts',
            'Almendra SC' => 'Google Fonts',
            'Amarante' => 'Google Fonts',
            'Amaranth' => 'Google Fonts',
            'Amatic SC' => 'Google Fonts',
            'Amethysta' => 'Google Fonts',
            'Amiko' => 'Google Fonts',
            'Amiri' => 'Google Fonts',
            'Amita' => 'Google Fonts',
            'Anaheim' => 'Google Fonts',
            'Andada' => 'Google Fonts',
            'Andika' => 'Google Fonts',
            'Angkor' => 'Google Fonts',
            'Annie Use Your Telescope' => 'Google Fonts',
            'Anonymous Pro' => 'Google Fonts',
            'Antic' => 'Google Fonts',
            'Antic Didone' => 'Google Fonts',
            'Antic Slab' => 'Google Fonts',
            'Anton' => 'Google Fonts',
            'Arapey' => 'Google Fonts',
            'Arbutus' => 'Google Fonts',
            'Arbutus Slab' => 'Google Fonts',
            'Architects Daughter' => 'Google Fonts',
            'Archivo' => 'Google Fonts',
            'Archivo Black' => 'Google Fonts',
            'Archivo Narrow' => 'Google Fonts',
            'Aref Ruqaa' => 'Google Fonts',
            'Arima Madurai' => 'Google Fonts',
            'Arimo' => 'Google Fonts',
            'Arizonia' => 'Google Fonts',
            'Armata' => 'Google Fonts',
            'Arsenal' => 'Google Fonts',
            'Artifika' => 'Google Fonts',
            'Arvo' => 'Google Fonts',
            'Arya' => 'Google Fonts',
            'Asap' => 'Google Fonts',
            'Asap Condensed' => 'Google Fonts',
            'Asar' => 'Google Fonts',
            'Asset' => 'Google Fonts',
            'Assistant' => 'Google Fonts',
            'Astloch' => 'Google Fonts',
            'Asul' => 'Google Fonts',
            'Athiti' => 'Google Fonts',
            'Atma' => 'Google Fonts',
            'Atomic Age' => 'Google Fonts',
            'Aubrey' => 'Google Fonts',
            'Audiowide' => 'Google Fonts',
            'Autour One' => 'Google Fonts',
            'Average' => 'Google Fonts',
            'Average Sans' => 'Google Fonts',
            'Averia Gruesa Libre' => 'Google Fonts',
            'Averia Libre' => 'Google Fonts',
            'Averia Sans Libre' => 'Google Fonts',
            'Averia Serif Libre' => 'Google Fonts',
            'Bad Script' => 'Google Fonts',
            'Bahiana' => 'Google Fonts',
            'Bai Jamjuree' => 'Google Fonts',
            'Baloo' => 'Google Fonts',
            'Baloo Bhai' => 'Google Fonts',
            'Baloo Bhaijaan' => 'Google Fonts',
            'Baloo Bhaina' => 'Google Fonts',
            'Baloo Chettan' => 'Google Fonts',
            'Baloo Da' => 'Google Fonts',
            'Baloo Paaji' => 'Google Fonts',
            'Baloo Tamma' => 'Google Fonts',
            'Baloo Tammudu' => 'Google Fonts',
            'Baloo Thambi' => 'Google Fonts',
            'Balthazar' => 'Google Fonts',
            'Bangers' => 'Google Fonts',
            'Barlow' => 'Google Fonts',
            'Barlow Condensed' => 'Google Fonts',
            'Barlow Semi Condensed' => 'Google Fonts',
            'Barrio' => 'Google Fonts',
            'Basic' => 'Google Fonts',
            'Battambang' => 'Google Fonts',
            'Baumans' => 'Google Fonts',
            'Bayon' => 'Google Fonts',
            'Belgrano' => 'Google Fonts',
            'Bellefair' => 'Google Fonts',
            'Belleza' => 'Google Fonts',
            'BenchNine' => 'Google Fonts',
            'Bentham' => 'Google Fonts',
            'Berkshire Swash' => 'Google Fonts',
            'Bevan' => 'Google Fonts',
            'Bigelow Rules' => 'Google Fonts',
            'Bigshot One' => 'Google Fonts',
            'Bilbo' => 'Google Fonts',
            'Bilbo Swash Caps' => 'Google Fonts',
            'BioRhyme' => 'Google Fonts',
            'BioRhyme Expanded' => 'Google Fonts',
            'Biryani' => 'Google Fonts',
            'Bitter' => 'Google Fonts',
            'Black And White Picture' => 'Google Fonts',
            'Black Han Sans' => 'Google Fonts',
            'Black Ops One' => 'Google Fonts',
            'Bokor' => 'Google Fonts',
            'Bonbon' => 'Google Fonts',
            'Boogaloo' => 'Google Fonts',
            'Bowlby One' => 'Google Fonts',
            'Bowlby One SC' => 'Google Fonts',
            'Brawler' => 'Google Fonts',
            'Bree Serif' => 'Google Fonts',
            'Bubblegum Sans' => 'Google Fonts',
            'Bubbler One' => 'Google Fonts',
            'Buda' => 'Google Fonts',
            'Buenard' => 'Google Fonts',
            'Bungee' => 'Google Fonts',
            'Bungee Hairline' => 'Google Fonts',
            'Bungee Inline' => 'Google Fonts',
            'Bungee Outline' => 'Google Fonts',
            'Bungee Shade' => 'Google Fonts',
            'Butcherman' => 'Google Fonts',
            'Butterfly Kids' => 'Google Fonts',
            'Cabin' => 'Google Fonts',
            'Cabin Condensed' => 'Google Fonts',
            'Cabin Sketch' => 'Google Fonts',
            'Caesar Dressing' => 'Google Fonts',
            'Cagliostro' => 'Google Fonts',
            'Cairo' => 'Google Fonts',
            'Calligraffitti' => 'Google Fonts',
            'Cambay' => 'Google Fonts',
            'Cambo' => 'Google Fonts',
            'Candal' => 'Google Fonts',
            'Cantarell' => 'Google Fonts',
            'Cantata One' => 'Google Fonts',
            'Cantora One' => 'Google Fonts',
            'Capriola' => 'Google Fonts',
            'Cardo' => 'Google Fonts',
            'Carme' => 'Google Fonts',
            'Carrois Gothic' => 'Google Fonts',
            'Carrois Gothic SC' => 'Google Fonts',
            'Carter One' => 'Google Fonts',
            'Catamaran' => 'Google Fonts',
            'Caudex' => 'Google Fonts',
            'Caveat' => 'Google Fonts',
            'Caveat Brush' => 'Google Fonts',
            'Cedarville Cursive' => 'Google Fonts',
            'Ceviche One' => 'Google Fonts',
            'Chakra Petch' => 'Google Fonts',
            'Changa' => 'Google Fonts',
            'Changa One' => 'Google Fonts',
            'Chango' => 'Google Fonts',
            'Charmonman' => 'Google Fonts',
            'Chathura' => 'Google Fonts',
            'Chau Philomene One' => 'Google Fonts',
            'Chela One' => 'Google Fonts',
            'Chelsea Market' => 'Google Fonts',
            'Chenla' => 'Google Fonts',
            'Cherry Cream Soda' => 'Google Fonts',
            'Cherry Swash' => 'Google Fonts',
            'Chewy' => 'Google Fonts',
            'Chicle' => 'Google Fonts',
            'Chivo' => 'Google Fonts',
            'Chonburi' => 'Google Fonts',
            'Cinzel' => 'Google Fonts',
            'Cinzel Decorative' => 'Google Fonts',
            'Clicker Script' => 'Google Fonts',
            'Coda' => 'Google Fonts',
            'Coda Caption' => 'Google Fonts',
            'Codystar' => 'Google Fonts',
            'Coiny' => 'Google Fonts',
            'Combo' => 'Google Fonts',
            'Comfortaa' => 'Google Fonts',
            'Coming Soon' => 'Google Fonts',
            'Concert One' => 'Google Fonts',
            'Condiment' => 'Google Fonts',
            'Content' => 'Google Fonts',
            'Contrail One' => 'Google Fonts',
            'Convergence' => 'Google Fonts',
            'Cookie' => 'Google Fonts',
            'Copse' => 'Google Fonts',
            'Corben' => 'Google Fonts',
            'Cormorant' => 'Google Fonts',
            'Cormorant Garamond' => 'Google Fonts',
            'Cormorant Infant' => 'Google Fonts',
            'Cormorant SC' => 'Google Fonts',
            'Cormorant Unicase' => 'Google Fonts',
            'Cormorant Upright' => 'Google Fonts',
            'Courgette' => 'Google Fonts',
            'Cousine' => 'Google Fonts',
            'Coustard' => 'Google Fonts',
            'Covered By Your Grace' => 'Google Fonts',
            'Crafty Girls' => 'Google Fonts',
            'Creepster' => 'Google Fonts',
            'Crete Round' => 'Google Fonts',
            'Crimson Text' => 'Google Fonts',
            'Croissant One' => 'Google Fonts',
            'Crushed' => 'Google Fonts',
            'Cuprum' => 'Google Fonts',
            'Cute Font' => 'Google Fonts',
            'Cutive' => 'Google Fonts',
            'Cutive Mono' => 'Google Fonts',
            'Damion' => 'Google Fonts',
            'Dancing Script' => 'Google Fonts',
            'Dangrek' => 'Google Fonts',
            'David Libre' => 'Google Fonts',
            'Dawning of a New Day' => 'Google Fonts',
            'Days One' => 'Google Fonts',
            'Dekko' => 'Google Fonts',
            'Delius' => 'Google Fonts',
            'Delius Swash Caps' => 'Google Fonts',
            'Delius Unicase' => 'Google Fonts',
            'Della Respira' => 'Google Fonts',
            'Denk One' => 'Google Fonts',
            'Devonshire' => 'Google Fonts',
            'Dhurjati' => 'Google Fonts',
            'Didact Gothic' => 'Google Fonts',
            'Diplomata' => 'Google Fonts',
            'Diplomata SC' => 'Google Fonts',
            'Do Hyeon' => 'Google Fonts',
            'Dokdo' => 'Google Fonts',
            'Domine' => 'Google Fonts',
            'Donegal One' => 'Google Fonts',
            'Doppio One' => 'Google Fonts',
            'Dorsa' => 'Google Fonts',
            'Dosis' => 'Google Fonts',
            'Dr Sugiyama' => 'Google Fonts',
            'Droid Arabic Kufi' => 'Google Fonts', // Hack for Google Early Access.
            'Droid Arabic Naskh' => 'Google Fonts', // Hack for Google Early Access.
            'Duru Sans' => 'Google Fonts',
            'Dynalight' => 'Google Fonts',
            'EB Garamond' => 'Google Fonts',
            'Eagle Lake' => 'Google Fonts',
            'East Sea Dokdo' => 'Google Fonts',
            'Eater' => 'Google Fonts',
            'Economica' => 'Google Fonts',
            'Eczar' => 'Google Fonts',
            'El Messiri' => 'Google Fonts',
            'Electrolize' => 'Google Fonts',
            'Elsie' => 'Google Fonts',
            'Elsie Swash Caps' => 'Google Fonts',
            'Emblema One' => 'Google Fonts',
            'Emilys Candy' => 'Google Fonts',
            'Encode Sans' => 'Google Fonts',
            'Encode Sans Condensed' => 'Google Fonts',
            'Encode Sans Expanded' => 'Google Fonts',
            'Encode Sans Semi Condensed' => 'Google Fonts',
            'Encode Sans Semi Expanded' => 'Google Fonts',
            'Engagement' => 'Google Fonts',
            'Englebert' => 'Google Fonts',
            'Enriqueta' => 'Google Fonts',
            'Erica One' => 'Google Fonts',
            'Esteban' => 'Google Fonts',
            'Euphoria Script' => 'Google Fonts',
            'Ewert' => 'Google Fonts',
            'Exo' => 'Google Fonts',
            'Exo 2' => 'Google Fonts',
            'Expletus Sans' => 'Google Fonts',
            'Fahkwang' => 'Google Fonts',
            'Fanwood Text' => 'Google Fonts',
            'Farsan' => 'Google Fonts',
            'Fascinate' => 'Google Fonts',
            'Fascinate Inline' => 'Google Fonts',
            'Faster One' => 'Google Fonts',
            'Fasthand' => 'Google Fonts',
            'Fauna One' => 'Google Fonts',
            'Faustina' => 'Google Fonts',
            'Federant' => 'Google Fonts',
            'Federo' => 'Google Fonts',
            'Felipa' => 'Google Fonts',
            'Fenix' => 'Google Fonts',
            'Finger Paint' => 'Google Fonts',
            'Fira Mono' => 'Google Fonts',
            'Fira Sans' => 'Google Fonts',
            'Fira Sans Condensed' => 'Google Fonts',
            'Fira Sans Extra Condensed' => 'Google Fonts',
            'Fjalla One' => 'Google Fonts',
            'Fjord One' => 'Google Fonts',
            'Flamenco' => 'Google Fonts',
            'Flavors' => 'Google Fonts',
            'Fondamento' => 'Google Fonts',
            'Fontdiner Swanky' => 'Google Fonts',
            'Forum' => 'Google Fonts',
            'Francois One' => 'Google Fonts',
            'Frank Ruhl Libre' => 'Google Fonts',
            'Freckle Face' => 'Google Fonts',
            'Fredericka the Great' => 'Google Fonts',
            'Fredoka One' => 'Google Fonts',
            'Freehand' => 'Google Fonts',
            'Fresca' => 'Google Fonts',
            'Frijole' => 'Google Fonts',
            'Fruktur' => 'Google Fonts',
            'Fugaz One' => 'Google Fonts',
            'GFS Didot' => 'Google Fonts',
            'GFS Neohellenic' => 'Google Fonts',
            'Gabriela' => 'Google Fonts',
            'Gaegu' => 'Google Fonts',
            'Gafata' => 'Google Fonts',
            'Galada' => 'Google Fonts',
            'Galdeano' => 'Google Fonts',
            'Galindo' => 'Google Fonts',
            'Gamja Flower' => 'Google Fonts',
            'Gentium Basic' => 'Google Fonts',
            'Gentium Book Basic' => 'Google Fonts',
            'Geo' => 'Google Fonts',
            'Geostar' => 'Google Fonts',
            'Geostar Fill' => 'Google Fonts',
            'Germania One' => 'Google Fonts',
            'Gidugu' => 'Google Fonts',
            'Gilda Display' => 'Google Fonts',
            'Give You Glory' => 'Google Fonts',
            'Glass Antiqua' => 'Google Fonts',
            'Glegoo' => 'Google Fonts',
            'Gloria Hallelujah' => 'Google Fonts',
            'Goblin One' => 'Google Fonts',
            'Gochi Hand' => 'Google Fonts',
            'Gorditas' => 'Google Fonts',
            'Gothic A1' => 'Google Fonts',
            'Goudy Bookletter 1911' => 'Google Fonts',
            'Graduate' => 'Google Fonts',
            'Grand Hotel' => 'Google Fonts',
            'Gravitas One' => 'Google Fonts',
            'Great Vibes' => 'Google Fonts',
            'Griffy' => 'Google Fonts',
            'Gruppo' => 'Google Fonts',
            'Gudea' => 'Google Fonts',
            'Gugi' => 'Google Fonts',
            'Gurajada' => 'Google Fonts',
            'Habibi' => 'Google Fonts',
            'Halant' => 'Google Fonts',
            'Hammersmith One' => 'Google Fonts',
            'Hanalei' => 'Google Fonts',
            'Hanalei Fill' => 'Google Fonts',
            'Handlee' => 'Google Fonts',
            'Hanuman' => 'Google Fonts',
            'Happy Monkey' => 'Google Fonts',
            'Harmattan' => 'Google Fonts',
            'Headland One' => 'Google Fonts',
            'Heebo' => 'Google Fonts',
            'Henny Penny' => 'Google Fonts',
            'Herr Von Muellerhoff' => 'Google Fonts',
            'Hi Melody' => 'Google Fonts',
            'Hind' => 'Google Fonts',
            'Hind Guntur' => 'Google Fonts',
            'Hind Madurai' => 'Google Fonts',
            'Hind Siliguri' => 'Google Fonts',
            'Hind Vadodara' => 'Google Fonts',
            'Holtwood One SC' => 'Google Fonts',
            'Homemade Apple' => 'Google Fonts',
            'Homenaje' => 'Google Fonts',
            'IBM Plex Mono' => 'Google Fonts',
            'IBM Plex Sans' => 'Google Fonts',
            'IBM Plex Sans Condensed' => 'Google Fonts',
            'IBM Plex Serif' => 'Google Fonts',
            'IM Fell DW Pica' => 'Google Fonts',
            'IM Fell DW Pica SC' => 'Google Fonts',
            'IM Fell Double Pica' => 'Google Fonts',
            'IM Fell Double Pica SC' => 'Google Fonts',
            'IM Fell English' => 'Google Fonts',
            'IM Fell English SC' => 'Google Fonts',
            'IM Fell French Canon' => 'Google Fonts',
            'IM Fell French Canon SC' => 'Google Fonts',
            'IM Fell Great Primer' => 'Google Fonts',
            'IM Fell Great Primer SC' => 'Google Fonts',
            'Iceberg' => 'Google Fonts',
            'Iceland' => 'Google Fonts',
            'Imprima' => 'Google Fonts',
            'Inconsolata' => 'Google Fonts',
            'Inder' => 'Google Fonts',
            'Indie Flower' => 'Google Fonts',
            'Inika' => 'Google Fonts',
            'Inknut Antiqua' => 'Google Fonts',
            'Irish Grover' => 'Google Fonts',
            'Istok Web' => 'Google Fonts',
            'Italiana' => 'Google Fonts',
            'Italianno' => 'Google Fonts',
            'Itim' => 'Google Fonts',
            'Jacques Francois' => 'Google Fonts',
            'Jacques Francois Shadow' => 'Google Fonts',
            'Jaldi' => 'Google Fonts',
            'Jim Nightshade' => 'Google Fonts',
            'Jockey One' => 'Google Fonts',
            'Jolly Lodger' => 'Google Fonts',
            'Jomhuria' => 'Google Fonts',
            'Josefin Sans' => 'Google Fonts',
            'Josefin Slab' => 'Google Fonts',
            'Joti One' => 'Google Fonts',
            'Jua' => 'Google Fonts',
            'Judson' => 'Google Fonts',
            'Julee' => 'Google Fonts',
            'Julius Sans One' => 'Google Fonts',
            'Junge' => 'Google Fonts',
            'Jura' => 'Google Fonts',
            'Just Another Hand' => 'Google Fonts',
            'Just Me Again Down Here' => 'Google Fonts',
            'K2D' => 'Google Fonts',
            'Kadwa' => 'Google Fonts',
            'Kalam' => 'Google Fonts',
            'Kameron' => 'Google Fonts',
            'Kanit' => 'Google Fonts',
            'Kantumruy' => 'Google Fonts',
            'Karla' => 'Google Fonts',
            'Karma' => 'Google Fonts',
            'Katibeh' => 'Google Fonts',
            'Kaushan Script' => 'Google Fonts',
            'Kavivanar' => 'Google Fonts',
            'Kavoon' => 'Google Fonts',
            'Kdam Thmor' => 'Google Fonts',
            'Keania One' => 'Google Fonts',
            'Kelly Slab' => 'Google Fonts',
            'Kenia' => 'Google Fonts',
            'Khand' => 'Google Fonts',
            'Khmer' => 'Google Fonts',
            'Khula' => 'Google Fonts',
            'Kirang Haerang' => 'Google Fonts',
            'Kite One' => 'Google Fonts',
            'Knewave' => 'Google Fonts',
            'KoHo' => 'Google Fonts',
            'Kodchasan' => 'Google Fonts',
            'Kosugi' => 'Google Fonts',
            'Kosugi Maru' => 'Google Fonts',
            'Kotta One' => 'Google Fonts',
            'Koulen' => 'Google Fonts',
            'Kranky' => 'Google Fonts',
            'Kreon' => 'Google Fonts',
            'Kristi' => 'Google Fonts',
            'Krona One' => 'Google Fonts',
            'Krub' => 'Google Fonts',
            'Kumar One' => 'Google Fonts',
            'Kumar One Outline' => 'Google Fonts',
            'Kurale' => 'Google Fonts',
            'La Belle Aurore' => 'Google Fonts',
            'Laila' => 'Google Fonts',
            'Lakki Reddy' => 'Google Fonts',
            'Lalezar' => 'Google Fonts',
            'Lancelot' => 'Google Fonts',
            'Lateef' => 'Google Fonts',
            'Lato' => 'Google Fonts',
            'League Script' => 'Google Fonts',
            'Leckerli One' => 'Google Fonts',
            'Ledger' => 'Google Fonts',
            'Lekton' => 'Google Fonts',
            'Lemon' => 'Google Fonts',
            'Lemonada' => 'Google Fonts',
            'Libre Barcode 128' => 'Google Fonts',
            'Libre Barcode 128 Text' => 'Google Fonts',
            'Libre Barcode 39' => 'Google Fonts',
            'Libre Barcode 39 Extended' => 'Google Fonts',
            'Libre Barcode 39 Extended Text' => 'Google Fonts',
            'Libre Barcode 39 Text' => 'Google Fonts',
            'Libre Baskerville' => 'Google Fonts',
            'Libre Franklin' => 'Google Fonts',
            'Life Savers' => 'Google Fonts',
            'Lilita One' => 'Google Fonts',
            'Lily Script One' => 'Google Fonts',
            'Limelight' => 'Google Fonts',
            'Linden Hill' => 'Google Fonts',
            'Lobster' => 'Google Fonts',
            'Lobster Two' => 'Google Fonts',
            'Londrina Outline' => 'Google Fonts',
            'Londrina Shadow' => 'Google Fonts',
            'Londrina Sketch' => 'Google Fonts',
            'Londrina Solid' => 'Google Fonts',
            'Lora' => 'Google Fonts',
            'Love Ya Like A Sister' => 'Google Fonts',
            'Loved by the King' => 'Google Fonts',
            'Lovers Quarrel' => 'Google Fonts',
            'Luckiest Guy' => 'Google Fonts',
            'Lusitana' => 'Google Fonts',
            'Lustria' => 'Google Fonts',
            'M PLUS 1p' => 'Google Fonts',
            'M PLUS Rounded 1c' => 'Google Fonts',
            'Macondo' => 'Google Fonts',
            'Macondo Swash Caps' => 'Google Fonts',
            'Mada' => 'Google Fonts',
            'Magra' => 'Google Fonts',
            'Maiden Orange' => 'Google Fonts',
            'Maitree' => 'Google Fonts',
            'Mako' => 'Google Fonts',
            'Mali' => 'Google Fonts',
            'Mallanna' => 'Google Fonts',
            'Mandali' => 'Google Fonts',
            'Manuale' => 'Google Fonts',
            'Marcellus' => 'Google Fonts',
            'Marcellus SC' => 'Google Fonts',
            'Marck Script' => 'Google Fonts',
            'Margarine' => 'Google Fonts',
            'Markazi Text' => 'Google Fonts',
            'Marko One' => 'Google Fonts',
            'Marmelad' => 'Google Fonts',
            'Martel' => 'Google Fonts',
            'Martel Sans' => 'Google Fonts',
            'Marvel' => 'Google Fonts',
            'Mate' => 'Google Fonts',
            'Mate SC' => 'Google Fonts',
            'Maven Pro' => 'Google Fonts',
            'McLaren' => 'Google Fonts',
            'Meddon' => 'Google Fonts',
            'MedievalSharp' => 'Google Fonts',
            'Medula One' => 'Google Fonts',
            'Meera Inimai' => 'Google Fonts',
            'Megrim' => 'Google Fonts',
            'Meie Script' => 'Google Fonts',
            'Merienda' => 'Google Fonts',
            'Merienda One' => 'Google Fonts',
            'Merriweather' => 'Google Fonts',
            'Merriweather Sans' => 'Google Fonts',
            'Metal' => 'Google Fonts',
            'Metal Mania' => 'Google Fonts',
            'Metamorphous' => 'Google Fonts',
            'Metrophobic' => 'Google Fonts',
            'Michroma' => 'Google Fonts',
            'Milonga' => 'Google Fonts',
            'Miltonian' => 'Google Fonts',
            'Miltonian Tattoo' => 'Google Fonts',
            'Mina' => 'Google Fonts',
            'Miniver' => 'Google Fonts',
            'Miriam Libre' => 'Google Fonts',
            'Mirza' => 'Google Fonts',
            'Miss Fajardose' => 'Google Fonts',
            'Mitr' => 'Google Fonts',
            'Modak' => 'Google Fonts',
            'Modern Antiqua' => 'Google Fonts',
            'Mogra' => 'Google Fonts',
            'Molengo' => 'Google Fonts',
            'Molle' => 'Google Fonts',
            'Monda' => 'Google Fonts',
            'Monofett' => 'Google Fonts',
            'Monoton' => 'Google Fonts',
            'Monsieur La Doulaise' => 'Google Fonts',
            'Montaga' => 'Google Fonts',
            'Montez' => 'Google Fonts',
            'Montserrat' => 'Google Fonts',
            'Montserrat Alternates' => 'Google Fonts',
            'Montserrat Subrayada' => 'Google Fonts',
            'Moul' => 'Google Fonts',
            'Moulpali' => 'Google Fonts',
            'Mountains of Christmas' => 'Google Fonts',
            'Mouse Memoirs' => 'Google Fonts',
            'Mr Bedfort' => 'Google Fonts',
            'Mr Dafoe' => 'Google Fonts',
            'Mr De Haviland' => 'Google Fonts',
            'Mrs Saint Delafield' => 'Google Fonts',
            'Mrs Sheppards' => 'Google Fonts',
            'Mukta' => 'Google Fonts',
            'Mukta Mahee' => 'Google Fonts',
            'Mukta Malar' => 'Google Fonts',
            'Mukta Vaani' => 'Google Fonts',
            'Muli' => 'Google Fonts',
            'Mystery Quest' => 'Google Fonts',
            'NTR' => 'Google Fonts',
            'Nanum Brush Script' => 'Google Fonts',
            'Nanum Gothic' => 'Google Fonts',
            'Nanum Gothic Coding' => 'Google Fonts',
            'Nanum Myeongjo' => 'Google Fonts',
            'Nanum Pen Script' => 'Google Fonts',
            'Neucha' => 'Google Fonts',
            'Neuton' => 'Google Fonts',
            'New Rocker' => 'Google Fonts',
            'News Cycle' => 'Google Fonts',
            'Niconne' => 'Google Fonts',
            'Niramit' => 'Google Fonts',
            'Nixie One' => 'Google Fonts',
            'Nobile' => 'Google Fonts',
            'Nokora' => 'Google Fonts',
            'Norican' => 'Google Fonts',
            'Nosifer' => 'Google Fonts',
            'Notable' => 'Google Fonts',
            'Nothing You Could Do' => 'Google Fonts',
            'Noticia Text' => 'Google Fonts',
            'Noto Kufi Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Naskh Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans' => 'Google Fonts',
            'Noto Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans JP' => 'Google Fonts',
            'Noto Sans KR' => 'Google Fonts',
            'Noto Serif' => 'Google Fonts',
            'Noto Serif JP' => 'Google Fonts',
            'Noto Serif KR' => 'Google Fonts',
            'Nova Cut' => 'Google Fonts',
            'Nova Flat' => 'Google Fonts',
            'Nova Mono' => 'Google Fonts',
            'Nova Oval' => 'Google Fonts',
            'Nova Round' => 'Google Fonts',
            'Nova Script' => 'Google Fonts',
            'Nova Slim' => 'Google Fonts',
            'Nova Square' => 'Google Fonts',
            'Numans' => 'Google Fonts',
            'Nunito' => 'Google Fonts',
            'Nunito Sans' => 'Google Fonts',
            'Odor Mean Chey' => 'Google Fonts',
            'Offside' => 'Google Fonts',
            'Old Standard TT' => 'Google Fonts',
            'Oldenburg' => 'Google Fonts',
            'Oleo Script' => 'Google Fonts',
            'Oleo Script Swash Caps' => 'Google Fonts',
            'Open Sans' => 'Google Fonts',
            'Open Sans Condensed' => 'Google Fonts',
            'Open Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Open Sans Hebrew Condensed' => 'Google Fonts', // Hack for Google Early Access.
            'Oranienbaum' => 'Google Fonts',
            'Orbitron' => 'Google Fonts',
            'Oregano' => 'Google Fonts',
            'Orienta' => 'Google Fonts',
            'Original Surfer' => 'Google Fonts',
            'Oswald' => 'Google Fonts',
            'Over the Rainbow' => 'Google Fonts',
            'Overlock' => 'Google Fonts',
            'Overlock SC' => 'Google Fonts',
            'Overpass' => 'Google Fonts',
            'Overpass Mono' => 'Google Fonts',
            'Ovo' => 'Google Fonts',
            'Oxygen' => 'Google Fonts',
            'Oxygen Mono' => 'Google Fonts',
            'PT Mono' => 'Google Fonts',
            'PT Sans' => 'Google Fonts',
            'PT Sans Caption' => 'Google Fonts',
            'PT Sans Narrow' => 'Google Fonts',
            'PT Serif' => 'Google Fonts',
            'PT Serif Caption' => 'Google Fonts',
            'Pacifico' => 'Google Fonts',
            'Padauk' => 'Google Fonts',
            'Palanquin' => 'Google Fonts',
            'Palanquin Dark' => 'Google Fonts',
            'Pangolin' => 'Google Fonts',
            'Paprika' => 'Google Fonts',
            'Parisienne' => 'Google Fonts',
            'Passero One' => 'Google Fonts',
            'Passion One' => 'Google Fonts',
            'Pathway Gothic One' => 'Google Fonts',
            'Patrick Hand' => 'Google Fonts',
            'Patrick Hand SC' => 'Google Fonts',
            'Pattaya' => 'Google Fonts',
            'Patua One' => 'Google Fonts',
            'Pavanam' => 'Google Fonts',
            'Paytone One' => 'Google Fonts',
            'Peddana' => 'Google Fonts',
            'Peralta' => 'Google Fonts',
            'Permanent Marker' => 'Google Fonts',
            'Petit Formal Script' => 'Google Fonts',
            'Petrona' => 'Google Fonts',
            'Philosopher' => 'Google Fonts',
            'Piedra' => 'Google Fonts',
            'Pinyon Script' => 'Google Fonts',
            'Pirata One' => 'Google Fonts',
            'Plaster' => 'Google Fonts',
            'Play' => 'Google Fonts',
            'Playball' => 'Google Fonts',
            'Playfair Display' => 'Google Fonts',
            'Playfair Display SC' => 'Google Fonts',
            'Podkova' => 'Google Fonts',
            'Poiret One' => 'Google Fonts',
            'Poller One' => 'Google Fonts',
            'Poly' => 'Google Fonts',
            'Pompiere' => 'Google Fonts',
            'Pontano Sans' => 'Google Fonts',
            'Poor Story' => 'Google Fonts',
            'Poppins' => 'Google Fonts',
            'Port Lligat Sans' => 'Google Fonts',
            'Port Lligat Slab' => 'Google Fonts',
            'Pragati Narrow' => 'Google Fonts',
            'Prata' => 'Google Fonts',
            'Preahvihear' => 'Google Fonts',
            'Press Start 2P' => 'Google Fonts',
            'Pridi' => 'Google Fonts',
            'Princess Sofia' => 'Google Fonts',
            'Prociono' => 'Google Fonts',
            'Prompt' => 'Google Fonts',
            'Prosto One' => 'Google Fonts',
            'Proza Libre' => 'Google Fonts',
            'Puritan' => 'Google Fonts',
            'Purple Purse' => 'Google Fonts',
            'Quando' => 'Google Fonts',
            'Quantico' => 'Google Fonts',
            'Quattrocento' => 'Google Fonts',
            'Quattrocento Sans' => 'Google Fonts',
            'Questrial' => 'Google Fonts',
            'Quicksand' => 'Google Fonts',
            'Quintessential' => 'Google Fonts',
            'Qwigley' => 'Google Fonts',
            'Racing Sans One' => 'Google Fonts',
            'Radley' => 'Google Fonts',
            'Rajdhani' => 'Google Fonts',
            'Rakkas' => 'Google Fonts',
            'Raleway' => 'Google Fonts',
            'Raleway Dots' => 'Google Fonts',
            'Ramabhadra' => 'Google Fonts',
            'Ramaraja' => 'Google Fonts',
            'Rambla' => 'Google Fonts',
            'Rammetto One' => 'Google Fonts',
            'Ranchers' => 'Google Fonts',
            'Rancho' => 'Google Fonts',
            'Ranga' => 'Google Fonts',
            'Rasa' => 'Google Fonts',
            'Rationale' => 'Google Fonts',
            'Ravi Prakash' => 'Google Fonts',
            'Redressed' => 'Google Fonts',
            'Reem Kufi' => 'Google Fonts',
            'Reenie Beanie' => 'Google Fonts',
            'Revalia' => 'Google Fonts',
            'Rhodium Libre' => 'Google Fonts',
            'Ribeye' => 'Google Fonts',
            'Ribeye Marrow' => 'Google Fonts',
            'Righteous' => 'Google Fonts',
            'Risque' => 'Google Fonts',
            'Roboto' => 'Google Fonts',
            'Roboto Condensed' => 'Google Fonts',
            'Roboto Mono' => 'Google Fonts',
            'Roboto Slab' => 'Google Fonts',
            'Rochester' => 'Google Fonts',
            'Rock Salt' => 'Google Fonts',
            'Rokkitt' => 'Google Fonts',
            'Romanesco' => 'Google Fonts',
            'Ropa Sans' => 'Google Fonts',
            'Rosario' => 'Google Fonts',
            'Rosarivo' => 'Google Fonts',
            'Rouge Script' => 'Google Fonts',
            'Rozha One' => 'Google Fonts',
            'Rubik' => 'Google Fonts',
            'Rubik Mono One' => 'Google Fonts',
            'Ruda' => 'Google Fonts',
            'Rufina' => 'Google Fonts',
            'Ruge Boogie' => 'Google Fonts',
            'Ruluko' => 'Google Fonts',
            'Rum Raisin' => 'Google Fonts',
            'Ruslan Display' => 'Google Fonts',
            'Russo One' => 'Google Fonts',
            'Ruthie' => 'Google Fonts',
            'Rye' => 'Google Fonts',
            'Sacramento' => 'Google Fonts',
            'Sahitya' => 'Google Fonts',
            'Sail' => 'Google Fonts',
            'Saira' => 'Google Fonts',
            'Saira Condensed' => 'Google Fonts',
            'Saira Extra Condensed' => 'Google Fonts',
            'Saira Semi Condensed' => 'Google Fonts',
            'Salsa' => 'Google Fonts',
            'Sanchez' => 'Google Fonts',
            'Sancreek' => 'Google Fonts',
            'Sansita' => 'Google Fonts',
            'Sarala' => 'Google Fonts',
            'Sarina' => 'Google Fonts',
            'Sarpanch' => 'Google Fonts',
            'Satisfy' => 'Google Fonts',
            'Sawarabi Gothic' => 'Google Fonts',
            'Sawarabi Mincho' => 'Google Fonts',
            'Scada' => 'Google Fonts',
            'Scheherazade' => 'Google Fonts',
            'Schoolbell' => 'Google Fonts',
            'Scope One' => 'Google Fonts',
            'Seaweed Script' => 'Google Fonts',
            'Secular One' => 'Google Fonts',
            'Sedgwick Ave' => 'Google Fonts',
            'Sedgwick Ave Display' => 'Google Fonts',
            'Sevillana' => 'Google Fonts',
            'Seymour One' => 'Google Fonts',
            'Shadows Into Light' => 'Google Fonts',
            'Shadows Into Light Two' => 'Google Fonts',
            'Shanti' => 'Google Fonts',
            'Share' => 'Google Fonts',
            'Share Tech' => 'Google Fonts',
            'Share Tech Mono' => 'Google Fonts',
            'Shojumaru' => 'Google Fonts',
            'Short Stack' => 'Google Fonts',
            'Shrikhand' => 'Google Fonts',
            'Siemreap' => 'Google Fonts',
            'Sigmar One' => 'Google Fonts',
            'Signika' => 'Google Fonts',
            'Signika Negative' => 'Google Fonts',
            'Simonetta' => 'Google Fonts',
            'Sintony' => 'Google Fonts',
            'Sirin Stencil' => 'Google Fonts',
            'Six Caps' => 'Google Fonts',
            'Skranji' => 'Google Fonts',
            'Slabo 13px' => 'Google Fonts',
            'Slabo 27px' => 'Google Fonts',
            'Slackey' => 'Google Fonts',
            'Smokum' => 'Google Fonts',
            'Smythe' => 'Google Fonts',
            'Sniglet' => 'Google Fonts',
            'Snippet' => 'Google Fonts',
            'Snowburst One' => 'Google Fonts',
            'Sofadi One' => 'Google Fonts',
            'Sofia' => 'Google Fonts',
            'Song Myung' => 'Google Fonts',
            'Sonsie One' => 'Google Fonts',
            'Sorts Mill Goudy' => 'Google Fonts',
            'Source Code Pro' => 'Google Fonts',
            'Source Sans Pro' => 'Google Fonts',
            'Source Serif Pro' => 'Google Fonts',
            'Space Mono' => 'Google Fonts',
            'Special Elite' => 'Google Fonts',
            'Spectral' => 'Google Fonts',
            'Spectral SC' => 'Google Fonts',
            'Spicy Rice' => 'Google Fonts',
            'Spinnaker' => 'Google Fonts',
            'Spirax' => 'Google Fonts',
            'Squada One' => 'Google Fonts',
            'Sree Krushnadevaraya' => 'Google Fonts',
            'Sriracha' => 'Google Fonts',
            'Srisakdi' => 'Google Fonts',
            'Stalemate' => 'Google Fonts',
            'Stalinist One' => 'Google Fonts',
            'Stardos Stencil' => 'Google Fonts',
            'Stint Ultra Condensed' => 'Google Fonts',
            'Stint Ultra Expanded' => 'Google Fonts',
            'Stoke' => 'Google Fonts',
            'Strait' => 'Google Fonts',
            'Stylish' => 'Google Fonts',
            'Sue Ellen Francisco' => 'Google Fonts',
            'Suez One' => 'Google Fonts',
            'Sumana' => 'Google Fonts',
            'Sunflower' => 'Google Fonts',
            'Sunshiney' => 'Google Fonts',
            'Supermercado One' => 'Google Fonts',
            'Sura' => 'Google Fonts',
            'Suranna' => 'Google Fonts',
            'Suravaram' => 'Google Fonts',
            'Suwannaphum' => 'Google Fonts',
            'Swanky and Moo Moo' => 'Google Fonts',
            'Syncopate' => 'Google Fonts',
            'Tajawal' => 'Google Fonts',
            'Tangerine' => 'Google Fonts',
            'Taprom' => 'Google Fonts',
            'Tauri' => 'Google Fonts',
            'Taviraj' => 'Google Fonts',
            'Teko' => 'Google Fonts',
            'Telex' => 'Google Fonts',
            'Tenali Ramakrishna' => 'Google Fonts',
            'Tenor Sans' => 'Google Fonts',
            'Text Me One' => 'Google Fonts',
            'The Girl Next Door' => 'Google Fonts',
            'Tienne' => 'Google Fonts',
            'Tillana' => 'Google Fonts',
            'Timmana' => 'Google Fonts',
            'Tinos' => 'Google Fonts',
            'Titan One' => 'Google Fonts',
            'Titillium Web' => 'Google Fonts',
            'Trade Winds' => 'Google Fonts',
            'Trirong' => 'Google Fonts',
            'Trocchi' => 'Google Fonts',
            'Trochut' => 'Google Fonts',
            'Trykker' => 'Google Fonts',
            'Tulpen One' => 'Google Fonts',
            'Ubuntu' => 'Google Fonts',
            'Ubuntu Condensed' => 'Google Fonts',
            'Ubuntu Mono' => 'Google Fonts',
            'Ultra' => 'Google Fonts',
            'Uncial Antiqua' => 'Google Fonts',
            'Underdog' => 'Google Fonts',
            'Unica One' => 'Google Fonts',
            'UnifrakturCook' => 'Google Fonts',
            'UnifrakturMaguntia' => 'Google Fonts',
            'Unkempt' => 'Google Fonts',
            'Unlock' => 'Google Fonts',
            'Unna' => 'Google Fonts',
            'VT323' => 'Google Fonts',
            'Vampiro One' => 'Google Fonts',
            'Varela' => 'Google Fonts',
            'Varela Round' => 'Google Fonts',
            'Vast Shadow' => 'Google Fonts',
            'Vesper Libre' => 'Google Fonts',
            'Vibur' => 'Google Fonts',
            'Vidaloka' => 'Google Fonts',
            'Viga' => 'Google Fonts',
            'Voces' => 'Google Fonts',
            'Volkhov' => 'Google Fonts',
            'Vollkorn' => 'Google Fonts',
            'Vollkorn SC' => 'Google Fonts',
            'Voltaire' => 'Google Fonts',
            'Waiting for the Sunrise' => 'Google Fonts',
            'Wallpoet' => 'Google Fonts',
            'Walter Turncoat' => 'Google Fonts',
            'Warnes' => 'Google Fonts',
            'Wellfleet' => 'Google Fonts',
            'Wendy One' => 'Google Fonts',
            'Wire One' => 'Google Fonts',
            'Work Sans' => 'Google Fonts',
            'Yanone Kaffeesatz' => 'Google Fonts',
            'Yantramanav' => 'Google Fonts',
            'Yatra One' => 'Google Fonts',
            'Yellowtail' => 'Google Fonts',
            'Yeon Sung' => 'Google Fonts',
            'Yeseva One' => 'Google Fonts',
            'Yesteryear' => 'Google Fonts',
            'Yrsa' => 'Google Fonts',
            'Zeyada' => 'Google Fonts',
            'Zilla Slab' => 'Google Fonts',
            'Zilla Slab Highlight' => 'Google Fonts',
        );
    }

    public function chaty_text() {
        load_plugin_textdomain("chaty", FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    }

    public function chaty_update_status() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'chaty_update_status')) {
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            update_option("chaty_update_message", 2);
            if($status == 1) {
                $url = 'https://go.premio.io/api/update.php?email='.$email.'&plugin=chaty';
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                curl_close($handle);
            }
        }
    }

    public function update_popup_status() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'chaty_update_popup_status')) {
            update_option("chaty_intro_popup", "hide");
        }
        echo esc_attr("1");
        die;
    }

    public function update_channel_setting() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], "Contact_Us-settings")) {
            update_option("chaty_contact_us_setting", "hide");
        }
        echo esc_attr("1");
        die;
    }

    public function wcp_admin_send_message_to_owner()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['errors'] = array();
        $response['message'] = "";
        $errorArray = [];
        $errorMessage = esc_attr__("%s is required", CHT_OPT);

        $textarea_text = filter_input(INPUT_POST, 'textarea_text', FILTER_SANITIZE_STRING);
        $user_email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        if (empty($textarea_text)) {
            $error = array(
                "key" => "textarea_text",
                "message" => esc_attr__("Please enter your message", "wcp")
            );
            $errorArray[] = $error;
        }
        if (empty($user_email)) {
            $error = array(
                "key" => "user_email",
                "message" => sprintf($errorMessage, esc_attr__("Email", "wcp"))
            );
            $errorArray[] = $error;
        } else if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $error = array(
                'key' => "user_email",
                "message" => "Email is not valid"
            );
            $errorArray[] = $error;
        }
        if (empty($errorArray)) {
            if (empty($nonce)) {
                $error = array(
                    'key' => "nonce",
                    "message" => "Your request is not valid"
                );
                $errorArray[] = $error;
            } else if (!wp_verify_nonce($nonce, "chaty_send_message_to_owner")) {
                $error = array(
                    'key' => "nonce",
                    "message" => "Your request is not valid"
                );
                $errorArray[] = $error;
            }
        }
        if (empty($errorArray)) {
            global $current_user;
            $text_message = $textarea_text;
            $email = $user_email;
            $domain = site_url();
            $user_name = $current_user->first_name . " " . $current_user->last_name;

	        /* sending message to Crisp */
	        $post_message = array();

	        $message_data = array();
	        $message_data['key'] = "Plugin";
	        $message_data['value'] = "Chaty";
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Domain";
	        $message_data['value'] = $domain;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Email";
	        $message_data['value'] = $email;
	        $post_message[] = $message_data;

	        $message_data = array();
	        $message_data['key'] = "Message";
	        $message_data['value'] = $text_message;
	        $post_message[] = $message_data;

	        $api_params = array(
		        'domain' => $domain,
		        'email' => $email,
		        'url' => site_url(),
		        'name' => $user_name,
		        'message' => $post_message,
		        'plugin' => "Chaty",
		        'type' => "Need Help",
	        );

	        /* Sending message to Crisp API */
	        $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

	        if (is_wp_error($crisp_response)) {
		        wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
	        }

	        $response['status'] = 1;
        } else {
            $response['error'] = 1;
            $response['errors'] = $errorArray;
        }
        echo json_encode($response);
        wp_die();
    }
}
new CHT_Admin_Base();

add_action('update_option_chaty_updated_on', function( $old_value, $value ) {
    if($old_value != $value) {
	    wp_redirect( "admin.php?page=chaty-app&show_message=1" );
	    exit;
    }
}, 10, 2);