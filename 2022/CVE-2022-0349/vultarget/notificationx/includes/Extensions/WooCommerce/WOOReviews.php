<?php

/**
 * WooCommerce Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\WooCommerce;

use NotificationX\Core\Database;
use NotificationX\Core\Helper;
use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;
use NotificationX\Types\Conversions;
use NotificationX\Types\CustomNotification;
use NotificationX\Types\DownloadStats;

/**
 * WooCommerce Extension Class
 */
class WooReviews extends Extension {
    /**
     * Instance of WooReviews
     *
     * @var WooReviews
     */
    use GetInstance;
    use Woo;

    public $priority              = 10;
    public $id                    = 'woo_reviews';
    public $img                   = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/woocommerce.png';
    public $doc_link              = 'https://notificationx.com/docs/woocommerce-sales-notifications/';
    public $types                 = 'reviews';
    public $module                = 'modules_woocommerce';
    public $module_priority       = 3;
    public $class                 = '\WooCommerce';
    public $default_theme         = 'woo_reviews_total-rated';
    public $exclude_custom_themes = true;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title        = $this->title ?        $this->title        : __('WooCommerce', 'notificationx');
        $this->module_title = $this->module_title ? $this->module_title : __('WooCommerce', 'notificationx');
        $this->themes = [
            'total-rated'     => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/total-rated.png',
                'image_shape' => 'square',
                'template'  => [
                    'first_param'         => 'tag_rated',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('people rated', 'notificationx'),
                    'third_param'         => 'tag_product_title',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'reviewed'     => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/reviewed.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         =>  'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_product_title',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review_saying' => [
                'source'               => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/saying-review.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('saying', 'notificationx'),
                    'third_param'         => 'tag_title',
                    'custom_third_param'  => __('Excellent', 'notificationx'),
                    'review_fourth_param' => __('about', 'notificationx'),
                    'fifth_param'         => 'tag_product_title',
                    'sixth_param'         => 'tag_custom',
                    'custom_sixth_param'  => __('Try it now', 'notificationx'),
                ]
            ],
            'review-comment' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment.jpg',
                'image_shape' => 'rounded',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review-comment-2' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment-2.jpg',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review-comment-3' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment-3.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
        ];
        $this->templates = [
            'wp_reviews_template_new'  => [
                'first_param' => [
                    'tag_username' => __('Username', 'notificationx'),
                    'tag_rated'    => __('Rated', 'notificationx'),
                ],
                'third_param' => [
                    'tag_product_title' => __('Product Title', 'notificationx'),
                    'tag_plugin_review'   => __('Review', 'notificationx'),
                    'tag_anonymous_title' => __('Anonymous Title', 'notificationx'),
                ],
                'fourth_param' => [
                    'tag_rating'   => __('Rating', 'notificationx'),
                    'tag_time'     => __('Definite Time', 'notificationx'),
                ],
                '_themes' => [
                    'woo_reviews_total-rated',
                    'woo_reviews_reviewed',
                    'woo_reviews_review-comment',
                    'woo_reviews_review-comment-2',
                    'woo_reviews_review-comment-3',
                ],
            ],
            'review_saying_template_new' => [
                'first_param' => [
                    'tag_username' => __('Username', 'notificationx'),
                ],
                'third_param' => [
                    'tag_title'           => __('Review Title', 'notificationx'),
                    'tag_anonymous_title' => __('Anonymous Title', 'notificationx'),
                ],
                'fifth_param' => [
                    'tag_product_title' => __('Product Title', 'notificationx'),
                ],
                'sixth_param' => [
                    // @todo maybe add some predefined texts.
                ],
                '_themes' => [
                    'woo_reviews_review_saying',
                ],
            ],
        ];
        parent::__construct();
    }


    public function init() {
        parent::init();
        add_filter("nx_filtered_data_{$this->id}", array($this, 'rated_woo_review'), 10, 2);
        add_action('comment_post', array($this, 'post_comment'), 10, 2);
        add_action('trash_comment', array($this, 'delete_comment'), 10);
        add_action('deleted_comment', array($this, 'delete_comment'), 10);
        add_action('transition_comment_status', array($this, 'transition_comment_status'), 10, 3);
    }

    public function init_fields(){
        parent::init_fields();
        $this->_init_fields();
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
     *
     * @return void
     */
    public function public_actions() {
        parent::public_actions();

    }

    // @todo
    public function fallback_data($data, $saved_data, $settings) {
        $data['username'] = __('someone', 'notificationx');
        $data['plugin_name_text'] = __('try it out', 'notificationx');
        $data['anonymous_title'] = __('Anonymous', 'notificationx');
        $data['plugin_review'] = __('Some review content', 'notificationx');
        $data['rating']    = 5;

        if(!empty($saved_data['tag_plugin_name'])){
            $data['tag_product_title'] = $saved_data['tag_plugin_name'];
        }
        return $data;
    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=woocommerce&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf(
                    '%s <a href="%s" target="_blank">%s</a> %s',
                    __('You have to install', 'notificationx'),
                    $url,
                    __('WooCommerce', 'notificationx'),
                    __('plugin first.', 'notificationx')
                ),
                'html' => true,
                'type' => 'error',
                'rules' => Rules::is('source', $this->id),
            ];
        }
        return $messages;
    }

    /**
     * Image Action
     */
    public function image_action() {
    }

    // @todo
    public function notification_image($image_data, $data, $settings) {
        if (!$settings['show_default_image'] && $settings['show_notification_image'] === 'featured_image') {
            if (!empty($data['product_id']) && has_post_thumbnail($data['product_id'])) {
                $product_image = wp_get_attachment_image_src(
                    get_post_thumbnail_id($data['product_id']),
                    '_nx_notification_thumb',
                    false
                );
                $image_data['url'] = is_array($product_image) ? $product_image[0] : '';
            }
        }

        $alt_title = isset($data['plugin_name']) ? $data['plugin_name'] : '';
        $alt_title = empty($alt_title) && isset($data['username']) ? $data['username'] : $alt_title;
        $image_data['alt'] = $alt_title;
        return $image_data;
    }

    // @todo convert to nx_frontend_get_entries
    public function rated_woo_review($entries, $settings) {
        if ($settings['notification-template']['first_param'] !== 'tag_rated') {
            return $entries;
        }

        $from = isset($settings['display_from']) ? intval($settings['display_from']) : 0;
        $needed = isset($settings['display_last']) ? intval($settings['display_last']) : 0;

        $ratings = [];
        foreach ($entries as $key => $item) {
            $rating = $item['rating'];
            if ($rating === '5') {
                if (isset($ratings[$item['product_id']])) {
                    $ratings[$item['product_id']] = [
                        'comment_ID' => $item['id'],
                        'rated' => ++$ratings[$item['product_id']]['rated'],
                        'image_data' => $item['image_data'],
                    ];
                } else {
                    $ratings[$item['product_id']] = [
                        'comment_ID' => $item['id'],
                        'rated' => 1,
                        'image_data' => $item['image_data'],
                    ];
                }
            }
        }
        $entries = [];
        foreach ($ratings as $key => $item) {
            $data               = $this->add($item['comment_ID']);
            $data['nx_id']      = $settings['nx_id'];
            $data['rated']      = $item['rated'];
            $data['image_data'] = $item['image_data'];

            $entries[] = $data;
        }

        return $entries;
    }

    /**
     * This function is responsible for making comment notifications ready if comments is approved.
     *
     * @param int $comment_ID
     * @param bool $comment_approved
     * @return void
     */
    public function post_comment($comment_ID, $comment_approved) {
        // @todo @mukul
        // if (count($this->notifications) === $this->cache_limit) {
        //     $sorted_data = NotificationX_Helper::sorter($this->notifications, 'key');
        //     array_pop($sorted_data);
        //     $this->notifications = $sorted_data;
        // }

        if (1 === $comment_approved) {
            $comment = $this->add($comment_ID);
            /**
             * Save the data to
             * notificationx_data ( options DB. )
             */
            $this->update_notification([
                'source'     => $this->id,
                'entry_key'  => $comment_ID,
                'data'       => $comment,
            ]);
        }
        return;
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
     * If a comment delete, than the notifications data set has to be updated as well.
     * this function is responsible for doing this.
     *
     * @param int $comment_ID
     * @param WP_Comment $comment
     * @return void
     */
    public function delete_comment($comment_ID) {
        $this->delete_notification($comment_ID);
    }

    public function saved_post($post, $data, $nx_id) {
        $this->delete_notification(null, $nx_id);
        $this->get_notification_ready($data);
    }

    /**
     * This function is responsible for making the notification ready for first time we make the notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready($data = array()) {
        if (!is_null($comments = $this->get_comments($data))) {
            $entries = [];
            foreach ($comments as $comment) {
                $entries[] = [
                    'nx_id'      => $data['nx_id'],
                    'source'     => $this->id,
                    'entry_key'  => $comment['id'],
                    'data'       => $comment,
                ];
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

        $from = isset($data['display_from']) ? intval($data['display_from']) : 0;
        $needed = isset($data['display_last']) ? intval($data['display_last']) : 0;

        $comments = get_comments([
            'status'     => 'approve',
            'number'     => $needed,
            'post_type'  => 'product',
            'date_query' => [
                'after'     => $from . ' days ago',
                'inclusive' => true,
            ]
        ]);

        if (empty($comments)) return null;
        $new_comments = [];
        foreach ($comments as $comment) {
            $new_comments[$comment->comment_ID] = $this->add($comment);
        }
        return $new_comments;
    }

    /**
     * This function is responsible for making ready the comments data!
     *
     * @param int|WP_Comment $comment
     * @return void
     */
    public function add($comment) {
        $comment_data = [];

        if (!$comment instanceof \WP_Comment) {
            $comment_id = intval($comment);
            $comment = get_comment($comment_id, 'OBJECT');
        }
        if ($comment->comment_type !== 'review') {
            return;
        }

        $comment_data['id']         = $comment->comment_ID;
        $comment_data['product_id'] = $comment->comment_post_ID;
        $comment_data['content']    = $comment->comment_content;
        $comment_data['link']       = get_comment_link($comment->comment_ID);
        $comment_data['title']      = get_the_title($comment->comment_post_ID);
        $comment_data['post_link']  = get_permalink($comment->comment_post_ID);
        $comment_data['timestamp']  = get_gmt_from_date($comment->comment_date);
        $comment_data['rating']     = get_comment_meta($comment->comment_ID, 'rating', true);

        // @todo move to somewhere.
        $comment_data['ip']  = $comment->comment_author_IP;

        if ($comment->user_id) {
            $comment_data['user_id']    = $comment->user_id;
            $user                       = get_userdata($comment->user_id);
            $comment_data['first_name'] = $user->first_name;
            $comment_data['last_name']  = $user->last_name;
            $comment_data['username']  = $user->display_name;
            $comment_data['name']       = $user->first_name . ' ' . mb_substr($user->last_name, 0, 1);
            $trimed = trim($comment_data['name']);
            if (empty($trimed)) {
                $comment_data['name'] = $user->user_nicename;
            }
        } else {
            $commenter_name = get_comment_author($comment->comment_ID);
            $comment_data['username'] = $commenter_name;
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

    public function doc() {
        return sprintf(__('<p>Make sure that you have <a target="_blank" href="%1$s">WooCommerce installed & activated</a> to use this campaign. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>🎦 Watch <a target="_blank" href="%3$s">video tutorial</a> to learn quickly</p>
		<p><strong>Recommended Blog:</strong></p>
		<p>🚀 How to <a target="_blank" href="%4$s">boost WooCommerce Sales</a> Using NotificationX</p>', 'notificationx'),
        'https://wordpress.org/plugins/woocommerce/',
        'https://notificationx.com/docs/woocommerce-product-reviews/',
        'https://www.youtube.com/watch?v=bHuaOs9JWvI',
        'https://wpdeveloper.com/ecommerce-sales-social-proof/'
        );
    }
}
