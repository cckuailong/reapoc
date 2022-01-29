<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_view_sett.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
wp_enqueue_media();
wp_register_script('rm-login-form-presentation', RM_BASE_URL . 'admin/js/script_rm_form_presentation.js', array(), null, false);
wp_localize_script('rm-login-form-presentation','pr_data',array('upload_btn_title'=>__('Choose Image','custom-registration-form-builder-with-submission-manager'),'older_ie'=>__('You are using older version of IE. Please update IE to latest version','custom-registration-form-builder-with-submission-manager'),'ajaxnonce' => wp_create_nonce('rm_form_settings_controller')));
wp_enqueue_script('rm-login-form-presentation');
$fields = $data['fields'];
$design = $data['design'];

$buttons= $data['buttons'];
$submit_btn_label = !empty($buttons['login_btn']) ? $buttons['login_btn'] : 'Login';

        echo '<style>';
                if(isset($design['btn_hover_color']))
                    echo '.rm_btn_selector .rm_btn_focus:hover{ background-color:'.$design['btn_hover_color'].' !important; }';
                if(isset($design['field_bg_focus_color']) || isset($design['text_focus_color'])){
                    echo '.rmagic .rmrow .rm_field_focus_bg:focus{';
                    if(isset($design['field_bg_focus_color']))
                        echo 'background-color:'.$design['field_bg_focus_color'].' !important; } ';
                    if(isset($design['text_focus_color']))
                        echo '.rmagic .rmrow .rm_field_focus_text:focus { color:'.$design['text_focus_color'].' !important; }';

                }
        echo '</style>';
?>
<pre class="rm-pre-wrapper-for-script-tags"><script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script></pre>

<div class="rmagic" ng-controller="formStyleCtrl"  ng-app="formStyleApp">
    <div class="operationsbar rm-form-design-view-head">
        <div class="rmtitle"><?php echo RM_UI_Strings::get('LABEL_FORM_PRESENTATION'); ?></div>
        <div class="nav">
            <ul>
               <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
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
        <div class="rm_form_container">
            <div class="rm_style_container" id="rm_style_container"  style='<?php echo $design['style_form']; ?>'>
                <div class="rm_element_selector"> <input class="rm_selector" type="button"  id="rm_form_selector" value="Form Selector" ng-click="selectForm()"/></div>
                <?php $loop_index = 0; ?>
                <?php foreach ($fields as $key => $field): $loop_index++; ?>
                        <div class="rmrow rm_edit_form_ui">
                            <div class="rmfield" style="<?php echo !empty($design['style_label']) ? $design['style_label'] : ''; ?>" id="rm_field_label"><?php echo $field['field_label']; ?></div>
                            <div class="rminput">
                                <input class="rm_field_focus_bg rm_field_focus_text" style='<?php echo $design['style_textfield']; ?>' type="text"  placeholder="<?php echo $field['placeholder']; ?>" id="rm_textfield" <?php echo !empty($design['field_bg_focus_color']) ? 'data-field-bg-focus-color="'.$design['field_bg_focus_color'].'"' : ''; ?> <?php echo !empty($design['text_focus_color']) ? 'data-field-text-focus-color="'.$design['text_focus_color'].'"' : ''; ?>/>
                            </div>
                        <?php if ($loop_index == 1) : ?>
                                    <div class="rm_element_selector">
                                        <input  type="button" class="rm_selector"  id="rm_text_field_selector" value="<?php _e("Text Field Selector", 'custom-registration-form-builder-with-submission-manager') ?>" ng-click="selectTextField()"/>
                                    </div>

                                    <div class="rm_style_action"  ng-show="selectedElement == 'rm_textfield'" >
                                        <style-action-box selected-element="rm_textfield" el-text="true"></style-action-box>
                                    </div>
                        <?php endif; ?>
                        </div>
                        <div class="rm_style_action"  ng-show="selectedElement == 'rm_style_container'" >
                            <style-action-box selected-element="rm_style_container" el-form="true"></style-action-box>
                        </div>
                <?php endforeach; ?>
                
                <div class="rm_btn_selector">
                    <input class="rm_btn_focus" type="button" style='<?php echo $design['style_btnfield']; ?>' value="<?php echo $submit_btn_label; ?>" id="rm_btnfield" <?php echo !empty($design['btn_hover_color']) ? 'data-btn-hover-color="'.$design['btn_hover_color'].'"' : ''; ?>/>
                    <input type="button" class="rm_selector"   id="rm_button_field_selector" value="" ng-click="selectButtonField()"/>
                    <div class="rm_style_action" ng-show="selectedElement == 'rm_btnfield'" >
                        <style-action-box selected-element="rm_btnfield" el-btn="true"></style-action-box>
                    </div>
                </div>
                
                <input type="hidden" id="rm_form_id" value="login">
            </div>
        </div>
    </fieldset>
    
        <div class="rmnotice rm-invite-field-row" style="text-transform:none"><?php echo RM_UI_Strings::get('DISCLAIMER_FORM_VIEW_SETTING'); ?></div>

    <div class="buttonarea popup-button-group" style="">
        <div class="cancel">
            <?php if(isset($_GET['rdrto']) && $_GET['rdrto']): ?>
            <a value="&amp;#8592; &amp;nbsp; Cancel" href="?page=<?php esc_html_e($_GET['rdrto']); ?>" id="form_sett_post_sub-element-18">← &nbsp; <?php _e("Cancel", 'custom-registration-form-builder-with-submission-manager') ?></a>
            <?php else: ?>
            <a value="&amp;#8592; &amp;nbsp; Cancel" href="?page=rm_login_sett_manage" id="form_sett_post_sub-element-18">← &nbsp; <?php _e("Cancel", 'custom-registration-form-builder-with-submission-manager') ?></a>
            <?php endif; ?>
        </div> 
        <input type="button" value="<?php _e("Save", 'custom-registration-form-builder-with-submission-manager') ?>" name="submit" id="rm_submit_btn" class="rm_btn btn btn-primary popup-submit" ng-click="saveStyles()">
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
                    <label><?php echo RM_UI_Strings::get('LABEL_LABEL_COLOR'); ?> </label>
                    <input type="text" id="rm_label_color" class="jscolor" ng-model="styles.label_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_TEXT_COLOR'); ?> </label>
                    <input type="text" id="rm_text_color" class="jscolor" ng-model="styles.text_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_PLACEHOLDER_COLOR'); ?></label>
                    <input type="text" id="rm_placeholder_color" class="jscolor" ng-model="styles.placeholder_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_OUTLINE_COLOR'); ?> </label>
                    <input type="text" id="rm_outline_color" class="jscolor" ng-model="styles.text_outline_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_FOCUS_COLOR'); ?> </label>
                    <input type="text" id="rm_field_focus_color" class="jscolor" ng-model="styles.text_focus_color" ng-change="executeAction()" >
                </div>
                
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_FOCUS_BG_COLOR'); ?> </label>
                    <input type="text" id="rm_field_bg_focus_color" class="jscolor" ng-model="styles.field_bg_focus_color" ng-change="executeAction()" >
                </div>
                
            </div>
            <div id="rm_form_styling_options" ng-show="elForm">
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_FORM_PADDING'); ?> </label>
                    <input type="text" id="rm_padding" ng-model="styles.padding" value="0" ng-change="executeAction()" >
                </div>
            </div>
            <div id="rm_section_styling_options" ng-show="elForm">
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_SECTION_TEXT_COLOR'); ?> </label>
                    <input type="text" class="jscolor" id="rm_section_text_color" ng-model="styles.section_text_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_SECTION_TEXT_STYLE'); ?> </label>
                    <select id="rm_section_text_style" ng-model="styles.section_text_style" ng-change="executeAction()" >
                        <option selected value=""><?php echo RM_UI_Strings::get('LABEL_SELECT'); ?></option>
                        <option  value="inherited">inherited</option>
                        <option  value="italic">italic</option>
                        <option  value="normal">normal</option>
                        <option  value="oblique">oblique</option>
                    </select>
                </div>
            </div>
            <div id="rm_border_styling_options">
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_BORDER_COLOR'); ?> </label>
                    <input type="text" id="rm_border_color" class="jscolor" ng-model="styles.border_color" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_BORDER_WIDTH'); ?> </label>
                    <input type="number" id="rm_border_width" ng-model="styles.border_width" ng-change="executeAction()">
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_BORDER_RADIUS'); ?> </label>
                    <input type="number" id="rm_border_radius" ng-model="styles.border_radius" ng-change="executeAction()" >
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_BORDER_STYLE'); ?></label>
                    <select id="rm_border_style" ng-model="styles.border_style" ng-change="executeAction()" >
                        <option selected value=""><?php echo RM_UI_Strings::get('LABEL_SELECT'); ?></option>
                        <option><?php _e('solid','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('dashed','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('dotted','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('double','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('groove','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('hidden','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('inherit','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('initial','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('inset','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('none','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('outset','custom-registration-form-builder-with-submission-manager'); ?></option>
                        <option><?php _e('ridge','custom-registration-form-builder-with-submission-manager'); ?></option>
                    </select>    
                </div>
            </div>
            <div class="rm_pop_up_row">
                <label><?php echo RM_UI_Strings::get('LABEL_BACKGROUND_IMAGE'); ?></label>
                <input type="button" class="upload-btn" value="Upload" ng-click="mediaUploader()">
                <input type="button" class="rm_trash" ng-click="removeBackImage()" value="Remove">
            </div>
            <div class="rm_pop_up_row">
                <label><?php echo RM_UI_Strings::get('LABEL_IMAGE_REPEAT'); ?> </label>
                <select id="rm_image_repeat" ng-model="styles.image_repeat" ng-change="executeAction()" >
                     <option selected value=""><?php echo RM_UI_Strings::get('LABEL_SELECT'); ?></option>
                    <option><?php _e('repeat','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('inherit','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('initial','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('no-repeat','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('repeat-x','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('repeat-y','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('round','custom-registration-form-builder-with-submission-manager'); ?></option>
                    <option><?php _e('space','custom-registration-form-builder-with-submission-manager'); ?></option>
                </select>    
            </div>
            <div id="rm_btn_styling_options" ng-show="elBtn">
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_BUTTON_LABEL'); ?></label>
                    <input type="text" class="ng-pristine ng-untouched ng-valid" ng-change="executeAction()" ng-model="styles.btn_label">
                </div>
                <div class="rm_pop_up_row">
                    <label><?php echo RM_UI_Strings::get('LABEL_FONT_COLOR'); ?></label>
                    <input type="text" class="jscolor" ng-change="executeAction()" ng-model="styles.btn_font_color"  >
                </div>
                
                <div class="rm_pop_up_tab">
                    <div class="rm_pop_up_row">
                        <label><?php echo RM_UI_Strings::get('LABEL_HOVER_COLOR'); ?></label>
                        <input type="text" class="jscolor" id="rm_btn_hover_color" ng-model="styles.btn_hover_color" ng-change="executeAction()"  >
                    </div>   
                </div>
            </div>
            <div class="rm_pop_up_row">
                <label><?php echo RM_UI_Strings::get('LABEL_BACKGROUND_COLOR'); ?></label>
                <input type="text" class="jscolor" id="rm_background_border" ng-model="styles.background_color" ng-change="executeAction()"  >
            </div>
            
            
            
        </div>
        
        <?php if(isset($design['placeholder_css'])) : ?>
            <div id="rm_custom_style"><?php echo $design['placeholder_css']; ?></div>
        <?php endif; ?>    
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