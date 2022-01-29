<?php
if (!defined('WPINC')) {
    die('Closed');
}
wp_enqueue_media();
$form = new RM_PFBC_Form("add-widget");
$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        <?php
        if (isset($data->model->field_id)) 
            $form->addElement(new Element_Hidden("field_id", $data->model->field_id));
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_F_DATA_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_Hidden("form_id", $data->form_id));
        
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . ":</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LABEL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_NUM_FORM_VIEWS') . ":</b>", "nu_form_views", array(1 => ""), array("id" => "nu_form_views", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->nu_form_views, "longDesc"=>RM_UI_Strings::get('HELP_NU_FORM_VIEWS'))));
        if(!$data->model->field_options->nu_form_views)
            $form->addElement(new Element_HTML('<div id="rm-nu-views" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-nu-views" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_BEFORE') . ":</b>", "nu_views_text_before", array("class" => "rm_static_field", "value" => $data->model->field_options->nu_views_text_before, "longDesc"=>RM_UI_Strings::get('HELP_NU_VIEW_TEXT_BEFORE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_AFTER') . ":</b>", "nu_views_text_after", array("class" => "rm_static_field", "value" => $data->model->field_options->nu_views_text_after, "longDesc"=>RM_UI_Strings::get('HELP_NU_VIEW_TEXT_AFTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_NUM_SUB') . ":</b>", "nu_submissions", array(1 => ""), array("id" => "nu_submissions", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->nu_submissions, "longDesc"=>RM_UI_Strings::get('HELP_NU_FORM_SUB'))));
        if(!$data->model->field_options->nu_submissions)
            $form->addElement(new Element_HTML('<div id="rm-nu-submissions" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-nu-submissions" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_BEFORE') . ":</b>", "nu_sub_text_before", array("class" => "rm_static_field", "value" => $data->model->field_options->nu_sub_text_before, "longDesc"=>RM_UI_Strings::get('HELP_NU_SUB_TEXT_BEFORE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_AFTER') . ":</b>", "nu_sub_text_after", array("class" => "rm_static_field", "value" => $data->model->field_options->nu_sub_text_after, "longDesc"=>RM_UI_Strings::get('HELP_NU_SUB_TEXT_AFTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SUB_LIMITS') . ":</b>", "sub_limits", array(1 => ""), array("id" => "sub_limits", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->sub_limits, "longDesc"=>RM_UI_Strings::get('HELP_FD_SUB_LIMITS'))));
        if(!$data->model->field_options->sub_limits)
            $form->addElement(new Element_HTML('<div id="rm-sub-limits" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-sub-limits" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_BEFORE') . ":</b>", "sub_limit_text_before", array("class" => "rm_static_field", "value" => $data->model->field_options->sub_limit_text_before, "longDesc"=>RM_UI_Strings::get('HELP_FD_SUB_TEXT_BEFORE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_AFTER') . ":</b>", "sub_limit_text_after", array("class" => "rm_static_field", "value" => $data->model->field_options->sub_limit_text_after, "longDesc"=>RM_UI_Strings::get('HELP_FD_SUB_TEXT_AFTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SUB_DATE_LIMITS') . ":</b>", "sub_date_limits", array(1 => ""), array("id" => "sub_date_limits", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->sub_date_limits, "longDesc"=>RM_UI_Strings::get('HELP_FD_SUB_DATE_LIMITS'))));
        if(!$data->model->field_options->sub_date_limits)
            $form->addElement(new Element_HTML('<div id="rm-sub-date-limits" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-sub-date-limits" class="childfieldsrow">'));
        $form->addElement(new Element_Radio(RM_UI_Strings::get('LABEL_TYPE'), "sub_limit_ind",array('date' => __('Date to go','custom-registration-form-builder-with-submission-manager'), 'days' => __('Day(s) to go','custom-registration-form-builder-with-submission-manager')), array("value" => $data->model->field_options->sub_limit_ind, "longDesc"=>'')));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_BEFORE') . ":</b>", "sub_date_limit_text_before", array("class" => "rm_static_field", "value" => $data->model->field_options->sub_date_limit_text_before, "longDesc"=>RM_UI_Strings::get('HELP_FD_SD_LIMIT_TEXT_BEFORE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_AFTER') . ":</b>", "sub_date_limit_text_after", array("class" => "rm_static_field", "value" => $data->model->field_options->sub_date_limit_text_after, "longDesc"=>RM_UI_Strings::get('HELP_FD_SD_LIMIT_TEXT_AFTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_LS_RECEIVED') . ":</b>", "last_sub_rec", array(1 => ""), array("id" => "last_sub_rec", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->last_sub_rec, "longDesc"=>RM_UI_Strings::get('HELP_FD_LS_REC'))));
        if(!$data->model->field_options->last_sub_rec)
            $form->addElement(new Element_HTML('<div id="rm-last-sub-rec" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-last-sub-rec" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_BEFORE') . ":</b>", "ls_text_before", array("class" => "rm_static_field", "value" => $data->model->field_options->ls_text_before, "longDesc"=>RM_UI_Strings::get('HELP_FD_LS_TEXT_BEFORE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_TEXT_AFTER') . ":</b>", "ls_text_after", array("class" => "rm_static_field", "value" => $data->model->field_options->ls_text_after, "longDesc"=>RM_UI_Strings::get('HELP_FD_LS_TEXT_AFTER'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SH_FONAME') . ":</b>", "show_form_name", array(1 => ""), array("id" => "show_form_name", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->show_form_name, "longDesc"=>RM_UI_Strings::get('HELP_FD_SH_F_NAME'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_FORM_DESC') . ":</b>", "form_desc", array(1 => ""), array("id" => "form_desc", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->form_desc, "longDesc"=>RM_UI_Strings::get('HELP_FD_F_DESC'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . ":</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
        
        //Button Area
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));

        $save_buttton_label = RM_UI_Strings::get('LABEL_FIELD_SAVE');

        if (isset($data->model->field_id))
            $save_buttton_label = RM_UI_Strings::get('LABEL_SAVE');

        $form->addElement(new Element_Button($save_buttton_label, "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));
       
        $form->render();
        ?>
        
        
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery("#nu_form_views-0").change(function(){
            if(jQuery(this).is(':checked')){
                jQuery("#rm-nu-views").slideDown();
                return;
            }
            jQuery("#rm-nu-views").slideUp();
        });
        
        jQuery("#nu_submissions-0").change(function(){
            if(jQuery(this).is(':checked')){
                jQuery("#rm-nu-submissions").slideDown();
                return;
            }
            jQuery("#rm-nu-submissions").slideUp();
        });
        
        jQuery("#sub_limits-0").change(function(){
            if(jQuery(this).is(':checked')){
                jQuery("#rm-sub-limits").slideDown();
                return;
            }
            jQuery("#rm-sub-limits").slideUp();
        });
        
        jQuery("#sub_date_limits-0").change(function(){
            if(jQuery(this).is(':checked')){
                jQuery("#rm-sub-date-limits").slideDown();
                return;
            }
            jQuery("#rm-sub-date-limits").slideUp();
        });
        
        jQuery("#last_sub_rec-0").change(function(){
            if(jQuery(this).is(':checked')){
                jQuery("#rm-last-sub-rec").slideDown();
                return;
            }
            jQuery("#rm-last-sub-rec").slideUp();
        });
        
    });
</script>    

