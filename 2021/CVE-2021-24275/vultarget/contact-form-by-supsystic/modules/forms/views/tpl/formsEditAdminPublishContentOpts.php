<?php if(!$this->isPro) { ?>
	<?php $proLink = $this->mainLink. '?utm_source=plugin&utm_medium=publish_content&utm_campaign=forms'; ?>
	<p style="margin-bottom: 10px;"><?php printf(__('This is PRO option. You can <a class="button" href="%s" target="_blank">Get PRO</a> with this and many other options <a href="%s" target="_blank">here</a>.', CFS_LANG_CODE), $proLink, $proLink)?></p>
	<?php /*This link is required only for opening of PRO dialog wnd*/ ?>
	<span class="cfsProOptMiniLabel" style="display: none;">
		<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
	</span>
<?php }?>
<div class="cfsFormOptRow">
	<label>
		<?php if($this->isPro) { ?>
		<?php echo htmlCfs::checkbox('params[tpl][enb_publish]', array(
			'checked' => htmlCfs::checkedOpt($this->form['params']['tpl'], 'enb_publish'),
			'attrs' => 'data-switch-block="postPublishContentShell" class="cfsProOpt"',
		))?>
		<?php }?>
		<?php  _e('Enable Publish Content', CFS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Publish Content feature allows your site visitors to post content through your Contact Form. <a href="%s" target="_blank">https://supsystic.com/documentation/contact-form-publish-content/</a>', CFS_LANG_CODE), 'https://supsystic.com/documentation/contact-form-publish-content/'))?>"></i>
	</label>
	<?php if(!$this->isPro) { ?>
	<span class="cfsProOptMiniLabel">
		<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
	</span>
	<br>
	<?php }?>
</div>

<?php if($this->isPro) { ?>
<div data-block-to-switch="postPublishContentShell">
	<table class="form-table" style="width: 100%">
		<tr>
			<th scope="row">
				<?php _e('Created Post Type', CFS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Post Type for created content.', CFS_LANG_CODE))?>"></i>
				<?php if(!$this->isPro) { ?>
				<span class="cfsProOptMiniLabel" style="display: none;">
					<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
				<?php }?>
			</th>
			<td>
				<div style="width: 150px;">
					<?php echo htmlCfs::selectbox('params[tpl][pub_post_type]', array(
						'options' => $this->postTypesForSelect,
						'value' => isset($this->form['params']['tpl']['pub_post_type']) ? $this->form['params']['tpl']['pub_post_type'] : 'post',
						'attrs' => 'class="chosen chosen-responsive cfsProOpt"',
					))?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php _e('Post Status', CFS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can make it published right after user will publish it here, or make it Pending Review - to be able approve it by Yourself.', CFS_LANG_CODE))?>"></i>
				<?php if(!$this->isPro) { ?>
				<span class="cfsProOptMiniLabel" style="display: none;">
					<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
				<?php }?>
			</th>
			<td>
				<div style="width: 150px;">
					<?php echo htmlCfs::selectbox('params[tpl][pub_post_status]', array(
						'options' => $this->postStatusesForSelect,
						'value' => isset($this->form['params']['tpl']['pub_post_status']) ? $this->form['params']['tpl']['pub_post_status'] : 'publish',
						'attrs' => 'class="chosen chosen-responsive cfsProOpt"',
					))?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php _e('Post Category', CFS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Push posts to categories.', CFS_LANG_CODE))?>"></i>
				<?php if(!$this->isPro) { ?>
				<span class="cfsProOptMiniLabel" style="display: none;">
					<a href="<?php echo $proLink;?>" target="_blank"><?php _e('PRO', CFS_LANG_CODE)?></a>
				</span>
				<?php }?>
			</th>
			<td>
				<?php echo htmlCfs::selectlist('params[tpl][pub_post_category]', array(
					'options' => $this->allCategoriesForSelect,
					'value' => isset($this->form['params']['tpl']['pub_post_category']) ? $this->form['params']['tpl']['pub_post_category'] : array(),
					'attrs' => 'class="chosen cfsProOpt"',
				))?>
			</td>
		</tr>

	</table>

</div>
<?php } ?>
