<?php
/**
 * The template for displaying job title and company name in list view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/list-view/job-title-company.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/list-view
 * @version     1.0.0
 * @since       2.4.0
 */
ob_start();
?>
<div class="col-md-5">
    <div class="job-info">
        <h4>
            <a href="<?php the_permalink(); ?>">
                <?php
                /**
                 * Template -> Title:
                 * 
                 * - Job Title
                 */
                get_simple_job_board_template('listing/list-view/title.php');

                /**
                 * Template -> Company:
                 * 
                 * - Company Name
                 */
                get_simple_job_board_template('listing/list-view/company.php');

                /**
                 * Fires after Job heading on job listing page.
                 * 
                 * @since   2.2.3
                 */
                do_action('sjb_job_listing_heading_after');
                ?>
            </a>
        </h4>
    </div>
</div>
<?php
$html = ob_get_clean();

/**
 * Modify the Job Listing -> Job Title & Company Name Template
 *                                       
 * @since   2.4.0
 * 
 * @param   html    $html   Job Title & Company Name HTML.                   
 */
echo apply_filters( 'sjb_list_view_job_title_company_template', $html );