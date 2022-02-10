<?php

namespace MEC\SingleBuilder\Widgets\EventTime;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventTime extends WidgetBase {

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

		ob_start();
			echo '<div class="mec-event-meta">';
			// Event Time
			if (isset($event_detail->data->meta['mec_date']['start']) and !empty($event_detail->data->meta['mec_date']['start'])) {
				if (isset($event_detail->data->meta['mec_hide_time']) and $event_detail->data->meta['mec_hide_time'] == '0') {
					$time_comment = isset($event_detail->data->meta['mec_comment']) ? $event_detail->data->meta['mec_comment'] : '';
					$allday = isset($event_detail->data->meta['mec_allday']) ? $event_detail->data->meta['mec_allday'] : 0;
					?>
						<div class="mec-single-event-time">
							<h3 class="mec-time"><i class="mec-sl-clock"> </i><?php _e('Time', 'modern-events-calendar-lite'); ?></h3>
							<i class="mec-time-comment"><?php echo (isset($time_comment) ? $time_comment : ''); ?></i>

							<?php if ($allday == '0' and isset($event_detail->data->time) and trim($event_detail->data->time['start'])) : ?>
								<dd><abbr class="mec-events-abbr"><?php echo $event_detail->data->time['start']; ?><?php echo (trim($event_detail->data->time['end']) ? ' - ' . $event_detail->data->time['end'] : ''); ?></abbr></dd>
							<?php else : ?>
								<dd><abbr class="mec-events-abbr"><?php _e('All of the day', 'modern-events-calendar-lite'); ?></abbr></dd>
							<?php endif; ?>
						</div>
					<?php
				}
			}
			echo '</div>';
		$html = ob_get_clean();

		return $html;
	}
}
