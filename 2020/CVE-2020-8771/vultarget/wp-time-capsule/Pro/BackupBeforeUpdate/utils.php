<?php

function wptc_mmb_get_error($error_object) {
	if (!is_wp_error($error_object)) {
		return $error_object != '' ? $error_object : '';
	} else {
		$errors = array();
		if (!empty($error_object->error_data)) {
			foreach ($error_object->error_data as $error_key => $error_string) {
				$errors[] = str_replace('_', ' ', ucfirst($error_key)) . ': ' . $error_string;
			}
		} elseif (!empty($error_object->errors)) {
			foreach ($error_object->errors as $error_key => $err) {
				$errors[] = 'Error: ' . str_replace('_', ' ', strtolower($error_key));
			}
		}
		return implode('<br />', $errors);
	}
}

function wptc_mmb_maintenance_mode($enable = false, $maintenance_message = '') {
	global $wp_filesystem;
	if (!$wp_filesystem) {
		initiate_filesystem_wptc();
		if (empty($wp_filesystem)) {
			send_response_wptc('FS_INIT_FAILED-014');
			return false;
		}
	}
	$maintenance_message .= '<?php $upgrading = ' . time() . '; ?>';

	$file = $wp_filesystem->abspath() . '.maintenance';
	if ($enable) {
		$wp_filesystem->delete($file);
		$wp_filesystem->put_contents($file, $maintenance_message, FS_CHMOD_FILE);
	} else {
		$wp_filesystem->delete($file);
	}
}