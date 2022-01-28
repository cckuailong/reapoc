<?php

namespace MEC\SingleBuilder\Widgets\EventCost;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventCost extends WidgetBase {

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
		if ( true === $this->is_editor_mode && !(isset($events_detail->data->meta['mec_cost']) && $events_detail->data->meta['mec_cost'] != '') ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if cost is set. In order for the widget in this page to be displayed correctly, please set cost for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/add-event/" target="_blank">' . __('How to set cost', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
			if (isset($events_detail->data->meta['mec_cost']) && $events_detail->data->meta['mec_cost'] != '') {
				?>
				<div class="mec-event-meta">
					<div class="mec-event-cost">
						<i class="mec-sl-wallet"></i>
						<h3 class="mec-cost"><?php echo Base::get_main()->m('cost', __('Cost', 'modern-events-calendar-lite')); ?></h3>
						<dd class="mec-events-event-cost"><?php echo (is_numeric($events_detail->data->meta['mec_cost']) ? Base::get_main()->render_price($events_detail->data->meta['mec_cost'], $events_detail ) : $events_detail->data->meta['mec_cost']); ?></dd>
					</div>
				</div>
				<?php
			}
			$html = ob_get_clean();
		}

		return $html;
	}
}
