<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumMembersList {
    private $asgarosforum = null;
    public $filter_type = 'role';
    public $filter_name = 'all';
    public $memberslist = array();

    public function __construct($object) {
        $this->asgarosforum = $object;

        // Set filter based on URL parameters.
        add_action('asgarosforum_prepare_members', array($this, 'load_members'));
        add_action('asgarosforum_breadcrumbs_members', array($this, 'add_breadcrumbs'));
    }

    public function functionality_enabled() {
        if (!$this->asgarosforum->options['enable_memberslist'] || ($this->asgarosforum->options['memberslist_loggedin_only'] && !is_user_logged_in())) {
            return false;
        } else {
            return true;
        }
    }

    public function add_breadcrumbs() {
        $element_link = $this->asgarosforum->get_link('members');
        $element_title = __('Members', 'asgaros-forum');
        $this->asgarosforum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function load_members() {
        if ($this->functionality_enabled()) {
            if (!empty($_GET['filter_type']) && !empty($_GET['filter_name'])) {
                $input_filter_type = sanitize_key($_GET['filter_type']);
                $input_filter_name = sanitize_key($_GET['filter_name']);

                if ($input_filter_type === 'role') {
                    switch ($input_filter_name) {
                        case 'all':
                        case 'normal':
                        case 'moderator':
                        case 'administrator':
                        case 'banned':
                            $this->filter_type = 'role';
                            $this->filter_name = 'all';

                            // Ensure that the filter is available.
                            if ($this->is_filter_available($input_filter_name)) {
                                $this->filter_name = $input_filter_name;
                            }
                        break;
                    }
                } else if ($input_filter_type === 'group') {
                    $this->filter_type = 'group';
                    $this->filter_name = $input_filter_name;
                }
            }

            $this->memberslist = $this->get_members();
        }
    }

    public function show_memberslist_link() {
        if ($this->functionality_enabled()) {
            $membersLink = $this->asgarosforum->get_link('members');
            $membersLink = apply_filters('asgarosforum_filter_members_link', $membersLink);
            $loginStatus = $this->asgarosforum->options['memberslist_loggedin_only'] ? 1 : 0;

            return array(
                'menu_class'        => 'members-link',
                'menu_link_text'    => esc_html__('Members', 'asgaros-forum'),
                'menu_url'          => $membersLink,
                'menu_login_status' => $loginStatus,
                'menu_new_tab'      => false
            );
        }
    }

    public function show_filters() {
        // Load usergroups.
        $usergroups = AsgarosForumUserGroups::getUserGroups(array(), true);

        // Dont show filters when there are no usergroups and no active filters.
        if (empty($usergroups) && !$this->is_filter_available('normal') && !$this->is_filter_available('moderator') && !$this->is_filter_available('administrator') && !$this->is_filter_available('banned')) {
            echo '<div class="title-element"></div>';
            return;
        }

        $filter_toggle_text = __('Show Filters', 'asgaros-forum');
        $filter_toggle_icon = 'fas fa-chevron-down';
        $filter_toggle_hide = 'none';

        if (!empty($_GET['filter_type']) && !empty($_GET['filter_name'])) {
            $filter_toggle_text = __('Hide Filters', 'asgaros-forum');
            $filter_toggle_icon = 'fas fa-chevron-up';
            $filter_toggle_hide = 'block';
        }

        echo '<div class="title-element" id="memberslist-filter-toggle">';
            echo '<span class="title-element-icon '.esc_attr($filter_toggle_icon).'"></span>';
            echo '<span class="title-element-text">'.esc_attr($filter_toggle_text).'</span>';
        echo '</div>';

        echo '<div id="memberslist-filter" data-value-show-filters="'.esc_attr__('Show Filters', 'asgaros-forum').'" data-value-hide-filters="'.esc_attr__('Hide Filters', 'asgaros-forum').'" style="display: '.esc_attr($filter_toggle_hide).';">';
            echo '<div id="roles-filter">';
                echo '<div class="filter-name">'.esc_html__('Roles:', 'asgaros-forum').'</div>';
                echo '<div class="filter-options">';
                    $this->render_filter_option('role', 'all', esc_html__('All Users', 'asgaros-forum'));

                    if ($this->is_filter_available('normal')) {
                        $users = $this->asgarosforum->permissions->get_users_by_role('normal');

                        if (count($users) > 0) {
                            echo '&nbsp;&middot;&nbsp;';
                            $this->render_filter_option('role', 'normal', esc_html__('Users', 'asgaros-forum'));
                        }
                    }

                    if ($this->is_filter_available('moderator')) {
                        $users = $this->asgarosforum->permissions->get_users_by_role('moderator');

                        if (count($users) > 0) {
                            echo '&nbsp;&middot;&nbsp;';
                            $this->render_filter_option('role', 'moderator', esc_html__('Moderators', 'asgaros-forum'));
                        }
                    }

                    if ($this->is_filter_available('administrator')) {
                        $users = $this->asgarosforum->permissions->get_users_by_role('administrator');
                        $users = $this->maybe_filter_siteadmins($users);

                        if (count($users) > 0) {
                            echo '&nbsp;&middot;&nbsp;';
                            $this->render_filter_option('role', 'administrator', esc_html__('Administrators', 'asgaros-forum'));
                        }
                    }

                    if ($this->is_filter_available('banned')) {
                        $users = $this->asgarosforum->permissions->get_users_by_role('banned');

                        if (count($users) > 0) {
                            echo '&nbsp;&middot;&nbsp;';
                            $this->render_filter_option('role', 'banned', esc_html__('Banned', 'asgaros-forum'));
                        }
                    }
                echo '</div>';
            echo '</div>';

            if (!empty($usergroups)) {
                $usergroups_filter_output = '';

                foreach ($usergroups as $usergroup) {
                    $users_counter = AsgarosForumUserGroups::countUsersOfUserGroup($usergroup->term_id);

                    // Only list usergroups with users in it.
                    if ($users_counter > 0) {
                        $font_weight = 'normal';

                        if ($this->filter_type == 'group' && $this->filter_name == $usergroup->term_id) {
                            $font_weight = 'bold';
                        }
                        $usergroups_filter_output .= AsgarosForumUserGroups::render_usergroup_tag($usergroup, $font_weight);
                    }
                }

                if (!empty($usergroups_filter_output)) {
                    echo '<div id="usergroups-filter">';
                        echo '<div class="filter-name">'.esc_html__('Usergroups:', 'asgaros-forum').'</div>';
                        echo '<div class="filter-options">'.$usergroups_filter_output.'</div>';
                    echo '</div>';
                }
            }

        echo '</div>';
    }

    public function render_filter_option($filter_type, $filter_name, $title) {
        if ($filter_type === $this->filter_type && $filter_name == $this->filter_name) {
            echo '<b>';
        }

		echo '<a href="'.esc_url($this->asgarosforum->rewrite->get_link('members', false, array('filter_type' => $filter_type, 'filter_name' => $filter_name))).'">'.esc_html($title).'</a>';

		if ($filter_type === $this->filter_type && $filter_name == $this->filter_name) {
            echo '</b>';
        }
    }

    public function show_memberslist() {
        $pagination_rendering = $this->asgarosforum->pagination->renderPagination('members');
        $paginationRendering = ($pagination_rendering) ? '<div class="pages-and-menu">'.$pagination_rendering.'<div class="clear"></div></div>' : '';
        echo $paginationRendering;

        $this->show_filters();

        echo '<div class="content-container">';

        $data = $this->memberslist;

        if (empty($data)) {
            $this->asgarosforum->render_notice(__('No users found!', 'asgaros-forum'));
        } else {
            $start = $this->asgarosforum->current_page * $this->asgarosforum->options['members_per_page'];
            $end = $this->asgarosforum->options['members_per_page'];

            $dataSliced = array_slice($data, $start, $end);

            foreach ($dataSliced as $element) {
                $userOnline = ($this->asgarosforum->online->is_user_online($element->ID)) ? 'user-online' : 'user-offline';

                echo '<div class="content-element member '.esc_attr($userOnline).'">';
                    if ($this->asgarosforum->options['enable_avatars']) {
                        echo '<div class="member-avatar">';
                        echo get_avatar($element->ID, 60, '', '', array('force_display' => true));
                        echo '</div>';
                    }

                    echo '<div class="member-name">';
                        echo $this->asgarosforum->getUsername($element->ID);
                        echo '<small>'.$this->asgarosforum->permissions->getForumRole($element->ID).'</small>';

                        $usergroups = AsgarosForumUserGroups::getUserGroupsOfUser($element->ID, 'all', true);

                        if (!empty($usergroups)) {
                            echo '<small>';

                            foreach ($usergroups as $usergroup) {
                                echo AsgarosForumUserGroups::render_usergroup_tag($usergroup);
                            }

                            echo '</small>';
                        }
                    echo '</div>';

                    echo '<div class="member-posts">';
                        $member_posts_i18n = number_format_i18n($element->forum_posts);
                        echo sprintf(_n('%s Post', '%s Posts', absint($element->forum_posts), 'asgaros-forum'), esc_html($member_posts_i18n));
                    echo '</div>';

                    if ($this->asgarosforum->online->functionality_enabled && $this->asgarosforum->options['show_last_seen']) {
                        echo '<div class="member-last-seen">';
                            echo '<i>'.esc_html($this->asgarosforum->online->last_seen($element->ID)).'</i>';
                        echo '</div>';
                    }
                echo '</div>';
            }
        }

        echo '</div>';

        echo $paginationRendering;
    }

    // Checks if a given filter is available.
    public function is_filter_available($filter) {
        // Filter for all users always available.
        if ($filter === 'all') {
            return true;
        }

        // Check if other filters are available.
        if (!empty($this->asgarosforum->options['memberslist_filter_'.$filter])) {
            return true;
        }

        // Otherwise the filter is not available.
        return false;
    }

    public function get_members() {
        $allUsers = false;

        if ($this->filter_type === 'role') {
            $allUsers = $this->asgarosforum->permissions->get_users_by_role($this->filter_name);
        } else if ($this->filter_type === 'group') {
            $allUsers = AsgarosForumUserGroups::get_users_in_usergroup($this->filter_name);
        }

        $allUsers = $this->maybe_filter_siteadmins($allUsers);

        if ($allUsers) {
            // Now get the amount of forum posts for all users.
            $postsCounter = $this->asgarosforum->db->get_results("SELECT author_id, COUNT(id) AS counter FROM {$this->asgarosforum->tables->posts} GROUP BY author_id ORDER BY COUNT(id) DESC;");

            // Change the structure of the results for better searchability.
            $postsCounterSearchable = array();

            foreach ($postsCounter as $postCounter) {
                $postsCounterSearchable[$postCounter->author_id] = $postCounter->counter;
            }

            // Now add the numbers of posts to the users array when they are listed in the post counter.
            foreach ($allUsers as $key => $user) {
                if (isset($postsCounterSearchable[$user->ID])) {
                    $allUsers[$key]->forum_posts = $postsCounterSearchable[$user->ID];
                } else {
                    $allUsers[$key]->forum_posts = 0;
                }
            }

            // Obtain a list of columns for array_multisort().
            $columnForumPosts = array();
            $columnDisplayName = array();

            foreach ($allUsers as $key => $user) {
                $columnForumPosts[$key] = $user->forum_posts;
                $columnDisplayName[$key] = $user->display_name;
            }

            // Ensure case insensitive sorting.
            $columnDisplayName = array_map('strtolower', $columnDisplayName);

            // Now sort the array based on the columns.
            array_multisort($columnForumPosts, SORT_NUMERIC, SORT_DESC, $columnDisplayName, SORT_STRING, SORT_ASC, $allUsers);
        }

        return $allUsers;
    }

    public function maybe_filter_siteadmins($users) {
        if ($this->asgarosforum->options['memberslist_filter_siteadmins'] && $users) {
            $siteAdmins = $this->asgarosforum->permissions->get_users_by_role('siteadmin');
            $users = array_diff_key($users, $siteAdmins);
        }

        return $users;
    }
}
