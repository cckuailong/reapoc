<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Auto Emails class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_autoemails extends MEC_base
{
    public $factory;
    public $main;
    public $PT;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Email Post Type Name
        $this->PT = $this->main->get_email_post_type();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    /**
     * Initialize Auto Email feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // PRO Version is required
        if(!$this->getPRO()) return false;

        // Show Auto Email feature only if module is enabled
        if(!isset($this->settings['auto_emails_module_status']) or (isset($this->settings['auto_emails_module_status']) and !$this->settings['auto_emails_module_status'])) return false;

        $this->factory->action('init', array($this, 'register_post_type'));
        $this->factory->action('save_post', array($this, 'save_email'), 10);
        $this->factory->action('add_meta_boxes', array($this, 'register_meta_boxes'), 1);

        return true;
    }

    /**
     * Registers email post type
     * @author Webnus <info@webnus.biz>
     */
    public function register_post_type()
    {
        $singular_label = __('Email', 'modern-events-calendar-lite');
        $plural_label = __('Emails', 'modern-events-calendar-lite');

        $capability = 'manage_options';
        register_post_type($this->PT,
            array(
                'labels'=>array
                (
                    'name'=>$plural_label,
                    'singular_name'=>$singular_label,
                    'add_new'=>sprintf(__('Add %s', 'modern-events-calendar-lite'), $singular_label),
                    'add_new_item'=>sprintf(__('Add %s', 'modern-events-calendar-lite'), $singular_label),
                    'not_found'=>sprintf(__('No %s found!', 'modern-events-calendar-lite'), strtolower($plural_label)),
                    'all_items'=>$plural_label,
                    'edit_item'=>sprintf(__('Edit %s', 'modern-events-calendar-lite'), $plural_label),
                    'not_found_in_trash'=>sprintf(__('No %s found in Trash!', 'modern-events-calendar-lite'), strtolower($singular_label))
                ),
                'public'=>false,
                'show_ui'=>(current_user_can($capability) ? true : false),
                'show_in_menu'=>false,
                'show_in_admin_bar'=>false,
                'show_in_nav_menus'=>false,
                'has_archive'=>false,
                'exclude_from_search'=>true,
                'publicly_queryable'=>false,
                'supports'=>array('title', 'editor'),
                'capabilities'=>array
                (
                    'read'=>$capability,
                    'read_post'=>$capability,
                    'read_private_posts'=>$capability,
                    'create_post'=>$capability,
                    'create_posts'=>$capability,
                    'edit_post'=>$capability,
                    'edit_posts'=>$capability,
                    'edit_private_posts'=>$capability,
                    'edit_published_posts'=>$capability,
                    'edit_others_posts'=>$capability,
                    'publish_posts'=>$capability,
                    'delete_post'=>$capability,
                    'delete_posts'=>$capability,
                    'delete_private_posts'=>$capability,
                    'delete_published_posts'=>$capability,
                    'delete_others_posts'=>$capability,
                ),
            )
        );
    }

    /**
     * Registers meta boxes
     * @author Webnus <info@webnus.biz>
     */
    public function register_meta_boxes()
    {
        add_meta_box('mec_email_metabox_details', __('Details', 'modern-events-calendar-lite'), array($this, 'meta_box_details'), $this->PT, 'normal', 'high');
    }

    public function meta_box_details($post)
    {
        $path = MEC::import('app.features.emails.details', true, true);

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Save email data from backend
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return void
     */
    public function save_email($post_id)
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_email_nonce'])) return;

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['mec_email_nonce']), 'mec_email_data')) return;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

        // MEC Data
        $mec = (isset($_POST['mec']) and is_array($_POST['mec'])) ? $_POST['mec'] : array();

        // All Options
        update_post_meta($post_id, 'mec', $mec);

        update_post_meta($post_id, 'mec_time', (isset($mec['time']) ? $mec['time'] : 1));
        update_post_meta($post_id, 'mec_type', (isset($mec['type']) ? $mec['type'] : 'day'));
        update_post_meta($post_id, 'mec_afterbefore', (isset($mec['afterbefore']) ? $mec['afterbefore'] : 'before'));

        $events = ((isset($mec['events']) and is_array($mec['events'])) ? $mec['events'] : array());

        $all = (isset($mec['all']) ? $mec['all'] : 1);
        if($all) $events = array();

        update_post_meta($post_id, 'mec_all', $all);
        update_post_meta($post_id, 'mec_events', $events);
    }
}