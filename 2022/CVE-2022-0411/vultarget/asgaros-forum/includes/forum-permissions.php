<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumPermissions {
    private $asgarosforum = null;
    public $currentUserID;

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('init', array($this, 'initialize'));
        add_action('asgarosforum_prepare_profile', array($this, 'change_ban_status'));

        // Users list in administration.
        add_filter('manage_users_columns', array($this, 'manage_users_columns'));
        add_action('manage_users_custom_column', array($this, 'manage_users_custom_column'), 10, 3);

        // Filtering users list in administration by forum role.
        add_filter('views_users', array($this, 'permission_views'), 10);
        add_action('pre_user_query', array($this, 'permission_user_query'));

	}

    public function initialize() {
        $this->currentUserID = get_current_user_id();

        if ($this->isAdministrator($this->currentUserID)) {
            // Bulk edit inside the users list.
            add_filter('bulk_actions-users', array($this, 'bulk_actions_users'), 10);
            add_filter('handle_bulk_actions-users', array($this, 'handle_bulk_actions_users'), 10, 3);
            add_action('admin_notices', array($this, 'bulk_actions_admin_notices'));
        }
    }

    public function isSiteAdministrator($user_id = false) {
        if ($user_id) {
            if ($user_id === 'current') {
                // Return for current user
                return $this->isSiteAdministrator($this->currentUserID);
            } else if (is_super_admin($user_id) || user_can($user_id, 'administrator')) {
                // Always true for site administrators
                return true;
            }
        }

        // Otherwise false ...
        return false;
    }

    public function isAdministrator($userID = false) {
        if ($userID) {
            if ($userID === 'current') {
                // Return for current user
                return $this->isAdministrator($this->currentUserID);
            } else if ($this->isSiteAdministrator($userID)) {
                // Always true for site administrators
                return true;
            } else if ($this->get_forum_role($userID) === 'administrator') {
                // And true for forum administrators of course ...
                return true;
            }
        }

        // Otherwise false ...
        return false;
    }

    public function isModerator($userID = false) {
        if ($userID) {
            if ($userID === 'current') {
                // Return for current user
                return $this->isModerator($this->currentUserID);
            } else if ($this->isAdministrator($userID)) {
                // Always true for (site) administrators
                return true;
            } else if ($this->get_forum_role($userID) === 'moderator') {
                // And true for moderators of course ...
                return true;
            }
        }

        // Otherwise false ...
        return false;
    }

    public function isBanned($userID = false) {
        if ($userID) {
            if ($userID === 'current') {
                // Return for current user
                return $this->isBanned($this->currentUserID);
            } else if ($this->isSiteAdministrator($userID)) {
                // Ensure that site-administrators cannot get banned.
                return false;
            } else if ($this->get_forum_role($userID) === 'banned') {
                // And true for banned users of course ...
                return true;
            }
        }

        // Otherwise false ...
        return false;
    }

    public function getForumRole($userID) {
        if ($this->isAdministrator($userID)) {
            return __('Administrator', 'asgaros-forum');
        } else if ($this->isModerator($userID)) {
            return __('Moderator', 'asgaros-forum');
        } else if ($this->isBanned($userID)) {
            return __('Banned', 'asgaros-forum');
        } else {
            return __('User', 'asgaros-forum');
        }
    }

    public function get_forum_role($user_id) {
        $role = get_user_meta($user_id, 'asgarosforum_role', true);

        if (!empty($role)) {
            return $role;
        }

        return 'normal';
    }

    public function set_forum_role($user_id, $role) {
        // Ensure that forum role cannot get changed for site administrators.
        if (!$this->isSiteAdministrator($user_id)) {
            switch ($role) {
                case 'normal':
                    delete_user_meta($user_id, 'asgarosforum_role');
                break;
                case 'moderator':
                    update_user_meta($user_id, 'asgarosforum_role', 'moderator');
                break;
                case 'administrator':
                    update_user_meta($user_id, 'asgarosforum_role', 'administrator');
                break;
                case 'banned':
                    update_user_meta($user_id, 'asgarosforum_role', 'banned');
                break;
            }
        }
    }

    public function canUserAccessForumCategory($userID, $forumCategoryID) {
        $access_level = get_term_meta($forumCategoryID, 'category_access', true);

        if ($access_level == 'moderator' && !$this->isModerator($userID)) {
            return false;
        }

        return true;
    }

    // This function checks if a user can edit a specified post. Optional parameters for author_id and post_date available to reduce database queries.
    public function can_edit_post($user_id, $post_id, $author_id = false, $post_date = false) {
        // Disallow when user is not logged-in.
        if (!is_user_logged_in()) {
            return false;
        }

        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Allow when user is moderator.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Disallow when post editing is disabled.
        if (!$this->asgarosforum->options['enable_edit_post']) {
            return false;
        }

        // Disallow when user is not the author of a post.
        $author_id = ($author_id) ? $author_id : $this->asgarosforum->get_post_author($post_id);

        if ($user_id != $author_id) {
            return false;
        }

        // Allow when there is no time limitation.
        $time_limitation = $this->asgarosforum->options['time_limit_edit_posts'];

        if ($time_limitation == 0) {
            return true;
        }

        // Otherwise decision based on current time.
        $date_creation = ($post_date) ? $post_date : $this->asgarosforum->get_post_date($post_id);
        $date_creation = strtotime($date_creation);
        $date_now = strtotime($this->asgarosforum->current_time());
        $date_difference = $date_now - $date_creation;

        if (($time_limitation * 60) < $date_difference) {
            return false;
        } else {
            return true;
        }
    }

    // This function checks if a user can delete a specific topic.
    public function can_delete_topic($user_id, $topic_id, $author_id = false, $post_date = false) {
        // Disallow when user is not logged-in.
        if (!is_user_logged_in()) {
            return false;
        }

        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Allow when user is moderator.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Disallow when deleting topics is disabled.
        if (!$this->asgarosforum->options['enable_delete_topic']) {
            return false;
        }

        // Get information about first post.
        $first_post = $this->asgarosforum->content->get_first_post($topic_id);

        // Disallow when user is not the author of the topic.
        if (!$author_id) {
            $author_id = $first_post->author_id;
        }

        if ($user_id != $author_id) {
            return false;
        }

        // Allow when there is no time limitation.
        $time_limitation = $this->asgarosforum->options['time_limit_delete_topics'];

        if ($time_limitation == 0) {
            return true;
        }

        // Otherwise decision based on current time.
        $date_creation = ($post_date) ? $post_date : $first_post->date;
        $date_creation = strtotime($date_creation);
        $date_now = strtotime($this->asgarosforum->current_time());
        $date_difference = $date_now - $date_creation;

        if (($time_limitation * 60) < $date_difference) {
            return false;
        } else {
            return true;
        }
    }

    // This function checks if a user can delete a specific post.
    public function can_delete_post($user_id, $post_id, $author_id = false, $post_date = false) {
        // Disallow when user is not logged-in.
        if (!is_user_logged_in()) {
            return false;
        }

        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Allow when user is moderator.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Disallow when deleting posts is disabled.
        if (!$this->asgarosforum->options['enable_delete_post']) {
            return false;
        }

        // Disallow when user is not the author of the post.
        $author_id = ($author_id) ? $author_id : $this->asgarosforum->get_post_author($post_id);

        if ($user_id != $author_id) {
            return false;
        }

        // Allow when there is no time limitation.
        $time_limitation = $this->asgarosforum->options['time_limit_delete_posts'];

        if ($time_limitation == 0) {
            return true;
        }

        // Otherwise decision based on current time.
        $date_creation = ($post_date) ? $post_date : $this->asgarosforum->get_post_date($post_id);
        $date_creation = strtotime($date_creation);
        $date_now = strtotime($this->asgarosforum->current_time());
        $date_difference = $date_now - $date_creation;

        if (($time_limitation * 60) < $date_difference) {
            return false;
        } else {
            return true;
        }
    }

    // This function checks if a user can open a specific topic.
    public function can_open_topic($user_id, $topic_id) {
        // Disallow when user is not logged-in.
        if (!is_user_logged_in()) {
            return false;
        }

        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Allow when user is moderator.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Disallow when opening topics is disabled.
        if (!$this->asgarosforum->options['enable_open_topic']) {
            return false;
        }

        // Allow when user is the author of the topic.
        $author_id = $this->asgarosforum->get_topic_starter($topic_id);

        if ($user_id == $author_id) {
            return true;
        }

        // Otherwise disallow.
        return false;
    }

    // This function checks if a user can close a specific topic.
    public function can_close_topic($user_id, $topic_id) {
        // Disallow when user is not logged-in.
        if (!is_user_logged_in()) {
            return false;
        }

        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Allow when user is moderator.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Disallow when closing topics is disabled.
        if (!$this->asgarosforum->options['enable_close_topic']) {
            return false;
        }

        // Allow when user is the author of the topic.
        $author_id = $this->asgarosforum->get_topic_starter($topic_id);

        if ($user_id == $author_id) {
            return true;
        }

        // Otherwise disallow.
        return false;
    }

    // Check if a user can ban another user.
    public function can_ban_user($user_id, $ban_id) {
        if ($this->isSiteAdministrator($user_id)) {
            // Site administrators cannot ban other site administrators.
            if ($this->isSiteAdministrator($ban_id)) {
                return false;
            }

            return true;
        }

        if ($this->isAdministrator($user_id)) {
            // Administrators cannot ban other (site) administrators.
            if ($this->isAdministrator($ban_id)) {
                return false;
            }

            return true;
        }

        if ($this->isModerator($user_id)) {
            // Moderators cannot ban other administrators/moderators.
            // Hint: This function also works for administrators because the
            // moderator-check function also return TRUE for administrators.
            if ($this->isModerator($ban_id)) {
                return false;
            }

            return true;
        }

        // Otherwise the user cannot ban.
        return false;
    }

    // Checks if an user can create a post.
    public function can_create_post($user_id) {
        // Moderators can always create a post.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Ensure that the topic is not closed.
        if (!$this->asgarosforum->is_topic_closed($this->asgarosforum->current_topic)) {
            // If a logged-in user is not banned, he can create a post.
            if (is_user_logged_in() && !$this->isBanned($user_id)) {
                return true;
            }

            // A logged-out user can create a post if guest-postings is activated.
            if (!is_user_logged_in() && $this->asgarosforum->options['allow_guest_postings']) {
                return true;
            }
        }

        // Otherwise its not possible to create a post.
        return false;
    }

    // Checks if an user can use a signature.
    public function can_use_signature($user_id) {
        // Disallow when user is banned.
        if ($this->isBanned($user_id)) {
            return false;
        }

        // Moderators can always use a signature.
        if ($this->isModerator($user_id)) {
            return true;
        }

        // Based on the settings, logged-in users can use a signature.
        if ($this->asgarosforum->options['signatures_permission'] == 'loggedin') {
            return true;
        }

        // Otherwise its not possible to use a signature.
        return false;
    }

    public function ban_user($user_id, $ban_id) {
        // Verify nonce first.
        if (wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'ban_user_'.$ban_id)) {
            // Check if the current user can ban another user.
            if ($this->can_ban_user($user_id, $ban_id)) {
                // Ensure that the user is not already banned.
                if (!$this->isBanned($ban_id)) {
                    $this->set_forum_role($ban_id, 'banned');
                }
            }
        }
    }

    public function unban_user($user_id, $unban_id) {
        // Verify nonce first.
        if (wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'unban_user_'.$unban_id)) {
            // Check if the current user can ban another user.
            if ($this->can_ban_user($user_id, $unban_id)) {
                // Ensure that the user is banned.
                if ($this->isBanned($unban_id)) {
                    $this->set_forum_role($unban_id, 'normal');
                }
            }
        }
    }

    public function change_ban_status() {
        if (!empty($_GET['ban_user'])) {
            $user_id = get_current_user_id();
            $ban_id = sanitize_key($_GET['ban_user']);

            $this->ban_user($user_id, $ban_id);
        }

        if (!empty($_GET['unban_user'])) {
            $user_id = get_current_user_id();
            $unban_id = sanitize_key($_GET['unban_user']);

            $this->unban_user($user_id, $unban_id);
        }
    }

    private $get_users_by_role_cache = array();
    public function get_users_by_role($role) {
        // Return cached value if available.
        if (isset($this->get_users_by_role_cache[$role])) {
            return $this->get_users_by_role_cache[$role];
        }

        $data = array();

        // Ensure we dont run core query modifications for this function.
        $this->asgarosforum->prevent_query_modifications = true;

        switch ($role) {
            case 'all':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name')));
                $data = $query->results;
            break;
            case 'role':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name'), 'meta_key' => 'asgarosforum_role'));
                $data = $query->results;
            break;
            case 'siteadmin':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name'), 'role' => 'administrator'));
                $data = $query->results;
            break;
            case 'normal':
                $users_all = $this->get_users_by_role('all');
                $users_role = $this->get_users_by_role('role');
                $users_siteadmin = $this->get_users_by_role('siteadmin');

                $data = array_diff_key($users_all, $users_role, $users_siteadmin);
            break;
            case 'moderator':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name'), 'meta_key' => 'asgarosforum_role', 'meta_value' => 'moderator'));
                $users_moderator = $query->results;
                $users_siteadmin = $this->get_users_by_role('siteadmin');

                $data = array_diff_key($users_moderator, $users_siteadmin);
            break;
            case 'administrator':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name'), 'meta_key' => 'asgarosforum_role', 'meta_value' => 'administrator'));
                $users_administrator = $query->results;
                $users_siteadmin = $this->get_users_by_role('siteadmin');

                $data = array_unique(($users_administrator + $users_siteadmin), SORT_REGULAR);
            break;
            case 'banned':
                $query = new AsgarosForumUserQuery(array('fields' => array('ID', 'display_name'), 'meta_key' => 'asgarosforum_role', 'meta_value' => 'banned'));
                $users_banned = $query->results;
                $users_siteadmin = $this->get_users_by_role('siteadmin');

                $data = array_diff_key($users_banned, $users_siteadmin);
            break;
        }

        // Reset settings for core query modifications.
        $this->asgarosforum->prevent_query_modifications = false;

        // Cache value and return it.
        $this->get_users_by_role_cache[$role] = $data;
        return $this->get_users_by_role_cache[$role];
    }

    // Users List in Administration.
    public function manage_users_columns($columns) {
        $columns['forum-user-role'] = __('Forum Role', 'asgaros-forum');
        return $columns;
  	}

    public function manage_users_custom_column($output, $column_name, $user_id) {
		if ($column_name === 'forum-user-role') {
            $output .= $this->getForumRole($user_id);
		}

        return $output;
	}

    public function permission_views($views) {
        $views['forum-user-role'] = '<b>'.__('Forum Roles:', 'asgaros-forum').'</b>&nbsp;';

        // Normal users.
        $users = $this->get_users_by_role('normal');
        $cssClass = (!empty($_GET['forum-user-role']) && $_GET['forum-user-role'] == 'normal') ? 'class="current"' : '';
        $views['forum-user-role'] .= '<a '.$cssClass.' href="'.admin_url('users.php?forum-user-role=normal').'">'.__('Users', 'asgaros-forum').'</a> ('.count($users).')';

        // Moderators.
        $users = $this->get_users_by_role('moderator');
        $cssClass = (!empty($_GET['forum-user-role']) && $_GET['forum-user-role'] == 'moderator') ? 'class="current"' : '';
        $views['forum-user-role'] .= '&nbsp;|&nbsp;';
        $views['forum-user-role'] .= '<a '.$cssClass.' href="'.admin_url('users.php?forum-user-role=moderator').'">'.__('Moderators', 'asgaros-forum').'</a> ('.count($users).')';

        // Administrators.
        $users = $this->get_users_by_role('administrator');
        $cssClass = (!empty($_GET['forum-user-role']) && $_GET['forum-user-role'] == 'administrator') ? 'class="current"' : '';
        $views['forum-user-role'] .= '&nbsp;|&nbsp;';
        $views['forum-user-role'] .= '<a '.$cssClass.' href="'.admin_url('users.php?forum-user-role=administrator').'">'.__('Administrators', 'asgaros-forum').'</a> ('.count($users).')';

        // Banned.
        $users = $this->get_users_by_role('banned');
        $cssClass = (!empty($_GET['forum-user-role']) && $_GET['forum-user-role'] == 'banned') ? 'class="current"' : '';
        $views['forum-user-role'] .= '&nbsp;|&nbsp;';
        $views['forum-user-role'] .= '<a '.$cssClass.' href="'.admin_url('users.php?forum-user-role=banned').'">'.__('Banned', 'asgaros-forum').'</a> ('.count($users).')';

		return $views;
	}

    public function permission_user_query($Query = '') {
		global $pagenow, $wpdb;

        if (!$this->asgarosforum->prevent_query_modifications) {
            if ($pagenow == 'users.php') {
                if (!empty($_GET['forum-user-role'])) {
        			$role = sanitize_key($_GET['forum-user-role']);
                    $users = $this->get_users_by_role($role);

                    if (!empty($users)) {
                        $user_ids = array();

                        foreach ($users as $user) {
                            $user_ids[] = $user->ID;
                        }

                        $ids = implode(',', wp_parse_id_list($user_ids));
                        $Query->query_where .= " AND $wpdb->users.ID IN ($ids)";
                    } else {
                        $Query->query_where .= " AND $wpdb->users.ID IN (-1)";
                    }
        		}
            }
        }
    }

    public function bulk_actions_users($bulk_actions) {
        $bulk_actions['forum_role_assign_normal'] = __('Assign forum role:', 'asgaros-forum').' '.__('User', 'asgaros-forum');
        $bulk_actions['forum_role_assign_moderator'] = __('Assign forum role:', 'asgaros-forum').' '.__('Moderator', 'asgaros-forum');
        $bulk_actions['forum_role_assign_administrator'] = __('Assign forum role:', 'asgaros-forum').' '.__('Administrator', 'asgaros-forum');
        $bulk_actions['forum_role_assign_banned'] = __('Assign forum role:', 'asgaros-forum').' '.__('Banned', 'asgaros-forum');

        return $bulk_actions;
    }

    public function handle_bulk_actions_users($redirect_to, $action, $user_ids) {
        // Cancel when the user_ids array is empty.
        if (empty($user_ids)) {
            return $redirect_to;
        }

        // Check for a triggered bulk action first.
        $role = false;

        switch ($action) {
            case 'forum_role_assign_normal':
                $role = 'normal';
            break;
            case 'forum_role_assign_moderator':
                $role = 'moderator';
            break;
            case 'forum_role_assign_administrator':
                $role = 'administrator';
            break;
            case 'forum_role_assign_banned':
                $role = 'banned';
            break;
        }

        // Cancel when no bulk action found.
        if (!$role) {
            return $redirect_to;
        }

        foreach ($user_ids as $user_id) {
            $this->set_forum_role($user_id, $role);
        }

        $redirect_to = add_query_arg('forum_role_assigned', 1, $redirect_to);
        return $redirect_to;
    }

    public function bulk_actions_admin_notices() {
        if (!empty($_REQUEST['forum_role_assigned'])) {
            printf('<div class="updated"><p>'.esc_html__('Forum role assigned.', 'asgaros-forum').'</p></div>');
        }
    }
}
