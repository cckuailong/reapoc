<?php if(!$this->isPro) { ?>
	<?php $proLink = $this->mainLink. '?utm_source=plugin&utm_medium=registration&utm_campaign=forms'; ?>
	<p style="margin-bottom: 10px;"><?php printf(__('This is PRO option. You can <a class="button" href="%s" target="_blank">Get PRO</a> with this and many other options <a href="%s" target="_blank">here</a>.', CFS_LANG_CODE), $proLink, $proLink)?></p>
	<?php /*This link is required only for opening of PRO dialog wnd*/ ?>
	<span class="cfsProOptMiniLabel" style="display: none;">
		<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
	</span>
<?php }?>
<div class="cfsFormOptRow">
	<label>
		<?php if($this->isPro) { ?>
		<?php echo htmlCfs::checkbox('params[tpl][enb_reg]', array(
			'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'enb_reg'),
			'attrs' => 'data-switch-block="regShell" class="cfsProOpt"',
		))?>
		<?php }?>
		<?php  _e('Enable Registration from Form', CFS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Registration form option allows you to create user registration forms and combine it with any kind of content.
You can add any field to get info and allow users to select username, password, fill out their bio, add custom user information to build a nice user profile. Also, you can select the role of the new user and set registration without confirmation. <a href="%s" target="_blank">https://supsystic.com/documentation/contact-form-registration/</a>', CFS_LANG_CODE), ' https://supsystic.com/documentation/contact-form-registration/'))?>"></i>
	</label>
	<?php if(!$this->isPro) { ?>
	<span class="cfsProOptMiniLabel">
		<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
	</span>
	<?php }?>
</div>
<?php if($this->isPro) { ?>
<div data-block-to-switch="regShell">
	<table class="form-table" style="width: 100%">
		<tr>
			<th scope="row">
				<?php _e('User Role', CFS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('New user role.', CFS_LANG_CODE))?>"></i>
				<?php if(!$this->isPro) { ?>
				<span class="cfsProOptMiniLabel" style="display: none;">
					<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
				<?php }?>
			</th>
			<td>
				<div style="width: 150px;">
					<?php echo htmlCfs::selectbox('params[tpl][reg_wp_create_user_role]', array(
						'options' => $this->regRolesForSelect,
						'value' => isset($this->form['params']['tpl']['reg_wp_create_user_role']) ? $this->form['params']['tpl']['reg_wp_create_user_role'] : 'subscriber',
						'attrs' => 'class="chosen chosen-responsive cfsProOpt"',
					))?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php _e('Ignore confirmation', CFS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Create new user without confirm - right after form submit.', CFS_LANG_CODE))?>"></i>
				<?php if(!$this->isPro) { ?>
				<span class="cfsProOptMiniLabel" style="display: none;">
					<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
				<?php }?>
			</th>
			<td>
				<?php echo htmlCfs::checkbox('params[tpl][reg_ignore_confirm]', array(
					'checked' => isset($this->form['params']['tpl']['reg_ignore_confirm']) ? $this->form['params']['tpl']['reg_ignore_confirm'] : false,
					'attrs' => 'class="cfsProOpt"',
				))?>
			</td>
		</tr>

	</table>

</div>
<?php }?>
