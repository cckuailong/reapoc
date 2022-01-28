<?php

namespace MEC\SingleBuilder\Widgets\EventHourlySchedule;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventHourlySchedule extends WidgetBase {

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
		$hourly_schedules = isset($event_detail->data->hourly_schedules) && is_array($event_detail->data->hourly_schedules) ? $event_detail->data->hourly_schedules : array();

		$html = '';
		if ( true === $this->is_editor_mode && 0 == count($hourly_schedules) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if Hourly Schedule is set. In order for the widget in this page to be displayed correctly, please set Hourly Schedule for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/hourly-schedule/" target="_blank">' . __('How to set Hourly Schedule', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$single         = new \MEC_skin_single();
			ob_start();
				$single->display_hourly_schedules_widget( $event_detail );
			$html = ob_get_clean();
		}

		return $html;
	}
}
