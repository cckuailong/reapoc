<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC User Events class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_userevents extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

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
    }

    /**
     * Initialize User Events Feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // User Events Shortcode
        $this->factory->shortcode('MEC_userevents', array($this, 'output'));
    }

    /**
     * Show user events
     * @param array $atts
     * @return string
     */
    public function output($atts = array())
    {
        // Force to array
        if(!is_array($atts)) $atts = array();

        // Show login/register message if user is not logged in and guest submission is not enabled.
        if(!is_user_logged_in())
        {
            // Show message
            $message = sprintf(__('Please %s/%s in order to see your own events.', 'modern-events-calendar-lite'), '<a href="'.wp_login_url($this->main->get_full_url()).'">'.__('Login', 'modern-events-calendar-lite').'</a>', '<a href="'.wp_registration_url().'">'.__('Register', 'modern-events-calendar-lite').'</a>');

            return '<div class="mec-userevents-message">
                <p>'.$message.'</p>
            </div>';
        }

        // Render Library
        $render = $this->getRender();

        // Settings
        $settings = $this->main->get_settings();

        $shortcode_id = (isset($settings['userevents_shortcode']) and trim($settings['userevents_shortcode'])) ? $settings['userevents_shortcode'] : NULL;

        $atts = apply_filters('mec_calendar_atts', $render->parse($shortcode_id, array(
            'author' => get_current_user_id()
        )));

        $skin = isset($atts['skin']) ? $atts['skin'] : 'monthly_view';
        return $render->skin($skin, $atts);
    }
}