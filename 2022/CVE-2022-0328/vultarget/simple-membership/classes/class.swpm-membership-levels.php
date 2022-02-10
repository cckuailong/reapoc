<?php

if (!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SwpmMembershipLevels extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => SwpmUtils::_('Membership Level'),
            'plural' => SwpmUtils::_('Membership Levels'),
            'ajax' => false
        ));
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />'
            , 'id' => SwpmUtils::_('ID')
            , 'alias' => SwpmUtils::_('Membership Level')
            , 'role' => SwpmUtils::_('Role')
            , 'valid_for' => SwpmUtils::_('Access Valid For/Until')
        );
    }

    function get_sortable_columns() {
        return array(
            'id' => array('id', true),
            'alias' => array('alias', true)
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'bulk_delete' => SwpmUtils::_('Delete')
        );
        return $actions;
    }

    function column_default($item, $column_name) {
        if ($column_name == 'valid_for') {
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::NO_EXPIRY) {
                return 'No Expiry';
            }
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::FIXED_DATE) {
                $formatted_date = SwpmUtils::get_formatted_date_according_to_wp_settings($item['subscription_period']);
                return $formatted_date;
            }
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::DAYS) {
                return $item['subscription_period'] . " Day(s)";
            }
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::WEEKS) {
                return $item['subscription_period'] . " Week(s)";
            }
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::MONTHS) {
                return $item['subscription_period'] . " Month(s)";
            }
            if ($item['subscription_duration_type'] == SwpmMembershipLevel::YEARS) {
                return $item['subscription_period'] . " Year(s)";
            }
        }
        if ($column_name == 'role') {
            return ucfirst($item['role']);
        }
        return stripslashes($item[$column_name]);
    }

    function column_id($item) {
        $delete_swpmlevel_nonce = wp_create_nonce( 'nonce_delete_swpmlevel_admin_end' );

        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=simple_wp_membership_levels&level_action=edit&id=%s">Edit</a>', $item['id']),
            'delete' => sprintf('<a href="admin.php?page=simple_wp_membership_levels&level_action=delete&id=%s&delete_swpmlevel_nonce=%s" onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>', $item['id'],$delete_swpmlevel_nonce),
        );
        return $item['id'] . $this->row_actions($actions);
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="ids[]" value="%s" />', $item['id']
        );
    }

    function prepare_items() {
        global $wpdb;

        $this->process_bulk_action();

        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        if (isset($_POST['s'])){
            $search_keyword = sanitize_text_field($_POST['s']);
            $search_keyword = esc_attr ($search_keyword);
            $query .= " AND alias LIKE '%" . $search_keyword . "%' ";
        }

        //Read and sanitize the sort inputs.
        $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'id';
        $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';

        $sortable_columns = $this->get_sortable_columns();
        $orderby = SwpmUtils::sanitize_value_by_array($orderby, $sortable_columns);
        $order = SwpmUtils::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

        if (!empty($orderby) && !empty($order)) {
            $query.=' ORDER BY ' . $orderby . ' ' . $order;
        }

        $totalitems = $wpdb->query($query); //Return the total number of affected rows
        $perpage = 50;
        $paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $wpdb->get_results($query, ARRAY_A);
    }

    function no_items() {
        SwpmUtils::e('No membership levels found.');
    }

    function process_form_request() {
        if (isset($_REQUEST['id'])) {
            //This is a level edit action
            $record_id = sanitize_text_field($_REQUEST['id']);
            if(!is_numeric($record_id)){
                wp_die('Error! ID must be numeric.');
            }
            return $this->edit($record_id);
        }

        //Level add action
        return $this->add();
    }

    function add() {
        //Level add interface
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_add_level.php');
        return false;
    }

    function edit($id) {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_membership_tbl WHERE id = %d", absint($id));
        $membership = $wpdb->get_row($query, ARRAY_A);
        extract($membership, EXTR_SKIP);
        $email_activation = get_option('swpm_email_activation_lvl_'.$id);
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_edit_level.php');
        return false;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        global $wpdb;

        if ('bulk_delete' === $this->current_action()) {
            $records_to_delete = array_map( 'sanitize_text_field', $_REQUEST['ids'] );
            if (empty($records_to_delete)) {
                echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
                return;
            }
            foreach ($records_to_delete as $record_id) {
                if( !is_numeric( $record_id )){
                    wp_die('Error! ID must be numeric.');
                }
                $query = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $record_id);
                $wpdb->query($query);
            }
            echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
        }
    }

    function delete_level() {
        global $wpdb;
        if (isset($_REQUEST['id'])) {

            //Check we are on the admin end and user has management permission
            SwpmMiscUtils::check_user_permission_and_is_admin('membership level delete');

            //Check nonce
            if ( !isset($_REQUEST['delete_swpmlevel_nonce']) || !wp_verify_nonce($_REQUEST['delete_swpmlevel_nonce'], 'nonce_delete_swpmlevel_admin_end' )){
                //Nonce check failed.
                wp_die(SwpmUtils::_("Error! Nonce verification failed for membership level delete from admin end."));
            }

            $id = sanitize_text_field($_REQUEST['id']);
            $id = absint($id);
            $query = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $id);
            $wpdb->query($query);
            echo '<div id="message" class="updated fade"><p>Selected record deleted successfully!</p></div>';
        }
    }

    function show_levels() {
        ?>
        <div class="swpm-margin-top-10"></div>
        <form method="post">
            <p class="search-box">
                <label class="screen-reader-text" for="search_id-search-input">
                    search:</label>
                <input id="search_id-search-input" type="text" name="s" value="" />
                <input id="search-submit" class="button" type="submit" name="" value="<?php echo  SwpmUtils::_('Search')?>" />
            </p>
        </form>

        <?php $this->prepare_items(); ?>
        <form method="post">
            <?php $this->display(); ?>
        </form>

        <p>
            <a href="admin.php?page=simple_wp_membership_levels&level_action=add" class="button-primary"><?php SwpmUtils::e('Add New') ?></a>
        </p>
        <?php
    }

    function manage() {
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_membership_manage.php');
    }

    function manage_categroy() {
        $selected = "category_list";
        include_once('class.swpm-category-list.php');
        $category_list = new SwpmCategoryList();
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_category_list.php');
    }

    function manage_post() {
        $selected = "post_list";
        include_once('class.swpm-post-list.php');
        $post_list = new SwpmPostList();
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_post_list.php');
    }

    function handle_main_membership_level_admin_menu(){
        do_action( 'swpm_membership_level_menu_start' );

        //Check current_user_can() or die.
        SwpmMiscUtils::check_user_permission_and_is_admin('Main Membership Level Admin Menu');

        $level_action = filter_input(INPUT_GET, 'level_action');
        $action = $level_action;
        $selected= $action;

        ?>
        <div class="wrap swpm-admin-menu-wrap"><!-- start wrap -->

        <!-- page title -->
        <h1><?php echo  SwpmUtils::_('Simple WP Membership::Membership Levels') ?></h1>

        <!-- start nav menu tabs -->
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo ($selected == "") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels"><?php echo SwpmUtils::_('Membership Levels') ?></a>
            <a class="nav-tab <?php echo ($selected == "add") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=add"><?php echo SwpmUtils::_('Add Level') ?></a>
            <a class="nav-tab <?php echo ($selected == "manage") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=manage"><?php echo SwpmUtils::_('Manage Content Protection') ?></a>
            <a class="nav-tab <?php echo ($selected == "category_list") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=category_list"><?php echo SwpmUtils::_('Category Protection') ?></a>
            <a class="nav-tab <?php echo ($selected == "post_list") ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=post_list"><?php echo SwpmUtils::_('Post and Page Protection') ?></a>
            <?php

            //Trigger hooks that allows an extension to add extra nav tabs in the membership levels menu.
            do_action ('swpm_membership_levels_menu_nav_tabs', $selected);

            $menu_tabs = apply_filters('swpm_membership_levels_additional_menu_tabs_array', array());
            foreach ($menu_tabs as $level_action => $title){
                ?>
                <a class="nav-tab <?php echo ($selected == $member_action) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=<?php echo $level_action; ?>" ><?php SwpmUtils::e($title); ?></a>
                <?php
            }

            ?>
        </h2>
        <!-- end nav menu tabs -->

        <?php

        do_action( 'swpm_membership_level_menu_after_nav_tabs' );

        //Trigger hook so anyone listening for this particular action can handle the output.
        do_action( 'swpm_membership_level_menu_body_' . $action );

        //Allows an addon to completely override the body section of the membership level admin menu for a given action.
        $output = apply_filters('swpm_membership_level_menu_body_override', '', $action);
        if (!empty($output)) {
            //An addon has overriden the body of this page for the given action. So no need to do anything in core.
            echo $output;
            echo '</div>';//<!-- end of wrap -->
            return;
        }

        //Switch case for the various different actions handled by the core plugin.
        switch ($action) {
            case 'add':
            case 'edit':
                $this->process_form_request();
                break;
            case 'manage':
                $this->manage();
                break;
            case 'category_list':
                $this->manage_categroy();
                break;
            case 'post_list':
                $this->manage_post();
                break;
            case 'delete':
                $this->delete_level();
            default:
                $this->show_levels();
                break;
        }

        echo '</div>';//<!-- end of wrap -->
    }

}
