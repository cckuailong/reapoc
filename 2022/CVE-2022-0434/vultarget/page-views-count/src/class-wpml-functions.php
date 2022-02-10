<?php
namespace A3Rev\PageViewsCount;

class WPML_Functions
{	
	public $plugin_wpml_name = 'Page Views Count';
	
	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		
		$this->wpml_ict_t();
		
	}
	
	/** 
	 * Register WPML String when plugin loaded
	 */
	public function plugins_loaded() {
		$this->wpml_register_dynamic_string();
	}
	
	/** 
	 * Get WPML String when plugin loaded
	 */
	public function wpml_ict_t() {
		
		$plugin_name = A3_PVC_KEY;
		
		// For Quote Mode Settings
		add_filter( $plugin_name . '_' . 'pvc_settings' . '_get_settings', array( $this, 'pvc_settings' ) );
	}
	
	// Registry Dynamic String for WPML
	public function wpml_register_dynamic_string() {

		if ( function_exists('icl_register_string') ) {
			
			$pvc_settings = array_map( array( $GLOBALS[A3_PVC_PREFIX.'admin_interface'], 'admin_stripslashes' ), get_option( 'pvc_settings', array() ) );

			icl_register_string($this->plugin_wpml_name, 'Total Text Before', $pvc_settings['total_text_before'] );

			icl_register_string($this->plugin_wpml_name, 'Total Text After', $pvc_settings['total_text_after'] );

			icl_register_string($this->plugin_wpml_name, 'Today Text Before', $pvc_settings['today_text_before'] );

			icl_register_string($this->plugin_wpml_name, 'Today Text After', $pvc_settings['today_text_after'] );
			
		}
	}
		
	public function pvc_settings( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['total_text_before'] ) ) 
			$current_settings['total_text_before'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Total Text Before', $current_settings['total_text_before'] ) : $current_settings['total_text_before'] );

		if ( is_array( $current_settings ) && isset( $current_settings['total_text_after'] ) ) 
			$current_settings['total_text_after'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Total Text After', $current_settings['total_text_after'] ) : $current_settings['total_text_after'] );

		if ( is_array( $current_settings ) && isset( $current_settings['today_text_before'] ) ) 
			$current_settings['today_text_before'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Today Text Before', $current_settings['today_text_before'] ) : $current_settings['today_text_before'] );

		if ( is_array( $current_settings ) && isset( $current_settings['today_text_after'] ) ) 
			$current_settings['today_text_after'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Today Text After', $current_settings['today_text_after'] ) : $current_settings['today_text_after'] );
		
		
		return $current_settings;
	}
	
}
