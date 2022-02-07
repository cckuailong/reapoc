<?php
/**
 * Job Detail Page Wrapper Start
 * 
 * Override this template by copying it to yourtheme/simple_job_board/v1/single-jobpost/single-job-wrapper-start.php
 * 
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.5.0
 */
if (FALSE !== get_option('job_post_layout_settings')) {
    $jobpost_layout_option = get_option('job_post_layout_settings');
    if ('job_post_layout_version_one' === $jobpost_layout_option)
        $job_class = 'v1';

    if ('job_post_layout_version_two' === $jobpost_layout_option)
        $job_class = 'v2';
} else {
    $job_class = 'v1';
}
ob_start();
?>
<!-- Start: Jobs Listing Wrapper -->
<div class="sjb-page">
    <div class="sjb-detail">
        <div class="list-data">
            <div class="<?php echo $job_class ?>">
            <?php

            $job_startwrapper = ob_get_clean();

            /**
             * Modify Job Detail Page Wrapper Start Template
             *                                       
             * @since   2.5.0
             * 
             * @param   html    $job_startwrapper   Starting HTML          .
             */
            echo apply_filters( 'sjb_single_job_wrapper_start_template', $job_startwrapper );