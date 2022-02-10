<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumUserGroups {
    private static $asgarosforum = null;
    private static $taxonomyName = 'asgarosforum-usergroup';

    public function __construct($object) {
		self::$asgarosforum = $object;

        add_action('init', array($this, 'initialize'));

        // Users list in administration.
        add_filter('manage_users_columns', array($this, 'manageUsersColumns'));
        add_action('manage_users_custom_column', array($this, 'manageUsersCustomColumn'), 10, 3);
        add_action('delete_user', array($this, 'delete_term_relationships'));

        // Filtering users list in administration by usergroup.
		add_filter('views_users', array($this, 'views'), 20);
        add_action('pre_user_query', array($this, 'user_query'));

		// Certain actions when a new user registers.
        add_action('user_register', array($this, 'add_new_user_to_usergroups'), 10, 1);
    }

    public function initialize() {
        self::initializeTaxonomy();

        if (self::$asgarosforum->permissions->isAdministrator(get_current_user_id())) {
            // Bulk edit inside the users list.
            add_filter('bulk_actions-users', array($this, 'bulk_actions_users'), 20);
            add_filter('handle_bulk_actions-users', array($this, 'handle_bulk_actions_users'), 10, 3);
            add_action('admin_notices', array($this, 'bulk_actions_admin_notices'));
        }
    }

    public static function initializeTaxonomy() {
        // Register the taxonomies.
        register_taxonomy(
			self::$taxonomyName,
			null,
			array(
				'public'        => false,
                'hierarchical'  => true,
				'rewrite'       => false
			)
		);

        self::$taxonomyName = apply_filters('asgarosforum_filter_user_groups_taxonomy_name', self::$taxonomyName);
    }

    //======================================================================
    // FUNCTIONS FOR INSERTING CONTENT.
    //======================================================================

    // Adds a new usergroup.
    public static function insertUserGroup($category_id, $name, $color = '#444444', $visibility = 'normal', $auto_add = 'no', $icon = '') {
        $name = trim($name);
        $color = trim($color);
        $visibility = trim($visibility);
        $auto_add = trim($auto_add);
        $icon = trim($icon);

        $status = wp_insert_term($name, self::$taxonomyName, array('parent' => $category_id));

        // Return possible error.
        if (is_wp_error($status)) {
            return $status;
        }

        $usergroup_id = $status['term_id'];

        $status = self::updateUserGroupColor($usergroup_id, $color);

        // Return possible error.
        if (is_wp_error($status)) {
            return $status;
        }

        $status = self::update_usergroup_visibility($usergroup_id, $visibility);
        $status = self::update_usergroup_auto_add($usergroup_id, $auto_add);
        $status = self::update_usergroup_icon($usergroup_id, $icon);

        return $status;
    }

    public static function insertUserGroupCategory($categoryName) {
        $categoryName = trim($categoryName);

        $status = wp_insert_term($categoryName, self::$taxonomyName);

        return $status;
    }

    public static function insertUserGroupsOfForumCategory($forumCategoryID, $user_group_ids) {
        // Only assign usergroup-IDs to a forum category when there are some. Otherwise delete them all.
        if (!empty($user_group_ids)) {
            update_term_meta($forumCategoryID, 'usergroups', $user_group_ids);
        } else {
            self::deleteUserGroupsOfForumCategory($forumCategoryID);
        }
    }

    // Assign an user to all given usergroups. The user gets removed from usergroups which are not inside the array anymore.
    public static function insertUserGroupsOfUsers($user_id, $usergroups) {
        // Get all current usergroups of the user first which allows us to run specific hooks.
        $current_usergroups = self::getUserGroupsOfUser($user_id, 'ids');

        if (!empty($usergroups)) {
            wp_set_object_terms($user_id, $usergroups, self::$taxonomyName);
            clean_object_term_cache($user_id, self::$taxonomyName);
        } else {
            self::deleteUserGroupsOfUser($user_id);
        }

        // Run hooks for adding the user to a specific group.
        foreach ($usergroups as $usergroup) {
            if (!in_array($usergroup, $current_usergroups)) {
                do_action('asgarosforum_usergroup_'.$usergroup.'_add_user', $user_id, $usergroup);
            }
        }

        // Run hooks for removing the user from a specific group.
        foreach ($current_usergroups as $usergroup) {
            if (!in_array($usergroup, $usergroups)) {
                do_action('asgarosforum_usergroup_'.$usergroup.'_remove_user', $user_id, $usergroup);
            }
        }
    }

    //======================================================================
    // FUNCTIONS FOR UPDATING CONTENT.
    //======================================================================

    public static function updateUserGroup($usergroup_id, $category_id, $name, $color = '#444444', $visibility = 'normal', $auto_add = 'no', $icon = '') {
        $name = trim($name);
        $color = trim($color);
        $visibility = trim($visibility);
        $auto_add = trim($auto_add);
        $icon = trim($icon);

        $status = wp_update_term($usergroup_id, self::$taxonomyName, array('parent' => $category_id, 'name' => $name));

        // Return possible error.
        if (is_wp_error($status)) {
            return $status;
        }

        $status = self::updateUserGroupColor($usergroup_id, $color);

        // Return possible error.
        if (is_wp_error($status)) {
            return $status;
        }

        $status = self::update_usergroup_visibility($usergroup_id, $visibility);
        $status = self::update_usergroup_auto_add($usergroup_id, $auto_add);
        $status = self::update_usergroup_icon($usergroup_id, $icon);

        return $status;
    }

    public static function updateUserGroupCategory($categoryID, $categoryName) {
        $categoryName = trim($categoryName);

        $status = wp_update_term($categoryID, self::$taxonomyName, array('name' => $categoryName));

        return $status;
    }

    public static function updateUserGroupColor($userGroupID, $userGroupColor) {
        $userGroupColor = trim($userGroupColor);
        $userGroupColor = (empty($userGroupColor)) ? '#444444' : $userGroupColor;

        $status = update_term_meta($userGroupID, 'usergroup-color', $userGroupColor);

        return $status;
    }

    public static function update_usergroup_visibility($usergroup_id, $usergroup_visibility) {
        $usergroup_visibility = trim($usergroup_visibility);
        $usergroup_visibility = (empty($usergroup_visibility)) ? 'normal' : $usergroup_visibility;

        $status = update_term_meta($usergroup_id, 'usergroup-visibility', $usergroup_visibility);

        return $status;
    }

    public static function update_usergroup_auto_add($usergroup_id, $usergroup_auto_add) {
        $usergroup_auto_add = trim($usergroup_auto_add);
        $usergroup_auto_add = (empty($usergroup_auto_add)) ? 'no' : $usergroup_auto_add;

        $status = update_term_meta($usergroup_id, 'usergroup-auto-add', $usergroup_auto_add);

        return $status;
    }

    public static function update_usergroup_icon($usergroup_id, $icon) {
        $icon = trim($icon);

        $status = update_term_meta($usergroup_id, 'usergroup-icon', $icon);

        return $status;
    }

    //======================================================================
    // FUNCTIONS FOR DELETING CONTENT.
    //======================================================================

    public static function deleteUserGroup($userGroupID) {
        wp_delete_term($userGroupID, self::$taxonomyName);
    }

    public static function deleteUserGroupCategory($categoryID) {
        // Get all usergroups of the category first.
        $userGroups = self::getUserGroupsOfCategory($categoryID);

        // Delete all usergroups of the category.
        foreach ($userGroups as $group) {
            self::deleteUserGroup($group->term_id);
        }

        // Now delete the category.
        wp_delete_term($categoryID, self::$taxonomyName);
    }

    public static function deleteUserGroupsOfForumCategory($forumCategoryID) {
        delete_term_meta($forumCategoryID, 'usergroups');
    }

    public static function deleteUserGroupsOfUser($userID) {
        wp_delete_object_term_relationships($userID, self::$taxonomyName);
        clean_object_term_cache($userID, self::$taxonomyName);
    }

    //======================================================================
    // FUNCTIONS FOR GETTING CONTENT.
    //======================================================================

    // Returns a specific usergroup.
    public static function getUserGroup($userGroupID) {
        return get_term($userGroupID, self::$taxonomyName);
    }

    // Returns all or specific usergroups.
    public static function getUserGroups($include = array(), $visible_groups_only = false) {
        $userGroups = false;

        // First load all terms.
        if ($visible_groups_only) {
            $userGroups = get_terms(self::$taxonomyName, array('hide_empty' => false, 'include' => $include, 'meta_key' => 'usergroup-visibility', 'meta_value' => 'normal'));
        } else {
            $userGroups = get_terms(self::$taxonomyName, array('hide_empty' => false, 'include' => $include));
        }

        // Now remove the categories so we only have usergroups.
        $userGroups = array_filter($userGroups, array('AsgarosForumUserGroups', 'getUserGroupsArrayFilter'));

        return $userGroups;
    }

    // Explicit callback function for array_filter() to support older versions of PHP.
    public static function getUserGroupsArrayFilter($term) {
        return ($term->parent != 0);
    }

    // Returns all usergroups of a specific category.
    public static function getUserGroupsOfCategory($categoryID) {
        return get_terms(self::$taxonomyName, array('hide_empty' => false, 'parent' => $categoryID));
    }

    // Returns all usergroups categories.
    public static function getUserGroupCategories($hide_empty = false) {
        $userGroupCategories = get_terms(self::$taxonomyName, array('hide_empty' => false, 'parent' => 0));

        // Hide categories without usergroups.
        if ($hide_empty) {
            foreach ($userGroupCategories as $key => $category) {
                $userGroupsInCategory = self::getUserGroupsOfCategory($category->term_id);

                if (empty($userGroupsInCategory)) {
                    unset($userGroupCategories[$key]);
                }
            }
        }

        return $userGroupCategories;
    }

    // Returns all usergroups of an user.
    public static function getUserGroupsOfUser($userID, $fields = 'all', $filter_hidden = false) {
        $usergroups_of_user = wp_get_object_terms($userID, self::$taxonomyName, array('fields' => $fields));

        // Remove hidden usergroups.
        if ($filter_hidden) {
            foreach ($usergroups_of_user as $key => $value) {
                $visibility = self::get_usergroup_visibility($value->term_id);

                if ($visibility === 'hidden') {
                    unset($usergroups_of_user[$key]);
                }
            }
        }

        return $usergroups_of_user;
    }

    // Returns the color of an usergroup.
    public static function getUserGroupColor($userGroupID) {
        return get_term_meta($userGroupID, 'usergroup-color', true);
    }

    // Returns the visibility of an usergroup.
    public static function get_usergroup_visibility($usergroup_id) {
        return get_term_meta($usergroup_id, 'usergroup-visibility', true);
    }

    // Returns the auto add setting of an usergroup.
    public static function get_usergroup_auto_add($usergroup_id) {
        return get_term_meta($usergroup_id, 'usergroup-auto-add', true);
    }

    // Returns the icon of an usergroup.
    public static function get_usergroup_icon($usergroup_id) {
        $icon = get_term_meta($usergroup_id, 'usergroup-icon', true);

        if (empty($icon)) {
            $icon = false;
        }

        return $icon;
    }

    // Returns all usergroups of a specific forum category.
    public static function getUserGroupsOfForumCategory($forumCategoryID) {
        $userGroupsIDs = self::getUserGroupsIDsOfForumCategory($forumCategoryID);

        if (!empty($userGroupsIDs)) {
            return self::getUserGroups($userGroupsIDs);
        }

        return false;
    }

    // Returns all usergroups IDs of a specific forum category.
    public static function getUserGroupsIDsOfForumCategory($forumCategoryID) {
        return get_term_meta($forumCategoryID, 'usergroups', true);
    }

    // Returns all users of a usergroup.
    public static function get_ids_of_users_in_usergroup($userGroupID) {
        return get_objects_in_term($userGroupID, self::$taxonomyName);
    }

    public static function get_users_in_usergroup($usergroup_id) {
        // Get IDs first.
        $user_ids = self::get_ids_of_users_in_usergroup($usergroup_id);

        if (!empty($user_ids)) {
            $query = new AsgarosForumUserQuery(array(
                'fields'    => array('ID', 'display_name'),
                'include'   => $user_ids
            ));

            return $query->results;
        }

        return false;
    }

    //======================================================================
    // MORE FUNCTIONS.
    //======================================================================

    // Checks if a specific user can access a specific forum category.
    public static function canUserAccessForumCategory($userID, $forumCategoryID) {
        // Default status is true.
        $canAccess = true;

        // We only need to check the access when the user is not an administrator.
        if (!self::$asgarosforum->permissions->isAdministrator($userID)) {
            // Get usergroups IDs of a forum category first.
            $userGroupsIDsOfForumCategory = self::getUserGroupsIDsOfForumCategory($forumCategoryID);

            // Only continue the check when there are usergroups IDs for a forum category.
            if (!empty($userGroupsIDsOfForumCategory)) {
                // Now get the usergroups IDs of a user.
                $userGroupsIDsOfUser = self::getUserGroupsOfUser($userID, 'ids');

                // Get the insersection.
                $intersection = array_intersect($userGroupsIDsOfForumCategory, $userGroupsIDsOfUser);

                // When the intersection is empty, the user cant access the forum category.
                if (empty($intersection)) {
                    $canAccess = false;
                }
            }
        }

        return $canAccess;
    }

    // Checks if a user is in a specific usergroup.
    public static function isUserInUserGroup($userID, $userGroupID) {
        return is_object_in_term($userID, self::$taxonomyName, $userGroupID);
    }

    // Counts the users of an usergroup.
    public static function countUsersOfUserGroup($userGroupID) {
        return count(self::get_ids_of_users_in_usergroup($userGroupID));
    }

    //======================================================================
    // ADDITIONAL FUNCTIONS.
    //======================================================================

    // Users List in Administration.
    public function manageUsersColumns($columns) {
        $columns['forum-user-groups'] = __('Forum Usergroups', 'asgaros-forum');
        return $columns;
  	}

    public function manageUsersCustomColumn($output, $column_name, $user_id) {
		if ($column_name === 'forum-user-groups') {
            $usergroups = self::getUserGroupsOfUser($user_id);

    		if (!empty($usergroups)) {
        		foreach ($usergroups as $usergroup) {
        			$link = add_query_arg(array('forum-user-group' => $usergroup->term_id), admin_url('users.php'));
        			$output .= '<a href="'.$link.'" title="'.$usergroup->name.'">';
                    $output .= self::render_usergroup_tag($usergroup);
                    $output .= '</a>';
        		}
            }
		}

        return $output;
	}

    public static function saveUserGroup() {
        $usergroup_id           = sanitize_key($_POST['usergroup_id']);
        $usergroup_name         = sanitize_text_field($_POST['usergroup_name']);
        $usergroup_category     = sanitize_key($_POST['usergroup_category']);
        $usergroup_color        = sanitize_hex_color($_POST['usergroup_color']);
        $usergroup_visibility   = (isset($_POST['usergroup_visibility'])) ? 'hidden' : 'normal';
        $usergroup_auto_add     = (isset($_POST['usergroup_auto_add'])) ? 'yes' : 'no';
        $usergroup_icon         = sanitize_key($_POST['usergroup_icon']);

        if ($usergroup_id === 'new') {
            return self::insertUserGroup($usergroup_category, $usergroup_name, $usergroup_color, $usergroup_visibility, $usergroup_auto_add, $usergroup_icon);
        } else {
            return self::updateUserGroup($usergroup_id, $usergroup_category, $usergroup_name, $usergroup_color, $usergroup_visibility, $usergroup_auto_add, $usergroup_icon);
        }
    }

    public static function saveUserGroupCategory() {
        $category_id    = sanitize_key($_POST['usergroup_category_id']);
        $category_name  = sanitize_text_field($_POST['usergroup_category_name']);

        if ($category_id === 'new') {
            return self::insertUserGroupCategory($category_name);
        } else {
            return self::updateUserGroupCategory($category_id, $category_name);
        }
    }

    // Adds a new usergroups string to the structure page.
    public static function renderUserGroupsInCategory($categoryID) {
        $userGroupsOfForumCategory = self::getUserGroupsOfForumCategory($categoryID);

        if (!empty($userGroupsOfForumCategory)) {
            echo ' &middot; '.esc_html__('Usergroups:', 'asgaros-forum').' ';

            foreach ($userGroupsOfForumCategory as $key => $userGroup) {
                if ($key > 0) {
                    echo ', ';
                }

                echo esc_html($userGroup->name);
            }
        }
    }

    public static function renderCategoryEditorFields() {
        $userGroupCategories = self::getUserGroupCategories(true);

        if (!empty($userGroupCategories)) {
            echo '<tr id="usergroups-editor">';
                echo '<th><label>'.esc_html__('Usergroups:', 'asgaros-forum').'</label></th>';
                echo '<td>';
                    foreach ($userGroupCategories as $category) {
                        echo '<span class="usergroup-category-name">'.esc_html($category->name).':</span>';

                        $userGroups = self::getUserGroupsOfCategory($category->term_id);

                        foreach ($userGroups as $usergroup) {
                            echo '<label><input type="checkbox" name="category_usergroups[]" value="'.esc_attr($usergroup->term_id).'">';
                            echo self::render_usergroup_tag($usergroup);
                            echo '</label>';
                        }
                    }
                    echo '<span class="description">'.esc_html__('When usergroups are selected, only users of the selected usergroups will have access to the category.', 'asgaros-forum').'</span>';
                echo '</td>';
            echo '</tr>';
        }
    }

    public static function renderHiddenFields($categoryID) {
        $userGroupsIDsOfForumCategory = self::getUserGroupsIDsOfForumCategory($categoryID);
        $userGroupsOfForumCategoryString = '';

        if (!empty($userGroupsIDsOfForumCategory)) {
            $userGroupsOfForumCategoryString = implode(',', $userGroupsIDsOfForumCategory);
        }

        echo '<input type="hidden" id="category_'.esc_attr($categoryID).'_usergroups" value="'.esc_attr($userGroupsOfForumCategoryString).'">';
    }

    public static function saveUserGroupsOfForumCategory($forumCategoryID) {
		$user_group_ids = array();

		if (!empty($_POST['category_usergroups'])) {
			$user_group_ids = array_map('sanitize_key', $_POST['category_usergroups']);
		}

        self::insertUserGroupsOfForumCategory($forumCategoryID, $user_group_ids);
    }

    public static function filterCategories($unfilteredCategories) {
        global $user_ID;
        $filteredCategories = $unfilteredCategories;

        // We only need to filter when the user is not an administrator and when there are categories to filter.
        if (!self::$asgarosforum->permissions->isAdministrator('current') && !empty($filteredCategories) && !is_wp_error($filteredCategories)) {
            foreach ($filteredCategories as $key => $forumCategory) {
                $canAccess = self::canUserAccessForumCategory($user_ID, $forumCategory->term_id);

                if (!$canAccess) {
                    unset($filteredCategories[$key]);
                }
            }
        }

        return $filteredCategories;
    }

    public static function checkAccess($forumCategoryID) {
        global $user_ID;

        return self::canUserAccessForumCategory($user_ID, $forumCategoryID);
    }

    // Makes sure that only users who have access to the forum category will receive mails.
    public static function filterSubscriberMails($mails, $category_id) {
        // Only filter when there are mails.
        if (!empty($mails)) {
            foreach ($mails as $key => $mail) {
                // Get the user of the mail.
                $userObject = get_user_by('email', $mail);

                if (!empty($userObject)) {
                    $canAccess = self::canUserAccessForumCategory($userObject->ID, $category_id);

                    // When the user cant access the usergroup, remove it from the mail list.
                    if (!$canAccess) {
                        unset($mails[$key]);
                    }
                }
            }
        }

        return $mails;
    }

    public static function showUserProfileFields($userID) {
        $output = '';
        $userGroupCategories = self::getUserGroupCategories(true);

        if (!empty($userGroupCategories)) {
            $output .= '<tr class="usergroups-editor">';
            $output .= '<th><label>'.__('Usergroups', 'asgaros-forum').'</label></th>';
            $output .= '<td>';

            foreach ($userGroupCategories as $category) {
                $output .= '<span class="usergroup-category-name">'.$category->name.':</span>';

                $userGroups = self::getUserGroupsOfCategory($category->term_id);

                foreach ($userGroups as $usergroup) {
                    $is_user_in_usergroup = self::isUserInUserGroup($userID, $usergroup->term_id);
                    $label_id = self::$taxonomyName.'-'.$usergroup->term_id;

    				$output .= '<input type="checkbox" name="'.self::$taxonomyName.'[]" id="'.$label_id.'" value="'.$usergroup->term_id.'" '.checked(true, $is_user_in_usergroup, false).'>';
                    $output .= '<label for="'.$label_id.'">';
                    $output .= self::render_usergroup_tag($usergroup);
                    $output .= '</label>';
                    $output .= '<br>';
                }
			}

            $output .= '</td>';
    		$output .= '</tr>';
		}

        return $output;
    }

    // Renders the tag for a usergroup which can be used inside profiles, posts and in the administration area.
    public static function render_usergroup_tag($usergroup_object, $font_weight = 'normal') {
        $color = self::getUserGroupColor($usergroup_object->term_id);
        $icon = self::get_usergroup_icon($usergroup_object->term_id);

        // If the memberslist is enabled and we are inside the front-end, we will
        // generate a link to the memberslist filtered by the selected usergroup.
        if (self::$asgarosforum->memberslist->functionality_enabled() && !is_admin()) {
            $link = self::$asgarosforum->rewrite->get_link('members', false, array('filter_type' => 'group', 'filter_name' => $usergroup_object->term_id));

            $output = '<a href="'.$link.'" class="af-usergroup-tag usergroup-tag-'.$usergroup_object->term_id.'" style="color: '.$color.' !important; border-color: '.$color.' !important; font-weight: '.$font_weight.' !important;">';

            if ($icon) {
                $output .= '<i class="'.$icon.'"></i>';
            }

            $output .= $usergroup_object->name;
            $output .= '</a>';

            return $output;
        } else {
            $output = '<span class="af-usergroup-tag usergroup-tag-'.$usergroup_object->term_id.'" style="color: '.$color.' !important; border-color: '.$color.' !important; font-weight: '.$font_weight.' !important;">';

            if ($icon) {
                $output .= '<i class="'.$icon.'"></i>';
            }

            $output .= $usergroup_object->name;
            $output .= '</span>';

            return $output;
        }
    }

    public static function updateUserProfileFields($user_id) {
        $user_groups = isset($_POST[self::$taxonomyName]) ? array_map('intval', $_POST[self::$taxonomyName]) : array();

		self::insertUserGroupsOfUsers($user_id, $user_groups);
    }

    public function views($views) {
        $usergroups = self::getUserGroups();

        if ($usergroups) {
            $views['forum-user-group'] = '<b>'.__('Forum Usergroups:', 'asgaros-forum').'</b>&nbsp;';

            $loopCounter = 0;

            foreach ($usergroups as $term) {
                $loopCounter++;
                $cssClass = (!empty($_GET['forum-user-group']) && $_GET['forum-user-group'] == $term->term_id) ? 'class="current"' : '';
                $usersCounter = self::countUsersOfUserGroup($term->term_id);

                if ($loopCounter > 1) {
                    $views['forum-user-group'] .= '&nbsp;|&nbsp;';
                }

                $views['forum-user-group'] .= '<a '.$cssClass.' href="'.admin_url('users.php?forum-user-group='.$term->term_id).'">'.$term->name.'</a> ('.$usersCounter.')';
            }
        }

		return $views;
	}

    // Delete assigned terms when deleting a user.
    public function delete_term_relationships($user_id) {
        self::deleteUserGroupsOfUser($user_id);
	}

    public function user_query($Query = '') {
		global $pagenow, $wpdb;

        if (!self::$asgarosforum->prevent_query_modifications) {
    		if ($pagenow == 'users.php') {
                if (!empty($_GET['forum-user-group'])) {
        			$userGroupID = sanitize_key($_GET['forum-user-group']);
        			$term = self::getUserGroup($userGroupID);

                    if (!empty($term)) {
            			$user_ids = self::get_ids_of_users_in_usergroup($term->term_id);

                        if (!empty($user_ids)) {
                			$ids = implode(',', wp_parse_id_list($user_ids));
                			$Query->query_where .= " AND $wpdb->users.ID IN ($ids)";
                        } else {
                            $Query->query_where .= " AND $wpdb->users.ID IN (-1)";
                        }
                    }
        		}
            }
        }
	}

    public function bulk_actions_users($bulk_actions) {
        $userGroups = self::getUserGroups();

        if (!empty($userGroups)) {
            foreach ($userGroups as $usergroup) {
                $bulk_actions['forum_user_group_add_'.$usergroup->term_id] = __('Add to', 'asgaros-forum').' '.$usergroup->name;
            }

            foreach ($userGroups as $usergroup) {
                $bulk_actions['forum_user_group_remove_'.$usergroup->term_id] = __('Remove from', 'asgaros-forum').' '.$usergroup->name;
            }
        }

        return $bulk_actions;
    }

    public function handle_bulk_actions_users($redirect_to, $action, $user_ids) {
        // Cancel when the user_ids array is empty.
        if (empty($user_ids)) {
            return $redirect_to;
        }

        // Check for a triggered bulk action first.
        $bulkActionFound = false;
        $userGroups = self::getUserGroups();

        if (!empty($userGroups)) {
            foreach ($userGroups as $usergroup) {
                if ($action == 'forum_user_group_add_'.$usergroup->term_id) {
                    $bulkActionFound = array('add', $usergroup->term_id);
                    break;
                } else if ($action == 'forum_user_group_remove_'.$usergroup->term_id) {
                    $bulkActionFound = array('remove', $usergroup->term_id);
                    break;
                }
            }
        }

        // Cancel when no bulk action found.
        if (!$bulkActionFound) {
            return $redirect_to;
        }

        foreach ($user_ids as $user_id) {
            $groupsOfUser = self::getUserGroupsOfUser($user_id, 'ids');

            if ($bulkActionFound[0] === 'add') {
                if (!in_array($bulkActionFound[1], $groupsOfUser)) {
                    $groupsOfUser[] = $bulkActionFound[1];
                }
            } else if ($bulkActionFound[0] === 'remove') {
                $searchKey = array_search($bulkActionFound[1], $groupsOfUser);

                if ($searchKey !== false) {
                    unset($groupsOfUser[$searchKey]);
                }
            }

            self::insertUserGroupsOfUsers($user_id, $groupsOfUser);
        }

        $redirect_to = add_query_arg('forum_user_groups_assigned', 1, $redirect_to);
        return $redirect_to;
    }

    public function bulk_actions_admin_notices() {
        if (!empty($_REQUEST['forum_user_groups_assigned'])) {
            printf('<div class="updated"><p>'.esc_html__('Usergroups assignments updated.', 'asgaros-forum').'</p></div>');
        }
    }

    // Adds a new user automatically to specific usergroups.
    public function add_new_user_to_usergroups($user_id) {
        $usergroups = self::getUserGroups();
        $auto_add_list = array();

        // Check for usergroups first where new users should be added automatically.
        if (!empty($usergroups)) {
            foreach ($usergroups as $usergroup) {
                $auto_add = self::get_usergroup_auto_add($usergroup->term_id);

                if ($auto_add == 'yes') {
                    $auto_add_list[] = $usergroup->term_id;
                }
            }
        }

        // Now add the user to those usergroups.
        if (!empty($auto_add_list)) {
            self::insertUserGroupsOfUsers($user_id, $auto_add_list);
        }
    }
}
