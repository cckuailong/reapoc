<form id="umsMapForm">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label class="label-big" for="map_opts_title">
					<?php _e('Map Name', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map name', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::text('map_opts[title]', array(
					'value' => $this->editMap ? $this->map['title'] : '',
					'attrs' => 'style="width: 100%;" id="map_opts_title"',
					'required' => true))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="map_opts_width">
					<?php _e('Map Width', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map width', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<div class="sup-col sup-w-25">
					<?php echo htmlUms::text('map_opts[width]', array(
						'value' => $this->editMap ? $this->map['html_options']['width'] : '100',
						'attrs' => 'style="width: 100%;" id="map_opts_width"'))?>
				</div>
				<div class="sup-col sup-w-75">
					<label class="supsystic-tooltip" title="<?php _e('Pixels', UMS_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlUms::radiobutton('map_opts[width_units]', array(
						'value' => 'px',
						'checked' => $this->editMap ? htmlUms::checkedOpt($this->map['params'], 'width_units', 'px') : false,
					))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?></label>
					<label style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlUms::radiobutton('map_opts[width_units]', array(
						'value' => '%',
						'checked' => $this->editMap ? htmlUms::checkedOpt($this->map['params'], 'width_units', '%') : true,
					))?>&nbsp;<?php _e('Percents', UMS_LANG_CODE)?></label>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="map_opts_height">
					<?php _e('Map Height', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your map height.' .
				'<br /><br />If Adapt map to screen height option is checked - map height will be recalculated on frontend and can be equals to:' .
				'<ul>' .
				'<li>1) your device screen height - height from top of page to top of map (if screen height > height from top of page to top of map)</li>' .
				'<li>2) your device screen height (in other cases)</li>' .
				'</ul>' .
				'Recalculation will be done for maps in page content and widgets except of maps which displaying in Ultimate Maps by Supsystic widget popup (Display as image mode).', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<div class="sup-col no-p">
					<div class="sup-col sup-w-50 umsMainHeightOpts" style="padding-right: 15px;">
						<?php echo htmlUms::text('map_opts[height]', array(
							'value' => $this->editMap ? $this->map['html_options']['height'] : '250',
							'attrs' => 'style="width: 100%;" id="map_opts_height"'))?>
					</div>
					<div class="sup-col sup-w-25 no-p umsMainHeightOpts">
						<label class="supsystic-tooltip" title="<?php _e('Pixels', UMS_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;"><?php echo htmlUms::radiobutton('map_opts_height_units_is_constant', array(
							'value' => 'px',
							'checked' => true,
						))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?></label>
					</div>
					<div class="sup-col sup-w-25 no-p">
						<label>
							<?php echo htmlUms::checkboxHiddenVal('map_opts[adapt_map_to_screen_height]', array(
								'value' => $this->editMap && isset($this->map['params']['adapt_map_to_screen_height']) ? $this->map['params']['adapt_map_to_screen_height'] : false,
							))?>
							<span style="vertical-align: middle;">
								<?php _e('Adapt map to screen height', UMS_LANG_CODE)?>
							</span>
						</label>
					</div>
				</div>

			</td>
		</tr>
	</table>
	<?php /*?><div id="umsExtendOptsBtnShell" class="row-pad">
		<a href="#" id="umsExtendOptsBtn" class="button"><?php _e('Extended Options', UMS_LANG_CODE)?></a>
	</div><?php */?>
	<div id="umsExtendOptsShell" class="supRow">
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="map_opts_navigation_bar_mode">
						<?php _e('Navigation bar Mode', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Control view for map type - you can see it in right upper corner by default', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div class="sup-col sup-w-100">
						<?php echo htmlUms::selectbox('map_opts[navigation_bar_mode]', array(
							'options' => $this->engineOpts['navigation_bar_mode'],
							'value' => $this->editMap && isset($this->map['params']['navigation_bar_mode']) ? $this->map['params']['navigation_bar_mode'] : 'HORIZONTAL_BAR',
							'attrs' => 'style="width: 100%;" id="map_opts_navigation_bar_mode"'))?>
					</div>
					<?php /*?><div class="sup-col sup-w-50">
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=navigation_bar_mode_position&utm_campaign=ultimatemaps'); ?>
						<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change type control position on map', UMS_LANG_CODE)?>"></i>
						<?php echo htmlUms::selectbox('map_opts[navigation_bar_mode_position]', array(
							'options' => $this->positionsList,
							'value' => $this->editMap && isset($this->map['params']['navigation_bar_mode_position']) ? $this->map['params']['navigation_bar_mode_position'] : 'TOP_RIGHT',
							'attrs' => 'data-for="mapTypeControlOptions" class="umsMapPosChangeSelect '. $this->addProElementClass. '"'. (empty($this->addProElementAttrs) ? '' : sprintf($this->addProElementAttrs, $proLink, $proLink))
						))?>
						<?php if(!$this->isPro) { ?>
							<span class="umsProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
						<?php }?>
					</div><?php */ ?>
				</td>
			</tr>
			<?php /*?><tr>
				<th scope="row">
					<label for="map_opts_zoom_control">
						<?php _e('Zoom control', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Zoom control type on your map. Note, to view Zoom control on the map the Custom Map Controls option must be disabled.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div>
						<div class="sup-col sup-w-50">
							<?php echo htmlUms::selectbox('map_opts[zoom_control]', array(
								'options' => array('none' => __('None', UMS_LANG_CODE), 'DEFAULT' => __('Default', UMS_LANG_CODE)),
								'value' => $this->editMap && isset($this->map['params']['zoom_control']) ? $this->map['params']['zoom_control'] : 'DEFAULT',
								'attrs' => 'style="width: 100%;" id="map_opts_zoom_control"'))?>
						</div>
						<div class="sup-col sup-w-50">
							<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=zoom_control_position&utm_campaign=ultimatemaps'); ?>
							<i class="fa fa-arrows supsystic-tooltip" title="<?php _e('Change zoom control position on map', UMS_LANG_CODE)?>"></i>
							<?php echo htmlUms::selectbox('map_opts[zoom_control_position]', array(
								'options' => $this->positionsList,
								'value' => $this->editMap && isset($this->map['params']['zoom_control_position']) ? $this->map['params']['zoom_control_position'] : 'TOP_LEFT',
								'attrs' => 'data-for="zoomControlOptions" class="umsMapPosChangeSelect '. $this->addProElementClass. '"'. (empty($this->addProElementAttrs) ? '' : sprintf($this->addProElementAttrs, $proLink, $proLink))
							))?>
							<?php if(!$this->isPro) { ?>
								<span class="umsProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
							<?php }?>
						</div>
					</div>
					<div id="umsDefaultZoomDisable" style="display: none;" title="<?php _e('Notice', UMS_LANG_CODE)?>">
						<p>
							<?php printf(__('Standard Zoom control will not displaying for this map, because the Custom Map Controls option enabled now.', UMS_LANG_CODE))?>
						</p>
					</div>
				</td>
			</tr><?php */?>
			<tr>
				<td colspan="2" style="padding: 0;">
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="map_optsdraggable_check">
									<?php _e('Draggable', UMS_LANG_CODE)?>:
								</label>
								<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Enable or disable possibility to drag your map using mouse', UMS_LANG_CODE)?>"></i>
							</th>
							<td>
								<?php echo htmlUms::selectbox('map_opts[draggable]', array(
									'options' => array(
										'enable' => 'Desktop & Mobile',
										'desktop' => 'Only Desktop',
										'mobile' => 'Only Mobile',
										'disable' => 'Disabled for Everyone',
									),
									'value' => $this->editMap && isset($this->map['params']['draggable']) ? $this->map['params']['draggable'] : 'enable',
									'attrs' => 'style="width: 100%;" id="draggable"'))?>

								<?php
								// echo htmlUms::checkboxHiddenVal('map_opts[draggable]', array(
								// 	'value' => $this->editMap && isset($this->map['params']['draggable']) ? $this->map['params']['draggable'] : true,
								// ))
								?>
							</td>
							<th scope="row">
								<label for="map_optsmouse_wheel_zoom_check">
									<?php _e('Mouse wheel to zoom', UMS_LANG_CODE)?>:
								</label>
								<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sometimes you need to disable possibility to zoom your map using mouse wheel. This can be required for example - if you need to use your wheel for some other action, for example scroll your site even if mouse is over your map.', UMS_LANG_CODE)?>"></i>
							</th>
							<td>
								<?php echo htmlUms::checkboxHiddenVal('map_opts[mouse_wheel_zoom]', array(
									'value' => $this->editMap && isset($this->map['params']['mouse_wheel_zoom']) ? $this->map['params']['mouse_wheel_zoom'] : true,
								))?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="map_optsdbl_click_zoom_check">
									<?php _e('Double click to zoom', UMS_LANG_CODE)?>:
								</label>
								<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('By default double left click on map will zoom it in. But you can change this here.', UMS_LANG_CODE)?>"></i>
							</th>
							<td>
								<?php echo htmlUms::checkboxHiddenVal('map_opts[dbl_click_zoom]', array(
									'value' => $this->editMap && isset($this->map['params']['dbl_click_zoom']) ? $this->map['params']['dbl_click_zoom'] : true,
								))?>
							</td>
							<th scope="row">
								<?php /*?><label for="map_optsis_static_check">
									<?php _e('Set Static', UMS_LANG_CODE)?>:
								</label>
								<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Show map as a Static image. This will allow you to make it cheeper according to new Google Maps API usage Rates. Be aware - not all options will work in this mode!', UMS_LANG_CODE)?>"></i>
								<?php */ ?>
							</th>
							<td>
								<?php /*?>
								<?php echo htmlUms::checkboxHiddenVal('map_opts[is_static]', array(
									'value' => $this->editMap && isset($this->map['params']['is_static']) ? $this->map['params']['is_static'] : false,
									'attrs' => 'class="umsProOpt"',
								))?>
								<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=static_map&utm_campaign=ultimatemaps'); ?>
								<?php if(!$this->isPro) { ?>
									<span class="umsProOptMiniLabel" style="padding-left: 20px;"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
								<?php }?>
								<?php */ ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_map_center_address" class="sup-medium-label">
						<?php _e('Map Center', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sets map center. You can set map center in next ways: type address to use its coords, type the coords\' values in appropriate fields or just drag the map on preview.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div>
						<label for="map_opts_map_center_address">
							<?php _e('Address', UMS_LANG_CODE)?>
						</label>
						<?php echo htmlUms::text('map_opts[map_center][address]', array(
							'value' => $this->editMap && isset($this->map['params']['map_center']['address']) ? $this->map['params']['map_center']['address'] : '',
							'placeholder' => '603 Park Avenue, Brooklyn, NY 11206, USA',
							'attrs' => 'style="width: 100%;" id="map_opts_map_center_address"'))?>
					</div>
					<div class="sup-col sup-w-50" style="margin-top: 10px;">
						<label for="map_opts_map_center_coord_x">
							<?php _e('Latitude', UMS_LANG_CODE)?>
						</label>
						<?php echo htmlUms::text('map_opts[map_center][coord_x]', array(
							'value' => $this->editMap ? $this->map['params']['map_center']['coord_x'] : '',
							'attrs' => 'style="width: 100%;" id="map_opts_map_center_coord_x"'))?>
					</div>
					<div class="sup-col sup-w-50" style="margin-top: 10px;">
						<label for="map_opts_map_center_coord_y">
							<?php _e('Longitude', UMS_LANG_CODE)?>
						</label>
						<?php echo htmlUms::text('map_opts[map_center][coord_y]', array(
							'value' => $this->editMap ? $this->map['params']['map_center']['coord_y'] : '',
							'attrs' => 'style="width: 100%;" id="map_opts_map_center_coord_y"'))?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_zoom_type" class="sup-medium-label">
						<?php _e('Map Zoom', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Sets map zoom.<br /><br />'
					//. '<b>Preset Zoom</b> - sets zoom value for map. You can change this value just change zoom on the map preview.<br /><br />'
					//. '<b>Fit Bounds</b> - map zoom will be changed on frontend in a way that all markers and figures will be visible.<br /><br />'
					. '<b>Min Zoom Level</b> - sets minimum zoom level (maximum estrangement), which can be applied for map.<br /><br />'
					. '<b>Max Zoom Level</b> - sets maximum zoom level (maximum approximation), which can be applied for map.
					', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php
						$zoomMin = 1;
						$zoomMax = 21;
						$zoomRange = array_combine(range($zoomMin, $zoomMax), range($zoomMin, $zoomMax));
					?>
					<?php /* echo htmlUms::selectbox('map_opts[zoom_type]', array(
						'options' => array('zoom_level' => __('Preset Zoom', UMS_LANG_CODE), 'fit_bounds' => __('Fit Bounds', UMS_LANG_CODE)),
						'value' => $this->editMap && isset($this->map['params']['zoom_type']) ? $this->map['params']['zoom_type'] : 'zoom_level',
						'attrs' => 'style="width: 100%;"'))*/ ?>
					<div id="zoom_type_options">
						<div style="clear: both;">
							<div class="zoom_level sup-col" style="width: 100%;">
								<label for="map_opts_zoom">
									<?php _e('Zoom Level', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::selectbox('map_opts[zoom]', array(
									'options' => $zoomRange,
									'value' => $this->editMap && isset($this->map['params']['zoom']) ? $this->map['params']['zoom'] : 8,
									'attrs' => 'style="width: 100%;"'))?>
								<?php //echo htmlUms::hidden('map_opts[zoom]', array('value' => $this->editMap ? $this->map['params']['zoom'] : ''))?>
							</div>
							<?php /*Don't use it for now*/ ?>
							<div class="zoom_level sup-col sup-w-50" style="display: none;">
								<label for="map_opts_zoom_mobile">
									<?php _e('Mobile Zoom Level', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::selectbox('map_opts[zoom_mobile]', array(
									'options' => $zoomRange,
									'value' => $this->editMap && isset($this->map['params']['zoom_mobile']) ? $this->map['params']['zoom_mobile'] : 8,
									'attrs' => 'style="width: 100%;"'))?>
							</div>
						</div>
						<div style="clear: both;">
							<div class="zoom_min_level sup-col sup-w-50">
								<label for="map_opts_zoom_min">
									<?php _e('Min Zoom Level', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::selectbox('map_opts[zoom_min]', array(
									'options' => $zoomRange,
									'value' => $this->editMap && isset($this->map['params']['zoom_min']) ? $this->map['params']['zoom_min'] : $zoomMin,
									'attrs' => 'style="width: 100%;"'))?>
							</div>
							<div class="zoom_max_level sup-col sup-w-50">
								<label for="map_opts_zoom_max">
									<?php _e('Max Zoom Level', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::selectbox('map_opts[zoom_max]', array(
									'options' => $zoomRange,
									'value' => $this->editMap && isset($this->map['params']['zoom_max']) ? $this->map['params']['zoom_max'] : $zoomMax,
									'attrs' => 'style="width: 100%;"'))?>
							</div>
						</div>
						<div id="umsZoomLelvelsError" class="umsErrorMsg" style="clear: both; display: none; width: 100%;">
							<?php _e('Min Zoom Level should be less then Max Zoom Level')?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_map_type">
						<?php _e('Map Type', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can select your Map Theme here.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php $mapTypeKeys = array_keys($this->engineOpts['map_type']); ?>
					<?php $defMapType = array_shift($mapTypeKeys); ?>
					<?php echo htmlUms::selectbox('map_opts[map_type]', array(
						'options' => $this->engineOpts['map_type'], //array('ROADMAP' => __('Road Map', UMS_LANG_CODE), 'HYBRID' => __('Hybrid', UMS_LANG_CODE), 'SATELLITE' => __('Satellite', UMS_LANG_CODE), 'TERRAIN' => __('Terrain', UMS_LANG_CODE)),
						'value' => $this->editMap && isset($this->map['params']['map_type'])
							? $this->map['params']['map_type']
							: $defMapType,
						'attrs' => 'style="width: 100%;" id="map_opts_map_type"'))?>
				</td>
			</tr>
			<?php if($this->engineOpts['map_stylization']) { ?>
			<tr>
				<th scope="row">
					<label for="map_opts_map_stylization">
						<?php _e('Map Stylization', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Make your map unique with our Map Themes, just try to change it here - and you will see results on your Map Preview.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::selectbox('map_opts[map_stylization]', array(
						'options' => $this->stylizationsForSelect,
						'value' => $this->editMap && isset($this->map['params']['map_stylization']) ? $this->map['params']['map_stylization'] : 'none',
						'attrs' => 'style="width: '. ($this->isPro ? '100%' : 'calc(100% - 200px)'). ';" id="map_opts_map_stylization"'))?>
					<?php if(!$this->isPro) {?>
						<a target="_blank" href="<?php echo $this->mainLink;?>" class="sup-standard-link">
							<i class="fa fa-plus"></i>
							<?php _e('Get 300+ Themes with PRO', UMS_LANG_CODE)?>
						</a>
					<?php }?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<th scope="row">
					<label for="map_optsdbl_click_zoom_check">
						<?php _e('Show marker description by hover', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Show marker description by hover. (Desktop)', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[marker_hover]', array(
						'value' => $this->editMap && isset($this->map['params']['marker_hover']) ? $this->map['params']['marker_hover'] : true,
					))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_clasterer" class="sup-medium-label">
						<?php _e('Markers Clusterization', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('If you have many markers - you can have a problems with viewing them when zoom out for example: they will just cover each-other. Marker clusterization can solve this problem by grouping your markers in groups when they are too close to each-other.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::selectbox('map_opts[marker_clasterer]', array(
						'options' => array('none' => __('None', UMS_LANG_CODE), 'MarkerClusterer' => __('Base Clusterization', UMS_LANG_CODE)),
						'value' => $this->editMap && isset($this->map['params']['marker_clasterer']) ? $this->map['params']['marker_clasterer'] : 'none',
						'attrs' => 'style="width: 100%;" id="map_opts_marker_clasterer"'));

					// Prevent to use old default claster icon cdn icon because it is missing
					$oldDefClasterIcon = '';
					$curClusterIcon = uriUms::_(
						$this->editMap
						&& isset($this->map['params']['marker_clasterer_icon'])
						&& $this->map['params']['marker_clasterer_icon']
						&& $this->map['params']['marker_clasterer_icon'] != $oldDefClasterIcon
							? $this->map['params']['marker_clasterer_icon']
							: UMS_MODULES_PATH . '/maps/img/m1.png');
					$curClusterIconWidth =
						$this->editMap
						&& isset($this->map['params']['marker_clasterer_icon_width'])
						&& $this->map['params']['marker_clasterer_icon_width']
							? $this->map['params']['marker_clasterer_icon_width']
							: 53;
					$curClusterIconHeight =
						$this->editMap
						&& isset($this->map['params']['marker_clasterer_icon_height'])
						&& $this->map['params']['marker_clasterer_icon_height']
							? $this->map['params']['marker_clasterer_icon_height']
							: 52;
					?>
					<div id="umsMarkerClastererSubOpts" style="display: none;">
						<?php /*?><div class="umsClastererSubOpts">
							<div class="sup-col" style="max-width: 50%; min-width: 20%; float: right; padding: 0; text-align: center;">
								<a id="umsUploadClastererIconBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Upload Icon', UMS_LANG_CODE)?></a><br />
								<a id="umsDefaultClastererIconBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Default Icon', UMS_LANG_CODE)?></a>
								<div class="umsClastererUplRes"></div>
							</div>
							<label for="map_opts_marker_clasterer_icon">
								<?php _e('Cluster Icon', UMS_LANG_CODE)?>
							</label><br />
							<img id="umsMarkerClastererIconPrevImg" src="<?php echo $curClusterIcon?>" style="max-width: 53px; height: auto; margin: 5px 0;" />
							<?php echo htmlUms::hidden('map_opts[marker_clasterer_icon]', array('value' => $curClusterIcon, ))?>
							<?php echo htmlUms::hidden('map_opts[marker_clasterer_icon_width]', array('value' => $curClusterIconWidth, ))?>
							<?php echo htmlUms::hidden('map_opts[marker_clasterer_icon_height]', array('value' => $curClusterIconHeight, ))?>
							<div style="clear: both;"></div>
						</div><?php */?>
						<div class="umsClastererSubOpts">
							<label for="map_opts_marker_clasterer_grid_size">
								<?php _e('Cluster Area Size', UMS_LANG_CODE)?>
							</label>
							<i class="fa fa-question supsystic-tooltip" title="<?php _e('Sets the grid size of cluster. The higher the size - the more area of capture the markers to the cluster.', UMS_LANG_CODE)?>"></i>
							<br />
							<div class="sup-col sup-w-75">
								<?php echo htmlUms::text('map_opts[marker_clasterer_grid_size]', array(
									'value' => $this->editMap && isset($this->map['params']['marker_clasterer_grid_size']) ? $this->map['params']['marker_clasterer_grid_size'] : '60',
									'attrs' => 'style="width: 100%;" id="umsMarkerClastererGridSize" '))?>
							</div>
							<div class="sup-col" style="max-width: 50%; min-width: 20%; float: right; padding: 0; text-align: center;">
								<a id="umsDefaultClastererGridSizeBtn" href="#" class="button" style="width: 100%; margin-bottom: 5px;"><?php _e('Default', UMS_LANG_CODE)?></a>
							</div>
							<div style="margin-top: 10px;">
								<label for="map_opts_marker_clasterer_background_color">
									<?php _e('Marker Clasterer Background Color', UMS_LANG_CODE)?>
								</label><i class="fa fa-question supsystic-tooltip" title="<?php _e('Works with Leaflet, MapBox, Thunderforest, Bing.', UMS_LANG_CODE)?>"></i></br>
								<?php echo htmlUms::colorpicker('map_opts[marker_clasterer_background_color]', array(
									'value' => $this->editMap && isset($this->map['params']['marker_clasterer_background_color']) ? $this->map['params']['marker_clasterer_background_color'] : '#2196f3'))?>
							</div>
							<div style="margin-top: 10px;">
								<label for="map_opts_marker_clasterer_border_color">
									<?php _e('Marker Clasterer Border Color', UMS_LANG_CODE)?>
								</label><i class="fa fa-question supsystic-tooltip" title="<?php _e('Works with Leaflet, MapBox, Thunderforest.', UMS_LANG_CODE)?>"></i></br>
								<?php echo htmlUms::colorpicker('map_opts[marker_clasterer_border_color]', array(
									'value' => $this->editMap && isset($this->map['params']['marker_clasterer_border_color']) ? $this->map['params']['marker_clasterer_border_color'] : '#1c7ba7'))?>
							</div>
							<div style="margin-top: 10px;">
								<label for="map_opts_marker_clasterer_text_color">
									<?php _e('Marker Clasterer Text Color', UMS_LANG_CODE)?>
								</label><i class="fa fa-question supsystic-tooltip" title="<?php _e('Works with Leaflet, MapBox, Thunderforest.', UMS_LANG_CODE)?>"></i></br>
								<?php echo htmlUms::colorpicker('map_opts[marker_clasterer_text_color]', array(
									'value' => $this->editMap && isset($this->map['params']['marker_clasterer_text_color']) ? $this->map['params']['marker_clasterer_text_color'] : 'white'))?>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<?php //TODO: Implement those PRO options ?>
			<tr>
				<th scope="row">
					<label for="map_opts_markers_list_type">
						<?php _e('Markers List', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Display all map markers - as list bellow Your map. This will help your users get more info about your markers and find required marker more faster.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=markers_list&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
						<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_marker_list_type]', array(
							'value' => $this->editMap && isset($this->map['params']['enable_marker_list_type']) ? $this->map['params']['enable_marker_list_type'] : false,
						))?>
						<?php _e('Enable markers list', UMS_LANG_CODE)?>

					<div id="umsMapMarkersListSettings" style="display: none;">
						<?php if($this->isPro) {?>
							<div style="margin-top:15px;">
								<a id="umsMapMarkersListBtn" href="#" class="button"><?php _e('Select Markers List type', UMS_LANG_CODE)?></a>
								<?php echo htmlUms::hidden('map_opts[markers_list_type]', array(
										'value' => $this->editMap && isset($this->map['params']['markers_list_type']) ? $this->map['params']['markers_list_type'] : ''))?>
							</div>

                     <div style="margin-top:15px;" class="slider_simple_table_show">
                        <label for="map_opts_slider_simple_table_width_address">
   								<?php _e('Width dimension', UMS_LANG_CODE)?>
   							</label>
                        </br>
                        <?php echo htmlUms::selectbox('map_opts[slider_simple_table_width_dimension]', array(
                           'options' => array('px' => __('px', UMS_LANG_CODE), '%' => __('%', UMS_LANG_CODE)),
                           'value' => $this->editMap && isset($this->map['params']['slider_simple_table_width_dimension']) ? $this->map['params']['slider_simple_table_width_dimension'] : 'px',
                           'attrs' => 'style="width: 100%;" id="map_opts_slider_simple_table_width_dimension"'));
                        ?>
                        </br>
                        <label for="map_opts_slider_simple_table_width_address">
   								<?php _e('Title Column Width', UMS_LANG_CODE)?>
   							</label>
                        </br>
                        <?php echo htmlUms::text('map_opts[slider_simple_table_width_title]', array(
                           'value' => $this->editMap && isset($this->map['params']['slider_simple_table_width_title']) ? $this->map['params']['slider_simple_table_width_title'] : '',
                           'attrs' => 'style="width: 100%;" '))
                        ?>
                        <label for="map_opts_slider_simple_table_width_address">
      						   <?php _e('Address Column Width', UMS_LANG_CODE)?>
      						</label>
                        </br>
                        <?php echo htmlUms::text('map_opts[slider_simple_table_width_address]', array(
                           'value' => $this->editMap && isset($this->map['params']['slider_simple_table_width_address']) ? $this->map['params']['slider_simple_table_width_address'] : '',
                           'attrs' => 'style="width: 100%;" '))
                        ?>
                        <label for="map_opts_slider_simple_table_width_address">
      						   <?php _e('Description Column Width', UMS_LANG_CODE)?>
      						</label>
                        </br>
                        <?php echo htmlUms::text('map_opts[slider_simple_table_width_description]', array(
                           'value' => $this->editMap && isset($this->map['params']['slider_simple_table_width_description']) ? $this->map['params']['slider_simple_table_width_description'] : '',
                           'attrs' => 'style="width: 100%;" '))
                        ?>
                        <label for="map_opts_slider_simple_table_width_address">
      						   <?php _e('Get Direction Column Width', UMS_LANG_CODE)?>
      						</label>
                        </br>
                        <?php echo htmlUms::text('map_opts[slider_simple_table_width_getdirection]', array(
                           'value' => $this->editMap && isset($this->map['params']['slider_simple_table_width_getdirection']) ? $this->map['params']['slider_simple_table_width_getdirection'] : '',
                           'attrs' => 'style="width: 100%;" '))
                        ?>
                     </div>

							<div style="margin-top:15px;">
								<?php echo htmlUms::checkboxHiddenVal('map_opts[hide_empty_block]', array(
									'value' => $this->editMap && isset($this->map['params']['hide_empty_block']) ? $this->map['params']['hide_empty_block'] : false,
								))?>
								<?php _e('Hide blocks without image', UMS_LANG_CODE)?>
							</div>
							<div style="margin-top:15px;">
								<?php echo htmlUms::checkboxHiddenVal('map_opts[autoplay_slider]', array(
									'value' => $this->editMap && isset($this->map['params']['autoplay_slider']) ? $this->map['params']['autoplay_slider'] : false,
								))?>
								<?php _e('Enable autoplay slider and set', UMS_LANG_CODE)?>
								<?php echo htmlUms::text('map_opts[slide_duration]', array(
									'value' => $this->editMap && isset($this->map['params']['slide_duration']) ? $this->map['params']['slide_duration'] : false,
									'placeholder' => '500',
									'attrs' => 'style="width:50px"',
								))?>
								<?php _e('slide duration in (ms)', UMS_LANG_CODE)?>
							</div>
						<?php } ?>
						<div style="margin-top: 10px;">
							<label for="map_opts_markers_list_color">
								<?php _e('Markers List Color', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::colorpicker('map_opts[markers_list_color]', array(
								'value' => $this->editMap && isset($this->map['params']['markers_list_color']) ? $this->map['params']['markers_list_color'] : '#55BA68'))?>
						</div>
					</div>
				</td>
			</tr>
			<?php /*?>
			<tr>
				<th scope="row">
					<label for="map_opts_enable_trafic_layer">
						<?php _e('Traffic Layer', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add real-time traffic information to your map.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=trafic_layer&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_trafic_layer]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_trafic_layer']) ? $this->map['params']['enable_trafic_layer'] : false,
						'attrs' => 'class="umsProOpt"'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_enable_transit_layer">
						<?php _e('Transit Layer', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Display the public transit network of a city on your map. When the Transit Layer is enabled, and the map is centered on a city that supports transit information, the map will display major transit lines as thick, colored lines.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=transit_layer&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_transit_layer]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_transit_layer']) ? $this->map['params']['enable_transit_layer'] : false,
						'attrs' => 'class="umsProOpt"'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_enable_bicycling_layer">
						<?php _e('Bicycling Layer', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add a layer of bike paths, suggested bike routes and other overlays specific to bicycling usage on top of the given map.Dark green routes indicated dedicated bicycle routes. Light green routes indicate streets with dedicated bike lanes. Dashed routes indicate streets or paths otherwise recommended for bicycle usage.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=bicycling_layer&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_bicycling_layer]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_bicycling_layer']) ? $this->map['params']['enable_bicycling_layer'] : false,
						'attrs' => 'class="umsProOpt"'))?>
				</td>
			</tr>
<?php */ ?>
			<tr>
				<th scope="row">
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=add_kml_layers&utm_campaign=ultimatemaps'); ?>
					<?php }?>
					<label for="map_opts_add_kml_layers">
						<?php _e('Add KML layers', UMS_LANG_CODE)?>:
					</label>
					<i class="fa fa-question supsystic-tooltip" style="float: right;" title="<?php _e('Add KML files to display custom layers on the map.', UMS_LANG_CODE);
						if(!$this->isPro){
							echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/kml/kml.png" /></a>');
						}?>"
					></i>
					<?php if(!$this->isPro) { ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php /* ?>
					<div>
						<label for="map_opts_enable_kml_filter">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_kml_filter]', array(
								'value' => $this->editMap && isset($this->map['params']['enable_kml_filter']) ? $this->map['params']['enable_kml_filter'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_enable_kml_filter"'))?>
							<?php _e('Enable KML layers filter', UMS_LANG_CODE)?>
						</label>
					</div><?php */ ?>
					<div id="umsKmlFileRowExample" class="umsKmlFileRow" style="display: none;">
						<div style="clear: both;"></div>
						<label><?php _e('Enter KML file URL', UMS_LANG_CODE)?></label>
						<?php /* ?><label class="umsShowSublayersLabel" style="float: right;">
							<?php echo htmlUms::hidden('map_opts[kml_filter][show_sublayers][]', array('value' => '', 'attrs' => 'class="umsShowSublayersInput umsProOpt" disabled="disabled"'))?>
							<?php _e('Hide Sublayers at KML filter', UMS_LANG_CODE)?>
						</label><?php */ ?>
						<div style="clear: both;"></div>
						<a href="#" title="<?php _e('Remove KML field', UMS_LANG_CODE)?>" class="button umsProOpt" onclick="umsKmlRemoveFileRowBtnClick(this); return false;">
							<i class="fa fa-trash-o"></i>
						</a>
						<?php echo htmlUms::text('map_opts[kml_file_url][]', array('value' => '', 'attrs' => 'class="umsProOpt" style="width: 86%; float: right;" disabled="disabled"'))?>
						<span class="umsKmlUploadMsg" style="float: right; width: 100%; text-align: right;" ></span>
						<a 	href="#"
							class="umsKmlUploadFileBtn button umsProOpt"
							data-nonce="<?php echo wp_create_nonce('upload-kml-file')?>"
							data-url="<?php echo uriUms::_(array(
								'baseUrl' => admin_url('admin-ajax.php'),
								'page' => 'kml',
								'action' => 'addFromFile',
								'reqType' => 'ajax',
								'pl' => UMS_CODE))?>"
							id="umsKmlUploadFileBtn"
							style="margin: 5px 0px; float: right;">
							<?php _e('or Upload KML file', UMS_LANG_CODE)?>
						</a><br />
						<?php /* ?><label class="umsKmlImportToMarkerLbl">
							<span class="umsKitmLblText"><?php _e('Import markers from layer', UMS_LANG_CODE); ?></span>
						</label><?php */ ?>
					</div>
					<?php
						if(!empty($this->map['params']['kml_import_to_marker'])
							&& count($this->map['params']['kml_import_to_marker'])
						) {
							foreach($this->map['params']['kml_import_to_marker'] as $omKey => $oneMarker) {
								$isKmlImpToMarkerVal = 0;
								if($oneMarker == 'on') {
									$isKmlImpToMarkerVal = 1;
								}
								echo htmlUms::hidden('map_opts[kml_import_to_marker][]', array(
									'value' => $isKmlImpToMarkerVal,
									'attrs' => ' class="umsProOpt umsKmlImportToMarkerHid" data-order="' . $omKey . '" ',
								));
							}
						}
					?>
					<div id="umsKmlFileRowsShell"></div>
					<a href="#" class="button umsProOpt" id="umsKmlAddFileRowBtn" style="margin: 5px 5px 5px 0px; float: left;">
						<?php _e('Add more files', UMS_LANG_CODE)?>
					</a>
				</td>
			</tr>
            <?php if(isset($this->engineOpts['hide_poi']) && $this->engineOpts['hide_poi']) { ?>
            <tr>
                <th scope="row">
                    <label for="map_opts_hide_poi">
                        <?php _e('Hide POI', UMS_LANG_CODE)?>:
                    </label>
                    <i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide the Points Of Interest - landmark or other object, the marked points on the map (only for Aerial and Birdseye types), for example: hotels, campsites, fuel stations etc.', UMS_LANG_CODE)?>"></i>
                    <?php if(!$this->isPro) { ?>
                        <?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=hide_poi&utm_campaign=ultimatemaps'); ?>
                        <br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
                    <?php }?>
                </th>
                <td>
                    <?php echo htmlUms::checkboxHiddenVal('map_opts[hide_poi]', array(
                        'value' => $this->editMap && isset($this->map['params']['hide_poi']) ? $this->map['params']['hide_poi'] : false,
                        'attrs' => 'class="umsProOpt"'))?>
                </td>
            </tr>
			<?php } ?>
            <tr>
                <th scope="row">
                    <label for="map_opts_frontend_add_markers">
                        <?php _e('Add markers on frontend', UMS_LANG_CODE)?>:
                    </label>
                    <i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e("You can add markers at the current map with the frontend using the form, which can be displayed using the shortcode (it placed below preview map). Additional options that affect the operation of the form:" .
                        "<br /><br /><b>Logged In Users Only</b> - form will be displayed only for logged in users." .
                        "<br /><br /><b>Disable WP Editor</b> - disable / enable WP Editor for the Marker Description field of the form." .
                        "<br /><br /><b>Delete markers</b> - disable / enable interface for deleting markers on frontend. Each user can delete only his own markers." .
                        "<br /><br /><b>Use markers categories</b> - disable / enable interface for choose the marker category on frontend." .
                        "<br /><br /><b>Use limits for marker's adding</b> - allows you to limit the number of markers, which user can add from one IP address at the current map for a certain amount of time." .
                        "<br /><br /><b>Max marker's count</b> - the maximum number of markers, which can be added over certain amount of time." .
                        "<br /><br /><b>For allotted time (minutes)</b> - the number of minutes, during which you can add the maximum number of markers." .
                        "<br /><br />For example, during three minutes you can add only two markers at the map. If you try to add a third marker - the form will not be saved and you will see the notice with amount of time you must wait. After the right amount of time will pass - you can add next two markers, etc." .
                        "<br /><br />Important! If map and form for add markers at this map are placed on one page - this page will be overload after marker adding.", UMS_LANG_CODE)?>"></i>
                    <?php if(!$this->isPro) { ?>
                        <?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=frontend_add_markers&utm_campaign=ultimatemaps'); ?>
                        <br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
                    <?php }?>
                </th>
                <td>
                    <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers]', array(
                        'value' => $this->editMap && isset($this->map['params']['frontend_add_markers']) ? $this->map['params']['frontend_add_markers'] : false,
                        'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers"'
                    ))?>
                    <div id="umsAddMarkersOnFrontendOptions" style="display: none;">
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::text('umsCopyTextCode', array(
                                'value' => '',	// Will be inserted from JS
                                'attrs' => 'class="umsCopyTextCode umsMapMarkerFormCodeShell umsStaticWidth" style="width: 100%; text-align: center;"'));?>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_logged_in_only]', array(
                                'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_logged_in_only']) ? $this->map['params']['frontend_add_markers_logged_in_only'] : false,
                                'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_logged_in_only"'
                            ))?>
                            <label for="map_opts_frontend_add_markers_logged_in_only"><?php _e('Logged In Users Only', UMS_LANG_CODE)?></label>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_disable_wp_editor]', array(
                                'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_disable_wp_editor']) ? $this->map['params']['frontend_add_markers_disable_wp_editor'] : false,
                                'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_disable_wp_editor"'
                            ))?>
                            <label for="map_opts_frontend_add_markers_disable_wp_editor"><?php _e('Disable WP Editor', UMS_LANG_CODE)?></label>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_delete_markers]', array(
                                'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_delete_markers']) ? $this->map['params']['frontend_add_markers_delete_markers'] : false,
                                'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_delete_markers"'
                            ))?>
                            <label for="map_opts_frontend_add_markers_delete_markers"><?php _e('Delete markers', UMS_LANG_CODE)?></label>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_use_markers_categories]', array(
                                'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_markers_categories']) ? $this->map['params']['frontend_add_markers_use_markers_categories'] : false,
                                'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_use_markers_categories"'
                            ))?>
                            <label for="map_opts_frontend_add_markers_use_markers_categories"><?php _e('Use markers categories', UMS_LANG_CODE)?></label>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_use_limits]', array(
                                'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_limits']) ? $this->map['params']['frontend_add_markers_use_limits'] : false,
                                'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_use_limits"'
                            ))?>
                            <label for="map_opts_frontend_add_markers_use_limits"><?php _e('Use limits for marker\'s adding', UMS_LANG_CODE)?></label>
                        </div>
                        <div id="umsUseLimitsForMarkerAddingOptions" style="display: none; margin-top: 10px;">
                            <div class="sup-col sup-w-50">
                                <label for="map_opts_frontend_add_markers_use_count_limits">
                                    <?php _e('Max marker\'s count', UMS_LANG_CODE)?>
                                </label>
                                <?php echo htmlUms::text('map_opts[frontend_add_markers_use_count_limits]', array(
                                    'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_count_limits']) ? $this->map['params']['frontend_add_markers_use_count_limits'] : '10',
                                    'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_count_limits"'))?>
                            </div>
                            <div class="sup-col sup-w-50">
                                <label for="map_opts_frontend_add_markers_use_time_limits">
                                    <?php _e('For allotted time (minutes)', UMS_LANG_CODE)?>
                                </label>
                                <?php echo htmlUms::text('map_opts[frontend_add_markers_use_time_limits]', array(
                                    'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_time_limits']) ? $this->map['params']['frontend_add_markers_use_time_limits'] : '10',
                                    'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_time_limits"'))?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
			<?php /*
			<tr>
				<th scope="row">
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_custom_map_controls&utm_campaign=ultimatemaps'); ?>
					<?php }?>
					<label for="map_opts_enable_custom_map_controls">
						<?php _e('Custom Map Controls', UMS_LANG_CODE)?>:
					</label>
					<i
						style="float: right;"
						class="fa fa-question supsystic-tooltip"
						title="<?php _e('Add custom map controls to the map.', UMS_LANG_CODE);
							if(!$this->isPro){
								echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/custom_controls/custom_map_controls.png" /></a>');
							}?>"
					></i>
					<?php if(!$this->isPro) { ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_custom_map_controls]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_custom_map_controls']) ? $this->map['params']['enable_custom_map_controls'] : false,
						'attrs' => 'class="umsProOpt" onclick="umsAddCustomControlsOptions()"'))?>
					<div id="custom_controls_options" style="display: none;">
						<div style="margin-top: 10px;">
						<label for="map_opts_custom_controls_type">
							<?php _e('Controls type', UMS_LANG_CODE)?>
						</label>
						<?php echo htmlUms::selectbox('map_opts[custom_controls_type]', array(
							'options' => array('umsSquareControls' => __('Square', UMS_LANG_CODE), 'umsRoundedEdgesControls' => __('Rounded edges', UMS_LANG_CODE), 'umsRoundControls' => __('Round', UMS_LANG_CODE)),
							'value' => $this->editMap && isset($this->map['params']['custom_controls_type']) ? $this->map['params']['custom_controls_type'] : 'round',
							'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_type"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_bg_color">
								<?php _e('Background color', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::colorpicker('map_opts[custom_controls_bg_color]', array(
								'attrs' => 'class="umsProOpt"',
								'value' => $this->editMap && isset($this->map['params']['custom_controls_bg_color']) ? $this->map['params']['custom_controls_bg_color'] : '#55BA68'))?>
						</div>
						<div style="margin-top: 10px;">
						<label for="map_opts_custom_controls_txt_color">
							<?php _e('Text color', UMS_LANG_CODE)?>
						</label></br>
						<?php echo htmlUms::colorpicker('map_opts[custom_controls_txt_color]', array(
							'attrs' => 'class="umsProOpt"',
							'value' => $this->editMap && isset($this->map['params']['custom_controls_txt_color']) ? $this->map['params']['custom_controls_txt_color'] : '#000000'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_position">
								<?php _e('Controls position', UMS_LANG_CODE)?>
							</label>
							<?php echo htmlUms::selectbox('map_opts[custom_controls_position]', array(
								'options' => $this->positionsList,
								'value' => $this->editMap && isset($this->map['params']['custom_controls_position']) ? $this->map['params']['custom_controls_position'] : 'TOP_LEFT',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_position"'
							))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_slider_min">
								<?php _e('Min Search Radius (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[custom_controls_slider_min]', array(
								'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_min']) ? $this->map['params']['custom_controls_slider_min'] : '100',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_min"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_slider_max">
								<?php _e('Max Search Radius (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[custom_controls_slider_max]', array(
								'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_max']) ? $this->map['params']['custom_controls_slider_max'] : '1000',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_max"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_slider_step">
								<?php _e('Search Step (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[custom_controls_slider_step]', array(
								'value' => $this->editMap && isset($this->map['params']['custom_controls_slider_step']) ? $this->map['params']['custom_controls_slider_step'] : '10',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_slider_step"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_custom_controls_search_country">
								<?php _e('Search Country', UMS_LANG_CODE)?>
							</label>
							<?php echo htmlUms::selectbox('map_opts[custom_controls_search_country]', array(
								'options' => array_merge(array('' => 'All Countries'), $this->countries),
								'value' => $this->editMap && isset($this->map['params']['custom_controls_search_country']) ? $this->map['params']['custom_controls_search_country'] : 'round',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_custom_controls_search_country"'))?>
						</div>
						<?php if($this->isCustSearchAndMarkersPeriodAvailable) { ?>
						<div style="margin-top: 10px;">
							<label>
								<?php
									$isCustomSearchParamArr = array();
									if(!empty($this->map['params']['custom_controls_improve_search'])
										&& $this->map['params']['custom_controls_improve_search'] == 1
									) {
										$isCustomSearchParamArr = array('checked' => 'checked');
									}
								echo htmlUms::checkbox('map_opts[custom_controls_improve_search]', $isCustomSearchParamArr)?>
								<?php _e('Use improved markers search', UMS_LANG_CODE);?>
							</label>
							<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('This option allows you to search and show multiple markers for selected date, categories and keywords. NOTE: it removes separate markers categories filter button from custom map controls.', UMS_LANG_CODE); ?>"></i>
						</div>
						<?php }?>
						<div style="margin-top: 10px;">
							<label>
								<?php
								$isFilterEnable = array();
								if(!empty($this->map['params']['button_filter_enable'])
									&& $this->map['params']['button_filter_enable'] == 1
								) {
									$isFilterEnable = array('checked' => 'checked');
								}
								echo htmlUms::checkbox('map_opts[button_filter_enable]', $isFilterEnable)?>
								<?php _e('Disable filter button', UMS_LANG_CODE);?>
							</label>
							<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Check this option if you want to disable filters button on frontend', UMS_LANG_CODE); ?>"></i>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_enable_full_screen_btn">
						<?php _e('Full Screen Button', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add a button on map to open it full screen.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_full_screen_btn&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_full_screen_btn]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_full_screen_btn']) ? $this->map['params']['enable_full_screen_btn'] : false,
						'attrs' => 'class="umsProOpt"'))?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="map_opts_hide_countries">
						<?php _e('Hide Countries', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide all administrative data about countries: names, borders etc.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=hide_countries&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[hide_countries]', array(
						'value' => $this->editMap && isset($this->map['params']['hide_countries']) ? $this->map['params']['hide_countries'] : false,
						'attrs' => 'class="umsProOpt"'))?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="map_opts_hide_marker icon title">
						<?php _e('Hide Tooltips of Markers', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide the tooltips, which displayed by mouse hover on markers\' icons.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[hide_marker_tooltip]', array(
						'value' => $this->editMap && isset($this->map['params']['hide_marker_tooltip']) ? $this->map['params']['hide_marker_tooltip'] : false,
					))?>
				</td>
			</tr>


			<tr>
				<th scope="row">
					<label for="map_opts_center_on_cur_marker_infownd">
						<?php _e('Center on current opened marker', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('On frontend the map will be centered on current marker with opened info window.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[center_on_cur_marker_infownd]', array(
						'value' => $this->editMap && isset($this->map['params']['center_on_cur_marker_infownd']) ? $this->map['params']['center_on_cur_marker_infownd'] : false,
						'attrs' => ''))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_center_on_cur_user_pos">
						<?php _e('Center on current user location', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('On frontend map will be centered on current user location.', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=center_on_cur_user_pos&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[center_on_cur_user_pos]', array(
						'value' => $this->editMap && isset($this->map['params']['center_on_cur_user_pos']) ? $this->map['params']['center_on_cur_user_pos'] : false,
						'attrs' => 'class="umsProOpt"'))?>
					<div id="umsCurUserPosOptions" style="margin-top: 10px; display: none;">
						<?php echo htmlUms::hidden('map_opts[center_on_cur_user_pos_icon]', array(
							'value' => $this->editMap && isset($this->map['params']['center_on_cur_user_pos_icon'])
								? $this->map['params']['center_on_cur_user_pos_icon']
								: 1 //Default Icon ID
							))?>
						<img id="umsCurUserPosIconPrevImg" src="" style="float: left;" />
						<div style="float: right">
							<a href="#" id="umsCurUserPosIconBtn" class="button umsProOpt"><?php _e('Choose Icon', UMS_LANG_CODE)?></a>
							<a href="#" id="umsUploadCurUserPosIconBtn" class="button umsProOpt"><?php _e('Upload Icon', UMS_LANG_CODE)?></a>
							<div class="umsCurUserPosUplRes"></div>
							<div class="umsCurUserPosFileUpRes"></div>
						</div>
						<div style="clear: both;"></div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_frontend_add_markers">
						<?php _e('Add markers on frontend', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e("You can add markers at the current map with the frontend using the form, which can be displayed using the shortcode (it placed below preview map). Additional options that affect the operation of the form:" .
						"<br /><br /><b>Logged In Users Only</b> - form will be displayed only for logged in users." .
						"<br /><br /><b>Disable WP Editor</b> - disable / enable WP Editor for the Marker Description field of the form." .
						"<br /><br /><b>Delete markers</b> - disable / enable interface for deleting markers on frontend. Each user can delete only his own markers." .
						"<br /><br /><b>Use markers categories</b> - disable / enable interface for choose the marker category on frontend." .
						"<br /><br /><b>Use limits for marker's adding</b> - allows you to limit the number of markers, which user can add from one IP address at the current map for a certain amount of time." .
						"<br /><br /><b>Max marker's count</b> - the maximum number of markers, which can be added over certain amount of time." .
						"<br /><br /><b>For allotted time (minutes)</b> - the number of minutes, during which you can add the maximum number of markers." .
						"<br /><br />For example, during three minutes you can add only two markers at the map. If you try to add a third marker - the form will not be saved and you will see the notice with amount of time you must wait. After the right amount of time will pass - you can add next two markers, etc." .
						"<br /><br />Important! If map and form for add markers at this map are placed on one page - this page will be overload after marker adding.", UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=frontend_add_markers&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers]', array(
						'value' => $this->editMap && isset($this->map['params']['frontend_add_markers']) ? $this->map['params']['frontend_add_markers'] : false,
						'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers"'
					))?>
					<div id="umsAddMarkersOnFrontendOptions" style="display: none;">
						<div style="margin-top: 10px;">
							<?php echo htmlUms::text('umsCopyTextCode', array(
								'value' => '',	// Will be inserted from JS
								'attrs' => 'class="umsCopyTextCode umsMapMarkerFormCodeShell umsStaticWidth" style="width: 100%; text-align: center;"'));?>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_logged_in_only]', array(
								'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_logged_in_only']) ? $this->map['params']['frontend_add_markers_logged_in_only'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_logged_in_only"'
							))?>
							<label for="map_opts_frontend_add_markers_logged_in_only"><?php _e('Logged In Users Only', UMS_LANG_CODE)?></label>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_disable_wp_editor]', array(
								'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_disable_wp_editor']) ? $this->map['params']['frontend_add_markers_disable_wp_editor'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_disable_wp_editor"'
							))?>
							<label for="map_opts_frontend_add_markers_disable_wp_editor"><?php _e('Disable WP Editor', UMS_LANG_CODE)?></label>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_delete_markers]', array(
								'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_delete_markers']) ? $this->map['params']['frontend_add_markers_delete_markers'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_delete_markers"'
							))?>
							<label for="map_opts_frontend_add_markers_delete_markers"><?php _e('Delete markers', UMS_LANG_CODE)?></label>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_use_markers_categories]', array(
								'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_markers_categories']) ? $this->map['params']['frontend_add_markers_use_markers_categories'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_use_markers_categories"'
							))?>
							<label for="map_opts_frontend_add_markers_use_markers_categories"><?php _e('Use markers categories', UMS_LANG_CODE)?></label>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[frontend_add_markers_use_limits]', array(
								'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_limits']) ? $this->map['params']['frontend_add_markers_use_limits'] : false,
								'attrs' => 'class="umsProOpt" id="map_opts_frontend_add_markers_use_limits"'
							))?>
							<label for="map_opts_frontend_add_markers_use_limits"><?php _e('Use limits for marker\'s adding', UMS_LANG_CODE)?></label>
						</div>
						<div id="umsUseLimitsForMarkerAddingOptions" style="display: none; margin-top: 10px;">
							<div class="sup-col sup-w-50">
								<label for="map_opts_frontend_add_markers_use_count_limits">
									<?php _e('Max marker\'s count', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::text('map_opts[frontend_add_markers_use_count_limits]', array(
									'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_count_limits']) ? $this->map['params']['frontend_add_markers_use_count_limits'] : '10',
									'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_count_limits"'))?>
							</div>
							<div class="sup-col sup-w-50">
								<label for="map_opts_frontend_add_markers_use_time_limits">
									<?php _e('For allotted time (minutes)', UMS_LANG_CODE)?>
								</label>
								<?php echo htmlUms::text('map_opts[frontend_add_markers_use_time_limits]', array(
									'value' => $this->editMap && isset($this->map['params']['frontend_add_markers_use_time_limits']) ? $this->map['params']['frontend_add_markers_use_time_limits'] : '10',
									'attrs' => 'style="width: 100%;" id="map_opts_frontend_add_markers_use_time_limits"'))?>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_places_en_toolbar">
						<?php _e('Use Places Toolbar', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e("Activate the toolbar for search Places (restaurants, schools, museums, etc.) on the map. Use the shortcode to display toolbar on wherever you need, but toolbar must be placed on the same page as its map.", UMS_LANG_CODE);
					echo '<br />';
					if(!$this->isPro){
						echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/places/places.png" /></a>');
					}?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=places_toolbar&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[places][en_toolbar]', array(
						'value' => $this->editMap && isset($this->map['params']['places']['en_toolbar']) ? $this->map['params']['places']['en_toolbar'] : false,
						'attrs' => 'class="umsProOpt" id="map_opts_places_en_toolbar"'
					))?>
					<div id="umsPlacesToolbarOptions" style="display: none;">
						<div style="margin-top: 10px;">
							<?php echo htmlUms::text('umsCopyTextCode', array(
								'value' => '',	// Will be inserted from JS
								'attrs' => 'class="umsCopyTextCode umsPlacesToolbarCodeShell umsStaticWidth" style="width: 100%; text-align: center;"'));?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_places_slider_min">
								<?php _e('Min Search Radius (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[places][slider_min]', array(
								'value' => $this->editMap && isset($this->map['params']['places']['slider_min']) ? $this->map['params']['places']['slider_min'] : '100',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_places_slider_min"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_places_slider_max">
								<?php _e('Max Search Radius (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[places][slider_max]', array(
								'value' => $this->editMap && isset($this->map['params']['places']['slider_max']) ? $this->map['params']['places']['slider_max'] : '1000',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_places_slider_max"'))?>
						</div>
						<div style="margin-top: 10px;">
							<label for="map_opts_places_slider_step">
								<?php _e('Search Step (in meters):', UMS_LANG_CODE)?>
							</label></br>
							<?php echo htmlUms::text('map_opts[places][slider_step]', array(
								'value' => $this->editMap && isset($this->map['params']['places][slider_step']) ? $this->map['params']['places][slider_step'] : '10',
								'attrs' => 'class="umsProOpt" style="width: 100%;" id="map_opts_places_slider_step"'))?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_title_color">
						<?php _e('Filter background', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Background color for markers filter. (for 7 markers list type)', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::colorpicker('map_opts[marker_filter_color]', array(
						'value' => $this->editMap && isset($this->map['params']['marker_filter_color']) ? $this->map['params']['marker_filter_color'] : '#f1f1f1;'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_title_color">
						<?php _e('Filters select all button title', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Filters select all button title. (for 7 markers list type)', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::text('map_opts[marker_filter_button_title]', array(
						'value' => $this->editMap && isset($this->map['params']['marker_filter_button_title']) ? $this->map['params']['marker_filter_button_title'] : 'Select all'))?>
				</td>
			</tr>
			<?php */ ?>
			<tr>
				<th scope="row">
					<label class="label-big">
						<?php _e('Info Window', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Parameters of markers / shapes info-window PopUp', UMS_LANG_CODE)?>"></i>
				</th>
				<td></td>
			</tr>
			<?php /* ?><tr>
				<th scope="row">
					<label for="map_opts_marker_infownd_type">
						<?php _e('Appearance', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Choose the appearance type of infowindow.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::selectbox('map_opts[marker_infownd_type]', array(
						'options' => array('' => __('Default', UMS_LANG_CODE), 'rounded_edges' => __('Rounded Edges', UMS_LANG_CODE),),
						'value' => $this->editMap && isset($this->map['params']['marker_infownd_type']) ? $this->map['params']['marker_infownd_type'] : 'default',
						'attrs' => 'style="width: 100%;" id="map_opts_marker_infownd_type"'))?>
					<div id="umsMarkerInfoWndTypeSubOpts">
						<div class="umsSubOpt" data-type="rounded_edges" style="display: none; margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[marker_infownd_hide_close_btn]', array(
								'value' => $this->editMap && isset($this->map['params']['marker_infownd_hide_close_btn']) ? $this->map['params']['marker_infownd_hide_close_btn'] : true,
								'attrs' => 'class="umsProOpt" id="map_opts_marker_infownd_hide_close_btn"'
							))?>
							<label for="map_opts_marker_infownd_hide_close_btn"><?php _e('Hide Close Button', UMS_LANG_CODE)?></label>
						</div>
					</div>
				</td>
			</tr><?php */ ?>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_infownd_width">
						<?php _e('Width', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Width of info window', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
				<?php
					$markersInfoWndWidthUnits = isset($this->map['params']['marker_infownd_width_units']) && $this->map['params']['marker_infownd_width_units'];
					$markersInfoWndWidthInput = isset($this->map['params']['marker_infownd_width']) && $this->map['params']['marker_infownd_width'];
					$markersInfoWndWidthInputViewStyle = $this->editMap && $markersInfoWndWidthUnits && htmlUms::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') ? 'block' : 'none';
					$markersInfoWndWidthUnitsLabelStyle = $this->editMap && $markersInfoWndWidthUnits && htmlUms::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') ? '7px' : '0px';
				?>
					<div class="sup-col" style="padding-right: 0px;">
						<label for="map_opts_marker_infownd_width_units" style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndWidthUnitsLabelStyle?>;">
							<?php echo htmlUms::radiobutton('map_opts[marker_infownd_width_units]', array(
								'value' => 'auto',
								'checked' => $this->editMap && $markersInfoWndWidthUnits ? htmlUms::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'auto') : true,
							))?>&nbsp;<?php _e('Auto', UMS_LANG_CODE)?>
						</label>
						<label
							for="map_opts_marker_infownd_width_units"
							class="supsystic-tooltip"
							title="<?php _e('The value defines maximum width of the description. Window will be drawn according to content size but not wider than the value.', UMS_LANG_CODE)?>"
							style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndWidthUnitsLabelStyle?>;"
						>
							<?php echo htmlUms::radiobutton('map_opts[marker_infownd_width_units]', array(
								'value' => 'px',
								'checked' => $this->editMap && $markersInfoWndWidthUnits ? htmlUms::checkedOpt($this->map['params'], 'marker_infownd_width_units', 'px') : false,
							))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?>
						</label>
					</div>
					<div class="sup-col sup-w-25">
						<?php echo htmlUms::text('map_opts[marker_infownd_width]', array(
							'value' => $this->editMap && $markersInfoWndWidthInput ? $this->map['params']['marker_infownd_width'] : '200',
							'attrs' => 'style="width: 100%; display: '. $markersInfoWndWidthInputViewStyle .';"'))?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_infownd_height">
						<?php _e('Height', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Height of info window', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php
					$markersInfoWndHeightUnits = isset($this->map['params']['marker_infownd_height_units']) && $this->map['params']['marker_infownd_height_units'];
					$markersInfoWndHeightInput = isset($this->map['params']['marker_infownd_height']) && $this->map['params']['marker_infownd_height'];
					$markersInfoWndHeightInputViewStyle = $this->editMap && $markersInfoWndHeightUnits && htmlUms::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') ? 'block' : 'none';
					$markersInfoWndHeightUnitsLabelStyle = $this->editMap && $markersInfoWndHeightUnits && htmlUms::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') ? '7px' : '0px';
					?>
					<div class="sup-col" style="padding-right: 0px;">
						<label for="map_opts_marker_infownd_height_units" style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndHeightUnitsLabelStyle?>;">
							<?php echo htmlUms::radiobutton('map_opts[marker_infownd_height_units]', array(
								'value' => 'auto',
								'checked' => $this->editMap && $markersInfoWndHeightUnits ? htmlUms::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'auto') : true,
							))?>&nbsp;<?php _e('Auto', UMS_LANG_CODE)?>
						</label>
						<label
							for="map_opts_marker_infownd_height_units"
							class="supsystic-tooltip"
							title="<?php _e('Pixels', UMS_LANG_CODE)?>"
							style="margin-right: 15px; position: relative; top: <?php echo $markersInfoWndHeightUnitsLabelStyle?>;"
							>
							<?php echo htmlUms::radiobutton('map_opts[marker_infownd_height_units]', array(
								'value' => 'px',
								'checked' => $this->editMap && $markersInfoWndHeightUnits ? htmlUms::checkedOpt($this->map['params'], 'marker_infownd_height_units', 'px') : false,
							))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?>
						</label>
					</div>
					<div class="sup-col sup-w-25">
						<?php echo htmlUms::text('map_opts[marker_infownd_height]', array(
							'value' => $this->editMap && $markersInfoWndHeightInput ? $this->map['params']['marker_infownd_height'] : '100',
							'attrs' => 'style="width: 100%; display: '. $markersInfoWndHeightInputViewStyle .';"'))?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_title_color">
						<?php _e('Title Color', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window title color here', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::colorpicker('map_opts[marker_title_color]', array(
						'value' => $this->editMap && isset($this->map['params']['marker_title_color']) ? $this->map['params']['marker_title_color'] : '#000000'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_infownd_bg_color">
						<?php _e('Background Color', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window background color here', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::colorpicker('map_opts[marker_infownd_bg_color]', array(
						'value' => $this->editMap && isset($this->map['params']['marker_infownd_bg_color']) ? $this->map['params']['marker_infownd_bg_color'] : '#FFFFFF'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_title_size">
						<?php _e('Title Font Size', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window title font size here', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div class="sup-col sup-w-25">
						<?php echo htmlUms::text('map_opts[marker_title_size]', array(
							'value' => $this->editMap && isset($this->map['params']['marker_title_size']) ? $this->map['params']['marker_title_size'] : '19',
							'attrs' => 'style="width: 100%;" id="map_opts_marker_title_size"'))?>
					</div>
					<div class="sup-col sup-w-75">
						<label class="supsystic-tooltip" title="<?php _e('Pixels', UMS_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;">
							<?php echo htmlUms::radiobutton('map_opts[marker_title_size_units]', array(
								'value' => 'px',
								'checked' => true,
							))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?></label>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_marker_desc_size">
						<?php _e('Description Font Size', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('You can set your info window description font size here', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div class="sup-col sup-w-25">
						<?php echo htmlUms::text('map_opts[marker_desc_size]', array(
							'value' => $this->editMap && isset($this->map['params']['marker_desc_size']) ? $this->map['params']['marker_desc_size'] : '13',
							'attrs' => 'style="width: 100%;" id="map_opts_marker_desc_size"'))?>
					</div>
					<div class="sup-col sup-w-75">
						<label class="supsystic-tooltip" title="<?php _e('Pixels', UMS_LANG_CODE)?>" style="margin-right: 15px; position: relative; top: 7px;">
							<?php echo htmlUms::radiobutton('map_opts[marker_desc_size_units]', array(
								'value' => 'px',
								'checked' => true,
							))?>&nbsp;<?php _e('Px', UMS_LANG_CODE)?></label>
					</div>
				</td>
			</tr>
			<?php /*?>
			<tr>
				<th scope="row">
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_directions_btn&utm_campaign=ultimatemaps'); ?>
					<?php }?>
					<label for="map_opts_enable_directions_btn">
						<?php _e('Directions Button', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip"
						title="<?php _e('Add a button at marker info window to get direction from the entered address to the marker. If Show route data option is enabled - the total route time and distance will be shown by click on the route polyline.', UMS_LANG_CODE);
						if(!$this->isPro){
							echo esc_html('<a href="'. $proLink. '" target="_blank"><img src="'. $this->promoModPath. 'img/directions/get_directions.png" /></a>');
						}?>"
						></i>
					<?php if(!$this->isPro) { ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_directions_btn]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_directions_btn']) ? $this->map['params']['enable_directions_btn'] : false,
						'attrs' => 'class="umsProOpt"'))?>
					<div id="umsDirectionsOptions" style="margin-top: 10px; display: none;">
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[directions_alternate_routes]', array(
								'value' => $this->editMap && isset($this->map['params']['directions_alternate_routes']) ? $this->map['params']['directions_alternate_routes'] : false,
								'attrs' => 'class="umsProOpt"'))?>
							<span>
							<?php _e('Show alternate routes', UMS_LANG_CODE)?>
						</span>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[directions_data_show]', array(
								'value' => $this->editMap && isset($this->map['params']['directions_data_show']) ? $this->map['params']['directions_data_show'] : false,
								'attrs' => 'class="umsProOpt"'))?>
							<span>
								<?php _e('Show route data', UMS_LANG_CODE)?>
							</span>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[directions_steps_show]', array(
								'value' => $this->editMap && isset($this->map['params']['directions_steps_show']) ? $this->map['params']['directions_steps_show'] : false,
								'attrs' => 'class="umsProOpt"'))?>
							<span>
								<?php _e('Show route steps', UMS_LANG_CODE)?>
							</span>
						</div>
						<div style="margin-top: 10px;">
							<?php echo htmlUms::checkboxHiddenVal('map_opts[directions_miles]', array(
								'value' => $this->editMap && isset($this->map['params']['directions_miles']) ? $this->map['params']['directions_miles'] : false,
								'attrs' => 'class="umsProOpt"'))?>
							<span>
								<?php _e('Use miles', UMS_LANG_CODE)?>
							</span>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="map_opts_enable_infownd_print_btn">
						<?php _e('Print Button', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Add Print button to markers info window', UMS_LANG_CODE)?>"></i>
					<?php if(!$this->isPro) { ?>
						<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=enable_infownd_print_btn&utm_campaign=ultimatemaps'); ?>
						<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
					<?php }?>
				</th>
				<td>
					<?php echo htmlUms::checkboxHiddenVal('map_opts[enable_infownd_print_btn]', array(
						'value' => $this->editMap && isset($this->map['params']['enable_infownd_print_btn']) ? $this->map['params']['enable_infownd_print_btn'] : false,
						'attrs' => 'class="umsProOpt"'
					))?>
				</td>
			</tr>
			<?php */ ?>
		</table>
	</div>
	<?php if(isset($this->engineFromReq) && !empty($this->engineFromReq)) {
		echo htmlUms::hidden('map_opts[engine_from_req]', array('value' => $this->engineFromReq));
	} ?>
	<?php echo htmlUms::hidden('mod', array('value' => 'maps'))?>
	<?php echo htmlUms::hidden('action', array('value' => 'save'))?>
	<?php echo htmlUms::hidden('map_opts[id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
	<?php echo htmlUms::hidden('map_opts[membershipEnable]', array('value' => isset($this->map['params']['membershipEnable']) ? $this->map['params']['membershipEnable'] : 0, 'attrs' => 'id="membershipHiddenEnable"'))?>
</form>
<!--Map Markers List Wnd-->
<div id="umsMarkersListWnd" style="display: none;" title="<?php _e('Show markers list with your map on frontend', UMS_LANG_CODE)?>">
	<!--Mml == Map Markers List-->
	<ul id="umsMml">
		<?php foreach($this->markerLists as $lKey => $lData) { ?>
		<li class="umsMmlElement umsMmlElement-<?php echo $lKey?>" data-key="<?php echo $lKey?>">
			<img src="<?php echo $this->promoModPath?>img/markers_list/<?php echo $lData['prev_img']?>" /><br />
			<div class="umsMmlElementBtnShell">
				<a href="<?php echo frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_list_' . $lKey . '&utm_campaign=ultimatemaps');?>" target="_blank" class="button button-primary umsMmlApplyBtn" data-apply-label="<?php _e('Apply', UMS_LANG_CODE)?>" data-active-label="<?php _e('Selected', UMS_LANG_CODE)?>">
					<?php $this->isPro ? _e('Apply', UMS_LANG_CODE) : _e('Available in PRO', UMS_LANG_CODE)?>
				</a>
			</div>
		</li>
		<?php }?>
	</ul>
</div>
<!--Icons Wnd-->
<div id="umsIconsWnd" style="display: none;">
	<ul class="iconsList">
		<?php foreach($this->icons as $icon) { ?>
			<li class="previewIcon"
				data-id="<?php echo $icon['id']?>"
				data-width="<?php echo $icon['width']?>"
				data-height="<?php echo $icon['height']?>"
				title="<?php echo $icon['title']?>">
				<img src="<?php echo $icon['path']?>" >
				<?php if(!(int)$icon['is_def']) { ?>
					<i class="fa fa-times" aria-hidden="true"></i>
				<?php } ?>
			</li>
		<?php }?>
	</ul>
</div>
