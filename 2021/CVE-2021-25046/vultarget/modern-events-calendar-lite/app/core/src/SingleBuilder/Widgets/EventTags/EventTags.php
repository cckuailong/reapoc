<?php

namespace MEC\SingleBuilder\Widgets\EventTags;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventTags extends WidgetBase {

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

		$tags = get_the_tags( $event_id );

		$html = '';
		if ( true === $this->is_editor_mode && empty( $tags ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if tags is set. In order for the widget in this page to be displayed correctly, please set tags for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/tags/" target="_blank">' . __('How to set tags', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			ob_start();
				echo '<div class="mec-events-meta-group mec-events-meta-group-tags">';
					echo __( 'Tags: ', 'modern-events-calendar-lite' );
					if ( $tags ) {
						echo implode(
							', ',
							array_map(
								function($tag) {
									return '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . ' </a>';
								},  $tags
							)
						);
					}
				echo '</div>';
			$html = ob_get_clean();
		}

		return $html;
	}
}
