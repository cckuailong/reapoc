<?php

class WPAM_Pages_Admin_SettingsPage {

    private $response;

    function __construct() {
        
    }

    public function render_settings_page() {   
        $request = $_REQUEST;
        $request = stripslashes_deep($request);

        if (isset($request['wpam_reset_logfile'])) {
            WPAM_Logger::reset_log_file();
            echo '<div class="updated fade"><p>Log file has been reset</p></div>';
        }

        if (isset($request['wpam_submit_settings']) && $request['wpam_submit_settings'] === '1') {
            $this->doFormSubmit($request);
        } else {
            $this->getSettingsForm();
        }

        echo $this->response->render();
    }

    protected function doFormSubmit($request) {
        $validator = new WPAM_Validation_Validator();
        if (isset($request['AffGeneralSettings'])) {
            $validator->addValidator('txtMinimumPayout', new WPAM_Validation_MoneyValidator());
            $validator->addValidator('txtCookieExpire', new WPAM_Validation_NumberValidator());
        }
        if (isset($request['AffRegSettings'])) {
            //$validator->addValidator('txtTnc', new WPAM_Validation_StringValidator(1));
        }
        //#61 allow these to be unset/null
        if (!empty($request['txtEmailName']))
            $validator->addValidator('txtEmailName', new WPAM_Validation_StringValidator(1));
        if (!empty($request['txtEmailAddress']))
            $validator->addValidator('txtEmailAddress', new WPAM_Validation_EmailValidator());

        if (isset($request['chkEnablePaypalMassPay'])) {
            $validator->addValidator('txtPaypalAPIUser', new WPAM_Validation_StringValidator(1));
            $validator->addValidator('txtPaypalAPIPassword', new WPAM_Validation_StringValidator(1));
            $validator->addValidator('txtPaypalAPISignature', new WPAM_Validation_StringValidator(1));
        }

        $vr = $validator->validate($request);

        if ($vr->getIsValid()) {
            $db = new WPAM_Data_DataAccess();
            if (isset($request['AffGeneralSettings'])) {  //General settings options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_general_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the General tab and Save Settings again.', 'affiliates-manager'));
                }
                update_option(WPAM_PluginConfig::$MinPayoutAmountOption, $request['txtMinimumPayout']);
                update_option(WPAM_PluginConfig::$CookieExpireOption, $request['txtCookieExpire']);
                update_option(WPAM_PluginConfig::$EmailNameOption, $request['txtEmailName']);
                update_option(WPAM_PluginConfig::$EmailAddressOption, $request['txtEmailAddress']);
                update_option(WPAM_PluginConfig::$AffBountyType, $request['affBountyType']);
                update_option(WPAM_PluginConfig::$AffBountyAmount, $request['affBountyAmount']);
                update_option(WPAM_PluginConfig::$AffCurrencySymbol, $request['affCurrencySymbol']);
                update_option(WPAM_PluginConfig::$AffCurrencyCode, $request['affCurrencyCode']);
                if (isset($request['autoaffapprove'])) {
                    update_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption, 1);
                } else {
                    update_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption, 0);
                }
                if (isset($request['doNotRecordZeroAmtCommission'])) {
                    update_option(WPAM_PluginConfig::$AffdoNotRecordZeroAmtCommission, 1);
                } else {
                    update_option(WPAM_PluginConfig::$AffdoNotRecordZeroAmtCommission, 0);
                }
                if (isset($request['chkImpressions'])) {
                    update_option(WPAM_PluginConfig::$AffEnableImpressions, 1);
                } else {
                    update_option(WPAM_PluginConfig::$AffEnableImpressions, 0);
                }
                if (isset($request['enable_debug'])) {
                    update_option(WPAM_PluginConfig::$AffEnableDebug, 1);
                } else {
                    update_option(WPAM_PluginConfig::$AffEnableDebug, 0);
                }
            }

            if (isset($request['AffPaymentSettings'])) {   //Payment settings options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_payment_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the Payment tab and Save Settings again.', 'affiliates-manager'));
                }
                if (isset($request['chkEnablePaypalMassPay'])) {
                    update_option(WPAM_PluginConfig::$PaypalMassPayEnabledOption, 1);
                    update_option(WPAM_PluginConfig::$PaypalAPIUserOption, $request['txtPaypalAPIUser']);
                    update_option(WPAM_PluginConfig::$PaypalAPIPasswordOption, $request['txtPaypalAPIPassword']);
                    update_option(WPAM_PluginConfig::$PaypalAPISignatureOption, $request['txtPaypalAPISignature']);
                    update_option(WPAM_PluginConfig::$PaypalAPIEndPointOption, $request['ddPaypalAPIEndPoint']);
                } else {
                    update_option(WPAM_PluginConfig::$PaypalMassPayEnabledOption, 0);
                }
            }

            if (isset($request['AffMsgSettings'])) {      //Messaging settings options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_msg_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the Messaging tab and Save Settings again.', 'affiliates-manager'));
                }
                foreach ($request['messages'] as $message) {
                    $messageModel = $db->getMessageRepository()->loadBy(array('name' => $message['name']));
                    if ($messageModel != NULL) {
                        $messageModel->content = $message['content'];
                        $db->getMessageRepository()->update($messageModel);
                    }
                }
                update_option(WPAM_PluginConfig::$EmailType, $request['emailType']);
                if (isset($request['sendAdminRegNotification'])) {
                    update_option(WPAM_PluginConfig::$SendAdminRegNotification, 1);
                } else {
                    update_option(WPAM_PluginConfig::$SendAdminRegNotification, 0);
                }
                update_option(WPAM_PluginConfig::$AdminRegNotificationEmail, $request['adminRegNotificationEmail']);
                if (isset($request['sendAffCommissionNotification'])) {
                    update_option(WPAM_PluginConfig::$SendAffCommissionNotification, 1);
                } else {
                    update_option(WPAM_PluginConfig::$SendAffCommissionNotification, 0);
                }
                if (isset($request['sendAdminAffCommissionNotification'])) {
                    update_option(WPAM_PluginConfig::$SendAdminAffCommissionNotification, 1);
                } else {
                    update_option(WPAM_PluginConfig::$SendAdminAffCommissionNotification, 0);
                }
                update_option(WPAM_PluginConfig::$AdminAffCommissionNotificationEmail, $request['adminAffCommissionNotificationEmail']);
            }

            if (isset($request['AffRegSettings'])) {    //Registration settings options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_reg_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the Affiliate Registration tab and Save Settings again.', 'affiliates-manager'));
                }
                if (isset($request['chkPayoutMethodManual'])) {
                    update_option(WPAM_PluginConfig::$PayoutMethodManualIsEnabledOption, 1);
                } else {
                    update_option(WPAM_PluginConfig::$PayoutMethodManualIsEnabledOption, 0);
                }
                if (isset($request['chkPayoutMethodPaypal'])) {
                    update_option(WPAM_PluginConfig::$PayoutMethodPaypalIsEnabledOption, 1);
                } else {
                    update_option(WPAM_PluginConfig::$PayoutMethodPaypalIsEnabledOption, 0);
                }
                if (isset($request['chkPayoutMethodCheck'])) {
                    update_option(WPAM_PluginConfig::$PayoutMethodCheckIsEnabledOption, 1);
                } else {
                    update_option(WPAM_PluginConfig::$PayoutMethodCheckIsEnabledOption, 0);
                }
                $affiliateFieldRepository = $db->getAffiliateFieldRepository();
                $affiliateFieldRepository->delete(array('type' => 'custom'));

                $order = 0;
                foreach ($request['field'] as $fieldName => $params) {
                    if ($params['type'] === 'custom') {

                        $field = new WPAM_Data_Models_AffiliateFieldModel();
                        $field->type = 'custom';
                        $field->databaseField = $fieldName;
                        $field->fieldType = $params['fieldType'];
                        $field->length = $params['maxLength'];
                        $field->name = $params['displayName'];
                    } else {
                        $field = $affiliateFieldRepository->loadby(array('databaseField' => $fieldName));
                    }

                    $field->order = $order++;
                    //#43 email is required (but not submitted b/c it's disabled on the form)
                    if ($fieldName == 'email') {
                        $field->enabled = 1;
                        $field->required = 1;
                    } else {
                        $field->enabled = isset($params['enabled']) ? 1 : 0;
                        $field->required = isset($params['required']) ? 1 : 0;
                    }

                    if ($params['type'] === 'custom') {
                        $affiliateFieldRepository->insert($field);
                    } else {
                        $affiliateFieldRepository->update($field);
                    }
                }              
                
                $affhomemsg = $request['affhomemsg'];
                if(empty($affhomemsg)){  //save the default home message if empty
                    $login_url = get_option(WPAM_PluginConfig::$AffLoginPageURL);
                    $register_page_id = get_option(WPAM_PluginConfig::$RegPageId);
                    $register_page_url = get_permalink($register_page_id);
                    $affhomemsg = sprintf( __( 'This is the affiliates section of this store. If you are an existing affiliate, please <a href="%s">log in</a> to access your control panel.', 'affiliates-manager' ), $login_url );
                    $affhomemsg .= '<br />';
                    $affhomemsg .= '<br />';
                    $affhomemsg .= sprintf( __( 'If you are not an affiliate, but wish to become one, you will need to apply. To apply, you must be a registered user on this blog. If you have an existing account on this blog, please <a href="%s">log in</a>. If not, please <a href="%s">register</a>.', 'affiliates-manager' ), $login_url, $register_page_url);
                }
                update_option(WPAM_PluginConfig::$AffHomeMsg, $affhomemsg);
                
                $affhomemsgnotregistered = $request['affhomemsgnotregistered'];
                if(empty($affhomemsgnotregistered)){  //save the default message if empty
                    $register_page_id = get_option(WPAM_PluginConfig::$RegPageId);
                    $register_page_url = get_permalink($register_page_id);
                    $affhomemsgnotregistered = sprintf( __( 'This is the affiliates section of this store. You are not currently an affiliate of this store. If you wish to become one, please <a href="%s"/>apply</a>.', 'affiliates-manager' ), $register_page_url );
                }
                update_option(WPAM_PluginConfig::$AffHomeMsgNotRegistered, $affhomemsgnotregistered);
            }

            if (isset($request['AffPagesSettings'])) {    //Affiliate pages/forms options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_pages_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the Pages/Forms tab and Save Settings again.', 'affiliates-manager'));
                }
                update_option(WPAM_PluginConfig::$AffHomePageURL, $request['affHomePage']);
                if(isset($request['affHomePage']) && !empty($request['affHomePage'])){
                    $home_page_id = url_to_postid($request['affHomePage']);
                    update_option(WPAM_PluginConfig::$HomePageId, $home_page_id);
                }
                update_option(WPAM_PluginConfig::$AffRegPageURL, $request['affRegPage']);
                if(isset($request['affRegPage']) && !empty($request['affRegPage'])){
                    $reg_page_id = url_to_postid($request['affRegPage']);
                    update_option(WPAM_PluginConfig::$RegPageId, $reg_page_id);
                }
                update_option(WPAM_PluginConfig::$AffLoginPageURL, $request['affLoginPage']);
                update_option(WPAM_PluginConfig::$AffTncPageURL, $request['affTncPage']);
            }
            
            if (isset($request['AffAdvancedSettings'])) {    //Advanced Settings options submitted
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'aff_advanced_settings_save')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the Advanced Settings tab and Save Settings again.', 'affiliates-manager'));
                }
                update_option(WPAM_PluginConfig::$AffLandingPageURL, $request['affLandingPage']);
                if (isset($request['disableOwnReferrals'])) {
                    update_option(WPAM_PluginConfig::$DisableOwnReferrals, 1);
                } else {
                    update_option(WPAM_PluginConfig::$DisableOwnReferrals, 0);
                }
                if (isset($request['autoDeleteWPUserAccount'])) {
                    update_option(WPAM_PluginConfig::$AutoDeleteWPUserAccount, 1);
                } else {
                    update_option(WPAM_PluginConfig::$AutoDeleteWPUserAccount, 0);
                }
            }

            return $this->getSettingsForm(NULL, "Settings updated.");
        }
        //else
        return $this->getSettingsForm($request, NULL, $vr);
    }

    protected function getSettingsForm($request = NULL, $message = NULL, $vr = NULL) {
        //add widget_form_error js to settings page
        add_action('admin_footer', array($this, 'onFooter'));

        $response = new WPAM_Pages_TemplateResponse('admin/settings/settings');
        $db = new WPAM_Data_DataAccess();

        $response->viewData['affiliateRegisterFields'] = $db->getAffiliateFieldRepository()->loadMultipleBy(array(), array('order' => 'asc'));
        $response->viewData['messages'] = $db->getMessageRepository()->loadAll();

        if ($request !== NULL) {
            $response->viewData['request']['affhomemsg'] = isset($request['affhomemsg']) ? $request['affhomemsg'] : '';
            $response->viewData['request']['affhomemsgnotregistered'] = isset($request['affhomemsgnotregistered']) ? $request['affhomemsgnotregistered'] : '';
            $response->viewData['request']['txtMinimumPayout'] = $request['txtMinimumPayout'];
            $response->viewData['request']['txtCookieExpire'] = $request['txtCookieExpire'];
            $response->viewData['request']['txtEmailName'] = $request['txtEmailName'];
            $response->viewData['request']['txtEmailAddress'] = $request['txtEmailAddress'];
            $response->viewData['request']['autoaffapprove'] = isset($request['autoaffapprove']) ? 1 : 0;
            $response->viewData['request']['affBountyType'] = $request['affBountyType'];
            $response->viewData['request']['affBountyAmount'] = $request['affBountyAmount'];
            $response->viewData['request']['affCurrencySymbol'] = $request['affCurrencySymbol'];
            $response->viewData['request']['affCurrencyCode'] = $request['affCurrencyCode'];
            $response->viewData['request']['doNotRecordZeroAmtCommission'] = isset($request['doNotRecordZeroAmtCommission']) ? 1 : 0;
            $response->viewData['request']['chkImpressions'] = isset($request['chkImpressions']) ? 1 : 0;
            $response->viewData['request']['enable_debug'] = isset($request['enable_debug']) ? 1 : 0;
            $response->viewData['request']['chkPayoutMethodCheck'] = isset($request['chkPayoutMethodCheck']) ? 1 : 0;
            $response->viewData['request']['chkPayoutMethodPaypal'] = isset($request['chkPayoutMethodPaypal']) ? 1 : 0;
            $response->viewData['request']['chkPayoutMethodManual'] = isset($request['chkPayoutMethodManual']) ? 1 : 0;
            $response->viewData['request']['emailType'] = $request['emailType'];
            $response->viewData['request']['sendAdminRegNotification'] = isset($request['sendAdminRegNotification']) ? 1 : 0;
            $response->viewData['request']['adminRegNotificationEmail'] = isset($request['adminRegNotificationEmail']) ? $request['adminRegNotificationEmail'] : '';
            $response->viewData['request']['sendAffCommissionNotification'] = isset($request['sendAffCommissionNotification']) ? 1 : 0;
            $response->viewData['request']['sendAdminAffCommissionNotification'] = isset($request['sendAdminAffCommissionNotification']) ? 1 : 0;
            $response->viewData['request']['adminAffCommissionNotificationEmail'] = isset($request['adminAffCommissionNotificationEmail']) ? $request['adminAffCommissionNotificationEmail'] : '';
            $response->viewData['request']['chkEnablePaypalMassPay'] = isset($request['chkEnablePaypalMassPay']) ? 1 : 0;
            $response->viewData['request']['txtPaypalAPIUser'] = isset($request['txtPaypalAPIUser']) ? $request['txtPaypalAPIUser'] : '';
            $response->viewData['request']['txtPaypalAPIPassword'] = isset($request['txtPaypalAPIPassword']) ? $request['txtPaypalAPIPassword'] : '';
            $response->viewData['request']['txtPaypalAPISignature'] = isset($request['txtPaypalAPISignature']) ? $request['txtPaypalAPISignature'] : '';
            $response->viewData['request']['ddPaypalAPIEndPoint'] = isset($request['ddPaypalAPIEndPoint']) ? $request['ddPaypalAPIEndPoint'] : '';
            $response->viewData['request']['affHomePage'] = isset($request['affHomePage']) ? $request['affHomePage'] : '';
            $response->viewData['request']['affRegPage'] = isset($request['affRegPage']) ? $request['affRegPage'] : '';
            $response->viewData['request']['affLoginPage'] = isset($request['affLoginPage']) ? $request['affLoginPage'] : '';
            $response->viewData['request']['affTncPage'] = isset($request['affTncPage']) ? $request['affTncPage'] : '';
            $response->viewData['request']['affLandingPage'] = isset($request['affLandingPage']) ? $request['affLandingPage'] : '';
            $response->viewData['request']['disableOwnReferrals'] = isset($request['disableOwnReferrals']) ? 1 : 0;
            $response->viewData['request']['autoDeleteWPUserAccount'] = isset($request['autoDeleteWPUserAccount']) ? 1 : 0;
            $response->viewData['validationResult'] = $vr;
        } else {
            $response->viewData['request']['affhomemsg'] = get_option(WPAM_PluginConfig::$AffHomeMsg);
            $response->viewData['request']['affhomemsgnotregistered'] = get_option(WPAM_PluginConfig::$AffHomeMsgNotRegistered);
            $response->viewData['request']['txtMinimumPayout'] = get_option(WPAM_PluginConfig::$MinPayoutAmountOption);
            $response->viewData['request']['txtCookieExpire'] = get_option(WPAM_PluginConfig::$CookieExpireOption);
            $response->viewData['request']['txtEmailName'] = get_option(WPAM_PluginConfig::$EmailNameOption);
            $response->viewData['request']['txtEmailAddress'] = get_option(WPAM_PluginConfig::$EmailAddressOption);
            $response->viewData['request']['autoaffapprove'] = get_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption);
            $response->viewData['request']['affBountyType'] = get_option(WPAM_PluginConfig::$AffBountyType);
            $response->viewData['request']['affBountyAmount'] = get_option(WPAM_PluginConfig::$AffBountyAmount);
            $response->viewData['request']['affCurrencySymbol'] = get_option(WPAM_PluginConfig::$AffCurrencySymbol);
            $response->viewData['request']['affCurrencyCode'] = get_option(WPAM_PluginConfig::$AffCurrencyCode);
            $response->viewData['request']['doNotRecordZeroAmtCommission'] = get_option(WPAM_PluginConfig::$AffdoNotRecordZeroAmtCommission);
            $response->viewData['request']['chkImpressions'] = get_option(WPAM_PluginConfig::$AffEnableImpressions);
            $response->viewData['request']['enable_debug'] = get_option(WPAM_PluginConfig::$AffEnableDebug);
            $response->viewData['request']['chkPayoutMethodCheck'] = get_option(WPAM_PluginConfig::$PayoutMethodCheckIsEnabledOption);
            $response->viewData['request']['chkPayoutMethodPaypal'] = get_option(WPAM_PluginConfig::$PayoutMethodPaypalIsEnabledOption);
            $response->viewData['request']['chkPayoutMethodManual'] = get_option(WPAM_PluginConfig::$PayoutMethodManualIsEnabledOption);
            $response->viewData['request']['emailType'] = get_option(WPAM_PluginConfig::$EmailType);
            $response->viewData['request']['sendAdminRegNotification'] = get_option(WPAM_PluginConfig::$SendAdminRegNotification);
            $response->viewData['request']['adminRegNotificationEmail'] = get_option(WPAM_PluginConfig::$AdminRegNotificationEmail);
            $response->viewData['request']['sendAffCommissionNotification'] = get_option(WPAM_PluginConfig::$SendAffCommissionNotification);
            $response->viewData['request']['sendAdminAffCommissionNotification'] = get_option(WPAM_PluginConfig::$SendAdminAffCommissionNotification);
            $response->viewData['request']['adminAffCommissionNotificationEmail'] = get_option(WPAM_PluginConfig::$AdminAffCommissionNotificationEmail);
            $response->viewData['request']['chkEnablePaypalMassPay'] = get_option(WPAM_PluginConfig::$PaypalMassPayEnabledOption);
            $response->viewData['request']['txtPaypalAPIUser'] = get_option(WPAM_PluginConfig::$PaypalAPIUserOption);
            $response->viewData['request']['txtPaypalAPIPassword'] = get_option(WPAM_PluginConfig::$PaypalAPIPasswordOption);
            $response->viewData['request']['txtPaypalAPISignature'] = get_option(WPAM_PluginConfig::$PaypalAPISignatureOption);
            $response->viewData['request']['ddPaypalAPIEndPoint'] = get_option(WPAM_PluginConfig::$PaypalAPIEndPointOption);
            $response->viewData['request']['affHomePage'] = get_option(WPAM_PluginConfig::$AffHomePageURL);
            $response->viewData['request']['affRegPage'] = get_option(WPAM_PluginConfig::$AffRegPageURL);
            $response->viewData['request']['affLoginPage'] = get_option(WPAM_PluginConfig::$AffLoginPageURL);
            $response->viewData['request']['affTncPage'] = get_option(WPAM_PluginConfig::$AffTncPageURL);
            $response->viewData['request']['affLandingPage'] = get_option(WPAM_PluginConfig::$AffLandingPageURL);
            $response->viewData['request']['disableOwnReferrals'] = get_option(WPAM_PluginConfig::$DisableOwnReferrals);
            $response->viewData['request']['autoDeleteWPUserAccount'] = get_option(WPAM_PluginConfig::$AutoDeleteWPUserAccount);
        }

        if ($message !== NULL)
            $response->viewData['updateMessage'] = $message;

        //save for form validation in the footer
        $this->response = $response;

        return $response;
    }

    public function onFooter() {
        $response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
        echo $response->render();
    }

}
