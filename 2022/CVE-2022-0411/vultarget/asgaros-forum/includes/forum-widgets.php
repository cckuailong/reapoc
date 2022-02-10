<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumWidgets {
    private static $asgarosforum = null;

    public function __construct($object) {
        self::$asgarosforum = $object;

        add_action('widgets_init', array($this, 'initializeWidgets'));
    }

    public function initializeWidgets() {
        if (!self::$asgarosforum->options['require_login'] || is_user_logged_in()) {
            register_widget('AsgarosForumRecentPosts_Widget');
            register_widget('AsgarosForumRecentTopics_Widget');
            register_widget('AsgarosForumSearch_Widget');
        }
    }

    public static function setUpLocation() {
        $locationSetUp = self::$asgarosforum->shortcode->checkForShortcode();

        // Try to get the forum-location when it is not set correctly.
        if (!$locationSetUp) {
            $pageID = self::$asgarosforum->db->get_var('SELECT ID FROM '.self::$asgarosforum->db->prefix.'posts WHERE post_type = "page" AND (post_content LIKE "%[forum]%" OR post_content LIKE "%[Forum]%");');
            if ($pageID) {
                self::$asgarosforum->options['location'] = $pageID;
                self::$asgarosforum->rewrite->set_links();
                $locationSetUp = true;
            }
        }

        return $locationSetUp;
    }
}
