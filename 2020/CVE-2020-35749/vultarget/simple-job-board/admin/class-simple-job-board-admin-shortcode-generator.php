<?php
/**
 * Simple_Job_Board_Admin_Shortcodes_Generator class
 *
 * Add job board shortcode builder to TinyMCE. Define the shortcode button and
 * parameters in TinyMCE. Also creates shortcodes with given parameters.
 *
 * @link       https://wordpress.org/plugins/simple-job-board
 * @since      2.2.3
 * @since      2.8.0 - Made TinyMCE compatible with Gutenberg Classic Editor
 *
 * @package    Simple_Job_Board
 * @subpackage Simple_Job_Board/admin
 * @author     PressTigers <support@presstigers.com>
 */
class Simple_Job_Board_Admin_Shortcodes_Generator
{

    /**
     * Initilaize class.
     *
     * @since   2.2.3
     */
    public function __construct()
    {

        /**
         * Detect plugin. For use on Front End only.
         */
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        // check for plugin using plugin name
        if (is_plugin_active('classic-editor/classic-editor.php')) {
            // Action -> Add SJB Button on TinyMCE Editor.
            add_action('admin_head', array($this, 'add_tinymce_button'));
        } else {
            add_filter('mce_external_plugins', array($this, 'add_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        }

    }

    /**
     * Add Filters for TinyMCE buttton.
     *
     * @since  2.2.3
     */
    public function add_tinymce_button()
    {

        global $typenow;

        // Check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // Verify the post type
        if (!in_array($typenow, array('post', 'page'))) {
            return;
        }

        // Check if WYSIWYG is enabled
        if ('true' === get_user_option('rich_editing')) {
            add_filter('mce_external_plugins', array($this, 'add_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        }

        // Enqueue Core Shortcode Generator Styles
        wp_enqueue_style('sjb-shortcode-generator', plugin_dir_url(__FILE__) . 'css/simple-job-board-admin-shortcode-generator.css', array(), '1.0.0', 'all', false);
    }

    /**
     * Loads the TinyMCE Button js File.
     *
     * This function specifies the path to the script with Job Board for TinyMCE.
     *
     * @since  2.2.3
     *
     * @param   array   $plugin_array
     * @return  array   $plugin_array   Load button script
     */
    public function add_tinymce_plugin($plugin_array)
    {

        // Enqueue Simple Job Board Shortcode Builder JS File
        $plugin_array['sjb_shortcodes_mce_button'] = plugins_url('/js/simple-job-board-admin-shortcodes-generator.js', __FILE__);
        return $plugin_array;
    }

    /**
     * Adds the TinyMCE button to the post editor buttons
     *
     * @since  2.2.3
     *
     * @param   array   $buttons    TinyMCE buttons
     * @return  array   $buttons    Append shortcode builder button with TinyMCE buttons list.
     */
    public function register_tinymce_button($buttons)
    {
        // Stack custom event to TinyMCE
        array_push($buttons, 'sjb_shortcodes_mce_button');
        return $buttons;
    }
}

new Simple_Job_Board_Admin_Shortcodes_Generator();
