<?php

include_once('ListTable.php');

class WPAM_List_Commission_Table extends WPAM_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'commission', //singular name of the listed records
            'plural' => 'commissions', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        //Just print the data for that column
        return $item[$column_name];
    }

    function column_transactionId($item) {
        
        //Build row actions
        $actions = array(
            'delete' => sprintf('<a href="admin.php?page=wpam-commission&delete_rowid=%s" onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>', $item['transactionId']),
        );

        //Return the id column contents
        return $item['transactionId'] . $this->row_actions($actions);
    }


    /* Custom column output - only use if you have some columns that needs custom output */
//    function column_<name_of_column>($item) {//Outputs the thubmnail image the way we want it
//        //$column_value = $item['<name_of_column'];
//        //DO some custom string manipulation        
//        return $column_value;
//    }


    /* overridden function to show a custom message when no records are present */

    function no_items() {
        echo '<br />'.__('No Commission Data Found!', 'affiliates-manager');
    }

    function column_cb($item) {
        return sprintf(
                        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                        /* $1%s */ $this->_args['singular'], //Let's reuse singular label
                        /* $2%s */ $item['transactionId'] //The value of the checkbox should be the record's key/id
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'transactionId' => __('Row ID', 'affiliates-manager'),
            'dateCreated' => __('Date', 'affiliates-manager'),
            'affiliateId' => __('Affiliate ID', 'affiliates-manager'),
            'amount' => __('Amount', 'affiliates-manager'),
            'referenceId' => __('Transaction ID', 'affiliates-manager'),
            'email' => __('Buyer Email', 'affiliates-manager')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'transactionId' => array('transactionId', false), //true means its already sorted
            'dateCreated' => array('dateCreated', false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'affiliates-manager')
        );
        return $actions;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered... //print_r($_GET);  
        if ('delete' === $this->current_action()) {
            $nvp_key = $this->_args['singular'];
            $records_to_delete = $_GET[$nvp_key];
            if (empty($records_to_delete)) {
                echo '<div id="message" class="updated fade"><p>' . __('Error! You need to select multiple records to perform a bulk action!', 'affiliates-manager') . '</p></div>';
                return;
            }
            global $wpdb;
            $record_table_name = WPAM_TRANSACTIONS_TBL; //The table name for the records 
            foreach ($records_to_delete as $row) {           
                $updatedb = "DELETE FROM $record_table_name WHERE transactionId='$row'";
                $results = $wpdb->query($updatedb);
            }
            echo '<div id="message" class="updated fade"><p>' . __('Selected records deleted successfully!', 'affiliates-manager') . '</p></div>';
        }
    }
    
    function process_individual_action() {

        if (isset($_REQUEST['page']) && 'wpam-commission' == $_REQUEST['page']) {
            if (isset($_REQUEST['delete_rowid'])) { //delete a transaction record
                $row_id = esc_sql($_REQUEST['delete_rowid']);
                if(!is_numeric($row_id)){
                    return;
                }
                global $wpdb;
                $record_table_name = WPAM_TRANSACTIONS_TBL; //The table name for the records              
                $updatedb = "DELETE FROM $record_table_name WHERE transactionId='$row_id'";
                $result = $wpdb->query($updatedb);
                echo '<div id="message" class="updated fade"><p>' . __('Selected record deleted successfully!', 'affiliates-manager') . '</p></div>';
            }
        }
    }

    function prepare_items() {
        // Lets decide how many records per page to show     
        $per_page = '50';

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_individual_action();
        $this->process_bulk_action();

        // This checks for sorting input and sorts the data.
        $orderby_column = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
        $sort_order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
        if (empty($orderby_column)) {
            $orderby_column = "transactionId";
            $sort_order = "DESC";
        }
        global $wpdb;
        $records_table_name = WPAM_TRANSACTIONS_TBL; //The table to query
        
        //pagination requirement
        $current_page = $this->get_pagenum();
        
        //count the total number of items
        $query = "SELECT COUNT(*) FROM $records_table_name WHERE type = 'credit'";
        $total_items = $wpdb->get_var($query);
        
        $query = "SELECT * FROM $records_table_name WHERE type = 'credit' ORDER BY $orderby_column $sort_order";

        $offset = ($current_page - 1) * $per_page;
        $query.=' LIMIT ' . (int) $offset . ',' . (int) $per_page;

        $data = $wpdb->get_results($query, ARRAY_A);       

        // Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
        $this->items = $data;

        //pagination requirement
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

}
