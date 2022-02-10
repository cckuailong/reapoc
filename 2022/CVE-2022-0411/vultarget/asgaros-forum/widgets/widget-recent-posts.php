<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumRecentPosts_Widget extends WP_Widget {
    private $asgarosforum = null;

    public function __construct() {
        global $asgarosforum;
        $this->asgarosforum = $asgarosforum;
        $widget_ops = array('classname' => 'asgarosforumrecentposts_widget', 'description' => __('Shows recent posts in Asgaros Forum.', 'asgaros-forum'));
		parent::__construct('asgarosforumrecentposts_widget', __('Asgaros Forum: Recent Posts', 'asgaros-forum'), $widget_ops);
    }

    public function widget($args, $instance) {
        // Ensure that the correct location is set.
        $location_check = AsgarosForumWidgets::setUpLocation();

        if (!$location_check) {
            $output = __('The forum has not been configured correctly.', 'asgaros-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Ensure that there are accessible categories available.
        $available_categories = $this->asgarosforum->content->get_categories_ids();

        if (empty($available_categories)) {
            $output = __('No topics yet!', 'asgaros-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available categories.
        $available_categories = implode(',', $available_categories);

        // Ensure that there are accessible forums available.
        $available_forums = $this->asgarosforum->db->get_col("SELECT id FROM {$this->asgarosforum->tables->forums} WHERE parent_id IN ({$available_categories});");

        if (empty($available_forums)) {
            $output = __('No topics yet!', 'asgaros-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Ensure that there are forums available after applying possible filters.
        $forum_filters = !empty($instance['forum_filter']) ? $instance['forum_filter'] : array();

        if (!empty($forum_filters)) {
            $available_forums = array_intersect($available_forums, $forum_filters);
        }

        if (empty($available_forums)) {
            $output = __('No topics yet!', 'asgaros-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available forums.
        $available_forums = implode(',', $available_forums);

        // Try to get forum posts.
        $number = ($instance['number']) ? absint($instance['number']) : 3;
        $group = isset($instance['group_by_topic']) ? $instance['group_by_topic'] : true;

        $post_ids = array();

        if ($group) {
            $post_ids = $this->asgarosforum->db->get_col("SELECT MAX(p.id) AS id FROM {$this->asgarosforum->tables->posts} AS p LEFT JOIN {$this->asgarosforum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.forum_id IN({$available_forums}) AND t.approved = 1 GROUP BY p.parent_id ORDER BY MAX(p.id) DESC LIMIT {$number};");
        } else {
            $post_ids = $this->asgarosforum->db->get_col("SELECT p.id FROM {$this->asgarosforum->tables->posts} AS p LEFT JOIN {$this->asgarosforum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.forum_id IN({$available_forums}) AND t.approved = 1 ORDER BY p.id DESC LIMIT {$number};");
        }

        // Ensure that there are forum posts available.
        if (empty($post_ids)) {
            $output = __('No topics yet!', 'asgaros-forum');
            $this->widget_output($args, $instance, $output);
            return;
        }

        // Generate stringified list of available forums.
        $post_ids = implode(',', $post_ids);

        // Get post details.
        $elements = $this->asgarosforum->db->get_results("SELECT p.id, p.text, p.date, p.parent_id, p.author_id, t.name, (SELECT COUNT(*) FROM {$this->asgarosforum->tables->posts} WHERE parent_id = p.parent_id) AS post_counter FROM {$this->asgarosforum->tables->posts} AS p LEFT JOIN {$this->asgarosforum->tables->topics} AS t ON (t.id = p.parent_id) WHERE p.id IN ({$post_ids}) ORDER BY p.id DESC;");

        // Get options.
        $show_avatar = isset($instance['show_avatar']) ? $instance['show_avatar'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : false;

        // Get custom values.
        $title_length = apply_filters('asgarosforum_filter_widget_title_length', 33);
        $excerpt_length = apply_filters('asgarosforum_widget_excerpt_length', 66);
        $avatar_size = apply_filters('asgarosforum_filter_widget_avatar_size', 30);

        // Generate output.
        $output = '<div class="asgarosforum-widget">';

        foreach ($elements as $element) {
            $output .= '<div class="widget-element">';

            // Add avatars
            if ($show_avatar) {
                $output .= '<div class="widget-avatar">'.get_avatar($element->author_id, $avatar_size, '', '', array('force_display' => true)).'</div>';
            }

            $output .= '<div class="widget-content">';
                // Generate link.
                $page = ceil($element->post_counter / $this->asgarosforum->options['posts_per_page']);
                $link = $this->asgarosforum->get_link('topic', $element->parent_id, array('part' => $page), '#postid-'.$element->id);

                $output .= '<span class="post-link"><a href="'.$link.'" title="'.esc_html(stripslashes($element->name)).'">'.esc_html($this->asgarosforum->cut_string(stripslashes($element->name), $title_length)).'</a></span>';
                $output .= '<span class="post-author">'.__('by', 'asgaros-forum').'&nbsp;<b>'.$this->asgarosforum->getUsername($element->author_id).'</b></span>';

                if ($show_excerpt) {
                    $text = esc_html(stripslashes(strip_tags(strip_shortcodes($element->text))));
                    $text = $this->asgarosforum->cut_string($text, $excerpt_length);

                    if (!empty($text)) {
                        $output .= '<span class="post-excerpt">'.$text.'&nbsp;<a class="post-read-more" href="'.$link.'">'.__('Read More', 'asgaros-forum').'</a></span>';
                    }
                }

                $output .= '<span class="post-date">'.$this->asgarosforum->get_activity_timestamp($element->date).'</span>';

                $custom_content = apply_filters('asgarosforum_widget_recent_posts_custom_content', '', $element->id);
                $output .= $custom_content;

            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>';

        $this->widget_output($args, $instance, $output);
    }

    public function widget_output($args, $instance, $output) {
        // Generate title.
        $title = __('Recent Forum Posts', 'asgaros-forum');

        if ($instance['title']) {
            $title = $instance['title'];
        }

        // Generate final output.
        echo $args['before_widget'];
        echo $args['before_title'];
        echo $title;
        echo $args['after_title'];
        echo $output;
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Recent forum posts', 'asgaros-forum');
        $number = isset($instance['number']) ? absint($instance['number']) : 3;
        $show_avatar = isset($instance['show_avatar']) ? (bool)$instance['show_avatar'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? (bool)$instance['show_excerpt'] : false;
        $group_by_topic = isset($instance['group_by_topic']) ? (bool)$instance['group_by_topic'] : true;
        $forum_filter = isset($instance['forum_filter']) ? $instance['forum_filter'] : array();

		echo '<p>';
		echo '<label for="'.esc_attr($this->get_field_id('title')).'">'.esc_html__('Title:', 'asgaros-forum').'</label>';
		echo '<input class="widefat" id="'.esc_attr($this->get_field_id('title')).'" name="'.esc_attr($this->get_field_name('title')).'" type="text" value="'.esc_attr($title).'">';
		echo '</p>';

        echo '<p>';
		echo '<label for="'.esc_attr($this->get_field_id('number')).'">'.esc_html__('Number of topics to show:', 'asgaros-forum').'</label>&nbsp;';
		echo '<input class="tiny-text" id="'.esc_attr($this->get_field_id('number')).'" name="'.esc_attr($this->get_field_name('number')).'" type="number" step="1" min="1" value="'.esc_attr($number).'" size="3">';
		echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($show_avatar, true, false).' id="'.esc_attr($this->get_field_id('show_avatar')).'" name="'.esc_attr($this->get_field_name('show_avatar')).'">';
		echo '<label for="'.esc_attr($this->get_field_id('show_avatar')).'">'.esc_html__('Show avatars', 'asgaros-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($show_excerpt, true, false).' id="'.esc_attr($this->get_field_id('show_excerpt')).'" name="'.esc_attr($this->get_field_name('show_excerpt')).'">';
		echo '<label for="'.esc_attr($this->get_field_id('show_excerpt')).'">'.esc_html__('Show excerpt', 'asgaros-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<input class="checkbox" type="checkbox" '.checked($group_by_topic, true, false).' id="'.esc_attr($this->get_field_id('group_by_topic')).'" name="'.esc_attr($this->get_field_name('group_by_topic')).'">';
		echo '<label for="'.esc_attr($this->get_field_id('group_by_topic')).'">'.esc_html__('Group posts by topic', 'asgaros-forum').'</label>';
        echo '</p>';

        echo '<p>';
        echo '<label for="'.esc_attr($this->get_field_id('forum_filter')).'">'.esc_html__('Forum filter:', 'asgaros-forum').'</label>';
        echo '<select name="'.esc_attr($this->get_field_name('forum_filter')).'[]" id="'.esc_attr($this->get_field_id('forum_filter')).'" class="widefat" size="6" multiple>';

        // Generate list of available forums.
        $categories = $this->asgarosforum->content->get_categories(false);

        if ($categories) {
            foreach ($categories as $category) {
                $forums = $this->asgarosforum->get_forums($category->term_id, 0);

                if ($forums) {
                    echo '<option disabled="disabled">'.esc_html($category->name).':</option>';

                    foreach ($forums as $forum) {
                        if (in_array($forum->id, $forum_filter)) {
                            echo '<option value="'.esc_attr($forum->id).'" selected="selected">- '.esc_html($forum->name).'</option>';
                        } else {
                            echo '<option value="'.esc_attr($forum->id).'">- '.esc_html($forum->name).'</option>';
                        }

                        if ($forum->count_subforums > 0) {
                            $subforums = $this->asgarosforum->get_forums($category->term_id, $forum->id);

                            foreach ($subforums as $subforum) {
                                if (in_array($subforum->id, $forum_filter)) {
                                    echo '<option value="'.esc_attr($subforum->id).'" selected="selected">-- '.esc_html($subforum->name).'</option>';
                                } else {
                                    echo '<option value="'.esc_attr($subforum->id).'">-- '.esc_html($subforum->name).'</option>';
                                }
                            }
                        }
                    }
                }
            }
        }

        echo '</select>';
        echo '</p>';
	}

    public function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['number'] = (int)$new_instance['number'];
        $instance['show_avatar'] = isset($new_instance['show_avatar']) ? (bool)$new_instance['show_avatar'] : false;
        $instance['show_excerpt'] = isset($new_instance['show_excerpt']) ? (bool)$new_instance['show_excerpt'] : false;
        $instance['group_by_topic'] = isset($new_instance['group_by_topic']) ? (bool)$new_instance['group_by_topic'] : false;
        $instance['forum_filter'] = isset($new_instance['forum_filter']) ? $new_instance['forum_filter'] : array();
		return $instance;
	}
}
