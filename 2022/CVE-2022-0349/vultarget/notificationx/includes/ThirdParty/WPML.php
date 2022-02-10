<?php

namespace NotificationX\ThirdParty;

use NotificationX\Admin\Settings;
use NotificationX\Core\Helper;
use NotificationX\Core\PostType;
use NotificationX\Core\REST;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\GetInstance;
use WP_Error;
use WP_REST_Server;

class WPML {
    /**
     * Instance of WPML
     *
     * @var WPML
     */
    use GetInstance;

    protected $inclued_entry_key = [
        'name',
        'first_name',
        'last_name',
        // 'link', // link is automatically translated.
        'title',
        'city',
        'state',
        'country',
        'city_country',
    ];

    private $template = [
        'custom_first_param'  => "Custom First Parameter",
        'second_param'        => "Second Param",
        'custom_third_param'  => "Custom Third Param",
        'custom_fourth_param' => "Custom Fourth Parameter",
        'custom_fifth_param'  => "Custom Fifth Parameter",
        'custom_sixth_param'  => "Custom Sixth Parameter",
        'map_fourth_param'    => "Map Fourth Parameter",
        'ga_fourth_param'     => "Google Analytics Fourth Parameter",
        'ga_fifth_param'      => "Google Analytics Fifth Parameter",
        'review_fourth_param' => "Review Fourth Parameter",
    ];

    /**
     * Constructor.
     *
     */
    public function __construct() {
        add_action('wpml_st_loaded', [$this, 'st_loaded'], 10);
        // localize moment even without wpml;
        add_action('notificationx_scripts', [$this, 'localize_moment'], 10);
        // can't load moment locale in admin. it cause problem in date picker.
        // add_action('notificationx_admin_scripts', [$this, 'localize_moment'], 10);
    }

    /**
     * This method is reponsible for Admin Menu of
     * NotificationX
     *
     * @return void
     */
    public function st_loaded() {

        add_action('init', [$this, 'init'], 10);

        add_action('nx_saved_post', [$this, 'register_package'], 10, 3);
        add_action('nx_delete_post', [$this, 'delete_translation'], 10, 2);
        add_filter('nx_get_post', [$this, 'translate_values'], 10);

        add_filter('nx_rest_data', [$this, 'rest_data']);
        add_filter('nx_builder_configs', [$this, 'builder_configs']);
        add_filter('nx_check_location', [$this, 'check_location'], 10, 3);
        add_action( 'wp_ajax_nx-translate', [$this, 'translate'] );

    }

    public function init(){
        // load translated version of the settings.
        Settings::get_instance()->_load();

    }

    public function get_meta($post){
        $meta = [];
        $meta['title'] = ['Title', 'LINE'];
        if($post['source'] == 'press_bar'){
            if(empty($post['elementor_id'])){
                $meta['press_content']          = ['Notification Bar Content', 'VISUAL'];
                $meta['button_text']            = ['Button Text', 'LINE'];
                $meta['button_url']             = ['Button URL', 'LINE'];
                $meta['countdown_expired_text'] = ['Countdown Expired Text', 'LINE'];
                $meta['countdown_text']         = ['Countdown Text', 'LINE'];
            }
        }
        else{
            if($post['link_type']){
                $meta['custom_url'] = ['Custom URL', 'LINE'];
            }
            if($post['template_adv']){
                $meta['advanced_template'] = ['Advance Template', 'VISUAL'];
            }
            if(($post['source'] == 'edd' || $post['source'] == 'woocommerce') && $post['combine_multiorder']){
                $meta['combine_multiorder_text'] = ['Combine Multi Order Text', 'LINE'];
            }
        }
        return $meta;
    }

    public function localize_moment($nx_ids = null, $return_url = false){
        $locale_url = '';
        if($locale = apply_filters( 'wpml_current_language', NULL )){
            $locale      = strtolower(str_replace('_', '-', $locale));
            $locale_path = NOTIFICATIONX_ASSETS_PATH . "public/locale/$locale.js";
            if(file_exists($locale_path)){
                $locale_url  = NOTIFICATIONX_ASSETS . "public/locale/$locale.js";
            }
        }
        else{
            $locale      = strtolower(str_replace('_', '-', get_locale()));
            $locale_path = NOTIFICATIONX_ASSETS_PATH . "public/locale/$locale.js";
            $locale_arr  = explode('-', $locale);
            if(file_exists($locale_path)){
                $locale_url  = NOTIFICATIONX_ASSETS . "public/locale/$locale.js";
            }
            else if(!empty($locale_arr[1])){
                $locale_path = NOTIFICATIONX_ASSETS_PATH . "public/locale/{$locale_arr[0]}.js";
                if(file_exists($locale_path)){
                    $locale_url  = NOTIFICATIONX_ASSETS . "public/locale/{$locale_arr[0]}.js";
                }
            }
        }
        if($locale_url && !$return_url){
            wp_enqueue_script( 'nx-moment-locale', $locale_url, ['moment']);
        }
        return $locale_url;
    }

    public function generate_package($post, $nx_id){
        return array(
            'kind'      => 'NotificationX',
            'name'      => "$nx_id",
            'title'     => "{$post['title']}", // ($nx_id)
            'edit_link' => PostType::get_instance()->get_edit_link($nx_id),
            'view_link' => PostType::get_instance()->get_edit_link($nx_id),
        );
    }

    public function register_package($post, $data, $nx_id){
        $data = array_merge($post, $data);
        if(empty($data['is_translated'])){
            return;
        }

        $package = $this->generate_package($data, $nx_id);

        foreach ($this->get_meta($data) as $key => $param) {
            if(!empty($data[$key])){
                $title = $param[0];
                $type  = $param[1];
                do_action('wpml_register_string', $data[$key], $key, $package, $title, $type);
            }
        }

        if($post['source'] != 'google'){
            unset($this->template['ga_fourth_param']);
            unset($this->template['ga_fifth_param']);
        }
        if(strpos($data['themes'], 'maps_theme') === false){
            unset($this->template['map_fourth_param']);
        }

        if($post['source'] != 'press_bar'){
            // @todo when theme template have all the params.
            // $source = $data['source'];
            // $theme = $data['themes'];
            // $ext = ExtensionFactory::get_instance()->get($source);
            // $themes = $ext ? $ext->get_themes() : null;
            // if(!empty($themes[$theme]['template'])){
            // }

            foreach ($this->template as $key => $param) {
                if(!empty($data['notification-template'][$key])){
                    do_action('wpml_register_string', $data['notification-template'][$key], $key, $package, $param, 'LINE');
                }
            }
        }

    }

    public function translate_values($post){
        if(empty($_GET['frontend']) && !did_action( "nx_inline" )){
            // checking if request came from frontend.
            return $post;
        }

        $package = $this->generate_package($post, $post['nx_id']);

        foreach ($this->get_meta($post) as $key => $param) {
            if(!empty($post[$key])){
                $post[$key] = apply_filters( 'wpml_translate_string', $post[$key], $key, $package );
            }
        }

        if($post['source'] != 'press_bar'){
            foreach ($this->template as $key => $param) {
                if(!empty($post['notification-template'][$key])){
                    $post['notification-template'][$key] = apply_filters( 'wpml_translate_string', $post['notification-template'][$key], $key, $package );
                }
            }
        }
        return $post;
    }

    public function delete_translation($nx_id, $post){
        $package = $this->generate_package($post, $nx_id);
        do_action( 'wpml_delete_package', $package['name'], $package['kind'] );
    }

    /**
     * We are only translating when user click translate button.
     *
     * @param [type] $request
     * @return void
     */
    public function translate($request){
        if(!empty($_GET['id'])){
            $nx_id = sanitize_text_field( $_GET['id'] );
            $post = PostType::get_instance()->get_post($nx_id);
            if($post['source'] == 'press_bar' && !empty($post['elementor_id'])){
		        $cookie = new \WPML_Cookie();
				$cookie_data = filter_var( http_build_query( ['type' => 'nx_bar'] ), FILTER_SANITIZE_URL );
				$cookie->set_cookie( 'wp-translation_dashboard_filter', $cookie_data, time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

                wp_redirect(admin_url("admin.php?page=wpml-translation-management/menu/main.php&sm=dashboard"));
                die;
            }
            else if($post){
                $post['is_translated'] = true;
                PostType::get_instance()->update_post([
                    'data' => $post,
                ], $nx_id);

                $this->register_package($post, [], $nx_id);
                wp_redirect(admin_url("admin.php?page=wpml-string-translation/menu/string-translation.php&context=notificationx-$nx_id"));
                die;
            }
        }
		return new WP_Error();
    }
    public function can_translate( $request ) {
        return current_user_can('wpml_manage_string_translation');
    }

    /**
     * Frontend append lang param.
     *
     * @param [type] $rest
     * @return void
     */
    public function rest_data($rest){
        $my_default_lang = apply_filters('wpml_default_language', NULL );
        $my_current_lang = apply_filters( 'wpml_current_language', NULL );
        if($my_default_lang != $my_current_lang){
            $rest['lang'] = $my_current_lang;
        }
        return $rest;
    }

    /**
     * Backend check if translation is enabled.
     *
     * @param [type] $rest
     * @return void
     */
    public function builder_configs($tabs){
        $tabs['can_translate'] = true;
        return $tabs;
    }

    public function check_location($check_location, $settings, $custom_ids){
        if(!$check_location && $custom_ids){
            global $post;
            if( empty( $ids ) || empty($post) ) {
                return false;
            }
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
            if( is_post_type_archive( 'product' ) ) {
                if( in_array( get_option( 'woocommerce_shop_page_id' ), $ids ) ) {
                    return true;
                }
            }
            return in_array( $post->ID, $ids ) ? true : false;
        }
        return $check_location;
    }
}