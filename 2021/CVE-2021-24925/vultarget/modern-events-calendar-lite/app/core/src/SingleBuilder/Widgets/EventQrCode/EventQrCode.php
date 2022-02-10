<?php

namespace MEC\SingleBuilder\Widgets\EventQrCode;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventQrCode extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !isset($settings['qrcode_module_status']) || !$settings['qrcode_module_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
				.'<span>'. __('This widget is displayed if QR Code is set. In order for the widget in this page to be displayed correctly, please set QR code module for your last event.', 'modern-events-calendar-lite').'</span>'
				. '<a href="https://webnus.net/dox/modern-events-calendar/qr-code-module/" target="_blank">' . __('How to set QR code module', 'modern-events-calendar-lite') . ' </a>'
			.'</p></div>';
		} else {

			ob_start();
				echo Base::get_main()->module('qrcode.details', array('event' => $event_detail));
			$html = ob_get_clean();
		}

		return $html;
	}
}
