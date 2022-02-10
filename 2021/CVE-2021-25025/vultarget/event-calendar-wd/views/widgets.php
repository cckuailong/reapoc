<?php
/**
 * Widget functions / views
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 *  Class functions for the SC widgets
 */
class ECWD_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                false, $name = __('Event Calendar WD', 'event-calendar-wd'), array('description' => __('Descr', 'event-calendar-wd'))
        );
    }

    /**
     * Widget HTML output

     */
    function widget($args, $instance) {
        $markup = '';
        extract($args);

        //Output before widget stuff
        echo $before_widget;
        // Check whether any calendars have been added yet

        if (wp_count_posts(ECWD_PLUGIN_PREFIX . '_calendar')->publish > 0) {
            //Output title stuff
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);

            if (!empty($title)) {
                echo $before_title . $title . $after_title;
            }

            $no_events_exist = true;
            $calendar_ids = array();

            if (!empty($instance['id'])) {
                //Break comma delimited list of event ids into array
                $calendar_ids = explode(',', str_replace(' ', '', $instance['id']));

                //Check each id is an integer, if not, remove it from the array
                foreach ($calendar_ids as $key => $calendar_id) {
                    if (0 == absint($calendar_id))
                        unset($calendar_ids[$key]);
                }

                //If at least one of the event ids entered exists, set no_events_exist to false
                foreach ($calendar_ids as $calendar_id) {
                    if (false !== get_post_meta($calendar_id))
                        $no_events_exist = false;
                }
            } else {
                if (current_user_can('manage_options')) {
                    _e('No valid Event IDs have been entered for this widget. Please check that you have entered the IDs correctly in the widget settings (Appearance > Widgets), and that the Events have not been deleted.', 'event-calendar-wd');
                }
            }

            //Check that at least one valid event id has been entered
            if (!empty($calendar_ids)) {
                //Turns event_ids back into string or event ids delimited by '-' ('1-2-3-4' for example)
                $calendar_ids = implode('-', $calendar_ids);

                $title_text = (!empty($instance['display_title_text']) ? $instance['display_title_text'] : null );
                $sort_order = ( isset($instance['order']) ) ? $instance['order'] : 'asc';
                $page_items = ( isset($instance['page_items']) ) ? $instance['page_items'] : '5';

                $args = array(
                    'title_text' => $title_text,
                    'sort' => $sort_order,
                    'page_items' => $page_items,
                    'month' => null,
                    'year' => null,
                    'widget' => 1,
                    'widget_theme' => (!isset($instance['theme']) || $instance['theme'] == "calendar_theme") ? null : $instance['theme']
                );

//				if( 'list-grouped' == $instance['display_type'] ) {
//					$args['grouped'] = 1;
//				}
                //echo $instance['display_type'].'------------<br />';
                $markup = ecwd_print_calendar($calendar_ids, $instance['display_type'], $args, true);

                echo $markup;
            }
        } else {
            if (current_user_can('manage_options')) {
                _e('You have not added any events yet.', 'event-calendar-wd');
            } else {
                return;
            }
        }

        //Output after widget stuff
        echo $after_widget;
    }

    /**
     * Update settings when saved
     */
    function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance['title'] = esc_html($new_instance['title']);
        $instance['id'] = esc_html($new_instance['id']);
        $instance['display_type'] = esc_html($new_instance['display_type']);
        $instance['page_items'] = esc_html($new_instance['page_items']);
        $instance['theme'] = esc_html($new_instance['theme']);


        return $instance;
    }

    /**
     * 
     * @param type $instance widget form in admin
     */
    function form($instance) {

        // Check for existing events and if there are none then display a message and return
        if (wp_count_posts(ECWD_PLUGIN_PREFIX . '_calendar')->publish <= 0) {
            echo '<p>' . __('There are no calendars created yet.', 'event-calendar-wd') .
            ' <a href="' . admin_url('edit.php?post_type=ecwd_calendar') . '">' . __('Add your first calendar!', 'event-calendar-wd') . '</a>' .
            '</p>';
            return;
        }
        $type = ECWD_PLUGIN_PREFIX . '_calendar';
        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => 1
        );
        $calendar_posts = get_posts($args);
        $title = ( isset($instance['title']) ) ? $instance['title'] : '';
        $ids = ( isset($instance['id']) ) ? $instance['id'] : '';
        $selected_theme = ( isset($instance['theme']) ) ? $instance['theme'] : '';
        $display_type = ( isset($instance['display_type']) ) ? $instance['display_type'] : 'mini';
        $page_items = ( isset($instance['page_items']) ) ? $instance['page_items'] : '5';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'event-calendar-wd'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('id'); ?>">
                <?php _e('Calendar to display', 'event-calendar-wd'); ?>
            </label>
            <?php if ($calendar_posts) { ?>
                <select id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" class="widefat">
                <?php foreach ($calendar_posts as $calendar_post) {
                    ?>
                        <option value="<?php echo $calendar_post->ID; ?>" <?php selected($ids, $calendar_post->ID); ?>><?php echo $calendar_post->post_title; ?></option>
                    <?php } ?>
                </select>
                <?php } ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('display_type'); ?>"><?php _e('Display Events as:', 'event-calendar-wd'); ?></label>
            <select id="<?php echo $this->get_field_id('display_type'); ?>" name="<?php echo $this->get_field_name('display_type'); ?>" class="widefat">
                <option value="mini"<?php selected($display_type, 'mini'); ?>><?php _e('Month', 'event-calendar-wd'); ?></option>
                <option value="list"<?php selected($display_type, 'list'); ?>><?php _e('List', 'event-calendar-wd'); ?></option>
                <option value="week" <?php selected($display_type, 'week'); ?>><?php _e('Week', 'event-calendar-wd'); ?></option>
                <option value="day" <?php selected($display_type, 'day'); ?>><?php _e('Day', 'event-calendar-wd'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Calendar theme', 'event-calendar-wd'); ?></label>
            <select id="<?php echo $this->get_field_id('theme'); ?>"
                    name="<?php echo $this->get_field_name('theme'); ?>"
                    class="widefat">
                <option
                  value="calendar_theme" <?php selected($selected_theme, "calendar_theme"); ?>><?php _e('Calendar theme', 'event-calendar-wd'); ?></option>
                <option
                  value="calendar" <?php selected($selected_theme, "calendar"); ?>><?php _e('Default Blue', 'event-calendar-wd'); ?></option>
                <option
                  value="calendar_grey" <?php selected($selected_theme, "calendar_grey"); ?>><?php _e('Grey', 'event-calendar-wd'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('page_items'); ?>"><?php _e('Events per page in list view:', 'event-calendar-wd'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('page_items'); ?>" name="<?php echo $this->get_field_name('page_items'); ?>" value="<?php echo $page_items; ?>" class="widefat" />
        </p>


        <?php
    }

}

if(defined('ECWD_MAIN_FILE') && is_plugin_active(ECWD_MAIN_FILE)) {
  add_action('widgets_init', 'ecwd_register_widget');
}

function ecwd_register_widget(){
  register_widget("ECWD_Widget");
}
