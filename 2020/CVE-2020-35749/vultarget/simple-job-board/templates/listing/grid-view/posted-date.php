<?php
/**
 * The template for displaying job post date in grid view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/grid-view/posted-date.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/grid-view/
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_grid_view_posted_date_template" filter.
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();
?>

<!-- Start Job's Post Date
================================================== -->
<div class="col-sm-12">
    <?php if ($job_posting_time = sjb_get_the_job_posting_time()) {
        ?>
        <div class="job-date"><i class="fa fa-calendar-check-o"></i><?php printf(__('Posted %s ago', 'simple-job-board'), sjb_get_the_job_posting_time() ); ?></div>
    <?php } ?>
</div>
<!-- ==================================================
End Job's Post Date -->

<?php
$html = ob_get_clean();

/**
 * Modify the Job Listing -> Job Posted Date Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Job Posted Date HTML.                   
 */
echo apply_filters( 'sjb_grid_view_posted_date_template', $html );