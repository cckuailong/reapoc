<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WP_List_Table' ) ) {
	class WPCF7R_List_Table extends WP_List_Table {

		public $list_data = array();
		/**
		* Constructor will create the menu item
		*/
		public function __construct( $columns, $data ) {

			parent::__construct();

			$this->list_data = $data;

			$this->columns = $columns;

		}

		/**
		 * Display the list table page
		 */
		public function list_table_page() {

			$this->prepare_items();

			$this->display();
		}

		/**
		 * Prepare the items for the table to process
		 */
		public function prepare_items() {
			$columns      = $this->get_columns();
			$hidden       = $this->get_hidden_columns();
			$sortable     = $this->get_sortable_columns();
			$data         = $this->list_data;
			$per_page     = 2;
			$current_page = $this->get_pagenum();

			usort( $data, array( &$this, 'sort_data' ) );
			$total_items = count( $data );

			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
				)
			);

			$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $data;

		}

		/**
		 * Override the parent columns method. Defines the columns to use in your listing table
		 */
		public function get_columns() {
			return $this->columns;
		}

		/**
		 * Define which columns are hidden
		 */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Define the sortable columns
		 */
		public function get_sortable_columns() {
			return array( 'title' => array( 'title', false ) );
		}

		/**
		 * Define what data to show on each column of the table
		 *
		 * @param $item
		 * @param $column_name
		 */
		public function column_default( $item, $column_name ) {

			switch ( $column_name ) {
				case 'id':
				case 'title':
				case 'description':
				case 'year':
				case 'director':
				case 'rating':
					return $item[ $column_name ];

				default:
					return print_r( $item, true );
			}
		}

		/**
		 * Allows you to sort the data by the variables set in the $_GET
		 *
		 * @param $a
		 * @param $b
		 */
		private function sort_data( $a, $b ) {
			// Set defaults
			$orderby = 'title';
			$order   = 'asc';

			// If orderby is set, use this as the sort column
			if ( ! empty( $_GET['orderby'] ) ) {
				$orderby = $_GET['orderby'];
			}

			// If order is set use this as the order
			if ( ! empty( $_GET['order'] ) ) {
				$order = $_GET['order'];
			}

			$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

			if ( 'asc' === $order ) {
				return $result;
			}

			return -$result;
		}
	}
}

