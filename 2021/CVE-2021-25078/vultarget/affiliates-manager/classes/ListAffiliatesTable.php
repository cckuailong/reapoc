<?php

include_once('ListTable.php');

class WPAM_List_Affiliates_Table extends WPAM_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'affiliate', //singular name of the listed records
            'plural' => 'affiliates', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        //Just print the data for that column
        return $item[$column_name];
    }

    function column_affiliateId($item) {
        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=wpam-affiliates&viewDetail=%s">View</a>', $item['affiliateId']),
            'delete' => sprintf('<a href="admin.php?page=wpam-affiliates&delete_aid=%s" onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>', $item['affiliateId']),
        );

        //Return the id column contents
        return $item['affiliateId'] . $this->row_actions($actions);
    }

    function column_dateCreated($item) {
        $item['dateCreated'] = date("m/d/Y", strtotime($item['dateCreated']));
        return $item['dateCreated'];
    }

    function column_viewDetail($item) {
        $item['viewDetail'] = '<a class="button-secondary" href="admin.php?page=wpam-affiliates&viewDetail=' . $item['affiliateId'] . '">' . __('View', 'affiliates-manager') . '</a>';
        return $item['viewDetail'];
    }

    /* overridden function to show a custom message when no records are present */

    function no_items() {
        echo '<br />No affiliates Found!';
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /* $1%s */ $this->_args['singular'], //Let's reuse singular label
                /* $2%s */ $item['affiliateId'] //The value of the checkbox should be the record's key/id
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'affiliateId' => __('Affiliate ID', 'affiliates-manager'),
            'status' => __('Status', 'affiliates-manager'),
            'balance' => __('Balance', 'affiliates-manager'),
            'earnings' => __('Earnings', 'affiliates-manager'),
            'firstName' => __('First Name', 'affiliates-manager'),
            'lastName' => __('Last Name', 'affiliates-manager'),
            'email' => __('Email', 'affiliates-manager'),
            'companyName' => __('Company', 'affiliates-manager'),
            'dateCreated' => __('Date Joined', 'affiliates-manager'),
            'websiteUrl' => __('Website', 'affiliates-manager'),
            'viewDetail' => __('', 'affiliates-manager'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'affiliateId' => array('affiliateId', false), //true means its already sorted
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
            foreach ($records_to_delete as $row) {
                //TODO delete all the selected rows
                global $wpdb;
                $record_table_name = WPAM_AFFILIATES_TBL; //The table name for the records
                $selectdb = $wpdb->get_row("SELECT * FROM $record_table_name WHERE affiliateId='$row'");
                $aff_email = $selectdb->email;
                $user = get_user_by('email', $aff_email);
                if ($user) {
                    if (!in_array('administrator', $user->roles)) {
                        wp_delete_user($user->ID);
                    }
                }
                $updatedb = "DELETE FROM $record_table_name WHERE affiliateId='$row'";
                $results = $wpdb->query($updatedb);
            }
            echo '<div id="message" class="updated fade"><p>' . __('Selected records deleted successfully!', 'affiliates-manager') . '</p></div>';
        }
    }

    function process_individual_action() {

        if (isset($_REQUEST['page']) && 'wpam-affiliates' == $_REQUEST['page']) {
            if (isset($_REQUEST['delete_aid'])) { //delete an affiliate record
                $aid = esc_sql($_REQUEST['delete_aid']);
                if (!is_numeric($aid)) {
                    return;
                }
                global $wpdb;
                $record_table_name = WPAM_AFFILIATES_TBL; //The table name for the records
                if(get_option(WPAM_PluginConfig::$AutoDeleteWPUserAccount) == 1){
                    $selectdb = $wpdb->get_row("SELECT * FROM $record_table_name WHERE affiliateId='$aid'");
                    $aff_email = $selectdb->email;
                    $user = get_user_by('email', $aff_email);
                    if ($user) {
                        if (!in_array('administrator', $user->roles)) {
                            wp_delete_user($user->ID);
                        }
                    }
                }
                $updatedb = "DELETE FROM $record_table_name WHERE affiliateId='$aid'";
                $result = $wpdb->query($updatedb);
                echo '<div id="message" class="updated fade"><p>' . __('Selected record deleted successfully!', 'affiliates-manager') . '</p></div>';
            }
        }
    }

    function prepare_items($ignore_pagination = false) {
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
            $orderby_column = "affiliateId";
            $sort_order = "DESC";
        }
        global $wpdb;
        $aff_table_name = WPAM_AFFILIATES_TBL;
        $trn_table_name = WPAM_TRANSACTIONS_TBL;

        //pagination requirement
        $current_page = $this->get_pagenum();

        $where = "";
        if (isset($_REQUEST['statusFilter']) && !empty($_REQUEST['statusFilter'])) {
            $status = esc_sql($_REQUEST['statusFilter']);
            if ($status == "all") {
                $where = "";
            } else if ($status == "all_active") {
                $where = " where status != 'declined' and status != 'blocked' and status != 'inactive'";
            } else {
                $where = " where status = '$status'";
            }
        }

        //count the total number of items
        $query = "select count(*) from $aff_table_name" . $where;

        $total_items = $wpdb->get_var($query);

        $query = "select
                $aff_table_name.*,
                (
                        select coalesce(sum(tr.amount),0)
                        from $trn_table_name tr
                        where
                                tr.affiliateId = $aff_table_name.affiliateId
                                and tr.status != 'failed'
                ) balance,
                (
                        select coalesce(sum(IF(tr.type = 'credit', amount, 0)),0)
                        from $trn_table_name tr
                        where
                                tr.affiliateId = $aff_table_name.affiliateId
                                and tr.status != 'failed'
                ) earnings
        from $aff_table_name 
        $where
        ORDER BY $orderby_column $sort_order     
        ";

        //pagination requirement
        if (!$ignore_pagination) {
            $offset = ($current_page - 1) * $per_page;
            $query .= ' LIMIT ' . (int) $offset . ',' . (int) $per_page;
            $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page, //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
            ));
        }
        $data = $wpdb->get_results($query, ARRAY_A);
        /*
          $records_table_name = WPAM_TRACKING_TOKENS_TBL; //The table to query
          $resultset = $wpdb->get_results("SELECT * FROM $records_table_name ORDER BY $orderby_column $sort_order", OBJECT);
         */
        // Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
        $this->items = $data;
    }

}
