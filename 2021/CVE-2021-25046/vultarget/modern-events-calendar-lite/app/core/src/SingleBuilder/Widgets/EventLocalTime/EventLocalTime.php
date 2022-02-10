<?php

namespace MEC\SingleBuilder\Widgets\EventLocalTime;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventLocalTime extends WidgetBase {

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

		$html = '';
		if ( true === $this->is_editor_mode && ( !isset($settings['local_time_module_status']) || !$settings['local_time_module_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if label is set. In order for the widget in this page to be displayed correctly, please set LocalTime module for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/local-time-module/" target="_blank">' . __('How to set LocalTime module', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$html = '<div class="mec-event-meta mec-local-time-details mec-frontbox">'
				.Base::get_main()->module('local-time.details', array('event'=>$event_detail)) .
			'</div>';
		}

		return $html;
	}
}
