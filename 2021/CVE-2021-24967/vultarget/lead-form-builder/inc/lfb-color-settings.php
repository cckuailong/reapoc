<?php

class LFB_COLORS {

  function change_color(){
    return '<a href="#0" class="lfb-cd-btn">Form Customize</a>';
  }

function lfb_color_form($fid){ 
  $color_palate ='#fff|#222|rgba(0,205,216,0.61)|#00CC22|rgba(219,54,153,0.82)|rgba(226,218,56,0.89)';
  global $wpdb;
  $lfbdb = NEW LFB_SAVE_DB();
   $current_url  = "//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&reset='.$fid;
   $reset = '';
    if(isset($_GET['reset']) && $_GET['reset'] == $fid){
        $reset = $lfbdb->lfb_reset_colors_data($fid);
    }

$colordata = $lfbdb->lfb_get_colors_data($fid);
if(isset($colordata[0]->colorData) && !empty($colordata[0]->colorData)):
$colors = unserialize($colordata[0]->colorData);
extract($colors);
endif;

$color_bg_ = isset($lfb_color_bg)?$lfb_color_bg:'rgba(255,255,255,0)';
$color_heading_ = isset($lfb_color_heading)?$lfb_color_heading:'#111111';

$color_label_ = isset($lfb_color_label)?$lfb_color_label:'#111111';
$color_field_border_ = isset($lfb_color_field_border)?$lfb_color_field_border:'rgba(33,15,15,0.67)';
$color_field_bg_ = isset($lfb_color_field_bg)?$lfb_color_field_bg:'#fff';
$color_field_placeholder_ = isset($lfb_color_field_placeholder)?$lfb_color_field_placeholder:'rgba(96,96,96,0.89)';

$color_button_text_ = isset($lfb_color_button_text)?$lfb_color_button_text:'#fff';
$color_button_bg_ = isset($lfb_color_button_bg)?$lfb_color_button_bg:'rgba(96,96,96,0.89)';
$lfb_color_button_border_ = isset($lfb_color_button_border)?$lfb_color_button_border:'rgba(96,96,96,0.89)';
$color_button_bg_hover_ = isset($lfb_color_button_bg_hover)?$lfb_color_button_bg_hover:'rgba(86,86,86,0.87)';
$lfb_button_font_size_ = isset($lfb_button_font_size)?$lfb_button_font_size:'16';
$lfb_btn_padding_tb_ = isset($lfb_btn_padding_tb)?$lfb_btn_padding_tb:'2';
$lfb_btn_padding_lr_ = isset($lfb_btn_padding_lr)?$lfb_btn_padding_lr:'35';
$lfb_button_aligment_ = isset($lfb_button_aligment)?$lfb_button_aligment:'left';

$lfb_header_image_ = isset($lfb_header_image)?$lfb_header_image:'';
$lfb_heading_font_size_ = isset($lfb_heading_font_size)?$lfb_heading_font_size:'26'; 
$lfb_heading_hide_ = isset($lfb_heading_hide)?$lfb_heading_hide:'block';
$lfb_heading_alignment_ = isset($lfb_heading_alignment)?$lfb_heading_alignment:'left';
$lfb_header_algmnt_tb_ = isset($lfb_header_algmnt_tb)?$lfb_header_algmnt_tb:0;
$lfb_header_algmnt_lr_ = isset($lfb_header_algmnt_lr)?$lfb_header_algmnt_lr:0;

$lfb_bg_image_ = isset($lfb_bg_image)?$lfb_bg_image:'';
$lfb_form_padding_left_ = isset($lfb_form_padding_left)?$lfb_form_padding_left:'2'; 
$lfb_form_padding_right_ = isset($lfb_form_padding_right)?$lfb_form_padding_right:'2'; 
$lfb_form_padding_top_ = isset($lfb_form_padding_top)?$lfb_form_padding_top:'2'; 
$lfb_form_padding_bottom_ = isset($lfb_form_padding_bottom)?$lfb_form_padding_bottom:'2'; 
$lfb_custom_css_ = isset($lfb_custom_css)?$lfb_custom_css:'';
$lfb_form_width_ = isset($lfb_form_width)?$lfb_form_width:'60';
  ?>

<main style="width:50%;" class="cd-main-content" colorid="<?php echo $fid; ?>" >
       <!-- your content here -->
    </main>
    <div class="cd-panel from-right">
        <header class="cd-panel-header">
        <h1><?php _e('Customize Form Skin','lead-form-builder'); ?></h1>
        <a href="#0" class="cd-panel-close">Close</a>
        </header>
        <div class="cd-panel-container">
        <div class="spin-over">
        <div style="float:none;" class="spinner"></div></div>
            <div class="cd-panel-content">
              <form id="lfb_formColor">
              <div id="lfb-accordion" >
              <h3><?php  _e('Form Size' ,'lead-form-builder'); ?></h3>
                <div>
                    <span class='color-wrap'>
                      <label class="lfb-form-size"><?php _e('Form Width in (%)','lead-form-builder'); ?></label>
                      <div id="lfb-formwidth">
                      <input type="hidden" name="lfb_form_width" id="lfb_form_width" value="<?php echo $lfb_form_width_; ?>" />
                        <div id="lfb-formwidth-handle" class="ui-slider-handle"></div>
                        <div id="lfb-formwidth-handle-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                  </span>
                </div>


                <h3><?php  _e('Header Settings ' ,'lead-form-builder'); ?></h3>
                <div>
                  
                  <span><label><?php  _e('Header Image' ,'lead-form-builder'); ?></label>
                  </span>
                  <div class="lfb_header_image">
                      <img class="lfb_custom_media_image lfb_custom_media_header_image" src="<?php echo $lfb_header_image_; ?>" style="margin:0;padding:0;max-width:50px;display:inline-block" /> 
                      <input type="hidden" class="widefat lfb_custom_media_header" name="lfb_header_image" lfb_hb='header' id="lfb_header_image" value="<?php echo $lfb_header_image_; ?>" style="margin-top:5px;">

                     <input type="button" class="button button-primary lfb_custom_media_button" id="lfb_custom_media_header_button" name="lfb_header_image_button" value="Upload Image" style="margin-top:5px;" />
                    <a class="image-panel-close button button-primary custom_remove_button"  onClick="remove_image('h');"><?php _e('Remove','lead-form-builder'); ?></a>
                  </div>        
                <span class='color-wrap'>
                  <label><?php _e('Heading Color','lead-form-builder'); ?> </label>        
                    <input type="text" class="alpha-color-picker" name="lfb_color_heading" id="lfb_color_heading" value="<?php echo $color_heading_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_heading_; ?>" data-show-opacity="true" />
                </span>
                <span class='color-wrap' >
                <label><?php  _e('Alignment' ,'lead-form-builder'); ?></label>

                    <input type="radio"  name="lfb_heading_alignment" class="alignment-heading" id="lfb_heading_left" value="left" <?php if($lfb_heading_alignment_=='left'){ echo "checked=''"; } ?> />Left
                    <input type="radio"  name="lfb_heading_alignment" class="alignment-heading" id="lfb_heading_right" value="right" <?php if($lfb_heading_alignment_=='right'){ echo "checked=''"; } ?>/>Right
                    <input type="radio"  name="lfb_heading_alignment" class="alignment-heading" id="lfb_heading_center" value="center"<?php if($lfb_heading_alignment_=='center'){ echo "checked=''"; } ?> />Center
                </span>
                <span class='color-wrap' >
                    <label><?php _e('Heading Show/Hide','lead-form-builder'); ?> </label>
                    <input type="radio"  name="lfb_heading_hide" class="lfb_heading_hide" id="lfb_heading_center" value="block" <?php if($lfb_heading_hide_=='block'){ echo 'checked=""'; } ?>/>Show
                    <input type="radio"  name="lfb_heading_hide" class="lfb_heading_hide" id="lfb_heading_right" value="none" <?php if($lfb_heading_hide_=='none'){ echo 'checked=""';} ?> />Hide
                </span>
                    <span class='color-wrap' >
                       <label class="lfb-header-fontsize"><?php _e('Heading Font Size','lead-form-builder'); ?></label>
                      <div id="lfb-heading-font">
                      <input type="hidden" name="lfb_heading_font_size" id="lfb_heading_font_size" value="<?php echo $lfb_heading_font_size_; ?>" />
                        <div id="lfb-heading-handle" class="ui-slider-handle"></div>
                        <div id="lfb-heading-handle-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                  </span>
                  <span class='color-wrap' >
                       <label class="lfb-header-paddingtb" ><?php _e('Top Padding','lead-form-builder'); ?></label>
                      <div id="lfb-header-algmnt-tb">
                      <input type="hidden" name="lfb_header_algmnt_tb" id="lfb_header_algmnt_tb" value="<?php echo $lfb_header_algmnt_tb_; ?>" />
                        <div id="lfb-header-algmnt-tb-handle" class="ui-slider-handle"></div>
                        <div id="lfb-header-algmnt-tb-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                  </span>
                    <span class='color-wrap' style="display: none;" >
                       <label class="lfb-header-paddinglr" ><?php _e('Header Left/right Padding','lead-form-builder'); ?></label>
                      <div id="lfb-header-algmnt-lr">
                      <input type="hidden" name="lfb_header_algmnt_lr" id="lfb_header_algmnt_lr" value="<?php echo $lfb_header_algmnt_lr_; ?>" />
                        <div id="lfb-header-algmnt-lr-handle" class="ui-slider-handle"></div>
                        <div id="lfb-header-algmnt-lr-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                  </span>
                
                </div>
                <h3><?php  _e('Background Settings' ,'lead-form-builder'); ?></h3>
                <div>
                      <span><label> <?php  _e('Background Image' ,'lead-form-builder'); ?> </label>
                      </span>
                      
                    <div class="lfb_header_image">
                        <img class="lfb_custom_media_image lfb_custom_media_bg_image" src="<?php echo $lfb_bg_image_; ?>" style="margin:0;padding:0;max-width:50px;display:inline-block" /> 
                      <input type="hidden" class="widefat lfb_custom_media_bg" name="lfb_bg_image" id="lfb_bg_image" lfb_hb='bg' value="<?php echo $lfb_bg_image_; ?>" style="margin-top:5px;">

                     <input type="button" class="button button-primary lfb_custom_media_button" id="lfb_custom_media_bg_button"   name="lfb_bg_image_button" value="Upload Image" style="margin-top:5px;" />
                    <a class="image-panel-close button button-primary custom_remove_button"  onClick="remove_image('b');"><?php _e('Remove','lead-form-builder'); ?></a>
                  </div>

                   <span class='color-wrap'>
            <label><?php _e('Form Background Color','lead-form-builder'); ?> </label>        
              <input type="text" class="alpha-color-picker" name="lfb_color_bg" id="lfb_color_bg" value="<?php echo $color_bg_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_bg_; ?>" data-show-opacity="false" />
              </span>

                <span>
                <label><?php  _e('Form Padding' ,'lead-form-builder'); ?></label>
                
              </span>
              <span class="color-wrap">
                  <label class="lfb-form-paddingtop">Top </label>
                 <div id="lfb-form-padding-top">
                      <input type="hidden"  name="lfb_form_padding_top" id="lfb_form_padding_top" value="<?php echo $lfb_form_padding_top_; ?>" />
                        <div id="lfb-form-padding-top-handle" class="ui-slider-handle"></div>
                        <div id="lfb-form-padding-top-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                   </span>
                   <span class="color-wrap">
                       <label class="lfb-form-paddingbottom" >Bottom</label>
                      <div id="lfb-form-padding-bottom">
                      <input type="hidden"  name="lfb_form_padding_bottom" id="lfb_form_padding_bottom" value="<?php echo $lfb_form_padding_bottom_; ?>" />
                        <div id="lfb-form-padding-bottom-handle" class="ui-slider-handle"></div>
                        <div id="lfb-form-padding-bottom-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                      </span>
                      <span class="color-wrap">
                       <label class="lfb-form-paddingleft" >Left</label>
                      <div id="lfb-form-padding-left">
                      <input type="hidden"  name="lfb_form_padding_left" id="lfb_form_padding_left" value="<?php echo $lfb_form_padding_left_; ?>" />
                        <div id="lfb-form-padding-left-handle" class="ui-slider-handle"></div>
                        <div id="lfb-form-padding-left-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                      </span>
                      <span class="color-wrap">
                       <label class="lfb-form-paddingright" >Right</label>
                      <div id="lfb-form-padding-right">
                      <input type="hidden"  name="lfb_form_padding_right" id="lfb_form_padding_right" value="<?php echo $lfb_form_padding_right_; ?>" />
                        <div id="lfb-form-padding-right-handle" class="ui-slider-handle"></div>
                        <div id="lfb-form-padding-right-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                      </span>
               
                </div>
            <h3>Field Color Settings</h3>
              <div>
               <span class='color-wrap'>
            <label><?php _e('Label Color','lead-form-builder'); ?> </label>        
              <input type="text" class="alpha-color-picker" name="lfb_color_label" id="lfb_color_label" value="<?php echo $color_label_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_label_; ?>" data-show-opacity="true" />
              </span>
               <span class='color-wrap'>
            <label><?php _e('Border Color','lead-form-builder'); ?> </label>        
              <input type="text" class="alpha-color-picker" name="lfb_color_field_border" id="lfb_color_field_border" value="<?php echo $color_field_border_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_field_border_; ?>" data-show-opacity="true" />
              </span>

              <span class='color-wrap'>
                <label><?php _e('Background Color','lead-form-builder'); ?> </label>    <input type="text" class="alpha-color-picker" name="lfb_color_field_bg" id="lfb_color_field_bg" value="<?php echo $color_field_bg_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_field_bg_; ?>" data-show-opacity="true" />
              </span>
               <span class='color-wrap'>
                <label><?php _e('Placeholder Color','lead-form-builder'); ?> </label>    <input type="text" class="alpha-color-picker" name="lfb_color_field_placeholder" id="lfb_color_field_placeholder"  value="<?php echo $color_field_placeholder_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_field_placeholder_; ?>" data-show-opacity="true" />
              </span> 

             </div> 

            <h3>Submit Button Settings</h3>
            <div>
             <span class='color-wrap'>
                <label><?php _e('Text Color','lead-form-builder'); ?> </label>   
                 <input type="text" class="alpha-color-picker" name="lfb_color_button_text" id="lfb_color_button_text" value="<?php $color_button_text_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php $color_button_text_; ?>" data-show-opacity="true" />
              </span>
              <span class='color-wrap'>
                <label><?php _e('Background Color','lead-form-builder'); ?> </label>    
                <input type="text" class="alpha-color-picker" name="lfb_color_button_bg" id="lfb_color_button_bg" value="<?php echo $color_button_bg_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_button_bg_; ?>" data-show-opacity="true" />
              </span>
              <span class='color-wrap'>
                <label><?php _e('Hover Background Color','lead-form-builder'); ?> </label>   
                 <input type="text" class="alpha-color-picker" name="lfb_color_button_bg_hover" id="lfb_color_button_bg_hover" value="<?php echo $color_button_bg_hover_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $color_button_bg_hover_; ?>" data-show-opacity="true" />
              </span>
              <span class='color-wrap'>
                <label><?php _e('Border Color','lead-form-builder'); ?> </label>   
                 <input type="text" class="alpha-color-picker" name="lfb_color_button_border" id="lfb_color_button_border" value="<?php echo $lfb_color_button_border_; ?>" data-palette="<?php echo $color_palate; ?>" data-default-color="<?php echo $lfb_color_button_border_; ?>" data-show-opacity="true" />
              </span>
            <span class='color-wrap' >
                <label><?php  _e('Button Alignment' ,'lead-form-builder'); ?></label>
                    <input type="radio"  name="lfb_button_aligment" class="lfb-btn-align" id="lfb_button_left" value="left" <?php if($lfb_button_aligment_=='left'){ echo "checked=''"; } ?>/>Left
                    <input type="radio"  name="lfb_button_aligment" class="lfb-btn-align" id="lfb_heading_right" value="right" <?php if($lfb_button_aligment_=='right'){ echo "checked=''"; } ?> />Right
                    <input type="radio"  name="lfb_button_aligment" class="lfb-btn-align" id="lfb_heading_center" value="center" <?php if($lfb_button_aligment_=='center'){ echo "checked=''"; } ?> />Center
            </span>
                <span class='color-wrap' >
                    <label class="lfb-button-fontsize" ><?php _e('Button Text Size','lead-form-builder'); ?> </label>
                      <div id="lfb-button-font">
                      <input type="hidden"  name="lfb_button_font_size" id="lfb_button_font_size" value="<?php echo $lfb_button_font_size_; ?>" />
                        <div id="lfb-button-handle" class="ui-slider-handle"></div>
                        <div id="lfb-button-font-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                </span>
                <span class='color-wrap' >
                  <label class="lfb-button-paddingtb"><?php _e('Top/Bottom Padding','lead-form-builder'); ?> </label>
                      <div id="lfb-btn-padding-tb">
                      <input type="hidden"  name="lfb_btn_padding_tb" id="lfb_btn_padding_tb" value="<?php echo $lfb_btn_padding_tb_; ?>" />
                        <div id="lfb-btn-padding-tb-handle" class="ui-slider-handle"></div>
                        <div id="lfb-btn-padding-tb-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                      </div>
                        </span>
                        <span class='color-wrap'>
                  <label class="lfb-button-paddinglr" ><?php _e('Button Width','lead-form-builder'); ?> </label>
                    <div id="lfb-btn-padding-lr">
                      <input type="hidden"  name="lfb_btn_padding_lr" id="lfb_btn_padding_lr" value="<?php echo $lfb_btn_padding_lr_; ?>" />
                        <div id="lfb-btn-padding-lr-handle" class="ui-slider-handle"></div>
                        <div id="lfb-btn-padding-lr-rng" class="ui-slider-range ui-widget-header ui-slider-range-min"></div>
                    </div>
                </span>
           
            </div>
            <h3><?php _e('Custom Css' ,'lead-form-builder'); ?></h3>
              <div>
                <p>
                  <span class='color-wrap'>
                  <label> <?php _e('Custom Css Write','lead-form-builder'); ?></label>    
                  <textarea rows="8" class="alpha-color-picker" name="lfb_custom_css" id="lfb_custom_css"><?php echo $lfb_custom_css_; ?></textarea>  
                  </span>
                </p>
              </div>
              <h3><?php  _e('Reset All Customization','lead-form-builder'); ?></h3>
                <div>
                    <span class='color-wrap' >
                       <label>
                       <?php _e('This will reset all styling and customization.','lead-form-builder'); ?>
                       </label>
                       <div class="reset-form" ><a class="reset-frm-btn" href="#" data-confirm="This will reset all styling and customization. Do you want to proceed?">Reset Form <?php echo LFB_FORM_PRO_FEATURE; ?>
</a></div>
                  </span>
                </div>
          </div>
            <?php echo LFB_FORM_PRO_TEXT. LFB_FORM_PRO_FEATURE; ?>
              </form>
            </div> <!-- cd-panel-content -->
        </div> <!-- cd-panel-container -->
    </div> <!-- cd-panel -->
    <?php
     } 
            } ?>