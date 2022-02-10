<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')){
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

class Asgaros_Forum_Admin_Structure_Table extends WP_List_Table {
    public $table_data = array();

    public function __construct($table_data) {
        $this->table_data = $table_data;

        parent::__construct(
            array(
                'singular'  => 'forum',
                'plural'    => 'forums',
                'ajax'      => false
            )
        );
    }

    public function column_default($item, $column_name) {
        return $item[$column_name];
    }

    public function column_name($item) {
        $forumIcon = trim(esc_html(stripslashes($item['icon'])));
        $forumIcon = (empty($forumIcon)) ? 'fas fa-comments' : $forumIcon;

        $columnHTML = '';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_name" value="'.esc_html(stripslashes($item['name'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_description" value="'.esc_html(stripslashes($item['description'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_icon" value="'.$forumIcon.'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_status" value="'.esc_html(stripslashes($item['forum_status'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_order" value="'.esc_html(stripslashes($item['sort'])).'">';
        $columnHTML .= '<input type="hidden" id="forum_'.$item['id'].'_count_subforums" value="'.esc_html(stripslashes($item['count_subforums'])).'">';

        if ($item['parent_forum']) {
            $columnHTML .= '<div class="subforum">';
        } else {
            $columnHTML .= '<div class="parentforum">';
        }

        $columnHTML .= '<span class="make-bold">';
        $forum_icon = trim(esc_html(stripslashes($item['icon'])));
        $forum_icon = (empty($forum_icon)) ? 'fas fa-comments' : $forum_icon;
        $columnHTML .= '<span class="forum-icon '.$forum_icon.'"></span>';

        $columnHTML .= esc_html(stripslashes($item['name'])).' <span class="element-id">('.__('ID', 'asgaros-forum').': '.$item['id'].')</span></span>';
        $columnHTML .= '<br>';
        $columnHTML .= '<span class="forum-description">';

        if (empty($item['description'])) {
            $columnHTML .= '<span class="element-id">'.__('No description yet ...', 'asgaros-forum').'</span>';
        } else {
            $columnHTML .= stripslashes($item['description']);
        }

        $columnHTML .= '</span>';
        $columnHTML .= '<div class="clear"></div>';
        $columnHTML .= '</div>';

        return $columnHTML;
    }

    public function column_status($item) {
        switch ($item['forum_status']) {
            case 'normal':
                return __('Normal', 'asgaros-forum');
            break;
            case 'closed':
                return __('Closed', 'asgaros-forum');
            break;
            case 'approval':
                return __('Approval', 'asgaros-forum');
            break;
        }
    }

    public function column_actions($item) {
        $actionHTML = '';
        $actionHTML .= '<a href="#" class="forum-delete-link link-delete" data-value-id="'.$item['id'].'" data-value-category="'.$item['parent_id'].'" data-value-editor-title="'.__('Delete Forum', 'asgaros-forum').'">';
        $actionHTML .= __('Delete Forum', 'asgaros-forum');
        $actionHTML .= '</a>';
        $actionHTML .= ' &middot; ';
        $actionHTML .= '<a href="#" class="forum-editor-link" data-value-id="'.$item['id'].'" data-value-category="'.$item['parent_id'].'" data-value-parent-forum="'.$item['parent_forum'].'" data-value-editor-title="'.__('Edit Forum', 'asgaros-forum').'">';
        $actionHTML .= __('Edit Forum', 'asgaros-forum');
        $actionHTML .= '</a>';

        if (!$item['parent_forum']) {
            $actionHTML .= ' &middot; ';
            $actionHTML .= '<a href="#" class="forum-editor-link" data-value-id="new" data-value-category="'.$item['parent_id'].'" data-value-parent-forum="'.$item['id'].'" data-value-editor-title="'.__('Add Sub-Forum', 'asgaros-forum').'">';
            $actionHTML .= __('Add Sub-Forum', 'asgaros-forum');
            $actionHTML .= '</a>';
        }

        return $actionHTML;
    }

    public function get_columns() {
        $columns = array(
            'name'      => __('Name:', 'asgaros-forum'),
            'status'    => __('Status:', 'asgaros-forum'),
            'sort'      => __('Order:', 'asgaros-forum'),
            'actions'   => __('Actions:', 'asgaros-forum')
        );

        return $columns;
    }

    public function prepare_items() {
        global $asgarosforum;

        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $data = array();

        foreach ($this->table_data as $forum) {
            $data[] = $forum;

            if ($forum['count_subforums'] > 0) {
                $subforums = $asgarosforum->get_forums($forum['parent_id'], $forum['id'], ARRAY_A);

                if (!empty($subforums)) {
                    foreach ($subforums as $subforum) {
                        $data[] = $subforum;
                    }
                }
            }
        }

        $this->items = $data;
    }
}
