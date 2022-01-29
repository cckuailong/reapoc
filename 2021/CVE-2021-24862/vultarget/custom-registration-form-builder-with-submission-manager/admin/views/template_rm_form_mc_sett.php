<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_mc_sett.php'); else {
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
        $form = new RM_PFBC_Form("form_sett_mailchimp");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
if (isset($data->model->form_id)) {
            $form->addElement(new Element_HTML('<div class="rmheader">' . $data->model->form_name . '</div>'));
            $form->addElement(new Element_HTML('<div class="rmsettingtitle">' . RM_UI_Strings::get('LABEL_F_MC_SETT') . '</div>'));
            $form->addElement(new Element_Hidden("form_id", $data->model->form_id));
        } else {
            $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_FORM_PAGE") . '</div>'));
        }
        $form->addElement(new Element_HTML('<input type="hidden" id="form_id" value="' . $data->model->get_form_id() . '"/>'));

        $form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_MAILCHIMP_LIST') . "</b>", "mailchimp_list", $data->mailchimp_list, array("id" => "mailchimp_list", "value" => $data->mc_form_list, "onchange" => "get_field(this);", "longDesc" => RM_UI_Strings::get('HELP_ADD_FORM_MC_LIST'))));

        
        $form->addElement(new Element_HTML('<div id="mc_fields">'.$data->mc_fields.'</div>'));

        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB') . "</b>", "form_is_opt_in_checkbox", array(1 => ""), array("id" => "rm_", "class" => "rm_op", "onclick" => "hide_show(this);", "value" => $data->model->form_options->form_is_opt_in_checkbox, "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB'))));

        if ($data->model->form_options->form_is_opt_in_checkbox == '1')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" >'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="rm_op_childfieldsrow" style="display:none">'));



        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_OPT_IN_CB_TEXT') . "</b>", "form_opt_in_text", array("id" => "rm_form_name", "value" => $data->model->form_options->form_opt_in_text, "longDesc" => RM_UI_Strings::get('HELP_OPT_IN_CB_TEXT'))));
        $form->addElement(new Element_Radio("<b>" . RM_UI_Strings::get('LABEL_DEFAULT_STATE') . "</b>", "form_opt_in_default_state", array('Checked'=>RM_UI_Strings::get('LABEL_CHECKED'),'Unchecked'=>RM_UI_Strings::get('LABEL_UNCHECKED')), array("id"=>"id_rm_default_state", "value" => $data->model->form_options->form_opt_in_default_state, "longDesc" => RM_UI_Strings::get('MSG_OPT_IN_DEFAULT_STATE'))));
       
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement (new Element_HTMLL ('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page='.$data->next_page.'&rm_form_id='.$data->model->form_id, array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit", "onClick" => "jQuery.prevent_field_add(event,'".__('This is required field.', 'custom-registration-form-builder-with-submission-manager')."')")));
        $form->render();
        ?>
    </div>
    
    <?php 
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>
<pre class='rm-pre-wrapper-for-script-tags'><script>

    function get_field(element) {
        var form_id = jQuery("#form_id").val();

        var list_id = jQuery(element).val();
        if (form_id == '')
        {
            alert(form_id);

        } else
        {
            var data = {
                'action': 'rm_get_fields',
                'list_id': list_id,
                'form_id': form_id,
                'rm_slug': 'get_mc_list_field'
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                document.getElementById("mc_fields").innerHTML = response;
            });
        }
    }

</script></pre>
<?php
}