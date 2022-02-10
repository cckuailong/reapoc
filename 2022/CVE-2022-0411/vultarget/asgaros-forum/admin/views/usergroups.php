<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap" id="af-structure">
    <?php
    $title = __('Usergroups', 'asgaros-forum');
    $titleUpdated = __('Usergroups updated.', 'asgaros-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <div id="editor-container" class="settings-box" style="display: none;">
        <div class="settings-header"></div>
        <div class="editor-instance" id="usergroup-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_save_usergroup'); ?>
                <input type="hidden" name="usergroup_id" value="new">
                <input type="hidden" name="usergroup_category" value="0">

                <table class="form-table">
                    <tr>
                        <th><label for="usergroup_name"><?php esc_html_e('Name:', 'asgaros-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="usergroup_name" id="usergroup_name" value="" required></td>
                    </tr>
                    <tr id="usergroup-color-settings">
                        <th><label for="usergroup_color"><?php esc_html_e('Color:', 'asgaros-forum'); ?></label></th>
                        <td><input type="text" value="#444444" class="color-picker" name="usergroup_color" id="usergroup_color" data-default-color="#444444"></td>
                    </tr>
                    <tr>
                        <th>
                            <label for="usergroup_icon"><?php esc_html_e('Icon:', 'asgaros-forum'); ?></label>
                            <span class="description"><?php esc_html_e('Set an optional icon for the usergroup.', 'asgaros-forum'); ?></span>
                        </th>
                        <td>
                            <input type="text" id="usergroup_icon" name="usergroup_icon" value="" placeholder="fas fa-users">
                            <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">
                                <?php esc_html_e('List of available icons.', 'asgaros-forum'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="usergroup_visibility"><?php esc_html_e('Hide usergroup:', 'asgaros-forum'); ?></label></th>
                        <td><input type="checkbox" id="usergroup_visibility" name="usergroup_visibility"></td>
                    </tr>
                    <tr>
                        <th><label for="usergroup_auto_add"><?php esc_html_e('Add new users automatically:', 'asgaros-forum'); ?></label></th>
                        <td><input type="checkbox" id="usergroup_auto_add" name="usergroup_auto_add"></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="af-create-edit-usergroup-submit" value="<?php esc_attr_e('Save', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="usergroup-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_delete_usergroup'); ?>
                <input type="hidden" name="usergroup-id" value="0">
                <p><?php esc_html_e('Are you sure you want to delete this usergroup?', 'asgaros-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="asgaros-forum-delete-usergroup" value="<?php esc_attr_e('Delete', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance" id="usergroup-category-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_save_usergroup_category'); ?>
                <input type="hidden" name="usergroup_category_id" value="new">

                <table class="form-table">
                    <tr>
                        <th><label for="usergroup_category_name"><?php esc_html_e('Name:', 'asgaros-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="usergroup_category_name" id="usergroup_category_name" value="" required></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="af-create-edit-usergroup-category-submit" value="<?php esc_attr_e('Save', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="usergroup-category-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_delete_usergroup_category'); ?>
                <input type="hidden" name="usergroup-category-id" value="0">
                <p><?php esc_html_e('Deleting this category will also permanently delete all usergroups inside it. Are you sure you want to delete this category?', 'asgaros-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="asgaros-forum-delete-usergroup-category" value="<?php esc_attr_e('Delete', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>
    </div>

    <a href="#" class="usergroup-category-editor-link add-element" data-value-id="new" data-value-editor-title="<?php esc_attr_e('Add Category', 'asgaros-forum'); ?>">
        <?php
        echo '<span class="fas fa-plus"></span>';
        esc_html_e('Add Category', 'asgaros-forum');
        ?>
    </a>

    <?php
    $userGroupsCategories = AsgarosForumUserGroups::getUserGroupCategories();

    if (!empty($userGroupsCategories)) {
        foreach ($userGroupsCategories as $category) {
            echo '<input type="hidden" id="usergroup_category_'.esc_attr($category->term_id).'_name" value="'.esc_html(stripslashes($category->name)).'">';

            $usergroups = AsgarosForumUserGroups::getUserGroupsOfCategory($category->term_id);
            ?>
            <div class="settings-box">
                <div class="settings-header">
                    <span class="fas fa-users"></span>
                    <?php echo esc_html($category->name); ?>
                    <span class="category-actions">
                        <a href="#" class="usergroup-category-delete-link action-delete" data-value-id="<?php echo esc_attr($category->term_id); ?>" data-value-editor-title="<?php esc_attr_e('Delete Category', 'asgaros-forum'); ?>"><?php esc_html_e('Delete Category', 'asgaros-forum'); ?></a>
                        &middot;
                        <a href="#" class="usergroup-category-editor-link action-edit" data-value-id="<?php echo esc_attr($category->term_id); ?>" data-value-editor-title="<?php esc_attr_e('Edit Category', 'asgaros-forum'); ?>"><?php esc_html_e('Edit Category', 'asgaros-forum'); ?></a>
                    </span>
                </div>
                <?php
                if (!empty($usergroups)) {
                    $userGroupsTable = new Asgaros_Forum_Admin_UserGroups_Table($usergroups);
                    $userGroupsTable->prepare_items();
                    $userGroupsTable->display();
                }
                ?>

                <a href="#" class="usergroup-editor-link add-element" data-value-id="new" data-value-category="<?php echo esc_attr($category->term_id); ?>" data-value-editor-title="<?php esc_attr_e('Add Usergroup', 'asgaros-forum'); ?>">
                    <span class="fas fa-plus"></span>
                    <?php esc_html_e('Add Usergroup', 'asgaros-forum'); ?>
                </a>
            </div>
            <?php
        }

        echo '<a href="#" class="usergroup-category-editor-link add-element" data-value-id="new" data-value-editor-title="'.esc_attr__('Add Category', 'asgaros-forum').'">';
            echo '<span class="fas fa-plus"></span>';
            esc_html_e('Add Category', 'asgaros-forum');
        echo '</a>';
    }
    ?>
</div>
