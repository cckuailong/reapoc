<?php
/**
 * The template for displaying company tagline
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost/job-meta/company-tagline.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost/job-meta
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_job_meta_company_tagline_template" filter.
 */
ob_start();
?>

<!-- Start Company Tagline 
================================================== -->
<?php if (sjb_get_the_company_tagline()): ?>
    <div class="col-sm-12">
        <p class="company-tagline"><?php sjb_the_company_tagline(); ?></p>
    </div>
<?php endif; ?>
<!-- ==================================================
End Company Tagline  -->

<?php
$html_company_tagline = ob_get_clean();

/**
 * Modify the Job Meta - Company Tagline Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_company_tagline   Company Tagline HTML.          
 */
echo apply_filters( 'sjb_job_meta_company_tagline_template', $html_company_tagline );