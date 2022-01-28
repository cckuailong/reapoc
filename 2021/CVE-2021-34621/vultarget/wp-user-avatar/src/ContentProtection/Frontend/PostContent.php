<?php

namespace ProfilePress\Core\ContentProtection\Frontend;


use ProfilePress\Core\ContentProtection\SettingsPage;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;

class PostContent
{
    public function __construct()
    {
        add_filter('the_content', [$this, 'the_content'], 9999999999999999999);
    }

    public function the_content($content)
    {
        $metas = PROFILEPRESS_sql::get_meta_data_by_key(SettingsPage::META_DATA_KEY);

        if (is_array($metas)) {

            foreach ($metas as $meta) {

                $meta = ppress_var($meta, 'meta_value', []);

                if ( ! in_array(ppress_var($meta, 'is_active', true), ['true', true], true)) continue;

                $access_condition = ppress_var($meta, 'access_condition', []);

                $noaccess_action = ppress_var($access_condition, 'noaccess_action');

                if ('message' != $noaccess_action) continue;

                $who_can_access = ppress_var($access_condition, 'who_can_access', 'everyone');

                $access_roles = ppress_var($access_condition, 'access_roles', []);

                $noaccess_message_type = ppress_var($access_condition, 'noaccess_action_message_type', 'global');

                $custom_message = ppress_var($access_condition, 'noaccess_action_message_custom', 'global');

                if (Checker::content_match($meta['content'])) {

                    if (Checker::is_blocked($who_can_access, $access_roles)) {
                        $content = $this->get_restricted_message($noaccess_message_type, $custom_message);
                    }

                    break;
                };
            }
        }

        return $content;
    }

    public function get_restricted_message($noaccess_message_type = 'global', $custom_message = '')
    {
        $message = '';

        $global_message = ppress_settings_by_key(
            'global_restricted_access_message',
            esc_html__('You are unauthorized to view this page.', 'wp-user-avatar'),
            true
        );

        switch ($noaccess_message_type) {
            case 'custom':
                $message = wpautop($custom_message);
                break;
            case 'post_excerpt':
                $message = $this->get_post_excerpt();
                break;
            case 'post_excerpt_global':
                $message = $this->get_post_excerpt() . $this->parse_message($global_message);
                break;
            case 'post_excerpt_custom':
                $message = $this->get_post_excerpt() . $this->parse_message($custom_message);
                break;
        }

        if (empty($message)) {
            $message = $this->parse_message($global_message);
        }

        return $message;
    }

    public function parse_message($message)
    {
        return do_shortcode(wpautop($message));
    }

    public function get_post_excerpt()
    {
        global $post;

        if ( ! is_object($post)) return false;

        $length = apply_filters('ppress_content_protection_excerpt_length', 100);

        $more = false;

        if (has_excerpt($post->ID)) {
            $the_excerpt = $post->post_excerpt;
        } elseif (strstr($post->post_content, '<!--more-->')) {
            $more        = true;
            $length      = strpos($post->post_content, '<!--more-->');
            $the_excerpt = $post->post_content;
        } else {
            $the_excerpt = $post->post_content;
        }

        $tags = apply_filters('ppress_content_protection_excerpt_tags', '<a><img><em><i><code><ins><del><strong><blockquote><ul><ol><li><h1><h2><h3><h4><h5><h6><b>');

        if ($more) {
            $the_excerpt = strip_shortcodes(strip_tags(stripslashes(substr($the_excerpt, 0, $length)), $tags));
        } else {
            $the_excerpt   = strip_shortcodes(strip_tags(stripslashes($the_excerpt), $tags));
            $the_excerpt   = preg_split('/\b/', $the_excerpt, $length * 2 + 1);
            $excerpt_waste = array_pop($the_excerpt);
            $the_excerpt   = implode($the_excerpt);

            if ( ! empty($the_excerpt)) {
                $the_excerpt .= apply_filters('ppress_content_protection_excerpt_extra', ' . . .');
            }
        }

        $the_excerpt = wpautop($the_excerpt);

        return apply_filters('ppress_content_protection_excerpt', $the_excerpt, $post, $length, $tags);
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}