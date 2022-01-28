<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="cfsFormContactsTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', CFS_LANG_CODE)?>">
					<button class="button" id="cfsFormContactsRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', CFS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', CFS_LANG_CODE)?>">
					<input id="cfsFormContactsTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', CFS_LANG_CODE)?>">
				</li>
				<li title="<?php _e('Forms to display contacts from', CFS_LANG_CODE)?>">
					<?php if(empty($this->allFormsForSelect)) { ?>
						<a href="<?php echo $this->addNewLink;?>" class="button"><?php _e('Create your first Form!', CFS_LANG_CODE)?></a>
					<?php } else { ?>
						<?php echo htmlCfs::selectbox('tbl_forms', array(
							'options' => $this->allFormsForSelect,
							'value' => $this->fid,
							'attrs' => 'class="chosen" id="cfsFormContactsTblSearchFidSelect"',
						))?>
					<?php }?>
				</li>
			</ul>
			<div id="cfsFormContactsTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="cfsFormContactsTbl"></table>
			<div id="cfsFormContactsTblNav"></div>
			<div id="cfsFormContactsTblEmptyMsg" style="display: none;">
				<h3><?php _e('You have no Contacts for now. When your visitors start submit your created Contact Forms, and if you enabled "Save contacts data" for them - their contacts will be displayed here.', CFS_LANG_CODE)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>
<div id="cfsFormContactViewWnd" style="display: none;" title="<?php _e('Contact Details', CFS_LANG_CODE)?>">
	<div id="cfsContactDetailsShell">

	</div>
	<div id="cfsFormContactFieldRowEx" class="supRow">
		<div class="supSm4 cfsFieldLabel"></div>
		<div class="supSm8 cfsFieldValue"></div>
	</div>
</div>
