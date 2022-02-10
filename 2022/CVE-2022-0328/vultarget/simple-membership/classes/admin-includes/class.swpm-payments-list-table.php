<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SWPMPaymentsListTable extends WP_List_Table {

	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct(
			array(
				'singular' => 'transaction', // singular name of the listed records
				'plural'   => 'transactions', // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);
	}

	function column_default( $item, $column_name ) {
		$val = $item[ $column_name ];
		switch ( $column_name ) {
			case 'payment_amount':
				$val = SwpmMiscUtils::format_money( $val );
				$val = apply_filters( 'swpm_transactions_page_amount_display', $val, $item );
				break;
			default:
				break;
		}
		return $val;
	}

	function column_id( $item ) {

		// Build row actions
		$actions = array(
			/* 'edit' => sprintf('<a href="admin.php?page=simple_wp_membership_payments&edit_txn=%s">Edit</a>', $item['id']),//TODO - Will be implemented in a future date */
			'delete' => sprintf( '<a href="admin.php?page=simple_wp_membership_payments&action=delete_txn&id=%s" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>', $item['id'] ),
		);

		// Return the refid column contents
		return $item['id'] . $this->row_actions( $actions );
	}

	function column_member_profile( $item ) {
		global $wpdb;
		$member_id    = $item['member_id'];
		$subscr_id    = $item['subscr_id'];
		$column_value = '';

		if ( empty( $member_id ) ) {// Lets try to get the member id using unique reference
			if ( ! empty( $subscr_id ) ) {
				$resultset = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl where subscr_id=%s", $subscr_id ), OBJECT );
				if ( $resultset ) {
					// Found a record
					$member_id = $resultset->member_id;
				}
			}
		}

		if ( ! empty( $member_id ) ) {
			$profile_page = 'admin.php?page=simple_wp_membership&member_action=edit&member_id=' . $member_id;
			$column_value = '<a href="' . $profile_page . '">' . SwpmUtils::_( 'View Profile' ) . '</a>';
		} else {
			$column_value = '';
		}
		return $column_value;
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/* $1%s */ $this->_args['singular'], // Let's reuse singular label (affiliate)
			/* $2%s */ $item['id'] // The value of the checkbox should be the record's key/id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'               => '<input type="checkbox" />', // Render a checkbox instead of text
			'id'               => SwpmUtils::_( 'Row ID' ),
			'email'            => SwpmUtils::_( 'Email Address' ),
			'first_name'       => SwpmUtils::_( 'First Name' ),
			'last_name'        => SwpmUtils::_( 'Last Name' ),
			'member_profile'   => SwpmUtils::_( 'Member Profile' ),
			'txn_date'         => SwpmUtils::_( 'Date' ),
			'txn_id'           => SwpmUtils::_( 'Transaction ID' ),
			'subscr_id'        => SwpmUtils::_( 'Subscriber ID' ),
			'payment_amount'   => SwpmUtils::_( 'Amount' ),
			'membership_level' => SwpmUtils::_( 'Membership Level' ),
                        'status' => SwpmUtils::_( 'Status/Note' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'id'               => array( 'id', false ), // true means its already sorted
			'membership_level' => array( 'membership_level', false ),
			'last_name'        => array( 'last_name', false ),
			'txn_date'         => array( 'txn_date', false ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => SwpmUtils::_( 'Delete' ),
		);
		return $actions;
	}

	function process_bulk_action() {
		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			$records_to_delete = array_map( 'sanitize_text_field', $_GET['transaction'] );
			if ( empty( $records_to_delete ) ) {
				echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
				return;
			}
			foreach ( $records_to_delete as $record_id ) {
				if ( ! is_numeric( $record_id ) ) {
					wp_die( 'Error! ID must be numeric.' );
				}
				global $wpdb;
				$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'swpm_payments_tbl WHERE id = %d', $record_id ) );
			}
			echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
		}
	}

	function delete_record( $record_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->prefix . 'swpm_payments_tbl WHERE id = %d', $record_id ) );
		// also delete record from swpm_transactions CPT
		$trans = get_posts(
			array(
				'meta_key'       => 'db_row_id',
				'meta_value'     => $record_id,
				'posts_per_page' => 1,
				'offset'         => 0,
				'post_type'      => 'swpm_transactions',
			)
		);
		wp_reset_postdata();
		if ( empty( $trans ) ) {
			return;
		}
		$trans = $trans[0];
		wp_delete_post( $trans->ID, true );

	}

	function prepare_items() {
		global $wpdb;

		// Lets decide how many records per page to show
		$per_page = apply_filters( 'swpm_transactions_menu_items_per_page', 50 );

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		// This checks for sorting input. Read and sanitize the inputs
		$orderby_column = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
		$sort_order     = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';
		if ( empty( $orderby_column ) ) {
			$orderby_column = 'id';
			$sort_order     = 'DESC';
		}
		$orderby_column = SwpmUtils::sanitize_value_by_array( $orderby_column, $sortable );
		$sort_order     = SwpmUtils::sanitize_value_by_array(
			$sort_order,
			array(
				'DESC' => '1',
				'ASC'  => '1',
			)
		);

		// pagination requirement
		$current_page = $this->get_pagenum();

		$search_term = filter_input( INPUT_POST, 'swpm_txn_search', FILTER_SANITIZE_STRING );
		$search_term = trim( $search_term );

		if ( $search_term ) {// Only load the searched records.
			$like          = $wpdb->esc_like( $search_term );
			$like          = '%' . $like . '%';
			$prepare_query = $wpdb->prepare( "SELECT * FROM  {$wpdb->prefix}swpm_payments_tbl WHERE `email` LIKE %s OR `txn_id` LIKE %s OR `first_name` LIKE %s OR `last_name` LIKE %s", $like, $like, $like, $like );
			$data          = $wpdb->get_results( $prepare_query, ARRAY_A );
			$total_items   = count( $data );
		} else { // Load all data in an optimized way (so it is only loading data for the current page)
			$query       = "SELECT COUNT(*) FROM {$wpdb->prefix}swpm_payments_tbl";
			$total_items = $wpdb->get_var( $query );

			// pagination requirement
			$query = "SELECT * FROM {$wpdb->prefix}swpm_payments_tbl ORDER BY $orderby_column $sort_order";

			$offset = ( $current_page - 1 ) * $per_page;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $per_page;

			$data = $wpdb->get_results( $query, ARRAY_A );
		}

		// Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
		$this->items = $data;

		// pagination requirement
		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items
				'per_page'    => $per_page, // WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page ),   // WE have to calculate the total number of pages
			)
		);
	}

}
