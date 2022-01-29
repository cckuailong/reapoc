<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_whats_new.php'); else {
$html = file_get_contents(RM_EXTERNAL_DIR.'whatisnew/index.html');
$ext_dir_as_url = plugin_dir_url(RM_EXTERNAL_DIR).'external/';

$html = str_replace("src='images/", "src='".$ext_dir_as_url.'whatisnew/images/', $html);
$html = str_replace('src="images/', 'src="'.$ext_dir_as_url.'whatisnew/images/', $html);
$html = str_replace('3.7.8.0', RM_PLUGIN_VERSION, $html);
echo $html;
}