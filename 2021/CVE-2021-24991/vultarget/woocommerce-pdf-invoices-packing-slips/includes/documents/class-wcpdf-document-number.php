<?php
namespace WPO\WC\PDF_Invoices\Documents;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Document_Number' ) ) :

/**
 * Document Number class
 * 
 * @class       \WPO\WC\PDF_Invoices\Documents\Document_Number
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

class Document_Number {
	/**
	 * The raw, unformatted number
	 * @var int
	 */
	public $number;

	/**
	 * Document number formatted for display
	 * @var String
	 */
	public $formatted_number;

	/**
	 * Number prefix
	 * @var string
	 */
	public $prefix;

	/**
	 * Number suffix
	 * @var string
	 */
	public $suffix;

	/**
	 * Document Type
	 * @var string
	 */
	public $document_type;

	/**
	 * Order ID
	 * @var int
	 */
	public $order_id;

	/**
	 * Zeros padding (total number of digits including leading zeros)
	 * @var int
	 */
	public $padding;

	public function __construct( $number, $settings = array(), $document = null, $order = null ) {
		$number = apply_filters( 'wpo_wcpdf_raw_document_number', $number, $settings, $document, $order );
		if ( !is_array( $number ) && !empty( $number ) ) {
			// we're creating a new number with settings as passed
			$this->number = $number;

			foreach ($settings as $key => $value) {
				$this->{$key} = $value;
			}

			if ( !isset( $this->formatted_number ) ) {
				$this->apply_formatting( $document, ( !empty( $document->order ) ? $document->order : $order ) );
			}

		} elseif ( is_array( $number ) ) {
			// loaded with full number data
			foreach ($number as $key => $value) {
				$this->{$key} = $value;
			}
		}

		if (!empty($document)) {
			$this->document_type = $document->get_type();
		}
		if (!empty($order)) {
			$this->order_id = WCX_Order::get_id( $order );
		}
	}

	public function __toString() {
		return (string) $this->get_formatted();
	}

	public function get_formatted() {
		$formatted_number = isset( $this->formatted_number ) ? $this->formatted_number : '';
		$formatted_number = apply_filters( 'wpo_wcpdf_formatted_document_number', $formatted_number, $this, $this->document_type, $this->order_id );
		return $formatted_number;
	}

	public function get_plain() {
		return $this->number;
	}

	public function apply_formatting( $document, $order ) {
		if ( empty( $document ) || empty( $order ) ) {
			$this->formatted_number = $this->number;
			return;
		}

		// load plain number
		$number = $this->number;

		// get dates
		$order_date = WCX_Order::get_prop( $order, 'date_created' );
		// order date can be empty when order is being saved, fallback to current time
		if ( empty( $order_date ) && function_exists('wc_string_to_datetime') ) {
			$order_date = wc_string_to_datetime( date_i18n('Y-m-d H:i:s') );
		}

		$document_date = $document->get_date();
		// fallback to order date if no document date available
		if (empty($document_date)) {
			$document_date = $order_date;
		}

		// get format settings
		$formats = array(
			'prefix'	=> $this->prefix,
			'suffix'	=> $this->suffix,
		);

		// load replacement values
		$order_year		= $order_date->date_i18n( 'Y' );
		$order_month	= $order_date->date_i18n( 'm' );
		$order_day		= $order_date->date_i18n( 'd' );
		$document_year	= $document_date->date_i18n( 'Y' );
		$document_month	= $document_date->date_i18n( 'm' );
		$document_day	= $document_date->date_i18n( 'd' );
		$order_number	= is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';

		// make replacements
		foreach ($formats as $key => $value) {
			$value = str_replace('[order_year]', $order_year, $value);
			$value = str_replace('[order_month]', $order_month, $value);
			$value = str_replace('[order_day]', $order_day, $value);
			$value = str_replace("[{$document->slug}_year]", $document_year, $value);
			$value = str_replace("[{$document->slug}_month]", $document_month, $value);
			$value = str_replace("[{$document->slug}_day]", $document_day, $value);
			$value = str_replace('[order_number]', $order_number, $value);

			// replace date tag in the form [invoice_date="{$date_format}"] or [order_date="{$date_format}"]
			$date_types = array( 'order', $document->slug );
			foreach ($date_types as $date_type) {
				if ( strpos($value, "[{$date_type}_date=") !== false ) {
					preg_match_all("/\[{$date_type}_date=\"(.*?)\"\]/", $value, $document_date_tags);
					if (!empty($document_date_tags[1])) {
						foreach ($document_date_tags[1] as $match_id => $date_format) {
							if ($date_type == 'order') {
								$value = str_replace($document_date_tags[0][$match_id], $order_date->date_i18n( $date_format ), $value);
							} else {
								$value = str_replace($document_date_tags[0][$match_id], $document_date->date_i18n( $date_format ), $value);
							}
						}
					}
				}
			}
			$formats[$key] = $value;
		}

		// Padding
		$padding_string = '';
		if ( function_exists('ctype_digit') ) { // requires the Ctype extension
			if ( ctype_digit( (string) $this->padding ) ) {
				$padding_string = (string) $this->padding;
			}
		} elseif ( !empty( $this->padding ) ) {
			$padding_string = (string) absint($this->padding);
		}

		if ( !empty( $padding_string ) ) {
			$number = sprintf('%0'.$padding_string.'d', $number);
		}

		// Add prefix & suffix
		$this->formatted_number = $formats['prefix'] . $number . $formats['suffix'] ;
		// Apply filters and store
		$this->formatted_number = apply_filters( 'wpo_wcpdf_format_document_number', $this->formatted_number, $this, $document, $order );

		return $this->formatted_number;
	}

	public function to_array() {
		return (array) $this;
	}
}

endif; // class_exists
