<?php

namespace MEC\SingleBuilder\Widgets\EventData;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventData extends WidgetBase {

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
		$data = (isset($event_detail->data->meta['mec_fields']) and is_array($event_detail->data->meta['mec_fields'])) ? $event_detail->data->meta['mec_fields'] : get_post_meta($event_detail->ID, 'mec_fields', true);

		$html = '';
		if ( true === $this->is_editor_mode && ( empty($data) || ( isset($settings['display_event_fields']) && !$settings['display_event_fields'] ) ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if event data is set. In order for the widget in this page to be displayed correctly, please set event data for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/custom-fields/" target="_blank">' . __('How to set Custom Fields', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$single         = new \MEC_skin_single();
			ob_start();
				$single->display_data_fields( $event_detail );
			$html = ob_get_clean();
		}

		return $html;
	}
}
