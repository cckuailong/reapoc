<?php

namespace MEC\SingleBuilder\Widgets\EventAttendees;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventAttendees extends WidgetBase {

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
		$events_detail = $this->get_event_detail($event_id);

		if ( true === $this->is_editor_mode && (!isset($settings['bp_status']) || !$settings['bp_status']) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if buddypress is set. In order for the widget in this page to be displayed correctly, please set buddypress for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/buddypress/" target="_blank">' . __('How to set buddypress', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
				echo Base::get_main()->module('attendees-list.details', array('event'=>$events_detail));
			$html = ob_get_clean();
		}

		return $html;
	}
}

