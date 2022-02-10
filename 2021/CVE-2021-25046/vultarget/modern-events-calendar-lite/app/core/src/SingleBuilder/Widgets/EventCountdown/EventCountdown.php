<?php

namespace MEC\SingleBuilder\Widgets\EventCountdown;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventCountdown extends WidgetBase {

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

		$html = '';
		if ( true === $this->is_editor_mode && ( !isset($settings['countdown_status']) || !$settings['countdown_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if cost is set. In order for the widget in this page to be displayed correctly, please set cost for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/add-event/" target="_blank">' . __('How to set cost', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$wrap_class = (true === $this->is_editor_mode) ? 'mec-wrap' : '';

			$html = '<div class="'. $wrap_class .' mec-events-meta-group mec-events-meta-group-countdown">'
					. Base::get_main()->module('countdown.details', array('event'=>array($events_detail))) .
				'</div>';
		}

		return $html;
	}
}
