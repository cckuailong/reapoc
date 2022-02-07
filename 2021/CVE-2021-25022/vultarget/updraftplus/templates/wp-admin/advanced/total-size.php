<?php
if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');
$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);
?>
<div class="advanced_tools total_size">
	<h3> <?php _e('Total (uncompressed) on-disk data:', 'updraftplus');?></h3>
	<p class="uncompressed-data">
		<em>
			<?php _e('N.B. This count is based upon what was, or was not, excluded the last time you saved the options.', 'updraftplus');?>
		</em>
	</p>
	<table>
		<?php
		foreach ($backupable_entities as $key => $info) {

			$sdescrip = preg_replace('/ \(.*\)$/', '', $info['description']);
			if (strlen($sdescrip) > 20 && isset($info['shortdescription'])) $sdescrip = $info['shortdescription'];
			
			$updraftplus_admin->settings_debugrow(ucfirst($sdescrip).':', '<span id="updraft_diskspaceused_'.$key.'"><em></em></span> <a href="'.UpdraftPlus::get_current_clean_url().'" class="count" data-type="' . $key . '" onclick="updraftplus_diskspace_entity(\''.$key.'\'); return false;">'.__('count', 'updraftplus').'</a>');
		}
		?>
	</table>
</div>