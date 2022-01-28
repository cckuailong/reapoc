<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="umsSettingsSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', UMS_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="umsSettingsForm" class="umsInputsWithDescrForm">
		<div class="supsystic-item supsystic-panel">
			<div id="containerWrapper">
				<table class="form-table">
					<?php foreach($this->options as $optCatKey => $optCatData) { ?>
						<?php /*if($optCatKey == 'system') continue;*/ /*It will be hidden for now*/?>
						<?php
							$catClass = 'umsOptCat_'. $optCatKey;
						?>
						<?php if(!isset($optCatData['hide_cat_label']) || !$optCatData['hide_cat_label']) {?>
							<tr class="<?php echo $catClass;?>">
								<th colspan="4">
									<h3><?php _e($optCatData['label'], UMS_LANG_CODE);?></h3>
								</th>
							</tr>
						<?php }?>
						<?php if(isset($optCatData['opts']) && !empty($optCatData['opts'])) { ?>
							<?php foreach($optCatData['opts'] as $optKey => $opt) { ?>
								<?php
									$htmlType = isset($opt['html']) ? $opt['html'] : false;
									$attrs = isset($opt['attrs']) ? $opt['attrs'] : '';
									if(empty($htmlType)) continue;
									if(in_array($optKey, array('cs_mode'))) continue;	// Custom options
									$htmlOums = array('value' => $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '" ' . $attrs);
									if(in_array($htmlType, array('selectbox', 'selectlist')) && isset($opt['options'])) {
										if(is_callable($opt['options'])) {
											$htmlOums['options'] = call_user_func( $opt['options'] );
										} elseif(is_array($opt['options'])) {
											$htmlOums['options'] = $opt['options'];
										}
									}
									if(isset($opt['pro']) && !empty($opt['pro'])) {
										$htmlOums['attrs'] .= ' class="umsProOpt"';
									}
									$htmlInput = htmlUms::$htmlType('opt_values['. $optKey. ']', $htmlOums);
									if(in_array($htmlType, array('hidden'))) {
										echo $htmlInput;	// Just show hidden field, without any row at all
										continue;
									}
								?>
								<tr 
									<?php if(isset($opt['connect']) && $opt['connect']) { ?>
										data-connect="<?php echo $opt['connect'];?>" style="display: none;"
									<?php }?>
								class="<?php echo $catClass;?>">
									<th scope="row" class="col-perc col-w-20perc">
										<?php _e($opt['label'], UMS_LANG_CODE);?>
										<?php if(!empty($opt['changed_on'])) {?>
											<br />
											<span class="description">
												<?php 
												$opt['value'] 
													? printf(__('Turned On %s', UMS_LANG_CODE), dateUms::_($opt['changed_on']))
													: printf(__('Turned Off %s', UMS_LANG_CODE), dateUms::_($opt['changed_on']))
												?>
											</span>
										<?php }?>
										<?php if(isset($opt['pro']) && !empty($opt['pro'])) { ?>
											<span class="umsProOptMiniLabel">
												<a href="<?php echo $opt['pro']?>" target="_blank">
													<?php _e('PRO option', UMS_LANG_CODE)?>
												</a>
											</span>
										<?php }?>
									</th>
									<td class="col-perc col-w-1perc">
										<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html($opt['desc']);?>"></i>
									</td>
									<td class="col-perc col-w-8perc">
										<?php echo $htmlInput;?>
									</td>
								</tr>
							<?php }?>
						<?php } ?>
						<?php if(isset($optCatData['opts_html'])) { ?>
							<tr class="<?php echo $catClass;?>">
								<td colspan="3">
									<?php
										if(is_callable($optCatData['opts_html'])) {
											echo call_user_func( $optCatData['opts_html'] );
										} elseif(is_string($opt['options'])) {
											echo $optCatData['opts_html'];
										}
									?>
								</td>
							</tr>
						<?php }?>
					<?php }?>
				</table>
				<div style="clear: both;"></div>
			</div>
		</div>
		<?php echo htmlUms::hidden('mod', array('value' => 'options'))?>
		<?php echo htmlUms::hidden('action', array('value' => 'saveGroup'))?>
	</form>
</section>