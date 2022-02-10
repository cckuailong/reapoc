<?php
if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('WPDM_Tags')) {
    class WPDM_Tags extends \WP_Widget
    {
        /** constructor */
        function __construct()
        {
            parent::__construct(false, 'WPDM Tags');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {
            extract($args);
            $instance['title'] = isset($instance['title']) ? $instance['title'] : '';
            $title = apply_filters('widget_title', $instance['title']);
            $subt = isset($instance['subt']) ? $instance['subt'] : 'about ' . get_the_title();
            if (!is_singular('wpdmpro')) $subt = 'about ' . get_the_title();
            $parent = isset($instance['parent']) && $instance['parent'] > 0 ? intval($instance['parent']) : 0;
            $style = isset($instance['style']) ? esc_attr($instance['style']) : 'flat';
            $toplevel = isset($instance['toplevel']) ? esc_attr($instance['toplevel']) : 0;
            $category_page = isset($instance['category_page']) ? esc_attr($instance['category_page']) : 'showall';
            $notags = isset($instance['notags']) ? esc_attr($instance['notags']) : -1;


            $args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => true
            );

            $object = get_queried_object();
            if (is_singular())
                $terms = wp_get_post_terms(get_the_ID(), 'wpdmtag');
            else
                $terms = get_terms("wpdmtag", $args);


            if (is_array($terms)) {
                echo $before_widget;
                if ($title)
                    echo $before_title . $title . $after_title;
                if ($subt)
                    echo "<div class='subt'>{$subt}</div>";
                echo "<div class='w3eden'><div class='list-group_'>";
                $n = 0;

                foreach ($terms as $term) {
                    $n++;
                    if ($notags > 0 && $n > $notags) break;
                    echo "<a href='" . get_term_link($term) . "'  class='btn btn-outline-secondary btn-sm _list-group-item'>{$term->name}</a>\n";
                }

                do_action("wpdm_tag_widget");

                echo "</div></div>\n";

                echo $after_widget;
            }

        }

        /** @see WP_Widget::update */
        function update($new_instance, $old_instance)
        {
            return $new_instance;
        }

        /** @see WP_Widget::form */
        function form($instance)
        {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : "";
            $subt = isset($instance['subt']) ? esc_attr($instance['subt']) : "";
            $notags = isset($instance['notags']) ? esc_attr($instance['notags']) : -1;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('subt'); ?>"><?php _e('Sub Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('subt'); ?>"
                       name="<?php echo $this->get_field_name('subt'); ?>" type="text" value="<?php echo $subt; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('notags'); ?>"><?php _e('Number of tags to show:', 'download-manager'); ?></label><br/>
                <input type="number" name="<?php echo $this->get_field_name('notags'); ?>"
                       value="<?php echo $notags; ?>">
            </p>
            <?php
        }

    }
}
register_widget('WPDM_Tags');
