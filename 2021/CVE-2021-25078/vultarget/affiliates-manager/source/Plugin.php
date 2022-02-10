<?php

require_once WPAM_BASE_DIRECTORY . "/source/Util/BinConverter.php";
require_once WPAM_BASE_DIRECTORY . "/source/Util/EmailHandler.php";
require_once WPAM_BASE_DIRECTORY . "/source/Util/UserHandler.php";
require_once WPAM_BASE_DIRECTORY . "/source/Util/AffiliateFormHelper.php";
require_once WPAM_BASE_DIRECTORY . "/source/Data/DataAccess.php";
require_once WPAM_BASE_DIRECTORY . "/source/Data/DatabaseInstaller.php";
require_once WPAM_BASE_DIRECTORY . "/source/PostHelper.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/AdminPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/MyCreativesPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/MyAffiliatesPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/NewAffiliatePage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/SettingsPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/Admin/PaypalPaymentsPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Options.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/TemplateResponse.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/PublicPage.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/AffiliatesHome.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/AffiliatesRegister.php";
require_once WPAM_BASE_DIRECTORY . "/source/Pages/AffiliatesLogin.php";
require_once WPAM_BASE_DIRECTORY . "/source/OutputCleaner.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/Validator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/StringValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/CountryCodeValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/StateCodeValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/SetValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/EmailValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/MoneyValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/PhoneNumberValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/MultiPartPhoneNumberValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/ZipCodeValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/MultiPartSocialSecurityNumberValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Tracking/RequestTracker.php";
require_once WPAM_BASE_DIRECTORY . "/source/Tracking/UniqueIdGenerator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Tracking/TrackingLinkBuilder.php";
require_once WPAM_BASE_DIRECTORY . "/source/TermsCompiler.php";
require_once WPAM_BASE_DIRECTORY . "/source/MessageHelper.php";
require_once WPAM_BASE_DIRECTORY . "/source/MoneyHelper.php";
require_once WPAM_BASE_DIRECTORY . "/source/PayPal/Service.php";
require_once WPAM_BASE_DIRECTORY . "/source/Util/JsonHandler.php";
require_once WPAM_BASE_DIRECTORY . "/source/display_functions.php";
require_once WPAM_BASE_DIRECTORY . "/source/Util/DebugLogger.php";
require_once WPAM_BASE_DIRECTORY . "/utility-functions.php";
require_once WPAM_BASE_DIRECTORY . "/classes/PluginsLoadedTasks.php";
require_once WPAM_BASE_DIRECTORY . "/classes/CommissionTracking.php";
require_once WPAM_BASE_DIRECTORY . "/classes/ClickTracking.php";

class WPAM_Plugin {

    //these are only used as an index and for initial slug naming, users can change it
    const PAGE_NAME_HOME = 'affiliate-home';
    const PAGE_NAME_REGISTER = 'affiliate-register';
    const PAGE_NAME_LOGIN = 'affiliate-login';
    const EXT_JQUERY_UI_VER = '1.8.13';

    private $adminPages = array();
    private $publicPages = array();
    private $affiliateHomePage = null;
    private $affiliateRegisterPage = null;
    private $affiliateLoginPage = null;
    private static $PUBLIC_PAGE_IDS = NULL;
    private static $ICON_URL = NULL;
    private $locale;
    private $setloc;

    public function __construct() {

        $this->define_constants();

        self::$ICON_URL = WPAM_URL . '/images/icon_cash.png';
        $this->adminPages = array(
            new WPAM_Pages_Admin_MyAffiliatesPage(
                    'wpam-affiliates', __('Affiliate Management', 'affiliates-manager'), __('Affiliates', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, array(
                new WPAM_Pages_Admin_MyAffiliatesPage(
                        "wpam-affiliates", __('Affiliates', 'affiliates-manager'), __('My Affiliates', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap
                ),
                new WPAM_Pages_Admin_NewAffiliatePage(
                        "wpam-newaffiliate", __('New Affiliate', 'affiliates-manager'), __('New Affiliate', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap
                ),
                new WPAM_Pages_Admin_MyCreativesPage(
                        "wpam-creatives", __('Creatives', 'affiliates-manager'), __('My Creatives', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap
                ),
                new WPAM_Pages_Admin_PaypalPaymentsPage(
                        "wpam-payments", __('PayPal Mass Pay', 'affiliates-manager'), __('PayPal Mass Pay', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap
                )
                    )
            )
        );

        $this->affiliateHomePage = new WPAM_Pages_AffiliatesHome(self::PAGE_NAME_HOME, __('Store Affiliates', 'affiliates-manager'));
        $this->affiliateRegisterPage = new WPAM_Pages_AffiliatesRegister(self::PAGE_NAME_REGISTER, __('Register', 'affiliates-manager'), $this->affiliateHomePage);
        $this->affiliateLoginPage = new WPAM_Pages_AffiliatesLogin(self::PAGE_NAME_LOGIN, __('Affiliate Login', 'affiliates-manager'), $this->affiliateHomePage);
        $this->publicPages = array(
            self::PAGE_NAME_HOME => $this->affiliateHomePage,
            self::PAGE_NAME_REGISTER => $this->affiliateRegisterPage,
            self::PAGE_NAME_LOGIN => $this->affiliateLoginPage
        );
        //shortcodes
        add_shortcode('wpam_custom_input', array($this, 'add_custom_input'));
        //
        add_action('plugins_loaded', array($this, 'onPluginsLoaded'));

        //set up base actions
        add_action('init', array($this, 'onInit'));
        
        add_action('wp_enqueue_scripts', array($this, 'load_shortcode_specific_scripts'));

        add_action('wp_head', array($this, 'handle_wp_head_hook'));

        //actions & filters
        add_action('template_redirect', array($this, 'onTemplateRedirect'));
        add_action('admin_menu', array($this, 'onAdminMenu'));
        add_action('current_screen', array($this, 'onCurrentScreen'));

        add_action('wp_ajax_wpam-ajax_request', array($this, 'onAjaxRequest'));

        add_filter('pre_user_email', array($this, 'filterUserEmail'));

        add_action('profile_update', array($this, 'update_affiliate_email'), 10, 2);
        //set the locale for money format & paypal
        /*
          $this->locale = WPAM_LOCALE_OVERRIDE ? WPAM_LOCALE_OVERRIDE : get_locale();
          $this->setloc = $this->setMonetaryLocale( $this->locale );
          //loading provided locale didn't work, choose default
          if ( ! $this->setloc && setlocale( LC_MONETARY, 0 ) == 'C')
          setlocale( LC_MONETARY, '' );
         */
        add_action('admin_notices', array($this, 'showAdminMessages'));

        if (!is_admin()) {
            add_filter('widget_text', 'do_shortcode');
        }

        add_shortcode('AffiliatesRegister', array($this->publicPages[self::PAGE_NAME_REGISTER], 'doShortcode'));
        add_shortcode('AffiliatesHome', array($this->publicPages[self::PAGE_NAME_HOME], 'doShortcode'));
        add_shortcode('AffiliatesLogin', array($this, 'doLoginShortcode'));
        add_action('save_post', array($this, 'onSavePage'), 10, 2);

        //handle CSV download
        add_action('admin_init', array($this, 'handle_csv_download'));

        /*         * * General integration hook handler ** */
        add_action('wpam_process_affiliate_commission', array('WPAM_Commission_Tracking', 'handle_commission_tracking_hook'));

        /*         * * Start integration handler hooks ** */
        //Getshopped/WP-eCommerce
        add_action('wpsc_transaction_result_cart_item', array($this, 'onWpscCheckout'));

        //Woocommerce
        add_action('woocommerce_checkout_update_order_meta', array($this, 'WooCheckoutUpdateOrderMeta'), 10, 2);
        add_action('woocommerce_order_status_completed', array($this, 'WooCommerceProcessTransaction')); //Executes when a status changes to completed
        add_action('woocommerce_order_status_processing', array($this, 'WooCommerceProcessTransaction')); //Executes when a status changes to processing
        add_action('woocommerce_checkout_order_processed', array($this, 'WooCommerceProcessTransaction'));
        add_action('woocommerce_order_status_refunded', array($this, 'WooCommerceRefundTransaction'));  //Executes when a status changes to refunded
        add_action('woocommerce_order_status_cancelled', array($this, 'WooCommerceRefundTransaction'));  //Executes when a status changes to cancelled
        //Exchange integration
        add_filter('it_exchange_add_transaction', array($this, 'onExchangeCheckout'), 10, 7);

        //simple cart integration
        add_filter('wpspc_cart_custom_field_value', array($this, 'wpspcAddCustomValue'));
        add_action('wpspc_paypal_ipn_processed', array($this, 'wpspcProcessTransaction'));

        //EDD integration
        add_filter('edd_payment_meta', array($this, 'edd_store_custom_fields'));
        add_action('edd_complete_purchase', array($this, 'edd_on_complete_purchase'));

        //Jigoshop integration
        add_action('jigoshop_new_order', array($this, 'jigoshopNewOrder'));
        /*         * * End integration hooks ** */
    }

    public function define_constants() {
        global $wpdb;
        //DB Table names
        define('WPAM_AFFILIATES_TBL', $wpdb->prefix . 'wpam_affiliates');
        define('WPAM_CREATIVES_TBL', $wpdb->prefix . 'wpam_creatives');
        define('WPAM_TRACKING_TOKENS_TBL', $wpdb->prefix . 'wpam_tracking_tokens');
        define('WPAM_EVENTS_TBL', $wpdb->prefix . 'wpam_events');
        define('WPAM_ACTIONS_TBL', $wpdb->prefix . 'wpam_actions');
        define('WPAM_TRANSACTIONS_TBL', $wpdb->prefix . 'wpam_transactions');
        define('WPAM_MESSAGES_TBL', $wpdb->prefix . 'wpam_messages');
        define('WPAM_TRACKING_TOKENS_PURCHASE_LOGS_TBL', $wpdb->prefix . 'wpam_tracking_tokens_purchase_logs');
        define('WPAM_AFFILIATES_FIELDS_TBL', $wpdb->prefix . 'wpam_affiliates_fields');
        define('WPAM_PAYPAL_LOGS_TBL', $wpdb->prefix . 'wpam_paypal_logs');
        define('WPAM_IMPRESSIONS_TBL', $wpdb->prefix . 'wpam_impressions');
    }

    public function onActivation() {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            //This is a WordPress multi-site install.
            //Now, we need to check if it is a network activation - if so, run the activation function for each blog id.
            if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                //Wordpress network activted (applies to all sites in this install)
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->run_installer();
                }
                switch_to_blog($old_blog);
                return;
            }
        }

        //WordPress individual-site activation (can be single-site or multi-site install). Not a newwork active.
        $this->run_installer();
    }

    public function run_installer() {
        global $wpdb;
        $this->initCaps();

        $options = new WPAM_Options();
        $options->initOptions();

        $dbInstaller = new WPAM_Data_DatabaseInstaller($wpdb);
        $dbInstaller->doDbInstall();
        $dbInstaller->doInstallPages($this->publicPages);
        $dbInstaller->doFreshInstallDbDefaultData();
    }

    //remove 'old' style capabilities and replace with 'new'
    private function initCaps() {
        //leave commented until http://core.trac.wordpress.org/ticket/16617 is fixed and released
        //$roleMgr = new WP_Roles();
        //$roleMgr->add_cap('administrator', WPAM_PluginConfig::$AdminCap, true);
        $role = get_role('administrator');
        $role->add_cap(WPAM_PluginConfig::$AdminCap);

        // create affiliate role in WP with subscriber capabilities
        $sub = get_role('subscriber');
        add_role('affiliate', 'Affiliate', $sub->capabilities);
    }

    private function setMonetaryLocale($locale) {
        $is_set = setlocale(LC_MONETARY, $locale, $locale . ' ISO-8859-1', $locale . '.iso88591', $locale . '.UTF-8', $locale . '.UTF8', $locale . '.utf8'
        );

        return $is_set;
    }

    public function onPluginsLoaded() {
        new WPAM_Plugins_Loaded_Tasks();
    }

    public function onInit() {
        
        $this->do_init_task();
        $this->do_page_upgrade_task();
        /*
          try	{
          if ( isset( $_GET[WPAM_PluginConfig::$RefKey] ) ) {
          $requestTracker = new WPAM_Tracking_RequestTracker();
          $query_args = $_GET;
          $requestTracker->handleIncomingReferral($query_args);
          }
          } catch (Exception $e) {
          wp_die("WPAM FAILED: " . $e->getMessage());
          }
         */
        //new affiliate tracking code
        WPAM_Click_Tracking::record_click();
    }
    
    public function do_init_task(){
        if(is_admin()){
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'wpam-creatives') { //affiliates manager creatives page
                    wp_enqueue_media();
                }
            }
        }
    }

    public function load_shortcode_specific_scripts() {
        //Use this function to load JS and CSS file that should only be loaded if the shortcode is present in the page
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'AffiliatesLogin')) {
            wp_enqueue_style('wpamloginstyle', WPAM_URL . '/style/wpam-login-styles.css');
        }
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'AffiliatesHome')) {
            wp_enqueue_style('wpampurestyle', WPAM_URL . '/style/pure-styles.css');
        }
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'AffiliatesRegister')) {
            wp_enqueue_style('wpampurestyle', WPAM_URL . '/style/pure-styles.css');
        }
    }

    public function handle_wp_head_hook() {
        $debug_marker = "<!-- Affiliates Manager plugin v" . WPAM_VERSION . " - https://wpaffiliatemanager.com/ -->";
        echo "\n${debug_marker}\n";
    }

    public function do_page_upgrade_task() { //doing page upgrade task on init since get_permalink() doesn't work on plugins_loaded
        //version comparison not possible since the version is automatically updated earlier in the doDbInstall function on plugins_loaded
        global $wpdb;
        $dbInstaller = new WPAM_Data_DatabaseInstaller($wpdb);
        $dbInstaller->doInstallPages($this->publicPages);
    }

    public function doLoginShortcode() {
        $home_page_id = get_option(WPAM_PluginConfig::$HomePageId);
        $home_page_url = get_permalink($home_page_id);

        if (is_user_logged_in()) {
            global $current_user;
            wp_get_current_user();
            $logout_url = wp_logout_url($home_page_url);
            $output = '<div class="wpam-logged-in">';
            $output .= '<p>' . __('You are currently logged in', 'affiliates-manager') . '</p>';
            $output .= '<div class="wpam-logged-in-gravatar"><img src="//www.gravatar.com/avatar/' . md5(trim(strtolower($current_user->user_email))) . '?s=64" /></div>';
            $output .= '<div class="wpam-logged-in-username">' . __('Username', 'affiliates-manager') . ': ' . $current_user->user_login . "</div>";
            $output .= '<div class="wpam-logged-in-email">' . __('Email', 'affiliates-manager') . ': ' . $current_user->user_email . "</div>";
            $output .= '<div class="wpam-logged-in-logout-link"><a href="' . $logout_url . '">' . __('Log out', 'affiliates-manager') . '</a></div>';
            $output .= '</div>';
            return $output;
        } else {
            $args = array(
                'echo' => false,
                'redirect' => $home_page_url,
                'remember' => true,
                'label_username' => __('Email Address', 'affiliates-manager')
            );
            $lost_password_link = '<a href="' . wp_lostpassword_url() . '" title="' . __('Password Lost and Found', 'affiliates-manager') . '">' . __('Lost your password?', 'affiliates-manager') . '</a>';
            $form_output = '<div class="wpam-login-form">';
            $form_output .= wp_login_form($args);
            $form_output .= $lost_password_link;
            $form_output .= '</div>';
            return $form_output;
        }
    }

    public function add_custom_input() {
        $wpam_id_var = '';
        if (isset($_COOKIE['wpam_id']) && !empty($_COOKIE['wpam_id'])) {
            $wpam_id = $_COOKIE['wpam_id'];
            $wpam_id_var = 'wpam_id=' . $wpam_id;
        }
        $custom_var = apply_filters('wpam_custom_input', $wpam_id_var);
        $custom_input = '<input type="hidden" name="custom" value="' . $custom_var . '" />';
        return $custom_input;
    }

    public function onCurrentScreen($screen) {
        //#64 only show this libary on the pages that need it (ones that use jquery-ui-tabs)
        if (strpos($screen->id, 'wpam') !== false) {
            wp_register_style('wpam_style', WPAM_URL . "/style/style.css");
            wp_enqueue_style('wpam_style');

            wp_enqueue_script('jquery-ui-datepicker');

            //used for persistent tabs

            wp_enqueue_script('jquery-ui-tabs');

            $this->enqueueDialog();
            wp_register_script('wpam_contact_info', WPAM_URL . '/js/contact_info.js', array('jquery-ui-dialog'));
            wp_register_script('wpam_money_format', WPAM_URL . '/js/money_format.js');

            wp_register_style('wpam_jquery_ui_theme', WPAM_URL . '/style/jquery-ui/smoothness/jquery-ui.css');
            wp_enqueue_style('wpam_jquery_ui_theme');
        }

        add_thickbox();
    }

    public function becomeAffiliate() {
        echo '<div id="aff_div" class="wrap">';
        echo '<div id="icon-users" class="icon32"></div><h2>' . __('Become an affiliate', 'affiliates-manager') . '</h2>';
        echo '<p>' . __('Are you interested in earning money by directing visitors to our site?', 'affiliates-manager') . '</p>';
        //@TODO check the rules on spaces for l10n
        echo '<p><a href="' . $this->affiliateRegisterPage->getLink() . '">' . __('Sign up', 'affiliates-manager') . '</a>' . __(' to become an affiliate today!', 'affiliates-manager');
        echo '</p></div></div>';
    }

    public function wpspcAddCustomValue($custom_field_val) {
        if (isset($_COOKIE['wpam_id'])) {
            $name = 'wpam_tracking';
            $value = $_COOKIE['wpam_id'];
            $new_val = $name . '=' . $value;
            $custom_field_val = $custom_field_val . '&' . $new_val;
            WPAM_Logger::log_debug('Simple WP Cart Integration - Adding custom field value. New value: ' . $custom_field_val);
        } else if (isset($_COOKIE[WPAM_PluginConfig::$RefKey])) {
            $name = 'wpam_tracking';
            $value = $_COOKIE[WPAM_PluginConfig::$RefKey];
            $new_val = $name . '=' . $value;
            $custom_field_val = $custom_field_val . '&' . $new_val;
            WPAM_Logger::log_debug('Simple WP Cart Integration - Adding custom field value. New value: ' . $custom_field_val);
        }
        return $custom_field_val;
    }

    public function wpspcProcessTransaction($ipn_data) {
        $custom_data = $ipn_data['custom'];
        WPAM_Logger::log_debug('Simple WP Cart Integration - IPN processed hook fired. Custom field value: ' . $custom_data);
        $custom_values = array();
        parse_str($custom_data, $custom_values);
        if (!isset($custom_values['wpam_tracking']) || empty($custom_values['wpam_tracking'])) {
            if (isset($_COOKIE['wpam_id']) && !empty($_COOKIE['wpam_id'])) {    //useful for onsite option such as smart checkout
                $custom_values['wpam_tracking'] = $_COOKIE['wpam_id'];
            }
        }
        if (isset($custom_values['wpam_tracking']) && !empty($custom_values['wpam_tracking'])) {
            $tracking_value = $custom_values['wpam_tracking'];
            WPAM_Logger::log_debug('Simple WP Cart Integration - Tracking data present. Need to track affiliate commission. Tracking value: ' . $tracking_value);

            $purchaseLogId = $ipn_data['txn_id'];
            $purchaseAmount = $ipn_data['mc_gross']; //TODO - later calculate sub-total only
            $buyer_email = $ipn_data['payer_email'];
            $strRefKey = $tracking_value;
            $requestTracker = new WPAM_Tracking_RequestTracker();
            $requestTracker->handleCheckoutWithRefKey($purchaseLogId, $purchaseAmount, $strRefKey, $buyer_email);
            WPAM_Logger::log_debug('Simple WP Cart Integration - Commission tracked for transaction ID: ' . $purchaseLogId . ', Purchase amt: ' . $purchaseAmount . ', Buyer Email: ' . $buyer_email);
        }
    }

    public function onWpscCheckout(array $purchaseInfo) {
        if ($purchaseInfo['purchase_log']['processed'] >= 2) {
            $purchaseAmount = $purchaseInfo['purchase_log']['totalprice'] - $purchaseInfo['purchase_log']['base_shipping'];
            $purchaseLogId = $purchaseInfo['purchase_log']['id'];
            $buyer_email = wpsc_get_buyers_email($purchaseLogId);
            $requestTracker = new WPAM_Tracking_RequestTracker();
            $requestTracker->handleCheckout($purchaseLogId, $purchaseAmount, $buyer_email);
        }
    }

    public function WooCheckoutUpdateOrderMeta($order_id, $posted) {
        $wpam_refkey = "";
        if (isset($_COOKIE['wpam_id'])) {
            $wpam_refkey = $_COOKIE['wpam_id'];
        } else if (isset($_COOKIE[WPAM_PluginConfig::$RefKey])) {   //remove this block when we don't expect wpam_refkey cookie anymore
            $wpam_refkey = $_COOKIE[WPAM_PluginConfig::$RefKey];
        }

        if (!empty($wpam_refkey)) {//Save the wpam_refkey in the order meta
            if (is_numeric($wpam_refkey)) {  //wpam_id cookie is found and contains affiliate ID.
                update_post_meta($order_id, '_wpam_id', $wpam_refkey);
                $wpam_refkey = get_post_meta($order_id, '_wpam_id', true);
                WPAM_Logger::log_debug("WooCommerce Integration - Saving wpam_id (" . $wpam_refkey . ") with order. Order ID: " . $order_id);
            } else { //remove this block when we don't expect wpam_refkey cookie anymore 
                update_post_meta($order_id, '_wpam_refkey', $wpam_refkey);
                $wpam_refkey = get_post_meta($order_id, '_wpam_refkey', true);
                WPAM_Logger::log_debug("WooCommerce Integration - Saving wpam_refkey (" . $wpam_refkey . ") with order. Order ID: " . $order_id);
            }
        }
    }

    public function WooCommerceProcessTransaction($order_id) {
        //affiliates manager code
        WPAM_Logger::log_debug('WooCommerce Integration - Order processed. Order ID: ' . $order_id);
        if (wpam_has_purchase_record($order_id)) {
            WPAM_Logger::log_debug('WooCommerce Integration - Affiliate commission for this transaction was awarded once. No need to process anything.');
            return;
        }
        WPAM_Logger::log_debug('WooCommerce Integration - Checking if affiliate commission needs to be awarded.');
        $order = new WC_Order($order_id);
        
        if(function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order, 'parent')){
            WPAM_Logger::log_debug("WooCommerce Integration - This notification is for a new subscription payment");
            WPAM_Logger::log_debug("The commission will be calculated via the recurring payemnt api call.", 2);
            return;
        }
        if(function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order, 'renewal')){
            WPAM_Logger::log_debug("WooCommerce Integration - This notification is for a recurring subscription payment");
            WPAM_Logger::log_debug("The commission will be calculated via the recurring payemnt api call.", 2);
            return;
        }
        if(function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order, 'resubscribe')){
            WPAM_Logger::log_debug("WooCommerce Integration - This notification is for a resubscription payment");
            WPAM_Logger::log_debug("The commission will be calculated via the recurring payemnt api call.", 2);
            return;
        }
        if(function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order, 'switch')){
            WPAM_Logger::log_debug("WooCommerce Integration - This notification is for a subscription switch");
            WPAM_Logger::log_debug("The commission will be calculated via the recurring payemnt api call.", 2);
            return;
        }

        $order_status = $order->get_status();
        WPAM_Logger::log_debug("WooCommerce Integration - Order status: " . $order_status);
        if (strtolower($order_status) != "completed" && strtolower($order_status) != "processing") {
            WPAM_Logger::log_debug("WooCommerce Integration - Order status for this transaction is not in a 'completed' or 'processing' state. Commission will not be awarded at this stage.", 2);
            WPAM_Logger::log_debug("WooCommerce Integration - Commission for this transaction will be awarded when you set the order status to completed or processing.", 2);
            return;
        }

        $total = $order->get_total();
        $shipping = $order->get_total_shipping();
        $tax = $order->get_total_tax();
        $fees = wpam_get_total_woocommerce_order_fees($order);
        WPAM_Logger::log_debug('WooCommerce Integration - Total amount: ' . $total . ', Total shipping: ' . $shipping . ', Total tax: ' . $tax . ', Fees: '. $fees);
        $purchaseAmount = $total - $shipping - $tax - $fees;
        $buyer_email = $order->get_billing_email();

        $wpam_refkey = get_post_meta($order_id, '_wpam_refkey', true);
        $wpam_id = get_post_meta($order_id, '_wpam_id', true);
        if (!empty($wpam_id)) {
            $wpam_refkey = $wpam_id;
        }
        $wpam_refkey = apply_filters('wpam_woo_override_refkey', $wpam_refkey, $order);
        if (empty($wpam_refkey)) {
            WPAM_Logger::log_debug("WooCommerce Integration - could not get wpam_id/wpam_refkey from cookie. This is not an affiliate sale", 4);
            return;
        }

        $requestTracker = new WPAM_Tracking_RequestTracker();
        WPAM_Logger::log_debug('WooCommerce Integration - awarding commission for order ID: ' . $order_id . ', Purchase amount: ' . $purchaseAmount . ', Affiliate ID: ' . $wpam_refkey . ', Buyer Email: ' . $buyer_email);
        $requestTracker->handleCheckoutWithRefKey($order_id, $purchaseAmount, $wpam_refkey, $buyer_email);
    }

    public function WooCommerceRefundTransaction($order_id) {
        WPAM_Logger::log_debug('WooCommerce integration - order refunded. Order ID: ' . $order_id);
        //$order = new WC_Order($order_id);
        $txn_id = $order_id;
        WPAM_Commission_Tracking::refund_commission($txn_id);
    }

    public function jigoshopNewOrder($order_id) {
        $order = new jigoshop_order($order_id);

        $total = floatval($order->order_subtotal);
        if ($order->order_discount) {
            $total = $total - floatval($order->order_discount);
        }
        if ($total < 0) {
            $total = 0;
        }
        $buyer_email = '';
        WPAM_Logger::log_debug('JigoShop Integration - new order received. Order ID: ' . order_id . '. Purchase amt: ' . $total);

        $requestTracker = new WPAM_Tracking_RequestTracker();
        $requestTracker->handleCheckout($order_id, $total, $buyer_email);
    }

    public function edd_store_custom_fields($payment_meta) {
        WPAM_Logger::log_debug('Easy Digital Downlaods Integration - payment_meta filter triggered');
        if (isset($_COOKIE['wpam_id'])) {
            $strRefKey = $_COOKIE['wpam_id'];
            $payment_meta['wpam_refkey'] = $strRefKey;
            WPAM_Logger::log_debug('Easy Digital Downlaods Integration - refkey: ' . $strRefKey);
        } else if (isset($_COOKIE[WPAM_PluginConfig::$RefKey])) {
            $strRefKey = $_COOKIE[WPAM_PluginConfig::$RefKey];
            $payment_meta['wpam_refkey'] = $strRefKey;
            WPAM_Logger::log_debug('Easy Digital Downlaods Integration - refkey: ' . $strRefKey);
        }
        return $payment_meta;
    }

    public function edd_on_complete_purchase($payment_id) {
        WPAM_Logger::log_debug('Easy Digital Downlaods Integration - complete purchase hook triggered for Order ID: ' . $payment_id . '. Checking if affiliate commission needs to be awarded.');
        $payment_meta = edd_get_payment_meta($payment_id);
        $strRefKey = "";
        if (isset($payment_meta['wpam_refkey']) && !empty($payment_meta['wpam_refkey'])) {
            $strRefKey = $payment_meta['wpam_refkey'];
            WPAM_Logger::log_debug('Easy Digital Downlaods Integration - This purchase was referred by an affiliate, refkey: ' . $strRefKey);
        } else {
            WPAM_Logger::log_debug('Easy Digital Downlaods Integration - refkey not found in the payment_meta. This purchase was not referred by an affiliate');
            return;
        }
        $purchaseAmount = edd_get_payment_amount($payment_id);
        $buyer_email = $payment_meta['email'];
        WPAM_Logger::log_debug('Easy Digital Downlaods Integration - Awarding commission for Order ID: ' . $payment_id . '. Purchase amt: ' . $purchaseAmount . ', Buyer Email: ' . $buyer_email);
        $requestTracker = new WPAM_Tracking_RequestTracker();
        $requestTracker->handleCheckoutWithRefKey($payment_id, $purchaseAmount, $strRefKey, $buyer_email);
    }

    public function onExchangeCheckout($transaction_id, $method, $method_id, $status, $customer_id, $cart_object, $args) {
        $purchaseAmount = it_exchange_get_transaction_subtotal($transaction_id, false);
        $buyer_email = '';
        $requestTracker = new WPAM_Tracking_RequestTracker();
        $requestTracker->handleCheckout($transaction_id, $purchaseAmount, $buyer_email);

        return $transaction_id;
    }

    public function onAdminMenu() {
        //let the hackery begin! #63
        global $menu;
        $menu_parent_slug = 'wpam-affiliates';

        //show this to affiliates, but not admins / affiliate managers
        if (!current_user_can(WPAM_PluginConfig::$AdminCap) && current_user_can(WPAM_PluginConfig::$AffiliateCap)) {

            //$icon_url = esc_url( self::$ICON_URL );
            //I won't necessarily guarantee this will work in the future
            $new_menu = array(
                __('Affiliates', 'affiliates-manager'),
                'read',
                $this->affiliateHomePage->getLink(),
                null,
                'menu-top',
                null,
                'dashicons-groups',
            );
            $menu[] = $new_menu;
        }

        //show to non-affiliates
        if (!current_user_can(WPAM_PluginConfig::$AffiliateCap) && !current_user_can(WPAM_PluginConfig::$AdminCap)) {
            add_menu_page(
                    __('Affiliates', 'affiliates-manager'), __('Be An Affiliate', 'affiliates-manager'), 'read', 'newaffiliate', array($this, 'becomeAffiliate'), 'dashicons-groups'
            );
        }

        //Add main affiliates menu object                                
        add_menu_page(__('Affiliate Management', 'affiliates-manager'), __('Affiliates', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-affiliates', array(), 'dashicons-groups', '25.3');

        $page = $this->adminPages[0];

        //Add the child pages
        $children = $page->getChildren();

        //Add my affiliates submenu page
        $childPage1 = $children[0];
        add_submenu_page($page->getId(), $childPage1->getName(), $childPage1->getMenuName(), $childPage1->getRequiredCap(), $childPage1->getId(), array($childPage1, "process"));

        //Add new affiliate submenu page
        $childPage2 = $children[1];
        add_submenu_page($page->getId(), $childPage2->getName(), $childPage2->getMenuName(), $childPage2->getRequiredCap(), $childPage2->getId(), array($childPage2, "process"));

        //Add my creatives submenu page
        $childPage3 = $children[2];
        add_submenu_page($page->getId(), $childPage3->getName(), $childPage3->getMenuName(), $childPage3->getRequiredCap(), $childPage3->getId(), array($childPage3, "process"));

        //Add paypal payments submenu page
        $childPage4 = $children[3];
        add_submenu_page($page->getId(), $childPage4->getName(), $childPage4->getMenuName(), $childPage4->getRequiredCap(), $childPage4->getId(), array($childPage4, "process"));

        //Add settings submenu page
        $settings_obj = new WPAM_Pages_Admin_SettingsPage();
        add_submenu_page($menu_parent_slug, __('Settings', 'affiliates-manager'), __('Settings', 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-settings', array($settings_obj, 'render_settings_page'));
        
        //Add admin functions submenu page
        include_once(WPAM_BASE_DIRECTORY . "/source/Admin-menu/wpam-admin-functions-menu.php");
        add_submenu_page($menu_parent_slug, __("Affiliates Manager Admin Functions", 'affiliates-manager'), __("Admin Functions", 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-admin-functions', 'wpam_display_admin_functions_menu');

        //Add manage payouts submenu page
        include_once(WPAM_BASE_DIRECTORY . "/source/Admin-menu/wpam-manage-payouts-menu.php");
        add_submenu_page($menu_parent_slug, __("Affiliates Manager Manage Payouts", 'affiliates-manager'), __("Manage Payouts", 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-manage-payouts', 'wpam_display_manage_payouts_menu');
        
        //Add clicks submenu page
        include_once(WPAM_BASE_DIRECTORY . "/source/Admin-menu/wpam-clicks-menu.php");
        add_submenu_page($menu_parent_slug, __("Affiliates Manager Click Tracking", 'affiliates-manager'), __("Click Tracking", 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-clicktracking', 'wpam_display_clicks_menu');

        //Add commission submenu page
        include_once(WPAM_BASE_DIRECTORY . "/source/Admin-menu/wpam-commission-menu.php");
        add_submenu_page($menu_parent_slug, __("Affiliates Manager Commission Data", 'affiliates-manager'), __("Commissions", 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-commission', 'wpam_display_commission_menu');

        //Add addons submenu page
        include_once(WPAM_BASE_DIRECTORY . "/source/Admin-menu/wpam-addons-menu.php");
        add_submenu_page($menu_parent_slug, __("Affiliates Manager Add-ons", 'affiliates-manager'), __("Add-ons", 'affiliates-manager'), WPAM_PluginConfig::$AdminCap, 'wpam-addons', 'wpam_display_addons_menu');

        //Hook for addons to create their menu
        do_action('wpam_after_main_admin_menu', $menu_parent_slug);
    }

    //for public pages
    public function onTemplateRedirect() {
        if (!is_array(self::$PUBLIC_PAGE_IDS)) {
            self::$PUBLIC_PAGE_IDS = array(
                $this->publicPages[WPAM_Plugin::PAGE_NAME_HOME]->getPageId(),
                $this->publicPages[WPAM_Plugin::PAGE_NAME_REGISTER]->getPageId());
        }

        //get the current page
        $page_id = NULL;
        $page = get_page($page_id);

        //register front-end scripts
        if (isset($page->ID) && in_array($page->ID, self::$PUBLIC_PAGE_IDS)) {
            //add jquery dialog + some style
            $this->enqueueDialog();
            wp_register_style('wpam_jquery_ui_theme', WPAM_URL . '/style/jquery-ui/smoothness/jquery-ui.css');
            wp_enqueue_style('wpam_jquery_ui_theme');
            wp_register_style('wpam_style', WPAM_URL . "/style/style.css");
            wp_enqueue_style('wpam_style');

            //#45 add a datepicker
            wp_enqueue_script('jquery-ui-datepicker');

            wp_register_script('wpam_contact_info', WPAM_URL . '/js/contact_info.js', array('jquery-ui-dialog'));
            wp_register_script('wpam_tnc', WPAM_URL . '/js/tnc.js', array('jquery-ui-dialog'));
            wp_register_script('wpam_payment_method', WPAM_URL . '/js/payment_method.js');
        }
    }

    /**
     * There's an upstream bug with JQuery UI Button that will probably be
     * fixed in JQuery UI 1.9, so we need to override the default WP one until
     * it's fixed and the fixed version is included in WP.
     * 
     * @see http://bugs.jqueryui.com/ticket/7680
     */
    private function enqueueDialog() {
        //things seem to be working OK with dialog/button as of WP 3.4, so we'll just use the included version

        wp_enqueue_script('jquery-ui-button');
        wp_enqueue_script('jquery-ui-dialog');
    }

    //#79 sync email when it's actually changed
    public function filterUserEmail($email) {
        $user = wp_get_current_user();
        $newEmail = get_option($user->ID . '_new_email');
        if (!empty($newEmail) && isset($_GET['newuseremail'])) {
            $db = new WPAM_Data_DataAccess();
            $affiliate = $db->getAffiliateRepository()->loadByUserId($user->ID);
            $affiliate->email = $email;
            $db->getAffiliateRepository()->update($affiliate);
        }
        return $email;
    }
    
    /* update the affiliate email when it's updated in WordPress */
    public function update_affiliate_email($user_id, $old_user_data) {
        global $wpdb;
        $table = WPAM_AFFILIATES_TBL;
        $user_data = get_user_by('id', $user_id);
        //WPAM_Logger::log_debug('profile_update hook fired. current email: '.$user_data->user_email.', old email: '.$old_user_data->user_email);
        if(isset($user_data->user_email) && !empty($user_data->user_email) && $user_data->user_email !== $old_user_data->user_email) {
            $wpdb->update($table, array('email' => $user_data->user_email), array('userId' => $user_id));
            //WPAM_Logger::log_debug('email updated');
        }
    }
    
    public function onSavePage($page_id, $page) {
        if ($page->post_type == 'page') {
            if (strpos($page->post_content, WPAM_PluginConfig::$ShortCodeHome) !== false) {
                update_option(WPAM_PluginConfig::$HomePageId, $page->ID);
            } elseif (strpos($page->post_content, WPAM_PluginConfig::$ShortCodeRegister) !== false) {
                update_option(WPAM_PluginConfig::$RegPageId, $page->ID);
            }
        }
    }

    public function showAdminMessages() {
        /* hide this error since the currency code and symbol comes from the general settings
          if ( empty( $this->setloc ) ){
          //don't bother showing this warning if they were trying to use 'en_US'
          if ( $this->locale == 'en_US' ) {
          return;
          }
          $code = WPAM_MoneyHelper::getCurrencyCode();
          $currency = WPAM_MoneyHelper::getDollarSign();

          echo '<div id="message" class="error">
          <p><strong>' . sprintf( __( 'WP Affiliate Manager was unable to load your currency from your WPLANG setting: %s', 'affiliates-manager' ), $this->locale ) . '<br/>' .
          sprintf( __( 'Your currency will be displayed as %s and PayPal payments will be paid in %s', 'affiliates-manager' ), $currency, $code ) . '</strong></p></div>';
          if ( WPAM_DEBUG ){
          echo "<!-- LC_MONETARY {$this->locale}, isset: ", var_export($this->setloc, true), PHP_EOL, var_export( localeconv(), true ), ' -->';
          }
          }
         */
    }

    public function onAjaxRequest() {
        //die(print_r($_REQUEST, true));
        $jsonHandler = new WPAM_Util_JsonHandler();
        $_REQUEST = wpam_sanitize_array($_REQUEST);
        try {
            switch ($_REQUEST['handler']) {
                case 'approveApplication':
                    $response = $jsonHandler->approveApplication($_REQUEST['affiliateId'], $_REQUEST['bountyType'], $_REQUEST['bountyAmount']);
                    break;
                case 'declineApplication':
                    $response = $jsonHandler->declineApplication($_REQUEST['affiliateId']);
                    break;
                case 'blockApplication':
                    $response = $jsonHandler->blockApplication($_REQUEST['affiliateId']);
                    break;
                case 'activateAffiliate':
                    $response = $jsonHandler->activateApplication($_REQUEST['affiliateId']);
                    break;
                case 'deactivateAffiliate':
                    $response = $jsonHandler->deactivateApplication($_REQUEST['affiliateId']);
                    break;
                case 'setCreativeStatus':
                    $response = $jsonHandler->setCreativeStatus($_REQUEST['creativeId'], $_REQUEST['status']);
                    break;
                case 'addTransaction':
                    $response = $jsonHandler->addTransaction($_REQUEST['affiliateId'], $_REQUEST['type'], $_REQUEST['amount'], $_REQUEST['description']);
                    break;
                case 'deleteCreative':
                    $response = $jsonHandler->deleteCreative($_REQUEST['creativeId']);
                    break;
                default: throw new Exception(__('Invalid JSON handler.', 'affiliates-manager'));
            }
        } catch (Exception $e) {
            $response = new JsonResponse(JsonResponse::STATUS_ERROR, $e->getMessage());
        }

        die(json_encode($response)); //required to return a proper result		
    }

    private function output_csv($items, $export_keys, $filename = 'data.csv') {
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");
        $output = fopen('php://output', 'w'); //open output stream
        fputcsv($output, $export_keys); //let's put column names first
        foreach ($items as $item) {
            unset($csv_line);
            foreach ($export_keys as $key => $value) {
                if (isset($item[$key])) {
                    $csv_line[] = $item[$key];
                }
            }
            fputcsv($output, $csv_line);
        }
    }

    public function handle_csv_download() {
        if (isset($_POST['wpam-export-affiliates-to-csv'])) {
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'wpam-export-affiliates-to-csv-nonce')) {
                die(_e('Nonce check failed for export My Affiliates to CSV!', 'affiliates-manager'));
            }
            include_once(WPAM_BASE_DIRECTORY . '/classes/ListAffiliatesTable.php');
            $affiliates = new WPAM_List_Affiliates_Table();
            $affiliates->prepare_items(true);
            $export_keys = array(
                'affiliateId' => 'Affiliate ID',
                'status' => 'Status',
                'balance' => 'Balance',
                'earnings' => 'Earnings',
                'firstName' => 'First Name',
                'lastName' => 'Last Name',
                'email' => 'Email',
                'companyName' => 'Company',
                'dateCreated' => 'Date Joined',
                'websiteUrl' => 'Website',
                'phoneNumber' => 'Phone',
            );
            $this->output_csv($affiliates->items, $export_keys, 'MyAffiliates.csv');
            die();
        }
    }

}
