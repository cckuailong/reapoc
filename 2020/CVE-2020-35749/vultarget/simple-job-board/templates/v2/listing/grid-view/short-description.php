<?php
/**
 * The template for displaying job short description in gird view.
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/grid-view/short-description.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/grid-view/
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_grid_view_short_description_template" filter.
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();

if ('logo-detail' === get_option('job_board_listing') || 'without-logo' === get_option('job_board_listing')) {
    ?>
    
    <!-- Start Job's Short Description 
    ================================================== -->
    <div class="job-description">
        <?php echo sjb_get_the_excerpt(); ?>
    </div>
    <!-- ==================================================
    End Job's Short Description  -->

    <?php
}

$html = ob_get_clean();

/**
 * Modify the Job Listing -> Short Description Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Short Description HTML.                   
 */
echo apply_filters('sjb_grid_view_short_description_template', $html);