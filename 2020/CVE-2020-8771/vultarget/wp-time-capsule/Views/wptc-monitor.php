<?php
/**
* A class with functions the perform a backup of WordPress
*
* @copyright Copyright (C) 2011-2014 Awesoft Pty. Ltd. All rights reserved.
* @author Michael De Wildt (http://www.mikeyd.com.au/)
* @license This program is free software; you can redistribute it and/or modify
*          it under the terms of the GNU General Public License as published by
*          the Free Software Foundation; either version 2 of the License, or
*          (at your option) any later version.
*
*          This program is distributed in the hope that it will be useful,
*          but WITHOUT ANY WARRANTY; without even the implied warranty of
*          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*          GNU General Public License for more details.
*
*          You should have received a copy of the GNU General Public License
*          along with this program; if not, write to the Free Software
*          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
*/

try {
	$config = WPTC_Factory::get('config');
	$backup = new WPTC_BackupController();
	$config->create_dump_dir(); //creating backup folder in the beginning if its not there
?>

<link href='<?php echo $uri; ?>/fullcalendar-2.0.2/fullcalendar.css?v=<?php echo WPTC_VERSION; ?>' rel='stylesheet' />
<link href='<?php echo $uri; ?>/fullcalendar-2.0.2/fullcalendar.print.css?v=<?php echo WPTC_VERSION; ?>' rel='stylesheet' media='print' />
<link href='<?php echo $uri; ?>/tc-ui.css?v=<?php echo WPTC_VERSION; ?>' rel='stylesheet' />
<script src='<?php echo $uri; ?>/fullcalendar-2.0.2/lib/moment.min.js?v=<?php echo WPTC_VERSION; ?>'></script>
<script src='<?php echo $uri; ?>/fullcalendar-2.0.2/fullcalendar.js?v=<?php echo WPTC_VERSION; ?>'></script>

<?php add_thickbox();?>

<div class="wrap" id="wptc">
			<h2 style="width: 195px; display: inline;"><?php _e('Backups', 'wptc');?>
			<div class="bp-progress-calender" style="display: none">
				<div class="l1 wptc_prog_wrap">
					<div class="bp_progress_bar_cont">
						<span id="bp_progress_bar_note"></span>
						<div class="bp_progress_bar" style="width:0%"></div>
					</div>
					<span class="rounded-rectangle-box-wptc reload-image-wptc" id="refresh-c-status-area-wtpc"></span><div class="last-c-sync-wptc">Last reload: - </div>
				</div>
			</div>
			</h2>

			<div class="top-links-wptc">
				<!-- <a style="position: absolute;right: 5px;top: 10px;" href="https://wptimecapsule.uservoice.com/" target="_blank">Suggest a Feature</a> -->
				<!-- <a style="position: absolute;right: 130px;top: 10px;" href="http://wptc.helpscoutdocs.com/article/7-commonly-asked-questions" target="_blank">Help</a> -->
			</div>

	<div id="progress">
		<div class="loading"><?php _e('Loading...')?></div>
	</div>
</div>

<?php
} catch (Exception $e) {
	echo '<h3>Error</h3>';
	echo '<p>' . __('There was a fatal error loading WordPress Time Capsule. Please fix the problems listed and reload the page.', 'wptc') . '</h3>';
	echo '<p>' . __('If the problem persists please re-install WordPress Time Capsule.', 'wptc') . '</h3>';
	echo '<p><strong>' . __('Error message:') . '</strong> ' . $e->getMessage() . '</p>';
}

wptc_init_monitor_js_keys();

// wp_enqueue_script('wptc-monitor', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Views/wptc-monitor.js', array(), WPTC_VERSION);
