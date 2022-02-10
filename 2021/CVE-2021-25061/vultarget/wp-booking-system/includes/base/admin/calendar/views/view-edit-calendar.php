<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$calendar_id = absint(!empty($_GET['calendar_id']) ? $_GET['calendar_id'] : 0);
$calendar = wpbs_get_calendar($calendar_id);

if (is_null($calendar)) {
    return;
}

$current_year = (!empty($_GET['year']) ? absint($_GET['year']) : current_time('Y'));
$current_month = (!empty($_GET['month']) ? absint($_GET['month']) : current_time('n'));

$settings = get_option('wpbs_settings', array());

$removable_query_args = wp_removable_query_args();

?>

<div class="wrap wpbs-wrap wpbs-wrap-edit-calendar">

	<form method="POST" action="" autocomplete="off">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __('Edit Calendar', 'wp-booking-system'); ?><span class="wpbs-heading-tag"><?php printf(__('Calendar ID: %d', 'wp-booking-system'), $calendar_id);?></span></h1>

		<!-- Page Heading Actions -->
		<div class="wpbs-heading-actions">

			<!-- Back Button -->
			<a href="<?php echo add_query_arg(array('page' => 'wpbs-calendars'), admin_url('admin.php')); ?>" class="button-secondary"><?php echo __('Back to all calendars','wp-booking-system') ?></a>

			<!-- Save button -->
			<input type="submit" class="wpbs-save-calendar button-primary" value="<?php echo __('Save Calendar', 'wp-booking-system'); ?>" />

		</div>

		<hr class="wp-header-end" />

		<div id="poststuff">

			<!-- Calendar Title -->
			<div id="titlediv">
				<div id="titlewrap">
					<input type="text" name="calendar_name" size="30" value="<?php echo esc_attr($calendar->get('name')) ?>" id="title">

					<?php if(isset($settings['active_languages']) && count($settings['active_languages']) > 0): ?>

						<a href="#" class="titlewrap-toggle"><?php echo __('Translate calendar title','wp-booking-system') ?> <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" ><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" class=""></path></svg></a>
						<div class="titlewrap-translations">
							<?php foreach($settings['active_languages'] as $language): ?>
								<div class="titlewrap-translation">
									<div class="titlewrap-translation-flag"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>assets/img/flags/<?php echo $language;?>.png" /></div>
									<input type="text" name="calendar_name_translation_<?php echo $language;?>" size="30" value="<?php echo esc_attr( WPBS_get_calendar_meta($calendar->get('id'), 'calendar_name_translation_' . $language, true) ) ?>" >
								</div>
							<?php endforeach; ?>
						</div>

					<?php endif ?>
				</div>
			</div>

			<div id="wpbs-bookings-postbox">
				<!-- Availability -->
				<div class="postbox">

					<h3 class="hndle"><?php echo __('Bookings', 'wp-booking-system'); ?></h3>

					<div class="inside">

						<div id="wpbs-bookings">
							<?php
								$bookings_outputter = new WPBS_Bookings_Outputter($calendar_id);
								$bookings_outputter->display();
							?>
						</div>

					</div>
				</div>
			</div>

			<div id="post-body" class="metabox-holder columns-2">

				<!-- Main Post Body Content -->
				<div id="post-body-content">

					<!-- Availability -->
					<div class="postbox">

						<h3 class="hndle"><?php echo __('Edit Dates', 'wp-booking-system'); ?></h3>

						<div class="inside">

							<div id="wpbs-calendar-events">
								<?php
								$calendar_args = array(
									'current_year' => $current_year,
									'current_month' => $current_month,
								);

								$calendar_editor = new WPBS_Calendar_Editor_Outputter($calendar, $calendar_args);
								$calendar_editor->display();

								
								?>
							</div>

						</div>
					</div>


					<?php

					/**
					 * Action hook to add extra form fields to the main calendar edit area
					 *
					 * @param WPBS_Calendar $calendar
					 *
					 */
					do_action('wpbs_view_edit_calendar_main', $calendar);

					?>

				</div><!-- / Main Post Body Content -->

				<!-- Sidebar Content -->
				<div id="postbox-container-1" class="postbox-container">

		 			<!-- Calendar -->
		 			<div class="postbox">

						<h3 class="hndle"><?php echo __('Calendar', 'wp-booking-system'); ?></h3>

						<div class="inside">

							<?php
							$calendar_args = array(
								'current_year' => $current_year,
								'current_month' => $current_month,
								'start_weekday' => 1,
								'show_title' => 0,
								'show_legend' => 0,
							);

							$calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);
							$calendar_outputter->display();
							?>

						</div>
					</div><!-- / Calendar -->

					<?php

					/**
					 * Action hook to add extra form fields to the main calendar edit area
					 *
					 * @param WPBS_Calendar $calendar
					 *
					 */
					do_action('wpbs_view_edit_calendar_sidebar_before', $calendar);

					?>

					<!-- Calendar Legend -->
		 			<div class="postbox">

						<h3 class="hndle"><?php echo __('Legend', 'wp-booking-system'); ?></h3>

						<div class="inside">

							<?php
							$legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar_id));

							foreach ($legend_items as $legend_item) {

								echo '<div class="wpbs-legend-item">';
								echo wpbs_get_legend_item_icon($legend_item->get('id'), $legend_item->get('type'), $legend_item->get('color'));
								echo '<span class="wpbs-legend-item-name">' . $legend_item->get('name') . '</span>';
								echo '</div>';

							}
							?>

						</div>

						<div class="wpbs-plugin-card-bottom plugin-card-bottom">
							<a class="button-secondary" href="<?php echo add_query_arg(array('subpage' => 'view-legend'), remove_query_arg($removable_query_args)); ?>"><?php echo __('Edit Legend Items', 'wp-booking-system'); ?></a>
						</div>

					</div><!-- / Calendar Legend -->

					<!-- iCal Export -->
					<div class="postbox">

						<h3 class="hndle"><?php echo __('iCal Import/Export', 'wp-booking-system'); ?></h3>

						<div class="inside">

							<p><?php echo __('To configure the iCal import & export settings and have access to the iCal export link please click the button below.', 'wp-booking-system'); ?></p>

							<a href="<?php echo add_query_arg(array('subpage' => 'ical-import-export'), remove_query_arg($removable_query_args)); ?>" class="button-secondary"><?php echo __('iCal Import/Export', 'wp-booking-system'); ?></a>

						</div>

					</div><!-- / iCal Export -->


					<?php

					/**
					 * Action hook to add extra form fields to the main calendar edit area
					 *
					 * @param WPBS_Calendar $calendar
					 *
					 */
					do_action('wpbs_view_edit_calendar_sidebar_after', $calendar);

					?>

				</div><!-- / Sidebar Content -->

			</div><!-- / #post-body -->

		</div><!-- / #poststuff -->

		<!-- Hidden fields -->
		<input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />

		<!-- Save button -->
		<input type="submit" class="wpbs-save-calendar button-primary" value="<?php echo __('Save Calendar', 'wp-booking-system'); ?>" />

		<!-- Save Button Spinner -->
		<div class="wpbs-save-calendar-spinner spinner"><!-- --></div>

		<div id="wpbs-placeholder-editor">
			<?php wp_editor('','wpbs_placeholder_editor', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)); ?>
		</div>

	</form>

</div>