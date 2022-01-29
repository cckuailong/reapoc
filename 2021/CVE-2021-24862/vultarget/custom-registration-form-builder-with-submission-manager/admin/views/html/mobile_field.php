<?php
if (!defined('WPINC')) {
    die('Closed');
}

 $form->addElement(new Element_Radio('<b>'.__('Format', 'custom-registration-form-builder-with-submission-manager').'</b>', "format_type", array('international'=>__('International','custom-registration-form-builder-with-submission-manager'),'local'=>__('Local','custom-registration-form-builder-with-submission-manager'),'custom'=>__('Custom','custom-registration-form-builder-with-submission-manager'),'limited'=>__('Specific Countries','custom-registration-form-builder-with-submission-manager')), array("value" =>empty($data->model->field_options->format_type) ? 'international' : $data->model->field_options->format_type,"class"=>"rm-mobile-formats", "longDesc"=>__( 'Select the format for displaying and validating user input for mobile field value.', 'custom-registration-form-builder-with-submission-manager' ))));
    // International format options
    $form->addElement(new Element_HTML('<div style="display:none" id="child_international" class="childfieldsrow rm-format-options">'));
        $form->addElement(new Element_Checkbox("<b>" .__('Sync with Country Field', 'custom-registration-form-builder-with-submission-manager'). "</b>", "sync_country", array(1 => ""), array("class" => "rm_sync_country rm_field_multiline rm_input_type", "value" => isset($data->model->field_options->sync_country) ? $data->model->field_options->sync_country: 0, "longDesc"=>__('If you have a country field on your form, the country in the phone field will automatically match the country selected by user in the country field.', 'custom-registration-form-builder-with-submission-manager'))));
            
            $form->addElement(new Element_HTML('<div id="sync_country_options" class="childfieldsrow">'));
                // Options
                $form->addElement(new Element_Select("<b>" .__('Select country field', 'custom-registration-form-builder-with-submission-manager'). "</b>", "country_field", $data->country_fields, array("value" => $data->model->field_options->country_field, "class" => "rm_static_field rm_required", "longDesc"=>'')));
                if(isset($data->country_fields['not_found'])){
                    $form->addElement(new Element_HTML('<div class="">'.__('There is no country or address field in your form yet. Once you add it, you can set it here later.', 'custom-registration-form-builder-with-submission-manager').'</div>'));
                }
                $form->addElement(new Element_Checkbox("<b>" .__('Force country match', 'custom-registration-form-builder-with-submission-manager'). "</b>", "country_match", array(1 => ""), array("class" => "rm_field_multiline rm_input_type", "value" => isset($data->model->field_options->country_match) ? $data->model->field_options->country_match : 0, "longDesc"=>__('When checked, the country in mobile number field will be locked same as the country field and user will not be able to change it.', 'custom-registration-form-builder-with-submission-manager'))));

                $count=0;  
                if(!empty($data->model->field_options->preferred_countries)){
                        $country_list= explode(',',$data->model->field_options->preferred_countries); 
                            $count= count($country_list);
                }   
                $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield"><label><b>'.__('Preferred Countries','custom-registration-form-builder-with-submission-manager').'</b></label></div><div class="rminput"><div id="preferred_countries_count">'.$count.' '.__('Selected','custom-registration-form-builder-with-submission-manager').' </div><a href="#rm-country-selector" onclick="openModal(this,\'preferred_countries\')">'.__('Select Countries', 'custom-registration-form-builder-with-submission-manager').'</a></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.__('Prominently display specific counties on top.','custom-registration-form-builder-with-submission-manager').'</div></div></div>'));
            $form->addElement(new Element_HTML('</div>'));
          
    $form->addElement(new Element_Checkbox("<b>" .__('Use GeoIP', 'custom-registration-form-builder-with-submission-manager'). "</b>", "en_geoip", array(1 => ""), array("class" => "rm_field_multiline rm_input_type", "value" => $data->model->field_options->en_geoip, "longDesc"=>__('Auto-select country based on user’s IP location.', 'custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_HTML('</div>')); 
    
    // Local format options
    $form->addElement(new Element_HTML('<div style="display:none" id="child_local" class="childfieldsrow rm-format-options">'));    
        $form->addElement(new Element_Textbox("<b>".__('Local Format', 'custom-registration-form-builder-with-submission-manager'). "</b>", "local_mask", array("readonly"=>1,"class" => "rm_static_field", "value" => '(000)-000-0000', "longDesc"=>__('Standard mobile number format will be used.', 'custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_HTML('</div>'));
    
    // Custom format options
    $form->addElement(new Element_HTML('<div style="display:none" id="child_custom" class="childfieldsrow rm-format-options">'));    
        $form->addElement(new Element_Textbox("<b>".__('Define custom format', 'custom-registration-form-builder-with-submission-manager'). "</b>", "custom_mobile_format", array("class" => "rm_static_field", "value" =>!empty($data->model->field_options->custom_mobile_format) ? $data->model->field_options->custom_mobile_format : '(000)-000-0000', "longDesc"=>__('Enter the custom format you wish to use for mobile number. Use 9 for numeric placeholder. You can combine it with () and -.', 'custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_HTML('</div>'));
     
    $lim_countries_count=0;
    if(!empty($data->model->field_options->lim_countries)){
            $countries= explode(',',$data->model->field_options->lim_countries); 
            $lim_countries_count= count($countries);
    }
    
    $lim_pref_countries_count=0;
    if(!empty($data->model->field_options->lim_pref_countries)){
            $countries= explode(',',$data->model->field_options->lim_pref_countries); 
            $lim_pref_countries_count= count($countries);
    }
   
      // Limited format options
    $form->addElement(new Element_HTML('<div style="display:none" id="child_limited" class="childfieldsrow rm-format-options">'));    
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield"><label><b>'.__('Select Countries', 'custom-registration-form-builder-with-submission-manager').'</b></label></div><div class="rminput"><div id="lim_countries_count">'.$lim_countries_count.' '.__('Selected', 'custom-registration-form-builder-with-submission-manager').' </div><a href="#rm-country-selector" onclick="openModal(this,\'lim_countries\')">'.__('Select Countries', 'custom-registration-form-builder-with-submission-manager').'</a></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.__('Select the country codes you wish to display in mobile number field.', 'custom-registration-form-builder-with-submission-manager').'</div></div></div>'));
        $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield"><label><b>'.__('Preferred Countries', 'custom-registration-form-builder-with-submission-manager').'</b></label></div><div class="rminput"><div id="lim_pref_countries_count">'.$lim_pref_countries_count.' '.__('Selected', 'custom-registration-form-builder-with-submission-manager').' </div><a href="#rm-country-selector" onclick="openModal(this,\'lim_pref_countries\')">'.__('Preferred Countries', 'custom-registration-form-builder-with-submission-manager').'</a></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">'.__('Prominently display specific countries on top.', 'custom-registration-form-builder-with-submission-manager').'</div></div></div>'));
         
    $form->addElement(new Element_HTML('</div>'));
     
    $form->addElement(new Element_Textarea("<b>".__('Invalid Error', 'custom-registration-form-builder-with-submission-manager')."</b>", "mobile_err_msg", array("class" => "rm_static_field ", "value" => isset($data->model->field_options->mobile_err_msg) ? $data->model->field_options->mobile_err_msg : __('This does not appears to be a valid mobile number.', 'custom-registration-form-builder-with-submission-manager'), "longDesc"=>__('Enter the contents of the the error that user seems when he/ she tries to submit the form with invalid mobile number format.', 'custom-registration-form-builder-with-submission-manager'))));
    $form->addElement(new Element_Hidden("preferred_countries", $data->model->field_options->preferred_countries,array('id'=>'preferred_countries')));
    $form->addElement(new Element_Hidden("lim_countries", $data->model->field_options->lim_countries,array('id'=>'lim_countries')));
    $form->addElement(new Element_Hidden("lim_pref_countries", $data->model->field_options->lim_pref_countries,array('id'=>'lim_pref_countries')));
?>

<div id="rm-country-selector" class="rm-modal-view" style="display:none">
        <div class="rm-modal-overlay"></div> 

        <div class="rm-modal-wrap">
            <div class="rm-modal-titlebar">
                <div class="rm-modal-title"> <?php _e('Choose country', 'custom-registration-form-builder-with-submission-manager') ?></div>
                <span  class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
                <div class="rmrow">
                    <div class="rm-field-selector">
                        <div class="rm_country_selector_wrap">
                        <?php  
                            $countries= RM_Utilities::get_countries();
                            $dial_codes= RM_Utilities::get_country_dial_codes();
                            foreach($countries as $key=>$country):
                                if(empty($key)) continue;
                            if(strtolower('antarctica')==strtolower($country))
                                continue;
                            $flag= RM_Utilities::get_country_code($key);  
                            $flag_src= RM_IMG_URL.'flag/16/'.$flag.'.png';
                            $dc= strtoupper($flag);
                            $dial_code= isset($dial_codes[$dc]) ? $dial_codes[$dc]: '';
                            $checked= '';
                            if(!empty($data->model->field_options->preferred_countries)){
                                $pc= explode(',', $data->model->field_options->preferred_countries);
                                $checked= in_array($key,$pc) ? 'checked' : '';
                            }
                        ?>     
                        <div class="rm_country_selector">
                            <div class="rm_country_lf">
                                <span class="rm-country-list"><input type="checkbox" <?php echo $checked; ?> value="<?php echo $key; ?>" name="country_list[]" class="country_list" /></span>
                                <span class="rm-country-flag"><img class="rm_country_flag" src="<?php echo $flag_src; ?>" /></span>
                            </div>
                                <div class="rm_country_rf"><span class="rm-country-name" ><?php echo $country; ?></span>
                            <?php if(!empty($dial_code)) : ?><span class="rm-country-code"><?php echo $dial_code; ?></span><?php endif; ?>
                            </div> 
                        </div>
                        <?php    endforeach;
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<script>
    var current_selected_country;
    jQuery(document).ready(function(){
        jQuery('.rm-modal-close, .rm-modal-overlay').click(function () {
            jQuery(this).parents('.rm-modal-view').hide();
        });
        
        jQuery(".rm-mobile-formats").change(function(){
           var selected_value= jQuery(this).val();
           var child= "#child_" + selected_value;
           if(jQuery(this).is(':checked')){
               jQuery(".rm-format-options").not(child).slideUp();
               jQuery(child).slideDown();
           } 
          
        });
        
        jQuery(".rm_sync_country").change(function(){
          if(jQuery(this).is(':checked')){
              jQuery('#sync_country_options').slideDown();
              return;
          }
           jQuery('#sync_country_options').slideUp();
        });
        
        jQuery(".rm-mobile-formats,.rm_sync_country").trigger('change');
        
        jQuery('.country_list').change(function(){
            var selected_countries= [];
            jQuery('.country_list:checked').each(function(){
                    selected_countries.push(jQuery(this).val());
            });
            jQuery("#" + current_selected_country).val(selected_countries.toString());
            jQuery("#" + current_selected_country + "_count").html(selected_countries.length + " Selected");
        });
        
    });
    
    function openModal(ele,el_id) {
        current_selected_country= el_id;
        jQuery('.country_list').prop('checked',false);
        var selected_values= jQuery("#" + current_selected_country).val();
        if(selected_values){
            selected_values= selected_values.split(',');
            for(var i=0;i<selected_values.length;i++){
                jQuery('.country_list[value="'+ selected_values[i] +'"]').prop('checked',true);
            }
        }

        jQuery(jQuery(ele).attr('href')).toggle();
    }
    
</script>    