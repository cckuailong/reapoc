<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_thirdparty.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


<?php
$form = new RM_PFBC_Form("options_thirdparty");
$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

$form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('GLOBAL_SETTINGS_EXTERNAL_INTEGRATIONS') . '</div>'));

/*
$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_LOGIN_FACEBOOK_OPTION'), "enable_facebook", array("yes" => ''), array("id" => "id_rm_enable_fb_cb", "class" => "id_rm_enable_fb_cb", "value" => $data['enable_facebook'], "onclick" => "hide_show(this)", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_ENABLE'))));
if ($data['enable_facebook'] == 'yes')
    $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_fb_cb_childfieldsrow">'));
else
    $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_fb_cb_childfieldsrow" style="display:none">'));


$form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_FACEBOOK_APP_ID'), "facebook_app_id", array("value" => $data['facebook_app_id'], "id" => "id_rm_fb_appid_tb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_APPID'))));
//$form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_FACEBOOK_SECRET'), "facebook_app_secret", array("value" => $data['facebook_app_secret'], "id" => "id_rm_fb_appsecret_tb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_FB_SECRET'))));

$form->addElement(new Element_HTML("</div>"));
 */

$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_MAILCHIMP_INTEGRATION'), "enable_mailchimp", array("yes" => ''), array("id" => "id_rm_enable_mc_cb", "class" => "id_rm_enable_mc_cb", "value" => $data['enable_mailchimp'], "onclick" => "hide_show(this)", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_MC_ENABLE'))));

if ($data['enable_mailchimp'] == 'yes')
    $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_mc_cb_childfieldsrow">'));
else
    $form->addElement(new Element_HTML('<div class="childfieldsrow" id="id_rm_enable_mc_cb_childfieldsrow" style="display:none">'));

$form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_MAILCHIMP_API'), "mailchimp_key", array("value" => $data['mailchimp_key'], "id" => "id_rm_mc_key_tb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_MC_ENABLE'))));
$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_MAILCHIMP_DOUBLE_OPTIN'), "mailchimp_double_optin", array("yes" => ''),array("id" => "id_rm_mc_dbl_optin", "class" => "id_rm_mc_dbl_optin" , "value" =>  $data['mailchimp_double_optin'],  "onclick" => "hide_show(this)", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_MC_DBL_OPTIN'))));
$form->addElement(new Element_HTML("</div>"));
$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_AWEBER_OPTION_INTEGRATION'), "enable_aweber", array("yes" => ''), array("id" => "", "class" => "", "value" => "", "disabled" => "disabled", "readonly" => "readonly", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
$form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_GOOGLE_API_KEY'), "google_map_key", array("value" => $data['google_map_key'], "id" => "id_rm_ggl_key_tb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_THIRDPARTY_GGL_API'))));
    $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_DROPBOX_OPTIONS_INTEGRATION'), "enable_aweber", array("yes" => ''), array("id" => "", "class" => "", "value" => "", "disabled" => "disabled", "readonly" => "readonly", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__("Cancel",'custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

$form->render();
?>
    </div>
        <?php
        include RM_ADMIN_DIR . 'views/template_rm_promo_banner_bottom.php';
        ?>
</div>

<?php }