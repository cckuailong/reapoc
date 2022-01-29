<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/public
 * @author     CMSHelplive
 */
class RM_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $registraion_magic    The ID of this plugin.
     */
    public $plugin_name;
    public static $form_counter=0;
    public static $login_form_counter=0;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $version    The current version of this plugin.
     */
    public $version;

    /**
     * The controller of this plugin.
     *
     * @since    1.0.0
     * @access   public
     * @var      string    $controller    The main controller of this plugin.
     */
    public $controller;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public static $editor_counter = 1;
    
    // Helps to avoid success message for same form twice
    public static $success_form= false;
    
    public function __construct($plugin_name, $version, $controller) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->controller = $controller;
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    public function get_controller() {
        return $this->controller;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     * 
     */
    public function enqueue_styles() {
        $settings = new RM_Options;
        $theme = $settings->get_value_of('theme');
        $layout = $settings->get_value_of('form_layout');
        if(defined('REGMAGIC_ADDON'))
             wp_enqueue_style('style_rm_rating', RM_ADDON_BASE_URL . 'public/js/rating3/rateit.css', array(), $this->version, 'all');

        switch ($theme) {
            case 'classic' :
                if ($layout == 'label_top') {
                    wp_enqueue_style('rm_theme_classic_label_top', plugin_dir_url(__FILE__) . 'css/theme_rm_classic_label_top.css', array(), $this->version, 'all');
                    if(defined('REGMAGIC_ADDON'))
                        wp_enqueue_style('rm_theme_classic_label_top_addon', RM_ADDON_BASE_URL . 'public/css/theme_rm_classic_label_top.css', array(), $this->version, 'all');
                } elseif ($layout == 'two_columns') {
                    wp_enqueue_style('rm_theme_classic_two_columns', plugin_dir_url(__FILE__) . 'css/theme_rm_classic_two_columns.css', array(), $this->version, 'all');
                    if(defined('REGMAGIC_ADDON'))
                        wp_enqueue_style('rm_theme_classic_two_columns_addon', RM_ADDON_BASE_URL . 'public/css/theme_rm_classic_two_columns.css', array(), $this->version, 'all');
                } else
                    wp_enqueue_style('rm_theme_classic', plugin_dir_url(__FILE__) . 'css/theme_rm_classic.css', array(), $this->version, 'all');
                break;

            /* case 'blue' :
              if ($layout == 'label_top')
              wp_enqueue_style('rm_theme_blue_label_top', plugin_dir_url(__FILE__) . 'css/theme_rm_blue_label_top.css', array(), $this->version, 'all');
              elseif ($layout == 'two_columns')
              wp_enqueue_style('rm_theme_blue_two_columns', plugin_dir_url(__FILE__) . 'css/theme_rm_blue_two_columns.css', array(), $this->version, 'all');
              else
              wp_enqueue_style('rm_theme_blue', plugin_dir_url(__FILE__) . 'css/theme_rm_blue.css', array(), $this->version, 'all');
              break; */

            default :
                if ($layout == 'label_top') {
                    wp_enqueue_style('rm_theme_matchmytheme_label_top', plugin_dir_url(__FILE__) . 'css/theme_rm_matchmytheme_label_top.css', array(), $this->version, 'all');
                    if(defined('REGMAGIC_ADDON'))
                        wp_enqueue_style('rm_theme_matchmytheme_label_top_addon', RM_ADDON_BASE_URL . 'public/css/theme_rm_matchmytheme_label_top.css', array(), $this->version, 'all');
                } elseif ($layout == 'two_columns') {
                    wp_enqueue_style('rm_theme_matchmytheme_two_columns', plugin_dir_url(__FILE__) . 'css/theme_rm_matchmytheme_two_columns.css', array(), $this->version, 'all');
                    if(defined('REGMAGIC_ADDON'))
                        wp_enqueue_style('rm_theme_matchmytheme_two_columns_addon', RM_ADDON_BASE_URL . 'public/css/theme_rm_matchmytheme_two_columns.css', array(), $this->version, 'all');
                } else
                    wp_enqueue_style('rm_theme_matchmytheme', plugin_dir_url(__FILE__) . 'css/theme_rm_matchmytheme.css', array(), $this->version, 'all');
                break;
        }
        //wp_enqueue_style('rm-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css', false, $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/style_rm_front_end.css', array(), $this->version, 'all');
        //wp_enqueue_style('rm_black_theme', plugin_dir_url(__FILE__) . 'css/rm_black_theme.css', array(), $this->version, 'all');
        if(defined('REGMAGIC_ADDON'))
            wp_enqueue_style($this->plugin_name . '_addon', RM_ADDON_BASE_URL . 'public/css/style_rm_front_end.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $gopt= new RM_Options();
        $magic_pop= $gopt->get_value_of('display_floating_action_btn');
        if(defined('REGMAGIC_ADDON'))
            wp_register_script('rm_front', RM_ADDON_BASE_URL . 'public/js/script_rm_front.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-datepicker','jquery-effects-core','jquery-effects-slide'), $this->version, false);
        else
            wp_register_script('rm_front', plugin_dir_url(__FILE__) . 'js/script_rm_front.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-datepicker','jquery-effects-core','jquery-effects-slide'), $this->version, false);
        $rm_ajax_data= array(
                        "url"=>admin_url('admin-ajax.php'),
                        "gmap_api"=>$gopt->get_value_of("google_map_key"),
                        'no_results'=>__('No Results Found','custom-registration-form-builder-with-submission-manager'),
                        'invalid_zip'=>__('Invalid Zip Code','custom-registration-form-builder-with-submission-manager'),
                        'request_processing'=>__('Please wait...','custom-registration-form-builder-with-submission-manager'),
                        'security'=>wp_create_nonce('rm-social-login-security'),
                        'hours'=>__('Hours','custom-registration-form-builder-with-submission-manager'),
                        'minutes'=>__('Minutes','custom-registration-form-builder-with-submission-manager'),
                        'seconds'=>__('Seconds','custom-registration-form-builder-with-submission-manager'),
                        'days'=>__('Days','custom-registration-form-builder-with-submission-manager'),
                        'months'=>__('Months','custom-registration-form-builder-with-submission-manager'),
                        'years'=>__('Years','custom-registration-form-builder-with-submission-manager'));
        if(defined('REGMAGIC_ADDON')) {
            $login_service= new RM_Login_Service();
            $auth_options= $login_service->get_auth_options();
            $rm_ajax_data['max_otp_attempt'] = !empty($auth_options['en_resend_otp']) ? $auth_options['otp_resend_limit'] : 0;
        }
        wp_localize_script('rm_front','rm_ajax',$rm_ajax_data);
        wp_enqueue_script('rm_front');
        
        wp_register_script('rm_front_form_script', RM_BASE_URL."public/js/rm_front_form.js",array('rm_front'), $this->version, false);
        //Register jQ validate scripts but don't actually enqueue it. Enqueue it from within the shortcode callback.
        wp_register_script('rm_jquery_validate', RM_BASE_URL."public/js/jquery.validate.min.js");
        wp_register_script('rm_jquery_validate_add', RM_BASE_URL."public/js/additional-methods.min.js");
        wp_register_script('rm_jquery_conditionalize', RM_BASE_URL."public/js/conditionize.jquery.js");
        
        if(isset($_GET['action']) && $_GET['action']=='registrationmagic_embedform' && defined('REGMAGIC_ADDON')){
            wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
            wp_enqueue_script("rm_chart_widget",RM_BASE_URL."public/js/google_chart_widget.js");
            $service= new RM_Services();
            $gmap_api_key= $service->get_setting('google_map_key');
            if(!empty($gmap_api_key)){
                wp_enqueue_script ('google_map_key', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places');
                wp_enqueue_script("rm_map_widget_script",RM_BASE_URL."public/js/map_widget.js");
            }
            wp_enqueue_script("rm_pwd_strength",RM_BASE_URL."public/js/password.min.js");
            wp_enqueue_script("rm_mobile_data_script", RM_BASE_URL . "public/js/mobile_field/data.js");
            wp_enqueue_script("rm_mobile_script", RM_BASE_URL . "public/js/mobile_field/intlTelInput.js");
            wp_enqueue_style("rm_mobile_style", RM_BASE_URL . "public/css/mobile_field/intlTelInput.css");
            wp_localize_script('rm_mobile_script','rm_country_list', RM_Utilities::get_countries() );
            wp_enqueue_script("rm_mask_script", RM_BASE_URL . "public/js/jquery.mask.min.js");
            wp_enqueue_script('rm_jquery_conditionalize');
        }
    }

    public function run_controller($attributes = null, $content = null, $shortcode = null) {
        return $this->controller->run();
    }

    public function rm_front_submissions($attr) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->rm_front_submissions($attr,$this);
        }
        $form_prev= isset($_GET['form_prev']) ? absint($_GET['form_prev']) : '';
        if(is_user_logged_in() && class_exists('Profile_Magic') && empty($attr) && empty($form_prev) && !isset($_REQUEST['submission_id']) ){
             return do_shortcode('[PM_Profile]');
        }
        $user_model= new RM_User;
         
        if(!empty($_GET['form_prev']) && !empty($_GET['form_id']) && is_super_admin())
        {  
            $form_id= $_GET['form_id'];
            $form_factory= defined('REGMAGIC_ADDON') ? new RM_Form_Factory_Addon() : new RM_Form_Factory();
            $form= $form_factory->create_form($form_id);
            $form->set_preview(true);
            echo '<script>jQuery(document).ready(function(){jQuery(".entry-header").remove();}); </script>';
            echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
            echo '<div class="rm_embedeed_form">' . $form->render() . '</div>';
            return;
        }
        
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }

        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');

        $request = new RM_Request($xml_loader);
        if(isset($_POST['rm_slug'])){
            $request->setReqSlug($_POST['rm_slug'], true);
        }
        else{
            $request->setReqSlug('rm_front_submissions', true);
        }

        $params = array('request' => $request, 'xml_loader' => $xml_loader,'attr'=>$attr);
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }

    public function rm_login($attributes) {
        self::$login_form_counter++;        
        $_REQUEST['login_popup_show']  = 0;
        if(defined('REGMAGIC_ADDON')) {
            if(!empty($_POST) && isset($_POST['rm_form_sub_id']) && ($_POST['rm_form_sub_id']=='rm_login_form_'.self::$login_form_counter || $_POST['rm_form_sub_id']=='rm_otp_form_'.self::$login_form_counter)){
                $_REQUEST['login_popup_show']  = 1;
            }
            $_REQUEST['hidden_forms_id'] = array();
        } else {
            if(!empty($_POST) && isset($_POST['rm_form_sub_id']) && $_POST['rm_form_sub_id']=='rm_login_form_'.self::$login_form_counter){
                $_REQUEST['login_popup_show']  = 1;
            }
        }
        if(!empty($_POST) && isset($_POST['rm_form_sub_id'])){
            if($_POST['rm_form_sub_id']=='rm_login_form_'.self::$login_form_counter){
                echo '<style>#'.$_POST['rm_form_sub_id'].'{display:block;}</style>';
                echo '<style>#'.str_replace('rm_login_form_','rm_otp_form_',$_POST['rm_form_sub_id']).'{display:block;}</style>';
            }else{
                if(defined('REGMAGIC_ADDON'))
                    $_REQUEST['hidden_forms_id'][] = 'rm_login_form_'.self::$login_form_counter;
                //echo '<script>jQuery(document).ready(function(){jQuery("#rm_login_form_'.self::$login_form_counter.'").html("<div class=\'rm-login-attempted-notice\'>'.__('Note: You are already attempting login using a different login form on this page. To keep your logging experience simple and secure, this login form in no longer accessible. Please continue the login process using the form with which you attempted login before the page refresh.','custom-registration-form-builder-with-submission-manager').'</div>")});</script>';
            }
        }
        
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }

        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');

        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_login_form', true);

        $params = array('request' => $request, 'xml_loader' => $xml_loader,'attr'=>$attributes);
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }

    public function rm_user_form_render($attribute) {
        RM_DBManager::add_form_published_pages(absint($attribute['id']),get_the_ID());
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->rm_user_form_render($attribute,$this);
        }
        self::$form_counter++;
        $this->disable_cache();
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }
        $form_id= $attribute['id'];
        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml') : RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');
        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_user_form_process', true);
        if(!self::$success_form && !empty($request->req['rm_success']) && !empty($form_id) && isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']) && $form_id==$request->req['rm_form_id']){
            self::$success_form= true;
            $form = new RM_Forms();
            $form->load_from_db($form_id);
            $form_options= $form->form_options;
            $html = "<div class='rm-post-sub-msg'>";
            $sub_id = isset($request->req['rm_sub_id']) ? absint($request->req['rm_sub_id']) : 0;
            $html .= $form_options->form_success_message != "" ? apply_filters('rm_form_success_msg',$form_options->form_success_message,$form_id,$sub_id) : $form->form_name . " Submitted ";
            $html .= '</div>';
            return $html;
        }
        $params = array('request' => $request, 'xml_loader' => $xml_loader, 'form_id' => isset($attribute['id']) ? $attribute['id'] : null);
        $params['force_enable_multiform'] = true;
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }
    
      // Disable cache 
    public function disable_cache()
    { 
        //Diable caches
        if(!defined('DONOTCACHEPAGE'))
            define( 'DONOTCACHEPAGE', true );
    }
    
    public function register_otp_widget() {
        register_widget('RM_OTP_Widget');
    }
    
    public function register_login_btn_widget()
    {  
        register_widget('RM_Login_Btn_Widget');
    }
    
    public function register_form_widget()
    {
        register_widget('RM_Form_Widget');
    }
    
    /* function add_field_invites()
      {
      $screen = get_current_screen();

      if($screen->base=='registrations_page_rm_form_add')
      {   if(self::$editor_counter==3) {
      $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');

      $request = new RM_Request($xml_loader);
      $request->setReqSlug('rm_editor_actions_add_email', true);

      $params = array('request' => $request, 'xml_loader' => $xml_loader);
      $this->controller = new RM_Main_Controller($params);
      $this->controller->run();
      }
      self::$editor_counter= self::$editor_counter +1;
      }

      } */

    function execute_login() {
        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');

        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_login_form', true);

        $params = array('request' => $request, 'xml_loader' => $xml_loader);
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }

    public function cron() {
        RM_DBManager::delete_front_user(1, 'h');
    }


    public function do_shortcode($content, $ignore_html = false) {
        if (has_shortcode($content,'RM_Form') || has_shortcode($content,'CRF_Login') || has_shortcode($content,'CRF_Form') || has_shortcode($content,'CRF_Submissions') || has_shortcode($content,'RM_Users') || has_shortcode($content,'RM_Front_Submissions')){
            return do_shortcode($content, $ignore_html);
        }
        return $content;
    }

    public function floating_action() {
        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');

        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_front_fab', true);

        $params = array('request' => $request, 'xml_loader' => $xml_loader);
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }
    
    public function render_embed() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->render_embed($this);
        }
    }
    
    public function rm_user_list($attribute){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->rm_user_list($attribute,$this);
        }
    }
    
    public function rm_mark_email_read(){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->rm_mark_email_read();
        }
    }
    
    public function unique_field_value_check(){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->unique_field_value_check();
        }
    }
    
    public function register_stat_ids() {
        $result = array();
        if(isset($_POST['form_ids'])) {            
            
            $form_ids = $_POST['form_ids'];
            
            if(is_array($form_ids) && count($form_ids) > 0) {
                $front_form_service = new RM_Front_Form_Service;            
                foreach($form_ids as $form_uid) {
                    $form_id = explode("_", $form_uid);
                    if(count($form_id) == 3) {
                        $form_id = intval($form_id[1]);                                                
                        $result[$form_uid] = $front_form_service->create_stat_entry(array('form_id' => $form_id));
                    }                
                }
            }
        }
        echo json_encode($result);
        wp_die();
    }
    
    public function request_non_cached_copy() {
        if(defined('REGMAGIC_ADDON'))
            return;
        
        global $post;
        
        if( isset($_GET['rmcb']) || isset($request->req['rm_pproc']))
            return;
        
        if($post instanceof WP_Post && has_shortcode($post->post_content, 'RM_Form')) {
            $red_url = esc_url(add_query_arg('rmcb', time()));
            wp_redirect($red_url);
            exit();
        }
    }
    
    public function load_states(){
        if(empty($_POST['country']))
            die('Unknown country');
            
        $country= strtolower($_POST['country']);
       
        $states= array();
        if($country=="us"){
            $states= RM_Utilities::get_usa_states();
        } else if($country=="canada"){
             $states= RM_Utilities::get_canadian_provinces();
        }
        echo json_encode($states);
        
        die;
    }
    
    public function send_activation_link(){
        $user_id= absint($_POST['user_id']);
        $response= array('success'=>true);
        
        if(empty($user_id)){
            $response['success']= false;
            $response['msg']= __('No such user exists', 'custom-registration-form-builder-with-submission-manager');
            echo json_encode($response);
            exit;
        }
        $user_info = get_userdata($user_id); 
        if(empty($user_info)){
            $response['success']= false;
            $response['msg']= __('No such user exists', 'custom-registration-form-builder-with-submission-manager');
            echo json_encode($response);
            exit;
        }
        
        $activation_nonce= sanitize_text_field($_POST['activation_nonce']);
        if(wp_verify_nonce( $activation_nonce, 'rm_send_verification_nonce' )){
            RM_Email_Service::send_activation_link($user_id);
            
            $response['msg']= __('Verification link has been sent on your registered email account. Please check.', 'custom-registration-form-builder-with-submission-manager');
        }
        else{
             $response['msg']= __('Incorrect security token. Please try after some time.', 'custom-registration-form-builder-with-submission-manager');
        }
        echo json_encode($response);
        exit;
    }
    
    public function remove_expired_otp(){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->remove_expired_otp();
        }
    }
    
    public function generate_fa_otp(){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->generate_fa_otp();
        }
    }
    
    public function logs_retention(){
        $login_service= new RM_Login_Service();
        $log_options= $login_service->get_log_options();
        
    }
    
    public function load_user_registrations(){
        $front_service= new RM_Front_Service();
       
        $data = new stdClass;
        $data->is_authorized = true;
        $data->submissions = array();
        $data->form_names = array();
        $data->submission_exists = false;
        $data->total_submission_count = 0;
        $user_email = $front_service->get_user_email();
        //data for user page
        $user = get_user_by('email', $user_email);
        if ($user instanceof WP_User) {
            $data->is_user = true;
            $data->user = $user;
            $data->custom_fields = $front_service->get_custom_fields($user_email);
        } else {
            $data->is_user = false;
        }

        //For pagination of submissions
        $entries_per_page_sub = 20;
        $req_page_sub = (isset($request->req['rm_reqpage_sub']) && $request->req['rm_reqpage_sub'] > 0) ? $request->req['rm_reqpage_sub'] : 1;
        $offset_sub = ($req_page_sub - 1) * $entries_per_page_sub;

        if (isset($request->req['rm_edit_user_details'])) { 
            $form_ids = json_decode(stripslashes($request->req['form_ids']));
            $submissions = $front_service->get_latest_submission_for_user($user_email, $form_ids);
            $data->total_submission_count = $total_entries_sub = count($submissions);
            $distinct = true;
        } else { 
            $submissions = $front_service->get_submissions_by_email($user_email, $entries_per_page_sub, $offset_sub);
            $data->total_submission_count = $total_entries_sub = $front_service->get_submission_count($user_email);
            $distinct = false;
        }

        $submission_ids = array();
        if ($submissions) 
        {
            $data->submission_exists = true;
            foreach ($submissions as $submission) {
                $form_name = $front_service->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 1, 'form_name');
                $data->submissions[$i] = new stdClass();
                $data->submissions[$i]->submission_ids = array();
                $data->submissions[$i]->submission_id = $submission->submission_id;
                $data->submissions[$i]->submitted_on = $submission->submitted_on;
                $data->submissions[$i]->form_name = $form_name;
                $data->form_names[$submission->submission_id] = $form_name;
                $submission_ids[$i] = $front_service->get_oldest_submission_from_group($submission->submission_id);
                $i++;
            }
            $total_entries_pay = 0;
            $settings = new RM_Options;
            $data->date_format = get_option('date_format');
            $data->payments = $front_service->get_payments_by_submission_id($submission_ids, 999999, 0, null, true);
            if ($data->payments)
                foreach ($data->payments as $i => $p) {
                    if (!isset($data->form_names[$p->submission_id])) {
                        $data->form_names[$p->submission_id] = $front_service->get('FORMS', array('form_id' => $p->form_id), array('%d'), 'var', 0, 1, 'form_name');
                    }
                    $data->payments[$i]->total_amount = $settings->get_formatted_amount($data->payments[$i]->total_amount, $data->payments[$i]->currency);
                    $total_entries_pay = $i+1;
                }

            //For pagination of payments
            $entries_per_page_pay = 20;
            $req_page_pay = (isset($request->req['rm_reqpage_pay']) && $request->req['rm_reqpage_pay'] > 0) ? $request->req['rm_reqpage_pay'] : 1;
            $data->offset_pay = $offset_pay = ($req_page_pay - 1) * $entries_per_page_pay;
            $data->total_pages_pay = (int) ($total_entries_pay / $entries_per_page_pay) + (($total_entries_pay % $entries_per_page_pay) == 0 ? 0 : 1);
            $data->curr_page_pay = $req_page_pay;
            $data->starting_serial_number_pay = $offset_pay + 1;
            $data->end_offset_this_page = ($data->curr_page_pay < $data->total_pages_pay) ? $data->offset_pay + $entries_per_page_pay : $total_entries_pay;
            $data->total_pages_sub = (int) ($total_entries_sub / $entries_per_page_sub) + (($total_entries_sub % $entries_per_page_sub) == 0 ? 0 : 1);
            $data->curr_page_sub = $req_page_sub;
            $data->starting_serial_number_sub = $offset_sub + 1;
            //Pagination Ends submissions
            $data->inbox = $this->get_inbox_data($user_email, $service, $request, $params);
            include('views/my_account/registrations.php');
        } elseif ($data->is_user === true) {
            $data->payments = false;
            $data->submissions = false;
            $data->inbox = $this->get_inbox_data($user_email, $service, $request, $params);
            include('views/my_account/registrations.php');
        } else {
            //$view = $this->mv_handler->setView('not_authorized', true);
            //$msg = RM_UI_Strings::get('MSG_NO_SUBMISSION_FRONT');
            //return $view->read($msg);
        }
        
    }
    
    public function paypal_ipn(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $paypal_service = new RM_Paypal_Service();
            $resp = $paypal_service->callback('ipn',null,null);
        }
        die;
    }
    
    public function payment_completed_response($response,$submission,$form_id,$payment_status){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->payment_completed_response($response,$submission,$form_id,$payment_status);
        }
    }
    
    public function intercept_login(){
        if(defined('REGMAGIC_ADDON')) {
            $addon_public = new RM_Public_Addon();
            return $addon_public->intercept_login();
        }
        $slug= isset($_POST['rm_slug']) ? sanitize_text_field($_POST['rm_slug']) : '';
        if($slug!='rm_login_form')
            return;
        
        $username = sanitize_text_field($_POST['username']);
        $login_service = new RM_Login_Service();
        $login_form= json_decode($login_service->get_form(),true);
        $user= $login_service->get_user($username);
        $password = sanitize_text_field($_POST['pwd']);
        if(empty($user))
            return;
        $user_service= new RM_User_Services();
        $is_disabled = (int) get_user_meta($user->ID, 'rm_user_status', true);
        if(empty($is_disabled)){
            $user = wp_signon(array('user_login'=>$user->user_login,'user_password'=>$password));
            if($user instanceof WP_User){
                wp_set_current_user($user->ID);
            }   
        }
    }
    
    public function password_recovery($attrs) {
        $xml_loader = defined('REGMAGIC_ADDON') ? RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml'): RM_XML_Loader::getInstance(plugin_dir_path(__FILE__) . 'rm_config.xml');
        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_login_lost_password', true);
        $params = array('request' => $request, 'xml_loader' => $xml_loader);
        $this->controller = new RM_Main_Controller($params);
        return $this->controller->run();
    }
    
    public function get_after_login_redirect() {
        $response = array();
        $rdrto = RM_Utilities::after_login_redirect(wp_get_current_user());
        if(!empty($rdrto)){
            $response['redirect'] = $rdrto;
        } else {
            $response['redirect'] = get_home_url();
        }
        echo json_encode($response);
        wp_die();
    }
}
