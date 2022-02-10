<?php


namespace MEC\SingleBuilder\Widgets\RSVPForm;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class RSVPForm extends WidgetBase {

	public function get_display_rsvp_form($event_id){

		ob_start();
			echo do_shortcode( "[mec-rsvp event-id='$event_id' ]" );
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

		$is_rsvp_enabled = false;

		if(!function_exists('is_plugin_active')) {

			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

        if( is_plugin_active('mec-rsvp/mec-rsvp.php') ) {

			$is_rsvp_enabled  = \MEC_RSVP\RSVP\EventRSVP::getInstance()->can_display_rsvp_form( $event_id );
		}

		$settings = $this->settings;
		$html = '';

		if ( true === $this->is_editor_mode && !$is_rsvp_enabled ) {

			$html = '<div class="mec-content-notification">
					<p>'
						.'<span>'
							. __('This widget is displayed if label is set. In order for the widget in this page to be displayed correctly, please set RSVP module for your last event.', 'modern-events-calendar-lite')
						.'</span>'
						.'<a href="https://webnus.net/dox/modern-events-calendar/rsvp-events-addon/" target="_blank">' . __('How to set RSVP module', 'modern-events-calendar-lite') . ' </a>'
					.'</p>'
				.'</div>';
		} elseif( $is_rsvp_enabled ) {

			$html = $this->get_display_rsvp_form( $event_id );
		}

		return $html;
	}
}
