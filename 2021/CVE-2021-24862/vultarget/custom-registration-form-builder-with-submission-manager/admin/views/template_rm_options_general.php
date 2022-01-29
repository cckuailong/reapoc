<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_general.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$image_dir = plugin_dir_url(dirname(dirname(__FILE__))) . "images";

$layout_checked_state = array('label_left' => null, 'label_top' => null, 'two_columns' => null);

if ($data['form_layout'] == 'two_columns')
    $layout_checked_state['two_columns'] = 'checked';
else if ($data['form_layout'] == 'label_top')
    $layout_checked_state['label_top'] = 'checked';
else
    $layout_checked_state['label_left'] = 'checked';


$layout_radio_button_html_string = '<div class="rmrow"><div class="rmfield" for="layout_radio"><label>' .
        RM_UI_Strings::get('LABEL_LAYOUT') .
        '</label></div><div class="rminput"><ul class="rmradio">' .
        '<li><div id="layout_left_container"><div class="rmlayoutimage"><img src="' . $image_dir . '/label-left.png" /></div><input id="layout_radio-1" type="radio" name="form_layout" value="label_left" ' . $layout_checked_state['label_left'] . '>' .
        RM_UI_Strings::get('LABEL_LAYOUT_LABEL_LEFT') .
        '</div></li><li><div id="layout_top_container"><div class="rmlayoutimage"><img src="' . $image_dir . '/label-top.png" /></div><input id="layout_radio-2" type="radio" name="form_layout" value="label_top" ' . $layout_checked_state['label_top'] . '>' .
        RM_UI_Strings::get('LABEL_LAYOUT_LABEL_TOP') .
        '</div></li><li><div id="layout_two_columns_container"><div class="rmlayoutimage"><img src="' . $image_dir . '/two-column.png" /></div><input id="layout_radio-3" type="radio" name="form_layout" value="two_columns" ' . $layout_checked_state['two_columns'] . '>' .
        RM_UI_Strings::get('LABEL_LAYOUT_TWO_COLUMNS') .
        '</div></li></ul></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">' .
        RM_UI_Strings::get('HELP_OPTIONS_GEN_LAYOUT') .
        '</div></div></div>';


//echo $layout_radio_button_html_string;
?>


<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
        $pages = get_pages();
        $wp_pages = RM_Utilities::wp_pages_dropdown();

        $form = new RM_PFBC_Form("options_general");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));

        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('GLOBAL_SETTINGS_GENERAL') . '</div>'));
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_FORM_STYLE'), "theme", array("classic" => __('Classic', 'custom-registration-form-builder-with-submission-manager'), "matchmytheme" => __("Match my theme", 'custom-registration-form-builder-with-submission-manager')), array("value" => $data['theme'], "id" => "theme_dropdown", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_THEME'))));
        $form->addElement(new Element_HTML(wp_nonce_field('rm_options_general')));
        $form->addElement(new Element_HTML($layout_radio_button_html_string));

        //Disabled for now, IP capture is needed to limit submissions from a given IP address. Unnecessary setting.
        //$form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_CAPTURE_INFO'), "user_ip", array("yes" => ''), $data['user_ip'] == 'yes' ? array("value" => "yes") : array()));

        $form->addElement(new Element_Textarea(RM_UI_Strings::get('LABEL_ALLOWED_FILE_TYPES'), "buy_pro", array("value" => $data['allowed_file_types'], 'disabled' => 1, "longDesc" => RM_UI_Strings::get('ALLOWED_FILE_TYPES_HELP'), "validation" => new Validation_RegExp("/[a-zA-Z0-9| ]*/", RM_UI_Strings::get('MSG_INVALID_CHAR')), "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_FILETYPES') . '<br><br>' . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));

        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ALLOWED_MULTI_FILES'), "buy_pro_2", array("yes" => ''), array("value" => "no", 'disabled' => 1, "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_FILE_MULTIPLE') . "<br><br>" . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));

        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_HIDE_TOOLBAR'), "hide_toolbar", array("yes" => ''), $data['hide_toolbar'] == 'yes' ? array("value" => "yes", 'onclick'=>'show_more_admin_toolbar_options(this)', "longDesc" => RM_UI_Strings::get('HELP_HIDE_TOOLBAR')) : array('onclick'=>'show_more_admin_toolbar_options(this)', "longDesc" => RM_UI_Strings::get('HELP_HIDE_TOOLBAR'))));
        if($data['hide_toolbar'] == 'yes')
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="more_admin_toolbar_options">'));
        else
            $form->addElement(new Element_HTML('<div class="childfieldsrow" id="more_admin_toolbar_options" style="display:none">'));
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_ENABLE_TOOLBAR_ADMIN'), "enable_toolbar_for_admin", array("yes" => ''), $data['enable_toolbar_for_admin'] == 'yes' ? array("value" => "yes", "longDesc" => RM_UI_Strings::get('HELP_ENABLE_TOOLBAR_ADMIN')) : array("longDesc" => RM_UI_Strings::get('HELP_ENABLE_TOOLBAR_ADMIN'))));
        $form->addElement(new Element_HTML('</div>'));

        $form->addElement(new Element_Checkbox(__("Show Form Limits above the Form?", 'custom-registration-form-builder-with-submission-manager'), "display_progress_bar", array("yes" => ''), $data['display_progress_bar'] == 'yes' ? array("value" => "yes", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_PROGRESS_BAR')) : array("longDesc" => RM_UI_Strings::get('HELP_OPTIONS_GEN_PROGRESS_BAR'))));

        $selected = ($data['post_submission_redirection_url'] !== null) ? $data['post_submission_redirection_url'] : 0;
        $pli_rdr_opts = array();

        ob_start();
        $pli_redir_select = ob_get_clean();
        $form->addElement(new Element_HTML($pli_redir_select));
        $form->addElement(new Element_File(RM_UI_Strings::get('LABEL_SUB_PDF_HEADER_IMG'), "", array("disabled" => "disabled", "id" => "sub_pdf_header_img", "accept" => "image/*", "longDesc" => RM_UI_Strings::get('SUB_PDF_HEADER_IMG_HELP') . '<br><br>' . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));
        $form->addElement(new Element_Textarea(RM_UI_Strings::get('LABEL_SUB_PDF_HEADER_TEXT'), "sub_pdf_header_text", array("disabled" => "disabled", "value" => "", "longDesc" => RM_UI_Strings::get('SUB_PDF_HEADER_TEXT_HELP') . '<br><br>' . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));

        $submission_type = array(
            "all" => __("All", 'custom-registration-form-builder-with-submission-manager'),
            "today" => __("Today", 'custom-registration-form-builder-with-submission-manager'),
            "week" => __("This Week", 'custom-registration-form-builder-with-submission-manager'),
            "month" => __("This Month", 'custom-registration-form-builder-with-submission-manager'),
            "year" => __("This Year", 'custom-registration-form-builder-with-submission-manager')
        );
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_SUBMISSION_ON_CARD'), "submission_on_card", $submission_type, array("value" => $data['submission_on_card'], "longDesc" => RM_UI_Strings::get('HELP_SUBMISSION_ON_CARD'))));
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_SHOW_ASTERIX'), "show_asterix", array("yes" => ''), $data['show_asterix'] == 'yes' ? array("value" => "yes", "longDesc" => RM_UI_Strings::get('HELP_SHOW_ASTERIX')) : array("longDesc" => RM_UI_Strings::get('HELP_SHOW_ASTERIX'))));
        $pdf_fonts= array('freeserif'=>__('FreeSerif','custom-registration-form-builder-with-submission-manager'),
                          'courier'=>__('Courier','custom-registration-form-builder-with-submission-manager'),'helvetica'=>__('Helvetica','custom-registration-form-builder-with-submission-manager'),
                          'times'=>__('Times','custom-registration-form-builder-with-submission-manager')); 
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_PDF_FONT'), "submission_pdf_font", $pdf_fonts, array("disabled"=>"disabled","value" =>"", "longDesc" => RM_UI_Strings::get('HELP_SUBMISSION_PDF_FONT').'<br><br>' . RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));


        $form->addElement(new Element_HTMLL('&#8592; &nbsp; ' . __("Cancel", 'custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));
        $form->render();
        ?> 

    </div>
    <?php
    include RM_ADMIN_DIR . 'views/template_rm_promo_banner_bottom.php';
    ?>
</div>
<pre class="rm-pre-wrapper-for-script-tags">
<script type="text/javascript">
    function show_more_admin_toolbar_options(obj){
        if(jQuery(obj).prop("checked") == true){
            jQuery("#more_admin_toolbar_options").show();
        } else {
            jQuery("#more_admin_toolbar_options").hide();
        }
    }

    jQuery(document).ready(function () {
        rm_setup_pli_rdr_opts();
    });

    function rm_setup_pli_rdr_opts() {
        var selected_opt = jQuery("#rm_pli_rdr_select").val();
        if (selected_opt == "0" || selected_opt == "__dashboard") {
            jQuery("#rm_pli_rdr_opts").hide();
        } else {
            jQuery("#rm_pli_rdr_opts").show();
        }
    }
</script></pre>

<?php   
}