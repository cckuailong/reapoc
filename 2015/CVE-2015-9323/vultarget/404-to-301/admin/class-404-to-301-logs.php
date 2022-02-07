<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die('Damn it.! Dude you are looking for what?');
}

/**
 * WP_List_Table is marked as private by WordPress. So they change it.
 * Details here - https://codex.wordpress.org/Class_Reference/WP_List_Table
 * So we have copied this class and using independently to avoid future issues. 
 */

if( ! class_exists( 'WP_List_Table_404' ) ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/core/class-wp-list-table.php';
}

/**
 * The listing page class for error logs.
 *
 * This class defines all the methods to output the error logs display table using
 * WordPress listing table class.
 *
 * @link       http://iscode.co/product/404-to-301/
 * @since      2.0.0
 * @package    I4T3
 * @subpackage I4T3/admin
 * @author     Joel James <me@joelsays.com>
 */
class _404_To_301_Logs extends WP_List_Table_404 {

    /**
     * The table name of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $table    The table name of this plugin in db.
     */
    private $table;

    /**
     * Data to be displayed in table.
     *
     * @since    2.0.0
     * @access   private
     * @var      array    $log_data    The error log data from db.
     */
	private $log_data;
    

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.0.0
     * @var     string    $table      The name of the table of plugin.
     * @var     array    $log_data    The error log data from db.
     * @var     global   $status, $page  Global variables from WordPress
     */
    public function __construct( $table ) {

        global $status, $page;
        $this->table = $table;
        $this->log_data = $this->i4t3_get_log_data();
		
        parent::__construct( array(
            'singular'  => __( 'i4t3_log', __( '404 Log', '404-to-301' ) ),     //singular name of the listed records
            'plural'    => __( 'i4t3_logs', __( '404 Logs', '404-to-301' ) ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
          )
        );

        $this->process_bulk_action(); // To perform bulk delete action
    }

    /**
     * Error log data to be displayed.
     *
     * Getting the error log data from the database and converts it to
     * the required structure.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @var     $wpdb    Global variable for db class.
     * @uses    apply_filters   i4t3_log_list_per_page  Custom filter to modify per page view.
     * @return  array   $error_data     Array of error log data.
     */
    public function i4t3_get_log_data() {

        global $wpdb;
        // Let us hide sql query errors
        $wpdb->hide_errors();

        // Per page filter
		$per_page = apply_filters( 'i4t3_log_list_per_page', 10 );

        $current_page = $this->get_pagenum();
		$limit = ( $current_page-1 )* $per_page;
		
		// If no sort, default to title
        $orderby = ( isset( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
        // If no order, default to asc
        $order = ( isset($_GET['order'] ) ) ? $_GET['order'] : 'ASC';

        $log_data = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS id as ID,date,url,ref,ip,ua FROM $this->table ORDER BY $orderby $order LIMIT %d,%d", array( $limit, $per_page) ), ARRAY_A );

        $error_data = array();
        foreach ($log_data as $key) {
          $error_data[] = $key;
        }
		
        return $error_data;
    }

    /**
     * Empty record text.
     *
     * Custom text to display where there is nothing to display in error
     * log table.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @return  void
     */
    public function no_items() {

        _e( 'Ulta pulta..! Seems like you had no errors to log.' );
    }


    /**
     * Default columns in list table.
     *
     * To show columns in error log list table. If there is nothing
     * for switch, printing the whole array.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @uses    switch    To switch between colums.
     */
    public function column_default( $item, $column_name ) {

        switch( $column_name ) {
            case 'date':
            case 'url':
            case 'ref':
            case 'ip':
            case 'ua':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }


    /**
     * Make colums sortable
     *
     * To make our custom columns in list table sortable. We have included
     * 4 columns except 'User Agent' column here.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @return  array   $sortable_columns    Array of columns to enable sorting.
     */
    public function get_sortable_columns() {

        $sortable_columns = array(
          'date'  => array('date',false),
          'url' => array('url',false),
          'ref' => array('ref',false),
          'ip'   => array('ip',false)
        );

        return $sortable_columns;
    }


    /**
     * Column titles
     *
     * Custom column titles to be displayed in listing table. You can change this to anything
     *
     * @since       2.0.0
     * @author      Joel James.
     * @return      array   $columns   Array of cloumn titles.
     */
    public function get_columns() {

        $columns = array(
            'date'=> __( 'Date and Time', '404-to-301' ),
            'url' => __( '404 Path', '404-to-301' ),
            'ref' => __( 'Came From', '404-to-301' ), // referer
            'ip'  => __( 'IP Address', '404-to-301' ),
            'ua'  => __( 'User Agent', '404-to-301' )
        );

        return $columns;
    }


    /**
     * Bulk actions drop down
     *
     * Options to be added to the bulk actions drop down for users
     * to select. We have added 'clear log' action.
     *
     * @since       2.0.0
     * @author      Joel James.
     * @return      array    $actions   Options to be added to the action select box.
     */
    public function get_bulk_actions() {

        $actions = array(
          'clear'    => __( 'Clear Logs', '404-to-301' )
        );

        return $actions;
    }


    /**
     * To modify the date column data
     *
     * This function is used to modify the column data for date in listing table.
     * We can change styles, texts etc. using this function.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @return  string    $date_data    Date column text data.
     */
    public function column_date( $item ) {
	
        // Apply filter - i4t3_log_list_date_column
        $date_data = apply_filters( 'i4t3_log_list_date_column', date("j M Y, g:i a", strtotime($item['date'])) );

        return $date_data;
    }


    /**
     * To modify the url column data
     *
     * This function is used to modify the column data for url in listing table.
     * We can change styles, texts etc. using this function.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @return  string    $url_data    Url column text data.
     */
    public function column_url( $item ) {

        // Apply filter - i4t3_log_list_url_column
        $url_data = apply_filters( 'i4t3_log_list_url_column', '<p class="i4t3-url-p">'.$item['url'].'</p>' );

        return $url_data;
    }


    /**
     * To modify the ref column data
     *
     * This function is used to modify the column data for ref in listing table.
     * We can change styles, texts etc. using this function.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @return  string    $ref_data    Ref column text data.
     */
    public function column_ref( $item ) {

        // Apply filter - i4t3_log_list_ref_column
        $ref_data = apply_filters( 'i4t3_log_list_ref_column', '<a href="'.$item['ref'].'">'.$item['ref'].'</a>' );

        return $ref_data;
    }


    /**
     * Main function to output the listing table using WP_List_Table class
     *
     * As name says, this function is used to prepare the lsting table based
     * on the custom rules and filters that we have given.
     * This function extends the lsiting table class and uses our custom data
     * to list in the table.
     * Here we set pagination, columns, sorting etc.
     * $this->items - Push our custom log data to the listing table.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @uses    $wpdb    The global variable for WordPress database operations.
     * @uses    hide_errors()   To hide if there are SQL query errors.
     */
    public function prepare_items() {
		
		global $wpdb;
		// Let us hide sql query errors
        $wpdb->hide_errors();
        
		$columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
		
        $current_page = $this->get_pagenum();

        // Per page filter
		$per_page = apply_filters( 'i4t3_log_list_per_page', 10 );
		
		$total = $wpdb->get_var( "SELECT count(id) as ID FROM $this->table" );
		
        if( $total ) {
			$total_items = $total;
		} else {
			$total_items = count( $this->log_data );
		}

        // only ncessary because we have sample data
        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
          )
        );

        $this->items = $this->log_data;
    }


    /**
     * To perform bulk actions.
     *
     * This function is used to check if bulk action is set in post.
     * If set it will call the required functions to perform the task.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @uses    wp_verify_nonce    To verify if the request is from WordPress.
     */
    public function process_bulk_action() {

        if( isset($_POST['_wpnonce'])) {

            $nonce  = '';
            $action = '';
            // security check!
            if ( ! empty( $_POST['_wpnonce'] ) ) {

                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];
            }

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

            $action = $this->current_action();
			if( $action == 'clear' ) {
				$this->clear_404_logs();
			}
        }

    }

    /**
     * To perform clear logs.
     *
     * This function is used to clear the entire log from db. This function is called
     * from process_bulk_action() method.
     * This function will delete the entire data from our log table and this can't be UNDONE.
     *
     * @since   2.0.0
     * @author  Joel James.
     * @retun   true if deleted something, else false.
     */
    public function clear_404_logs() {

        global $wpdb;
        // Let us hide sql query errors if any
        $wpdb->hide_errors();
        $total = $wpdb->query( "DELETE FROM $this->table" );
        if ( $total > 0 ) {
			wp_redirect(admin_url('admin.php?page=i4t3-logs'));
			exit();
		}
    }

}