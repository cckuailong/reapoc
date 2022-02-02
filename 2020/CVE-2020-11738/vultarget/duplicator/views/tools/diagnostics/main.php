<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<style>
	div.success {color:#4A8254}
	div.failed {color:red}
	table.dup-reset-opts td:first-child {font-weight: bold}
	table.dup-reset-opts td {padding:10px}
	button.dup-fixed-btn {min-width: 150px; text-align: center}
	div#dup-tools-delete-moreinfo {display: none; padding: 5px 0 0 20px; border:1px solid silver; background-color: #fff; border-radius: 5px; padding:10px; margin:5px; width:750px }
	div.dup-alert-no-files-msg {padding:10px 0 10px 0}
	div.dup-alert-secure-note {font-style: italic; max-width:800px; padding:15px 0 20px 0}

	div#message {margin:0px 0px 10px 0px}
	div#dup-server-info-area { padding:10px 5px;  }
	div#dup-server-info-area table { padding:1px; background:#dfdfdf;  -webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px; width:100% !important; box-shadow:0 8px 6px -6px #777; }
	div#dup-server-info-area td, th {padding:3px; background:#fff; -webkit-border-radius:2px;-moz-border-radius:2px;border-radius:2px;}
	div#dup-server-info-area tr.h img { display:none; }
	div#dup-server-info-area tr.h td{ background:none; }
	div#dup-server-info-area tr.h th{ text-align:center; background-color:#efefef;  }
	div#dup-server-info-area td.e{ font-weight:bold }
	td.dup-settings-diag-header {background-color:#D8D8D8; font-weight: bold; border-style: none; color:black}
	.widefat th {font-weight:bold; }
	.widefat td {padding:2px 2px 2px 8px}
	.widefat td:nth-child(1) {width:10px;}
	.widefat td:nth-child(2) {padding-left: 20px; width:100% !important}
	textarea.dup-opts-read {width:100%; height:40px; font-size:12px}
	div.lite-sub-tabs {padding: 10px 0 10px 0; font-size: 14px}
</style>


<?php
$action_response = null;

$ctrl_ui = new DUP_CTRL_UI();
$ctrl_ui->setResponseType('PHP');
$data = $ctrl_ui->GetViewStateList();

$ui_css_srv_panel   = (isset($data->payload['dup-settings-diag-srv-panel'])  && $data->payload['dup-settings-diag-srv-panel'])   ? 'display:block' : 'display:none';
$ui_css_opts_panel  = (isset($data->payload['dup-settings-diag-opts-panel']) && $data->payload['dup-settings-diag-opts-panel'])  ? 'display:block' : 'display:none';

$section        = isset($_GET['section']) ? $_GET['section'] : 'info';
$txt_diagnostic = __('Information', 'duplicator');
$txt_log        = __('Logs', 'duplicator');
$txt_support    = __('Support', 'duplicator');;
$tools_url      = 'admin.php?page=duplicator-tools&tab=diagnostics';

switch ($section) {
    case 'info':
        echo "<div class='lite-sub-tabs'><b>".esc_html($txt_diagnostic)."</b> &nbsp;|&nbsp; <a href='".esc_url($tools_url."&section=log")."'>".esc_html($txt_log)."</a> &nbsp;|&nbsp; <a href='".esc_url($tools_url."&section=support")."'>".esc_html($txt_support)."</a></div>";
        include(dirname(__FILE__) . '/information.php');
        break;

    case 'log':
        echo "<div class='lite-sub-tabs'><a href='".esc_url($tools_url."&section=info")."'>".esc_html($txt_diagnostic)."</a>  &nbsp;|&nbsp;<b>".esc_html($txt_log)."</b>  &nbsp;|&nbsp; <a href='".esc_url($tools_url."&section=support")."'>".esc_html($txt_support)."</a></div>";
        include(dirname(__FILE__) . '/logging.php');
        break;

    case 'support':
        echo "<div class='lite-sub-tabs'><a href='".esc_url($tools_url."&section=info")."'>".esc_html($txt_diagnostic)."</a> &nbsp;|&nbsp; <a href='".esc_url($tools_url."&section=log")."'>".esc_html($txt_log)."</a> &nbsp;|&nbsp; <b>".esc_html($txt_support)."</b> </div>";
        include(dirname(__FILE__) . '/support.php');
        break;
}
?>