<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'AICP_BANNED_USER_TABLE' ) ) {
	class AICP_BANNED_USER_TABLE extends WP_List_Table {
		public $banned_users, $table_name;

		public function __construct(){
			global $status, $page, $wpdb;
			$this->table_name = $wpdb->prefix . 'adsense_invalid_click_protector';

			parent::__construct( array(
	            'singular'  => __( 'Banned User', 'aicp' ),     //singular name of the listed records
	            'plural'    => __( 'Banned Users', 'aicp' ),   //plural name of the listed records
	            'ajax'      => false        //does this table support ajax?
		    ) );
		}

		public function column_default( $item, $column_name ) {
			switch( $column_name ) { 
				case 'ip':
				case 'click_count':
				case 'timestamp':
					return $item->$column_name;
				default:
		    		return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
		}

		public function column_ip( $item ) {
			$actions = array(
				'delete'    => sprintf( '<a class="aicp_delete" href="?page=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->id ),
			);

			return sprintf( '%1$s %2$s', $item->ip, $this->row_actions( $actions ) );
		}

		public function column_cb( $item ) {
	        return sprintf(
	            '<input type="checkbox" name="id[]" value="%s" />', $item->id
	        );    
	    }

		public function get_columns(){
			$columns = array(
				'cb' => '<input type="checkbox" />',
			    'ip' => 'IP Address',
			    'click_count' => 'Click Count',
			    'timestamp' => 'Timestamp'
			);
			return $columns;
		}

		public function get_sortable_columns() {
			$sortable_columns = array(
				'ip'  => array( 'ip', false ),
				'click_count' => array( 'click_count', false ),
				'timestamp'   => array( 'timestamp', false )
			);
			return $sortable_columns;
		}

		public function usort_reorder( $a, $b ) {
			// If no sort, default to title
			$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'timestamp';
			// If no order, default to asc
			$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
			// Determine sort order
			$result = strcmp( $a->$orderby, $b->$orderby );
			// Send final sort direction to usort
			return ( $order === 'asc' ) ? $result : -$result;
		}

		public function get_bulk_actions() {
			$actions = array(
				'delete'    => 'Delete'
				);
			return $actions;
		}

		public function prepare_items() {
			global $wpdb;
			$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
			$do_search = ( $search ) ? " WHERE " . $this->table_name . ".ip LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%' " : '';
			$query = "SELECT * FROM " . $this->table_name . $do_search . " ORDER BY timestamp DESC";
			$this->banned_users = $wpdb->get_results( $query );

			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			$this->process_bulk_action();
			//$this->items = $this->banned_users;
			$per_page = 10;
			$current_page = $this->get_pagenum();
			$total_items = count( $this->banned_users );

	  		// only ncessary because we have sample data
			$found_data = array_slice($this->banned_users,(($current_page-1)*$per_page),$per_page);

			$this->set_pagination_args( array(
			    'total_items' => $total_items,                  //WE have to calculate the total number of items
			    'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			    'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
			) );
			usort( $found_data, array( &$this, 'usort_reorder' ) );
			$this->items = $found_data;
		}
	} // end of class AICP_BANNED_USER_TABLE
} // end of checking if AICP_BANNED_USER_TABLE class exists