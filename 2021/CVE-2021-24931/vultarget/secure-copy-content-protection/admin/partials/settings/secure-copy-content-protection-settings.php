<?php
$actions = $this->settings_obj;

if (isset($_REQUEST['ays_submit'])) {
	$actions->store_data($_REQUEST);
}
if (isset($_GET['ays_sccp_tab'])) {
	$ays_sccp_tab = sanitize_text_field($_GET['ays_sccp_tab']);
} else {
	$ays_sccp_tab = 'tab1';
}
$db_data = $actions->get_db_data();

$options = ($actions->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes( $actions->ays_get_setting('options') ), true);

// global $wp_roles;
// $ays_users_roles = $wp_roles->role_names;

$mailchimp_res      = ($actions->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $actions->ays_get_setting('mailchimp');
$mailchimp          = json_decode($mailchimp_res, true);
$mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '';
$mailchimp_api_key  = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '';

// WP Editor height
$sccp_wp_editor_height = (isset($options['sccp_wp_editor_height']) && $options['sccp_wp_editor_height'] != '' && $options['sccp_wp_editor_height'] != 0) ? absint( sanitize_text_field($options['sccp_wp_editor_height']) ) : 150 ;

// Do not store IP adressess
$options['sccp_disable_user_ip'] = isset($options['sccp_disable_user_ip']) ? $options['sccp_disable_user_ip'] : 'off';
$sccp_disable_user_ip = (isset($options['sccp_disable_user_ip']) && $options['sccp_disable_user_ip'] == "on") ? true : false;

?>
<div class="wrap" style="position:relative;">
    <div class="container-fluid">
        <form method="post">
            <input type="hidden" name="ays_sccp_tab" value="<?php echo htmlentities($ays_sccp_tab); ?>">
            <h1 class="wp-heading-inline">
				<?php
				echo __('Settings', $this->plugin_name);
				?>
            </h1>
			<?php
			if (isset($_REQUEST['status'])) {
				$actions->sccp_settings_notices($_REQUEST['status']);
			}
			?>
            <hr/>
            <div class="ays-gen-settings-wrapper">
                <div>
                    <div class="nav-tab-wrapper" style="position:sticky; top:35px;">
                        <a href="#tab1" data-tab="tab1"
                           class="nav-tab <?php echo ($ays_sccp_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">                           
							<?php echo __("General", $this->plugin_name); ?>
                        </a>  
                        <a href="#tab2" data-tab="tab2"
                           class="nav-tab <?php echo ($ays_sccp_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">                                                                                                    
                            <?php echo __("Integrations", $this->plugin_name); ?>
                        </a>                        
                    </div>
                </div>
                <div class="ays-sccp-tabs-wrapper">
                    <div id="tab1"
                         class="ays-sccp-tab-content <?php echo ($ays_sccp_tab == 'tab1') ? 'ays-sccp-tab-content-active' : ''; ?>">
                        <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
                        <hr/>                        
                        <fieldset>
                            <legend>
                                <strong style="font-size:30px;"><i class="ays_fa ays_fa_question_circle"></i></strong>
                                <h5><?php echo __('Default parameters for copy protection',$this->plugin_name)?></h5>
                            </legend>
                           <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_sccp_wp_editor_height">
                                        <?php echo __( "WP Editor height", $this->plugin_name ); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Give the default value to the height of the WP Editor. It will apply to all WP Editors within the plugin on the dashboard.',$this->plugin_name); ?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" name="ays_sccp_wp_editor_height" id="ays_sccp_wp_editor_height" class="ays-text-input" value="<?php echo $sccp_wp_editor_height; ?>">
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <fieldset>
                            <legend>
                                <strong style="font-size:30px;"><i class="ays_fa ays_fa_user_ip"></i></strong>
                                <h5><?php echo __('Users IP adressess',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_sccp_disable_user_ip">
                                        <?php echo __( "Do not store IP adressess", $this->plugin_name ); ?>
                                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, IP address of the users will not be stored in database.',$this->plugin_name)?>">
                                            <i class="ays_fa ays_fa_info_circle"></i>
                                        </a>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="checkbox" class="ays-checkbox-input" id="ays_sccp_disable_user_ip" name="ays_sccp_disable_user_ip" value="on" <?php echo $sccp_disable_user_ip ? 'checked' : ''; ?> />
                                </div>
                            </div>
                        </fieldset> <!-- Users IP adressess -->                        
                    </div>                    
                    <div id="tab2"
                         class="ays-sccp-tab-content <?php echo ($ays_sccp_tab == 'tab2') ? 'ays-sccp-tab-content-active' : ''; ?>">
                         <p class="ays-subtitle"><?php echo __('Integrations',$this->plugin_name)?></p>
                        <hr/>                            
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo SCCP_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
                                <h5><?php echo __('MailChimp',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_mailchimp_username">
                                                <?php echo __('MailChimp Username',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   class="ays-text-input"
                                                   id="ays_mailchimp_username"
                                                   name="ays_mailchimp_username"
                                                   value="<?php echo $mailchimp_username; ?>"
                                            />
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_mailchimp_api_key">
                                                <?php echo __('MailChimp API Key',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                   class="ays-text-input"
                                                   id="ays_mailchimp_api_key"
                                                   name="ays_mailchimp_api_key"
                                                   value="<?php echo $mailchimp_api_key; ?>"
                                            />
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://us20.admin.mailchimp.com/account/api/", __( "Account Extras menu", $this->plugin_name ) ); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset>                        
                    </div>
                </div>
            </div>
            <hr/>
			<?php
			wp_nonce_field('settings_action', 'settings_action');
			$other_attributes = array();
			submit_button(__('Save changes', $this->plugin_name), 'primary ays-button ays-sccp-save-comp', 'ays_submit', true, $other_attributes);
			?>
        </form>
    </div>
</div>