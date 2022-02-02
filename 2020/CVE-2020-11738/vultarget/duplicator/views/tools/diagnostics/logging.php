<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
require_once(DUPLICATOR_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/views/inc.header.php');

function _duplicatorSortFiles($a,$b) {
	return filemtime($b) - filemtime($a);
}

$logs = glob(DUPLICATOR_SSDIR_PATH . '/*.log') ;
if ($logs != false && count($logs))  {
	usort($logs, '_duplicatorSortFiles');
	@chmod(DUP_Util::safePath($logs[0]), 0644);
}

$logname	 = (isset($_GET['logname'])) ? trim(sanitize_text_field($_GET['logname'])) : "";
$refresh	 = (isset($_POST['refresh']) && $_POST['refresh'] == 1) ? 1 : 0;
$auto		 = (isset($_POST['auto'])    && $_POST['auto'] == 1)    ? 1 : 0;

//Check for invalid file
if (!empty($logname))
{
	$validFiles = array_map('basename', $logs);
	if (validate_file($logname, $validFiles) > 0) {
		unset($logname);
	}
	unset($validFiles);
}

if (!isset($logname) || !$logname) {
	$logname  = (count($logs) > 0) ? basename($logs[0]) : "";
}

$logurl	 = get_site_url(null, '', is_ssl() ? 'https' : 'http') . '/' . DUPLICATOR_SSDIR_NAME . '/' . $logname;
$logfound = (strlen($logname) > 0) ? true :false;
?>

<style>
    div#dup-refresh-count {display: inline-block}
    table#dup-log-panels {width:100%; }
    td#dup-log-panel-left {width:75%;}
    td#dup-log-panel-left div.name {float:left; margin: 0px 0px 5px 5px;}
    td#dup-log-panel-left div.opts {float:right;}
    td#dup-log-panel-right {vertical-align: top; padding-left:15px; max-width: 375px}
    #dup-log-content {
        padding:5px; 
        background: #fff; 
        min-height:500px; 
        width: calc(100vw - 630px);; 
        border:1px solid silver;
        overflow:scroll; 
        word-wrap: break-word; 
        margin:0;
        line-height: 2;
    }

    /* OPTIONS */
    div.dup-log-hdr {font-weight: bold; font-size:16px; padding:2px; }
    div.dup-log-hdr small{font-weight:normal; font-style: italic}
    div.dup-log-file-list {font-family:monospace;}
    div.dup-log-file-list a, span.dup-log{display: inline-block; white-space: nowrap; text-overflow: ellipsis; max-width: 375px; overflow:hidden}
    div.dup-log-file-list span {color:green}
    div.dup-opts-items {border:1px solid silver; background: #efefef; padding: 5px; border-radius: 4px; margin:2px 0px 10px -2px;}
    label#dup-auto-refresh-lbl {display: inline-block;}
</style>

<script>
jQuery(document).ready(function($)
{
	Duplicator.Tools.FullLog = function() {
		var $panelL = $('#dup-log-panel-left');
		var $panelR = $('#dup-log-panel-right');

		if ($panelR.is(":visible") ) {
			$panelR.hide(400);
			$panelL.css({width: '100%'});
		} else {
			$panelR.show(200);
			$panelL.css({width: '75%'});
		}
	}

	Duplicator.Tools.Refresh = function() {
		$('#refresh').val(1);
		$('#dup-form-logs').submit();
	}

	Duplicator.Tools.RefreshAuto = function() {
		if ( $("#dup-auto-refresh").is(":checked")) {
			$('#auto').val(1);
			startTimer();
		}  else {
			$('#auto').val(0);
		}
	}

	Duplicator.Tools.GetLog = function(log) {
		window.location =  log;
	}

	Duplicator.Tools.WinResize = function() {
		var height = $(window).height() - 225;
		$("#dup-log-content").css({height: height + 'px'});
	}

    Duplicator.Tools.readLogfile = function() {
        $.get('<?php echo esc_url($logurl); ?>', function(data) {
            $('#dup-log-content').text(data);
        }, 'text');
    };

	var duration = 10;
	var count = duration;
	var timerInterval;
	function timer() {
		count = count - 1;
		$("#dup-refresh-count").html(count.toString());
		if (! $("#dup-auto-refresh").is(":checked")) {
			 clearInterval(timerInterval);
			 $("#dup-refresh-count").text(count.toString().trim());
			 return;
		}

		if (count <= 0) {
			count = duration + 1;
			Duplicator.Tools.Refresh();
		}
	}

	function startTimer() {
		timerInterval = setInterval(timer, 1000);
	}

	//INIT Events
	$(window).resize(Duplicator.Tools.WinResize);
	$('#dup-options').click(Duplicator.Tools.FullLog);
	$("#dup-refresh").click(Duplicator.Tools.Refresh);
	$("#dup-auto-refresh").click(Duplicator.Tools.RefreshAuto);
	$("#dup-refresh-count").html(duration.toString());

    // READ LOG FILE
    Duplicator.Tools.readLogfile();

	//INIT
	Duplicator.Tools.WinResize();
	<?php if ($refresh)  :	?>
		//Scroll to Bottom
		$("#dup-log-content").load(function () {
			var $contents = $('#dup-log-content').contents();
			$contents.scrollTop($contents.height());
		});
		<?php if ($auto)  :	?>
			$("#dup-auto-refresh").prop('checked', true);
			Duplicator.Tools.RefreshAuto();
		<?php endif; ?>
	<?php endif; ?>
});
</script>

<form id="dup-form-logs" method="post" action="">
<input type="hidden" id="refresh" name="refresh" value="<?php echo ($refresh) ? 1 : 0 ?>" />
<input type="hidden" id="auto" name="auto" value="<?php echo ($auto) ? 1 : 0 ?>" />

<?php if (! $logfound)  :	?>
	<div style="padding:20px">
		<h2><?php esc_html_e("Log file not found or unreadable", 'duplicator') ?>.</h2>
		<?php esc_html_e("Try to create a package, since no log files were found in the snapshots directory with the extension *.log", 'duplicator') ?>.<br/><br/>
		<?php esc_html_e("Reasons for log file not showing", 'duplicator') ?>: <br/>
		- <?php esc_html_e("The web server does not support returning .log file extentions", 'duplicator') ?>. <br/>
		- <?php esc_html_e("The snapshots directory does not have the correct permissions to write files.  Try setting the permissions to 755", 'duplicator') ?>. <br/>
		- <?php esc_html_e("The process that PHP runs under does not have enough permissions to create files.  Please contact your hosting provider for more details", 'duplicator') ?>. <br/>
	</div>
<?php else: ?>
	<table id="dup-log-panels">
		<tr>
			<td id="dup-log-panel-left">
				<div class="name">
					<i class='fa fa-list-alt'></i> <b><?php echo basename($logurl); ?></b> &nbsp; | &nbsp;
					<i style="cursor: pointer"
						data-tooltip-title="<?php esc_attr_e("Host Recommendation:", 'duplicator'); ?>"
						data-tooltip="<?php esc_attr_e('Duplicator recommends going with the high performance pro plan or better from our recommended list', 'duplicator'); ?>">
						 <i class="far fa-lightbulb" aria-hidden="true"></i>
							<?php
								printf("%s <a target='_blank' href='//snapcreek.com/wordpress-hosting/'>%s</a> %s",
								esc_html__("Consider our recommended", 'duplicator'),
								esc_html__("host list", 'duplicator'),
								esc_html__("if you’re unhappy with your current provider", 'duplicator'));
							?>
					</i>
				</div>
				<div class="opts"><a href="javascript:void(0)" id="dup-options"><?php esc_html_e("Options", 'duplicator') ?> <i class="fa fa-angle-double-right"></i></a> &nbsp;</div>
				<br style="clear:both" />
				<pre id="dup-log-content"></pre>
			</td>
			<td id="dup-log-panel-right">
				<h2><?php esc_html_e("Options", 'duplicator') ?> </h2>
				<div class="dup-opts-items">
					<input type="button" class="button button-small" id="dup-refresh" value="<?php esc_attr_e("Refresh", 'duplicator') ?>" /> &nbsp;
					<input type='checkbox' id="dup-auto-refresh" style="margin-top:1px" />
					<label id="dup-auto-refresh-lbl" for="dup-auto-refresh">
						<?php esc_attr_e("Auto Refresh", 'duplicator') ?>
						[<div id="dup-refresh-count"></div>]
					</label>
				</div>

				<div class="dup-log-hdr">
					<?php esc_html_e("Package Logs", 'duplicator') ?>
					<small><?php esc_html_e("Top 20", 'duplicator') ?></small>
				</div>

				<div class="dup-log-file-list">
					<?php
						$count=0;
						$active = basename($logurl);
						foreach ($logs as $log) {
							$time = date('m/d/y h:i:s', filemtime($log));
							$name = basename($log);
							$url  = '?page=duplicator-tools&tab=diagnostics&section=log&logname=' . esc_html($name);
							echo ($active == $name)
								? "<span class='dup-log' title='".esc_attr($name)."'>".esc_html($time)."-".esc_html($name)."</span>"
								: "<a href='javascript:void(0)'  title='".esc_attr($name)."' onclick='Duplicator.Tools.GetLog(\"".esc_js($url)."\")'>".esc_html($time)."-".esc_html($name)."</a>";
							if ($count > 20) break;
						}
					?>
				</div>
			</td>
		</tr>
	</table>
<?php endif; ?>
</form>
