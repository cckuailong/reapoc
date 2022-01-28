<table class="form-table cfsFormFieldsSettingsOptsTbl" style="width: 100%">
	<tr>
		<th scope="row">
			<?php _e('Field invalid error message', CFS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default our plugin will show standard browser error messages about invalid or empty fields values. But if you need - you can replace it here. Use [label] - to set field name in your error message. For example "Please fill out [label] field". You can just leave this field empty - to use standard browser messages.', CFS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlCfs::text('params[tpl][field_error_invalid]', array(
				'value' => isset($this->form['params']['tpl']['field_error_invalid']) ? $this->form['params']['tpl']['field_error_invalid'] : ''
			))?>
		</td>
	</tr>
</table>
<div style="clear: both;"></div>
<a href="#" class="cfsAddFieldBtn button">
	<i class="fa fa-plus"></i>
	<?php _e('Add New Field', CFS_LANG_CODE)?>
</a>
<div style="clear: both;"></div>
<hr />
<div style="clear: both;"></div>
<div id="cfsFieldsEditShell"></div>
<a href="#" class="cfsMoveVFieldHandle" id="cfsMoveVFieldHandleExl" title="<?php _e('Move Up / Down', CFS_LANG_CODE)?>">
	<i class="fa fa-arrows-v" style="font-size: 24px;"></i>
</a>
<div id="cfsFieldShellEx" class="cfsFieldShell">
	<div class="cfsFieldShellBody button">
		<div class="cfsFieldPanel">
			<a href="#" class="cfsMoveHFieldHandle" title="<?php _e('Move Left / Right', CFS_LANG_CODE)?>">
				<i class="fa fa-arrows-h" style="font-size: 24px;"></i>
			</a>
			<a href="#" class="cfsAddTopBtn" title="<?php _e('Add New Field at the Top', CFS_LANG_CODE)?>">
				<i class="fa fa-arrow-up" style="position: absolute; bottom: 13px; right: 1px;"></i>
				<i class="fa fa-plus" style="font-size: 10px;"></i>
			</a>
			<a href="#" class="cfsAddRightBtn" title="<?php _e('Add New Field at the Right', CFS_LANG_CODE)?>">
				<i class="fa fa-plus" style="font-size: 10px; position: absolute; top: -7px; left: 3px;"></i>
				<i class="fa fa-arrow-right"></i>
			</a>
			<a href="#" class="cfsAddBottomBtn" title="<?php _e('Add New Field at the Bottom', CFS_LANG_CODE)?>">
				<i class="fa fa-plus" style="font-size: 10px; position: absolute; top: -7px; left: 3px;"></i>
				<i class="fa fa-arrow-down"></i>
			</a>
			<a href="#" class="cfsAddLeftBtn" title="<?php _e('Add New Field at the Left', CFS_LANG_CODE)?>">
				<i class="fa fa-arrow-left" style="position: absolute; bottom: 13px; right: 1px;"></i>
				<i class="fa fa-plus" style="font-size: 10px;"></i>
			</a>
			<a href="#" class="cfsFieldRemoveBtn" title="<?php _e('Remove', CFS_LANG_CODE)?>">
				<i class="fa fa-trash fa-2x"></i>
			</a>
		</div>
		<div class="csfFieldIcon"></div>
		<div class="csfFieldLabel"></div>
		<div class="csfFieldType"></div>
	</div>
	<?php echo htmlCfs::hidden('params[fields][][bs_class_id]')?>
	<?php /*echo htmlCfs::hidden('params[fields][][label]')?>
	<?php echo htmlCfs::hidden('params[fields][][placeholder]')?>
	<?php echo htmlCfs::hidden('params[fields][][html]')?>
	<?php echo htmlCfs::hidden('params[fields][][value]')?>
	<?php echo htmlCfs::hidden('params[fields][][mandatory]')?>
	<?php echo htmlCfs::hidden('params[fields][][name]')?>
	<?php echo htmlCfs::hidden('params[fields][][bs_class_id]')?>
	<?php echo htmlCfs::hidden('params[fields][][display]')?>
	
	<?php echo htmlCfs::hidden('params[fields][][min_size]')?>
	<?php echo htmlCfs::hidden('params[fields][][max_size]')?>
	<?php echo htmlCfs::hidden('params[fields][][add_classes]')?>
	<?php echo htmlCfs::hidden('params[fields][][add_styles]')?>
	<?php echo htmlCfs::hidden('params[fields][][add_attr]')?>
	
	<?php echo htmlCfs::hidden('params[fields][][vn_only_number]')?>
	<?php echo htmlCfs::hidden('params[fields][][vn_only_letters]')?>
	<?php echo htmlCfs::hidden('params[fields][][vn_pattern]')*/?>
</div>
<div id="cfsFieldsAddWnd" title="<?php _e('Click on required elements from list bellow', CFS_LANG_CODE)?>">
	<div class="cfsFieldsAddWndElementsShell">
		<?php foreach($this->fieldTypes as $ftCode => $ft) { ?>
		<?php $pro = (isset($ft['pro']) && !$this->isPro && !empty($ft['pro'])) ? $ft['pro'] : false;?>
		<div class="cfsFieldWndElement button" 
			 data-html="<?php echo $ftCode;?>"
			 <?php if($pro) { ?>
				 data-pro="1"
			 <?php }?>
		>
			<i class="fa <?php echo $ft['icon']?>"></i>
			<span class="cfsFieldWndElementLabel"><?php echo $ft['label']?></span>
			<?php if($pro) { ?>
				<span class="cfsProOptMiniLabel">
					<a href="<?php echo $pro;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>
<div id="cfsFieldsEditWnd" title="<?php _e('Edit field settings', CFS_LANG_CODE)?>">
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab" href="#cfsFormFieldBaseSettings">
			<i class="fa fa-cog"></i>
			<?php _e('Basic Settings', CFS_LANG_CODE)?>
		</a>
		<a class="nav-tab" href="#cfsFormFieldIconSettings">
			<i class="fa fa-picture-o"></i>
			<?php _e('Icon', CFS_LANG_CODE)?>
		</a>
		<a class="nav-tab" href="#cfsFormFieldAdvancedSettings">
			<i class="fa fa-cogs"></i>
			<?php _e('Advanced', CFS_LANG_CODE)?>
		</a>
		<a class="nav-tab" href="#cfsFormFieldValidation">
			<i class="fa fa-wrench"></i>
			<?php _e('Field Validation', CFS_LANG_CODE)?>
		</a>
	</h3>
	<div id="cfsFormFieldBaseSettings" class="cfsTabContent">
		<table class="form-table">
			<tr class="cfsFieldParamRow" data-not-for="checkboxsubscribe,wooattrs">
				<th>
					<?php _e('Name', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Name attribute for your field. You can use here latin letters, numbers or symdols "-", "_".', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('name')?>
				</td>
			</tr>
			<tr class="cfsFieldEditErrorRow" data-for="name">
				<td colspan="2" class="description">
					<?php _e('Please fill-in Name for your field, and make sure that it contains only latin letters, numbers or symdols "-", "_".', CFS_LANG_CODE)?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow">
				<th>
					<?php _e('Label', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Field label - that your users will see on your Form right near your field.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('label')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="selectlist,selectbox,checkbox,checkboxlist,radiobutton,radiobuttons,countryList,countryListMultiple,recaptcha,checkboxsubscribe,button,submit,reset,rating,wooattrs,wpcategories">
				<th>
					<?php _e('Placeholder', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Field placeholder - will be printed in your field as a tip.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('placeholder')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="rating">
				<th>
					<?php _e('Rate Stars Count', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Number of icons for rating.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::number('rate_num', array(
						'value' => 5,
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="time">
				<th>
					<?php _e('Time Format', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Format for your Time input.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('time_format', array(
						'options' => array(
							'am_pm' => 'AM / PM'
						,	'24h' => __('24 Hours', CFS_LANG_CODE)
						),
						'value' => 'am_pm',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldEditErrorRow" data-for="label-placeholder">
				<td colspan="2" class="description">
					<?php _e('Please fill-in Label or Placeholder for your field - it\'s required for users to know - what field in Form that are filling-in.', CFS_LANG_CODE)?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow cfsFieldFixedSelectRow" data-not-for="file,recaptcha,button,submit,reset,rating,wooattrs">
				<th>
					<?php _e('Default Value', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can set default value for your field, and one it appear on your site - field will be pre-filled with this value.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('value')?><br />
					<?php echo htmlCfs::selectbox('value_preset', array(
						'options' => array(
							'' => __('or select preset', CFS_LANG_CODE),
							'user_ip' => __('User IP', CFS_LANG_CODE), 
							'user_country_code' => __('User Country code', CFS_LANG_CODE),
							'user_country_label' => __('User Country name', CFS_LANG_CODE),
							'page_title' => __('Page Title', CFS_LANG_CODE),
							'page_url' => __('Page URL', CFS_LANG_CODE),
						),
						'attrs' => 'class="wnd-chosen"',
					))?><i class="fa fa-question supsystic-tooltip" style="float: none; margin-left: 5px;" title="<?php echo esc_html(__('Allow to insert some pre-defined values, like current user IP addres, or his country - to send you this data.', CFS_LANG_CODE))?>"></i>
					<?php if(!$this->isPro) { ?>
						<span class="cfsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=value_preset&utm_campaign=forms';?>"><?php _e('PRO option', CFS_LANG_CODE)?></a></span>
					<?php }?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="selectbox,selectlist,radiobuttons,checkboxlist,countryList,countryListMultiple">
				<th>
					<?php _e('Label Delimiter', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Those fields will have possibility to add some delimiter after field label if required, for example - ":" symbol.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('label_delim')?>
				</td>
			</tr>
			<tr class="cfsFieldsEditForCheckRadioLists cfsFieldParamRow" data-for="radiobuttons,checkboxlist">
				<th><?php _e('Display as', CFS_LANG_CODE)?></th>
				<td>
					<?php echo htmlCfs::selectbox('display', array('options' => array(
						'row' => __('In row', CFS_LANG_CODE),
						'col' => __('In column', CFS_LANG_CODE),
					)))?>
				</td>
				<?php echo htmlCfs::hidden('params[fields][][display]')?>
			</tr>
			<tr class="cfsFieldsEditForLists cfsFieldParamRow" style="display: none;">
				<th>
					<?php _e('Select Options', CFS_LANG_CODE)?>
				</th>
				<td>
					<a class="button cfsFieldsAddListOpt">
						<i class="fa fa-plus"></i>
						<?php _e('Add Option', CFS_LANG_CODE)?>
					</a>
				</td>
			</tr>
			<tr class="cfsFieldsEditForLists cfsFieldParamRow" style="display: none; height: auto;">
				<td colspan="2" style="padding: 0;">
					<div id="cfsFieldsListOptsShell">
						<div id="cfsFieldListOptShellExl" class="cfsFieldListOptShell">
							<i class="fa fa-arrows-v lcsMoveHandle"></i>
							<?php echo htmlCfs::text('options[][name]', array(
								'placeholder' => __('Name', CFS_LANG_CODE),
								'disabled' => true,
							))?>
							<?php echo htmlCfs::text('options[][label]', array(
								'placeholder' => __('Label', CFS_LANG_CODE),
								'disabled' => true,
							))?>
							<a href="#" class="button button-small cfsFieldsListOptRemoveBtn" title="<?php _e('Remove', CFS_LANG_CODE)?>">
								<i class="fa fa-trash-o"></i>
							</a>
						</div>
					</div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="checkbox,radiobutton,checkboxsubscribe">
				<th><?php _e('Checked by Default', CFS_LANG_CODE)?></th>
				<td>
					<?php echo htmlCfs::checkbox('def_checked', array(
						'value' => 1,
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="wooattrs">
				<th>
					<?php _e('Select Attribute', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Select attribute from Woo for link this field to.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('wooattrs')?>
					<div id="cfsWooAttrsLoader"></div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="wooattrs">
				<th>
					<?php _e('Multiple', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Possibility to select multiple items.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('wooattrs_mult', array(
						'value' => 1
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="wpcategories">
				<th>
					<?php _e('Select Category Type', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Categories can be different - depends on what Post Type you need to use.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('tax')?>
					<div id="cfsWpCatLoader"></div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="wpcategories">
				<th>
					<?php _e('Categories', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('And select required categories for Form.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectlist('terms', array(
						'attrs' => 'class="wnd-chosen-terms" data-placeholder="'. __('Select available list', CFS_LANG_CODE). '" style="min-width: 200px;"',
					))?>
					<div id="cfsWpCatsLoader"></div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="wpcategories">
				<th>
					<?php _e('Display as', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('How user will be able to select category.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('wpcat_display', array(
						'options' => array(
							'selectbox' => __('Selectbox', CFS_LANG_CODE),
							'checkbox' => __('Checkbox', CFS_LANG_CODE),
						),
					))?>
					<div id="cfsWpCatsLoader"></div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="address">
				<th>
					<?php _e('Google Maps API Key', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can use your own Google API key, check the <a href="//supsystic.com/google-maps-api-key/" target="_blank">instruction</a> how to create it. To use plugin\'s default API key leave this field blank.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('gmapkey')?>
					<div id="cfsWpCatsLoader"></div>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="recaptcha,button,submit,reset">
				<th><?php _e('Required', CFS_LANG_CODE)?></th>
				<td>
					<?php echo htmlCfs::checkbox('mandatory', array(
						'value' => 1,
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('Site Key', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Your site key, generated on <a href="%s" target="_blank">%s</a>. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', CFS_LANG_CODE), 'https://www.google.com/recaptcha/admin#list', 'https://www.google.com/recaptcha/admin#list', 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('recap-sitekey')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('Secret Key', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Your secret key, generated on <a href="%s" target="_blank">%s</a>. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', CFS_LANG_CODE), 'https://www.google.com/recaptcha/admin#list', 'https://www.google.com/recaptcha/admin#list', 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('recap-secret')?>
				</td>
			</tr>
		</table>
	</div>
	<div id="cfsFormFieldIconSettings" class="cfsTabContent">
		<table class="form-table">
			<tr class="cfsFieldParamRow" data-not-for="file,recaptcha,reset,hidden">
				<td colspan="2">
					<?php if(!$this->isPro) { ?>
						<?php $proLink = $this->mainLink. '?utm_source=plugin&utm_medium=field_icon&utm_campaign=forms'; ?>
						<p style="margin-bottom: 10px;"><?php printf(__('This is PRO option. You can <a class="button" href="%s" target="_blank">Get PRO</a> with this and many other options <a href="%s" target="_blank">here</a>.', CFS_LANG_CODE), $proLink, $proLink)?></p>
						<?php /*This link is required only for opening of PRO dialog wnd*/ ?>
						<span class="cfsProOptMiniLabel" style="display: none;">
							<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
						</span>
					<?php }?>
					<?php echo htmlCfs::text('icon_search', array(
						'attrs' => 'placeholder="'. __('Search', CFS_LANG_CODE). '" id="cfsFieldIconSearchInp"',
					))?>
					<span id="cfsFieldIconSelected"></span>
					<?php echo htmlCfs::hidden('icon_class', array(
						'attrs' => 'id="cfsFieldIconClassInp"'
					))?>
					<br />
					<ul id="cfsFieldIconsShell"></ul>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="file,recaptcha,reset,hidden">
				<th>
					<?php _e('Icon Size', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Set size for icon, default is size like all other text.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('icon_size', array(
						'options' => array(
							'' => __('Default', CFS_LANG_CODE),
							'lg' => __('33% increase', CFS_LANG_CODE),
							'2x' => __('2x', CFS_LANG_CODE),
							'3x' => __('3x', CFS_LANG_CODE),
							'4x' => __('4x', CFS_LANG_CODE),
							'5x' => __('5x', CFS_LANG_CODE),
						),
						'attrs' => 'class="cfsProOpt"',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="file,recaptcha,reset,hidden">
				<th>
					<?php _e('Icon Color', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Custom color for your icon.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::colorpicker('icon_color', array(
						'attrs' => 'class="cfsProOpt"',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="rating">
				<th>
					<?php _e('Selected Color', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Color for selected rating stars.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::colorpicker('icon_selected_color', array(
						'attrs' => 'class="cfsProOpt"',
					))?>
				</td>
			</tr>
			
		</table>
	</div>
	<div id="cfsFormFieldAdvancedSettings" class="cfsTabContent">
		<table class="form-table">
			<tr class="cfsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCaptcha Theme', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('The color theme. You can select from themes, provided by Google, for your reCaptcha. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', CFS_LANG_CODE), 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('recap-theme', array(
						'options' => array('light' => __('Light', CFS_LANG_CODE), 'dark' => __('Dark', CFS_LANG_CODE)),
						'value' => 'light',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCaptcha Type', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('The type of CAPTCHA to serve.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('recap-type', array(
						'options' => array('audio' => __('Audio', CFS_LANG_CODE), 'image' => __('Image', CFS_LANG_CODE)),
						'value' => 'image',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCaptcha Size', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('The size of the CAPTCHA widget.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('recap-size', array(
						'options' => array('compact' => __('Compact', CFS_LANG_CODE), 'normal' => __('Normal', CFS_LANG_CODE)),
						'value' => 'normal',
					))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional classes', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal CSS classes for your field.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('add_classes')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional styles', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal CSS styles, that will be included in "style" tag, for your field.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('add_styles')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional HTML attributes', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal HTML attributes, such as "id", or other, for your field.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('add_attr')?>
				</td>
			</tr>
		</table>
	</div>
	<div id="cfsFormFieldValidation" class="cfsTabContent">
		<table class="form-table">
			<tr class="cfsFieldParamRow" data-for="text,email,textarea,number,date,month,week,time,color,range,url,file,rating">
				<th>
					<?php _e('Minimum length', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Possibility to bound minimum length for field value. For text fields - it is the text length. For Files fields types - this is restriction for file size, in Mb.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('min_size')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="text,email,textarea,number,date,month,week,time,color,range,url,file,rating">
				<th>
					<?php _e('Maximum length', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Possibility to bound maximum length for field value. For text fields - it is the text length. For Files fields types - this is restriction for file size, in Mb.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('max_size')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="text,textarea,email,url,date,time,number,rating">
				<th>
					<?php _e('Only numbers', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Allow users to enter in this field - only numeric values.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('vn_only_number')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="text,textarea,email,url,date,time,number,rating">
				<th>
					<?php _e('Only letters', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Only letters will be allowed.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::checkbox('vn_only_letters')?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="text,textarea,email,url,date,time,number,file,rating">
				<th>
					<?php _e('Validation Pattern', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can modify or set here your custom patters. Edit this ONLY if you know how to modify regular expression patterns! For Files fields types you can set here file extensions, separated by comma - ",".', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::text('vn_pattern', array('attrs' => 'style="width: 100%;"'))?>
				</td>
			</tr>
			<tr class="cfsFieldParamRow" data-for="text,textarea,email,url,date,time,number">
				<th>
					<?php _e('Equal to', CFS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Will require this field to be equal to other field from form. For example - use this is you want to create Email confirm fields.', CFS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlCfs::selectbox('vn_equal', array('attrs' => 'class="wnd-equal-to-chosen"'))?>
				</td>
			</tr>
		</table>
	</div>
	<?php echo htmlCfs::hidden('html')?>
</div>
<div id="cfsFormFieldHtmlInpWnd" style="display: none;" title="<?php _e('HTML / Text / Images / etc.', CFS_LANG_CODE)?>">
	<?php wp_editor('', 'cfs_html_field_editor')?>
</div>
<div id="cfsFormFieldGoogleMapsWnd" style="display: none;" title="<?php _e('Select desired Map', CFS_LANG_CODE)?>">
	<?php if($this->isGoogleMapsAvailable) { ?>
		<?php if(!empty($this->allGoogleMapsForSelect)) { ?>
			<label><?php _e('Select Map')?>: <?php echo htmlCfs::selectbox('cfs_gmap_sel', array(
				'options' => $this->allGoogleMapsForSelect,
				'attrs' => 'id="cfsFieldGoogleMapsSel"',
			))?></label>
		<?php } else { ?>
			<div class="description"><p><?php printf(__('You have no Google Maps for now. <a href="%s" target="_blank" class="button">Create Maps</a> at first, then you will be able to select it here and past into your form', CFS_LANG_CODE), frameGmp::_()->getModule('options')->getTabUrl('gmap_add_new'))?></p></div>
		<?php } ?>
	<?php } else { ?>
		<div class="description"><p><?php printf(__('To use this field type you need to have installed and activated <a href="%s" target="_blank">Google Maps Easy</a> plugin - it\'s Free! Just install it <a class="button" target="_blank" href="%s">here.</a>', CFS_LANG_CODE), 'https://wordpress.org/plugins/google-maps-easy/', admin_url('plugin-install.php?tab=search&s=Google+Maps+Easy+Supsystic'))?></p></div>
	<?php } ?>
</div>
