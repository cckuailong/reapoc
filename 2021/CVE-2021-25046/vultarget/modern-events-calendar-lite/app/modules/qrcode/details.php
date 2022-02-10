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
if(isset($_REQUEST['occurrence'])) $url = $this->add_qs_var('occurrence', $_REQUEST['occurrence'], $url);

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

$image_url = $upload_dir['baseurl'].'/mec/'.$file_name;
?>
<div class="mec-qrcode-details mec-frontbox">
    <img src="<?php echo $image_url; ?>" width="120" height="120" alt="<?php echo __('QR Code', 'modern-events-calendar-lite'); ?>" />
</div>