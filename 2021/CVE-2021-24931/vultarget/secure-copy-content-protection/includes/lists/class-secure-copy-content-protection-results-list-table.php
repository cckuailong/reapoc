<?php
ob_start();

class Sccp_Results_List_Table extends WP_List_Table {
	private $plugin_name;

	/** Class constructor */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		parent::__construct(array(
			'singular' => __('Result', $this->plugin_name), //singular name of the listed records
			'plural'   => __('Results', $this->plugin_name), //plural name of the listed records
			'ajax'     => false, //does this table support ajax?
		));
		add_action('admin_notices', array($this, 'results_notices'));

	}

	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_reports( $per_page = 7, $page_number = 1, $search = '' ) {

		global $wpdb;
		$reports_table = esc_sql($wpdb->prefix."ays_sccp_reports");
		$sql = "SELECT * FROM ".$reports_table;

        $args = array();

		$get_by_id = ( isset($_REQUEST['orderbyshortcode']) && $_REQUEST['orderbyshortcode'] != 0 ) ? true : false;

		$where = array();

        if( $search != '' ){
            $where[] = $search;
        }

        if($get_by_id){
        	$where[] = " subscribe_id=". absint( sanitize_text_field( $_REQUEST['orderbyshortcode'] ) );
        }

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

		if ( ! empty( $_REQUEST['orderby'] ) ) {

            $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                $sql .= ' ORDER BY '. $reports_table .'.' . $sql_orderby;
            } else {
                $sql .= ' ORDER BY '. $reports_table .'.id DESC';
            }
        }else{
            $sql .= ' ORDER BY '. $reports_table .'.id DESC';
        }

		$sql .= " LIMIT %d";
		$args[] = $per_page;
		$offset = ($page_number - 1) * $per_page;
		$sql .= " OFFSET %d";
		$args[] = $offset;

		$result = $wpdb->get_results(
		  	$wpdb->prepare( $sql, $args),
		  	'ARRAY_A'
		);

		return $result;
	}

	public function get_sccp_by_id() {
		global $wpdb;
		$sccp_report_table = esc_sql($wpdb->prefix."ays_sccp_reports");
		$sql = "SELECT DISTINCT subscribe_id FROM ".$sccp_report_table." ORDER BY subscribe_id";
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}

	public function get_report_by_id( $id ) {
		global $wpdb;
		$report_id    = absint(sanitize_text_field($id));
		$report_table = esc_sql($wpdb->prefix."ays_sccp_reports");

		$sql  = "SELECT * FROM ".$report_table." WHERE id=%d";
		$result = $wpdb->get_row(
		  	$wpdb->prepare( $sql, $report_id),
		  	'ARRAY_A'
		);

		return $result;
	}

	public function mark_as_read() {
		global $wpdb;
		$results_table = $wpdb->prefix . "ays_sccp_reports";
		$res           = $wpdb->update(
			$results_table,
			array('unread' => 0),
			array('unread' => 1),
			array('%d'),
			array('%d')
		);
		if ($res) {
			return true;
		}
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_reports( $id ) {

		global $wpdb;
		
		$rep_table = esc_sql($wpdb->prefix."ays_sccp_reports");				
		$arg_id    = absint( esc_sql($id) );

		$wpdb->delete( $rep_table,
			array('id' => $arg_id),
			array('%d')
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count($sh_id) {
		global $wpdb;
		$filter = array();

		// $shortcode_id = $sh_id != '' ? " WHERE subscribe_id=".$sh_id : "";
		$reports_table = esc_sql($wpdb->prefix."ays_sccp_reports");
		$sql = "SELECT COUNT(*) FROM ".$reports_table;

        $search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;
        $s = array();
        if( $search ){
            $s[] = sprintf(" `subscribe_email` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf(" `user_name` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );

            $filter[] = ' ( ' . implode(' OR ', $s) . ' ) ';
        }

        if ($sh_id != ''){
        	$filter[] = "subscribe_id=". absint( $sh_id );
        }
        
        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

		return $wpdb->get_var($sql);
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e('There are no results yet.', $this->plugin_name);
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		$other_info = !empty($item['other_info']) ? json_decode($item['other_info']) : array();
		switch ( $column_name ) {
			case 'subscribe_id':
			case 'user_ip':
			case 'subscribe_email':
			case 'user_name':
			case 'vote_date':
			case 'unread':
			case 'user_address':
				return $item[$column_name];
				break;
			case 'user_id':
				return $item[$column_name] > 0 ? get_user_by('ID', $item[$column_name])->display_name : __("Guest", $this->plugin_name);
				break;			
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s">', $item['id']
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_subscribe_id( $item ) {
		global $wpdb;

		$delete_nonce = wp_create_nonce($this->plugin_name . '-delete-result');
		$sub_id       = absint(sanitize_text_field($item['subscribe_id']));

		$title = $sub_id;

		$actions = [
			'delete' => sprintf('<a href="?page=%s&action=%s&result=%s&_wpnonce=%s" class="ays_confirm_del">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
		];

		return $title . $this->row_actions($actions);
	}

	function column_vote_date( $item ) {
		return date('H:i:s d.m.Y', strtotime($item['vote_date']));
	}

	function column_unread( $item ) {
		$unread = $item['unread'] == 1 ? "unread-result" : "";

		return "<div class='unread-result-badge $unread'></div>";
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'         	  	=> '<input type="checkbox" />',
			'subscribe_id'    	=> __('Shortcode ID', $this->plugin_name),
			'subscribe_email' 	=> __('User Email', $this->plugin_name),
			'user_name' 		=> __('User Name', $this->plugin_name),
			'user_ip'    		=> __('User IP', $this->plugin_name),
			'user_id'    		=> __('WP User', $this->plugin_name),
			'vote_date'  		=> __('Date', $this->plugin_name),
			'unread'     		=> __('Read Status', $this->plugin_name),
			'user_address'     	=> __('City, Country', $this->plugin_name)
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'subscribe_id' 		=> array('subscribe_id', true),
			'subscribe_email' 	=> array('subscribe_email', true),
			'user_name' 		=> array('user_name', true),
			'user_ip'   		=> array('user_ip', true),
			'user_id'   		=> array('user_id', true),
			'vote_date' 		=> array('vote_date', true),
			'unread'			=> array('unread', true),			
			'user_address'		=> array('user_address', true)			
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
		);

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		global $wpdb;

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();
		$shortcode_id = isset($_REQUEST['orderbyshortcode']) && $_REQUEST['orderbyshortcode'] != '' && $_REQUEST['orderbyshortcode'] != 0 ? absint( sanitize_text_field($_REQUEST['orderbyshortcode']) ) : "";
		$per_page = $this->get_items_per_page('sccp_results_per_page', 7);

		$current_page = $this->get_pagenum();
		$total_items  = self::record_count($shortcode_id);

		$this->set_pagination_args(array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page, //WE have to determine how many items to show on a page
		));


		$search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;
        $esc_search = esc_sql( $wpdb->esc_like( $search ) );

        // $s = sprintf(" ( `subscribe_email` LIKE '%%".$search."%%' OR `user_name` LIKE '%%".$search."%%' ) ", $esc_search , $esc_search );
         $s = " ( `subscribe_email` LIKE '%%".$esc_search."%%' OR `user_name` LIKE '%%".$esc_search."%%' )";

        $do_search = ( $search ) ? $s : '';

        $this->items = self::get_reports( $per_page, $current_page, $do_search );
	}

	public function process_bulk_action() {	
		//Detect when a bulk action is being triggered...
		$message = 'deleted';
		if ('delete' === $this->current_action()) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr($_REQUEST['_wpnonce']);

			if (!wp_verify_nonce($nonce, $this->plugin_name . '-delete-result')) {
				die('Go get a life script kiddies');
			} else {
				global $wpdb;
				$result = $this->get_report_by_id($_GET['result']);
				self::delete_reports(absint($_GET['result']));

				// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
				// add_query_arg() return the current url

				$url = esc_url_raw(remove_query_arg(array('action', 'result', '_wpnonce'))) . '&status=' . $message;
				wp_redirect($url);
			}

		}

		// If the delete bulk action is triggered
		if ((isset($_POST['action']) && 'bulk-delete' == $_POST['action'])
		    || (isset($_POST['action2']) && 'bulk-delete' == $_POST['action2'])
		) {

			$delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				$res = $this->get_report_by_id($id); 		
				self::delete_reports($id);

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url

			$url = esc_url_raw(remove_query_arg(['action', 'result', '_wpnonce'])) . '&status=' . $message;
			wp_redirect($url);
		}
	}

	public function results_notices() {
		$status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

		if (empty($status)) {
			return;
		}

		if ('deleted' == $status) {
			$updated_message = esc_html(__('Result deleted.', $this->plugin_name));
		}

		if (empty($updated_message)) {
			return;
		}

		?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
		<?php
	}

}
