<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Paid Membership Pro addon class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_PMP extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Factory class
        $this->factory = $this->getFactory();
        
        // MEC Main class
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize the PMP addon
     * @author Webnus <info@webnus.biz>
     * @return boolean
     */
    public function init()
    {
        // Module is not enabled
        if(!isset($this->settings['pmp_status']) or (isset($this->settings['pmp_status']) and !$this->settings['pmp_status'])) return false;

        // Metabox
        add_action('admin_menu', array($this, 'metabox'));

        // Display Access Error
        add_filter('mec_show_event_details_page', array($this, 'check'), 10, 2);

        return true;
    }

    public function metabox()
    {
        if(!defined('PMPRO_VERSION')) return;

        // Register
        add_meta_box('pmpro_page_meta', esc_html__('Require Membership', 'modern-events-calendar-lite'), 'pmpro_page_meta', $this->main->get_main_post_type(), 'side', 'high');
    }

    public function check($status, $event_id)
    {
        if(!defined('PMPRO_VERSION')) return $status;

        // Has Access
        if(function_exists('pmpro_has_membership_access'))
        {
            $response = pmpro_has_membership_access($event_id, NULL, true);
            $available = (isset($response[0]) ? $response[0] : true);

            if(!$available)
            {
                $post_membership_levels_ids = $response[1];
                $post_membership_levels_names = $response[2];

                $content = pmpro_get_no_access_message('', $post_membership_levels_ids, $post_membership_levels_names);
                $status = '<div class="mec-wrap mec-no-access-error"><h1>'.get_the_title($event_id).'</h1>'.$content.'</div>';
            }
        }

        return $status;
    }
}