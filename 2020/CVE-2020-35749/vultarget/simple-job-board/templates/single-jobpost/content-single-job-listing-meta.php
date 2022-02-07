<?php
/**
 * Single view Job Meta Box
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost/content-single-job-listing-meta.php
 * 
 * Hooked into single_job_listing_start priority 20
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/Templates
 * @version     2.0.0
 * @since       2.0.0
 * @since       2.3.0   Added "sjb_job_meta_template" filter.
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();
global $post;

/**
 * Fires on job detail page before comapny meta  
 *                   
 * @since   2.1.0                   
 */
do_action('single_job_listing_meta_before');
?>

<!-- Start Company Meta
================================================== -->
<header>
    <div class="row">            
        <?php
        /**
        * Template -> Title:
        * 
        * - Job Title
        */
        get_simple_job_board_template( 'single-jobpost/job-meta/job-title.php' );
        
        /**
         * Template -> Company Logo:
         * 
         * - Display Company Logo.
         */
        get_simple_job_board_template( 'single-jobpost/job-meta/company-logo.php' );
        ?>
        
        <div class="col-md-11 col-sm-10 header-margin-top">
            <div class="row">
                
                <?php
                /**
                 * Template -> Company Name:
                 * 
                 * - Display Company Name.
                 */
                get_simple_job_board_template( 'single-jobpost/job-meta/company-name.php' );

                /**
                 * Template -> Job Type:
                 * 
                 * - Display Job Type.
                 */
                get_simple_job_board_template( 'single-jobpost/job-meta/job-type.php' );

                /**
                 * Template -> Job Location:
                 * 
                 * - Display Job Location.
                 */
                get_simple_job_board_template( 'single-jobpost/job-meta/job-location.php' );

                /**
                 * Template -> Job Posted Date:
                 * 
                 * - Display Job Posted Date.
                 */
                get_simple_job_board_template( 'single-jobpost/job-meta/job-posted-date.php' );
                ?>
            </div>
        </div>        
        <?php
        /**
         * Template -> Company Tagline:
         * 
         * - Display Company Tagline.
         */
        get_simple_job_board_template( 'single-jobpost/job-meta/company-tagline.php' );
        ?>
    </div>
</header>
<!-- ==================================================
End Company Meta -->

<?php
/**
 * Fires on job detail page after comapny meta  
 *                   
 * @since   2.1.0                   
 */
do_action('single_job_listing_meta_after');

$html_job_meta = ob_get_clean();

/**
 * Modify the Job Meta Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_job_meta   Job Meta HTML.                   
 */
echo apply_filters( 'sjb_job_meta_template', $html_job_meta );