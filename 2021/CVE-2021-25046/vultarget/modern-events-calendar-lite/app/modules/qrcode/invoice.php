<?php
/** no direct access **/
defined('MECEXEC') or die();

// PRO Version is required
if(!$this->getPRO()) return;

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(isset($settings['qrcode_module_status']) and !$settings['qrcode_module_status']) return;

$url = get_post_permalink($event->ID);
$file_name = 'qr_'.md5($url).'.png';

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] .DS. 'mec' .DS. $file_name;

$file = $this->getFile();
if(!$file->exists($file_path))
{
    if(!$file->exists(dirname($file_path)))
    {
        $folder = $this->getFolder();
        $folder->create(dirname($file_path));
    }

    $QRcode = $this->getQRcode();
    $QRcode->png($url, $file_path, 'L', 4, 2);
}

echo $file_path;