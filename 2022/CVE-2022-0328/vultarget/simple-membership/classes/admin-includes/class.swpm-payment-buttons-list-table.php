<?php

if (!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SwpmPaymentButtonsListTable extends WP_List_Table {

    private $per_page;

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'payment button', //singular name of the listed records
            'plural' => 'payment buttons', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));

        $this->per_page = 50;
    }

    function column_default($item, $column_name) {
        //We need to read the values from our CPT and feed the column value for the given column name manually.

        switch ($column_name) {
            case 'title':
                return get_the_title($item['ID']);
                break;
            case 'membership_level':
                return get_post_meta($item['ID'], 'membership_level_id', true);
                break;
            case 'button_type':
                $button_type = get_post_meta($item['ID'], 'button_type', true);
                $button_name=SwpmMiscUtils::get_button_type_name($button_type);
                return $button_name;
                break;
            case 'button_shortcode':
                $level_id = get_post_meta($item['ID'], 'membership_level_id', true);
                if(!SwpmUtils::membership_level_id_exists($level_id)){
                    //This membership level doesn't exist. Show an error instead of the shortcode.
                    $shortcode = 'Error! The membership level you specified in this button does not exist. You may have deleted this level. Edit this button and use a valid membership level.';
                } else {
                    $shortcode = '[swpm_payment_button id='.$item['ID'].']';
                }
                return $shortcode;
                break;            
        }
    }

    function column_ID($item) {

        $button_type = get_post_meta($item['ID'], 'button_type', true);
        //Build row actions
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=simple_wp_membership_payments&tab=edit_button&button_id=%s&button_type=%s">Edit</a>', $item['ID'], $button_type),
            'delete' => sprintf('<a href="admin.php?page=simple_wp_membership_payments&tab=payment_buttons&action=delete_payment_btn&button_id=%s" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>', $item['ID']),
        );

        //Return the refid column contents
        return $item['ID'] . $this->row_actions($actions);
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /* $1%s */ $this->_args['singular'], //Let's reuse singular label (affiliate)
                /* $2%s */ $item['ID'] //The value of the checkbox should be the record's key/id
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'ID' => SwpmUtils::_('Payment Button ID'),
            'title' => SwpmUtils::_('Payment Button Title'),
            'membership_level' => SwpmUtils::_('Membership Level ID'),
            'button_type' => SwpmUtils::_('Button Type'),
            'button_shortcode' => SwpmUtils::_('Button Shortcode'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array();
//        $sortable_columns = array(
//            'ID' => array('ID', false), //true means its already sorted
//        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => SwpmUtils::_('Delete')
        );
        return $actions;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            $records_to_delete = array_map( 'sanitize_text_field', $_REQUEST['paymentbutton'] );
            if (empty($records_to_delete)) {
                echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
                return;
            }

            foreach ($records_to_delete as $record_id) {
                if(!is_numeric($record_id)){
                    wp_die('Error! ID must be a numeric number.');
                }
                wp_delete_post( $record_id );
            }
            echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
        }
    }

    function process_delete_action() {

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_payment_btn') { //Delete link was clicked for a row in list table
            $record_id = sanitize_text_field($_REQUEST['button_id']);
            if(!is_numeric($record_id)){
                wp_die('Error! ID must be a numeric number.');
            }
            wp_delete_post( $record_id );
            $success_msg = '<div id="message" class="updated"><p>';
            $success_msg .= SwpmUtils::_('The selected entry was deleted!');
            $success_msg .= '</p></div>';
            echo $success_msg;
        }
    }

    /**
     * Retrieve the current page number
     */
    function get_paged() {
        return isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    }

    /**
     * Retrieve the total number of CPT items
     */
    function get_total_items() {
        $counts = wp_count_posts('swpm_payment_button');
        $total = 0;
        foreach ($counts as $count)
            $total += $count;

        return $total;
    }

    function payment_buttons_data() {
        $data = array();
        $cpt_args = array(
            'post_type' => 'swpm_payment_button',
            'post_status' => 'publish',
            'posts_per_page' => $this->per_page,
            'paged' => $this->get_paged()
        );

        //TODO - Do search and sort stuff (see example code)
        
        //Retrieve all the CPT items
        $items = get_posts($cpt_args);
        if ($items) {
            foreach ($items as $item) {

                $membership_level = get_post_meta($item->ID, 'membership_level_id', true);
                $data[] = array(
                    'ID' => $item->ID,
                    'title' => get_the_title($item->ID),
                    'membership_level' => $membership_level,
                );
            }
        }

        return $data;
    }

    function prepare_items() {

        // Lets decide how many records per page to show
        $per_page = $this->per_page;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_delete_action();
        $this->process_bulk_action();

        // Pagination requirement
        $current_page = $this->get_pagenum();
        $total_items = $this->get_total_items();

        // Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
        $data = $this->payment_buttons_data();
        $this->items = $data;

        //pagination requirement
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

}
