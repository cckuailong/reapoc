<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_DownloadPreview_Config {

	/** @var  DLM_Download */
	private $download = null;

	/** @var string */
	private $template = "";

	/**
	 * @return DLM_Download
	 */
	public function get_download() {
		return $this->download;
	}

	/**
	 * @param DLM_Download $download
	 */
	public function set_download( $download ) {
		$this->download = $download;
	}

	/**
	 * @return string
	 */
	public function get_template() {

		if ( "settings" == $this->template ) {
			$this->template = dlm_get_default_download_template();
		}

		return $this->template;
	}

	/**
	 * @param string $template
	 */
	public function set_template( $template ) {
		$this->template = $template;
	}


}