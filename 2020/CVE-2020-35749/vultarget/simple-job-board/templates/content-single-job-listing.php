<?php
/**
 * Single view Job Fetures
 * 
 * The template for displaying job content in the single-jobpost.php template
 * 
 * Override this template by copying it to yourtheme/simple_job_board/content-single-job-listing.php
 * 
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/Templates
 * @version     1.0.0
 * @since       2.1.0
 * @since       2.2.3   Added the_content function.
 * @since       2.3.0   Added "sjb_single_job_listing_template" filter.
 */
ob_start();
?>

<!-- Start Job Details
================================================== -->

<?php
/**
 * single_job_listing_start hook
 *
 * @hooked job_listing_meta_display - 20 ( Job Listing Company Meta )
 * 
 * @since   2.1.0
 */
do_action('sjb_single_job_listing_start');
?>

<div class="job-description">
    
    <?php
    /**
     * Display the post content.
     * 
     * The "the_content" is used to filter the content of the job post. Also make other plugins shortcode compatible with job post editor. 
     */
    the_content();
    ?>
</div>
<div class="clearfix"></div>

<?php
/**
 * single-job-listing-end hook
 * 
 * @hooked job_listing_features - 20 ( Job Features )
 * @hooked job_listing_application_form - 30 ( Job Application Form )
 * 
 * @since   2.1.0
 */
do_action('sjb_single_job_listing_end');
?>
<!-- ==================================================
End Job Details -->

<?php
$html = ob_get_clean();

/**
 * Modify the Single Job Listing Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Single Job Listing HTML.                   
 */
echo apply_filters('sjb_single_job_listing_template', $html);