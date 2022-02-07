<?php
/**
 * Template for displaying searh button
 *
 * Override this template by copying it to yourtheme/simple_job_board/search/search-btn.php
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/search
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_search_btn_template" filter.
 * @since       2.4.0   Revised whole HTML structure
 */
ob_start();

if( apply_filters( 
        'sjb_is_search_btn',
        sjb_is_filter_dropdowns() ||
        sjb_is_keyword_search()
        ) 
    )
{
    // Search Button 
    $search_button = '<div class="sjb-search-button ' . apply_filters('sjb_filters_button_class', 'col-md-2') . '">'
        . '<input class="btn-search btn btn-primary" value="&#xf002;" type="submit">'
        . '</div>';
    echo apply_filters( 'sjb_job_filters_search_button', $search_button );
}

$html_search_btn = ob_get_clean();

/**
 * Modify the Search Button Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_search_btn   Search Button HTML.                   
 */
echo apply_filters( 'sjb_search_btn_template', $html_search_btn );