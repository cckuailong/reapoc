<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="cfsFormTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', CFS_LANG_CODE)?>">
					<button class="button" id="cfsFormRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', CFS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', CFS_LANG_CODE)?>">
					<input id="cfsFormTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', CFS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="cfsFormTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="cfsFormTbl"></table>
			<div id="cfsFormTblNav"></div>
			<div id="cfsFormTblEmptyMsg" style="display: none;">
				<h3><?php printf(__('You have no Forms for now. <a href="%s" style="font-style: italic;">Create</a> your Form!', CFS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>