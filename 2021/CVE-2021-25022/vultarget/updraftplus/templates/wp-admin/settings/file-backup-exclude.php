<?php
if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');
?>
<div class="updraft_exclude_entity_container" data-include-backup-file="<?php echo esc_attr($key);?>">
	<?php
	if (!empty($include_exclude)) {
	$include_exclude_entities = explode(',', $include_exclude);
		foreach ($include_exclude_entities as $include_exclude_entity) {
		?>
			<div class="updraft_exclude_entity_wrapper">
				<input type="text" class="updraft_exclude_entity_field" name="<?php echo esc_attr('updraft_include_'.$key.'_exclude_entity[]');?>" value="<?php echo esc_attr($include_exclude_entity);?>" data-val="<?php echo esc_attr($include_exclude_entity);?>" data-include-backup-file="<?php echo esc_attr($key);?>" readonly="readonly"/><a href="#" class="updraft_exclude_entity_edit dashicons dashicons-edit" data-include-backup-file="<?php echo esc_attr($key);?>" title="<?php _e('Edit', 'updraftplus'); ?>"></a><a href="#" class="updraft_exclude_entity_update dashicons dashicons-yes" data-include-backup-file="<?php echo esc_attr($key);?>" style="display: none;" title="<?php _e('Confirm change', 'updraftplus'); ?>"></a><a href="#" class="updraft_exclude_entity_delete dashicons dashicons-no" data-include-backup-file="<?php echo esc_attr($key);?>" title="<?php _e('Delete', 'updraftplus'); ?>"></a>
			</div>
		<?php
		}
	}
	?>
</div>
<a href="#" class="updraft_add_exclude_item updraft_icon_link" data-include-backup-file="<?php echo esc_attr($key);?>" data-path="<?php echo esc_attr($path);?>" aria-label="<?php echo sprintf(__('Add an exclusion rule for %s', 'updraftplus'), esc_attr($key)); ?>"><span class="dashicons dashicons-plus"></span><?php echo __('Add an exclusion rule', 'updraftplus');?></a>
