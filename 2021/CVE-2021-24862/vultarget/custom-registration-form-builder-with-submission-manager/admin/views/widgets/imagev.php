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
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_IMG_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_Hidden("form_id", $data->form_id));
        
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . ":</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LABEL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $field_value= $data->model->get_field_value();
        $img= wp_get_attachment_image_src($field_value,'thumbnail');
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield">&nbsp;</div><div class="rminput">'));
        if(!empty($img[0])){
            $form->addElement(new Element_HTML('<div class="rm-img-preview"><img id="img_preview" src="'.$img[0].'"/></div>'));
        } else{
             $form->addElement(new Element_HTML('<div class="rm-img-preview"><img id="img_preview" src=""/></div>'));
        }
        
        $form->addElement(new Element_HTML('<div class="rm-select-img-button"><input type="button" value="Select Image" name="upload_image" id="upload_image_button" class="rm_btn rm-widgetimg-upload btn"></div>'));
        $form->addElement(new Element_HTML('</div></div>'));
        
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SIZE') . ":</b>", "img_size",array(__('Thumbnail', 'custom-registration-form-builder-with-submission-manager'),"50%","100%") ,array("class" => "rm_static_field rm_required", "value" => $data->model->field_options->img_size, "longDesc"=>RM_UI_Strings::get('HELP_IMG_SIZE'))));
        
        // Image effects related
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_EFFECTS') . ":</b>", "img_effects_enabled", array(1 => ""), array("onchange"=>"toggleEffects()","id" => "img_effects_enabled", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->img_effects_enabled, "longDesc"=>RM_UI_Strings::get('HELP_IMG_EFFECTS'))));
        
        if(!$data->model->field_options->img_effects_enabled)
            $form->addElement(new Element_HTML('<div id="rm-image-effects" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rm-image-effects" class="childfieldsrow">'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_BORDER_COLOR') . ":</b>", "border_color", array("class" => "rm_static_field rm_required jscolor", "value" => $data->model->field_options->border_color, "longDesc"=>RM_UI_Strings::get('HELP_IMG_BORDER_COLOR'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_BORDER_WIDTH') . ":</b>", "border_width", array("class" => "rm_static_field rm_required", "value" => $data->model->field_options->border_width=='' ? 5: $data->model->field_options->border_width, "longDesc"=>RM_UI_Strings::get('HELP_IMG_BORDER_WIDTH'))));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_BORDER_SHAPE') . ":</b>", "border_shape",array(__('Square', 'custom-registration-form-builder-with-submission-manager'),__('Circle', 'custom-registration-form-builder-with-submission-manager')) ,array("class" => "rm_static_field rm_required", "value" => $data->model->field_options->border_shape, "longDesc"=>RM_UI_Strings::get('HELP_IMG_BORDER_SHAPE'))));
        $form->addElement(new Element_HTML('</div>'));
         
        $form->addElement(new Element_Radio(RM_UI_Strings::get('LABEL_ANCHOR_LINK'), "link_type",array(''=>__('None', 'custom-registration-form-builder-with-submission-manager'),'url' => __('URL', 'custom-registration-form-builder-with-submission-manager'), 'page' => __('Page', 'custom-registration-form-builder-with-submission-manager')), array("onchange"=>"toggleURLOption(this)", "value" => $data->model->field_options->link_type, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_ANCHOR_LINK'))));
        if($data->model->field_options->link_type!='url')
            $form->addElement(new Element_HTML('<div id="rmurl" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rmurl" class="childfieldsrow">'));
        $form->addElement(new Element_Url("<b>" . RM_UI_Strings::get('LABEL_URL') . ":</b>", "link_href", array("id" => "rm_widget_link_href", "class" => "rm_static_field rm_field_value", "value" =>$data->model->field_options->link_href, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_URL'))));
        $form->addElement(new Element_HTML('</div>'));
        
        if($data->model->field_options->link_type!='page')  
            $form->addElement(new Element_HTML('<div id="rmpage" style="display:none" class="childfieldsrow">'));
        else
            $form->addElement(new Element_HTML('<div id="rmpage" class="childfieldsrow">'));
        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_CHOOSE_PP') . ":</b>", "link_page",  RM_Utilities::wp_pages_dropdown(), array("id" => "rm_widget_link_page", "class" => "rm_static_field", "value" => $data->model->field_options->link_page, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_PAGE'))));
        $form->addElement(new Element_HTML('</div>'));
        
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_DISPLAY_POP') . ":</b>", "img_pop_enabled", array(1 => ""), array("id" => "img_pop_enabled", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->img_pop_enabled, "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_IMG_POPUP'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_CAPTION') . ":</b>", "img_caption_enabled", array(1 => ""), array("id" => "img_caption_enabled", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->img_caption_enabled, "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_CAPTION'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_TITLE') . ":</b>", "img_title_enabled", array(1 => ""), array("id" => "img_title_enabled", "class" => "rm_static_field rm_input_type", "value" => $data->model->field_options->img_title_enabled, "longDesc"=>RM_UI_Strings::get('HELP_WIDGET_TITLE'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . ":</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
        
        //Button Area
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));

        $save_buttton_label = RM_UI_Strings::get('LABEL_FIELD_SAVE');

        if (isset($data->model->field_id))
            $save_buttton_label = RM_UI_Strings::get('LABEL_SAVE');

        $form->addElement(new Element_Button($save_buttton_label, "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));
        $form->addElement(new Element_Hidden("field_value",$field_value,array("id"=>"field_value")));
        $form->render();
        ?>
        
        
    </div>
</div>
	
<script type='text/javascript'>
    function toggleURLOption(element){
        element.value=='url'? jQuery("#rmurl").show() : jQuery("#rmurl").hide(); 
        element.value=='page'? jQuery("#rmpage").show() : jQuery("#rmpage").hide();

    }
    
    function toggleEffects()
    {
        if(jQuery("#img_effects_enabled-0").is(':checked')){
            jQuery("#rm-image-effects").show();
            return;
        }
        jQuery("#rm-image-effects").hide();
        
    }
        jQuery( document ).ready( function( $ ) {
                // Uploading files
                var file_frame;
                var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                var set_to_post_id = <?php echo empty($field_value)? 0 : $field_value; ?>; // Set this

                jQuery('#upload_image_button').on('click', function( event ){
                        event.preventDefault();
                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                                // Set the post ID to what we want
                                file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                                // Open frame
                                file_frame.open();
                                return;
                        } else {
                                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                                wp.media.model.settings.post.id = set_to_post_id;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.file_frame = wp.media({
                                title: 'Select a image to upload',
                                button: {
                                        text: 'Use this image',
                                },
                                multiple: false	// Set to true to allow multiple files to be selected
                        });

                        // When an image is selected, run a callback.
                        file_frame.on( 'select', function() {
                                // We set multiple to false so only get one image from the uploader
                                attachment = file_frame.state().get('selection').first().toJSON();
                                if(attachment.mime.indexOf('image')==-1){
                                    alert("<?php _e('Only image can be used','custom-registration-form-builder-with-submission-manager') ?>");
                                    file_frame.open();
                                    return;
                                }
                                $( '#field_value' ).val( attachment.id );  
                                // Do something with attachment.id and/or attachment.url here
                                $( '#img_preview' ).attr( 'src', attachment.sizes.thumbnail.url ).css( 'width', 'auto' );
                                // Restore the main post ID
                                wp.media.model.settings.post.id = wp_media_post_id;
                        });

                                // Finally, open the modal
                                file_frame.open();
                });

                // Restore the main ID when the add media button is pressed
                jQuery( 'a.add_media' ).on( 'click', function() {
                        wp.media.model.settings.post.id = wp_media_post_id;
                });
        });
</script>