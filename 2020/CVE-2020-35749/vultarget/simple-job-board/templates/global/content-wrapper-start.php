<?php
/**
 * Content Wrappers Start
 *
 * Override this template by copying it to yourtheme/simple_job_board/global/content-wrapper-start.php
 * 
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/global 
 * @version     1.1.0
 * @since       2.2.0
 */
if (!defined('ABSPATH')) { exit; } // Exit if accessed directly

ob_start();

// Get Current Theme Name 
$template = get_option('template');

// Appearance Settings -> User Defined Container Class
if (get_option('job_board_container_class')) {
    $container_class = get_option('job_board_container_class');
    $container_class = str_replace(',', ' ', $container_class);
} else {
    $container_class = 'container sjb-container';
}

// Get Container Id
if (get_option('job_board_container_id')) {
    $container_ids = explode( " ", get_option('job_board_container_id'));
    $container_id = $container_ids[0];
} else {
    $container_id = 'container';
}

switch ($template) {
    case 'twentyeleven' :
        echo '<div id="primary"><div role="main"><div class="sjb-archive-wrapper">';
        break;
    case 'twentytwelve' :
        echo '<div id="primary"><div id="content" role="main" class="twentytwelve">';
        break;
    case 'twentythirteen' :
        echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
        break;
    case 'twentyfourteen' :
        echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc"><div class="sjb-archive-wrapper">';
        break;
    case 'twentyfifteen' :
        echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc"><div class="sjb-archive-wrapper">';
        break;
    case 'twentysixteen' :
        echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main"><div class="sjb-archive-wrapper">';
        break;
    default :
        echo '<div class="' . esc_attr( $container_class ) . '" id="' . esc_attr( $container_id ) . '"><div id="content" class="sjb-content" role="main">';
        break;
}

$html_wrapper_start = ob_get_clean();

/**
 * Modify the Content Wrapper Start Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_wrapper_start   Content Wrapper Start HTML.                   
 */
echo apply_filters('sjb_content_wrapper_start_template', $html_wrapper_start);