<?php
class CME_PublishPressRoles {
    public static function scripts() {
    ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(function ($) {
                $('#the-list').children('tr').each(function(index, e) {
                    if (!$(e).find('td.display_name div.row-actions span.edit-capabilities').count) {
                        var link = '';
                        var is_administrator_role = ('role-administrator' == $(e).attr('id'));
                        var role_name = $(e).attr('id');
                        role_name = role_name.replace('role-', '');

                        if (is_administrator_role) {
                            link += ' | ';
                        }

                        link += '<a href="<?php echo admin_url('admin.php?page=pp-capabilities');?>' + '&role=' + role_name + '">' + 'Capabilities' + '</a> ';

                        if (!is_administrator_role) {
                            link += '| ';
                        }

                        $(e).find('td.display_name div.row-actions span.edit-role').after(' <span class="edit edit-capabilities">' + link + '</span>');
                    }
                });
            });
            /* ]]> */
        </script>
        <style type="text/css">
            
        </style>
    <?php
    }
}
