<?php

/**
 * Class DLM_Service
 *
 * Partial DI Service Provider, limited due to PHP 5.2 restriction
 */
class DLM_Services {

	/** @var array */
	private $services;

	/**
	 * Get service by key
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		try {
			if ( ! isset( $this->services[ $key ] ) ) {
				$method = "cb_" . $key;
				if ( ! method_exists( $this, $method ) ) {
					throw new Exception( "Requested service not found" );
				}

				$this->services[ $key ] = $this->$method();
			}

			return $this->services[ $key ];
		} catch ( Exception $e ) {
			DLM_Debug_Logger::log( $e->getMessage() );
		}

	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_WordPress_Download_Repository
	 */
	private function cb_download_repository() {
		return new DLM_WordPress_Download_Repository();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Download_Factory
	 */
	private function cb_download_factory() {
		return new DLM_Download_Factory();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_WordPress_Version_Repository
	 */
	private function cb_version_repository() {
		return new DLM_WordPress_Version_Repository();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_File_Manager
	 */
	private function cb_file_manager() {
		return new DLM_File_Manager();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_File_Manager
	 */
	private function cb_view_manager() {
		return new DLM_View_Manager();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Template_Handler
	 */
	private function cb_template_handler() {
		return new DLM_Template_Handler();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Hasher
	 */
	private function cb_hasher() {
		return new DLM_Hasher();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Transient_Manager
	 */
	private function cb_transient_manager() {
		return new DLM_Transient_Manager();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Version_Manager
	 */
	private function cb_version_manager() {
		return new DLM_Version_Manager();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_WordPress_Log_Item_Repository
	 */
	private function cb_log_item_repository() {
		return new DLM_WordPress_Log_Item_Repository();
	}

	/**
	 * Dynamically called via get()
	 *
	 * @return DLM_Settings_Helper
	 */
	private function cb_settings() {
		return new DLM_Settings_Helper();
	}
}