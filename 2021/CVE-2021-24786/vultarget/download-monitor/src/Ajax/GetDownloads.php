<?php

class DLM_Ajax_GetDownloads extends DLM_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'get_downloads' );
	}

	/**
	 * AJAX callback method
	 *
	 * @return void
	 */
	public function run() {
		// check nonce
		$this->check_nonce();

		$downloads       = download_monitor()->service( 'download_repository' )->retrieve( array(
			'orderby' => 'title',
			'order'   => 'ASC'
		) );
		$downloads_array = array();
		if ( ! empty( $downloads ) ) {
			/** @var DLM_Download $download */
			foreach ( $downloads as $download ) {
				$downloads_array[] = array(
					'value'    => $download->get_id(),
					'label' => $download->get_title()
				);
			}
		}

		wp_send_json( $downloads_array );

		exit;
	}

}