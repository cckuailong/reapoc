<?php
/**
 * Job Listing Start
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/listing
 * @version     2.0.0
 * @since       2.1.0
 * @since       2.4.0   Revised whole HTML template
 */

ob_start();
?>

<div class="sjb-listing">
    <?php
        $view = get_option('job_board_listing_view');
        $class = ( 'list-view' === $view ) ? 'list-view' : 'grid-view';
    ?>
    <!-- start Jobs Listing: List View -->
    <div class="<?php echo esc_attr( $class ); ?>">
        
    <?php
    $html_listing_start = ob_get_clean();

    /**
     * Modify Job Listing Start Template
     *                                       
     * @since   2.4.0
     * 
     * @param   html    $html_listing_start   Job Listing Start HTML          .
     */
    echo apply_filters( 'sjb_listing_start_template', $html_listing_start );