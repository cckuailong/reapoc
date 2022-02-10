<?php

namespace MEC\SingleBuilder\Widgets\EventExport;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventExport extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !isset($settings['export_module_status']) || !$settings['export_module_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if export module is set. In order for the widget in this page to be displayed correctly, please set export module for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/export-module/" target="_blank">' . __('How to set export module', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
				echo Base::get_main()->module('export.details', array('event'=>$event_detail));
			$html = ob_get_clean();
		}

		return $html;
	}
}
