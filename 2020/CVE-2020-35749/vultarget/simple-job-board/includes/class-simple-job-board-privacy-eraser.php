<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Privacy_Eraser Class
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * 
 * @since       2.6.0
 * @since       2.7.0   Hook added to 
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Privacy_Eraser {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.6.0
     */
    public function __construct() {
        
        // Hook - Register SJB Eraser Implementation to WP Core Eraser
        add_filter('wp_privacy_personal_data_erasers', array($this, 'register_erasers'));
    }

    /**
     * Integrate applicant data eraser implementation in WP core eraser.
     * 
     * @since   2.6.0
     *
     * @param   array   $erasers List of eraser callbacks.
     * @return  array
     */
    public function register_erasers($erasers = array()) {

        $erasers['applicant-eraser'] = array(
            'eraser_friendly_name' => __('Applicant Data Eraser'),
            'callback' => array($this, 'applicant_data_eraser'),
        );

        return $erasers;
    }

    /**
     * Erase applicant's data.
     * 
     * @since   2.6.0
     */
    public function applicant_data_eraser($email_address, $page) {
        global $wpdb;
        
        $page = (int) $page;
        $erasure_enabled = sjb_string_to_bool(get_option('sjb_erasure_request_removes_applicant_data', 'no'));       
        
        // Response Array
        $response = array(
            'items_removed' => FALSE,
            'items_retained' => FALSE,
            'messages' => array(),
            'done' => TRUE,
        );
        
        // Get applicants assoicated with requested user email
        $applicants = $wpdb->get_results($wpdb->prepare(" 
        SELECT p.ID FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pt ON p.ID = pt.post_id WHERE pt.meta_value = %s AND p.post_type = %s AND p.post_status = %s", $email_address, 'jobpost_applicants', 'publish'));

        if (!empty($applicants)):
            foreach ($applicants as $applicant) {
                if ( $erasure_enabled ) {

                    $this->remove_applicant_personal_data( $applicant->ID );

                    /* Translators: %s Application ID. */
                    $response['messages'][] = sprintf(__('Removed personal data from application  %s.', 'simple-job-board'), $applicant->ID);
                    $response['items_removed'] = TRUE;
                } else {
                    
                    /* Translators: %s Application ID. */
                    $response['messages'][] = sprintf(__('Personal data within application %s has been retained.', 'simple-job-board'), $applicant->ID);
                    $response['items_retained'] = TRUE;                    
                }
            }
        endif;

        return $response;
    }

    /**
     * Remove applicant personal details.
     * 
     * @since       2.6.0
     * 
     * $applicant   Object  Applicant Object
     */
    public function remove_applicant_personal_data( $applicant_id ) {

        $keys = get_post_custom_keys($applicant_id);

        // Applicant Name
        if (NULL != $keys):
            foreach ($keys as $key) {
                if ('jobapp_' === substr($key, 0, 7)) {
                    update_post_meta($applicant_id, $key, '[deleted]');
                }
            }
            
            // Delete resume & update data in DB
            if ( '' != get_post_meta($applicant_id, 'resume_path', TRUE)) {
                unlink(get_post_meta($applicant_id, 'resume_path', TRUE));
                update_post_meta($applicant_id, 'resume', 'Resume[deleted]');
            }
        endif;
        
        /**
         * Action Hook -> Do eraser action after SJB data eraser completion
         * 
         * @since   2.7.0
         */
        do_action( 'sjb_personal_data_erasers', $applicant_id );
    }

}

new Simple_Job_Board_Privacy_Eraser();