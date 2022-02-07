<?php
/**
 * The template for displaying job loactaion in list view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/list-view/location.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/list-view
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_list_view_job_location_template" filter.
 * @since       2.4.0   Revised whole HTML structure
 */
ob_start();
?>

<!-- Start Job's Location
================================================== -->
<div class="col-md-2 col-sm-4 col-xs-12">
    <?php if ($job_location = sjb_get_the_job_location()) {
        ?>
        <div class="job-location"><i class="fa fa-map-marker"></i><?php sjb_the_job_location(); ?></div>
    <?php } ?>
</div>
<!-- ==================================================
End Job's Location -->

<?php
$html = ob_get_clean();

/**
 * Modify the Job Listing -> Job Location Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Job Location HTML.                   
 */
echo apply_filters('sjb_list_view_job_location_template', $html);