<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumAdmin {
    private $asgarosforum = null;
    public $saved = false;
    public $error = false;
    public $option_views = false;

    public function __construct($object) {
        $this->asgarosforum = $object;

        // Set the views for the available options.
        $this->set_option_views();

        add_action('wp_loaded', array($this, 'save_settings'));
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // User profile options.
        add_action('edit_user_profile', array($this, 'user_profile_fields'));
        add_action('show_user_profile', array($this, 'user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'user_profile_fields_update'));
        add_action('personal_options_update', array($this, 'user_profile_fields_update'));

        // Users list in administration.
        add_filter('manage_users_columns', array($this, 'manage_users_columns'));
        add_action('manage_users_custom_column', array($this, 'manage_users_custom_column'), 10, 3);
    }

    public function set_option_views() {
        $this->option_views = array(
            'general' => array(
                'label' => __('General', 'asgaros-forum'),
                'icon' => 'fas fa-sliders-h'
            ),
            'features' => array(
                'label' => __('Features', 'asgaros-forum'),
                'icon' => 'fas fa-plug'
            ),
            'urls' => array(
                'label' => __('URLs & SEO', 'asgaros-forum'),
                'icon' => 'fas fa-link'
            ),
            'permissions' => array(
                'label' => __('Permissions', 'asgaros-forum'),
                'icon' => 'fas fa-user-shield'
            ),
            'breadcrumbs' => array(
                'label' => __('Breadcrumbs', 'asgaros-forum'),
                'icon' => 'fas fa-map-marked'
            ),
            'notifications' => array(
                'label' => __('Notifications', 'asgaros-forum'),
                'icon' => 'fas fa-envelope'
            ),
            'mentioning' => array(
                'label' => __('Mentioning', 'asgaros-forum'),
                'icon' => 'fas fa-at'
            ),
            'memberslist' => array(
                'label' => __('Members List', 'asgaros-forum'),
                'icon' => 'fas fa-users'
            ),
            'profiles' => array(
                'label' => __('Profiles', 'asgaros-forum'),
                'icon' => 'fas fa-user'
            ),
            'uploads' => array(
                'label' => __('Uploads', 'asgaros-forum'),
                'icon' => 'fas fa-upload'
            ),
            'reports' => array(
                'label' => __('Reports', 'asgaros-forum'),
                'icon' => 'fas fa-exclamation-triangle'
            ),
            'signatures' => array(
                'label' => __('Signatures', 'asgaros-forum'),
                'icon' => 'fas fa-signature'
            ),
            'activity' => array(
                'label' => __('Activity', 'asgaros-forum'),
                'icon' => 'fas fa-bullhorn'
            ),
            'polls' => array(
                'label' => __('Polls', 'asgaros-forum'),
                'icon' => 'fas fa-poll-h'
            ),
            'spoilers' => array(
                'label' => __('Spoilers', 'asgaros-forum'),
                'icon' => 'fas fa-eye-slash'
            ),
            'reputation' => array(
                'label' => __('Reputation', 'asgaros-forum'),
                'icon' => 'fas fa-medal'
            )
        );
    }

    public function render_options_header($option) {
        echo '<div class="settings-header">';
            echo '<span class="'.esc_attr($this->option_views[$option]['icon']).'"></span>';
            echo esc_html($this->option_views[$option]['label']);
        echo '</div>';
    }

    public function user_profile_fields($user) {
        $output = '';

        // Show settings only when current user is admin ...
        if (current_user_can('manage_options')) {
            // ... and he edits a non-admin user.
            if (!user_can($user->ID, 'manage_options')) {
                $role = $this->asgarosforum->permissions->get_forum_role($user->ID);

                $output .= '<tr>';
                $output .= '<th><label for="asgarosforum_role">'.__('Forum Role', 'asgaros-forum').'</label></th>';
                $output .= '<td>';

                $output .= '<select name="asgarosforum_role" id="asgarosforum_role">';
                $output .= '<option value="normal" '.selected($role, 'normal', false).'>'.__('User', 'asgaros-forum').'</option>';
                $output .= '<option value="moderator" '.selected($role, 'moderator', false).'>'.__('Moderator', 'asgaros-forum').'</option>';
                $output .= '<option value="administrator" '.selected($role, 'administrator', false).'>'.__('Administrator', 'asgaros-forum').'</option>';
                $output .= '<option value="banned" '.selected($role, 'banned', false).'>'.__('Banned', 'asgaros-forum').'</option>';
                $output .= '</select>';

                $output .= '</td>';
                $output .= '</tr>';
            }

            $output .= AsgarosForumUserGroups::showUserProfileFields($user->ID);
        }

        if ($this->asgarosforum->options['enable_mentioning']) {
            $output .= '<tr>';
            $output .= '<th><label for="asgarosforum_mention_notify">'.__('Notify me when I get mentioned', 'asgaros-forum').'</label></th>';
            $output .= '<td><input type="checkbox" name="asgarosforum_mention_notify" id="asgarosforum_mention_notify" value="1" '.checked($this->asgarosforum->mentioning->user_wants_notification($user->ID), true, false).'></td>';
            $output .= '</tr>';
        }

        if ($this->asgarosforum->options['allow_signatures']) {
            // Ensure that the user has permission to use a signature.
            if ($this->asgarosforum->permissions->can_use_signature($user->ID)) {
                $output .= '<tr>';
                $output .= '<th><label for="asgarosforum_signature">'.__('Signature', 'asgaros-forum').'</label></th>';
                $output .= '<td>';
                $output .= '<textarea rows="5" cols="30" name="asgarosforum_signature" id="asgarosforum_signature">'.get_user_meta($user->ID, 'asgarosforum_signature', true).'</textarea>';

                // Show info about allowed HTML tags.
                if ($this->asgarosforum->options['signatures_html_allowed']) {
                    $output .= '<p class="description">';
                    $output .= __('You can use the following HTML tags in signatures:', 'asgaros-forum');
                    $output .= '&nbsp;<code>'.esc_html($this->asgarosforum->options['signatures_html_tags']).'</code>';
                    $output .= '</p>';
                } else {
                    $output .= '<p class="description">'.__('HTML tags are not allowed in signatures.', 'asgaros-forum').'</p>';
                }

                $output .= '</td>';
                $output .= '</tr>';
            }
        }

        if (!empty($output)) {
            echo '<h2>'.esc_html__('Forum', 'asgaros-forum').'</h2>';
            echo '<table class="form-table">';
            echo $output;
            echo '</table>';
        }
    }

    public function user_profile_fields_update($user_id) {
        $user_id = absint($user_id);

        if (current_user_can('manage_options')) {
            if (!user_can($user_id, 'manage_options')) {
                if (isset($_POST['asgarosforum_role'])) {
                    $this->asgarosforum->permissions->set_forum_role($user_id, sanitize_key($_POST['asgarosforum_role']));
                }
            }

            AsgarosForumUserGroups::updateUserProfileFields($user_id);
        }

        if ($this->asgarosforum->options['enable_mentioning']) {
            if (isset($_POST['asgarosforum_mention_notify'])) {
                update_user_meta($user_id, 'asgarosforum_mention_notify', 'yes');
            } else {
                update_user_meta($user_id, 'asgarosforum_mention_notify', 'no');
            }
        }

        if ($this->asgarosforum->options['allow_signatures']) {
            // Ensure that the user has permission to use a signature.
            if ($this->asgarosforum->permissions->can_use_signature($user_id)) {
                if (isset($_POST['asgarosforum_signature'])) {
                    if ($this->asgarosforum->options['signatures_html_allowed']) {
						// Parse signature before saving.
						$allowed_signature_html_tags = array();

						if (!empty($this->asgarosforum->options['signatures_html_tags'])) {
							$tags = $this->asgarosforum->options['signatures_html_tags'];
							$tags = str_replace('><', ',', $tags);
							$tags = str_replace('<', '', $tags);
							$tags = str_replace('>', '', $tags);
							$tags = explode(',', $tags);

							foreach ($tags as $tag) {
								$allowed_signature_html_tags[$tag] = array();
							}
						}

                        update_user_meta($user_id, 'asgarosforum_signature', trim(wp_kses($_POST['asgarosforum_signature'], $allowed_signature_html_tags)));
                    } else {
                        update_user_meta($user_id, 'asgarosforum_signature', sanitize_textarea_field($_POST['asgarosforum_signature']));
                    }
                } else {
                    delete_user_meta($user_id, 'asgarosforum_signature');
                }
            }
        }
    }

    // Add all required pages to the menu.
    public function add_admin_pages() {
        if ($this->asgarosforum->permissions->isAdministrator('current')) {
            add_menu_page(__('Forum', 'asgaros-forum'), __('Forum', 'asgaros-forum'), 'read', 'asgarosforum-structure', array($this, 'structure_page'), 'none');
            add_submenu_page('asgarosforum-structure', __('Structure', 'asgaros-forum'), __('Structure', 'asgaros-forum'), 'read', 'asgarosforum-structure', array($this, 'structure_page'));
            add_submenu_page('asgarosforum-structure', __('Appearance', 'asgaros-forum'), __('Appearance', 'asgaros-forum'), 'read', 'asgarosforum-appearance', array($this, 'appearance_page'));
            add_submenu_page('asgarosforum-structure', __('Usergroups', 'asgaros-forum'), __('Usergroups', 'asgaros-forum'), 'read', 'asgarosforum-usergroups', array($this, 'usergroups_page'));

            do_action('asgarosforum_add_admin_submenu_page');

            add_submenu_page('asgarosforum-structure', __('Settings', 'asgaros-forum'), __('Settings', 'asgaros-forum'), 'read', 'asgarosforum-options', array($this, 'options_page'));
        }
    }

    public function options_page() {
        require 'views/options.php';
    }

    public function structure_page() {
        require 'views/structure.php';
    }

    public function appearance_page() {
        require 'views/appearance.php';
    }

    public function usergroups_page() {
        require 'views/usergroups.php';
    }

    public function enqueue_admin_scripts($hook) {
        wp_enqueue_style('asgarosforum-fontawesome', $this->asgarosforum->plugin_url.'libs/fontawesome/css/all.min.css', array(), $this->asgarosforum->version);
        wp_enqueue_style('asgarosforum-fontawesome-compat-v4', $this->asgarosforum->plugin_url.'libs/fontawesome/css/v4-shims.min.css', array(), $this->asgarosforum->version);
        wp_enqueue_style('asgarosforum-admin-css', $this->asgarosforum->plugin_url.'admin/css/admin.css', array(), $this->asgarosforum->version);

        if (strstr($hook, 'asgarosforum') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_code_editor(array('type' => 'text/html'));
            wp_enqueue_script('asgarosforum-admin-js', $this->asgarosforum->plugin_url.'admin/js/admin.js', array('jquery', 'wp-color-picker'), $this->asgarosforum->version, true);
        }
    }

    public function save_settings() {
        // Only save changes when the user is an forum/site administrator.
        if ($this->asgarosforum->permissions->isAdministrator('current')) {
            if (isset($_POST['af_options_submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_options');

                $this->save_options();
            } else if (isset($_POST['af_appearance_submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_appearance');

                $this->save_appearance();
            } else if (isset($_POST['af-create-edit-forum-submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_forum');

                $this->save_forum();
            } else if (isset($_POST['asgaros-forum-delete-forum'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_delete_forum');

                if (!empty($_POST['forum-id']) && is_numeric($_POST['forum-id']) && !empty($_POST['forum-category']) && is_numeric($_POST['forum-category'])) {
                    $this->delete_forum(sanitize_key($_POST['forum-id']), sanitize_key($_POST['forum-category']));
                }
            } else if (isset($_POST['af-create-edit-category-submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_category');

                $this->save_category();
            } else if (isset($_POST['asgaros-forum-delete-category'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_delete_category');

                if (!empty($_POST['category-id']) && is_numeric($_POST['category-id'])) {
                    $this->delete_category(sanitize_key($_POST['category-id']));
                }
            } else if (isset($_POST['af-create-edit-usergroup-category-submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_usergroup_category');

                $saveStatus = AsgarosForumUserGroups::saveUserGroupCategory();

                if (is_wp_error($saveStatus)) {
                    $this->error = $saveStatus->get_error_message();
                } else {
                    $this->saved = $saveStatus;
                }
            } else if (isset($_POST['af-create-edit-usergroup-submit'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_save_usergroup');

                $saveStatus = AsgarosForumUserGroups::saveUserGroup();

                if (is_wp_error($saveStatus)) {
                    $this->error = $saveStatus->get_error_message();
                } else {
                    $this->saved = $saveStatus;
                }
            } else if (isset($_POST['asgaros-forum-delete-usergroup'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_delete_usergroup');

                if (!empty($_POST['usergroup-id']) && is_numeric($_POST['usergroup-id'])) {
                    AsgarosForumUserGroups::deleteUserGroup(sanitize_key($_POST['usergroup-id']));
                }
            } else if (isset($_POST['asgaros-forum-delete-usergroup-category'])) {
                // Verify nonce first.
                check_admin_referer('asgaros_forum_delete_usergroup_category');

                if (!empty($_POST['usergroup-category-id']) && is_numeric($_POST['usergroup-category-id'])) {
                    AsgarosForumUserGroups::deleteUserGroupCategory(sanitize_key($_POST['usergroup-category-id']));
                }
            }
        }
    }

    /* OPTIONS */
    public function save_options() {
        $saved_ops = array();

        foreach ($this->asgarosforum->options_default as $k => $v) {
            if (isset($_POST[$k])) {
                if (is_numeric($v)) {
                    $saved_ops[$k] = ((int)$_POST[$k] >= 0) ? (int)$_POST[$k] : $v;
                } else if (is_bool($v)) {
                    $saved_ops[$k] = (bool)$_POST[$k];
                } else if ($k === 'allowed_filetypes') {
                    $tmp = strtolower(sanitize_text_field($_POST[$k]));
                    $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
				} else if ($k === 'signatures_html_tags') {
					$tmp = wp_kses_post($_POST[$k]);
                    $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
				} else {
                    $tmp = sanitize_text_field($_POST[$k]);
                    $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
                }
            } else {
                if (is_bool($v)) {
                    $saved_ops[$k] = false;
                } else {
                    $saved_ops[$k] = $v;
                }
            }
        }

        $this->asgarosforum->save_options($saved_ops);
        $this->saved = true;
    }

    public function save_appearance() {
        $saved_ops = array();

        foreach ($this->asgarosforum->appearance->options_default as $k => $v) {
            if (isset($_POST[$k])) {
				$tmp = sanitize_text_field($_POST[$k]);
                $saved_ops[$k] = (!empty($tmp)) ? $tmp : $v;
            } else {
                $saved_ops[$k] = $v;
            }
        }

        $this->asgarosforum->appearance->save_options($saved_ops);
        $this->saved = true;
    }

    /* STRUCTURE */
    public function save_category() {
        $category_id        = sanitize_key($_POST['category_id']);
        $category_name      = sanitize_text_field($_POST['category_name']);
        $category_access    = sanitize_key($_POST['category_access']);
        $category_order     = (is_numeric($_POST['category_order'])) ? sanitize_key($_POST['category_order']) : 1;

        if (!empty($category_name)) {
            if ($category_id === 'new') {
                $newTerm = wp_insert_term($category_name, 'asgarosforum-category');

                // Return possible error.
                if (is_wp_error($newTerm)) {
                    $this->error = $newTerm->get_error_message();
                    return;
                }

                $category_id = $newTerm['term_id'];
            } else {
                wp_update_term($category_id, 'asgarosforum-category', array('name' => $category_name));
            }

            update_term_meta($category_id, 'category_access', $category_access);
            update_term_meta($category_id, 'order', $category_order);
            AsgarosForumUserGroups::saveUserGroupsOfForumCategory($category_id);

            $this->saved = true;
        }
    }

    public function save_forum() {
        // ID of the forum.
        $forum_id           = sanitize_key($_POST['forum_id']);

        // Determine parent IDs.
        $parent_ids          = explode('_', sanitize_key($_POST['forum_parent']));
        $forum_category     = $parent_ids[0];
        $forum_parent_forum = $parent_ids[1];

        // Additional data.
        $forum_name         = sanitize_text_field($_POST['forum_name']);
        $forum_description  = sanitize_text_field($_POST['forum_description']);
        $forum_icon         = sanitize_text_field($_POST['forum_icon']);
        $forum_icon         = (empty($forum_icon)) ? 'fas fa-comments' : $forum_icon;
        $forum_status       = sanitize_key($_POST['forum_status']);
        $forum_order        = (is_numeric($_POST['forum_order'])) ? sanitize_key($_POST['forum_order']) : 0;

        if (!empty($forum_name)) {
            if ($forum_id === 'new') {
                $this->asgarosforum->content->insert_forum($forum_category, $forum_name, $forum_description, $forum_parent_forum, $forum_icon, $forum_order, $forum_status);
            } else {
                // Update forum.
                $this->asgarosforum->db->update(
                    $this->asgarosforum->tables->forums,
                    array('name' => $forum_name, 'description' => $forum_description, 'icon' => $forum_icon, 'sort' => $forum_order, 'forum_status' => $forum_status, 'parent_id' => $forum_category, 'parent_forum' => $forum_parent_forum),
                    array('id' => $forum_id),
                    array('%s', '%s', '%s', '%d', '%s', '%d', '%d'),
                    array('%d')
                );

                // Update category ids of sub-forums in case the forum got moved.
                $this->asgarosforum->db->update(
                    $this->asgarosforum->tables->forums,
                    array('parent_id' => $forum_category),
                    array('parent_forum' => $forum_id),
                    array('%d'),
                    array('%d')
                );

                // Approve all unapproved topics in a forum if its status is not set to approval.
                if ($forum_status != 'approval') {
                    // Get all unapproved topics from this forum.
                    $unapproved_topics = $this->asgarosforum->approval->get_unapproved_topics($forum_id);

                    // Approve those topics if found.
                    if (!empty($unapproved_topics)) {
                        foreach ($unapproved_topics as $topic) {
                            $this->asgarosforum->approval->approve_topic($topic->id);
                        }
                    }
                }
            }

            $this->saved = true;
        }
    }

    public function delete_category($categoryID) {
        $forums = $this->asgarosforum->db->get_col("SELECT id FROM {$this->asgarosforum->tables->forums} WHERE parent_id = {$categoryID};");

        if (!empty($forums)) {
            foreach ($forums as $forum) {
                $this->delete_forum($forum, $categoryID);
            }
        }

        wp_delete_term($categoryID, 'asgarosforum-category');
    }

    public function delete_forum($forum_id, $category_id) {
        // Delete all subforums first
        $subforums = $this->asgarosforum->get_forums($category_id, $forum_id);

        if (count($subforums) > 0) {
            foreach ($subforums as $subforum) {
                $this->delete_forum($subforum->id, $category_id);
            }
        }

        // Delete all topics.
        $topics = $this->asgarosforum->db->get_col("SELECT id FROM {$this->asgarosforum->tables->topics} WHERE parent_id = {$forum_id};");

        if (!empty($topics)) {
            foreach ($topics as $topic) {
                $this->asgarosforum->delete_topic($topic, true, false);
            }
        }

        // Delete subscriptions for this forum.
        $this->asgarosforum->notifications->remove_all_forum_subscriptions($forum_id);

        // Last but not least delete the forum
        $this->asgarosforum->db->delete($this->asgarosforum->tables->forums, array('id' => $forum_id), array('%d'));

        $this->saved = true;
    }

    /* USERGROUPS */
    public function render_admin_header($title, $titleUpdated) {
        // Workaround to ensure that admin-notices are shown outside of our panel.
        echo '<h1 id="asgaros-panel-notice-area"></h1>';

        echo '<div id="asgaros-panel">';
            echo '<div class="header-panel">';
                echo '<div class="sub-panel-left">';
                    echo '<img src="'.esc_url($this->asgarosforum->plugin_url.'admin/images/logo.png').'">';
                echo '</div>';
                echo '<div class="sub-panel-left">';
                    echo '<h1>'.esc_html($title).'</h1>';
                echo '</div>';
                echo '<div class="sub-panel-right">';
                    echo '<a href="https://www.asgaros.de/support/" target="_blank">';
                        echo '<span class="asgaros-panel-icon fas fa-user"></span>';
                        echo esc_html__('Official Support Forum', 'asgaros-forum');
                    echo '</a>';
                    echo '&bull;';
                    echo '<a href="https://www.asgaros.de/docs/" target="_blank">';
                        echo '<span class="asgaros-panel-icon fas fa-book"></span>';
                        echo esc_html__('Documentation', 'asgaros-forum');
                    echo '</a>';
                    echo '&bull;';
                    echo '<a href="https://www.asgaros.de/donate/" target="_blank">';
                        echo '<span class="asgaros-panel-icon donate-icon fas fa-heart"></span>';
                        echo esc_html__('Donate', 'asgaros-forum');
                    echo '</a>';
                echo '</div>';
                echo '<div class="clear"></div>';
            echo '</div>';

            if ($this->error) {
                echo '<div class="error-panel"><p>'.esc_html($this->error).'</p></div>';
            } else if ($this->saved) {
                echo '<div class="updated-panel"><p>'.esc_html($titleUpdated).'</p></div>';
            }

        echo '</div>';
    }

    // Users List in Administration.
    public function manage_users_columns($columns) {
        $columns['forum-user-posts'] = __('Forum Posts', 'asgaros-forum');
        return $columns;
  	}

    public function manage_users_custom_column($output, $column_name, $user_id) {
		if ($column_name === 'forum-user-posts') {
            $output .= $this->asgarosforum->content->count_posts_by_user($user_id);
		}

        return $output;
	}
}
