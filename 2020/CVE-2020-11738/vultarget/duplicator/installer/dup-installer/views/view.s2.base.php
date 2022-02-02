<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

require_once($GLOBALS['DUPX_INIT'] . '/classes/config/class.archive.config.php');

//-- START OF VIEW STEP 2
$_POST['dbcharset'] = isset($_POST['dbcharset']) ? trim($_POST['dbcharset']) : $GLOBALS['DBCHARSET_DEFAULT'];
$_POST['dbcollate'] = isset($_POST['dbcollate']) ? trim($_POST['dbcollate']) : $GLOBALS['DBCOLLATE_DEFAULT'];
$_POST['exe_safe_mode'] = (isset($_POST['exe_safe_mode'])) ? DUPX_U::sanitize_text_field($_POST['exe_safe_mode']) : 0;
$is_dbtest_mode = isset($_POST['dbonlytest']) ? 1 : 0;

if (isset($_POST['logging'])) {
	$post_logging = DUPX_U::sanitize_text_field($_POST['logging']);
	$_POST['logging'] = trim($post_logging);
} else {
	$_POST['logging'] = 1;
}

$cpnl_supported =  DUPX_U::$on_php_53_plus ? true : false;
?>

<form id='s2-input-form' method="post" class="content-form"  autocomplete="off" data-parsley-validate="true" data-parsley-excluded="input[type=hidden], [disabled], :hidden">

	<?php if ($is_dbtest_mode) : ?>
		<div class="hdr-main">Database Validation	</div>
	<?php else : ?>
		<div class="dupx-logfile-link">
			<?php DUPX_View_Funcs::installerLogLink(); ?>
		</div>
		<div class="hdr-main">
			Step <span class="step">2</span> of 4: Install Database
			<div class="sub-header">This step will install the database from the archive.</div>
		</div>
		<div class="s2-btngrp">
			<input id="s2-basic-btn" type="button" value="Basic" class="active" onclick="DUPX.togglePanels('basic')" />
			<input id="s2-cpnl-btn" type="button" value="cPanel" class="in-active" onclick="DUPX.togglePanels('cpanel')" />
		</div>
	<?php endif; ?>

	<!--  POST PARAMS -->
	<div class="dupx-debug">
		<i>Step 2 - Page Load</i>
		<input type="hidden" name="view" value="step2" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step2'); ?>">
		<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
		<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
		<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
		<input type="hidden" name="logging" id="logging" value="<?php echo DUPX_U::esc_attr($_POST['logging']); ?>" />
		<input type="hidden" name="dbcolsearchreplace"/>
		<input type="hidden" name="ctrl_action" value="ctrl-step2" />
		<input type="hidden" name="ctrl_csrf_token" value="<?php echo DUPX_CSRF::generate('ctrl-step2'); ?>">
		<input type="hidden" name="view_mode" id="s2-input-form-mode" />
		<input type="hidden" name="exe_safe_mode" id="exe-safe-mode"  value="<?php echo DUPX_U::esc_attr($_POST['exe_safe_mode']); ?>"/>
		<textarea name="dbtest-response" id="debug-dbtest-json"></textarea>
	</div>

	<!-- DATABASE CHECKS -->
	<?php require_once('view.s2.dbtest.php');	?>

	<!-- BASIC TAB -->
	<div id="s2-basic-pane">
		<?php require_once('view.s2.basic.php'); ?>
	</div>

	<!-- CPANEL TAB -->
	<div id="s2-cpnl-pane" style="display: none">
		<?php require_once('view.s2.cpnl.lite.php'); ?>
	</div>
</form>


<!-- CONFIRM DIALOG -->
<div id="dialog-confirm" title="Install Confirmation" style="display:none">
	<div style="padding: 10px 0 25px 0">
		<b>Run installer with these settings?</b>
	</div>

	<b>Database Settings:</b><br/>
	<table style="margin-left:20px">
		<tr>
			<td><b>Server:</b></td>
			<td><i id="dlg-dbhost"></i></td>
		</tr>
		<tr>
			<td><b>Name:</b></td>
			<td><i id="dlg-dbname"></i></td>
		</tr>
		<tr>
			<td><b>User:</b></td>
			<td><i id="dlg-dbuser"></i></td>
		</tr>
	</table>
	<br/><br/>

	<small><i class="fa fa-exclamation-triangle fa-sm"></i> WARNING: Be sure these database parameters are correct! Entering the wrong information WILL overwrite an existing database.
		Make sure to have backups of all your data before proceeding.</small><br/>
</div>


<!-- =========================================
VIEW: STEP 2 - AJAX RESULT
Auto Posts to view.step3.php  -->
<form id='s2-result-form' method="post" class="content-form" style="display:none" autocomplete="off">

	<div class="dupx-logfile-link"><?php DUPX_View_Funcs::installerLogLink(); ?></div>
	<div class="hdr-main">
		Step <span class="step">2</span> of 4: Install Database
		<div class="sub-header">This step will install the database from the archive.</div>
	</div>

	<!--  POST PARAMS -->
	<div class="dupx-debug">
		<i>Step 2 - AJAX Response</i>
		<input type="hidden" name="view" value="step3" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step3'); ?>">
		<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
		<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
	<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
		<input type="hidden" name="logging" id="ajax-logging" />
		<input type="hidden" name="dbaction" id="ajax-dbaction" />
		<input type="hidden" name="dbhost" id="ajax-dbhost" />
		<input type="hidden" name="dbname" id="ajax-dbname" />
		<input type="hidden" name="dbuser" id="ajax-dbuser" />
		<input type="hidden" name="dbpass" id="ajax-dbpass" />
		<input type="hidden" name="dbcharset" id="ajax-dbcharset" />
		<input type="hidden" name="dbcollate" id="ajax-dbcollate" />
		<input type="hidden" name="exe_safe_mode" id="ajax-exe-safe-mode" />
		<input type="hidden" name="config_mode" value="<?php echo DUPX_U::esc_attr($_POST['config_mode']); ?>" />
		<input type="hidden" name="json"   id="ajax-json" />
		<input type='submit' value='manual submit'>
	</div>

	<!--  PROGRESS BAR -->
	<div id="progress-area">
		<div style="width:500px; margin:auto">
			<div style="font-size:1.7em; margin-bottom:20px"><i class="fas fa-circle-notch fa-spin"></i> Installing Database</div>
			<div id="progress-bar"></div>
			<h3> Please Wait...</h3><br/><br/>
			<i>Keep this window open during the creation process.</i><br/>
			<i>This can take several minutes.</i>
		</div>
	</div>

	<!--  AJAX SYSTEM ERROR -->
	<div id="ajaxerr-area" style="display:none">
		<p>Please try again an issue has occurred.</p>
		<div style="padding: 0px 10px 10px 0px;">
			<div id="ajaxerr-data">An unknown issue has occurred with the file and database setup process.  Please see the <?php DUPX_View_Funcs::installerLogLink(); ?> file for more details.</div>
			<div style="text-align:center; margin:10px auto 0px auto">
				<input type="button" onclick="$('#s2-result-form').hide();  $('#s2-input-form').show(200);  $('#dbchunk_retry').val(0);" value="&laquo; Try Again" class="default-btn" /><br/><br/>
				<i style='font-size:11px'>See online help for more details at <a href='https://snapcreek.com/' target='_blank'>snapcreek.com</a></i>
			</div>
		</div>
	</div>
</form>

<script>
	/**
	 *  Toggles the cpanel Login area  */
	DUPX.togglePanels = function (pane)
	{
		$('#s2-basic-pane, #s2-cpnl-pane').hide();
		$('#s2-basic-btn, #s2-cpnl-btn').removeClass('active in-active');
		var cpnlSupport = <?php echo var_export($cpnl_supported); ?>

		if (pane == 'basic') {
			$('#s2-input-form-mode').val('basic');
			$('#s2-basic-pane').show();
			$('#s2-basic-btn').addClass('active');
			$('#s2-cpnl-btn').addClass('in-active');
			if (! cpnlSupport) {
				$('#s2-opts-hdr-basic, div.footer-buttons').show();
			}
		} else {
			$('#s2-input-form-mode').val('cpnl');
			$('#s2-cpnl-pane').show();
			$('#s2-cpnl-btn').addClass('active');
			$('#s2-basic-btn').addClass('in-active');
			if (! cpnlSupport) {
				$('#s2-opts-hdr-cpnl, div.footer-buttons').hide();
			}
		}
	}


	/**
	 * Open an in-line confirm dialog*/
	DUPX.confirmDeployment= function ()
	{
        var dbhost = $("#dbhost").val();
		var dbname = $("#dbname").val();
		var dbuser = $("#dbuser").val();
		var dbchunk = $("#dbchunk").val();

		var $formInput = $('#s2-input-form');
		$formInput.parsley().validate();
		if (!$formInput.parsley().isValid()) {
			return;
		}

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height: "auto",
			width: 550,
			modal: true,
			position: { my: 'top', at: 'top+150' },
			buttons: {
				"OK": function() {
					DUPX.runDeployment();
					$(this).dialog("close");
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			}
		});

		$('#dlg-dbhost').html(dbhost);
		$('#dlg-dbname').html(dbname);
		$('#dlg-dbuser').html(dbuser);
	}

	/**
	 * Performs Ajax post to extract files and create db
	 * Timeout (10000000 = 166 minutes) */
	DUPX.runDeployment = function (data)
	{
		var $formInput = $('#s2-input-form');
		var $formResult = $('#s2-result-form');
		var local_data = data;
        var dbhost = $("#dbhost").val();
        var dbname = $("#dbname").val();
        var dbuser = $("#dbuser").val();
        var dbchunk = $("#dbchunk").is(':checked');

		if(local_data === undefined && dbchunk == true){
		    local_data = {
		        continue_chunking: dbchunk == true,
                pos: 0,
                pass: 0,
                first_chunk: 1,
            };
        }else if(!dbchunk){
		    local_data = {};
        }
        var new_data = (local_data !== undefined) ? '&'+$.param(local_data) : '';

		$.ajax({
			type: "POST",
			timeout: 10000000,
			url: window.location.href,
			data: $formInput.serialize()+new_data,
			beforeSend: function () {
				DUPX.showProgressBar();
				$formInput.hide();
				$formResult.show();
			},
			success: function (respData, textStatus, xhr) {
				try {
					var data = DUPX.parseJSON(respData);
				} catch(err) {
					console.error(err);
					console.error('JSON parse failed for response data: ' + respData);
					var status  = "<b>Server Code:</b> "	+ xhr.status		+ "<br/>";
					status += "<b>Status:</b> "			+ xhr.statusText	+ "<br/>";
					status += "<b>Response:</b> "		+ xhr.responseText  + "<hr/>";

					if(textStatus && textStatus.toLowerCase() == "timeout" || textStatus.toLowerCase() == "service unavailable") {
						status += "<b>Recommendation:</b><br/>";
						status += "To resolve this problem please follow the instructions showing <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-100-q'>in the FAQ</a>.<br/><br/>";
					}
					else if((xhr.status == 403) || (xhr.status == 500)) {
						status += "<b>Recommendation</b><br/>";
						status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-120-q'>this section</a> of the Technical FAQ for possible resolutions.<br/><br/>"
					}
					else if(xhr.status == 0) {
						status += "<b>Recommendation</b><br/>";
						status += "This may be a server timeout and performing a 'Manual Extract' install can avoid timeouts. See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?reload=1#faq-installer-015-q'>this section</a> of the FAQ for a description of how to do that.<br/><br/>"
					} else {
						status += "<b>Additional Troubleshooting Tips:</b><br/> ";
						status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/'>Help Resources</a><br/>";
						status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/'>Technical FAQ</a>";
					}

					$('#ajaxerr-data').html(status);
					DUPX.hideProgressBar();
					return false;
				}

			    if(local_data.continue_chunking){
                    DUPX.runDeployment(data);
                    return;
                }
				if (typeof (data) != 'undefined' && data.pass == 1)
				{
					$("#ajax-dbaction").val($("#dbaction").val());
					$("#ajax-dbhost").val(dbhost);
					$("#ajax-dbname").val(dbname);
					$("#ajax-dbuser").val(dbuser);
					$("#ajax-dbpass").val($("#dbpass").val());

					//Advanced Opts
					$("#ajax-dbcharset").val($("#dbcharset").val());
					$("#ajax-dbcollate").val($("#dbcollate").val());
					$("#ajax-logging").val($("#logging").val());
					$("#ajax-exe-safe-mode").val($("#exe-safe-mode").val());
					$("#ajax-json").val(escape(JSON.stringify(data)));

					<?php if (! $GLOBALS['DUPX_DEBUG']) : ?>
						setTimeout(function () {$formResult.submit();}, 1000);
					<?php endif; ?>
					$('#progress-area').fadeOut(700);
				} else {
					if (data.error_message) {
						$('#ajaxerr-data').html(data.error_message);
					}
					DUPX.hideProgressBar();
				}
			},
			error: function (xhr, textStatus) {
				var status  = "<b>Server Code:</b> "	+ xhr.status		+ "<br/>";
				status += "<b>Status:</b> "			+ xhr.statusText	+ "<br/>";
				status += "<b>Response:</b> "		+ xhr.responseText  + "<hr/>";

				if(textStatus && textStatus.toLowerCase() == "timeout" || textStatus.toLowerCase() == "service unavailable") {
					status += "<b>Recommendation:</b><br/>";
					status += "To resolve this problem please follow the instructions showing <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-100-q'>in the FAQ</a>.<br/><br/>";
				}
				else if((xhr.status == 403) || (xhr.status == 500)) {
					status += "<b>Recommendation</b><br/>";
					status += "See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-120-q'>this section</a> of the Technical FAQ for possible resolutions.<br/><br/>"
				}
				else if(xhr.status == 0) {
					status += "<b>Recommendation</b><br/>";
					status += "This may be a server timeout and performing a 'Manual Extract' install can avoid timeouts. See <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/?reload=1#faq-installer-015-q'>this section</a> of the FAQ for a description of how to do that.<br/><br/>"
				} else {
					status += "<b>Additional Troubleshooting Tips:</b><br/> ";
					status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/'>Help Resources</a><br/>";
					status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/'>Technical FAQ</a>";
				}

				$('#ajaxerr-data').html(status);
				DUPX.hideProgressBar();
			}
		});
	};

	//DOCUMENT LOAD
	$(document).ready(function () {
		//Init		
        DUPX.togglePanels("basic");
		$("*[data-type='toggle']").click(DUPX.toggleClick);

	});
</script>