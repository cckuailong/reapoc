<?php

class DLM_Download_Factory {

	/**
	 * @param string $type
	 *
	 * @return DLM_Download | \Never5\DownloadMonitor\Shop\DownloadProduct\DownloadProduct
	 */
	public function make( $type = 'regular' ) {

		$class_name = 'DLM_Download';

		// check if this is a download product (a download that can be sold), if so create a DownloadProduct instance
		if ( 'product' === $type ) {
			$class_name = '\Never5\DownloadMonitor\Shop\DownloadProduct\DownloadProduct';
		}

		// make it filterable
		$class_name = apply_filters( 'dlm_download_factory_class_name', $class_name, $type );

		// check if class exists
		if ( ! class_exists( $class_name ) ) {
			$class_name = 'DLM_Download';
		}

		return new $class_name();
	}

}