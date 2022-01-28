<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Widget
 * @author Webnus <info@webnus.biz>
 */
class MEC_MEC_widget extends WP_Widget
{
    /**
     * @var MEC_render
     */
    public $render;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Render Class
        $this->render = MEC::getInstance('app.libraries.render');
        
        // MEC Main Class
        $this->main = MEC::getInstance('app.libraries.main');
        
        parent::__construct('MEC_MEC_widget', __('Modern Events Calendar', 'modern-events-calendar-lite'), array('description'=>__('Show events based on created shortcodes.', 'modern-events-calendar-lite')));
    }

    /**
     * How to display the widget on the screen.
     * @param array $args
     * @param array $instance
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function widget($args, $instance)
    {
        // Inclue OWL Assets. It's needee if Widget is set to load grid view
        $this->main->load_owl_assets();

        // Before Widget
        echo (isset($args['before_widget']) ? $args['before_widget'] : '');
        
        // Print the widget title
        if(!empty($instance['title']))
        {
			echo (isset($args['before_title']) ? $args['before_title'] : '').apply_filters('widget_title', $instance['title']).(isset($args['after_title']) ? $args['after_title'] : '');
		}
        
        $calendar_id = isset($instance['calendar_id']) ? $instance['calendar_id'] : 0;

        // Get Skin Options
        $sk_options = get_post_meta($calendar_id, 'sk-options', true);
        $sk_options_list_style = (isset($sk_options['list']) and isset($sk_options['list']['style'])) ? trim($sk_options['list']['style']) : 'classic';

        $current_hide = (isset($instance['current_hide']) ? $instance['current_hide'] : '');
        $autoplay = (isset($instance['autoplay']) ? $instance['autoplay'] : 1);
        $autoplay_time = (isset($instance['autoplay_time']) ? $instance['autoplay_time'] : 3000);
        $loop = (isset($instance['loop']) ? $instance['loop'] : 1);

        // Print the skin output
        echo $this->render->widget($calendar_id, array(
            'html-class'=>'mec-widget '.$current_hide,
            'style'=>$sk_options_list_style,
            'widget'=>true,
            'widget_autoplay'=>$autoplay,
            'widget_loop'=>$loop,
            'widget_autoplay_time'=>$autoplay_time,
        ));
        
        // After Widget
        echo (isset($args['after_widget']) ? $args['after_widget'] : '');
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * @param array $instance
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function form($instance)
    {
        $calendars = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1', 'meta_query'=>array(array('key'=>'skin', 'value'=>array('list', 'grid', 'monthly_view'), 'compare'=>'IN'))));

        $current_hide = (isset($instance['current_hide']) ? $instance['current_hide'] : '');
        $autoplay = (isset($instance['autoplay']) ? $instance['autoplay'] : 1);
        $autoplay_time = (isset($instance['autoplay_time']) ? $instance['autoplay_time'] : 3000);
        $loop = (isset($instance['loop']) ? $instance['loop'] : 1);

        $monthly_view_options = false;
        $grid_view_options = false;

        echo '<p class="mec-widget-row-container">'
        .'<label for="'.$this->get_field_id('title').'">'.__('Title:', 'modern-events-calendar-lite').'</label>'
        .'<input class="widefat" type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.(isset($instance['title']) ? $instance['title'] : '').'" />'
        .'</p>';
        
        if(count($calendars))
        {
            echo '<p class="mec-widget-row-container">'
                .'<label for="'.$this->get_field_id('calendar_id').'">'.__('Shortcode:', 'modern-events-calendar-lite').'</label>'
                .'<select class="widefat" name="'.$this->get_field_name('calendar_id').'" id="'.$this->get_field_id('calendar_id').'" onchange="mec_show_widget_options(this);"><option value="">-----</option>';
            
            foreach($calendars as $calendar) 
            {
                $skin = get_post_meta($calendar->ID, 'skin', true);

                if(!$monthly_view_options) $monthly_view_options = (trim($skin) == 'monthly_view' and (isset($instance['calendar_id']) and $instance['calendar_id'] == $calendar->ID)) ? true : false;
                if(!$grid_view_options) $grid_view_options = (trim($skin) == 'grid' and (isset($instance['calendar_id']) and $instance['calendar_id'] == $calendar->ID)) ? true : false;

                echo '<option data-skin="'.trim($skin).'" value="'.$calendar->ID.'" '.((isset($instance['calendar_id']) and $instance['calendar_id'] == $calendar->ID) ? 'selected="selected"' : '').'>'.$calendar->post_title.'</option>';
            }

            echo '</select></p>';

            // Monthly View Options
            echo '<p class="mec-widget-row-container mec-current-check-wrap '.(($monthly_view_options) ? '' : 'mec-util-hidden').'"><label for="'.$this->get_field_id('current_hide').'">'.__('Enable No Event Block Display: ', 'modern-events-calendar-lite').'</label><input type="checkbox" id="'.$this->get_field_id('current_hide').'" name="'.$this->get_field_name('current_hide').'" value="current-hide" '.checked($current_hide, 'current-hide', false).'></p>';

            // Grid Options
            echo '<div class="mec-grid-options-wrap '.(($grid_view_options) ? '' : 'mec-util-hidden').'">
                <p class="mec-widget-row-container">
                    <label for="'.$this->get_field_id('autoplay').'">'.__('Autoplay: ', 'modern-events-calendar-lite').'</label>
                    <input type="hidden" name="'.$this->get_field_name('autoplay').'" value="0">
                    <input type="checkbox" id="'.$this->get_field_id('autoplay').'" name="'.$this->get_field_name('autoplay').'" value="1" '.($autoplay ? 'checked="checked"' : '').'>
                </p>
                <p class="mec-widget-row-container">
                    <label for="'.$this->get_field_id('autoplay_time').'">'.__('Autoplay Time: ', 'modern-events-calendar-lite').'</label>
                    <input type="number" id="'.$this->get_field_id('autoplay_time').'" name="'.$this->get_field_name('autoplay_time').'" value="'.$autoplay_time.'">
                </p>
                <p class="mec-widget-row-container">
                    <label for="'.$this->get_field_id('loop').'">'.__('Loop: ', 'modern-events-calendar-lite').'</label>
                    <input type="hidden" name="'.$this->get_field_name('loop').'" value="0">
                    <input type="checkbox" id="'.$this->get_field_id('loop').'" name="'.$this->get_field_name('loop').'" value="1" '.($loop ? 'checked="checked"' : '').'>
                </p>
            </div>';
        }
        else
        {
            echo '<p class="mec-widget-row-container"><a href="'.$this->main->add_qs_var('post_type', 'mec_calendars', $this->main->URL('admin').'edit.php').'">'.__('Create some calendars first.').'</a></p>';
        }
    }

    /**
     * Update the widget settings.
     * @author Webnus <info@webnus.biz>
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
        $instance['calendar_id'] = isset($new_instance['calendar_id']) ? intval($new_instance['calendar_id']) : 0;
        $instance['current_hide'] = isset($new_instance['current_hide']) ? strip_tags($new_instance['current_hide']) : '';
        $instance['autoplay'] = isset($new_instance['autoplay']) ? sanitize_text_field($new_instance['autoplay']) : 0;
        $instance['autoplay_time'] = isset($new_instance['autoplay_time']) ? sanitize_text_field($new_instance['autoplay_time']) : 3000;
        $instance['loop'] = isset($new_instance['loop']) ? sanitize_text_field($new_instance['loop']) : 0;

        return $instance;
    }
}