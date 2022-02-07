<?php

class DLM_Reports_Ajax {

	/**
	 * Setup AJAX report hooks
	 */
	public function setup() {
		add_action( 'wp_ajax_dlm_reports_data', array( $this, 'handle' ) );
	}

	public function handle() {

		// check nonce
		check_ajax_referer( 'dlm_reports_data', 'nonce' );

		// check permission
		if ( ! current_user_can( 'dlm_view_reports' ) ) {
			die();
		}

		// getters
		$id     = ( ! empty( $_GET['id'] ) ) ? $_GET['id'] : null;
		$from   = ( ! empty( $_GET['from'] ) ) ? $_GET['from'] : null;
		$to     = ( ! empty( $_GET['to'] ) ) ? $_GET['to'] : null;
		$period = ( ! empty( $_GET['period'] ) ) ? $_GET['period'] : 'day';

		// setup date filter query
		$filters   = array(
			array( "key" => "download_status", "value" => array( "completed", "redirected" ), "operator" => "IN" ),
		);
		$fromObj   = new DateTime( $from );
		$toObj     = new DateTime( $to );
		$filters[] = array(
			'key'      => 'download_date',
			'value'    => $fromObj->format( 'Y-m-d 00:00:00' ),
			'operator' => '>='
		);

		$filters[] = array(
			'key'      => 'download_date',
			'value'    => $toObj->format( 'Y-m-d 23:59:59' ),
			'operator' => '<='
		);

		/** @var DLM_WordPress_Log_Item_Repository $repo */
		$repo = download_monitor()->service( 'log_item_repository' );

		$response = array();
		if ( null != $id ) {
			switch ( $id ) {
				case 'total_downloads_chart':

					$data = $repo->retrieve_grouped_count( $filters, $period );

					$chart                = new DLM_Reports_Chart( $data, array(
						'from' => $from,
						'to'   => $to
					), $period );
					$response['labels']   = $chart->generate_labels();
					$response['datasets'] = array( $chart->generate_chart_data() );

					break;
				case 'total_downloads_summary':

					// fetch totals
					$total = $repo->num_rows( $filters );

					// calculate how many days are in this range
					$interval = $fromObj->diff( $toObj );
					$days     = absint( $interval->format( "%a" ) ) + 1;

					// fetch download stats grouped by downloads
					$popular_download = "n/a";
					$data             = $repo->retrieve_grouped_count( $filters, $period, "download_id", 1, 0, "amount", "DESC" );
					if ( ! empty( $data ) ) {
						$d           = array_shift( $data );
						$download_id = $d->value;
						try {
							/** @var DLM_Download $download */
							$download         = download_monitor()->service( 'download_repository' )->retrieve_single( $download_id );
							$popular_download = $download->get_title();
						} catch ( Exception $e ) {

						}
					}

					$response['total']   = $total;
					$response['average'] = round( ( $total / $days ), 2 );
					$response['popular'] = $popular_download;
					break;
				case 'total_downloads_table':
					$total = $repo->num_rows( $filters );

					$data = $repo->retrieve_grouped_count( $filters, $period, "download_id", 0, 0, "amount", "DESC" );
					if ( ! empty( $data ) ) {

						/** @var DLM_Download_Repository $download_repo */
						$download_repo = download_monitor()->service( 'download_repository' );

						$response[] = array( "Download Title", "Times Downloaded", "%" );
						foreach ( $data as $row ) {

							$percentage = round( 100 * ( absint( $row->amount ) / absint( $total ) ), 2 );

							try {

								$download   = $download_repo->retrieve_single( $row->value );
								$response[] = array(
									sprintf( "%s (#%d)", $download->get_title(), $download->get_id() ),
									$row->amount,
									$percentage . "%"
								);

							} catch ( Exception $e ) {
								$response[] = array(
									sprintf( "Download no longer exists (#%d)", $row->value, $percentage . "%" ),
									$row->amount
								);
							}


						}
					}
					break;
				case 'total_downloads_browser_table':

					// get total
					$total = $repo->num_rows( $filters );

					// get data
					$data = $repo->retrieve_grouped_count( $filters, $period, "user_agent", 0, 0, "amount", "DESC" );

					// UA parser
					$ua_parser = new UAParser();

					// header row
					$response[] = array( "Browser", "Downloaded", "%" );

					// we need to parse raw UA data
					$formatted_data = array();

					if ( ! empty( $data ) ) {
						foreach ( $data as $row ) {

							$ua = $ua_parser->parse( $row->value );

							if ( ! isset( $formatted_data[ $ua->ua->family ] ) ) {
								$formatted_data[ $ua->ua->family ] = 0;
							}

							$formatted_data[ $ua->ua->family ] += $row->amount;

						}
					}

					if ( ! empty( $formatted_data ) ) {

						foreach ( $formatted_data as $ua => $amount ) {

							$percentage = round( 100 * ( absint( $amount ) / absint( $total ) ), 2 );

							$response[] = array( $ua, $amount, $percentage . "%" );
						}
					}
					break;
			}
		}

		wp_send_json( $response );
		exit;
	}

}