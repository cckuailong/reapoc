<?php
/**
 * Template for displays the company logo
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost/job-meta/company-logo.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost/job-meta
 * @version     2.0.1
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_job_meta_company_logo_template" filter.
 * @since       2.4.0   Revised whole HTML template
 * @since       2.5.0   Fix the logo issue when option is not available.
 */
ob_start();

if ('with-logo' === get_option('job_board_jobpost_content') || FALSE === get_option('job_board_jobpost_content') ) {
    ?>

    <!-- Start Company Logo 
    ================================================== -->
    <div class="col-md-1 col-sm-2">
        <div class="company-logo">
            <?php
            if ($website = sjb_get_the_company_website()):
                ?>
                <a href="<?php echo esc_url($website); ?>"  target="_blank" rel="nofollow"><?php sjb_the_company_logo(); ?></a>
                <?php
            else:
                sjb_the_company_logo();
            endif;
            ?>
        </div>
    </div>    
    <!-- ==================================================
    End Company Logo  -->

    <?php
}

$html_logo = ob_get_clean();

/**
 * Modify the Job Meta - Company Logo Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_logo   Job Meta HTML.                   
 */
echo apply_filters('sjb_job_meta_company_logo_template', $html_logo);