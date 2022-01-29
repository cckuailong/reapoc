<?php
if (!defined('WPINC')) {
    die('Closed');
}
$form = new RM_PFBC_Form("login-add-field");
$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));//echo '<pre>';print_r($data->field);echo '</pre>';
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        <?php
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_LINK_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_ANCHOR_TEXT') . "</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => isset($data->field['field_label'])?$data->field['field_label']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LINK'))));
        $form->addElement(new Element_HTML('</div>'));
        
        
        $form->addElement(new Element_Radio(RM_UI_Strings::get('LABEL_ANCHOR_LINK'), "link_type",array('url' => 'URL', 'page' => 'Page'), array("onchange"=>"toggleURLOption(this)","required"=>1, "value" => isset($data->field['link_type'])?$data->field['link_type']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_ANCHOR_LINK'))));
        if(isset($data->field['link_type']) &&  $data->field['link_type']!='url')
            $form->addElement(new Element_HTML('<div id="rmurl" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rmurl" class="childfieldsrow">'));
        $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_URL') . "</b>", "link_href", array("id" => "rm_widget_link_href", "class" => "rm_static_field rm_field_value", "value" =>isset($data->field['link_href'])?$data->field['link_href']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_URL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        if(isset($data->field['link_type']) &&  $data->field['link_type']!='page')  
            $form->addElement(new Element_HTML('<div id="rmpage" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rmpage" class="childfieldsrow">'));
        
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CHOOSE_PP') . "</b>", "link_page",  RM_Utilities::wp_pages_dropdown(), array("id" => "rm_widget_link_page", "class" => "rm_static_field", "value" => isset($data->field['link_page'])?$data->field['link_page']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_PAGE'))));
        $form->addElement(new Element_HTML('</div>'));
        
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_LINK_SAME_WINDOW') . "</b>", "link_same_window", array(1 => ""), array("id" => "rm_field_link_same_window", "class" => "rm_static_field rm_input_type", "value" => isset($data->field['link_same_window'])?1:'', "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_LINK_SW'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => isset($data->field['field_css_class'])?$data->field['field_css_class']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
        
        //Button Area
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_field_manage', array('class' => 'cancel')));

        $save_buttton_label = RM_UI_Strings::get('LABEL_FIELD_SAVE');

        if (isset($data->model->field_id))
            $save_buttton_label = RM_UI_Strings::get('LABEL_SAVE');

        $form->addElement(new Element_Button($save_buttton_label, "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));

        include('widget_default_fields.php');
        $form->render();
        ?>
    </div>
</div>

<script>
    function toggleURLOption(element){
        element.value=='url'? jQuery("#rmurl").show() : jQuery("#rmurl").hide(); 
        element.value=='page'? jQuery("#rmpage").show() : jQuery("#rmpage").hide();

    }
</script>    