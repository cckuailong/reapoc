<?php

namespace MEC\SingleBuilder\Widgets\EventBreadcrumbs;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventBreadcrumbs extends WidgetBase {

	public function get_breadcrumb_html($event_id = 0){

		if(!$event_id){

			$event_id = $this->get_event_id();
		}

		if(!$event_id){
			return '';
		}

        $single         = new \MEC_skin_single();

		ob_start();
			echo '<div class="mec-breadcrumbs">';
				$single->display_breadcrumb_widget( $event_id );
			echo '</div>';
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

		$settings = $this->settings;
		$events_detail = $this->get_event_detail($event_id);

		if ( true === $this->is_editor_mode && ( !isset($settings['breadcrumbs']) || !$settings['breadcrumbs'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if breadcrumbs is set. In order for the widget in this page to be displayed correctly, please set breadcrumbs for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank">' . __('How to set breadcrumbs', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$html = $this->get_breadcrumb_html($event_id);
		}

		return $html;
	}
}
