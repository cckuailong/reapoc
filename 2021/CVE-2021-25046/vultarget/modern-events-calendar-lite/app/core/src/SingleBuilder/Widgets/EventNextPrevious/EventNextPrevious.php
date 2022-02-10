<?php

namespace MEC\SingleBuilder\Widgets\EventNextPrevious;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventNextPrevious extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !isset($settings['next_previous_events']) || !$settings['next_previous_events'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if related events is set. In order for the widget in this page to be displayed correctly, please set Related Event for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/next-previous-events/" target="_blank">' . __('How to set Next/Previous Events', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$single = new \MEC_skin_single();
			ob_start();
				$single->display_next_previous_events($event_detail);
			$html = ob_get_clean();
		}

		return $html;
	}
}
