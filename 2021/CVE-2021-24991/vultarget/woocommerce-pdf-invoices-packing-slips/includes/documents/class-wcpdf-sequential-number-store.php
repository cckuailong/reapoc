<?php
namespace WPO\WC\PDF_Invoices\Documents;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Documents\\Sequential_Number_Store' ) ) :

/**
 * Class handling database interaction for sequential numbers
 * 
 * @class       \WPO\WC\PDF_Invoices\Documents\Sequential_Number_Store
 * @version     2.0
 * @category    Class
 * @author      Ewout Fernhout
 */

class Sequential_Number_Store {
	/**
	 * Name of the number store (used for table_name)
	 * @var String
	 */
	public $store_name;

	/**
	 * Number store method, either 'auto_increment' or 'calculate'
	 * @var String
	 */
	public $method;

	/**
	 * Name of the table that stores the number sequence (including the wp_wcpdf_ table prefix)
	 * @var String
	 */
	public $table_name;

	public function __construct( $store_name, $method = 'auto_increment' ) {
		global $wpdb;
		$this->store_name = $store_name;
		$this->method = $method;
		$this->table_name = apply_filters( "wpo_wcpdf_number_store_table_name", "{$wpdb->prefix}wcpdf_{$store_name}", $store_name, $method ); // i.e. wp_wcpdf_invoice_number

		$this->init();
	}

	public function init() {
		global $wpdb;
		// check if table exists
		if( $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") == $this->table_name) {
			// check calculated_number column if using 'calculate' method
			if ( $this->method == 'calculate' ) {
				$column_exists = $wpdb->get_var("SHOW COLUMNS FROM `{$this->table_name}` LIKE 'calculated_number'");
				if (empty($column_exists)) {
					$wpdb->query("ALTER TABLE {$this->table_name} ADD calculated_number int (16)");
				}
			}
			return; // no further business
		}

		// create table (in case of concurrent requests, this does no harm if it already exists)
		$charset_collate = $wpdb->get_charset_collate();
		// dbDelta is a sensitive kid, so we omit indentation
$sql = "CREATE TABLE {$this->table_name} (
  id int(16) NOT NULL AUTO_INCREMENT,
  order_id int(16),
  date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  calculated_number int (16),
  PRIMARY KEY  (id)
) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result = dbDelta( $sql );

		return $result;
	}

	/**
	 * Consume/create the next number and return it
	 * @param  integer $order_id WooCommerce Order ID
	 * @param  string  $date     Local date, formatted as Y-m-d H:i:s
	 * @return int               Number that was consumed/created
	 */
	public function increment( $order_id = 0, $date = null ) {
		global $wpdb;
		if ( empty( $date ) ) {
			$date = get_date_from_gmt( date( 'Y-m-d H:i:s' ) );
		}

		do_action( 'wpo_wcpdf_before_sequential_number_increment', $this, $order_id, $date );

		$data = array(
			'order_id'	=> (int) $order_id,
			'date'		=> $date,
		);

		if ( $this->method == 'auto_increment' ) {
			$wpdb->insert( $this->table_name, $data );
			$number = $wpdb->insert_id;
		} elseif ( $this->method == 'calculate' ) {
			$number = $data['calculated_number'] = $this->get_next();
			$wpdb->insert( $this->table_name, $data );
		}
		
		// return generated number
		return $number;
	}

	/**
	 * Get the number that will be used on the next increment
	 * @return int next number
	 */
	public function get_next() {
		global $wpdb;
		if ( $this->method == 'auto_increment' ) {
			// clear cache first on mysql 8.0+
			if ( $wpdb->get_var( "SHOW VARIABLES LIKE 'information_schema_stats_expiry'" ) ) {
				$wpdb->query( "SET SESSION information_schema_stats_expiry = 0" );
			}
			// get next auto_increment value
			$table_status = $wpdb->get_row("SHOW TABLE STATUS LIKE '{$this->table_name}'");
			$next = $table_status->Auto_increment;
		} elseif ( $this->method == 'calculate' ) {
			$last_row = $wpdb->get_row( "SELECT * FROM {$this->table_name} WHERE id = ( SELECT MAX(id) from {$this->table_name} )" );
			if ( empty( $last_row ) ) {
				$next = 1;
			} elseif ( !empty( $last_row->calculated_number ) ) {
				$next = (int) $last_row->calculated_number + 1;
			} else {
				$next = (int) $last_row->id + 1;
			}
		}
		return $next;
	}

	/**
	 * Set the number that will be used on the next increment
	 */
	public function set_next( $number = 1 ) {
		global $wpdb;

		// delete all rows
		$delete = $wpdb->query("TRUNCATE TABLE {$this->table_name}");

		// set auto_increment
		if ( $number > 1 ) {
			// if AUTO_INCREMENT is not 1, we need to make sure we have a 'highest value' in case of server restarts
			// https://serverfault.com/questions/228690/mysql-auto-increment-fields-resets-by-itself
			$highest_number = (int) $number - 1;
			$wpdb->query( $wpdb->prepare( "ALTER TABLE {$this->table_name} AUTO_INCREMENT=%d;", $highest_number ) );
			$data = array(
				'order_id'	=> 0,
				'date'		=> get_date_from_gmt( date( 'Y-m-d H:i:s' ) ),
			);
			
			if ( $this->method == 'calculate' ) {
				$data['calculated_number'] = $highest_number;
			}

			// after this insert, AUTO_INCREMENT will be equal to $number
			$wpdb->insert( $this->table_name, $data );
		} else {
			// simple scenario, no need to insert any rows
			$wpdb->query( $wpdb->prepare( "ALTER TABLE {$this->table_name} AUTO_INCREMENT=%d;", $number ) );
		}
	}

	public function get_last_date( $format = 'Y-m-d H:i:s' ) {
		global $wpdb;
		$row = $wpdb->get_row( "SELECT * FROM {$this->table_name} WHERE id = ( SELECT MAX(id) from {$this->table_name} )" );
		$date = isset( $row->date ) ? $row->date : 'now';
		$formatted_date = date( $format, strtotime( $date ) );

		return $formatted_date;
	}
}

endif; // class_exists
