<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// check onces and wordpress rights, else DIE
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
		die('Sorry, but this request is invalid');
	}
}

$ajax_nonce = wp_create_nonce( 'cmp-coming-soon-ajax-secret' );
$this->cmp_purge_cache();
// get all wp pages to array(id->name);
$pages = $this->cmp_get_pages('publish');

if ( isset( $_POST['niteoCS_bypass_id'] ) ) {
	if ( $_POST['niteoCS_bypass_id'] == '' ) {
		update_option('niteoCS_bypass_id', md5( get_home_url() ));
	} else {
		update_option('niteoCS_bypass_id', sanitize_text_field( $_POST['niteoCS_bypass_id'] ));
	}
	
}

if ( isset( $_POST['niteoCS_bypass'] ) && is_numeric($_POST['niteoCS_bypass']) ) {
	update_option('niteoCS_bypass', sanitize_text_field( $_POST['niteoCS_bypass'] ));
}

if ( isset( $_POST['niteoCS_bypass_expire'] ) ) {
	if ( $_POST['niteoCS_bypass_expire'] == '' ) {
		update_option('niteoCS_bypass_expire', 172800);
	} else {
		update_option('niteoCS_bypass_expire', filter_var( $_POST['niteoCS_bypass_expire'], FILTER_SANITIZE_NUMBER_INT ));
	}
	
}

if ( isset( $_POST['niteoCS_page_filter'] ) ) {
	update_option('niteoCS_page_filter', sanitize_text_field( $_POST['niteoCS_page_filter'] ));
}

// update page whitelist if set
if ( isset( $_POST['niteoCS_page-whitelist'] ) ) {

	$whitelist = $_POST['niteoCS_page-whitelist'];
	$sane = false;

	foreach ( $whitelist as $id ) {
		if ( !is_numeric( $id ) ) {
			break;
		} else {
			$sane = true;
		}
	}

	if ( $sane ) {
		$whitelist_json = json_encode( $whitelist );
	}

	update_option('niteoCS_page_whitelist', sanitize_text_field( $whitelist_json ));

} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_page_whitelist', '[]');
}

if ( isset( $_POST['niteoCS-whitelist-custom'] ) ) {
	$url_list = explode("\r\n", $_POST['niteoCS-whitelist-custom']);

	if ( !empty( $url_list) && $url_list[0] !== '' ) {
		foreach ( $url_list as $url ) {
			if ( !empty($url) ) {
				$sanitized_whitelist[] = esc_attr( $url );
			}
		}

		update_option('niteoCS_page_whitelist_custom', json_encode( array_filter($sanitized_whitelist) ));

	} else {
		update_option('niteoCS_page_whitelist_custom', '[]');
	}
}

// update page blacklist if set
if ( isset( $_POST['niteoCS_page-blacklist'] ) ) {

	$blacklist = $_POST['niteoCS_page-blacklist'];
	$sane = false;

	foreach ( $blacklist as $id ) {
		if ( !is_numeric( $id ) ) {
			break;
		} else {
			$sane = true;
		}
	}

	if ( $sane ) {
		$blacklist_json = json_encode( $blacklist );
	}

	update_option('niteoCS_page_blacklist', sanitize_text_field( $blacklist_json ));

} else if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_page_blacklist', '[]');
}

if ( isset( $_POST['niteoCS-blacklist-custom'] ) ) {
	$url_blacklist = explode("\r\n", $_POST['niteoCS-blacklist-custom']);

	if ( !empty( $url_blacklist) && $url_blacklist[0] !== '' ) {
		foreach ( $url_blacklist as $bl_url ) {
			if ( !empty($bl_url) ) {
				$sanitized_blacklist[] = esc_attr( $bl_url );
			}
		}

		update_option('niteoCS_page_blacklist_custom', json_encode( array_filter($sanitized_blacklist) ));

	} else {
		update_option('niteoCS_page_blacklist_custom', '[]');
	}
}


// update cmp bypass roles if set
if ( isset( $_POST['niteoCS_roles'] ) ) {

	$roles = $_POST['niteoCS_roles'];
	$sane = false;

	foreach ( $roles as $id => $role ) {
		$roles[$id] = sanitize_text_field($role);
	}

	update_option('niteoCS_roles', json_encode( $roles ));
	
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_roles', '[]');
}


// update cmp roles topbar access
if ( isset( $_POST['niteoCS_roles_topbar'] ) ) {

	$roles = $_POST['niteoCS_roles_topbar'];
	$sane = false;

	foreach ( $roles as $id => $role ) {
		$roles[$id] = sanitize_text_field($role);
	}

	update_option('niteoCS_roles_topbar', json_encode( $roles ));
	
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_roles_topbar', '[]');
}


// update header scripts
if ( isset( $_POST['niteoCS_head_scripts'] ) ) {

	$head_scripts = $_POST['niteoCS_head_scripts'];
	$sane = false;

	foreach ( $head_scripts as $id => $head_script ) {
		$h_scripts[$id] = sanitize_text_field($head_script);
	}

	update_option('niteoCS_head_scripts', json_encode( $h_scripts ));

} else if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_head_scripts', '[]');
}

// update cmp footer scripts
if ( isset( $_POST['niteoCS_footer_scripts'] ) ) {

	$footer_scripts = $_POST['niteoCS_footer_scripts'];
	$sane = false;

	foreach ( $footer_scripts as $id => $footer_script ) {
		$f_scripts[$id] = sanitize_text_field( $footer_script );
	}

	update_option('niteoCS_footer_scripts', json_encode( $f_scripts ));
	
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
	update_option('niteoCS_footer_scripts', '[]');
}


// Notifications save 

if ( isset( $_POST['niteoCS-mode-change-email-address'] ) ) {
	update_option('niteoCS_mode_change_email_address', sanitize_text_field( $_POST['niteoCS-mode-change-email-address'] ));
}
if ( isset( $_POST['niteoCS-countdown-email-address'] ) ) {
	update_option('niteoCS_countdown_email_address', sanitize_text_field( $_POST['niteoCS-countdown-email-address'] ));
}
if ( isset( $_POST['niteoCS-subscribe-email-address'] ) ) {
	update_option('niteoCS_subscribe_email_address', sanitize_text_field( $_POST['niteoCS-subscribe-email-address'] ));
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if ( isset($_POST['niteoCS-countdown-notification']) ) {
		update_option('niteoCS_countdown_notification', $this->sanitize_checkbox($_POST['niteoCS-countdown-notification']) );
	} else {
		update_option('niteoCS_countdown_notification', '0');
	}

	if ( isset($_POST['niteoCS-mode-change-notification']) ) {
		update_option('niteoCS_mode_change_notification', $this->sanitize_checkbox($_POST['niteoCS-mode-change-notification']) );
	} else {
		update_option('niteoCS_mode_change_notification', '0');
	}
	if ( isset($_POST['niteoCS-subscribe-notification']) ) {
		update_option('niteoCS_subscribe_notification', $this->sanitize_checkbox($_POST['niteoCS-subscribe-notification']) );
	} else {
		update_option('niteoCS_subscribe_notification', '0');
	}

	if ( isset($_POST['niteoCS-rss-status']) ) {
		update_option('niteoCS_rss_status', $this->sanitize_checkbox($_POST['niteoCS-rss-status']) );
	} else {
		update_option('niteoCS_rss_status', '0');
	}

	if ( isset($_POST['niteoCS-restapi-status']) ) {
		update_option('niteoCS_rest_api_status', $this->sanitize_checkbox($_POST['niteoCS-restapi-status']) );
	} else {
		update_option('niteoCS_rest_api_status', '0');
	}
}

if ( isset( $_POST['niteoCS_custom_login_url'] ) ) {
	update_option('niteoCS_custom_login_url', sanitize_text_field( $_POST['niteoCS_custom_login_url'] ));
}

if ( isset( $_POST['niteoCS_wpautop'] ) && is_numeric($_POST['niteoCS_wpautop']) ) {
	update_option('niteoCS_wpautop', sanitize_text_field( $_POST['niteoCS_wpautop'] ));
}

if ( isset( $_POST['cmp_cookie_notice_comp'] ) && is_numeric($_POST['cmp_cookie_notice_comp']) ) {
	update_option('cmp_cookie_notice_comp', sanitize_text_field( $_POST['cmp_cookie_notice_comp'] ));
}



$niteoCS_page_filter 			= get_option('niteoCS_page_filter', '0');
$niteoCS_page_whitelist			= json_decode(get_option('niteoCS_page_whitelist', '[]'), true);
$niteoCS_page_whitelist_custom	= json_decode(get_option('niteoCS_page_whitelist_custom', '[]'), true);
$niteoCS_page_blacklist			= json_decode(get_option('niteoCS_page_blacklist', '[]'), true);
$niteoCS_page_blacklist_custom	= json_decode(get_option('niteoCS_page_blacklist_custom', '[]'), true);
$custom_login_url				= get_option('niteoCS_custom_login_url', get_option( 'whl_page' ));
$niteoCS_roles					= json_decode(get_option('niteoCS_roles', '[]'), true);
$niteoCS_roles_topbar			= json_decode(get_option('niteoCS_roles_topbar', '[]'), true);
$head_scripts					= json_decode(get_option('niteoCS_head_scripts', '[]'), true);
$footer_scripts					= json_decode(get_option('niteoCS_footer_scripts', '[]'), true);
$bypass							= get_option('niteoCS_bypass', '0');
$bypass_id 						= get_option('niteoCS_bypass_id', md5( get_home_url() ));
$bypass_expire 					= get_option('niteoCS_bypass_expire', '172800');
$topbar_icon 					= get_option('niteoCS_topbar_icon', '1');
$topbar_version 				= get_option('niteoCS_topbar_version', 'cmp-topbar-full');
$wpautop 						= get_option('niteoCS_wpautop', '1');

$cmp_countdown_notif 			= get_option('niteoCS_countdown_notification', '1');
$cmp_mode_change_notif 			= get_option('niteoCS_mode_change_notification', '0');
$cmp_subscribe_notif 			= get_option('niteoCS_subscribe_notification', '0');
$countdown_email 				= get_option('niteoCS_countdown_email_address', get_option( 'admin_email' ));
$mode_change_email 				= get_option('niteoCS_mode_change_email_address', get_option( 'admin_email' ));
$subscribe_email 				= get_option('niteoCS_subscribe_email_address', get_option( 'admin_email' ));
$cmp_cookie_notice_comp			= get_option('cmp_cookie_notice_comp', '1');
$cmp_rss 						= get_option('niteoCS_rss_status', '1');
$cmp_rest_api 					= get_option('niteoCS_rest_api_status', '1');
?>


<div class="wrap cmp-coming-soon-maintenance cmp-advanced">

	<h1></h1>
	<div id="icon-users" class="icon32"></div>
	<div class="settings-wrap">
	<form method="post"	action="admin.php?page=cmp-advanced&status=settings-saved" id="csoptions">
		<?php wp_nonce_field('save_options','save_options_field'); ?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-whitelist-blacklist" data-tab="cmp-whitelist-blacklist"><i class="fas fa-lock"></i><?php _e('Whitelist/Blacklist', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-bypass" data-tab="cmp-bypass"><i class="fas fa-user-lock"></i><?php _e('Bypass', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-notifications" data-tab="cmp-notifications"><i class="far fa-envelope"></i><?php _e('Notifications', 'cmp-coming-soon-maintenance');?></a>
			
			<a class="nav-tab" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-misc" data-tab="cmp-misc"><i class="fas fa-wrench"></i><?php _e('Misc', 'cmp-coming-soon-maintenance');?></a>

			<a class="nav-tab" href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-export-import" data-tab="cmp-export-import"><i class="fas fa-download"></i><?php _e('Import/Export', 'cmp-coming-soon-maintenance');?></a>

		</h2>

		<div class="cmp-advanced" style="margin-top:6px">

			<div class="cmp-inputs-wrapper">

				<div class="table-wrapper cmp-whitelist-blacklist">

					<h3 class="no-icon"><?php _e('CMP Page Whitelist and Blacklist Settings', 'cmp-coming-soon-maintenance');?></h3>
					<table>
						<tbody>
						<tr>

							<th>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Whitelist Settings', 'cmp-coming-soon-maintenance');?></span>
									</legend>

									<p>
										<label title="Page Whitelist">
										 	<input type="radio" class="page-whitelist" name="niteoCS_page_filter" value="1" <?php checked('1', $niteoCS_page_filter);?>><?php _e('Page Whitelist', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Page Blacklist">
										 	<input type="radio" class="page-whitelist" name="niteoCS_page_filter" value="2" <?php checked('2', $niteoCS_page_filter);?>><?php _e('Page Blacklist', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Disabled">
										 	<input type="radio" class="page-whitelist" name="niteoCS_page_filter" value="0" <?php checked('0', $niteoCS_page_filter);?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

								</fieldset>
							</th>

							<td>
								<fieldset class="page-whitelist-switch x1">
									<h4><?php _e('CMP Whitelist - set CMP landing page to specific page(s) only', 'cmp-coming-soon-maintenance');?></h4>
									<select name="niteoCS_page-whitelist[]" class="cmp-whitelist-select" multiple="multiple">
										<option value="-1" <?php echo in_array('-1', $niteoCS_page_whitelist) ? 'selected' : '';?>><?php _e('Homepage', 'cmp-coming-soon-maintenance');?></option>
										<?php
										foreach ( $pages as $page ) { ?>
											<option value="<?php echo esc_attr( $page['id'] );?>" <?php echo in_array($page['id'], $niteoCS_page_whitelist) ? 'selected' : ''; ?>><?php echo esc_attr( $page['name'] );?></option>
											<?php 
										} ?>
									</select>

									<p class="cmp-hint" style="margin-top:0"><?php _e('By default CMP is enabled on all pages. Leave this field empty to use default settings.', 'cmp-coming-soon-maintenance');?></p>
									
									<h4><?php _e('You can also add the page URLs manually.', 'cmp-coming-soon-maintenance');?></h4>
									<textarea name="niteoCS-whitelist-custom" cols="40" rows="5"><?php 
										if ( !empty($niteoCS_page_whitelist_custom) ) {
											foreach ($niteoCS_page_whitelist_custom as $wl_url) {
												echo esc_attr($wl_url) . PHP_EOL;
											}
										} ?></textarea>
								</fieldset>

								<fieldset class="page-whitelist-switch x2">
									<h4><?php _e('CMP Blacklist - select the pages to NOT display CMP landing page', 'cmp-coming-soon-maintenance');?></h4>
									<select name="niteoCS_page-blacklist[]" class="cmp-blacklist" multiple="multiple">
										<option value="-1" <?php echo in_array('-1', $niteoCS_page_blacklist) ? 'selected' : '';?>><?php _e('Homepage', 'cmp-coming-soon-maintenance');?></option>
										<?php
										foreach ( $pages as $page ) { ?>
											<option value="<?php echo esc_attr( $page['id'] );?>" <?php echo in_array($page['id'], $niteoCS_page_blacklist) ? 'selected' : '';?>><?php echo esc_attr( $page['name'] );?></option>
											<?php 
										} ?>
									</select>

									<p class="cmp-hint" style="margin-top:0"><?php _e('If you want to exclude some pages from CMP you can select them here.', 'cmp-coming-soon-maintenance');?></p>

									<h4><?php _e('You can also add the page URLs manually.', 'cmp-coming-soon-maintenance');?></h4>
									<textarea name="niteoCS-blacklist-custom" cols="40" rows="5"><?php 
										if ( !empty( $niteoCS_page_blacklist_custom ) ) {
											foreach ( $niteoCS_page_blacklist_custom as $bl_url ) {
												echo esc_attr($bl_url) . PHP_EOL;
											}
										} ?></textarea>
								</fieldset>

								<p class="cmp-hint page-whitelist-switch x1 x2" style="margin-top:0"><?php _e('Insert URL with http(s) separately at each line. Please make sure your Permalinks work correctly!<br> You can also use asterisk (*) as a wildcard to match any part of the URL.', 'cmp-coming-soon-maintenance');?></p>

								<p class="page-whitelist-switch x0"><?php _e('CMP landing page is displayed on all pages by default. You can enable Page Whitelist to display CMP only on specific page(s) or Page Blacklist to exclude CMP landing page on specific page(s) by enabling Page Whitelist or Page Blacklist here.', 'cmp-coming-soon-maintenance');?></p>

							</td>
						</tr>

						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<div class="table-wrapper cmp-whitelist-blacklist">

					<h3 class="no-icon"><?php _e('Custom Login URL', 'cmp-coming-soon-maintenance');?></h3>
					<table>
						<tbody>
						<tr>

							<th><?php _e('Login URL', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<h4><?php _e('Login URL', 'cmp-coming-soon-maintenance');?></h4>
									<input type="text" name="niteoCS_custom_login_url" value="<?php echo esc_html( $custom_login_url ); ?>" class="regular-text"><br>

									<p class="cmp-hint" style="margin-top:0"><?php _e('Insert custom login URL if you are using plugins to change default WordPress login URL.', 'cmp-coming-soon-maintenance');?></p>
									

								</fieldset>

							</td>
						</tr>

						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<!-- BYPASS -->

				<div class="table-wrapper general cmp-bypass">

					<h3 class="no-icon"><?php _e('CMP Bypass by User Roles', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>
						<tr>
							<th><?php _e('Bypass User Roles', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<h4><?php _e('Select User Roles to bypass CMP landing page', 'cmp-coming-soon-maintenance');?></h4>
									
									<select name="niteoCS_roles[]" class="cmp-user_roles" multiple="multiple">

										<?php 
										$roles = get_editable_roles();

										foreach ( $roles as $role => $details ) {

											if ( $role != 'administrator') { ?>
												<option value="<?php echo esc_attr($role);?>" <?php echo in_array($role, $niteoCS_roles) ? 'selected' : '';?>><?php echo esc_attr($details['name']);?></option>
												<?php 
											}
										} ?>

									</select>

									<p class="cmp-hint" style="margin-top:0"><?php _e('Administrator role always bypass CMP by default.', 'cmp-coming-soon-maintenance');?></p>

								</fieldset>

							</td>
						</tr>

						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<div class="table-wrapper general cmp-bypass">
					<h3 class="no-icon"><?php _e('CMP Bypass URL', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>
						<tr>

							<th>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Whitelist Settings', 'cmp-coming-soon-maintenance');?></span>
									</legend>

									<p>
										<label title="Page Whitelist">
										 	<input type="radio" class="cmp-bypass" name="niteoCS_bypass" value="1" <?php checked('1', $bypass);?>><?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Disabled">
										 	<input type="radio" class="cmp-bypass" name="niteoCS_bypass" value="0" <?php checked('0', $bypass);?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

								</fieldset>
							</th>

							<td>

								<fieldset class="cmp-bypass-switch x1">
									
									<h4 style="margin-bottom:0.5em"><?php _e('Bypass URL', 'cmp-coming-soon-maintenance');?></h4>
									<code id="bypass-code"><?php echo get_home_url().'/?cmp_bypass=' . $bypass_id;?></code><br><br>
									<a href="<?php echo admin_url(); ?>admin.php?page=cmp-advanced#cmp-bypass" class="button" id="copy-bypass">Copy to clipboard</a>
										
									<p><?php _e('You can use this URL to bypass CMP maintenance page. Once you access your website with this URL, CMP Cookie will be set with default expiration of 2 days. If the cookie expires, you need to access your website again with this URL.', 'cmp-coming-soon-maintenance');?></p>

									<h4><?php _e('Set Bypass Passphrase', 'cmp-coming-soon-maintenance');?></h4>
									<input type="text" name="niteoCS_bypass_id" value="<?php echo esc_attr( $bypass_id ); ?>" class="regular-text code"><br>

									<p class="cmp-hint" style="margin-top:0"><?php _e('You can use passphrase which contains letters, numbers, underscores or dashes only.', 'cmp-coming-soon-maintenance');?></p>

									<h4><?php _e('Set bypass cookie Expiration Time in seconds', 'cmp-coming-soon-maintenance');?></h4>
									<input type="text" name="niteoCS_bypass_expire" value="<?php echo esc_attr( $bypass_expire ); ?>" class="regular-text code"><br>

									<p class="cmp-hint" style="margin-top:0"><?php _e('You can set custom Bypass CMP Cookie expiration time in seconds (1hour = 3600). Default expiration time is 2 days (172800).', 'cmp-coming-soon-maintenance');?></p>

									<p><?php _e('Please note this solution is using browser cookies which might not work correctly if you are using caching plugins.', 'cmp-coming-soon-maintenance');?></p>

								</fieldset>

								<p class="cmp-bypass-switch x0"><?php _e('You can Enable CMP Bypass where you can set custom URL parameter to bypass CMP page. You can send this URL to anyone who would like to sneak peak into your Website while it is under development or maintanence.', 'cmp-coming-soon-maintenance');?></p>

							</td>
						</tr>

						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>
				<div class="table-wrapper general cmp-bypass">
					<h3 class="no-icon"><?php _e('RSS & REST API', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>
							<tr>
								<th><?php _e('RSS', 'cmp-coming-soon-maintenance');?></th>
								<td>
									<fieldset>
										<label for="cmp-rss-status">
											<input type="checkbox" name="niteoCS-rss-status" id="cmp-rss-status" value="1" <?php checked('1', $cmp_rss);?>><?php _e('Allow RSS', 'cmp-coming-soon-maintenance');?>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><?php _e('REST API', 'cmp-coming-soon-maintenance');?></th>
								<td>
									<fieldset>
										<label for="cmp-restapi-status">
											<input type="checkbox" name="niteoCS-restapi-status" id="cmp-restapi-status" value="1" <?php checked('1', $cmp_rest_api);?>><?php _e('Allow REST API', 'cmp-coming-soon-maintenance');?>
										</label>
									</fieldset>
								</td>
							</tr>

							<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<!-- NOTIFICATIONS -->

				<div class="table-wrapper cmp-notifications">
					<h3 class="no-icon"><?php _e('Email Notifications', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>
							<tr>
								<th><?php _e('Timer Expiration', 'cmp-coming-soon-maintenance');?></th>

								<td>
									<fieldset>
	
										<label for="cmp-countdown-notification">
											<input type="checkbox" class="countdown-toggle" name="niteoCS-countdown-notification" id="cmp-countdown-notification" value="1" <?php checked('1', $cmp_countdown_notif);?>><?php _e('Enable Email notification when Countdown timer expires to 0.', 'cmp-coming-soon-maintenance');?>
										</label>


										<div class="countdown-toggle-switch x1" style="margin-top:1em">
											<h4><?php _e('Specify Email Address', 'cmp-coming-soon-maintenance');?></h4>
											<input type="text" name="niteoCS-countdown-email-address" value="<?php echo esc_attr( $countdown_email ); ?>" class="regular-text code"><br>
										</div>
									</fieldset>
								</td>
							</tr>

							<tr>
								<th><?php _e('CMP Status Change', 'cmp-coming-soon-maintenance');?></th>

								<td>
									<fieldset>

										<label for="cmp-mode-change-notification">
											<input type="checkbox" name="niteoCS-mode-change-notification" class="mode-change-toggle" id="cmp-mode-change-notification" value="1" <?php checked('1', $cmp_mode_change_notif);?>><?php _e('Enable Email notification if Coming Soon Mode is enabled or disabled by the user.', 'cmp-coming-soon-maintenance');?>
										</label>


										<div class="mode-change-toggle-switch x1" style="margin-top:1em">
											<h4><?php _e('Specify Email Address', 'cmp-coming-soon-maintenance');?></h4>
											<input type="text" name="niteoCS-mode-change-email-address" value="<?php echo esc_attr( $mode_change_email ); ?>" class="regular-text code"><br>
										</div>
									</fieldset>
								</td>
							</tr>

							<tr>
								<th><?php _e('New CMP Subscriber', 'cmp-coming-soon-maintenance');?></th>

								<td>
									<fieldset>

										<label for="cmp-subscribe-notification">
											<input type="checkbox" name="niteoCS-subscribe-notification" class="subscribe-toggle" id="cmp-subscribe-notification" value="1" <?php checked('1', $cmp_subscribe_notif);?>><?php _e('Enable Email notification if there is a new CMP Subscriber. Applies only for a default CMP Custom Subscribe list.', 'cmp-coming-soon-maintenance');?>
										</label>


										<div class="subscribe-toggle-switch x1" style="margin-top:1em">
											<h4><?php _e('Specify Email Address', 'cmp-coming-soon-maintenance');?></h4>
											<input type="text" name="niteoCS-subscribe-email-address" value="<?php echo esc_attr( $subscribe_email ); ?>" class="regular-text code"><br>
										</div>
									</fieldset>
								</td>
							</tr>

							<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<!-- MISC -->
				<div class="table-wrapper cmp-misc">

					<h3 class="no-icon"><?php _e('CMP Top Bar', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>

						<tr>
							<th>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('CMP Top Bar', 'cmp-coming-soon-maintenance');?></span>
									</legend>

									<p>
										<label title="Enabled">
										 	<input type="radio" class="cmp-topbar-icon" name="niteoCS_topbar_icon" value="1" <?php checked('1', $topbar_icon);?>><?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Disabled">
										 	<input type="radio" class="cmp-topbar-icon" name="niteoCS_topbar_icon" value="0" <?php checked('0', $topbar_icon);?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>
								</fieldset>
							</th>

							<td>
								<fieldset class="cmp-topbar-icon-switch x1">
									<h4><?php _e('You can specify which roles have access to CMP Top Bar', 'cmp-coming-soon-maintenance');?></h4>
									
									<select name="niteoCS_roles_topbar[]" class="cmp-user_roles" multiple="multiple">
										<?php 
										$roles = get_editable_roles();
										foreach ( $roles as $role => $details ) {

											if ( $role != 'administrator') { ?>
												<option value="<?php echo esc_attr($role);?>" <?php echo in_array( $role, $niteoCS_roles_topbar ) ? 'selected' : '';?>><?php echo esc_attr( $details['name'] );?></option>
												<?php 
											}
										} ?>
									</select>
									<p class="cmp-hint" style="margin-top:0"><?php _e('Administrator role can always access Top Bar', 'cmp-coming-soon-maintenance');?></p>
									
									<h4><?php _e('CMP Top Bar layout', 'cmp-coming-soon-maintenance');?></h4>
									<select name="niteoCS_topbar_version">
										<option value="cmp-topbar-full" <?php selected($topbar_version, 'cmp-topbar-full');?>><?php _e('Full - CMP Icon with description', 'cmp-coming-soon-maintenace');?></option>
										<option value="cmp-topbar-compact" <?php selected($topbar_version, 'cmp-topbar-compact');?>><?php _e('Compact - CMP Icon only ', 'cmp-coming-soon-maintenace');?></option>
									</select>
								
								</fieldset>
								
								<div class="cmp-topbar-icon-switch x0">
									<p><?php _e('CMP Top Bar is disabled.', 'cmp-coming-soon-maintenance');?></p>
									<img src="<?php echo plugins_url('/img/topbar.png', __FILE__);?>" alt="CMP Top Bar">
									<p><?php _e(' Once enabled, you can quickly enable or disable Coming Soon Mode or check out CMP Preview without visiting CMP Settings Plugin page.', 'cmp-coming-soon-maintenance');?></p>
								</div>

							</td>
						</tr>
						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<div class="table-wrapper cmp-misc">

					<h3 class="no-icon"><?php _e('Automatic paragraphs', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>

						<tr>
							<th>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Automatic paragraphs', 'cmp-coming-soon-maintenance');?></span>
									</legend>

									<p>
										<label title="Enabled">
										 	<input type="radio" class="cmp-wpautop" name="niteoCS_wpautop" value="1" <?php checked('1', $wpautop);?>><?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Disabled">
										 	<input type="radio" class="cmp-wpautop" name="niteoCS_wpautop" value="0" <?php checked('0', $wpautop);?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>
								</fieldset>
							</th>

							<td>
								<div class="cmp-wpautop-switch x1">
									<p style="margin-top:0"><?php _e('Paragraphs will be automatically created in the Content area. You can disable this if you have issues with rendering 3rd party shortcodes in the content.', 'cmp-coming-soon-maintenance');?></p>
								</div>
								
								<div class="cmp-wpautop-switch x0">
									<p style="margin-top:0"><?php _e('Automatic paragraphs are disabled.', 'cmp-coming-soon-maintenance');?></p>
								</div>

							</td>
						</tr>
						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<div class="table-wrapper cmp-misc">

					<h3 class="no-icon"><?php _e('Custom External Scripts', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>
						<tr>
							<th><?php _e('Head Scripts', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<h4><?php _e('Insert Javascript or CSS external file URL', 'cmp-coming-soon-maintenance');?></h4>
									<div id="wrapper-head_scripts">

										<div class="source-repeater-fields">
											<input type="text" name="niteoCS_head_scripts[]" value="<?php echo (empty( $head_scripts )) ? '' : esc_attr( $head_scripts[0] );?>" placeholder="Insert script full URL" class="regular-text code"><a href="#cmp-misc" class="delete-head_scripts"><i class="far fa-trash-alt"></i></a>
										</div>

										<div class="target-repeater-fields">
											<?php 
											if ( count( $head_scripts )> 1 ) {
												foreach ( $head_scripts as $id => $script ) {
													if ( $id != 0 ) {?>
													<input type="text" name="niteoCS_head_scripts[]" value="<?php echo esc_attr( $script );?>" placeholder="Insert full script full URL" class="regular-text code"><a href="#cmp-misc" class="delete-head_scripts"><i class="far fa-trash-alt"></i></a>
													<?php 
													}
												}
											} ?>
										</div>

									</div>

									<button id="add-head_scripts" class="button"><?php _e('Add More', 'cmp-coming-soon-maintenance');?></button>
								</fieldset>

							</td>
						</tr>

						<tr>
							<th><?php _e('Footer Scripts', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<h4><?php _e('Insert Javascript or CSS external file URL', 'cmp-coming-soon-maintenance');?></h4>
									<div id="wrapper-footer_scripts">

										<div class="source-repeater-fields">
											<input type="text" name="niteoCS_footer_scripts[]" value="<?php echo (empty( $footer_scripts )) ? '' : esc_attr( $footer_scripts[0] );?>" placeholder="Insert script full URL" class="regular-text code"><a href="#cmp-misc" class="delete-footer_scripts"><i class="far fa-trash-alt"></i></a>
										</div>

										<div class="target-repeater-fields">
											<?php 
											if ( count( $footer_scripts )> 1 ) {
												foreach ( $footer_scripts as $id => $footer_script ) {
													if ( $id != 0 ) {?>
													<input type="text" name="niteoCS_footer_scripts[]" value="<?php echo esc_attr( $footer_script );?>" placeholder="Insert script full URL" class="regular-text code"><a href="#cmp-misc" class="delete-footer_scripts"><i class="far fa-trash-alt"></i></a>
													<?php 
													}
												}
											} ?>
										</div>

									</div>

									<button id="add-footer_scripts" class="button"><?php _e('Add More', 'cmp-coming-soon-maintenance');?></button>
								</fieldset>

							</td>
						</tr>

						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<div class="table-wrapper cmp-misc">

					<h3 class="no-icon"><?php _e('Cookie Notice Compatibility', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>

						<tr>
							<th>
								<fieldset>
									<legend class="screen-reader-text">
										<span><?php _e('Cookie Notice Compatibility', 'cmp-coming-soon-maintenance');?></span>
									</legend>

									<p>
										<label title="Enabled">
										 	<input type="radio" class="cmp-cookienotice" name="cmp_cookie_notice_comp" value="1" <?php checked('1', $cmp_cookie_notice_comp);?>><?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>

									<p>
										<label title="Disabled">
										 	<input type="radio" class="cmp-cookienotice" name="cmp_cookie_notice_comp" value="0" <?php checked('0', $cmp_cookie_notice_comp);?>><?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
										</label>
									</p>
								</fieldset>
							</th>

							<td>
								<div class="cmp-cookienotice-switch x1">
									<p style="margin-top:0"><?php printf(__('If %s plugin is installed and activated then Cookie notice will be automatically displayed also on Coming Soon page.', 'cmp-coming-soon-maintenance'), '<a href="https://wordpress.org/plugins/cookie-notice/" target="_blank">Cookie Notice</a>');?></p>
								</div>
								
								<div class="cmp-cookienotice-switch x0">
									<p style="margin-top:0"><?php printf(__('Compatibility with %s plugin is disabled.', 'cmp-coming-soon-maintenance'), '<a href="https://wordpress.org/plugins/cookie-notice/" target="_blank">Cookie Notice</a>');?></p>
								</div>

							</td>
						</tr>
						<?php echo $this->render_settings->submit(); ?>

						</tbody>
					</table>

				</div>

				<!-- IMPORT AND EXPORT -->

				<div class="table-wrapper cmp-export-import">
					<h3 class="no-icon"><?php _e('Export or Import CMP Settings', 'cmp-coming-soon-maintenance');?></h3>
					<table class="general">
						<tbody>

						<tr>
							<th><?php _e('Export CMP Settings', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<button id="cmp-export-json" class="cmp-button import-export-button" data-security="<?php echo esc_attr( $ajax_nonce );?>"><?php _e('Export to JSON file', 'cmp-coming-soon-maintenance');?></button>
									<p class="cmp-hint"><?php _e('You can export complete CMP Settings to external JSON file. Which can be used to backup or transfer CMP Settings to another website.', 'cmp-coming-soon-maintenance');?></p>										
								</fieldset>
							</td>
						</tr>

						<tr>
							<th><?php _e('Import CMP Settings', 'cmp-coming-soon-maintenance');?></th>

							<td>
								<fieldset>
									<input type="text" hidden name="cmp-import-input" id="cmp-import-input" />

									<label class='cmp-input-file'>
										<span class="cmp-button import-export-button import-json-label" data-default="<?php _e('Select JSON file', 'cmp-coming-soon-maintenance');?>" style="display:inline-block"></span>
										<input type="file" name="cmp-import-json" id="cmp-import-json" accept=".json">
									</label>

									<p>
										<label for="cmp-import-media"><input type="checkbox" name="cmp-import-media" id="cmp-import-media" value="1"><?php _e('Import with media images', 'cmp-coming-soon-maintenance');?></label>
									</p>

									<p class="cmp-hint"><?php _e('Insert valid JSON file with CMP Settings to import complete CMP Settings. All current settings will be overwritten.', 'cmp-coming-soon-maintenance');?></p>	
									
								</fieldset>
							</td>
						</tr>

						<tr><th>
							<p class="cmp-submit">
								<input disabled type="submit" name="submit" class="button cmp-button submit" id="cmp-import-settings" value="<?php _e('Import Settings', 'cmp-coming-soon-maintenance');?>" form="csoptions" data-security="<?php echo esc_attr($ajax_nonce);?>"/>
							</p>
						</th></tr>
						</tbody>
					</table>

				</div>

			</div> <!-- <div class="cmp-inputs-wrapper"> -->

		</div> <!-- <div class="cmp-settings-wrapper"> -->

	</form>
	<?php 
	// get sidebar with "widgets"
	if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
		require (dirname(__FILE__) . '/cmp-sidebar.php');
	}

	?>
	</div>
</div> <!-- <div id="wrap"> -->

