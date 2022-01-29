<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_field_add.php'); else {
wp_enqueue_script( 'jquery-ui-dialog', '', 'jquery' ); 
$data= (object) $data;
$field = $data->params['field_type'];
$data->form_id=0; // By passing form ID
?>
    <div class="rmagic">

        <!--Dialogue Box Starts-->
        <div class="rmcontent">
            <?php
            $form = new RM_PFBC_Form("login-add-field");

            $form->configure(array(
                "prevent" => array("bootstrap", "jQuery"),
                "action" => ""
            ));
            
            $form->addElement(new Element_HTML('<div class="rmheader">'.__('Edit Field', 'custom-registration-form-builder-with-submission-manager').'</div>'));
            if ($field == 'username') {
                // Username fields
                $form->addElement(new Element_Select("<b>".__('Username field accepts', 'custom-registration-form-builder-with-submission-manager')."</b>", "username_accepts", array('username' => __("Only Username",'custom-registration-form-builder-with-submission-manager')), array("value" => 'username', "class" => "rm_static_field rm_required", "required" => "1", "longDesc" => __('Define what the Username field accepts in your login form. You can allow it to accept only Username, only email or both.', 'custom-registration-form-builder-with-submission-manager').RM_UI_Strings::get('MSG_BUY_PRO_BOTH_INLINE'))));
                $form->addElement(new Element_Textbox("<b>".__('Username field label', 'custom-registration-form-builder-with-submission-manager')."</b>", "field_label", array("class" => "rm_static_field", "value" => $data->field['field_label'], "longDesc" => __('Label of the Username field in Login Form on frontend.', 'custom-registration-form-builder-with-submission-manager'))));
                $form->addElement(new Element_Textbox("<b>".__('Username field placeholder', 'custom-registration-form-builder-with-submission-manager')."</b>", "placeholder", array("class" => "rm_static_field", "value" => !isset($data->field['placeholder']) ? __('Enter Username','custom-registration-form-builder-with-submission-manager') : $data->field['placeholder'], "longDesc" => __('Placeholder is the faded background text users see inside the input box before they start typing in. It is a browser feature and commonly used on sites to guide the user about filling the input field value.', 'custom-registration-form-builder-with-submission-manager'))));
                $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield" for="login-add-field-element-3"><label><b>'.__('Username Field Empty Error','custom-registration-form-builder-with-submission-manager').'</b></label></div><div class="rminput"><div class="rmnotice" style="width:372px">'.__("The username empty error message is automatically handled by the user's browser in its default language.",'custom-registration-form-builder-with-submission-manager').'</div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent"></div></div></div>'));
            } 
            else if ($field == 'password') {
                // Password fields
                $form->addElement(new Element_Textbox("<b>".__('Password field label', 'custom-registration-form-builder-with-submission-manager')."</b>", "field_label", array("class" => "rm_static_field", "value" => $data->field['field_label'], "longDesc" => __('Label of the Password field in Login Form on frontend.', 'custom-registration-form-builder-with-submission-manager'))));
                $form->addElement(new Element_Textbox("<b>".__('Password field placeholder', 'custom-registration-form-builder-with-submission-manager')."</b>", "placeholder", array("class" => "rm_static_field", "value" => !isset($data->field['placeholder']) ? __('Enter Password','custom-registration-form-builder-with-submission-manager') : $data->field['placeholder'], "longDesc" => __('Placeholder is the faded background text users see inside the input box before they start typing in. It is a browser feature and commonly used on sites to guide the user about filling the input field value.', 'custom-registration-form-builder-with-submission-manager'))));
               
                $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield" for="login-add-field-element-3"><label><b>'.__('Password Field Empty Error','custom-registration-form-builder-with-submission-manager').'</b></label></div><div class="rminput"><div class="rmnotice" style="width:372px">'.__("The password empty error message is automatically handled by the user's browser in its default language.",'custom-registration-form-builder-with-submission-manager').'</div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent"></div></div></div>'));
            }

            /** *Begin :Icon Settings***** */
            $icon_shapes = array('square' => RM_UI_Strings::get('FIELD_ICON_SHAPE_SQUARE'),
                                 'sticker' => RM_UI_Strings::get('FIELD_ICON_SHAPE_STICKER'),
                                 'round' => RM_UI_Strings::get('FIELD_ICON_SHAPE_ROUND'));

            $f_icon = new stdClass;
            $f_icon->codepoint = isset($data->field['input_selected_icon_codepoint']) ? $data->field['input_selected_icon_codepoint'] : null;
            $f_icon->fg_color = isset($data->field['icon_fg_color']) ? $data->field['icon_fg_color'] : '000000';
            $f_icon->bg_color = isset($data->field['icon_bg_color']) ? $data->field['icon_bg_color'] : 'ffffff';
            $f_icon->shape = isset($data->field['icon_shape']) ? $data->field['icon_shape'] : 'square';
            $f_icon->bg_alpha = isset($data->field['icon_bg_alpha']) ? $data->field['icon_bg_alpha'] : 1.0;

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


            $form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_icon_sett_collapsed" id="rm_icon_field_settings_header" onclick="rm_toggle_icon_settings()"><a>' . RM_UI_Strings::get('ICON_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
            $form->addElement(new Element_HTML('<div id="rm_icon_field_settings_container" style="display:none">'));
            $form->addElement(new Element_HTML('<div id="rm_icon_setting_container">'));
            $form->addElement(new Element_HTML('<div class="rmrow" id="rm_jqnotice_row_date_type"><div class="rmfield" for="rm_field_value_options_textarea"><label>' . RM_UI_Strings::get('LABEL_FIELD_ICON') . '</label></div><div class="rminput" id="rm_field_icon_chosen"><i class="material-icons"' . $icon_style . ' id="id_show_selected_icon">' . $f_icon->codepoint . '</i><div class="rm-icon-action"><div onclick="show_icon_reservoir()"><a href="javascript:void(0)">' . RM_UI_Strings::get('LABEL_FIELD_ICON_CHANGE') . '</a></div> <div onclick="rm_remove_icon()"><a href="javascript:void(0)">' . RM_UI_Strings::get('LABEL_REMOVE') . '</a></div></div></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">' . RM_UI_Strings::get('HELP_FIELD_ICON') . '</div></div></div>'));
            $form->addElement(new Element_Hidden('input_selected_icon_codepoint', $f_icon->codepoint, array('id' => 'id_input_selected_icon')));
            $form->addElement(new Element_Color(__('Label icon color', 'custom-registration-form-builder-with-submission-manager'), "icon_fg_color", array("id" => "rm_", "value" => $data->field['icon_fg_color'], "onchange" => "change_icon_fg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_FG_COLOR'))));

            $form->addElement(new Element_Color(__('Label icon container', 'custom-registration-form-builder-with-submission-manager'), "icon_bg_color", array("id" => "rm_", "value" => $data->field['icon_bg_color'], "onchange" => "change_icon_bg_color(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_COLOR'))));

            $form->addElement(new Element_Range(__('Label icon container opacity', 'custom-registration-form-builder-with-submission-manager'), "icon_bg_alpha", array("id" => "rm_", "value" => $data->field['icon_bg_alpha'], "step" => 0.1, "min" => 0, "max" => 1, "oninput" => "finechange_icon_bg_color()", "onchange" => "finechange_icon_bg_color()", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_BG_ALPHA'))));

            $form->addElement(new Element_Select(__('Label icon container shape', 'custom-registration-form-builder-with-submission-manager'), "icon_shape", $icon_shapes, array("id" => "rm_", "value" => $data->field['icon_shape'], "onchange" => "change_icon_shape(this)", "longDesc" => RM_UI_Strings::get('HELP_FIELD_ICON_SHAPE'))));
            $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_HTML('</div>'));
            /** *END :Icon Settings***** */

            /**** Begin: Advanced Field Settings ****/
            $form->addElement(new Element_HTML('<div class="rmrow rm_field_settings_group_header rm_adv_sett_collapsed" id="rm_advance_field_settings_header" onclick="rm_toggle_adv_settings()"><a>' . RM_UI_Strings::get('ADV_FIELD_SETTINGS') . '<span class="rm-toggle-settings"></span></a></div>'));
                $form->addElement(new Element_HTML('<div id="rm_advance_field_settings_container" style="display:none">'));
                    $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . "</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" =>$data->field['field_css_class'], "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
            $form->addElement(new Element_HTML('</div>'));    

            $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_field_manage', array('class' => 'cancel')));
            $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));
            $form->render();
            ?>
        </div>
    </div>

    <?php require_once RM_EXTERNAL_DIR.'icons/icons_list.php'; $ico_arr = rm_get_icons_array(); ?>
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
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css"/>
    <pre class='rm-pre-wrapper-for-script-tags'><script>
        function show_icon_reservoir() {
            jQuery('#id_rm_field_icon_reservoir').show();
            jQuery(".rm_field_icon_reservoir").dialog();
            jQuery(".ui-dialog.ui-widget").addClass("rmdialog");

        }

        function close_icon_reservoir() {
            jQuery('#id_rm_field_icon_reservoir').hide();
        }

        function rm_remove_icon() {
            //Get old icon
            var oic = jQuery('#id_input_selected_icon').val();
            if (typeof oic != 'undefined')
            {
                if (oic)
                {
                    var oicid = 'rm-icon_' + (oic.slice(3));
                    jQuery('#' + oicid).removeClass('rm_active_icon');
                }
            }

            //jQuery('#rm-icon_'+ico_cp).addClass('rm_active_icon');
            jQuery('#id_show_selected_icon').html('');
            jQuery('#id_input_selected_icon').val('');
        }

        function rm_select_icon(e) {
            var icid = jQuery(e).attr('id');
            id_show_selected_icon;
            if (typeof icid != 'undefined')
            {
                var x = icid.split('_');
                var ico_cp = x[1];

                //Get old icon
                var oic = jQuery('#id_input_selected_icon').val();
                if (typeof oic != 'undefined')
                {
                    var oicid = 'rm-icon_' + (oic.slice(3));
                    jQuery('#' + oicid).removeClass('rm_active_icon');
                }

                jQuery('#rm-icon_' + ico_cp).addClass('rm_active_icon');
                jQuery('#id_show_selected_icon').html('&#x' + ico_cp);
                jQuery('#id_input_selected_icon').val('&#x' + ico_cp);
            }
        }

        function change_icon_fg_color(e) {
            var fg_color = jQuery(e).val();
            jQuery('#id_show_selected_icon').css("color", "#" + fg_color);
        }

        function finechange_icon_fg_color() {
            var fg_color = jQuery(":input[name='icon_fg_color']").val();
            jQuery('#id_show_selected_icon').css("color", "#" + fg_color);
        }

        function change_icon_bg_color(e) {
            var bg_color = jQuery(e).val();
            var r = parseInt(bg_color.slice(0, 2), 16);
            var g = parseInt(bg_color.slice(2, 4), 16);
            var b = parseInt(bg_color.slice(4, 6), 16);
            var a = jQuery(":input[name='icon_bg_alpha']").val();
            jQuery('#id_show_selected_icon').css("background-color", "rgba(" + r + "," + g + "," + b + "," + a + ")");
        }

        function finechange_icon_bg_color() {
            var bg_color = jQuery(":input[name='icon_bg_color']").val();
            var r = parseInt(bg_color.slice(0, 2), 16);
            var g = parseInt(bg_color.slice(2, 4), 16);
            var b = parseInt(bg_color.slice(4, 6), 16);
            var a = jQuery(":input[name='icon_bg_alpha']").val();
            jQuery('#id_show_selected_icon').css("background-color", "rgba(" + r + "," + g + "," + b + "," + a + ")");
        }

        function change_icon_shape(e) {
            var shape = jQuery(e).val();
            if (shape == 'square')
                jQuery('#id_show_selected_icon').css("border-radius", "0px");
            else if (shape == 'round')
                jQuery('#id_show_selected_icon').css("border-radius", "100px");
            else if (shape == 'sticker')
                jQuery('#id_show_selected_icon').css("border-radius", "4px");
        }

        function toggle_custom_validation(e) {
            var value = jQuery(e).val();
            if (value == 'custom')
                jQuery('#custom_validation_div').slideDown();
            else
                jQuery('#custom_validation_div').slideUp();
        }

        function rm_test_date_format() {

            var date_format = jQuery("#rm_field_dateformat").val().toString().trim();
            if (!date_format)
                return;
            var test_date = jQuery.datepicker.formatDate(date_format, new Date());
            var ele_testbox = jQuery("#id_rm_dateformat_test");
            ele_testbox.html(test_date);

            var data = {action: "rm_test_date", date: test_date};
        }

        function rm_toggle_adv_settings() {
            var $adv_sett = jQuery("#rm_advance_field_settings_container");
            var $adv_sett_header = jQuery("#rm_advance_field_settings_header");
            if ($adv_sett_header.hasClass("rm_adv_sett_expanded")) {
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
            if ($adv_sett_header.hasClass("rm_icon_sett_expanded")) {
                $adv_sett.slideUp();
                $adv_sett_header.removeClass("rm_icon_sett_expanded").addClass("rm_icon_sett_collapsed");
            } else {
                $adv_sett.slideDown();
                $adv_sett_header.removeClass("rm_icon_sett_collapsed").addClass("rm_icon_sett_expanded");
            }
        }

        function rm_add_meta() {
            if (jQuery(".field_show_on_user_page").attr('checked')) {
                jQuery("#rm_meta_key").css('display', 'block');
            } else {
                jQuery("#rm_meta_key").css('display', 'none');
            }
        }
    </script></pre>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">




<?php } ?>