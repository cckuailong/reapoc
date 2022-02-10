<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')){
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

class Asgaros_Forum_Admin_UserGroups_Table extends WP_List_Table {
    public $table_data = array();

    public function __construct($table_data) {
        $this->table_data = $table_data;

        parent::__construct(
            array(
                'singular'  => 'usergroup',
                'plural'    => 'usergroups',
                'ajax'      => false
            )
        );
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }

    public function column_name($item) {
        $users_i18n = number_format_i18n($item['users']);

        $columnHTML = '';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_name" value="'.esc_html(stripslashes($item['name'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_color" value="'.esc_html(stripslashes($item['color'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_visibility" value="'.esc_html(stripslashes($item['visibility'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_auto_add" value="'.esc_html(stripslashes($item['auto_add'])).'">';
        $columnHTML .= '<input type="hidden" id="usergroup_'.$item['term_id'].'_icon" value="'.esc_html(stripslashes($item['icon'])).'">';

        $columnHTML .= '<div class="usergroup-color" style="background-color: '.$item['color'].';"></div>';
        $columnHTML .= '<a class="usergroup-name" href="'.admin_url('users.php?forum-user-group='.$item['term_id']).'" style="color: '.$item['color'].';">';

        if ($item['icon']) {
            $columnHTML .= '<i class="'.$item['icon'].'"></i>';
        }

        $columnHTML .= stripslashes($item['name']);
        $columnHTML .= '&nbsp;';
        $columnHTML .= '<span class="element-id">';
        $columnHTML .= '('.__('ID', 'asgaros-forum').': '.$item['term_id'].')';
        $columnHTML .= '</span>';
        $columnHTML .= '</a>';
        $columnHTML .= '<br>';
        $columnHTML .= '<span class="forum-description">';
        $columnHTML .= sprintf(_n('%s User', '%s Users', $item['users'], 'asgaros-forum'), $users_i18n);
        $columnHTML .= '</span>';

        return $columnHTML;
    }

    public function column_visibility($item) {
        if ($item['visibility'] == 'hidden') {
            return __('Hidden', 'asgaros-forum');
        } else {
            return __('Visible', 'asgaros-forum');
        }
    }

    public function column_auto_add($item) {
        if ($item['auto_add'] == 'yes') {
            return __('Yes', 'asgaros-forum');
        } else {
            return __('No', 'asgaros-forum');
        }
    }

    public function column_actions($item) {
        $columnHTML = '';
        $columnHTML .= '<a href="#" class="usergroup-delete-link link-delete" data-value-id="'.$item['term_id'].'" data-value-editor-title="'.__('Delete Usergroup', 'asgaros-forum').'">'.__('Delete', 'asgaros-forum').'</a>';
        $columnHTML .= ' &middot; ';
        $columnHTML .= '<a href="#" class="usergroup-editor-link" data-value-id="'.$item['term_id'].'" data-value-category="'.$item['parent'].'" data-value-editor-title="'.__('Edit Usergroup', 'asgaros-forum').'">'.__('Edit', 'asgaros-forum').'</a>';

        return $columnHTML;
    }

    public function get_columns() {
        $columns = array(
            'name'          => __('Name:', 'asgaros-forum'),
            'visibility'    => __('Visibility:', 'asgaros-forum'),
            'auto_add'      => __('Automatically Add:', 'asgaros-forum'),
            'actions'       => __('Actions:', 'asgaros-forum')
        );

        return $columns;
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $data = array();

        foreach ($this->table_data as $usergroup) {
            $usergroup = (array)$usergroup; // Convert object to array.
            $usergroup['color'] = AsgarosForumUserGroups::getUserGroupColor($usergroup['term_id']);
            $usergroup['visibility'] = AsgarosForumUserGroups::get_usergroup_visibility($usergroup['term_id']);
            $usergroup['auto_add'] = AsgarosForumUserGroups::get_usergroup_auto_add($usergroup['term_id']);
            $usergroup['users'] = AsgarosForumUserGroups::countUsersOfUserGroup($usergroup['term_id']);
            $usergroup['icon'] = AsgarosForumUserGroups::get_usergroup_icon($usergroup['term_id']);
            $data[] = $usergroup;
        }

        $this->items = $data;
    }
}
