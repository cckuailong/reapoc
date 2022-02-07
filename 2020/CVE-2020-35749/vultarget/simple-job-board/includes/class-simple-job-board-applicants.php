<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Applicants Class
 * 
 * This is used to display the applicant details in WP admin. It also display the 
 * applicant data & resume. 
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       1.0.0
 * @since       2.3.0   Added "sjb_resume_link_after" and "sjb_applicant_name" hooks.
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Applicants {

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {

        // Hook -> Job Applicants Data
        add_action('edit_form_after_title', array($this, 'jobpost_applicants_detail_page_content'));
    }

    /**
     * Create Detail Page for Applicants
     * 
     * @since   1.0.0
     */
    public function jobpost_applicants_detail_page_content() {
        global $post;

        if (!empty($post) and 'jobpost_applicants' === $post->post_type):

            $keys = get_post_custom_keys($post->ID);

            /**
             * Fires before displaying the applicant details
             * 
             * @since 2.2.0
             */
            do_action('sjb_applicants_details_before', $post->ID);
            ?>
            <div class="wrap"><div id="icon-tools" class="icon32"></div>
                <?php
                // Applicant Name
                if (NULL != $keys):
                    foreach ($keys as $key) {
                        if ('jobapp_' === substr($key, 0, 7)) {
                            $place = strpos($key, 'name');
                            if (!empty($place)) {
                                $applicant_name = get_post_meta($post->ID, $key, TRUE);
                                $applicant_name = apply_filters('sjb_applicant_name', $applicant_name);
                                break;
                            }
                        }
                    }
                endif;
                ?>

                <!-- Applicant's Name & Resume in Admin Area -->
                <h3>
                    <?php
                    echo isset($applicant_name) ? $applicant_name : '';

                    // Applicant Resume
                    if (in_array('resume', $keys) && 'Resume[deleted]' == get_post_meta($post->ID, 'resume', TRUE)) {
                        $resume = '&nbsp; &nbsp; <small>Resume[deleted]</small>';
                    } elseif (in_array('resume', $keys) && '/' != get_post_meta($post->ID, 'resume', TRUE)) {
                        $resume = '&nbsp; &nbsp; <small><a href="' . esc_url(get_admin_url() . 'post.php?post=' . intval($post->ID) . '&action=edit&resume_id=' . intval($post->ID)) . '" rel="nofollow">' . esc_html__('Resume', 'simple-job-board') . '</a></small>';
                    } else {
                        $resume = '';
                    }

                    echo apply_filters('sjb_applicant_resume', $resume, $post->ID);
                    ?>                    
                </h3>
                <?php
                /**
                 * Action -> Fires after Resume Link
                 * 
                 * @since   2.3.0
                 */
                do_action('sjb_resume_link_after', $post->ID);
                ?>

                <!-- Applicant's Detail in Admin Area -->
                <table class="widefat striped">
                    <?php
                    /**
                     * Fires at start of applicant details
                     * 
                     * @since 2.2.0
                     */
                    do_action('sjb_applicants_details_start', $post->ID);
                    $parent_id = wp_get_post_parent_id($post->ID);                          
                    foreach ($keys as $key):
                        if (substr($key, 0, 7) == 'jobapp_') {
                            
                            $val = get_post_meta( $parent_id, $key, TRUE );
                            $val = maybe_unserialize($val);
                            $label = isset($val['label']) ? $val['label'] : ucwords(str_replace('_', ' ', substr($key, 7)));

                            if (!is_serialized(get_post_meta($post->ID, $key, TRUE))) {
                                echo '<tr><td>' . esc_attr( $label ) . '</td><td>' . get_post_meta($post->ID, $key, TRUE) . '</td></tr>';
                            } else {
                                $values = maybe_unserialize(get_post_meta($post->ID, $key, TRUE));
                                if (is_array($values)) {
                                    echo '<tr><td>' . esc_attr( $label ) . '</td><td>';
                                    $count = sizeof($values);

                                    foreach ($values as $val):
                                        echo esc_attr($val);
                                        if ($count > 1) {
                                            echo ',&nbsp';
                                        }
                                        $count--;
                                    endforeach;
                                    echo '</td></tr>';
                                } else {
                                    echo '<tr><td>' . esc_attr( $label ) . '</td><td>' . get_post_meta($post->ID, $key, TRUE) . '</td></tr>';
                                }
                            }
                        }
                    endforeach;

                    /**
                     * Fires at the end of applicant details
                     * 
                     * @since 2.2.0
                     */
                    do_action('sjb_applicants_details_end', $post->ID);
                    ?>

                </table>
            </div>

            <?php
            /**
             * Fires after displaying the applicant details
             * 
             * @since 2.2.0
             */
            do_action('sjb_applicants_details_after', $post->ID);
            ?>

            <h2><?php esc_html_e('Application Notes', 'simple-job-board'); ?></h2>

            <?php
            /**
             * Fires after displaying the applicant details
             * 
             * @since 2.2.0 
             */
            do_action('sjb_applicantion_notes', $post->ID);

        endif;
    }

}

new Simple_Job_Board_Applicants();