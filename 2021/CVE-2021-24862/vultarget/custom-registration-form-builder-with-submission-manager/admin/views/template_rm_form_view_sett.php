<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_view_sett.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
wp_enqueue_media();
$submit_btn_label = $data->model->get_form_options()->form_submit_btn_label ? : __('Submit', 'custom-registration-form-builder-with-submission-manager');
wp_register_script('rm-form_presentation', RM_BASE_URL. 'admin/js/script_rm_form_presentation.js', array(), null, false);
wp_localize_script('rm-form_presentation','pr_data',array('upload_btn_title'=>__('Choose Image','custom-registration-form-builder-with-submission-manager'),'ajaxnonce' => wp_create_nonce('rm_form_settings_controller')));
wp_enqueue_script('rm-form_presentation');
        echo '<style>';
        if($data->model->form_options->btn_hover_color)
            echo '.rm_btn_selector .rm_btn_focus:hover{ background-color:'.$data->model->form_options->btn_hover_color.' !important; }';
        if($data->model->form_options->field_bg_focus_color || $data->model->form_options->text_focus_color){
            echo '.rmagic .rmrow .rm_field_focus_bg:focus{';
            if($data->model->form_options->field_bg_focus_color)
                echo 'background-color:'.$data->model->form_options->field_bg_focus_color.' !important; } ';
            if($data->model->form_options->text_focus_color)
                echo '.rmagic .rmrow .rm_field_focus_text:focus { color:'.$data->model->form_options->text_focus_color.' !important; }';
            
        }
        echo '</style>';
?>
<pre class="rm-pre-wrapper-for-script-tags"><script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script></pre>

<div class="rmagic" ng-controller="formStyleCtrl"  ng-app="formStyleApp">
    <div class="operationsbar rm-form-design-view-head">
        <div class="rmtitle"><?php echo RM_UI_Strings::get('LABEL_FORM_PRESENTATION'); ?></div>
        <div class="nav">
            <ul><li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
              
                <li><a href="javascript:void(0)" ng-click='resetAll()' id="rm-field-selection-popup"><?php echo RM_UI_Strings::get('LABEL_RESET'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="rmnotice-row">
        <div class="rmnotice">
            To modify layout elements like label positions and form columns, go to <a target="_blank" href="<?php echo admin_url('admin.php?page=rm_options_general'); ?>">Global Settings</a>.            
        </div>
    </div>
    <!--Dialogue Box Starts-->
    <fieldset class="rm_form_presentation_fs">
        <legend style="display:none" id="rm_section_name" style="<?php echo $data->model->form_options->style_section; ?>"><?php _e('Section Name','custom-registration-form-builder-with-submission-manager'); ?></legend>
        <div class="rm_form_container">
            <div class="rm_style_container" id="rm_style_container" style='<?php echo $data->model->get_form_options()->style_form; ?>'>
                <div class="rm_element_selector"> <input class="rm_selector" type="button"  id="rm_form_selector" value="<?php _e('Form Selector', 'custom-registration-form-builder-with-submission-manager') ?>" ng-click="selectForm()"/></div>

                <div class="rmrow rm_edit_form_ui">
                    <div class="rmfield" id="rm_field_label"><?php _e('Field Label', 'custom-registration-form-builder-with-submission-manager') ?></div>
                    <div class="rminput">
                        <input class="rm_field_focus_bg rm_field_focus_text" type="text" style='<?php echo $data->model->get_form_options()->style_textfield; ?>' placeholder="<?php _e('Field','custom-registration-form-builder-with-submission-manager'); ?>" id="rm_textfield" />
                    </div>
                    <div class="rm_element_selector">
                        <input  type="button" class="rm_selector"  id="rm_text_field_selector" value="<?php _e('Text Field Selector','custom-registration-form-builder-with-submission-manager'); ?>" ng-click="selectTextField()"/>
                    </div>

                    <div class="rm_style_action"  ng-show="selectedElement == 'rm_textfield'" >
                        <style-action-box selected-element="rm_textfield" el-text="true"></style-action-box>
                    </div>
                </div>
                <div class="rmrow rm_edit_form_ui">
                    <div class="rmfield" id="rm_field_label"><?php _e('Field Label', 'custom-registration-form-builder-with-submission-manager') ?></div>
                    <div class="rminput">
                        <input class="rm_field_focus_bg rm_field_focus_text" style='<?php echo $data->model->get_form_options()->style_textfield; ?>' type="text" placeholder="<?php _e('Field','custom-registration-form-builder-with-submission-manager'); ?>" id="rm_textfield" />
                    </div>
                </div>
                <div class="rmrow rm_edit_form_ui">
                    <div class="rmfield" id="rm_field_label"><?php _e('Field Label', 'custom-registration-form-builder-with-submission-manager') ?></div>
                    <div class="rminput">
                        <input class="rm_field_focus_bg rm_field_focus_text" style='<?php echo $data->model->get_form_options()->style_textfield; ?>' type="text" placeholder="<?php _e('Field','custom-registration-form-builder-with-submission-manager'); ?>" id="rm_textfield" />
                    </div>
                </div>
                <div class="rmrow rm_edit_form_ui">
                    <div class="rmfield" id="rm_field_label"><?php _e('Field Label', 'custom-registration-form-builder-with-submission-manager') ?></div>
                    <div class="rminput">
                        <input class="rm_field_focus_bg rm_field_focus_text" style='<?php echo $data->model->get_form_options()->style_textfield; ?>' type="text" placeholder="<?php _e('Field','custom-registration-form-builder-with-submission-manager'); ?>" id="rm_textfield" />
                    </div>
                </div>
                <div class="rm_style_action"  ng-show="selectedElement == 'rm_style_container'" >
                    <style-action-box selected-element="rm_style_container" el-form="true"></style-action-box>
                </div>
                <div class="rm_btn_selector">
                    <input class="rm_btn_focus" type="button" style='<?php echo $data->model->get_form_options()->style_btnfield; ?>' value="<?php echo $submit_btn_label; ?>" id="rm_btnfield"/>
                    <input type="button" class="rm_selector"   id="rm_button_field_selector" value="" ng-click="selectButtonField()"/>
                    <div class="rm_style_action" ng-show="selectedElement == 'rm_btnfield'" >
                        <style-action-box selected-element="rm_btnfield" el-btn="true"></style-action-box>
                    </div>
                </div>
                <input type="hidden" value="<?php echo $data->model->get_form_id(); ?>" id="rm_form_id">
            </div>
        </div>
    </fieldset>
    
        <div class="rmnotice rm-invite-field-row" style="text-transform:none"><?php echo RM_UI_Strings::get('DISCLAIMER_FORM_VIEW_SETTING');?></div>

    <div class="buttonarea popup-button-group" style="">
        <div class="cancel">
            <?php if(isset($_GET['rdrto']) && $_GET['rdrto']): ?>
                <a value="&amp;#8592; &amp;nbsp; Cancel" href="?page=<?php esc_html_e($_GET['rdrto']); ?>&amp;rm_form_id=<?php echo $data->model->form_id; ?>" id="form_sett_post_sub-element-18">← &nbsp; <?php _e('Cancel', 'custom-registration-form-builder-with-submission-manager') ?></a>
            <?php else: ?>
                <a value="&amp;#8592; &amp;nbsp; Cancel" href="?page=rm_form_sett_manage&amp;rm_form_id=<?php echo $data->model->form_id; ?>" id="form_sett_post_sub-element-18">← &nbsp; <?php _e('Cancel', 'custom-registration-form-builder-with-submission-manager') ?></a>
            <?php endif; ?>
        </div> 
        <input type="button" value="<?php _e('Save', 'custom-registration-form-builder-with-submission-manager') ?>" name="submit" id="rm_submit_btn" class="rm_btn btn btn-primary popup-submit" ng-click="saveStyles()">
    </div>
        
<!--     /**** Landing Page Promo ****/-->

    <div id="landing_page_sub_footer" class="" style="display:none;">
        <a target="__blank" href="https://registrationmagic.com/landing-page-addon/">
            <img src="<?php echo RM_IMG_URL . 'landing_page_banner.png'; ?>">
        </a>
    </div>
<!--     /**** Landing Page Promo ****/-->
        
    <div id="rm_styling_options" style="display:none">
        <div class="rm_pop_up_close" ng-click="close()">X</div>
        
        <div class="rm_pop_up_tab">
            <div id="rm_field_styling_options" ng-show="elText"> 
                <div class="rm_pop_up_row">
                    <label><?php _e('Label Color', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_label_color" class="jscolor" ng-model="styles.label_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Text Color', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_text_color" class="jscolor" ng-model="styles.text_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Placeholder Color', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_placeholder_color" class="jscolor" ng-model="styles.placeholder_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php _e('Outline Color', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_outline_color" class="jscolor" ng-model="styles.text_outline_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php _e('Color on Focus', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_field_focus_color" class="jscolor" ng-model="styles.text_focus_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php _e('Background on Focus', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_field_bg_focus_color" class="jscolor" ng-model="styles.field_bg_focus_color" ng-change="executeAction()" >
                </div>
                
            </div>
            <div id="rm_form_styling_options" ng-show="elForm">
                <div class="rm_pop_up_row">
                    <label><?php _e('Form Padding', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_padding" ng-model="styles.padding" value="0" ng-change="executeAction()" >
                </div>
            </div>
           
            <div id="rm_border_styling_options">
                <div class="rm_pop_up_row">
                    <label><?php _e('Border Color', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="text" id="rm_border_color" class="jscolor" ng-model="styles.border_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Border Width', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="number" id="rm_border_width" ng-model="styles.border_width" ng-change="executeAction()">
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Border Radius', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <input type="number" id="rm_border_radius" ng-model="styles.border_radius" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Border Style', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                    <select id="rm_border_style" ng-model="styles.border_style" ng-change="executeAction()" >
                        <option selected value=""><?php echo RM_UI_Strings::get('LABEL_SELECT');?></option>
                        <option><?php _e('solid', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('dashed', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('dotted', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('double', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('groove', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('hidden', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('inherit', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('initial', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('inset', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('none', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('outset', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('ridge', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    </select>    
                </div>
            </div>
            <div class="rm_pop_up_row">
                <label><?php _e('Background Image', 'custom-registration-form-builder-with-submission-manager') ?></label>
                <input type="button" class="upload-btn" value="<?php _e('Upload', 'custom-registration-form-builder-with-submission-manager') ?>" ng-click="mediaUploader()">
                <input type="button" class="rm_trash" ng-click="removeBackImage()" value="<?php _e('Remove', 'custom-registration-form-builder-with-submission-manager') ?>">
            </div>
            <div class="rm_pop_up_row">
                <label><?php _e('Image Repeat', 'custom-registration-form-builder-with-submission-manager') ?> </label>
                <select id="rm_image_repeat" ng-model="styles.image_repeat" ng-change="executeAction()" >
                    <option selected value=""><?php echo RM_UI_Strings::get('LABEL_SELECT');?></option>
                    <option><?php _e('repeat', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('inherit', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('initial', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('no-repeat', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('repeat-x', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('repeat-y', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('round', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('space', 'custom-registration-form-builder-with-submission-manager'); ?></option>
                </select>    
            </div>
            <div id="rm_btn_styling_options" ng-show="elBtn">
                <div class="rm_pop_up_row">
                    <label><?php _e('Button Label', 'custom-registration-form-builder-with-submission-manager') ?></label>
                    <input type="text" class="ng-pristine ng-untouched ng-valid" ng-change="executeAction()" ng-model="styles.btn_label">
                </div>
                <div class="rm_pop_up_row">
                    <label><?php _e('Font Color', 'custom-registration-form-builder-with-submission-manager') ?></label>
                    <input type="text" class="jscolor" ng-change="executeAction()" ng-model="styles.btn_font_color"  >
                </div>
                
                <div class="rm_pop_up_tab">
                    <div class="rm_pop_up_row">
                        <label><?php _e('Hover Color', 'custom-registration-form-builder-with-submission-manager') ?></label>
                        <input type="text" class="jscolor" id="rm_btn_hover_color" ng-model="styles.btn_hover_color" ng-change="executeAction()"  >
                    </div>   
                </div>
            </div>
            <div class="rm_pop_up_row">
                <label><?php _e('Background Color', 'custom-registration-form-builder-with-submission-manager') ?></label>
                <input type="text" class="jscolor" id="rm_background_border" ng-model="styles.background_color" ng-change="executeAction()"  >
            </div>
            
            
            
        </div>
        
        <div id="rm_custom_style"><?php echo $data->model->form_options->placeholder_css; ?></div>
    </div>
</div>
<script>
jQuery(document).ready(function(){
    jQuery('.jscolor').each(function(index){
        var myColor = new jscolor(jQuery(this)[0]);
    });
});
</script>
<?php } ?>