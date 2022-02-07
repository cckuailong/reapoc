<?php
/**
 * The template for displaying job logo in grid view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/grid-view/logo.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/grid-view/
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_grid_view_company_logo_template" filter.
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();

if ('logo-detail' === get_option('job_board_listing') || 'without-detail' === get_option('job_board_listing')) {
    ?>

    <!-- Start Jobs Logo
    ================================================== -->
    <div class="col-sm-3 hidden-xs">
        <div class="company-logo">
            <a href="<?php the_permalink(); ?>">
                <?php sjb_the_company_logo(); ?>
            </a>
        </div>
    </div>
    <!-- ==================================================
    End Jobs Logo-->
    
    <?php
}

$html = ob_get_clean();

/**
 * Modify the Job Listing -> Company Logo Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Company Logo HTML.                   
 */
echo apply_filters('sjb_grid_view_company_logo_template', $html);