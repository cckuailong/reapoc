<?php

class LikeBtnLikeButtonMostLikedWidget extends WP_Widget {

    public $liked_by_user = false;

    public $shortcode = 'likebtn_most_liked';

    // Default thumbnail size
    const TUMBNAIL_SIZE = 32;
    // Default number of items to show
    const NUMBER_OF_ITEMS = 5;

    public static $instance_default = array(
        'title' => 'Most Liked Content',
        'entity_name' => array(LIKEBTN_ENTITY_POST),
        'include_categories' => array(),
        'exclude_categories' => array(),
        'author' => '',
        'number' => self::NUMBER_OF_ITEMS,
        'order' => 'likes',
        'time_range' => 'all',
        'vote_time_range' => 'all',
        'title_length' => LIKEBTN_WIDGET_TITLE_LENGTH,
        'thumbnail_size' => 'thumbnail',
        'show_likes' => '',
        'show_dislikes' => '',
        'show_dislikes' => '',
        'show_thumbnail' => '1',
        'show_excerpt' => '',
        'show_date' => '',
        'show_author' => '',
        'show_button' => '',
        'show_button_use_entity' => '',
        'voter' => '',
        'empty_text' => 'No items liked yet.'
    );

    function __construct() {
        load_plugin_textdomain('likebtn-like-button', false, dirname(plugin_basename(__FILE__)) . '/languages');
        $widget_ops = array('description' => __('A list of the most liked posts, comments, etc', 'likebtn-like-button'));
        parent::__construct(false, __('(LikeBtn) Most Liked Content', 'likebtn-like-button'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance, $output = true) {
        if ($this->liked_by_user) {
            global $LikeBtnLikeButtonMostLikedByUser;
            $html = $LikeBtnLikeButtonMostLikedByUser->widget($args, $instance);
        } else {
            global $LikeBtnLikeButtonMostLiked;
            $html = $LikeBtnLikeButtonMostLiked->widget($args, $instance);
        }
        
        if (!empty($output)) {
            echo $html;
        } else {
            return $html;
        }
    }

    function form($instance) {
        global $likebtn_entities;

        // Enque scripts
        wp_register_script('select2', _likebtn_get_public_url().'js/jquery/select2/select2.js', array('jquery'), LIKEBTN_VERSION, true);
        wp_register_style('select2-css', _likebtn_get_public_url().'css/jquery/select2/select2.css', false, LIKEBTN_VERSION, 'all');
        wp_enqueue_script('select2');
        wp_enqueue_style('select2-css');
  
        $instance = $this->prepareInstance($instance);

        $widget_mnemonic = time()+mt_rand(0, 10000000);

        $likebtn_entities = _likebtn_get_entities(true, false, false);

        // Custom item
        $likebtn_entities[LIKEBTN_ENTITY_CUSTOM_ITEM] = __('Custom item');

        $order_list = array(
            'likes' => __('Likes', 'likebtn-like-button'),
            'dislikes' => __('Dislikes', 'likebtn-like-button'),
            'likes_minus_dislikes' => __('Likes minus dislikes', 'likebtn-like-button')
        );
        if ($this->liked_by_user) {
            $order_list = array('vote_date' => __('Vote date', 'likebtn-like-button')) + $order_list;
        }

        $thumbnail_size_list = array(
            'thumbnail' => __('Thumbnail', 'likebtn-like-button'),
            'medium' => __('Medium', 'likebtn-like-button'),
            'large' => __('Large', 'likebtn-like-button'),
            'full' => __('Full size', 'likebtn-like-button'),
        );

        $time_range_list = array(
            'all' => __('All time', 'likebtn-like-button'),
            '1' => __('1 day', 'likebtn-like-button'),
            '2' => __('2 days', 'likebtn-like-button'),
            '3' => __('3 days', 'likebtn-like-button'),
            '7' => __('1 week', 'likebtn-like-button'),
            '14' => __('2 weeks', 'likebtn-like-button'),
            '21' => __('3 weeks', 'likebtn-like-button'),
            '1m' => __('1 month', 'likebtn-like-button'),
            '2m' => __('2 months', 'likebtn-like-button'),
            '3m' => __('3 months', 'likebtn-like-button'),
            '6m' => __('6 months', 'likebtn-like-button'),
            '1y' => __('1 year', 'likebtn-like-button')
        );
        
        // Normalize instance
        if (!isset($instance['title'])) {
            $instance['title'] = __(self::$instance_default['title'], 'likebtn-like-button');
        }
        if (empty($instance['entity_name']) || !is_array($instance['entity_name'])) {
            $instance['entity_name'] = self::$instance_default['entity_name'];
        }
        if (empty($instance['include_categories']) || !is_array($instance['include_categories'])) {
            $instance['include_categories'] = self::$instance_default['include_categories'];
        }
        if (empty($instance['exclude_categories']) || !is_array($instance['exclude_categories'])) {
            $instance['exclude_categories'] = self::$instance_default['exclude_categories'];
        }
        if (empty($instance['number']) || (int)$instance['number'] < 1) {
            $instance['number'] = self::$instance_default['number'];
        }
        if (empty($instance['order'])) {
            $instance['order'] = self::$instance_default['order'];
        }
        if (empty($instance['time_range'])) {
            $instance['time_range'] = self::$instance_default['time_range'];
        }
        if (empty($instance['vote_time_range'])) {
            $instance['vote_time_range'] = self::$instance_default['vote_time_range'];
        }
        if (empty($instance['thumbnail_size'])) {
            $instance['thumbnail_size'] = self::$instance_default['thumbnail_size'];
        }
        if (empty($instance['title_length'])) {
            $instance['title_length'] = self::$instance_default['title_length'];
        }
        if (empty($instance['empty_text'])) {
            $instance['empty_text'] = __(self::$instance_default['empty_text'], 'likebtn-like-button');
        }

        ?>
        <div id="likebtn_widget_<?php echo $widget_mnemonic; ?>">
            <?php if (!_likebtn_is_stat_enabled()): ?>
                <p class="likebtn_error">
                    <?php echo strtr(__('Synchronization is not enabled — widget will not be functioning. Please %a_begin%enable synchronization%a_end% in order to use the widget.', 'likebtn-like-button'), 
                        array('%a_begin%'=>'<a href="'.admin_url().'admin.php?page=likebtn_settings">', '%a_end%'=>'</a>')); ?>
                </p>
            <?php endif ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'likebtn-like-button'); ?>:</label>
                <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" data-property="title" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('entity_name'); ?>"><?php _e('Items to show', 'likebtn-like-button'); ?>:</label><br/>

                <?php foreach ($likebtn_entities as $entity_name_value => $entity_title): ?>
                    <input type="checkbox" name="<?php echo $this->get_field_name('entity_name'); ?>[]" id="<?php echo $this->get_field_id('entity_name'); ?>_<?php echo $entity_name_value ?>" value="<?php echo $entity_name_value; ?>" <?php echo (in_array($entity_name_value, $instance['entity_name']) ? 'checked="checked"' : ''); ?> data-property="entity_name" /> <label for="<?php echo $this->get_field_id('entity_name'); ?>_<?php echo $entity_name_value ?>"><?php _e($entity_title, 'likebtn-like-button'); ?></label><br/>
                <?php endforeach ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('include_categories'); ?>"><?php _e('Allow items by category', 'likebtn-like-button'); ?>:</label><br/>
                <select multiple="multiple" id="<?php echo $this->get_field_id('include_categories'); ?>" name="<?php echo $this->get_field_name('include_categories'); ?>[]" class="likebtn_include_categories widefat" data-property="include_categories" autocomplete="off">
                    <?php
                    $categories = _likebtn_get_categories();

                    foreach ($categories as $category) {
                        $selected = (in_array($category->cat_ID, $instance['include_categories'])) ? 'selected="selected"' : '';
                        $option = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
                        $option .= $category->cat_name;
                        $option .= ' (' . $category->category_count . ')';
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('exclude_categories'); ?>"><?php _e('Exclude items by category', 'likebtn-like-button'); ?>:</label><br/>
                <select multiple="multiple" id="<?php echo $this->get_field_id('exclude_categories'); ?>" name="<?php echo $this->get_field_name('exclude_categories'); ?>[]" class="likebtn_exclude_categories widefat" data-property="exclude_categories" autocomplete="off">
                    <?php
                    $categories = _likebtn_get_categories();

                    foreach ($categories as $category) {
                        $selected = (in_array($category->cat_ID, $instance['exclude_categories'])) ? 'selected="selected"' : '';
                        $option = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
                        $option .= $category->cat_name;
                        $option .= ' (' . $category->category_count . ')';
                        $option .= '</option>';
                        echo $option;
                    }
                    ?>
                </select>
            </p>
            <?php if (!$this->liked_by_user): ?>
                <p>
                    <label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Filter by author (comma separated IDs)', 'likebtn-like-button'); ?>:</label><br/>
                    <input type="text" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" value="<?php echo $instance['author']; ?>" data-property="author" class="widefat"/>
                </p>
            <?php endif ?>
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of items to show:', 'likebtn-like-button'); ?></label>
                <input type="number" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $instance['number']; ?>" size="3" data-property="number" class="widefat" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order by:', 'likebtn-like-button'); ?></label>
                <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" data-property="order" >
                    <?php foreach ($order_list as $order_value => $order_name): ?>
                        <option value="<?php echo $order_value; ?>" <?php selected($order_value, $instance['order']); ?> ><?php _e($order_name, 'likebtn-like-button'); ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('time_range'); ?>"><?php _e('Item publication period:', 'likebtn-like-button'); ?></label>
                <select name="<?php echo $this->get_field_name('time_range'); ?>" id="<?php echo $this->get_field_id('time_range'); ?>" data-property="time_range" >
                    <?php foreach ($time_range_list as $time_range_value => $time_range_name): ?>
                        <option value="<?php echo $time_range_value; ?>" <?php selected($time_range_value, $instance['time_range']); ?> ><?php _e($time_range_name, 'likebtn-like-button'); ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('vote_time_range'); ?>"><?php _e('Votes period:', 'likebtn-like-button'); ?></label>
                <select name="<?php echo $this->get_field_name('vote_time_range'); ?>" id="<?php echo $this->get_field_id('vote_time_range'); ?>" data-property="vote_time_range" >
                    <?php foreach ($time_range_list as $time_range_value => $time_range_name): ?>
                        <option value="<?php echo $time_range_value; ?>" <?php selected($time_range_value, $instance['vote_time_range']); ?> ><?php _e($time_range_name, 'likebtn-like-button'); ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Max title length', 'likebtn-like-button'); ?>:</label>
                <input type="number" id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>" value="<?php echo $instance['title_length']; ?>" data-property="title_length" class="widefat" />
            </p>
            <p>
                <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_likes'); ?>" name="<?php echo $this->get_field_name('show_likes'); ?>" value="1" <?php checked($instance['show_likes']); ?> data-property="show_likes" />
                <label for="<?php echo $this->get_field_id('show_likes'); ?>"><?php _e('Display likes count', 'likebtn-like-button'); ?></label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_dislikes'); ?>" name="<?php echo $this->get_field_name('show_dislikes'); ?>" value="1" <?php checked($instance['show_dislikes']); ?> data-property="show_dislikes" />
                <label for="<?php echo $this->get_field_id('show_dislikes'); ?>"><?php _e('Display dislikes count', 'likebtn-like-button'); ?></label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['show_thumbnail']); ?> id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>" value="1" data-property="show_thumbnail" />
                <label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e('Display featured image', 'likebtn-like-button'); ?></label>
                <select name="<?php echo $this->get_field_name('thumbnail_size'); ?>" id="<?php echo $this->get_field_id('thumbnail_size'); ?>" data-property="thumbnail_size" class="widefat">
                    <?php foreach ($thumbnail_size_list as $thumbnail_size_value => $thumbnail_size_name): ?>
                        <option value="<?php echo $thumbnail_size_value; ?>" <?php selected($thumbnail_size_value, $instance['thumbnail_size']); ?> ><?php _e($thumbnail_size_name, 'likebtn-like-button'); ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['show_excerpt']); ?> id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" value="1" data-property="show_excerpt" />
                <label for="<?php echo $this->get_field_id('show_excerpt'); ?>"><?php _e('Display excerpt', 'likebtn-like-button'); ?></label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['show_date']); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" value="1" data-property="show_date" />
                <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Display item date', 'likebtn-like-button'); ?></label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['show_author']); ?> id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" value="1" data-property="show_author" />
                <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Display author', 'likebtn-like-button'); ?></label>
            </p>
            <p>
                <input class="checkbox" type="checkbox" <?php checked($instance['show_button']); ?> id="<?php echo $this->get_field_id('show_button'); ?>" name="<?php echo $this->get_field_name('show_button'); ?>" value="1" data-property="show_button" />
                <label for="<?php echo $this->get_field_id('show_button'); ?>"><?php _e('Display button and use settings from', 'likebtn-like-button'); ?></label>
                <select name="<?php echo $this->get_field_name('show_button_use_entity'); ?>" id="<?php echo $this->get_field_id('show_button_use_entity'); ?>" data-property="show_button_use_entity" class="widefat">
                    <?php foreach ($likebtn_entities as $entity_name_value => $entity_title): ?>
                        <option value="<?php echo $entity_name_value; ?>" <?php selected($entity_name_value, $instance['show_button_use_entity']); ?> ><?php _e($entity_title, 'likebtn-like-button'); ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Text when there are no items', 'likebtn-like-button'); ?>:</label>
                <input class="widefat" type="text" id="<?php echo $this->get_field_id('empty_text'); ?>" name="<?php echo $this->get_field_name('empty_text'); ?>" value="<?php echo $instance['empty_text']; ?>" data-property="empty_text" />
            </p>
            <p>
                <a href="javascript:likebtnPopup('<?php echo __('http://likebtn.com/en/', 'likebtn-like-button'); ?>wordpress-like-button-plugin#most_liked_template');void(0);"><?php _e('Need a custom template?', 'likebtn-like-button'); ?></a> | 
                <a href="javascript:likebtnWidgetShortcode('<?php echo $widget_mnemonic; ?>', '<?php echo $this->shortcode; ?>', '<?php _e('Please save widget first', 'likebtn-like-button'); ?>')"><?php _e('Get shortcode', 'likebtn-like-button'); ?></a> <small>▼</small>
            </p>
            <p id="likebtn_sc_wr_<?php echo $widget_mnemonic; ?>" class="likebtn_sc_wr">
                <textarea class="likebtn_input likebtn_disabled" rows="5" id="likebtn_sc_<?php echo $widget_mnemonic; ?>" readonly="readonly"></textarea>
            </p>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery("#likebtn_widget_<?php echo $widget_mnemonic ?> :input").on("keyup change", function(event) {
                    likebtnWidgetShortcode('<?php echo $widget_mnemonic ?>', '<?php echo $this->shortcode; ?>', '<?php _e('Please save widget first', 'likebtn-like-button'); ?>', true);
                });
                jQuery("#likebtn_widget_<?php echo $widget_mnemonic ?> select.likebtn_include_categories:first").select2();
                jQuery("#likebtn_widget_<?php echo $widget_mnemonic ?> select.likebtn_exclude_categories:first").select2();
            });
        </script>
        <?php
    }

    // Set default values
    public function prepareInstance($instance)
    {
        return self::prepareInstanceStatic($instance, self::$instance_default);
    }

    public static function prepareInstanceStatic($instance, $instance_default)
    {
        if (!is_array($instance)) {
            return $instance;
        }
        foreach ($instance_default as $field => $default_value) {
            if (!isset($instance[$field])) {
                if ($field == 'title') {
                    $instance['title'] = __($default_value, 'likebtn-like-button');
                } else {
                    $instance[$field] = '';
                }
            }
        }
        return $instance;
    }

}

class LikeBtnLikeButtonMostLikedByUserWidget extends LikeBtnLikeButtonMostLikedWidget {

    public $liked_by_user = true;
    public $shortcode = 'likebtn_liked_by_user';

    public static $instance_default = array(
        'title' => 'You Liked',
        'entity_name' => array(LIKEBTN_ENTITY_POST),
        'include_categories' => array(),
        'exclude_categories' => array(),
        'author' => '',
        'number' => self::NUMBER_OF_ITEMS,
        'order' => 'vote_date',
        'time_range' => 'all',
        'vote_time_range' => 'all',
        'title_length' => LIKEBTN_WIDGET_TITLE_LENGTH,
        'thumbnail_size' => 'thumbnail',
        'show_likes' => '',
        'show_dislikes' => '',
        'show_dislikes' => '',
        'show_thumbnail' => '1',
        'show_excerpt' => '',
        'show_date' => '',
        'show_author' => '',
        'show_button' => '',
        'show_button_use_entity' => '',
        'voter' => ''
    );

    function __construct() {
        load_plugin_textdomain('likebtn-like-button', false, dirname(plugin_basename(__FILE__)) . '/languages');
        $widget_ops = array('description' => __('Content liked by the current authenticated user', 'likebtn-like-button'));
        WP_Widget::__construct(false, __('(LikeBtn) Liked by User', 'likebtn-like-button'), $widget_ops);
    }

    // Set default values
    public function prepareInstance($instance)
    {
        return self::prepareInstanceStatic($instance, self::$instance_default);
    }
}

// Class to display widget on frontend
class LikeBtnLikeButtonMostLiked {

    const WIDGET_TYPE_GENERAL = 'LikeBtnLikeButtonMostLikedWidget';
    const WIDGET_TYPE_BY_USER = 'LikeBtnLikeButtonMostLikedByUserWidget';
    const WIDGET_TYPE_BY_UM_USER = 'LikeBtnLikeButtonMostLikedByUmUserWidget';

    public static $templates = array(
        self::WIDGET_TYPE_GENERAL => 'most-liked-widget.php',
        self::WIDGET_TYPE_BY_USER => 'liked-by-user-widget.php',
        self::WIDGET_TYPE_BY_UM_USER => 'um-liked-content.php',
    );

    public $type = '';

    function __construct($type = self::WIDGET_TYPE_GENERAL, $init = true) {
        $this->type = $type;
        if ($init) {
            add_action('widgets_init', array(&$this, 'init'));
        }
    }

    function init() {
        register_widget($this->type);
    }

    function widget($args, $instance = array()) {
        global $wpdb;
        global $likebtn_nonpost_entities;
        global $likebtn_bbp_post_types;

        $has_posts = false;
        $post_types_count = 0;

        if (is_array($args)) {
            extract($args);
        }

        if ($this->type != self::WIDGET_TYPE_BY_USER) {
            $instance = LikeBtnLikeButtonMostLikedWidget::prepareInstanceStatic($instance, LikeBtnLikeButtonMostLikedWidget::$instance_default);
        } else {
            $instance = LikeBtnLikeButtonMostLikedByUserWidget::prepareInstanceStatic($instance, LikeBtnLikeButtonMostLikedByUserWidget::$instance_default);
            $instance['voter'] = (int)get_current_user_id();
        }

        if (is_array($instance)) {
            extract($instance);
        }

        if (empty($instance['entity_name'])) {
            $instance['entity_name'] = array(LIKEBTN_ENTITY_POST);
        }
        if (empty($instance['include_categories'])) {
            $instance['include_categories'] = array();
        }
        if (empty($instance['exclude_categories'])) {
            $instance['exclude_categories'] = array();
        }

        // Author
        $author_in = '';
        if (!empty($instance['author'])) {
            $author_in = "'" . implode("','", explode(",", preg_replace("/[^0-9,]/", '', $instance['author']))) . "'";
        }

        // Add bbPress
        $nonpost_entities = $likebtn_nonpost_entities;
        $nonpost_entities[] = LIKEBTN_ENTITY_BBP_POST;
        foreach ($instance['entity_name'] as $entity_index => $entity_name) {
            $instance['entity_name'][$entity_index] = str_replace("'", '', trim($entity_name));

            if (!in_array($entity_name, $nonpost_entities)) {
                $has_posts = true;
            }
        }

        $query_limit = '';
        if (isset($instance['number']) && (int) $instance['number'] > 0) {
            $query_limit = "LIMIT " . (int) $instance['number'];
        }

        // getting the most liked content
        $query = '';

        // Posts
        if ($has_posts) {
            $query_post_types = "'" . implode("','", $instance['entity_name']) . "'";

            $query_include_categories = '';
            if (is_array($instance['include_categories']) && count($instance['include_categories'])) {
                $query_include_categories = "'" . implode("','", $instance['include_categories']) . "'";
            }

            $query_exclude_categories = '';
            if (is_array($instance['exclude_categories']) && count($instance['exclude_categories'])) {
                $query_exclude_categories = "'" . implode("','", $instance['exclude_categories']) . "'";
            }

            $query_attachment = '';
            if (in_array(LIKEBTN_ENTITY_ATTACHMENT, $instance['entity_name'])) {
                $query_attachment = " OR (p.post_type = 'attachment') ";
            }

            $query .= "
                 SELECT
                    DISTINCT p.ID as 'post_id',
                    p.post_title,
                    p.post_date,
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    p.post_type,
                    p.post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}postmeta pm_likes
                 LEFT JOIN {$wpdb->prefix}posts p
                     ON (p.ID = pm_likes.post_id)
                 LEFT JOIN {$wpdb->prefix}postmeta pm_dislikes
                     ON (pm_dislikes.post_id = pm_likes.post_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}postmeta pm_likes_minus_dislikes
                     ON (pm_likes_minus_dislikes.post_id = pm_likes.post_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "') ";
            if ($query_include_categories) {
                $query .= "
                    LEFT JOIN {$wpdb->term_relationships} t_rel ON (t_rel.object_id = p.ID) 
                    LEFT JOIN {$wpdb->term_taxonomy} t_tax ON (t_tax.term_taxonomy_id = t_rel.term_taxonomy_id)
                ";
            }
            $query .= "
                WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                    AND ((p.post_status = 'publish'
                    AND p.post_type in ({$query_post_types})) {$query_attachment}) 
            ";
            if ($query_include_categories) {
                $query .= " AND t_tax.term_id IN ({$query_include_categories})";
            }
            
            if ($query_exclude_categories) {

                $query .= " AND NOT EXISTS (
                    SELECT t_tax.term_id
                    FROM {$wpdb->term_relationships} t_rel
                    LEFT JOIN {$wpdb->term_taxonomy} t_tax ON (t_tax.term_taxonomy_id = t_rel.term_taxonomy_id)
                    WHERE t_rel.object_id = p.ID AND t_tax.term_id IN ({$query_exclude_categories})
                ) ";
            }
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND p.post_date >= '" . $this->timeRangeToDateTime($instance['time_range']) . "' ";
            }
            if ($author_in) {
                $query .= " AND p.post_author IN (" . $author_in . ") ";
            }
            $post_types_count++;
        }

        // Comments
        if (in_array(LIKEBTN_ENTITY_COMMENT, $instance['entity_name'])) {
            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query .= "
                 SELECT
                    p.comment_ID as 'post_id',
                    p.comment_content as post_title,
                    p.comment_date as 'post_date',
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    '".LIKEBTN_ENTITY_COMMENT."' as post_type,
                    '' as post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}commentmeta pm_likes
                 LEFT JOIN {$wpdb->prefix}comments p
                    ON (p.comment_ID = pm_likes.comment_id)
                 LEFT JOIN {$wpdb->prefix}commentmeta pm_dislikes
                    ON (pm_dislikes.comment_id = pm_likes.comment_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}commentmeta pm_likes_minus_dislikes
                     ON (pm_likes_minus_dislikes.comment_id = pm_likes.comment_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
                 WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "' 
                    AND p.comment_approved = 1 ";
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND comment_date >= '" . $this->timeRangeToDateTime($instance['time_range']) . "'";
            }
            if ($author_in) {
                $query .= " AND p.comment_author IN (" . $author_in . ") ";
            }
            $post_types_count++;
        }

        // Custom items
        if (in_array(LIKEBTN_ENTITY_CUSTOM_ITEM, $instance['entity_name'])) {
            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query_post_types = "'" . implode("','", $instance['entity_name']) . "'";
            $query .= "
                 SELECT
                    p.ID as 'post_id',
                    p.identifier as 'post_title',
                    '' as 'post_date',
                    p.likes,
                    p.dislikes,
                    p.likes_minus_dislikes,
                    '".LIKEBTN_ENTITY_CUSTOM_ITEM."' as 'post_type',
                    '' as 'post_mime_type',
                    url
                 FROM {$wpdb->prefix}".LIKEBTN_TABLE_ITEM." p
                 WHERE
                    1 = 1 ";
            $post_types_count++;
        }

        // BuddyPress Member
        if (_likebtn_is_bp_active() && in_array(LIKEBTN_ENTITY_BP_MEMBER, $instance['entity_name'])) {
            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query .= "
                 SELECT
                    p.ID as 'post_id',
                    p.display_name as post_title,
                    p.user_registered as 'post_date',
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    '" . LIKEBTN_ENTITY_BP_MEMBER . "' as post_type,
                    '' as post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}bp_xprofile_meta pm_likes
                 LEFT JOIN {$wpdb->prefix}users p
                    ON (p.ID = pm_likes.object_id AND pm_likes.object_type = '" . LIKEBTN_BP_XPROFILE_OBJECT_TYPE . "')
                 LEFT JOIN {$wpdb->prefix}bp_xprofile_meta pm_dislikes
                    ON (pm_dislikes.object_id = pm_likes.object_id AND pm_dislikes.object_type = '" . LIKEBTN_BP_XPROFILE_OBJECT_TYPE . "' AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}bp_xprofile_meta pm_likes_minus_dislikes
                    ON (pm_likes_minus_dislikes.object_id = pm_likes.object_id AND pm_likes_minus_dislikes.object_type = '" . LIKEBTN_BP_XPROFILE_OBJECT_TYPE . "' AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
                 WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "' 
                    AND p.user_status = 0 ";
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND p.user_registered >= '" . $this->timeRangeToDateTime($instance['time_range']) . "'";
            }
            $post_types_count++;
        }

        // BuddyPress Activities
        if (_likebtn_is_bp_active() && 
            (in_array(LIKEBTN_ENTITY_BP_ACTIVITY_POST, $instance['entity_name']) ||
            in_array(LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE, $instance['entity_name']) ||
            in_array(LIKEBTN_ENTITY_BP_ACTIVITY_COMMENT, $instance['entity_name']) ||
            in_array(LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC, $instance['entity_name']))
        ) {
            // Get main table collation
            $collation = '';
            $query_coll = "SHOW TABLE STATUS where name like '{$wpdb->prefix}posts'";
            $coll = $wpdb->get_row($query_coll);

            if (!empty($coll->Collation)) {
                $collation = $coll->Collation;
            }

            // Test collation
            $wpdb->last_error = null;
            $query_coll = "SELECT content COLLATE {$collation} FROM {$wpdb->prefix}bp_activity LIMIT 1";
            $coll = $wpdb->get_row($query_coll);
            if ($wpdb->last_error) {
                $collation = '';
            }

            $collate = '';
            if ($collation) {
                $collate = " COLLATE {$collation} ";
            }

            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query .= "
                SELECT 
                    p.id as 'post_id',
                    CONCAT( IF(p.action != '', p.action, IF(p.content !='', p.content, IF(p.primary_link != '', p.primary_link, p.type))), IF(p.content != '' && p.type != 'bbp_topic_create' && p.type != 'new_blog_post', CONCAT(': ', p.content), '') ) {$collate} as 'post_title',
                    p.date_recorded as 'post_date',
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    IF (p.type = 'bbp_topic_create',
                        '" . LIKEBTN_ENTITY_BP_ACTIVITY_TOPIC . "',
                        IF (p.type = 'new_blog_post',
                            '" . LIKEBTN_ENTITY_BP_ACTIVITY_POST . "',
                            '" . LIKEBTN_ENTITY_BP_ACTIVITY_UPDATE . "'
                        )
                    ) as post_type,
                    '' as post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}bp_activity_meta pm_likes
                 LEFT JOIN {$wpdb->prefix}bp_activity p
                     ON (p.id = pm_likes.activity_id)
                 LEFT JOIN {$wpdb->prefix}bp_activity_meta pm_dislikes
                     ON (pm_dislikes.activity_id = pm_likes.activity_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}bp_activity_meta pm_likes_minus_dislikes
                     ON (pm_likes_minus_dislikes.activity_id = pm_likes.activity_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
                 WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "' 
                    AND p.hide_sitewide = 0
                    AND p.is_spam = 0 ";
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND p.user_registered >= '" . $this->timeRangeToDateTime($instance['time_range']) . "'";
            }
            if ($author_in) {
                $query .= " AND p.user_id IN (" . $author_in . ") ";
            }
            $post_types_count++;
        }

        // bbPress Post
        if (_likebtn_is_bbp_active() && in_array(LIKEBTN_ENTITY_BBP_POST, $instance['entity_name'])) {
            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query .= "
                 SELECT
                    p.ID as 'post_id',
                    IF (p.post_title != '', p.post_title, p.post_content) as post_title,
                    p.post_date,
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    '".LIKEBTN_ENTITY_BBP_POST."' as post_type,
                    p.post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}postmeta pm_likes
                 LEFT JOIN {$wpdb->prefix}posts p
                     ON (p.ID = pm_likes.post_id)
                 LEFT JOIN {$wpdb->prefix}postmeta pm_dislikes
                     ON (pm_dislikes.post_id = pm_likes.post_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}postmeta pm_likes_minus_dislikes
                     ON (pm_likes_minus_dislikes.post_id = pm_likes.post_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
                 WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "'
                    AND p.post_type in ('".implode("', '", $likebtn_bbp_post_types)."') 
                    AND p.post_status = 'publish' ";
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND post_date >= '" . $this->timeRangeToDateTime($instance['time_range']) . "'";
            }
            if ($author_in) {
                $query .= " AND p.post_author IN (" . $author_in . ") ";
            }
            $post_types_count++;
        }

        // bbPress User
        // UM User
        $user_post_type = '';
        if (_likebtn_is_bbp_active() && in_array(LIKEBTN_ENTITY_BBP_USER, $instance['entity_name'])) {
            $user_post_type = LIKEBTN_ENTITY_BBP_USER;
        }
        if (in_array(LIKEBTN_ENTITY_UM_USER, $instance['entity_name'])) {
            $user_post_type = LIKEBTN_ENTITY_UM_USER;
        }
        if ($user_post_type) {
            if ($post_types_count > 0) {
                $query .= " UNION ";
            }
            $query .= "
                 SELECT
                    p.ID as 'post_id',
                    p.display_name as post_title,
                    p.user_registered as 'post_date',
                    CONVERT(pm_likes.meta_value, UNSIGNED INTEGER) as 'likes',
                    CONVERT(pm_dislikes.meta_value, UNSIGNED INTEGER) as 'dislikes',
                    CONVERT(pm_likes_minus_dislikes.meta_value, SIGNED INTEGER) as 'likes_minus_dislikes',
                    '" . $user_post_type . "' as post_type,
                    '' as post_mime_type,
                    '' as url
                 FROM {$wpdb->prefix}usermeta pm_likes
                 LEFT JOIN {$wpdb->prefix}users p
                    ON (p.ID = pm_likes.user_id)
                 LEFT JOIN {$wpdb->prefix}usermeta pm_dislikes
                    ON (pm_dislikes.user_id = pm_likes.user_id AND pm_dislikes.meta_key = '" . LIKEBTN_META_KEY_DISLIKES . "')
                 LEFT JOIN {$wpdb->prefix}usermeta pm_likes_minus_dislikes
                    ON (pm_likes_minus_dislikes.user_id = pm_likes.user_id AND pm_likes_minus_dislikes.meta_key = '" . LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES . "')
                 WHERE
                    pm_likes.meta_key = '" . LIKEBTN_META_KEY_LIKES . "' 
                    AND p.user_status = 0 ";
            if (!empty($instance['time_range']) && $instance['time_range'] != 'all') {
                $query .= " AND p.user_registered >= '" . $this->timeRangeToDateTime($instance['time_range']) . "'";
            }
            $post_types_count++;
        }

        if ((int)$instance['voter'] && !empty($instance['vote_time_range']) && $instance['vote_time_range'] != 'all') {
        
            $query = "
                SELECT 
                    post_id, post_title, post_date, post_type, post_mime_type, url,
                    count(case v.type when 1 then 1 else null end) as likes,
                    count(case v.type when -1 then 1 else null end) as dislikes,
                    sum(v.type) as likes_minus_dislikes,
                    v.created_at,
                    v.type
                FROM (".$query. ") main_query
            ";
            $query .= " 
                    INNER JOIN ".$wpdb->prefix.LIKEBTN_TABLE_VOTE." v ON v.identifier = CONCAT(main_query.post_type, '_', main_query.post_id) AND v.user_id = ".(int)$instance['voter']. " AND v.created_at >= '" . $this->timeRangeToDateTime($instance['vote_time_range']) . "' 
                    GROUP BY v.identifier
                ";
        } else if (!empty($instance['vote_time_range']) && $instance['vote_time_range'] != 'all') {
        
            $query = "
                SELECT 
                    post_id, post_title, post_date, post_type, post_mime_type, url,
                    count(case v.type when 1 then 1 else null end) as likes,
                    count(case v.type when -1 then 1 else null end) as dislikes,
                    sum(v.type) as likes_minus_dislikes
                FROM (".$query. ") main_query
            ";
            $query .= " 
                    INNER JOIN ".$wpdb->prefix.LIKEBTN_TABLE_VOTE." v ON v.identifier = CONCAT(main_query.post_type, '_', main_query.post_id) AND v.created_at >= '" . $this->timeRangeToDateTime($instance['vote_time_range']) . "' 
                    GROUP BY v.identifier
                ";
        } else if ((int)$instance['voter']) {
            $query = "SELECT * FROM (".$query. ") main_query";
            // WHERE (likes != 0 OR dislikes != 0)
            $query .= " 
                INNER JOIN ".$wpdb->prefix.LIKEBTN_TABLE_VOTE." v ON v.identifier = CONCAT(main_query.post_type, '_', main_query.post_id) AND v.user_id = ".(int)$instance['voter']. " 
                GROUP BY v.identifier
            ";
        } else if ($post_types_count > 1) {
            $query = "SELECT * FROM (".$query. ") main_query";
            //$query .= " WHERE (likes != 0 OR dislikes != 0) ";
        }

        $query .= "
            ORDER BY ";

        switch ($instance['order']) {
            default:
            case 'likes':
                $query .= "likes";
                break;

            case 'dislikes':
                $query .= "dislikes";
                break;

            case 'likes_minus_dislikes':
                $query .= "likes_minus_dislikes";
                break;

            case 'vote_date':
                $query .= " v.created_at ";
                break;
        }

        $query .= " DESC";

        $query .= " {$query_limit}";


        $posts = $wpdb->get_results($query);

        if ($wpdb->last_error && strstr($wpdb->last_error, 'Illegal mix of collations')) {
            // Try to change votes table collation.
            $charset = '';
            $collation = '';
            $query_coll = "SHOW TABLE STATUS where name like '{$wpdb->prefix}posts'";
            $coll = $wpdb->get_row($query_coll);

            if (!empty($coll->Collation)) {
                $collation = $coll->Collation;
                $charset = preg_replace("/^([^_]+)_.*/", "$1", $collation);
            }
            if (!empty($charset) && !empty($collation)) {
                $sql = "ALTER TABLE {$wpdb->prefix}".LIKEBTN_TABLE_VOTE." CONVERT TO CHARACTER SET $charset COLLATE '$collation';";
                $wpdb->query($sql);

                $posts = $wpdb->get_results($query);
            }
        }
        
        $post_loop = array();

        if (count($posts) > 0) {
            foreach ($posts as $i=>$db_post) {
                // Remove posts with zero values
                if ($db_post->likes == 0 && $db_post->dislikes == 0) {
                    continue;
                }

                $post = array(
                    'id' => $db_post->post_id,
                    'type' => $db_post->post_type,
                    'post_mime_type' => $db_post->post_mime_type,
                    'title' => '',
                    'link' => '',
                    'likes' => '',
                    'dislikes' => '',
                    'date' => '',
                    'excerpt' => '',
                    'author_id' => '',
                    'author_name' => '',
                    'button_html' => '',
                );

                if (empty($instance['title_length'])) {
                    $instance['title_length'] = LIKEBTN_WIDGET_TITLE_LENGTH;
                }
                // Title
                $post['title'] = _likebtn_prepare_title($db_post->post_type, $db_post->post_title, $instance['title_length']);

                // Link
                $post['link'] = _likebtn_get_entity_url($db_post->post_type, $db_post->post_id, $db_post->url);

                $post['likes'] = $db_post->likes;
                $post['dislikes'] = $db_post->dislikes;

                if ($show_date) {
                    $post['date'] = strtotime($db_post->post_date);
                }

                if ($show_author) {
                    $author_id = _likebtn_get_author_id($db_post->post_type, $db_post->post_id);
                    if ($author_id) {
                        $post['author_id'] = $author_id;
                        $post['author_name'] = _likebtn_get_entity_title(LIKEBTN_ENTITY_USER, $author_id);
                    }
                }

                if (!empty($show_button) && !empty($show_button_use_entity)) {
                    $post['button_html'] = _likebtn_get_markup($db_post->post_type, $db_post->post_id, array(), $show_button_use_entity, true, false);
                }

                if ($show_excerpt) {
                    $post['excerpt'] = _likebtn_get_entity_excerpt($db_post->post_type, $db_post->post_id);
                }

                // For bbPress replies
                if (!$post['title']) {
                    $post['title'] = _likebtn_shorten_title($post['excerpt'], $instance['title_length']);
                }

                if ((int)$instance['voter']) {
                    $post['vote_date'] = $db_post->created_at;
                    $post['vote_type'] = (int)$db_post->type;
                }

                $post_loop[$i] = $post;
            }
        }

        $template = self::$templates[$this->type];

        // Get and include the template we're going to use
        ob_start();
		include(likebtn_get_template_hierarchy($template));
        $result = ob_get_contents();
        ob_get_clean();

        return $result;
    }

    function timeRangeToDateTime($range) {
        $day = 0;
        $month = 0;
        $year = 0;
        switch ($range) {
            case "1":
                $day = 1;
                break;
            case "2":
                $day = 2;
                break;
            case "3":
                $day = 3;
                break;
            case "7":
                $day = 7;
                break;
            case "14":
                $day = 14;
                break;
            case "21":
                $day = 21;
                break;
            case "1m":
                $month = 1;
                break;
            case "2m":
                $month = 2;
                break;
            case "3m":
                $month = 3;
                break;
            case "6m":
                $month = 6;
                break;
            case "1y":
                $year = 1;
                break;
        }

        $now_date_time = strtotime(date('Y-m-d H:i:s'));
        $range_timestamp = mktime(date('H', $now_date_time), date('i', $now_date_time), date('s', $now_date_time), date('m', $now_date_time) - $month, date('d', $now_date_time) - $day, date('Y', $now_date_time) - $year);

        return date('Y-m-d H:i:s', $range_timestamp);
    }
}

$LikeBtnLikeButtonMostLiked = new LikeBtnLikeButtonMostLiked();
$LikeBtnLikeButtonMostLikedByUser = new LikeBtnLikeButtonMostLiked(LikeBtnLikeButtonMostLiked::WIDGET_TYPE_BY_USER);
