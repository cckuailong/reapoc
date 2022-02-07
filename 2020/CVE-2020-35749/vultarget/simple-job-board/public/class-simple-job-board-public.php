<?php
/**
 * Simple_Job_Board_Public Class
 *
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/simple-job-board
 * @since      1.0.0
 *
 * @package    Simple_Job_Board
 * @subpackage Simple_Job_Board/public
 * @author     PressTigers <support@presstigers.com>
 */
class Simple_Job_Board_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $simple_job_board    The ID of this plugin.
     */
    private $simple_job_board;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $simple_job_board       The name of the plugin.
     * @param    string    $version                The version of this plugin.
     */
    public function __construct($simple_job_board, $version)
    {

        $this->simple_job_board = $simple_job_board;
        $this->version = $version;

        /**
         * The class responsible for defining all the custom post types in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-job-board-post-types-init.php';

        /**
         * The class responsible for defining all the shortcodes in the front end area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-job-board-shortcode-jobpost.php';

        /**
         * The class responsible for Ajax Call on Job Submission in the front end area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-job-board-ajax.php';

        /**
         * The class responsible for Sending email notificatins to Applicant, Admin & HR.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-job-board-notifications.php';

        /**
         * The class responsible for loading job board typography.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-simple-job-board-typography.php';

        // Action -> Load Template Functions.
        add_action('after_setup_theme', array($this, 'sjb_template_functions'), 11);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     * @since    2.4.0  Updated Outdated Styles
     */
    public function enqueue_styles()
    {

        // Enqueue Google Fonts
        wp_enqueue_style($this->simple_job_board . '-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i', array(), $this->version, 'all');

        // Enqueue Font Awesome Styles
        wp_enqueue_style($this->simple_job_board . '-font-awesome', plugin_dir_url(__FILE__) . 'css/font-awesome.min.css', array(), '4.7.0', 'all');
        wp_enqueue_style($this->simple_job_board . '-jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), '1.12.1', 'all');

        // Enqueue Front-end RTL Styles
        if (is_rtl()) {
            wp_enqueue_style($this->simple_job_board . '-frontend-rtl', plugin_dir_url(__FILE__) . 'css/rtl/simple-job-board-public-rtl.css', array(), '2.0.0', 'all');
        } else {
            wp_enqueue_style($this->simple_job_board . '-frontend', plugin_dir_url(__FILE__) . 'css/simple-job-board-public.css', array(), '3.0.0', 'all');
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     * @since    2.4.0  Updated InputTel Scripts
     */
    public function enqueue_scripts()
    {

        // Register Simple Job Board Front-end Core JS
        wp_register_script($this->simple_job_board . '-front-end', plugin_dir_url(__FILE__) . 'js/simple-job-board-public.js', array('jquery', 'jquery-ui-datepicker'), '1.4.0', true);

        // Register Input Telephone JS
        wp_register_script($this->simple_job_board . '-validate-telephone-input', plugin_dir_url(__FILE__) . 'js/intlTelInput.min.js', array('jquery'), '9.2.4', true);
        wp_register_script($this->simple_job_board . '-validate-telephone-input-utiliy', plugin_dir_url(__FILE__) . 'js/intlTelInput-utils.js', array('jquery'), '7.7.3', true);
        wp_localize_script(
            $this->simple_job_board . '-front-end',
            'application_form',
            array(
                'ajaxurl' => esc_js(admin_url('admin-ajax.php')),
                'setting_extensions' => is_array(get_option('job_board_upload_file_ext')) ? array_map('esc_js', get_option('job_board_upload_file_ext')) : esc_js(get_option('job_board_upload_file_ext')),
                'all_extensions_check' => esc_js(get_option('job_board_all_extensions_check')),
                'allowed_extensions' => is_array(get_option('job_board_allowed_extensions')) ? array_map('esc_js', get_option('job_board_allowed_extensions')) : esc_js(get_option('job_board_allowed_extensions')),
                'job_listing_content' => esc_js(get_option('job_board_listing')),
                'jobpost_content' => esc_js(get_option('job_board_jobpost_content')),
                'jquery_alerts' => array(
                    'invalid_extension' => apply_filters('sjb_invalid_file_ext_alert', esc_html__('This is not an allowed file extension.', 'simple-job-board')),
                    'application_not_submitted' => apply_filters('sjb_job_not_submitted_alert', esc_html__('Your application could not be processed.', 'simple-job-board')),
                ),
                'file' => array(
                    'browse' => esc_html__('Browse', 'simple-job-board'),
                    'no_file_chosen' => esc_html__('No file chosen', 'simple-job-board'),
                ),
            )
        );
    }

    /**
     * Load Templates
     *
     * @since    2.1.0
     */
    public function sjb_template_functions()
    {
        include 'partials/simple-job-board-template-functions.php';
    }
}
