<?php 
	/**
		* HTML Code
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$html_code = array(
		'id'   => 'html_code',
		'name' => 'content_html',	
		'type' => 'textarea',
		'val' => isset( $param['content_html'] ) ? $param['content_html'] : '',	
	);
	$html_code_help = array ( 
		'text' => __('Enter your HTML code', 'wpcoder'),
	);
	
?>

<div class="wow-container">
	<div class="wow-element">
		<?php _e('HTML Code', 'wpcoder'); ?> <?php echo self::tooltip( $html_code_help ); ?>
		<?php echo self::option( $html_code ); ?>
	</div>	
</div>