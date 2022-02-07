<?php
namespace WPO\WC\PDF_Invoices\Documents;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Bulk_Document' ) ) :

/**
 * Bulk Document
 *
 * Wraps single documents in a bulk document
 *
 * @class       \WPO\WC\PDF_Invoices\Documents\Bulk_Document
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

class Bulk_Document {
	/**
	 * Document type.
	 * @var String
	 */
	public $type;

	/**
	 * Wrapper document - used for filename etc.
	 * @var String
	 */
	public $wrapper_document;

	/**
	 * Order IDs.
	 * @var array
	 */
	public $order_ids;

	public function __construct( $document_type, $order_ids = array() ) {
		$this->type = $document_type;
		$this->order_ids = $order_ids;
		$this->is_bulk = true;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_pdf() {
		do_action( 'wpo_wcpdf_before_pdf', $this->get_type(), $this );

		$html = $this->get_html();
		$pdf_settings = array(
			'paper_size'		=> apply_filters( 'wpo_wcpdf_paper_format', $this->wrapper_document->get_setting( 'paper_size', 'A4' ), $this->get_type(), $this ),
			'paper_orientation'	=> apply_filters( 'wpo_wcpdf_paper_orientation', 'portrait', $this->get_type(), $this ),
			'font_subsetting'	=> $this->wrapper_document->get_setting( 'font_subsetting', false ),
		);
		$pdf_maker = wcpdf_get_pdf_maker( $html, $pdf_settings );
		$pdf = apply_filters( 'wpo_wcpdf_pdf_data', $pdf_maker->output(), $this );
		
		do_action( 'wpo_wcpdf_after_pdf', $this->get_type(), $this );

		return $pdf;
	}

	public function get_html() {
		do_action( 'wpo_wcpdf_before_html', $this->get_type(), $this );
		$html_content = array();
		foreach ( $this->order_ids as $key => $order_id ) {
			do_action( 'wpo_wcpdf_process_template_order', $this->get_type(), $order_id );

			$order = WCX::get_order( $order_id );

			if ( $document = wcpdf_get_document( $this->get_type(), $order, true ) ) {
				$html_content[ $key ] = $document->get_html( array( 'wrap_html_content' => false ) );
			}
		}

		// get wrapper document & insert body content
		$this->wrapper_document = wcpdf_get_document( $this->get_type(), null );
		$html = $this->wrapper_document->wrap_html_content( $this->merge_documents( $html_content ) );
		do_action( 'wpo_wcpdf_after_html', $this->get_type(), $this );
		
		return $html;
	}


	public function merge_documents( $html_content ) {
		// insert page breaks merge
		$page_break = "\n<div style=\"page-break-before: always;\"></div>\n";
		$html = implode( $page_break, $html_content );
		return apply_filters( 'wpo_wcpdf_merged_bulk_document_content', $html, $html_content, $this );
	}

	public function output_pdf( $output_mode = 'download' ) {
		$pdf = $this->get_pdf();
		wcpdf_pdf_headers( $this->get_filename(), $output_mode, $pdf );
		echo $pdf;
		die();
	}

	public function output_html() {
		echo $this->get_html();
		die();
	}

	public function get_filename( $context = 'download', $args = array() ) {
		if ( empty( $this->wrapper_document ) ) {
			$this->wrapper_document = wcpdf_get_document( $this->get_type(), null );
		}
		$default_args = array(
			'order_ids' => $this->order_ids,
		);
		$args = $args + $default_args;
		$filename = $this->wrapper_document->get_filename( $context, $args );
		return $filename;
	}

}

endif; // class_exists
