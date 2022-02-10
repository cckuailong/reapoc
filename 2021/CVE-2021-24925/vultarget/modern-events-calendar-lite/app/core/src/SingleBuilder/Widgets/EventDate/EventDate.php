<?php

namespace MEC\SingleBuilder\Widgets\EventDate;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventDate extends WidgetBase {

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

		$start_timestamp = isset($event_detail->date['start']) ? $event_detail->date['start'] : array();
		$end_timestamp = isset($event_detail->date['end']) ? $event_detail->date['end'] : array();
		$date_format = isset($settings['single_date_format1']) ? $settings['single_date_format1'] : get_option( 'date_format' );
		$date_label = Base::get_main()->date_label( $start_timestamp, $end_timestamp, $date_format );

		$html = '';

		ob_start();
			?>
			<div class="mec-event-meta">
				<div class="mec-single-event-date">
					<i class="mec-sl-calendar"></i>
					<h3 class="mec-date"><?php _e('Date', 'modern-events-calendar-lite'); ?></h3>
					<dl class="mec-events-event-date">
						<dd><abbr class="mec-events-abbr"><?php echo $date_label; ?></abbr></dd>
					</dl>
					<?php echo Base::get_main()->holding_status( $event_detail ); ?>
				</div>
			</div>
			<?php

		$html = ob_get_clean();

		return $html;
	}
}
