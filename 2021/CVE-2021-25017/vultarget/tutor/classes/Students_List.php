<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tutor_List_Table' ) ) {
	include_once tutor()->path.'classes/Tutor_List_Table.php';
}

class Students_List extends \Tutor_List_Table {

	const STUDENTS_LIST_PAGE = 'tutor-students';

	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'student',     //singular name of the listed records
			'plural'    => 'students',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
	}

	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'user_email':
			case 'display_name':
				return $item->$column_name;
			case 'course_taken':
				$course_taken = tutor_utils()->get_enrolled_courses_ids_by_user($item->ID);
				return is_array( $course_taken ) ? count( $course_taken) : 0;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * @param $item
	 *
	 * Completed Course by User
	 */
	function column_completed_course( $item ) {
		$user_id = $item->ID;

		$courses = tutor_utils()->get_courses_by_user( $user_id );
		if ( $courses && is_array( $courses->posts ) && count( $courses->posts ) ) {
			foreach ( $courses->posts as $course ) {
				echo '<a href="' . get_the_permalink( $course->ID ) . '" target="_blank">' . $course->post_title . '</a> ';
			}
		}
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("student")
			/*$2%s*/ $item->ID                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
			'display_name'      => __('Name', 'tutor'),
			'user_email'        => __('E-Mail', 'tutor'),
			'course_taken'		=> __( 'Course Taken', 'tutor' ),
			'completed_course'  => __('Completed Course', 'tutor'),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			//'display_name'     => array('title',false),     //true means it's already sorted
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			//'delete'    => 'Delete'
		);
		return $actions;
	}

	function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() ) {
			wp_die('Items deleted (or they would be if we had items to delete)!');
		}
	}

	function prepare_items() {
		$per_page = 20;

		$search_term = '';
		if ( isset( $_REQUEST['s'] ) ) {
			$search_term = sanitize_text_field( $_REQUEST['s'] );
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		//$this->process_bulk_action();

		$current_page = $this->get_pagenum();

		$total_items = tutor_utils()->get_total_students( $search_term );
		$this->items = tutor_utils()->get_students( ($current_page-1) * $per_page, $per_page, $search_term );

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}
}