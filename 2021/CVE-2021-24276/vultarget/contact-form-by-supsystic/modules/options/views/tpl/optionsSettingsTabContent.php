<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="cfsSettingsSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', CFS_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="cfsSettingsForm" class="cfsInputsWithDescrForm">
		<div class="supsystic-item supsystic-panel">
			<div id="containerWrapper">
				<table class="form-table">
					<?php foreach($this->options as $optCatKey => $optCatData) { ?>
						<?php if(isset($optCatData['opts']) && !empty($optCatData['opts'])) { ?>
							<?php foreach($optCatData['opts'] as $optKey => $opt) { ?>
								<?php
									$htmlType = isset($opt['html']) ? $opt['html'] : false;
									if(empty($htmlType)) continue;
									$htmlOpts = array('value' => empty($opt['value']) ? '' : $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '"');
									if(in_array($htmlType, array('selectbox', 'selectlist')) && isset($opt['options'])) {
										if(is_callable($opt['options'])) {
											$htmlOpts['options'] = call_user_func( $opt['options'] );
										} elseif(is_array($opt['options'])) {
											$htmlOpts['options'] = $opt['options'];
										}
									}
									if(isset($opt['pro']) && !empty($opt['pro'])) {
										$htmlOpts['attrs'] .= ' class="cfsProOpt"';
									}
								?>
								<tr
									<?php if(isset($opt['connect']) && $opt['connect']) { ?>
										data-connect="<?php echo $opt['connect'];?>" style="display: none;"
									<?php }?>
								>
									<th scope="row" class="col-w-30perc">
										<?php echo $opt['label']?>
										<?php if(!empty($opt['changed_on'])) {?>
											<br />
											<span class="description">
												<?php 
												$opt['value'] 
													? printf(__('Turned On %s', CFS_LANG_CODE), dateCfs::_($opt['changed_on']))
													: printf(__('Turned Off %s', CFS_LANG_CODE), dateCfs::_($opt['changed_on']))
												?>
											</span>
										<?php }?>
										<?php if(isset($opt['pro']) && !empty($opt['pro'])) { ?>
											<span class="cfsProOptMiniLabel">
												<a href="<?php echo $opt['pro']?>" target="_blank">
													<?php _e('PRO option', CFS_LANG_CODE)?>
												</a>
											</span>
										<?php }?>
									</th>
									<td class="col-w-1perc">
										<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html($opt['desc'])?>"></i>
									</td>
									<td class="col-w-1perc">
										<?php echo htmlCfs::$htmlType('opt_values['. $optKey. ']', $htmlOpts)?>
									</td>
									<td class="col-w-60perc">
										<div id="cfsFormOptDetails_<?php echo $optKey?>" class="cfsOptDetailsShell">
										<?php switch($optKey) {

										}?>
										<?php
											if(isset($opt['add_sub_opts']) && !empty($opt['add_sub_opts'])) {
												if(is_string($opt['add_sub_opts'])) {
													echo $opt['add_sub_opts'];
												} elseif(is_callable($opt['add_sub_opts'])) {
													echo call_user_func_array($opt['add_sub_opts'], array($this->options));
												}
											}
										?>
										</div>
									</td>
								</tr>
							<?php }?>
						<?php }?>
					<?php }?>
				</table>
				<div style="clear: both;"></div>
			</div>
		</div>
		<?php echo htmlCfs::hidden('mod', array('value' => 'options'))?>
		<?php echo htmlCfs::hidden('action', array('value' => 'saveGroup'))?>
	</form>
</section>