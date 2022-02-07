<?php

class DLM_Reports_Chart {

	private $current_period;
	private $date_range;

	private $data;

	public function __construct( $data, $date_range, $current_period ) {
		$this->data           = $data;
		$this->date_range     = $date_range;
		$this->current_period = $current_period;
	}

	/**
	 * Get currently used date format
	 *
	 * @return string
	 */
	private function get_current_date_format() {
		$format = "Y-m-d";
		if ( 'month' === $this->current_period ) {
			$format = "Y-m";
		}

		return $format;
	}

	/**
	 * Generate labels
	 *
	 * @return array
	 */
	public function generate_labels() {

		$range = $this->date_range;

		$startDate = new DateTime( $range['from'] );
		$endDate   = new DateTime( $range['to'] );

		$labels = array();

		$format = "j M Y";
		if ( 'month' === $this->current_period ) {
			$format = "M";
		}

		while ( $startDate <= $endDate ) {
			$labels[] = $startDate->format( $format );
			$startDate->modify( "+1 " . $this->current_period );
		}

		return $labels;
	}

	/**
	 * Get log items based on filters
	 *
	 * @return array
	 */
	public function generate_chart_data() {

		$data_map = array();
		foreach ( $this->data as $data_row ) {
			$data_map[ $data_row->value ] = $data_row->amount;
		}

		$range = $this->date_range;

		$startDate = new DateTime( $range['from'] );
		$endDate   = new DateTime( $range['to'] );

		$data_formatted = array();

		$format = $this->get_current_date_format();

		while ( $startDate <= $endDate ) {

			if ( isset( $data_map[ $startDate->format( $format ) ] ) ) {
				$data_formatted[] = absint( $data_map[ $startDate->format( $format ) ] );
			} else {
				$data_formatted[] = 0;
			}

			$startDate->modify( "+1 " . $this->current_period );
		}

		return array( 'title' => '', 'color' => 'blue', 'values' => $data_formatted );
	}

}