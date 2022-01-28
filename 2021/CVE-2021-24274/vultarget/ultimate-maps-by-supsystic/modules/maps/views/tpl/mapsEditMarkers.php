<form id="umsMarkerForm">
	<table class="form-table">
		<tr>
			<th scope="row">
				<label class="label-big" for="marker_opts_title">
					<?php _e('Marker Name', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your marker title', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::text('marker_opts[title]', array(
					'value' => '',
					'attrs' => 'style="width: 100%;" id="marker_opts_title"'))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label>
					<?php _e('Marker Description', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Write here all text, that you want to appear in marker info-window PopUp', UMS_LANG_CODE)?>"></i>
			</th>
			<td></td>
		</tr>
		<tr>
			<th colspan="2">
				<?php wp_editor('', 'markerDescription', array(
					//'textarea_name' => 'marker_opts[description]',
					'textarea_rows' => 10
				));?>
				<?php echo htmlUms::hidden('marker_opts[description]', array('value' => ''))?>
			</th>
		</tr>
		<tr>
			<th scope="row">
				<label class="label-big" for="umsMarkerIconBtn">
					<?php _e('Icon', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Your marker Icon, that will appear on your map for this marker', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::hidden('marker_opts[icon]', array(
					'value' => 1 /*Default Icon ID*/ ))?>
				<img id="umsMarkerIconPrevImg" src="" style="float: left;" />
				<div style="float: right">
					<a id="umsMarkerIconBtn" href="#" class="button"><?php _e('Choose Icon', UMS_LANG_CODE)?></a>
					<a id="umsUploadIconBtn" href="#" class="button"><?php _e('Upload Icon', UMS_LANG_CODE)?></a>
					<div class="umsUplRes"></div>
					<div class="umsFileUpRes"></div>
				</div>
				<div style="clear: both;"></div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_address">
					<?php _e('Address', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Search your location by address, just start typing here', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::text('marker_opts[address]', array(
					'value' => '',
					'placeholder' => '603 Park Avenue, Brooklyn, NY 11206, USA',
					'attrs' => 'style="width: 100%;" id="marker_opts_address"'))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_coord_x">
					<?php _e('Latitude', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Latitude for your marker', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::text('marker_opts[coord_x]', array(
					'value' => '',
					'placeholder' => '40.69827799999999',
					'attrs' => 'style="width: 100%;" id="marker_opts_coord_x"'))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_coord_y">
					<?php _e('Longitude', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Longitude for your marker', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::text('marker_opts[coord_y]', array(
					'value' => '',
					'placeholder' => '-73.95141139999998',
					'attrs' => 'style="width: 100%;" id="marker_opts_coord_y"'))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_marker_group_id">
					<?php _e('Marker Category', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Choose marker category', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<div style="width: 100%;">
					<?php echo htmlUms::selectlist('marker_opts[marker_group_id]', array(
						'options' => $this->markerGroupsForSelect,
						'value' => '',
						'attrs' => 'style="width: 100%;" id="marker_opts_marker_group_id" class="chosen"'))?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_marker_link">
					<?php _e('Marker Link', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Link for opening by click on the marker', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][marker_link]', array(
					'checked' => '',
					'attrs' => 'id="marker_link" onclick="umsAddLinkOptions()"',
				))?>
				<div id="link_options" style="display: none;">
					<?php echo htmlUms::text('marker_opts[params][marker_link_src]', array(
						'value' => '',
						'attrs' => 'style="width: 90%; float: right; margin: 0px 0px 10px 0px;"',
					))?>
					<div style="clear: both;"></div>
					<?php echo htmlUms::checkbox('marker_opts[params][marker_link_new_wnd]', array(
						'checked' => ''))?>
					<span>
						<?php _e('Open in new window', UMS_LANG_CODE)?>
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_show_description">
					<?php _e('Show description by default', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Open marker description when map load', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][show_description]', array(
					'checked' => ''))?>
			</td>
		</tr>
		<?php /*?>
		<tr>
			<th scope="row">
				<label for="marker_opts_description_mouse_hover">
					<?php _e('Show description by mouse hover', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Open marker description by mouse hover', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][description_mouse_hover]', array(
					'checked' => ''))?>
			</td>
		</tr>
		<tr id="marker_opts_description_mouse_leave">
			<th scope="row">
				<label for="marker_opts_description_mouse_leave">
					<?php _e('Hide description on mouse leave', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Hide description when mouse leaves the marker area', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][description_mouse_leave]', array(
					'checked' => ''))?>
			</td>
		</tr>
		<tr style="display: none;">
			<th scope="row">
				<label for="marker_opts_marker_list_def_img">
					<?php _e('Marker List Default Image', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('If there is no image tag in the marker description - this image will be used for displaying in the map\'s markers list', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][marker_list_def_img]', array(
					'checked' => ''))?>
				<div id="umsMarkerListDefImgOptions" style="display: none;">
					<a 	href="#"
						id="umsMarkerListDefImgUploadFileBtn"
						class="button umsProOpt"
						title="<?php _e('Upload', UMS_LANG_CODE)?>"
						data-nonce="<?php echo wp_create_nonce('upload-marker-list-def-img-file')?>"
						data-url="<?php echo uriUms::_(array(
							'baseUrl' => admin_url('admin-ajax.php'),
							'page' => 'add_map_options',
							'action' => 'addFromFile',
							'reqType' => 'ajax',
							'pl' => UMS_CODE))?>"
						  style="float: right;">
						<i class="fa fa-upload"></i>
					</a>
					<?php echo htmlUms::text('marker_opts[params][marker_list_def_img_url]', array(
						'value' => '',
						'attrs' => 'id="umsMarkerListDefImgUrl" style="width: 78%; margin-right: 5px; float: right;"',
					))?>
					<span class="umsMarkerListDefImgUploadMsg" style="	float: right; width: 100%; text-align: right;" ></span>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="marker_opts_clasterer_exclude">
					<?php _e('Exclude from Cluster', UMS_LANG_CODE)?>:
				</label>
				<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Exclude marker from cluster if Markers Clusterization option is enabled.', UMS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlUms::checkbox('marker_opts[params][clasterer_exclude]', array(
					'checked' => ''))?>
			</td>
		</tr>
		<?php if($this->isCustSearchAndMarkersPeriodAvailable) { ?>
		<tr>
			<th>
				<label>
					<?php _e('Period From', UMS_LANG_CODE);?>
				</label>
<!--										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="--><?php //_e('', UMS_LANG_CODE); ?><!--"></i>-->
				<?php if(!$this->isPro) { ?>
					<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_period_from&utm_campaign=ultimatemaps'); ?>
					<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
				<?php }?>
			</th>
			<td>
				<?php
				if($this->isPro) {
					echo htmlUms::text('marker_opts[period_date_from]', array(
						'value' => '',
						'attrs' => 'id="markerPeriodDateFrom"',
					));
				}
				?>
			</td>
		</tr>
		<tr>
			<th>
				<label>
					<?php _e('Period To', UMS_LANG_CODE);?>
				</label>
<!--										<i style="float: right;" class="fa fa-question supsystic-tooltip" title="--><?php //_e('', UMS_LANG_CODE); ?><!--"></i>-->
				<?php if(!$this->isPro) { ?>
					<?php $proLink = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=marker_period_to&utm_campaign=ultimatemaps'); ?>
					<br /><span class="umsProOptMiniLabel"><a target="_blank" href="<?php echo $proLink?>"><?php _e('PRO option', UMS_LANG_CODE)?></a></span>
				<?php }?>
			</th>
			<td>
				<?php
				if($this->isPro) {
					echo htmlUms::text('marker_opts[period_date_to]', array(
						'value' => '',
						'attrs' => 'id="markerPeriodDateTo"',
					));
				}?>
			</td>
		</tr>
		<?php }?>
		<?php */ ?>
	</table>
	<?php echo htmlUms::hidden('mod', array('value' => 'marker'))?>
	<?php echo htmlUms::hidden('action', array('value' => 'save'))?>
	<?php echo htmlUms::hidden('marker_opts[id]', array('value' => ''))?>
	<?php echo htmlUms::hidden('marker_opts[map_id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
	<?php echo htmlUms::hidden('marker_opts[path]', array('value' => ''))?>
</form>
