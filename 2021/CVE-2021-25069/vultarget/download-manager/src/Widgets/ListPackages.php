<?php
if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('ListPackages')) {
    class ListPackages extends WP_Widget
    {
        /** constructor */
        function __construct()
        {
            parent::__construct(false, 'WPDM Packages');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {
            global $post;
            extract($args);
            $title = apply_filters('widget_title', (isset($instance['title']) ? $instance['title'] : ''));
            $sdc3 = isset($instance['sdc3']) ? $instance['sdc3'] : '';
            $cat = isset($instance['scat']) ? $instance['scat'] : '';
            $nop = !isset($instance['nop1']) || $instance['nop1'] <= 0 ? 5 : $instance['nop1'];
            $html = "";
            $order_by = isset($instance['order_by']) ? $instance['order_by'] : 'publish_date';
            $order = isset($instance['order']) ? $instance['order'] : 'desc';
            $params = array('post_type' => 'wpdmpro', 'posts_per_page' => $nop, 'orderby' => $order_by, 'order' => $order);
            if ($cat > 0)
                $params['tax_query'] = array(array('taxonomy' => 'wpdmcategory', 'terms' => array($cat), 'field' => 'id'));
            if (strstr($order_by, "_wpdm_")) {
                $params['orderby'] = 'meta_value_num';
                $params['meta_key'] = $order_by;
            }
            $newp = new \WP_Query($params);
            //$params['orderby'] = 'meta_value_num';
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
            $instance['order_by'] = strip_tags($new_instance['order_by']);
            $instance['order'] = strip_tags($new_instance['order']);
            return $instance;
        }

        /** @see WP_Widget::form */
        function form($instance)
        {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
            $sdc3 = isset($instance['sdc3']) ? $instance['sdc3'] : 0;
            $scat = isset($instance['scat']) ? esc_attr($instance['scat']) : 0;
            $nop = isset($instance['nop1']) ? esc_attr($instance['nop1']) : 5;
            $order_by = isset($instance['order_by']) ? $instance['order_by'] : '';
            $order = isset($instance['order']) ? $instance['order'] : '';

            $args = array(
                'show_option_all' => 'All Categories',
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
                <strong><label><?php echo __("On Single Download Page", "download-manager"); ?>:</label></strong><br/>
                <label><input type="checkbox" value="1" name="<?php echo $this->get_field_name('cpc'); ?>"
                              id="<?php echo $this->get_field_id('cpc'); ?>"> <?php _e("Show packages from current package category", "download-manager") ?>
                </label>

            </p>
            <p>
                <strong>Order:</strong><br/>
                <select id="plob" style="margin-right: 5px" name="<?php echo $this->get_field_name('order_by'); ?>">
                    <option value="date">Order By:</option>
                    <option value="date" <?php selected('date', $order_by); ?>>Publish Date</option>
                    <option value="post_title" <?php selected('post_title', $order_by); ?>>Title</option>
                    <option value="__wpdm_download_count" <?php selected('__wpdm_download_count', $order_by); ?>>
                        Downloads
                    </option>
                    <option value="__wpdm_package_size_b" <?php selected('__wpdm_package_size_b', $order_by); ?>>Package
                        Size
                    </option>
                    <option value="__wpdm_view_count" <?php selected('__wpdm_view_count', $order_by); ?>>Views</option>
                    <option value="modified" <?php selected('modified', $order_by); ?>>Update Date</option>
                </select><select id="plobs" style="margin-right: 5px"
                                 name="<?php echo $this->get_field_name('order'); ?>">
                    <option value="asc">Order:</option>
                    <option value="asc" <?php selected('asc', $order); ?>>Asc</option>
                    <option value="desc" <?php selected('desc', $order); ?>>Desc</option>
                </select>
            </p>
            <p>
                <strong><label
                            for="<?php echo $this->get_field_id('nop1'); ?>"><?php _e("Number of packages to show:", "download-manager"); ?></label></strong>
                <input class="widefat form-control" id="<?php echo $this->get_field_id('nop1'); ?>"
                       name="<?php echo $this->get_field_name('nop1'); ?>" type="text" value="<?php echo $nop; ?>"/>
            </p>
            <p>

                <strong><label
                            for="<?php echo $this->get_field_id('sdc3'); ?>"><?php _e("Link Template:", "download-manager"); ?></label></strong>
                <?php echo WPDM()->packageTemplate->dropdown(['name' => $this->get_field_name('sdc3'), 'id' => $this->get_field_id('sdc3'), 'selected' => $sdc3], true); ?>


            </p>
            <?php
        }

    }
}
register_widget('ListPackages');
