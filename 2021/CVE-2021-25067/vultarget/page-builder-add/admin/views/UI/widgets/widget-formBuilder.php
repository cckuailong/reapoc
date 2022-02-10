<?php if ( ! defined( 'ABSPATH' ) ) exit; 
?>

<?php

$pbp_is_MCextensionActive = false;
if ( is_plugin_active('page-builder-add-mailchimp-extension/page-builder-add-mailchimp-extension.php')  || is_plugin_active('PluginOps-Extensions-Pack/extension-pack.php') ) {
  $pbp_is_MCextensionActive = true;
}

$pbp_MCtabDisplay = 'none';
$pbp_MCNotice = 'block';
$pbp_customFormField = 'none';
if ($pbp_is_MCextensionActive == true) {
    $pbp_MCtabDisplay = 'inline-block';
    $pbp_customFormField = 'block';
    $pbp_MCNotice = 'none';
}

?>

<form id="formBuilderWidgetOpsForm">
<div class="pluginops-tabs2" style="width: 99%; min-width: 400px;">
  <ul class="pluginops-tab2-links formBuilderTabs">
    <li class="active" style="margin: 0;"><a href="#formBuilderTab_cf1" class="pluginops-tab2_link">Fields</a></li>
    <li style="margin: 0;"><a href="#formBuilderTab_cf2" class="pluginops-tab2_link">Field Styles</a></li>
    <li style="margin: 0;"><a href="#formBuilderTab_cf3" class="pluginops-tab2_link">Button Styles</a></li>
    <li style="margin: 0; "><a href="#formBuilderTab_cf5" class="pluginops-tab2_link">Integrations</a></li>
    <li style="margin: 0;"><a href="#formBuilderTab_cf6" class="pluginops-tab2_link"> Actions </a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;" >
    <div id="formBuilderTab_cf1" class="pluginops-tab2 active">
          <div class="btn btn-blue" id="addNewFormField" > <span class="dashicons dashicons-plus-alt"></span> Add Field </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget  formFieldItemsContainer">
            
          </ul>
          
          <ul class="PB_accordion_customHTMlForm  customFormHtmlContianer" style=" display: <?php echo $pbp_customFormField; ?> ; ">
            <h4>Custom HTML Form</h4>
            <div>
                <textarea class="formCustomHTML" style="width: 350px; height: 300px;" data-optname="formCustomHTML"></textarea>
            </div>
          </ul>
          
    </div>
    <div id="formBuilderTab_cf2" class="pluginops-tab2">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
            <label>Field Size: </label>
            <select class="formBuilderFieldSize" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldSize" >
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
            </select>
            <br><br><hr><br>
            <label>Vertical Gap</label>
            <input type="number" class="formBuilderFieldVGap" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldVGap" >%
            <br><br><hr><br>
            <label>Horizontal Gap</label>
            <input type="number" class="formBuilderFieldHGap" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldHGap" >%
            <br><br><hr><br>
            <label>Display Labels : </label>
            <select class="formBuilderFieldLabelDisplay" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldLabelDisplay" >
                <option value="unset">Yes</option>
                <option value="none">No</option>
            </select>
            <br><br><hr><br>
            <label>Label Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderLabelColor" id="formBuilderLabelColor" value='#333333' data-alpha='true' data-optname="widgetPbFbFormFeildOptions.formBuilderLabelColor" >
            <br><br><hr><br>
            <div>
                <h4>Label Font Size
                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                    <label></label>
                    <input type="number" class="formBuilderLabelSize" data-optname="widgetPbFbFormFeildOptions.formBuilderLabelSize" >px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                    <input type="number" class="formBuilderLabelSizeTablet" data-optname="widgetPbFbFormFeildOptions.formBuilderLabelSizeTablet" >px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                    <input type="number" class="formBuilderLabelSizeMobile" data-optname="widgetPbFbFormFeildOptions.formBuilderLabelSizeMobile" >px
                </div>
            </div>
            <br><br><hr><br>
            <label>Text Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderFieldColor" id="formBuilderFieldColor" value='#333333' data-alpha='true' data-optname="widgetPbFbFormFeildOptions.formBuilderFieldColor" >
            <br><br><hr><br>
            <label>Font family :</label>
            <input class="formBuilderFieldFontFamily gFontSelectorulpb" id="formBuilderFieldFontFamily" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldFontFamily" >
            <br><br><hr><br><br>
            <label>Background Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderFieldBgColor" id="formBuilderFieldBgColor" value='#333333' data-alpha='true' data-optname="widgetPbFbFormFeildOptions.formBuilderFieldBgColor" >
            <br><br><hr><br>
            <label>Border Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderFieldBorderColor" id="formBuilderFieldBorderColor" value='#333333' data-alpha='true' data-optname="widgetPbFbFormFeildOptions.formBuilderFieldBorderColor" >
            <br><br><hr><br>
            <label>Border Width : </label>
            <input type="number" class="formBuilderFieldBorderWidth" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldBorderWidth" >
            <br><br><hr><br>
            <label>Corner Radius : </label>
            <input type="number" class="formBuilderFieldBorderRadius" data-optname="widgetPbFbFormFeildOptions.formBuilderFieldBorderRadius" >
            <br>
            <br>
            <br>
        </div>
    </div>
    <div id="formBuilderTab_cf3" class="pluginops-tab2">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >
            <br>
            <label>Button Text</label>
            <input type="text" class="formBuilderBtnText"  placeholder="Button Text" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnText" >
            <br><br><hr><br>
            <label>Button Size: </label>
            <select class="formBuilderBtnSize" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnSize" >
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
            </select>
            <br><br><hr><br>
            <label> Field Width :</label>
            <select class="formBuilderBtnWidth" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnWidth" >
                <option value="100">Default</option>
                <option value="20">20%</option>
                <option value="25">25%</option>
                <option value="33">33%</option>
                <option value="40">40%</option>
                <option value="50">50%</option>
                <option value="60">60%</option>
                <option value="66">66%</option>
                <option value="75">75%</option>
                <option value="80">80%</option>
                <option value="100">100%</option>
            </select>
            <br><br><hr><br>
            <label>Vertical Gap</label>
            <input type="number" class="formBuilderBtnVGap" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnVGap" >%
            <br><br><hr><br>
            <label>Horizontal Gap</label>
            <input type="number" class="formBuilderBtnHGap" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnHGap" >%
            <br><br><hr><br>
            <label>Background Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderBtnBgColor" id="formBuilderBtnBgColor" value='#333333' data-alpha='true' data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnBgColor" >
            <br><br><hr><br>
            <label>Hover BG Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderBtnHoverBgColor" id="formBuilderBtnHoverBgColor" data-alpha='true' value='#333333' data-alpha='true' data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnHoverBgColor" >
            <br><br><hr><br>
            <label>Hover Text Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderBtnHoverTextColor" id="formBuilderBtnHoverTextColor" data-alpha='true' value='#333333' data-alpha='true' data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnHoverTextColor" >
            <br><br><hr><br>
            <label>Text Color :</label>
            <input type="text" class="color-picker_btn_two formBuilderBtnColor" id="formBuilderBtnColor" data-alpha='true' data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnColor" >
            <br><br><hr><br>
            <div>
                <h4>Font size
                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                    <label></label>
                    <input type="number" class="formBuilderBtnFontSize" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnFontSize" >px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                    <input type="number" class="formBuilderBtnFontSizeTablet" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnFontSizeTablet" >px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                    <input type="number" class="formBuilderBtnFontSizeMobile" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnFontSizeMobile" >px
                </div>
            </div>
            <br><br><hr><br>
            <label>Font family :</label>
            <input class="formBuilderBtnFontFamily gFontSelectorulpb" id="formBuilderBtnFontFamily" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnFontFamily" >
            <br><br><hr><br>
            <label>Border Width: </label>
            <input type="number" class="formBuilderBtnBorderWidth" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnBorderWidth" >
            <br><br><hr><br>
            <label>Border Color: </label>
            <input type="text" class="color-picker_btn_two formBuilderBtnBorderColor" id="formBuilderBtnBorderColor" value='#ffffff' data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnBorderColor" >
            <br><br><hr><br>
            <label>Corner Radius: </label>
            <input type="number" class="formBuilderBtnBorderRadius" max="100" min="0" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnBorderRadius" >
            <br><br><hr><br>
            <label>Alignment :</label>
            <select class="formBuilderBtnAlignment" data-optname="widgetPbFbFormSubmitOptions.formBuilderBtnAlignment" >
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
            </select>
            <br><br><hr><br>
            <label>Select Icon</label>
            <input data-placement="bottomRight" class="icp pbicp-auto formBuilderbtnSelectedIconpbicp-auto" value="" type="text" data-optname="widgetPbFbFormSubmitOptions.formBuilderbtnSelectedIcon" />
            <span class="input-group-addon formBuilderbtnSelectedIcon" style="font-size: 16px;"></span>
            <br><br><hr><br>
            <label>Icon Position</label>
            <select class="formBuilderbtnIconPosition" data-optname="widgetPbFbFormSubmitOptions.formBuilderbtnIconPosition" >
                <option value="before">Before Text</option>
                <option value="after">After Text</option>
            </select>
            <br><br><hr><br>
            <label>Icon Gap</label>
            <input type="number" class="formBuilderbtnIconGap" data-optname="widgetPbFbFormSubmitOptions.formBuilderbtnIconGap" >px
            <br><br><hr><br>
            <label>Icon Hover Animation</label>
            <select class="formBuilderbtnIconAnimation" data-optname="widgetPbFbFormSubmitOptions.formBuilderbtnIconAnimation" >
                <option value="">None</option>
                <optgroup label="Attention Seekers">
                    <option value="bounce">bounce</option>
                    <option value="flash">flash</option>
                    <option value="pulse">pulse</option>
                    <option value="rubberBand">rubberBand</option>
                    <option value="shake">shake</option>
                    <option value="swing">swing</option>
                    <option value="tada">tada</option>
                    <option value="wobble">wobble</option>
                    <option value="jello">jello</option>
                    <option value="flip">flip</option>
                </optgroup>
            </select>
            <br><br><hr><br>
            <br>
            <br>
        </div>
    </div>
    <div id="formBuilderTab_cf5" class="pluginops-tab2">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >

            <?php 
                $mailPoetLists = '<p> MailPoet 3 Plugin is not Active please install & activate it and then reload this page to complete integration. </p>';
                $MPdropdownListMessage = '';
                if (class_exists(\MailPoet\API\API::class)) {
                  // Get MailPoet API instance
                  $mailpoet_api = \MailPoet\API\API::MP('v1');
                  // Get available list so that a subscriber can choose in which to subscribe 
                  $mail_poet_lists = $mailpoet_api->getLists();

                  if ( !is_array($mail_poet_lists) ) {
                      $mail_poet_lists = array();
                      $MPdropdownListMessage = "<p> No Lists Found - Please add a list to your MailPoet and reload this page. </p> \n ";
                      $MPdropdownListMessage = '';
                  }else{
                    $MPdropdownLists = '';
                    foreach ($mail_poet_lists as $value) {
                    $dropdownMPListThis = "<option value='".$value['id']."'>".$value['name']."</option> \n ";

                    $MPdropdownLists = $MPdropdownLists.$dropdownMPListThis;
                    }

                  }
                  
                  if ($MPdropdownLists != '') {
                        $mailPoetLists = '
                            <label>Enable MailPoet</label>
                            <select class="wfbMPEnable" data-optname="widgetPbFbFormMailChimp.wfbMPEnable" >
                                <option value="">Select</option>
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                            <br><br><br><br><hr><br>
                            <label>Select List</label>
                            <select class="wfbMPList" data-optname="widgetPbFbFormMailChimp.wfbMPList" >
                                <option value="">Select</option>
                                '.$MPdropdownLists.' 
                            </select>
                            <br><br><br><br><hr><br>
                            <label>Confirmation Email</label>
                            <select class="wfbMPConfEmail" data-optname="widgetPbFbFormMailChimp.wfbMPConfEmail" >
                                <option value="">Select</option>
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                            <br><br><br><br><hr><br>
                            <label>Welcome Email</label>
                            <select class="wfbMPWelcEmail" data-optname="widgetPbFbFormMailChimp.wfbMPWelcEmail" >
                                <option value="">Select</option>
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                        ';
                    
                  }else{
                    $mailPoetLists = $MPdropdownListMessage;
                  }

                }
            ?>

            <?php  
                if (function_exists('ulpb_prem_extension_integrations_form_builder')) {
                    ulpb_prem_extension_integrations_form_builder( $mailPoetLists );  
                } else{
                    echo '
                    <br>
                    <label>Service</label>
                    <select class="formDataSaveType">
                        <option value="" >Email Notification</option>
                        <option value="database" disabled="disabled">Database (Pro Only)</option>
                        <option value="" disabled="disabled">MailChimp (Pro Only)</option>
                        <option value="" disabled="disabled">Get Response (Pro Only)</option>
                        <option value="" disabled="disabled">Active Campaign (Pro Only)</option>
                        <option value="" disabled="disabled">Campaign Monitor (Pro Only)</option>
                        <option value="" disabled="disabled">Aweber (Pro Only)</option>
                        <option value="" disabled="disabled">Drip (Pro Only)</option>
                        <option value="" disabled="disabled">MarketHero (Pro Only)</option>
                        <option value="" disabled="disabled">SendInBlue (Pro Only)</option>
                        <option value="" disabled="disabled"> ConvertKit (Pro Only)</option>
                        <option value="" disabled="disabled"> MailPoet (Pro Only)</option>
                        <option value="" disabled="disabled"> ConstantContact (Pro Only)</option>
                        <option value="" disabled="disabled">Custom Webhook (Pro Only)</option>
                    </select>
                    <br><br><br><hr><br>

                    <div class="PB_accordion" style="width:90% !important;">
                        <h4>Email Notification</h4>
                        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                            <br>
                            <label>Form Name :</label>
                            <input type="text" class="formEmailformName" data-optname="widgetPbFbFormEmailOptions.formEmailformName">
                            <br>
                            <br>
                            <hr>
                            <br>
                            <label>Email To :</label>
                            <input type="text" class="formEmailTo" data-optname="widgetPbFbFormEmailOptions.formEmailTo">
                            <br>
                            <br>
                            <hr>
                            <br>
                            <label>From Email Address :</label>
                            <input type="text" class="formEmailfromEmail" data-optname="widgetPbFbFormEmailOptions.formEmailfromEmail">
                            <br>
                            <br>
                            <p style="font-size:14px;"><i>Note :</i> Enter a valid email address. e.g : email@yourdomain.com also you can\'t use same email address. </p>
                            <hr>
                            <br>
                            <label>Email Subject :</label>
                            <input type="text" class="formEmailSubject" data-optname="widgetPbFbFormEmailOptions.formEmailSubject">
                            <label style="display: none;">From Email :</label>
                            <input type="hidden" class="formEmailFromEmail" data-optname="widgetPbFbFormEmailOptions.formEmailFromEmail">
                            <br>
                            <br>
                            <hr>
                            <br>
                            <label>Email From Name :</label>
                            <input type="text" class="formEmailName" data-optname="widgetPbFbFormEmailOptions.formEmailName">
                            <br>
                            <br>
                            <hr>
                            <br>
                            <label>Email Format :</label>
                            <select class="formEmailFormat" data-optname="widgetPbFbFormEmailOptions.formEmailFormat">
                                <option value="plain">Plain Text</option>
                                <option value="HTML">HTML</option>
                            </select>
                            <br>
                            <br>
                            <hr>
                            <br>
                            <label>Allow Duplicates : </label>
                            <select class="widgetPbFbFormAllowDuplicates" data-optname="widgetPbFbFormAllowDuplicates">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                            <br>
                            <br>
                            <hr>
                            <br>
                            <br>
                            <label>Require All Integrations : </label>
                            <select class="widgetPbFbFormReqAllIntegration" data-optname="widgetPbFbFormReqAllIntegration">
                                <option value="">Select</option>
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                            <br>
                            <br>
                            <p style="font-size:14px;"> Require all integration conditions to be true to show success message. </p>
                            <br>
                            <br>
                            <hr>
                            <br>
                            <br>
                        </div>

                        <h4>Google ReCaptcha </h4>
                        <div class="pbp_form">
                            <label>Select ReCaptcha</label>
                            <select class="fbgreCaptcha" data-optname="widgetPbFbFormMailChimp.fbgreCaptcha" >
                                <option value="false">Off</option>
                                <option value="true">v2</option>
                                <option value="v3">v3</option>
                            </select>
                            <br><br><br><br><hr><br>
                            <label>Site Key <span style="font-size:12px;">(ReCaptcha)</span></label>
                            <input type="text" class="fbgreCSiteKey" style="width: 200px;" data-optname="widgetPbFbFormMailChimp.fbgreCSiteKey" >
                            <br><br><br><br><br><hr><br>
                            <label>Secret key <span style="font-size:12px;">(ReCaptcha)</span></label>
                            <input type="text" class="fbgreCSiteSecret" style="width: 200px;" data-optname="widgetPbFbFormMailChimp.fbgreCSiteSecret" >
                            <br><br><br><br><hr><br>
                        </div>

                    </div>
                    ';
                }
            ?>

            <input type="hidden" class="formBuilderMCGroupsList" style="width: 200px;" data-optname="widgetPbFbFormMailChimp.formBuilderMCGroupsList">
            
            <input type="hidden" class="formBuilderMRGroupsList" style="width: 200px;" data-optname="widgetPbFbFormMailChimp.formBuilderMRGroupsList">
        </div>
    </div>
    <div id="formBuilderTab_cf6" class="pluginops-tab2">
        <div class="PB_accordion" style="width:100% !important;">
            <h4>Success Actions</h4>
            <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >
                <label>Success Action :</label>
                <select class="formSuccessAction" data-optname="widgetPbFbFormEmailOptions.formSuccessAction" >
                    <option value="showMessage">Show Message</option>
                    <option value="redirect">Redirect To URL</option>
                </select>
                <br><br><hr><br>
                <div class="successFormActionCont" style="display: none; background: #fff;">
                    <label>Redirect URL <span style="font-size:10px">(With http://)</span></label>
                    <input type="url" class="formSuccessActionURL" placeholder="URL" data-optname="widgetPbFbFormEmailOptions.formSuccessActionURL" >
                    <br><br><hr><br>
                </div>
                <label>Success Message :</label> <br>
                <input type="text" class="formSuccessMessage" style="width: 280px;" data-optname="widgetPbFbFormEmailOptions.formSuccessMessage" >
                <br><br><br><hr><br>
                <label>Custom Action (JavaScript) <span style="font-size: 10px;">Only enter scripts here</span></label>
                <textarea class="formSuccessCustomAction"  rows="7" style="width: 280px;" data-optname="widgetPbFbFormEmailOptions.formSuccessCustomAction" ></textarea>
            </div>
            <h4>Processing Action</h4>
            <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >
                <label>Custom Action (JavaScript) <span style="font-size: 10px;">Scripts for data validation while processing</span> </label>
                <textarea class="formProcessCustomAction"  rows="7" style="width: 280px;" data-optname="widgetPbFbFormEmailOptions.formProcessCustomAction" ></textarea>
                <br><br><br><hr><br>
            </div>
            <h4>Duplicate Submission</h4>
            <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >
                <label>Message :</label> <br>
                <input type="text" class="formDuplicateMessage" style="width: 200px;" data-optname="widgetPbFbFormEmailOptions.formDuplicateMessage" >
                <br><br><br><hr><br>
            </div>
            <h4>Failure Actions</h4>
            <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;" >
                <label>Failure Message :</label> <br>
                <input type="text" class="formFailureMessage" style="width: 200px;" data-optname="widgetPbFbFormEmailOptions.formFailureMessage" >
                <br><br><br><hr><br>
                <label>Required Field Message :</label> <br>
                <input type="text" class="formRequiredFieldMessage" style="width: 200px;" data-optname="widgetPbFbFormEmailOptions.formRequiredFieldMessage" >
                <br><br><br><hr><br>
                <label>Custom Action (JavaScript)</label>
                <textarea class="formFailureCustomAction"  rows="7" style="width: 280px;" data-optname="widgetPbFbFormEmailOptions.formFailureCustomAction" ></textarea>
                <br><br><br><hr><br>
            </div>

        </div>
    </div>
</div>
</div>
</form>
<p class="widgetErrorNotice formErrorNotice"> Your email notification is not setup properly and you will not receive email notification when user submits their information, Go to integrations tab to setup email notification to not lose any valuable information. </p>

<p class="widgetErrorNotice fromEmailErrorNotice"> "Email To" & "From Email Address" fields must not have same values. </p>

<h3 style="display: <?php echo $pbp_MCNotice; ?> ;"> Tip You can also sync your form data with MailChimp and also save it in database : <a href="https://pluginops.com/page-builder/?ref=formbuilderWidget" target="_blank"> Learn More :</a></h3>

<script type="text/javascript">
(function($){
(function (original) {
  jQuery.fn.clone = function () {
    var result           = original.apply(this, arguments),
        my_textareas     = this.find('textarea').add(this.filter('textarea')),
        result_textareas = result.find('textarea').add(result.filter('textarea')),
        my_selects       = this.find('select').add(this.filter('select')),
        result_selects   = result.find('select').add(result.filter('select'));

    for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
    for (var i = 0, l = my_selects.length;   i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;

    return result;
  };
}) (jQuery.fn.clone);

})(jQuery);
</script>

<script type="text/javascript">
    (function($){
        jQuery('#addNewFormField').on('click',function(){
            var index = $(".formFieldItemsContainer li").length;
            jQuery('.formFieldItemsContainer').append('<li>'+
                ' <h3 class="handleHeader"> Field<span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <span class="dashicons dashicons-admin-page slideDuplicateButton" style="float: right;" title="Duplicate"></span> </h3>'+
                '<div class="accordContentHolder" style="background: #fff;"> '+
                    '<label>Type : </label>'+
                    '<select class="fbFieldType" data-optname="widgetPbFbFormFeilds.'+index+'.fbFieldType" >'+
                        '<option value="text">Text</option> '+
                        '<option value="tel">Tel</option> '+
                        '<option value="email">Email</option> '+
                        '<option value="number">Number</option> '+
                        '<option value="url">URL</option> '+
                        '<option value="date">Date</option> '+
                        '<option value="time">Time</option> '+
                        '<option value="textarea">Textarea</option> '+
                        '<option value="select">Select</option> '+
                        '<option value="radio">Radio</option> '+
                        '<option value="checkbox">Checkbox</option> '+
                        '<option value="hidden">Hidden</option> '+
                        '<option value="html">Text/HTML</option> '+
                    '</select>'+
                    '<br> <br> <hr> <br> '+
                    '<div class="thisFieldOptions"> '+
                        '<label> Label :</label>'+
                        '<input type="text" class="fbFieldLabel" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldLabel" > <br> <br> <hr> <br>'+
                        '<label> Field Name :</label>'+
                        '<input type="text" class="fbFieldName" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldName" > <br> <br> <hr> <br>'+
                        '<label> Placeholder :</label>'+
                        '<input type="text" class="fbFieldPlaceHolder" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldPlaceHolder" > <br> <br> <hr> <br> '+
                        '<label> Required :</label>'+
                        '<select class="fbFieldRequired" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldRequired" > <option value="false">No</option> <option value="true">Yes</option> </select> <br> <br> <hr> <br> '+
                        '<label> Field Width :</label>'+
                        '<select class="fbFieldWidth" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldWidth" > '+
                            '<option value="100">Default</option> '+
                            '<option value="20">20%</option> '+
                            '<option value="25">25%</option> '+
                            '<option value="33">33%</option> '+
                            '<option value="40">40%</option> '+
                            '<option value="50">50%</option> '+
                            '<option value="60">60%</option> '+
                            '<option value="66">66%</option> '+
                            '<option value="75">75%</option> '+
                            '<option value="80">80%</option> '+
                            '<option value="100">100%</option> '+
                        '</select>  <br> <br> <hr> <br>  '+
                        '<label> Preset Value :</label>'+
                        '<input type="text" class="fbFieldPreset" value="" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbFieldPreset" >  <br> <br> <hr> <br>'+
                    '</div> <br> <br> '+
                    '<div class="textareaOptions pb_hidden"> '+
                        '<label>Textarea Rows: </label> '+
                        '<input type="number" class="fbtextareaRows" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbtextareaRows" > <br> <hr> <br> <br>'+
                    '</div>'+
                    '<div class="textHtmlFeildOptions pb_hidden"> '+
                        '<label>Enter Text or HTML :</label> '+
                        '<textarea class="fbTextContent" rows="5" value="" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.fbTextContent" style="width:310px;" ></textarea>'+
                        '<br><hr><br><br>'+
                    '</div>'+
                    '<div class="multiOptionField pb_hidden">'+
                        '<label>Options: </label>'+
                        '<textarea class="multiOptionFieldValues" rows="5" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.multiOptionFieldValues" ></textarea> <br> <span> Enter each option in separate line.</span> <br> <hr> <br> <br>'+
                        '<label>Display Inline :</label>'+
                        '<select class="displayFieldsInline" data-optname="widgetPbFbFormFeilds.'+index+'.thisFieldOptions.displayFieldsInline" >'+
                            '<option value="inline-block">Yes</option>'+
                            '<option value="block">No</option>'+
                        '</select>'+
                    '</div> '+
                '</div> '+
            '</li>');

            jQuery( '.formFieldItemsContainer' ).accordion( "refresh" );

            pageBuilderApp.changedOpType = 'specific';
            pageBuilderApp.changedOpName = 'slideListEdit';
            
            var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
            jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

            ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
            currentEditableColId = jQuery('.currentEditableColId').val();
            jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');
        }); // CLICK function ends here.


        $(document).on( 'change','.fbFieldType', function(){
            var currentSelectedVal = $(this).val();

            $(this).siblings('.thisFieldOptions').removeClass('pb_hidden');
            $(this).siblings('.mcgroupsFieldContainer').addClass('pb_hidden');
            if (currentSelectedVal == 'textarea') {
                $(this).siblings('.textareaOptions').removeClass('pb_hidden');
                $(this).siblings('.multiOptionField').addClass('pb_hidden');
                $(this).siblings('.textHtmlFeildOptions').addClass('pb_hidden');

            } else if(currentSelectedVal == 'select' || currentSelectedVal == 'radio' || currentSelectedVal == 'checkbox'){
                $(this).siblings('.multiOptionField').removeClass('pb_hidden');
                $(this).siblings('.textareaOptions').addClass('pb_hidden');
                $(this).siblings('.textHtmlFeildOptions').addClass('pb_hidden');
            }
            else{
                $(this).siblings('.textareaOptions').addClass('pb_hidden');
                $(this).siblings('.multiOptionField').addClass('pb_hidden');
                $(this).siblings('.textHtmlFeildOptions').addClass('pb_hidden');
            }
            

            if (currentSelectedVal == 'html') {
                $(this).siblings('.textHtmlFeildOptions').removeClass('pb_hidden');
                $(this).siblings('.multiOptionField').addClass('pb_hidden');
                $(this).siblings('.textareaOptions').addClass('pb_hidden');
                $(this).siblings('.thisFieldOptions').addClass('pb_hidden');
            }

            if (currentSelectedVal == 'mcgroups') {
                $(this).siblings('.textHtmlFeildOptions').addClass('pb_hidden');
                $(this).siblings('.multiOptionField').addClass('pb_hidden');
                $(this).siblings('.textareaOptions').addClass('pb_hidden');
                $(this).siblings('.thisFieldOptions').addClass('pb_hidden');
                $(this).siblings('.mrgroupsFieldContainer').addClass('pb_hidden');
                $(this).siblings('.mcgroupsFieldContainer').removeClass('pb_hidden');

            }

            if (currentSelectedVal == 'mrgroups') {
                $(this).siblings('.textHtmlFeildOptions').addClass('pb_hidden');
                $(this).siblings('.multiOptionField').addClass('pb_hidden');
                $(this).siblings('.textareaOptions').addClass('pb_hidden');
                $(this).siblings('.thisFieldOptions').addClass('pb_hidden');
                $(this).siblings('.mcgroupsFieldContainer').addClass('pb_hidden');
                $(this).siblings('.mrgroupsFieldContainer').removeClass('pb_hidden');

            }

        });

        $(document).on( 'change','.fbFieldLabel', function(){
            if ($(this).val() == '') {
                
            } else{
                fieldLabel  = $(this).val().slice(0,30);
                $(this).parent().parent().siblings('.handleHeader').html(fieldLabel + '<span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <span class="dashicons dashicons-admin-page slideDuplicateButton" style="float: right;" title="Duplicate"></span>');
            }
        });

    $(document).on( 'click','.slideRemoveButton', function(){
        jQuery(this).parent().parent().remove();
        pageBuilderApp.changedOpType = 'specific';
        pageBuilderApp.changedOpName = 'slideListEdit';
        
        var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
        jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

        ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
        currentEditableColId = jQuery('.currentEditableColId').val();
        jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');

    });

    $(document).on( 'click','.slideDuplicateButton', function(){
        jQuery(this).parent().parent().clone(true, true).insertAfter( jQuery(this).parent().parent() );

        thisSlideListType = jQuery(this).parent().parent().parent().attr('id');
        if (thisSlideListType == 'iconListItemsContainer') {
            jQuery('.pbIconListPicker').iconpicker({ });
            jQuery('.pbIconListPicker').on('iconpickerSelected',function(event){
                $(this).val(event.iconpickerValue);
                $(this).trigger('change');
            });
        }

        pageBuilderApp.changedOpType = 'specific';
        pageBuilderApp.changedOpName = 'slideListEdit';

        var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
        jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

        ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
        currentEditableColId = jQuery('.currentEditableColId').val();
        jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');

        jQuery(this).parent().parent().parent('.sortableAccordionWidget').accordion( "refresh" );
    });

    $('.formSuccessAction').on('change',function(){
        var selectedAction = $('.formSuccessAction').val();
        if (selectedAction == 'redirect') {
            $('.successFormActionCont').css('display','block');
        }else{
            $('.successFormActionCont').css('display','none');
        }
    });


    // for Img Widget
    $('.imgSize').on('change',function(){
        $('.customImageSizeDiv').css('display','none');
        if ($(this).val() == 'custom' )  {
          $('.customImageSizeDiv').css('display','block');
        }
    });


    })(jQuery);
</script>

<style type="text/css"> .formBuilderTabs li { font-size: 12px !important; } .accordContentHolder { overflow: auto !important; }</style>