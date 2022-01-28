<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="umsMapsTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Search', UMS_LANG_CODE)?>">
					<input id="umsMapsTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', UMS_LANG_CODE)?>">
				</li>
				<li title="<?php _e('Clone selected', UMS_LANG_CODE)?>">
					<button class="button" id="umsMapsCloneGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-clone"></i>
						<?php _e('Clone selected', UMS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Delete selected', UMS_LANG_CODE)?>">
					<button class="button" id="umsMapsRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', UMS_LANG_CODE)?>
					</button>
				</li>
				<?php /*We don't need this feature for now*/ ?>
				<?php /*?><li title="<?php _e('Clear All')?>">
					<button class="button" id="umsMapsClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', UMS_LANG_CODE)?>
					</button>
				</li><?php */?>
			</ul>
			<div id="umsMapsTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="umsMapsTbl"></table>
			<div id="umsMapsTblNav"></div>
			<div id="umsMapsTblEmptyMsg" style="display: none;">
				<h3><?php printf(__("You have no Maps for now. <a href='%s' style='font-style: italic;'>Create</a> your first Map!", UMS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>