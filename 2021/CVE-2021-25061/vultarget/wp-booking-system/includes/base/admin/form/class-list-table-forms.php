<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * List table class outputter for Forms
 *
 */
Class WPBS_WP_List_Table_Forms extends WPBS_WP_List_Table {

	/**
	 * The number of forms that should appear in the table
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $items_per_page;

	/**
	 * The number of the page being displayed by the pagination
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $paged;

	/**
	 * The data of the table
	 *
	 * @access public
	 * @var array
	 *
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		parent::__construct( array(
			'plural' 	=> 'wpbs_forms',
			'singular' 	=> 'wpbs_form',
			'ajax' 		=> false
		));

		/**
		 * Filter the number of form shown in the table
		 *
		 * @param int
		 *
		 */
		$this->items_per_page = apply_filters( 'wpbs_list_table_form_items_per_page', 20 );

		$this->paged = ( ! empty( $_GET['paged'] ) ? (int)$_GET['paged'] : 1 );

		$this->set_pagination_args( array(
            'total_items' => wpbs_get_forms( array( 'number' => -1, 'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ) ), true ),
            'per_page'    => $this->items_per_page
        ));

		// Get and set table data
		$this->set_table_data();
		
		// Add column headers and table items
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->items 		   = $this->data;

	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns() {

		$columns = array(
			'name'		    => __( 'Name', 'wp-booking-system' ),
			'id' 		    => __( 'ID', 'wp-booking-system' ),
			'date_created'  => __( 'Date Created', 'wp-booking-system' ),
			'date_modified' => __( 'Date Modified', 'wp-booking-system' )
		);

		/**
		 * Filter the columns of the forms table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters( 'wpbs_list_table_forms_columns', $columns );

	}


	/**
     * Overwrites the parent class.
     * Define which columns are sortable
     *
     * @return array
     *
     */
    public function get_sortable_columns() {

        return array(
            'name' => array( 'name', false ),
            'id'   => array( 'id', false ),
            'date_created'  => array( 'date_created', false ),
			'date_modified' => array( 'date_modified', false )
        );

    }


	/**
     * Returns the possible views for the form list table
     *
     */
    protected function get_views() {

    	$form_status = ( ! empty( $_GET['form_status'] ) ? sanitize_text_field( $_GET['form_status'] ) : 'active' );

        $views = array(
            'active' => '<a href="' . add_query_arg( array( 'page' => 'wpbs-forms', 'form_status' => 'active', 'paged' => 1, 's' => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ) ), admin_url( 'admin.php' ) ) . '" ' . ( $form_status == 'active' ? 'class="current"' : '' ) . '>' . __( 'Active', 'wp-booking-system' ) . ' <span class="count">(' . wpbs_get_forms( array( 'status' => 'active', 'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ) ), true ) . ')</span></a>',
            'trash'  => '<a href="' . add_query_arg( array( 'page' => 'wpbs-forms', 'form_status' => 'trash', 'paged' => 1, 's' => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ) ), admin_url( 'admin.php' ) ) . '" ' . ( $form_status == 'trash' ? 'class="current"' : '' ) . '>' . __( 'Trash', 'wp-booking-system' ) . ' <span class="count">(' . wpbs_get_forms( array( 'status' => 'trash', 'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' ) ), true ) . ')</span></a>',
        );

		/**
		 * Filter the views of the forms table
		 *
		 * @param array $views
		 *
		 */
		return apply_filters( 'wpbs_list_table_forms_views', $views );

    }


	/**
	 * Gets the forms data and sets it
	 *
	 */
	private function set_table_data() {

		$form_args = array(
			'number'  => $this->items_per_page,
			'offset'  => ( $this->paged - 1 ) * $this->items_per_page,
			'status'  => ( ! empty( $_GET['form_status'] ) ? sanitize_text_field( $_GET['form_status'] ) : 'active' ),
			'orderby' => ( ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id' ),
			'order'   => ( ! empty( $_GET['order'] ) ? sanitize_text_field( strtoupper( $_GET['order'] ) ) : 'DESC' ),
			'search'  => ( ! empty( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '' )
		);

		$forms = wpbs_get_forms( $form_args );

		if( empty( $forms ) )
			return;

		foreach( $forms as $form ) {

			$row_data = $form->to_array();

			/**
			 * Filter the form row data
			 *
			 * @param array 		 $row_data
			 * @param WPBS_Form $form
			 *
			 */
			$row_data = apply_filters( 'wpbs_list_table_forms_row_data', $row_data, $form );

			$this->data[] = $row_data;

		}
		
	}


	/**
	 * Returns the HTML that will be displayed in each columns
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '-';

	}


	/**
	 * Returns the HTML that will be displayed in the "name" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_name( $item ) {

		if( $item['status'] == 'active' ) {

			$output  = '<strong><a class="row-title" href="' . add_query_arg( array( 'page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '">' . ( !empty( $item['name'] ) ? $item['name'] : '' ) . '</a></strong>';

			$actions = array(
				'edit_form' => '<a href="' . add_query_arg( array( 'page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $item['id'] ) , admin_url( 'admin.php' ) ) . '">' . __( 'Edit form', 'wp-booking-system' ) . '</a>',
				'trash' 		=> '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to send this form to the trash?", "wp-booking-system" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'wpbs-forms', 'wpbs_action' => 'trash_form', 'form_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'wpbs_trash_form', 'wpbs_token' ) . '" class="submitdelete">' . __( 'Trash', 'wp-booking-system' ) . '</a></span>'
			);

		}

		if( $item['status'] == 'trash' ) {

			$output  = '<strong>' . ( !empty( $item['name'] ) ? $item['name'] : '' ) . '</strong>';

			$actions = array(
				'restore_form' => '<a href="' . wp_nonce_url( add_query_arg( array( 'page' => 'wpbs-forms', 'wpbs_action' => 'restore_form', 'form_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'wpbs_restore_form', 'wpbs_token' ) . '">' . __( 'Restore form', 'wp-booking-system' ) . '</a>',
				'delete' 		   => '<span class="trash"><a onclick="return confirm( \'' . __( "Are you sure you want to delete this form?", "wp-booking-system" ) . ' \' )" href="' . wp_nonce_url( add_query_arg( array( 'page' => 'wpbs-forms', 'wpbs_action' => 'delete_form', 'form_id' => $item['id'] ) , admin_url( 'admin.php' ) ), 'wpbs_delete_form', 'wpbs_token' ) . '" class="submitdelete">' . __( 'Delete Permanently', 'wp-booking-system' ) . '</a></span>'
			);

		}

		/**
		 * Filter the row actions before adding them to the table
		 *
		 * @param array $actions
		 * @param array $item
		 *
		 */
		$actions = apply_filters( 'wpbs_list_table_forms_row_actions', $actions, $item );

		$output .= $this->row_actions( $actions );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_created( $item ) {

		$output = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item['date_created'] ) );

		return $output;

	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_modified( $item ) {

		$output = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item['date_modified'] ) );

		return $output;

	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items() {

		echo __( 'No forms found.', 'wp-booking-system' );

	}

}