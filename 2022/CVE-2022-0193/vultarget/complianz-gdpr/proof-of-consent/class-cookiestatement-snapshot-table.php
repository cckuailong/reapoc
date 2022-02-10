<?php
/**
 * CookieSnapshot Table Class
 *
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class cmplz_CookieStatement_Snapshots_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 1.5
	 */
	public $per_page = 30;

	/**
	 * Number of customers found
	 *
	 * @var int
	 * @since 1.7
	 */
	public $count = 0;

	/**
	 * Total customers
	 *
	 * @var int
	 * @since 1.95
	 */
	public $total = 0;

	/**
	 * The arguments for the data set
	 *
	 * @var array
	 * @since  2.6
	 */
	public $args = array();

	public $all = array();

	/**
	 * Get things started
	 *
	 * @since 1.5
	 * @see   WP_List_Table::__construct()
	 */


	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
				'singular' => __( 'Cookiebanner', 'complianz-gdpr' ),
				'plural'   => __( 'Cookiebanners', 'complianz-gdpr' ),
				'ajax'     => false,
		) );

	}

	/**
	 * Show the search field
	 *
	 * @param string $text     Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 * @since 1.7
	 *
	 */


	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="'
				 . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="'
				 . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		$search = $this->get_search();
		?>

		<p class="search-box">
			<label class="screen-reader-text"
				   for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" name="s"
				   placeholder="<?php _e( "Search", "complianz-gdpr" ) ?>"
				   value="<?php echo $search ?>">
			<?php submit_button( $text, 'button', false, false,
					array( 'ID' => 'search-submit' ) ); ?>
		</p>
		<?php

	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string Name of the primary column.
	 * @since  2.5
	 * @access protected
	 *
	 */
	protected function get_primary_column_name() {
		return __( 'name', 'complianz-gdpr' );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array  $item        Contains all the data of the customers
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since 1.5
	 *
	 */
	public function column_default( $item, $column_name ) {

		$date  = date( get_option( 'date_format' ), $item['time'] );
		$date  = cmplz_localize_date( $date );
		$time  = date( get_option( 'time_format' ), $item['time'] );
		$value = $date . " " . $time;

		return apply_filters( 'cmplz_cookiestatement_snapshots_column_'. $column_name, $value, $item['file'] );
	}

	public function column_name( $item ) {
		$name = ! empty( $item['file'] ) ? $item['file']
				: '<em>' . __( 'File not found', 'complianz-gdpr' ) . '</em>';

		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_url = $uploads['baseurl'];
		$url        = str_replace( $upload_dir, $upload_url, $item['path'] );
		$actions    = array(
				'download' => '<a href="' . $url . '" target="_blank">'
							  . __( 'Download', 'complianz-gdpr' ) . '</a>',
				'delete'   => '<a class="cmplz-delete-snapshot" data-id="'
							  . $item['file'] . '" href="#">' . __( 'Delete',
								'complianz-gdpr' ) . '</a>'
		);

		return $name . $this->row_actions( $actions );
	}


	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.5
	 */
	public function get_columns() {
		$columns = array(
				'name' => __( 'File', 'complianz-gdpr' ),
				'time' => __( 'Created', 'complianz-gdpr' ),
		);

		return apply_filters( 'cmplz_cookie_snapshots_columns', $columns );

	}

	/**
	 * Get the sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 2.1
	 */
	public function get_sortable_columns() {
		$columns = array(
				'name' => array( 'name', true ),
		);

		return $columns;
	}

	/**
	 * Outputs the reporting views
	 *
	 * @return void
	 * @since 1.5
	 */
	public function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place
	}

	/**
	 * Retrieve the current page number
	 *
	 * @return int Current page number
	 * @since 1.5
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @return mixed string If search is present, false otherwise
	 * @since 1.7
	 */

	public function get_search() {
		return ! empty( $_GET['s'] )
				? urldecode( trim( sanitize_text_field( $_GET['s'] ) ) ) : false;
	}

	public function date_select() {
		// Month Select
		$selected = false;
		if ( isset( $_GET['cmplz-month-select'] ) ) {
			if ( isset( $_GET['cmplz-year-select'] ) &&  $_GET['cmplz-year-select'] == 0) {
				$selected = 0;
			} else {
				$selected = intval($_GET['cmplz-month-select']);
			}
		}
		$months = array(
				__('Month',"complianz-gdpr"),
				__( 'January' ),
				__( 'February' ),
				__( 'March' ),
				__( 'April' ),
				__( 'May' ),
				__( 'June' ),
				__( 'July' ),
				__( 'August' ),
				__( 'September' ),
				__( 'October' ),
				__( 'November' ),
				__( 'December' )
		);
		echo '<select style="float:right" name="cmplz-month-select" id="cmplz-month-select" class="cmplz-month-select">';
		foreach($months as $value => $label) {
			echo '<option value="' . $value . '" ' . ($selected==$value ? 'selected' : '') . '>' . $label . '</option>';
		}
		echo '</select>';

		// Year Select
		$selected = false;
		if ( isset( $_GET['cmplz-year-select'] ) ) {
			if (isset($_GET['cmplz-month-select']) && $_GET['cmplz-month-select'] == 0) {
				$selected = 0;
			} else {
				$selected = intval($_GET['cmplz-year-select']);
			}
		}
		if (! empty($this->all) ) {
			$first_year = date("Y", $this->get_oldest_snapshot_time());
			$end_year = date("Y", $this->get_newest_snapshot_time());
			$years  = range($first_year, $end_year);
		} else {
			$years = array();
		}

		echo '<select style="float:right" name="cmplz-year-select" id="cmplz-year-select" class="cmplz-year-select">';
		echo '<option value="0" ' . ($selected==0 ? 'selected' : '') . '>'.__("Year","complianz-gdpr").'</option>';
		foreach($years as $year) {
			echo '<option value="' . $year . '" ' . ($selected==$year ? 'selected' : '') . '>' . $year . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Get time of oldest poc pdf
	 * @return int
	 */
	private function get_oldest_snapshot_time() {
		return min(array_column( $this->all, 'time' ));
	}

	/**
	 * Get time of latest poc pdf
	 * @return int
	 */
	private function get_newest_snapshot_time() {
		return max(array_column( $this->all, 'time' ));
	}

	/**
	 * Build all the reports data
	 *
	 * @return array $reports_data All the data for snapshots
	 * @global object $wpdb Used to query the database using the WordPress
	 *                      Database API
	 * @since 1.5
	 */
	public function reports_data() {

		if ( ! cmplz_user_can_manage() ) {
			return array();
		}

		$data    = array();
		$paged   = $this->get_paged();
		$offset  = $this->per_page * ( $paged - 1 );
		$search  = $this->get_search();
		$order   = isset( $_GET['order'] )
				? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$orderby = isset( $_GET['orderby'] )
				? sanitize_text_field( $_GET['orderby'] ) : 'id';

		$args = array(
				'number'  => $this->per_page,
				'offset'  => $offset,
				'order'   => $order,
				'orderby' => $orderby,
				'search'  => $search,
		);

		if ( isset( $_GET['cmplz-month-select'] ) && isset( $_GET['cmplz-year-select'] )
			 && $_GET['cmplz-month-select'] != 0 && $_GET['cmplz-year-select'] != 0 ) {
			$args['start_date']  = COMPLIANZ::$proof_of_consent->get_time_stamp_for_date($_GET['cmplz-year-select'], $_GET['cmplz-month-select'], 'start_date');
			$args['end_date']  = COMPLIANZ::$proof_of_consent->get_time_stamp_for_date($_GET['cmplz-year-select'], $_GET['cmplz-month-select'], 'end_date');
		}

		$this->args = $args;
		return COMPLIANZ::$proof_of_consent->get_cookie_snapshot_list( $args );
	}


	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $this->reports_data();
		$this->all   = COMPLIANZ::$proof_of_consent->get_cookie_snapshot_list();
		$this->total = is_array( $this->all ) ? count( $this->all ) : 0;
		// Add condition to be sure we don't divide by zero.
		// If $this->per_page is 0, then set total pages to 1.
		$total_pages = $this->per_page ? ceil( (int) $this->total
											   / (int) $this->per_page ) : 1;

		$this->set_pagination_args( array(
				'total_items' => $this->total,
				'per_page'    => $this->per_page,
				'total_pages' => $total_pages,
		) );
	}
}
