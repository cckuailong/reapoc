<?php
/**
 * Displayed when no jobs are found matching the current query
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/content-no-jobs-found.php
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing
 * @version     1.0.0
 * @since       2.1.0
 * @since       2.4.0   Revised the whole HTML template
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

ob_start();

// Get Current Page Slug  
$page_slug = sjb_get_slugs();
$slug = ( get_option('permalink_structure') ) ? $page_slug : '';

echo '<div class="no-job-listing"><p>' . esc_html__('No jobs found.', 'simple-job-board') . '</p>';

if ( ( NULL != filter_input( INPUT_GET, 'selected_category' ) || NULL != filter_input( INPUT_GET, 'selected_jobtype' ) || NULL != filter_input( INPUT_GET, 'selected_location' ) || filter_input( INPUT_GET, 'search_keywords' ) ) ) {
    echo '<p><a href="' . esc_url(home_url('/')) . $slug . '" class="btn btn-primary">' . __( 'Back to Jobs Page', 'simple-job-board') . '</a></p></div>';
} else {
   echo '</div>'; 
}

$html = ob_get_clean();

/**
 * Modify No Job Found Template. 
 *                                       
 * @since   2.4.0
 * 
 * @param   html    $html   No Job Found HTML.                   
 */
echo apply_filters('sjb_no_jobs_found_template', $html);