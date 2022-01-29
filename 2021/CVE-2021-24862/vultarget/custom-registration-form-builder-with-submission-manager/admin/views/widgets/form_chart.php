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
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_F_CHART_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_Hidden("form_id", $data->form_id));
        
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . ":</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LABEL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CHART_TYPE') . ":</b>", "field_value",array("sot"=>__('Submissions over time', 'custom-registration-form-builder-with-submission-manager'),"conversion_chart"=>__('Conversion%', 'custom-registration-form-builder-with-submission-manager'),"browser_usage_chart"=>__('Browser Used', 'custom-registration-form-builder-with-submission-manager'),"browser_conversion"=>__('Browser wise conversion', 'custom-registration-form-builder-with-submission-manager')) ,array("id"=>"chart_type","class" => "rm_static_field rm_required", "value" => $data->model->get_field_value(), "longDesc"=>RM_UI_Strings::get('HELP_F_CHART_TYPE'))));
        
        $form->addElement(new Element_HTML('<div class="childfieldsrow" id="chart_time_range">'));
            $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_TIMERANGE') . ":</b>", "time_range",array("7"=>sprintf(__("Last %d days",'custom-registration-form-builder-with-submission-manager'),7),"30"=>sprintf(__("Last %d days",'custom-registration-form-builder-with-submission-manager'),30),"60"=>sprintf(__("Last %d days",'custom-registration-form-builder-with-submission-manager'),60),"90"=>sprintf(__("Last %d days",'custom-registration-form-builder-with-submission-manager'),90)) ,array("class" => "rm_static_field", "value" =>$data->model->field_options->time_range , "longDesc"=>'')));
        $form->addElement(new Element_HTML('</div>'));
        
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
       var time_range= jQuery("#chart_time_range"); 
       jQuery("#chart_type").change(function(){
           var selected_value= jQuery(this).val();
           if(selected_value=="sot"){
               time_range.show();
           }
           else
           {
               time_range.hide();
           }
       }); 
       
       jQuery("#chart_type").trigger('change');
    });
</script>    
