<?php

/**
 * This interface is the basic blueprint of the host (plugin) methods needed by UpdraftCentral
 * for it to work and function properly.
 */
interface UpdraftCentral_Host_Interface {
	public function retrieve_show_message($key, $echo = false);
	public function is_host_dir_set();
	public function get_logline_filter();
	public function get_debug_mode();
	public function get_udrpc($indicator_name);
	public function register_wp_http_option_hooks($register = true);
	public function get_class_name();
	public function get_instance();
	public function get_admin_instance();
	public function get_version();
	public function is_force_debug();
	public function debugtools_dashboard();
}
