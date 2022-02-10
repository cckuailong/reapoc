<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

if (! class_exists('Tutor_List_Table')){
	include_once tutor()->path.'classes/Tutor_List_Table.php';
}

class Question_Answers_List extends \Tutor_List_Table {

	const Question_Answer_PAGE = 'question_answer';

	function __construct(){
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'question',     //singular name of the listed records
			'plural'    => 'questions',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
	}

	function column_default($item, $column_name){
		switch($column_name){
			case 'user_email':
			case 'display_name':
			case 'post_title':
			case 'answer_count':
				return $item->$column_name;
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_question($item){
		//Build row actions
		$actions = array(
			//'edit'      => sprintf('<a href="?page=%s&action=%s&instructor=%s">Edit</a>',$_REQUEST['page'],'edit',$item->comment_ID),
			//'delete'    => sprintf('<a href="?page=%s&action=%s&instructor=%s">Delete</a>',$_REQUEST['page'],'delete',$item->comment_ID),
		);
		$answer_action_text = __( 'Answer', 'tutor' );

		$actions['answer'] = sprintf('<a href="?page=%s&sub_page=%s&question_id=%s">'.$answer_action_text.'</a>',$_REQUEST['page'],'answer',$item->comment_ID);


		//Return the title contents
		return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			stripslashes($item->comment_content),
			$item->comment_ID,
			$this->row_actions($actions)
		);
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("instructor")
			/*$2%s*/ $item->comment_ID                //The value of the checkbox should be the record's id
		);
	}

	function column_course($item) {

		return $item->comment_ID;
	}

	function get_columns(){
		$columns = array(
			'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
			'question'          => __('Question', 'tutor'),
			'display_name'      => __('Student', 'tutor'),
			'post_title'        => __('Course', 'tutor'),
			'answer_count'            => __('Answer', 'tutor'),
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
			'delete'    => 'Delete'
		);
		return $actions;
	}

	function process_bulk_action() {
		global $wpdb;

		//Detect when a bulk action is being triggered...
		if( 'delete' === $this->current_action() ) {
			if ( empty($_GET['question']) || ! is_array($_GET['question'])){
				return;
			}

			$question_ids = array_map('sanitize_text_field', $_GET['question']);
			$question_ids = implode( ',', array_map( 'absint', $question_ids ) );

			//Deleting question (comment), child question and question meta (comment meta)
			$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE {$wpdb->comments}.comment_ID IN($question_ids)" );
			$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE {$wpdb->comments}.comment_parent IN($question_ids)" );
			$wpdb->query( "DELETE FROM {$wpdb->commentmeta} WHERE {$wpdb->commentmeta}.comment_id IN($question_ids)" );
		}
	}

	function prepare_items() {
		$per_page = 20;

		$search_term = '';
		if (isset($_REQUEST['s'])){
			$search_term = sanitize_text_field($_REQUEST['s']);
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();

		$current_page = $this->get_pagenum();

		$total_items = tutor_utils()->get_total_qa_question($search_term);
		$this->items = tutor_utils()->get_qa_questions(($current_page-1)*$per_page, $per_page, $search_term);

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );
	}
}