<?php

namespace MEC\SingleBuilder\Widgets\EventSocialShare;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventSocialShare extends WidgetBase {

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
		if ( true === $this->is_editor_mode && ( !isset($settings['social_network_status']) || !$settings['social_network_status'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
				.'<span>'. __('This widget is displayed if social networks is set. In order for the widget in this page to be displayed correctly, please set label for your last event.', 'modern-events-calendar-lite').'</span>'
				. '<a href="https://webnus.net/dox/modern-events-calendar/social-networks/" target="_blank">' . __('How to set social networks', 'modern-events-calendar-lite') . ' </a>'
			.'</p></div>';
		} else {

			$single         = new \MEC_skin_single();
			ob_start();
				$url = isset($event_detail->data->permalink) ? $event_detail->data->permalink : '';
				if (trim($url) == '') {
					return;
				}
				$socials = Base::get_main()->get_social_networks();
				?>
				<div class="mec-event-social mec-frontbox">
					<h3 class="mec-social-single mec-frontbox-title"><?php _e('Share this event', 'modern-events-calendar-lite'); ?></h3>
					<div class="mec-event-sharing">
						<div class="mec-links-details">
							<ul>
								<?php
								$social_networks = isset($settings['sn']) && is_array($settings['sn']) ? $settings['sn'] : array();
								foreach ($socials as $social) {
									$social_id = $social['id'];
									$is_enabled = isset($social_networks[$social_id]) && !$social_networks[$social_id];
									if ( $is_enabled ) {
										continue;
									}

									if (is_callable($social['function'])) {
										echo call_user_func($social['function'], $url, $event_detail);
									}
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<?php
			$html = ob_get_clean();
		}

		return $html;
	}
}
