<?php

namespace MEC\Books;

use MEC\Settings\Settings;
use MEC\Singleton;

class BookingForm extends Singleton {

    public function enqueue(){

        wp_enqueue_style('mec-custom-form', MEC_CORE_URL.'src/Forms/custom-form.css');
    }

    public function display_form($event_id){

        if(!$event_id){
            return;
        }

        $this->enqueue();
        $mainClass      = new \MEC_main();
		$single         = new \MEC_skin_single();
        $settings = Settings::getInstance()->get_settings();

        global $MEC_Events;
        $MEC_Events = $single->get_event_mec($event_id);
        $single_event = $MEC_Events[0];
        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        $occurrence = (isset($single_event->date['start']['date']) ? $single_event->date['start']['date'] : (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : ''));
        $occurrence_end_date = trim($occurrence) ? $mainClass->get_end_date_by_occurrence($single_event->data->ID, (isset($single_event->date['start']['date']) ? $single_event->date['start']['date'] : $occurrence)) : '';

        if ($mainClass->is_sold($single_event, (trim($occurrence) ? $occurrence : $single_event->date['start']['date'])) && count($single_event->dates) <= 1) : ?>
            <div class="mec-sold-tickets warning-msg"><?php esc_html_e('Sold out!', 'wpl'); ?></div>
        <?php
        elseif ($mainClass->can_show_booking_module($single_event)) :
            $data_lity_class = '';
            if (isset($settings['single_booking_style']) && $settings['single_booking_style'] == 'modal') {
                $data_lity_class = 'lity-hide ';
            }
            ?>
            <div id="mec-events-meta-group-booking-<?php echo $single->uniqueid; ?>" class="<?php echo $data_lity_class; ?>mec-events-meta-group mec-events-meta-group-booking mec-custom-form-box">
                <?php
                if( isset($settings['booking_user_login']) && $settings['booking_user_login'] == '1' && !is_user_logged_in() ) {
                    echo do_shortcode('[MEC_login]');
                } elseif ( isset($settings['booking_user_login']) && $settings['booking_user_login'] == '0' && !is_user_logged_in() && isset($booking_options['bookings_limit_for_users']) && $booking_options['bookings_limit_for_users'] == '1' ) {
                    echo do_shortcode('[MEC_login]');
                } else {
                    echo $mainClass->module('booking.default', array('event' => $MEC_Events));
                }
                ?>
            </div>
            <?php
        endif;
    }
}