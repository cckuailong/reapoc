<?php

/**
 * WPComments Extension
 *
 * @package NotificationX\Extensions
 */

//  @todo trim comment to length

namespace NotificationX\Extensions\WordPress;

use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;

/**
 * WPComments Extension
 */
class WPComments extends Extension {
    /**
     * Instance of WPComments
     *
     * @var WPComments
     */
    use GetInstance;
    use WordPress;

    public $priority = 5;
    public $id       = 'wp_comments';
    public $img      = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/wp-comments.png';
    public $doc_link = 'https://notificationx.com/docs-category/configurations/';
    public $types    = 'comments';
    public $module   = 'modules_wordpress';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('WP Comments', 'notificationx');
        $this->module_title = __('WordPress', 'notificationx');
        parent::__construct();
    }



    public function init() {
        parent::init();
        add_action('comment_post', array($this, 'post_comment'), 10, 2);
        add_action('trash_comment', array($this, 'delete_comment'), 10, 2);
        add_action('deleted_comment', array($this, 'delete_comment'), 10, 2);
        add_action('transition_comment_status', array($this, 'transition_comment_status'), 10, 3);
    }

    /**
     * This functions is hooked
     *
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();
    }

    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     *
     * @return void
     */
    public function public_actions() {
        parent::public_actions();

        add_filter("nx_filtered_entry_{$this->id}", array($this, 'conversion_data'), 10, 2);
    }

    public function saved_post($post, $data, $nx_id) {
        $this->delete_notification(null, $nx_id);
        $this->get_notification_ready($data);
    }

    /**
     * This function responsible for making ready the notifications for the first time
     * we have made a notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready($data = array()) {
        if (!is_null($comments = $this->get_comments($data))) {
            $entries = [];
            foreach ($comments as $comment) {
                if ($comment) {
                    // $comment, $comment['id'], $data['nx_id']
                    $entries[] = [
                        'nx_id'      => $data['nx_id'],
                        'source'     => $this->id,
                        'entry_key'  => $comment['id'],
                        'data'       => $comment,
                    ];
                }
            }
            $this->update_notifications($entries);
        }
    }
    /**
     * This function is responsible for getting the comments from wp_comments data table.
     *
     * @param array $data
     * @return array
     */
    public function get_comments($data) {
        if (empty($data)) return null;

        global $wp_version;

        $from = isset($data['display_from']) ? intval($data['display_from']) : 0;
        $needed = isset($data['display_last']) ? intval($data['display_last']) : 0;

        $args = [
            'status'     => 'approve',
            'number'     => $needed,
            'date_query' => [
                'after' => $from . ' days ago',
                'inclusive' => true,
            ]
        ];

        if (version_compare($wp_version, '5.5', '==')) {
            $args['type'] = 'comment';
        }

        $comments = get_comments($args);

        if (empty($comments)) return null;
        $new_comments = [];
        foreach ($comments as $comment) {
            if ($_comment = $this->add($comment)) {
                $new_comments[$comment->comment_ID] = $_comment;
            }
        }
        return $new_comments;
    }
    /**
     * This function is responsible for transition comment status
     * from approved to unapproved or unapproved to approved
     *
     * @param string $new_status
     * @param string $old_status
     * @param WP_Comment $comment
     * @return void
     */
    public function transition_comment_status($new_status, $old_status, $comment) {
        if ('unapproved' === $new_status) {
            $this->delete_comment($comment->comment_ID, $comment);
        }
        if ('approved' === $new_status) {
            $this->delete_notification($comment->comment_ID);
            $this->post_comment($comment->comment_ID, 1);
        }
    }
    /**
     * This function is responsible for making comment notifications ready if comments is approved.
     *
     * @param int $comment_ID
     * @param bool $comment_approved
     * @return void
     */
    public function post_comment($comment_ID, $comment_approved) {
        if (1 === $comment_approved) {
            $comment = $this->add($comment_ID);
            if (is_array($comment)) {
                $this->update_notification([
                    'source'     => $this->id,
                    'entry_key'  => $comment['id'],
                    'data'       => $comment,
                ]);
            }
        }
        return;
    }

    /**
     * This function is responsible for making ready the comments data!
     *
     * @param int|WP_Comment $comment
     * @return void
     */
    public function add($comment) {
        global $wp_version;
        $comment_data = [];

        if (!$comment instanceof \WP_Comment) {
            $comment_id = intval($comment);
            $comment = get_comment($comment_id, 'OBJECT');
        }
        if (
            ($comment->comment_type !== '' && version_compare($wp_version, '5.5', '<')) ||
            ($comment->comment_type !== 'comment' && version_compare($wp_version, '5.5', '>='))
        ) {
            return;
        }

        $comment_data['id']         = $comment->comment_ID;
        $comment_data['link']       = get_comment_link($comment->comment_ID);
        $comment_data['post_title'] = get_the_title($comment->comment_post_ID);
        $comment_data['post_comment'] = $comment->comment_content;
        $comment_data['post_link']  = get_permalink($comment->comment_post_ID);
        $comment_data['timestamp']  = get_gmt_from_date($comment->comment_date);

        $comment_data['ip']  = $comment->comment_author_IP;

        if ($comment->user_id) {
            $comment_data['user_id']    = $comment->user_id;
            $user                       = get_userdata($comment->user_id);
            $comment_data['first_name'] = $user->first_name;
            $comment_data['last_name']  = $user->last_name;
            $comment_data['display_name']  = $user->display_name;
            $comment_data['name']       = $user->first_name . ' ' . mb_substr($user->last_name, 0, 1);
            $trimed = trim($comment_data['name']);
            if (empty($trimed)) {
                $comment_data['name'] = $user->user_nicename;
            }
        } else {
            $commenter_name = get_comment_author($comment->comment_ID);
            $comment_data['name'] = $commenter_name;
            $commenter_name = explode(' ', $commenter_name);
            if (isset($commenter_name[0])) {
                $comment_data['first_name'] = $commenter_name[0];
            }
            $commenter_count = count($commenter_name);
            if (isset($commenter_name[$commenter_count - 1])) {
                $comment_data['last_name'] = $commenter_name[$commenter_count - 1];
            }
        }
        $comment_data['email'] = get_comment_author_email($comment->comment_ID);
        return $comment_data;
    }

    /**
     * If a comment delete, than the notifications data set has to be updated as well.
     * this function is responsible for doing this.
     *
     * @param int $comment_ID
     * @param WP_Comment $comment
     * @return void
     */
    public function delete_comment($comment_ID, $comment) {
        $this->delete_notification($comment_ID);
    }

    // @todo
    public function fallback_data($data, $saved_data, $settings) {
        $data['name']           = __('Someone', 'notificationx');
        $data['first_name']     = __('Someone', 'notificationx');
        $data['last_name']      = __('Someone', 'notificationx');
        $data['display_name']   = __('Someone', 'notificationx');
        $data['anonymous_post'] = __('Anonymous Post', 'notificationx');
        $data['sometime']       = __('Some time ago', 'notificationx');
        $data['post_comment']   = __('Some comment', 'notificationx');
        return $data;
    }

    // @todo Frontend
    public function conversion_data($saved_data, $settings) {
        if (!empty($saved_data['post_comment'])) {
            $trim_length = 100;
            if ($settings['themes'] == 'comments_theme-seven-free' || $settings['themes'] == 'comments_theme-eight-free') {
                $trim_length = 80;
            }
            $nx_trimmed_length = apply_filters('nx_text_trim_length', $trim_length, $settings);
            $comment = $saved_data['post_comment'];
            if (strlen($comment) > $nx_trimmed_length) {
                $comment = substr($comment, 0, $nx_trimmed_length) . '...';
            }
            if ($settings['themes'] == 'comments_theme-seven-free') { // || $settings['theme'] == 'comments_theme-six-free'
                $comment = '" ' . $comment . ' "';
            }
            $saved_data['post_comment'] = $comment;
        }
        return $saved_data;
    }
}
