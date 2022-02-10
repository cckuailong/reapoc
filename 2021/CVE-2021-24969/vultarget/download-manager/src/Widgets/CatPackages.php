<?php
if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('CatPackages')) {
    class CatPackages extends WP_Widget
    {
        /** constructor */
        function __construct()
        {
            parent::__construct(false, 'WPDM Packages by Category');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {
            global $post;
            extract($args);
            $title = apply_filters('widget_title', $instance['title']);
            $sdc3 = $instance['sdc3'];
            $cat = $instance['scat'];
            $nop = $instance['nop1'] <= 0 ? 5 : $instance['nop1'];
            $html = "";
            $newp = new \WP_Query(array('post_type' => 'wpdmpro', 'posts_per_page' => $nop, 'order_by' => 'publish_date', 'order' => 'desc',
                'tax_query' => array(array('taxonomy' => 'wpdmcategory', 'terms' => array($cat), 'field' => 'id'))));

            ?>
            <?php echo $before_widget; ?>
            <?php if ($title)
            echo $before_title . $title . $after_title;
            echo "<div class='w3eden'>";
            while ($newp->have_posts()) {
                $newp->the_post();

                $pack = (array)$post;
                echo wpdm_fetch_template($sdc3, $pack);
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
            $instance['sdc3'] = strip_tags($new_instance['sdc3']);
            $instance['scat'] = strip_tags($new_instance['scat']);
            $instance['nop1'] = strip_tags($new_instance['nop1']);
            return $instance;
        }

        /** @see WP_Widget::form */
        function form($instance)
        {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
            $sdc3 = isset($instance['sdc3']) ? $instance['sdc3'] : 0;
            $scat = isset($instance['scat']) ? esc_attr($instance['scat']) : 0;
            $nop = isset($instance['nop1']) ? esc_attr($instance['nop1']) : 5;
            $args = array(
                'show_option_all' => '',
                'show_option_none' => '',
                'orderby' => 'ID',
                'order' => 'ASC',
                'show_count' => 0,
                'hide_empty' => 1,
                'child_of' => 0,
                'exclude' => '',
                'echo' => true,
                'selected' => $scat,
                'hierarchical' => 0,
                'name' => $this->get_field_name('scat'),
                'id' => '',
                'class' => 'postform widefat',
                'depth' => 0,
                'tab_index' => 0,
                'taxonomy' => 'wpdmcategory',
                'hide_if_empty' => false,
                'walker' => ''
            );
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('scat'); ?>"><?php _e("Select Category:", "download-manager"); ?></label>
                <?php wp_dropdown_categories($args); ?>

            </p>
            <p>
                <label for="<?php echo $this->get_field_id('nop1'); ?>"><?php _e("Number of packages to show:", "download-manager"); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('nop1'); ?>"
                       name="<?php echo $this->get_field_name('nop1'); ?>" type="text" value="<?php echo $nop; ?>"/>
            </p>
            <p>

                <label for="<?php echo $this->get_field_id('sdc3'); ?>"><?php _e("Link Template:", "download-manager"); ?></label>
                <?php echo WPDM()->packageTemplate->dropdown(['name' => $this->get_field_name('sdc3'), 'id' => $this->get_field_id('sdc3'), 'selected' => $sdc3], true); ?>


            </p>
            <?php
        }

    }
}
register_widget('CatPackages');
