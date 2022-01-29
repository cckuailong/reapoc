<?php
if (!defined('WPINC')) {
    die('Closed');
}

$form = new RM_PFBC_Form("add-field");

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

if (isset($data->model->field_id)){
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_EDIT_FIELD_PAGE") . '</div>'));
    $form->addElement(new Element_Hidden("field_id", $data->model->field_id));
} else{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FIELD_PAGE") . '</div>'));
}
$form->addElement(new Element_Hidden("form_id", $data->form_id));   

$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_TYPE') . "</b>", "field_type", $field_types_array, array("id" => "rm_field_type_select_dropdown", "value" => $data->selected_field, "class" => "rm_static_field", "onchange" => "rm_toggle_field_add_form_fields(this)", "longDesc"=>__('This is Woocommerce Shipping field. Type of this field can not be changed.','custom-registration-form-builder-with-submission-manager'))));

$form->addElement(new Element_HTML('<div id="field_lable_container" >'));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . "</b>", "field_label", array("id" => "rm_field_label", "class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_LABEL'))));
$form->addElement(new Element_HTML('</div>'));



$firstname_label= empty($data->model->field_options->field_wcs_firstname_label) ? __('First Name', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_firstname_label;
$lastname_label= empty($data->model->field_options->field_wcs_lastname_label) ? __('Last Name', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_lastname_label;
$company_label= empty($data->model->field_options->field_wcs_company_label) ? __('Company', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_company_label;
$city_label= empty($data->model->field_options->field_wcs_city_label) ? __('City', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_city_label;
$state_label= empty($data->model->field_options->field_wcs_state_label) ? __('State or Region', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_state_label;
$zip_label= empty($data->model->field_options->field_wcs_zip_label) ? __('Zip', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_zip_label;
$country_label= empty($data->model->field_options->field_wcs_country_label) ? __('Country', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_country_label;
$address1_label= empty($data->model->field_options->field_wcs_address1_label) ? __('Street Address 1', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_address1_label;
$address2_label= empty($data->model->field_options->field_wcs_address2_label) ? __('Street Address 2', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_wcs_address2_label;

$firstname_en= isset($data->model->field_options->field_wcs_firstname_en) ? $data->model->field_options->field_wcs_firstname_en : 1;
$lastname_en= isset($data->model->field_options->field_wcs_lastname_en) ? $data->model->field_options->field_wcs_lastname_en : 1;
$company_en= isset($data->model->field_options->field_wcs_company_en) ? $data->model->field_options->field_wcs_company_en : 1;
$city_en= isset($data->model->field_options->field_wcs_city_en) ? $data->model->field_options->field_wcs_city_en : 1;
$state_en= isset($data->model->field_options->field_wcs_state_en) ? $data->model->field_options->field_wcs_state_en : 1;
$zip_en= isset($data->model->field_options->field_wcs_zip_en) ? $data->model->field_options->field_wcs_zip_en : 1;
$country_en= isset($data->model->field_options->field_wcs_country_en) ? $data->model->field_options->field_wcs_country_en : 1;
$address1_en= isset($data->model->field_options->field_wcs_address1_en) ? $data->model->field_options->field_wcs_address1_en : 1;
$lmark_en= isset($data->model->field_options->field_wcs_lmark_en) ? $data->model->field_options->field_wcs_lmark_en : 0;
$address2_en= isset($data->model->field_options->field_wcs_address2_en) ? $data->model->field_options->field_wcs_address2_en : 1;

$address_type= empty($data->model->field_options->field_address_type) ? "ca" : $data->model->field_options->field_address_type; 
$wcs_state_type= isset($data->model->field_options->wcs_state_type) ? $data->model->field_options->wcs_state_type : 'all'; 

$firstname_req = isset($data->model->field_options->field_wcs_firstname_req) ? $data->model->field_options->field_wcs_firstname_req : 0;
$lastname_req = isset($data->model->field_options->field_wcs_lastname_req) ? $data->model->field_options->field_wcs_lastname_req : 0;
$company_req = isset($data->model->field_options->field_wcs_company_req) ? $data->model->field_options->field_wcs_company_req : 0;
$address1_req= isset($data->model->field_options->field_wcs_address1_req) ? $data->model->field_options->field_wcs_address1_req : 0;
$lmark_req= isset($data->model->field_options->field_wcs_lmark_req) ? $data->model->field_options->field_wcs_lmark_req : 0;
$address2_req= isset($data->model->field_options->field_wcs_address2_req) ? $data->model->field_options->field_wcs_address2_req : 0;
$state_req= isset($data->model->field_options->field_wcs_state_req) ? $data->model->field_options->field_wcs_state_req : 0;
$country_req = isset($data->model->field_options->field_wcs_country_req) ? $data->model->field_options->field_wcs_country_req : 0;
$zip_req = isset($data->model->field_options->field_wcs_zip_req) ? $data->model->field_options->field_wcs_zip_req : 0;
$city_req = isset($data->model->field_options->field_wcs_city_req) ? $data->model->field_options->field_wcs_city_req : 0;
$label_as_placeholder= isset($data->model->field_options->field_wcs_label_as_placeholder) ? $data->model->field_options->field_wcs_label_as_placeholder : 0;

$united_state_options= '<option>'.__('Select State or Region', 'custom-registration-form-builder-with-submission-manager').'</option>';
$united_states= RM_Utilities::get_usa_states();
foreach($united_states as $key=>$state){
    $united_state_options .= "<option>$state</option>";
}

$canadian_options= '<option>'.__('Select State or Region', 'custom-registration-form-builder-with-submission-manager').'</option>';
$canadian_provinces= RM_Utilities::get_canadian_provinces();
foreach($canadian_provinces as $key=>$province){
    $canadian_options .= "<option>$province</option>";
}

$countries= RM_Utilities::get_countries();
$country_options='';
foreach($countries as $country){
    $country_options .= "<option>$country</option>";
}
$state_codes='';
$state_codes_enabled = isset($data->model->field_options->field_wcs_state_codes) ? $data->model->field_options->field_wcs_state_codes : '';
$wcs_country_america_can= isset($data->model->field_options->field_wcs_country_america_can)?$data->model->field_options->field_wcs_country_america_can:'';
$wcs_field_wcs_country_limited= empty($data->model->field_options->field_wcs_country_limited) ? '' : $data->model->field_options->field_wcs_country_limited;
$country_search_enabled= isset($data->model->field_options->field_wcs_en_country_search) ? $data->model->field_options->field_wcs_en_country_search : 0;

$hide_search_opt= ($wcs_state_type!="all" && $wcs_state_type!="limited") ? 'style="display:none"':'';

$form->addElement(new Element_HTML('
<div id="rm-address-field" >
    <div class="rmrow">
        <div class="rmfield" for="rm_field_is_required_range">
            <label><b>'.__('Shipping Fields', 'custom-registration-form-builder-with-submission-manager').'</b></label>
        </div>
    </div>
    <div class="childfieldsrow rm_wcs_field" id="rm_field_is_required_range_childfieldsrow">
        <div class="rmrow">
        
            <div class="rm-address-field-row rm-address-field-col2">
                <div class="rm-address-field" id="rm-address-field-firstname">
                    <input type="text" name="rrm_field_wcs_firstname" id="rm_field_wcs_firstname" class="" value="">
                    <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'firstname\',this)" class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_firstname_req"><input type="checkbox" name="field_wcs_firstname_req" value="1" '.rm_checkbox_state($firstname_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_firstname_label\')">'.$firstname_label.'</label>
                </div>

                <div class="rm-address-field" id="rm-address-field-lastname">
                    <input type="text" name="rrm_field_wcs_lastname" id="rm_field_wcs_lastname" class="" value="">
                    <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'lastname\',this)" class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_lastname_req"><input type="checkbox" name="field_wcs_lastname_req" value="1" '.rm_checkbox_state($lastname_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_lastname_label\')">'.$lastname_label.'</label>
                </div>
            </div>
            
            <div class="rm-address-field-row">
                <div class="rm-address-field" id="rm-address-field-company">
                    <input type="text" name="rm_field_wcs_company" id="rm_field_wcs_company" class="" value="">
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_company_label\')">'.$company_label.'</label>
                    <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'company\',this)"  class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_company_req"><input type="checkbox" name="field_wcs_company_req" value="1" '.rm_checkbox_state($company_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
                </div>
            </div>

            <div class="rm-address-field-row">
                <div class="rm-address-field" id="rm-address-field-address">
                    <input type="text" name="rm_field_wcs_address1" id="rm_field_wcs_address1" class="" value="">
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_address1_label\')">'.$address1_label.'</label>


                    <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'address1\',this)"  class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_address1_req"><input type="checkbox" name="field_wcs_address1_req" value="1" '.rm_checkbox_state($address1_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>

                </div>
            </div>

           <div class="rm-address-field-row">
             <div class="rm-address-field" id="rm-address-field-address2">
             <input type="text" name="rm_field_wcs_address2" id="rm_field_wcs_address2" class="" value="">
             <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_address2_label\')">'.$address2_label.'</label>
                   <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'address2\',this)"  class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_address2_req"><input type="checkbox" name="field_wcs_address2_req" value="1" '.rm_checkbox_state($address2_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
             </div>
           </div>

            <div class="rm-address-field-row rm-address-field-col2" >
                <div class="rm-address-field" id="rm-address-field-city">
                    <input type="text" name="rm_field_wcs_city" id="rm_field_wcs_city" class="" value="">
                    <div class="rm-ca-actions">
                        <span  onclick="wcs_field_visibility(\'city\',this)" class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_city_req"><input type="checkbox" name="field_wcs_city_req" value="1" '.rm_checkbox_state($city_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_city_label\')">'.$city_label.'</label>
                </div>  
                <div class="rm-address-field" id="rm-address-field-states">

                    <div id="rm_field_wcs_state_all" style="display:none" class="rmstates">
                        <input type="text" value="" class="input-ca-state" id="rm_field_wcs_state_all">
                        <div class="rm-ca-actions">
                            <span  onclick="wcs_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                            <span id="rm_field_wcs_state_req"><input type="checkbox" name="field_wcs_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                        </div>
                    </div>    

                    <div id="rm_field_wcs_state_america" style="display:none" class="rmstates">
                        <select class="input-ca-state" id="input_wcs_state_america">'.$united_state_options.'</select>
                        <div class="rm-ca-actions">
                            <span  onclick="wcs_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                            <span id="rm_field_wcs_state_req"><input type="checkbox" name="field_wcs_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                        </div>    
                    </div>

                    <div id="rm_field_wcs_state_america_can" style="display:none" class="rmstates">    
                         <select class="input-ca-state" id="input_america_states">'.$united_state_options.'</select>    
                         <select class="input-ca-state" id="input_can_states">'.$canadian_options.'</select> 
                        <div class="rm-ca-actions">
                            <span  onclick="wcs_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                            <span id="rm_field_wcs_state_req"><input type="checkbox" name="field_wcs_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                        </div>    
                    </div>

                    <div id="rm_field_wcs_state_limited" style="display:none" class="rmstates">  
                        <input class="input-ca-state" type="text" value="" id="input_wcs_state_limited">  
                        <div class="rm-ca-actions">
                            <span  onclick="wcs_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                            <span id="rm_field_wcs_state_req"><input type="checkbox" name="field_wcs_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                        </div>
                    </div>


                    <span id="rm_field_wcs_state_as_codes" style="display:none"><input name="field_wcs_state_codes"   value="1" type="checkbox" '.$state_codes.'/>'.__('Show as codes', 'custom-registration-form-builder-with-submission-manager').'</span>
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_state_label\')">'.$state_label.'</label>
                </div>


            </div>

            <div class="rm-address-field-row rm-address-field-col2">
                <div class="rm-address-field" id="rm-address-field-country">

                    <select style="display:none" class="form-dropdown wcs_country" id="rm_field_wcs_country_all">'.$country_options.'</select>
                    <select style="display:none" class="form-dropdown wcs_country" id="rm_field_wcs_country_america" data-component="country" tabindex="-1" disabled=""><option>'.__('United States', 'custom-registration-form-builder-with-submission-manager').'</option></select>
                    <select style="display:none" class="form-dropdown wcs_country" id="rm_field_wcs_country_america_can" name="field_wcs_country_america_can" data-component="country" tabindex="-1"><option value="america">'.__('United States', 'custom-registration-form-builder-with-submission-manager').'</option><option value="america_can">'.__('Canada', 'custom-registration-form-builder-with-submission-manager').'</option></select>
                    <textarea style="display:none" id="rm_field_wcs_country_limited" name="field_wcs_country_limited" class="wcs_country">'.$wcs_field_wcs_country_limited.'</textarea>

                    <div class="rm-ca-actions">
                        <span  onclick="wcs_field_visibility(\'country\',this)" class="rm-address-field-visibility"></span>
                        <span '.$hide_search_opt.' id="field_wcs_en_country_search"><input type="checkbox"  name="field_wcs_en_country_search" value="1" '.rm_checkbox_state($country_search_enabled).'>'.__('Enable Search', 'custom-registration-form-builder-with-submission-manager').'</span>    
                        <span id="rm_field_wcs_country_req"><input type="checkbox" name="field_wcs_country_req" value="1" '.rm_checkbox_state($country_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>

                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_country_label\')">'.$country_label.'</label>
                    <div id="rm_country_note" style="display:none" class="rm-country-note rm-dbfl">'.RM_UI_Strings::get('HELP_REG_ADD_ALL_COUNTRY').'</div>    
                </div>

                <div class="rm-address-field" id="rm-address-field-zip">
                    <input type="text" name="rrm_field_wcs_zip" id="rm_field_wcs_zip" class="" value="">
                    <div class="rm-ca-actions">
                        <span onclick="wcs_field_visibility(\'zip\',this)" class="rm-address-field-visibility"></span>
                        <span id="rm_field_wcs_zip_req"><input type="checkbox" name="field_wcs_zip_req" value="1" '.rm_checkbox_state($zip_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                    </div>
                    <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_wcs_zip_label\')">'.$zip_label.'</label>
                </div> 


            </div>

        <div class="rm-address-field-row">
            <div class="rm-difl">
               <span> Use labels as placeholders</span>
                <input type="checkbox" name="field_wcs_label_as_placeholder" value="1" '.rm_checkbox_state($label_as_placeholder).'/>
            </div>

             <div class="rm-address-field-legand rm-difl">
               <span class="rm-difl"> Click to hide field </span> <span class="rm-difl"><i class="material-icons">&#xE417;</i></span>

            </div>


        </div>


    </div> 
    <input type="hidden" name="field_wcs_firstname_label" id="rm_field_wcs_firstname_label" value="'.$firstname_label.'"/>
    <input type="hidden" name="field_wcs_lastname_label" id="rm_field_wcs_lastname_label" value="'.$lastname_label.'"/>
    <input type="hidden" name="field_wcs_company_label" id="rm_field_wcs_company_label" value="'.$company_label.'"/>
    <input type="hidden" name="field_wcs_city_label" id="rm_field_wcs_city_label" value="'.$city_label.'" />
    <input type="hidden" name="field_wcs_address1_label" id="rm_field_wcs_address1_label" value="'.$address1_label.'"/>
    <input type="hidden" name="field_wcs_address2_label" id="rm_field_wcs_address2_label" value="'.$address2_label.'"/>
    <input type="hidden" name="field_wcs_state_label" id="rm_field_wcs_state_label" value="'.$state_label.'"/>
    <input type="hidden" name="field_wcs_zip_label" id="rm_field_wcs_zip_label" value="'.$zip_label.'"/>
    <input type="hidden" name="field_wcs_country_label" id="rm_field_wcs_country_label" value="'.$country_label.'"/>
        
    <input class="wcs_en_field" type="hidden" name="field_wcs_firstname_en" id="rm_field_wcs_firstname_en" value="'.$firstname_en.'" />
    <input class="wcs_en_field" type="hidden" name="field_wcs_lastname_en" id="rm_field_wcs_lastname_en" value="'.$lastname_en.'" />
    <input class="wcs_en_field" type="hidden" name="field_wcs_company_en" id="rm_field_wcs_company_en" value="'.$company_en.'" />
    <input class="wcs_en_field" type="hidden" name="field_wcs_city_en" id="rm_field_wcs_city_en" value="'.$city_en.'" />
    <input class="wcs_en_field" type="hidden" name="field_wcs_address1_en" id="rm_field_wcs_address1_en" value="'.$address1_en.'"/>
    <input class="wcs_en_field" type="hidden" name="field_wcs_lmark_en" id="rm_field_wcs_lmark_en" value="'.$lmark_en.'"/>
    <input class="wcs_en_field" type="hidden" name="field_wcs_address2_en" id="rm_field_wcs_address2_en" value="'.$address2_en.'"/>
    <input class="wcs_en_field" type="hidden" name="field_wcs_state_en" id="rm_field_wcs_state_en" value="'.$state_en.'"/>
    <input class="wcs_en_field" type="hidden" name="field_wcs_zip_en" id="rm_field_wcs_zip_en" value="'.$zip_en.'"/>
    <input class="wcs_en_field" type="hidden" name="field_wcs_country_en" id="rm_field_wcs_country_en" value="'.$country_en.'"/>    
</div>'));









$form->addElement(new Element_HTML('<div id="rm_field_helptext_container">'));
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HELP_TEXT') . "</b>", "help_text", array("id" => "rm_field_helptext", "class" => "", "value" => $data->model->field_options->help_text, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_HELP_TEXT'))));
$form->addElement(new Element_HTML('</div>'));

/***Begin :Icon Settings******/
$form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_icon_sett_collapsed" id="rm_icon_field_settings_header" onclick="rm_toggle_icon_settings()"><a>' . RM_UI_Strings::get('ICON_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
$form->addElement(new Element_HTML('<div id="rm_icon_field_settings_container" style="display:none">'));
$form->addElement(new Element_HTML('<div id="rm_icon_setting_container">'));
$form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_type"><div class="rmfield" for="rm_field_value_options_textarea"><label>'.RM_UI_Strings::get('LABEL_FIELD_ICON').'</label></div><div class="rminput" id="rm_field_icon_chosen"><i class="material-icons"'.$icon_style.' id="id_show_selected_icon">'.$f_icon->codepoint.'</i><div class="rm-icon-action"><div onclick="show_icon_reservoir()"><a href="javascript:void(0)">'.RM_UI_Strings::get('LABEL_FIELD_ICON_CHANGE').'</a></div> <div onclick="rm_remove_icon()"><a href="javascript:void(0)">'.RM_UI_Strings::get('LABEL_REMOVE').'</a></div></div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.RM_UI_Strings::get('HELP_FIELD_ICON').'</div></div></div>'));
$form->addElement(new Element_Hidden('input_selected_icon_codepoint', $f_icon->codepoint, array('id'=>'id_input_selected_icon')));
$form->addElement(new Element_Color(RM_UI_Strings::get('LABEL_FIELD_ICON_FG_COLOR'), "icon_fg_color", array("id" => "rm_", "value" => $f_icon->fg_color, "onchange" => "change_icon_fg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_FG_COLOR'))));

$form->addElement(new Element_Color(RM_UI_Strings::get('LABEL_FIELD_ICON_BG_COLOR'), "icon_bg_color", array("id" => "rm_", "value" => $f_icon->bg_color, "onchange" => "change_icon_bg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_COLOR'))));

$form->addElement(new Element_Range(RM_UI_Strings::get('LABEL_FIELD_ICON_BG_ALPHA'), "icon_bg_alpha", array("id" => "rm_", "value" => $f_icon->bg_alpha, "step" => 0.1, "min" => 0, "max" => 1, "oninput" => "finechange_icon_bg_color()", "onchange" => "finechange_icon_bg_color()", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_ALPHA'))));

$form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_FIELD_ICON_SHAPE'), "icon_shape", $icon_shapes, array("id" => "rm_", "value" => $f_icon->shape, "onchange" => "change_icon_shape(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_SHAPE'))));
$form->addElement(new Element_HTML('</div>'));
$form->addElement(new Element_HTML('</div>'));
/***END :Icon Settings******/

$form->addElement(new Element_HTML('<div style="display:none">'));
$form->addElement(new Element_jQueryUIDate("", '', array()));
$form->addElement(new Element_HTML('</div>'));


/**** Begin: Advanced Field Settings ****/
$form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_adv_sett_collapsed" id="rm_advance_field_settings_header" onclick="rm_toggle_adv_settings()"><a>' . RM_UI_Strings::get('ADV_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
$form->addElement(new Element_HTML('<div id="rm_advance_field_settings_container" style="display:none">'));

$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));

$form->addElement(new Element_HTML('</div>'));
 /**** End: Advanced Field Settings */    
    
//Button Area
$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));
$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));

?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<?php 

$form->render();
?>

<script>
    jQuery(document).ready(function(){
        jQuery(".en_confirm_pwd").change(function(){ 
           var el= jQuery(this);
           if(el.is(':checked')){
               jQuery("#confirm_pwd_options").slideDown();
               return;
           }
           jQuery("#confirm_pwd_options").slideUp();
        });
        
        jQuery(".en_pass_strength").change(function(){ 
           var el= jQuery(this);
           if(el.is(':checked')){
               jQuery("#pwd_strength_options").slideDown();
               return;
           }
           jQuery("#pwd_strength_options").slideUp();
        });
        
        jQuery(".en_confirm_pwd,.en_pass_strength").trigger('change');
       
    });
</script>
<script>
    // Intitializes field settings on page load
    jQuery(document).ready(function(){
        
       var address_type= "<?php echo $address_type; ?>";  
       if(address_type=="ca")
           jQuery("#rm_field_is_ca").attr('checked', 'checked');
       else 
           jQuery("#rm_field_is_ga").attr('checked', 'checked');

       rm_address_type_changed(address_type); 
       
       
       var wcs_state_type= "<?php echo $wcs_state_type; ?>";
       jQuery("#rm_wcs_state_" + wcs_state_type).attr('checked',true);
       rm_show_state_country_fields(wcs_state_type);
       
       jQuery("#rm_field_wcs_country_america_can").change(function(){
           america_can_country_changed();
       });
       
       if("<?php echo $state_codes_enabled; ?>"==1){
           jQuery("#rm_field_wcs_state_as_codes input").attr('checked',true);
       }
       
       if(jQuery("#rm_wcs_state_america_can").is(":checked")){
           jQuery("#rm_field_wcs_country_america_can").val("<?php echo $wcs_country_america_can; ?>");
           america_can_country_changed();
       }
       
       jQuery(".wcs_en_field").each(function(){
           var id1= jQuery(this).attr('id');
           
           var id2= id1.replace("_en","");
           
           if(jQuery("#" + id1).val()!="1"){
              jQuery("#" + id2).addClass('rm-disable');
              if(id2=="rm_field_wcs_state"){
                  jQuery("[id=rm_field_wcs_state_req]:not(:hidden)").addClass('rm-disable');
                  //jQuery(".rmstates input").addClass('rm-disable');
                  
              }
              else if(id2=="rm_field_wcs_country"){
                  jQuery("[id=field_wcs_en_country_search]:not(:hidden)").addClass('rm-disable'); 
              }

              jQuery("#" + id2 + "_req").addClass('rm-disable');
              jQuery("#" + id2).siblings('.rm-ca-actions').addClass('rm-field-visibility');
          }
          
       });
       
       // Check for states visibility
       if(jQuery("#rm_field_wcs_state_en").val()!="1"){
           jQuery(".input-ca-state").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).addClass('rm-disable');
                    jQuery(this).siblings('.rm-ca-actions').addClass('rm-field-visibility');
                }
            }); 
       }
       
       // Check for country visbility
       if(jQuery("#rm_field_wcs_country_en").val()!="1"){
            jQuery(".wcs_country").each(function(){ 
                     if(!jQuery(this).is(":hidden")){ 
                         jQuery(this).addClass('rm-disable');
                         jQuery(this).siblings('.rm-ca-actions').addClass('rm-field-visibility');
                     }
            });
        }
        
       if(jQuery("#rm_wcs_state_limited").is(":checked")){
           jQuery("#rm_country_note").show();
       }
       
       
    });
    
    // Update labels inside hidden fields
    function rm_custom_address_label_changed(obj,target){
        jQuery("#" + target).val(obj.innerHTML);
    }
    
    // Show/Hide address options 
    function rm_address_type_changed(type){
        jQuery(".rm-addresstype-note").hide();
        if(type=="ga"){
            jQuery(".rm_wcs_field").hide();
            jQuery("#rm_no_api_notice").show();
            jQuery("#rm_field_is_required-0").attr("disabled",false);
            
        } else{
            jQuery(".rm_wcs_field").show();
            jQuery("#rm_no_api_notice").hide();
            jQuery("#rm_field_is_required-0").attr("disabled",true);
            
        }
        jQuery("#note-" + type).show();
    }
    
    // Toggle individual field visibility 
    function wcs_field_visibility(field_type,obj){
        var enable_field;
        var req_div= jQuery("[name=field_wcs_" + field_type + "_req]").closest("span");
            
        if(field_type=="state"){
            jQuery(".input-ca-state").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).toggleClass('rm-disable');
                }
            }); 

            enable_field= jQuery("#rm_field_wcs_state_en");
            if(enable_field.val()==1)
                enable_field.val(0);
            else
                enable_field.val(1);
            req_div.toggleClass('rm-disable');
        }
        else if(field_type=="country")
        {
            jQuery(".wcs_country").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).toggleClass('rm-disable');
                }
            });
            
            enable_field= jQuery("#rm_field_wcs_country_en");
            if(enable_field.val()==1)
                enable_field.val(0);
            else
                enable_field.val(1);
            var country_search= jQuery("#field_wcs_en_country_search")
            req_div.toggleClass('rm-disable');
            country_search.toggleClass('rm-disable');
        }
        else
        {  
            enable_field= jQuery("#rm_field_wcs_" + field_type + "_en");
            var field= jQuery("#rm_field_wcs_" + field_type );
            
            
            if(field.hasClass("rm-disable"))
                enable_field.val(1);
            else
                enable_field.val(0);
            
            field.toggleClass('rm-disable');
            req_div.toggleClass('rm-disable');
        }
                    
        jQuery(obj).closest(".rm-ca-actions").toggleClass('rm-field-visibility');
    }
    
    
    // Show/Hide United States/Canada states
    function america_can_country_changed(){
           var selected_val= jQuery("#rm_wcs_state_america_can").val();
           jQuery(".rmstates").hide();
           jQuery("#rm_field_wcs_state_" + selected_val).show();
           rm_show_state_code_options();
           rm_show_state_by_country();
    }
    
    function rm_show_state_country_fields(type){
        jQuery(".rmstates").hide();
        jQuery("#rm_field_wcs_state_" + type).show();
        
        jQuery(".wcs_country").hide();
        jQuery("#rm_field_wcs_country_" + type).show();  
        rm_show_state_code_options();
        if(type=="all"){
            jQuery("#field_wcs_en_country_search").hide();
        }
    }
    
    function rm_show_state_by_country(){
        var country= jQuery("#rm_field_wcs_country_america_can").val();
        jQuery("#rm_field_wcs_state_america_can").show();
        if(country=="america")
        {   
            jQuery("#input_america_states").show();
            jQuery("#input_can_states").hide();
        }
        else
        {
            jQuery("#input_america_states").hide();
            jQuery("#input_can_states").show();
        }  
    }
    
    function rm_show_state_code_options(){
       var  america_can= jQuery("#rm_wcs_state_america_can");
       var america= jQuery("#rm_wcs_state_america");
       var show_codes_cb= jQuery("#rm_field_wcs_state_as_codes");
       
       if(america_can.is(":checked") || america.is(":checked"))
       {
            show_codes_cb.show();
       } 
       else
       {
           show_codes_cb.hide();
       }
       
    }
       
</script>

<?php 
    function rm_checkbox_state($value){
        return $value ? 'checked': '';
    }
?>