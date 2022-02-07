<?php

class DomainCheckAdminHeader {

	private static $m_navCount = 0;

	public static function admin_header($nav = true, $nav_pages = null, $current_page = null) {
		?>
		<style type="text/css">
			.domain-check-link-icon {
				text-underline: none !important;
				text-decoration: none !important;
			}
			.svg-fill-red path {
				fill: #ff0000;
			}
			.svg-fill-green path {
				fill: #00AA00;
			}
			.svg-fill-blue path {
				fill: #0000FF;
			}
			.svg-fill-gray path {
				fill: #4D4D4D;
			}

			.svg-fill-disabled path {
				fill: #CDCDCD;
			}

			.svg-fill-error path,
			.svg-fill-status-1 path,
			.svg-fill-taken path {
				fill: #dd3d36;
			}
			.svg-fill-success path,
			.svg-fill-updated path,
			.svg-fill-status-0 path,
			.svg-fill-available path {
				fill: #7ad03a;
			}

			.svg-fill-status-2 path,
			.svg-fill-owned path {
				fill: #0000AA;
			}
			.svg-fill-update-nag path {
				fill: #ffba00;
			}
			.svg-icon-h1 {
				height: 30px;
				width: auto;
				display: inline-block;
				margin-right: 5px;
			}
			.svg-icon-h2 {
				height: 24px;
				width: auto;
				display: inline-block;
				margin-right: 5px;
			}
			.svg-icon-h3 {
				height: 20px;
				width: auto;
				display: inline-block;
				margin-right: 5px;
			}
			.svg-icon-table {
				height: 16px;
				width: auto;
				display: inline-block;
			}
			.svg-icon-table-links {
				margin-left: 6px;
				margin-right: 6px;
			}
			.svg-icon-table-links:hover {
				background-color: #aaaaaa;
			}
			.svg-icon-table-small {
				height: 8px;
			}
			.svg-icon-table-mid {
				height: 14px;
			}
			.svg-icon-admin-notice {
				height: 24px;
				width: auto;
				display: inline-block;
				margin-right: 5px;
			}

			div.notice-owned {
				border-color: #0000AA;
			}

			.setting-div {
				max-width: 44%;
				min-width: 44%;
				padding: 10px;
				margin: 10px;
				background-color: #ffffff;
				display: inline-block;
				vertical-align: top;
			}

			.setting-box-lg {
				max-width: 90%;
				min-width: 90%;
				padding: 10px;
				margin: 10px;
				background-color: #ffffff;
				display: inline-block;
				vertical-align: top;
			}

			.domain-check-nav-div-dashboard {
				margin: 5px;
				padding: 5px;
				padding-top: 0px;
				margin-top: 0px;
				margin-left: -10px;
				width:101%;
				float: left;
			}
			.domain-check-nav-div {
				margin: 5px;
				padding: 5px;
				padding-top: 0px;
				margin-top: 0px;
				width:auto;
				float: right;
			}

			.domain-check-admin-search-input {
				font-size: 32px;
				padding-top: 5px;
				padding-bottom: 5px;
				width: 35%;
			}
			.domain-check-admin-search-input-btn {
				height: 46px !important;
				padding-top: 10px !important;
			}
			.domain-check-admin-search-input-dashboard {
				font-size: 18px;
			}
			.domain-check-admin-search-input-dashboard-btn {

			}
			#poststuff #post-body.columns-2 {
				margin-right: 0px;
			}

			.domain-check-admin-nav-mobile {
				display: none;
			}

			a.domain-check-admin-nav-button {
				display: inline-block;
				margin-right: 3px !important;
				margin-top: 5px !important;
			}

			.domain-check-footer-container {
				width: 100%;
				margin-top: 30px;
				float: left;
			}

			.domain-check-footer-col {
				width: 30%;
				vertical-align: middle;
				text-align: center;
				display: inline-block;
			}

			.domain-check-coupon-ad {
				display: inline-block;
				margin: 5px;
				padding: 10px;
				background-color: #ffffff;
				width: 20%;
				float: left;
				border: 2px black dashed;
			}

			.domain-check-coupon-ad-dashboard {
				width: 80%;
			}

			.domain-check-img-ad {
				display: inline-block;
				margin: 5px;
				padding: 5px;
				background-color: #ffffff;
				max-width: 100%;
				overflow: hidden;
				float: left;
			}

			.domain-check-admin-dashboard-search-box {
				width: 44%;
				display: inline-block;
				float: left;
				background-color: #ffffff;
				padding:10px;
				margin:10px;
			}

			.domain-check-dasboard-table-tr {
				height: 28px;
			}

			.domain-check-admin-search-input {
				font-size: 30px;
			}

			.domain-check-profile-code {
				white-space: pre-wrap;
				word-wrap: break-word;
			}

			.domain-check-profile-li {
				width: 100%;
			}
			.domain-check-profile-li:hover{
				background-color: #F4F4F4;
			}
			.domain-check-profile-li-div-left {
				width: 48%;
				text-align: left;
				display: inline-block;
				height: 100%;
			}
			.domain-check-profile-li-div-right {
				width: 45%;
				display: inline-block;
				padding-right: 10px;
				height: 100%;
			}

			.domain-check-text-input {
				font-size: 18px;
			}

			.domain-check-profile-settings-input {
				width: 100%;
			}
			.domain-check-profile-settings-textarea {
				width: 100%;
			}

			#the-list > tr:hover {
				background-color: #e9e9e9;
			}

			.autorenew-link {
				cursor: pointer;
			}

			@media (min-width: 782px) {
				.hidden-desktop {
					display: none;
				}
			}

			@media (max-width: 782px) {

				.column-links {
					text-align: left;
				}

				.setting-div {
					max-width: 90%;
					min-width: 90%;
				}

				.domain-check-admin-search-input {
					width: 100%;
				}

				.domain-check-title-text {
					display: none;
				}

				.domain-check-nav-div-dashboard {
					display: none;
				}

				.domain-check-admin-nav-mobile {
					display: inline-block;
					cursor: pointer;
					width: 48%;
				}

				a.domain-check-admin-nav-button,
				div.domain-check-admin-nav-button,
				button.domain-check-admin-nav-button
				{
					width: 100%;
				}

				a.domain-check-admin-nav-header-button,
				button.domain-check-admin-nav-header-button {
					width: 100%;
				}

				.domain-check-footer-col {
					width: 100%;
					padding: 10px;
				}

				.domain-check-coupon-ad {
					width: auto;
					min-height: initial;
				}

				.domain-check-admin-dashboard-search-box {
					min-width: 90%;
					max-width: 90%;
				}

				.domain-check-profile-li-div-left-settings {
					display: block;
					width: 100%;
				}

				.domain-check-profile-li-div-right-settings {
					display: block;
					width: 100%
				}

				.hidden-mobile {
					display: none;
				}

				#screen-meta, #screen-meta-links  {
					display: block !important;
				}

			}

		</style>
		<script type="text/javascript">
		/*
		 * Replace all SVG images with inline SVG
		 */

		//search
		var jqueryReady = false;
		jQuery(document).ready(function($) {
			jqueryReady = true;

			jQuery('.updated').each(function(){
				var classList = jQuery(this).attr('class').split(/\s+/);
				var plugin_notice = false;
				for (var i in classList) {
					if (classList[i] == 'domain-check-notice') {
						plugin_notice = true;
					}
				}
				if (!plugin_notice) {
					jQuery(this).css('display', 'none');
				}
			});

			jQuery('.update-nag').each(function(){
				var classList = jQuery(this).attr('class').split(/\s+/);
				var plugin_notice = false;
				for (var i in classList) {
					if (classList[i] == 'domain-check-notice') {
						plugin_notice = true;
					}
				}
				if (!plugin_notice) {
					jQuery(this).css('display', 'none');
				}
			});

			jQuery('.error').each(function(){
				var classList = jQuery(this).attr('class').split(/\s+/);
				var plugin_notice = false;
				for (var i in classList) {
					if (classList[i] == 'domain-check-notice') {
						plugin_notice = true;
					}
				}
				if (!plugin_notice) {
					jQuery(this).css('display', 'none');
				}
			});

			jQuery('img.svg').each(function(){

				//does not have the class set, already colored
				if ( $(this).attr('class').indexOf( ' svg-fill ' ) === (-1) ) {
					return;
				}

				var $img = jQuery(this);
				var imgID = $img.attr('id');
				var imgClass = $img.attr('class');
				var imgURL = $img.attr('src');

				jQuery.get(imgURL, function(data) {
					// Get the SVG tag, ignore the rest
					var $svg = jQuery(data).find('svg');

					// Add replaced image's ID to the new SVG
					if(typeof imgID !== 'undefined') {
						$svg = $svg.attr('id', imgID);
					}
					// Add replaced image's classes to the new SVG
					if(typeof imgClass !== 'undefined') {
						$svg = $svg.attr('class', imgClass+' replaced-svg');
					}

					// Remove any invalid XML tags as per http://validator.w3.org
					$svg = $svg.removeAttr('xmlns:a');

					// Check if the viewport is set, if the viewport is not set the SVG wont't scale.
					if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
						$svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
					}

					// Replace image with new SVG
					$img.replaceWith($svg);

				}, 'xml');

			});
		});
		function domain_check_ajax_call(data, callback) {
			jQuery.post(
				ajaxurl,
				data,
				function (response) {
					response = JSON.parse(response);
					if (response.hasOwnProperty('success') && response.hasOwnProperty('data')) {
						//success!
						if (typeof callback === 'function') {
							callback(response.data)
						}
					} else {
						//errors
						if (response.hasOwnProperty('error')) {
							//create div
							//append to page...
							if (response.hasOwnProperty('error')) {
								jQuery('#domain-check-wrapper').append('<div class="notice error domain-check-notice">').append('<p>' + response.error + '</p>');
							}
							callback(response);
						}
					}
				}
			);
		}

		function watch_trigger_callback(data) {
			var htmlDomain = data.domain.replace(/\./g, '-');
			var iconDir = '<?php echo plugins_url('/images/icons/color/', __FILE__); ?>';
			if (data.watch) {
				jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
				//jQuery('#watch-link-' + htmlDomain).text('Stop Watching');

				var replace = '<img id="watch-image-' + htmlDomain + '" src="' + iconDir + '207-eye.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-gray" onload="paint_svg(\'watch-image-' + htmlDomain + '\')">';
				jQuery('#watch-image-' + htmlDomain).replaceWith(replace);
			} else {
				jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.message + '</p></div>');
				//jQuery('#watch-link-' + htmlDomain).text('Watch');
				var replace = '<img id="watch-image-' + htmlDomain + '" src="' + iconDir + '207-eye-disabled.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-disabled" onload="paint_svg(\'watch-image-' + htmlDomain + '\')">';
				jQuery('#watch-image-' + htmlDomain).replaceWith(replace);
			}
		}

		function ssl_watch_trigger_callback(data) {
			var htmlDomain = data.domain.replace(/\./g, '-');
			var iconDir = '<?php echo plugins_url('/images/icons/color/', __FILE__); ?>';
			if (data.watch) {
				jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
				var replace = '<img id="watch-image-' + htmlDomain + '" src="' + iconDir + 'bell.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-gray" onload="paint_svg(\'watch-image-' + htmlDomain + '\')">';
				jQuery('#watch-image-' + htmlDomain).replaceWith(replace);
			} else {
				jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.message + '</p></div>');
				var replace = '<img id="watch-image-' + htmlDomain + '" src="' + iconDir + '/bell-disabled.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-disabled" onload="paint_svg(\'watch-image-' + htmlDomain + '\')">';
				jQuery('#watch-image-' + htmlDomain).replaceWith(replace);
			}
		}

		function status_trigger_callback(data) {
			var htmlDomain = data.domain.replace('.', '-');
			if (data.status == 2) {
				jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
				jQuery('#status-link-' + htmlDomain).text('Owned');
			} else if (data.status == 1) {
				jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.message + '</p></div>');
				jQuery('#status-link-' + htmlDomain).text('Taken');
			}
		}

		function autorenew_trigger_callback(data) {
			var htmlDomain = data.domain.replace(/\./g, '-');
			var iconDir = '<?php echo plugins_url('/images/icons/color/', __FILE__); ?>';
			if ( data.autorenew ) {
				jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
				//jQuery('#watch-link-' + htmlDomain).text('Stop Watching');
				var replace = '<img id="autorenew-image-' + htmlDomain + '" src="' + iconDir + 'infinity-green.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-updated">';
				jQuery( '#autorenew-image-' + htmlDomain ).replaceWith( replace );
				jQuery( '#autorenew-text-' + htmlDomain ).text( 'on' );
			} else {
				jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.message + '</p></div>');
				//jQuery('#watch-link-' + htmlDomain).text('Watch');
				var replace = '<img id="autorenew-image-' + htmlDomain + '" src="' + iconDir + 'infinity-disabled.svg" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-disabled">';
				jQuery( '#autorenew-image-' + htmlDomain ).replaceWith( replace );
				jQuery( '#autorenew-text-' + htmlDomain ).text( 'off' );
			}
		}

		function paint_svg(elem_id) {

			var $img = jQuery('#' + elem_id);

			//does not have the class set, already colored
			if ( $img.attr('class').indexOf( ' svg-fill ' ) === (-1) ) {
				return;
			}

			var imgID = $img.attr('id');
			var imgClass = $img.attr('class');
			var imgURL = $img.attr('src');

			jQuery.get(
				imgURL,
				function(data) {
					// Get the SVG tag, ignore the rest
					var $svg = jQuery(data).find('svg');

					// Add replaced image's ID to the new SVG
					if(typeof imgID !== 'undefined') {
						$svg = $svg.attr('id', imgID);
					}
					// Add replaced image's classes to the new SVG
					if(typeof imgClass !== 'undefined') {
						$svg = $svg.attr('class', imgClass+' replaced-svg');
					}

					// Remove any invalid XML tags as per http://validator.w3.org
					$svg = $svg.removeAttr('xmlns:a');

					// Check if the viewport is set, if the viewport is not set the SVG wont't scale.
					if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
						$svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
					}

					// Replace image with new SVG
					$img.replaceWith($svg);
				}
			);
		}

		function showHide( elemId, displayStyle ) {
			if ( typeof displayStyle === 'undefined' || !displayStyle ) {
				displayStyle = 'block';
			}
			if ( document.getElementById( elemId ).style.display === 'none' || !document.getElementById( elemId ).style.display ) {
				document.getElementById( elemId ).style.display = displayStyle;
			} else {
				document.getElementById( elemId ).style.display = 'none';
			}
		}

		function update_setting(setting_id) {
			var data_obj = {
				action: "settings",
				method: setting_id
			};
			data_obj[setting_id] = jQuery("#" + setting_id.replace( /_/g, '-' ) ).val();
			switch (setting_id) {
				case 'domain_extension_favorites':
					data_obj = {
						action:"settings",
						method:"domain_extension_favorites",
						domain_extension_favorites: document.getElementById('domain-extension-favorites').value
					};
					break;
				case 'email_additional_emails':
					data_obj = {
						action:"settings",
						method:"email_additional_emails",
						email_additional_emails: document.getElementById('email-additional-emails').value
					};
					break;
				case 'email_primary_email':
					data_obj = {
						action:"settings",
						method:"email_primary_email",
						email_primary_email: document.getElementById('email-primary-email').value
					};
					break;
				case 'coupons_primary_site':
					data_obj = {
						action:"settings",
						method:"coupons_primary_site",
						coupons_primary_site: jQuery("#coupons-primary-site" ).val()
					};
					break;
				case 'email_schedule_cron':
					data_obj = {
						action:"settings",
						method:"email_schedule_cron",
						email_schedule_cron: jQuery("#email-schedule-cron" ).val()
					};
					break;
			}
			domain_check_ajax_call(
				data_obj,
				update_setting_callback
			);
		}

		function update_setting_callback(data) {
			if (!data.hasOwnProperty('error')) {
				jQuery('#domain-check-admin-notices').append('<div class="notice updated domain-check-notice"><p>' + data.message + '</p></div>');
			} else {
				jQuery('#domain-check-admin-notices').append('<div class="notice error domain-check-notice"><p>' + data.error + '</p></div>');
			}
		}
		</script>

		<?php
		if (class_exists('DomainCheckDebug') && isset($_GET['test_ftue'])) {
			DomainCheckDebug::debug();
		}
		if ( function_exists( 'get_option' ) && function_exists('get_current_screen') ) {
			//get ftue array
			$ftue = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'ftue');
			$ftue_exists = true;
			if (!$ftue || !count($ftue)) {
				$ftue = array();
				$ftue_exists = false;
			}
			//check if they have seen the intro message...
			if ( !isset($ftue['intro']) ) {
				$icon = 'circle-www2';
				$options = array();
				$image = '<img src="' . plugins_url('/images/icons/color/' . $icon . '.svg', __FILE__) .'" class="svg svg-icon-admin-notice svg-fill-gray">';

				$message = '<h2>' . $image . 'Welcome to Domain Check!</h2>';
				$message .= '<br>';
				$message .= 'Get started by using <a href="admin.php?page=domain-check-import-export" class="button"><img src="'. plugins_url('/images/icons/color/data-transfer-upload.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">Import</a> to add all of your domains and SSL certificates at once or by using <a href="admin.php?page=domain-check-search" class="button"><img src="'. plugins_url('/images/icons/color/magnifying-glass.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">Domain Search</a> or <a href="admin.php?page=domain-check-ssl-check" class="button"><img src="'. plugins_url('/images/icons/color/lock-locked-gray.svg', __FILE__) . '" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">SSL Check</a> to add them one at a time!';
				$message .= '<br><br>';

				if ($options && is_array($options) && count($options)) {
					$message .= '<br><br>' . "\n";
					$first = true;
					foreach($options as $option_name => $option_url) {
						if (!$first) {
							$message .= ' | ';
						}
						if ($option_name == 'Launch [&raquo]') {
							$message .= '<a href="http://'.$option_url.'" target="_blank">'.$option_name.'</a>';
						} else {
							$message .= '<a href="'.$option_url.'">'.$option_name.'</a>';
						}
						if ($first) {
							$first = false;
						}
					}
				}
				?>
				<div class="updated domain-check-notice">
				<?php echo $message; ?>
				</div>
				<?php
			}

			//if they are on import or search remove the ftue message
			$screen = get_current_screen();
			$screen_page = null;
			if (is_object($screen) && property_exists($screen, 'base') && $screen->base) {
				$screen_page = $screen->base;
				$screen_page = str_replace('toplevel_page_', '', $screen_page);
				$screen_page = str_replace('domains_page_', '', $screen_page);
			}
			if ( !isset($ftue['intro']) &&
				(
					$screen_page == 'domain-check-search'
					|| $screen_page == 'domain-check-import-export'
					|| $screen_page == 'domain-check-ssl-check'
				)
			) {
				$ftue['intro'] = time();
				if ($ftue_exists) {
					update_option( DomainCheckConfig::OPTIONS_PREFIX . 'ftue', $ftue );
				} else {
					add_option( DomainCheckConfig::OPTIONS_PREFIX . 'ftue', $ftue );
				}
			}
		}
		?>
		<div id="domain-check-admin-notices"></div>
		<?php
		if ($nav) {
			self::admin_header_nav($nav_pages, $current_page);
		}
		?>
		<?php
	}

	public static function admin_header_nav( $nav_pages = null, $current_page = null, $section = 'Domain Check' ) {
		//[0] - text name
		//[1] - icon name
		//[2] - sub page
		if (!$nav_pages || !is_array($nav_pages)) {
			$nav_pages = array(
				array(
					'domain-check',
					'Dashboard',
					'circle-www2'
				),
				array(
					'domain-check-your-domains',
					'Your Domains',
					'flag'
				),
				array(
					'domain-check-search',
					'Search',
					'magnifying-glass'
				),
				array(
					'domain-check-watch',
					'Watch',
					'207-eye'
				),
				array(
					'domain-check-ssl-check',
					'SSL Check',
					'lock-locked'
				),
				array(
					'domain-check-ssl-watch',
					'SSL Alerts',
					'bell'
				),
				array(
					'domain-check-import-export',
					'Import',
					'data-transfer-upload'
				),
				array(
					'domain-check-settings',
					'Settings',
					'cog'
				),
				array(
					'domain-check-help',
					'Help',
					'266-question'
				),
				array(
					'domain-check-coupons',
					'Coupons',
					'055-price-tags'
				)
			);
		}

		//find page for mobile nav
		foreach ($nav_pages as $nav_page_idx => $nav_data) {
			$nav_page = $nav_data[0];

			if ($current_page && $current_page == $nav_page) {
				$current_page_name = $nav_data[1];
			}

			//extra route
			if (isset($nav_data[3])) {
				if ($current_page && $current_page == $nav_data[3]) {
					$current_page_name = $nav_data[1];
				}
			}
		}

		?>
		<div id="domain-check-admin-nav-mobile-<?php echo self::$m_navCount; ?>" class="domain-check-admin-nav-mobile" onclick="showHide('domain-check-admin-nav-<?php echo self::$m_navCount; ?>');">
			<button class="btn btn-default button button-default domain-check-admin-nav-button domain-check-admin-nav-header-button">
				<img src="<?php echo plugins_url('/images/icons/color/menu.svg', __FILE__); ?>" class="svg svg-icon-table svg-icon-table-links svg-fill-gray domain-check-link-icon" />
				<?php echo $section; ?>
			</button>
		</div>
		<div id="domain-check-admin-nav-<?php echo self::$m_navCount; ?>" class="domain-check-nav-div-dashboard">
		<?php

		if ( $current_page === null && isset( $_GET['page'] ) ) {
			$current_page = $_GET['page'];
		}

		foreach ($nav_pages as $nav_page_idx => $nav_data) {
			$css_class = 'button';
			$old_nav_page = $nav_data[0];
			$nav_page = $nav_data[0];

			if ($current_page && $current_page == $nav_page) {
				$css_class = 'button-primary';
			}

			//extra route
			if (isset($nav_data[3])) {
				$nav_page = $nav_data[0];
				$nav_page .= '&domain-check-page=' . $nav_data[3];
				$css_class = 'button';
				if ($current_page && $current_page == $nav_data[3]) {
					$css_class = 'button-primary';
				}
			}

			?>
			<a href="admin.php?page=<?php echo $nav_page; ?>" class="<?php echo $css_class; ?> domain-check-admin-nav-button">
				<img src="<?php echo plugins_url('/images/icons/' . $nav_data[2] . '.svg', __FILE__); ?>" class="svg svg-fill svg-icon-table svg-icon-table-links svg-fill-gray">
				<?php echo $nav_data[1]; ?>
			</a>
			<?php
		}
		?>
		</div>
		<div class="hidden-mobile" style="clear:both;"></div>
		<?php
		self::$m_navCount += 1;
	}

	public static function footer() {
		?>
		<div style="clear:both;"></div>
		<div class="domain-check-footer-container">
			<div class="domain-check-footer-col">
				Questions, Comments, and Support<br><br>
				<a href="mailto:info@domaincheckplugin.com">info@domaincheckplugin.com</a>
			</div>
			<div class="domain-check-footer-col">
				<a href="https://wordpress.org/support/plugin/domain-check/reviews/" target="_blank">
					Like Domain Check? Love Domain Check? Leave a Review!
				</a>
				<br><br>
				<a href="https://wordpress.org/support/plugin/domain-check/reviews/" target="_blank" style="text-decoration: none;">
					<span style="color: #ffba00; font-size: 20px; text-decoration: none;">
						&#9733;
						&#9733;
						&#9733;
						&#9733;
						&#9733;
					</span>
				</a>
			</div>
			<div class="domain-check-footer-col">
				Please Tip Your Programmer!<br>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="SWTN8L4NX48EY">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
		</div>
		<?php
	}
}