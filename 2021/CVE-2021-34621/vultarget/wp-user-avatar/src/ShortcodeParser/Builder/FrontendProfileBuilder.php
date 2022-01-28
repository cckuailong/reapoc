<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\ExtensionManager as EM;

class FrontendProfileBuilder
{
    /** @var \WP_User user_data */
    static private $user_data;

    /**
     * Define all front-end profile sub-shortcode.
     *
     * @param $user
     */
    public function __construct($user)
    {
        self::$user_data = $user;

        add_shortcode('profile-username', array($this, 'profile_username'));

        add_shortcode('profile-email', array($this, 'profile_email'));

        add_shortcode('profile-website', array($this, 'profile_website'));

        add_shortcode('profile-nickname', array($this, 'profile_nickname'));

        add_shortcode('profile-display-name', array($this, 'profile_display_name'));

        add_shortcode('profile-first-name', array($this, 'profile_first_name'));

        add_shortcode('profile-last-name', array($this, 'profile_last_name'));

        add_shortcode('profile-bio', array($this, 'profile_bio'));

        add_shortcode('profile-cpf', array($this, 'profile_custom_profile_field'));

        add_shortcode('profile-file', array($this, 'profile_user_uploaded_file'));

        add_shortcode('profile-cover-image-url', array($this, 'cover_image_url'));

        add_shortcode('profile-avatar-url', array($this, 'user_avatar_url'));
        // backward compat
        add_shortcode('user-avatar-url', array($this, 'user_avatar_url'));

        add_shortcode('profile-hide-empty-data', array($this, 'hide_empty_data'));

        add_shortcode('post-count', array($this, 'post_count'));
        add_shortcode('profile-post-count', array($this, 'post_count'));

        add_shortcode('comment-count', array($this, 'get_comment_count'));
        add_shortcode('profile-comment-count', array($this, 'get_comment_count'));

        add_shortcode('profile-post-list', array($this, 'author_post_list'));
        add_shortcode('profile-comment-list', array($this, 'author_comment_list'));

        add_shortcode('profile-author-posts-url', array($this, 'author_post_url'));

        add_shortcode('profile-date-registered', array($this, 'date_user_registered'));

        add_shortcode('jcarousel-author-posts', array($this, 'pp_jcarousel_author_posts'));

        /**
         * @param object $user WP_User object
         */
        do_action('ppress_register_profile_shortcode', $user);
    }

    public function date_user_registered()
    {
        return date('F jS, Y', strtotime(self::$user_data->user_registered));
    }

    public function author_post_url()
    {
        return get_author_posts_url(self::$user_data->ID);
    }

    public function author_post_list($attributes)
    {
        $attributes = shortcode_atts(array('limit' => 10), $attributes);

        $user_id = self::$user_data->ID;
        $limit   = absint($attributes['limit']);

        $cache_key = "pp_profile_post_list_{$user_id}_{$limit}";
        $output    = get_transient($cache_key);

        if ($output === false) {

            $posts = get_posts(array(
                'author'         => $user_id,
                'posts_per_page' => $limit,
                'offset'         => 0
            ));

            $output = '';

            if ( ! empty($posts)) {

                $output .= "<ul class='pp-user-post-list'>";
                /** @var \WP_Post $post */
                foreach ($posts as $post) {
                    $output .= sprintf('<li class="pp-user-post-item"><a href="%s"><h3 class="pp-post-item-head">%s</h3></a></li>', get_permalink($post->ID), $post->post_title);
                }

                $output .= "</ul>";

                set_transient($cache_key, $output, HOUR_IN_SECONDS);
            } else {

                $note = esc_html__('This user has not created any post.', 'wp-user-avatar');

                if (self::$user_data->ID == get_current_user_id()) {
                    $note = esc_html__('You have not created any post.', 'wp-user-avatar');
                }

                $output .= sprintf('<div class="pp-user-comment-no-item"><span>%s</span></div>', $note);
            }
        }

        return $output;
    }

    public function author_comment_list($attributes)
    {
        $attributes = shortcode_atts(array('limit' => 10), $attributes);

        $user_id = self::$user_data->ID;

        $limit = absint($attributes['limit']);

        $cache_key = "pp_profile_comments_list_{$user_id}_{$limit}";

        $output = get_transient($cache_key);

        if ($output === false) {

            $comments = get_comments([
                'number'      => $limit,
                'user_id'     => $user_id,
                'post_status' => ['publish'],
                'status'      => 'approve'
            ]);

            $output = '';

            if ( ! empty($comments)) {

                $output .= '<div class="pp-user-comment-list">';
                /** @var \WP_Comment $comment */
                foreach ($comments as $comment) {
                    $output .= '<div class="pp-user-comment-item">';
                    $output .= '<div class="pp-user-comment-item-link">';
                    $output .= sprintf(
                        '<a href="%s">%s</a>',
                        esc_url(get_comment_link($comment->comment_ID)),
                        get_comment_excerpt($comment->comment_ID)
                    );
                    $output .= '</div>';
                    $output .= '<div class="pp-user-comment-item-meta">';
                    $output .= sprintf('On <a href="%s">%s</a>', get_permalink($comment->comment_post_ID), get_the_title($comment->comment_post_ID));
                    $output .= '</div>';
                    $output .= '</div>';
                }

                $output .= "</div>";

                set_transient($cache_key, $output, HOUR_IN_SECONDS);

            } else {

                $note = esc_html__('This user has not made any comment.', 'wp-user-avatar');

                if (self::$user_data->ID == get_current_user_id()) {
                    $note = esc_html__('You have not made any comment.', 'wp-user-avatar');
                }

                $output .= sprintf('<div class="pp-user-comment-no-item"><span>%s</span></div>', $note);
            }
        }

        return $output;
    }

    /**
     * Profile username
     *
     * @return mixed
     */
    public function profile_username()
    {
        $capitalization = apply_filters('ppress_capitalize_username', true);

        $username = self::$user_data->user_login;

        $username = $capitalization ? ucwords($username) : $username;

        return apply_filters('ppress_profile_username', $username, self::$user_data);
    }


    /**
     * User email
     *
     * @return mixed
     */
    public function profile_email()
    {
        return apply_filters('ppress_profile_email', self::$user_data->user_email, self::$user_data);
    }

    /**
     * Return user avatar image url
     *
     * @param array $atts shortcode attributes
     *
     * @return string image url
     */
    public function user_avatar_url($atts)
    {
        $atts = shortcode_atts(
            array(
                'size' => '400',
                'url'  => '',
            ),
            $atts
        );

        $user_id = self::$user_data->ID;

        return apply_filters('ppress_profile_avatar_url', get_avatar_url($user_id, ['size' => $atts['size']]), self::$user_data);
    }

    /**
     * Return user cover image url
     *
     * @return string image url
     */
    public function cover_image_url()
    {
        $user_id = self::$user_data->ID;

        return apply_filters('ppress_profile_avatar_url', ppress_get_cover_image_url($user_id), self::$user_data);
    }

    /**
     * User website URL
     *
     * @return mixed
     */
    public function profile_website()
    {
        return apply_filters('ppress_profile_website', self::$user_data->user_url, self::$user_data);
    }

    /**
     * Nickname of user
     *
     * @return mixed
     */
    public function profile_nickname()
    {
        return apply_filters('ppress_profile_nickname', ucwords(self::$user_data->nickname), self::$user_data);
    }

    /**
     * Display name of profile
     *
     * @return mixed
     */
    public function profile_display_name($atts = false)
    {
        $display_name = self::$user_data->display_name;

        if ( ! empty($atts['format']) && ! empty(self::$user_data->first_name) && ! empty(self::$user_data->last_name)) {

            switch ($atts['format']) {
                case 'first_last_names':
                    $display_name = self::$user_data->first_name . ' ' . self::$user_data->last_name;
                    break;
                case 'last_first_names':
                    $display_name = self::$user_data->last_name . ' ' . self::$user_data->first_name;
                    break;
                case 'first_name_initial_l':
                    $display_name = self::$user_data->first_name . ' ' . self::$user_data->last_name[0];
                    break;
                case 'f_initial_last_name':
                    $display_name = self::$user_data->first_name[0] . ' ' . self::$user_data->last_name;
                    break;
            }
        }

        return apply_filters('ppress_profile_display_name', ucwords($display_name), self::$user_data);
    }

    /**
     * Profile first name
     *
     * @return mixed
     */
    public function profile_first_name()
    {
        return apply_filters('ppress_profile_first_name', ucwords(self::$user_data->first_name), self::$user_data);
    }


    /**
     * Last name of user.
     *
     * @return mixed
     */
    public function profile_last_name()
    {
        return apply_filters('ppress_profile_last_name', ucwords(self::$user_data->last_name), self::$user_data);
    }

    /**
     * Description/bio of user.
     *
     * @return mixed
     */
    public function profile_bio()
    {
        return apply_filters('ppress_profile_bio', make_clickable(wpautop(wp_kses_post(html_entity_decode(self::$user_data->description)))), self::$user_data);
    }

    /**
     * Custom profile data of user.
     *
     * @param $atts array shortcode attributes
     *
     * @return string
     */
    public function profile_custom_profile_field($atts)
    {
        if ( ! EM::is_enabled(EM::CUSTOM_FIELDS)) return '';

        $atts = shortcode_atts(
            array(
                'key' => '',
            ),
            $atts
        );

        $key = $atts['key'];

        if (empty($key)) return esc_html__('Field key is missing', 'wp-user-avatar');

        $data = self::$user_data->{$key};

        if (is_array($data)) {
            $data = implode(', ',
                array_filter($data, function ($val) {
                    return ! empty($val);
                })
            );
        }

        return apply_filters('ppress_profile_cpf', $data, self::$user_data);
    }

    public function profile_user_uploaded_file($atts)
    {
        $atts = ppress_normalize_attributes($atts);

        $atts = shortcode_atts(
            array(
                'key' => '',
                'raw' => false,
            ),
            $atts
        );

        $key = $atts['key'];

        $user_upload_data = get_user_meta(self::$user_data->ID, 'pp_uploaded_files', true);

        if (empty($user_upload_data)) return '';

        $filename = $user_upload_data[$key];

        if (empty($filename)) return '';

        $link = PPRESS_FILE_UPLOAD_URL . $filename;

        if ( ! empty($atts['raw']) && ($atts['raw'] === true || $atts['raw'] == 'true')) {
            $return = $link;
        } else {
            $return = "<a href='$link'>$filename</a>";
        }

        return apply_filters('ppress_profile_file', $return, self::$user_data);

    }

    /**
     * Return number of post written by a user
     *
     * @return int
     */
    public function post_count()
    {
        return apply_filters('ppress_profile_post_count', count_user_posts(self::$user_data->ID), self::$user_data, true);
    }

    public function hide_empty_data($atts, $content)
    {
        $atts = shortcode_atts(
            array(
                'field' => '',
            ),
            $atts
        );

        $key = ! empty($atts['field']) ? strip_tags($atts['field']) : '';

        switch ($key) {
            case 'username':
                $key = 'user_login';
                break;
            case 'email':
                $key = 'user_email';
                break;
            case 'website':
                $key = 'user_url';
                break;
            case 'nickname':
                $key = 'nickname';
                break;
            case 'display_name':
                $key = 'display_name';
                break;
            case 'first_name':
                $key = 'first_name';
                break;
            case 'last_name':
                $key = 'last_name';
                break;
        }

        if ( ! empty($key) && ! empty(self::$user_data->$key)) {
            return do_shortcode($content);
        }
    }

    /**
     * Return the total comment count made by a user
     */
    public function get_comment_count()
    {
        global $wpdb;
        $userId = self::$user_data->ID;

        $count = $wpdb->get_var('
             SELECT COUNT(comment_ID)
             FROM ' . $wpdb->comments . '
             WHERE user_id = "' . $userId . '" AND comment_type = "" AND comment_approved = 1');

        return apply_filters('ppress_profile_comment_count', $count, self::$user_data);
    }

    protected function jcarousel_css()
    {
        ob_start();
        ?>
        <style type="text/css">
            /* jcarousel responsive */
            a.jcarousel-control-next, a.jcarousel-control-prev {
                text-decoration: none !important;
            }

            .pp-jcarousel-wrapper ul li a {
                color: #5B5B5B;
                text-decoration: none
            }

            .pp-jcarousel-wrapper {
                color: #5B5B5B;
                background-color: #F5F5F5;
                margin: 20px auto;
                position: relative;
                border: 10px solid #fff;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 0 2px #999;
                -moz-box-shadow: 0 0 2px #999;
                box-shadow: 0 0 2px #999;
            }

            /** Carousel **/
            .ppjcarousel {
                position: relative;
                overflow: hidden;
                width: 100%;
            }

            .ppjcarousel ul {
                width: 20000em;
                position: relative;
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .ppjcarousel li {
                width: 200px;
                float: left;
                margin: 1px 3px 1px 0;
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }

            .ppjcarousel img {
                display: block;
                width: 100%;
                height: 100px !important;
            }

            .jc-no-post {
                font-family: inherit;
                font-size: 15px;
                padding: 5px;
                text-align: center;
            }

            .jc-title {
                font-family: inherit;
                font-size: 15px;
                word-wrap: break-word;
                padding: 5px;
                margin: 0 4px;
                text-align: center;
            }

            /** Carousel Controls **/
            .jcarousel-control-prev, .jcarousel-control-next {
                position: absolute;
                top: 50%;
                margin-top: -15px;
                width: 30px;
                height: 30px;
                text-align: center;
                background: #4E443C;
                color: #fff;
                text-decoration: none;
                text-shadow: 0 0 1px #000;
                font: 24px/27px Arial, sans-serif;
                -webkit-border-radius: 30px;
                -moz-border-radius: 30px;
                border-radius: 30px;
                -webkit-box-shadow: 0 0 4px #F0EFE7;
                -moz-box-shadow: 0 0 4px #F0EFE7;
                box-shadow: 0 0 4px #F0EFE7;
            }

            .jcarousel-control-prev {
                left: 15px;
            }

            .jcarousel-control-next {
                right: 15px;
            }

            /** Carousel Pagination **/
            .jcarousel-pagination {
                position: absolute;
                bottom: -40px;
                left: 50%;
                -webkit-transform: translate(-50%, 0);
                -ms-transform: translate(-50%, 0);
                -moz-transform: translate(-50%, 0);
                transform: translate(-50%, 0);
                margin: 0;
            }

            .jcarousel-pagination a {
                text-decoration: none;
                display: inline-block;
                font-size: 11px;
                height: 10px;
                width: 10px;
                line-height: 10px;
                background: #fff;
                color: #4E443C;
                border-radius: 10px;
                text-indent: -9999px;
                margin-right: 7px;
                -webkit-box-shadow: 0 0 2px #4E443C;
                -moz-box-shadow: 0 0 2px #4E443C;
                box-shadow: 0 0 2px #4E443C;
            }

            .jcarousel-pagination a.active {
                background: #4E443C;
                color: #fff;
                opacity: 1;
                -webkit-box-shadow: 0 0 2px #F0EFE7;
                -moz-box-shadow: 0 0 2px #F0EFE7;
                box-shadow: 0 0 2px #F0EFE7;
            }
        </style>
        <?php
        return ppress_minify_css(ob_get_clean());
    }

    /**
     * jCarousel author latest post slider
     *
     * @param $atts
     *
     * @return string
     */
    public function pp_jcarousel_author_posts($atts)
    {
        wp_enqueue_script('pp-jcarousel', PPRESS_ASSETS_URL . '/js/jcarousel.min.js', array('jquery'), PPRESS_VERSION_NUMBER, true);

        $atts = shortcode_atts(
            array(
                'count'   => 10,
                'default' => PPRESS_ASSETS_URL . '/images/frontend/jc_dft_img.jpg',
                'width'   => '',
            ),
            $atts
        );

        $posts = get_posts(
            array(
                'post_type'      => 'post',
                'posts_per_page' => (int)$atts['count'],
                'author'         => self::$user_data->ID,
            )
        );

        $default_img = $atts['default'];
        $width       = ! empty($atts['width']) ? ' style="width: ' . $atts['width'] . ';"' : null;

        ob_start();
        echo $this->jcarousel_css();
        ?>
        <div class="pp-jcarousel-wrapper"<?php echo $width; ?>>
            <div class="ppjcarousel">
                <?php
                if (empty($posts)) {
                    echo '<div class="jc-no-post">' . apply_filters('jcarousel_no_post', esc_html__('No post written yet.', 'wp-user-avatar')) . '</div>';
                } else {

                    echo '<ul>';
                    foreach ($posts as $post) {
                        $feature_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium', false);

                        $feature_img = isset($feature_img[0]) ? $feature_img[0] : false;

                        if ( ! $feature_img) {
                            $feature_img = $default_img;
                        }
                        ?>
                        <li>
                            <a href="<?php echo get_permalink($post->ID); ?>">
                                <img src="<?php echo $feature_img; ?>" alt="<?php echo $post->post_title; ?>">

                                <div class="jc-title"><?php echo $post->post_title; ?></div>
                            </a>
                        </li>
                        <?php
                    }
                    echo '</ul>';
                }
                ?>
            </div>

            <?php
            // hide jcarousel nav link if no post is found
            if ( ! empty($posts)) { ?>
                <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                <a href="#" class="jcarousel-control-next">&rsaquo;</a>
                <p class="jcarousel-pagination"></p>
            <?php } ?>
        </div>
        <?php
        return apply_filters('ppress_jcarousel_author_posts', ob_get_clean(), self::$user_data);
    }

    public static function get_instance($user = '')
    {
        static $instance = false;
        $user = isset($user) && ! empty($user) ? $user : wp_get_current_user();
        if ( ! $instance) {
            $instance = new self($user);
        }

        return $instance;
    }
}