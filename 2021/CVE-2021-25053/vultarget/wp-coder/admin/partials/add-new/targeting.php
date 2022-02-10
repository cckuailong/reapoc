<?php if ( ! defined( 'ABSPATH' ) ) exit;
	/**
		* Targeting
		*
		* @package     Lead_Generation
		* @subpackage  Add-new
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	include_once ( 'settings/targeting.php' );
	
	$option_name_view = '_lg_tool_popup_view_counter_' . $tool_id;
	$option_name_action = '_lg_tool_popup_action_counter_' . $tool_id;
	$tool_view = get_option( $option_name_view, '0' );	
	$tool_action = get_option( $option_name_action, '0' );
	if ( !empty( $tool_view ) ) {
		$conversion = round( $tool_action/$tool_view*100, 2 ) . '%';
	}
	else {
		$conversion = '0%';
	}
?>
<div id="lg-analytics" class="postbox lg-sidebar">
	<h2><?php _e('Analytics', 'leadgeneration'); ?></h2>
	<div class="inside">
	<div class="lg-container">
		<div class="lg-element">			
			<span class="dashicons dashicons-visibility"></span> <?php _e('Views', 'leadgeneration'); ?> - <span id="tool_view"><?php echo $tool_view; ?></span><p/>			
			<span class="dashicons dashicons-external"></span> <?php _e('Actions', 'leadgeneration'); ?> - <span id="tool_action"><?php echo $tool_action; ?></span><?php echo apply_filters( 'lg_help_tip', $action_help ); ?><p/>
			<span class="dashicons dashicons-filter"></span> <?php _e('Conversion', 'leadgeneration'); ?> - <span id="conversion"><?php echo $conversion; ?></span><p/>			
			<span class="preview button" onclick="reset_counts('popup', <?php echo $tool_id; ?>);"><?php _e('Reset', 'leadgeneration'); ?></span>					
		</div>		
	</div>
	</div>
</div>

<div id="lg-targeting" class="postbox lg-sidebar">
	<h2><?php _e('Targeting', 'leadgeneration'); ?></h2>
	<div class="inside">				
		<div class="lg-container">
			<div class="lg-element">	
				<h4><?php _e('Show on devices', 'leadgeneration'); ?><?php echo apply_filters( 'lg_help_tip', $show_screen_help ); ?></h4>
				<?php echo apply_filters( 'lg_create_option', $max_screen_enable ); ?> <label for="lg_max_screen_enable"><?php _e("Don't show on screens more", 'leadgeneration'); ?></label><p/>			
				<?php echo apply_filters( 'lg_create_option', $max_screen ); ?><p/>
				<?php echo apply_filters( 'lg_create_option', $min_screen_enable ); ?> <label for="lg_min_screen_enable"><?php _e("Don't show on screens less", 'leadgeneration'); ?></label><p/>			
				<?php echo apply_filters( 'lg_create_option', $min_screen ); ?><p/>			
			</div>		
		</div>		
		<?php if( class_exists( 'LG_Popups_Targeting' ) ) {
			echo apply_filters( 'lg_popups_targeting_fields', $param, $tool_id ); 
		} else {
			echo '<span class="dashicons dashicons-migrate" style="color:#37c781;"></span> ';
			printf( __( '<a href="%s" target="_blank">More advanced targeting</a>','leadgeneration' ), esc_url( 'https://dayes.co/downloads/popup-targeting/' ) );
		}
		
		?>	
		
	</div>
</div>