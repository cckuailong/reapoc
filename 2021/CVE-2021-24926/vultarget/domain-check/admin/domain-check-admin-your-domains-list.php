<?php

//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

if (!function_exists('is_admin') || !is_admin()) {
	die();
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Class for rendering the MyStyle Designs list within the WordPress
 * Administrator.
 * @package MyStyle
 * @since 0.1.0
 */
class DomainCheck_Your_Domains_List extends WP_List_Table {

	/**
	* Constructor.
	*/
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Domains', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Domains', 'sp' ), //plural name of the listed records
			'ajax'	=> false //should this table support ajax?
		) );
	}
	/**
	* Retrieve designs from the database.
	* 
	* This function is called by the prepare_items() function below.
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	* @todo add unit testing 
	*/
	public static function get_domains( $per_page = 50, $page_number = 1 ) {
		global $wpdb;
		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE status = 2';
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= ' ORDER BY domain_url ASC';
		}
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}
	
	/**
	* Delete a design.
	* 
	* This is not currently supported.
	* 
	* This function overrides the parent method by the same name.
	*
	* @param int $id customer ID
	*/
	public static function delete_design( $id ) {
		//DO NOTHING
		/*
		global $wpdb;
		
		$wpdb->delete(
			MyStyle_Design::get_table_name(),
			[ 'ID' => $id ],
			[ '%d' ]
		);
		*/
	}
	
	/**
	* Returns the count of records in the database.
	* 
	* This function overrides the parent method by the same name.
	*
	* @return null|string
	* @todo add unit testing
	*/
	public static function record_count() {
		global $wpdb;
		$sql = "SELECT COUNT(domain_id) FROM " . DomainCheck::$db_prefix . '_domains WHERE status = 2';
		return $wpdb->get_var( $sql );
	}
	
	/**
	* Text displayed when no customer data is available.
	* 
	* This function overrides the parent method by the same name.
	* 
	* @todo add unit testing
	*/
	public function no_items() {
	 _e( 'No domains added.', 'sp' );
	}
	
	/**
	* Method for the design_id column
	*
	* @param array $item An array of DB data for the row.
	* @return string Returns the content for the column cell.
	* @todo add unit testing
	*/
	function column_domain_id( $item ) {
		$out = '<strong>' . $item['domain_id'] . '</strong>';
		return $out;
	}
	
	/**
	* Method for thumbnail column
	*
	* @param array $item An array of DB data for the row.
	* @return string Returns the content for the column cell.
	* @todo add unit testing
	*/
	function column_domain_url( $item ) {
		
		$out = '';
		
		if ($item['domain_expires']) {
			$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
			$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
			if ($days_flat < 60) {
				$fill = 'gray';
				if ($days_flat < 30) {
					$fill = 'update-nag';
				}
				if ($days_flat < 10) {
					$fill = 'error';
				}
				if ($days_flat < 3) {
					$fill = 'red';
				}
				$out .= '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . ' hidden-desktop">';
			}
			if ($days_flat < 0) {
				$out = '<img src="' . plugins_url('/images/icons/color/clock-error.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-error hidden-desktop">';
			} else {
				//$out .= ' ' . number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0) . ' Days';
			}
		}
		
		$out .= ' <a href="?page=domain-check-profile&domain='.$item['domain_url'].'"><strong>'.$item['domain_url'].'</strong></a>&nbsp;&nbsp;'
			. '<a href="//' . $item['domain_url'] . '" target="_blank">'
			. '<img src="' . plugins_url('/images/icons/color/external-link.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-small svg-fill-gray">'
			. '</a>';
		return $out;
	}

	public function column_domain_extension( $item ) {
		$out = '';

		if ( isset( $item['domain_extension'] ) && $item['domain_extension'] ) {
			$out .= '.' . $item['domain_extension'];
		}

		return $out;
	}

	public function column_registrar( $item ) {
		$out = '';
		if ( isset($item['registrar']) && $item['registrar'] && $item['registrar'] != '' ) {
			//$out = '<a href="">' .
			$out .= DomainCheckWhoisData::get_registrar_name( $item['registrar'] );
		}
		return $out;
	}

	public function column_nameserver( $item ) {
		$out = '';
		if ( isset( $item['nameserver'] ) && $item['nameserver'] && $item['nameserver'] != '') {
			$out = $item['nameserver'];
		}
		return $out;
	}

	public function column_autorenew( $item ) {
		$out = '';

		if ( array_key_exists( 'autorenew', $item ) ) {
			$item['autorenew'] = (int)$item['autorenew'];
			if ( $item['autorenew'] && $item['autorenew'] !== 0 ) {
				//yes
				$fill = 'green';
				$text = 'on';
				$alt_text = 'Autorenew is On. Click to turn Off.';
			} else {
				//no
				$text = 'off';
				$fill = 'disabled';
				$alt_text = 'Autorenew is Off. Click to turn On.';
			}
			$out .= '<a id="autorenew-link-' . str_replace('.', '-', $item['domain_url']) . '" class="autorenew-link" alt="'.$alt_text.'" title="'.$alt_text.'" onclick="domain_check_ajax_call({\'action\':\'autorenew_trigger\', \'domain\':\'' . $item['domain_url'] . '\'}, autorenew_trigger_callback);">'
			. '<img id="autorenew-image-' . str_replace('.', '-', $item['domain_url']) . '" src="' . plugins_url('/images/icons/color/infinity-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-' . $fill . '">'
			. '<span id="autorenew-text-' . str_replace('.', '-', $item['domain_url']) . '">' . $text . '</span>'
			. '</a>';

			//$out = '<img src="' . plugins_url('/images/icons/color/infinity-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">';
		}
		return $out;
	}

	public function column_owner( $item ) {
		$out = '';
		if (isset($item['owner']) && $item['owner'] && $item['owner'] != '') {
			$out = $item['owner'];
		}
		return $out;
	}

	/**
	* Method for thumbnail column
	*
	* @param array $item An array of DB data for the row.
	* @return string Returns the content for the column cell.
	* @todo add unit testing
	*/
	function column_domain_root( $item ) {
		$out = '<a href="//' . $item['domain_root'] . '" target="_blank">' . $item['domain_root'] . '</a>';
		return $out;
	}

	function column_domain_expires( $item ) {
		if ($item['domain_expires']) {
			$days = number_format(($item['domain_expires'] - time())/60/60/24, 0);
			$days_flat = (int)floor(($item['domain_expires'] - time())/60/60/24);
			$out = '';
			if ($days_flat < 60) {
				$fill = 'gray';
				if ($days_flat < 30) {
					$fill = 'update-nag';
				}
				if ($days_flat < 10) {
					$fill = 'error';
				}
				if ($days_flat < 3) {
					$fill = 'red';
				}
				$out .= '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">';
			}
			if ($days_flat < 0) {
				$out = '<img src="' . plugins_url('/images/icons/color/clock-error.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-error">'
					. ' Expired';
			} else {
				$out .= ' ' . number_format(($item['domain_expires'] - time())/60/60/24, 0) . ' Days';
			}
			if ($days_flat < 60) {
				$out .= '&nbsp;&nbsp;<a href="' . DomainCheckLinks::homepage($item['domain_url']) . '" class="button" target="_blank">Renew</a>';
			}
		} else {
			$out = 'Unknown';
			$out = '';
		}
		return $out;
	}

	function column_domain_last_check( $item ) {
		if ($item['domain_last_check']) {
				$out = number_format((time() - $item['domain_last_check'])/60/60/24, 0) . ' Days';
		} else {
			$out = 'Unknown';
		}
		return $out;
	}

	/**
	 * Method for thumbnail column
	 *
	 * @param array $item An array of DB data for the row.
	 * @return string Returns the content for the column cell.
	 * @todo add unit testing
	 */
	function column_status( $item ) {
		$status = 0;
		if (array_key_exists('status', $item)) {
			$status = $item['status'];
		}
		switch ($status) {
			case 0:
				$out = 'Available';
				break;
			case 1:
				$out = 'Taken';
				break;
			case 2:
				$out = 'Owned';
				break;
			default:
				$out = 'Available';
				break;
		}
		return $out;
	}


	
	/**
	* Method for links column
	*
	* @param array $item An array of DB data for the row.
	* @return string Returns the content for the column cell.
	* @todo add unit testing
	*/
	function column_links( $item ) {
		if ($item['domain_watch']) {
			$text = 'Stop Watching';
			$fill = 'gray';
		} else {
			$text = 'Watch';
			$fill = 'disabled';
		}

		$paged = '';
		if ($this->get_pagenum()) {
			$paged = '&paged=' . $this->get_pagenum();
		}
		$order = '';
		if (isset($_REQUEST['orderby']) && isset($_REQUEST['order'])) {
			$order = '&orderby=' . $_REQUEST['orderby'] . '&order=' . $_REQUEST['order'];
		}

		$out = '';
		$out .= '<a href="?page=domain-check-your-domains&domain_check_search=' . $item['domain_url']  . $paged . $order . '" alt="Refresh" title="Refresh">'
			. '<img src="' . plugins_url('/images/icons/color/303-loop2.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">'
			//. ' Refresh'
			. '</a>';

		$out .= '<a id="watch-link-' . str_replace('.', '-', $item['domain_url']) . '"  alt="'.$text.'" title="'.$text.'" onclick="domain_check_ajax_call({\'action\':\'watch_trigger\', \'domain\':\'' . $item['domain_url'] . '\'}, watch_trigger_callback);">'
			. '<img id="watch-image-' . str_replace('.', '-', $item['domain_url']) . '" src="' . plugins_url('/images/icons/color/207-eye-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-' . $fill . '">'
			//. $text
			. '</a>';

		$out .= '<a href="?page=domain-check-your-domains&domain_check_delete=' . $item['domain_url']  . $paged . $order . '" alt="Delete" title="Delete">'
			. '<img src="' . plugins_url('/images/icons/color/174-bin2.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">'
			//. ' Delete'
			. '</a>';

		return $out;
	}
	
	/**
	* Render a column when no column specific method exists.
	* 
	* This function overrides the parent method by the same name.
	*
	* @param array $item The item/row that we are rendering.
	* @param string $column_name The name of the column whose cell we are
	* rendering.
	* @return mixed Returns the content for the column cell.
	* @todo add unit testing
	*/
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'domain_id':
			case 'domain_url':
			case 'domain_root':
			case 'status':
			case 'domain_last_check':
			case 'domain_next_check':
			case 'domain_expires':
			case 'ssl_last_check':
			case 'ssl_next_check':
			case 'ssl_expires':
		  	case 'links':
				return $item[ $column_name ];
			default:
				return 'Debug from column_default function: ' . print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb($item) {
		return '<input type="checkbox" name="bulk_domains[]" value="' . htmlentities($item['domain_url']) . '" />';
	}

	/**
	* Associative array of columns
	*
	* This function overrides the parent method by the same name.
	* 
	* @return array Returns the list of columns that are a part of the list.
	* @todo add unit testing
	*/

	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			//'domain_id'	=> __('Domain ID', 'sp'),
			//'domain_root' => __('Root Domain', 'sp'),
			'domain_url'	=> __('Domain', 'sp'),
			'domain_extension'	=> __('Extension', 'sp'),
			'domain_expires' => __('Expires', 'sp'),
			'domain_last_check' => __('Last Checked', 'sp'),
			'registrar' => __('Registrar', 'sp'),
			'nameserver' => __('Nameserver', 'sp'),
			'autorenew' => __('Autorenew', 'sp'),
			'owner' => __('Owner', 'sp'),
			//'notes' => __('Notes', 'sp'),
			//'domain_check' => __('Domain', 'sp'),
			//'ssl_check' => __('SSL', 'sp'),
			//'hosting_check' => __('Hosting'),
			'links' => __('Links'),
		);
		return $columns;
	}
	
	/**
	* Columns to make sortable.
	* 
	* This function overrides the parent method by the same name.
	*
	* @return array Returns a list of columns to make sortable.
	* @todo add unit testing
	*/
	public function get_sortable_columns() {
		$sortable_columns = array();
		
	  $sortable_columns = array(
		  'domain_id' => array( 'domain_id', true ),
		  //'domain_root' => array( 'domain_root', true ),
		  'domain_url' => array( 'domain_url', true ),
		  'domain_extension' => array( 'domain_extension', true ),
		  'domain_expires' => array( 'domain_expires', true ),
		  'status' => array( 'status', true ),
		  'registrar' => array( 'registrar', true ),
		  'nameserver' => array( 'nameserver', true ),
		  'autorenew' => array( 'autorenew', true ),
		  'owner' => array( 'owner', true ),
	  );
 
		return $sortable_columns;
	}
	
	/**
	* Returns an associative array containing the bulk actions
	* 
	* This function overrides the parent method by the same name.
	*
	* @return array Returns an associative array containing the bulk actions.
	* @todo add unit testing
	*/
	public function get_bulk_actions() {

		$actions = array(
			'bulk_delete' => 'Delete',
			'bulk_watch_start' => 'Watch',
			'bulk_watch_stop' => 'Stop Watching'
		);

		//$actions = array();
 
		return $actions;
	}

	/**
	* Force columns to be hidden on first load...
	*
	*/
	public function set_hidden_columns() {

		$hidden = get_user_option( 'manage' . $this->screen->id . 'columnshidden' );

		$use_defaults = ! is_array( $hidden );

		if ($use_defaults) {
			$hidden = array( 'owner', 'nameserver', 'domain_last_check' );
			update_user_option( get_current_user_id(), 'manage' . $this->screen->id . 'columnshidden', $hidden, true );
		}

	}

	/**
	* Handles data query and filter, sorting, and pagination.
	* 
	* This function overrides the parent method by the same name.
	* @todo add unit testing
	*/
	public function prepare_items() {

		$this->set_hidden_columns();

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();
 
		$per_page	= $this->get_items_per_page( 'domains_per_page', 50 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
 
		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'	=> $per_page //WE have to determine how many items to show on a page
		) );

		$items = self::get_domains( $per_page, $current_page );

		foreach ($items as $item_idx => $item) {
			$item['domain_settings'] = ($item['domain_settings'] ? json_decode(gzuncompress($item['domain_settings']), true) : null);
			$items[$item_idx] = $item;
		}

		$this->items = $items;
	}
	
	/**
	* Process Bulk actions for the list.
	* 
	* This function overrides the parent method by the same name.
	* @todo add unit testing
	*/
	public function process_bulk_action() {

		if (isset($_POST['bulk_domains'])) {
			switch($this->current_action()) {
				case 'bulk_delete':
					DomainCheckAdmin::callInstance('bulk_domain_delete', $_POST['bulk_domains']);
					break;
				case 'bulk_watch_start':
					DomainCheckAdmin::callInstance('bulk_domain_watch', $_POST['bulk_domains']);
					break;
				case 'bulk_watch_stop':
					DomainCheckAdmin::callInstance('bulk_domain_watch_stop', $_POST['bulk_domains']);
					break;
			}
		}
	}
}

