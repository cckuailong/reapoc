<?php
/**
 * Template for displaying job type filter
 *
 * Override this template by copying it to yourtheme/simple_job_board/search/type-filter.php
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/search
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_job_type_filter_template" filter.
 * @since       2.4.0   Revised whole HTML structure
 */
ob_start();

// Check For Settings Option and the Term Existance
if (sjb_is_type_filter()) {    
    $selected_jobtype = ( NULL != filter_input( INPUT_GET, 'selected_jobtype' ) ) ? sanitize_text_field( filter_input( INPUT_GET, 'selected_jobtype' ) ) : FALSE;

    /**
     * Creating list on non-empty job type
     * 
     * Job Type Selectbox
     */
    // Job Type Arguments
    $jobtype_args = array(
        'show_option_none'  => esc_html__( 'Job Type', 'simple-job-board' ),
        'orderby'           => 'NAME',
        'order'             => 'ASC',
        'hide_empty'        => 0,
        'echo'              => FALSE,
        'name'              => 'selected_jobtype',
        'id'                => 'jobtype',
        'class'             => 'form-control',
        'selected'          => $selected_jobtype,
        'hierarchical'      => TRUE,
        'taxonomy'          => 'jobpost_job_type',
        'value_field'       => 'slug',
    );

    // Display or retrieve the HTML dropdown list of job type     
    $jobtype_select = wp_dropdown_categories(apply_filters('sjb_job_type_filter_args', $jobtype_args, $atts));
    ?> 

    <!-- Job Type Filter -->
    <div class="sjb-search-job-type <?php echo apply_filters('sjb_job_type_filter_class', 'col-md-3'); ?>">
        <div class="form-group">
            <?php
            if (NULL != $jobtype_select) {
                echo $jobtype_select;
            }
            ?>
        </div>
    </div>
    <?php
}

$html_type_filter = ob_get_clean();

/**
 * Modify the Job Type Filter Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_type_filter   Job Type Filter HTML.                   
 */
echo apply_filters( 'sjb_job_type_filter_template', $html_type_filter );