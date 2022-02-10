<?php

namespace MEC\SingleBuilder\Widgets\EventOrganizers;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventOrganizers extends WidgetBase {

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
		$single        = new \MEC_skin_single();
		$organizers    = isset($event_detail->data->organizers) ? $event_detail->data->organizers : array();
		$primary_organizer_id = isset($event_detail->data->meta['mec_organizer_id']) ? $event_detail->data->meta['mec_organizer_id'] : '';

		$html = '';
		if ( true === $this->is_editor_mode && ( empty($organizers) || !isset($organizers[$primary_organizer_id]) ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if organizer is set. In order for the widget in this page to be displayed correctly, please set organizer for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/organizer-and-other-organizer/" target="_blank">' . __('How to set organizer', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		}  elseif ( !empty($organizers) && isset($organizers[$primary_organizer_id]) && !empty($organizers[$primary_organizer_id])) {

			ob_start();
			$organizer = $organizers[$primary_organizer_id];

			echo '<div class="mec-event-meta">';
				?>
				<div class="mec-single-event-organizer">
					<?php if(isset($organizer['thumbnail']) and trim($organizer['thumbnail'])): ?>
						<img class="mec-img-organizer" src="<?php echo esc_url($organizer['thumbnail']); ?>" alt="<?php echo (isset($organizer['name']) ? $organizer['name'] : ''); ?>">
					<?php endif; ?>
					<h3 class="mec-events-single-section-title"><?php echo Base::get_main()->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')); ?></h3>
					<?php if(isset($organizer['thumbnail'])): ?>
						<dd class="mec-organizer">
							<i class="mec-sl-home"></i>
							<h6><?php echo (isset($organizer['name']) ? $organizer['name'] : ''); ?></h6>
						</dd>
					<?php endif;
					if(isset($organizer['tel']) && !empty($organizer['tel'])): ?>
					<dd class="mec-organizer-tel">
						<i class="mec-sl-phone"></i>
						<h6><?php _e('Phone', 'modern-events-calendar-lite'); ?></h6>
						<a href="tel:<?php echo $organizer['tel']; ?>"><?php echo $organizer['tel']; ?></a>
					</dd>
					<?php endif;
					if(isset($organizer['email']) && !empty($organizer['email'])): ?>
					<dd class="mec-organizer-email">
						<i class="mec-sl-envelope"></i>
						<h6><?php _e('Email', 'modern-events-calendar-lite'); ?></h6>
						<a href="mailto:<?php echo $organizer['email']; ?>"><?php echo $organizer['email']; ?></a>
					</dd>
					<?php endif;
					if(isset($organizer['url']) && !empty($organizer['url']) and $organizer['url'] != 'http://'): ?>
					<dd class="mec-organizer-url">
						<i class="mec-sl-sitemap"></i>
						<h6><?php _e('Website', 'modern-events-calendar-lite'); ?></h6>
						<span><a href="<?php echo (strpos($organizer['url'], 'http') === false ? 'http://'.$organizer['url'] : $organizer['url']); ?>" class="mec-color-hover" target="_blank"><?php echo $organizer['url']; ?></a></span>
					</dd>
					<?php endif;
					$organizer_description_setting = isset( $set['organizer_description'] ) ? $set['organizer_description'] : ''; $organizer_terms = get_the_terms($event_detail->data, 'mec_organizer');  if($organizer_description_setting == '1'): foreach($organizer_terms as $organizer_term) { if ($organizer_term->term_id == $organizer['id'] ) {  if(isset($organizer_term->description) && !empty($organizer_term->description)): ?>
					<dd class="mec-organizer-description">
						<p><?php echo $organizer_term->description;?></p>
					</dd>
					<?php endif; } } endif; ?>
				</div>
				<?php
				$single->show_other_organizers($event_detail); // Show Additional Organizers
			echo '</div>';
			$html = ob_get_clean();
		}

		return $html;
	}
}