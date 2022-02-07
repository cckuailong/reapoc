<?php
/**
 * Loader image for job application.
 *
 * Override this template by copying it to yourtheme/simple_job_board/single-jobpost/loader.php
 * 
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.7.0
 */
ob_start();

// Get Settings Loader Image
if (FALSE !== get_option('sjb_loader_image')) {
    $image_url = get_option('sjb_loader_image');
} else {
    $image_url = plugin_dir_url( dirname( dirname(__FILE__) ) ) . 'public/images/loader.gif';
}

if( $image_url ) {
?>

<!-- Start Loader Overlay 
================================================== -->
<div class="sjb-loading">
    <div class="sjb-overlay">
    </div>
    <!-- loading image -->
    <div class="sjb-loader-wrapper">
        <img class="sjb-loader" src="<?php echo esc_url($image_url); ?>" />
    </div>
</div>
<!-- ==================================================
End Loader Overlay  -->
<?php
}

$html_loader = ob_get_clean();

/**
 * Modify the Job Meta - Loader Overlay Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_loader   Loader Overlay HTML.                   
 */
echo apply_filters( 'sjb_job_meta_loader_template', $html_loader );