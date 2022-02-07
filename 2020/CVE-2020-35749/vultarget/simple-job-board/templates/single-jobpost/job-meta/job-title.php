<?php
/**
 * The template for displaying job title
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost/job-meta/job-title.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost/job-meta
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_job_meta_job_title_template" filter.
 */
ob_start();
?>

<!-- Start Job Title 
================================================== -->
<?php 
if( 'sjb-layout' === get_option( 'job_board_pages_layout' ) || FALSE === get_option( 'job_board_pages_layout' ) ):
?>

<div class="col-md-12">
    <div class="job-detail">        
        <?php the_title('<h3><span class="job-title">', '</span></h3>'); ?>            
    </div>
</div>
<!-- End Job Title 
================================================== -->

<?php
endif;
$html_title = ob_get_clean();

/**
 * Modify the Job Title Template.
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_title   Job Title HTML.                   
 */
echo apply_filters( 'sjb_job_meta_job_title_template', $html_title );