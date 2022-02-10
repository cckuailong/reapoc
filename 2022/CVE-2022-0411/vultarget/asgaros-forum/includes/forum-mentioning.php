<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumMentioning {
    private $asgarosforum = null;
    private $regex_users = '#@([^\r\n\t\s\0<>\[\]!,\.\(\)\'\"\|\?\@]+)($|[\r\n\t\s\0<>\[\]!,\.\(\)\'\"\|\?\@])#isu';

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('asgarosforum_enqueue_css_js', array($this, 'enqueue_css_js'));
        add_filter('tiny_mce_before_init', array($this, 'add_mentioning_to_editor'));
        add_action('rest_api_init', array($this, 'initialize_routes'));
    }

    public function enqueue_css_js() {
        // Cancel if mentioning functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning']) {
            return;
        }

        // Cancel if mentioning-suggestions functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning_suggestions']) {
            return;
        }

        $themeurl = $this->asgarosforum->appearance->get_current_theme_url();

        wp_enqueue_script('jquery-caret', $this->asgarosforum->plugin_url.'libs/jquery.caret.js', array('jquery'), $this->asgarosforum->version, true);
        wp_enqueue_script('jquery-atwho', $this->asgarosforum->plugin_url.'libs/jquery.atwho.js', array('jquery', 'jquery-caret'), $this->asgarosforum->version, true);
        wp_enqueue_script('asgarosforum-js-mentioning', $this->asgarosforum->plugin_url.'js/script-mentioning.js', array('jquery', 'jquery-atwho', 'wp-api'), $this->asgarosforum->version, true);

        wp_enqueue_style('asgarosforum-css-mentioning', $themeurl.'/style-mentioning.css', array(), $this->asgarosforum->version);
    }

    // TinyMCE callback for mentionings.
    public function add_mentioning_to_editor($settings) {
        // Cancel if the current page-request is inside of the administration-area.
        if (is_admin()) {
            return $settings;
        }

        // Cancel if mentioning functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning']) {
            return $settings;
        }

        // Cancel if mentioning-suggestions functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning_suggestions']) {
            return $settings;
        }

        $settings['init_instance_callback'] = 'window.asgaros.suggestions_initialize';

        return $settings;
    }

    public function initialize_routes() {
        // Cancel if mentioning functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning']) {
            return;
        }

        // Cancel if mentioning-suggestions functionality is disabled.
        if (!$this->asgarosforum->options['enable_mentioning_suggestions']) {
            return;
        }

        register_rest_route(
            'asgaros-forum/v1',
            '/suggestions/mentioning/(?P<term>[a-zA-Z0-9-]+)',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'mentioning_callback'),
                'permission_callback' => '__return_true'
            )
        );
    }

    public function mentioning_callback($data) {
        // Build response-array.
        $response = array();
        $response['status'] = false;

        $user_query = array(
            'fields'          => array('ID', 'user_nicename', 'display_name'),
			'populate_extras' => false,
			'type'            => 'alphabetical',
			'page'            => 1,
			'per_page'        => 10,
			'search_terms'    => $data['term']
		);

        $user_query = new AsgarosForumUserQuery($user_query);
		$response['data'] = array();

		foreach ($user_query->results as $user) {
			$result          = new stdClass();
			$result->ID      = $user->user_nicename;
            $result->image   = get_avatar_url($user->ID, array('size' => 30));
			$result->name    = $user->display_name;

			$response['data'][] = $result;
		}

        if (!empty($response['data'])) {
            $response['status'] = true;
        }

        return new WP_REST_Response($response, 200);
    }

    public function render_nice_name($user_id) {
        if ($this->asgarosforum->options['enable_mentioning']) {
            $user_data = get_userdata($user_id);

            echo '<span class="mention-nice-name">@'.esc_html($user_data->user_nicename).'</span>';
        }
    }

    public function nice_name_to_link($content) {
        if ($this->asgarosforum->options['enable_mentioning']) {
            $content = preg_replace_callback($this->regex_users, array($this, 'create_link'), $content);
        }

        return $content;
    }

    private function create_link($match) {
        $link = $match[0];
        $user = get_user_by('slug', $match[1]);

        if ($user) {
            $link = $this->asgarosforum->renderUsername($user, '@'.$match[1]).$match[2];
        }

        return $link;
    }

    public function user_wants_notification($user_id) {
        $mention_user = get_user_meta($user_id, 'asgarosforum_mention_notify', true);

        if ($mention_user == 'no') {
            return false;
        }

        return true;
    }

    public function mention_users($post_id) {
        // Cancel if this functionality is not enabled.
        if (!$this->asgarosforum->options['enable_mentioning']) {
            return false;
        }

        // Return-variable which contains all receivers.
        $receivers = false;

        // Load required data.
        $post = $this->asgarosforum->content->get_post($post_id);
        $topic = $this->asgarosforum->content->get_topic($post->parent_id);
        $text = stripslashes($post->text);

        // Try to remove blockquotes to prevent unnecessary mentionings.
        // This functionality requires the libxml and dom extensions of PHP.
        if (extension_loaded('libxml') && extension_loaded('dom')) {
            // Enable search.
            $search = true;

            // Load the HTML-document.
            $document = new DOMDocument();
            libxml_use_internal_errors(true);
            $document->loadHTML($text);
            libxml_clear_errors();

            // A search will performed multiple times because otherwise nested
            // blockquotes caused by multiple quotes can not get removed correctly.
            do {
                // Look for blockquotes inside of the HTML-document.
                $elements = $document->getElementsByTagName('blockquote');

                if ($elements->length === 0) {
                    // Stop search when no blockquotes are found.
                    $search = false;
                } else {
                    // Remove the first found blockquote from the HTML-document.
                    $elements[0]->parentNode->removeChild($elements[0]);
                }
            } while ($search === true);

            // Get the cleaned-up HTML-document.
            $text = $document->saveHTML();
        }

        // Find mentioned users in the post-text.
        $matches = array();
        preg_match_all($this->regex_users, $text, $matches, PREG_SET_ORDER);

        if (!empty($matches)) {
            foreach ($matches as $match) {
                $user = get_user_by('slug', $match[1]);

                if ($user && $this->user_wants_notification($user->ID)) {
                    $this->asgarosforum->notifications->add_to_mailing_list($user->user_email);
                }
            }

            if (!empty($this->asgarosforum->notifications->mailing_list)) {
                // Set receivers-list.
                $receivers = $this->asgarosforum->notifications->mailing_list;

                // Get author-username.
                $author_name = $this->asgarosforum->getUsername($post->author_id);

                // Get post-link.
                $post_link = $this->asgarosforum->rewrite->get_post_link($post_id, $topic->id);

                // Prepare message-content.
                $message_content = wpautop(stripslashes($post->text));
                $message_content .= $this->asgarosforum->uploads->show_uploaded_files($post->id, $post->uploads);

                // Create mail content.
                $replacements = array(
                    '###AUTHOR###'  => $author_name,
                    '###LINK###'    => '<a href="'.$post_link.'">'.$post_link.'</a>',
                    '###TITLE###'   => esc_html(stripslashes($topic->name)),
                    '###CONTENT###' => $message_content
                );

                $notification_subject = $this->asgarosforum->options['mail_template_mentioned_subject'];
                $notification_message = $this->asgarosforum->options['mail_template_mentioned_message'];
                $notification_message = apply_filters('asgarosforum_filter_notify_mentioned_user_message', $notification_message, $replacements);

                // Send the notifications.
                $this->asgarosforum->notifications->send_notifications($this->asgarosforum->notifications->mailing_list, $notification_subject, $notification_message, $replacements);
            }
        }

        // Return all receivers.
        return $receivers;
    }
}
