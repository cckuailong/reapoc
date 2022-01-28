<?php
// TODO: Make this as it should be : in classe, or just in normal way - not this, thing ...............
$promoData = frameUms::_()->getModule('supsystic_promo')->addPromoMapTabs();
?>
<?php if($this->isPro) {?>
	<form id="umsHeatmapForm">
		<table class="form-table">
			<tr>
				<th scope="row">
					<label class="label-big">
						<?php _e('Points', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('To add Heatmap Layer points you need to activate Add Points button and draw each point by click on map. To remove points you need to activate Remove Points button and delete necessary point by click on it or just click on Delete Heatmap Layer button to remove all Heatmap Layer points. Important! You must to deactivate Add by Click and Remove by Click buttons after ending of the add / remove points.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<div class="umsHeatmapPointsBtns">
						<a href="#" class="button" id="umsHeatmapAddPointBtn">
							<?php _e('Add Point', UMS_LANG_CODE)?>
						</a>
						<a href="#" class="button" id="umsHeatmapRemovePointBtn">
							<?php _e('Remove Point', UMS_LANG_CODE)?>
						</a>
					</div>
					<div class="umsHeatmapPointsCount">
						<label>
							<?php _e('Points Count', UMS_LANG_CODE)?>:
						</label>
						<div id="umsHeatmapPointsNumber"></div>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>
						<?php _e('Radius', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points radius in pixels', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::text('heatmap_opts[params][radius]', array(
						'value' => '',
						'attrs' => 'style="width: 100%;"'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>
						<?php _e('Opacity', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points opacity', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<?php echo htmlUms::selectbox('heatmap_opts[params][opacity]', array(
						'options' => array(
							'0' => 0, '0.1' => 0.1, '0.2' => 0.2, '0.3' => 0.3
						,	'0.4' => 0.4, '0.5' => 0.5, '0.6' => 0.6
						,	'0.7' => 0.7, '0.8' => 0.8, '0.9' => 0.9, '1' => 1),
						'value' => '',
						'attrs' => 'style="width: 100%;"'))?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label>
						<?php _e('Gradient', UMS_LANG_CODE)?>:
					</label>
					<i style="float: right;" class="fa fa-question supsystic-tooltip" title="<?php _e('Heatmap Layer points color gradient.', UMS_LANG_CODE)?>"></i>
				</th>
				<td>
					<a href="#" class="button" id="umsHeatmapAddColorBtn">
						<?php _e('Add Color', UMS_LANG_CODE)?>
					</a>
					<a href="#" class="button" id="umsHeatmapClearColorsBtn" style="float: right;">
						<?php _e('Clear', UMS_LANG_CODE)?>
					</a>
					<div class="umsHeatmapGradientExample umsHeatmapGradient" style="display: none; margin-top: 10px;">
						<input type="text" name="heatmap_opts[params][gradient][]" value="#5ED836" disabled="disabled" />
						<a href="#" class="button umsHeatmapRemoveColorBtn" title="<?php _e('Remove Color', UMS_LANG_CODE)?>" onclick="umsHeatmapRemoveColorBtnClick(this); return false;">
							<i class="fa fa-trash-o"></i>
						</a>
					</div>
					<div id="umsHeatmapGradientFirstColorContainer">

					</div>
					<?php echo htmlUms::hidden('heatmap_opts[params][gradient][]', array('value' => '', 'attrs' => 'class="firstHeatmapColor"'))?>
					<div id="umsHeatmapGradientContainer"></div>
				</td>
			</tr>
		</table>
		<?php echo htmlUms::hidden('mod', array('value' => 'heatmap'))?>
		<?php echo htmlUms::hidden('action', array('value' => 'save'))?>
		<?php echo htmlUms::hidden('heatmap_opts[id]', array('value' => ''))?>
		<?php echo htmlUms::hidden('heatmap_opts[map_id]', array('value' => $this->editMap ? $this->map['id'] : ''))?>
	</form>
<?php } else {
	echo $promoData['umsHeatmapTab']['content'];
}?>
