<?php

/**
 * The template for displaying company name
 *
 * Override this template by copying it to yourtheme/simple_job_board/v2/single-jobpost/job-meta/company-name.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost/job-meta
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_job_meta_company_name_template" filter.
 */
ob_start();

$class = ('with-logo' === get_option('job_board_jobpost_content')) ? 'job-info-margin' : '';

$without_company = ((!sjb_get_the_company_name()) || (!sjb_get_the_company_website() )) ? 'job-without-company' : '';

?>

<!-- Start Company Name 
================================================== -->
<div class="sjb-with-logo">
    <div class="job-info <?php echo $class; ?> <?php echo $without_company; ?>">
        <?php
        /**
         * Template -> Title:
         * 
         * - Job Title
         */
        get_simple_job_board_template('single-jobpost/job-meta/job-title.php');
        ?>
        <h4>
            <?php
            // Company Name -> Linked with Company Website
            if (sjb_get_the_company_name()) {
                if ($website = sjb_get_the_company_website()) :
            ?>
                    <span><i class="fa fa-building"></i><a class="website" href="<?php echo esc_url($website); ?>" target="_blank" rel="nofollow"><?php sjb_the_company_name(); ?></a></span>
            <?php
                else :
                    sjb_the_company_name('<span> <i class="fa fa-building"></i>', '</span>');
                endif;
            }

            /**
             * Fires after Job heading on job listing page.
             * 
             * @since   2.2.3
             */
            do_action('sjb_job_listing_heading_after');
            ?>
        </h4>
        <?php
        /**
         * Template -> Company Tagline:
         * 
         * - Display Company Tagline.
         */
        get_simple_job_board_template('single-jobpost/job-meta/company-tagline.php');
        ?>
    </div>
</div>
<!-- End Company Name 
================================================== -->

<?php
$html_company_name = ob_get_clean();

/**
 * Modify the Company Name Template.
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_company_name   Company Name HTML.                   
 */
echo apply_filters('sjb_job_meta_company_name_template', $html_company_name);
