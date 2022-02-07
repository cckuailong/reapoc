<?php
/**
 * Content Wrapper End
 *
 * Override this template by copying it to yourtheme/simple_job_board/global/content-wrapper-end.php
 * 
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/global
 * @version     1.1.0
 * @since       2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {	exit; } // Exit if accessed directly

ob_start();
 
$template = get_option( 'template' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div></div></div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div></div>';
		break;
	case 'twentysixteen' :
		echo '</div></main></div>';
		break;
	default :
		echo '</div></div>';
		break;
}

$html_wrapper_end = ob_get_clean();

/**
 * Modify the Content Wrapper End Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_wrapper_end   Content Wrapper End HTML.                   
 */
echo apply_filters( 'sjb_content_wrapper_end_template', $html_wrapper_end );