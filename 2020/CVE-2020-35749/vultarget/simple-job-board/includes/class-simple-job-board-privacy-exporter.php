<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Privacy_Exporter Class
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * 
 * @since       2.6.0
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Privacy_Exporter {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.6.0
     */
    public function __construct() {

        // Hook - Register SJB Exporter Implementation to WP Core Exporter
        add_filter('wp_privacy_personal_data_exporters', array($this, 'register_exporter'));

        // Add applicant resumes to exported zip file.
        add_action('wp_privacy_personal_data_export_file_created', array($this, 'add_resume_to_zip'), 999, 4);
    }

    /**
     * Integrate applicant data eraser implementation in WP core eraser.
     * 
     * @since   2.6.0
     *
     * @param   array   $erasers List of eraser callbacks.
     * @return  array
     */
    public function register_exporter($exporter = array()) {

        $erasers['sjb-application-exporter'] = array(
            'exporter_friendly_name' => __('Application Exporter'),
            'callback' => array($this, 'applicant_data_exporter'),
        );

        return $erasers;
    }

    /**
     * Export applicant's data.
     * 
     * @since   2.6.0
     */
    public function applicant_data_exporter($email_address, $page) {
        global $wpdb;

        $done = FALSE;
        $page = (int) $page;
        $user = get_user_by('email', $email_address); // Check if user has an ID in the DB to load stored personal data.
        $data_to_export = array();


        // Get applicants assoicated with requested user email
        $applicants = $wpdb->get_results($wpdb->prepare(" 
        SELECT p.ID FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pt ON p.ID = pt.post_id WHERE pt.meta_value = %s AND p.post_type = %s AND p.post_status = %s", $email_address, 'jobpost_applicants', 'publish'));

        if (!empty($applicants)):
            foreach ($applicants as $applicant) {
                $data_to_export[] = array(
                    'group_id' => 'applicant_details',
                    'group_label' => __('Applicant Details', 'simple-job-board'),
                    'item_id' => 'applicant-' . intval($applicant->ID),
                    'data' => $this->get_applicant_personal_data(intval($applicant->ID)),
                );
            }
        endif;

        return array(
            'data' => $data_to_export,
            'done' => TRUE,
        );
    }

    /**
     * Get applicant details against his email ID.
     * 
     * @since   2.6.0
     * 
     * @param   int     $applicant_id   Applicant ID
     * @return  array   $data_to_export Data export array.
     */
    public function get_applicant_personal_data($applicant_id) {

        $keys = get_post_custom_keys($applicant_id);

        if (!empty($keys)):
            $data_to_export[] = array(
                'name' => __('Job Applied for', 'simple-job-board'),
                'value' => get_the_title($applicant_id),
            );
            foreach ($keys as $key):
                if (substr($key, 0, 7) == 'jobapp_') {
                    $count = 0;

                    if (!is_serialized(get_post_meta($applicant_id, $key, TRUE))) {
                        $data_to_export[] = array(
                            'name' => ucwords(str_replace('_', ' ', substr($key, 7))),
                            'value' => get_post_meta($applicant_id, $key, TRUE),
                        );
                    } else {
                        $values = maybe_unserialize(get_post_meta($applicant_id, $key, TRUE));
                        if (is_array($values)) {

                            foreach ($values as $val):
                                $val = $val;
                                if ($count > 1) {
                                    $val.= ',&nbsp';
                                }
                                $count--;
                            endforeach;

                            $data_to_export[] = array(
                                'name' => ucwords(str_replace('_', ' ', substr($key, 7))),
                                'value' => $val,
                            );
                        } else {
                            $data_to_export[] = array(
                                'name' => ucwords(str_replace('_', ' ', substr($key, 7))),
                                'value' => get_post_meta($applicant_id, $key, TRUE),
                            );
                        }
                    }
                }
                $count++;
            endforeach;
        endif;
        
        /**
         * Filter -> Modify export array data
         * 
         * @since   2.6.1
         */
        return apply_filters( 'sjb_personal_data_exporter', $data_to_export, $applicant_id );
    }

    /**
     * Add resume to exported zip.
     * 
     * @since       2.6.0
     * 
     * $archive_pathname        string  Export file path.
     * $archive_url             string  Export file url.
     * $html_report_pathname    string  HTML file path.
     * $request_id              int     Export request id.
     */
    function add_resume_to_zip($archive_pathname, $archive_url, $html_report_pathname, $request_id) {

        global $wpdb;

        // Get the request data.
        $user = wp_get_user_request_data($request_id);
        $email_address = sanitize_email($user->email);

        $applicants = $wpdb->get_results($wpdb->prepare(' 
        SELECT p.ID FROM wp_posts AS p INNER JOIN wp_postmeta AS pt ON p.ID = pt.post_id WHERE pt.meta_value = %s AND post_type = %s', $email_address, 'jobpost_applicants'));

        $zip = new ZipArchive;
        if (!empty($applicants)):

            foreach ($applicants as $applicant) {
                if (get_post_meta($applicant->ID, 'resume_path', TRUE)) {

                    $html_report_pathname = get_post_meta($applicant->ID, 'resume_path', TRUE);

                    // Resume Name
                    $filename = basename($html_report_pathname);

                    if (TRUE === $zip->open($archive_pathname, ZipArchive::CREATE)) {
                        if (!$zip->addFile($html_report_pathname, 'resume/' . $filename)) {
                            $error = __('Unable to add data to export file.');
                        }
                    }
                }

                // Fetch multiple Attachment add-on data 
                if ($files = get_post_meta($applicant->ID, 'attachments_meta', TRUE)) {
                    $count = count($files['file_name']);

                    for ($i = 0; $i < $count; $i++) {

                        if ('' != $files['file_name'][$i]) {
                            $file_path = $files['base_dir'] . '/' . esc_attr($files['file_name'][$i]);

                            if (TRUE === $zip->open($archive_pathname, ZipArchive::CREATE)) {
                                if (!$zip->addFile($file_path, 'resume/' . $files['file_name'][$i])) {
                                    $error = __('Unable to add data to export file.');
                                }
                            }
                        }
                    }
                }
            }

            $zip->close();
        endif;
    }

}

new Simple_Job_Board_Privacy_Exporter();
