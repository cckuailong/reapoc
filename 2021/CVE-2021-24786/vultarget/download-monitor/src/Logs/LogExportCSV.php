<?php

class DLM_Log_Export_CSV {

	/**
	 * Check if is allowed
	 *
	 * @return bool
	 */
	private function is_allowed() {

		if ( empty( $_GET['dlm_download_logs'] ) ) {
			return false;
		}

		if ( ! current_user_can( 'manage_downloads' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Run
	 */
	public function run() {
		global $wpdb;

		// check if allowed
		if ( ! $this->is_allowed() ) {
			wp_die( "You're not allowed to export logs." );
		}

		// catch and sanitize filter values
		$filter_status = isset( $_REQUEST['filter_status'] ) ? sanitize_text_field( $_REQUEST['filter_status'] ) : '';
		$filter_month  = ! empty( $_REQUEST['filter_month'] ) ? sanitize_text_field( $_REQUEST['filter_month'] ) : '';

		// setup filters
		$filters = array();

		// setup status filter
		if ( ! empty( $filter_status ) ) {
			$filters[] = array( "key" => "download_status", "value" => $filter_status );
		}

		// setup month filter
		if ( ! empty( $filter_month ) ) {
			$filters[] = array(
				'key'      => 'download_date',
				'value'    => date( 'Y-m-01', strtotime( $filter_month ) ),
				'operator' => '>='
			);

			$filters[] = array(
				'key'      => 'download_date',
				'value'    => date( 'Y-m-t', strtotime( $filter_month ) ),
				'operator' => '<='
			);
		}

		// get downloads
		$items = download_monitor()->service( 'log_item_repository' )->retrieve( $filters );

		// rows
		$rows   = array();
		$row    = array();
		$row[]  = __( 'Download ID', 'download-monitor' );
		$row[]  = __( 'Download Title', 'download-monitor' );
		$row[]  = __( 'Version ID', 'download-monitor' );
		$row[]  = __( 'Filename', 'download-monitor' );
		$row[]  = __( 'User ID', 'download-monitor' );
		$row[]  = __( 'User Login', 'download-monitor' );
		$row[]  = __( 'User Email', 'download-monitor' );
		$row[]  = __( 'User IP', 'download-monitor' );
		$row[]  = __( 'User Agent', 'download-monitor' );
		$row[]  = __( 'Date', 'download-monitor' );
		$row[]  = __( 'Status', 'download-monitor' );
		$row[]  = __( 'Meta Data', 'download-monitor' );
		$rows[] = '"' . implode( '","', $row ) . '"';

		if ( ! empty( $items ) ) {

			/** @var DLM_Log_Item $item */
			foreach ( $items as $item ) {

				try {
					/** @var DLM_Download $download */
					$download = download_monitor()->service( 'download_repository' )->retrieve_single( $item->get_download_id() );
				} catch ( Exception $e ) {
					$download = new DLM_Download();
				}

				try {
					$version = download_monitor()->service( 'version_repository' )->retrieve_single( $item->get_version_id() );
					$download->set_version( $version );
				} catch ( Exception $e ) {

				}

				$row   = array();
				$row[] = $item->get_download_id();

				if ( $download->exists() ) {
					$row[] = $download->get_title();
				} else {
					$row[] = '-';
				}

				$row[] = $item->get_version_id();

				if ( $download->exists() && $download->get_version()->get_filename() ) {
					$row[] = $download->get_version()->get_filename();
				} else {
					$row[] = '-';
				}

				$row[] = $item->get_user_id();

				if ( $item->get_user_id() ) {
					$user = get_user_by( 'id', $item->get_user_id() );
				}

				if ( ! isset( $user ) || ! $user ) {
					$row[] = '-';
					$row[] = '-';
				} else {
					$row[] = $user->user_login;
					$row[] = $user->user_email;
				}

				unset( $user );

				$row[] = $item->get_user_ip();
				$row[] = $item->get_user_agent();
				$row[] = $item->get_download_date()->format( 'Y-m-d H:i:s' );
				$row[] = $item->get_download_status() . ( $item->get_download_status_message() ? ' - ' : '' ) . $item->get_download_status_message();

				// setup meta data string
				$meta     = $item->get_meta_data();
				$meta_str = "";
				if ( ! empty( $meta ) ) {
					foreach ( $meta as $mk => $mv ) {
						$meta_str .= $mk . ": " . $mv . "\n";
					}
				}
				$row[] = $meta_str;

				$rows[] = '"' . implode( '","', $row ) . '"';
			}
		}

		$log = implode( "\n", $rows );

		header( "Content-type: text/csv" );
		header( "Content-Disposition: attachment; filename=download_log.csv" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Content-Length: " . strlen( $log ) );
		echo $log;
		exit;
	}
}