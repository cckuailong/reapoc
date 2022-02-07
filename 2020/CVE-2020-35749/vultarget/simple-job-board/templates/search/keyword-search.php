<?php
/**
 * Template for displaying keyword search
 *
 * Override this template by copying it to yourtheme/simple_job_board/search/keyword-search.php
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/search
 * @version     1.0.0
 * @since       2.2.3
 * @since       2.3.0   Added "sjb_keyword_search_template" filter.
 * @since       2.4.0   Revised whole HTML structure.
 */
ob_start();

if ( sjb_is_keyword_search() ) { 
    $class = sjb_is_filter_dropdowns() ? 'col-md-12' : 'col-md-10';
    ?>

    <!-- Keywords Search-->    
    <div class="sjb-search-keywords <?php echo $class; ?>">
        <div class="form-group">
            <?php            
            $search_keyword = ( NULL != filter_input( INPUT_GET, 'search_keywords') ) ? filter_input( INPUT_GET, 'search_keywords' ) : '';

            // Append Query string With Page ID When Permalinks are not Set
            if (!get_option('permalink_structure') && !is_home() && !is_front_page()) {
                ?>
                <input type="hidden" value="<?php echo get_the_ID(); ?>" name="page_id" >
            <?php } ?>
            <label class="sr-only" for="keywords"><?php esc_html_e('Keywords', 'simple-job-board'); ?></label>
            <input type="text" class="form-control" value="<?php echo esc_attr( strip_tags( $search_keyword ) ); ?>" placeholder="<?php _e('Keywords', 'simple-job-board'); ?>" id="keywords" name="search_keywords">
        </div>
    </div>
    <?php
}

$html_keyword_search = ob_get_clean();

/**
 * Modify the Keyword Search Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_keyword_search   Keyword Search HTML.                   
 */
echo apply_filters( 'sjb_keyword_search_template', $html_keyword_search );