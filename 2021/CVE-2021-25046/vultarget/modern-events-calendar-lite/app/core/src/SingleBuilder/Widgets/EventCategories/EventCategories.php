<?php


namespace MEC\SingleBuilder\Widgets\EventCategories;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventCategories extends WidgetBase {

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
		$categories    = isset($events_detail->data->categories) ? $events_detail->data->categories : array();

		if ( true === $this->is_editor_mode && empty( $categories ) ) {

			$html = '<div class="mec-content-notification"><p>'
						.'<span>'. __('This widget is displayed if category is set. In order for the widget in this page to be displayed correctly, please set category for your last event.', 'modern-events-calendar-lite').'</span>'
						. '<a href="https://webnus.net/dox/modern-events-calendar/categories/" target="_blank">' . __('How to set category', 'modern-events-calendar-lite') . ' </a>'
					.'</p></div>';
		} elseif ( !empty($categories) ) {

			ob_start();
				echo '<div class="mec-single-event-category mec-event-meta mec-frontbox">';
				?>
				<i class="mec-sl-folder"></i>
				<dt><?php echo Base::get_main()->m('taxonomy_categories', __('Category', 'modern-events-calendar-lite')); ?></dt>
				<?php
				foreach ($categories as $category) {
					$icon = get_metadata('term', $category['id'], 'mec_cat_icon', true);
					$icon = isset($icon) && $icon != '' ? '<i class="' . $icon . ' mec-color"></i>' : '<i class="mec-fa-angle-right"></i>';
					echo '<dd class="mec-events-event-categories">
						<a href="' . get_term_link($category['id'], 'mec_category') . '" class="mec-color-hover" rel="tag">' . $icon . $category['name'] . '</a></dd>';
				}
				echo '</div>';
			$html = ob_get_clean();
		}

		return $html;
	}
}
