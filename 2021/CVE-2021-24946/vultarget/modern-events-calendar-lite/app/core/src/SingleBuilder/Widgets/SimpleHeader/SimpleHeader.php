<?php

namespace MEC\SingleBuilder\Widgets\SimpleHeader;

use MEC\SingleBuilder\Widgets\WidgetBase;

class SimpleHeader extends WidgetBase {

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

		$html = '<h1 class="mec-single-title">'
			.get_the_title($event_id).
		'</h1>';

		return $html;
	}
}
