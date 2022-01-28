<?php

namespace ProfilePress\Core\Admin\SettingsPages\EmailSettings;

class WPListTable extends \WP_List_Table
{
    public $items;

    public function __construct($data)
    {
        $this->items = $data;

        parent::__construct(array(
            'singular' => 'pp-email-notification',
            'plural'   => 'pp-email-notifications',
            'ajax'     => false
        ));

    }

    public function no_items()
    {
        _e('No email available.', 'wp-user-avatar');
    }

    public function get_columns()
    {
        $columns = [
            'title'     => esc_html__('Email', 'wp-user-avatar'),
            'recipient' => esc_html__('Recipient', 'wp-user-avatar'),
            'configure' => ''
        ];

        return $columns;
    }

    public function display_tablenav($which)
    {
        return '';
    }

    public function column_default($item, $column_name)
    {
        $url = esc_url_raw(add_query_arg('type', sanitize_text_field($item['key'])));

        if ($column_name == 'configure') {
            return '<a class="button pp-email-configure" href="' . $url . '"><span class="dashicons dashicons-admin-generic"></span></a>';
        }

        return isset($item[$column_name]) ? $item[$column_name] : '';
    }

    public function column_title($item)
    {
        $key   = sanitize_text_field($item['key']);
        $class = 'dashicons pp-email-notification-status dashicons-no-alt';

        if (ppress_get_setting($key . '_email_enabled', 'on') == 'on') {
            $class = 'dashicons pp-email-notification-status dashicons-yes';
            $class .= ' pp-is-active ';
        }

        $url  = esc_url_raw(add_query_arg('type', $key));
        $flag = '<span class="' . $class . '"></span>';

        $hint = '';
        if ( ! empty($item['description'])) {
            $hint = sprintf(
                ' <span class="ppress-hint-tooltip hint--top hint--medium hint--bounce" aria-label="%s"><span class="dashicons dashicons-editor-help"></span></span>',
                $item['description']
            );
        }

        return sprintf('%s<strong><a href="%s">%s</a>%s</strong>', $flag, $url, $item['title'], $hint);
    }

    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();
    }
}
