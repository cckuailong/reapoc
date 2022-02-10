<?php

// BookingWidget Class
class BookingWidget extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::__construct(false, $name = 'Booking Calendar');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        
        $booking_widget_title = __('Booking Calendar', 'booking');              //FixIn: 6.1.1.11
        $booking_widget_show = 'booking_form';
        $booking_widget_type = 1;
        $booking_widget_calendar_count = 1;
        $booking_widget_last_field = '';
        
        
        if ( isset( $instance['booking_widget_title'] ) )                       //FixIn: 6.1.1.11
            $booking_widget_title = apply_filters('widget_title', $instance['booking_widget_title']);
        if (function_exists('icl_translate')) 
            $booking_widget_title = icl_translate( 'wpml_custom', 'wpbc_custom_widget_booking_title1', $booking_widget_title);
        
        if ( isset( $instance['booking_widget_show'] ) )                        //FixIn: 6.1.1.11
            $booking_widget_show = $instance['booking_widget_show'];
        if ( isset( $instance['booking_widget_type'] ) )                        //FixIn: 6.1.1.11
            $booking_widget_type = $instance['booking_widget_type'];
        if (empty($booking_widget_type)) $booking_widget_type=1;
        if ( isset( $instance['booking_widget_calendar_count'] ) )              //FixIn: 6.1.1.11
            $booking_widget_calendar_count = $instance['booking_widget_calendar_count'];
        if ( isset( $instance['booking_widget_last_field'] ) )                  //FixIn: 6.1.1.11
            $booking_widget_last_field = $instance['booking_widget_last_field'];

                

        echo $before_widget;
        if (isset($_GET['booking_hash'])) {
            _e('You need to use special shortcode [bookingedit] for booking editing.' ,'booking');
            echo $after_widget;
            return;
        }

        if ($booking_widget_title != '') echo $before_title . htmlspecialchars_decode($booking_widget_title) . $after_title;

        echo "<div class='widget_wpdev_booking wpdevelop months_num_in_row_1'>";                                        //FixIn: 8.4.2.3
        if ($booking_widget_show == 'booking_form') {
            // do_action('wpdev_bk_add_form', $booking_widget_type , $booking_widget_calendar_count);
            $my_booking_form_name = apply_bk_filter('wpbc_get_default_custom_form', 'standard', $booking_widget_type);
            make_bk_action('wpdevbk_add_form', $booking_widget_type , $booking_widget_calendar_count, true, $my_booking_form_name );
            
        } else {
	        echo "<div class='wpbc_only_calendar'>";																	//FixIn: 8.0.1.2
            	echo "<div id='calendar_booking_unselectable" . $booking_widget_type . "'></div>";          			//FixIn: 6.1.1.13
            	do_action( 'wpdev_bk_add_calendar', $booking_widget_type, $booking_widget_calendar_count );
            echo '</div>';
        }

        if ($booking_widget_last_field !== '') echo '<br/>' . htmlspecialchars_decode($booking_widget_last_field);
        echo "</div>";

        echo $after_widget;


    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;

	$instance['booking_widget_title']           = strip_tags($new_instance['booking_widget_title']);
	$instance['booking_widget_show']            = strip_tags($new_instance['booking_widget_show']);
	$instance['booking_widget_type'] 			= ( empty( $new_instance['booking_widget_type'] ) ? 1 : strip_tags( $new_instance['booking_widget_type'] ) );
	$instance['booking_widget_calendar_count']  = strip_tags($new_instance['booking_widget_calendar_count']);
	$instance['booking_widget_last_field']      = $new_instance['booking_widget_last_field'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        if ( isset($instance['booking_widget_title']) ) $booking_widget_title           = esc_attr($instance['booking_widget_title']);
        else $booking_widget_title = '';
        if ( isset($instance['booking_widget_show']) ) $booking_widget_show            = esc_attr($instance['booking_widget_show']);
        else $booking_widget_show = '';
        if ( ( class_exists('wpdev_bk_personal') ) && ( isset($instance['booking_widget_type']) ) ) {
            $booking_widget_type            = esc_attr($instance['booking_widget_type']);
        } else $booking_widget_type=1;
        if ( isset($instance['booking_widget_calendar_count']) ) $booking_widget_calendar_count  = esc_attr($instance['booking_widget_calendar_count']);
        else $booking_widget_calendar_count = 1;
        if ( isset($instance['booking_widget_last_field']) ) $booking_widget_last_field      = esc_attr($instance['booking_widget_last_field']);
        else $booking_widget_last_field = '';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_title'); ?>"><?php _e('Title' ,'booking'); ?>:</label><br/>
            <input value="<?php echo $booking_widget_title; ?>"
                   name="<?php echo $this->get_field_name('booking_widget_title'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_title'); ?>"
                   type="text" class="widefat" style="width:100%;line-height: 1.5em;" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_show'); ?>"><?php _e('Show' ,'booking'); ?>:</label><br/>
            <select
                   name="<?php echo $this->get_field_name('booking_widget_show'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_show'); ?>" style="width:100%;line-height: 1.5em;">
                <option <?php if($booking_widget_show == 'booking_form') echo "selected"; ?> value="booking_form"><?php _e('Booking form with calendar' ,'booking'); ?></option>
                <option <?php if($booking_widget_show == 'booking_calendar') echo "selected"; ?> value="booking_calendar"><?php _e('Only availability calendar' ,'booking'); ?></option>
            </select>
        </p>


        <?php
        if ( class_exists('wpdev_bk_personal')) {
            $types_list = wpbc_get_br_as_objects(); ?>
            <p>
                <label for="<?php echo $this->get_field_id('booking_widget_type'); ?>"><?php _e('Booking resource' ,'booking'); ?>:</label><br/>
                <!--input id="calendar_type"  name="calendar_type" class="input" type="text" -->
                <select
                       name="<?php echo $this->get_field_name('booking_widget_type'); ?>"
                       id="<?php echo $this->get_field_id('booking_widget_type'); ?>"
                       style="width:100%;line-height: 1.5em;">
                            <?php foreach ($types_list as $tl) { ?>
                    <option  <?php if($booking_widget_type == $tl->id ) echo "selected"; ?>
                        style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:600;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                        value="<?php echo $tl->id; ?>"><?php echo $tl->title; ?></option>
                                <?php } ?>
                </select>

            </p>
        <?php } ?>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_calendar_count'); ?>"><?php _e('Visible months' ,'booking'); ?>:</label><br/>

            <select style="width:100%;line-height: 1.5em;"
                    name="<?php echo $this->get_field_name('booking_widget_calendar_count'); ?>"
                    id="<?php echo $this->get_field_id('booking_widget_calendar_count'); ?>"
            >
            <?php foreach ( array(1,2,3,4,5,6,7,8,9,10,11,12) as $tl) { ?>
                <option  <?php if($booking_widget_calendar_count == $tl ) echo "selected"; ?>
                        style="font-weight:600;"
                        value="<?php echo $tl; ?>"><?php echo $tl; ?></option>
            <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('booking_widget_last_field'); ?>"><?php _e('Footer' ,'booking'); ?>:</label><br/>
            <input value="<?php echo $booking_widget_last_field; ?>"
                   name="<?php echo $this->get_field_name('booking_widget_last_field'); ?>"
                   id="<?php echo $this->get_field_id('booking_widget_last_field'); ?>"
                   type="text" style="width:100%;line-height: 1.5em;" /><br/>
            <em style="font-size:11px;"><?php printf(__("Example: %sMake booking here%s" ,'booking'),"<code>&lt;a href='". get_site_url() ."'&gt;",'&lt;/a&gt;</code>'); ?></em>
        </p>

        <p style="font-size:10px;" > 
                    <?php printf(__("%sImportant!!!%s Please note, if you show booking calendar (inserted into post/page) with widget at the same page, then the last will not be visible." ,'booking'),'<strong>','</strong>'); ?>

                    <?php if (!class_exists('wpdev_bk_personal')) { ?>

                            <em><?php printf(__("%sSeveral widgets are supported at %spaid versions%s." ,'booking'),'<span style="">','<a href="https://wpbookingcalendar.com/" target="_blank" style="text-decoration:none;color:#3A5670;">','</a>','</span>'); ?></em>

                    <?php
                    }
?></p><?php
    }

} // class BookingWidget

// register widget - New, since WordPress - 2.8
//add_action('widgets_init', create_function('', 'return register_widget("BookingWidget");'));
//FixIn: 8.1.3.18
function register_wpbc_widget() {
    register_widget("BookingWidget");
}
add_action( 'widgets_init', 'register_wpbc_widget' );