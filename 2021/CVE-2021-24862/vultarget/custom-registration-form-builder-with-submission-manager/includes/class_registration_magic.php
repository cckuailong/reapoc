<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://registration_magic.com
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 * @author     CMSHelplive
 */
class Registration_Magic
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @access   public
     * @var      RM_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    public $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @access   public
     * @var      string    $registraion_magic    The string used to uniquely identify this plugin.
     */
    public $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @access   public
     * @var      string    $version    The current version of the plugin.
     */
    public $version;

    /**
     * The controller of this plugin.
     *
     * @access   public
     * @var      string    $controller    The main controller of this plugin.
     */
    public $controller;

    /**
     * The xml_loader of this plugin.
     *
     * @access   public
     * @var      string    $xml_loader    The xml loader of this plugin.
     */
    public $xml_loader;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     */
    public function __construct()
    {
        $this->plugin_name = 'RegistrationMagic';
        $this->version = RM_PLUGIN_VERSION;
        $this->loader = new RM_Loader();
        $this->set_locale();
        add_action( 'init', array($this,'set_toolbar') );
        $this->define_global_hooks();
            
        $this->xml_loader = registration_magic_is_addon_enabled() ? RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config_addon.xml') : RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');
        
        $request = new RM_Request($this->xml_loader);
        $params = array('request' => $request, 'xml_loader' => $this->xml_loader);
        $this->controller = new RM_Main_Controller($params);
        $this->define_public_hooks();
        $this->define_admin_hooks();
        $this->add_ob_start($request->req['rm_slug']);
    }
    
    public function set_toolbar()
    {
        if(!is_user_logged_in())
            return;
        
        $val = get_option('rm_option_hide_toolbar', $default = 'no');
        $admin_val = get_option('rm_option_enable_toolbar_for_admin', $default = 'no');

        if($val == 'yes') {
            if($admin_val == 'yes') {
                $roles = wp_get_current_user()->roles;
                if(in_array('administrator',$roles)){
                    //add_filter('show_admin_bar','__return_true',100); 
                    return;
                }
            }
            add_filter('show_admin_bar','__return_false',100);
        }
        
        //add_filter('show_admin_bar',$val==='yes' ? '__return_false' : '__return_true',100);
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access   public
     */
    public function set_locale()
    {

        $rm_i18n = new RM_i18n();

        $this->loader->add_action('plugins_loaded', $rm_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access   public
     */
    public function define_admin_hooks()
    {
        $rm_admin = new RM_Admin($this->get_plugin_name(), $this->get_version(), $this->get_controller());

        $this->loader->add_action('admin_enqueue_scripts', $rm_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $rm_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $rm_admin, 'add_menu');
        $this->loader->add_action('wp_ajax_rm_sort_form_fields', $this->controller, 'run');
        $this->loader->add_action('wp_ajax_rm_get_stats', $this->controller, 'run');
        $this->loader->add_action('wp_dashboard_setup', $rm_admin, 'add_dashboard_widget');
        $this->loader->add_action('edit_user_profile', $rm_admin, 'user_edit_page_widget');
        $this->loader->add_action('show_user_profile', $rm_admin, 'user_edit_page_widget');
        $this->loader->add_action('wp_ajax_rm_test_smtp_config', 'RM_Utilities', 'check_smtp');
        $this->loader->add_action('wp_ajax_rm_test_wordpress_default_mail', 'RM_Utilities', 'check_wordpress_default_mail');
        $this->loader->add_action('wp_ajax_rm_fb_subscribe_action', 'RM_Utilities', 'handel_fb_subscribe');
        $this->loader->add_action('wp_ajax_rm_get_fields', new RM_Map_MailChimp_Controller(), 'get_mc_list_field');
        $this->loader->add_action('wp_ajax_rm_save_form_view_sett', new RM_Form_Settings_Controller(), 'view');
        $this->loader->add_action('wp_ajax_review_banner_handler', 'RM_Utilities', 'handle_rating_operations');
        $this->loader->add_action('wp_ajax_newsletter_sub_handler', 'RM_Utilities', 'disable_newsletter_banner');
        $this->loader->add_action('wp_ajax_set_default_form', 'RM_Utilities', 'set_default_form');
        $this->loader->add_action('wp_ajax_unset_default_form', 'RM_Utilities', 'unset_default_form');
        $this->loader->add_action('wp_ajax_rm_activate_user', 'RM_Utilities', 'link_activate_user');
        $this->loader->add_action('wp_ajax_nopriv_rm_activate_user', 'RM_Utilities', 'link_activate_user');
        $this->loader->add_action('wp_ajax_import_first', 'RM_Services', 'import_form_first_ajax');
        $this->loader->add_filter('plugin_action_links', $this, 'add_plugin_link', 10, 5);
        $this->loader->add_action('media_buttons', $rm_admin, 'add_new_form_editor_button');
        $this->loader->add_action('media_buttons', $rm_admin, 'add_field_autoresponder');
        $this->loader->add_action('plugins_loaded', 'RM_Utilities', 'safe_login', 10);
        $this->loader->add_action('wp_ajax_rm_save_fab_settings', $this->controller, 'run');
        $this->loader->add_action('wp_ajax_import_data', 'RM_Services', 'import_form');
        $this->loader->add_action('wp_ajax_rm_admin_js_data', 'RM_Utilities', 'load_admin_js_data');
        $this->loader->add_action('wp_ajax_nopriv_rm_login_social_user', new RM_User_Services(), 'social_login_using_email');
        $this->loader->add_action('wp_ajax_rm_add_default_form',new RM_User_Services(), 'add_default_form');
        $this->loader->add_action('wp_ajax_send_email_user_view', new RM_User_Services(), 'send_email_ajax'); 
        $this->loader->add_action('wp_ajax_joyride_tour_update', 'RM_Utilities', 'update_tour_state_ajax');
        $this->loader->add_action('wp_ajax_remove_queue', $rm_admin, 'remove_queue');
        $this->loader->add_action('wp_ajax_form_preview', $rm_admin, 'form_preview');
        $this->loader->add_action('rm_pre_admin_template_render', $rm_admin, 'add_version_header');
        $this->loader->add_action('wp_ajax_rm_one_time_action_update', 'RM_Utilities', 'update_action_state_ajax');
        //$this->loader->add_action('wp_ajax_rm_post_feedback', $rm_admin, 'post_feedback');
        //$this->loader->add_action('admin_footer', $rm_admin, 'feedback_dialog');
        $this->loader->add_action('wp_ajax_rm_admin_upload_template', $rm_admin, 'upload_template');
        $this->loader->add_action('wp_ajax_rm_update_submit_field', $rm_admin, 'update_submit_field_config');
        $this->loader->add_action('wp_ajax_rm_fcm_update_form', $rm_admin, 'fcm_update_form');
        $this->loader->add_action('rm_form_saved', $this, 'form_saved');
        $this->loader->add_action('admin_notices', $rm_admin, 'admin_notices');
        $this->loader->add_action('wp_ajax_rm_sort_login_fields', $this->controller, 'run');
        $this->loader->add_action('wp_ajax_rm_update_login_button', $rm_admin, 'update_login_button_config');
        $this->loader->add_action('wp_ajax_rm_suspend_user', 'RM_Utilities', 'suspend_user');
        $this->loader->add_action('wp_ajax_rm_activate_user', 'RM_Utilities', 'activate_user');
        $this->loader->add_action('wp_ajax_rm_activate_rm_user', 'RM_Utilities', 'activate_rm_user');
        $this->loader->add_action('wp_ajax_rm_deactivate_rm_user', 'RM_Utilities', 'deactivate_rm_user');
        $this->loader->add_action('wp_ajax_rm_reset_password', 'RM_Utilities', 'reset_password');
        $this->loader->add_action('wp_ajax_rm_block_ip', 'RM_Utilities', 'block_ip');
        $this->loader->add_action('wp_ajax_rm_unblock_ip', 'RM_Utilities', 'unblock_ip');
        $this->loader->add_action('wp_ajax_rm_send_email', 'RM_Utilities', 'send_email_to_user');
        $this->loader->add_action('wp_ajax_rm_login_field_view_sett', new RM_Login_Manage_Controller(), 'view_sett');
        $this->loader->add_action('wp_ajax_rm_login_form_view_sett', new RM_Login_Manage_Controller(), 'view_sett');
        $this->loader->add_action('wp_ajax_rm_delete_data', 'RM_Utilities', 'rm_delete_data');
        $this->loader->add_filter('wp_privacy_personal_data_exporters', $this, 'register_rm_plugin_exporter', 10, 5);
        $this->loader->add_filter('wp_privacy_personal_data_erasers', $this, 'register_rm_plugin_eraser', 10, 5);
        $this->loader->add_action('admin_init', $this, 'user_online_status');
        $this->loader->add_action('admin_init', $rm_admin, 'rm_editor_style');
        if(registration_magic_is_addon_enabled()) {
            $this->loader->add_action('wp_ajax_rm_sort_form_pages', $this->controller, 'run');
            $this->loader->add_action('wp_ajax_nopriv_rm_get_instagram_user', new RM_User_Services(), 'get_instagram_user');
            $this->loader->add_action('wp_ajax_rm_add_filter', 'RM_Submissions', 'add_new_filter');
            $this->loader->add_action('wp_ajax_rm_delete_filter', 'RM_Submissions', 'delete_filter');
            $this->loader->add_action('wp_ajax_rm_admin_disable_notice', $rm_admin, 'disable_notice');
            $this->loader->add_action('wp_ajax_rm_admin_custom_status_update', $rm_admin, 'custom_status_update');
            $this->loader->add_action('wp_ajax_rm_print_pdf', $this,'check_for_submission_print');
            $this->loader->add_action('wp_ajax_get_price_fields', $this->controller, 'run');
            $this->loader->add_action('wp_ajax_nopriv_get_price_fields', $this->controller, 'run');
            $this->loader->add_filter('rm_form_success_msg','RM_Utilities','filter_success_msg',10,3);
            $this->loader->add_action('wp_ajax_nopriv_rm_get_intent_from_stripe', RM_Stripe_Service::get_instance(),'create_payment_intent');
            $this->loader->add_action('wp_ajax_rm_get_intent_from_stripe', RM_Stripe_Service::get_instance(),'create_payment_intent');
            $this->loader->add_action('wp_ajax_nopriv_rm_stripe_after_intent', RM_Stripe_Service::get_instance(),'after_intent_process');
            $this->loader->add_action('wp_ajax_rm_stripe_after_intent', RM_Stripe_Service::get_instance(),'after_intent_process');
            $this->loader->add_action('wp_ajax_rm_charge_amount_from_stripe', RM_Stripe_Service::get_instance(),'charge');
            $this->loader->add_action('wp_ajax_nopriv_rm_charge_amount_from_stripe', RM_Stripe_Service::get_instance(),'charge');
            $this->loader->add_action('wp_ajax_nopriv_rm_stripe_localize_data', RM_Stripe_Service::get_instance(),'localize_data_json');
            $this->loader->add_filter('wp_ajax_rm_stripe_localize_data',RM_Stripe_Service::get_instance(),'localize_data_json');
        }
   }
   
   
   function register_rm_plugin_exporter( $exporters ) {//echo 'yyyy'.plugin_basename( __FILE__ );die;
        $exporters[] = array(
            'exporter_friendly_name' => __( 'RegistrationMagic Export' ),
            'callback' => array( $this, 'rm_data_exporter' ),
        );
        /*
        $exporters[] = array(
            'exporter_friendly_name' => __( 'RegistrationMagic IPs' ),
            'callback' => array( $this, 'rm_ip_exporter' ),
        );
        */
        return $exporters;
    }
    
    function rm_data_exporter( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $export_items = array();
        $ips_arr = array();
        
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        //echo '<pre>';print_r($related_subs);die;
        
        foreach($related_subs as $related_sub){
            $submission_data = unserialize($related_sub->data);
            $data = '';
            foreach($submission_data as $submission){
                if(is_array($submission->value)){
                    $sub_str = '';
                    if(in_array($submission->type,array('File'))){
                        $sub_str = $submission->value[0];
                    }else if(in_array($submission->type,array('Address','Checkbox','Price','Select','Repeatable'))){
                        foreach($submission->value as $sub_val){
                            if($sub_val!=''){
                                $sub_str.= $sub_val.', ';
                            }
                        }
                        if($sub_str!=''){
                            $sub_str = substr($sub_str, 0, -2);
                        }
                    }
                    $submission->value = $sub_str;
                }
                $data.='Label: '.$submission->label.', Value: '.$submission->value.', Type: '.$submission->type.'<br>';
            }
            
            $export_items[] = array(
                'group_id' => 'rm_submissions',
                'group_label' => __( 'RM Form Submissions' ),
                'item_id' => 'submission-'.$related_sub->submission_id,
                'data' => array(
                    array(
                        'name' => __( 'Submission ID' ),
                        'value' => $related_sub->submission_id
                    ),
                    array(
                        'name' => __( 'Form ID' ),
                        'value' => $related_sub->form_id
                    ),array(
                        'name' => __( 'Data' ),
                        'value' => $data
                    ),array(
                        'name' => __( 'Submitted On' ),
                        'value' => $related_sub->submitted_on
                    )
                )
            );
            
            $IP_detail = RM_DBManager::get_ip_from_stats($related_sub->submission_id);
            if(!empty($IP_detail) && !in_array($IP_detail,$ips_arr)){
                $ips_arr[] = $IP_detail;
                $export_items[] = array(
                    'group_id' => 'rm_recorded_ips',
                    'group_label' => __( 'RM Recorded IPs' ),
                    'item_id' => 'submission-ip-'.$related_sub->submission_id,
                    'data' => array(
                        array(
                            'name' => __( 'IP' ),
                            'value' => $IP_detail
                        )
                    )
                );
            }
            
            
            $payment_detail = RM_DBManager::get_payment_details($related_sub->submission_id);
            if(!empty($payment_detail)){
                $payer_name = '';
                $payer_email = '';
                $payment_data = unserialize($payment_detail->log);
                
                if(!empty($payment_data['payer']->name)){
                    $payer_name = $payment_data['payer']->name;
                }else if(!empty($payment_data['address_name'])){
                    $payer_name = $payment_data['address_name'];
                }
                
                if(!empty($payment_data['payer']->email)){
                    $payer_email = $payment_data['payer']->email;
                } else if(!empty($payment_data['payer_email'])){
                    $payer_email = $payment_data['payer_email'];
                }
                
                $export_items[] = array(
                    'group_id' => 'rm_payment_records',
                    'group_label' => __( 'RM Payment Records' ),
                    'item_id' => 'submission-pay-'.$related_sub->submission_id,
                    'data' => array(
                        array(
                            'name' => __( 'Payer Name' ),
                            'value' => $payer_name
                        ),
                        array(
                            'name' => __( 'Payer Email' ),
                            'value' => $payer_email
                        ),
                        array(
                            'name' => __( 'Submission ID' ),
                            'value' => $related_sub->submission_id
                        ),
                        array(
                            'name' => __( 'Form ID' ),
                            'value' => $related_sub->form_id
                        ),array(
                            'name' => __( 'Invoice ID' ),
                            'value' => $payment_detail->invoice
                        ),array(
                            'name' => __( 'Transaction ID' ),
                            'value' => $payment_detail->txn_id
                        ),array(
                            'name' => __( 'Status' ),
                            'value' => ucfirst($payment_detail->status)
                        ),
                        array(
                            'name' => __( 'Amount' ),
                            'value' => $payment_detail->total_amount.' '.$payment_detail->currency
                        ),array(
                            'name' => __( 'Mode Of Payment' ),
                            'value' => ($payment_detail->pay_proc=='anet_sim')?'Authorize.net':ucfirst($payment_detail->pay_proc)
                        ),array(
                            'name' => __( 'Payment Date' ),
                            'value' => $payment_detail->posted_date
                        )
                    )
                );
            }
        }
        
        $IP_login_details = RM_DBManager::get_ip_from_login($email_address);
        if(!empty($IP_login_details)){
            foreach ($IP_login_details as $IP_login_detail){
                if(!in_array($IP_login_detail->ip,$ips_arr)){
                    $ips_arr[] = $IP_login_detail->ip;
                    $export_items[] = array(
                        'group_id' => 'rm_recorded_ips',
                        'group_label' => __( 'RM Recorded IPs' ),
                        'item_id' => 'submission-ip-'.$related_sub->submission_id,
                        'data' => array(
                            array(
                                'name' => __( 'IP' ),
                                'value' => $IP_login_detail->ip
                            )
                        )
                    );
                }
            }
        }
        
        // Tell core if we have more comments to work on still
        $done = count($export_items) < $number;
        return array(
          'data' => $export_items,
          'done' => $done,
        );
    }
    
    function register_rm_plugin_eraser( $erasers ) {
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_data_eraser' ),
        );
        
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_email_log_eraser' ),
        );
        
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_notes_eraser' ),
        );
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_ip_eraser' ),
        );
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_payments_eraser' ),
        );
        $erasers[] = array(
            'eraser_friendly_name' => __( 'RegistrationMagic Eraser' ),
            'callback' => array( $this, 'rm_submissions_eraser' ),
        );
        return $erasers;
    }
    
    function rm_email_log_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        RM_DBManager::delete_login_log_by_email($email_address);
        //$items_removed = true;
        RM_DBManager::delete_sent_emails($email_address);
        
        $erase_count = 2;
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    function rm_data_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        $erase_count = 0;
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        foreach($related_subs as $related_sub){
            $form_data = unserialize($related_sub->data);
            foreach($form_data as $key=>$value){
                if($value->type=='File'){
                    wp_delete_attachment( $value->value[0] , true );
                    $erase_count++;
                }
            }
            
            $items_removed = true;
            $erase_count = $erase_count+4;
        }
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    function rm_notes_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        $erase_count = 0;
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        foreach($related_subs as $related_sub){
            RM_DBManager::delete_notes_by_id($related_sub->submission_id);
            
            $items_removed = true;
            $erase_count = $erase_count++;
        }
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    function rm_ip_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        $erase_count = 0;
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        foreach($related_subs as $related_sub){
            RM_DBManager::delete_ip_from_stats($related_sub->submission_id);
            
            $items_removed = true;
            $erase_count = $erase_count++;
        }
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    function rm_payments_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        $erase_count = 0;
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        foreach($related_subs as $related_sub){
            RM_DBManager::delete_payment_by_submissions_by_id($related_sub->submission_id);
            
            $items_removed = true;
            $erase_count = $erase_count++;
        }
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    function rm_submissions_eraser( $email_address, $page = 1 ) {
        $number = 500; // Limit us to avoid timing out
        $page = (int) $page;
        
        $items_removed = false;
        
        $erase_count = 0;
        $rm_sr=new RM_Services;
        $related_subs=$rm_sr->get_submissions_by_email($email_address);
        foreach($related_subs as $related_sub){
            RM_DBManager::delete_submissions_by_id($related_sub->submission_id);
            
            $items_removed = true;
            $erase_count = $erase_count++;
        }
        
        // Tell core if we have more comments to work on still
        $done = $erase_count < $number;
        return array( 'items_removed' => $items_removed,
            'items_retained' => false, // always false in this example
            'messages' => array(), // no messages in this example
            'done' => $done,
        );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access   public
     */
    public function define_public_hooks()
    {
        $rm_public = new RM_Public($this->get_plugin_name(), $this->get_version(), $this->get_controller());

        $this->loader->add_action('init', $rm_public, 'cron');
        $this->loader->add_action('init', $rm_public, 'logs_retention');
        $this->loader->add_action('wp_enqueue_scripts', $rm_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $rm_public, 'enqueue_scripts');
        $this->loader->add_shortcode('RM_Login', $rm_public, 'rm_login');
        $this->loader->add_shortcode('RM_Form', $rm_public, 'rm_user_form_render');
        $this->loader->add_shortcode('RM_password_recovery', $rm_public, 'password_recovery');
        $this->loader->add_shortcode('RM_Front_Submissions', $rm_public, 'rm_front_submissions');
        $this->loader->add_action('widgets_init', $rm_public, 'register_otp_widget');
        $this->loader->add_action('wp_ajax_nopriv_rm_set_otp', $this->controller, 'run');
        $this->loader->add_action('wp_ajax_registrationmagic_embedform', $rm_public, 'render_embed');
        $this->loader->add_action('wp_ajax_nopriv_registrationmagic_embedform', $rm_public, 'render_embed');
//for shortcodes in widgets.
        $this->loader->add_filter('widget_text', $rm_public, 'do_shortcode');
        //For legacy version
        $this->loader->add_shortcode('CRF_Login', $rm_public, 'rm_login');
        $this->loader->add_shortcode('CRF_Form', $rm_public, 'rm_user_form_render');
        $this->loader->add_shortcode('CRF_Submissions', $rm_public, 'rm_front_submissions');
        $this->loader->add_action('wp_footer', $rm_public, 'floating_action');
        $this->loader->add_action('wp_ajax_rm_toggle_form_option', $this->controller, 'run');
        //Ajax calls for Username checking
        $this->loader->add_action('wp_ajax_nopriv_rm_user_exists', $this->controller, 'run');
        //Ajax call to get the state field
        $this->loader->add_action('wp_ajax_rm_get_state', 'RM_Utilities', 'get_state');
        $this->loader->add_action('wp_ajax_nopriv_rm_get_state', 'RM_Utilities', 'get_state');
        
        $this->loader->add_action('wp_ajax_rm_js_data', 'RM_Utilities', 'load_js_data');
        $this->loader->add_action('wp_ajax_nopriv_rm_js_data', 'RM_Utilities', 'load_js_data');
        $this->loader->add_action('wp_ajax_rm_save_submit_label', 'RM_Utilities', 'save_submit_label');
        $this->loader->add_action('wp_ajax_nopriv_rm_load_front_users', $rm_public, 'rm_user_list');
        $this->loader->add_action('wp_ajax_rm_register_stat_ids', $rm_public, 'register_stat_ids');
        $this->loader->add_action('wp_ajax_nopriv_rm_register_stat_ids', $rm_public, 'register_stat_ids');
        $this->loader->add_action('widgets_init', $rm_public, 'register_form_widget');
        $this->loader->add_action('widgets_init', $rm_public, 'register_login_btn_widget');
       // $this->loader->add_action('wp', $rm_public, 'request_non_cached_copy');
        $this->loader->add_action('wp_ajax_rm_load_states', $rm_public, 'load_states');
        $this->loader->add_action('wp_ajax_nopriv_rm_load_states', $rm_public, 'load_states');
        $this->loader->add_action('rm_user_registered', 'RM_Email_Service', 'send_activation_link');
        $this->loader->add_action('wp_ajax_rm_activation_link', $rm_public, 'send_activation_link');
        $this->loader->add_action('wp_ajax_nopriv_rm_activation_link', $rm_public, 'send_activation_link');
        $this->loader->add_action('rm_load_user_registrations',$rm_public,'load_user_registrations');
        $this->loader->add_action('init', $this, 'user_online_status');
        $this->loader->add_action('wp_ajax_rm_paypal_ipn',$rm_public,'paypal_ipn');
        $this->loader->add_action('wp_ajax_nopriv_rm_paypal_ipn',$rm_public,'paypal_ipn');
        //$this->loader->add_action('init', $rm_public, 'intercept_login',20);
        $this->loader->add_filter('wp_ajax_rm_get_after_login_redirect',$rm_public,'get_after_login_redirect');
        if(registration_magic_is_addon_enabled()) {
            $this->loader->add_shortcode('RM_Users', $rm_public, 'rm_user_list');
            $this->loader->add_action('wp_ajax_rm_load_front_users', $rm_public, 'rm_user_list');
            $this->loader->add_action('wp_ajax_rm_mark_email_read', $rm_public, 'rm_mark_email_read');
            $this->loader->add_action('wp_ajax_nopriv_rm_mark_email_read', $rm_public, 'rm_mark_email_read');
            $this->loader->add_action('wp_ajax_nopriv_rm_unique_field', $rm_public, 'unique_field_value_check');
            $this->loader->add_action('wp_ajax_rm_unique_field', $rm_public, 'unique_field_value_check');
            $this->loader->add_action('init', $rm_public, 'remove_expired_otp');
            $this->loader->add_action('wp_ajax_rm_genrate_fa_otp', $rm_public, 'generate_fa_otp');
            $this->loader->add_action('wp_ajax_nopriv_rm_genrate_fa_otp', $rm_public, 'generate_fa_otp');
            $this->loader->add_filter('rm_payment_completed_response',$rm_public,'payment_completed_response',10,4);
        }
    }

    /**
     * Register all the hooks common with both public and admin facing
     * functionality of the plugin
     *
     * @access   public
     */
    public function define_global_hooks()
    {
        $this->loader->add_filter('login_redirect', $this, 'after_login_redirect', 12, 3);
        $this->loader->add_filter('register_url', $this, 'rm_register_redirect', 12);
        $this->loader->add_action('wp_login', $this, 'prevent_deactivated_logins');
        $this->loader->add_filter('login_message', $this, 'login_notice');
        $this->loader->add_action('wpmu_new_blog', 'RM_Table_Tech', 'on_create_blog',10,6);
        $this->loader->add_filter('wpmu_drop_tables', 'RM_Table_Tech', 'on_delete_blog');
        $this->loader->add_filter('init', $this, 'run_onload_tasks');
        // using clear_auth_cookie instead of wp_logout as wp_logout do not retain user after wordpress version 5.2
        $this->loader->add_filter('clear_auth_cookie', $this, 'after_logout_redirect',5);
        //$this->loader->add_filter('wp_logout', $this, 'after_logout_redirect',5);
        $this->loader->add_filter('wp_authenticate_user', $this, 'authenticate',10,2);
        $this->loader->add_action('rm_ip_unblocked',new RM_Login_Service(),'unblock_ip_from_log');
        $this->loader->add_filter('lostpassword_url',$this, 'lost_password_page',10,2);
        $this->loader->add_filter('wp_mail_content_type',$this, 'set_html_mail_content_type');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    RM_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    public function get_controller()
    {
        return $this->controller;
    }

    public function start_session()
    {
        if (!session_id())
        {            
            $drake = new stdClass;
            $drake->status = 'OKAY';
            $drake->payload_data = '';
            
            $drake = apply_filters('rm_session_path_hook', $drake);
            
            if($drake->status == 'OKAY')
                session_start();
            elseif($drake->status == 'USE_CUSTOM')
            {
                session_save_path($drake->payload_data);
                session_start();
            }
            elseif($drake->status == 'ERROR')
            {
                global $regmagic_errors;
                $err_msg = sprintf(RM_UI_Strings::get('ERR_SESSION_DIR_NOT_WRITABLE'), session_save_path());
                $regmagic_errors[RM_ERR_ID_SESSION_PATH] = (object) array('msg' => $err_msg, 'should_cont' => false);
                return;
            }
            
        }
    }

    /**
     * Prevents the deactivated user form login
     *
     * @param   string      $user_login     login name of the user
     * @param   object      $user           WP_user object
     * @return boolean
     */
    public function prevent_deactivated_logins($user_login, $user = null)
    {
        if (!$user)
        {
            $user = get_user_by('login', $user_login);
        }
        if (!$user)
        {
            return false;
        }

        $is_disabled = (int) get_user_meta($user->ID, 'rm_user_status', true);
        $prov_acc_act = apply_filters('rm_addon_is_prov_login_active', $user->ID);

        if ($is_disabled == 1 && empty($prov_acc_act))
        {
            wp_clear_auth_cookie();

            $goto = site_url('wp-login.php', 'login');

            //$goto = esc_url(add_query_arg('is_disabled', '1', $goto));
            $goto = esc_url(add_query_arg(array('is_disabled'=>'1','rm_user'=>$user->ID), $goto));

            wp_redirect($goto);

            exit;
        }
    }

    /**
     * returns the message when deactivated user tries to login
     *
     * @param string $notice
     * @return string
     */
    public function login_notice($notice)
    {
        if(defined('REGMAGIC_ADDON')) {
            return apply_filters('rm_addon_login_notice',$notice);
        } else {
            if (isset($_GET['is_disabled']) && $_GET['is_disabled'] === '1')
                $notice = '<div id="login_error"><strong>'.RM_UI_Strings::get('LABEL_ERROR').':</strong> ' . apply_filters('rm_login_notice', RM_UI_Strings::get ('ACCOUNT_NOT_ACTIVE_YET')) . '</div>';
            elseif(isset($_GET['is_reset']) && $_GET['is_reset'] === '1')
                $notice = '<p id="rm_login_error" class="message">' . apply_filters('rm_login_notice', RM_UI_Strings::get('LOGIN_AGAIN_AFTER_RESET')) . '</p>';
            return $notice;
        }
    }

    public function after_login_redirect($redirect_to, $request, $user)
    {
        $rdrto = RM_Utilities::after_login_redirect($user);
        if(!$rdrto)
            return $redirect_to;
        return $rdrto;
    }
    
    public function get_user_before_logout() {
        $_SESSION['RM_LOGOUT_USER'] = wp_get_current_user();
    }
    
    public function after_logout_redirect()
    {
        $user= wp_get_current_user();
        
        if($user->ID !== 0) {
            // Deleting user online status
            $this->clean_user_online_status($user->ID);

            $redirect_to = '';
            
            //if(isset($_SERVER['HTTP_REFERER'])){
                //$redirect_to = $_SERVER['HTTP_REFERER'];
            //}

            $login_service = new RM_Login_Service();
            $red = $login_service->get_redirections();
            if ($red['redirection_type'] == 'common') {
                if($red['admin_redirection_link'] === 1) {
                    $is_admin = user_can($user->ID, 'manage_options');
                    if (!empty($red['logout_redirection']) && $is_admin) {
                        $redirect_to = admin_url();
                    }
                } elseif (!empty($red['logout_redirection'])) {
                    $redirect_to = get_permalink($red['logout_redirection']);
                }
            } elseif ($red['redirection_type'] == 'role_based') {
                $user_meta = get_userdata($user->ID);
                if(!empty($user_meta->roles)){
                    $user_roles = $user_meta->roles;
                    if (!empty($red['role_based_login_redirection'])) {
                        foreach ($user_roles as $role) {
                            $role= strtolower(str_replace(' ', '', $role));
                            if (in_array($role, $red['role_based_login_redirection'])) {
                                if (!empty($red[$role . '_logout_redirection'])) {
                                    $redirect_to = get_permalink($red[$role . '_logout_redirection']);
                                    break;
                                }
                            }
                        }
                    }
                }
            } else {
                $post_id = get_option('rm_option_post_logout_redirection_page_id');
                if($post_id) {
                    $redirect_to = get_permalink($post_id);
                }
            }
            
            $redirect_to= apply_filters('rm_logout_redirection',$redirect_to,$user);
            if(!empty($redirect_to)) {
                wp_set_current_user(0);
                wp_redirect($redirect_to);
                exit;
            } else {
                wp_set_current_user(0);
            }
        }
    }

    public function rm_register_redirect($registration_redirect)
    {
        $post_id = get_option('rm_option_default_registration_url');
        if ($post_id != 0)
        {
            $url = home_url("?p=" . $post_id);
            return $url;
        }
        return $registration_redirect;
    }

    public function add_ob_start($slug)
    {
        $pass = array(
            'rm_login_form',
            'rm_attachment_download_all',
            'rm_submission_print_pdf',
            'rm_attachment_download',
            'rm_attachment_download_selected',
            'rm_submission_export',
            'rm_front_log_off',
            'rm_form_export',
            'rm_login_sett_manage'
        );

        if (in_array($slug, $pass))
            if(defined('REGMAGIC_ADDON')) {
                define('RM_BUFFER_STARTED',true);
            }
            ob_start();

        // Incase facebook
        if (isset($_REQUEST['rm_target']) && $_REQUEST['rm_target'] == 'fbcb')
        {
            if(defined('REGMAGIC_ADDON')) {
                define('RM_BUFFER_STARTED',true);
            }
            ob_start();
        }
    }
    
    //Add custom links on wp plugin listing page.
    public function add_plugin_link($actions, $plugin_file)
    {        
        if (RM_PLUGIN_BASENAME == $plugin_file)
        {
            if(!defined('REGMAGIC_ADDON')) {
                $extra_menus = array('upgrade' => '<a class="rm-upgrade-menu-link" target="_blank" href="'.RM_Utilities::comparison_page_link().'"><strong style="color: #11967A; display: inline;">Upgrade</strong></a>',
                           'settings' => '<a href="' . get_admin_url() . 'admin.php?page=rm_options_manage">Settings</a>',
                           'support' => '<a href="' . get_admin_url() . 'admin.php?page=rm_support_forum">Support</a>');
            } else {
                $extra_menus = array(
                           'settings' => '<a href="' . get_admin_url() . 'admin.php?page=rm_options_manage">Settings</a>',
                           'support' => '<a href="' . get_admin_url() . 'admin.php?page=rm_support_forum">Support</a>'
                );
            }
            
            $actions = $extra_menus + $actions;
        }

        return $actions;
    }
    
    public function on_upgrade_migrate() {
        global $rmbasic;
        
        if (!function_exists('is_plugin_active_for_network')) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        if (is_plugin_active_for_network($rmbasic)) 
            RM_Activator::migrate(true);
        else
            RM_Activator::migrate(false);

    }
     public function patch_dbversion_miss() {
        global $wpdb;
        
        $existing_rm_db_version = get_site_option('rm_option_db_version', false);
        $existing_plugin_version = get_site_option('rm_option_rm_version', false);
        $db_name = DB_NAME;
        $sub_table_name = $wpdb->prefix."rm_submissions";
        $field_table_name = $wpdb->prefix."rm_fields";
        
        if (($existing_rm_db_version == false && $existing_plugin_version) || floatval($existing_rm_db_version) < 4.4 )
        {      
            $test_query = $wpdb->prepare("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'page_no'",esc_sql($db_name),esc_sql($field_table_name));
            $result = $wpdb->get_results($test_query);
            if($result == NULL || count($result) == 0)
                update_site_option('rm_option_db_version', '4.0');
            else
            {
                $test_query = $wpdb->prepare("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'field_is_editable'",esc_sql($db_name),esc_sql($field_table_name));
                $result = $wpdb->get_results($test_query);
                if($result == NULL || count($result) == 0)
                    update_site_option('rm_option_db_version', '4.1');
                else
                {
                    update_site_option('rm_option_db_version', RM_DB_VERSION);
                }
            }
        }
    }
    public function run_onload_tasks(){
         $this->patch_dbversion_miss();
         $this->on_upgrade_migrate();
     }
     
    public function form_saved($form_id){
        RM_Utilities::sync_hide_option_with_fields($form_id);
    } 
    /*
     * Called on every login attempt
     */
    public function log_login_attempt($user,$username,$password){
        if (!empty($username) || !empty( $password)) {
            $u= get_user_by('login',$username);
            if(empty($u)){
                $u= get_user_by('email',$username);
            }
            if(!empty($u) && !wp_check_password($password, $u->data->user_pass, $u->ID)){
                $args= array('email'=>$u->user_email,'username_used'=>$username,'ip'=>$_SERVER['REMOTE_ADDR'],'status'=>0);
                RM_DBManager::insert_login_log($args);
            }
            else if(!empty($u) && wp_check_password($password, $u->data->user_pass, $u->ID)){
                $args= array('email'=>$u->user_email,'username_used'=>$username,'ip'=>$_SERVER['REMOTE_ADDR'],'status'=>1);
                RM_DBManager::insert_login_log($args);
            }
        }
        return $user;
    } 
    
    public function login_failed($username){
        $u= get_user_by('login',$username);
        $args= array('email'=>'','username_used'=>$username,'ip'=>$_SERVER['REMOTE_ADDR'],'status'=>0);
        if(empty($u)){
            $u= get_user_by('email',$username);
        }
        if(!empty($u)){
            $args['email']= $u->user_email;
        }
        RM_DBManager::insert_login_log($args);
        
    }
    
    public function user_online_status(){
        $this->clean_user_online_status(); // Removing any older user entries
        $logged_in_users = get_transient('rm_user_online_status');
        $logged_in_users = $logged_in_users==false ? array() : $logged_in_users;
        
        // get current user ID
        $user = wp_get_current_user();
        if(!empty($user->ID)){
            $no_need_to_update = isset($logged_in_users[$user->ID]) && $logged_in_users[$user->ID] >  (time()-(30 * 60));
            // update the list if needed
            if(!$no_need_to_update){
              $logged_in_users[$user->ID] = time();
              set_transient('rm_user_online_status', $logged_in_users, $expire_in = (30*60)); // 30 mins 
            }
        }
        // Removing duplicate cron jobs
        $cron_jobs = get_option( 'cron' );
        if(is_array($cron_jobs)){
            $cron_deleted= false;
            foreach($cron_jobs as $index=>$cj){
                    if(isset($cj['twicedaily'])){
                            unset($cron_jobs[$index]);
                            $cron_deleted= true;
                    }
            }
            if($cron_deleted){
                    update_option('cron',$cron_jobs);
            }
        }
    }
    
    public function clean_user_online_status($user_id=0){
        $logged_in_users = get_transient('rm_user_online_status');
        if(!empty($logged_in_users)){
            if(empty($user_id)){
                foreach($logged_in_users as $user=>$time){
                    if(time()>= $time + 1800){
                        unset($logged_in_users[$user]);
                    }
                }
            }
            else
            {
                if(isset($logged_in_users[$user_id])){
                    unset($logged_in_users[$user_id]);
                }
            }
            set_transient('rm_user_online_status', $logged_in_users, $expire_in = (30*60));
        }
        
    }
    
    public function lost_password_page($lostpassword_url, $redirect){
        $login_service= new RM_Login_Service;
        $gopt = new RM_Options();
        $recovery_options= $login_service->get_recovery_options();
        if(!empty($recovery_options['en_pwd_recovery']) && !empty($recovery_options['rec_redirect_default'])){
            $page_id= $recovery_options['recovery_page'];
            if(!empty($page_id)){
                $lostpassword_url= get_permalink($page_id);
            }
        }
        return $lostpassword_url;
    }
    
    public function authenticate($user,$password){
        if($user instanceof WP_Error){
            return $user;
        }
        $is_disabled = absint(get_user_meta($user->ID, 'rm_user_status', true));
        if(!empty($is_disabled)){
            return new WP_Error('user_not_active',__('Your account has not been activated yet.','custom-registration-form-builder-with-submission-manager'));
        }
        return $user;
   }
    
    public function check_for_submission_print() {
        if(is_user_logged_in()){
           if(!empty($_REQUEST['rm_submission_id']) && !empty($_REQUEST['action']) && $_REQUEST['action']=='rm_print_pdf') {
               $xml_loader = registration_magic_is_addon_enabled() ? RM_XML_Loader::getInstance(RM_INCLUDES_DIR . 'rm_config_addon.xml') : RM_XML_Loader::getInstance(RM_INCLUDES_DIR . 'rm_config.xml');
               $request = new RM_Request($xml_loader);
               $request->setReqSlug('rm_submission_print_pdf', true);
               $params = array('request' => $request, 'xml_loader' => $xml_loader,'attribute'=>array());
               $this->controller = new RM_Main_Controller($params);
               return $this->controller->run();
           }
        }
    }
    
    public function set_html_mail_content_type() {
    	return 'text/html';
	}

}
