<?php
$viewId = $this->currentMap['view_id'];
$mapHtmlId = $this->currentMap['view_html_id'];
$mapPreviewClassname = @$this->currentMap['html_options']['classname'];
//$mapOptsClassname = $popup ? 'display_as_popup' : '';

if($this->markersDisplayType === 'slider_checkbox_table') {
	$mapsWrapperStart = "<div class='umsLeft'>";
	$mapsWrapperEnd = "</div>";
	$filtersWrapperStart = "<div class='filterRight'>";
	$filtersWrapperEnd = "</div>";
} else {
	$mapsWrapperStart = "";
	$mapsWrapperEnd = "";
	$filtersWrapperStart = "";
	$filtersWrapperEnd = "";
}?>
<div class="ums_map_opts" id="mapConElem_<?php echo $viewId;?>"
	data-id="<?php echo $this->currentMap['id']; ?>" data-view-id="<?php echo $viewId;?>"
	<?php if(!empty($this->mbsIntegrating)) {
		echo 'data-mbs-gme-map="' . $this->currentMap['id'] . '" style="display:none;"';
	} else if(!empty($this->mbsMapId) && !empty($this->mbsMapInfo)) {
		echo "data-mbs-gme-map-id='" . $this->mbsMapId . "' data-mbs-gme-map-info='" . $this->mbsMapInfo . "'";
	}
	?>
>
	<?php echo $mapsWrapperStart; ?>
	<div class="umsMapDetailsContainer" id="umsMapDetailsContainer_<?php echo $viewId ;?>">
		<i class="umsKMLLayersPreloader fa fa-spinner fa-spin" aria-hidden="true" style="display: none;"></i>
		<div class="ums_MapPreview <?php echo $mapPreviewClassname;?>" id="<?php echo $mapHtmlId ;?>"></div>
	</div>
	<?php echo $mapsWrapperEnd; ?>

	<?php echo $filtersWrapperStart; ?>
	<div class="umsMapMarkerFilters" id="umsMapMarkerFilters_<?php echo $viewId;?>">
		<?php dispatcherUms::doAction('addMapFilters', $this->currentMap); ?>
	</div>
	<?php echo $filtersWrapperEnd; ?>

	<div class="umsMapProControlsCon" id="umsMapProControlsCon_<?php echo $viewId;?>">
		<?php dispatcherUms::doAction('addMapBottomControls', $this->currentMap); ?>
	</div>
	<div class="umsMapProDirectionsCon" id="umsMapProDirectionsCon_<?php echo $viewId;?>" >
		<?php dispatcherUms::doAction('addMapDirectionsData', $this->currentMap); ?>
	</div>
	<div class="umsMapProKmlFilterCon" id="umsMapProKmlFilterCon_<?php echo $viewId;?>" >
		<?php dispatcherUms::doAction('addMapKmlFilterData', $this->currentMap); ?>
	</div>
	<div class="umsSocialSharingShell umsSocialSharingShell_<?php echo $viewId ;?>">
		<?php echo $this->currentMap['params']['ss_html'];?>
	</div>
	<div style="clear: both;"></div>
</div>
