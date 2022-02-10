<?php if ( ! defined( 'ABSPATH' ) ) exit;
	/**
		* Main Page
		*
		* @package    Lead_Generation
		* @subpackage  
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	$logo = plugin_dir_url( __FILE__ ).'image/icon.png.';
?>
<style>
	.about-wrap .wow-badge {
		position: absolute;
		top: 0;
		right: 0;
	}

	.wow-badge {
		background: url(<?php echo esc_url( $logo );?>) center 15px no-repeat #1f9ef8;
		background-size: 100px 100px;
		color: #ffffff;
		font-size: 14px;
		text-align: center;
		font-weight: 600;
		margin: 5px 0 0;
		padding-top: 120px;
		height: 40px;
		display: inline-block;
		width: 140px;
		text-rendering: optimizeLegibility;
		box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
	}

	.wow-subscribe {
		padding: 5px 7px;
		border-radius: 5px;
		border: 1px solid #ccc;
		text-decoration: none;
		font-size: 14px;
		line-height: 14px;
		color: #999;
	}

	.wow-subscribe::before {
		font-family: dashicons;
		font-size: 12px;
		line-height: 12px;
	}

	.button-pro {
		vertical-align: top;
		display: inline-block;
		text-decoration: none;
		font-size: 13px;
		line-height: 26px;
		height: 28px;
		margin: 0;
		padding: 0 10px 1px;
		cursor: pointer;
		border-width: 0;
		border-style: solid;
		-webkit-appearance: none;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		white-space: nowrap;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		color: #fff !important;
		background: #37c781;
	}

	.button-pro:hover {
		background: #303030;
	}

	.button-pro:active {
		background: #303030;
	}
	.wow-thank-you {
		color: #777777;
		font-style: italic;
	}
	.about-wrap.full-width-layout {
		max-width: 100%;
	}
</style>


<div class="wrap about-wrap full-width-layout">
	
	<h1><?php esc_attr_e( 'Welcome', 'wpcoder' ); ?> </h1>
	
	<p class="about-text">		
	<?php esc_attr_e( 'Congratulations! You are about to use one of the plugins from Wow-Company.', 'wpcoder' ); ?> </p>
	<p>
		<a href="https://www.facebook.com/wowaffect/" class="wow-subscribe" target="_blank">Stay in touch <span class="dashicons dashicons-facebook-alt"></span></a> 
	</p>
	<span  class="wow-badge">Wow-Company</span>
	
	<?php
		$current = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'wow-plugins';
		$tabs = array(										
		'wow-plugins'  => __( 'Plugins', 'wpcoder' ),
		);
		
		echo '<h2 class="nav-tab-wrapper wp-clearfix">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' .esc_attr( $class ) . '" href="?page=wow-company&tab=' . esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
		}
		echo '</h2>';
		
		echo '<div class="stem-content">';
		include ( $current.'.php' );
		echo '</div>';
	?>
</div>

