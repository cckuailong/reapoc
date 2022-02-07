<?php
/**
 * Show filters for jobs
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/Templates
 * @version     2.0.0
 * @since       2.1.0
 * @since       2.2.0   Added @hooks for dropdowns & column classes.
 * @since       2.2.3   Added more @hooks for dropdowns & broke filter's template structure.
 * @since       2.4.0   Revised whole HTML template.
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

ob_start();

// Get Shortcode Attributes 
$atts = empty($atts) ? '' : $atts;

/**
 * Fires before filters on job listing page.
 * 
 * @since    2.1.0
 */
do_action('simple_job_board_job_filters_before', $atts);

$class = ( apply_filters('sjb_is_search_filters', sjb_is_keyword_search() || sjb_is_category_filter() || sjb_is_type_filter() || sjb_is_location_filter()) ) ? 'sjb-filters' : '';
?>

<!-- Start Job Filters
================================================== -->
<div class="<?php echo apply_filters('filters_form_class', $class); ?>">
    <?php
    // Get Current Page Slug  
    $page_slug = sjb_get_slugs();
    $slug = ( get_option('permalink_structure') ) ? $page_slug : '';

    /**
     * Job Filters Form 
     * 
     * - Keywords Search.
     * - Job Category Filter.
     * - Job Type Filter.
     * - Job Location Filter.
     * 
     * Search jobs by category, job location, job type and keywords.
     */
    ?>
    <form class="filters-form" action="<?php echo apply_filters('sjb_job_filters_form_action', esc_url(home_url('/')) . $slug); ?>" method="<?php echo apply_filters('sjb_job_filters_form_method', 'get'); ?>">
        <div class="row">
            <?php
            /**
             * Fires before keyword search on job listing page.
             * 
             * @since   2.1.0
             */
            do_action('simple_job_board_job_filters_start', $atts);

            /**
             * Template -> Keyword Search:
             * 
             * - Keyword Search.
             */
            get_simple_job_board_template('search/keyword-search.php', array('atts' => $atts));

            /**
             * Fires before category dropdown on job listing page
             * 
             * @since   2.2.0
             */
            do_action('simple_job_board_job_filters_dropdowns_start', $atts);

            /**
             * Template -> Category Filter:
             * 
             * - Display Category Filter Dropdown.
             */
            get_simple_job_board_template('search/category-filter.php', array('atts' => $atts));

            /**
             * Fires after "Category" dropdown on job listing page
             * 
             * @since   2.2.3
             */
            do_action('sjb_category_filter_dropdown_after', $atts);

            /**
             * Template -> Type Filter:
             * 
             * - Display Job Type Filter Dropdown.
             */
            get_simple_job_board_template('search/type-filter.php', array('atts' => $atts));

            /**
             * Fires after "Job Type" dropdown on job listing page
             * 
             * @since   2.2.3
             */
            do_action('sjb_job_type_filter_dropdown_after', $atts);

            /**
             * Template -> Location Filter:
             * 
             * - Display Job Location Filter Dropdown.
             */
            get_simple_job_board_template('search/location-filter.php', array('atts' => $atts));

            /**
             * Fires after job location dropdown on job listing page.
             * 
             * @since   2.2.0
             */
            do_action('simple_job_board_job_filters_dropdowns_end', $atts);

            /**
             * Template -> Search Button:
             * 
             * - Display Search Button.
             */
            get_simple_job_board_template('search/search-btn.php');
            ?>
        </div>
    </form>
</div>
<!-- ==================================================
End Job Filters -->

<?php
/**
 * Fires after job filters on job listing page.
 * 
 * @since   2.1.0
 */
do_action('simple_job_board_job_filters_after', $atts);

$html_job_filters = ob_get_clean();

/**
 * Modify the Job Filters Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_job_filters   Job Filters HTML.                   
 */
echo apply_filters('sjb_job_filters_template', $html_job_filters);