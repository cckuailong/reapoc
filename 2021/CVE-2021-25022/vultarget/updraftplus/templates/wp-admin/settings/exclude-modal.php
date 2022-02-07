<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>
<div id="updraft_exclude_modal" title="UpdraftPlus - <?php _e('Exclude files/directories', 'updraftplus');?>">
	<input type="hidden" id="updraft_exclude_modal_for" value=""/>
	<input type="hidden" id="updraft_exclude_modal_path" value=""/>
	<div id="updraft_exclude_modal_main">
		<p><?php _e("Select a way to exclude files or directories from the backup", 'updraftplus');?>:</p>		
		<ol class="updraft_exclude_actions_list">
			<li>
				<a href="#" class="updraft-exclude-link" data-panel="file-dir"><?php _e('File/directory', 'updraftplus');?></a>
			</li>
			<li>
				<a href="#" class="updraft-exclude-link" data-panel="extension"><?php _e('All files with this extension', 'updraftplus');?></a>
			</li>
			<li>
				<a href="#" class="updraft-exclude-link" data-panel="begin-with"><?php _e('All files beginning with given characters', 'updraftplus');?></a>
			</li>
			<li>
				<a href="#" class="updraft-exclude-link" data-panel="contain-clause"><?php _e('Files/Directories containing the given characters in their names', 'updraftplus');?></a>
			</li>
		</ol>
	</div>
	<?php $panel = 'file-dir';?>
	<div class="updraft-exclude-panel updraft-hidden" data-panel="<?php echo $panel;?>" style="display:none;">
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-heading.php', false, array('title' => __('File/directory', 'updraftplus')));
		?>
		<div class="updraft-add-dir-file-cont">
			<div id="updraft_exclude_jstree_info_container" class="updraft-jstree-info-container">
				<p>
					<span id="updraft_exclude_jstree_path_text">
						<?php _e('Select a file/folder which you would like to exclude', 'updraftplus'); ?>
					</span>
				</p>
			</div>
			<div id="updraft_exclude_files_jstree_container">
				<div id="updraft_exclude_files_folders_jstree" class="updraft_jstree"></div>
			</div>
			<?php
			$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-submit.php', false, array('panel' => $panel));
			?>
		</div>
	</div>
	
	<?php $panel = 'extension';?>
	<div class="updraft-exclude-panel updraft-hidden" data-panel="<?php echo $panel;?>" style="display:none;">
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-heading.php', false, array('title' => __('All files with this extension', 'updraftplus')));
		?>
		<label for="updraft_exclude_extension_field"><?php _e('All files with this extension', 'updraftplus');?>: </label>
		<input type="text" name="updraft_exclude_extension_field" id="updraft_exclude_extension_field" size="25" placeholder="<?php _e('Type an extension like zip', 'updraftplus');?>" />
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-submit.php', false, array('panel' => $panel));
		?>
	</div>
	
	<?php $panel = 'begin-with';?>
	<div class="updraft-exclude-panel updraft-hidden" data-panel="<?php echo $panel;?>" style="display:none;">
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-heading.php', false, array('title' => __('All files beginning with these characters', 'updraftplus')));
		?>
		<label for="updraft_exclude_prefix_field"><?php _e('All files beginning with these characters', 'updraftplus');?>: </label>
		<input type="text" name="updraft_exclude_prefix_field" id="updraft_exclude_prefix_field" size="25" placeholder="<?php _e('Type a file prefix', 'updraftplus');?>" />
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-submit.php', false, array('panel' => $panel));
		?>
	</div>

	<?php $panel = 'contain-clause';?>
	<div class="updraft-exclude-panel updraft-hidden" data-panel="<?php echo $panel;?>" style="display:none;">
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-heading.php', false, array('title' => __('All files/directories containing the given characters in their names', 'updraftplus')));
		?>
		<div id="updraft_exclude_jstree_info_container" class="updraft-jstree-info-container">
			<p>
				<span id="updraft_exclude_jstree_path_text">
					<?php _e('Select the folder in which the files or sub-directories you would like to exclude are located', 'updraftplus'); ?>
				</span>
			</p>
		</div>
		<div id="updraft_exclude_files_jstree_container">
				<div id="updraft_exclude_files_folders_wildcards_jstree" class="updraft_jstree"></div>
		</div>
		<label for="updraft_exclude_prefix_field" class="contain-clause-sub-label"><?php _e('All files/directories containing ', 'updraftplus');?></label>
		<div class="clause-input-container">
			<input class="wildcards-input" type="text" size="25" placeholder="<?php _e('these characters', 'updraftplus');?>" />
			<select class="clause-options wildcards-input">
				<option value="beginning"><?php _e('at the beginning of their names', 'updraftplus');?></option>
				<option value="middle"><?php _e('anywhere in their names', 'updraftplus');?></option>
				<option value="end"><?php _e('at the end of their names', 'updraftplus');?></option>
			</select>
		</div>
		<?php
		$updraftplus_admin->include_template('wp-admin/settings/exclude-settings-modal/exclude-panel-submit.php', false, array('panel' => $panel, 'text_button' => __('Add exclusion rule', 'updraftplus')));
		?>
	</div>
</div>