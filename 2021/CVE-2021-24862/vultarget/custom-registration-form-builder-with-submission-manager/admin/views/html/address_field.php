<?php
if (!defined('WPINC')) {
    die('Closed');
}

        $city_label= empty($data->model->field_options->field_ca_city_label) ? __('City', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_city_label;
        $state_label= empty($data->model->field_options->field_ca_state_label) ? __('State or Region', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_state_label;
        $zip_label= empty($data->model->field_options->field_ca_zip_label) ? __('Zip', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_zip_label;
        $country_label= empty($data->model->field_options->field_ca_country_label) ? __('Country', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_country_label;
        $address1_label= empty($data->model->field_options->field_ca_address1_label) ? __('Address Line 1', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_address1_label;
        $lmark_label= empty($data->model->field_options->field_ca_lmark_label) ? __('Landmark', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_lmark_label;
        $address2_label= empty($data->model->field_options->field_ca_address2_label) ? __('Address Line 2', 'custom-registration-form-builder-with-submission-manager') : $data->model->field_options->field_ca_address2_label;
        
        
        $city_en= isset($data->model->field_options->field_ca_city_en) ? $data->model->field_options->field_ca_city_en : 1;
        $state_en= isset($data->model->field_options->field_ca_state_en) ? $data->model->field_options->field_ca_state_en : 1;
        $zip_en= isset($data->model->field_options->field_ca_zip_en) ? $data->model->field_options->field_ca_zip_en : 1;
        $country_en= isset($data->model->field_options->field_ca_country_en) ? $data->model->field_options->field_ca_country_en : 1;
        $address1_en= isset($data->model->field_options->field_ca_address1_en) ? $data->model->field_options->field_ca_address1_en : 1;
        $lmark_en= isset($data->model->field_options->field_ca_lmark_en) ? $data->model->field_options->field_ca_lmark_en : 0;
        $address2_en= isset($data->model->field_options->field_ca_address2_en) ? $data->model->field_options->field_ca_address2_en : 1;
        
        $address_type= empty($data->model->field_options->field_address_type) ? "ca" : $data->model->field_options->field_address_type; 
        $ca_state_type= isset($data->model->field_options->ca_state_type) ? $data->model->field_options->ca_state_type : 'all'; 
        
        
        $address1_req= isset($data->model->field_options->field_ca_address1_req) ? $data->model->field_options->field_ca_address1_req : 0;
        $lmark_req= isset($data->model->field_options->field_ca_lmark_req) ? $data->model->field_options->field_ca_lmark_req : 0;
        $address2_req= isset($data->model->field_options->field_ca_address2_req) ? $data->model->field_options->field_ca_address2_req : 0;
        $state_req= isset($data->model->field_options->field_ca_state_req) ? $data->model->field_options->field_ca_state_req : 0;
        $country_req = isset($data->model->field_options->field_ca_country_req) ? $data->model->field_options->field_ca_country_req : 0;
        $zip_req = isset($data->model->field_options->field_ca_zip_req) ? $data->model->field_options->field_ca_zip_req : 0;
        $city_req = isset($data->model->field_options->field_ca_city_req) ? $data->model->field_options->field_ca_city_req : 0;
        $label_as_placeholder= isset($data->model->field_options->field_ca_label_as_placeholder) ? $data->model->field_options->field_ca_label_as_placeholder : 0;
        
        $united_state_options= '<option>'.__('Select State or Region', 'custom-registration-form-builder-with-submission-manager').'</option>';
        $united_states= RM_Utilities::get_usa_states();
        foreach($united_states as $key=>$state){
            $united_state_options .= "<option>$state</option>";
        }
        
        $canadian_options= '<option>'.__('Select State or Region', 'custom-registration-form-builder-with-submission-manager').'</option>';
        $canadian_provinces= RM_Utilities::get_canadian_provinces();
        foreach($canadian_provinces as $key=>$province){
            $canadian_options .= "<option>$province</option>";
        }
        
        $countries= RM_Utilities::get_countries();
        $country_options='';
        foreach($countries as $country){
            $country_options .= "<option>$country</option>";
        }
        $state_codes='';
        $state_codes_enabled = isset($data->model->field_options->field_ca_state_codes) ? $data->model->field_options->field_ca_state_codes : '';
        $ca_country_america_can= $data->model->field_options->field_ca_country_america_can;
        $ca_field_ca_country_limited= empty($data->model->field_options->field_ca_country_limited) ? '' : $data->model->field_options->field_ca_country_limited;
        $country_search_enabled= isset($data->model->field_options->field_ca_en_country_search) ? $data->model->field_options->field_ca_en_country_search : 0;
        
        $hide_search_opt= ($ca_state_type!="all" && $ca_state_type!="limited") ? 'style="display:none"':'';
        
        $form->addElement(new Element_HTML('
        <div id="rm-address-field" >
            <div class="rmrow">
                <div class="rmfield" for="rm_field_is_required_range">
                    <label><b>'.__('Address Field Type', 'custom-registration-form-builder-with-submission-manager').'</b></label>
                </div>
                <div class="rminput">
                    <ul class="rmradio" style="list-style:none;">
                        <li>
                            <input id="rm_field_is_ca" type="radio" name="field_address_type" class="rm_field_is_ca" onchange="rm_address_type_changed(this.value)" value="ca">
                            <label for="rm_field_is_ca"><span>'.__('Regular', 'custom-registration-form-builder-with-submission-manager').'</span></label>
                        </li>
                        <li>
                            <input id="rm_field_is_ga" type="radio" name="field_address_type" class="field_is_ga" value="ga" onchange="rm_address_type_changed(this.value)">
                            <label for="rm_field_is_ga" class="rm-google-field"><span class="rm-g-field-label">'.__('Autocomplete Powered by', 'custom-registration-form-builder-with-submission-manager').'</span><span class="rm-g-field-img"><img src="'.RM_BASE_URL . "images/rm-google-map-logo.png".'" /></span></label>
                        </li>
                        
                    </ul>
                </div>
                <div class="rmnote rm-addresstype-note" id="note-ca">
                    <div class="rmprenote"></div>
                    <div class="rmnotecontent">'.__('This will render a normal address section with a group of input boxes for filling in various address details.', 'custom-registration-form-builder-with-submission-manager').'</div>
                </div>
                
                <div class="rmnote rm-addresstype-note" id="note-ga">
                    <div class="rmprenote"></div>
                    <div class="rmnotecontent">'.__('This will allow users to type in their address in a search box and their address will be fetched automatically using Google Maps and entered into appropriate address input boxes.', 'custom-registration-form-builder-with-submission-manager').'</div>
                </div>
                
            </div>
            <div class="childfieldsrow rm_ca_field" id="rm_field_is_required_range_childfieldsrow">
                <div class="rmrow">

                    <div class="rm-address-field-row">
                        <div class="rm-address-field" id="rm-address-field-address">
                            <input type="text" name="rm_field_ca_address1" id="rm_field_ca_address1" class="" value="">
                            <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_address1_label\')">'.$address1_label.'</label>
                            

                            <div class="rm-ca-actions">
                                <span onclick="ca_field_visibility(\'address1\',this)"  class="rm-address-field-visibility"></span>
                                <span id="rm_field_ca_address1_req"><input type="checkbox" name="field_ca_address1_req" value="1" '.rm_checkbox_state($address1_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                            
                        </div>
                    </div>
                    
                   <div class="rm-address-field-row">
                     <div class="rm-address-field" id="rm-address-field-address2">
                     <input type="text" name="rm_field_ca_address2" id="rm_field_ca_address2" class="" value="">
                     <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_address2_label\')">'.$address2_label.'</label>
                           <div class="rm-ca-actions">
                                <span onclick="ca_field_visibility(\'address2\',this)"  class="rm-address-field-visibility"></span>
                                <span id="rm_field_ca_address2_req"><input type="checkbox" name="field_ca_address2_req" value="1" '.rm_checkbox_state($address2_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                     </div>
                   </div>
                   
                   <div class="rm-address-field-row">
                     <div class="rm-address-field" id="rm-address-field-address2">
                     <input type="text" name="rm_field_ca_lmark" id="rm_field_ca_lmark" class="" value="">
                     <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_lmark_label\')">'.$lmark_label.'</label>
                           <div class="rm-ca-actions">
                                <span onclick="ca_field_visibility(\'lmark\',this)"  class="rm-address-field-visibility"></span>
                                <span id="rm_field_ca_lmark_req"><input type="checkbox" name="field_ca_lmark_req" value="1" '.rm_checkbox_state($lmark_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                     </div>
                   </div>


                    <div class="rm-address-field-row rm-address-field-col2" >
                        <div class="rm-address-field" id="rm-address-field-city">
                            <input type="text" name="rm_field_ca_city" id="rm_field_ca_city" class="" value="">
                            <div class="rm-ca-actions">
                                <span  onclick="ca_field_visibility(\'city\',this)" class="rm-address-field-visibility"></span>
                                <span id="rm_field_ca_city_req"><input type="checkbox" name="field_ca_city_req" value="1" '.rm_checkbox_state($city_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                            <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_city_label\')">'.$city_label.'</label>
                        </div>  
                        <div class="rm-address-field" id="rm-address-field-states">
                            
                            <div id="rm_field_ca_state_all" style="display:none" class="rmstates">
                                <input type="text" value="" class="input-ca-state" id="rm_field_ca_state_all">
                                <div class="rm-ca-actions">
                                    <span  onclick="ca_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                                    <span id="rm_field_ca_state_req"><input type="checkbox" name="field_ca_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                                </div>
                            </div>    
                            
                            <div id="rm_field_ca_state_america" style="display:none" class="rmstates">
                                <select class="input-ca-state" id="input_ca_state_america">'.$united_state_options.'</select>
                                <div class="rm-ca-actions">
                                    <span  onclick="ca_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                                    <span id="rm_field_ca_state_req"><input type="checkbox" name="field_ca_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                                </div>    
                            </div>
                            
                            <div id="rm_field_ca_state_america_can" style="display:none" class="rmstates">    
                                 <select class="input-ca-state" id="input_america_states">'.$united_state_options.'</select>    
                                 <select class="input-ca-state" id="input_can_states">'.$canadian_options.'</select> 
                                <div class="rm-ca-actions">
                                    <span  onclick="ca_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                                    <span id="rm_field_ca_state_req"><input type="checkbox" name="field_ca_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                                </div>    
                            </div>
                            
                            <div id="rm_field_ca_state_limited" style="display:none" class="rmstates">  
                                <input class="input-ca-state" type="text" value="" id="input_ca_state_limited">  
                                <div class="rm-ca-actions">
                                    <span  onclick="ca_field_visibility(\'state\',this)" class="rm-address-field-visibility"></span>
                                    <span id="rm_field_ca_state_req"><input type="checkbox" name="field_ca_state_req" value="1" '.rm_checkbox_state($state_req).'></span>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'
                                </div>
                            </div>
                            
                            
                            <span id="rm_field_ca_state_as_codes" style="display:none"><input name="field_ca_state_codes"   value="1" type="checkbox" '.$state_codes.'/>'.__('Show as codes', 'custom-registration-form-builder-with-submission-manager').'</span>
                            <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_state_label\')">'.$state_label.'</label>
                        </div>


                    </div>

                    <div class="rm-address-field-row rm-address-field-col2">
                        <div class="rm-address-field" id="rm-address-field-country">
                        
                            <select style="display:none" class="form-dropdown ca_country" id="rm_field_ca_country_all">'.$country_options.'</select>
                            <select style="display:none" class="form-dropdown ca_country" id="rm_field_ca_country_america" data-component="country" tabindex="-1" disabled=""><option>'.__('United States', 'custom-registration-form-builder-with-submission-manager').'</option></select>
                            <select style="display:none" class="form-dropdown ca_country" id="rm_field_ca_country_america_can" name="field_ca_country_america_can" data-component="country" tabindex="-1"><option value="america">'.__('United States', 'custom-registration-form-builder-with-submission-manager').'</option><option value="america_can">'.__('Canada', 'custom-registration-form-builder-with-submission-manager').'</option></select>
                            <textarea style="display:none" id="rm_field_ca_country_limited" name="field_ca_country_limited" class="ca_country">'.$ca_field_ca_country_limited.'</textarea>
                            
                            <div class="rm-ca-actions">
                                <span  onclick="ca_field_visibility(\'country\',this)" class="rm-address-field-visibility"></span>
                                <span '.$hide_search_opt.' id="field_ca_en_country_search"><input type="checkbox"  name="field_ca_en_country_search" value="1" '.rm_checkbox_state($country_search_enabled).'>'.__('Enable Search', 'custom-registration-form-builder-with-submission-manager').'</span>    
                                <span id="rm_field_ca_country_req"><input type="checkbox" name="field_ca_country_req" value="1" '.rm_checkbox_state($country_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                            
                            <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_country_label\')">'.$country_label.'</label>
                            <div id="rm_country_note" style="display:none" class="rm-country-note rm-dbfl">'.RM_UI_Strings::get('HELP_REG_ADD_ALL_COUNTRY').'</div>    
                        </div>
                        
                        <div class="rm-address-field" id="rm-address-field-zip">
                            <input type="text" name="rrm_field_ca_zip" id="rm_field_ca_zip" class="" value="">
                            <div class="rm-ca-actions">
                                <span onclick="ca_field_visibility(\'zip\',this)" class="rm-address-field-visibility"></span>
                                <span id="rm_field_ca_zip_req"><input type="checkbox" name="field_ca_zip_req" value="1" '.rm_checkbox_state($zip_req).'>'.__('Required', 'custom-registration-form-builder-with-submission-manager').'</span>
                            </div>
                            <label contenteditable="true" spellcheck="false" onkeyup="rm_custom_address_label_changed(this,\'rm_field_ca_zip_label\')">'.$zip_label.'</label>
                        </div> 

                       
                    </div>


                    <div class="rm-address-field-row">

                        <div class="rm-address-field-state">
                            <ul>
                                <li>  
                                    <input id="rm_ca_state_all" type="radio" name="ca_state_type" value="all"> 
                                    <label for="rm-addr-textbox-state" onclick="rm_state_type_changed(\'all\')">'.__('All Countries', 'custom-registration-form-builder-with-submission-manager').'</label>
                                
                                </li>
                                <li>
                                          <input id="rm_ca_state_america" type="radio" name="ca_state_type" value="america">    
                                          <label for="rm-addr-select-state" onclick="rm_state_type_changed(\'america\')">'.__('U.S. States', 'custom-registration-form-builder-with-submission-manager').'</label>    
                                </li>
                                <li>
                                          <input id="rm_ca_state_america_can" type="radio" name="ca_state_type" value="america_can">    
                                          <label for="rm-addr-select-state" onclick="rm_state_type_changed(\'america_can\')">'.__('U.S. States & Canadian Provinces', 'custom-registration-form-builder-with-submission-manager').'</label>    
                                </li>
                                <li>
                                          <input id="rm_ca_state_limited" type="radio" name="ca_state_type" value="limited">    
                                          <label for="rm-addr-select-state" onclick="rm_state_type_changed(\'limited\')">'.__('Limited Countries', 'custom-registration-form-builder-with-submission-manager').'</label>    
                                </li>


                            </ul>
                        </div>

                    </div>
                    
                <div class="rm-address-field-row">
                    <div class="rm-difl">
                       <span> Use labels as placeholders</span>
                        <input type="checkbox" name="field_ca_label_as_placeholder" value="1" '.rm_checkbox_state($label_as_placeholder).'/>
                    </div>
                    
                     <div class="rm-address-field-legand rm-difl">
                       <span class="rm-difl"> Click to hide field </span> <span class="rm-difl"><i class="material-icons">&#xE417;</i></span>
                       
                    </div>


                </div>


            </div> 
            <input type="hidden" name="field_ca_city_label" id="rm_field_ca_city_label" value="'.$city_label.'" />
            <input type="hidden" name="field_ca_address1_label" id="rm_field_ca_address1_label" value="'.$address1_label.'"/>
            <input type="hidden" name="field_ca_address2_label" id="rm_field_ca_address2_label" value="'.$address2_label.'"/>
            <input type="hidden" name="field_ca_lmark_label" id="rm_field_ca_lmark_label" value="'.$lmark_label.'"/>
            <input type="hidden" name="field_ca_state_label" id="rm_field_ca_state_label" value="'.$state_label.'"/>
            <input type="hidden" name="field_ca_zip_label" id="rm_field_ca_zip_label" value="'.$zip_label.'"/>
            <input type="hidden" name="field_ca_country_label" id="rm_field_ca_country_label" value="'.$country_label.'"/>
                
            <input class="ca_en_field" type="hidden" name="field_ca_city_en" id="rm_field_ca_city_en" value="'.$city_en.'" />
            <input class="ca_en_field" type="hidden" name="field_ca_address1_en" id="rm_field_ca_address1_en" value="'.$address1_en.'"/>
            <input class="ca_en_field" type="hidden" name="field_ca_lmark_en" id="rm_field_ca_lmark_en" value="'.$lmark_en.'"/>
            <input class="ca_en_field" type="hidden" name="field_ca_address2_en" id="rm_field_ca_address2_en" value="'.$address2_en.'"/>
            <input class="ca_en_field" type="hidden" name="field_ca_state_en" id="rm_field_ca_state_en" value="'.$state_en.'"/>
            <input class="ca_en_field" type="hidden" name="field_ca_zip_en" id="rm_field_ca_zip_en" value="'.$zip_en.'"/>
            <input class="ca_en_field" type="hidden" name="field_ca_country_en" id="rm_field_ca_country_en" value="'.$country_en.'"/>    
</div>'));

?>

<script>
    // Intitializes field settings on page load
    jQuery(document).ready(function(){
        
       var address_type= "<?php echo $address_type; ?>";  
       if(address_type=="ca")
           jQuery("#rm_field_is_ca").attr('checked', 'checked');
       else 
           jQuery("#rm_field_is_ga").attr('checked', 'checked');

       rm_address_type_changed(address_type); 
       
       
       var ca_state_type= "<?php echo $ca_state_type; ?>";
       jQuery("#rm_ca_state_" + ca_state_type).attr('checked',true);
       rm_show_state_country_fields(ca_state_type);
       
       jQuery("#rm_field_ca_country_america_can").change(function(){
           america_can_country_changed();
       });
       
       if("<?php echo $state_codes_enabled; ?>"==1){
           jQuery("#rm_field_ca_state_as_codes input").attr('checked',true);
       }
       
       if(jQuery("#rm_ca_state_america_can").is(":checked")){
           jQuery("#rm_field_ca_country_america_can").val("<?php echo $ca_country_america_can; ?>");
           america_can_country_changed();
       }
       
       jQuery(".ca_en_field").each(function(){
           var id1= jQuery(this).attr('id');
           
           var id2= id1.replace("_en","");
           
           if(jQuery("#" + id1).val()!="1"){
              jQuery("#" + id2).addClass('rm-disable');
              if(id2=="rm_field_ca_state"){
                  jQuery("[id=rm_field_ca_state_req]:not(:hidden)").addClass('rm-disable');
                  //jQuery(".rmstates input").addClass('rm-disable');
                  
              }
              else if(id2=="rm_field_ca_country"){
                  jQuery("[id=field_ca_en_country_search]:not(:hidden)").addClass('rm-disable'); 
              }

              jQuery("#" + id2 + "_req").addClass('rm-disable');
              jQuery("#" + id2).siblings('.rm-ca-actions').addClass('rm-field-visibility');
          }
          
       });
       
       // Check for states visibility
       if(jQuery("#rm_field_ca_state_en").val()!="1"){
           jQuery(".input-ca-state").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).addClass('rm-disable');
                    jQuery(this).siblings('.rm-ca-actions').addClass('rm-field-visibility');
                }
            }); 
       }
       
       // Check for country visbility
       if(jQuery("#rm_field_ca_country_en").val()!="1"){
            jQuery(".ca_country").each(function(){ 
                     if(!jQuery(this).is(":hidden")){ 
                         jQuery(this).addClass('rm-disable');
                         jQuery(this).siblings('.rm-ca-actions').addClass('rm-field-visibility');
                     }
            });
        }
        
       if(jQuery("#rm_ca_state_limited").is(":checked")){
           jQuery("#rm_country_note").show();
       }
       
       
    });
    
    // Update labels inside hidden fields
    function rm_custom_address_label_changed(obj,target){
        jQuery("#" + target).val(obj.innerHTML);
    }
    
    // Show/Hide address options 
    function rm_address_type_changed(type){
        jQuery(".rm-addresstype-note").hide();
        if(type=="ga"){
            jQuery(".rm_ca_field").hide();
            jQuery("#rm_no_api_notice").show();
            jQuery("#rm_field_is_required-0").attr("disabled",false);
            
        } else{
            jQuery(".rm_ca_field").show();
            jQuery("#rm_no_api_notice").hide();
            jQuery("#rm_field_is_required-0").attr("disabled",true);
            
        }
        jQuery("#note-" + type).show();
    }
    
    // Toggle individual field visibility 
    function ca_field_visibility(field_type,obj){
        var enable_field;
        var req_div= jQuery("[name=field_ca_" + field_type + "_req]").closest("span");
            
        if(field_type=="state"){
            jQuery(".input-ca-state").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).toggleClass('rm-disable');
                }
            }); 

            enable_field= jQuery("#rm_field_ca_state_en");
            if(enable_field.val()==1)
                enable_field.val(0);
            else
                enable_field.val(1);
            req_div.toggleClass('rm-disable');
        }
        else if(field_type=="country")
        {
            jQuery(".ca_country").each(function(){
                if(!jQuery(this).is(":hidden")){
                    jQuery(this).toggleClass('rm-disable');
                }
            });
            
            enable_field= jQuery("#rm_field_ca_country_en");
            if(enable_field.val()==1)
                enable_field.val(0);
            else
                enable_field.val(1);
            var country_search= jQuery("#field_ca_en_country_search")
            req_div.toggleClass('rm-disable');
            country_search.toggleClass('rm-disable');
        }
        else
        {  
            enable_field= jQuery("#rm_field_ca_" + field_type + "_en");
            var field= jQuery("#rm_field_ca_" + field_type );
            
            
            if(field.hasClass("rm-disable"))
                enable_field.val(1);
            else
                enable_field.val(0);
            
            field.toggleClass('rm-disable');
            req_div.toggleClass('rm-disable');
        }
                    
        jQuery(obj).closest(".rm-ca-actions").toggleClass('rm-field-visibility');
    }
    
    
    // Show/Hide United States/Canada states
    function america_can_country_changed(){
           var selected_val= jQuery("#rm_ca_state_america_can").val();
           jQuery(".rmstates").hide();
           jQuery("#rm_field_ca_state_" + selected_val).show();
           rm_show_state_code_options();
           rm_show_state_by_country();
    }
    
    function rm_show_state_country_fields(type){
        jQuery(".rmstates").hide();
        jQuery("#rm_field_ca_state_" + type).show();
        
        jQuery(".ca_country").hide();
        jQuery("#rm_field_ca_country_" + type).show();  
        rm_show_state_code_options();
        if(type=="all"){
            jQuery("#field_ca_en_country_search").hide();
        }
    }
    
    function rm_state_type_changed(type){
        jQuery("#rm_ca_state_" + type).attr('checked',true);
        rm_show_state_country_fields(type);
       
        if(type=="america_can"){
             jQuery(".rmstates").hide();
             rm_show_state_code_options();
             rm_show_state_by_country();
        }
        
        if(type=="limited"){
            jQuery("#rm_country_note").show();
            jQuery("#field_ca_en_country_search").show();
        } else {
            jQuery("#rm_country_note").hide();
            jQuery("#field_ca_en_country_search").hide();
        }
    }
    
    function rm_show_state_by_country(){
        var country= jQuery("#rm_field_ca_country_america_can").val();
        jQuery("#rm_field_ca_state_america_can").show();
        if(country=="america")
        {   
            jQuery("#input_america_states").show();
            jQuery("#input_can_states").hide();
        }
        else
        {
            jQuery("#input_america_states").hide();
            jQuery("#input_can_states").show();
        }  
    }
    
    function rm_show_state_code_options(){
       var  america_can= jQuery("#rm_ca_state_america_can");
       var america= jQuery("#rm_ca_state_america");
       var show_codes_cb= jQuery("#rm_field_ca_state_as_codes");
       
       if(america_can.is(":checked") || america.is(":checked"))
       {
            show_codes_cb.show();
       } 
       else
       {
           show_codes_cb.hide();
       }
       
    }
       
</script>

<?php 
    function rm_checkbox_state($value){
        return $value ? 'checked': '';
    }
?>