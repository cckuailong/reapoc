<?php 
	$elements_widgets = get_option( 'mystickyelements-widgets' );
?>
<input type="hidden" name="hide_tab_index" id="hide_tab_index" value="<?php ?>"/>
<?php 
	if( isset($elements_widgets) && !empty($elements_widgets)){
		?>
		<input type="hidden" name="widget_name" value="<?php echo ( isset($elements_widgets[0]) && $elements_widgets[0]!='' ) ? $elements_widgets[0] : 'MyStickyElement #1' ; ?>" />
		<?php
	}
	else{
		?>
		<input type="hidden" name="widget_name" value="MyStickyElement #1" />
		<?php
		
	}
$contact_form['name_require'] = isset($contact_form['name_require']) ? $contact_form['name_require'] : '';
$contact_form['message_require'] = isset($contact_form['message_require'])  ? $contact_form['message_require']: '';
$contact_form['dropdown_require'] = isset($contact_form['dropdown_require'])  ? $contact_form['dropdown_require']: '';
$contact_form['consent_text_require'] = isset($contact_form['consent_text_require'])  ? $contact_form['consent_text_require']: '';
$contact_form['redirect'] = isset($contact_form['redirect'])  ? $contact_form['redirect']: '';
$contact_form['open_new_tab'] = isset($contact_form['open_new_tab'])  ? $contact_form['open_new_tab']: '';
$contact_form['close_form_automatic'] = isset($contact_form['close_form_automatic'])  ? $contact_form['close_form_automatic']: '';
$contact_form['close_form_automatic'] = isset($contact_form['close_form_automatic'])  ? $contact_form['close_form_automatic']: '';
$contact_form['dropdown'] = isset($contact_form['dropdown']) ? $contact_form['dropdown'] : '';
?>
<div id="mystickyelements-tab-contact-form" class="mystickyelements-tab-contact-form mystickyelements-options" style="display: <?php echo ( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-contact-form' ) ? 'block' : 'none'; ?>;">
	<div class="">
		<div class="myStickyelements-header-title mystickyelements-option-field">
			<div class="myStickyelements-header-title-left">
				<h3 for="myStickyelements-contact-form-enabled">
					<?php esc_html_e('Show the Contact Form', 'mystickyelements'); ?>
				</h3>
			</div>
			<div class="myStickyelements-header-title-right">
				<input type="hidden" name="widgest_status" value="<?php echo $is_widgest_create; ?>"/>
				<label for="myStickyelements-contact-form-enabled" class="myStickyelements-switch">
					<input type="checkbox" id="myStickyelements-contact-form-enabled" name="contact-form[enable]" value="1" <?php checked( @$contact_form['enable'], '1' );?> />
				
					<span class="slider round"></span>
				</label>
			</div>
			<p class="contact-form-description" id="contact-form-disabled-info"><?php esc_html_e( 'Collect form submissions right from sticky side, top, or bottom bar of your website.', 'mystickyelements');?></p>
			<div class="turn-off-message" style="display:none;">
				<p><i class="fas fa-info-circle"></i><span><?php esc_html_e('Contact form in sticky bar has been turned off.','mystickyelements');?></span>&nbsp;&nbsp;<a href="javascript:void(0)" class="mystickyelements-turnit-on" data-turnit="myStickyelements-contact-form-enabled"><?php esc_html_e( 'Turn it on', 'mystickyelements' );?></a><?php esc_html_e( ' to collect user submitted forms from sidebar.', 'mystickyelements' );?></p>
			</div>
			<div class="mystickyelements-action-popup-open mystickyelements-action-popup-status" id="contactform-status-popup" style="display:none;">
				<div class="popup-ui-widget-header">
					<span id="ui-id-1" class="ui-dialog-title"><?php echo esc_html_e( 'Are you sure?', 'mystickyelement');?></span><span class="close-dialog" data-from ='contact-form'> &#10006 </span>
				</div>	
				<div id="widget-delete-confirm" class="ui-widget-content"><p><?php 
					echo esc_html_e( "You're about to turn off the ", "mystickyelement");
				?> <span><?php echo esc_html_e( "contact form", "mystickyelement"); ?></span><?php echo esc_html_e( " widget. By turning it off, this widget won't appear on your website. Are you sure?", "mystickyelement"); ?></p></div>
				<div class="popup-ui-dialog-buttonset"><button type="button" class="btn-disable-cancel button-contact-popup-disable"><?php echo esc_html_e('Disable anyway','mystickyelement');?></button><button type="button" class="mystickyelement-keep-widget-btn button-contact-popup-keep" data-from = "contact-form" ><?php echo esc_html_e('Keep using','mystickyelement');?></button></div>
			</div>
			<div id="mystickyelement-contact-popup-overlay" class="stickyelement-overlay" data-from = "contact-form" style="display:none;"></div>
		</div>
		<div class="mystickyelements-disable-wrap">
			<div class="mystickyelements-disable-content-wrap" style="display:none;">
				<div class="mystickyelements-disable-content">
					<i class="fas fa-eye-slash"></i>
					<p><?php esc_html_e( 'DISABLED', 'mystickyelements' );?></p>
				</div>
			</div>
			<div class="myStickyelements-header-title mystickyelements-option-field mystickyelements-sub-header-color">
				<h3><?php esc_html_e( 'Customize Form Fields', 'mystickyelements' );?></h3>
			</div>
			<div id="mystickyelements-contact-form-fields" class="mystickyelements-contact-form-fields">
				<?php 
					foreach ($contact_field as $value) :
						$val = $value;
						switch ( $val ) {
							case 'name' :
								$enable_class = '';
								if( !isset($contact_form['name']) && @$contact_form['name'] != '1') {
									$enable_class = 'hide_field';
								}
							?>
								<div class="mystickyelements-option-field contact-form-option myStickyelements-icon-wrap mystickyelements-name_enable <?php echo $enable_class; ?>">
									<!-- <p class="mystickyelement-field-hide-content"><?php //esc_html_e('Field is hidden', 'mystickyelements');?></p> -->
									
									<span class="mystickyelement-field-hide-content"><?php esc_html_e('Field is hidden.', 'mystickyelements');?> <label for="name_enable"><a><?php esc_html_e('Show the field', 'mystickyelements'); ?></a></label></span>

									<div class="mystickyelements-move-handle"></div>
									<div class="sticky-col-1">
										<input type="hidden" class="contact-fields" name="contact-field[]" value="name" />
										<label><i class="fas fa-user"></i><?php esc_html_e('Name', 'mystickyelements');?></label>
									</div>
									<div class="sticky-col-2">
										<div class="mystickyelements-reqired-wrap">	
											<input type="text" name="contact-form[name_value]" value="<?php echo $contact_form['name_value'];?>" placeholder="<?php _e('Name','mystickyelements');?>" />							
										</div>
										<div class="mystickyelements-action">
											<ul>
												<li>													
													<label  class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
														<input type="checkbox" id= "name_enable" name="contact-form[name]" value="1" <?php checked( @$contact_form['name'], '1' );?> />
														<span class="visible-icon">
															<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
															<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
														</span>
													</label>
												</li>
												<li>
													<label for="name_require"><?php _e('Required', 'mystickyelements');?></label>
													<label for="name_require" class="myStickyelements-switch">
														<input type="checkbox" id="name_require" class="required" name="contact-form[name_require]" value="1"  <?php checked( @$contact_form['name_require'], '1' );?> />
														<span class="slider round"></span>
													</label>
												</li>
											</ul>
											<div class="mystickyelements-hide-field-guide">
												<p><?php esc_html_e( 'The field is hidden and won’t show.', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
								</div>	
							<?php
							break;
							
							case 'phone' : 
								$enable_class = '';
								if( !isset($contact_form['phone']) && @$contact_form['phone'] != '1') {
									$enable_class = 'hide_field';
								}
							?>
								<div class="mystickyelements-option-field contact-form-option myStickyelements-icon-wrap mystickyelements-enable_phone <?php echo $enable_class; ?>">
									
									<span class="mystickyelement-field-hide-content"><?php esc_html_e('Field is hidden.', 'mystickyelements');?> <label for="enable_phone"><a><?php esc_html_e('Show the field', 'mystickyelements'); ?></a></label></span>
									<div class="mystickyelements-move-handle"></div>
									<div class="sticky-col-1">
										<input type="hidden" class="contact-fields" name="contact-field[]" value="phone" />
										<label><i class="fas fa-phone"></i><?php esc_html_e('Phone', 'mystickyelements');?></label>
									</div>
									<div class="sticky-col-2">
										<div class="mystickyelements-reqired-wrap">	
											<input type="text" name="contact-form[phone_value]" value="<?php echo $contact_form['phone_value'];?>" placeholder="<?php _e('Phone','mystickyelements');?>"/>
										</div>
										<div class="mystickyelements-action">
											<ul>
												<li>													
													<label  class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
														<input type="checkbox" id="enable_phone" name="contact-form[phone]" value="1" <?php checked( @$contact_form['phone'], '1' );?> />
														<span class="visible-icon">
															<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
															<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
														</span>
													</label>
												</li>
												<li>
													<label for="phone_require"><?php _e('Required', 'mystickyelements');?></label>
													<label for="phone_require" class="myStickyelements-switch">
														<input type="checkbox" id="phone_require" class="required" name="contact-form[phone_require]" value="1" <?php checked( @$contact_form['phone_require'], '1' );?> />
														<span class="slider round"></span>
													</label>
												</li>
											</ul>
											<div class="mystickyelements-hide-field-guide">
												<p><?php esc_html_e( 'The field is hidden and won’t show.', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
								</div>
							<?php 
							break;
							case 'email' : 
								$enable_class = '';
								if( !isset($contact_form['email']) && @$contact_form['email'] != '1') {
									$enable_class = 'hide_field';
								}
							?>
								<div class="mystickyelements-option-field contact-form-option myStickyelements-icon-wrap mystickyelements-email_enable <?php echo $enable_class; ?>">
									
									<span class="mystickyelement-field-hide-content"><?php esc_html_e('Field is hidden.', 'mystickyelements');?> <label for="email_enable"><a><?php esc_html_e('Show the field', 'mystickyelements'); ?></a></label></span>
									<div class="mystickyelements-move-handle"></div>
									<div class="sticky-col-1">
										<input type="hidden" class="contact-fields" name="contact-field[]" value="email" />
										<label><i class="fas fa-envelope"></i><?php esc_html_e('Email', 'mystickyelements');?></label>
									</div>
									<div class="sticky-col-2">
										<div class="mystickyelements-reqired-wrap">	
											<input type="text" name="contact-form[email_value]" value="<?php echo $contact_form['email_value'];?>" placeholder="<?php _e('Email','mystickyelements');?>" />
										</div>
										<div class="mystickyelements-action">
											<ul>
												<li>													
													<label  class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
														<input type="checkbox" id="email_enable" name="contact-form[email]" value="1" <?php checked( @$contact_form['email'], '1' );?> />
														<span class="visible-icon">
															<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
															<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
														</span>
													</label>
												</li>
												<li>
													<label for="email_require"><?php _e('Required', 'mystickyelements');?></label>
													<label for="email_require" class="myStickyelements-switch">
														<input type="checkbox" id="email_require" class="required" name="contact-form[email_require]" value="1"  <?php checked( @$contact_form['email_require'], '1' );?> />
														<span class="slider round"></span>
													</label>
												</li>
											</ul>
											<div class="mystickyelements-hide-field-guide">
												<p><?php esc_html_e( 'The field is hidden and won’t show.', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
								</div>	
							<?php
							
							break;
							
							case 'message' :
								$enable_class = '';
								if( !isset($contact_form['message']) && @$contact_form['message'] != '1') {
									$enable_class = 'hide_field';
								}
							?>
								<div class="mystickyelements-option-field contact-form-option myStickyelements-icon-wrap mystickyelements-message_enable <?php echo $enable_class; ?>">
									
									<span class="mystickyelement-field-hide-content"><?php esc_html_e('Field is hidden.', 'mystickyelements');?> <label for="message_enable"><a><?php esc_html_e('Show the field', 'mystickyelements'); ?></a></label></span>
									<div class="mystickyelements-move-handle"></div>
									<div class="sticky-col-1">
										<input type="hidden" class="contact-fields" name="contact-field[]" value="message" />
										<label><i class="fas fa-comment-dots"></i><?php esc_html_e('Message', 'mystickyelements');?></label>
									</div>
									<div class="sticky-col-2">
										<div class="mystickyelements-reqired-wrap">	
											<textarea name="contact-form[message_value]" rows="5" cols="50" placeholder="<?php _e('Message','mystickyelements');?>" ><?php echo $contact_form['message_value'];?></textarea>
										</div>
										<div class="mystickyelements-action">
											<ul>
												<li><label  class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
														<input type="checkbox" id="message_enable" name="contact-form[message]" value="1" <?php checked( @$contact_form['message'], '1' );?> />
														<span class="visible-icon">
															<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
															<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
														</span>
													</label>
												</li>
												<li>
													<label for="message_require"><?php _e('Required', 'mystickyelements');?></label>
													<label for="message_require" class="myStickyelements-switch">
														<input type="checkbox" class="required"  id="message_require" name="contact-form[message_require]" value="1" <?php checked( @$contact_form['message_require'], '1' );?> /> 
														<span class="slider round"></span>
													</label>
												</li>
											</ul>
											<div class="mystickyelements-hide-field-guide">
												<p><?php esc_html_e( 'The field is hidden and won’t show.', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
								</div>	
							<?php
							break;
							
							case 'dropdown' :
								$enable_class = '';
								
								if( !isset($contact_form['dropdown']) || @$contact_form['dropdown'] != '1' ) {
									$enable_class = 'hide_field';
								}	
							?>
								<div class="mystickyelements-option-field contact-form-option myStickyelements-icon-wrap mystickyelements-dropdown_enable <?php echo $enable_class; ?> hide_field" >
									<p class="mystickyelement-field-hide-content upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></p>
									
									<div class="mystickyelements-move-handle"></div>
									<div class="sticky-col-1">
										<span class="myStickyelements-label">
											<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip myStickyelements-hide-tooltip">
												<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
												<p><?php esc_html_e("Show dropdown in contact form", 'mystickyelements'); ?>
													<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/dropdown-image.jpeg">
												</p>
											</div>
											<input type="hidden" class="contact-fields" name="contact-field[]" value="dropdown"/>
											<label><?php esc_html_e('Dropdown', 'mystickyelements');?></label>
										</span>
									</div>
									<div class="sticky-col-2">
										<div class="mystickyelements-reqired-wrap">	
											<select name="contact-form[dropdown_value]"
													id="" <?php echo !$is_pro_active ? "disabled" : "" ?> >
												<option value=""><?php echo "Select " . @$contact_form['dropdown-placeholder']; ?></option>
												<?php if (isset($contact_form['dropdown-option']) && !empty($contact_form['dropdown-option'])) :
													foreach ($contact_form['dropdown-option'] as $option) :
														if ($option == '') {
															continue;
														}
														echo "<option>" . esc_html($option) . "</option>";
													endforeach;
												endif;
												?>
											</select>
										</div>
										<div class="mystickyelements-action">
											<ul>
												<li>
													<label  class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
														<input type="checkbox" id="dropdown_enable" name="contact-form[dropdown]" value="1" <?php checked(@$contact_form['dropdown'], '1'); ?> <?php echo !$is_pro_active ? "disabled" : "" ?> />
														<span class="visible-icon">
															<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
															<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
														</span>
													</label>
												</li>
												<li>
													<label for="dropdown_require"><?php _e('Required', 'mystickyelements');?></label>
													<label for="dropdown_require" class="myStickyelements-switch">
														<input type="checkbox" id="dropdown_require" class="required" name="contact-form[dropdown_require]"value="1" <?php checked(@$contact_form['dropdown_require'], '1'); ?> <?php echo !$is_pro_active ? "disabled" : "" ?> />
														<span class="slider round"></span>
													</label>
												</li>
												<li>
													<label class="myStickyelements-setting-label">
														<span class="contact-form-dropdown-popup contact-form-popup-setting">
															<i class="fas fa-cog"></i>&nbsp;<?php esc_html_e('Settings', 'mystickyelements'); ?>
														</span>
													</label>
												</li>
											</ul>
											<div class="mystickyelements-hide-field-guide">
												<p><?php esc_html_e( 'Upgrade to Pro to use dropdown.', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
								</div>
							<?php
							break;
						}
					endforeach;

					$enable_class = '';
								
					if( !isset($contact_form['consent_checkbox']) && @$contact_form['consent_checkbox'] != 'yes') {
						$enable_class = 'hide_field';
					}
				?>
				<div class="myStickyelements-consent-main-field">
					<div class="mystickyelements-option-field-iplog mystickyelements-option-field contact-form-option myStickyelements-icon-wrap <?php echo $enable_class; ?>">
						<p class="mystickyelement-field-hide-content upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></p>
						<div class="mystickyelements-move-handle"></div>
						<div class="sticky-col-1">
							<!--<span class="myStickyelements-label"> -->
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip myStickyelements-hide-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("Add a checkbox that asks for users' consent while submitting a form", 'mystickyelements'); ?>
										<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/consent-gif.gif">
									</p>
								</div>
								<label><?php _e( 'Consent Checkbox', 'mystickyelements' );?></label>
								
							<!--</span> -->
						</div>
						<div class="sticky-col-2">
							<div class="mystickyelements-reqired-wrap">	
								<?php $consent_text = ( isset($contact_form['consent_text'])) ? $contact_form['consent_text'] : 'I agree to the terms and conditions.'; ?><input type="text" id="consent_text" name="contact-form[consent_text]" value="<?php echo htmlentities(stripslashes($consent_text));?>" placeholder="<?php _e('Enter contact form conset text','mystickyelements');?>" disabled />
							</div>
							<div class="mystickyelements-action">
								<ul>
									<li>
										<label class="myStickyelements-visible-icon mystickyelements-custom-fields-tooltip">
											<input type="checkbox" name="contact-form[consent_checkbox]" id="consent_checkbox" value="yes" <?php checked( @$contact_form['consent_checkbox'], 'yes' );?> disabled  />
											<span class="visible-icon">
												<p class="show-field-tooltip"><?php _e('Show Field','mystickyelements');?></p>
												<p class="hide-field-tooltip"><?php _e('Hide Field','mystickyelements');?></p>
											</span>
										</label>
									</li>
									
									<li>
										<label for="consent_text_require"><?php _e('Required', 'mystickyelements');?></label>
										<label  class="myStickyelements-switch">
											<input type="checkbox" class="required" name="contact-form[consent_text_require]" value="1" <?php checked( @$contact_form['consent_text_require'], '1' );?> disabled />
											<span class="slider round"></span>
										</label>
									</li>
								</ul>
								<div class="mystickyelements-hide-field-guide">
									<p><?php esc_html_e( 'Upgrade to Pro to use Consent Checkbox.', 'mystickyelements');?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="myStickyelements-contact-form-field-hide myStickyelements-contact-form-field-option">
				<div class="mystickyelements-add-custom-fields">
					<div class="mystickyelements-custom-fields-tooltip">
						<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a><p><?php esc_html_e("Add custom fields to your contact form including text, text area, dropdowns, file upload, website, date, and number fields", 'mystickyelements'); ?></p>
					</div>
				<!--	<span class="upgrade-myStickyelements"><a href="<?php //echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php //_e('UPGRADE NOW', 'mystickyelements'); ?></a></span> -->
					<a href="#" class="mystickyelements-add-custom-fields"> <?php esc_html_e( 'Add new field', 'mystickyelements'); ?><svg style="fill: #fff;"id="plus-circle" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 36 36"><path id="Path_1928" data-name="Path 1928" d="M18,7.875A1.125,1.125,0,0,1,19.125,9v9A1.125,1.125,0,0,1,18,19.125H9a1.125,1.125,0,0,1,0-2.25h7.875V9A1.125,1.125,0,0,1,18,7.875Z" fill-rule="evenodd"/><path id="Path_1929" data-name="Path 1929" d="M16.875,18A1.125,1.125,0,0,1,18,16.875h9a1.125,1.125,0,0,1,0,2.25H19.125V27a1.125,1.125,0,0,1-2.25,0Z" fill-rule="evenodd"/><path id="Path_1930" data-name="Path 1930" d="M18,33.75A15.75,15.75,0,1,0,2.25,18,15.75,15.75,0,0,0,18,33.75ZM18,36A18,18,0,1,0,0,18,18,18,0,0,0,18,36Z" fill-rule="evenodd"/></svg><!--<i class="fas fa-plus"></i> --></a>
				</div>
			</div>
			
			<div class="myStickyelements-content-section mystickyelements-display-main-options">
				<!-- <div class="mystickyelements-header-main-title">
					<h2><i class="fas fa-cog"></i><?php //_e('Contact Form Preference', 'mystickyelements'); ?></h2>
				</div> -->
				<div class="mystickyelements-display-above-options myStickyelements-contact-form-tab">
					<div class="myStickyelements-header-title">
						<h3><?php _e('Contact Tab Settings', 'mystickyelements'); ?></h3>
					</div>
					<div class="myStickyelements-setting-wrap-list-main">
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Devices', 'mystickyelements');?></label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<label>
									<input type="checkbox" name="contact-form[desktop]" value= "1"<?php checked( @$contact_form['desktop'], '1' );?> /> &nbsp;<?php _e( 'Desktop', 'mystickyelements' );?>
								</label>
								<label>
									<input type="checkbox" name="contact-form[mobile]" value="1" <?php checked( @$contact_form['mobile'], '1' );?> /> &nbsp;<?php _e( 'Mobile', 'mystickyelements' );?>
								</label>
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Direction', 'mystickyelements');?></label>
							</div>
							<div class="myStickyelements-inputs mystickyelements-setting-wrap-right myStickyelements-direction-rtl">
								<label>
									<input type="radio" name="contact-form[direction]" value= "LTR" <?php checked( @$contact_form['direction'], 'LTR' );?> /> &nbsp;<?php _e( 'LTR', 'mystickyelements' );?>
								</label>
								<label>
									<input type="radio" name="contact-form[direction]" value="RTL" <?php checked( @$contact_form['direction'], 'RTL' );?> /> &nbsp;<?php _e( 'RTL', 'mystickyelements' );?>
								</label>
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Change the background color of the floating bar that opens the contact form", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Background Color:', 'mystickyelements' );?>
								</label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="tab_background_color" name="contact-form[tab_background_color]" class="mystickyelement-color" value="<?php echo $contact_form['tab_background_color'];?>" />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Change the text color of the floating bar that opens the contact form", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Text Color:', 'mystickyelements' );?>
								</label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="tab_text_color" name="contact-form[tab_text_color]" class="mystickyelement-color" value="<?php echo $contact_form['tab_text_color'];?>" />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("The background color of the form that appears when someone hover/clicks to open the contact form", 'mystickyelements'); ?></p>
									</div>
									<?php _e('Form Background Color:', 'mystickyelements'); ?>
								</label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="form_bg_color" name="contact-form[form_bg_color]" class="mystickyelement-color" value="<?php echo ( isset($contact_form['form_bg_color']))? $contact_form['form_bg_color'] : '#ffffff'; ?>"/>
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("The headline color of the form that appears when someone hover/clicks to open the contact form", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Form Headline Color:', 'mystickyelements' );?>
								</label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="headine_text_color" name="contact-form[headine_text_color]" class="mystickyelement-color" value="<?php echo $contact_form['headine_text_color'];?>" />
							</div>
						</div>
						<!--div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list placeholder-text-color" -->
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half" >
							<div class="mystickyelements-setting-wrap-left">
								<span class="myStickyelements-label" style="width: 150px;">
									<label>
										<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
											<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
											<p><?php esc_html_e("Change the placeholder color of fields inside the contact form", 'mystickyelements'); ?></p>
										</div>
										<?php _e( 'Placeholder text color', 'mystickyelements' );?>
									</label>
									
								</span>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<div class="mystickyelements-content-section-wrap">
									<div class="myStickyelements-inputs myStickyelements-label placeholder-text-color" style="position:relative;margin-left: 1px;">										
										<input type="text" id="placeholder_color" name="general-settings[placeholder_color]" class="mystickyelement-color" value="<?php echo ( isset( $general_settings['placeholder_color'] ) && $general_settings['placeholder_color'] != '' ) ? $general_settings['placeholder_color'] : '#4F4F4F'; ?>" />
									</div>									
								</div>
								
							</div>
						</div>
						
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Text in tab', 'mystickyelements' );?></label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" name="contact-form[text_in_tab]" value="<?php echo $contact_form['text_in_tab'];?>" placeholder="<?php _e('Enter text here...','mystickyelements');?>" />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Contact Form Title', 'mystickyelements' );?></label>
							</div>
							<?php if( isset( $contact_form['contact_title_text'] ) && $contact_form['contact_title_text'] != '' ) {
								$contact_title_text = $contact_form['contact_title_text']; 
							} else { 
								$contact_title_text = "Contact Form"; 
							} ?>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" name="contact-form[contact_title_text]" value="<?php echo $contact_title_text; ?>" placeholder="<?php _e('Enter text here...','mystickyelements');?>" />
							</div>
						</div>
						<table>
							<tr class="myStickyelements-contact-form-field-hide">
								<td>
									<div class="multiselect">
										<?php
										if ( isset($contact_form['send_leads']) && !is_array( $contact_form['send_leads'])) {
											$contact_form['send_leads'] = explode(', ', $contact_form['send_leads']);
										}
										?>
										<div id="checkboxes">
											<label>
											 	<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
                                                    <a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
                                                    <p><?php esc_html_e("Save the leads locally in your website", 'mystickyelements'); ?></p>
                                                </div>
												<input type="checkbox" name="contact-form[send_leads][]" id="send_leads_database" value="database" <?php if ( !empty($contact_form['send_leads']) && in_array( 'database', $contact_form['send_leads']) ) { echo 'checked="checked"'; } ?> checked="checked"  />&nbsp;<?php _e( 'Save leads to <a href="'. admin_url('admin.php?page=my-sticky-elements-leads') .'" target="_blank">this site</a>', 'mystickyelements' );?>
											</label>
											<a href="<?php echo admin_url('admin.php?page=my-sticky-elements-leads'); ?>" id="send_lead_to_contact_form" target="_blank"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 6H6C4.89543 6 4 6.89543 4 8V18C4 19.1046 4.89543 20 6 20H16C17.1046 20 18 19.1046 18 18V14M14 4H20M20 4V10M20 4L10 14" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
										</div>
									</div>
									<div class="multiselect send-lead-email-upgrade">
										<div id="checkboxes">
											<label>
												<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
													<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
													<p><?php esc_html_e("Get notified when someone submits a response to the contact form", 'mystickyelements'); ?></p>
												</div>
												<input type="checkbox"  id="send_leads_mail" value="mail" data-url = "<?php echo admin_url("admin.php?page=my-sticky-elements-upgrade"); ?>"  />&nbsp;<?php _e( 'Send leads to your email', 'mystickyelements' );?>
											</label>
											<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>											
										</div>
									</div>
									<div id="contact-form-send-mail" class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list" style="display:none">
										<div class="mystickyelements-setting-wrap-left">
											<label><?php _e( 'Email', 'mystickyelements' );?></label>
										</div>
										<div class="mystickyelements-setting-wrap-right">
											<input type="text" name="contact-form[sent_to_mail]" value="<?php echo @$contact_form['sent_to_mail'];?>" placeholder="<?php _e('Enter your email','mystickyelements');?>" />
											<p class="description"><?php esc_html_e( 'Check your Spam folder and Promotions tab', 'mystickyelements');?></p>
											<div class="mystickyelements-custom-fields-tooltip mystickyelements-email-tooltip">
												<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
												<p><?php esc_html_e( 'If you want to send leads to more than one email address, please add your email addresses separated by commas', 'mystickyelements');?></p>
											</div>
										</div>
									</div>
									<div id="contact-form-sendr-name" class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list" style="display:none">
										<div class="mystickyelements-setting-wrap-left">	
											<label><?php _e( "Sender's name", 'mystickyelements' );?></label>
										</div>
										<div class="mystickyelements-setting-wrap-right">
											<?php $contact_form['sender_name'] = ( isset($contact_form['sender_name'])) ? $contact_form['sender_name'] : '';?>
											<input type="text" name="contact-form[sender_name]" value="<?php echo $contact_form['sender_name'];?>" placeholder="<?php _e('Enter sender name');?>" />												
											<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
												<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
												<p><?php esc_html_e("The name that will appear as the sender name in your email", 'mystickyelements'); ?></p>
											</div>
										</div>
									</div>
									<div id="contact-form-mail-subject-line" class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list" style="display:none">
										<div class="mystickyelements-setting-wrap-left">	
											<label><?php _e( 'Email subject line', 'mystickyelements' );?></label>
										</div>
										<div class="mystickyelements-setting-wrap-right">
											<?php $email_subject_line = ( isset($contact_form['email_subject_line'])) ? $contact_form['email_subject_line'] : 'New lead from MyStickyElements from {name} on {date} {hour}'; ?>
											<input type="text" name="contact-form[email_subject_line]" value="<?php echo $email_subject_line;?>" placeholder="<?php _e('Enter your email subject line','mystickyelements');?>" />
											<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
												<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
												<p><?php esc_html_e("The subject line of the emails that you'll recieve from each contact form submission", 'mystickyelements'); ?></p>
											</div>
										</div>
									</div>
									<div class="multiselect">
										<div id="checkboxes">
											<label>
												<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
													<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
													<p><?php esc_html_e("Integrate MailChimp to directly sync email leads on MailChimp", 'mystickyelements'); ?></p>
												</div>
												<input type="checkbox" name="contact-form[send_leads][]" id="send_leads_mailchimp" data-url = "<?php echo admin_url("admin.php?page=my-sticky-elements-upgrade"); ?>" value="mailchimp" <?php if ( !empty($contact_form['send_leads']) && in_array( 'mailchimp', $contact_form['send_leads']) ) { echo 'checked="checked"'; } ?> />&nbsp;<?php _e( 'Sends leads to MailChimp', 'mystickyelements' );?>
											</label>
											<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
											<?php //endif; ?>
										</div>
									</div>
									<div class="multiselect send-lead-mailpoet-upgrade">
										<div id="checkboxes">
											<label>
												<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
													<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
													<p><?php esc_html_e("Integrate MailPoet to directly sync email leads on MailPoet", 'mystickyelements'); ?></p>
												</div>
												<input type="checkbox" name="contact-form[send_leads][]" id="send_leads_mailpoet" data-url="<?php echo admin_url("admin.php?page=my-sticky-elements-upgrade"); ?>" value="mailpoet" <?php if ( !empty($contact_form['send_leads']) && in_array( 'mailpoet', $contact_form['send_leads']) ) { echo 'checked="checked"'; } ?>/>&nbsp;<?php _e( 'Sends leads to MailPoet', 'mystickyelements' );?>
											</label>												
											<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
											<?php //endif; ?>
										</div>
									</div>
									
								</td>
							</tr>
						</table>
					</div>
				
					<div class="myStickyelements-header-title">
						<h3><?php _e('Submit Button Settings', 'mystickyelements'); ?></h3>
					</div>
					<div class="myStickyelements-setting-wrap-list-main"> 
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Background Color:', 'mystickyelements' );?></label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="submit_button_background_color" name="contact-form[submit_button_background_color]" class="mystickyelement-color" value="<?php echo esc_attr($contact_form['submit_button_background_color']); ?>" />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list myStickyelements-setting-half">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Text Color:', 'mystickyelements' );?></label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="submit_button_text_color" name="contact-form[submit_button_text_color]" class="mystickyelement-color" value="<?php echo esc_attr($contact_form['submit_button_text_color']);?>" />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Text on the submit button', 'mystickyelements' );?></label>
							</div>
							<div class="mystickyelements-setting-wrap-right">
								<input type="text" id="contact-form-submit-button" name="contact-form[submit_button_text]" value="<?php echo $contact_form['submit_button_text'];?>" placeholder="<?php _e('Enter text here...','mystickyelements');?>"  />
							</div>
						</div>
						<div class="myStickyelements-redirect-link-wrap myStickyelements-setting-wrap">
							<div class="myStickyelements-redirect-block">
								<label>
									<input type="checkbox" id="redirect_after_submission" name="contact-form[redirect]" value="1" <?php checked( @$contact_form['redirect'], '1' );?> <?php echo !$is_pro_active?"disabled":"" ?> /> &nbsp; <?php _e('Redirect visitors after submission', 'mystickyelements');?>
								</label>
								<label class="myStickyelements-redirect-new-tab" style="display: none;">
									<input type="checkbox" name="contact-form[open_new_tab]" value= "1"<?php checked( @$contact_form['open_new_tab'], '1' );?> /> &nbsp;<?php _e( 'Open in a new tab', 'mystickyelements' );?>
								</label>
							</div>
							<div class="redirect-link-input">
								<input type="text" name="contact-form[redirect_link]" value="<?php echo @$contact_form['redirect_link'];?>" class="myStickyelements-redirect-link" placeholder="<?php _e('Enter redirect link','mystickyelements');?>" <?php echo !$is_pro_active?"disabled":"" ?> />
								
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label><?php _e( 'Thank you message', 'mystickyelements' );?></label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</div>

							<div class="myStickyelements-thankyou-input mystickyelements-setting-wrap-right">
								<?php $thank_you_message = ( isset($contact_form['thank_you_message'])) ? $contact_form['thank_you_message'] : 'Your message was sent successfully';?>
								<input type="text" name="contact-form[thank_you_message]" value="<?php echo $thank_you_message;?>" placeholder="<?php _e('Enter thank you message here...','mystickyelements');?>"  <?php echo !$is_pro_active?"disabled":"" ?> />
							</div>
						</div>
						<div class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list">
							<div class="mystickyelements-setting-wrap-left">
								<label for="myStickyelements-contact-form-close">
									<span class="mystickyelements-custom-fields-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p>Close the form automatically after a few seconds based on your choice</p>
									</span>
									<?php _e( 'Close form automatically after submission', 'mystickyelements' );?>
								</label>
							</div>

							<div class="myStickyelements-thankyou-input mystickyelements-setting-wrap-right">
								<label for="myStickyelements-contact-form-close" class="myStickyelements-switch">
									<input type="checkbox" id="myStickyelements-contact-form-close" name="contact-form[close_form_automatic]" value="1" <?php checked( @$contact_form['close_form_automatic'], '1' );?>>
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						<div id="contact-form-close-after" class="myStickyelements-setting-wrap myStickyelements-setting-wrap-list" <?php if( !isset($contact_form['close_form_automatic']) ):?> style="display:none" <?php endif;?>>
							<div class="mystickyelements-setting-wrap-left">
								<label for="myStickyelements-contact-form-close-after"><?php _e( 'Close after', 'mystickyelements' );?></label>
							</div>

							<div class="myStickyelements-thankyou-input mystickyelements-setting-wrap-right">
								<?php $close_after = ( isset($contact_form['close_after'])) ? $contact_form['close_after'] : '1';?>
								<label>
									<input type="number" name="contact-form[close_after]" value="<?php echo $close_after;?>" placeholder=""  <?php echo !$is_pro_active?"disabled":"" ?> style="width:140px;"/>&nbsp; seconds
								</label>
								<p class="mystickyelement-field-hide-content upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></p>
							</div>
						</div>
					</div>
					<!-- work -->
				</div>
			</div>
			
			<div id='contact_form_field_open' class='contact-form-field-open contact-form-setting-popup-open' style="display:none;">
				<div class='contact-form-popup-label'>
					<h3><?php esc_html_e("Choose which custom field you'd like to add"); ?></h3>
					<div class="contact-form-field-select-wrap mystickyelements-free-version">
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="far fas fa-edit"></i><?php _e('Text', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-align-justify"></i><?php _e('Text Area', 'mystickyelements'); ?> </span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-phone"></i><?php _e('Number', 'mystickyelements'); ?> </span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-calendar-week"></i><?php _e('Date', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-link"></i><?php _e('Website', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-caret-down"></i><?php _e('Dropdown', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-file-upload"></i><?php _e('File upload', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-tools"></i><?php _e('IP Log', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="fas fa-tools"></i><?php _e('reCAPTCHA', 'mystickyelements'); ?></span>
						</label>
						<label class="contact-form-field-select">
							<input type="radio" name="radio_btn" />
							<span><i class="far far fa-newspaper"></i><?php _e('Text Block', 'mystickyelements'); ?></span>
						</label>
						<div class="upgrade-myStickyelements-link">
							<a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">
								<i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?>
							</a>
							<p style="color: #000;">What can you do with the custom fields? </p>
							<a href=" https://premio.io/help/mystickyelements/how-to-add-custom-fields-to-your-contact-form/?utm_source=mseplugin" target="_blank">Show me the guide </a>
						</div>
					</div>
					<span class="contact-form-dropdfown-close"><i class="fas fa-times"></i></span>
				</div>
			</div>
			
			<div class="contact-form-dropdown-open contact-form-setting-popup-open" style="display: none;">
				<div class="contact-form-dropdown-main">
					<input type="text" name="contact-form[dropdown-placeholder]"
							class="contact-form-dropdown-select"
							value="<?php if(isset($contact_form['dropdown-placeholder']) && $contact_form['dropdown-placeholder'] != '' ){ echo esc_attr(@$contact_form['dropdown-placeholder']); }else{ echo "- Select -"; } ?>"
							placeholder="<?php esc_html_e('Select...', 'mystickyelement'); ?>"/>
					<div class="contact-form-dropdown-option">
						<div class="option-value-field">
							<span class="move-icon"></span>
							<input type="text" name="contact-form[dropdown-option][]" value=""/> <span class="add-dropdown-option"><?php esc_html_e('Add', 'mystickyelement'); ?></span>
						</div>
						<?php if (isset($contact_form['dropdown-option']) && !empty($contact_form['dropdown-option'])) :
							foreach ($contact_form['dropdown-option'] as $option) :
								if ($option == '') {
									continue;
								}
								?>
								<div class="option-value-field">
									<span class="move-icon"></span>
									<input type="text" name="contact-form[dropdown-option][]"value="<?php echo esc_attr($option); ?>"/> <span class="delete-dropdown-option"><i class="fas fa-times"></i></span>
								</div>
							<?php
							endforeach;
						endif; ?>

					</div>
					<input type="submit" name="submit" class="button button-primary btn-save-dropdown"
							value="<?php _e('Save', 'mystickyelements'); ?>">
				</div>
				<span class="contact-form-dropdfown-close"><i class="fas fa-times"></i></span>
			</div>
			
			<div class="contactform-sendleads-upgrade-popup mystickyelements-action-popup-open mystickyelements-blue-popup" style="display:none;">
				<div class="popup-ui-widget-header">
					<span id="ui-id-1" class="ui-dialog-title"><?php echo esc_html_e("Upgrade to pro for more options","mystickyelement");?></span>
					<span class="close-dialog" data-from="sendleads-upgrade">						
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill="#31373D" d="M22.238 18.004l9.883-9.883c1.172-1.171 1.172-3.071 0-4.243-1.172-1.171-3.07-1.171-4.242 0l-9.883 9.883-9.883-9.882c-1.171-1.172-3.071-1.172-4.243 0-1.171 1.171-1.171 3.071 0 4.243l9.883 9.882-9.907 9.907c-1.171 1.171-1.171 3.071 0 4.242.585.586 1.354.879 2.121.879s1.536-.293 2.122-.879l9.906-9.906 9.882 9.882c.586.586 1.354.879 2.121.879s1.535-.293 2.121-.879c1.172-1.171 1.172-3.071 0-4.242l-9.881-9.883z"/></svg>
					</span>
				</div>
				<div class="ui-widget-content">
					<p><?php echo esc_html_e("The free version allows you to save the form submissions (form leads) locally to your website. If you want to send leads to your email, or automatically sync leads to MailChimp or Mailpoet, consider upgrading to the premium version.","mystickyelement");?></p>
				</div>
				<div class="popup-ui-dialog-buttonset">
					<a href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade")); ?>" class="new-upgrade-button" target="blank">Upgrade to Pro 🎉</a>
				</div>
			</div>
			<div id="contactform_sendleads_popup_overlay" class="stickyelement-overlay" style="display:none;"></div>
		</div>
	</div>
</div>