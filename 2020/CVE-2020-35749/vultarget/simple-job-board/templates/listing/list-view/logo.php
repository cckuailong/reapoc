<?php
/**
 * The template for displaying company logo in list view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/list-view/logo.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/list-view
 * @version     2.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_list_view_company_logo_template" filter.
 * @since       2.4.0   Revised whole HTML template
 */
ob_start();
?>

<!-- Start Company Logo
================================================== -->
<?php
if ('logo-detail' === get_option('job_board_listing') || 'without-detail' === get_option('job_board_listing')) {
    ?>
<div class="col-md-1 col-sm-2 hidden-xs">
    <div class="company-logo">
        <a href="<?php the_permalink(); ?>"><?php sjb_the_company_logo(); ?></a>
    </div>
</div>
<?php
}
?>

<!-- ==================================================
End Company Logo -->

<?php
$html = ob_get_clean();

/**
 * Modify the Job Listing -> Company Logo Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Company Logo HTML.                   
 */
echo apply_filters('sjb_list_view_company_logo_template', $html);