<?php


namespace WPDM\User;


use PrivateMessage\__\__;
use WPDM\__\Template;

class User
{
    public $ID;
    public $name;
    public $profile;
    public $avatar;
    public $description;
    public $signup_date;
    public $title;
    public $following = [];
    public $contacts = [];
    public $blocked = [];
    public $me;

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct($userID = null)
    {
        global $current_user;

        $this->ID = $userID ? $userID : get_current_user_id();

        if($this->ID) {
            $this->profile = get_userdata($this->ID);
            $this->profile->avatar = get_avatar($this->ID, 512);
            $this->description = get_user_meta($this->ID, 'description', true);
            $this->signup_date = wp_date(get_option('date_format'), strtotime($this->profile->user_registered));
            $this->title = get_user_meta($this->ID, '__wpdm_title', true);
            $this->name = $this->profile->display_name;
            $this->avatar = get_avatar($this->ID, 512, '', $this->name, ['class' => 'profile-avatar']);
        }

        $this->me = $current_user;

        add_shortcode("wpdm_user_favourites", [$this, 'favourites']);
        add_shortcode('wpdm_members', [$this, 'members']);
        add_shortcode('wpdm_authors', [$this, 'members']);
    }

    function getFollowings($userID = null)
    {
        if(!is_user_logged_in()) return [];
        $follower = $userID ? $userID : $this->ID;
        $followings = get_user_meta($follower, '__wpdm_following', true);
        $followings = maybe_unserialize($followings);
        if(!is_array($followings)) $followings = [];
        $this->following = $followings;
        return $followings;
    }

    function getContacts($userID = null)
    {
        if(!is_user_logged_in()) return [];
        $user = $userID ? $userID : $this->ID;
        $contacts = get_user_meta($user, '__wpdm_contacts', true);
        $contacts = maybe_unserialize($contacts);
        if(!is_array($contacts)) $contacts = [];
        $this->contacts = $contacts;
        return $contacts;
    }

    function getBlockedContacts($userID = null)
    {
        if(!is_user_logged_in()) return [];
        $user = $userID ? $userID : $this->ID;
        $blockedContacts = get_user_meta($user, '__wpdm_blocked_contacts', true);
        $blockedContacts = maybe_unserialize($blockedContacts);
        if(!is_array($blockedContacts)) $blockedContacts = [];
        $this->blocked = $blockedContacts;
        return $blockedContacts;
    }

    function follow($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getFollowings($this->ID);
        $this->following[$userID] = time();
        update_user_meta($this->ID, '__wpdm_following', $this->following);
        return true;
    }

    function isFollowing($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getFollowings($this->ID);
        return isset($this->following[$userID]) ? $this->following[$userID] : false;
    }

    function unfollow($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getFollowings($this->ID);
        if(isset($this->following[$userID])) unset($this->following[$userID]);
        update_user_meta($this->ID, '__wpdm_following', $this->following);
    }

    function addToContact($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getContacts($this->ID);
        $this->contacts[$userID] = time();
        update_user_meta($this->ID, '__wpdm_contacts', $this->contacts);
        return true;
    }

    function inContactList($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getContacts($this->ID);
        return isset($this->contacts[$userID]) ? $this->contacts[$userID] : false;
    }

    function removeContact($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getContacts($this->ID);
        if(isset($this->contacts[$userID])) unset($this->contacts[$userID]);
        update_user_meta($this->ID, '__wpdm_contacts', $this->contacts);
    }


    function block($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getBlockedContacts($this->ID);
        $this->blocked[$userID] = time();
        update_user_meta($this->ID, '__wpdm_blocked_contacts', $this->blocked);
        return true;
    }

    function isBlocked($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getBlockedContacts($this->ID);
        return isset($this->blocked[$userID]) ? $this->blocked[$userID] : false;
    }

    function unblock($userID)
    {
        if(!is_user_logged_in()) return false;
        $this->getBlockedContacts($this->ID);
        if(isset($this->blocked[$userID])) unset($this->blocked[$userID]);
        update_user_meta($this->ID, '__wpdm_blocked_contacts', $this->contacts);
    }

    function favourites($params = array())
    {
        global $wpdb, $current_user;
        if (!isset($params['user']) && !is_user_logged_in()) return WPDM()->user->login->form();
        ob_start();
        include  Template::locate('user-favourites.php', __DIR__.'/views');
        return ob_get_clean();
    }


    function members($params = array())
    {
        $sid = isset($params['sid']) ? $params['sid'] : '';
        update_post_meta(get_the_ID(), '__wpdm_users_params' . $sid, $params);
        ob_start();
        include Template::locate("members.php", __DIR__.'/views');
        return ob_get_clean();
    }

    function listAuthors($params = [])

    {

        if (!$params) $params = get_post_meta(wpdm_query_var('_pid', 'int'), '__wpdm_users_params' . wpdm_query_var('_sid'), true);
        $page = isset($_REQUEST['cp']) && $_REQUEST['cp'] > 0 ? (int)$_REQUEST['cp'] : 1;
        $items_per_page = isset($params['items_per_page']) ? $params['items_per_page'] : 12;
        //$offset = $page * $items_per_page;
        $cols = isset($params['cols']) && in_array($params['cols'], array(1, 2, 3, 4, 6)) ? $params['cols'] : 0;
        if ($cols > 0) $cols_class = "col-md-" . (12 / $cols);

        $args = array(
            'role' => isset($params['role']) ? $params['role'] : '',
            'role__in' => isset($params['role__in']) ? explode(",", $params['role__in']) : array(),
            'role__not_in' => isset($params['role__not_in']) ? explode(",", $params['role__not_in']) : array(),
            'meta_key' => isset($params['meta_key']) ? $params['meta_key'] : '',
            'meta_value' => isset($params['meta_value']) ? $params['meta_value'] : '',
            'meta_compare' => isset($params['meta_compare']) ? $params['meta_compare'] : '',
            //'meta_query'   => array(),
            //'date_query'   => array(),
            'include' => isset($params['include']) ? explode(",", $params['include']) : array(),
            'exclude' => isset($params['exclude']) ? explode(",", $params['exclude']) : array(),
            'orderby' => isset($params['orderby']) ? $params['orderby'] : 'login',
            'order' => isset($params['order']) ? $params['order'] : 'DESC',
            //'offset'       => $offset,
            'search' => isset($params['search']) ? $params['search'] : '',
            'number' => $items_per_page,
            'paged' => $page,
            'count_total' => true,
        );
        $users = new \WP_User_Query($args);
        if ($cols > 0) echo "<div class='row'>";
        foreach ($users->get_results() as $user) {
            if (isset($cols_class)) echo "<div class='$cols_class'>";
            include Template::locate("profile-cards/default.php", __DIR__.'/views');
            if (isset($cols_class)) echo "</div>";
        }
        if ($cols > 0) echo "</div>";
        $total = $users->get_total();
        $contid = isset($params['sid']) ? "-{$params['sid']}" : '';
        if (isset($params['paging']) && (int)$params['paging'] == 1)
            echo wpdm_paginate_links($total, $items_per_page, $page, 'cp', array('async' => 1, 'container' => "#wpdm-authors{$contid}"));
    }

}
