<?php

if (!defined('ABSPATH')) exit;

class AsgarosForum {
    public $version = '1.15.20';
    public $executePlugin = false;
    public $db = null;
    public $tables = null;
    public $plugin_url = '';
    public $plugin_path = '';
    public $date_format = '';
    public $time_format = '';
    public $error = false;
    public $current_description = false;
    public $current_category = false;
    public $current_forum = false;
    public $current_forum_name = false;
    public $current_topic = false;
    public $current_topic_name = false;
    public $current_post = false;
    public $current_view = false;
    public $current_element = false;
    public $current_page = 0;
    public $parent_forum = false;
    public $parent_forum_name = false;
    public $category_access_level = false;
    public $parents_set = false;

    // We need this flag if want to prevent modifications of core queries in certain cases.
    public $prevent_query_modifications = false;

    public $options = array();
    public $options_default = array(
        'forum_title'                       => '',
        'forum_description'                 => '',
        'location'                          => 0,
        'posts_per_page'                    => 10,
        'topics_per_page'                   => 20,
        'members_per_page'                  => 25,
        'allow_shortcodes'                  => false,
        'embed_content'                     => true,
        'allow_guest_postings'              => false,
        'allowed_filetypes'                 => 'jpg,jpeg,gif,png,bmp,pdf',
        'allow_file_uploads'                => false,
        'upload_permission'                 => 'loggedin',
        'hide_uploads_from_guests'          => false,
        'hide_profiles_from_guests'         => false,
        'uploads_maximum_number'            => 5,
        'uploads_maximum_size'              => 5,
        'uploads_show_thumbnails'           => true,
        'admin_subscriptions'               => false,
        'allow_subscriptions'               => true,
        'notification_sender_name'          => '',
        'notification_sender_mail'          => '',
        'receivers_admin_notifications'     => '',
        'mail_template_new_post_subject'    => '',
        'mail_template_new_post_message'    => '',
        'mail_template_new_topic_subject'   => '',
        'mail_template_new_topic_message'   => '',
        'mail_template_mentioned_subject'   => '',
        'mail_template_mentioned_message'   => '',
        'allow_signatures'                  => false,
        'signatures_permission'             => 'loggedin',
        'signatures_html_allowed'           => false,
        'signatures_html_tags'              => '<br><a><i><b><u><s><img><strong>',
        'enable_avatars'                    => true,
        'enable_mentioning'                 => true,
        'enable_mentioning_suggestions'     => true,
        'enable_reactions'                  => true,
        'reactions_show_names'              => true,
        'enable_search'                     => true,
        'enable_profiles'                   => true,
        'enable_memberslist'                => true,
        'memberslist_filter_normal'         => true,
        'memberslist_filter_moderator'      => true,
        'memberslist_filter_administrator'  => true,
        'memberslist_filter_banned'         => true,
        'enable_rss'                        => false,
        'load_fontawesome'                  => true,
        'load_fontawesome_compat_v4'        => true,
        'count_topic_views'                 => true,
        'reports_enabled'                   => true,
        'reports_notifications'             => true,
        'memberslist_loggedin_only'         => false,
        'memberslist_filter_siteadmins'     => false,
        'show_login_button'                 => true,
        'show_logout_button'                => true,
        'show_register_button'              => true,
        'show_who_is_online'                => true,
        'show_last_seen'                    => true,
        'show_newest_member'                => true,
        'show_statistics'                   => true,
        'enable_breadcrumbs'                => true,
        'breadcrumbs_show_category'         => true,
        'highlight_admin'                   => true,
        'highlight_authors'                 => true,
        'show_author_posts_counter'         => true,
        'show_edit_date'                    => true,
        'enable_edit_post'                  => true,
        'time_limit_edit_posts'             => 0,
        'enable_delete_post'                => false,
        'time_limit_delete_posts'           => 3,
        'enable_delete_topic'               => false,
        'time_limit_delete_topics'          => 3,
        'enable_open_topic'                 => false,
        'enable_close_topic'                => false,
        'show_description_in_forum'         => false,
        'require_login'                     => false,
        'require_login_posts'               => false,
        'create_blog_topics_id'             => 0,
        'approval_for'                      => 'guests',
        'enable_activity'                   => true,
        'activity_days'                     => 14,
        'activities_per_page'               => 50,
        'enable_polls'                      => true,
        'polls_permission'                  => 'loggedin',
        'polls_results_visible'             => false,
        'enable_seo_urls'                   => true,
        'seo_url_mode_content'              => 'slug',
        'seo_url_mode_profile'              => 'slug',
        'custom_url_login'                  => '',
        'custom_url_register'               => '',
        'view_name_activity'                => 'activity',
        'view_name_subscriptions'           => 'subscriptions',
        'view_name_search'                  => 'search',
        'view_name_forum'                   => 'forum',
        'view_name_topic'                   => 'topic',
        'view_name_addtopic'                => 'addtopic',
        'view_name_movetopic'               => 'movetopic',
        'view_name_addpost'                 => 'addpost',
        'view_name_editpost'                => 'editpost',
        'view_name_markallread'             => 'markallread',
        'view_name_members'                 => 'members',
        'view_name_profile'                 => 'profile',
        'view_name_history'                 => 'history',
        'view_name_unread'                  => 'unread',
        'view_name_unapproved'              => 'unapproved',
        'view_name_reports'                 => 'reports',
        'title_separator'                   => '-',
        'enable_spoilers'                   => true,
        'hide_spoilers_from_guests'         => false,
        'subforums_location'                => 'above',
        'enable_reputation'                 => true,
        'reputation_level_1_posts'          => 10,
        'reputation_level_2_posts'          => 25,
        'reputation_level_3_posts'          => 100,
        'reputation_level_4_posts'          => 250,
        'reputation_level_5_posts'          => 1000,
        'activity_timestamp_format'         => 'relative',
    );
    public $options_editor = array(
        'media_buttons' => false,
        'editor_height' => 250,
        'teeny'         => false,
        'quicktags'     => false
    );
    public $cache          = array();   // Used to store selected database queries.
    public $rewrite        = null;
    public $shortcode      = null;
    public $reports        = null;
    public $profile        = null;
    public $editor         = null;
    public $reactions      = null;
    public $mentioning     = null;
    public $notifications  = null;
    public $appearance     = null;
    public $uploads        = null;
    public $search         = null;
    public $online         = null;
    public $content        = null;
    public $breadcrumbs    = null;
    public $activity       = null;
    public $memberslist    = null;
    public $pagination     = null;
    public $unread         = null;
    public $feed           = null;
    public $permissions    = null;
    public $approval       = null;
    public $spoilers       = null;
    public $polls          = null;

    public function __construct() {
        // Initialize database.
        global $wpdb;
        $database = new AsgarosForumDatabase();
        $this->tables = $database->getTables();
        $this->db = $wpdb;

        $this->plugin_url = plugin_dir_url(dirname(__FILE__));
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->load_options();
        $this->date_format = get_option('date_format');
        $this->time_format = get_option('time_format');

        add_action('wp', array($this, 'prepare'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_css_js'), 10);

        // Add certain classes to body-tag.
        add_filter('body_class', array($this, 'add_body_classes'));

        // Add filters for modifying the title of the page.
        add_filter('wp_title', array($this, 'change_wp_title'), 100, 3);
        add_filter('document_title_parts', array($this, 'change_document_title_parts'), 100);
        add_filter('pre_get_document_title', array($this, 'change_pre_get_document_title'), 100);
        add_filter('document_title_separator', array($this, 'change_document_title_separator'), 100);

        add_filter('oembed_dataparse', array($this, 'prevent_oembed_dataparse'), 10, 3);

        // Deleting an user.
        add_action('delete_user_form', array($this, 'delete_user_form_reassign'), 10, 2);
        add_action('deleted_user', array($this, 'deleted_user_reassign'), 10, 2);

        // Hooks for automatic topic creation.
        add_action('transition_post_status', array($this, 'post_transition_update'), 10, 3);
        add_action('load-post.php', array($this, 'add_topic_meta_box_setup'));
        add_action('load-post-new.php', array($this, 'add_topic_meta_box_setup'));

        new AsgarosForumCompatibility($this);
        new AsgarosForumStatistics($this);
        new AsgarosForumUserGroups($this);
        new AsgarosForumWidgets($this);

        $this->rewrite          = new AsgarosForumRewrite($this);
        $this->shortcode        = new AsgarosForumShortcodes($this);
        $this->reports          = new AsgarosForumReports($this);
        $this->profile          = new AsgarosForumProfile($this);
        $this->editor           = new AsgarosForumEditor($this);
        $this->reactions        = new AsgarosForumReactions($this);
        $this->mentioning       = new AsgarosForumMentioning($this);
        $this->notifications    = new AsgarosForumNotifications($this);
        $this->appearance       = new AsgarosForumAppearance($this);
        $this->uploads          = new AsgarosForumUploads($this);
        $this->search           = new AsgarosForumSearch($this);
        $this->online           = new AsgarosForumOnline($this);
        $this->content          = new AsgarosForumContent($this);
        $this->breadcrumbs      = new AsgarosForumBreadCrumbs($this);
        $this->activity         = new AsgarosForumActivity($this);
        $this->memberslist      = new AsgarosForumMembersList($this);
        $this->pagination       = new AsgarosForumPagination($this);
        $this->unread           = new AsgarosForumUnread($this);
        $this->feed             = new AsgarosForumFeed($this);
        $this->permissions      = new AsgarosForumPermissions($this);
        $this->approval         = new AsgarosForumApproval($this);
        $this->spoilers         = new AsgarosForumSpoilers($this);
        $this->polls            = new AsgarosForumPolls($this);
    }

    //======================================================================
    // FUNCTIONS FOR GETTING AND SETTING OPTIONS.
    //======================================================================

    public function load_options() {
        // Get options and merge them with the default ones.
		$current_options = get_option('asgarosforum_options', array());
        $this->options = array_merge($this->options_default, $current_options);

        // Ensure default values if some needed inputs got deleted.
        if (empty($this->options['forum_title'])) {
            $this->options['forum_title'] = __('Forum', 'asgaros-forum');
        }

        if (empty($this->options['notification_sender_name'])) {
            $this->options['notification_sender_name'] = get_bloginfo('name');
        }

        if (empty($this->options['notification_sender_mail'])) {
            $this->options['notification_sender_mail'] = get_bloginfo('admin_email');
        }

        if (empty($this->options['receivers_admin_notifications'])) {
            $this->options['receivers_admin_notifications'] = get_bloginfo('admin_email');
        }

        if (empty($this->options['mail_template_new_post_subject'])) {
            $this->options['mail_template_new_post_subject'] = __('New reply: ###TITLE###', 'asgaros-forum');
        }

        if (empty($this->options['mail_template_new_post_message'])) {
            $this->options['mail_template_new_post_message'] = __('Hello ###USERNAME###,<br><br>You received this message because there is a new reply in a forum-topic you have subscribed to.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Reply:<br>###CONTENT###<br><br>Link:<br>###LINK###<br><br>If you dont want to receive these mails anymore you can unsubscribe via the subscription-area. Please dont reply to this mail!', 'asgaros-forum');
        }

        if (empty($this->options['mail_template_new_topic_subject'])) {
            $this->options['mail_template_new_topic_subject'] = __('New topic: ###TITLE###', 'asgaros-forum');
        }

        if (empty($this->options['mail_template_new_topic_message'])) {
            $this->options['mail_template_new_topic_message'] = __('Hello ###USERNAME###,<br><br>You received this message because there is a new forum-topic.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Text:<br>###CONTENT###<br><br>Link:<br>###LINK###<br><br>If you dont want to receive these mails anymore you can unsubscribe via the subscription-area. Please dont reply to this mail!', 'asgaros-forum');
        }

        if (empty($this->options['mail_template_mentioned_subject'])) {
            $this->options['mail_template_mentioned_subject'] = __('You have been mentioned!', 'asgaros-forum');
        }

        if (empty($this->options['mail_template_mentioned_message'])) {
            $this->options['mail_template_mentioned_message'] = __('Hello ###USERNAME###,<br><br>You have been mentioned in a forum-post.<br><br>Topic:<br>###TITLE###<br><br>Author:<br>###AUTHOR###<br><br>Text:<br>###CONTENT###<br><br>Link:<br>###LINK###', 'asgaros-forum');
        }

        if (empty($this->options['title_separator'])) {
            $this->options['title_separator'] = '-';
        }
    }

    public function save_options($options) {
        update_option('asgarosforum_options', $options);

        // Reload options after saving them.
        $this->load_options();
    }

    //======================================================================
    // FUNCTIONS FOR PAGE TITLE.
    //======================================================================

    public function change_wp_title($title, $sep, $seplocation) {
        return $this->get_title($title);
    }

    public function change_document_title_parts($title) {
        $title['title'] = $this->get_title($title['title']);
        return $title;
    }

    public function change_pre_get_document_title($title) {
        // Only modify it when a title is already set.
        if (!empty($title)) {
            $title = $this->get_title($title);
        }

        return $title;
    }

    public function change_document_title_separator($document_title_separator) {
        if ($this->executePlugin) {
            $document_title_separator = $this->get_title_separator();
        }

        return $document_title_separator;
    }

    public function get_title_separator() {
        // The default title-separator is based on the forum-settings.
        $title_separator = $this->options['title_separator'];

        // Allow other plugins to modify the title-separator.
        $title_separator = apply_filters('asgarosforum_title_separator', $title_separator);

        return $title_separator;
    }

    public function get_title($title) {
        if ($this->executePlugin) {
            $metaTitle = $this->getMetaTitle();

            if ($metaTitle) {
                $title_separator = $this->get_title_separator();
                $title = $metaTitle.' '.$title_separator.' '.$title;
            }
        }

        return $title;
    }

    // Gets the pages meta title.
    public function getMetaTitle() {
        // Get the main title by default with disabled default title generation.
        $metaTitle = $this->getMainTitle(false);

        // Apply custom modifications.
        if (!$this->error && $this->current_view) {
            if ($this->current_view === 'forum' && $this->current_forum) {
                $metaTitle = $this->add_current_page_to_title($metaTitle);
            } else if ($this->current_view === 'topic' && $this->current_topic) {
                $metaTitle = $this->add_current_page_to_title($metaTitle);
            }
        }

        return $metaTitle;
    }

    // Adds the current page to a title.
    public function add_current_page_to_title($someString) {
        if ($this->current_page > 0) {
            $currentPage = $this->current_page + 1;
            $title_separator = $this->get_title_separator();
            $someString .= ' '.$title_separator.' '.__('Page', 'asgaros-forum').' '.$currentPage;
        }

        return $someString;
    }

    public function prepare() {
        global $post;

        if (is_a($post, 'WP_Post') && $this->shortcode->checkForShortcode($post)) {
            $this->executePlugin = true;
            $this->options['location'] = $post->ID;
        }

        do_action('asgarosforum_execution_check');

        // Set all base links.
        if ($this->executePlugin || get_post($this->options['location'])) {
            $this->rewrite->set_links();
        }

        if (!$this->executePlugin) {
            return;
        }

        // Parse the URL.
        $this->rewrite->parse_url();

        // Update online status.
        $this->online->update_online_status();

        do_action('asgarosforum_prepare');

        switch ($this->current_view) {
            case 'forum':
            case 'addtopic':
                $this->setParents($this->current_element, 'forum');
            break;
            case 'movetopic':
            case 'topic':
            case 'addpost':
                $this->setParents($this->current_element, 'topic');
            break;
            case 'editpost':
                $this->setParents($this->current_element, 'post');
            break;
            case 'markallread':
            case 'unread':
            break;
            case 'subscriptions':
                // Go back to the overview when this functionality is not enabled or the user is not logged-in.
                if (!$this->options['allow_subscriptions'] || !is_user_logged_in()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'search':
                // Go back to the overview when this functionality is not enabled.
                if (!$this->options['enable_search']) {
                    $this->current_view = 'overview';
                }
            break;
            case 'profile':
            case 'history':
                if (!$this->profile->functionalityEnabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'members':
                // Go back to the overview when this functionality is not enabled.
                if (!$this->memberslist->functionality_enabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'activity':
                if (!$this->activity->functionality_enabled()) {
                    $this->current_view = 'overview';
                }
            break;
            case 'unapproved':
                // Ensure that the user is at least a moderator.
                if (!$this->permissions->isModerator('current')) {
                    $this->current_view = 'overview';
                }
            break;
            case 'reports':
                // Ensure that the user is at least a moderator.
                if (!$this->permissions->isModerator('current')) {
                    $this->current_view = 'overview';
                }
            break;
            default:
                $this->current_view = 'overview';
            break;
        }

        $this->shortcode->handleAttributes();

        // Check access.
        $this->check_access();

        // Override editor settings.
        $this->options_editor = apply_filters('asgarosforum_filter_editor_settings', $this->options_editor);

        // Prevent generation of some head-elements.
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');

        if (isset($_POST['submit_action'])) {
            $this->content->do_insertion();
        } else if (isset($_GET['move_topic'])) {
            $this->moveTopic();
        } else if (isset($_GET['delete_topic']) && wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'asgaros_forum_delete_topic')) {
			if (!empty($_REQUEST['_wpnonce'])) {
				if (wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'asgaros_forum_delete_topic')) {
					$this->delete_topic($this->current_topic);
				}
			}
        } else if (isset($_GET['remove_post']) && wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'asgaros_forum_delete_post')) {
			if (!empty($_REQUEST['_wpnonce'])) {
				if (wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'asgaros_forum_delete_post')) {
					$post_id = (!empty($_GET['post'])) ? absint($_GET['post']) : 0;
            		$this->remove_post($post_id);
				}
			}
        } else if (!empty($_POST['sticky_topic']) || isset($_GET['sticky_topic'])) {
            $sticky_mode = 1;

            if (!empty($_POST['sticky_topic'])) {
                $sticky_mode = (int) $_POST['sticky_topic'];
            }

            $this->set_sticky($this->current_topic, $sticky_mode);
        } else if (isset($_GET['unsticky_topic'])) {
            $this->set_sticky($this->current_topic, 0);
        } else if (isset($_GET['open_topic'])) {
            $this->change_status('open');
        } else if (isset($_GET['close_topic'])) {
            $this->change_status('closed');
        } else if (isset($_GET['approve_topic'])) {
            $this->approval->approve_topic($this->current_topic);
        } else if (isset($_GET['subscribe_topic'])) {
            $topic_id = (!empty($_GET['subscribe_topic'])) ? absint($_GET['subscribe_topic']) : 0;
            $this->notifications->subscribe_topic($topic_id);
        } else if (isset($_GET['unsubscribe_topic'])) {
            $topic_id = (!empty($_GET['unsubscribe_topic'])) ? absint($_GET['unsubscribe_topic']) : 0;
            $this->notifications->unsubscribe_topic($topic_id);
        } else if (isset($_GET['subscribe_forum'])) {
            $forum_id = (!empty($_GET['subscribe_forum'])) ? absint($_GET['subscribe_forum']) : 0;
            $this->notifications->subscribe_forum($forum_id);
        } else if (isset($_GET['unsubscribe_forum'])) {
            $forum_id = (!empty($_GET['unsubscribe_forum'])) ? absint($_GET['unsubscribe_forum']) : 0;
            $this->notifications->unsubscribe_forum($forum_id);
        } else if (isset($_GET['report_add'])) {
            $post_id = (!empty($_GET['post'])) ? absint($_GET['post']) : 0;
            $reporter_id = get_current_user_id();

            $this->reports->add_report($post_id, $reporter_id);
        } else if (!empty($_GET['report_delete']) && is_numeric($_GET['report_delete'])) {
            $this->reports->remove_report(sanitize_key($_GET['report_delete']));
        }

        do_action('asgarosforum_prepare_'.$this->current_view);
    }

    public function check_access() {
        // Check login access.
        if ($this->options['require_login'] && !is_user_logged_in()) {
            $this->error = __('Sorry, only logged-in users can access the forum.', 'asgaros-forum');
            $this->error = apply_filters('asgarosforum_filter_error_message_require_login', $this->error);
            return;
        }

        // Check category access.
        $this->category_access_level = get_term_meta($this->current_category, 'category_access', true);

        if ($this->category_access_level) {
            if ($this->category_access_level === 'loggedin' && !is_user_logged_in()) {
                $this->error = __('Sorry, only logged-in users can access this category.', 'asgaros-forum');
                return;
            }

            if ($this->category_access_level === 'moderator' && !$this->permissions->isModerator('current')) {
                $this->error = __('Sorry, you cannot access this area.', 'asgaros-forum');
                return;
            }
        }

        // Check usergroups access.
        if (!AsgarosForumUserGroups::checkAccess($this->current_category)) {
            $this->error = __('Sorry, you cannot access this area.', 'asgaros-forum');
            return;
        }

        if ($this->current_view === 'topic' || $this->current_view === 'addpost') {
            // Check topic-access.
            if ($this->options['require_login_posts'] && !is_user_logged_in()) {
                $this->error = __('Sorry, only logged-in users can access this topic.', 'asgaros-forum');
                return;
            }

            // Check unapproved-topic access.
            if (!$this->approval->is_topic_approved($this->current_topic) && !$this->permissions->isModerator('current')) {
                $this->rewrite->redirect('overview');
            }
        }

        // Check custom access.
        $custom_access = apply_filters('asgarosforum_filter_check_access', true, $this->current_category);

        if (!$custom_access) {
            $this->error = __('Sorry, you cannot access this area.', 'asgaros-forum');
            return;
        }

        // Add a login-notice if necessary.
        if (!is_user_logged_in() && !$this->options['allow_guest_postings']) {
            $show_login = $this->showLoginLink();
            $show_register = $this->showRegisterLink();

            $notice = '';

            if ($show_login) {
                $login_link = '<u><a class="'.$show_login['menu_class'].'" href="'.$show_login['menu_url'].'">'.$show_login['menu_link_text'].'</a></u>';

                if ($show_register) {
                    $register_link = '<u><a class="'.$show_register['menu_class'].'" href="'.$show_register['menu_url'].'">'.$show_register['menu_link_text'].'</a></u>';
                    $notice = sprintf(esc_html__('Please %s or %s to create posts and topics.', 'asgaros-forum'), $login_link, $register_link);
                } else {
                    $notice = sprintf(esc_html__('Please %s to create posts and topics.', 'asgaros-forum'), $login_link);
                }
            } else {
                $notice = __('You need to log in to create posts and topics.', 'asgaros-forum');
            }

            $notice = apply_filters('asgarosforum_filter_login_message', $notice);

            $this->add_notice($notice);
        }
    }

    public function enqueue_css_js() {
        if ($this->options['load_fontawesome']) {
			wp_enqueue_style('af-fontawesome', $this->plugin_url.'libs/fontawesome/css/all.min.css', array(), $this->version);
		}

		if ($this->options['load_fontawesome_compat_v4']) {
			wp_enqueue_style('af-fontawesome-compat-v4', $this->plugin_url.'libs/fontawesome/css/v4-shims.min.css', array(), $this->version);
		}

        $themeurl = $this->appearance->get_current_theme_url();

		wp_enqueue_style('af-widgets', $themeurl.'/widgets.css', array(), $this->version);

        if (!$this->executePlugin) {
            return;
        }

        wp_enqueue_style('af-style', $themeurl.'/style.css', array(), $this->version);

        if (is_rtl()) {
            wp_enqueue_style('af-rtl', $themeurl.'/rtl.css', array(), $this->version);
        }

        wp_enqueue_script('asgarosforum-js', $this->plugin_url.'js/script.js', array('jquery', 'wp-api'), $this->version, false);
        wp_localize_script('wp-api', 'wpApiSettings', array('root' => esc_url_raw(rest_url()), 'nonce' => wp_create_nonce('wp_rest')));

        if ($this->options['enable_spoilers']) {
            wp_enqueue_script('asgarosforum-js-spoilers', $this->plugin_url.'js/script-spoilers.js', array('jquery'), $this->version, false);
        }

        do_action('asgarosforum_enqueue_css_js');
    }

    // Add certain classes to body-tag.
    public function add_body_classes($classes) {
        if ($this->executePlugin) {
            $classes[] = 'asgaros-forum';
            $classes[] = 'asgaros-forum-'.$this->current_view;
        }

        return $classes;
    }

    // Gets the pages main title.
    public function getMainTitle($setDefaultTitle = true) {
        $mainTitle = false;

        if ($setDefaultTitle) {
            $mainTitle = $this->options['forum_title'];
        }

        if (!$this->error && $this->current_view) {
            if ($this->current_view === 'forum' && $this->current_forum) {
                $mainTitle = esc_html(stripslashes($this->current_forum_name));
            } else if ($this->current_view === 'topic' && $this->current_topic) {
                $mainTitle = esc_html(stripslashes($this->current_topic_name));
            } else if ($this->current_view === 'editpost') {
                $mainTitle = __('Edit Post', 'asgaros-forum');
            } else if ($this->current_view === 'addpost') {
                $mainTitle = __('Post Reply', 'asgaros-forum').': '.esc_html(stripslashes($this->current_topic_name));
            } else if ($this->current_view === 'addtopic') {
                $mainTitle = __('New Topic', 'asgaros-forum');
            } else if ($this->current_view === 'movetopic') {
                $mainTitle = __('Move Topic', 'asgaros-forum');
            } else if ($this->current_view === 'search') {
                $mainTitle = __('Search', 'asgaros-forum');
            } else if ($this->current_view === 'subscriptions') {
                $mainTitle = __('Subscriptions', 'asgaros-forum');
            } else if ($this->current_view === 'profile') {
                $mainTitle = $this->profile->get_profile_title();
            } else if ($this->current_view === 'history') {
                $mainTitle = $this->profile->get_history_title();
            } else if ($this->current_view === 'members') {
                $mainTitle = __('Members', 'asgaros-forum');
            } else if ($this->current_view === 'activity') {
                $mainTitle = __('Activity', 'asgaros-forum');
            } else if ($this->current_view === 'unread') {
                $mainTitle = __('Unread Topics', 'asgaros-forum');
            } else if ($this->current_view === 'unapproved') {
                $mainTitle = __('Unapproved Topics', 'asgaros-forum');
            } else if ($this->current_view === 'reports') {
                $mainTitle = __('Reports', 'asgaros-forum');
            }
        }

        return $mainTitle;
    }

    // Holds all notices.
    private $notices = array();

    // Adds a new notice to the notices array.
    public function add_notice($notice_message, $notice_link = false, $notice_icon = false) {
        $this->notices[] = array(
            'message'   => $notice_message,
            'link'      => $notice_link,
            'icon'      => $notice_icon
        );
    }

    // Renders a single given notice.
    public function render_notice($notice_message, $notice_link = false, $notice_icon = false, $in_panel = false) {
        if ($in_panel) { echo '<div class="notices-panel">'; }

        echo '<div class="notice">';

        if ($notice_icon) {
            echo '<span class="notice-icon '.esc_attr($notice_icon).'"></span>';
        }

        if ($notice_link) { echo '<a href="'.esc_url($notice_link).'">'; }

        echo wp_kses_post($notice_message);

        if ($notice_link) { echo '</a>'; }

        echo '</div>';

        if ($in_panel) { echo '</div>'; }
    }

    // Renders all existing notices.
    public function render_notices() {
        if (!empty($this->notices)) {
            echo '<div class="notices-panel">';

            foreach ($this->notices as $notice) {
                $this->render_notice($notice['message'], $notice['link'], $notice['icon']);
            }

            echo '</div>';
        }
    }

    public function forum() {
        ob_start();
        echo '<div id="af-wrapper">';

        do_action('asgarosforum_content_top');
        do_action('asgarosforum_'.$this->current_view.'_custom_content_top');

        // Show Header Area except for single posts.
        if ($this->current_view !== 'post' && apply_filters('asgarosforum_filter_show_header', true)) {
            $this->showHeader();

            do_action('asgarosforum_content_header');
        }

        $this->error = apply_filters('asgarosforum_filter_error', $this->error);

        if ($this->has_error()) {
            $this->render_error();
        } else {
            if ($this->current_view === 'post') {
                $this->showSinglePost();
            } else {
                $this->render_notices();
                $this->showMainTitleAndDescription();

                switch ($this->current_view) {
                    case 'search':
                        $this->search->show_search_results();
                    break;
                    case 'subscriptions':
                        $this->notifications->show_subscription_overview();
                    break;
                    case 'movetopic':
                        $this->showMoveTopic();
                    break;
                    case 'forum':
                        $this->show_forum();
                    break;
                    case 'topic':
                        $this->showTopic();
                    break;
                    case 'addtopic':
                    case 'addpost':
                    case 'editpost':
                        $this->editor->showEditor($this->current_view);
                    break;
                    case 'profile':
                        $this->profile->showProfile();
                    break;
                    case 'history':
                        $this->profile->show_history();
                    break;
                    case 'members':
                        $this->memberslist->show_memberslist();
                    break;
                    case 'activity':
                        $this->activity->show_activity();
                    break;
                    case 'unread':
                        $this->unread->show_unread_topics();
                    break;
                    case 'unapproved':
                        $this->approval->show_unapproved_topics();
                    break;
                    case 'reports':
                        $this->reports->show_reports();
                    break;
                    default:
                        $this->overview();
                    break;
                }

                // Action hook for optional bottom navigation elements.
                echo '<div id="bottom-navigation">';
                do_action('asgarosforum_bottom_navigation', $this->current_view);
                echo '</div>';
            }
        }

        do_action('asgarosforum_content_bottom');
        do_action('asgarosforum_'.$this->current_view.'_custom_content_bottom');

        echo '<div class="clear"></div>';
        echo '</div>';
        return ob_get_clean();
    }

    public function showMainTitleAndDescription() {
        $mainTitle = $this->getMainTitle();

        echo '<h1 class="main-title main-title-'.esc_attr($this->current_view).'">';

        // Show lock symbol for closed topics.
        if ($this->current_view == 'topic' && $this->is_topic_closed($this->current_topic)) {
            echo '<span class="main-title-icon fas fa-lock"></span>';
        }

        echo esc_html($mainTitle);
        echo '</h1>';

        if ($this->current_view === 'forum' && $this->options['show_description_in_forum'] && !empty($this->current_description)) {
            $forum_object = $this->content->get_forum($this->current_forum);

            echo '<div class="main-description">'.esc_html(stripslashes($forum_object->description)).'</div>';
        }
    }

    public function overview() {
        $categories = $this->content->get_categories();

        require 'views/overview.php';
    }

    public function showSinglePost() {
        $counter = 0;
        $topicStarter = $this->get_topic_starter($this->current_topic);
        $post = $this->content->get_post($this->current_post);

        echo '<div class="title-element"></div>';
        require 'views/post-element.php';
    }

    public function show_forum() {
        require 'views/forum.php';
    }

    public function render_forum_element($forum) {
        // Get counters and format them.
        $count_topics = $this->get_forum_topic_counter($forum->id);
        $count_topics_i18n = number_format_i18n($count_topics);
        $count_posts = $this->get_forum_post_counter($forum->id);
        $count_posts_i18n = number_format_i18n($count_posts);

        // Get the read/unread status of a forum.
        $unread_status = $this->unread->get_status_forum($forum->id, $count_topics);

        echo '<div class="content-element forum" id="forum-'.esc_attr($forum->id).'">';
            $forum_icon = trim(esc_html(stripslashes($forum->icon)));
            $forum_icon = (empty($forum_icon)) ? 'fas fa-comments' : $forum_icon;

            echo '<div class="forum-status '.esc_attr($unread_status).'"><i class="'.esc_attr($forum_icon).'"></i></div>';
            echo '<div class="forum-name">';
                echo '<a class="forum-title" href="'.esc_url($this->get_link('forum', absint($forum->id))).'">'.esc_html(stripslashes($forum->name)).'</a>';

                // Show the description of the forum when it is not empty.
                $forum_description = stripslashes($forum->description);
                if (!empty($forum_description)) {
                    echo '<small class="forum-description">'.esc_html($forum_description).'</small>';
                }

                // Show forum stats.
                echo '<small class="forum-stats">';
                    echo sprintf(_n('%s Topic', '%s Topics', absint($count_topics), 'asgaros-forum'), esc_html($count_topics_i18n));
                    echo '&nbsp;&middot;&nbsp;';
                    echo sprintf(_n('%s Post', '%s Posts', absint($count_posts), 'asgaros-forum'), esc_html($count_posts_i18n));
                echo '</small>';

                echo '<small class="forum-lastpost-small">';
                    $this->render_lastpost_in_forum($forum->id, true);
                echo '</small>';

                // Show subforums.
                if ($forum->count_subforums > 0) {
                    echo '<small class="forum-subforums">';
                    echo '<b>'.esc_html__('Subforums', 'asgaros-forum').':</b>&nbsp;';

                    $subforums = $this->get_forums($forum->parent_id, $forum->id);
                    $subforumsFirstDone = false;

                    foreach ($subforums as $subforum) {
                        echo ($subforumsFirstDone) ? '&nbsp;&middot;&nbsp;' : '';
                        echo '<a href="'.esc_url($this->get_link('forum', absint($subforum->id))).'">'.esc_html(stripslashes($subforum->name)).'</a>';
                        $subforumsFirstDone = true;
                    }

                    echo '</small>';
                }
            echo '</div>';
            do_action('asgarosforum_custom_forum_column', $forum->id);
            echo '<div class="forum-poster">';
				$this->render_lastpost_in_forum($forum->id);
			echo '</div>';
        echo '</div>';

        do_action('asgarosforum_after_forum');
    }

    public function render_topic_element($topic_object, $topic_type = 'topic-normal', $show_topic_location = false) {
        $lastpost_data = $this->get_lastpost_in_topic($topic_object->id);
        $unread_status = $this->unread->get_status_topic($topic_object->id);
        $topic_title = esc_html(stripslashes($topic_object->name));

        echo '<div class="content-element topic '.esc_attr($topic_type).'">';
            echo '<div class="topic-status '.esc_attr($unread_status).'"><i class="far fa-comments"></i></div>';
            echo '<div class="topic-name">';
                if ($this->is_topic_sticky($topic_object->id)) {
                    echo '<span class="topic-icon fas fa-thumbtack" title="'.esc_attr__('This topic is sticked', 'asgaros-forum').'"></span>';
                }

                if ($this->is_topic_closed($topic_object->id)) {
                    echo '<span class="topic-icon fas fa-lock" title="'.esc_attr__('This topic is closed', 'asgaros-forum').'"></span>';
                }

                if ($this->polls->has_poll($topic_object->id)) {
                    echo '<span class="topic-icon fas fa-poll-h" title="'.esc_attr__('This topic contains a poll', 'asgaros-forum').'"></span>';
                }

                echo '<a href="'.esc_url($this->get_link('topic', absint($topic_object->id))).'" title="'.esc_attr($topic_title).'">';
                echo esc_html($topic_title);
                echo '</a>';

                echo '<small>';
                echo esc_html__('By', 'asgaros-forum').'&nbsp;'.$this->getUsername($topic_object->author_id);

                // Show the name of the forum in which a topic is located in. This is currently only used for search results.
                if ($show_topic_location) {
                    echo '&nbsp;&middot;&nbsp;';
                    echo esc_html__('In', 'asgaros-forum').'&nbsp;';
                    echo '<a href="'.esc_url($this->rewrite->get_link('forum', absint($topic_object->forum_id))).'">';
                    echo esc_html(stripslashes($topic_object->forum_name));
                    echo '</a>';
                }

                $this->pagination->renderTopicOverviewPagination($topic_object->id, ($topic_object->answers + 1));

                echo '</small>';

                // Show topic stats.
                echo '<small class="topic-stats">';
                    $count_answers_i18n = number_format_i18n($topic_object->answers);
                    echo sprintf(_n('%s Reply', '%s Replies', absint($topic_object->answers), 'asgaros-forum'), esc_html($count_answers_i18n));

                    if ($this->options['count_topic_views']) {
                        $count_views_i18n = number_format_i18n($topic_object->views);
                        echo '&nbsp;&middot;&nbsp;';
                        echo sprintf(_n('%s View', '%s Views', absint($topic_object->views), 'asgaros-forum'), esc_html($count_views_i18n));
                    }
                echo '</small>';

                // Show lastpost info.
                echo '<small class="topic-lastpost-small">';
                    $this->render_lastpost_in_topic($topic_object->id, true);
                echo '</small>';
            echo '</div>';
            do_action('asgarosforum_custom_topic_column', $topic_object->id);
            echo '<div class="topic-poster">';
				$this->render_lastpost_in_topic($topic_object->id);
			echo '</div>';
        echo '</div>';

        do_action('asgarosforum_after_topic');
    }

    // Renders all subforums inside of a category/forum combination.
    public function render_subforums($category_id, $forum_id) {
        $subforums = $this->get_forums($category_id, $forum_id);

        if (!empty($subforums)) {
            echo '<div class="title-element">';
                echo esc_html__('Subforums', 'asgaros-forum');
                echo '<span class="last-post-headline">'.esc_html__('Last post', 'asgaros-forum').'</span>';
            echo '</div>';

            echo '<div class="content-container">';
                foreach ($subforums as $forum) {
                    $this->render_forum_element($forum);
                }
            echo '</div>';
        }
    }

    public function showTopic() {
        // Create a unique slug for this topic if necessary.
        $topic = $this->content->get_topic($this->current_topic);

        if (empty($topic->slug)) {
            $slug = $this->rewrite->create_unique_slug($topic->name, $this->tables->topics, 'topic');
            $this->db->update($this->tables->topics, array('slug' => $slug), array('id' => $topic->id), array('%s'), array('%d'));
        }

        // Get posts of topic.
        $posts = $this->get_posts();

        if ($posts) {
            $this->incrementTopicViews();
            $this->polls->render_poll($this->current_topic);
            $this->render_sticky_panel();

            echo '<div class="pages-and-menu">';
                $paginationRendering = $this->pagination->renderPagination($this->tables->posts, $this->current_topic);
                echo $paginationRendering;
                echo $this->show_topic_menu();
                echo '<div class="clear"></div>';
            echo '</div>';

            echo '<div class="title-element"></div>';

            $counter = 0;
            $topicStarter = $this->get_topic_starter($this->current_topic);
            foreach ($posts as $post) {
                require 'views/post-element.php';
            }
            $this->editor->showEditor('addpost', true);

            echo '<div class="pages-and-menu">';
                echo $paginationRendering;
                echo $this->show_topic_menu(false);
                echo '<div class="clear"></div>';
            echo '</div>';
        } else {
            echo '<div class="pages-and-menu">';
                // Render delete-button for empty topics.
				$current_user_id = get_current_user_id();

				if ($this->permissions->can_delete_topic($current_user_id, $this->current_topic)) {
					$delete_topic_link = $this->get_link('topic', $this->current_topic, array('delete_topic' => 1, '_wpnonce' => wp_create_nonce('asgaros_forum_delete_topic')));

					echo '<div class="forum-menu">';
					echo '<a class="button button-red" href="'.esc_url($delete_topic_link).'" onclick="return confirm(\''.esc_attr__('Are you sure you want to remove this?', 'asgaros-forum').'\');">';
						echo '<span class="menu-icon fas fa-trash-alt"></span>';
						echo esc_html__('Delete', 'asgaros-forum');
					echo '</a>';
					echo '</div>';
				}

                echo '<div class="clear"></div>';
            echo '</div>';

            // Show notice that this topic has no posts.
            $this->render_notice(__('Sorry, but there are no posts.', 'asgaros-forum'));
        }
    }

    public function incrementTopicViews() {
        if ($this->options['count_topic_views']) {
            $this->db->query($this->db->prepare("UPDATE {$this->tables->topics} SET views = views + 1 WHERE id = %d", $this->current_topic));
        }
    }

    public function showMoveTopic() {
        if ($this->permissions->isModerator('current')) {
            echo '<form method="post" action="'.esc_url($this->get_link('movetopic', absint($this->current_topic), array('move_topic' => 1))).'">';
            echo '<div class="title-element">'.sprintf(__('Move "<strong>%s</strong>" to new forum:', 'asgaros-forum'), esc_html(stripslashes($this->current_topic_name))).'</div>';
            echo '<div class="content-container">';
            echo '<br><select name="newForumID">';

            $categories = $this->content->get_categories();

            if ($categories) {
                foreach ($categories as $category) {
                    $forums = $this->get_forums($category->term_id, 0);

                    if ($forums) {
                        foreach ($forums as $forum) {
                            echo '<option value="'.esc_attr($forum->id).'"'.($forum->id == $this->current_forum ? ' selected="selected"' : '').'>'.esc_html($forum->name).'</option>';

                            if ($forum->count_subforums > 0) {
                                $subforums = $this->get_forums($category->term_id, $forum->id);

                                foreach ($subforums as $subforum) {
                                    echo '<option value="'.esc_attr($subforum->id).'"'.($subforum->id == $this->current_forum ? ' selected="selected"' : '').'>&mdash; '.esc_html($subforum->name).'</option>';
                                }
                            }
                        }
                    }
                }
            }

            echo '</select><br><input class="button button-normal" type="submit" value="'.esc_attr__('Move', 'asgaros-forum').'"><br><br></div></form>';
        } else {
            $this->render_notice(__('You are not allowed to move topics.', 'asgaros-forum'));
        }
    }

    public function get_category_name($category_id) {
        $category = get_term($category_id, 'asgarosforum-category');

        if ($category) {
            return $category->name;
        } else {
            return false;
        }
    }

    public $topic_counters_cache = false;
    public function get_topic_counters() {
        // If the cache is not set yet, create it.
        if ($this->topic_counters_cache === false) {
            // Get all topic-counters of each forum first.
            $topic_counters = $this->db->get_results("SELECT parent_id AS forum_id, COUNT(*) AS topic_counter FROM {$this->tables->topics} WHERE approved = 1 GROUP BY parent_id;");

            // Assign topic-counter for each forum.
            if (!empty($topic_counters)) {
                foreach ($topic_counters as $counter) {
                    $this->topic_counters_cache[$counter->forum_id] = $counter->topic_counter;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign topic-counter for each forum based on the topic-counters in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no topics.
                    if (!isset($this->topic_counters_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no topics.
                    if (!isset($this->topic_counters_cache[$subforum->parent_forum])) {
                        $this->topic_counters_cache[$subforum->parent_forum] = $this->topic_counters_cache[$subforum->id];
                        continue;
                    }

                    // Otherwise add subforum-topics to the counter of the parent forum.
                    $this->topic_counters_cache[$subforum->parent_forum] = ($this->topic_counters_cache[$subforum->parent_forum] + $this->topic_counters_cache[$subforum->id]);
                }
            }
        }

        return $this->topic_counters_cache;
    }

    public function get_forum_topic_counter($forum_id) {
        $counters = $this->get_topic_counters();

        if (isset($counters[$forum_id])) {
            return (int) $counters[$forum_id];
        } else {
            return 0;
        }
    }

    public $post_counters_cache = false;
    public function get_post_counters() {
        // If the cache is not set yet, create it.
        if ($this->post_counters_cache === false) {
            // Get all post-counters of each forum first.
            $post_counters = $this->db->get_results("SELECT t.parent_id AS forum_id, COUNT(*) AS post_counter FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.parent_id = t.id AND t.approved = 1 GROUP BY t.parent_id;");

            // Assign post-counter for each forum.
            if (!empty($post_counters)) {
                foreach ($post_counters as $counter) {
                    $this->post_counters_cache[$counter->forum_id] = $counter->post_counter;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign post-counter for each forum based on the post-counters in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no posts.
                    if (!isset($this->post_counters_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no posts.
                    if (!isset($this->post_counters_cache[$subforum->parent_forum])) {
                        $this->post_counters_cache[$subforum->parent_forum] = $this->post_counters_cache[$subforum->id];
                        continue;
                    }

                    // Otherwise add subforum-posts to the counter of the parent forum.
                    $this->post_counters_cache[$subforum->parent_forum] = ($this->post_counters_cache[$subforum->parent_forum] + $this->post_counters_cache[$subforum->id]);
                }
            }
        }

        return $this->post_counters_cache;
    }

    public function get_forum_post_counter($forum_id) {
        $counters = $this->get_post_counters();

        if (isset($counters[$forum_id])) {
            return (int) $counters[$forum_id];
        } else {
            return 0;
        }
    }

    public function get_forums($id = false, $parent_forum = 0, $output_type = OBJECT) {
        if ($id) {
            $query_count_subforums = "SELECT COUNT(*) FROM {$this->tables->forums} WHERE parent_forum = f.id";
            return $this->db->get_results($this->db->prepare("SELECT f.*, ({$query_count_subforums}) AS count_subforums FROM {$this->tables->forums} AS f WHERE f.parent_id = %d AND f.parent_forum = %d ORDER BY f.sort ASC;", $id, $parent_forum), $output_type);
        }
    }

    public function getSpecificForums($ids) {
        $results = $this->db->get_results("SELECT id, parent_id AS category_id, name FROM {$this->tables->forums} WHERE id IN (".implode(',', $ids).") ORDER BY id ASC;");
        return $results;
    }

    public function getSpecificTopics($ids) {
        $results = $this->db->get_results("SELECT t.id, f.parent_id AS category_id, t.name FROM {$this->tables->topics} AS t LEFT JOIN {$this->tables->forums} AS f ON (f.id = t.parent_id) WHERE t.id IN (".implode(',', $ids).") ORDER BY t.id ASC;");
        return $results;
    }

    public function get_posts() {
        $start = $this->current_page * $this->options['posts_per_page'];
        $end = $this->options['posts_per_page'];

        $order = apply_filters('asgarosforum_filter_get_posts_order', 'p1.id ASC');
        $results = $this->db->get_results($this->db->prepare("SELECT p1.id, p1.text, p1.date, p1.date_edit, p1.author_id, p1.author_edit, (SELECT COUNT(*) FROM {$this->tables->posts} AS p2 WHERE p2.author_id = p1.author_id) AS author_posts, p1.uploads FROM {$this->tables->posts} AS p1 WHERE p1.parent_id = %d ORDER BY {$order} LIMIT %d, %d;", $this->current_topic, $start, $end));
        $results = apply_filters('asgarosforum_filter_get_posts', $results);
        return $results;
    }

    private $is_first_post_cache = array();
    public function is_first_post($post_id, $topic_id = false) {
        // When we dont know the topic-id, we need to figure it out.
        if (!$topic_id) {
            // Check if there exists an current-topic-id.
            if ($this->current_topic) {
                $topic_id = $this->current_topic;
            }
        }

        if (empty($this->is_first_post_cache[$topic_id])) {
            $this->is_first_post_cache[$topic_id] = $this->db->get_var("SELECT id FROM {$this->tables->posts} WHERE parent_id = {$topic_id} ORDER BY id ASC LIMIT 1;");
        }

        if ($post_id == $this->is_first_post_cache[$topic_id]) {
            return true;
        } else {
            return false;
        }
    }

    public function cut_string($string, $length = 33, $at_next_space = false) {
        $string_length = 0;

        // Get string-length first.
        if (function_exists('mb_strlen')) {
            $string_length = mb_strlen($string, 'UTF-8');
        } else {
            $string_length = strlen($string);
        }

        // Only cut string if it is longer than defined.
        if ($string_length > $length) {
            // Try to find position of next space if necessary.
            if ($at_next_space) {
                $space_position = false;

                // Get position of space.
                if (function_exists('mb_strpos')) {
                    $space_position = mb_strpos($string, ' ', $length, 'UTF-8');
                } else {
                    $space_position = strpos($string, ' ', $length);
                }

                if ($space_position) {
                    $length = $space_position;
                } else {
                    return $string;
                }
            }

            // Return substring.
            if (function_exists('mb_substr')) {
                return mb_substr($string, 0, $length, 'UTF-8').' &#8230;';
            } else {
                return substr($string, 0, $length).' &#8230;';
            }
        }

        return $string;
    }

    // TODO: Clean up the complete username logic ...
    public function get_plain_username($user_id) {
        if ($user_id) {
            $user = get_userdata($user_id);

            if ($user) {
                return $user->display_name;
            } else {
                return __('Deleted user', 'asgaros-forum');
            }
        } else {
            return __('Guest', 'asgaros-forum');
        }
    }

    /**
     * Returns and caches a username.
     */
    public $cacheGetUsername = array();
    public function getUsername($user_id) {
        if ($user_id) {
            if (empty($this->cacheGetUsername[$user_id])) {
                $user = get_userdata($user_id);

                if ($user) {
                    $this->cacheGetUsername[$user_id] = $this->renderUsername($user);
                } else {
                    $this->cacheGetUsername[$user_id] = __('Deleted user', 'asgaros-forum');
                }
            }

            return $this->cacheGetUsername[$user_id];
        } else {
            return __('Guest', 'asgaros-forum');
        }
    }

    /**
     * Renders a username.
     */
    public function renderUsername($userObject, $custom_name = false) {

        // Add filter to change username
        $user_name = apply_filters('asgarosforum_filter_username', $userObject->display_name, $userObject);

        if ($custom_name) {
            $user_name = $custom_name;
        }

        $renderedUserName = $user_name;

        $profileLink = $this->profile->getProfileLink($userObject);

        if ($profileLink) {
            $renderedUserName = '<a class="profile-link" href="'.$profileLink.'">'.$user_name.'</a>';
        } else {
            $renderedUserName = $user_name;
        }

        $renderedUserName = $this->highlight_username($userObject, $renderedUserName);

        return $renderedUserName;
    }

    /**
     * Highlights a username when he is an administrator/moderator.
     */
    public function highlight_username($user, $string) {
        if ($this->options['highlight_admin']) {
            if ($this->permissions->isAdministrator($user->ID)) {
                return '<span class="highlight-admin">'.$string.'</span>';
            } else if ($this->permissions->isModerator($user->ID)) {
                return '<span class="highlight-moderator">'.$string.'</span>';
            }
        }

        return $string;
    }

    private $lastpost_forum_cache = false;
    public function lastpost_forum_cache() {
        if ($this->lastpost_forum_cache === false) {
            // Get all lastpost-elements of each forum first. Selection on topics is needed here because we only want posts of approved topics.
            $lastpost_elements = $this->db->get_results("SELECT t.parent_id AS forum_id, MAX(p.id) AS id FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.parent_id = t.id AND t.approved = 1 GROUP BY t.parent_id;");

            // Assign lastpost-ids for each forum.
            if (!empty($lastpost_elements)) {
                foreach ($lastpost_elements as $element) {
                    $this->lastpost_forum_cache[$element->forum_id] = $element->id;
                }
            }

            // Now get all subforums.
            $subforums = $this->content->get_all_subforums();

            // Re-assign lastpost-ids for each forum based on the lastposts in its subforums.
            if (!empty($subforums)) {
                foreach ($subforums as $subforum) {
                    // Continue if the subforum has no posts.
                    if (!isset($this->lastpost_forum_cache[$subforum->id])) {
                        continue;
                    }

                    // Re-assign value when the parent-forum has no posts.
                    if (!isset($this->lastpost_forum_cache[$subforum->parent_forum])) {
                        $this->lastpost_forum_cache[$subforum->parent_forum] = $this->lastpost_forum_cache[$subforum->id];
                    }

                    // Otherwise re-assign value when a subforum has a more recent post.
                    if ($this->lastpost_forum_cache[$subforum->id] > $this->lastpost_forum_cache[$subforum->parent_forum]) {
                        $this->lastpost_forum_cache[$subforum->parent_forum] = $this->lastpost_forum_cache[$subforum->id];
                    }
                }
            }
        }
    }

    private $get_lastpost_in_forum_cache = array();
    public function get_lastpost_in_forum($forum_id) {
        if (!isset($this->get_lastpost_in_forum_cache[$forum_id])) {
            $this->lastpost_forum_cache();

            if (isset($this->lastpost_forum_cache[$forum_id])) {
                $this->get_lastpost_in_forum_cache[$forum_id] = $this->db->get_row("SELECT p.id, p.date, p.parent_id, p.author_id, t.name FROM {$this->tables->posts} AS p, {$this->tables->topics} AS t WHERE p.id = ".$this->lastpost_forum_cache[$forum_id]." AND t.id = p.parent_id;");
            } else {
                $this->get_lastpost_in_forum_cache[$forum_id] = false;
            }
        }

        return $this->get_lastpost_in_forum_cache[$forum_id];
    }

    public function render_lastpost_in_forum($forum_id, $compact = false) {
        $lastpost = $this->get_lastpost_in_forum($forum_id);

        if ($lastpost === false) {
            echo '<small class="no-topics">'.esc_html__('No topics yet!', 'asgaros-forum').'</small>';
        } else {
            $post_link = $this->rewrite->get_post_link($lastpost->id, $lastpost->parent_id);

            if ($compact === true) {
                echo esc_html__('Last post:', 'asgaros-forum');
                echo '&nbsp;';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->cut_string(stripslashes($lastpost->name), 34)).'</a>';
                echo '&nbsp;&middot;&nbsp;';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->get_activity_timestamp($lastpost->date)).'</a>';
                echo '&nbsp;&middot;&nbsp;';
                echo $this->getUsername($lastpost->author_id);
            } else {
                // Avatar
                if ($this->options['enable_avatars']) {
                    echo '<div class="forum-poster-avatar">'.get_avatar($lastpost->author_id, 40, '', '', array('force_display' => true)).'</div>';
                }

                // Summary
                echo '<div class="forum-poster-summary">';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->cut_string(stripslashes($lastpost->name), 25)).'</a><br>';
                echo '<small>';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->get_activity_timestamp($lastpost->date)).'</a>';
                echo '<span>&nbsp;&middot;&nbsp;</span>';
                echo $this->getUsername($lastpost->author_id);
                echo '</small>';
                echo '</div>';
            }
        }
    }

    public function get_lastpost_in_topic($topic_id) {
        if (empty($this->cache['get_lastpost_in_topic'][$topic_id])) {
            $this->cache['get_lastpost_in_topic'][$topic_id] = $this->db->get_row("SELECT id, date, author_id, parent_id FROM {$this->tables->posts} WHERE parent_id = {$topic_id} ORDER BY id DESC LIMIT 1;");
        }

        return $this->cache['get_lastpost_in_topic'][$topic_id];
    }

    public function render_lastpost_in_topic($topic_id, $compact = false) {
        $lastpost = $this->get_lastpost_in_topic($topic_id);

        // Ensure that a lastpost exists. This is a required check in case a topic is empty due to problems during post-creation.
        if (!empty($lastpost)) {
            $post_link = $this->rewrite->get_post_link($lastpost->id, $lastpost->parent_id);

            if ($compact === true) {
                echo esc_html__('Last post:', 'asgaros-forum');
                echo '&nbsp;';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->get_activity_timestamp($lastpost->date)).'</a>';
                echo '&nbsp;&middot;&nbsp;';
                echo $this->getUsername($lastpost->author_id);
            } else {
                // Avatar
                if ($this->options['enable_avatars']) {
                    echo '<div class="topic-poster-avatar">'.get_avatar($lastpost->author_id, 40, '', '', array('force_display' => true)).'</div>';
                }

                // Summary
                echo '<div class="forum-poster-summary">';
                echo '<a href="'.esc_url($post_link).'">'.esc_html($this->get_activity_timestamp($lastpost->date)).'</a><br>';
                echo '<small>';
                echo $this->getUsername($lastpost->author_id);
                echo '</small>';
                echo '</div>';
            }
        }
    }

    public function get_topic_starter($topic_id) {
        return $this->db->get_var($this->db->prepare("SELECT author_id FROM {$this->tables->topics} WHERE id = %d;", $topic_id));
    }

    public function format_date($date, $full = true) {
        if ($full) {
            return date_i18n($this->date_format.', '.$this->time_format, strtotime($date));
        } else {
            return date_i18n($this->date_format, strtotime($date));
        }
    }

    public function current_time() {
        return current_time('Y-m-d H:i:s');
    }

    // Returns the timestamp of an activity based on the settings (relative or actual timestamp).
    public function get_activity_timestamp($timestamp, $force = false) {
        $timestamp_mode = ($force === false) ? $this->options['activity_timestamp_format'] : $force;

        switch ($timestamp_mode) {
            case 'relative':
                return sprintf(__('%s ago', 'asgaros-forum'), human_time_diff(strtotime($timestamp), current_time('timestamp')));
            break;
            case 'actual':
                return $this->format_date($timestamp, true);
            break;
        }
    }

    public function get_post_author($post_id) {
        return $this->db->get_var($this->db->prepare("SELECT author_id FROM {$this->tables->posts} WHERE id = %d;", $post_id));
    }

    public function get_post_date($post_id) {
        return $this->db->get_var($this->db->prepare("SELECT `date` FROM {$this->tables->posts} WHERE id = %d;", $post_id));
    }

    // Returns the topics created by a user.
    public function countTopicsByUser($user_id) {
        return $this->db->get_var("SELECT COUNT(*) FROM {$this->tables->topics} WHERE author_id = {$user_id};");
    }

    public function countPostsByUser($userID) {
        return $this->db->get_var("SELECT COUNT(*) FROM {$this->tables->posts} WHERE author_id = {$userID};");
    }

    public function render_sticky_panel() {
        // Cancel if the current user is not at least a moderator.
        if (!$this->permissions->isModerator('current')) {
            return;
        }

        echo '<div id="sticky-panel">';
            echo '<div class="title-element title-element-dark">';
                echo '<span class="title-element-icon fas fa-thumbtack"></span>';
                echo esc_html__('Select Sticky Mode:', 'asgaros-forum');
            echo '</div>';
            echo '<div class="content-container">';
                echo '<form method="post" action="'.esc_url($this->get_link('topic', absint($this->current_topic))).'">';
                    echo '<div class="action-panel">';
                        echo '<label class="action-panel-option">';
                            echo '<input type="radio" name="sticky_topic" value="1">';
                            echo '<span class="action-panel-title">';
                                echo '<span class="action-panel-icon fas fa-thumbtack"></span>';
                                echo esc_html__('Sticky', 'asgaros-forum');
                            echo '</span>';
                            echo '<span class="action-panel-description">';
                                esc_html_e('The topic will be sticked to the current forum.', 'asgaros-forum');
                            echo '</span>';
                        echo '</label>';
                        echo '<label class="action-panel-option">';
                            echo '<input type="radio" name="sticky_topic" value="2">';
                            echo '<span class="action-panel-title">';
                                echo '<span class="action-panel-icon fas fa-globe-europe"></span>';
                                echo esc_html__('Global Sticky', 'asgaros-forum');
                            echo '</span>';
                            echo '<span class="action-panel-description">';
                                esc_html_e('The topic will be sticked to all forums.', 'asgaros-forum');
                            echo '</span>';
                        echo '</label>';
                    echo '</div>';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }

    // Generate forum menu.
    public function showForumMenu() {
        $menu = '';

        if ($this->forumIsOpen()) {
            if ((is_user_logged_in() && !$this->permissions->isBanned('current')) || (!is_user_logged_in() && $this->options['allow_guest_postings'])) {
                // New topic button.
                $menu .= '<div class="forum-menu">';
                $menu .= '<a class="button button-normal forum-editor-button" href="'.$this->get_link('addtopic', $this->current_forum).'">';
                    $menu .= '<span class="menu-icon fas fa-plus-square"></span>';
                    $menu .= __('New Topic', 'asgaros-forum');
                $menu .= '</a>';
                $menu .= '</div>';
            }
        }

        $menu = apply_filters('asgarosforum_filter_forum_menu', $menu);

        return $menu;
    }

    // Generate topic menu.
    public function show_topic_menu($show_all_buttons = true) {
        $menu = '';

        $current_user_id = get_current_user_id();

        // Show reply and certain moderation-buttons only for approved topics.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            if ($this->permissions->can_create_post($current_user_id)) {
                // Reply button.
                $menu .= '<a class="button button-normal forum-editor-button" href="'.$this->get_link('addpost', $this->current_topic).'">';
                    $menu .= '<span class="menu-icon fas fa-comment-dots"></span>';
                    $menu .= __('Reply', 'asgaros-forum');
                $menu .= '</a>';
            }

            if ($show_all_buttons) {
                if ($this->permissions->isModerator('current')) {
                    // Move button.
                    $menu .= '<a class="button button-normal" href="'.$this->get_link('movetopic', $this->current_topic).'">';
                        $menu .= '<span class="menu-icon fas fa-random"></span>';
                        $menu .= __('Move', 'asgaros-forum');
                    $menu .= '</a>';

                    if ($this->is_topic_sticky($this->current_topic)) {
                        // Undo sticky button.
                        $menu .= '<a class="button button-normal topic-button-unsticky" href="'.$this->get_link('topic', $this->current_topic, array('unsticky_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-thumbtack"></span>';
                            $menu .= __('Unsticky', 'asgaros-forum');
                        $menu .= '</a>';
                    } else {
                        // Sticky button.
                        $menu .= '<a class="button button-normal topic-button-sticky" href="'.$this->get_link('topic', $this->current_topic, array('sticky_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-thumbtack"></span>';
                            $menu .= __('Sticky', 'asgaros-forum');
                        $menu .= '</a>';
                    }
                }

                if ($this->is_topic_closed($this->current_topic)) {
                    // Open button.
                    if ($this->permissions->can_open_topic($current_user_id, $this->current_topic)) {
                        $menu .= '<a class="button button-normal" href="'.$this->get_link('topic', $this->current_topic, array('open_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-unlock"></span>';
                            $menu .= __('Open', 'asgaros-forum');
                        $menu .= '</a>';
                    }
                } else {
                    // Close button.
                    if ($this->permissions->can_close_topic($current_user_id, $this->current_topic)) {
                        $menu .= '<a class="button button-normal" href="'.$this->get_link('topic', $this->current_topic, array('close_topic' => 1)).'">';
                            $menu .= '<span class="menu-icon fas fa-lock"></span>';
                            $menu .= __('Close', 'asgaros-forum');
                        $menu .= '</a>';
                    }
                }
            }
        } else {
            if ($this->permissions->isModerator('current') && $show_all_buttons) {
                // Approve button.
                if (!$this->approval->is_topic_approved($this->current_topic)) {
                    $menu .= '<a class="button button-green" href="'.$this->get_link('topic', $this->current_topic, array('approve_topic' => 1)).'">';
                        $menu .= '<span class="menu-icon fas fa-check"></span>';
                        $menu .= __('Approve', 'asgaros-forum');
                    $menu .= '</a>';
                }
            }
        }

        if ($this->permissions->can_delete_topic($current_user_id, $this->current_topic) && $show_all_buttons) {
            // Delete button.
            $delete_topic_link = $this->get_link('topic', $this->current_topic, array('delete_topic' => 1, '_wpnonce' => wp_create_nonce('asgaros_forum_delete_topic')));

            $menu .= '<a class="button button-red" href="'.$delete_topic_link.'" onclick="return confirm(\''.__('Are you sure you want to remove this?', 'asgaros-forum').'\');">';
                $menu .= '<span class="menu-icon fas fa-trash-alt"></span>';
                $menu .= __('Delete', 'asgaros-forum');
            $menu .= '</a>';
        }

        $menu = (!empty($menu)) ? '<div class="forum-menu">'.$menu.'</div>' : $menu;
        $menu = apply_filters('asgarosforum_filter_topic_menu', $menu);

        return $menu;
    }

    // Generate post menu.
    public function show_post_menu($post_id, $author_id, $counter, $post_date) {
        $menu = '';

        // Only show post-menu when the topic is approved.
        if ($this->approval->is_topic_approved($this->current_topic)) {
            if (is_user_logged_in()) {
                $current_user_id = get_current_user_id();

                if ($this->permissions->can_delete_post($current_user_id, $post_id, $author_id, $post_date) && ($counter > 1 || $this->current_page >= 1)) {
                    // Delete button.
                    $delete_post_link = $this->get_link('topic', $this->current_topic, array('post' => $post_id, 'remove_post' => 1, '_wpnonce' => wp_create_nonce('asgaros_forum_delete_post')));

                    $menu .= '<a class="delete-forum-post" onclick="return confirm(\''.__('Are you sure you want to remove this?', 'asgaros-forum').'\');" href="'.$delete_post_link.'">';
                        $menu .= '<span class="menu-icon fas fa-trash-alt"></span>';
                        $menu .= __('Delete', 'asgaros-forum');
                    $menu .= '</a>';
                }

                if ($this->permissions->can_edit_post($current_user_id, $post_id, $author_id, $post_date)) {
                    // Edit button.
                    $menu .= '<a href="'.$this->get_link('editpost', $post_id, array('part' => ($this->current_page + 1))).'">';
                        $menu .= '<span class="menu-icon fas fa-pencil-alt"></span>';
                        $menu .= __('Edit', 'asgaros-forum');
                    $menu .= '</a>';
                }
            }

            if ($this->permissions->isModerator('current') || (!$this->is_topic_closed($this->current_topic) && ((is_user_logged_in() && !$this->permissions->isBanned('current')) || (!is_user_logged_in() && $this->options['allow_guest_postings'])))) {
                // Quote button.
                $menu .= '<a class="forum-editor-quote-button" data-value-id="'.$post_id.'" href="'.$this->get_link('addpost', $this->current_topic, array('quote' => $post_id)).'">';
                    $menu .= '<span class="menu-icon fas fa-quote-left"></span>';
                    $menu .= __('Quote', 'asgaros-forum');
                $menu .= '</a>';
            }
        }

        // Show report button.
        $menu .= $this->reports->render_report_button($post_id, $this->current_topic);

        $menu = (!empty($menu)) ? '<div class="forum-post-menu">'.$menu.'</div>' : $menu;
        $menu = apply_filters('asgarosforum_filter_post_menu', $menu);

        return $menu;
    }

    public function showHeader() {
        echo '<div id="forum-header">';
            echo '<div id="forum-navigation-mobile">';
                echo '<a>';
                    echo '<span class="fas fa-bars"></span>';
                    echo esc_html__('Menu', 'asgaros-forum');
                echo '</a>';
            echo '</div>';

            echo '<span class="screen-reader-text">'.esc_html__('Forum Navigation', 'asgaros-forum').'</span>';

            echo '<div id="forum-navigation">';
                /**
                 * $menu_entries = array(
                 *     array(
                 *         'menu_class'         => 'HTML Class'
                 *         'menu_link_text'     => 'Link Text',
                 *         'menu_url'           => '/url',
                 *         'menu_login_status'  => '0', (0 = all, 1 = only logged in, 2 = only logged out)
                 *         'menu_new_tab'       => true (true = open in new tab, false = open in same tab)
                 *     )
                 * );
                 */

                $menu_entries = array();
                $menu_entries['home'] = $this->homeLink();
                $menu_entries['profile'] = $this->profile->myProfileLink();
                $menu_entries['memberslist'] = $this->memberslist->show_memberslist_link();
                $menu_entries['subscription'] = $this->notifications->show_subscription_overview_link();
                $menu_entries['activity'] = $this->activity->show_activity_link();
                $menu_entries['login'] = $this->showLoginLink();
                $menu_entries['register'] = $this->showRegisterLink();
                $menu_entries['logout'] = $this->showLogoutLink();

                // Apply filter to menu entries
                $menu_entries = apply_filters('asgarosforum_filter_header_menu', $menu_entries);

                $this->drawMenuEntries($menu_entries);
                do_action('asgarosforum_custom_header_menu');
            echo '</div>';

            $this->search->show_search_input();

            echo '<div class="clear"></div>';
        echo '</div>';

        $this->breadcrumbs->show_breadcrumbs();
    }

    public function drawMenuEntries($menu_entries) {
        // Ensure that menu-entries are not empty.
        if (empty($menu_entries) || !is_array($menu_entries)) return;

        foreach ($menu_entries as $menu_entry) {
            // Check menu entry.
            if (empty($menu_entry)) continue;
            if (!isset($menu_entry['menu_new_tab'])) $menu_entry['menu_new_tab'] = false;
            if (!isset($menu_entry['menu_url'])) $menu_entry['menu_url'] = '/';
            if (!isset($menu_entry['menu_login_status'])) $menu_entry['menu_login_status'] = 0;
            if (!isset($menu_entry['menu_link_text'])) $menu_entry['menu_link_text'] = __('Link Text Missing', 'asgaros-forum');

            // Check login status.
            $login_status = is_user_logged_in();
            $menu_login_status = $menu_entry['menu_login_status'];

            if ($menu_login_status == 1 && ! $login_status) {
                continue;
            } else if ($menu_login_status == 2 && $login_status) {
                continue;
            }

            // Check if menu-URL is a slug.
            $menu_url = $menu_entry['menu_url'];

            if ($menu_url[0] == '/') {
                $menu_url = get_home_url().$menu_url;
            }

            // Check menu class.
            $menu_class = (isset($menu_entry['menu_class'])) ? 'class="'.$menu_entry['menu_class'].'"' : '';

            // Check if link has to open in a new tab.
            $new_tab = ($menu_entry['menu_new_tab']) ? '_blank' : '_self';

            echo '<a '.esc_attr($menu_class).' href="'.esc_url($menu_url).'" target="'.esc_attr($new_tab).'">'.esc_html($menu_entry['menu_link_text']).'</a>';
        }
    }

    public function homeLink(){
        $home_url = $this->get_link('home');

        return array(
            'menu_class'        => 'home-link',
            'menu_link_text'    => esc_html__('Forum', 'asgaros-forum'),
            'menu_url'          => $home_url,
            'menu_login_status' => 0,
            'menu_new_tab'      => false
        );
    }

    public function showLogoutLink() {
        if ($this->options['show_logout_button']) {
            $logout_url = wp_logout_url($this->get_link('current', false, false, '', false));

            return array(
                'menu_class'        => 'logout-link',
                'menu_link_text'    => esc_html__('Logout', 'asgaros-forum'),
                'menu_url'          => $logout_url,
                'menu_login_status' => 1,
                'menu_new_tab'      => false
            );
        }
    }

    public function showLoginLink() {
        if ($this->options['show_login_button']) {
            $login_url = wp_login_url($this->get_link('current', false, false, '', false));

            if (!empty($this->options['custom_url_login'])) {
                $login_url = $this->options['custom_url_login'];
            }

            return array(
                'menu_class'        => 'login-link',
                'menu_link_text'    => esc_html__('Login', 'asgaros-forum'),
                'menu_url'          => $login_url,
                'menu_login_status' => 2,
                'menu_new_tab'      => false
            );
        }
    }

    public function showRegisterLink() {
        if ( $this->options['show_register_button']) {
            $register_url = wp_registration_url();

            if (!empty($this->options['custom_url_register'])) {
                $register_url = $this->options['custom_url_register'];
            }

            return array(
                'menu_class'        => 'register-link',
                'menu_link_text'    => esc_html__('Register', 'asgaros-forum'),
                'menu_url'          => $register_url,
                'menu_login_status' => 2,
                'menu_new_tab'      => false
            );
        }
    }

    public function delete_topic($topic_id, $admin_action = false, $permission_check = true) {
        // Cancel when no topic is given.
        if (!$topic_id) {
            return false;
        }

        // Cancel when topic does not exist.
        if (!$this->content->topic_exists($topic_id)) {
            return false;
        }

        // Cancel if user cannot delete topic.
        if ($permission_check) {
            $user_id = get_current_user_id();

            if (!$this->permissions->can_delete_topic($user_id, $topic_id)) {
                return false;
            }
        }

        // Continue ...
        do_action('asgarosforum_before_delete_topic', $topic_id);

        // Delete posts.
        $posts = $this->db->get_col($this->db->prepare("SELECT id FROM {$this->tables->posts} WHERE parent_id = %d;", $topic_id));
        foreach ($posts as $post) {
            $this->remove_post($post, false);
        }

        // Delete topic.
        $this->db->delete($this->tables->topics, array('id' => $topic_id), array('%d'));
        $this->notifications->remove_all_topic_subscriptions($topic_id);

        do_action('asgarosforum_after_delete_topic', $topic_id);

        if (!$admin_action) {
            wp_safe_redirect(html_entity_decode($this->get_link('forum', $this->current_forum)));
            exit;
        }
    }

    public function moveTopic() {
        $newForumID = sanitize_key($_POST['newForumID']);

        if ($this->permissions->isModerator('current') && $newForumID && $this->content->forum_exists($newForumID)) {
            $this->db->update($this->tables->topics, array('parent_id' => $newForumID), array('id' => $this->current_topic), array('%d'), array('%d'));
            $this->db->update($this->tables->posts, array('forum_id' => $newForumID), array('parent_id' => $this->current_topic), array('%d'), array('%d'));
            wp_safe_redirect(html_entity_decode($this->get_link('topic', $this->current_topic)));
            exit;
        }
    }

    public function remove_post($post_id, $permission_check = true) {
        // Cancel if no post is given.
        if (!$post_id) {
            return false;
        }

        // Cancel if post does not exist.
        if (!$this->content->post_exists($post_id)) {
            return false;
        }

        // Cancel if user cannot delete post.
        if ($permission_check) {
            $user_id = get_current_user_id();

            if (!$this->permissions->can_delete_post($user_id, $post_id)) {
                return false;
            }
        }

        // Delete post.
        do_action('asgarosforum_before_delete_post', $post_id);
        $this->uploads->delete_post_files($post_id);
        $this->reports->remove_report($post_id, false);
        $this->reactions->remove_all_reactions($post_id);
        $this->db->delete($this->tables->posts, array('id' => $post_id), array('%d'));
        do_action('asgarosforum_after_delete_post', $post_id);
    }

    public function change_status($property) {
        $user_id = get_current_user_id();

        if ($property == 'closed') {
            if (!$this->permissions->can_close_topic($user_id, $this->current_topic)) {
                return false;
            }

            $this->db->update($this->tables->topics, array('closed' => 1), array('id' => $this->current_topic), array('%d'), array('%d'));
        } else if ($property == 'open') {
            if (!$this->permissions->can_open_topic($user_id, $this->current_topic)) {
                return false;
            }

            $this->db->update($this->tables->topics, array('closed' => 0), array('id' => $this->current_topic), array('%d'), array('%d'));
        }
    }

    public function set_sticky($topic_id, $sticky_mode) {
        if (!$this->permissions->isModerator('current')) {
            return;
        }

        // Ensure that only correct values can get set.
        switch ($sticky_mode) {
            case 0:
            case 1:
            case 2:
                $this->db->update($this->tables->topics, array('sticky' => $sticky_mode), array('id' => $topic_id), array('%d'), array('%d'));
            break;
        }
    }

    public function is_topic_sticky($topic_id) {
        $status = $this->db->get_var("SELECT sticky FROM {$this->tables->topics} WHERE id = {$topic_id};");

        if ((int) $status > 0) {
            return true;
        } else {
            return false;
        }
    }

    private $is_topic_closed_cache = array();
    public function is_topic_closed($topic_id) {
        if (!isset($this->is_topic_closed_cache[$topic_id])) {
            $status = $this->db->get_var("SELECT closed FROM {$this->tables->topics} WHERE id = {$topic_id};");

            if ((int) $status === 1) {
                $this->is_topic_closed_cache[$topic_id] = true;
            } else {
                $this->is_topic_closed_cache[$topic_id] = false;
            }
        }

        return $this->is_topic_closed_cache[$topic_id];
    }

    // Returns TRUE if the forum is opened or the user has at least moderator rights.
    public function forumIsOpen() {
        if (!$this->permissions->isModerator('current')) {
            $status = $this->db->get_var($this->db->prepare("SELECT forum_status FROM {$this->tables->forums} WHERE id = %d;", $this->current_forum));

            if ($status == 'closed') {
                return false;
            }
        }

        return true;
    }

    // Builds and returns a requested link.
    public function get_link($type, $elementID = false, $additionalParameters = false, $appendix = '', $escapeURL = true) {
        return $this->rewrite->get_link($type, $elementID, $additionalParameters, $appendix, $escapeURL);
    }

    // Checks if an element exists and sets all parent IDs based on the given id and its content type.
    public function setParents($id, $contentType) {
        // Set possible error messages.
        $error = array();
        $error['post']  = __('Sorry, this post does not exist.', 'asgaros-forum');
        $error['topic'] = __('Sorry, this topic does not exist.', 'asgaros-forum');
        $error['forum'] = __('Sorry, this forum does not exist.', 'asgaros-forum');

        if ($id) {
            $query = '';
            $results = false;

            // Build the query.
            switch ($contentType) {
                case 'post':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, t.id AS current_topic, t.name AS current_topic_name, p.id AS current_post, p.text AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) LEFT JOIN {$this->tables->topics} AS t ON (f.id = t.parent_id) LEFT JOIN {$this->tables->posts} AS p ON (t.id = p.parent_id) WHERE p.id = {$id};";
                break;
                case 'topic':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, t.id AS current_topic, t.name AS current_topic_name, (SELECT td.text FROM {$this->tables->posts} AS td WHERE td.parent_id = t.id ORDER BY td.id ASC LIMIT 1) AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) LEFT JOIN {$this->tables->topics} AS t ON (f.id = t.parent_id) WHERE t.id = {$id};";
                break;
                case 'forum':
                    $query = "SELECT f.parent_id AS current_category, f.id AS current_forum, f.name AS current_forum_name, f.parent_forum AS parent_forum, pf.name AS parent_forum_name, f.description AS current_description FROM {$this->tables->forums} AS f LEFT JOIN {$this->tables->forums} AS pf ON (pf.id = f.parent_forum) WHERE f.id = {$id};";
                break;
            }

            $results = $this->db->get_row($query);

            // When the element exists, set parents and exit function.
            if ($results) {
                if ($contentType === 'post' || $contentType === 'topic' || $contentType === 'forum') {
                    // Generate description.
                    $description = $results->current_description;
                    $description = strip_tags($description);
                    $description = esc_html($description);
                    $description = str_replace(array("\r", "\n"), '', $description);
                    $this->current_description = $this->cut_string($description, 155, true);

                    $this->current_category = $results->current_category;
                    $this->parent_forum = $results->parent_forum;
                    $this->parent_forum_name = $results->parent_forum_name;
                    $this->current_forum = $results->current_forum;
                    $this->current_forum_name = $results->current_forum_name;
                }

                $this->current_topic        = ($contentType === 'post' || $contentType === 'topic') ? $results->current_topic : false;
                $this->current_topic_name   = ($contentType === 'post' || $contentType === 'topic') ? $results->current_topic_name : false;
                $this->current_post         = ($contentType === 'post') ? $results->current_post : false;
                $this->parents_set          = true;
                return;
            }
        }

        // Assign error message, because when this location is reached, no parents has been set.
        $this->error = $error[$contentType];
    }

    // Returns the amount of users.
    public function count_users() {
        return $this->db->get_var("SELECT COUNT(u.ID) FROM {$this->db->users} u");
    }

    // Prevents oembed dataparsing for links which points to the own forum.
    public function prevent_oembed_dataparse($return, $data, $url) {
        $url_check = strpos($url, $this->rewrite->get_link('home'));

        if ($url_check !== false) {
            return $url;
        }

        return $return;
    }

    public function create_file($path, $content) {
        // Create binding for the file first.
        $binding = @fopen($path, 'wb');

        // Check if the binding could get created.
        if ($binding) {
            // Write the content to the file.
            $writing = @fwrite($binding, $content);

            // Close the file.
            fclose($binding);

            // Clear stat cache so we can set the correct permissions.
            clearstatcache();

            // Get information about the file.
            $file_stats = @stat(dirname($path));

            // Update the permissions of the file.
            $file_permissions = $file_stats['mode'] & 0007777;
    		$file_permissions = $file_permissions & 0000666;
    		@chmod($path, $file_permissions);

            // Clear stat cache again so PHP is aware of the new permissions.
            clearstatcache();

            return true;
        }

        return false;
    }

    public function read_file($path) {
        // Create binding for the file first.
    	$binding = @fopen($path, 'r');

        // Check if the binding could get created.
    	if ($binding) {
            // Ensure that the file is not empty.
    		$file_size = @filesize($path);

    		if (isset($file_size) && $file_size > 0) {
                // Read the complete file.
    			$file_data = fread($binding, $file_size);

                // Close the file.
    			fclose($binding);

                // Return the file data.
    			return $file_data;
    		}
    	}

        return false;
    }

    public function delete_file($path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function delete_user_form_reassign($current_user, $userids) {
        // Remove own ID from users which should get deleted.
        $userids = array_diff($userids, array($current_user->ID));

        // Cancel if there are no users to delete.
        if (empty($userids)) {
            return;
        }

        // Check if users have posts or topics.
        $users_have_content = false;

		if ($this->db->get_var("SELECT ID FROM {$this->tables->posts} WHERE author_id IN(".implode(',', $userids).") LIMIT 1;")) {
			$users_have_content = true;
		}

        if ($this->db->get_var("SELECT ID FROM {$this->tables->topics} WHERE author_id IN(".implode(',', $userids).") LIMIT 1;")) {
			$users_have_content = true;
		}

        // Cancel if users have no posts.
        if ($users_have_content === false) {
            return;
        }

        // Show reassign-options.
        echo '<h2>'.esc_html__('Forum', 'asgaros-forum').'</h2>';

        echo '<fieldset>';
        echo '<p><legend>';

        // Count users to delete.
		$go_delete = count($userids);

        if ($go_delete === 1) {
            echo esc_html__('What should be done with forum posts owned by this user?', 'asgaros-forum');
        } else {
            echo esc_html__('What should be done with forum posts owned by these users?', 'asgaros-forum');
        }
        echo '</legend></p>';

	    echo '<ul style="list-style: none;">';
		echo '<li><input type="radio" id="forum_reassign0" name="forum_reassign" value="no" checked="checked">';
        echo '<label for="forum_reassign0">'.esc_html__('Do not reassign forum posts.', 'asgaros-forum').'</label></li>';

		echo '<li><input type="radio" id="forum_reassign1" name="forum_reassign" value="yes">';
		echo '<label for="forum_reassign1">'.esc_html__('Reassign all forum posts to:', 'asgaros-forum').'</label>&nbsp;';
        wp_dropdown_users(array('name' => 'forum_reassign_user', 'exclude' => $userids, 'show' => 'display_name_with_login'));
		echo '</li></ul></fieldset>';
    }

    public function deleted_user_reassign($id, $reassign) {
        // Ensure that correct values are passed.
        if (empty($_POST['forum_reassign'])) {
            return;
        }

        if ($_POST['forum_reassign'] != 'yes') {
            return;
        }

        if (empty($_POST['forum_reassign_user'])) {
            return;
        }

        // Reassign forum posts and topics.
        $author_id = sanitize_key($_POST['forum_reassign_user']);
        $this->db->update($this->tables->posts, array('author_id' => $author_id), array('author_id' => $id), array('%d'), array('%d'));
        $this->db->update($this->tables->topics, array('author_id' => $author_id), array('author_id' => $id), array('%d'), array('%d'));
    }

    // Extract the first URL of an image from a given string.
    public function extract_image_url($content) {
    	$images = array();

        // Check if content is given.
        if ($content) {
            // Try to find images.
            preg_match_all('#https?://[^\s\'\"<>]+\.(?:jpg|jpeg|png|gif)#isu', $content, $found, PREG_SET_ORDER);

            if (empty($found)) {
                preg_match_all('#//[^\s\'\"<>]+\.(?:jpg|jpeg|png|gif)#isu', $content, $found, PREG_SET_ORDER);
            }

            if (empty($found)) {
                preg_match_all('#https?://[^\s\'\"<>]+#isu', $content, $found, PREG_SET_ORDER);
            }

            if (empty($found)) {
                preg_match_all('#//[^\s\'\"<>]+#isu', $content, $found, PREG_SET_ORDER);
            }

            // If images found, check extensions.
            if (!empty($found)) {
                foreach ($found as $match) {
                    $extension = pathinfo($match[0], PATHINFO_EXTENSION);

                    if ($extension) {
                        $check = false;
                        $extension = strtolower($extension);

                    	if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
                    		$check = true;
                    	}

                        if ($check) {
                            $images[] = $match[0];
                            break;
                        }
                    }
                }
            }
        }

    	if (empty($images)) {
            return false;
        } else {
            return $images[0];
        }
    }

    // Tries to get the signature for a given user.
    public function get_signature($user_id) {
        // Ensure signatures are enabled.
        if (!$this->options['allow_signatures']) {
            return false;
        }

        // Ensure that the user has the permission to use a signature.
        if (!$this->permissions->can_use_signature($user_id)) {
            return false;
        }

        // Try to get signature.
        $signature = get_user_meta($user_id, 'asgarosforum_signature', true);

        // Prepare signature based on settings.
        if ($this->options['signatures_html_allowed']) {
            $signature = strip_tags($signature, $this->options['signatures_html_tags']);
        } else {
            $signature = esc_html(strip_tags($signature));
        }

        // Trim it.
        $signature = trim($signature);

        // Allow filtering the signature.
        $signature = apply_filters('asgarosforum_signature', $signature);

        // Ensure signature is not empty.
        if (empty($signature)) {
            return false;
        }

        return $signature;
    }

    public function render_reputation_badges($number) {
        if ($this->options['enable_reputation']) {
            echo '<small class="reputation-badges">';

            if ($number < $this->options['reputation_level_1_posts']) {
                echo '<i class="far fa-star-half"></i>';
            } else {
                echo '<i class="fas fa-star"></i>';

                // Add an additional star for every remaining level.
                for ($i = 2; $i <= 5; $i++) {
                    if ($number >= $this->options['reputation_level_'.$i.'_posts']) {
                        echo '<i class="fas fa-star"></i>';
                    } else {
                        break;
                    }
                }
            }

            echo '</small>';
        }
    }

    /*******************************************/
    /* Functions for automatic topic creation. */
    /*******************************************/

    public function post_transition_update($new_status, $old_status, $post) {
        // Ensure that the required option is activated.
        if ($this->options['create_blog_topics_id'] == 0) {
            return;
        }

        if ($post->post_type == 'post' && $new_status == 'publish' && $old_status != 'publish') {
            $forum_id = $this->options['create_blog_topics_id'];

            $this->create_blog_topic($forum_id, $post);
        }
    }

    public function create_blog_topic($forum_id, $post_object) {
        if ($this->content->forum_exists($forum_id)) {
            $post_title = apply_filters('asgarosforum_filter_automatic_topic_title', $post_object->post_title, $post_object);
            $post_content = apply_filters('asgarosforum_filter_automatic_topic_content', $post_object->post_content, $post_object);

            $this->content->insert_topic($forum_id, $post_title, $post_content, $post_object->post_author);
        }
    }

    public function add_topic_meta_box_setup() {
        add_action('add_meta_boxes', array($this, 'add_topic_meta_box'), 10, 2);
        add_action('save_post', array($this, 'process_topic_meta_box'), 10, 2);
    }

    public function add_topic_meta_box($post_type, $post) {
        if (!current_user_can('publish_post', $post->ID)) {
            return;
        }

        $post_types = apply_filters('asgarosforum_filter_meta_post_type', array('post', 'page'));

        add_meta_box(
            'asgaros-forum-topic-meta-box',
            __('Create Forum Topic', 'asgaros-forum'),
            array($this, 'add_topic_meta_box_content'),
            $post_types,
            'side',
            'low'
        );
    }

    public function add_topic_meta_box_content($post) {
        echo '<select name="add_topic_in_forum" id="add_topic_in_forum">';
        echo '<option value="0"></option>';

        // Generate list of available forums.
        $categories = $this->content->get_categories(false);

        if ($categories) {
            foreach ($categories as $category) {
                $forums = $this->get_forums($category->term_id, 0);

                if ($forums) {
                    foreach ($forums as $forum) {
                        echo '<option value="'.esc_attr($forum->id).'">'.esc_html($forum->name).'</option>';

                        if ($forum->count_subforums > 0) {
                            $subforums = $this->get_forums($category->term_id, $forum->id);

                            foreach ($subforums as $subforum) {
                                echo '<option value="'.esc_attr($subforum->id).'">--- '.esc_html($subforum->name).'</option>';
                            }
                        }
                    }
                }
            }
        }

        echo '</select>';
        echo '<br>';
        echo '<label for="add_topic_in_forum">'.esc_html__('A topic is created in the selected forum when you save this document.', 'asgaros-forum').'</label>';
    }

    public function process_topic_meta_box($post_id, $post) {
        if (!current_user_can('publish_post', $post_id)) {
            return;
        }

		$forum_id = !empty($_POST['add_topic_in_forum']) ? sanitize_key($_POST['add_topic_in_forum']) : 0;

        if ($forum_id > 0) {
            $this->create_blog_topic($forum_id, $post);
        }
    }

	public function has_error() {
		if (!empty($this->error)) {
			return true;
		}

		return false;
	}

	public function render_error() {
		echo '<div class="error">';
		echo wp_kses_post($this->error);
		echo '</div>';
	}
}
