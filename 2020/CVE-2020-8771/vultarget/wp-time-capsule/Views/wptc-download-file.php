<?php

if (empty($wptc_file_path) || !file_exists($wptc_file_path)) {
	return ;
}

$relative_path       = wptc_replace_abspath($wptc_file_path, false);
$plugin_dir          = WPTC_PLUGIN_DIR . 'wp-tcapsule-bridge/upload/php/files';
$relative_plugin_dir = wptc_replace_abspath($plugin_dir, false);

if (dirname($relative_path) !== $relative_plugin_dir ) {
	wptc_log(array(),'-----------filename not match----------------');
	return ;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($wptc_file_path).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($wptc_file_path));
readfile($wptc_file_path);
exit;

?>
