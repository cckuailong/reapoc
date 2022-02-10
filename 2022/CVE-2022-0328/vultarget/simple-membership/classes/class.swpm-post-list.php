<?php

/**
 * BCategoryList
 *
 * @author nur
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SwpmPostList extends WP_List_Table {

    public $selected_level_id = 1;
    public $post;
    public $type;

    function __construct() {
        parent::__construct(array(
            'singular' => SwpmUtils::_('Membership Level'),
            'plural' => SwpmUtils::_('Membership Levels'),
            'ajax' => false
        ));
        $selected = filter_input(INPUT_POST, 'membership_level_id');
        $this->selected_level_id = empty($selected) ? 1 : $selected;
        $this->post = ($this->selected_level_id == 1) ?
                SwpmProtection::get_instance() :
                SwpmPermission::get_instance($this->selected_level_id);
        $this->type = filter_input(INPUT_GET, 'list_type');
        if (is_null($this->type)) {
            $this->type = filter_input(INPUT_POST, 'list_type');
        }
        if (is_null($this->type)) {
            $this->type = 'post';
        }
    }

    function get_columns() {
        switch ($this->type) {
            case 'page':
                return array(
                    'cb' => '<input type="checkbox" />'
                    , 'date' => SwpmUtils::_('Date')
                    , 'title' => SwpmUtils::_('Title')
                    , 'author' => SwpmUtils::_('Author')
                    , 'status' => SwpmUtils::_('Status')
                );
                break;
            case 'post':
                return array(
                    'cb' => '<input type="checkbox" />'
                    , 'date' => SwpmUtils::_('Date')
                    , 'title' => SwpmUtils::_('Title')
                    , 'author' => SwpmUtils::_('Author')
                    , 'categories' => SwpmUtils::_('Categories')
                    , 'status' => SwpmUtils::_('Status')
                );
                break;
            case 'custom_post':
                return array(
                    'cb' => '<input type="checkbox" />'
                    , 'date' => SwpmUtils::_('Date')
                    , 'title' => SwpmUtils::_('Title')
                    , 'author' => SwpmUtils::_('Author')
                    , 'type' => SwpmUtils::_('Type')
                    , 'status' => SwpmUtils::_('Status')
                );
                break;
        }
    }

    function get_sortable_columns() {
        return array();
    }

    function column_default($item, $column_name) {
        return stripslashes($item[$column_name]);
    }

    function column_term_id($item) {
        return $item->term_id;
    }

    function column_taxonomy($item) {
        $taxonomy = $item->taxonomy;
        if ($taxonomy == 'category') {
            $taxonomy = 'Post Category';
        } else {
            $taxonomy = 'Custom Post Type (' . $taxonomy . ')';
        }
        return $taxonomy;
    }

    function column_cb($item) {
        return sprintf(
                '<input type="hidden" name="ids_in_page[]" value="%s">
            <input type="checkbox" %s name="ids[]" value="%s" />', $item['ID'], $item['protected'], $item['ID']
        );
    }

    public static function update_post_list() {
        //Check we are on the admin end and user has management permission 
        SwpmMiscUtils::check_user_permission_and_is_admin('post protection update');

        //Check nonce
        $swpm_post_prot_update_nonce = filter_input(INPUT_POST, 'swpm_post_prot_update_nonce');
        if (!wp_verify_nonce($swpm_post_prot_update_nonce, 'swpm_post_prot_update_nonce_action')) {
            //Nonce check failed.
            wp_die(SwpmUtils::_("Error! Nonce security verification failed for Post Protection Update action. Clear cache and try again."));
        }
        
        $type = filter_input(INPUT_POST, 'list_type');

        $selected = filter_input(INPUT_POST, 'membership_level_id');
        $selected_level_id = empty($selected) ? 1 : $selected;
        $post = ($selected_level_id == 1) ?
                SwpmProtection::get_instance() :
                SwpmPermission::get_instance($selected_level_id);
        $args = array('ids' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_REQUIRE_ARRAY,
        ));
        $filtered = filter_input_array(INPUT_POST, $args);
        $ids = $filtered['ids'];
        $args = array('ids_in_page' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_REQUIRE_ARRAY,
        ));
        $filtered = filter_input_array(INPUT_POST, $args);
        $ids_in_page = $filtered['ids_in_page'];
        $post->remove($ids_in_page, $type)->apply($ids, $type)->save();
        $message = array('succeeded' => true, 'message' => '<p class="swpm-green-box">' . SwpmUtils::_('Protection settings updated!') . '</p>');
        SwpmTransfer::get_instance()->set('status', $message);
    }

    function prepare_items() {
        global $wpdb;
        switch ($this->type) {
            case 'page':
                $args = array(
                    'child_of' => 0,
                    'sort_order' => 'ASC',
                    'sort_column' => 'post_title',
                    'hierarchical' => 0,
                    'parent' => -1,
                );
                $all_pages = get_pages($args);
                $filtered_items = array();
                foreach ($all_pages as $page) {
                    $page_summary = array();
                    $user_info = get_userdata($page->post_author);
                    $page_summary['protected'] = $this->post->in_pages($page->ID) ? " checked='checked'" : "";
                    $page_summary['ID'] = $page->ID;
                    $page_summary['date'] = $page->post_date;
                    $page_summary['title'] = '<a href="' . get_permalink($page->ID) . '" target="_blank">' . $page->post_title . '</a>';
                    $page_summary['author'] = $user_info->user_login;
                    $page_summary['status'] = $page->post_status;
                    $filtered_items[] = $page_summary;
                }
                break;
            case 'post':
                $sql = "SELECT ID,post_date,post_title,post_author, post_type, post_status FROM $wpdb->posts ";
                $sql .= " WHERE post_type = 'post' AND post_status = 'publish'";
                $all_posts = $wpdb->get_results($sql);
                $filtered_items = array();
                foreach ($all_posts as $post) {
                    //if($post->post_type=='page')continue;
                    $post_summary = array();
                    $user_info = get_userdata($post->post_author);
                    $categories = get_the_category($post->ID);
                    $cat = array();
                    foreach ($categories as $category)
                        $cat[] = $category->category_nicename;
                    $post_summary['protected'] = $this->post->in_posts($post->ID) ? " checked='checked'" : "";
                    $post_summary['ID'] = $post->ID;
                    $post_summary['date'] = $post->post_date;
                    $post_summary['title'] = '<a href="' . get_permalink($post->ID) . '" target="_blank">' . $post->post_title . '</a>';
                    $post_summary['author'] = $user_info->user_login;
                    $post_summary['categories'] = rawurldecode(implode(' ', $cat));
                    $post_summary['status'] = $post->post_status;
                    $filtered_items[] = $post_summary;
                }
                break;
            case 'custom_post':
                $filtered_items = array();
                $args = array('public' => true, '_builtin' => false);
                $post_types = get_post_types($args);
                $arg = "'" . implode('\',\'', $post_types) . "'";
                if (!empty($arg)) {
                    $sql = "SELECT ID,post_date,post_title,post_author, post_type, post_status FROM $wpdb->posts ";
                    $sql .= " WHERE post_type IN (" . $arg . ") AND (post_status='inherit' OR post_status='publish')";
                    $all_posts = $wpdb->get_results($sql);
                    foreach ($all_posts as $post) {
                        $post_summary = array();
                        $user_info = get_userdata($post->post_author);
                        $post_summary['protected'] = $this->post->in_custom_posts($post->ID) ? "checked='checked'" : "";
                        $post_summary['ID'] = $post->ID;
                        $post_summary['date'] = $post->post_date;
                        $post_summary['title'] = '<a href="' . get_permalink($post->ID) . '" target="_blank">' . $post->post_title . '</a>';
                        $post_summary['author'] = $user_info->user_login;
                        $post_summary['type'] = $post->post_type;
                        $post_summary['status'] = $post->post_status;
                        $filtered_items[] = $post_summary;
                    }
                }
                break;
        }
        $totalitems = count($filtered_items);
        $perpage = 100;
        $paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        $offset = 0;
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
        }
        for ($i = $offset; $i < ((int) $offset + (int) $perpage) && !empty($filtered_items[$i]); $i++) {
            $all_items[] = $filtered_items[$i];
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
        $this->items = $all_items;
    }

    function no_items() {
        SwpmUtils::e('No items found.');
    }

}
