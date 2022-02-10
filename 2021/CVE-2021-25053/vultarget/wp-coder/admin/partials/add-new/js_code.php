<?php 
	/**
		* JS Code
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$js_code = array(
		'id'   => 'js_code',
		'name' => 'content_js',	
		'type' => 'textarea',
		'val' => isset( $param['content_js'] ) ? $param['content_js'] : '',	
	);
	$js_code_help = array ( 
		'text' => __('Enter your JS code', 'wpcoder'),
	);
	
?>

<div class="wow-container">
	<div class="wow-element">
		<?php _e('JS Code', 'wpcoder'); ?> <?php echo self::tooltip( $js_code_help ); ?>
		<?php echo self::option( $js_code ); ?>
	</div>	
</div>