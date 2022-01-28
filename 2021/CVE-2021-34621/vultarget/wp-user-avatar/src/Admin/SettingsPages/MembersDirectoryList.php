<?php

namespace ProfilePress\Core\Admin\SettingsPages;

class MembersDirectoryList extends FormList
{
    public function no_items()
    {
        printf(
            esc_html__('No members directory is currently available. %sConsider creating one%s', 'wp-user-avatar'),
            '<a href="' . add_query_arg('view', 'add-new', PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE) . '">',
            '</a>'
        );
    }
}