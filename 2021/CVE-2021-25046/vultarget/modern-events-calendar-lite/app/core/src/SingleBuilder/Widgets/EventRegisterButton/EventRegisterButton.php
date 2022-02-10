<?php

namespace MEC\SingleBuilder\Widgets\EventRegisterButton;

use MEC\Base;
use MEC\SingleBuilder\Widgets\WidgetBase;

class EventRegisterButton extends WidgetBase {

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

		$html = '';
		if ( true === $this->is_editor_mode && ( !isset($settings['single_booking_style']) || !$settings['single_booking_style'] ) ) {

			$html = '<div class="mec-content-notification"><p>'
					.'<span>'. __('This widget is displayed if register button is set. In order for the widget in this page to be displayed correctly, please set register button for your last event.', 'modern-events-calendar-lite').'</span>'
					. '<a href="https://webnus.net/dox/modern-events-calendar/booking/" target="_blank">' . __('How to set register button', 'modern-events-calendar-lite') . ' </a>'
				.'</p></div>';
		} else {

			$single         = new \MEC_skin_single();

			$mec_more_info = isset($event_detail->data->meta['mec_more_info']) && trim($event_detail->data->meta['mec_more_info']) && $event_detail->data->meta['mec_more_info'] != 'http://';
			$mec_more_info_target = isset($event_detail->data->meta['mec_more_info_target']) ? $event_detail->data->meta['mec_more_info_target'] : '_self';
			if (isset($event_detail->data->meta['mec_more_info_title']) && trim($event_detail->data->meta['mec_more_info_title'])){
				$button_text = esc_html(trim($event_detail->data->meta['mec_more_info_title']), 'modern-events-calendar-lite');
			}else{
				$button_text = esc_html(Base::get_main()->m('register_button', __('REGISTER', 'modern-events-calendar-lite')));
			}

			$classes = '';
			if (isset($settings['single_booking_style']) && $settings['single_booking_style'] != 'modal'){
				$classes = 'simple-booking';
			}
			ob_start();
			?>
			<!-- Register Booking Button -->
			<div class="mec-reg-btn mec-frontbox">
				<?php if (Base::get_main()->can_show_booking_module($event_detail)) : ?>
					<?php

					$data_lity = '';
					if (isset($settings['single_booking_style']) && $settings['single_booking_style'] == 'modal') {
						$data_lity = 'data-lity';
						$classes .= ' mec-booking-data-lity';
					}
					?>
					<a class="mec-booking-button mec-bg-color <?php echo $classes; ?>" href="#mec-events-meta-group-booking-<?php echo $single->uniqueid; ?>" <?php echo $data_lity; ?>><?php echo $button_text; ?></a>
					<script>
					// Fix modal booking in some themes
					if( 'undefined' === typeof mec_booking_lity_init){

						function mec_booking_lity_init(e){

							e.preventDefault();
							var book_id =  jQuery(this).attr('href');
							lity(book_id);

							return false;
						}
						jQuery( ".mec-booking-button.mec-booking-data-lity" ).on('click',mec_booking_lity_init);
					}
					</script>
				<?php elseif ( $mec_more_info ) : ?>
					<a target="<?php echo $mec_more_info_target; ?>" class="mec-booking-button mec-bg-color" href="<?php echo $event_detail->data->meta['mec_more_info']; ?>">
						<?php echo $button_text; ?>
					</a>
				<?php endif; ?>
			</div>
			<?php
			$html = ob_get_clean();
		}

		return $html;
	}
}
