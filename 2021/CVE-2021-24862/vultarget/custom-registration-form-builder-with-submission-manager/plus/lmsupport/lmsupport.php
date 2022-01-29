<?php

/* 
 * Supporter for LeadMagic extension
 */

class RM_EX_LMSupport {
    
    public $base_dir;
    public $base_url;
    public $lm_slug;
    
    public function __construct() {
        $this->lm_slug = 'custom-landing-pages-leadmagic';
        $this->base_dir =  plugin_dir_path(__FILE__);
        $this->base_url =  plugin_dir_url(__FILE__);
        $this->attach_hooks();
        $this->load_dependencies();
    }
    
    public function attach_hooks() {
        add_filter('rm_global_setting_manager', array($this,'extend_global_setting'), 11);
        add_filter('admin_menu', array($this,'create_admin_page'));
        add_action('rm_frontend_primer_content', array($this,'extend_frontend_primer'));
        add_action('rm_formflow_publish_page', array($this,'extend_formflow_publish_page'),10,1);
    }
    
    public function load_dependencies() {
        include $this->base_dir."helpers/ui_strings.php";
    }
    
    public function extend_global_setting($html) {
        $opt = '';
        ob_start();
        include $this->base_dir."templates/global_settings_icon.php";
        $opt = ob_get_clean();
        return $html.$opt;
    }
    
    public function extend_frontend_primer() {
        $data = new stdClass;
        $data->lm_install_url = $this->get_lm_install_url();
        $data->is_lm_activated = class_exists("RM_LP");
        $data->lm_page_url = '';
        if($data->is_lm_activated) {
            $data->is_lm_installed = true;
            $data->lm_page_url = admin_url('edit.php?post_type=rmlp');
        } else {
            $plugins = get_plugins();
            $data->is_lm_installed = array_key_exists($this->lm_slug."/rm-landing.php", $plugins);
            if($data->is_lm_installed)
                $data->lm_activate_url = $this->get_lm_activate_url();
        }
        
        include $this->base_dir."templates/frontend_primer.php";        
    }
    
    public function display_promo_page() {
        $data = new stdClass;
        $data->lm_install_url = $this->get_lm_install_url();
        $data->lm_activate_url = '';
        $data->is_lm_activated = class_exists("RM_LP");
        $data->lm_page_url = '';
        if($data->is_lm_activated) {
            $data->is_lm_installed = true;
            $data->lm_page_url = admin_url('edit.php?post_type=rmlp');
        } else {
            $plugins = get_plugins();
            $data->is_lm_installed = array_key_exists($this->lm_slug."/rm-landing.php", $plugins);
            if($data->is_lm_installed)
                $data->lm_activate_url = $this->get_lm_activate_url();
        }        
        wp_enqueue_style('rm_lms_promopage_style', $this->base_url."templates/css/style.css");
        include $this->base_dir."templates/promo_page.php";
    }
    
    public function create_admin_page() {
        add_submenu_page("", "Landing Page", "Landing Page", "manage_options", "rm_ex_lmsupport", array($this,'display_promo_page'));        
    }
    
    public function get_lm_install_url() {
        $installUrl = admin_url('update.php?action=install-plugin&plugin=' . $this->lm_slug);
        $installUrl = wp_nonce_url($installUrl, 'install-plugin_' . $this->lm_slug);
        return $installUrl;
    }
    
    public function get_lm_activate_url()
    {
        $plugin = $this->lm_slug."/rm-landing.php";

        if (strpos($plugin, '/')) {
            $plugin = str_replace('/', '%2F', $plugin);
        }

        $activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s'), $plugin);
        
        $activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $this->lm_slug."/rm-landing.php");
        
        return $activateUrl;
    }
    
    public function extend_formflow_publish_page($form_id) {     
        $data = new stdClass;
        $data->lm_install_url = $this->get_lm_install_url();
        $data->lm_activate_url = '';
        $data->is_lm_activated = class_exists("RM_LP");
        $data->lm_page_url = '';
        if($data->is_lm_activated) {
            $data->is_lm_installed = true;
            $data->lm_page_url = admin_url('edit.php?post_type=rmlp');
        } else {
            $plugins = get_plugins();
            $data->is_lm_installed = array_key_exists($this->lm_slug."/rm-landing.php", $plugins);
            if($data->is_lm_installed)
                $data->lm_activate_url = $this->get_lm_activate_url();
        }
        include $this->base_dir."templates/formflow_publish_extended.php";
    }
}

$GLOBALS['RM_EX_LMSupport'] = new RM_EX_LMSupport;

function RM_EX_LMS() {
    return $GLOBALS['RM_EX_LMSupport'];
}