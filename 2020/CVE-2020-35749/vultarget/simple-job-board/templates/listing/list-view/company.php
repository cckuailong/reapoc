<?php
/**
 * The template for displaying company name in list view
 *
 * Override this template by copying it to yourtheme/simple_job_board/listing/list-view/company.php
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing/list-view
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_list_view_company_name_template" filter.
 */
ob_start();

// Company Name
if (sjb_get_the_company_name()) {
    sjb_the_company_name(' | <span class="company-name">', '</span>');
}

$html = ob_get_clean();

/**
 * Modify the Job Listing -> Company Name Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html   Company Name HTML.                   
 */
echo apply_filters( 'sjb_list_view_company_name_template', $html );