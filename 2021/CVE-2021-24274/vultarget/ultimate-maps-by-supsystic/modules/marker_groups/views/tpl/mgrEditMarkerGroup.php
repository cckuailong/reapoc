<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<div id="umsMgrTab" class="mgrTabContent">
				<a
					href="<?php echo $this->addNewLink?>"
					class="button button-table-action"
					id="addMarkerGroup"
					style="display: <?php echo $this->editMarkerGroup ? 'inline-block;' : 'none;'?>"
				>
					<?php _e('Add Category', UMS_LANG_CODE)?>
				</a>
				<button class="button" id="umsMgrSaveBtn">
					<i class="fa fa-save"></i>
					<?php _e('Save', UMS_LANG_CODE)?>
				</button>
				<form id="umsMgrForm">
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="marker_group_title">
									<?php _e('Category Title', UMS_LANG_CODE)?>:
								</label>
							</th>
							<td>
								<?php echo htmlUms::text('marker_group[title]', array(
									'value' => $this->editMarkerGroup ? $this->marker_group['title'] : '',
									'attrs' => 'style="width: 50%;" id="marker_group_title"',
									'required' => true))?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="marker_group_parent">
									<?php _e('Parent Category', UMS_LANG_CODE)?>:
								</label>
							</th>
							<td>
								<?php echo htmlUms::selectbox('marker_group[parent]', array(
									'options' => $this->parentsList,
									'value' => $this->editMarkerGroup ? $this->marker_group['parent'] : 0,
									'attrs' => 'style="width: 50%;" id="marker_group_parent"',
								))?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="marker_group_bg_color">
									<?php _e('Background Color', UMS_LANG_CODE)?>:
								</label>
							</th>
							<td>
								<?php echo htmlUms::colorpicker('marker_group[bg_color]', array(
									'value' => $this->editMarkerGroup && $this->marker_group['params']['bg_color'] ? $this->marker_group['params']['bg_color'] : '#E4E4E4'))?>
							</td>
						</tr>
                  <tr>
							<th scope="row">
								<label for="marker_group_text_color">
									<?php _e('Text Color', UMS_LANG_CODE)?>:
								</label>
							</th>
							<td>
								<?php echo htmlUms::colorpicker('marker_group[text_color]', array(
									'value' => $this->editMarkerGroup && $this->marker_group['params']['text_color'] ? $this->marker_group['params']['text_color'] : '#E4E4E4'))?>
							</td>
						</tr>
						<?php /*?><tr>
							<th scope="row">
								<label for="marker_group_claster_icon">
									<?php _e('Cluster Icon', UMS_LANG_CODE)?>:
								</label>
							</th>
							<td>
								<?php
								$curMarkerGroupClusterIcon = uriUms::_(
									$this->editMarkerGroup
									&& isset($this->marker_group['params']['claster_icon'])
									&& $this->marker_group['params']['claster_icon']
										? $this->marker_group['params']['claster_icon']
										: UMS_MODULES_PATH . 'maps/img/m1.png');
								$curMarkerGroupClusterIconWidth =
									$this->editMarkerGroup
									&& isset($this->marker_group['params']['clasterer_icon_width'])
									&& $this->marker_group['params']['clasterer_icon_width']
										? $this->marker_group['params']['clasterer_icon_width']
										: 53;
								$curMarkerGroupClusterIconHeight =
									$this->editMarkerGroup
									&& isset($this->marker_group['params']['clasterer_icon_height'])
									&& $this->marker_group['params']['marker_clasterer_icon_height']
										? $this->marker_group['params']['marker_clasterer_icon_height']
										: 52;
								?>
								<img id="umsMarkerGroupClastererIconPrevImg" class="umsSubOpt" src="<?php echo $curMarkerGroupClusterIcon?>" style="max-width: 53px; height: auto;" />
								<a id="umsUploadMarkerGroupClastererIconBtn" class="button umsSubOpt" href="#" ><?php _e('Upload Icon', UMS_LANG_CODE)?></a>
								<a id="umsDefaultMarkerGroupClastererIconBtn" class="button umsSubOpt" href="#" ><?php _e('Default Icon', UMS_LANG_CODE)?></a>
								<div class="umsMarkerGroupClastererUplRes"></div>
								<?php echo htmlUms::hidden('marker_group[claster_icon]', array('value' => $curMarkerGroupClusterIcon, ))?>
								<?php echo htmlUms::hidden('marker_group[claster_icon_width]', array('value' => $curMarkerGroupClusterIconWidth, ))?>
								<?php echo htmlUms::hidden('marker_group[claster_icon_height]', array('value' => $curMarkerGroupClusterIconHeight, ))?>
							</td>
						</tr><?php */?>
					</table>
					<?php echo htmlUms::hidden('mod', array('value' => 'marker_groups'))?>
					<?php echo htmlUms::hidden('action', array('value' => 'save'))?>
					<?php echo htmlUms::hidden('marker_group[id]', array('value' => $this->editMarkerGroup ? $this->marker_group['id'] : ''))?>
				</form>
			</div>
		</div>
	</div>
</section>
