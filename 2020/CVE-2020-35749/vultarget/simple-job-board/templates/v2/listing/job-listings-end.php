<?php
/**
 * Job Listing End
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing
 * @version     2.0.0
 * @since       2.1.0
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();
?>

    </div> <!-- end Jobs Listing: List View -->
</div>
<!-- End: Jobs Listing -->

<?php
$html_listing_end = ob_get_clean();

/**
 * Modify Job Listing End Template
 *                                       
 * @since   2.4.0
 * 
 * @param   html    $html_listing_end   Job Listing End HTML          .
 */
echo apply_filters('sjb_listing_end_template', $html_listing_end);