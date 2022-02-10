<?php

namespace MEC\SingleBuilder\Widgets\EventLabels;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventLabels extends WidgetBase {

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
		$labels    = isset($event_detail->data->labels) ? $event_detail->data->labels : array();

		$html = '';
		ob_start();
		if ( empty($labels) && true === $this->is_editor_mode ) {

			echo '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if label is set. In order for the widget in this page to be displayed correctly, please set label for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/label/" target="_blank">' . __('How to set label', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} elseif ( !empty($labels) ) {

			echo '<div class="mec-event-meta">';
			$mec_items = count($labels);
			$mec_i = 0; ?>
			<div class="mec-single-event-label">
				<i class="mec-fa-bookmark-o"></i>
				<h3 class="mec-cost"><?php echo Base::get_main()->m('taxonomy_labels', __('Labels', 'modern-events-calendar-lite')); ?></h3>
				<?php foreach ($labels as $k => $label) :
					$seperator = (++$mec_i === $mec_items) ? '' : ',';
					echo '<dd style="color:' . $label['color'] . '">' . $label["name"] . $seperator . '</dd>';
				endforeach; ?>
			</div>
			<?php
			echo '</div>';
		}
		$html = ob_get_clean();

		return $html;
	}
}
