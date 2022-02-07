<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Meta_Box_Application_Status Class
 * 
 * This meta box is designed for storing application's status.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.5.0
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/partials/meta-boxes
 * @author      PressTigers <support@presstigers.com>
 */
class Simple_Job_Board_Meta_Box_Application_Status {

    /**
     * Add job data meta box options.
     * 
     * @since   2.5.0
     */
    public static function sjb_meta_box_output() {

        global $post;

        // Add a nonce field so we can check for it later.
        wp_nonce_field('sjb_jobpostapp_meta_box', 'jobpostapp_meta_box_nonce');

        $crt_status = get_post_meta( $post->ID, 'sjb_jobapp_status', TRUE) ? get_post_meta($post->ID, 'sjb_jobapp_status', TRUE) : 
            apply_filters('sjb_default_status', 'not_any');
        
        // Application Statuses
        $app_statuses = apply_filters( 'job_application_statuses', array(
            'not_any'         => __('Not Any', 'simple-job-board'),
            'new'         => __('New', 'simple-job-board'),
            'in-process'  => __('In Process', 'simple-job-board'),
            'shortlisted' => __('Shortlisted', 'simple-job-board'),
            'rejected'    => __('Rejected', 'simple-job-board'),
            'selected'    => __('Selected', 'simple-job-board'),
        ) );
        ?>

        <div class="job-application-metabox">
            <p><b><?php _e('Status', 'simple-job-board'); ?></b></p>
            <select name="sjb_jobapp_status" class="app-status">
                <?php
                foreach ($app_statuses as $key => $status) {
                    if ($key == $crt_status) {
                        $selected = 'selected=selected';
                    } else {
                        $selected = '';
                    }
                    echo '<option value="' . $key . '"' . $selected . '>' . $status . '</option>';
                }
                ?>
            </select>
        </div>
        <?php
    }

    /**
     * Save job application meta box.
     * 
     * @since   2.5.0
     * 
     * @param   int     $post_id    Post id
     * @return  void
     */
    public static function sjb_save_jobpost_applicants_meta($post_id) {

        $POST_data = filter_input_array(INPUT_POST);       

        foreach ( $POST_data as $key => $value ) {
            if (strstr($key, 'sjb_jobapp_status')) {
                update_post_meta( $post_id, sanitize_key( $key ), $value );
            }
        }
    }
}