<?php
if (!defined('WPINC')) {
    die('Closed');
}
$form = new RM_PFBC_Form("login-add-field");
$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        <?php
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_YOUTUBE_WIDGET_PAGE") . '</div>'));
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . "</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => isset($data->field['field_label'])?$data->field['field_label']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LABEL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Url(RM_UI_Strings::get('LABEL_VIDEO_URL'), "field_value", array("validation"=> new Validation_RegExp("/.*youtube.com.*/", __('Must be a valid YouTube video url.', 'custom-registration-form-builder-with-submission-manager')),"required"=>1, "value" => isset($data->field['field_value'])?$data->field['field_value']:'', "longDesc"=>RM_UI_Strings::get('HELP_WI_VIDEO_URL'))));
        $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_WIDTH'), "yt_player_width", array("id" => "rm_yt_player_width", "value" => isset($data->field['yt_player_width'])?$data->field['yt_player_width']:'', "longDesc" => RM_UI_Strings::get('HELP_FIELD_YT_WIDTH'))));
        $form->addElement(new Element_Textbox(RM_UI_Strings::get('LABEL_HEIGHT'), "yt_player_height", array("id" => "rm_yt_player_height", "value" => isset($data->field['yt_player_height'])?$data->field['yt_player_height']:'', "longDesc" => RM_UI_Strings::get('HELP_FIELD_YT_HEIGHT'))));

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_AUTO_PLAY') . "</b>", "yt_auto_play", array(1 => ""), array("id" => "rm_field_yt_auto_play", "class" => "rm_static_field rm_input_type", "value" => !empty($data->field['yt_auto_play'])? 1:'', "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_YT_AUTOPLAY'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_REPEAT') . "</b>", "yt_repeat", array(1 => ""), array("id" => "rm_field_yt_repeat", "class" => "rm_static_field rm_input_type", "value" => !empty($data->field['yt_repeat'])?1:'', "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_YT_REPEAT'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_RELATED_VIDEOS') . "</b>", "yt_related_videos", array(1 => ""), array("id" => "rm_field_yt_related_videos", "class" => "rm_static_field rm_input_type", "value" => !empty($data->field['yt_related_videos'])?1:'', "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_YT_RELATED'))));
        
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => isset($data->field['field_css_class'])?$data->field['field_css_class']:'', "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
        $form->addElement(new Element_Hidden("rm_widget", 1));   // Saving a flag for Youtube widget as it is also a field.
        //Button Area
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_field_manage', array('class' => 'cancel')));

        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));

        include('widget_default_fields.php');
        $form->render();
        ?>
    </div>
</div>

<script>
    function width_changed(el){
        jQuery("#rm_player_length").html(el.value);
    }

</script>    