<?php
/**
 * Job Detail Page Wrapper Start
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.5.0
 */

ob_start();
?>
<!-- Start: Jobs Listing Wrapper -->
<div class="sjb-page">
    <div class="sjb-detail">
        <div class="list-data">
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