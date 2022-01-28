<div id="cfsFormSubmitEditTabs">
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab" href="#cfsFormSubmitEditTabMain">
			<i class="fa fa-cog"></i>
			<?php _e('General', CFS_LANG_CODE)?>
		</a>
		<?php foreach($this->submitOptsAddTabs as $tCode => $t) { ?>
			<a class="nav-tab" href="#<?php echo $tCode;?>">
				<i class="fa <?php echo $t['fa_icon']?>"></i>
				<?php echo $t['title'];?>
			</a>
		<?php }?>
	</h3>
	<div id="cfsFormSubmitEditTabMain" class="cfsTabContent nav-tab-active">
		<table class="form-table cfsFormSubmitOptsTbl" style="width: 100%">
			<tr>
				<th scope="row">
					<?php _e('Form sent message', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Message, that your users will see after success form submition.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('params[tpl][form_sent_msg]', array('value' => $this->form['params']['tpl']['form_sent_msg']))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Form sent message color', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Text color for your Success message.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::colorpicker('params[tpl][form_sent_msg_color]', array(
						'value' => (isset($this->form['params']['tpl']['form_sent_msg_color']) ? $this->form['params']['tpl']['form_sent_msg_color'] : '#4ae8ea'),
					))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Hide form after submit', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default form will be hidden after successful submit, but you can disable this here - and after submit form will be just cleared.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkboxHiddenVal('params[tpl][hide_on_submit]', array(
						'value' => (isset($this->form['params']['tpl']['hide_on_submit']) ? $this->form['params']['tpl']['hide_on_submit'] : 1),
					))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Redirect after submit', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('If you want - you can redirect user after Form was submitted. Just enter required Redirect URL here - and each time after Form will be submitted - user will be redirected to that URL. Just leave this field empty - if you don\'t need this functionality in your Form.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('params[tpl][redirect_on_submit]', array(
						'value' => (isset($this->form['params']['tpl']['redirect_on_submit']) ? esc_url( $this->form['params']['tpl']['redirect_on_submit'] ) : ''),
						'attrs' => 'placeholder="http://example.com" style="width: 100%;"',
					))?><br />
					<label style="float: left; margin-right: 5px;">
						<?php echo htmlCfs::checkbox('params[tpl][redirect_on_submit_new_wnd]', array(
							'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'redirect_on_submit_new_wnd')))?>
						<?php _e('Open in a new window (tab)', CFS_LANG_CODE)?>
					</label>
					<label style="float: left;" class="supsystic-tooltip" title="<?php echo esc_html(sprintf(__('After user will enter all data in contact form, and form will be submitted - he will be redirected to page with his submitted data. Enter in the text field above - link to page, where you inserted shortcode [%s] - and check results. Don\'t forget to enable "Save contacts data" option too.', CFS_LANG_CODE), CFS_SHORTCODE_SUBMITTED))?>">
						<?php echo htmlCfs::checkbox('params[tpl][redirect_to_submitted]', array(
							'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'redirect_to_submitted')))?>
						<?php _e('Show submitted data', CFS_LANG_CODE)?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Email data as in Form', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default email data from form will be listed in one column. But you can enable this option - and all data will be listed as they are in form: if in row there are 2 fields - 2 fields will be in email, etc.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('params[tpl][email_form_data_as_tbl]', array(
						'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'email_form_data_as_tbl')
					))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Test Email Function', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Email delivery depends from your server configuration. For some cases - you and your subscribers can not receive emails just because email on your server is not working correctly. You can easy test it here - by sending test email. If you receive it - then it means that email functionality on your server works well. If not - this means that it is not working correctly and you should contact your hosting provider with this issue and ask them to setup email functionality for you on your server.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::email('params[tpl][test_email]', array(
						'value' => (isset($this->form['params']['tpl']['test_email']) ? $this->form['params']['tpl']['test_email'] : $this->adminEmail),
					))?>
					<a href="#" class="cfsTestEmailFuncBtn button">
						<i class="fa fa-paper-plane"></i>
						<?php _e('Send Test Email', CFS_LANG_CODE)?>
					</a>
					<div class="cfsTestEmailWasSent" style="display: none;">
						<?php _e('Email was sent. Now check your email inbox / spam folders for test mail. If you don’t find it - it means that your server can\'t send emails - and you need to contact your hosting provider with this issue.', CFS_LANG_CODE)?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Save contacts data', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Store each contact form submission - into database, so you will be able to check all your form submit data.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('params[tpl][save_contacts]', array(
						'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'save_contacts')))?>
					<span class="cfsContactExportCfsBtnShell">
						<a href="<?php echo $this->allContactsListUrl;?>" target="_blank" class="button"><?php _e('Show all contacts', CFS_LANG_CODE)?></a>
						&nbsp;
						<a href="<?php echo $this->csvExportUrl;?>" class="button cfsContactsExportBtn"><?php _e('Export to CSV', CFS_LANG_CODE)?></a>
						&nbsp;
						<label class="supsystic-tooltip" title="<?php _e('Delimiter for CSV file columns', CFS_LANG_CODE)?>">
							<?php _e('Delimiter', CFS_LANG_CODE)?>
							<?php echo htmlCfs::text('params[tpl][exp_delim]', array(
								'value' => (isset($this->form['params']['tpl']['exp_delim']) ? $this->form['params']['tpl']['exp_delim'] : ';'),
								'attrs' => 'style="max-width: 40px;" id="cfsContactsExportDelimTxt"',
							))?>
						</label>
					</span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Send only Field values', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default - we will send field labels + values like:<br /><b>Field Label</b>: Field Value<br />But if you need to receive only values - you can disable sending Field Values here - just check this checkbox and it will done.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('params[tpl][dsbl_send_labels]', array(
						'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'dsbl_send_labels')))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Facebook Conversion', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Enable Facebook Pixel and Custom conversion. More about this feature and it\'s integration - <a href="%s" target="_blank">here</a>.', CFS_LANG_CODE), 'https://www.facebook.com/business/help/952192354843755?helpref=faq_content'))?>"></i>
					<?php if(!$this->isPro) { ?>
						<span class="cfsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=fb_convert&utm_campaign=forms';?>"><?php _e('PRO option', CFS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('params[tpl][enb_fb_convert]', array(
						'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'enb_fb_convert'),
						'attrs' => 'data-switch-block="fbConvert" class="cfsProOpt"'
					))?>
				</td>
			</tr>
			<tr data-block-to-switch="fbConvert">
				<th scope="row">
					<?php _e('Fb Pixel base code', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Insert here your pixel base code from your Facebook Pixel account.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::textarea('params[tpl][fb_convert_base]', array(
						'value' => (isset($this->form['params']['tpl']['fb_convert_base']) ? $this->form['params']['tpl']['fb_convert_base'] : ''),
					))?>
				</td>
			</tr>
		</table>
		<div style="clear: both;"></div>
		<a href="" class="cfsFormSubmitAddOpt button">
			<i class="fa fa-plus"></i>
			<?php _e('Add additional data for submit', CFS_LANG_CODE)?>
		</a>
		<div id="cfsFormSubmitToListShell"></div>
		<div id="cfsFormSubmitToShellEx" class="cfsFormSubmitToShell">
			<table width="100%" class="cfsSubmitOptsHelpTbl" cellspacing="0" cellpadding="0">
				<tr>
					<td style="min-width: 150px; vertical-align: top;"><a href="#" class="cfsFormSubmitToAddCcBtn button" 
							data-on-txt="<?php _e('Add Copy', CFS_LANG_CODE)?>"
							data-off-txt="<?php _e('Remove Copy', CFS_LANG_CODE)?>"
						 >
							 <i class="fa fa-plus"></i>
							 <span class="cfsOnOffBtnLabel"></span>
						 </a>
						 <a href="#" class="cfsFormSubmitToRemoveBtn button" title="<?php _e('Remove', CFS_LANG_CODE)?>">
							 <i class="fa fa-trash-o"></i>
						 </a>
					</td>
					<td><span class="description" style="display: block;">
						<?php _e('You can use next variables in any field bellow: [sitename] - name of your site, [siteurl] - URL address of your site, [user_FIELD_NAME] - any user field, entered by user, where FIELD_NAME - is name of required field, for example insert in subject [user_email] - and there will be user email field data, or [user_first_name] - and there will be inserted user First Name - if such field exists in your form fields list, and variable [form_data] - only for Message field - it will contans full generated input form data.', CFS_LANG_CODE)?>
					</span></td>
				</tr>
			</table>
			<table class="form-table cfsFormSubmitToTbl" style="width: 100%">
				<tr>
					<th scope="row">
						<?php _e('To', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('Email where we need to send contact form info. Can enter several email addresses, separated by comma ",".', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::text('params[submit][][to]', array(
							'value' => $this->adminEmail
						))?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e('From', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('"From" parameter in your emails. Usually - this is your main admin WP email address.', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::text('params[submit][][from]', array(
							'value' => $this->adminEmail
						))?>
					</td>
				</tr>
				<tr class="cfsFormSubmitToCcShell">
					<th scope="row">
						<?php _e('Copy', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('Add recipients to copy email addresses. Can enter several email addresses, separated by comma ",".', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::text('params[submit][][cc]')?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e('Reply To', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('Reply To parameter in your email', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::text('params[submit][][reply]')?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e('Subject', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('Email subject', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::text('params[submit][][subject]')?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e('Message', CFS_LANG_CODE)?>
						<i class="fa fa-question supsystic-tooltip sup-no-init" title="<?php echo esc_html(__('Email message content', CFS_LANG_CODE))?>"></i>
					</th>
					<td>
						<?php echo htmlCfs::textarea('params[submit][][msg]')?>
					</td>
				</tr>
			</table>
			<?php echo htmlCfs::hidden('params[submit][][enb_cc]');?>
		</div>
	</div>
	<?php foreach($this->submitOptsAddTabs as $tCode => $t) { ?>
		<div id="<?php echo $tCode;?>" class="cfsTabContent"><?php echo $t['content'];?></div>
	<?php }?>
</div>

