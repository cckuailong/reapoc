<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Create a new table class that will extend the WP_List_Table
 */
class cmp_subs_list_table extends WP_List_Table {
	var $dateformat;
	var $subscriber_list;

	function __construct(){
		$this->subscriber_list = get_option('niteoCS_subscribers_list');
		$this->dateformat = get_option('date_format');
		parent::__construct( array(
			'singular'  => __( 'subscriber', 'cmp-coming-soon-maintenance' ),     //singular name of the listed records
            'plural'    => __( 'subscribers', 'cmp-coming-soon-maintenance' ),   //plural name of the listed records
        ));
	}
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    function prepare_items() {

        $columns 	= $this->get_columns();
        $hidden 	= $this->get_hidden_columns();
        $sortable 	= $this->get_sortable_columns();
        $this->process_bulk_action();
        $data 		= $this->table_data();        
        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = is_array($data) ? count($data) : 0;

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        if (is_array($data)) {
        	usort( $data, array( &$this, 'sort_data' ) );
        	$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        }
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        

        
    }

   	// Displaying checkboxes!
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[%s]" value="%s" />', $item['id'], $item['id']
        );    
    }
	/**
	 * Define bulk actions
	 * 
	 * @since 1.2
	 * @returns array() $actions Bulk actions
	 */
	function get_bulk_actions() {
	    $actions = array(
	        'delete' 			=> __( 'Delete Selected' , 'cmp-coming-soon-maintenance'),
            'delete-all'            => __( 'Delete All' , 'cmp-coming-soon-maintenance'),
	        // 'export' 	=> __( 'Export Selected' , 'cmp-coming-soon-maintenance')
	    );

	    return $actions;
	}

    /**
     * Display delete action at each email row.
     *
     * @return Mixed
     */
    function column_email($item) {
        $complete_url = wp_nonce_url( sprintf('?page=%s&action=%s&id=%s', $_REQUEST['page'],'delete',$item['id']), 'cmp_delete_subscriber', '_nonce' );
        $actions = array(
                'delete'    => '<a href="'.$complete_url.'">'.__('Delete', 'cmp-coming-soon-maintenance').'</a>',
            );    

      return sprintf('%1$s %2$s', $item['email'], $this->row_actions($actions) );
    }

	/**
	 * Process bulk actions
	 * 
	 * @since 1.2
	 */
	function process_bulk_action() {
        // security check!
		// check onces and wordpress rights, else DIE
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Sorry, but this request is invalid.' );

        }

		// check onces and wordpress rights, else DIE
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
			if( !wp_verify_nonce($_GET['_nonce'], 'cmp_delete_subscriber' ) || !current_user_can('publish_pages') ) {
				die('Sorry, but this request is invalid.');
			}
		}

        $action = $this->current_action();

        switch ( $action ) {

            case 'delete-all':
                // save new subscribers array
                update_option('niteoCS_subscribers_list', false);
                // force reload page
                if ( headers_sent() ) {
                    echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=admin.php?page=cmp-subscribers" ) . "' />";
                } else {
                    wp_redirect( self_admin_url( "admin.php?page=cmp-subscribers" ) );
                }
            break;

            case 'delete':
                // if bulk action
                if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && is_array($_POST['id']) ) {
            	    foreach ( $this->subscriber_list as $key => $subscriber ) {
                        // unset posted ids from subscribers bulk action
					    if (in_array($subscriber['id'], $_POST['id'])) {
					    	unset($this->subscriber_list[$key]);
					    }
            		}
                }

            	// if delete action
            	if ( $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET['id'] ) ) {
                    foreach ($this->subscriber_list as $key => $subscriber) {
                        // unset posted id from subscribers delete
					    if ( $subscriber['id'] ==  $_GET['id'] ) {
					    	unset( $this->subscriber_list[$key] );
					    }
                    }
        		}

				// reindex subscribers array
				$this->subscriber_list = array_values( $this->subscriber_list );

				// change subscribers array column id
				foreach ($this->subscriber_list as $key => $subscriber) {
					$this->subscriber_list[$key]['id'] = $key;
				}
				// save new subscribers array
				update_option('niteoCS_subscribers_list', $this->subscriber_list);

                // force reload page
                if ( headers_sent() ) {
                    echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=admin.php?page=cmp-subscribers" ) . "' />";
                } else {
                    wp_redirect( self_admin_url( "admin.php?page=cmp-subscribers" ) );
                }
                exit;

                break;

            case 'export':
                return;
                break;

            default:
                // do nothing or something else
                return;
                break;
        }
        

        return;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    function get_columns() {
        $columns = array(
        	'cb'        	=> '<input type="checkbox" />',
            'id'			=> __('ID', 'cmp-coming-soon-maintenance'),
            'firstname'     => __('First Name', 'cmp-coming-soon-maintenance'),
            'lastname'      => __('Last Name', 'cmp-coming-soon-maintenance'),
            'email'			=> __('Email', 'cmp-coming-soon-maintenance'),
            'timestamp'		=> __('Time', 'cmp-coming-soon-maintenance'),
            'ip_address'	=> __('IP Address', 'cmp-coming-soon-maintenance'),

        );
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    function get_hidden_columns() {
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    function get_sortable_columns() {
        return array('timestamp' => array('timestamp', false));
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    function table_data() {       
    	return $this->subscriber_list;
    }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
            	return $item[ $column_name ]+1;
            case 'email':
            case 'ip_address':
            case 'firstname':
            case 'lastname':
				return isset($item[ $column_name ]) ? $item[ $column_name ] : '';

            case 'timestamp':
            	return date($this->dateformat, $item[ $column_name ]);
                
            default:
                return print_r( $item, true ) ;
        }
    }


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'timestamp';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }

	function no_items() {
	  _e( 'No subscribers yet!', 'cmp-coming-soon-maintenance' );
	}
}