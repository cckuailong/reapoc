<?php

namespace WPDM\Admin\Menu;


use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Template;

class Templates
{

    function __construct()
    {
        add_filter('init', array($this, 'livePreview'), 99 );
        add_filter('wdm_before_fetch_template', array($this, 'introduceCustomTags'), 99, 3 );
        //add_action('admin_init', array($this, 'Save'));
        add_action('wp_ajax_template_preview', array($this, 'preview'));
        add_action('wp_ajax_wpdm_save_email_template', array($this, 'saveEmailTemplate'));
        add_action('wp_ajax_update_template_status', array($this, 'updateTemplateStatus'));
        add_action('wp_ajax_wpdm_save_email_setting', array($this, 'saveEmailSetting'));
        add_action('wp_ajax_connect_template_server', array($this, 'templateServer'));
        add_action('admin_menu', array($this, 'menu'));
    }

    function livePreview(){
        if(wpdm_query_var('template_preview') !== '') {
	        __::isAuthentic('_tplnonce', NONCE_KEY, WPDM_ADMIN_CAP);
            add_filter( 'show_admin_bar', '__return_false' );
            $page_template = Template::locate('template-preview.php', WPDM_SRC_DIR.'__/views');
            $type = wpdm_query_var('_type');
            global $post;
            if(wpdm_query_var('pid'))
                $package = get_post(wpdm_query_var('pid'), ARRAY_A);
            else {
                $package = get_posts(array('post_type' => 'wpdmpro', 'posts_per_page' => 1, 'post_status' => 'publish'));
                $package = (array)$package[0];
            }
            $template = Crypt::decrypt(wpdm_query_var('template_preview'));
            $template = stripslashes_deep(str_replace(array("\r", "\n"), "", html_entity_decode(urldecode($template))));
            $output = wpdm_fetch_template($template, $package, $type);
            $template = "<div class='w3eden' style='max-width: 900px;margin: 20px auto !important;padding: 40px;'>{$output}</div><script> jQuery(function($) {  var body = document.body, html = document.documentElement; var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight ); window.parent.wpdmifh(height); });</script>";
            include $page_template;
            die();
        }
    }

    function menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Templates &lsaquo; Download Manager" , "download-manager" ), __( "Templates" , "download-manager" ), WPDM_MENU_ACCESS_CAP, 'templates', array($this, 'UI'));
    }

    function UI(){
        $ttype = isset($_GET['_type']) ? wpdm_query_var('_type') : 'link';

        if (isset($_GET['task']) && ($_GET['task'] == 'EditTemplate' || $_GET['task'] == 'NewTemplate'))
            Templates::editor();
        else if (isset($_GET['task']) && $_GET['task'] == 'EditEmailTemplate')
            Templates::emailEditor();
        else
            Templates::Show();
    }


    public static function editor(){
        include wpdm_admin_tpl_path("templates/template-editor.php");
    }


    public static function emailEditor(){
        include wpdm_admin_tpl_path("templates/email-template-editor.php");
    }


    public static function Show(){
        WPDM()->packageTemplate->covertAll()->covertAll('page');
        include wpdm_admin_tpl_path('templates/templates.php');
    }

    function saveEmailTemplate(){
        if (isset($_POST['email_template'])) {
            __::isAuthentic('__setnonce', WPDM_PRI_NONCE, WPDM_ADMIN_CAP);
            $email_template = wpdm_query_var('email_template', array('validate' => array('subject' => '', 'message' => 'escs', 'from_name' => '', 'from_email' => '')));
            update_option("__wpdm_etpl_".wpdm_query_var('id'), $email_template, false);
            wp_send_json(array('success' => true, 'message' => 'Email Template Saved!'));
            //header("location: edit.php?post_type=wpdmpro&page=templates&_type=$ttype");
            die();
        }
    }

    /**
     * @usage Preview link/page template
     */
    function preview()
    {

        $wposts = array();
        __::isAuthentic('_tplnonce', WPDM_PUB_NONCE, WPDM_ADMIN_CAP);
        $template = isset($_REQUEST['template'])?wpdm_query_var('template', 'escs'):'';
        $type = wpdm_query_var("_type");
        $css = wpdm_query_var("css","txt");


        $args=array(
            'post_type' => 'wpdmpro',
            'posts_per_page' => 1
        );

        $wposts = get_posts( $args  );
        $template = stripslashes($template);
        $template = urlencode($template);
        $tplnonce = wp_create_nonce(NONCE_KEY);
        $template_enc = Crypt::encrypt($template);
        $preview_link = home_url("/?template_preview={$template_enc}&_type={$type}&_tplnonce={$tplnonce}");

        if(count($wposts)==0) $html = "<div class='w3eden'><div class='col-md-12'><div class='alert alert-info'>".__( "No package found! Please create at least 1 package to see template preview" , "download-manager" )."</div> </div></div>";
        else
            $html = "<a class='btn btn-link btn-block' href='{$preview_link}' target='_blank'><i class='fa fa-external-link-alt'></i> Preview in a new window</a><iframe id='templateiframe' src='{$preview_link}' style='border: 0;width: 100%;height: 200px;overflow: hidden'></iframe><script>function wpdmifh( h ){ jQuery('#templateiframe').height(h); }</script>";

        echo $html;
        die();

    }

    public static function dropdown($params, $activeOnly = false)
    {
        extract($params);
        $type = isset($type) && in_array($type, array('link', 'page', 'email')) ? esc_attr($type) : 'link';
        $tplstatus = maybe_unserialize(get_option("_fm_{$type}_template_status"));

        $xactivetpls = array();
        $activetpls = array();
        if(is_array($tplstatus)) {
            foreach ($tplstatus as $tpl => $active) {
                if (!$active)
                    $xactivetpls[] = $tpl;
                else
                    $activetpls[] = $tpl;
            }
        }

        $ttpldir = get_stylesheet_directory() . '/download-manager/' . $type . '-templates/';
        $ttpls = array();
        if(file_exists($ttpldir)) {
            $ttpls = scandir($ttpldir);
            array_shift($ttpls);
            array_shift($ttpls);
        }

        $ltpldir = WPDM()->package->templateDir . $type . '-templates/';

        $ctpls = scandir($ltpldir);
        array_shift($ctpls);
        array_shift($ctpls);

        foreach($ctpls as $ind => $tpl){
                $ctpls[$ind] = $ltpldir.$tpl;
        }

        foreach($ttpls as $tpl){
            if(!in_array($ltpldir.$tpl, $ctpls)) {
                    $ctpls[] = $ttpldir . $tpl;
            }
        }

        //$custom_templates = maybe_unserialize(get_option("_fm_{$type}_templates", array()));
        $custom_templates = WPDM()->packageTemplate->getCustomTemplates($type);
        $custom_templates = array_reverse($custom_templates);
        $name = isset($name)?$name:$type.'_template';
        $css = isset($css)?"style='$css'":'';
        $id = isset($id)?$id:uniqid();
        $default = $type == 'link'?'link-template-default.php':'page-template-default.php';
        $xdf = str_replace(".php", "", $default);
        $html = "";
        if(is_array($xactivetpls) && count($xactivetpls) > 0)
            $default = (in_array($xdf, $xactivetpls) || in_array($default, $xactivetpls)) && isset($activetpls[0])?$activetpls[0]:$default;

        $html .= "<select name='$name' id='$id' class='form-control template {$type}_template' {$css}><option value='$default'>Select ".ucfirst($type)." Template</option>";
        $data = array();
        if(is_array($custom_templates)) {
            foreach ($custom_templates as $id => $template) {
                if(!$activeOnly || ($activeOnly && (!isset($tplstatus[$id]) || $tplstatus[$id] == 1))) {
                    $data[$id] = $template['title'];
                    $eselected = isset($selected) && $selected == $id ? 'selected=selected' : '';
                    $html .= "<option value='{$id}' {$eselected}>{$template['title']}</option>";
                }
            }
        }
        foreach ($ctpls as $ctpl) {
            $ind = str_replace(".php", "", basename($ctpl));
            if(!$activeOnly || ($activeOnly && (!isset($tplstatus[$ind]) || $tplstatus[$ind] == 1))) {
                $tmpdata = file_get_contents($ctpl);
                $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                if (preg_match($regx, $tmpdata, $matches)) {
                    $data[basename($ctpl)] = $matches[1];
                    $eselected = isset($selected) && $selected == basename($ctpl) ? 'selected=selected' : '';

                    $html .= "<option value='" . basename($ctpl) . "' {$eselected}>{$matches[1]}</option>";
                }
            }
        }
        $html .= "</select>";

        return isset($data_type) && $data_type == 'ARRAY'? $data : $html;
    }

    function saveEmailSetting(){
        __::isAuthentic('__sesnonce', WPDM_PRI_NONCE, WPDM_ADMIN_CAP, true);

	    update_option('__wpdm_email_template', wpdm_query_var('__wpdm_email_template'), false);
	    $email_settings = wpdm_query_var('__wpdm_email_setting', array('validate' => array('logo' => 'url', 'banner' => 'url', 'youtube' => 'url', 'twitter' => 'url', 'facebook' => 'url', 'footer_text' => 'txts')));
	    update_option('__wpdm_email_setting', $email_settings, false);
	    die("Done!");
    }

    function updateTemplateStatus(){
        if(!current_user_can(WPDM_ADMIN_CAP)) die('error');
        $type = wpdm_query_var('type');
        $tpldata = maybe_unserialize(get_option("_fm_{$type}_template_status"));
        $tpldata[wpdm_query_var('template')] = wpdm_query_var('status');
        update_option("_fm_{$type}_template_status", $tpldata, false);
        echo "OK";
        die();
    }

    function introduceCustomTags($vars, $template, $type){
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        $tags_dir = $upload_dir.'/wpdm-custom-tags/';
        if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
        $custom_tags = scandir($tags_dir);
        foreach ($custom_tags as $custom_tag) {
            if (strstr($custom_tag, '.tag')) {
                $content = file_get_contents($tags_dir . $custom_tag);
                $custom_tag = str_replace(".tag", "", $custom_tag);
                $vars[$custom_tag] = stripslashes($content);
            }
        }
        return $vars;
    }

    function templateServer(){
	    wpdmpro_required();
        die();
    }

}
