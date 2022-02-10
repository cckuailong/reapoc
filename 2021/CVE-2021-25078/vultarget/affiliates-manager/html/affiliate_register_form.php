<?php

// This widget will also highlight the labels below, assuming they're properly
// associated with the input being validated
// -jgh
require_once WPAM_BASE_DIRECTORY . "/html/widget_form_errors_panel.php";

$request = @$this->viewData['request'];
if(is_user_logged_in()){  //this block checks whether the user is logged in and already has an affiliate account
    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;  
    $query = "SELECT * FROM ".WPAM_AFFILIATES_TBL." WHERE userId = %d";        
    $affiliate = $wpdb->get_row($wpdb->prepare($query, $user_id));
    if($affiliate != null) { //The affiliate has an account already
        _e("Looks like you already have an affiliate account. So you don't need to register again. Simply go to the affiliate portal and start referring users.", 'affiliates-manager');
        return;
    }
}
?>

<form action="" method="post" id="mainForm" class="pure-form pure-form-stacked">
    <?php wp_nonce_field('wpam_reg_submit'); ?>
		<?php _e( '* Required fields', 'affiliates-manager' ) ?><br /><br />
		<fieldset>
			<?php foreach ($this->viewData['affiliateFields'] as $field) { ?>
					<label for="_<?php echo $field->databaseField?>"><?php _e( $field->name, 'affiliates-manager' ) ?><?php echo $field->required ? '&nbsp;*': '' ?></label>
					
						<?php switch ($field->fieldType) {
                                                        case 'email':
                                                            $email = (isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '';
                                                            if(is_user_logged_in()){
                                                                $current_user = wp_get_current_user();
                                                                $email = $current_user->user_email;
                                                                ?>
                                                                <input type="text" id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" value="<?php echo esc_attr($email)?>" readonly />
                                                                <p class="wpam_registration_input_help_text"><?php _e('This is the email address associated with your currently logged in WordPress user account.', 'affiliates-manager')?></p>
                                                                <p class="wpam_registration_input_help_text"><?php _e('If you want to use a different email address, log out of your WordPress account then try a new registration.', 'affiliates-manager')?></p>
                                                                <?php
                                                            }
                                                            else{
                                                            ?>
                                                            <input type="text" id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" value="<?php echo esc_attr($email)?>" <?php echo $field->required ? 'required': '' ?> />
                                                            <?php
                                                            }
                                                             break;
							case 'string':
							case 'number':
							case 'zipCode':
							?>
							<input type="text" id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" value="<?php echo esc_attr((isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '');?>" <?php echo $field->required ? 'required': '' ?> />
							<?php break;
                                                        case 'textarea':
							?>
							<textarea id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>"<?php echo $field->required ? ' required': '' ?>><?php echo esc_textarea((isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '');?></textarea>
							<?php break;
							case 'phoneNumber':?>
							<input type="text" id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" value="<?php echo esc_attr((isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '');?>" <?php echo $field->required ? 'required': '' ?> />
							<?php break;
							case 'ssn':?>
							<input type="password" size="3" maxlength="3" id="_<?php echo $field->databaseField?>[0]" name="_<?php echo $field->databaseField?>[0]" value="<?php echo esc_attr((isset($request['_'.$field->databaseField][0]) && !empty($request['_'.$field->databaseField][0])) ? $request['_'.$field->databaseField][0] : '');?>" /> -
							<input type="password" size="2" maxlength="2" id="_<?php echo $field->databaseField?>[1]" name="_<?php echo $field->databaseField?>[1]" value="<?php echo esc_attr((isset($request['_'.$field->databaseField][1]) && !empty($request['_'.$field->databaseField][1])) ? $request['_'.$field->databaseField][1] : '');?>" /> -
							<input type="password" size="4" maxlength="4" id="_<?php echo $field->databaseField?>[2]" name="_<?php echo $field->databaseField?>[2]" value="<?php echo esc_attr((isset($request['_'.$field->databaseField][2]) && !empty($request['_'.$field->databaseField][2])) ? $request['_'.$field->databaseField][2] : '');?>" />
							<?php break;
							case 'stateCode':?>
							<select id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" <?php echo $field->required ? 'required': '' ?>>
								<?php
                                                                $state_code = (isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '';
                                                                wpam_html_state_code_options($state_code); 
                                                                ?>
							</select>
							<?php break;
							case 'countryCode':?>
							<select id="_<?php echo $field->databaseField?>" name="_<?php echo $field->databaseField?>" <?php echo $field->required ? 'required': '' ?>>
								<?php
                                                                $country_code = (isset($request['_'.$field->databaseField]) && !empty($request['_'.$field->databaseField])) ? $request['_'.$field->databaseField] : '';
                                                                wpam_html_country_code_options($country_code); 
                                                                ?>
							</select>
							<?php break; default: break;
						}?>
			<?php } //end foreach ?>

                        <label for="chkAgreeTerms" id="agreeTermsLabel" class="pure-checkbox"><input type="checkbox" id="chkAgreeTerms" name="chkAgreeTerms" <?php echo (isset($request['chkAgreeTerms']) ? 'checked="checked"':'')?> />&nbsp;<?php _e('I have read and agree to the', 'affiliates-manager' ) ?> <a target="_blank" href="<?php echo get_option( WPAM_PluginConfig::$AffTncPageURL );?>"><?php _e('Terms and Conditions', 'affiliates-manager' ) ?></a></label>
                        <div id="termsAgreeWarning" style="color: red; display: none"><?php _e( 'You must agree to the terms.', 'affiliates-manager' ) ?></div>
			
               
                <?php 
                $output = apply_filters( 'wpam_before_registration_submit_button', '');
                if(!empty($output)){
                    echo $output;
                }
                ?> 
                <!--<div class="wpam-registration-form">-->
                <input type="hidden" name="wpam_reg_submit" value="1" />
                <input type="submit" name="submit" value="<?php _e( 'Submit Application', 'affiliates-manager' ) ?>" class="wpam-registration-form-submit pure-button pure-button-active" />
                <!--</div>-->  
                </fieldset>
</form>

<div id="tncDialog" style="display: none">
	<div id="termsBox" style="padding: 20px; width: auto; height: 380px; overflow: scroll; background-color: white; color: black; border: 1px solid black; white-space: pre-wrap;"><?php echo $this->viewData['tnc']?></div>
</div>
