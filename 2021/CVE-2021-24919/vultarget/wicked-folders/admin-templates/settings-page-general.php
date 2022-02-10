<form action="" method="post">
    <input type="hidden" name="action" value="wicked_folders_save_settings" />
    <?php wp_nonce_field( 'wicked_folders_save_settings', 'nonce' ); ?>
    <h2><?php _e( 'General', 'wicked-folders' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Enable folders for:', 'wicked-folders' ); ?>
            </th>
            <td>
                <?php foreach ( $post_types as $post_type ) : ?>
                    <?php
                        if ( ! $is_pro_active && in_array( $post_type->name, $pro_post_types ) ) continue;
                        if ( ! $post_type->show_ui ) continue;
                    ?>
                    <label>
                        <input type="checkbox" name="post_type[]" value="<?php echo esc_attr( $post_type->name ); ?>"<?php if ( in_array( $post_type->name, $enabled_posts_types ) ) echo ' checked="checked"'; ?>/>
                        <?php echo esc_html( $post_type->label ); ?>
                    </label>
                    <br />
                <?php endforeach; ?>
                <?php if ( ! $is_pro_active && Wicked_Folders::is_upsell_enabled() ) : ?>
                    <?php foreach ( $post_types as $post_type ) : ?>
                        <?php
                            if ( ! in_array( $post_type->name, $pro_post_types ) ) continue;
                            if ( ! $post_type->show_ui ) continue;
                        ?>
                        <label>
                            <input type="checkbox" name="post_type[]" value="<?php echo esc_attr( $post_type->name ); ?>" disabled="disabled" />
                            <?php echo esc_html( $post_type->label ); ?>
                        </label>
                        <br />
                    <?php endforeach; ?>
                    <label>
                        <input type="checkbox" name="post_type[]" value="wf_plugin" disabled="disabled" />
                        <?php _e( 'Plugins', 'wicked-folders' ); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" name="post_type[]" value="wf_user" disabled="disabled" />
                        <?php _e( 'Users', 'wicked-folders' ); ?>
                    </label>
                    <br />
                    <?php if ( $is_gravity_forms_active ) : ?>
                        <label>
                            <input type="checkbox" name="post_type[]" value="wf_gf_entry" disabled="disabled" />
                            <?php _e( 'Gravity Forms Entries', 'wicked-folders' ); ?>
                        </label>
                        <br />
                        <label>
                            <input type="checkbox" name="post_type[]" value="wf_gf_form" disabled="disabled" />
                            <?php _e( 'Gravity Forms Forms', 'wicked-folders' ); ?>
                        </label>
                        <br />
                    <?php endif; ?>
                    <p><?php _e( '<a href="https://wickedplugins.com/plugins/wicked-folders/?utm_source=core_settings&utm_campaign=wicked_folders&utm_content=post_types" target="_blank">Upgrade to Wicked Folders Pro</a> to manage media, users, plugins, and more using folders.' ); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="show_item_counts" value="1"<?php if ( $show_item_counts ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Show number of items in each folder', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When checked (default), the number of items assigned to each folder is displayed next to the folder's name.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="show_unassigned_folder" value="1"<?php if ( $show_unassigned_folder ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Show unassigned items folder', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When checked (default), the 'Unassigned Items' folder will always be shown in the folder pane.  When left unchecked, the 'Unassigned Items' folder will appear as a child folder within 'Dynamic Folders'.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="show_folder_search" value="1"<?php if ( $show_folder_search ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Show folder search', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When checked (default), a search field is displayed above the folder tree that allows you to search folders by name.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="show_breadcrumbs" value="1"<?php if ( $show_breadcrumbs ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Show folder breadcrumbs', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( 'Displays a breadcrumb trail at the top of post lists.', 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="show_hierarchy_in_folder_column" value="1"<?php if ( $show_hierarchy_in_folder_column ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Show folder hierarchy in folder column', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When unchecked (default), folders will be displayed as a comma-separated list in the folder column that appears in post lists.  When checked, a breadcrumb path will be displayed showing the hierarchy of each folder the item is assigned to.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="include_children" value="1"<?php if ( $include_children ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Include items from child folders', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When unchecked (default) and a folder is selected, only items assigned to that folder will be displayed.  When checked, items in the selected folder *and* items in any of the folder's child folders will be displayed.  Please note: this setting does not apply to media.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="enable_ajax_nav" value="1"<?php if ( $enable_ajax_nav ) echo ' checked="checked"'; ?>/>
                    <?php _e( "Don't reload page when navigating folders", 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When checked (default), navigating between folders will not cause the page to reload.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
    </table>
    <h2><?php _e( 'Dynamic Folders', 'wicked-folders' ); ?></h2>
    <p><?php _e( 'Dynamic folders are generated on the fly based on your content.  They are useful for finding content based on things like date, author, etc.', 'wicked-folders' ); ?></p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Enable dynamic folders for:', 'wicked-folders' ); ?>
            </th>
            <td>
                <?php foreach ( $post_types as $post_type ) : ?>
                    <?php
                        if ( ! $is_pro_active && in_array( $post_type->name, $pro_post_types ) ) continue;
                        if ( in_array( $post_type->name, array( Wicked_Folders::get_plugin_post_type_name(), Wicked_Folders::get_gravity_forms_form_post_type_name(), Wicked_Folders::get_gravity_forms_entry_post_type_name(), 'tablepress_table' ) ) ) continue;
                        if ( ! $post_type->show_ui ) continue;
                    ?>
                    <label>
                        <input type="checkbox" name="dynamic_folder_post_type[]" value="<?php echo esc_attr( $post_type->name ); ?>"<?php if ( in_array( $post_type->name, $dynamic_folders_enabled_posts_types ) ) echo ' checked="checked"'; ?><?php //if ( ! in_array( $post_type->name, $enabled_posts_types ) ) echo ' disabled="disabled"'; ?>/>
                        <?php echo esc_html( $post_type->label ); ?>
                    </label>
                    <br />
                <?php endforeach; ?>
            </td>
        </tr>
        <?php /* ?>
        <th scope="row">
            <?php _e( 'Tree View', 'wicked-folders' ); ?>
        </th>
        <td>
            <label>
                <input type="checkbox" name="show_folder_contents_in_tree_view" value="1"<?php if ( $show_folder_contents_in_tree_view ) echo ' checked="checked"'; ?>/>
                <?php _e( 'Show folder contents in tree view', 'wicked-folders' ); ?>
            </label>
            <p class="description"><?php _e( "When checked, the tree view will display each folder's items in addition to its sub folders.", 'wicked-folders' ); ?></p>
        </td>
        <?php */ ?>
    </table>
    <?php if ( $is_pro_active ) : ?>
        <h2><?php _e( 'Media', 'wicked-folders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <?php //_e( 'Sync folder upload dropdown', 'wicked-folders' ); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="sync_upload_folder_dropdown" value="1"<?php if ( $sync_upload_folder_dropdown ) echo ' checked="checked"'; ?>/>
                        <?php _e( 'Sync folder upload dropdown', 'wicked-folders' ); ?>
                        <span class="dashicons dashicons-editor-help" title="<?php _e( 'When checked, the dropdown that lets you to choose which folder to assign new uploads to will change as you browse folders and default to the currently selected folder. If left unchecked, the dropdown will default to no folder selected.', 'wicked-folders' ); ?>"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    &nbsp;
                </th>
                <td>
                    <label>
                        <input type="checkbox" name="include_attachment_children" value="1"<?php if ( $include_attachment_children ) echo ' checked="checked"'; ?>/>
                        <?php _e( 'Include media from child folders', 'wicked-folders' ); ?>
                        <span class="dashicons dashicons-editor-help" title="<?php _e( "When unchecked (default) and a folder is selected, only media assigned to that folder will be displayed.  When checked, media in the selected folder *and* media in any of the folder's child folders will be displayed.", 'wicked-folders' ); ?>"></span>
                    </label>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <?php if ( $is_pro_active && ! is_multisite() ) : ?>
        <h2><?php _e( 'Wicked Folders Pro', 'wicked-folders' ); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="wicked-folders-pro-license-key"><?php _e( 'License Key', 'wicked-folders' ); ?></label>
                </th>
                <td>
                    <?php if ( ! apply_filters( 'wicked_folders_mask_license_key', true ) ) : ?>
                        <p class="code"><?php echo esc_html( $license_key ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! $valid_license ) : ?>
                        <input type="text" id="wicked-folders-pro-license-key" class="regular-text" name="wicked_folders_pro_license_key" value="<?php echo esc_attr( $license_key ); ?>" />
                    <?php endif; ?>
                    <?php if ( $license_status ) : ?>
                        <div class="wicked-folders-license-status"><?php echo esc_html( $license_status ); ?></div>
                    <?php endif; ?>
                    <?php if ( $valid_license ) : ?>
                        <input name="deactivate_license" id="deactivate-license" class="button" value="<?php _e( 'Deactivate License', 'wicked-folders' ); ?>" type="submit" />
                    <?php else : ?>
                        <input name="activate_license" id="activate-license" class="button" value="<?php _e( 'Activate License', 'wicked-folders' ); ?>" type="submit" />
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>
    <p class="submit">
        <input name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>" type="submit" />
    </p>
</form>
