<?php
/**
 * The template for displaying apply now button in Grid view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/grid-view/apply.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/grid-view
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_grid_view_apply_now_template" filter.
 * @since       2.4.0   Revised whole HTML structure
 */
ob_start();

global $post;
?>

<!-- Start apply now button
================================================== -->
<div class="sjb-aapply-button">
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
echo apply_filters('sjb_grid_view_apply_now_template', $html);