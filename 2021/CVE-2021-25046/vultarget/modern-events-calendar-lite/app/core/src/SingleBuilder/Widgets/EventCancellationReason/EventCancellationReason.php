<?php

namespace MEC\SingleBuilder\Widgets\EventCancellationReason;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventCancellationReason extends WidgetBase {

	public function get_html($event_id = 0){

		if(!$event_id){

			$event_id = $this->get_event_id();
		}

		if(!$event_id){
			return '';
		}

		$events_detail = $this->get_event_detail($event_id);

		$display_reason = get_post_meta( $event_id , 'mec_display_cancellation_reason_in_single_page', true);
		ob_start();
			if ($display_reason) {
				echo Base::get_main()->display_cancellation_reason($events_detail, $display_reason);
			}
		return ob_get_clean();
	}

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

		$display_reason = get_post_meta( $event_id , 'mec_display_cancellation_reason_in_single_page', true);
		if ( !$display_reason && $this->is_editor_mode() ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if cancellation reason is set. In order for the widget in this page to be displayed correctly, please set cancellation reason for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="#" target="_blank">' . __('Cancellation Reason', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$html = $this->get_html($event_id);
		}

		return $html;
	}
}
