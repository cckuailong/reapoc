<?php
	//$isPro = frameUms::_()->getModule('supsystic_promo')->isPro();
	//$promoData = frameUms::_()->getModule('supsystic_promo')->addPromoMapTabs();
	//$addProElementAttrs = $this->isPro ? '' : ' title="'. esc_html(__("This option is available in <a target='_blank' href='%s'>PRO version</a> only, you can get it <a target='_blank' href='%s'>here.</a>", UMS_LANG_CODE)). '"';
	//$addProElementClass = $this->isPro ? '' : 'supsystic-tooltip umsProOpt';
	//$addProElementBottomHtml = $this->isPro ? '' : '<span class="umsProOptMiniLabel"><a target="_blank" href="'. $this->mainLink. '">'. __('PRO option', UMS_LANG_CODE). '</a></span>';
	//$addProElementOptBottomHtml = $this->isPro ? '' : '<br /><span class="umsProOptMiniLabel" style="padding-left: 0;"><a target="_blank" href="'. $this->mainLink. '">'. __('PRO option', UMS_LANG_CODE). '</a></span>';
	/*$isCustSearchAndMarkersPeriodAvailable = true;
	if($this->isPro) {	// It's not available for old PRO
		$isCustSearchAndMarkersPeriodAvailable = false;
		if(frameUms::_()->getModule('custom_controls')
			&& method_exists(frameUms::_()->getModule('custom_controls'), 'isCustSearchAndMarkersPeriodAvailable')
			&& frameUms::_()->getModule('custom_controls')->isCustSearchAndMarkersPeriodAvailable()
		) {
			$isCustSearchAndMarkersPeriodAvailable = true;
		}
	}*/
?>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php /* ?>
			<div class="umsMapBtns supsistic-half-side-box">
				<button id="umsInsertToContactForm" class="button"><?php _e('Insert to Contact Form', UMS_LANG_CODE)?></button>
				<?php
				if(property_exists($this, 'membershipPluginError')) {
					//echo $this->membershipPluginError;
				} else if(property_exists($this, 'pluginInstallUrl')) {
					echo '<a class="button" target="_blank" href="' . $this->pluginInstallUrl . '">';
					_e('Integrate with Membership', UMS_LANG_CODE);
					echo '</a>';
				} else if(property_exists($this, 'canUseMembershipFeature') && $this->canUseMembershipFeature == 1) {
					echo '<div class="mbs-turn-on-wrapper">';
					echo htmlUms::checkboxHiddenVal('map_opts[membership-selectbox]', array(
						'value' => isset($this->map['params']['membershipEnable']) ? $this->map['params']['membershipEnable'] : 0,
						'attrs' => 'id="membershipPropEnable"',
					));
					echo '<label for="membershipPropEnable">' . _e('Enable for Membership', UMS_LANG_CODE) . '</label>';
					echo '</div>';
				}
				?>
			</div>
			<?php */ ?>
			<div style="position: relative;">
				<div class="sup-col sup-w-50">
					<?php echo htmlUms::selectbox('shortcode_example', array(
						'attrs' => 'id="umsCopyTextCodeExamples" class="umsBigSelect"',
						'options' => array(
							'shortcode' => __('Map shortcode', UMS_LANG_CODE),
							'php_code' => __('PHP code', UMS_LANG_CODE),
						),
					))?>
					<?php echo htmlUms::text('umsCopyTextCode', array(
						'value' => __('Shortcode will appear after you save map.', UMS_LANG_CODE),
						'attrs' => 'class="umsMapShortCodeShell umsStaticWidth" style="width: 64%; height: 31px; float: right; margin: 0; text-align: center;"',
						'readonly' => true,
					))?>
				</div>
				<div class="sup-col sup-w-50">
					<p>
						<label style="font-size: 15px;">
							<?php _e('Engine', UMS_LANG_CODE)?>
							<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Map engine to render. It set on <a href="%s" target="_blank">Settings page</a> for all maps, but you can select separate engines for your maps here. Just make sure that you entered all required credentials (like API Key) for your engine on Settings page, and after you will select new engine - just re-save and re-load map edit page - to make sure all changes applied.', UMS_LANG_CODE), frameUms::_()->getModule('options')->getTabUrl('settings')))?>"></i>
							<?php echo htmlUms::selectbox('engine', array(
								'options' => $this->enginesForSelect,
								'attrs' => 'id="umsEngineSel" class="umsBigSelect"',
								'value' => $this->editMap && !empty($this->map['engine']) 
									? $this->map['engine'] 
									: $this->defEngine,
							))?>
						</label>
					</p>
				</div>
			</div>
			<div style="clear: both;"></div>
			<?php do_action('ums_lang_tabs'); ?>
			<div style="clear: both;"></div>
			<div id="umsMapPropertiesTabs" style="display: none;">
				<h3 class="nav-tab-wrapper" style="margin-bottom: 12px;">
					<?php foreach($this->tabs as $tId => $t) { ?>
						<a class="nav-tab <?php echo $tId == 'umsMapTab' ? 'nav-tab-active' : ''; ?>" href="#<?php echo $tId; ?>">
							<p>
								<i class="fa <?php echo $t['icon']?>" aria-hidden="true"></i>
								<?php echo $t['label']; ?>
								<?php if(isset($t['btns'])) { ?>
									<?php foreach($t['btns'] as $btn) { ?>
										<button class="button <?php echo (isset($btn['classes']) ? $btn['classes'] : '')?>" id="<?php echo $btn['id'];?>">
											<?php echo $btn['label']; ?>
										</button>
									<?php } ?>
								<?php } ?>
							</p>
						</a>
					<?php } ?>
				</h3>
				<div style="clear: both;"></div>
				<div class="supsistic-half-side-box">
					<?php foreach($this->tabs as $tId => $t) { ?>
						<div id="<?php echo $tId; ?>" class="umsTabContent"><?php echo $t['content']; ?></div>
					<?php } ?>
				</div>
				<div class="supsistic-half-side-box" style="position: relative;">
				<div id="umsMapRightStickyBar" class="supsystic-sticky">
					<div id="umsMapPreview" style="width: 100%; height: 350px;"></div>
					<div class="umsMapProControlsCon" id="umsMapProControlsCon_<?php echo $this->viewId;?>">
						<?php dispatcherUms::doAction('addAdminMapBottomControls', $this->editMap ? $this->map : array()); ?>
					</div>
					<?php echo htmlUms::hidden('rand_view_id', array('value' => $this->viewId, 'attrs' => 'id="umsViewId"'))?>
					<div id="umsMapMainBtns" class="umsControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="umsMapSaveBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-admin-site"></i>
								<?php _e('Save Map', UMS_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="umsMapDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Map', UMS_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="umsMarkerMainBtns" class="umsControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="umsSaveMarkerBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-map-marker"></i>
								<?php _e('Save Marker', UMS_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="umsMarkerDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Marker', UMS_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="umsShapeMainBtns" class="umsControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="umsSaveShapeBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-cubes"></i>
								<?php _e('Save Shape', UMS_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="umsShapeDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Shape', UMS_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="umsHeatmapMainBtns" class="umsControlBtns row" style="display: none;">
						<div class="sup-col sup-w-50">
							<button id="umsSaveHeatmapBtn" class="button button-primary" style="width: 100%;">
								<i class="fa fa-map"></i>
								<?php _e('Save Heatmap Layer', UMS_LANG_CODE)?>
							</button>
						</div>
						<div class="sup-col sup-w-50" style="padding-right: 0;">
							<button id="umsHeatmapDeleteBtn" class="button button-primary" style="width: 100%;">
								<i class="fa dashicons-before dashicons-trash"></i>
								<?php _e('Delete Heatmap Layer', UMS_LANG_CODE)?>
							</button>
						</div>
						<div style="clear: both;"></div>
					</div>
					<div id="umsMarkerList">
						<input id="umsMarkersSearchInput" type="text" placeholder="<?php _e('Search by name', UMS_LANG_CODE)?>" style="display: none; width: 100%; margin: 0;" >
						<table id="umsMarkersListGrid" class="supsystic-tbl-pagination-shell"></table>
					</div>
					<div id="umsShapeList">
						<table id="umsShapesListGrid" class="supsystic-tbl-pagination-shell"></table>
					</div>
					<?php /*?>
					<div class="supRow">
						<div id="umsMarkerList">
							<div style="display: none;" id="markerRowTemplate" class="row umsMapMarkerRow">
								<div class="supXs12 egm-marker">
									<div class="supRow">
										<div class="supXs2 egm-marker-icon">
											<img alt="" src="">
										</div>
										<div class="supXs4 egm-marker-title">
										</div>
										<div class="supXs3 egm-marker-latlng">
										</div>
										<div class="supXs3 egm-marker-actions">
											<button title="<?php _e('Edit', UMS_LANG_CODE)?>" type="button" class="button button-small egm-marker-edit">
												<i class="fa fa-fw fa-pencil"></i>
											</button>
											<button title="<?php _e('Delete', UMS_LANG_CODE)?>" type="button" class="button button-small egm-marker-remove">
												<i class="fa fa-fw fa-trash-o"></i>
											</button>
										</div>
									</div>
								</div>
								<div style="clear: both;"></div>
							</div>
						</div>
					</div>
				<?php */?>
				</div>
			</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</section>

<!--Insert To Contact Form Wnd-->
<?php /* ?><div id="umsInsertToContactFormWnd" style="display: none;" title="<?php _e('Select Contact Form', UMS_LANG_CODE)?>">
	<?php if($this->isContactFormsInstalled) {?>
		<?php if($this->contactFormsForSelect) {?>
			<select name="contact_form" style="width: 100%; margin: 20px 0 0 0;">
				<?php foreach($this->contactFormsForSelect as $k => $v) { ?>
					<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
				<?php }?>
			</select>
		<?php } else {?>
			<span style="font-size: 14px; line-height: 25px;"><?php echo sprintf(
					'You have no Contact Forms for now. <a target="_blank" href="%s">Create your first contact form</a> then just reload page with your Map settings, and you will see list with available Contact Forms for your Map.',
					frameCfs::_()->getModule('options')->getTabUrl('forms_add_new')); ?>
			</span>
		<?php }?>
	<?php } else {?>
		<span style="font-size: 14px; line-height: 25px;"><?php echo sprintf(
				'You need to install Contact Forms by Supsystic to use this feature. <a target="_blank" href="%s">Install plugin</a> from your admin area, or visit it\'s official page on Wordpress.org <a target="_blank" href="%s">here.</a>',
				admin_url('plugin-install.php?tab=search&type=term&s=Contact+Forms+by+Supsystic'),
				'https://wordpress.org/plugins/contact-form-by-supsystic/'); ?>
		</span>
	<?php }?>
</div><?php */ ?>
<!--Map Markers List Wnd-->
<div id="umsEngineChangeWnd" style="display: none;" title="<?php _e('Change Engine', UMS_LANG_CODE)?>">
	<p><?php _e('You will now change engine for your Map. Be adviced that this action will make your map look different: each map engine have it\'s own renderer and it\'s own features.', UMS_LANG_CODE)?></p>
	<div id="umsEngineChangeMsg"></div>
</div>
