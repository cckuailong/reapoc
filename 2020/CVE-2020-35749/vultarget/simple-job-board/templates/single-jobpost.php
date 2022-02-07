<?php
/**
 * The Template for displaying job details
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/Templates
 * @version     1.1.0
 * @since       2.2.0
 * @since       2.2.3   Enqueued Front Styles & Revised the HTML structure.
 * @since       2.2.4   Enqueued Front end Scripts.
 * @since       2.3.0   Added "sjb_archive_template" filter.
 */
get_header();

ob_start();
global $post;

/**
 * Enqueue Frontend Scripts.
 * 
 * @since   2.2.4
 */
do_action('sjb_enqueue_scripts');

/**
 * Hook -> sjb_before_main_content
 * 
 * @hooked sjb_job_listing_wrapper_start - 10 
 * - Output Opening div of Main Container.
 * - Output Opening div of Content Area.
 * 
 * @since   2.2.0
 * @since   2.2.3   Removed the content wrapper opening div.
 */
do_action( 'sjb_before_main_content' );


if (FALSE !== get_option('job_post_layout_settings')) {
    $jobpost_layout_option = get_option('job_post_layout_settings');
    if ('job_post_layout_version_one' === $jobpost_layout_option)
        $job_class = 'v1';

    if ('job_post_layout_version_two' === $jobpost_layout_option)
        $job_class = 'v2';
} else {
    $job_class = 'v1';
}

?>

<!-- Start Content Wrapper
================================================== -->
<div class="sjb-page">
    <div class="sjb-detail sjb-<?php echo $job_class ?>">
        <div class="list-data">
            <div class="<?php echo $job_class ?>">
                <?php
                while ( have_posts() ) : the_post();
                    /**
                     * Template -> Content Single Job Listing:
                     * 
                     * - Company Meta
                     * - Job Description 
                     * - Job Features
                     * - Job Application Form
                     */
                    get_simple_job_board_template('content-single-job-listing.php');
                endwhile;
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- ==================================================
End Content Wrapper -->

<?php
/**
 * Hook -> sjb_after_main_content
 *  
 * @hokoed sjb_job_listing_wrapper_end - 10
 * 
 * - Output Closing div of Main Container.
 * - Output Closing div of Content Area.
 * 
 * @since   2.2.0
 * @since   2.2.3   Removed the content wrapper closing div
 */
do_action('sjb_after_main_content');

$html_single = ob_get_clean();

/**
 * Modify the Jobs Archive Page Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_archive   Jobs Archive Page HTML.                   
 */
echo apply_filters('sjb_single_template', $html_single);

get_footer();