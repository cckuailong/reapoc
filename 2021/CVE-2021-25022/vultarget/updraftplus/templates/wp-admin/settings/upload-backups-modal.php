<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<div id="updraft-upload-modal" title="UpdraftPlus - <?php _e('Upload backup', 'updraftplus');?>">
	<p><?php _e("Select the remote storage destinations you want to upload this backup set to", 'updraftplus');?>:</p>
	<form id="updraft_upload_form" method="post">
		<fieldset>
			<input type="hidden" name="backup_timestamp" value="0" id="updraft_upload_timestamp">
			<input type="hidden" name="backup_nonce" value="0" id="updraft_upload_nonce">

			<?php
				global $updraftplus;
				
				$service = (array) $updraftplus->just_one($updraftplus->get_canonical_service_list());

				foreach ($service as $value) {
					if ('' == $value) continue;
					echo '<input class="updraft_remote_storage_destination" id="updraft_remote_'.esc_attr($value).'" checked="checked" type="checkbox" name="updraft_remote_storage_destination_'. esc_attr($value) . '" value="'.esc_attr($value).'"> <label for="updraft_remote_'.esc_attr($value).'">'.$updraftplus->backup_methods[$value].' <span style="display: none">('.__('already uploaded', 'updraftplus').')</span></label><br>';
				}
			?>
		</fieldset>
	</form>
	<p id="updraft-upload-modal-error"></p>
</div>
