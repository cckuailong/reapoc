<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if (isset($_POST['niteoCS_subscribe_type']) && is_numeric($_POST['niteoCS_subscribe_type'])) {
	update_option('niteoCS_subscribe_type', sanitize_text_field($_POST['niteoCS_subscribe_type']));
}

if (isset($_POST['niteoCS_subscribe_label']))  {
	update_option('niteoCS_subscribe_label', $this->niteo_sanitize_html($_POST['niteoCS_subscribe_label']));
}
if (isset($_POST['niteoCS_subscribe_label_popup']))  {
	update_option('niteoCS_subscribe_label_popup', $this->niteo_sanitize_html($_POST['niteoCS_subscribe_label_popup']));
}

if ( isset($_POST['niteoCS_subscribe_code']) ) {
	$shortcode = str_replace('"', '\'', $_POST['niteoCS_subscribe_code']);
	update_option('niteoCS_subscribe_code', sanitize_text_field($shortcode));
} 

if (isset($_POST['niteoCS_subscribe_method']))  {
	update_option('niteoCS_subscribe_method', sanitize_text_field($_POST['niteoCS_subscribe_method']));
}

if (isset($_POST['niteoCS_mailchimp_apikey']))  {
	update_option('niteoCS_mailchimp_apikey', sanitize_text_field($_POST['niteoCS_mailchimp_apikey']));
}

if (isset($_POST['niteoCS_mailchimp_list_selected']))  {
	update_option('niteoCS_mailchimp_list_selected', sanitize_text_field($_POST['niteoCS_mailchimp_list_selected']));
}

if (isset($_POST['niteoCS_mailpoet_list_selected']))  {
	update_option('niteoCS_mailpoet_list_selected', sanitize_text_field($_POST['niteoCS_mailpoet_list_selected']));
}

if (isset($_POST['niteoCS_mailster_list_selected']))  {
	update_option('niteoCS_mailster_list_selected', sanitize_text_field($_POST['niteoCS_mailster_list_selected']));
}

if (isset($_POST['niteoCS_mailoptin_selected']))  {
	update_option('niteoCS_mailoptin_selected', sanitize_text_field($_POST['niteoCS_mailoptin_selected']));
}
if (isset($_POST['niteoCS_recaptcha_secret']))  {
	update_option('niteoCS_recaptcha_secret', sanitize_text_field($_POST['niteoCS_recaptcha_secret']));
}
if (isset($_POST['niteoCS_recaptcha_site']))  {
	update_option('niteoCS_recaptcha_site', sanitize_text_field($_POST['niteoCS_recaptcha_site']));
}

if ( isset($_POST['niteoCS_subscribe_title']) ) {
	update_option('niteoCS_subscribe_title', esc_html( $_POST['niteoCS_subscribe_title']) );
}
if ( isset($_POST['niteoCS_subscribe_popup_title']) ) {
	update_option('niteoCS_subscribe_popup_title', esc_html( $_POST['niteoCS_subscribe_popup_title']) );
}
if ( isset($_POST['niteoCS_subscribe_popup_time']) ) {
	update_option('niteoCS_subscribe_popup_time', esc_html( $_POST['niteoCS_subscribe_popup_time']) );
}

if (isset($_POST['niteoCS_subs_img_id']) && ( is_numeric($_POST['niteoCS_subs_img_id']) || empty($_POST['niteoCS_subs_img_id']))) {
	update_option('niteoCS_subs_img_id', sanitize_text_field( $_POST['niteoCS_subs_img_id']) );
}

if (isset($_POST['niteoCS_subs_img_popup_id']) && ( is_numeric($_POST['niteoCS_subs_img_popup_id']) || empty($_POST['niteoCS_subs_img_popup_id']))) {
	update_option('niteoCS_subs_img_popup_id', sanitize_text_field( $_POST['niteoCS_subs_img_popup_id']) );
}
	

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if ( isset($_POST['niteoCS_mailchimp_double_opt']) ) {
		update_option('niteoCS_mailchimp[double-opt]', $this->sanitize_checkbox($_POST['niteoCS_mailchimp_double_opt']));
	} else {
		update_option('niteoCS_mailchimp[double-opt]', false);
	}

	if ( isset($_POST['niteoCS_mailster_double_opt']) ) {
		update_option('niteoCS_mailster_double_opt', $this->sanitize_checkbox($_POST['niteoCS_mailster_double_opt']));
	} else {
		update_option('niteoCS_mailster_double_opt', '0');
	}

	if ( isset($_POST['niteoCS_recaptcha_status']) ) {
		update_option('niteoCS_recaptcha_status', $this->sanitize_checkbox($_POST['niteoCS_recaptcha_status']));
	} else {
		update_option('niteoCS_recaptcha_status', '0');
	}

	if ( isset($_POST['niteoCS_subscribe_gdpr_checkbox']) ) {
		update_option('niteoCS_subscribe_gdpr_checkbox', $this->sanitize_checkbox($_POST['niteoCS_subscribe_gdpr_checkbox']));
	} else {
		update_option('niteoCS_subscribe_gdpr_checkbox', '0');
	}
	
	if ( isset($_POST['niteoCS_subscribe_popup']) ) {
		update_option('niteoCS_subscribe_popup', $this->sanitize_checkbox($_POST['niteoCS_subscribe_popup']));
	} else {
		update_option('niteoCS_subscribe_popup', '0');
	}

	if ( isset($_POST['niteoCS_subscribe_gdpr_checkbox_popup']) ) {
		update_option('niteoCS_subscribe_gdpr_checkbox_popup', $this->sanitize_checkbox($_POST['niteoCS_subscribe_gdpr_checkbox_popup']));
	} else {
		update_option('niteoCS_subscribe_gdpr_checkbox_popup', '0');
	}

	if ( isset($_POST['niteoCS_subscribe_firstname_popup']) ) {
		update_option('niteoCS_subscribe_firstname_popup', $this->sanitize_checkbox($_POST['niteoCS_subscribe_firstname_popup']));
	} else {
		update_option('niteoCS_subscribe_firstname_popup', '0');
	}

	if ( isset($_POST['niteoCS_subscribe_lastname_popup']) ) {
		update_option('niteoCS_subscribe_lastname_popup', $this->sanitize_checkbox($_POST['niteoCS_subscribe_lastname_popup']));
	} else {
		update_option('niteoCS_subscribe_lastname_popup', '0');
	}

	if ( isset($_POST['niteoCS_inpage_subscribe']) ) {
		update_option('niteoCS_inpage_subscribe', $this->sanitize_checkbox($_POST['niteoCS_inpage_subscribe']));
	} else {
		update_option('niteoCS_inpage_subscribe', '0');
	}
}


// delete_option('niteoCS_mailchimp_lists');

// get subscribe settings
$niteoCS_subscribe_type 	= get_option('niteoCS_subscribe_type', '2');
$niteoCS_subscribe_code 	= get_option('niteoCS_subscribe_code');
$niteoCS_subscribe_label	= stripslashes(get_option('niteoCS_subscribe_label'));
$niteoCS_subscribers_list 	= get_option('niteoCS_subscribers_list');
$niteoCS_subscribe_title 	= stripslashes( get_option('niteoCS_subscribe_title', 'SUBSCRIBE US') );
$inpage_subscribe			= get_option('niteoCS_inpage_subscribe', '1');

$subscribe_method 			= get_option('niteoCS_subscribe_method', 'cmp');
$mailchimp_apikey 			= get_option('niteoCS_mailchimp_apikey', '');
$mailchimp_list_selected	= get_option('niteoCS_mailchimp_list_selected');
$mailchimp_lists 			= json_decode(get_option('niteoCS_mailchimp_lists', false), true);
$mailpoet_list_selected		= get_option('niteoCS_mailpoet_list_selected');
$mailster_list_selected		= get_option('niteoCS_mailster_list_selected');
$mailoptin_selected			= get_option('niteoCS_mailoptin_selected');


$recaptcha_status			= get_option('niteoCS_recaptcha_status', '0');
$recaptcha_secret			= get_option('niteoCS_recaptcha_secret', '');
$recaptcha_site				= get_option('niteoCS_recaptcha_site', '');
$niteoCS_subs_img_id		= get_option('niteoCS_subs_img_id', '');
$subscribe_popup			= get_option('niteoCS_subscribe_popup', '0');
$subscribe_popup_title		= stripslashes( get_option('niteoCS_subscribe_popup_title',  get_option('niteoCS_subscribe_title', 'SUBSCRIBE US')) );
$subscribe_label_popup		= stripslashes(get_option('niteoCS_subscribe_label_popup'));
$subs_gdpr_popup 			= get_option( 'niteoCS_subscribe_gdpr_checkbox_popup', '0' );
$subs_firstname_popup 		= get_option( 'niteoCS_subscribe_firstname_popup', '0' );
$subs_lastname_popup 		= get_option( 'niteoCS_subscribe_lastname_popup', '0' );
$subscribe_popup_time		= get_option('niteoCS_subscribe_popup_time', '10');
$niteoCS_subs_img_popup_id	= get_option('niteoCS_subs_img_popup_id', '');

// get logo url from id
if ( $niteoCS_subs_img_id != '' ) {
	$subs_img_url = wp_get_attachment_image_src($niteoCS_subs_img_id, 'large');
	if ( isset($subs_img_url[0]) ) {
		$subs_img_url = $subs_img_url[0];
	}
}


if ( $niteoCS_subs_img_popup_id != '' ) {
	$subs_img_url_popup = wp_get_attachment_image_src($niteoCS_subs_img_popup_id, 'large');
	if ( isset($subs_img_url_popup[0]) ) {
		$subs_img_url_popup = $subs_img_url_popup[0];
	}
} ?>

<div class="table-wrapper content" id="subscribe-section">
	<h3><?php _e('Subscribe Form', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
	<tr>
		<th>
			<fieldset>
				<legend class="screen-reader-text">
					<span><?php _e('Subscribe Form Options', 'cmp-coming-soon-maintenance');?></span>
				</legend>

				<p>
					<label title="CMP Subscribe Form">
					 	<input type="radio" class="subscribe" name="niteoCS_subscribe_type" value="2"<?php checked($niteoCS_subscribe_type, 2);?>>&nbsp;<?php _e('CMP Subscribe Form', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="MailOptin">
					 	<input type="radio" class="subscribe" name="niteoCS_subscribe_type" value="3"<?php checked($niteoCS_subscribe_type, 3);?>>&nbsp;<?php _e('MailOptin Integration', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="3rd Party Plugin">
					 	<input type="radio" class="subscribe" name="niteoCS_subscribe_type" value="1"<?php checked($niteoCS_subscribe_type, 1);?>>&nbsp;<?php _e('3rd Party Plugin', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

				<p>
					<label title="Disabled">
					 	<input type="radio" class="subscribe" name="niteoCS_subscribe_type" value="0"<?php checked($niteoCS_subscribe_type, 0);?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
					</label>
				</p>

			</fieldset>
		</th>

		<td class="subscribe-switch x0">
			<p><?php _e('Subscribe Form is disabled.', 'cmp-coming-soon-maintenance');?></p>
		</td>

		<?php if ( $this->cmp_selectedTheme() == 'agency' || (isset( $theme_supports['subscribe_title'] ) && $theme_supports['subscribe_title'] === true )): ?>

		<td class="subscribe-switch x1 x2">
			<h4><?php _e('Subcribe Form Heading', 'cmp-coming-soon-maintenance');?></h4>
			<input type="text" name="niteoCS_subscribe_title" id="niteoCS_subscribe_title" value="<?php echo stripslashes( esc_attr($niteoCS_subscribe_title ));?>" class="regular-text code">

		</td>
		<?php endif; ?>

		<td class="subscribe-switch x1">
			<fieldset>
				<h4 class="subscribe"><?php _e('3rd Party Plugin Shortcode', 'cmp-coming-soon-maintenance');?></h4>
				<input type="text" name="niteoCS_subscribe_code" id="niteoCS_subscribe_code" value="<?php echo stripslashes( esc_attr($niteoCS_subscribe_code ));?>" class="regular-text code">
				<p><?php _e('You can find Shortode in your Contact Form Plugin settings. Should be something similar to code below: ', 'cmp-coming-soon-maintenance');?><br><code>[contact-form-7 id="8" title='Contact form 1']</code> or <code>[mc4wp_form id='7']</code></p>
				<p><?php _e('If you use 3rd party shortcode for subscribe or contact form, you might need to apply custom CSS to style the form correctly.', 'cmp-coming-soon-maintenance');?></p>
			</fieldset>
		</td>

		<td class="subscribe-switch x2">
			<label for="cmp-inpage-subscribe" style="font-weight: 600">
				<input type="checkbox" class="inpage-subscribe" name="niteoCS_inpage_subscribe" id="cmp-inpage-subscribe" value="1" <?php checked('1', $inpage_subscribe);?>><?php _e('Enable In Page Subscribe Form', 'cmp-coming-soon-maintenance');?>
			</label>

			<fieldset class="inpage-subscribe-switch x1">
				<p><?php _e('CMP Subscribe form will be used. It is guaranteed to always match selected CMP Theme\'s style.', 'cmp-coming-soon-maintenance');?></p>
			
				<h4 class="subscribe" for="niteoCS_subscribe_label"><?php _e('Subscribe Form message (can be GDPR or any custom message). Supports anchor HTML tags.', 'cmp-coming-soon-maintenance');?></h4>
				<textarea name="niteoCS_subscribe_label" id="niteoCS_subscribe_label" class="regular-text code" placeholder="<?php _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>"><?php echo esc_attr( $niteoCS_subscribe_label );?></textarea>
				<label><input type="checkbox" name="niteoCS_subscribe_gdpr_checkbox" value="1" <?php checked( '1', get_option( 'niteoCS_subscribe_gdpr_checkbox', '0' ) ); ?> class="regular-text code"><?php _e('Display GDPR Checkbox', 'cmp-coming-soon-maintenance');?></label>
				<br>
				<p class="cmp-hint"><?php printf( __('* Did you know you can translate Subscribe form Labels and Placeholders to your language in %s Settings?', 'cmp-coming-soon-maintenance'), '<a href="' . get_admin_url() . 'admin.php?page=cmp-translate">Translation</a>');?></p>
			</fieldset>
			
			<?php if ( $themeslug === 'atlas' ) : ?>
			<fieldset>
				<h4 ><?php _e('Subscribe Container Background Image', 'cmp-coming-soon-maintenance');?></h4>
				<input type="hidden" class="widefat" id="niteoCS-subs-img-id" name="niteoCS_subs_img_id" value="<?php echo esc_attr( $niteoCS_subs_img_id ); ?>" />
				<input id="add-subs-img" type="button" class="button" value="Select Subscribe Image" />

				<div class="subs-img-wrapper"><?php 
					if ( isset($subs_img_url) && $subs_img_url !== '' ) {
						echo '<img src="'.esc_url($subs_img_url).'" alt="Subscribe Image">';
					} ?></div>
				<input id="delete-subs-img" type="button" class="button" value="Remove Image" style="display:none"/><br><br>
			</fieldset>
			<?php endif; ?>

			<fieldset>
				<br>
				<label for="cmp-subscribe-popup" style="font-weight: 600">
					<input type="checkbox" class="subscribe-popup" name="niteoCS_subscribe_popup" id="cmp-subscribe-popup" value="1" <?php checked('1', $subscribe_popup);?>><?php _e('Enable Automatic Subscribe Popup', 'cmp-coming-soon-maintenance');?>
				</label>

				<div class="subscribe-popup-switch x1" style="margin-top:1em">
					<h4><?php _e('Automatic Popup Timing in Seconds', 'cmp-coming-soon-maintenance');?></h4>
					<input type="number" name="niteoCS_subscribe_popup_time" value="<?php echo esc_attr( $subscribe_popup_time ); ?>" min="1" max="60" class="regular-text"><br><br>
					
					<h4><?php _e('Popup Title', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" name="niteoCS_subscribe_popup_title" value="<?php echo esc_attr( $subscribe_popup_title ); ?>" class="regular-text"><br><br>

					<h4 for="niteoCS_subscribe_label_popup"><?php _e('Popup Subscribe Form message (can be GDPR or any custom message). Supports anchor HTML tags.', 'cmp-coming-soon-maintenance');?></h4>
					<textarea name="niteoCS_subscribe_label_popup" id="niteoCS_subscribe_label_popup" class="regular-text code" placeholder="<?php _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>"><?php echo esc_attr( $subscribe_label_popup );?></textarea>
					
					<label><input type="checkbox" name="niteoCS_subscribe_gdpr_checkbox_popup" value="1" <?php checked( '1', $subs_gdpr_popup ); ?> class="regular-text code"><?php _e('Display GDPR Checkbox', 'cmp-coming-soon-maintenance');?></label>
					<br><br>

					<label><input type="checkbox" name="niteoCS_subscribe_firstname_popup" value="1" <?php checked( '1', $subs_firstname_popup ); ?> class="regular-text code"><?php _e('Display Firstname Input', 'cmp-coming-soon-maintenance');?></label><br><br>
					<label><input type="checkbox" name="niteoCS_subscribe_lastname_popup" value="1" <?php checked( '1', $subs_lastname_popup ); ?> class="regular-text code"><?php _e('Display Lastname Input', 'cmp-coming-soon-maintenance');?></label><br><br>
					
					<h4 ><?php _e('Subscribe Popup Background Image', 'cmp-coming-soon-maintenance');?></h4>
					<input type="hidden" class="widefat" id="niteoCS-subs-img-popup-id" name="niteoCS_subs_img_popup_id" value="<?php echo esc_attr( $niteoCS_subs_img_popup_id ); ?>" />
					<input id="add-subs-img-popup" type="button" class="button" value="Select Popup Image" />

					<div class="subs-img-popup-wrapper"><?php 
						if ( isset($subs_img_url_popup) && $subs_img_url_popup !== '' ) {
							echo '<img src="'.esc_url($subs_img_url_popup).'" alt="Popup Image" style="max-width:300px">';
						} ?></div>
					<input id="delete-subs-img-popup" type="button" class="button" value="Remove Image" style="display:none"/>

				</div>
			</fieldset>

			<fieldset>
				<br>
				<h4><?php _e('Select how to save your Subscribers', 'cmp-coming-soon-maintenance');?></h4>
				<select name="niteoCS_subscribe_method" id ="niteoCS_subscribe_method" class="subscribe-method">
					<option value="cmp" <?php selected( 'cmp', $subscribe_method ); ?>><?php _e('CMP Custom Subscribe List', 'cmp-coming-soon-maintenance');?></option>
					<option value="mailchimp" <?php selected( 'mailchimp', $subscribe_method ); ?>><?php _e('MailChimp Integration', 'cmp-coming-soon-maintenance');?></option>
					<option value="mailpoet" <?php selected( 'mailpoet', $subscribe_method ); ?>><?php _e('MailPoet Integration', 'cmp-coming-soon-maintenance');?></option>
					<option value="mailster" <?php selected( 'mailster', $subscribe_method ); ?>><?php _e('Mailster Integration', 'cmp-coming-soon-maintenance');?></option>
				</select>


				<div class="subscribe-method cmp">
					<p><?php _e( 'Emails will be stored in custom CMP list with CSV export support. If you ever delete CMP plugin then subscriber list will be purged as well.', 'cmp-coming-soon-maintenance' );?></p>
					<p style="margin-bottom:0"><?php _e( 'Total Subscribers: ', 'cmp-coming-soon-maintenance' );?><a href="<?php echo admin_url(); ?>admin.php?page=cmp-subscribers"><?php echo $niteoCS_subscribers_list ? count( $niteoCS_subscribers_list ) : '0';?></a></p>
				</div>

				<div class="subscribe-method mailchimp">
					<br>
					<h4><?php _e('MailChimp API key', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" id="niteoCS_mailchimp_apikey" name="niteoCS_mailchimp_apikey" value="<?php echo esc_attr( $mailchimp_apikey );?>" class="regular-text code" placeholder="<?php _e('MailChimp API Key', 'cmp-coming-soon-maintenance');?>"><br><br>
					<button id="connect-mailchimp" class="button" data-security="<?php echo esc_attr($ajax_nonce);?>"><?php _e('Retrieve Lists', 'cmp-coming-soon-maintenance');?></button>
					<p><?php printf(__('You can find or create new API key in your %s.', 'cmp-coming-soon-maintenance'), '<a href="https://admin.mailchimp.com/account/api/" target="_blank">MailChimp Account</a>'); ?></p>
					<h4 for="niteoCS_mailchimp_list"><?php _e('Select MailChimp subscriber`s list.', 'cmp-coming-soon-maintenance');?></h4>
					<select name="niteoCS_mailchimp_list_selected" id="niteoCS_mailchimp_list">
						<?php 
						if ( is_array( $mailchimp_lists ) ) {

							if ( $mailchimp_lists['response'] == 200 ) {
								foreach ( $mailchimp_lists['lists'] as $list) { ?>
									<option value="<?php echo esc_attr( $list['id'] );?>" <?php selected( $list['id'], $mailchimp_list_selected ); ?>><?php echo esc_attr( $list['name'] );?></option>
									<?php 
								}
							} else { ?>
								<option value="error"><?php echo esc_attr( $mailchimp_lists['response'] . ': ' . $mailchimp_lists['message']);?></option>
								<?php 
							}

						} else { ?>
							<option value="error"><?php _e('Please insert MailChimp API key to retrieve Lists.', 'cmp-coming-soon-maintenance');?></option>
							<?php 
						} ?>
					</select>
					<br><br>
					<label><input type="checkbox" name="niteoCS_mailchimp_double_opt" value="1" <?php checked( '1', get_option( 'niteoCS_mailchimp[double-opt]', '0' ) ); ?> class="regular-text code"><?php _e('Enable MailChimp Double Opt In', 'cmp-coming-soon-maintenance');?></label>
				</div>

				<div class="subscribe-method mailpoet">
					<?php 
					$mailpoet_lists = null;
					// get MailPoet Lists
					if (class_exists(\MailPoet\API\API::class)) {
						// Get MailPoet API instance
						$mailpoet_api = \MailPoet\API\API::MP('v1');
						// Get available list so that a subscriber can choose in which to subscribe 
						$mailpoet_lists = $mailpoet_api->getLists();
					} ?>

					<br>

					<h4 for="niteoCS_mailpoet_list"><?php _e('Select MailPoet subscriber`s list.', 'cmp-coming-soon-maintenance');?></h4>
					<select name="niteoCS_mailpoet_list_selected" id="niteoCS_mailpoet_list">
						<?php 
						if ( is_array( $mailpoet_lists ) ) {
							foreach ( $mailpoet_lists as $list) { ?>
								<option value="<?php echo esc_attr( $list['id'] );?>" <?php selected( $list['id'], $mailpoet_list_selected ); ?>><?php echo esc_attr( $list['name'] );?></option>
								<?php 
							}

						} else { ?>
							<option value="error"><?php _e('Could not retrieve MailPoet subcribers lists. Please make sure MailPoet is correctly installed and configured.', 'cmp-coming-soon-maintenance');?></option>
							<?php 
						} ?>
					</select>
				</div>

				<div class="subscribe-method mailster">
					<?php 
					$mailster_lists = null;
					// get Mailster Lists
					if ( function_exists( 'mailster' ) ){
						// Get available list so that a subscriber can choose in which to subscribe 
						$mailster_lists = mailster( 'lists' )->get();
					} ?>

					<br>

					<h4 for="niteoCS_mailster_list"><?php _e('Select Mailster subscriber`s list.', 'cmp-coming-soon-maintenance');?></h4>
					<select name="niteoCS_mailster_list_selected" id="niteoCS_mailster_list">
						<?php 
						if ( is_array( $mailster_lists ) ) {
							foreach ( $mailster_lists as $list) { ?>
								<option value="<?php echo esc_attr( $list->ID );?>" <?php selected( $list->ID, $mailster_list_selected ); ?>><?php echo esc_attr( $list->name );?></option>
								<?php 
							}
						} else { ?>
							<option value="error"><?php _e('Could not retrieve Mailster subribers lists. Please make sure Mailster is correctly installed and configured.', 'cmp-coming-soon-maintenance');?></option>
							<?php 
						} ?>
					</select>
					<br><br>
					<label><input type="checkbox" name="niteoCS_mailster_double_opt" value="1" <?php checked( '1', get_option( 'niteoCS_mailster_double_opt', '1' ) ); ?> class="regular-text code"><?php _e('Enable Mailster Double Opt In', 'cmp-coming-soon-maintenance');?></label>

				</div>

			</fieldset>

			<fieldset>
				<br>
				<label for="cmp-recaptcha-status">
					<input type="checkbox" class="recaptcha-toggle" name="niteoCS_recaptcha_status" id="cmp-recaptcha-status" value="1" <?php checked('1', $recaptcha_status);?>><?php _e('Enable Google reCAPTCHA v3 Integration', 'cmp-coming-soon-maintenance');?>
				</label>

				<div class="recaptcha-toggle-switch x1" style="margin-top:1em">
					<h4><?php _e('Insert Site Key', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" name="niteoCS_recaptcha_site" value="<?php echo esc_attr( $recaptcha_site ); ?>" class="regular-text code"><br><br>

					<h4><?php _e('Insert Secret Key', 'cmp-coming-soon-maintenance');?></h4>
					<input type="text" name="niteoCS_recaptcha_secret" value="<?php echo esc_attr( $recaptcha_secret ); ?>" class="regular-text code"><br>
					<p class="cmp-hint"><?php printf( __('Google reCAPTCHA protects you against spam and other types of automated abuse. %1$s', 'cmp-coming-soon-maintenance'), sprintf('<a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">%s</a>',__('How to set reCAPTCHA v3?', 'cmp-coming-soon-maintenance')));?></p>
				</div>
			</fieldset>

		</td>


		<td class="subscribe-switch x3">

			<p><?php _e('MailOptin is a proven list building plugin that allows you to create popups and embedded forms so you can convert visitors to your coming soon site into subscribers. Supports all major email providers including. MailChimp, AWeber, Campaign Monitor, Constant Contact, Mailerlite.', 'cmp-coming-soon-maintenance'); ?></p>
			<?php 

				if ( file_exists( WP_PLUGIN_DIR . '/mailoptin/mailoptin.php' ) ) {
	
					if ( is_plugin_active('mailoptin/mailoptin.php') ) { 
						$optins = MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaigns();
						?>
						<fieldset>
							<h4 for="niteoCS_mailoptin_list"><?php _e('Select MailOptin Campaigns.', 'cmp-coming-soon-maintenance');?></h4>
							<select name="niteoCS_mailoptin_selected" id="niteoCS_mailoptin_list">
								<?php 
								if ( !empty( $optins ) ) {
									foreach ( $optins as $optin) { ?>
										<option value="<?php echo esc_attr( $optin['id'] );?>" <?php selected( $optin['id'], $mailoptin_selected ); ?>><?php echo esc_attr( $optin['name'] );?></option>
										<?php 
									}
								} else { ?>
									<option value="error"><?php _e('No MailOptin Campaign was found. Please make there is at least one Optin Campaign created.', 'cmp-coming-soon-maintenance');?></option>
									<?php 
								} ?>?>
							</select>
						</fieldset>
						<?php 
					} else { 
						$activate_url = wp_nonce_url(admin_url('plugins.php?action=activate&plugin=mailoptin%2Fmailoptin.php'), 'activate-plugin_mailoptin/mailoptin.php');
						?>
						<p><?php _e('Please activate MailOptin plugin first by clicking button below.');?></p>
						<a href="<?php echo $activate_url;?>" class="button" target="_blank"><?php _e('Activate MailOptin plugin', 'cmp-coming-soon-maintenance');?></a>
						<?php 
					} 

				} else {
					$install_url = wp_nonce_url(
						admin_url('update.php?action=install-plugin&plugin=mailoptin'),
						'install-plugin_mailoptin'
					)
					?>
					<p><?php _e('Please Install MailOptin plugin first by clicking button below.');?></p>
					<a href="<?php echo $install_url;?>" class="button" target="_blank"><?php _e('Install MailOptin plugin', 'cmp-coming-soon-maintenance');?></a>
					<?php 
				} ?>
		</td>

	</tr>

	<?php echo $this->render_settings->submit(); ?>
	
	</tbody>
	</table>
</div>