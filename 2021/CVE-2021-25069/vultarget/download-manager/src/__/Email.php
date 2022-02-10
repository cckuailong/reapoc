<?php
/**
 * Email Handler Class for WordPress Download Manager Pro
 * Since: v4.6.0
 * Author: Shahjada
 * Version: 2.0.2
 */
namespace WPDM\__;

class Email {

    var $email_hooks = [
        'wpdm_before_email_download_link' => [
            'title' => 'Before email download link',
            'params' => 2
        ],
        'wpdm_onstart_download' => [
            'title' => 'Just before download starts',
            'params' => 1
        ],
        'create_package_frontend' => [
            'title' => 'Create new package from front-end',
            'params' => 2
        ],
        'edit_package_frontend' => [
            'title' => 'Update a package from frontend',
            'params' => 2
        ],
        'wpdm_after_checkout' => [
            'title' => 'After a successful checkout',
            'params' => 2
        ],
    ];

    public $templateDir;

    function __construct() {
        $this->templateDir = __DIR__.'/views/email-templates/';
    }

    public static function templates() {
        $admin_email = get_option( 'admin_email' );
        $sitename    = get_option( "blogname" );
        $templates   = array(
            'default' => array(
                'label' => __( "General Email Template" , "download-manager" ),
                'for' => 'varies',
                'default' => array( 'subject' => '[#subject#]',
                    'from_name' => get_option('blogname'),
                    'from_email' => $admin_email,
                    'message' => '[#message#]</b><br/><br/>Best Regards,<br/>Support Team<br/><b><a href="[#homeurl#]">[#sitename#]</a></b>'
                )
            ),
            'user-signup'          => array(
                'label'   => __( "User Signup Notification" , "download-manager" ),
                'for'     => 'customer',
                'default' => array(
                    'subject'    => sprintf( __( "Welcome to %s" , "download-manager" ), $sitename ),
                    'from_name'  => get_option( 'blogname' ),
                    'from_email' => $admin_email,
                    'message'    => '<h3>Welcome to [#sitename#]</h3>Hello [#first_name#],<br/>Thanks for registering to [#sitename#]. For the record, here is your login info again:<br/>Username: [#username#]<br/>Password: [#password#]<br/><b>Login URL: <a href="[#loginurl#]">[#loginurl#]</a></b><br/><br/>Best Regards,<br/>Support Team<br/><b><a href="[#homeurl#]">[#sitename#]</a></b>'
                )
            ),
            'user-signup-admin'          => array(
                'label'   => __( "User Signup Notification" , "download-manager" ),
                'for'     => 'admin',
                'default' => array(
                    'subject'    => sprintf( __( "[ %s ] New User Registration" , "download-manager" ), $sitename ),
                    'from_name'  => get_option( 'blogname' ),
                    'from_email' => $admin_email,
                    'to_email'   => $admin_email,
                    'message'    => __( "New user registration on your site WordPress Download Manager:" , "download-manager" ).'<hr/>Username: [#username#]<br/>Email: [#email#]<br/>IP: [#user_ip#]<hr/>[#edit_user_btn#]<br/><br/>Best Regards,<br/>Support Team<br/><b><a href="[#homeurl#]">[#sitename#]</a></b>'
                )
            ),
            'password-reset'       => array(
                'label'   => __( "Password Reset Notification" , "download-manager" ),
                'for'     => 'customer',
                'default' => array(
                    'subject'    => sprintf( __( "Request to reset your %s password" , "download-manager" ), $sitename ),
                    'from_name'  => get_option( 'blogname' ),
                    'from_email' => $admin_email,
                    'message'    => 'You have requested for your password to be reset.<br/>Please confirm by clicking the button below:  <a href="[#reset_password#]">[#reset_password#]</a><br/>No action required if you did not request it.</b><br/><br/>Best Regards,<br/>Support Team<br/><b><a href="[#homeurl#]">[#sitename#]</a></b>'
                )
            ),
            'email-lock'           => array(
                'label'   => __( "Email Lock Notification" , "download-manager" ),
                'for'     => 'customer',
                'default' => array(
                    'subject'    => __( "Download [#package_name#]" , "download-manager" ),
                    'from_name'  => get_option( 'blogname' ),
                    'from_email' => $admin_email,
                    'message'    => 'Thanks for Subscribing to [#sitename#]<br/>Please click on following link to start download:<br/><b><a style="display: block;text-align: center" class="button" href="[#download_url#]">Download</a></b><br/><br/><br/>Best Regards,<br/>Support Team<br/><b>[#sitename#]</b>'
                )
            ),
            'new-package-frontend' => array(
                'label'   => __( "New Package Notification" , "download-manager" ),
                'for'     => 'admin',
                'default' => array(
                    'subject'    => __( "New Package is Added By [#name#]" , "download-manager" ),
                    'from_name'  => get_option( 'blogname' ),
                    'from_email' => $admin_email,
                    'to_email'   => $admin_email,
                    'message'    => 'A new package is added<br/><br/><table style="width: 100%" cellpadding="10px"><tr><td width="120px">Package Name:</td><td>[#package_name#]</td></tr><tr><td width="120px">Added By:</td><td>[#author#]</td></tr><tr><td width="120px"></td><td><div style="padding-top: 10px;"><a class="btn" href="[#edit_url#]">Review The Package</a></div></td></tr></table>'
                )
            ),
        );

        $templates = apply_filters( 'wpdm_email_templates', $templates );

        return $templates;

    }

    public static function info( $id ) {
        $templates = self::templates();
        return isset($templates[ $id ]) ? $templates[ $id ] : null;
    }

    public static function tags() {
        $tags = array(

            "[#support_email#]" => array( 'value' => get_option( 'admin_email' ), 'desc' => 'Support Email' ),
            "[#img_logo#]"     => array( 'value' => '', 'desc' => 'Site Logo' ),
            "[#banner#]"     => array( 'value' => '', 'desc' => 'Banner/Background Image URL' ),
            "[#site_url#]"       => array( 'value' => home_url( '/' ), 'desc' => 'Home URL of your website' ),
            "[#homeurl#]"       => array( 'value' => home_url( '/' ), 'desc' => 'Home URL of your website' ),
            "[#sitename#]"      => array(
                'value' => get_option( 'blogname' ),
                'desc'  => 'The name/title of your website'
            ),
            "[#site_tagline#]"  => array(
                'value' => get_bloginfo( 'description' ),
                'desc'  => 'The name/title of your website'
            ),
            "[#loginurl#]"      => array( 'value' => wp_login_url(), 'desc' => 'Login page URL' ),
            "[#name#]"          => array( 'value' => '', 'desc' => 'Members First Name' ),
            "[#username#]"      => array( 'value' => '', 'desc' => 'Username' ),
            "[#password#]"      => array( 'value' => '', 'desc' => 'Members account password' ),
            "[#date#]"          => array(
                'value' => date_i18n( get_option( 'date_format' ), time() ),
                'desc'  => 'Current Date'
            ),
            "[#package_name#]"  => array( 'value' => '', 'desc' => 'Package Name' ),
            "[#author#]"        => array( 'value' => '', 'desc' => 'Package author profile' ),
            "[#package_url#]"   => array( 'value' => '', 'desc' => 'Package URL' ),
            "[#edit_url#]"      => array( 'value' => '', 'desc' => 'Package Edit URL' )
        );

        $tags["[#client_ip#]"] = ['value' => wpdm_get_client_ip(), 'desc' => 'User IP'];

        if(is_user_logged_in()) {
            global $current_user;
            $tags["[#user_login#]"] = ['value' => $current_user->user_login, 'desc' => 'User login'];
            $tags["[#user_email#]"] = ['value' => $current_user->user_email, 'desc' => 'User email'];
            $tags["[#user_first_name#]"] = ['value' => $current_user->user_firstname, 'desc' => 'User first name'];
            $tags["[#user_last_name#]"] = ['value' => $current_user->user_lastname, 'desc' => 'User last name'];
            $tags["[#user_display_name#]"] = ['value' => $current_user->display_name, 'desc' => 'User display name'];
            $tags["[#user_description#]"] = ['value' => get_user_meta($current_user->ID, 'description', true), 'desc' => 'User display name'];
        }

        return apply_filters( "wpdm_email_template_tags", $tags );
    }

    public static function defaultTemplate( $id ) {
        $templates = self::templates();

        return isset($templates[ $id ], $templates[ $id ]['default']) ? $templates[ $id ]['default'] : null;
    }

    public static function template( $id ) {
        $template = maybe_unserialize( get_option( "__wpdm_etpl_" . $id, false ) );
        //print_r($template);die();
        $default = self::defaultTemplate( $id );
        if ( ! $template ) {
            $template = $default;
        }
        $template['message'] = ! isset( $template['message'] ) || trim( strip_tags( $template['message'] ) ) == '' ? $default['message'] : $template['message'];

        return $template;
    }

    public static function prepare( $id, $params ) {
        $template = self::template( $id );

        $params   = apply_filters( "wpdm_email_params_" . $id, $params );
        $template = apply_filters( "wpdm_email_template_" . $id, $template );
        if(!is_array($params)) $params = [];
        $__wpdm_email_setting = maybe_unserialize( get_option( '__wpdm_email_setting', array() ) );
        if(!is_array($__wpdm_email_setting)) $__wpdm_email_setting = [];
        $params                 = $params + $__wpdm_email_setting;
        $logo = isset($params['logo']) ? esc_url($params['logo']) : '';
        $banner = isset($params['banner']) ? esc_url($params['banner']) : '';
        $params['img_logo']     = isset( $params['logo'] ) && $params['logo'] != '' ? "<img style='max-width: 70%' src='{$logo}' alt='".esc_attr(get_option('blogname'))."' />" : get_bloginfo('name');
        $params['banner']       = isset( $params['banner'] ) && $params['banner'] != '' ? esc_url($params['banner']) : "";
        $params['banner_img']   = isset( $params['banner'] ) && $params['banner'] != '' ? "<img style='max-width: 100%;' src='{$banner}' alt='Banner Image' />" : "";
        $template_file          = get_option( "__wpdm_email_template", "default.html" );
        $emltpl = null;
        if ( isset( $params['template_file'] ) ) {
            $template_file = $params['template_file'];
            $emltpl = Template::locate( "email-templates/".sanitize_file_name($template_file), __DIR__ . '/views' );
        }
        if(!$emltpl)
            $emltpl = Template::locate( "email-templates/".sanitize_file_name($template_file), __DIR__ . '/views' );
        if($emltpl)
            $emltpl = realpath($emltpl);

        if($template_file === '' || !$emltpl)
            $emltpl = Template::locate( "email-templates/default.html", __DIR__ . '/views' );

        if(file_exists($emltpl))
            $template_data = file_get_contents( $emltpl );

        $template['message'] = str_replace( "[#message#]", stripslashes( wpautop( $template['message'] ) ), $template_data );
        $tags                = self::tags();
        $new_pasrams         = array();
        foreach ( $params as $key => $val ) {
            $new_pasrams["[#{$key}#]"] = stripslashes($val);
        }
        $params = $new_pasrams;
        foreach ( $tags as $key => $info ) {
            if ( ! isset( $params[$key] )) {
                $params[$key] = $info['value'];
            }
        }

        $template['subject'] = str_replace( array_keys( $params ), array_values( $params ), $template['subject'] );
        if(isset($template['to_email']))
            $template['to_email'] = str_replace( array_keys( $params ), array_values( $params ), $template['to_email'] );
        $template['message'] = str_replace( array_keys( $params ), array_values( $params ), $template['message'] );
        $template['message'] = self::compile($template['message']);
        return $template;
    }

    public static function send( $id, $params ) {
        $email       = self::info( $id );
        $template    = self::prepare( $id, $params );
        $headers[]     = "From: " . $template['from_name'] . " <" . $template['from_email'] . ">";
        $headers[]     = "Content-type: text/html";
        if(!isset($template['to_email'])) {
            $template['to_email'] = get_option('admin_email');
        }
        $to          = $email['for'] !== 'admin' && !isset($params['to_seller']) && isset($params['to_email']) ? $params['to_email'] : $template['to_email'];
        $headers     = apply_filters( "wpdm_email_headers_" . str_replace("-", "_", $id), $headers );
        if(isset($params['cc'])){
            $headers[] = "CC: {$params['cc']}";
            unset($params['cc']);
        }
        if(isset($params['bcc'])){
            $headers[] = "Bcc: {$params['bcc']}";
            unset($params['bcc']);
        }

        $attachments = apply_filters( "wpdm_email_attachments_" . str_replace("-", "_", $id), array(), $params );
        return wp_mail( $to, html_entity_decode($template['subject']), $template['message'], $headers, $attachments );
    }


    public function preview() {
        global $current_user;


        if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'email_template_preview' ) {
            return;
        }

        __::isAuthentic("__empnonce", WPDM_PRI_NONCE, WPDM_MENU_ACCESS_CAP, false);


        $id     = wpdm_query_var('id');
        $email  = self::info( $id );
        $params = array(
            "name"         => $current_user->display_name,
            "username"     => $current_user->user_login,
            "password"     => "**************",
            "package_name" => __( "Sample Package Name" , "download-manager" ),
            "author"       => $current_user->display_name,
            "package_url"  => "#",
            "edit_url"     => "#"
        );

        if ( isset( $_REQUEST['etmpl'] ) ) {
            $params['template_file'] = wpdm_query_var('etmpl');
        }
        $template = self::prepare( $id, $params );
        echo wpdm_valueof($template, 'message', 'html');
        die();

    }

    static public function fetch($template, $message) {
        global $current_user;
        if ( ! current_user_can( WPDM_MENU_ACCESS_CAP ) ) {
            die( 'Error' );
        }

        $params['template_file'] = $template;

        $template = self::prepare( 'default', $params );
        return $template['message'];
    }

    static function compile($template, $rule = "/\[\#(.*)\#\]/")
    {
        $compiled = preg_replace_callback($rule, [new self, '_var'], $template);
        return $compiled;
    }

    static function _var($matched)
    {
        if(substr_count($matched[1], "acfx_user_meta_")){
            $meta_name = str_replace("acfx_user_meta_", "", $matched[1]);
            $meta_value = function_exists('get_field') ? get_field($meta_name, 'user_'.get_current_user_id()) : '';
            return $meta_value;
        }
        if(substr_count($matched[1], "acf_user_meta_")){
            $meta_name = str_replace("acf_user_meta_", "", $matched[1]);
            $data = maybe_unserialize(get_user_meta(get_current_user_id(), 'wpdm_cregf', true));
            $value = wpdm_valueof($data, $meta_name);
            if(is_array($value)) $value = implode(", ", $value);
            return $value;
        }
        if(substr_count($matched[1], "user_meta_")){
            $meta_name = str_replace("user_meta_", "", $matched[1]);
            if(substr_count($meta_name, '/')){
                $meta_name = explode("/", $meta_name);
                $meta_value = get_user_meta(get_current_user_id(), $meta_name[0], true);
                array_shift($meta_name);
                $meta_value = wpdm_valueof($meta_value, implode("/", $meta_name));
                return $meta_value;
            }
            return get_user_meta(get_current_user_id(), $meta_name, true);
        }
        if(substr_count($matched[1], "SERVER_")){
            $meta_name = str_replace("SERVER_", "", $matched[1]);
            $meta_value = wpdm_valueof($_REQUEST, $meta_name);
            return $meta_value;
        }
        if(substr_count($matched[1], "REQUEST_")){
            $meta_name = str_replace("REQUEST_", "", $matched[1]);
            $meta_value = wpdm_valueof($_REQUEST, $meta_name);
            if(is_array($meta_value)) $meta_value = implode(", ", $meta_value);
            return $meta_value;
        }
        return $matched[1];
    }


}
