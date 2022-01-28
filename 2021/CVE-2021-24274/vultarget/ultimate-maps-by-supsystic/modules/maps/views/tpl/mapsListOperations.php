<div class="supsystic-actions-wrap">
	<a class="button button-table-action" id="editMap<?php echo $this->map['id']; ?>" href="<?php echo $this->editLink?>">
		<i class="fa fa-fw fa-pencil"></i>
	</a>
	<a class="button button-table-action" id="deleteMap<?php echo $this->map['id']; ?>" href="#" onclick="umsRemoveMapFromTblClick(<?php echo $this->map['id'];?>);">
		<i class="fa fa-fw fa-trash-o"></i>
	</a>
	<div id="umsRemoveElemLoader__<?php echo $this->map['id'];?>" style="display: inline-block;"></div>
</div>