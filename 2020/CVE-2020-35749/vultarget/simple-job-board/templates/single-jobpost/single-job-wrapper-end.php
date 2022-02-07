<?php
/**
 * Job Detail Page Wrapper End
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.5.0
 */

ob_start();?>

            </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- End: Jobs Detail Page Wrapper -->

<?php

$job_endwrapper = ob_get_clean();

/**
 * Modify Job Detail Page Wrapper End Template
 *                                       
 * @since   2.5.0
 * 
 * @param   html    $job_endwrapper   Wrapper Enclosing HTML          .
 */
echo apply_filters( 'sjb_single_job_wrapper_end_template', $job_endwrapper );