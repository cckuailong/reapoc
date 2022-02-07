<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Notifications Class
 *
 * This class is used to notify Admin, HR and Applicant on application submission.
 * 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       1.0.0
 * @since       2.2.2   Added "sjb_notification_template" filter.
 * @since       2.2.3   Added "sjb_applicant_details_notification" filter.
 * @since       2.3.0   Revised the Admin, HR and Applicant notification templates.
 * @since       2.4.0   Added Email Reply-to & From Parameters, & Revised Inputs & Outputs, Sanitization & Escaping
 * @since       2.4.5   Added $post_id in HR & Admin Email-to filters' parameters.
 *                      - Breakdown the email notification templates into their respective functions.
 *                      - Introduced "sjb_admin_email_template" filter.
 *                      - Introduced "sjb_hr_email_template" filter.
 *                      - Introduced "sjb_applicant_email_template" filter.
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes 
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Notifications {

    /**
     * Admin Notification
     *
     * @since  1.0.0
     * 
     * @param   $post_id  Post ID
     * @return  void 
     */
    public static function admin_notification($post_id) {

        // Applied job title
        $job_title = get_the_title($post_id);
        $applicant_post_keys = get_post_custom_keys($post_id);

        // Applicant Email
        $applicant_email = self::applicant_details('mail', $post_id);

        // Applicant Name
        $applicant_name = self::applicant_details('name', $post_id);

        // Admin Email Address
        $admin_email = ( FALSE !== get_option( 'settings_admin_email' ) ) ? get_option( 'settings_admin_email' ) : get_option( 'admin_email' );
        $to = apply_filters('sjb_admin_notification_to', esc_attr( $admin_email ) , $post_id);
        $subject = apply_filters('sjb_admin_notification_sbj', sprintf(esc_html__('Applicant Resume Received %s ', 'simple-job-board'), html_entity_decode( $job_title )), $job_title, $post_id);

        // Email Header: Reply-to & From Parameters        
        $headers[] = apply_filters( 'sjb_admin_notification_from', 'From: ' . get_bloginfo('name') . ' <' . esc_attr( $admin_email ) . '>', $post_id );

        if (!empty($applicant_name) && !empty($applicant_email)) {
            $headers[] = 'Reply-To: ' . $applicant_name . ' <' . $applicant_email . '>';
        }

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $message = self::job_notification_templates($post_id, 'Admin');
        $attachment = apply_filters('sjb_admin_notification_attachment', '', $post_id);
        wp_mail($to, $subject, $message, $headers, $attachment);
    }

    /**
     * HR Notification
     *
     * @since  1.0.0
     * 
     * @param   $post_id  Post ID
     * @return  void 
     */
    public static function hr_notification( $post_id ) {

        // Applied job title
        $job_title = get_the_title($post_id);

        // Applicant Email
        $applicant_email = self::applicant_details('mail', $post_id);

        // Applicant Name
        $applicant_name = self::applicant_details('name', $post_id);

        $to = apply_filters('sjb_hr_notification_to', get_option('settings_hr_email'), $post_id);
        $subject = apply_filters('sjb_hr_notification_sbj', sprintf(esc_html__('Applicant Resume Received %s ', 'simple-job-board'), html_entity_decode( $job_title )), $job_title, $post_id);
        $message = self::job_notification_templates($post_id, 'HR');
        
        // Admin Email
        $admin_email = ( FALSE !== get_option( 'settings_admin_email' ) ) ? get_option( 'settings_admin_email' ) : get_option( 'admin_email' );

        // Email Header: Reply-to & From Parameters
        $headers[] = apply_filters( 'sjb_hr_notification_from', 'From: ' . get_bloginfo('name') . ' <' . esc_attr( $admin_email ) . '>', $post_id );

        if (!empty($applicant_name) && !empty($applicant_email)) {
            $headers[] = 'Reply-To: ' . $applicant_name . ' <' . $applicant_email . '>';
        }

        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $attachment = apply_filters('sjb_hr_notification_attachment', '', $post_id);
        if ('' != $to)
            wp_mail($to, $subject, $message, $headers, $attachment);
    }

    /**
     * Applicant Notification
     *
     * @since   1.0.0
     * 
     * @param   $post_id  Post ID
     * @return  void 
     */
    public static function applicant_notification($post_id) {

        // Applied job title
        $job_title = get_the_title($post_id);

        // Applicant Email        
        $applicant_email = apply_filters( 'sjb_applicant_email', self::applicant_details('mail', $post_id), $post_id );

        $subject = apply_filters('sjb_applicant_notification_sbj', sprintf(esc_html__('Your Resume Received for Job %s ', 'simple-job-board'), html_entity_decode( $job_title )), $job_title, $post_id);
        $message = self::job_notification_templates($post_id, 'applicant');
        
        // Admin Email
        $admin_email = ( FALSE !== get_option( 'settings_admin_email' ) ) ? get_option( 'settings_admin_email' ) : get_option( 'admin_email' );
        
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        
        // Email Header: Reply-to & From Parameters
        $from_email = apply_filters('sjb_applicant_noreply_email', 'noreply@' . $sitename, $post_id);
        $headers[] = apply_filters( 'sjb_applicant_notification_from', 'From: ' . get_bloginfo('name') . ' <' . esc_attr( $admin_email ) . '>', $post_id );
        $headers[] = 'Reply-To: ' . get_bloginfo('name') . '<' . $from_email . '>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        
        $attachment = apply_filters('sjb_applicant_notification_attachment', '', $post_id);
        
        // Validate Applicant Email
        if ( isset( $applicant_email ) && is_email( $applicant_email ) )
            wp_mail( $applicant_email, $subject, $message, $headers, $attachment );
    }

    /**
     * Email Template
     *
     * @since  1.0.0
     * 
     * @param  int      $post_id                Post ID
     * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)
     * @return string   $message                Email Template
     */
    public static function job_notification_templates($post_id, $notification_receiver) {

        $message = self::email_start_template( $post_id, $notification_receiver );

        if ('HR' === $notification_receiver) {
            $message .= self::hr_email_template($post_id, $notification_receiver );
        } elseif ('Admin' === $notification_receiver) {

            $message .= self::admin_email_template( $post_id, $notification_receiver );
        } else {
            $message .= self::applicant_email_template( $post_id, $notification_receiver );
        }
        
        $message .= self::email_end_template( $post_id, $notification_receiver );

        /**
         * Hook -> Notification Message.
         * 
         * @since  2.2.0
         * @since  2.2.3   Added $post_id and $notification_receiver parameters in filter.
         * 
         * @param  string  $message                Email Template
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver 
         */
        return apply_filters( 'sjb_notification_template', $message, $post_id, $notification_receiver );
    }

    /**
     * Email Start Template
     *
     * @since  2.4.5
     * 
     * @param  int      $post_id                Post ID
     * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)
     * @return string   $message                Email Start Template
     */
    public static function email_start_template( $post_id, $notification_receiver ) {
        $header_title = ( 'applicant' != $notification_receiver ) ? esc_html__('Job Application', 'simple-job-board') : esc_html__('Job Application Acknowledgement', 'simple-job-board');
        $message = '<div style="width:700px; margin:0 auto;  border: 1px solid #95B3D7;font-family:Arial;">'
                . '<div style="border: 1px solid #95B3D7; background-color:#95B3D7;">'
                . ' <h2 style="text-align:center;">' . $header_title . '</h2>'
                . ' </div>'
                . '<div  style="margin:10px;">'
                . '<p>' . date("Y/m/d") . '</p>'
                . '<p>';

        /**
         * Modify Email Start Template
         *
         * @since  2.4.5
         * 
         * @param  string   $message                Email Start Template
         * @param  int      $post_id                Post ID
         * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)         * 
         */
        return apply_filters('sjb_email_start_template', $message, $notification_receiver, $post_id);
    }
    
    /**
     * Email End Template
     *
     * @since  2.4.5
     */
    public static function email_end_template( $post_id, $notification_receiver ) {
        $message = '</div>'
                . '</div>';
        
        /**
         * Modify Email End Template
         *
         * @since  2.4.5
         * 
         * @param  string   $message                Email End Template
         * @param  int      $post_id                Post ID
         * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)         * 
         */
        return apply_filters('sjb_email_end_template', $message, $post_id, $notification_receiver);
    }
    
    /**
     * Admin Email Template
     *
     * @since  2.4.5
     * 
     * @param  int      $post_id                Post ID
     * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)
     * @return string   $message                Admin Email Template
     */
    public static function admin_email_template( $post_id, $notification_receiver ) {

        // Applied Job Title
        $job_title = get_the_title($post_id);

        // Applicant Name       
        $applicant_name = self::applicant_details('name', $post_id);
        
        $admin = esc_html__('Admin', 'simple-job-board');

        $message = sprintf( esc_html__('Hi %s', 'simple-job-board'), $admin ) . ',</p>';

        $message .= '<p>' . esc_html__('I am applying for the job post', 'simple-job-board') . ' <b>' . esc_attr($job_title) . '</b> ' . esc_html__('with interest. I have attached my resume with the job application. I have also filled out the required details.', 'simple-job-board') . '</p>';

        /**
         * Hook -> Applicant details.
         * 
         * Add applicant's details in notification template.
         *
         * @since  2.2.3   
         * 
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver 
         * @return string  $message                Message Template          
         */
        $message = apply_filters('sjb_applicant_details_notification', $message, $post_id, $notification_receiver);

        $message .= '<p>' . esc_html__('I look forward to hearing from you.', 'simple-job-board') . '</p>'
                . esc_html__('Warm Regards,', 'simple-job-board') . '<br>';

        if ( NULL != $applicant_name ):
            $message.= $applicant_name . '';
        endif;
        
        /**
         * Modify Admin Email Template
         * 
         * @since  2.4.5   
         * 
         * @param  string  $message                Admin Email Template
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver                  
         */
        return apply_filters( 'sjb_admin_email_template', $message, $post_id, $notification_receiver );
    }
    
    /**
     * HR Email Template
     *
     * @since  2.4.5
     * 
     * @param  int      $post_id                Post ID
     * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)
     * @return string   $message                HR Email Template
     */
    public static function hr_email_template( $post_id, $notification_receiver) {

        // Site URL 
        $site_url = get_option('home');

        // Applied Job Title
        $job_title = get_the_title($post_id);

        // Applicant Name
        $applicant_name = self::applicant_details('name', $post_id);
        $hr = esc_html__('HR', 'simple-job-board');

        $message = sprintf( esc_html__('Dear %s,', 'simple-job-board'), $hr );
        $message .= '</p>'
                . '<p>';

        if (NULL != $applicant_name):
            $message.= '<b>' . $applicant_name . '</b> ';
        else:
            $message.= esc_html__('Applicant', 'simple-job-board') . ' ';
        endif;

        $message .= esc_html__('has applied against your job opening', 'simple-job-board') . ' <b>' . esc_attr($job_title) . '</b> ' . esc_html__('at', 'simple-job-board') . ' <a href="' . esc_url($site_url) . '">' . get_bloginfo('name') . '</a> '
                . esc_html__("Please login to your account to download the CV or check from the applicant's list from the dashboard.", "simple-job-board") . '</p>';

        /**
         * Hook -> Applicant details.
         * 
         * Add applicant's details in notification template.
         *
         * @since  2.2.3   
         * 
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver 
         * @return string  $message                Message Template          
         */
        $message = apply_filters('sjb_applicant_details_notification', $message, $post_id, $notification_receiver);

        $message .= '<br>' . esc_html__('Best Regards,', 'simple-job-board') . '<br>'
                . esc_html__('Admin', 'simple-job-board') . '<br>';
        
        /**
         * Modify HR Email Template
         * 
         * @since  2.4.5   
         * 
         * @param  string  $message                HR Email Template
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver                  
         */
        return apply_filters( 'sjb_hr_email_template', $message, $post_id, $notification_receiver );
    }
    
    /**
     * Applicant Email Template
     *
     * @since  2.4.5
     * 
     * @param  int      $post_id                Post ID
     * @param  string   $notification_receiver  Notification Receiver (Admin or HR or || Applicant)
     * @return string   $message                Applicant Email Template
     */
    public static function applicant_email_template($post_id, $notification_receiver ) {

        // Site URL 
        $site_url = get_option('home');

        // Applied Job Title
        $job_title = get_the_title($post_id);
        $applicant_name = self::applicant_details( 'name', $post_id );

        // Applicant Email Template.            
        $message = esc_html__('Hi', 'simple-job-board');

        if (NULL != $applicant_name):
            $message .= ' ' . $applicant_name . ',';
        else:
            $message .= ' ' . esc_html__('Applicant', 'simple-job-board') . ',';
        endif;

        $message .= '<p>' . esc_html__('Your application for the position of', 'simple-job-board') . '<b> ' . esc_attr($job_title) . '</b> ' . esc_html__('at', 'simple-job-board') . ' <a href="' . esc_url($site_url) . '">' . get_bloginfo('name') . '</a> ' . esc_html__('has been successfully submitted. You will hear back from', 'simple-job-board') . ' <a href="' . esc_url($site_url) . '">' . get_bloginfo('name') . '</a> ' . esc_html__('based on their evaluation of your CV.', 'simple-job-board') . '</p>'
                . '<p>' . esc_html__('Good Luck!', 'simple-job-board') . '</p>'
                . esc_html__('Best Regards,', 'simple-job-board') . '<br>'
                . esc_html__('Admin', 'simple-job-board');
        
        /**
         * Modify Applicant Email Template
         * 
         * @since  2.4.5   
         * 
         * @param  string  $message                Applicant Email Template
         * @param  int     $post_id                Post Id
         * @param  string  $notification_receiver  Notification Receiver                  
         */
        return apply_filters( 'sjb_applicant_email_template', $message, $post_id, $notification_receiver );
    }    
    
    /**
     * Applicant Details
     *
     * @since  2.4.5
     * 
     * @param  string   $paramter
     * @param  int      $post_id    Post Id
     * @return string   $applicant_details   Applicant Details
     */
    public static function applicant_details($paramter, $post_id) {

        $applicant_post_keys = get_post_custom_keys($post_id);
        $applicant_details = '';

        // Search Applicant Name
        if (NULL != $applicant_post_keys):
            foreach ($applicant_post_keys as $key) {
                if ('jobapp_' === substr($key, 0, 7)) {
                    $place = strpos($key, $paramter);
                    if (!empty($place)) {
                        $applicant_details = get_post_meta($post_id, $key, TRUE);
                        break;
                    }
                }
            }
        endif;

        return $applicant_details;
    }

}

new Simple_Job_Board_Notifications();