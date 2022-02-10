<?php

namespace MEC\SingleBuilder\Widgets\EventMoreInfo;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventMoreInfo extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !(isset($event_detail->data->meta['mec_more_info']) && $event_detail->data->meta['mec_more_info'] != '') ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if read more is set. In order for the widget in this page to be displayed correctly, please set read more for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/add-event/" target="_blank">' . __('How to set read more', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
			if (isset($event_detail->data->meta['mec_more_info']) && trim($event_detail->data->meta['mec_more_info']) && $event_detail->data->meta['mec_more_info'] != 'http://') {
				?>
				<div class="mec-event-meta">
					<div class="mec-event-more-info">
						<i class="mec-sl-info"></i>
						<h3 class="mec-more-info-label"><?php echo Base::get_main()->m('more_info_link', __('More Info', 'modern-events-calendar-lite')); ?></h3>
						<dd class="mec-events-event-more-info"><a class="mec-more-info-button a mec-color-hover" target="<?php echo (isset($event_detail->data->meta['mec_more_info_target']) ? $event_detail->data->meta['mec_more_info_target'] : '_self'); ?>" href="<?php echo $event_detail->data->meta['mec_more_info']; ?>"><?php echo ((isset($event_detail->data->meta['mec_more_info_title']) && trim($event_detail->data->meta['mec_more_info_title'])) ? $event_detail->data->meta['mec_more_info_title'] : __('Read More', 'modern-events-calendar-lite')); ?></a></dd>
					</div>
				</div>
				<?php
			}
			$html = ob_get_clean();
		}

		return $html;
	}
}
