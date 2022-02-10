<?php 
	/**
		* CSS Code
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$css_code = array(
		'id'   => 'css_code',
		'name' => 'content_css',	
		'type' => 'textarea',
		'val' => isset( $param['content_css'] ) ? $param['content_css'] : '',	
	);
	$css_code_help = array ( 
		'text' => __('Enter your CSS code', 'wpcoder'),
	);
	
?>

<div class="wow-container">
	<div class="wow-element">
		<?php _e('CSS Code', 'wpcoder'); ?> <?php echo self::tooltip( $css_code_help ); ?>
		<?php echo self::option( $css_code ); ?>
	</div>	
</div>