<div class="supsystic-actions-wrap" style="display: none;">
	<a class="button-table-action" id="editMarkerGroup<?php echo $this->marker_group['id']; ?>" href="<?php echo $this->editLink?>">
		<i class="fa fa-fw fa-pencil"></i>
	</a>
	<a class="button-table-action" id="deleteMarkerGroup<?php echo $this->marker_group['id']; ?>" href="#" onclick="umsRemoveMarkerGroupFromTblClick(<?php echo $this->marker_group['id'];?>);">
		<i class="fa fa-fw fa-trash-o"></i>
	</a>
	<div id="mgrRemoveElemLoader__<?php echo $this->marker_group['id'];?>" style="display: inline-block;"></div>
</div>