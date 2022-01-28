<?php

namespace ProfilePress\Core\ContentProtection;

use ProfilePress\Core\Base;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;

class WPListTable extends \WP_List_Table
{
    public function __construct()
    {
        parent::__construct(array(
            'singular' => esc_html__('Protection Rule', 'wp-user-avatar'),
            'plural'   => esc_html__('Protection Rules', 'wp-user-avatar'),
            'ajax'     => false
        ));
    }

    public function no_items()
    {
        _e('No protection rule found.', 'wp-user-avatar');
    }

    public function get_columns()
    {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'title'   => esc_html__('Title', 'wp-user-avatar'),
            'content' => esc_html__('Protected Contents', 'wp-user-avatar'),
            'access'  => esc_html__('Access', 'wp-user-avatar'),
        ];

        return $columns;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'date' :
                $value = mysql2date('F j, Y', $item['date']);
                break;
            default:
                $value = $item[$column_name];
                break;
        }

        return apply_filters('ppress_forms_table_column', $value, $item, $column_name);
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="rule_id[]" value="%s" />', $item['id']);
    }

    public static function delete_rule_url($rule_id)
    {
        $nonce_delete = wp_create_nonce('pp_content_protection_delete_rule');

        return add_query_arg(['action' => 'delete', 'id' => $rule_id, '_wpnonce' => $nonce_delete], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
    }

    public function column_title($item)
    {
        $rule_id = absint($item['id']);

        $item = ppress_var($item, 'meta_value', []);

        $is_active = in_array(ppress_var($item, 'is_active', true), ['true', true], true);

        $edit_link       = add_query_arg(['action' => 'edit', 'id' => $rule_id], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
        $duplicate_link  = add_query_arg(['action' => 'duplicate', 'id' => $rule_id, '_wpnonce' => wp_create_nonce('pp_content_protection_duplicate_rule')], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
        $activate_link   = add_query_arg(['action' => 'activate', 'id' => $rule_id, '_wpnonce' => wp_create_nonce('pp_content_protection_activate_rule')], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
        $deactivate_link = add_query_arg(['action' => 'deactivate', 'id' => $rule_id, '_wpnonce' => wp_create_nonce('pp_content_protection_deactivate_rule')], PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
        $delete_link     = self::delete_rule_url($rule_id);

        $actions = [
            'edit'      => sprintf('<a href="%s">%s</a>', $edit_link, esc_html__('Edit', 'wp-user-avatar')),
            'duplicate' => sprintf('<a href="%s">%s</a>', $duplicate_link, esc_html__('Duplicate', 'wp-user-avatar'))
        ];

        if (true === $is_active) {
            $actions['deactivate'] = sprintf('<a href="%s">%s</a>', $deactivate_link, esc_html__('Deactivate', 'wp-user-avatar'));
        }

        if (false === $is_active) {
            $actions['activate'] = sprintf('<a href="%s">%s</a>', $activate_link, esc_html__('Activate', 'wp-user-avatar'));
        }

        $actions['delete'] = sprintf('<a class="pp-confirm-delete" href="%s">%s</a>', $delete_link, esc_html__('Delete', 'wp-user-avatar'));

        $a = '<a href="' . $edit_link . '">' . ppress_var($item, 'title') . '</a>';

        return '<strong>' . $a . '</strong>' . $this->row_actions($actions);
    }

    public function get_condition_title($condition_id)
    {
        $condition = ContentConditions::get_instance()->get_condition($condition_id);

        return ppress_var($condition, 'title');
    }

    public function rule_title_transform($condition_id, $title)
    {
        $condition = ContentConditions::get_instance()->get_condition($condition_id);

        if (isset($condition['overview_title'])) {
            $title = $condition['overview_title'];
        }

        return $title;
    }

    public function rule_value_transform($condition_id, $value)
    {
        $condition = ContentConditions::get_instance()->get_condition($condition_id);

        $field_type = ppress_var(ppress_var($condition, 'field'), 'type');

        if ($field_type == 'postselect') {

            if (is_array($value)) {

                $value = array_map(function ($val) {
                    return sprintf('<a href="%s" target="_blank">%s</a>', get_permalink($val), $val);
                }, $value);
            }
        }

        if ($field_type == 'taxonomyselect') {

            if (is_array($value)) {

                $value = array_map(function ($val) {
                    return get_term($val)->name;
                }, $value);
            }
        }

        if ($field_type == 'select') {

            if (is_array($value)) {

                $select_options = ppress_var(ppress_var($condition, 'field'), 'options');

                $value = array_map(function ($val) use ($select_options) {
                    return ppress_var($select_options, $val, '');
                }, $value);
            }
        }

        return is_array($value) ? implode(', ', $value) : $value;
    }

    public function column_content($item)
    {
        $html = '';
        if (isset($item['meta_value']['content']) && is_array($item['meta_value']['content'])) {
            foreach ($item['meta_value']['content'] as $rule_group) {
                if (is_array($rule_group)) {
                    foreach ($rule_group as $rule) {
                        if (isset($rule['condition'])) {
                            $condition_id = $rule['condition'];
                            $html         .= sprintf(
                                '<p><strong>%s</strong>%s',
                                $this->rule_title_transform($condition_id, $this->get_condition_title($condition_id)),
                                isset($rule['value']) ? ': ' . $this->rule_value_transform($condition_id, $rule['value']) : ''
                            );
                        }
                    }
                }
            }
        }

        return $html;
    }

    public function column_access($item)
    {
        $html           = '';
        $who_can_access = false;
        $access_roles   = false;
        if (isset($item['meta_value']['access_condition']) && is_array($item['meta_value']['access_condition'])) {

            if (isset($item['meta_value']['access_condition']['who_can_access'])) {
                switch ($item['meta_value']['access_condition']['who_can_access']) {
                    case 'everyone':
                        $who_can_access = esc_html__('Everyone', 'wp-user-avatar');
                        break;
                    case 'login':
                        $who_can_access = esc_html__('Logged in users', 'wp-user-avatar');
                        break;
                    case 'logout':
                        $who_can_access = esc_html__('Logged out users', 'wp-user-avatar');
                        break;
                }
            }

            if (
                isset($item['meta_value']['access_condition']['who_can_access']) &&
                $item['meta_value']['access_condition']['who_can_access'] == 'login' &&
                isset($item['meta_value']['access_condition']['access_roles']) &&
                is_array($item['meta_value']['access_condition']['access_roles'])
            ) {
                $access_roles = implode(', ', array_map(function ($role) {
                    return ppress_var(ppress_wp_roles_key_value(false), $role);
                }, $item['meta_value']['access_condition']['access_roles']));
            }
        }

        if ( ! empty($who_can_access)) {
            $html = sprintf(
                '<p><strong>%s</strong>%s', $who_can_access,
                ! empty($access_roles) ? ': ' . $access_roles : ''
            );
        }

        return $html;
    }

    public function get_rules($per_page, $current_page = 1)
    {
        global $wpdb;

        $replacement = [SettingsPage::META_DATA_KEY, $per_page];

        $table = Base::meta_data_db_table();

        $sql = "SELECT * FROM $table WHERE meta_key = %s";
        $sql .= " ORDER BY id DESC";
        $sql .= " LIMIT %d";
        if ($current_page > 1) {
            $sql           .= "  OFFSET %d";
            $replacement[] = ($current_page - 1) * $per_page;
        }

        $result = $wpdb->get_results($wpdb->prepare($sql, $replacement), 'ARRAY_A');

        if (empty($result)) return false;

        $output = [];
        foreach ($result as $key => $meta) {
            $output[$key] = array_reduce(array_keys($meta), function ($carry, $item) use ($meta) {
                $carry[$item] = $item == 'meta_value' ? unserialize($meta[$item]) : $meta[$item];

                return $carry;
            });
        }

        return $output;
    }


    public function record_count()
    {
        global $wpdb;

        $sql = sprintf("SELECT COUNT(*) FROM %s WHERE meta_key = '%s'", Base::meta_data_db_table(), SettingsPage::META_DATA_KEY);

        return $wpdb->get_var($sql);
    }

    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('rules_per_page', 10);;
        $current_page = $this->get_pagenum();
        $total_items  = $this->record_count();

        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ));

        $this->items = $this->get_rules($per_page, $current_page);
    }

    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => esc_html__('Delete', 'wp-user-avatar')
        ];

        return $actions;
    }

    public function single_row($item)
    {
        $old_item = $item;

        $item = ppress_var($item, 'meta_value', []);

        $is_active = in_array(ppress_var($item, 'is_active', true), ['true', true], true);

        printf('<tr%s>', $is_active === false ? ' style="background: rgba(247, 8, 8, 0.1)"' : '');

        $item = $old_item;
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function process_bulk_action()
    {
        $rule_id = absint(ppress_var($_GET, 'id', 0));

        if ('deactivate' === $this->current_action()) {

            check_admin_referer('pp_content_protection_deactivate_rule');

            if ( ! current_user_can('manage_options')) return;

            $meta = PROFILEPRESS_sql::get_meta_value($rule_id, SettingsPage::META_DATA_KEY);

            $meta['is_active'] = 'false';

            PROFILEPRESS_sql::update_meta_value($rule_id, SettingsPage::META_DATA_KEY, $meta);

            wp_safe_redirect(PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
            exit;
        }

        if ('activate' === $this->current_action()) {

            check_admin_referer('pp_content_protection_activate_rule');

            if ( ! current_user_can('manage_options')) return;

            $meta = PROFILEPRESS_sql::get_meta_value($rule_id, SettingsPage::META_DATA_KEY);

            $meta['is_active'] = 'true';

            PROFILEPRESS_sql::update_meta_value($rule_id, SettingsPage::META_DATA_KEY, $meta);

            wp_safe_redirect(PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
            exit;
        }

        if ('delete' === $this->current_action()) {

            check_admin_referer('pp_content_protection_delete_rule');

            if ( ! current_user_can('manage_options')) return;

            if (PROFILEPRESS_sql::delete_meta_data($rule_id)) {

                do_action('ppress_content_protection_delete_rule', $rule_id);

                wp_safe_redirect(PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
                exit;
            }
        }

        if ('duplicate' === $this->current_action()) {

            check_admin_referer('pp_content_protection_duplicate_rule');

            if ( ! current_user_can('manage_options')) return;

            $meta = PROFILEPRESS_sql::get_meta_value($rule_id, SettingsPage::META_DATA_KEY);

            PROFILEPRESS_sql::add_meta_data(SettingsPage::META_DATA_KEY, $meta);

            do_action('ppress_content_protection_duplicate_rule', $rule_id);

            wp_safe_redirect(PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
            exit;
        }

        if ('bulk-delete' === $this->current_action()) {
            check_admin_referer('bulk-pp_cp_rules');

            $delete_ids = array_map('absint', $_POST['rule_id']);

            foreach ($delete_ids as $rule_id) {
                PROFILEPRESS_sql::delete_meta_data($rule_id);
            }

            do_action('ppress_content_protection_after_bulk_delete', $delete_ids);

            wp_safe_redirect(PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE);
            exit;
        }
    }

    /**
     * @return array List of CSS classes for the table tag.
     */
    public function get_table_classes()
    {
        return array('widefat', 'fixed', 'striped', 'content_protection', 'ppview');
    }
}
