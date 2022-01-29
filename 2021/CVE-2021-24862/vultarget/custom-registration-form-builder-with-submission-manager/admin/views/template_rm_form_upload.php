<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_upload.php'); else {
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
        $form = new RM_PFBC_Form("form_import");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
     
        $form->addElement(new Element_HTML('<div class="rmheader"><span style="text-transform:none">'.__('RegistrationMagic', 'custom-registration-form-builder-with-submission-manager').'</span> '.__('Import Engine', 'custom-registration-form-builder-with-submission-manager').'</div>'));
        if(isset($data->status) && $data->status==true )
        {
             
             $form->addElement(new Element_HTML("<div id='rm_import_progress' style='margin:20px'></div>"));
               $form->render();
            ?>
        <pre class='rm-pre-wrapper-for-script-tags'><script>
        
         jQuery( document ).ready(function() {
         jQuery( "#rm_import_progress" ).append("<b><?php _e('Starting Import','custom-registration-form-builder-with-submission-manager');  ?></b>" );
        var ajaxnonce = '<?php echo wp_create_nonce('rm_import_first'); ?>';   
        var data = {
			'action': 'import_first',
                         'rm_ajaxnonce': ajaxnonce
		};
		jQuery.post(ajaxurl, data, function(response) {
                   if(response==0)
                    {
                       jQuery( "#rm_import_progress" ).append("<?php _e('(Imported)<br><br>All forms successfully Imported.','custom-registration-form-builder-with-submission-manager');  ?>");
                    } else if(response === "INVALID_FILE") {
                        jQuery( "#rm_import_progress" ).append("<br/><br/><?php _e('Error: Invalid file format.','custom-registration-form-builder-with-submission-manager');  ?>");
                    }
                    else{
                         var pre= parseInt(response)-1;
                         jQuery( "#rm_import_progress" ).append("</br></br><?php _e('Importing RM Form','custom-registration-form-builder-with-submission-manager');  ?>--"+pre+"(<?php _e('Imported','custom-registration-form-builder-with-submission-manager');  ?>)</br></br><?php _e('Importing RM Form','custom-registration-form-builder-with-submission-manager');  ?>--"+response+"");
                        recursive_import(response);
                    }
                   
		});
                 });
            function recursive_import(form_id){
                var id=form_id;
                var ajaxnonce = '<?php echo wp_create_nonce('rm_import_first'); ?>';
                var data = {
                                    'action': 'import_first',
                                    'rm_ajaxnonce': ajaxnonce,
                                    'form_id':id
                            };
                            jQuery.post(ajaxurl, data, function(response) {
                                if(response==0)
                                {
                                   jQuery( "#rm_import_progress" ).append("(<?php _e('Imported','custom-registration-form-builder-with-submission-manager');  ?>)</br></br><?php _e('All forms and their data is imported successfully.','custom-registration-form-builder-with-submission-manager');  ?>");
                                }
                                else{
                                   
                                     jQuery( "#rm_import_progress" ).append("(<?php _e('Imported','custom-registration-form-builder-with-submission-manager');  ?>)</br></br><?php _e('Importing RM Form','custom-registration-form-builder-with-submission-manager');  ?>--"+response+"");
                                     
                                    recursive_import(response);
                                }
                            });
}
        </script></pre>
        
            <?php
        }
        else
        {
        $form->addElement(new Element_HTML("<div id='upload'>"));
        $form->addElement(new Element_File(RM_UI_Strings::get('UPLOAD_XML'), "Forms", array("id" => "mailchimp_list","accept"=>".xml", "accept" => "xml","longDesc" => RM_UI_Strings::get('UPLOAD_XML_HELP'))));
       

        $form->addElement (new Element_HTMLL ('&#8592; &nbsp; Cancel', '?page=rm_form_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_IMPORT'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is a required field.','custom-registration-form-builder-with-submission-manager')."')")));
         $form->addElement(new Element_HTML("</div>"));
        $form->addElement(new Element_HTML("<div id='import'></div>"));
        
        $form->render();
        }
        ?>
    </div>
</div>


   
<?php
}