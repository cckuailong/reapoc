<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

if (! class_exists('Tutor_List_Table')){
	include_once tutor()->path.'classes/Tutor_List_Table.php';
}

class Withdraw_Requests_List extends \Tutor_List_Table {

	const WITHDRAW_REQUEST_LIST_PAGE = 'tutor_withdraw_requests';

	function __construct(){
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'withdraw',     //singular name of the listed records
			'plural'    => 'withdraw',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );

		$this->process_bulk_action();
	}

	function column_default($item, $column_name){
		switch($column_name){
			case 'testing_col':
				return $item->$column_name;
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("student")
			/*$2%s*/ $item->withdraw_id                //The value of the checkbox should be the record's id
		);
	}

	function column_requested_user($item){
		echo "<p>{$item->user_name}</p><p>{$item->user_email}</p>";

		$actions = array();
		switch ($item->status){
			case 'pending':
				$actions['approved'] = sprintf('<a href="?page=%s&action=%s&withdraw_id=%s">'.__('Approve', 'tutor').'</a>',self::WITHDRAW_REQUEST_LIST_PAGE,'approved',
					$item->withdraw_id);
				$actions['rejected'] = sprintf('<a href="?page=%s&action=%s&withdraw_id=%s">'.__('Rejected', 'tutor').'</a>',self::WITHDRAW_REQUEST_LIST_PAGE,'rejected',$item->withdraw_id);
				break;
			case 'approved':
				$actions['rejected'] = sprintf('<a href="?page=%s&action=%s&withdraw_id=%s">'.__('Rejected', 'tutor').'</a>',self::WITHDRAW_REQUEST_LIST_PAGE,'rejected',$item->withdraw_id);
				break;
			case 'rejected':
				$actions['approved'] = sprintf('<a href="?page=%s&action=%s&withdraw_id=%s">'.__('Approve', 'tutor').'</a>',self::WITHDRAW_REQUEST_LIST_PAGE,'approved',$item->withdraw_id);
				break;
		}

		$actions['delete'] = sprintf('<a href="?page=%s&action=%s&withdraw_id=%s" onclick="return confirm(\'' . __('Are you Sure? It can not be undone.', 'tutor') . '\')">' . __('Delete', 'tutor') . '</a>', self::WITHDRAW_REQUEST_LIST_PAGE, 'delete', $item->withdraw_id);

		return "<div class='withdraw-list-row-actions'>". $this->row_actions($actions)."</div>";
	}
	function column_withdraw_method($item){
		if ($item->method_data){
			$data = maybe_unserialize($item->method_data);

			$method_name = tutor_utils()->avalue_dot('withdraw_method_name', $data);

			if ($method_name){
				echo "<p><strong>{$method_name}</strong></p>";
			}

			unset($data['withdraw_method_key'], $data['withdraw_method_name']);

			if (tutor_utils()->count($data)){
				foreach ($data as $method_field){
					$label = tutor_utils()->avalue_dot('label', $method_field);
					$value = tutor_utils()->avalue_dot('value', $method_field);
					echo "<p class='withdraw-method-data-row'> <span class='withdraw-method-label'>{$label}</span> : <span class='withdraw-method-value'>{$value}</span> </p>";
				}
			}

		}
		return '';
	}

	function column_requested_at($item){
		echo "<p>".date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($item->created_at))."</p>";
	}

	function column_amount($item){
		$available_status = array(
			'pending'	=> __( 'pending', 'tutor' ),
			'approved'	=> __( 'approved', 'tutor' ),
			'rejected'	=> __( 'rejected', 'tutor' ),
		);
		echo "<p>".tutor_utils()->tutor_price($item->amount)."</p>";
		echo "<p><span class='withdraw-status withdraw-status-{$item->status}'>".__( isset( $available_status[$item->status] ) ? $available_status[$item->status] : $item->status, 'tutor' )."</span></p>";
	}

	function get_columns(){
		$columns = array(
			'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
			'requested_user'    => __('Requested By', 'tutor'),
			'amount'            => __('Amount', 'tutor'),
			'withdraw_method'   => __('Withdrawal Method', 'tutor'),
			'requested_at'      => __('Requested Time', 'tutor'),
		);
		return $columns;
	}

	function get_bulk_actions() {
		$actions = array(
			//'delete'    => 'Delete'
		);
		return $actions;
	}

	function process_bulk_action() {
		global $wpdb;

		$withdraw_page_url = admin_url('admin.php?page=' . self::WITHDRAW_REQUEST_LIST_PAGE);
		$date = date("Y-m-d H:i:s", tutor_time());
		$redirect = false;

		//Detect when a bulk action is being triggered...
		if( 'delete'===$this->current_action() ) {
			$should_withdraw_delete = apply_filters('tutor_should_withdraw_delete', true);

			if ($should_withdraw_delete){
				$withdraw_id = (int) sanitize_text_field($_GET['withdraw_id']);

				do_action('tutor_before_delete_withdraw', $withdraw_id);

				$wpdb->delete($wpdb->prefix."tutor_withdraws",array('withdraw_id' =>$withdraw_id));

				do_action('tutor_after_delete_withdraw', $withdraw_id);

				$redirect = true;
			}else{
				wp_die('Items deleted (or they would be if we had items to delete)!');
			}
		}


		/**
		 * Reject Withdraw
		 */
		if( 'approved' === $this->current_action() ) {
			$withdraw_id = (int) sanitize_text_field($_GET['withdraw_id']);
			$withdraw = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_withdraws WHERE withdraw_id = %d ", $withdraw_id));
			if ( ! $withdraw || $withdraw->status === 'approved'){
				return;
			}

			do_action('tutor_before_approved_withdraw', $withdraw_id);

			$wpdb->update($wpdb->prefix."tutor_withdraws", array('status' => 'approved', 'updated_at' => $date ), array('withdraw_id' =>$withdraw_id));

			do_action('tutor_after_approved_withdraw', $withdraw_id);

			$redirect = true;
		}

		/**
		 * Rejected
		 */
		if( 'rejected' === $this->current_action() ) {
			$withdraw_id = (int) sanitize_text_field($_GET['withdraw_id']);
			$withdraw = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_withdraws WHERE withdraw_id = %d ", $withdraw_id));
			if ( ! $withdraw || $withdraw->status === 'rejected'){
				return;
			}

			do_action('tutor_before_rejected_withdraw', $withdraw_id);

			$wpdb->update($wpdb->prefix."tutor_withdraws", array('status' => 'rejected', 'updated_at' => $date ), array('withdraw_id' =>$withdraw_id));

			do_action('tutor_after_rejected_withdraw', $withdraw_id);

			$redirect = true;
		}


		if ($redirect){
			die("<script>location.href='{$withdraw_page_url}';</script>");
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

		$this->_column_headers = array($columns, $hidden);
		$current_page = $this->get_pagenum();

		$start = ($current_page-1)*$per_page;
		$withdraw_requests = tutor_utils()->get_withdrawals_history(null, compact('start', 'per_page', 'search_term') );
		$this->items = $withdraw_requests->results;
		$count_result = $withdraw_requests->count;

		$this->set_pagination_args( array(
			'total_items' => $count_result,
			'per_page'    => $per_page,
			'total_pages' => ceil($count_result/$per_page)
		) );
	}
}
