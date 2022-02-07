<?php

/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   2.8.0
 * @package Simple_Job_Board/SJB_BLOCK
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 2.8.0
 */

/**
 * Handler for SJB Listing block
 * @param $atts
 *
 * @return string
 */
function sjb_joblisting_block_handler($atts) {
    return sjb_joblisting_blocks_display($atts['sjb_layout'], $atts['numberofposts'], $atts['order'], $atts['jobsearch']);
}

/**
 * Output the SJB Job Listing
 *
 * @param string $sjb_layout The Job Listing layout View:Grid
 * @param int $numberofposts Number of Posts
 * @param string $order Job Listing Order Ascending:Descending
 * @param boolean $jobsearch Use to Disable/Enable Job Search
 *
 * @return Mixed
 */
function sjb_joblisting_blocks_display($sjb_layout, $numberofposts, $order, $jobsearch) {
    /**
     * Enqueue Frontend Scripts.
     *
     * @since   2.8.0
     */
    do_action('sjb_enqueue_scripts');

    ob_start();

    global $job_query;

    //Default Args
    $default_args = array(
        'posts' => $numberofposts,
        'category' => '',
        'type' => '',
        'location' => '',
        'keywords' => '',
        'order' => $order,
        'search' => $jobsearch,
    );

    // Get paged variable.
    if (get_query_var('paged')) {
        $paged = (int) get_query_var('paged');
    } elseif (get_query_var('page')) {
        $paged = (int) get_query_var('page');
    } else {
        $paged = 1;
    }

    // WP Query Default Arguments
    $args = apply_filters(
            'sjb_block_output_jobs_args', array(
        'post_status' => 'publish',
        'posts_per_page' => esc_attr($default_args['posts']),
        'post_type' => 'jobpost',
        'paged' => $paged,
        'order' => esc_attr($default_args['order']),
            )
    );

    // Merge $arg array on each $_GET element
    $args['jobpost_category'] = (!empty($_GET['selected_category']) && -1 != $_GET['selected_category']) ? esc_attr($_GET['selected_category']) : esc_attr($default_args['category']);
    $args['jobpost_job_type'] = (!empty($_GET['selected_jobtype']) && -1 != $_GET['selected_jobtype']) ? esc_attr($_GET['selected_jobtype']) : esc_attr($default_args['type']);
    $args['jobpost_location'] = (!empty($_GET['selected_location']) && -1 != $_GET['selected_location']) ? esc_attr($_GET['selected_location']) : esc_attr($default_args['location']);

    $args['s'] = (null != filter_input(INPUT_GET, 'search_keywords')) ? sanitize_text_field($_GET['search_keywords']) : '';

    // Job Query
    $job_query = new WP_Query($args);

    /**
     * Fires before listing jobs on job listing page.
     *
     * @since   2.8.0
     */
    do_action('sjb_job_filters_before');

    /**
     * Template -> Job Listing Wrapper Start:
     *
     * - SJB Starting of Job Listing Wrapper
     */
    get_simple_job_board_template('listing/listing-wrapper-start.php');

    if ('false' != strtolower($default_args['search']) && !empty($default_args['search'])):

        /**
         * Template -> Job Filters:
         *
         * - Keywords Search.
         * - Job Category Filter.
         * - Job Type Filter.
         * - Job Location Filter.
         *
         * Search jobs by keywords, category, location & type.
         */
        get_simple_job_board_template('job-filters.php', array('per_page' => $default_args['posts'], 'order' => $default_args['order'], 'categories' => $default_args['category'], 'job_types' => $default_args['type'], 'location' => $default_args['location'], 'keywords' => $default_args['keywords']));
    endif;

    /**
     * Template -> Job Listing Start:
     *
     * - SJB Starting of Job List
     */
    get_simple_job_board_template('listing/job-listings-start.php');

    /**
     * Fires before listing jobs on job listing page.
     *
     * @since   2.8.0
     */
    do_action('sjb_job_listing_before');

    if ($job_query->have_posts()):
        global $counter, $post_count;
        $counter = 1;
        $post_count = $job_query->post_count;

        while ($job_query->have_posts()): $job_query->the_post();

            // Display the user defined job listing view
            if ('grid-view' === $sjb_layout) {
                get_simple_job_board_template('content-job-listing-grid-view.php');
            } else {
                get_simple_job_board_template('content-job-listing-list-view.php');
            }
        endwhile;

        /**
         * Template -> Pagination:
         *
         * - Add Pagination to Resulted Jobs.
         */
        get_simple_job_board_template('listing/job-pagination.php', array('job_query' => $job_query));
    else:

        /**
         * Template -> No Job Found:
         *
         * - Display Message on No Job Found.
         */
        get_simple_job_board_template_part('listing/content-no-jobs-found');
    endif;

    wp_reset_postdata();

    /**
     * Fires after listing jobs on job listing page.
     *
     * @since   2.8.0
     */
    do_action('sjb_job_listing_after');

    /**
     * Template -> Job Listing End:
     *
     * - SJB Ending of Job List.
     */
    get_simple_job_board_template('listing/job-listings-end.php');

    /**
     * Template -> Job Listing Wrapper End:
     *
     * - SJB Endding of Job Listing Wrapper
     */
    get_simple_job_board_template('listing/listing-wrapper-end.php');

    $html = ob_get_clean();

    /**
     * Filter -> Modify the Job Listing Shortcode
     *
     * @since   2.8.0
     *
     * @param   HTML    $html    Job Listing HTML Structure.
     */
    return apply_filters('sjb_job_listing_block', $html);
}

function sjb_shortcode_block_cgb_block_assets() {


    // Register block styles for both frontend + backend.
    wp_register_style(
            'sjb_shortcode_block-cgb-style-css', // Handle.
            plugins_url('dist/blocks.style.build.css', dirname(__FILE__)), // Block style CSS.
            array('wp-editor'), // Dependency to include the CSS after it.
            null// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
    );

    // Register block editor script for backend.
    wp_register_script(
            'sjb_shortcode_block-cgb-block-js', // Handle.
            plugins_url('/dist/blocks.build.js', dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components'), // Dependencies, defined above. 
            filemtime(plugin_dir_path(__DIR__) . 'dist/blocks.build.js'), // Version: filemtime — Gets file modification time.
            true// Enqueue the script in the footer.
    );

    // Register block editor styles for backend.
    wp_register_style(
            'sjb_shortcode_block-cgb-block-editor-css', // Handle.
            plugins_url('dist/blocks.editor.build.css', dirname(__FILE__)), // Block editor CSS.
            array('wp-edit-blocks'), // Dependency to include the CSS after it.
            null// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
    );

    // WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
    wp_localize_script(
            'sjb_shortcode_block-cgb-block-js', 'cgbGlobal', // Array containing dynamic data for a JS Global.
            [
        'pluginDirPath' => plugin_dir_path(__DIR__),
        'pluginDirUrl' => plugin_dir_url(__DIR__),
            // Add more data here that you want to access from `cgbGlobal` object.
            ]
    );

    if (is_admin()) {

        // Enqueue Font Awesome Styles
        wp_enqueue_style('sjb-editor-font-awesome', plugins_url('dist/font-awesome.min.css', dirname(__FILE__)), array(), '4.7.0', 'all');
    }

    /**
     * Register Gutenberg block on server-side.
     *
     * Register the block on server-side to ensure that the block
     * scripts and styles for both frontend and backend are
     * enqueued when the editor loads.
     *
     * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
     * @since 2.8.0
     */
    register_block_type(
            'cgb/block-sjb-shortcode-block', array(
        // Enqueue blocks.style.build.css on both frontend & backend.
        'style' => 'sjb_shortcode_block-cgb-style-css',
        // Enqueue blocks.build.js in the editor only.
        'editor_script' => 'sjb_shortcode_block-cgb-block-js',
        // Enqueue blocks.editor.build.css in the editor only.
        'editor_style' => 'sjb_shortcode_block-cgb-block-editor-css',
        'items' => array('type' => 'integer'),
        'render_callback' => 'sjb_joblisting_block_handler',
        'attributes' => [
            'sjb_layout' => [
                'default' => 'list',
                'type' => 'string',
            ],
            'numberofposts' => [
                'default' => 15,
                'type' => 'integer',
            ],
            'order' => [
                'default' => 'DESC',
                'type' => 'string',
            ],
            'jobsearch' => [
                'default' => true,
                'type' => 'boolean',
            ],
        ],
            )
    );
}

// Hook: Block assets.
add_action('init', 'sjb_shortcode_block_cgb_block_assets');