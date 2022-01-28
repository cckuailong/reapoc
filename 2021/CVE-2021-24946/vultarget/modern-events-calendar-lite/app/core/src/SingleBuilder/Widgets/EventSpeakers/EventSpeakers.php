<?php

namespace MEC\SingleBuilder\Widgets\EventSpeakers;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventSpeakers extends WidgetBase {

	/**
	 *  Get HTML Output
	 *
	 * @param int $event_id
	 * @param array $atts
	 *
	 * @return string
	 */
	public function output( $event_id = 0, $atts = array() ){

		if( !$event_id ){

			$event_id = $this->get_event_id();
		}

		if(!$event_id){
			return '';
		}

		$settings = $this->settings;
		$event_detail = $this->get_event_detail($event_id);
		$speakers = (isset($event_detail->data->speakers) and is_array($event_detail->data->speakers)) ? $event_detail->data->speakers : array();

		$html = '';
		if ( true === $this->is_editor_mode && ( empty($speakers) || (!isset($settings['speakers_status']) || !$settings['speakers_status']) ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if speaker is set. In order for the widget in this page to be displayed correctly, please set speaker for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/speaker/" target="_blank">' . __('How to set speaker', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} elseif ( true === $this->is_editor_mode && isset($settings['speakers_status']) && $settings['speakers_status'] ) {

			$html = Base::get_main()->module('speakers.details', array('event'=>$event_detail));
		} else {

			ob_start();
				// Event Speaker
				echo Base::get_main()->module('speakers.details', array('event'=>$event_detail));
				?>
				<script>
					// Fix modal speaker in some themes
					jQuery( ".mec-speaker-avatar a" ).click(function(e) {
						e.preventDefault();
						var id =  jQuery(this).attr('href');
						lity(id);
					});
					// Fix modal booking in some themes
					function openBookingModal(){
						jQuery( ".mec-booking-button.mec-booking-data-lity" ).on('click',function(e) {
							e.preventDefault();
							var book_id =  jQuery(this).attr('href');
							Lity.close();
							lity(book_id);
						});
					}
				</script>
			<?php
			$html = ob_get_clean();
		}

		return $html;
	}
}
