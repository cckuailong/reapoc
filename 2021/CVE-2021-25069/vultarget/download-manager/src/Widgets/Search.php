<?php

use WPDM\__\Messages;

if (!defined("ABSPATH")) die("Shit happens!");
if (!class_exists('WPDM_Search')) {
    class WPDM_Search extends WP_Widget
    {

        function __construct()
        {
            parent::__construct(false, 'WPDM Search');
        }

        function widget($args, $instance)
        {
            extract($args);
            if (wpdm_valueof($instance, 'result_page') > 0) {
                $title = apply_filters('widget_title', wpdm_valueof($instance, 'title'));
                $url = get_permalink(wpdm_valueof($instance, 'result_page'));
                echo isset($before_widget) ? $before_widget : "";
                if ($title) echo (isset($before_title) ? $before_title : "") . $title . (isset($after_title) ? $after_title : "");
                echo "<div class='w3eden'><form action='" . $url . "' class='wpdm-pro'>";
                echo "<div class='input-group input-group-lg'><input placeholder='" . __("Search...", "download-manager") . "' type=text class='form-control' name='search' /><span class='input-group-btn input-group-append'><button class='btn btn-secondary'><i class='fas fa-search'></i></button></span></div><div class='clear'></div>";
                echo "</form></div>";
                echo isset($after_widget) ? $after_widget : "";
            } else {
                Messages::warning(__("Search result page was not configured properly. Please create a page with shortcode [wpdm_search_result] and select thee page from widget settings", "download-manager"), 0);
            }
        }

        function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['result_page'] = (int)$new_instance['result_page'];
            $instance['cv'] = $new_instance['cv'];
            return $instance;
        }

        function form($instance)
        {
            $title = isset($instance['title']) ? esc_attr($instance['title']) : "";
            $result_page = isset($instance['result_page']) ? esc_attr($instance['result_page']) : "";
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </p>
            <p>
                <?php echo __("Search Result Page", "wpdmap") . ":<br/>"; ?>
                <?php wp_dropdown_pages(['selected' => $result_page, 'name' => $this->get_field_name('result_page'), 'id' => $this->get_field_id('result_page'), 'class' => 'widefat']) ?>
            </p>

            <div style="border:1px solid #ccc;padding:15px;margin-bottom: 15px;font-size:8pt">
                <?php _e("Note: Create a page with short-code <code>[wpdm_search_result]</code> and select that page as search redult page", "wpdmap"); ?>
            </div>
            <?php
        }
    }
}
register_widget('WPDM_Search');
