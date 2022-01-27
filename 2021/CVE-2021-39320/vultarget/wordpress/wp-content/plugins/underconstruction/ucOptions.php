<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(!wp_verify_nonce($_POST['save_options_field'], 'save_options') || ! current_user_can('publish_pages')){
		die("Sorry, but this request is invalid");
	}
}

if (isset($_GET['turnOnUnderConstructionMode']))
{
	update_option('underConstructionActivationStatus', 1);
}

if (isset($_GET['turnOffUnderConstructionMode']))
{
	update_option('underConstructionActivationStatus', 0);
}

// ======================================
// 		process display options
// ======================================

if (isset($_POST['display_options']))
{
	if ($_POST['display_options'] == 0) //they want to just use the default
	{
		update_option('underConstructionDisplayOption', 0);
	}

	if ($_POST['display_options'] == 1) //they want to use the default with custom text
	{
		$values = array('pageTitle'=>'', 'headerText'=>'', 'bodyText'=>'');

		if (isset($_POST['pageTitle']))
		{
			$values['pageTitle'] = esc_attr($_POST['pageTitle']);
		}

		if (isset($_POST['headerText']))
		{
			$values['headerText'] = esc_attr($_POST['headerText']);
		}

		if (isset($_POST['bodyText']))
		{
			$values['bodyText'] = esc_attr($_POST['bodyText']);
		}


		update_option('underConstructionCustomText', $values);
		update_option('underConstructionDisplayOption', 1);
	}

	if ($_POST['display_options'] == 2) //they want to use their own HTML
	{
		if (isset($_POST['ucHTML']))
		{
			update_option('underConstructionHTML', esc_attr($_POST['ucHTML']));
			update_option('underConstructionDisplayOption', 2);
		}
	}
	
	if ($_POST['display_options'] == 3){ //they want to use the under construction page in their theme
		update_option('underConstructionDisplayOption', 3);
	}

}

// ======================================
// 		process http status codes
// ======================================
if (isset($_POST['activate']))
{
	if ($_POST['activate'] == 0)
	{
		update_option('underConstructionActivationStatus', 0);
	}

	if ($_POST['activate'] == 1)
	{
		update_option('underConstructionActivationStatus', 1);
	}
}

// ======================================
// 		process on/off status
// ======================================
if (isset($_POST['http_status']))
{
	if ($_POST['http_status'] == 200)
	{
		update_option('underConstructionHTTPStatus', 200);
	}

	if ($_POST['http_status'] == 301)
	{
		update_option('underConstructionHTTPStatus', 301);
		update_option('underConstructionRedirectURL', $_POST['url']);
	}

	if ($_POST['http_status'] == 503)
	{
		update_option('underConstructionHTTPStatus', 503);
	}
}

// ======================================
// 		process IP addresses
// ======================================

if(isset($_POST['ip_address']) && $_POST['ip_address']) {

	$ip = $_POST['ip_address'];
	$ip = inet_ntop(inet_pton($ip));
	//$ip = long2ip(ip2long($ip));

	if($ip != "0.0.0.0"){
		$array = get_option('underConstructionIPWhitelist');

		if(!$array){
			$array = array();
		}

		$array[] = $ip;

		$array = array_unique($array);

		update_option('underConstructionIPWhitelist', $array);
	}
}

if(isset($_POST['remove_selected_ip_btn'])){
	if(isset($_POST['ip_whitelist'])){
		$array = get_option('underConstructionIPWhitelist');

		if(!$array){
			$array = array();
		}

		unset($array[$_POST['ip_whitelist']]);
		$array = array_values($array);
		update_option('underConstructionIPWhitelist', $array);
	}
}

if(isset($_POST['required_role'])){
	update_option('underConstructionRequiredRole', $_POST['required_role']);
}

$current_theme_has_uc_page = file_exists(get_template_directory() . '/under-construction.php');

add_thickbox();

if (array_key_exists('underconstruction_global_notification', $_GET) && $_GET['underconstruction_global_notification'] == 0) {
	update_option('underconstruction_global_notification', 0);
}
?>
<noscript>
	<div class='updated' id='javascriptWarn'>
		<p><?php _e('JavaScript appears to be disabled in your browser. For this plugin to work correctly, please enable JavaScript or switch to a more modern browser.', 'underconstruction');?></p>
	</div>
</noscript>
<style type="text/css">
	#underconstruction_global_notification a.button:active {vertical-align:baseline;}
</style> 
<div class="wrap">
	<div class="under-construction-content-left">
		<div id="icon-options-general" class="icon32">
			<br />
		</div>
		<form method="post"
			action="<?php echo $GLOBALS['PHP_SELF'] . '?page=' . $this->mainOptionsPage; ?>"
			id="ucoptions">
			<h2><?php _e('Under Construction', 'underconstruction');?></h2>
			<table>
				<tr>
					<td>
						<h3><?php _e('Activate or Deactivate', 'underconstruction');?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e('Activate or Deactivate', 'underconstruction');?></span>
							</legend>
							<label title="activate">
							  <input type="radio" name="activate" value="1"<?php if ($this->pluginIsActive()) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('on', 'underconstruction');?>
							</label><br />
							<label title="deactivate">
							  <input type="radio" name="activate" value="0"<?php if (!$this->pluginIsActive()) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('off', 'underconstruction');?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<h3><?php _e('HTTP Status Code', 'underconstruction');?></h3>
						<p><?php _e("You can choose to send the standard HTTP status code with the under construction page, or send a 503 \"Service Unavailable\" status code. This will tell Google that this page isn't ready yet, and cause your site not to be listed until this plugin is disabled.", 'underconstruction');?></p>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e('HTTP Status Code', 'underconstruction');?></span>
							</legend>
							<label title="HTTP200">
							  <input type="radio" name="http_status" value="200" id="200_status"<?php if ($this->httpStatusCodeIs(200)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('HTTP 200 - ok', 'underconstruction');?> 
							</label> <br />
							<label title="HTTP301"> 
							  <input type="radio" name="http_status" value="301" id="301_status"<?php if ($this->httpStatusCodeIs(301)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('HTTP 301 - Redirect', 'underconstruction');?> </label> <br />
							<label title="HTTP503">
							  <input type="radio" name="http_status" value="503" id="503_status"<?php if ($this->httpStatusCodeIs(503)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('HTTP 503 - Service Unavailable', 'underconstruction');?>
							</label>
						</fieldset>
						<div id="redirect_panel" <?php echo !$this->httpStatusCodeIs(301) ? 'class="hidden"' : '';?>><br />
						  <label for="url"><?php _e('Redirect Location:', 'underconstruction');?></label>
							<input type="text" name="url" id="url" value="<?php echo get_option('underConstructionRedirectURL');?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<h3><?php _e('Restrict By Role', 'underconstruction');?></h3>
					</td>
				</tr>
				<tr>
					<td><?php _e('Only users at or above this level will be able to log in:', 'underconstruction');?> 
					<select id="required_role" name="required_role">
	  				<option value="0"><?php _e('All Users', 'underconstruction');?></option>
	  				<?php
	  				$selected = get_option('underConstructionRequiredRole');
	  				$editable_roles = get_editable_roles();
	  				//to get rid of Notices "Undefined var"...
	  				$p = $r = '';
	  
	  				foreach ( $editable_roles as $role => $details ) {
	  					$name = translate_user_role($details['name'] );
	  					if ( $selected == $role ) // preselect specified role
	  					  $p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
	  					else
	  					  $r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
	  				}
	  				echo $p . $r;
	  				?>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<h3><?php _e('IP Address Whitelist', 'underconstruction');?></h3>
					</td>
				</tr>
				<tr>
					<td>
					<?php $whitelist = get_option('underConstructionIPWhitelist');
					if($whitelist && count($whitelist)): ?> 
					  <select size="4" id="ip_whitelist" name="ip_whitelist" style="width: 250px; height: 100px;">
						<?php for($i = 0; $i < count($whitelist); $i++):?>
							<option id="<?php echo $i; ?>" value="<?php echo $i;?>">
							<?php echo $whitelist[$i];?>
							</option>
							<?php endfor;?>
	          </select><br />

	          <input type="submit" value="<?php _e('Remove Selected IP Address', 'underconstruction'); ?>" name="remove_selected_ip_btn" class="button" id="remove_selected_ip_btn" /> <br /> <br /> 
	        <?php endif; ?> 
	        <label><?php _e('IP Address:', 'underconstruction');?> <input type="text" name="ip_address" id="ip_address" /> </label>
						<a id="add_current_address_btn" style="cursor: pointer;" class="button"><?php _e('Add Current Address', 'underconstruction');?></a>
						<span id="loading_current_address" class="hidden">Loading...</span>
						<br />
					</td>
				</tr>
				<tr>
					<td>
						<h3><?php _e('Display Options', 'underconstruction');?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e('Display Options', 'underconstruction');?> </span>
							</legend>
							<label title="<?php _e('Display the default under construction page', 'underconstruction');?>">
							  <input type="radio" name="display_options" value="0" id="displayOption0"<?php if ($this->displayStatusCodeIs(0)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Display the default under construction page', 'underconstruction');?>
							</label> <br />
							<label title="<?php _e('Display the under construction page that is part of the active theme', 'underconstruction');?>">
							  <input <?php if(!$current_theme_has_uc_page): ?>disabled="disabled" <?php endif; ?> type="radio" name="display_options" value="3" id="displayOption3"<?php if ($this->displayStatusCodeIs(3)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Display the under construction page that is part of the active theme', 'underconstruction');?>
							  
							  <?php if(!$current_theme_has_uc_page): ?>
							  <br /> <em style="margin-left: 24px;"><?php _e('Only available for themes with an under-construction.php file', 'underconstruction');?></em>
							  <?php endif; ?>
							  
							</label> <br /> 
							<label title="<?php _e('Display the default under construction page, but use custom text', 'underconstruction');?>"> 
							  <input type="radio" name="display_options" value="1" id="displayOption1"<?php if ($this->displayStatusCodeIs(1)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Display the default under construction page, but use custom text', 'underconstruction');?>
							</label> <br /> 
							<label title="<?php _e('Display a custom page using your own HTML', 'underconstruction');?>"> 
							  <input type="radio" name="display_options" value="2" id="displayOption2"<?php if ($this->displayStatusCodeIs(2)) { echo ' checked="checked"'; } ?>>&nbsp;<?php _e('Display a custom page using your own HTML', 'underconstruction');?>
							</label> <br /> 
						</fieldset>
					</td>
				</tr>
			</table>
			
			<div id="customText"<?php if (!$this->displayStatusCodeIs(1) && !$this->displayStatusCodeIs(2)) { echo ' style="display: none;"'; } ?>>
				<h3><?php _e('Display Custom Text', 'underconstruction');?></h3>
				<p><?php _e('The text here will replace the text on the default page', 'underconstruction');?></p>
				<table>
					<tr valign="top">
						<th scope="row"><label for="pageTitle"> <?php _e('Page Title', 'underconstruction');?> </label></th>
						<td><input name="pageTitle" type="text" id="pageTitle" value="<?php echo $this->getCustomPageTitle(); ?>" class="regular-text" size="50"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="headerText"> <?php _e('Header Text', 'underconstruction');?> </label></th>
						<td><input name="headerText" type="text" id="headerText" value="<?php echo $this->getCustomHeaderText(); ?>" class="regular-text" size="50"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bodyText"> <?php _e('Body Text', 'underconstruction');?> </label></th>
						<td><?php echo '<textarea rows="2" cols="44" name="bodyText" id="bodyText" class="regular-text">'.trim($this->getCustomBodyText()).'</textarea>'; ?></td>
					</tr>
				</table>
			</div>
			
			<div id="customHTML"<?php if (!$this->displayStatusCodeIs(2)) { echo ' style="display: none;"'; } ?>>
				<h3><?php _e('Under Construction Page HTML', 'underconstruction');?></h3>
				<p><?php _e('Put in this area the HTML you want to show up on your front page', 'underconstruction');?></p>
				<?php echo '<textarea name="ucHTML" rows="15" cols="75">'.$this->getCustomHTML().'</textarea>'; ?>
			</div>
			
			<p class="submit">
			<?php wp_nonce_field('save_options','save_options_field'); ?>
				<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'underconstruction'); ?>" id="submitChangesToUnderConstructionPlugin" />
			</p>
		</form>
	</div>

	<div class="under-construction-content-right">
	    <div class="under-construction-content-container-right">
	        <div class="under-construction-promo-box entry-content">
	            <p class="under-construction-promo-box-header">Your one stop WordPress shop</p>
	            <ul>
	               <li>&#8226; Get the latest WordPress software deals</li>
	               <li>&#8226; Plugins, themes, form builders, and more</li>
	               <li>&#8226; Shop with confidence; 60-day money-back guarantee</li>
	            </ul>
	            <div align="center">
	                <button onclick="window.open('https://appsumo.com/tools/wordpress/?utm_source=sumo&utm_medium=wp-widget&utm_campaign=underconstruction')" class="under-construction-appsumo-capture-container-button" type="submit">Show Me The Deals</button>
	            </div>
	        </div>

	        <div class="under-construction-promo-box under-construction-promo-box-form  entry-content">
	            <?php include plugin_dir_path( __FILE__ ).'appsumo-capture-form.php'; ?>
	        </div>
	    </div>
	</div>
	
</div>
