<?php
/**
 * Job Listing Wrapper Start
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing
 * @version     1.0.0
 * @since       2.4.3
 */

ob_start();
?>
<div class="sjb-page">
<!-- Start: Jobs Listing Wrapper -->

<?php

$html_listing_start = ob_get_clean();

/**
 * Modify Job Listing End Template
 *                                       
 * @since   2.4.0
 * 
 * @param   html    $html_listing_end   Job Listing End HTML          .
 */
echo apply_filters( 'sjb_listing_wrapper_start_template', $html_listing_start );