<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Meta_Boxes_Init Class
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.4.0   Revised the Nonce Field Checks
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Meta_Boxes_Init {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct() {

        /**
         * The class responsible for defining job data meta box options under custom post type in the admin area.
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/meta-boxes/class-simple-job-board-meta-box-job-data.php';
          
        /**
         * The class responsible for defining job features meta box options under custom post type in the admin area.
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/meta-boxes/class-simple-job-board-meta-box-job-features.php';

        /**
         * The class responsible for defining job application meta box options under custom post type in the admin area.
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/meta-boxes/class-simple-job-board-meta-box-job-application.php';
        
        /**
         * The class responsible for defining application status meta box options under applicant post type in the admin area.
         */
        require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/meta-boxes/class-simple-job-board-meta-box-application-status.php';

        // Action -> Load WP Media Uploader Scripts.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_script_loader' ) );

        // Action -> Post Type -> Jobpost -> Add Meta Boxes. 
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

        // Action -> Post Type -> Jobpost -> Save Meta Boxes.
        add_action( 'save_post_jobpost', array( $this, 'save_meta_boxes' ), 10, 1 );
        
        // Action -> Post Type -> Jobpost Applicants -> Save Meta Boxes.
        add_action( 'save_post_jobpost_applicants', array( $this, 'save_applicants_meta_boxes' ), 10, 1 );

        // Action -> Post Type -> Jobpost -> Save Job Features Meta Box.
        add_action( 'sjb_save_jobpost_meta', array( 'Simple_Job_Board_Meta_Box_Job_Features', 'sjb_save_jobpost_meta' ), 10 );

        // Action -> Post Type -> Jobpost -> Save Job Application Meta Box.
        add_action( 'sjb_save_jobpost_meta', array( 'Simple_Job_Board_Meta_Box_Job_Application', 'sjb_save_jobpost_meta' ), 20 );

        // Action -> Post Type -> Jobpost -> Save Job Data Meta Box.
        add_action( 'sjb_save_jobpost_meta', array( 'Simple_Job_Board_Meta_Box_Job_Data', 'sjb_save_jobpost_meta' ), 30 );

        // Action -> Post Type -> Jobpost Applicants -> Save Application Meta Box.
        add_action( 'sjb_save_jobpost_applicants_meta', array( 'Simple_Job_Board_Meta_Box_Application_Status', 'sjb_save_jobpost_applicants_meta' ), 10 );
    }

    /**
     * Load backend scripts
     * 
     * @since   2.1.0
     */
    function admin_script_loader() {
        
        global $pagenow;

        if (is_admin() && ( in_array($pagenow, array('post-new.php', 'post.php'))) ) {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');            
        }
    }

    /**
     * Add jobpost meta boxes.
     *
     * @since 2.1.0
     */
    public function add_meta_boxes() {
        
        global $wp_post_types;
        add_meta_box('jobpost_metas', sprintf(esc_html__('%s Features', 'simple-job-board'), $wp_post_types['jobpost']->labels->singular_name), array('Simple_Job_Board_Meta_Box_Job_Features', 'sjb_meta_box_output'), 'jobpost', 'normal', 'high');
        add_meta_box('jobpost_application_fields', esc_html__('Application Form Fields', 'simple-job-board'), array('Simple_Job_Board_Meta_Box_Job_Application', 'sjb_meta_box_output'), 'jobpost', 'normal', 'high');
        add_meta_box('simple-job-board-post_options', esc_html__('Job Data', 'simple-job-board'), array('Simple_Job_Board_Meta_box_Job_Data', 'sjb_meta_box_output'), 'jobpost', 'normal');
        add_meta_box('sjb-application-status', esc_html__('Application Status', 'simple-job-board'), array('Simple_Job_Board_Meta_Box_Application_Status', 'sjb_meta_box_output'), 'jobpost_applicants', 'side');
    }

    /**
     * Save Meta Boxes.
     *
     * @since 2.1.0
     */
    public function save_meta_boxes( $post_id  ) {
        
        /**
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */
        
        // Check if nonce is set.
        if ( NULL == filter_input( INPUT_POST, 'jobpost_meta_box_nonce' ) ) {
            return;
        }
        
        // Verify that the nonce is valid.
        check_admin_referer( 'sjb_jobpost_meta_box', 'jobpost_meta_box_nonce' );

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if ( NULL != filter_input( INPUT_POST, 'post_type' ) && 'page' == filter_input( INPUT_POST, 'post_type') ) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        /**
         * @hooked sjb_save_jobpost_meta - 10
         * @hooked sjb_save_jobpost_meta - 20
         * @hooked sjb_save_jobpost_meta - 30
         * 
         * Save Jobpost Meta Box:
         * 
         * - Save job features meta box.
         * - Save job application meta box.
         * - Save job data meta box. 
         * 
         * @since   2.2.3
         * 
         * @param   int    $post_id    Post Id
         */
        do_action( 'sjb_save_jobpost_meta', $post_id );
    }
    
    /**
     * Save Application Status Meta Box.
     *
     * @since 2.5.0
     */
    public function save_applicants_meta_boxes( $post_id ) {
        
         /**
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */
        
        // Check if nonce is set.
        if ( NULL == filter_input( INPUT_POST, 'jobpostapp_meta_box_nonce' ) ) {
            return;
        }
        
        // Verify that the nonce is valid.
        check_admin_referer( 'sjb_jobpostapp_meta_box', 'jobpostapp_meta_box_nonce' );

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions.
        if ( NULL != filter_input( INPUT_POST, 'post_type' ) && 'page' == filter_input( INPUT_POST, 'post_type') ) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        /**
         * @hooked sjb_save_jobpost_meta - 10
         * 
         * Save Jobpost Meta Box:
         * 
         * - Save application status meta.
         * 
         * @since   2.5.0
         * 
         * @param   int    $post_id    Post Id
         */
        do_action( 'sjb_save_jobpost_applicants_meta', $post_id );
    }
}

new Simple_Job_Board_Meta_Boxes_Init();