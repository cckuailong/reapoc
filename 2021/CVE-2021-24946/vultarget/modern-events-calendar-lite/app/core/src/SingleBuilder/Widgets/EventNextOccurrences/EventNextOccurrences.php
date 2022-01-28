<?php

namespace MEC\SingleBuilder\Widgets\EventNextOccurrences;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventNextOccurrences extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !isset($settings['next_event_module_status']) || !$settings['next_event_module_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if next events module is set. In order for the widget in this page to be displayed correctly, please set next events module for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/next-event-module/" target="_blank">' . __('How to set next events module', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
				echo Base::get_main()->module('next-event.details', array('event'=>$event_detail));
			$html = ob_get_clean();
		}

		return $html;
	}
}
