<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_field_add.php'); else {
//echo'<pre>';var_dump($data->model);die;
/**
 * @internal Plugin Template File [Add Text Type Field]
 * 
 * This view generates the form for adding text type field to the form
 */

wp_enqueue_script( 'jquery-ui-dialog', '', 'jquery' );
wp_enqueue_script('select2');
wp_enqueue_style('select2');
if(isset($data->model->field_options->icon))
{
    $f_icon = $data->model->field_options->icon;
    if(!isset($f_icon->bg_alpha))
        $f_icon->bg_alpha = 1.0;
}
else
{
    $f_icon = new stdClass;
    $f_icon->codepoint = null;
    $f_icon->fg_color = '000000';
    $f_icon->bg_color = 'ffffff';
    $f_icon->shape = 'square';
    $f_icon->bg_alpha = 1.0;
}

$icon_shapes = array('square' => RM_UI_Strings::get('FIELD_ICON_SHAPE_SQUARE'),
    'sticker' => RM_UI_Strings::get('FIELD_ICON_SHAPE_STICKER'),
    'round' => RM_UI_Strings::get('FIELD_ICON_SHAPE_ROUND'));


if($f_icon->shape == 'square')
    $radius = '0px';
else if($f_icon->shape == 'round')
    $radius = '100px';
else if($f_icon->shape == 'sticker')
    $radius = '4px';
                    
$bg_r = intval(substr($f_icon->bg_color,0,2),16);
$bg_g = intval(substr($f_icon->bg_color,2,2),16);
$bg_b = intval(substr($f_icon->bg_color,4,2),16);

$icon_style = "style=\"padding:5px;color:#{$f_icon->fg_color};background-color:rgba({$bg_r},{$bg_g},{$bg_b},{$f_icon->bg_alpha});border-radius:{$radius};\"";
$field_types_array = RM_Utilities::get_field_types(false);

?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="rmagic">
       
<!--Dialogue Box Starts-->
<div class="rmcontent">
<?php
    require_once RM_EXTERNAL_DIR.'icons/icons_list.php';


if(in_array($data->selected_field,array('Username','UserPassword')) || (isset($data->model->is_field_primary) && $data->model->is_field_primary == 1)){
    include_once plugin_dir_path(__FILE__).'template_rm_primary_field_add.php';
}else if(in_array($data->selected_field,array('WCBilling'))){
    include 'html/wc_billing_field.php';
}else if(in_array($data->selected_field,array('WCShipping'))){
    include 'html/wc_shipping_field.php';
}

else{
    
$form = new RM_PFBC_Form("add-field");

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

if (isset($data->model->field_id))
{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_EDIT_FIELD_PAGE") . '</div>'));
    $form->addElement(new Element_Hidden("field_id", $data->model->field_id));
} else
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FIELD_PAGE") . '</div>'));

$form->addElement(new Element_Hidden("form_id", $data->form_id));

if($data->selected_field)
    $field_help_text = RM_UI_Strings::get('FIELD_HELP_TEXT_'.$data->selected_field); 
else
    $field_help_text = RM_UI_Strings::get('HELP_ADD_FIELD_SELECT_TYPE'); 


$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_TYPE') . "</b>", "field_type", $field_types_array, array("id" => "rm_field_type_select_dropdown", "value" => $data->selected_field , "class" => "rm_static_field rm_required", "required" => "1", "onchange" => "rm_toggle_field_add_form_fields(this)", "longDesc"=>$field_help_text)));
$form->addElement(new Element_Hidden('field_type',$data->selected_field));

if($data->selected_field=="Privacy"){
    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . "</b>", "field_label", array("id" => "rm_field_label", "class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>__("Privacy Policy Label will not appear in your form. Users only see what you define in Contents field below.", 'custom-registration-form-builder-with-submission-manager') )));
}else{
    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . "</b>", "field_label", array("id" => "rm_field_label", "class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_LABEL'))));
}

//$form->addElement(new Element_HTML('</div>'));

if($_GET['rm_field_type']=="Address")
    include 'html/address_field.php';

$form->addElement(new Element_HTML('<div id="time_Zone" style="display:none" >'));
$form->addElement(new Element_Timezone("<b>" . RM_UI_Strings::get('LABEL_TIME_ZONE') . "</b>", "field_timezone", array("id" => "field_timezone", "class" => "", "value" => $data->model->field_options->field_timezone, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_TIME_ZONE'))));
 $form->addElement(new Element_HTML('</div>'));
//Field_value fields only one can be used at a time
 $form->addElement(new Element_HTML('<div id="rm_tnc_cb_label_container" style="display:none" >'));
 $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_T_AND_C_CB_LABEL') . "</b>", "tnc_cb_label", array("id" => "rm_tnc_cb_label", "value" => $data->model->field_options->tnc_cb_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_TnC_CB_LABEL'))));
 $form->addElement(new Element_HTML('</div>'));
 
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_T_AND_C') . "</b>", "field_value", array("id" => "rm_field_value_terms", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_TnC_VAL'))));

$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_FILE_TYPES') . "</b>", "field_value", array("id" => "rm_field_value_file_types", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_FILETYPE'))));


$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_PRICING_FIELD') . "</b>", "field_value", $data->paypal_fields, array("id" => "rm_field_value_pricing", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "class" => "rm_field_value rm_static_field", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_PRICE_FIELD'))));
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_PARAGRAPF_TEXT') . "</b>", "field_value", array("id" => "rm_field_value_paragraph", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_PARA_TEXT'))));
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_SHORTCODE_TEXT') . "</b>", "field_value", array("id" => "rm_field_value_shortcode", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_SHORTCODE_TEXT'))));

$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HEADING_TEXT') . "</b>", "field_value", array("id" => "rm_field_value_heading", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_HEADING_TEXT'))));
$form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_OPTIONS') . "</b>(" . RM_UI_Strings::get('LABEL_DROPDOWN_OPTIONS_DSC') . ")", "field_value", array("id" => "rm_field_value_options_textarea", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? null : $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_OPTIONS_COMMASEP'))));
$form->addElement(new Element_Textboxsortable("<b>" . RM_UI_Strings::get('LABEL_OPTIONS') . "</b>", "field_value[]", array("id" => "rm_field_value_options_sortable", "class" => "rm_static_field rm_field_value", "value" => is_array($data->model->get_field_value()) ? $data->model->get_field_value() : explode(',' , $data->model->get_field_value()), "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_OPTIONS_SORTABLE'))));


//$form->addElement(new Element_HTML(""));
if(!$data->model->field_options->field_is_other_option)
    $form->addElement(new Element_HTML('<div id="rmaddotheroptiontextboxdiv" style="display:none">'));
else
    $form->addElement(new Element_HTML('<div id="rmaddotheroptiontextboxdiv">'));
$form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield" for="rm_other_option_text"><label>  </label></div><div class="rminput"><input type="text" name="rm_textbox" id="rm_other_option_text" class="rm_static_field" readonly="disabled" value="'.RM_UI_Strings::get('MSG_THEIR_ANS').'"><div id="rmaddotheroptiontextdiv2"><div onclick="jQuery.rm_delete_textbox_other(this)"><a href=javascript:void(0)>'.RM_UI_Strings::get('LABEL_DELETE').'</a></div></div></div></div>'));
$form->addElement(new Element_HTML('</div>'));
//$form->addElement(new Element_HTML("<div onclick=''>".RM_UI_Strings::get('LABEL_DELETE')."</div></div>"));
if(!$data->model->field_options->field_is_other_option)
    $form->addElement(new Element_Hidden("field_is_other_option", "", array("id" => "rm_field_is_other_option")));
else
    $form->addElement(new Element_Hidden("field_is_other_option", "1", array("id" => "rm_field_is_other_option")));
$form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_jqnotice_text"></div></div>'));

if(strtolower($data->selected_field)=="mobile"){
    include 'html/mobile_field.php';
}

$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_PLACEHOLDER_TEXT') . "</b>", "field_placeholder", array("id" => "rm_field_placeholder", "class" => "rm_static_field rm_text_type_field rm_input_type", "value" => $data->model->field_options->field_placeholder, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_PLACEHOLDER'))));

if($data->selected_field !== 'HTMLP' && $data->selected_field !== 'HTMLH')
{
    $form->addElement(new Element_HTML('<div id="rm_field_helptext_container">'));
    if($data->selected_field=="Privacy"){
        $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HELP_TEXT') . "</b>", "help_text", array("id" => "rm_field_helptext", "value" => $data->model->field_options->help_text, "longDesc" => __("Content to be displayed on fade-in tooltip for the users.", 'custom-registration-form-builder-with-submission-manager') )));
    }else{
        $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HELP_TEXT') . "</b>", "help_text", array("id" => "rm_field_helptext", "value" => $data->model->field_options->help_text, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_HELP_TEXT'))));
    }
    $form->addElement(new Element_HTML('</div>'));
}
else
{
    $form->addElement(new Element_HTML('<div id="rm_field_helptext_container" style="display:none">'));
    $form->addElement(new Element_Textarea("<b>" . RM_UI_Strings::get('LABEL_HELP_TEXT') . "</b>", "help_text", array("id" => "rm_field_helptext", "class" => "", "value" => $data->model->field_options->help_text, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_HELP_TEXT'))));
    $form->addElement(new Element_HTML('</div>'));
}

$privacy_page_id = 0;
if(function_exists('get_privacy_policy_url')){
    $privacy_page_id = url_to_postid( get_privacy_policy_url() );
}
$form->addElement(new Element_HTML('<div id="privacy_policy">'));
$form->addElement(new Element_Select("<b>" . __('Privacy Policy Page','custom-registration-form-builder-with-submission-manager') . "</b>", "privacy_policy_page", RM_Utilities::wp_pages_dropdown(), array("id" => "rm_privacy_policy_page", "value" => isset($data->model->field_options->privacy_policy_page)?$data->model->field_options->privacy_policy_page:$privacy_page_id, "class" => "", "longDesc"=>sprintf(__("Define your Privacy Policy page. By default it will select page you have already defined in Settings → Privacy (available in WordPress 4.9.6 and above). If you have not, we encourage you to write down your privacy policy, which can be later used at different places. For guidelines, <a target='_blank' class='rm-more' href='%s'>click here</a>", 'custom-registration-form-builder-with-submission-manager'),'tools.php?wp-privacy-policy-guide'))));
$form->addElement(new Element_Textarea("<b>" . __('Contents','custom-registration-form-builder-with-submission-manager') . "</b>", "privacy_policy_content", array("id" => "rm_privacy_policy_content", "value" => isset($data->model->field_options->privacy_policy_content)?$data->model->field_options->privacy_policy_content:__('By submitting this form you confirm that you have read and understood our {{privacy_policy}}.','custom-registration-form-builder-with-submission-manager'), "longDesc" => __('Define contents of the Privacy Policy notice that appears in the form. Use code {{privacy policy}} to insert a link to your policy page selected in Privacy Policy Page field above.','custom-registration-form-builder-with-submission-manager'))));
$form->addElement(new Element_Checkbox(__('Display Checkbox','custom-registration-form-builder-with-submission-manager'), "privacy_display_checkbox", array(1 => ""), array("id" => "rm_display_checkbox", "class" => "", "value" => isset($data->model->field_options->privacy_display_checkbox)?$data->model->field_options->privacy_display_checkbox:0, "longDesc"=>__('Display a checkbox along with privacy notice. Submit button will be in disabled state unless user checks this checkbox.','custom-registration-form-builder-with-submission-manager'))));
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


/**** Begin: Advanced Field Settings ****/
$form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_adv_sett_collapsed" id="rm_advance_field_settings_header" onclick="rm_toggle_adv_settings()"><a>' . RM_UI_Strings::get('ADV_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
$form->addElement(new Element_HTML('<div id="rm_advance_field_settings_container" style="display:none">'));

//The unusual Date Format field
    $rm_date_format = !$data->model->field_options->date_format ? "mm/dd/yy" : $data->model->field_options->date_format;
    $rm_date_format_label = RM_UI_Strings::get('LABEL_DATE_FORMAT');
    //Preprocess this special help text
    $rm_date_format_helptext = sprintf(RM_UI_Strings::get('HELP_ADD_FIELD_DATEFORMAT'),"href='javascript:void(0)' onclick='jQuery(\"#id_rm_dateformat_help\").slideToggle()'");

$rm_date_format_fieldhtml = '<div class="rmrow" id="rm_field_dateformat_container"><div class="rmfield" for="rm_field_dateformat"><label><b>'.$rm_date_format_label .'</b></label></div><div class="rminput"><input type="text" name="date_format" id="rm_field_dateformat" class="rm_static_field rm_text_type_field rm_input_type" value="'.$rm_date_format.'" onkeyup="rm_test_date_format()" onchange="rm_test_date_format()" data-rmvaliddateformat="true">'.
                            '<div id="id_rm_dateformat_test"></div><div id="id_rm_dateformat_help" style="display: none; padding: 20px 0px;">'.
                            '<strong>'.__('The format can be combinations of the following.', 'custom-registration-form-builder-with-submission-manager').':</strong>'.
                            '<ul style="list-style: disc; list-style-position: inside;">'.
                            '<li>'.__('d - day of month (no leading zero)', 'custom-registration-form-builder-with-submission-manager').'</li>'. 
                            '<li>'.__('dd - day of month (two digit)', 'custom-registration-form-builder-with-submission-manager').'</li>'.  
                            '<li>'.__('o - day of the year (no leading zeros)', 'custom-registration-form-builder-with-submission-manager').'</li>'.  
                            '<li>'.__('oo - day of the year (three digit)', 'custom-registration-form-builder-with-submission-manager').'</li>'.  
                            '<li>'.__('D - day name short', 'custom-registration-form-builder-with-submission-manager').'</li>'.    
                            '<li>'.__('DD - day name long', 'custom-registration-form-builder-with-submission-manager').'</li>'.   
                            '<li>'.__('m - month of year (no leading zero)', 'custom-registration-form-builder-with-submission-manager').'</li>'.   
                            '<li>'.__('mm - month of year (two digit)', 'custom-registration-form-builder-with-submission-manager').'</li>'.  
                            '<li>'.__('M - month name short', 'custom-registration-form-builder-with-submission-manager').'</li>'.  
                            '<li>'.__('MM - month name long', 'custom-registration-form-builder-with-submission-manager').'</li>'.   
                            '<li>'.__('y - year (two digit)', 'custom-registration-form-builder-with-submission-manager').'</li>'.       
                            '<li>'.__('yy - year (four digit)', 'custom-registration-form-builder-with-submission-manager').'</li></ul><a href="javascript:void(0)" onclick="jQuery(\'#id_rm_dateformat_help\').slideUp()">&#9652;'. __('Hide', 'custom-registration-form-builder-with-submission-manager').'</a>'.
                            '</div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.$rm_date_format_helptext.'</div></div></div>';


    $form->addElement(new Element_HTML($rm_date_format_fieldhtml));
//PHEW..

/* Option releated to Repeatable Field */
$form->addElement(new Element_HTML('<div id="field_repeatable_line_type" >'));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ALLOW_MULTILINE') . "</b>", "field_is_multiline", array(1 => ""), array("id" => "rm_field_multiline", "class" => "rm_field_multiline rm_input_type", "value" => $data->model->field_options->field_is_multiline, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_ALLOW_MULTILINE'))));
$form->addElement(new Element_HTML('</div>'));
    
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_MAX_LENGTH') . "</b>", "field_max_length", array("id" => "rm_field_max_length", "class" => "rm_static_field rm_text_type_field rm_input_type", "value" => $data->model->field_options->field_max_length, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_MAX_LEN'))));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_DEFAULT_VALUE') . "</b>", "field_default_value", array("id" => "rm_field_default_value", "class" => "rm_static_field rm_options_type_fields rm_input_type", "value" => is_array(maybe_unserialize($data->model->field_options->field_default_value)) ? null : $data->model->field_options->field_default_value, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_DEF_VALUE'))));
$form->addElement(new Element_Textboxsortable("<b>" . RM_UI_Strings::get('LABEL_DEFAULT_VALUE') . "</b>", "field_default_value[]", array("id" => "rm_field_default_value_sortable", "class" => "rm_static_field rm_options_type_fields rm_input_type", "value" => $data->model->get_field_default_value())));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_COLUMNS') . "</b>", "field_textarea_columns", array("id" => "rm_field_columns", "class" => "rm_static_field rm_textarea_type rm_input_type", "value" => $data->model->field_options->field_textarea_columns, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_COLS'))));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_ROWS') . "</b>", "field_textarea_rows", array("id" => "rm_field_rows", "class" => "rm_static_field rm_textarea_type rm_input_type", "value" => $data->model->field_options->field_textarea_rows, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_ROWS'))));
$form->addElement(new Element_HTML('<div id="rm_sub_heading" class="rmrow rm_sub_heading">' . RM_UI_Strings::get('TEXT_RULES') . '</div>'));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED') . "</b>", "field_is_required", array(1 => ""), array("id" => "rm_field_is_required", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->field_is_required, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_IS_REQUIRED'))));
$form->addElement(new Element_HTML('<div id="rm_unique_div">'));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_UNIQUE') . "</b>", "buy_pro", array(1 => ""), array("id" => "rm_field_is_unique", 'disabled' => 1, "class" => "rm_input_type", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_IS_UNIQUE'). "<br><br>" . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));
$form->addElement(new Element_HTML('</div>'));

$meta_options = array(
    'do_not_add' => __('Do not add','custom-registration-form-builder-with-submission-manager'),
    'existing_user_meta' => __('Associate with Existing User Meta Keys','custom-registration-form-builder-with-submission-manager'),
    'define_new_user_meta' => __('Define New User Meta Key','custom-registration-form-builder-with-submission-manager')
);
if(empty($data->model->field_options->field_user_profile)){
    if(!empty($data->model->field_options->field_meta_add)){
        $data->model->field_options->field_user_profile= 'define_new_user_meta';
    }
    else{
        $data->model->field_options->field_user_profile= 'do_not_add';
    }
}
$non_meta_fields= array('Price','ImageV','Shortcode','MapV','SubCountV','Form_Chart','FormData','Feed','Username','UserPassword','Privacy','WCBilling','WCShipping','WCBillingPhone','Fname','Lname','BInfo','Nickname','SecEmail','Website');
if(!in_array($data->selected_field, $non_meta_fields)){
    $form->addElement(new Element_HTML("<div id='rm_user_meta_options'>"));
        $form->addElement(new Element_Radio(__('Add Field to WordPress User Profile','custom-registration-form-builder-with-submission-manager').":", "field_user_profile",$meta_options, array("id" => "field_user_profile", "value" =>$data->model->field_options->field_user_profile, "longDesc"=>__('Saves the field value in a profile field in WordPress User Profile using User Meta. You can create new custom fields in the profile by selecting Define New User Meta Key','custom-registration-form-builder-with-submission-manager'))));
        $display_user_meta_options= $data->model->field_options->field_user_profile=='existing_user_meta' || $data->model->field_options->field_user_profile=='define_new_user_meta' ? '' : 'style="display:none"';
        $form->addElement(new Element_HTML("<div class='childfieldsrow' id='rm_user_meta_key_options' $display_user_meta_options>"));
            $form->addElement(new Element_Select("<b>" .__('Select User Meta Key','custom-registration-form-builder-with-submission-manager'). "</b>", "existing_user_meta_key",$data->metas, array('id'=>"existing_user_meta","value" =>$data->model->field_options->existing_user_meta_key, "class" => "rm_user_meta_option", "longDesc"=>__('Select a User Meta Key from wp_usermeta table in which you wish to save user response to this field. This is very useful for using form data with other plugins. But please be very careful that the field type matches expected meta key value. If you are not sure, consider creating a new user meta key.','custom-registration-form-builder-with-submission-manager'))));
            $form->addElement(new Element_Textbox("<b>" .__('Name of new Meta Key','custom-registration-form-builder-with-submission-manager'). "</b>", "field_meta_add", array('id'=>"define_new_user_meta","class" => "rm_meta_add rm_field_meta_add rm_user_meta_option", "value" => $data->model->field_options->field_meta_add, "longDesc"=>__('Define a new user meta key in which you wish to save field value. If the key does not exist, we will create a new one for you. It is recommended to avoid spaces and any special characters except underscore and dash.','custom-registration-form-builder-with-submission-manager'))));
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SHOW_ON_USER_PAGE') . "</b>", "field_show_on_user_page", array(1 => ""), array("id" => "rm_field_show_on", "class" => "rm_static_field rm_required", "value" => $data->model->field_show_on_user_page, "onclick"=>"rm_add_meta()", "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_SHOW_ON_USERPAGE'))));
        //$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_META_ADD') . "</b>", "field_meta_add", array("id" => "rm_meta_add", "class" => "rm_meta_add rm_required rm_field_meta_add", "value" => $data->model->field_options->field_meta_add, "longDesc"=>RM_UI_Strings::get('HELP_META_ADD'))));
    $form->addElement(new Element_HTML('</div>'));
}  

$form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_meta_key" style="display:none;">'));
$form->addElement(new Element_HTML('</div>'));



$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_FIELD_EDITABLE') . "</b>", "field_is_editable", array(1 => ""), array("id" => "rm_field_is_editable", "class" => "rm_static_field", "value" => $data->model->get_field_is_editable(), "longDesc"=>RM_UI_Strings::get('HELP_LABEL_IS_FIELD_EDITABLE'))));

$form->addElement(new Element_HTML('<div id="scroll" style="display:none">'));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED_SCROLL') . "</b>", "field_is_required_scroll", array(1 => ""), array("id" => "rm_field_is_required_scroll", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_is_required_scroll, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_REQUIRED_SCROLL'))));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_CHECK_ABOVE_TC') . "</b>", "field_check_above_tc", array(1 => ""), array("id" => "rm_check_above_tc", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_check_above_tc, "longDesc" => RM_UI_Strings::get('HELP_CHECK_ABOVE_TC'))));
$form->addElement(new Element_HTML('</div>'));
$form->addElement(new Element_HTML('<div id="date_range" style="display:none" >'));
$form->addElement(new Element_HTML('<div class="rmrow rm_sub_heading">' . RM_UI_Strings::get('TEXT_RANGE') . '</div>'));


 
 $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED_RANGE') . "</b>", "field_is_required_range", array(1 => ""), array("id" => "rm_field_is_required_range", "class" => "rm_field_is_required_range","value" => $data->model->field_options->field_is_required_range, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_BDATE_RANGE'))));

         $form->addElement(new Element_jQueryUIDate("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED_MAX_RANGE') . "</b>", 'field_is_required_max_range', array('class' => 'rm_dateelement',"id" => "rm_is_required_max_range", "value" => $data->model->field_options->field_is_required_max_range, "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_AUTO_EXP_TIME_LIMIT'))));
$form->addElement(new Element_jQueryUIDate("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED_MIN_RANGE') . "</b>", "field_is_required_min_range", array("id" => "rm_is_required_min_range", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_is_required_min_range, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_SHOW_ON_USERPAGE'))));
$form->addElement(new Element_HTML('<div class="rmrow" id="rm_range_error_row"><div class="rmfield" for="rm_field_value_options_textarea"><label></label></div><div class="rminput" id="rm_range_error_text"></div></div>'));

 
 $form->addElement(new Element_HTML('</div>'));
// $form->addElement(new Element_HTML('<div id="scroll" style="display:none">'));
// $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_REQUIRED_SCROLL') . "</b>", "field_is_required_scroll", array(1 => ""), array("id" => "rm_field_is_required_scroll", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_is_required_scroll, "longDesc" => RM_UI_Strings::get('HELP_ADD_FIELD_REQUIRED_SCROLL'))));
//$form->addElement(new Element_HTML('</div>'));
 
  $form->addElement(new Element_HTML('</div>'));
 /**** End: Advanced Field Settings */
 
$form->addElement(new Element_HTML('<div id="rm_no_api_notice" style="display:none">'.RM_UI_Strings::get('MSG_RM_NO_API_NOTICE').'</div>'));

//Button Area
$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));

$save_buttton_label = RM_UI_Strings::get('LABEL_FIELD_SAVE');

if (isset($data->model->field_id))
    $save_buttton_label = RM_UI_Strings::get('LABEL_SAVE');

$form->addElement(new Element_Button($save_buttton_label, "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));



$form->render();
//array('field_type','field_label','field_value','field_default_value','field_order','field_options');
}
?>  
</div>
<?php 
    $rm_promo_banner_title = __('Unlock all custom field types by upgrading', 'custom-registration-form-builder-with-submission-manager');
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
    </div>

<?php
$ico_arr = rm_get_icons_array();
?>
<div class='rm_field_icon_res_container' id='id_rm_field_icon_reservoir' style='display:none'>    
<div class='rm_field_icon_reservoir'>
<?php
foreach( $ico_arr as $icon_name => $icon_codepoint):
    //var_dump($icon_codepoint);var_dump($f_icon->codepoint);
    if('&#x'.$icon_codepoint == $f_icon->codepoint) {
    ?>
    <i class="material-icons rm-icons-get-ready rm_active_icon" onclick="rm_select_icon(this)" id="rm-icon_<?php echo $icon_codepoint; ?>"><?php echo '&#x'.$icon_codepoint; ?></i>
    <?php }
    else {
        ?>
    <i class="material-icons rm-icons-get-ready" onclick="rm_select_icon(this)" id="rm-icon_<?php echo $icon_codepoint; ?>"><?php echo '&#x'.$icon_codepoint; ?></i>
    <?php }
    
endforeach;
?>
</div>
</div>

<pre class='rm-pre-wrapper-for-script-tags'><script>
function show_icon_reservoir(){
    jQuery('#id_rm_field_icon_reservoir').show();
    console.log("inside dialog function");
    jQuery(".rm_field_icon_reservoir").dialog();
    jQuery (".ui-dialog.ui-widget").addClass("rmdialog");
}

function close_icon_reservoir(){
    jQuery('#id_rm_field_icon_reservoir').hide();
}

function rm_remove_icon(){    
        //Get old icon
        var oic = jQuery('#id_input_selected_icon').val();
        if(typeof oic != 'undefined')
        {
            if(oic)
            {
                var oicid =  'rm-icon_'+ (oic.slice(3));
                jQuery('#'+oicid).removeClass('rm_active_icon');
            }
        }
            
        //jQuery('#rm-icon_'+ico_cp).addClass('rm_active_icon');
        jQuery('#id_show_selected_icon').html('');
        jQuery('#id_input_selected_icon').val('');
}

function rm_select_icon(e){
    var icid = jQuery(e).attr('id'); id_show_selected_icon;
    if(typeof icid != 'undefined')
    {
        var x = icid.split('_');
        var ico_cp = x[1];
        
        //Get old icon
        var oic = jQuery('#id_input_selected_icon').val();
        if(typeof oic != 'undefined')
            {
               var oicid =  'rm-icon_'+ (oic.slice(3));
               jQuery('#'+oicid).removeClass('rm_active_icon');
            }
            
        jQuery('#rm-icon_'+ico_cp).addClass('rm_active_icon');
        jQuery('#id_show_selected_icon').html('&#x'+ico_cp);
        jQuery('#id_input_selected_icon').val('&#x'+ico_cp);
    }
}

function change_icon_fg_color(e){
    var fg_color = jQuery(e).val();
    jQuery('#id_show_selected_icon').css("color", "#"+fg_color);
}

function finechange_icon_fg_color(){
    var fg_color = jQuery(":input[name='icon_fg_color']").val();
    jQuery('#id_show_selected_icon').css("color", "#"+fg_color);
}

function change_icon_bg_color(e){
    var bg_color = jQuery(e).val();
    var r = parseInt(bg_color.slice(0,2),16);
    var g = parseInt(bg_color.slice(2,4),16);
    var b = parseInt(bg_color.slice(4,6),16);
    var a = jQuery(":input[name='icon_bg_alpha']").val();
    jQuery('#id_show_selected_icon').css("background-color", "rgba("+r+","+g+","+b+","+a+")");
}

function finechange_icon_bg_color(){
    var bg_color = jQuery(":input[name='icon_bg_color']").val();
    var r = parseInt(bg_color.slice(0,2),16);
    var g = parseInt(bg_color.slice(2,4),16);
    var b = parseInt(bg_color.slice(4,6),16);
    var a = jQuery(":input[name='icon_bg_alpha']").val();
    jQuery('#id_show_selected_icon').css("background-color", "rgba("+r+","+g+","+b+","+a+")");
}

function change_icon_shape(e){
    var shape = jQuery(e).val();
    if(shape == 'square')
        jQuery('#id_show_selected_icon').css("border-radius", "0px");
    else if(shape == 'round')
        jQuery('#id_show_selected_icon').css("border-radius", "100px");
    else if(shape == 'sticker')
        jQuery('#id_show_selected_icon').css("border-radius", "4px");
}

function rm_get_help_text(ftype){
    
    switch(ftype)
    {
        <?php foreach($field_types_array as $type => $disp_name) { if(!$type) continue;?>         
        case '<?php echo $type; ?>':return '<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_".$type); ?>';        
        <?php } ?>
        default: return '<?php echo RM_UI_Strings::get("HELP_ADD_FIELD_SELECT_TYPE"); ?>';
    }
}


</script></pre>

<pre class='rm-pre-wrapper-for-script-tags'><script>
 jQuery(document).ready(function () {
      jQuery(":input[name='icon_fg_color']").addClass("{onFineChange:'finechange_icon_fg_color()'}");
      jQuery(":input[name='icon_bg_color']").addClass("{onFineChange:'finechange_icon_bg_color()'}");
      
        jQuery("#rm_submit_btn").click(
            function (e) {
                if(jQuery(".rm_field_is_required_range").attr('checked'))
                {
              
               var max_date=new Date(jQuery("#rm_is_required_max_range").val());
               var min_date=new Date(jQuery("#rm_is_required_min_range").val());
               if(max_date<=min_date)
               {
                   jQuery('#rm_range_error_text').html('Invalid Range');
                   jQuery('#rm_range_error_row').show();
                   e.preventDefault();
               }
               }
            }            
        );

          if(jQuery(".field_show_on_user_page").attr('checked')){         
             jQuery("#rm_meta_key").css('display','block');       
         }
         else{
              jQuery("#rm_meta_key").css('display','none');   
         }
         jQuery("[name=field_user_profile]").click(function(){
            if(jQuery(this).is(':checked')){
                var selected_val= jQuery(this).val();
                if(selected_val=='existing_user_meta' || selected_val=='define_new_user_meta'){
                    jQuery(".rm_user_meta_option").closest('.rmrow').hide();
                    jQuery("#" + selected_val).closest('.rmrow').show();
                    jQuery("#rm_user_meta_key_options").show();
                }
                else{
                    jQuery("#rm_user_meta_key_options").hide();
                    jQuery(".rm_user_meta_option").closest('.rmrow').hide();
                }
            }
        });
        jQuery("[name=field_user_profile]:checked").trigger('click');
        jQuery('#existing_user_meta').select2({
            selectOnClose: true
        });
 
    });
    
    function rm_add_meta(){
        if(jQuery(".field_show_on_user_page").attr('checked')){         
             jQuery("#rm_meta_key").css('display','block');       
         }
         else{
              jQuery("#rm_meta_key").css('display','none');   
         }
    }
    
    function rm_test_date_format() {
       
       var date_format = jQuery("#rm_field_dateformat").val().toString().trim();
       if(!date_format)
           return;
       var test_date = jQuery.datepicker.formatDate( date_format, new Date() );
       var ele_testbox = jQuery("#id_rm_dateformat_test");
       ele_testbox.html(test_date);       
   }
   
   function rm_toggle_adv_settings() {
       var $adv_sett = jQuery("#rm_advance_field_settings_container");
       var $adv_sett_header = jQuery("#rm_advance_field_settings_header");
       if($adv_sett_header.hasClass("rm_adv_sett_expanded")) {
           $adv_sett.slideUp();
           $adv_sett_header.removeClass("rm_adv_sett_expanded").addClass("rm_adv_sett_collapsed");
        } else {
           $adv_sett.slideDown();
           $adv_sett_header.removeClass("rm_adv_sett_collapsed").addClass("rm_adv_sett_expanded");
       }
   }
   
    function rm_toggle_icon_settings() {
       var $adv_sett = jQuery("#rm_icon_field_settings_container");
       var $adv_sett_header = jQuery("#rm_icon_field_settings_header");
       if($adv_sett_header.hasClass("rm_icon_sett_expanded")) {
           $adv_sett.slideUp();
           $adv_sett_header.removeClass("rm_icon_sett_expanded").addClass("rm_icon_sett_collapsed");
        } else {
           $adv_sett.slideDown();
           $adv_sett_header.removeClass("rm_icon_sett_collapsed").addClass("rm_icon_sett_expanded");
       }
   }
    </script></pre>

<?php } ?>