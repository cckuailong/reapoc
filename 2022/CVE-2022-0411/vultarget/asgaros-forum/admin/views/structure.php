<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap" id="af-structure">
    <?php
    $title = __('Structure', 'asgaros-forum');
    $titleUpdated = __('Structure updated.', 'asgaros-forum');
    $this->render_admin_header($title, $titleUpdated);

    $categories = $this->asgarosforum->content->get_categories(false);
    ?>

    <div id="hidden-data" style="display: none;">
        <select id="data-forum-parent-one-level">
            <?php
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<option value="'.esc_attr($category->term_id).'_0">'.esc_html($category->name).'</option>';
                }
            }
            ?>
        </select>

        <select id="data-forum-parent-two-level">
            <?php
            if ($categories) {
                foreach ($categories as $category) {
                    echo '<option value="'.esc_attr($category->term_id).'_0">'.esc_html($category->name).'</option>';

                    $forums = $this->asgarosforum->get_forums($category->term_id, 0);

                    if ($forums) {
                        foreach ($forums as $forum) {
                            echo '<option value="'.esc_attr($category->term_id).'_'.esc_attr($forum->id).'">&mdash; '.esc_html($forum->name).'</option>';
                        }
                    }
                }
            }
            ?>
        </select>
    </div>

    <div id="editor-container" class="settings-box" style="display: none;">
        <div class="settings-header"></div>
        <div class="editor-instance" id="category-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_save_category'); ?>
                <input type="hidden" name="category_id" value="new">

                <table class="form-table">
                    <tr>
                        <th><label for="category_name"><?php esc_html_e('Name:', 'asgaros-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="200" name="category_name" id="category_name" value="" required></td>
                    </tr>
                    <tr>
                        <th><label for="category_access"><?php esc_html_e('Access:', 'asgaros-forum'); ?></label></th>
                        <td>
                            <select name="category_access" id="category_access">
                                <option value="everyone"><?php esc_html_e('Everyone', 'asgaros-forum'); ?></option>
                                <option value="loggedin"><?php esc_html_e('Logged in users only', 'asgaros-forum'); ?></option>
                                <option value="moderator"><?php esc_html_e('Moderators only', 'asgaros-forum'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="category_order"><?php esc_html_e('Order:', 'asgaros-forum'); ?></label></th>
                        <td><input type="number" size="4" id="category_order" name="category_order" value="" min="1"></td>
                    </tr>
                    <?php AsgarosForumUserGroups::renderCategoryEditorFields(); ?>
                </table>

                <p class="submit">
                    <input type="submit" name="af-create-edit-category-submit" value="<?php esc_attr_e('Save', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance" id="forum-editor" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_save_forum'); ?>
                <input type="hidden" name="forum_id" value="new">
                <input type="hidden" name="forum_category" value="0">
                <input type="hidden" name="forum_parent_forum" value="0">

                <table class="form-table">
                    <tr>
                        <th><label for="forum_name"><?php esc_html_e('Name:', 'asgaros-forum'); ?></label></th>
                        <td><input class="element-name" type="text" size="100" maxlength="255" name="forum_name" id="forum_name" value="" required></td>
                    </tr>
                    <tr>
                        <th><label for="forum_description"><?php esc_html_e('Description:', 'asgaros-forum'); ?></label></th>
                        <td><input type="text" size="100" maxlength="255" id="forum_description" name="forum_description" value=""></td>
                    </tr>
                    <tr>
                        <th><label for="forum_parent"><?php esc_html_e('Parent:', 'asgaros-forum'); ?></label></th>
                        <td>
                            <select name="forum_parent" id="forum_parent"></select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_icon"><?php esc_html_e('Icon:', 'asgaros-forum'); ?></label></th>
                        <td>
                            <input type="text" size="50" id="forum_icon" name="forum_icon" value="">
                            <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">
                                <?php esc_html_e('List of available icons.', 'asgaros-forum'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_status"><?php esc_html_e('Status:', 'asgaros-forum'); ?></label></th>
                        <td>
                            <select name="forum_status" id="forum_status">
                                <?php

                                // Available options for forum-status.
                                $forum_status_options = array(
                                    array(
                                        'name'  => __('Normal', 'asgaros-forum'),
                                        'value' => 'normal'
                                    ),
                                    array(
                                        'name'  => __('Closed', 'asgaros-forum'),
                                        'value' => 'closed'
                                    ),
                                    array(
                                        'name'  => __('Approval', 'asgaros-forum'),
                                        'value' => 'approval'
                                    )
                                );

                                $forum_status_options = apply_filters('asgarosforum_filter_forum_status_options', $forum_status_options);

                                foreach ($forum_status_options as $forum_status_option) {
                                    echo '<option value="'.esc_attr($forum_status_option['value']).'">'.esc_html($forum_status_option['name']).'</option>';
                                }

                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="forum_order"><?php esc_html_e('Order:', 'asgaros-forum'); ?></label></th>
                        <td><input type="number" size="4" id="forum_order" name="forum_order" value="" min="1"></td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="af-create-edit-forum-submit" value="<?php esc_attr_e('Save', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="category-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_delete_category'); ?>
                <input type="hidden" name="category-id" value="0">
                <p><?php esc_html_e('Deleting this category will also permanently delete all forums, sub-forums, topics and posts inside it. Are you sure you want to delete this category?', 'asgaros-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="asgaros-forum-delete-category" value="<?php esc_attr_e('Delete', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>

        <div class="editor-instance delete-layer" id="forum-delete" style="display: none;">
            <form method="post">
                <?php wp_nonce_field('asgaros_forum_delete_forum'); ?>
                <input type="hidden" name="forum-id" value="0">
                <input type="hidden" name="forum-category" value="0">
                <p><?php esc_html_e('Deleting this forum will also permanently delete all sub-forums, topics and posts inside it. Are you sure you want to delete this forum?', 'asgaros-forum'); ?></p>

                <p class="submit">
                    <input type="submit" name="asgaros-forum-delete-forum" value="<?php esc_attr_e('Delete', 'asgaros-forum'); ?>" class="button button-primary">
                    <a class="button-cancel button button-secondary"><?php esc_html_e('Cancel', 'asgaros-forum'); ?></a>
                </p>
            </form>
        </div>
    </div>

    <a href="#" class="category-editor-link add-element" data-value-id="new" data-value-editor-title="<?php esc_attr_e('Add Category', 'asgaros-forum'); ?>">
        <?php
        echo '<span class="fas fa-plus"></span>';
        esc_html_e('Add Category', 'asgaros-forum');
        ?>
    </a>

    <?php
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $term_meta = get_term_meta($category->term_id);
            $access = (!empty($term_meta['category_access'][0])) ? $term_meta['category_access'][0] : 'everyone';
            $order = (!empty($term_meta['order'][0])) ? $term_meta['order'][0] : 1;
            echo '<input type="hidden" id="category_'.esc_attr($category->term_id).'_name" value="'.esc_html(stripslashes($category->name)).'">';
            echo '<input type="hidden" id="category_'.esc_attr($category->term_id).'_access" value="'.esc_attr($access).'">';
            echo '<input type="hidden" id="category_'.esc_attr($category->term_id).'_order" value="'.esc_attr($order).'">';
            AsgarosForumUserGroups::renderHiddenFields($category->term_id);

            $forums = $this->asgarosforum->get_forums($category->term_id, 0, ARRAY_A);
            ?>
            <div class="settings-box">
                <div class="settings-header">
                    <span class="fas fa-box"></span>
                    <?php echo esc_html($category->name); ?>&nbsp;
                    <span class="element-id">
                        <?php
                        echo '(';
                        echo esc_html__('ID', 'asgaros-forum').': '.esc_html($category->term_id);
                        echo ' &middot; ';
                        echo esc_html__('Access:', 'asgaros-forum').' ';
                        if ($access === 'everyone') {
                            esc_html_e('Everyone', 'asgaros-forum');
                        } else if ($access === 'loggedin') {
                            esc_html_e('Logged in users only', 'asgaros-forum');
                        } else if ($access === 'moderator') {
                            esc_html_e('Moderators only', 'asgaros-forum');
                        }
                        echo ' &middot; ';
                        echo esc_html__('Order:', 'asgaros-forum').' '.esc_html($order);
                        AsgarosForumUserGroups::renderUserGroupsInCategory($category->term_id);
                        do_action('asgarosforum_admin_show_custom_category_data', $category->term_id);
                        echo ')';
                        ?>
                    </span>
                    <span class="category-actions">
                        <a href="#" class="category-delete-link action-delete" data-value-id="<?php echo esc_attr($category->term_id); ?>" data-value-editor-title="<?php esc_attr_e('Delete Category', 'asgaros-forum'); ?>"><?php esc_html_e('Delete Category', 'asgaros-forum'); ?></a>
                        &middot;
                        <a href="#" class="category-editor-link action-edit" data-value-id="<?php echo esc_attr($category->term_id); ?>" data-value-editor-title="<?php esc_attr_e('Edit Category', 'asgaros-forum'); ?>"><?php esc_html_e('Edit Category', 'asgaros-forum'); ?></a>
                    </span>
                </div>
                <?php
                if (!empty($forums)) {
                    $structureTable = new Asgaros_Forum_Admin_Structure_Table($forums);
                    $structureTable->prepare_items();
                    $structureTable->display();
                }
                ?>
                <a href="#" class="forum-editor-link add-element" data-value-id="new" data-value-category="<?php echo esc_attr($category->term_id); ?>" data-value-parent-forum="0" data-value-editor-title="<?php esc_attr_e('Add Forum', 'asgaros-forum'); ?>">
                    <?php
                    echo '<span class="fas fa-plus"></span>';
                    esc_html_e('Add Forum', 'asgaros-forum');
                    ?>
                </a>
            </div>
            <?php
        }

        echo '<a href="#" class="category-editor-link add-element" data-value-id="new" data-value-editor-title="'.esc_html__('Add Category', 'asgaros-forum').'">';
            echo '<span class="fas fa-plus"></span>';
            esc_html_e('Add Category', 'asgaros-forum');
        echo '</a>';
    }
    ?>
</div>
