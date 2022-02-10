<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ebfrtbExportPDF' ) ) {
/**
 * Handle PDF exports
 *
 * mPDF or TCPDF is used to render the PDFs.
 *
 * mPDF has better HTML/CSS support, but may not
 * be compatible with all servers.
 *
 * http://www.mpdf1.com/
 *
 * TCPDF should have better server compatiblity,
 * so it is a fallback in case the main renderer
 * is not compatible.
 *
 * http://www.tcpdf.org/
 *
 * @since 1.4.1
 */
class ebfrtbExportPDF extends ebfrtbExport {

	/**
	 * PDF Rendering Library
	 *
	 * @since 0.1
	 */
	public $lib;

	/**
	 * Paper size to use
	 *
	 * @since 0.1
	 */
	public $paper_size;

	/**
	 * Arguments for the query used to fetch
	 * bookings for this export
	 *
	 * @since 0.1
	 */
	public $query_args;

	/**
	 * Insantiate the PDF export
	 *
	 * @since 0.1
	 */
	public function __construct( $bookings, $args = array() ) {

		$this->bookings = $bookings;

		global $rtb_controller;
		$this->lib = !empty( $args['pdf-lib'] ) ? sanitize_key( $args['pdf-lib'] ) : $rtb_controller->settings->get_setting( 'ebfrtb-pdf-lib' );

		// Get paper size
		$this->paper_size = !empty( $args['paper-size'] ) ? sanitize_key( $args['paper-size'] ) : $rtb_controller->settings->get_setting( 'ebfrtb-paper-size' );

		// Query arguments
		if ( !empty( $args['query_args'] ) ) {
			$this->query_args = $args['query_args'];
		}

		// Maybe add table hooks
		add_filter( 'ebfrtb_mpdf_after_details', array($this, 'add_booking_table_to_pdf' ) );
		add_filter( 'ebfrtb_tcpdf_after_details', array($this, 'add_booking_table_to_pdf' ) );

		// Add custom field hooks
		add_filter( 'ebfrtb_mpdf_after_details', array($this, 'add_custom_fields_to_mpdf' ) );
		add_filter( 'ebfrtb_tcpdf_after_details', array($this, 'add_custom_fields_to_tcpdf' ) );
	}

	/**
	 * Set document information
	 *
	 * Sets document meta data like title, creator, etc. Both
	 * TCPDF and mPDF use the same methods for this.
	 *
	 * @since 0.1
	 */
	public function set_doc_info( $pdf ) {

		$pdf->SetCreator( get_bloginfo( 'sitename' ) );
		$pdf->SetAuthor( get_bloginfo( 'sitename' ) );
		$pdf->SetTitle( sprintf( _x( 'Bookings at %s', 'Title of PDF documents', 'restaurant-reservations' ), get_bloginfo( 'sitename' ) ) );
		$pdf->SetSubject( $this->get_date_phrase() );

		return $pdf;
	}

	/**
	 * Compile the PDF file
	 *
	 * This routes to the appropriate export method
	 * depending on the PDF library being used.
	 *
	 * @since 0.1
	 */
	public function export() {

		if ( $this->lib === 'tcpdf' ) {
			$this->export_tcpdf();

		} elseif ( $this->lib === 'mpdf' ) {
			$this->export_mpdf();

		} else {
			do_action( 'ebcfrtb_pdf_export_' . $this->lib, $this );
		}

		return $this->export;
	}

	/**
	 * Deliver the PDF to the browser
	 *
	 * @since 0.1
	 */
	public function deliver() {

		// Generate the export if it's not been done yet
		if ( empty( $this->export ) ) {
			$this->export();
		}

		$filename = apply_filters( 'ebfrtb_export_pdf_filename', sanitize_file_name( $this->get_date_phrase() ) . '.pdf' );

		// Clean any stray errors, warnings or notices that may have been
		// printed to the buffer
		ob_get_clean();

		if ( $this->lib === 'tcpdf' || $this->lib === 'mpdf' ) {
			$this->export->Output( $filename, 'I');
			exit();

		} else {
			do_action( 'ebcfrtb_pdf_deliver_' . $this->lib, $this );
		}

		// Just in case we forget...
		wp_die( __( 'An unexpected error occurred and your export request could not be fulfilled.', 'restaurant-reservations' ) );
	}

	/**
	 * Compile PDF file with TCPDF library
	 *
	 * @since 0.1
	 */
	public function export_tcpdf() {

		// Load TCPDF library
		require_once( RTB_PLUGIN_DIR . '/lib/tcpdf/config/tcpdf_config.php' );
		require_once( RTB_PLUGIN_DIR . '/lib/tcpdf/tcpdf.php' );

		// TCPDF uses a different identifier for U.S. Letter format
		if ( $this->paper_size === 'LETTER' ) {
			$this->paper_size = 'ANSI_A';
		}

		$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $this->paper_size, true, 'UTF-8', false);
		$tcpdf = $this->set_doc_info( $tcpdf );

		// set default header data
		$header_title = get_bloginfo( 'sitename' );
		if ( !empty( $this->query_args['location'] ) ) {
			$term = get_term( $this->query_args['location'] );
			if ( is_a( $term, 'WP_Term' ) ) {
				$header_title = $term->name;
			}
		}
		$tcpdf->SetHeaderData('', '', $header_title, $this->get_date_phrase(), array( 117, 117, 117 ), array( 117, 117, 117 ) );
		$tcpdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Subset fonts for smaller doc sizes
		$tcpdf->setFontSubsetting(true);

		// Add a page
		$tcpdf->AddPage();
		$tcpdf->SetCellPadding(0);

		// Generate the bookings HTML
		ob_start();
		$this->include_template( 'tcpdf.php' );
		$html = ob_get_clean();

		// Print HTML
		$tcpdf->writeHTML($html, false, true, false, false, '');

		$this->export = $tcpdf;

		return $this->export;
	}

	/**
	 *Maybe add the table for a booking to PDF output
	 *
	 * @since 2.2
	 */
	public function add_booking_table_to_pdf( $booking ) {
		global $rtb_controller;

		if ( $rtb_controller->settings->get_setting( 'enable-tables' ) ) { ?>
			
			<p class="booking-table">
				<span class="label"><?php echo __( 'Table(s)' ) . esc_html_x( ': ', 'Appears after table label in PDF exports', 'custom-fields-for-rtb' ); ?></span>
				<?php echo implode( ',', $booking->table ); ?>
			</p>

		<?php }
	}

	/**
 	* Add custom fields to TCPDF output
 	*
 	* @since 2.0
 	*/
	public function add_custom_fields_to_tcpdf( $booking ) {
		global $rtb_controller;

	    $fields = rtb_get_custom_fields();
	    $cf = isset( $booking->custom_fields ) ? $booking->custom_fields : array();
	
	    foreach( $fields as $field ) {
	
	        if ( $field->type == 'fieldset' || !isset( $cf[ $field->slug ] ) ) {
	            continue;
	        }
	
	        $val = $cf[ $field->slug ];
	        $display_val = apply_filters( 'cffrtb_display_value_tcpdf', $rtb_controller->fields->get_display_value( $val, $field, '', true ), $val, $field, $booking );
	
	        ?>
	
	        <div class="custom-field <?php echo esc_attr( $field->type . ' ' . $field->slug ); ?>">
				<?php echo esc_html( $field->title ) . esc_html_x( ': ', 'Appears after field label in TCPDF exports', 'custom-fields-for-rtb' ); ?>
				<?php echo $display_val ?>
	        </div>
	
	        <?php
	    }
	}

	/**
	 * Compile PDF file with mPDF library
	 *
	 * @since 0.1
	 */
	public function export_mpdf() {

		if ( !extension_loaded( 'mbstring' ) ) {
			$rtb_settings_link = '<a href="' . admin_url( 'admin.php?page=rtb-settings&tab=rtb-export' ) . '">' . _x( 'export settings', 'Name of a link to the Export tab on the settings page', 'restaurant-reservations' ) . '</a>';
			wp_die( sprintf( __( 'Your server has not loaded the mbstring PHP extension, or has disabled the mbregex PHP extension. mPDF requires both to output a PDF file. Please contact your website host and ask them to enable these PHP extensions. Or switch to the TCPDF library, which has fewer server requirements. You can change the PDF Renderer in the %s.', 'restaurant-reservations' ), $rtb_settings_link ) );
		}

		// Load mPDF library
		require_once( RTB_PLUGIN_DIR . '/lib/mpdf/vendor/autoload.php' );

		$mpdf = new \Mpdf\Mpdf( [], $this->paper_size, '', '', 15, 15, 25 );
		$mpdf = $this->set_doc_info( $mpdf );

		// Support languages automatically
		$mpdf->autoScriptToLang = true;
		$mpdf->baseScript = 1;
		$mpdf->autoVietnamese = true;
		$mpdf->autoArabic = true;
		$mpdf->autoLangToFont = true;

		// Generate the bookings HTML
		ob_start();
		$this->include_template( 'mpdf.php' );
		$html = ob_get_clean();

		$mpdf->WriteHTML($html);

		$this->export = $mpdf;

		return $this->export;
	}

	/**
	 * Add custom fields to mPDF output
	 *
	 * @since 2.0
	 */
	public function add_custom_fields_to_mpdf( $booking ) {
		global $rtb_controller;

	    $fields = rtb_get_custom_fields();
	    $cf = isset( $booking->custom_fields ) ? $booking->custom_fields : array();
	
	    foreach( $fields as $field ) {
	
	        if ( $field->type == 'fieldset' || !isset( $cf[ $field->slug ] ) ) {
	            continue;
	        }
	
	        $val = $cf[ $field->slug ];
	        $display_val = apply_filters( 'cffrtb_display_value_mpdf', $rtb_controller->fields->get_display_value( $val, $field, '', true ), $val, $field, $booking );
	
	        ?>
	
	        <p class="custom-field <?php echo esc_attr( $field->type . ' ' . $field->slug ); ?>">
				<span class="label"><?php echo esc_html( $field->title ) . esc_html_x( ': ', 'Appears after field label in mPDF exports', 'custom-fields-for-rtb' ); ?></span>
				<?php echo $display_val ?>
			</p>
	
	        <?php
	    }
	}

}
} // endif
