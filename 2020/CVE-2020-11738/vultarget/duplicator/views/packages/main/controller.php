<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPLICATOR_PLUGIN_PATH . '/classes/ui/class.ui.dialog.php');

$current_tab = isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'list';
$_GET['_wpnonce'] = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : null;

$txt_invalid_msg1 = __("An invalid request was made to this page.", 'duplicator');
$txt_invalid_msg2 = __("Please retry by going to the", 'duplicator');
$txt_invalid_lnk  = __("Packages Screen", 'duplicator');

switch ($current_tab) {
	case 'new1':
		if (!wp_verify_nonce($_GET['_wpnonce'], 'new1-package')) {
			die(printf("%s <br/>%s <a href='admin.php?page=duplicator'>%s</a>.", $txt_invalid_msg1, $txt_invalid_msg2, $txt_invalid_lnk));
		}
		break;
	case 'new2':
		if (!wp_verify_nonce($_GET['_wpnonce'], 'new2-package')) {
			die(printf("%s <br/>%s <a href='admin.php?page=duplicator'>%s</a>.", $txt_invalid_msg1, $txt_invalid_msg2, $txt_invalid_lnk));
		}
		break;
	case 'new3':
		if (!wp_verify_nonce($_GET['_wpnonce'], 'new3-package')) {
			die(printf("%s <br/>%s <a href='admin.php?page=duplicator'>%s</a>.", $txt_invalid_msg1, $txt_invalid_msg2, $txt_invalid_lnk));
		}
		break;
}
?>

<style>
	/*TOOLBAR TABLE*/
	table#dup-toolbar td {white-space: nowrap !important; padding:10px 0 0 0}
	table#dup-toolbar td .button {box-shadow: none !important;}
	table#dup-toolbar {width:100%; border:0 solid red; padding: 0; margin:8px 0 4px 0; height: 35px}
	table#dup-toolbar td:last-child {font-size:16px; width:100%; text-align: right; vertical-align: bottom;white-space:nowrap;}
	table#dup-toolbar td:last-child a {top:0; margin-top:10px; font-weight: bold; }
	table#dup-toolbar td:last-child span {display:inline-block; font-weight: bold; padding:0 5px 5px 5px; color:#000}
	hr.dup-toolbar-line {margin:2px 0 10px 0}
	
    /*WIZARD TABS */
    div#dup-wiz {padding:0px; margin:0;  }
    div#dup-wiz-steps {margin:10px 0px 0px 10px; padding:0px;  clear:both; font-size:13px; min-width:350px;}
    div#dup-wiz-title {padding:2px 0px 0px 0px; font-size:18px;}
    #dup-wiz a { position:relative; display:block; width:auto; min-width:55px; height:25px; margin-right:8px; padding:0px 10px 0px 10px; float:left; line-height:24px; 
		color:#000; background:#E4E4E4; border-radius:5px; letter-spacing:1px; border:1px solid #E4E4E4; text-align: center }
    #dup-wiz .active-step a {color:#fff; background:#ACACAC; font-weight: bold; border:1px solid #888}
    #dup-wiz .completed-step a {color:#E1E1E1; background:#BBBBBB; }

    /*Footer */
    div.dup-button-footer input {min-width: 105px}
    div.dup-button-footer {padding: 1px 10px 0px 0px; text-align: right}
</style>

<?php
	switch ($current_tab) {
		case 'list': 
			duplicator_header(__("Packages &raquo; All", 'duplicator'));
			include('packages.php');
			break;
		case 'new1': 
			duplicator_header(__("Packages &raquo; New", 'duplicator'));
			include('s1.setup1.php');
			break;
		case 'new2': 
			duplicator_header(__("Packages &raquo; New", 'duplicator'));
			include('s2.scan1.php');
			break;
		case 'new3': 
			duplicator_header(__("Packages &raquo; New", 'duplicator'));
			include('s3.build.php');
			break;
	}
?>