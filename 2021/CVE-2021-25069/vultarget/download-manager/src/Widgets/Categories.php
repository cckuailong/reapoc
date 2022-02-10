<?php

use WPDM\Category\CategoryController;

if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('WPDM_Categories')) {
    class WPDM_Categories extends \WP_Widget
    {
        /** constructor */
        function __construct()
        {
            parent::__construct(false, 'WPDM Categories');
        }

        /** @see WP_Widget::widget */
        function widget($args, $instance)
        {
            extract($args);
            $instance['title'] = isset($instance['title']) ? $instance['title'] : '';
            $title = apply_filters('widget_title', $instance['title']);
            $parent = isset($instance['parent']) && $instance['parent'] > 0 ? intval($instance['parent']) : 0;
            $style = isset($instance['style']) ? esc_attr($instance['style']) : 'flat';
            $toplevel = isset($instance['toplevel']) ? esc_attr($instance['toplevel']) : 0;
            $category_page = isset($instance['category_page']) ? esc_attr($instance['category_page']) : 'showall';
            $hideon0 = isset($instance['hideon0']) ? esc_attr($instance['hideon0']) : 0;


            $args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
                'exclude' => array(),
                'exclude_tree' => array(),
                'include' => array(),
                'number' => '',
                'fields' => 'all',
                'slug' => '',
                'parent' => '',
                'hierarchical' => ($style == 'flat' ? false : true),
                'child_of' => $parent,
                'childless' => false,
                'get' => '',
                'name__like' => '',
                'description__like' => '',
                'pad_counts' => false,
                'offset' => '',
                'search' => '',
                'cache_domain' => 'core'
            );

            $object = get_queried_object();
            if ($category_page == 'subs' && !isset($object->post_type))
                $args['parent'] = get_queried_object_id();

            $terms = get_terms("wpdmcategory", $args);

            if ($hideon0 == 1 && count($terms) == 0) {
                return;
            }

            echo $before_widget;
            if ($title)
                echo $before_title . $title . $after_title;

            if ($style == 'flat') {
                echo "<div class='w3eden'><div class='list-group'>";
                foreach ($terms as $term) {
                    if (($toplevel == 1 && ($term->parent == 0 || $term->parent === $parent)) || $toplevel == 0)
                        echo "<a href='" . get_term_link($term) . "'  class='list-group-item d-flex justify-content-between align-items-center'>{$term->name}<span class='badge'>{$term->count}</span></a>\n";
                }

                echo "</div></div>\n";
            } else if ($style == 'icon') {


                echo "<div class='wpdm-categories icon-list'>";
                foreach ($terms as $term) {
                    $icon = CategoryController::icon($term->term_id);
                    if ($icon == '') $icon = "https://cdn1.iconfinder.com/data/icons/gradient-android-apps/64/1-05-512.png";
                    if (($toplevel == 1 && ($term->parent == 0 || $term->parent === $parent)) || $toplevel == 0)
                        echo "<div class='icon-cat'><a href='" . get_term_link($term) . "'><img src='{$icon}' class='cat-icon' /> {$term->name}</a></div>\n";
                }
                echo "</div>";
            } else {

                function wpdm_categories_tree($parent = 0, $selected = array())
                {
                    $categories = get_terms('wpdmcategory', array('hide_empty' => 0, 'parent' => $parent));
                    $checked = "";
                    foreach ($categories as $category) {
                        if ($selected) {
                            foreach ($selected as $ptype) {
                                if ($ptype->term_id == $category->term_id) {
                                    $checked = "checked='checked'";
                                    break;
                                } else $checked = "";
                            }
                        }
                        echo '<li><a href="' . get_term_link($category) . '"> ' . $category->name . ' </a>';
                        $termchildren = get_term_children($category->term_id, 'wpdmcategory');
                        if ($termchildren) {
                            echo "<ul>";
                            wpdm_categories_tree($category->term_id, $selected);
                            echo "</ul>";
                        }
                        echo "</li>";
                    }
                }

                echo "<ul class='wpdm-categories'>";
                $cparent = $parent;
                if ($cparent !== 0) {
                    $cparent = get_term_by('slug', $cparent, 'wpdmcategory');
                    $cparent = $cparent->term_id;
                }
                wpdm_categories_tree($cparent, $terms);
                echo "</ul>";
            }
            echo $after_widget; ?>
            <?php
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
            $parent = isset($instance['parent']) ? intval($instance['parent']) : 0;
            $style = isset($instance['style']) ? esc_attr($instance['style']) : 'flat';
            $toplevel = isset($instance['toplevel']) ? esc_attr($instance['toplevel']) : 0;
            $category_page = isset($instance['category_page']) ? esc_attr($instance['category_page']) : 0;
            $hideon0 = isset($instance['hideon0']) ? esc_attr($instance['hideon0']) : 0;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('parent'); ?>"><?php _e('Parent:'); ?></label><br/>
                <?php wpdm_dropdown_categories($this->get_field_name('parent'), $parent, $this->get_field_id('parent')); ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('category_page'); ?>"><?php _e('On Category Page:', 'download-manger'); ?></label><br/>
                <select id="<?php echo $this->get_field_id('category_page'); ?>"
                        name="<?php echo $this->get_field_name('category_page'); ?>">
                    <option value="showall" <?php selected('showall', $category_page); ?>><?php echo __("Show all", "download-manager") ?></option>
                    <option value="subs" <?php selected('subs', $category_page); ?>><?php echo __("Show subcategories only", "download-manager") ?></option>
                </select>
            </p>
            <p>
                <label><?php _e('Style:'); ?></label><br/>
                <label><input type="radio"
                              name="<?php echo $this->get_field_name('style'); ?>" <?php checked('flat', $style); ?>
                              value="flat"> Flat List</label><br/>
                <label><input type="radio"
                              name="<?php echo $this->get_field_name('style'); ?>" <?php checked('tree', $style); ?>
                              value="tree"> Hierarchy List</label><br/>
                <label><input type="radio"
                              name="<?php echo $this->get_field_name('style'); ?>" <?php checked('icon', $style); ?>
                              value="icon"> Icon List ( Flat )</label><br/>
                <!-- label><input type="radio" name="<?php echo $this->get_field_name('style'); ?>" <?php checked('dropdown', $style); ?> value="dropdown"> Dropdown List</label></br -->
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('toplevel'); ?>"><input type="checkbox"
                                                                                   name="<?php echo $this->get_field_name('toplevel'); ?>" <?php checked('1', $toplevel); ?>
                                                                                   value="1"> <?php _e('Top Level Only', 'download-manager'); ?>
                </label><br/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('hideon0'); ?>"><input type="checkbox"
                                                                                  name="<?php echo $this->get_field_name('hideon0'); ?>" <?php checked('1', $hideon0); ?>
                                                                                  value="1"> <?php _e('Hide widget when no category', 'download-manager'); ?>
                </label><br/>
            </p>
            <?php
        }

    }
}

register_widget('WPDM_Categories');

