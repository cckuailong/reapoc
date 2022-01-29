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
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_FEED_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_Hidden("form_id", $data->form_id));
        
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . ":</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LABEL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_IDENTIFICATION') . ":</b>", "field_value",array("user_login"=>"Username","first_name"=>"First Name","last_name"=>"Last Name","both_names"=>"First Name + Last Name","in_last_name"=>"First Initial + Last name","display_name"=>"Display Name","custom"=>"Custom") ,array("class" => "rm_static_field", "value" => $data->model->field_value, "longDesc"=>RM_UI_Strings::get('HELP_IDENTIFICATION'))));
        if(!$data->model->field_options->custom_value)
            $form->addElement(new Element_HTML('<div id="rm-initials" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-initials" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CUSTOM_TEXT') . ":</b>", "custom_value", array("class" => "rm_static_field", "value" => $data->model->field_options->custom_value, "longDesc"=>RM_UI_Strings::get('HELP_FEED_CUSTOM_TEXT'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_HIDE_DATE') . ":</b>", "hide_date", array(1 => ""), array("id" => "hide_date", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->hide_date, "longDesc"=>RM_UI_Strings::get('HELP_FEED_HIDE_DATE'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_HIDE_COUNTRY') . ":</b>", "hide_country", array(1 => ""), array("id" => "hide_country", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->hide_country, "longDesc"=>RM_UI_Strings::get('HELP_FEED_HIDE_COUNTRY'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_MAX_ITEMS') . ":</b>", "max_items", array("class" => "rm_static_field", "value" => $data->model->field_options->max_items, "longDesc"=>RM_UI_Strings::get('HELP_FEED_MAX_ITEMS'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SHOW_GRAVATAR') . ":</b>", "show_gravatar", array(1 => ""), array("id" => "hide_date", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->show_gravatar, "longDesc"=>RM_UI_Strings::get('HELP_FEED_SHOW_GRAVATAR'))));
         
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
        jQuery("select[name='field_value']").change(function(){
            if(jQuery(this).val()=="custom"){
                jQuery("#rm-initials").slideDown();
                return;
            }
            jQuery("#rm-initials").slideUp();
        });
    });
</script>    

