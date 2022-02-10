<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ebfrtbExportCSV' ) ) {
/**
 * Handle CSV exports
 *
 * PHP's fputcsv() function is used to stream a
 * CSV file to the browser.
 *
 * @since 0.2
 */
class ebfrtbExportCSV extends ebfrtbExport {

	/**
	 * Arguments for the query used to fetch
	 * bookings for this export
	 *
	 * @since 0.1
	 */
	public $query_args;

	/**
	 * Insantiate the CSV export
	 *
	 * @since 0.1
	 */
	public function __construct( $bookings, $args = array() ) {

		$this->bookings = $bookings;

		// Date range
		if ( !empty( $args['query_args'] ) ) {
			$this->query_args = $args['query_args'];
		}

		// Locations
		global $rtb_controller;
		if ( !empty( $rtb_controller->locations ) && !empty( $rtb_controller->locations->post_type ) ) {
			add_filter( 'ebfrtb_export_csv_booking_headers', array( $this, 'add_location_header' ) );
			add_filter( 'ebfrtb_export_csv_booking', array( $this, 'add_location' ), 10, 2 );
		}

		// Privacy consent
		if ( $rtb_controller->settings->get_setting( 'require-consent' ) ) {
			add_filter( 'ebfrtb_export_csv_booking_headers', array( $this, 'add_privacy_header' ) );
			add_filter( 'ebfrtb_export_csv_booking', array( $this, 'add_privacy' ), 10, 2 );
		}

		if ( $rtb_controller->settings->get_setting( 'enable-tables' ) ) {
			add_filter( 'ebfrtb_export_csv_booking_headers', array( $this, 'add_table_header' ), 9 );
			add_filter( 'ebfrtb_export_csv_booking', array( $this, 'add_table' ), 9, 2 );
		}

		if ( $rtb_controller->settings->get_setting( 'require-deposit' ) ) {
			add_filter( 'ebfrtb_export_csv_booking_headers', array( $this, 'add_deposit_header' ) );
			add_filter( 'ebfrtb_export_csv_booking', array( $this, 'add_deposit' ), 10, 2 );
		}

		add_filter( 'ebfrtb_export_csv_booking_headers', array( $this, 'add_custom_fields_header' ) );
		add_filter( 'ebfrtb_export_csv_booking', array( $this, 'add_custom_fields' ), 10, 2 );
	}

	/**
	 * Compile an array for the CSV file
	 *
	 * @since 0.1
	 */
	public function export() {

		global $rtb_controller;
		$date_format = $rtb_controller->settings->get_setting( 'ebfrtb-csv-date-format' );

		// Compile bookings arrayarray headers
		$arr = apply_filters( 'ebfrtb_export_csv_booking_headers', array(
			array(
				'ID' => __( 'Booking ID', 'restaurant-reservations' ),
				'date' => __( 'Date', 'restaurant-reservations' ),
				'name' => __( 'Name', 'restaurant-reservations' ),
				'party' => __( 'Party', 'restaurant-reservations' ),
				'email' => __( 'Email', 'restaurant-reservations' ),
				'phone' => __( 'Phone', 'restaurant-reservations' ),
				'message' => __( 'Message', 'restaurant-reservations' ),
				'date_submission' => __( 'Date the request was made', 'restaurant-reservations' ),
				'status' => __( 'Booking Status', 'restaurant-reservations' ),
			)
		) );

		// Compile bookings array
		foreach( $this->bookings as $booking ) {
			$arr[] = apply_filters( 'ebfrtb_export_csv_booking', array(
				'ID' => $booking->ID,
				'date' => date_i18n( $date_format, strtotime( $booking->date ) ),
				'name' => $booking->name,
				'party' => $booking->party,
				'email' => $booking->email,
				'phone' => $booking->phone,
				'message' => str_replace( array( "\r\n", "\n", "\r", '<br />', '<br>', '<br/>' ), ' ', $booking->message ),
				'date_submission' => date_i18n( $date_format, $booking->date_submission ),
				'status' => $booking->post_status,
			), $booking );
		}

		$this->export = apply_filters( 'ebfrtb_export_csv_bookings', $arr );

		return $this->export;
	}

	/**
	 * Deliver the CSV file to the browser
	 *
	 * @since 0.1
	 */
	public function deliver() {

		// Generate the export if it's not been done yet
		if ( empty( $this->export ) ) {
			$this->export();
		}

		$filename = apply_filters( 'ebfrtb_export_csv_filename', sanitize_file_name( $this->get_date_phrase() ) . '.csv' );
		$delimiter = apply_filters( 'ebfrtb_export_csv_delimiter', ',' );

		header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Expires: 0' );
		header( 'Pragma: no-cache' );

		$output = @fopen( 'php://output', 'w' );

		foreach( $this->export as $booking ) {
			fputcsv( $output, $booking, $delimiter );
		}

		fclose( $output );

		exit();
	}

	/**
	 * Add a location header if locations are active
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 1.1
	 */
	public function add_location_header( $headers ) {

		$headers[0] = array_merge(
			array( 'location' => __( 'Location', 'restaurant-reservations' ) ),
			$headers[0]
		);

		return $headers;
	}

	/**
	 * Add location data from a booking to the array for conversion to csv
	 *
	 * @param array $arr Assoc array of booking data compiled for conversion to
	 *        csv
	 * @param rtbBooking $booking Original booking object
	 * @since 1.1
	 */
	public function add_location( $arr, $booking ) {

		$location = '';
		if ( !empty( $booking->location ) ) {
			$term = get_term( $booking->location );
			if ( is_a( $term, 'WP_Term' ) ) {
				$location = $term->name;
			}
		}

		$arr = array_merge(
			array( 'location' => $location ),
			$arr
		);

		return $arr;
	}

	/**
	 * Add a header for the privacy consent if it's active
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 1.1.1
	 */
	public function add_privacy_header( $headers ) {

		$headers[0] = array_merge(
			$headers[0],
			array( 'consent_acquired' => __( 'Data Privacy Consent', 'restaurant-reservations' ) )
		);

		return $headers;
	}

	/**
	 * Add privacy consent collection status to the array for conversion to csv
	 *
	 * @param array $arr Assoc array of booking data compiled for conversion to
	 *        csv
	 * @param rtbBooking $booking Original booking object
	 * @since 1.1
	 */
	public function add_privacy( $arr, $booking ) {

		$consent_acquired = !empty( $booking->consent_acquired ) ? __( 'Yes', 'restaurant-reservations' ) : __( 'No', 'restaurant-reservations' );
		$arr = array_merge(
			$arr,
			array( 'consent_acquired' =>  $consent_acquired )
		);

		return $arr;
	}

	/**
	 * Add a header for the table(s) feature if it's active
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 2.2.0
	 */
	public function add_table_header( $headers ) {

		$headers[0] = array_merge(
			$headers[0],
			array( 'table' => __( 'Table(s)', 'restaurant-reservations' ) )
		);

		return $headers;
	}

	/**
	 * Add the selected table(s) to the array for conversion to csv
	 *
	 * @param array $arr Assoc array of booking data compiled for conversion to
	 *        csv
	 * @param rtbBooking $booking Original booking object
	 * @since 2.2.0
	 */
	public function add_table( $arr, $booking ) {

		$arr = array_merge(
			$arr,
			array( 'table' =>  implode( ',', $booking->table ) )
		);

		return $arr;
	}

	/**
	 * Add a header for the payment(s) feature if it's active
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 2.2.8
	 */
	public function add_deposit_header( $headers ) {

		$headers[0] = array_merge(
			$headers[0],
			array(
				'deposit' => __( 'Deposit', 'restaurant-reservations' ),
				'receipt_id' => __( 'Receipt ID', 'restaurant-reservations' )
			)
		);

		return $headers;
	}

	/**
	 * Add the deposit(s) to the array for conversion to csv
	 *
	 * @param array $arr Assoc array of booking data compiled for conversion to
	 *        csv
	 * @param rtbBooking $booking Original booking object
	 * @since 2.2.0
	 */
	public function add_deposit( $arr, $booking ) {

		global $rtb_controller;

		$arr = array_merge(
			$arr,
			array( 
				'deposit'    => $rtb_controller->settings->get_setting( 'rtb-currency' ) .' '. $booking->deposit,
				'receipt_id' => $booking->receipt_id
			)
		);

		return $arr;
	}

	/**
	 * Add custom fields to CSV headers
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 2.0
	 */
	public function add_custom_fields_header( $headers ) {

	    $fields = rtb_get_custom_fields();
	
	    foreach( $fields as $field ) {
	
	        if ( $field->type == 'fieldset' ) {
	            continue;
	        }
	
	        $headers[0][ 'cf-' . $field->slug ] = $field->title;
	    }
	
	    return $headers;
	}

	/**
	 * Add custom fields to CSV headers
	 *
	 * @param array $headers Key/value of spreadsheet header rows id/label
	 * @since 2.0
	 */
	public function add_custom_fields( $row, $booking ) {
		global $rtb_controller;

	    $fields = rtb_get_custom_fields();

	    $cf = isset( $booking->custom_fields ) ? $booking->custom_fields : array();
	
	    foreach( $fields as $field ) {
	
	        if ( $field->type == 'fieldset' ) {
	            continue;
	        }
	
	        $val = isset( $cf[ $field->slug ] ) ? $cf[ $field->slug ] : '';
	        $display_val = apply_filters( 'cffrtb_display_value_csv', $rtb_controller->fields->get_display_value( $val, $field, '', false ), $val, $field, $booking );
	
	        $row[ 'cf-' . $field->slug ] = $display_val;
	    }
	
	    return $row;
	}

	/**
	 * Add custom fields to CSV data row
	 *
	 * @param array $row Assoc array of booking data compiled for conversion to
	 *        csv
	 * @param rtbBooking $booking Original booking object
	 * @since 2.0
	 */
	public function cffrtb_ebfrtb_add_csv_row( $row, $booking ) {
		global $rtb_controller;

	    $fields = rtb_get_custom_fields();
	
	    $cf = isset( $booking->custom_fields ) ? $booking->custom_fields : array();
	
	    foreach( $fields as $field ) {
	
	        if ( $field->type == 'fieldset' ) {
	            continue;
	        }
	
	        $val = isset( $cf[ $field->slug ] ) ? $cf[ $field->slug ] : '';
	        $display_val = apply_filters( 'cffrtb_display_value_csv', $rtb_controller->fields->get_display_value( $val, $field, '', false ), $val, $field, $booking );
	
	        $row[ 'cf-' . $field->slug ] = $display_val;
	    }
	
	    return $row;
	}

}
} // endif
