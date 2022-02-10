<?php
if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('WPDM_NewDownloads')) {
    class WPDM_NewDownloads extends WP_Widget
    {
        /** constructor */
        function __construct()
        {
            parent::__construct(false, 'WPDM New Packages');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {
            global $post;
            extract($args);
            $title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : "";
            $sdc = isset($instance['sdc']) ? $instance['sdc'] : '';
            $nop = isset($instance['nop1']) ? $instance['nop1'] : 5;

            $newp = new \WP_Query(array('post_type' => 'wpdmpro', 'posts_per_page' => $nop, 'orderby' => 'date', 'order' => 'desc'));
            ?>
            <?php echo $before_widget; ?>
            <?php if ($title)
            echo $before_title . $title . $after_title;
            echo "<div class='w3eden'>";
            while ($newp->have_posts()) {
                $newp->the_post();

                $pack = (array)$post;
                echo wpdm_fetch_template($sdc, $pack);
            }
            echo "</div>";
            echo $after_widget;
            wp_reset_query();
        }

        /** @see WP_Widget::update */
        function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['sdc'] = strip_tags($new_instance['sdc']);
            $instance['nop1'] = strip_tags($new_instance['nop1']);
            return $instance;
        }

        /** @see WP_Widget::form */
        function form($instance)
        {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : "";
            $sdc = isset($instance['sdc']) ? esc_attr($instance['sdc']) : 'link-template-default.php';
            $nop = isset($instance['nop1']) ? esc_attr($instance['nop1']) : 5;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('nop1'); ?>"><?php _e('Number of packages to show:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('nop1'); ?>"
                       name="<?php echo $this->get_field_name('nop1'); ?>" type="text" value="<?php echo $nop; ?>"/>
            </p>
            <p>

                <label for="<?php echo $this->get_field_id('sdc'); ?>"><?php _e('Link Template:'); ?></label>
                <?php echo WPDM()->packageTemplate->dropdown(['name' => $this->get_field_name('sdc'), 'id' => $this->get_field_id('sdc'), 'selected' => $sdc], true); ?>
            </p>
            <?php
        }

    }
}
register_widget('WPDM_NewDownloads');
