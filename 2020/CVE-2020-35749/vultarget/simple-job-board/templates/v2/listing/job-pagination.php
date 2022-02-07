<?php
/**
 * Pagination - Show numbered pagination for jobs
 * 
 * Override this template by copying it to yourtheme/simple_job_board/v2/listing/listing-wrapper-start.php
 *
 * @author  PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing
 * @version     1.0.0
 * @since       2.2.0
 * @since       2.4.0   Revised Pagination HTML Structure
 */
ob_start();
global $wp_rewrite;

// Get Shortcode Attributes 
$job_query = empty( $job_query ) ? '' : $job_query;

/**
 * Job listing pagination
 * 
 * Show pagiantion after displaying
 */
$job_query->query_vars['paged'] > 1 ? $current = $job_query->query_vars['paged'] : $current = 1;

if(( NULL != filter_input(INPUT_GET, 'selected_category')  || NULL != filter_input(INPUT_GET, 'selected_jobtype') || NULL != filter_input(INPUT_GET, 'selected_location') || filter_input(INPUT_GET, 'search_keywords') )) {
    if ($wp_rewrite->using_permalinks()) {
        $url = explode('?', get_pagenum_link(999999999)); // Get URL without Query String
        $pagination_args['base'] = str_replace(999999999, '%#%', esc_url($url[0]));
    }
    
} else {
    $pagination_args['base'] = str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999)));
}

// Pagination Arguments
$pagination_args = array(
    //'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),    
    'format'      => '?paged=%#%',    
    'total'     => $job_query->max_num_pages,
    'current'   => $current,
    'show_all'  => FALSE,
    'next_text' => '<i class="fa fa-angle-right"></i>',
    'prev_text' => '<i class="fa fa-angle-left"></i>',
    'type'      => 'array',
    'end_size'  => 4,
    'mid_size'  => 4,
);

/**
 * Modify query string.
 *  
 * Remove query "page" argument from permalink
 */
if (is_archive()) {
    $url = explode('?', get_pagenum_link(1));
    
    if ($wp_rewrite->using_permalinks()) {
        $pagination_args['base'] = user_trailingslashit(trailingslashit(remove_query_arg('page', esc_url($url[0] ))) . '?page=%#%/', 'paged');
    }

    if (!empty($job_query->query_vars['s'])) {
        $pagination_args['add_args'] = array('s' => get_query_var('s'));
    }
}

$pagination = apply_filters('sjb_pagination_links_default_args', $pagination_args);

// Retrieve paginated link for job posts
$pages = paginate_links( $pagination );

if ( is_array( $pages ) ) {
    $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php
                foreach ( $pages as $page ) {
                    echo "<li class='list-item'>$page</li>";
                }
            ?>    
        </ul>
        <div class="clearfix"></div>
    </nav>
    
    <?php
}

$html_pagination = ob_get_clean();

/**
 * Modify the Job Meta - Company Tagline Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_company_tagline   Company Tagline HTML.          
 */
echo apply_filters( 'sjb_pagination_template', $html_pagination );