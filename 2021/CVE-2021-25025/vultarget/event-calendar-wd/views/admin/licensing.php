<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ecwd_settings;
global $ecwd_tabs;
?>

<div class="wrap">
	<div id="ecwd-settings">

		<div id="ecwd-settings-content">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<div id="featurs_tables"><span id="previous_table"></span>

				<div id="featurs_table1">
          <span><?php _e('WordPress 3.9+ ready','event-calendar-wd')?></span>
					<span><?php _e('SEO-friendly','event-calendar-wd')?></span>
          <span><?php _e('Responsive design','event-calendar-wd')?></span>
					<span><?php _e('Unlimited number of calendars/events','event-calendar-wd')?></span>
					<span><?php _e('Structured event markup (microdata)','event-calendar-wd')?></span>
					<span><?php _e('Event categories','event-calendar-wd')?></span>
					<span><?php _e('Event tag','event-calendar-wd')?></span>
					<span><?php _e('Support for venues','event-calendar-wd')?></span>
					<span><?php _e('Support for organizers','event-calendar-wd')?></span>
					<span><?php _e('Google Maps integration','event-calendar-wd')?></span>
					<span><?php _e('Event search','event-calendar-wd')?></span>
					<span><?php _e('Social media integration','event-calendar-wd')?></span>
					<span><?php _e('Month, week, day, list views','event-calendar-wd')?></span>
					<span><?php _e('Recurring events','event-calendar-wd')?></span>
					<span><?php _e('5 beautiful customizable themes','event-calendar-wd')?></span>
					<span><?php _e('Posterboard view','event-calendar-wd')?></span>
					<span><?php _e('4 days view','event-calendar-wd')?></span>
					<span><?php _e('Map view','event-calendar-wd')?></span>
					<span><?php _e('Add ons support','event-calendar-wd')?></span>

				</div>
				<div id="featurs_table2">
					<span style="padding-top: 18px;height: 39px;"><?php _e('Free','event-calendar-wd')?></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="no"></span>
					<span class="no"></span>
					<span class="no"></span>
					<span class="no"></span>
					<span class="no"></span>
					<span class="no"></span>

				</div>
				<div id="featurs_table3"><span><?php _e('Premium Version','event-calendar-wd')?></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
					<span class="yes"></span>
				</div>

			</div>
			<div style="float: left; clear: both;">
				<p><?php _e('After purchasing the commercial version follow these steps','event-calendar-wd')?>:</p>
				<ol>
					<li><?php _e('Deactivate Event Calendar WD plugin.','event-calendar-wd')?></li>
					<li><?php _e('Delete Event Calendar WD plugin.','event-calendar-wd')?></li>
					<li><?php _e('Install the downloaded commercial version of the plugin.','event-calendar-wd')?></li>
				</ol>
			</div>
		</div>
