<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

	//-- START OF VIEW STEP 3
	$_POST['dbaction'] = isset($_POST['dbaction']) ? DUPX_U::sanitize_text_field($_POST['dbaction']) : 'create';
	
	if (isset($_POST['dbhost'])) {
		$post_db_host = DUPX_U::sanitize_text_field($_POST['dbhost']);
		$_POST['dbhost'] = trim($post_db_host);
	} else {
		$_POST['dbhost'] = null;
	}
	
	if (isset($_POST['dbname'])) {
		$post_db_name = DUPX_U::sanitize_text_field($_POST['dbname']);
		$_POST['dbname'] = trim($post_db_name);
	} else {
		$_POST['dbname'] = null;
	}	 
	
	if (isset($_POST['dbuser'])) {
		$post_db_user = DUPX_U::sanitize_text_field($_POST['dbuser']);
		$_POST['dbuser'] = trim($post_db_user);
	} else {
		$_POST['dbuser'] = null;
	}
	
	if (isset($_POST['dbpass'])) {
		$_POST['dbpass'] = $_POST['dbpass'];
	} else {
		$_POST['dbpass'] = null;
	}
	
	if (isset($_POST['dbhost'])) {
		$post_db_host = DUPX_U::sanitize_text_field($_POST['dbhost']);
		$_POST['dbport'] = parse_url($post_db_host, PHP_URL_PORT);
	} else {
		$_POST['dbport'] = 3306;
	}

	if (!empty($_POST['dbport'])) {
		$_POST['dbport'] = DUPX_U::sanitize_text_field($_POST['dbport']);
	} else {
		$_POST['dbport'] = 3306;
	}
	
	$_POST['exe_safe_mode']	= isset($_POST['exe_safe_mode']) ? DUPX_U::sanitize_text_field($_POST['exe_safe_mode']) : 0;

	$dbh = DUPX_DB::connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['dbport']);

	$all_tables = DUPX_DB::getTables($dbh);
	$active_plugins = DUPX_U::getActivePlugins($dbh);
	$old_path = $GLOBALS['DUPX_AC']->wproot;

	// RSR TODO: need to do the path too?
	$new_path = $GLOBALS['DUPX_ROOT'];
	$new_path = ((strrpos($old_path, '/') + 1) == strlen($old_path)) ? DUPX_U::addSlash($new_path) : $new_path;
	$empty_schedule_display = (DUPX_U::$on_php_53_plus) ? 'table-row' : 'none';
?>

<!-- =========================================
VIEW: STEP 3- INPUT -->
<form id='s3-input-form' method="post" class="content-form" autocomplete="off">

	<div class="logfile-link">
		<?php DUPX_View_Funcs::installerLogLink(); ?>
	</div>
	<div class="hdr-main">
		Step <span class="step">3</span> of 4: Update Data
		<div class="sub-header">This step will update the database and config files to match your new sites values.</div>
	</div>

	<?php
		if ($_POST['dbaction'] == 'manual') {
			echo '<div class="dupx-notice s3-manaual-msg">Manual SQL execution is enabled</div>';
		}
	?>

	<!--  POST PARAMS -->
	<div class="dupx-debug">
		<i>Step 3 - Page Load</i>
		<input type="hidden" name="ctrl_action"	  value="ctrl-step3" />
		<input type="hidden" name="ctrl_csrf_token" value="<?php echo DUPX_CSRF::generate('ctrl-step3'); ?>"> 
		<input type="hidden" name="view"		  value="step3" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step3'); ?>">
		<input type="hidden" name="secure-pass"   value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
		<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
		<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
		<input type="hidden" name="logging"		  value="<?php echo DUPX_U::esc_attr($_POST['logging']); ?>" />
		<input type="hidden" name="dbhost"		  value="<?php echo DUPX_U::esc_attr($_POST['dbhost']); ?>" />
		<input type="hidden" name="dbuser" 		  value="<?php echo DUPX_U::esc_attr($_POST['dbuser']); ?>" />
		<input type="hidden" name="dbpass" 		  value="<?php echo DUPX_U::esc_attr($_POST['dbpass']); ?>" />
		<input type="hidden" name="dbname" 		  value="<?php echo DUPX_U::esc_attr($_POST['dbname']); ?>" />
		<input type="hidden" name="dbcharset" 	  value="<?php echo DUPX_U::esc_attr($_POST['dbcharset']); ?>" />
		<input type="hidden" name="dbcollate" 	  value="<?php echo DUPX_U::esc_attr($_POST['dbcollate']); ?>" />
		<input type="hidden" name="config_mode"	  value="<?php echo DUPX_U::esc_attr($_POST['config_mode']); ?>" />
		<input type="hidden" name="exe_safe_mode" id="exe-safe-mode" value="<?php echo DUPX_U::esc_attr($_POST['exe_safe_mode']); ?>" />
		<input type="hidden" name="json"		  value="<?php echo DUPX_U::esc_attr($_POST['json']); ?>" />
	</div>

	<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s3-new-settings">
        <a href="javascript:void(0)"><i class="fa fa-minus-square"></i>Setup</a>
    </div>
    <div id="s3-new-settings">
        <table class="s3-opts">
            <tr>
                <td>Title:</td>
                <td><input type="text" name="blogname" id="blogname" value="<?php echo DUPX_U::esc_attr($GLOBALS['DUPX_AC']->blogname); ?>" /></td>
            </tr>
            <tr>
                <td>URL:</td>
                <td>
                    <input type="text" name="url_new" id="url_new" value="" />
                    <a href="javascript:DUPX.getNewURL('url_new')" style="font-size:12px">get</a>
                </td>
            </tr>
            <tr>
                <td>Path:</td>
                <td><input type="text" name="path_new" id="path_new" value="<?php echo DUPX_U::esc_attr($new_path); ?>" /></td>
            </tr>
        </table>
    </div>
    <br/><br/>

    <!-- =========================
    SEARCH AND REPLACE -->
    <div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s3-custom-replace">
        <a href="javascript:void(0)"><i class="fa fa-plus-square"></i>Replace</a>
    </div>

    <div id="s3-custom-replace" class="hdr-sub1-area" style="display:none; text-align: center">
        <div class="help-target">
            <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
        </div><br/>
		Add additional search and replace URLs to replace additional data.<br/>
		This option is available only in
		<a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=duplicator_pro&utm_content=free_inst_replaceopts">Duplicator Pro</a>
    </div>
    <br/><br/>
    
	<!-- ==========================
    OPTIONS -->
	<div class="hdr-sub1 toggle-hdr" data-type="toggle" data-target="#s3-adv-opts">
		<a href="javascript:void(0)"><i class="fa fa-plus-square"></i>Options</a>
	</div>
	<div id="s3-adv-opts" class="hdr-sub1-area" style="display:none;">

	<!-- START TABS -->
	<div id="tabs">
		<ul>
			<li><a href="#tabs-admin-account">Admin Account</a></li>
			<li><a href="#tabs-scan-options">Scan Options</a></li>
			<li><a href="#tabs-wp-config-file">WP-Config File</a></li>
		</ul>

		<!-- =====================
		ADMIN TAB -->
		<div id="tabs-admin-account">
			<div class="help-target">
				<?php DUPX_View_Funcs::helpIconLink('step3'); ?>
			</div><br/>

			<div class="hdr-sub3">New Admin Account</div>
			<div style="text-align: center">
				<i style="color:gray;font-size: 11px">This feature is optional.  If the username already exists the account will NOT be created or updated.</i>
			</div>

			<table class="s3-opts" style="margin-top:7px">
				<tr>
					<td>Username:</td>
					<td><input type="text" name="wp_username" id="wp_username" value="" title="4 characters minimum" placeholder="(4 or more characters)" /></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
                        <?php
                        DUPX_U_Html::inputPasswordToggle('wp_password', 'wp_password', array(),
                            array(
                            'placeholder' => '(6 or more characters)',
                            'title' => '6 characters minimum'
                        ));
                        ?>
                </tr>
				<tr>
					<td>Email:</td>
					<td><input type="text" name="wp_mail" id="wp_mail" value="" title=""  placeholder="" /></td>
				</tr>
				<tr>
					<td>Nickname:</td>
					<td><input type="text" name="wp_nickname" id="wp_nickname" value="" title="if username is empty"  placeholder="(if username is empty)" /></td>
				</tr>
				<tr>
					<td>First name:</td>
					<td><input type="text" name="wp_first_name" id="wp_first_name" value="" title="optional"  placeholder="(optional)" /></td>
				</tr>
				<tr>
					<td>Last name:</td>
					<td><input type="text" name="wp_last_name" id="wp_last_name" value="" title="optional"  placeholder="(optional)" /></td>
				</tr>
			</table>
			<br/><br/>
		</div>

		<!-- =====================
		SCAN TAB -->
		<div id="tabs-scan-options">
			<div class="help-target">
				<?php DUPX_View_Funcs::helpIconLink('step3'); ?>
			</div><br/>
			<div class="hdr-sub3">Database Scan Options</div>
			<table  class="s3-opts">
				<tr>
					<td style="width:105px">Site URL:</td>
					<td style="white-space: nowrap">
						<input type="text" name="siteurl" id="siteurl" value="" />
						<a href="javascript:DUPX.getNewURL('siteurl')" style="font-size:12px">get</a><br/>
					</td>
				</tr>
				<tr valign="top">
					<td style="width:80px">Old URL:</td>
					<td>
						<input type="text" name="url_old" id="url_old" value="<?php echo DUPX_U::esc_attr($GLOBALS['DUPX_AC']->url_old); ?>" readonly="readonly"  class="readonly" />
						<a href="javascript:DUPX.editOldURL()" id="edit_url_old" style="font-size:12px">edit</a>
					</td>
				</tr>
				<tr valign="top">
					<td>Old Path:</td>
					<td>
						<input type="text" name="path_old" id="path_old" value="<?php echo DUPX_U::esc_attr($old_path); ?>" readonly="readonly"  class="readonly" />
						<a href="javascript:DUPX.editOldPath()" id="edit_path_old" style="font-size:12px">edit</a>
					</td>
				</tr>
			</table><br/>

			<table>
				<tr>
					<td style="padding-right:10px">
						<b>Scan Tables:</b>
						<div class="s3-allnonelinks">
							<a href="javascript:void(0)" onclick="$('#tables option').prop('selected',true);">[All]</a>
							<a href="javascript:void(0)" onclick="$('#tables option').prop('selected',false);">[None]</a>
						</div><br style="clear:both" />
						<select id="tables" name="tables[]" multiple="multiple" style="width:315px;" size="10">
							<?php
							foreach( $all_tables as $table ) {
								echo '<option selected="selected" value="' . DUPX_U::esc_attr( $table ) . '">' . DUPX_U::esc_html($table) . '</option>';
							}
							?>
						</select>

					</td>
					<td valign="top">
						<b>Activate Plugins:</b>
						<?php echo ($_POST['exe_safe_mode'] > 0) ? '<small class="s3-warn">Safe Mode Enabled</small>' : '' ; ?>
						<div class="s3-allnonelinks" style="<?php echo ($_POST['exe_safe_mode']>0)? 'display:none':''; ?>">
							<a href="javascript:void(0)" onclick="$('#plugins option').prop('selected',true);">[All]</a>
							<a href="javascript:void(0)" onclick="$('#plugins option').prop('selected',false);">[None]</a>
						</div><br style="clear:both" />
						<select id="plugins" name="plugins[]" multiple="multiple" style="width:315px;" <?php echo ($_POST['exe_safe_mode'] > 0) ? 'disabled="true"' : ''; ?> size="10">
							<?php
							$selected_string = 'selected="selected"';
							foreach ($active_plugins as $plugin) {
								$label = dirname($plugin) == '.' ? $plugin : dirname($plugin);
                                echo "<option {$selected_string} value='" . DUPX_U::esc_attr( $plugin ) . "'>" . DUPX_U::esc_html($label) . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<br/>
			<input type="checkbox" name="search_replace_email_domain" id="search_replace_email_domain" value="1" /> <label for="search_replace_email_domain">Update email domains</label><br/>
			<input type="checkbox" name="fullsearch" id="fullsearch" value="1" /> <label for="fullsearch">Use Database Full Search Mode</label><br/>
			<input type="checkbox" name="postguid" id="postguid" value="1" /> <label for="postguid">Keep Post GUID Unchanged</label><br/>
            <label>
                <B>Max size check for serialize objects:</b>
                <input type="number"
                       name="<?php echo DUPX_CTRL::NAME_MAX_SERIALIZE_STRLEN_IN_M; ?>"
                       value="<?php echo DUPX_Constants::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M; ?>"
                       min="0" max="99" step="1" size="2"
                       style="width: 40px;width: 50px; text-align: center;" /> MB
            </label>
			<br/><br/>
		</div>
		
		<!-- =====================
		WP-CONFIG TAB -->
		<div id="tabs-wp-config-file">
			<div class="help-target">
				<?php DUPX_View_Funcs::helpIconLink('step3'); ?>
			</div><br/>
			<div class="hdr-sub3">WP-Config File</div>
			<?php
            require_once($GLOBALS['DUPX_INIT'].'/lib/config/class.wp.config.tranformer.php');
			$root_path		= $GLOBALS['DUPX_ROOT'];
			$root_path = $GLOBALS['DUPX_ROOT'];
			$wpconfig_ark_path	= ($GLOBALS['DUPX_AC']->installSiteOverwriteOn) ? "{$root_path}/dup-wp-config-arc__{$GLOBALS['DUPX_AC']->package_hash}.txt" : "{$root_path}/wp-config.php";

            if (file_exists($wpconfig_ark_path)) {
				$config_transformer = new WPConfigTransformer($wpconfig_ark_path);
            } else {
                $config_transformer = null;
            }
            
			?>
			<table class="dupx-opts dupx-advopts">
                <?php
                if (file_exists($wpconfig_ark_path)) { ?>
				<tr>
					<td>Cache:</td>
					<td>
						<?php
						$wp_cache_val = false;
						if (!is_null($config_transformer) && $config_transformer->exists('constant', 'WP_CACHE')) {
							$wp_cache_val = $config_transformer->get_value('constant', 'WP_CACHE');
						}
						?>
						<input type="checkbox" name="cache_wp" id="cache_wp" <?php DupLiteSnapLibUIU::echoChecked($wp_cache_val);?> /> <label for="cache_wp">Keep Enabled</label>
					</td>
				</tr>
                <tr>
					<td></td>
                    <td>
						<?php
						$wpcachehome_val = '';
						if (!is_null($config_transformer) && $config_transformer->exists('constant', 'WPCACHEHOME')) {
							$wpcachehome_val = $config_transformer->get_value('constant', 'WPCACHEHOME');
						}
						?>
						<input type="checkbox" name="cache_path" id="cache_path" <?php DupLiteSnapLibUIU::echoChecked($wpcachehome_val);?> /> <label for="cache_path">Keep Home Path</label>
                        <br><br>
					</td>
				</tr>
				<tr>
					<td>SSL:</td>
					<td>
						<?php
						$force_ssl_admin_val = false;
						if (!is_null($config_transformer) && $config_transformer->exists('constant', 'FORCE_SSL_ADMIN')) {
							$force_ssl_admin_val = $config_transformer->get_value('constant', 'FORCE_SSL_ADMIN');
						}
						?>
						<input type="checkbox" name="ssl_admin" id="ssl_admin" <?php DupLiteSnapLibUIU::echoChecked($force_ssl_admin_val);?> /> <label for="ssl_admin">Enforce on Admin</label>
					</td>
				</tr>
                <?php } else { ?>
                <tr>
                    <td>wp-config.php not found</td>
                    <td>No action on the wp-config is possible.<br>
                        After migration, be sure to insert a properly modified wp-config for correct wordpress operation.</td>
                </tr>
                <?php } ?>
			</table><br/>
			<i>
				Need more control? With <a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=duplicator_pro&utm_content=wpconfig" target="_blank">Duplicator Pro</a> 
				you can change many wp-config settings automatically from this section, without having to manually edit the file.
			</i>
		</div>
	</div>
	<!-- END TABS -->
	</div>
	<br/><br/><br/><br/>


	<div class="footer-buttons">
		<button id="s3-next" type="button"  onclick="DUPX.runUpdate()" class="default-btn"> Next <i class="fa fa-caret-right"></i> </button>
	</div>
</form>

<!-- =========================================
VIEW: STEP 3 - AJAX RESULT  -->
<form id='s3-result-form' method="post" class="content-form" style="display:none" autocomplete="off">

	<div class="logfile-link"><?php DUPX_View_Funcs::installerLogLink(); ?></div>
	<div class="hdr-main">
		Step <span class="step">3</span> of 4: Update Data
		<div class="sub-header">This step will update the database and config files to match your new sites values.</div>
	</div>

	<!--  POST PARAMS -->
	<div class="dupx-debug">
		<i>Step 3 - AJAX Response</i>
		<input type="hidden" name="view"  value="step4" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step4'); ?>">
		<input type="hidden" name="secure-pass" value="<?php echo DUPX_U::esc_attr($_POST['secure-pass']); ?>" />
		<input type="hidden" name="bootloader" value="<?php echo DUPX_U::esc_attr($GLOBALS['BOOTLOADER_NAME']); ?>" />
	<input type="hidden" name="archive" value="<?php echo DUPX_U::esc_attr($GLOBALS['FW_PACKAGE_PATH']); ?>" />
		<input type="hidden" name="logging" id="logging" value="<?php echo DUPX_U::esc_attr($_POST['logging']); ?>" />
		<input type="hidden" name="url_new" id="ajax-url_new"  />
		<input type="hidden" name="exe_safe_mode" id="ajax-exe-safe-mode" />
		<input type="hidden" name="json"    id="ajax-json" />
		<input type='submit' value='manual submit'>
	</div>

	<!--  PROGRESS BAR -->
	<div id="progress-area">
		<div style="width:500px; margin:auto">
			<div style="font-size:1.7em; margin-bottom:20px"><i class="fas fa-circle-notch fa-spin"></i> Processing Data Replacement</div>
			<div id="progress-bar"></div>
			<h3> Please Wait...</h3><br/><br/>
			<i>Keep this window open during the replacement process.</i><br/>
			<i>This can take several minutes.</i>
		</div>
	</div>

	<!--  AJAX SYSTEM ERROR -->
	<div id="ajaxerr-area" style="display:none">
		<p>Please try again an issue has occurred.</p>
		<div style="padding: 0px 10px 10px 10px;">
			<div id="ajaxerr-data">An unknown issue has occurred with the update setup step.  Please see the <?php DUPX_View_Funcs::installerLogLink(); ?> file for more details.</div>
			<div style="text-align:center; margin:10px auto 0px auto">
				<input type="button" onclick='DUPX.hideErrorResult2()' value="&laquo; Try Again"  class="default-btn" /><br/><br/>
				<i style='font-size:11px'>See online help for more details at <a href='https://snapcreek.com' target='_blank'>snapcreek.com</a></i>
			</div>
		</div>
	</div>
</form>

<script>
/** 
* Timeout (10000000 = 166 minutes) */
DUPX.runUpdate = function()
{
	//Validation
	var wp_username = $.trim($("#wp_username").val()).length || 0;
	var wp_password = $.trim($("#wp_password").val()).length || 0;
    var wp_mail = $.trim($("#wp_mail").val()).length || 0;


	if ( $.trim($("#url_new").val()) == "" )  {alert("The 'New URL' field is required!"); return false;}
	if ( $.trim($("#siteurl").val()) == "" )  {alert("The 'Site URL' field is required!"); return false;}

    if (wp_username >= 1) {
        if (wp_username < 4) {
            alert("The New Admin Account 'Username' must be four or more characters");
            return false;
        } else if (wp_password < 6) {
            alert("The New Admin Account 'Password' must be six or more characters");
            return false;
        } else if (wp_mail === 0) {
            alert("The New Admin Account 'mail' is required");
            return false;
        }
    }
    
	var nonHttp = false;
	var failureText = '';

	/* IMPORTANT - not trimming the value for good - just in the check */
	$('input[name="search[]"]').each(function() {
		var val = $(this).val();

		if(val.trim() != "") {
			if(val.length < 3) {
				failureText = "Custom search fields must be at least three characters.";
			}

			if(val.toLowerCase().indexOf('http') != 0) {
				nonHttp = true;
			}
		}
	});

	$('input[name="replace[]"]').each(function() {
		var val = $(this).val();
		if(val.trim() != "") {
			// Replace fields can be anything
			if(val.toLowerCase().indexOf('http') != 0) {
				nonHttp = true;
			}
		}
	});

	if(failureText != '') {
		alert(failureText);
		return false;
	}

	if(nonHttp) {
		if(confirm('One or more custom search and replace strings are not URLs.  Are you sure you want to continue?') == false) {
			return false;
		}
	}

	$.ajax({
		type: "POST",
		timeout: 10000000,
		url: window.location.href,
		data: $('#s3-input-form').serialize(),
		beforeSend: function() {
			DUPX.showProgressBar();
			$('#s3-input-form').hide();
			$('#s3-result-form').show();
		},
		success: function(respData, textStatus, xHr){
			try {
                var data = DUPX.parseJSON(respData);
            } catch(err) {
                console.error(err);
                console.error('JSON parse failed for response data: ' + respData);
				var status  = "<b>Server Code:</b> "	+ xHr.status		+ "<br/>";
				status += "<b>Status:</b> "			+ textStatus	+ "<br/>";
				status += "<b>Response:</b> "		+ xHr.responseText  + "<hr/>";
				status += "<b>Additional Troubleshooting Tips:</b><br/>";
				status += "- Check the <a href='./<?php echo DUPX_U::esc_attr($GLOBALS["LOG_FILE_NAME"]);?>' target='dup-installer'>dup-installer-log.txt</a> file for warnings or errors.<br/>";
				status += "- Check the web server and PHP error logs. <br/>";
				status += "- For timeout issues visit the <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-100-q' target='_blank'>Timeout FAQ Section</a><br/>";
				$('#ajaxerr-data').html(status);
				DUPX.hideProgressBar();
                return false;
            }
			if (typeof(data) != 'undefined' && data.step3.pass == 1) {
				$("#ajax-url_new").val($("#url_new").val());
				$("#ajax-exe-safe-mode").val($("#exe-safe-mode").val());
				$("#ajax-json").val(escape(JSON.stringify(data)));
				<?php if (! $GLOBALS['DUPX_DEBUG']) : ?>
					setTimeout(function(){$('#s3-result-form').submit();}, 1000);
				<?php endif; ?>
				$('#progress-area').fadeOut(1800);
			} else {
				DUPX.hideProgressBar();
			}
		},
		error: function(xhr) {
			var status  = "<b>Server Code:</b> "	+ xhr.status		+ "<br/>";
			status += "<b>Status:</b> "			+ xhr.statusText	+ "<br/>";
			status += "<b>Response:</b> "		+ xhr.responseText  + "<hr/>";
			status += "<b>Additional Troubleshooting Tips:</b><br/>";
			status += "- Check the <a href='./<?php echo DUPX_U::esc_attr($GLOBALS["LOG_FILE_NAME"]);?>' target='dup-installer'>dup-installer-log.txt</a> file for warnings or errors.<br/>";
			status += "- Check the web server and PHP error logs. <br/>";
			status += "- For timeout issues visit the <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-100-q' target='_blank'>Timeout FAQ Section</a><br/>";
			$('#ajaxerr-data').html(status);
			DUPX.hideProgressBar();
		}
	});
};

/**
 * Returns the windows active url */
DUPX.getNewURL = function(id)
{
	var filename = window.location.pathname.split('/').pop() || 'main.installer.php' ;
	var newVal	 = window.location.href.split("?")[0];
	newVal = newVal.replace("/" + filename, '');
	var last_slash = newVal.lastIndexOf("/");
	newVal = newVal.substring(0, last_slash);

	$("#" + id).val(newVal);
};

/**
 * Allows user to edit the package url  */
DUPX.editOldURL = function()
{
	var msg = 'This is the URL that was generated when the package was created.\n';
	msg += 'Changing this value may cause issues with the install process.\n\n';
	msg += 'Only modify  this value if you know exactly what the value should be.\n';
	msg += 'See "General Settings" in the WordPress Administrator for more details.\n\n';
	msg += 'Are you sure you want to continue?';

	if (confirm(msg)) {
		$("#url_old").removeAttr('readonly');
		$("#url_old").removeClass('readonly');
		$('#edit_url_old').hide('slow');
	}
};

/**
 * Allows user to edit the package path  */
DUPX.editOldPath = function()
{
	var msg = 'This is the SERVER URL that was generated when the package was created.\n';
	msg += 'Changing this value may cause issues with the install process.\n\n';
	msg += 'Only modify  this value if you know exactly what the value should be.\n';
	msg += 'Are you sure you want to continue?';

	if (confirm(msg)) {
		$("#path_old").removeAttr('readonly');
		$("#path_old").removeClass('readonly');
		$('#edit_path_old').hide('slow');
	}
};



/**
 * Go back on AJAX result view */
DUPX.hideErrorResult2 = function()
{
	$('#s3-result-form').hide();
	$('#s3-input-form').show(200);
};

//DOCUMENT LOAD
$(document).ready(function()
{
	setTimeout(function() {
		$('#wp_username').val('');
		$('#wp_password').val('');
	}, 900);
	$("#tabs").tabs();
	DUPX.getNewURL('url_new');
	DUPX.getNewURL('siteurl');
	$("*[data-type='toggle']").click(DUPX.toggleClick);
	$("#wp_password").passStrength({
			shortPass: 		"top_shortPass",
			badPass:		"top_badPass",
			goodPass:		"top_goodPass",
			strongPass:		"top_strongPass",
			baseStyle:		"top_testresult",
			userid:			"#wp_username",
			messageloc:		1
	});
});
</script>
