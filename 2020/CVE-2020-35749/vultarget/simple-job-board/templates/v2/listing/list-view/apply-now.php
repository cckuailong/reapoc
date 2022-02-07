<?php
/**
 * The template for displaying apply now button in list view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/list-view/apply-now.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/list-view
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_list_view_apply_now_template" filter.
 * @since       2.4.0   Revised whole HTML structure
 */
ob_start();

global $post;
?>

<!-- Start apply now button
================================================== -->
<div class="col-md-4 col-sm-4 col-xs-12 sjb-apply-now-btn">
	<?php echo sjb_get_the_apply_now_btn(); ?>
</div>
<!-- ==================================================
End apply now button -->

<?php
$html = ob_get_clean();

/**
 * Modify the Apply Now button -> Apply Now Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Apply Now HTML.                   
 */
echo apply_filters('sjb_list_view_apply_now_template', $html);