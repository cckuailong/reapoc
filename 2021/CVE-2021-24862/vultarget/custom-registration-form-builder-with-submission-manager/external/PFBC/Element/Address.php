<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Map
 *
 * @author CMSHelplive
 */
class Element_Address extends Element
{

    public $default_add = array (
                            'rm_field_type' => '',
                            'original' => '',
                            'st_number' => '',
                            'st_route' => '',
                            'city' => '',
                            'state' => '',
                            'zip' => '',
                            'country' => ''
                          );
    public $_attributes = array();
    public $jQueryOptions = "";
    public $properties= array();
    public $api_key;
    

    public function getCSSFiles()
    {
       if(isset($this->_attributes['country_search_enabled']) && $this->_attributes['country_search_enabled']){
           return array(
                'style_rm_select2' => RM_BASE_URL . 'public/css/style_rm_select2.css',
           );
       } 
    }  

    public function __construct($label, $name, $api_key, array $properties = null)
    {
       
        parent::__construct($label, $name, $properties);
        $this->api_key = $api_key;
        $this->_attributes['id'] = 'autocomplete'.$name;
    }
    
    public function show_advance_search($field_id){
        if(isset($this->_attributes['country_search_enabled']) && $this->_attributes['country_search_enabled']){
           echo '<script>
                jQuery(document).ready(function() {
                        jQuery("#'.$field_id.'").select2();
                });
            </script>'; 
         }
    }

    public function getJSFiles()
    {
         if($this->_attributes['address_type']=="ca"){
            $scripts= array('script_rm_address' => RM_BASE_URL . 'public/js/script_rm_address.js');
            if(isset($this->_attributes['country_search_enabled']) && $this->_attributes['country_search_enabled'])
                    $scripts['script_rm_select2']=RM_BASE_URL."public/js/script_rm_select2.js";
            return $scripts;
         }
        
        return array(
            'script_rm_address' => RM_BASE_URL . 'public/js/script_rm_address.js',
            'google_map_api' => "https://maps.googleapis.com/maps/api/js?key=" . $this->api_key . "&libraries=places&callback=rmInitGoogleApi",
        );
    }

    public function getJSDeps()
    {
        return array(
            'script_rm_address',
            'script_rm_select2'
        );
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
    }

    public function render()
    {   @ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
        $name = $this->_attributes['name'];
        $value = wp_parse_args(maybe_unserialize($this->getAttribute('value')), $this->default_add);
        if($this->isRequired())
            $required = 'required';
        else
            $required = '';
 
        if(isset($this->_attributes['style'])){
            $style = "style='".$this->_attributes["style"]."'";
           unset($this->_attributes["style"]);
        }
        else
            $style = '';
        
        if($this->_attributes['address_type']!="ca") : ?>
            
            
            <div id="locationField">
                <input type="hidden" name="<?php echo $name; ?>[rm_field_type]" value="Address">
                <input type="text" id="<?php echo $name; ?>" placeholder="<?php echo RM_UI_Strings::get("LABEL_GMAP_ADDRESS"); ?>" class="rmgoogleautocompleteapi" onFocus="(new rmAutocomplete('<?php echo $name; ?>')).geolocate()" onkeydown="rm_prevent_submission(event)" <?php echo $style; ?> type="text" <?php echo $required; ?> name="<?php echo $name; ?>[original]" value="<?php echo $value['original']; ?>"></input>
                <span><?php echo RM_UI_Strings::get("LABEL_POWERED_GMAP"); ?></span>
            </div>

            <div id="address" class="rm_address_type_<?php echo $this->_attributes['address_type']; ?>">
                <div class="rm_ad_container">       
                        <div class="slimField rm-address-fw">
                            <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_street_number"
                                   name="<?php echo $name; ?>[st_number]" value="<?php echo $value['st_number']; ?>"></input>
                            <div class="label"><?php echo $this->_attributes['street_no_label']; ?></div>
                        </div>

                        <div class="wideField rm-semi-field rm-address-fw" colspan="2">
                            <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_route"
                                   name="<?php echo $name; ?>[st_route]" value="<?php echo $value['st_route']; ?>"></input>
                            <div class="label"><?php echo $this->_attributes['street_label']; ?></div>
                        </div>
                </div>


                <div class="rm_ad_container">
                    <div class="wideField rm-alone rm-address-hw" colspan="3">
                        <input type="text" <?php echo $style; ?> id="<?php echo $name; ?>_locality" name="<?php echo $name; ?>[city]" value="<?php echo $value['city']; ?>"/>                                                       
                        <div class="label"><?php echo $this->_attributes['city_label']; ?></div>
                    </div>

                    <div class="slimField rm-address-hw">
                        <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_administrative_area_level_1"  name="<?php echo $name; ?>[state]" value="<?php echo $value['state']; ?>" />
                        <div class="label"><?php echo $this->_attributes['state_label']; ?></div>
                    </div>
                </div>


                <div class="rm_ad_container">

                   

                    <div class="wideField rm-alone rm-address-hw" colspan="3">
                        <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_country"   name="<?php echo $name; ?>[country]" value="<?php echo $value['country']; ?>"></input>
                        <div class="label"><?php echo $this->_attributes['country_label']; ?></div>
                    </div>
                    
                     <div class="wideField rm-semi-field-with-label rm-address-hw">
                        <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_postal_code" name="<?php echo $name; ?>[zip]" value="<?php echo $value['zip']; ?>"></input> 
                        <div class="label label-short"><?php echo $this->_attributes['zip_label']; ?></div>
                    </div>

                </div>
        </div> 

        <?php else : ?>

        <div id="address" class="rm_address_type_<?php echo $this->_attributes['address_type']; ?>">
            <div class="rm_ad_container">       
                        <?php if ($this->_attributes['address1_en']) : ?>
                            <div class="slimField rm-address-fw">
                                <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_address1"
                                       placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['address1_label'] : '' ?>"
                                       <?php echo $this->_attributes['address1_req'] ?>
                                       name="<?php echo $name; ?>[address1]" value="<?php echo $value['address1']; ?>"></input>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['address1_label']; ?>
                                    <?php if(!empty($this->_attributes['address1_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    
                                    </div>
                                <?php endif;  ?> 
                           
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($this->_attributes['address2_en']) : ?>
                            <div class="wideField rm-semi-field rm-address-fw" colspan="2">
                                <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_route"
                                       <?php echo $this->_attributes['address2_req'] ?>
                                       placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['address2_label'] : '' ?>"
                                       name="<?php echo $name; ?>[address2]" value="<?php echo $value['address2']; ?>"></input>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['address2_label']; ?>
                                    <?php if(!empty($this->_attributes['address2_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?>  
                                    
                                    </div>
                                <?php endif; ?>    
                            
                            </div>
                        <?php endif; ?>
            </div>
            
            <?php if ($this->_attributes['lmark_en']) : ?>
                <div class="rm_ad_container">
                        <div class="wideField rm-alone rm-address-fw" colspan="2">
                            <input type="text" <?php echo $style; ?> name="<?php echo $name; ?>[lmark]"
                                   <?php echo $this->_attributes['lmark_req'] ?>
                                   placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['lmark_label'] : '' ?>"
                                   value="<?php echo $value['lmark']; ?>"/>    
                            <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                <div class="label"><?php echo $this->_attributes['lmark_label']; ?>
                                <?php if(!empty($this->_attributes['lmark_req'])): ?>    
                                <sup class="address_req required">&nbsp;*</sup>
                            <?php endif; ?> 
                                
                                </div>
                            <?php endif; ?>
                       
                        </div>
                </div>
             <?php endif; ?>
            
            <div class="rm_ad_container">
                    <?php if ($this->_attributes['city_en']) : ?>
                        <div class="wideField rm-alone rm-address-hw" colspan="3">
                            <input type="text" <?php echo $style; ?> name="<?php echo $name; ?>[city]" 
                                   id="<?php echo $name; ?>_locality" name="<?php echo $name; ?>[city]"                      
                                   <?php echo $this->_attributes['city_req'] ?>
                                   placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['city_label'] : '' ?>"
                                   value="<?php echo $value['city']; ?>"/>    
                            <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                <div class="label"><?php echo $this->_attributes['city_label']; ?>
                                <?php if(!empty($this->_attributes['city_req'])): ?>    
                                <sup class="address_req required">&nbsp;*</sup>
                            <?php endif; ?>                                   
                                </div>
                            <?php endif; ?>
                         
                        </div>
                    <?php endif; ?>
                    
                    <?php  if ($this->_attributes['state_en']) : ?>
                        <?php if($this->_attributes['state_type']=='all'): ?>
                            <div class="slimField rm-address-hw">
                                <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_administrative_area_level_1"  
                                        <?php echo $this->_attributes['state_req'] ?>
                                        placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['state_label'] : '' ?>"
                                       name="<?php echo $name; ?>[state]" value="<?php echo $value['state']; ?>" />
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['state_label']; ?>
                                    <?php if(!empty($this->_attributes['state_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    </div>
                                <?php endif; ?>
                          
                            </div>
                        <?php endif; ?>
                    
                
                        <?php if($this->_attributes['state_type']=='america'): ?>
                            <div class="slimField rm-address-hw">
                                <select <?php echo $style; ?> class="field" <?php echo $this->_attributes['state_req'] ?>
                                        id="<?php echo $name; ?>_administrative_area_level_1"  
                                        name="<?php echo $name; ?>[state]" value="<?php echo $value['state']; ?>">
                                    <option value=""><?php echo $this->_attributes['state_label']; ?></option>
                                    <?php $usa_states= RM_Utilities::get_usa_states(); ?>
                                    <?php foreach($usa_states as $code=>$state): ?>
                                          <?php if($this->_attributes["state_as_code"]==1) : $state= $code; endif;?>
                                            <?php if($value['state']==$state) : ?>
                                                 <option selected><?php echo $state; ?></option>
                                            <?php else:?>     
                                                 <option><?php echo $state; ?></option>
                                            <?php endif; ?>     
                                    <?php endforeach; ?>   
                                </select>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['state_label']; ?>
                                            <?php if(!empty($this->_attributes['state_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    </div>
                                <?php endif; ?> 
                           
                            </div>
                        <?php endif; ?>

                        <?php if($this->_attributes['state_type']=='limited'): ?>
                            <div class="slimField rm-address-hw">
                                <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_administrative_area_level_1"  
                                        <?php echo $this->_attributes['state_req'] ?>
                                        placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['state_label'] : '' ?>"
                                       name="<?php echo $name; ?>[state]" value="<?php echo $value['state']; ?>" />
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['state_label']; ?>
                                    <?php if(!empty($this->_attributes['state_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?>  
                                    </div>
                                <?php endif; ?>
                           
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>    
                
                     <?php if($this->_attributes['state_type']=='america_can'): ?>
                            <div class="slimField rm-address-hw">
                                <select <?php echo $style; ?> class="field" <?php echo $this->_attributes['state_req'] ?>
                                        id="<?php echo $name; ?>_administrative_area_level_1"  
                                        name="<?php echo $name; ?>[state]" value="<?php echo $value['state']; ?>">
                                    <option value=""><?php echo $this->_attributes['state_label']; ?></option> 
                                </select>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['state_label']; ?>
                                    <?php if(!empty($this->_attributes['state_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    </div>
                                <?php endif; ?>
   
                            </div>
                        <?php endif; ?>
            </div>
            
            <div class="rm_ad_container">
                    <?php if ($this->_attributes['country_en']) : ?>
                        <?php if($this->_attributes['state_type']=='all'): ?>
                            <div  class="wideField rm-alone rm-address-hw" colspan="3">
                                <select <?php echo $style; ?> id="<?php echo $name; ?>_country" <?php echo $this->_attributes['country_req'] ?>
                                        name="<?php echo $name; ?>[country]">
                                    <?php $countries= RM_Utilities::get_countries(); ?>
                                    <option value=""><?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['country_label'] : '' ?></option>
                                    <?php foreach($countries as $index=>$country) : if(empty($index)) continue; ?>
                                            <?php if($value['country']==$country) : ?>
                                                 <option selected><?php echo $country; ?></option>
                                            <?php else:?>     
                                                 <option><?php echo $country; ?></option>
                                            <?php endif; ?> 
                                        
                                    <?php endforeach; ?>    
                                </select>
                                <?php $this->show_advance_search($name.'_country'); ?>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['country_label']; ?>
                                    <?php if(!empty($this->_attributes['country_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    </div>
                                <?php endif; ?>
                             
                            </div>
                        <?php endif; ?>        
                
                        <?php if($this->_attributes['state_type']=='america'): ?>
                            <div class="wideField rm-alone rm-address-hw" colspan="3">
                                <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_country"   
                                       <?php echo $this->_attributes['country_req'] ?>
                                       name="<?php echo $name; ?>[country]" value="United States" readonly></input>
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label"><?php echo $this->_attributes['country_label']; ?>
                                        <?php if(!empty($this->_attributes['country_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?> 
                                    </div>
                                <?php endif; ?>
                             
                            </div>
                        <?php endif; ?>    
                
                        <?php if($this->_attributes['state_type']=='america_can'):  ?>
                            <div class="wideField rm-alone rm-address-hw" colspan="3">
                                <select <?php echo $style; ?> onchange="rm_load_states(this.value,'<?php echo $name; ?>_administrative_area_level_1','<?php echo $this->_attributes["state_as_code"] ?>'); rm_validate_zipcode('<?php echo $name; ?>')" class="field" id="<?php echo $name; ?>_country" 
                                         name="<?php echo $name; ?>[country]">
                                    <option value="US" <?php echo $value['country']=="US" ? 'selected' :'' ?>>United States</option>
                                    <option value="Canada" <?php echo $value['country']=="Canada" ? 'selected' :'' ?>>Canada</option>
                                </select>
                                <?php //!empty($value['country']) && !empty($value['state']) ?>
                                <script>
                                    jQuery(document).ready(function(){
                                        var selected_country= jQuery("#<?php echo $name; ?>_country").val();
                                        rm_load_states(selected_country,'<?php echo $name; ?>_administrative_area_level_1','<?php echo $this->_attributes["state_as_code"] ?>',"<?php echo $value['state'] ?>");
                                    });
                                </script>
                            </div>
                        <?php endif; ?> 
                
                        <?php if($this->_attributes['state_type']=='limited'): ?>
                        <?php if(empty($this->_attributes['countries'])) : ?>
                                <div class="wideField rm-alone rm-address-hw" colspan="3">
                                    <input type="text" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_country"   
                                           <?php echo $this->_attributes['country_req'] ?>
                                           placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['country_label'] : '' ?>"
                                           name="<?php echo $name; ?>[country]" value="<?php echo $value['country']; ?>"></input>
                                    <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                        <div class="label"><?php echo $this->_attributes['country_label']; ?>
                                            <?php if(!empty($this->_attributes['country_req'])): ?>    
                                        <sup class="address_req required">&nbsp;*</sup>
                                    <?php endif; ?>   
                                        
                                        </div>
                                    <?php endif; ?>
                                 
                                </div>
                        <?php else: ?>
                              <div class="wideField rm-alone rm-address-hw" colspan="3">
                                <select <?php echo $style; ?> <?php echo $this->_attributes['country_req'] ?> class="field" id="<?php echo $name; ?>_country" 
                                         name="<?php echo $name; ?>[country]" value="United States">
                                    <option value=""><?php echo $this->_attributes['country_label']; ?></option>
                                    <?php foreach($this->_attributes['countries'] as $country) : ?>
                                            <option><?php echo $country; ?></option>
                                    <?php endforeach; ?>        
                                </select>
                                <?php $this->show_advance_search($name.'_country'); ?>
                                <?php if(!empty($this->_attributes['country_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?>  
                             </div>  
                        <?php endif; ?>
                      <?php endif; ?>  
                
                       
                        
                    <?php endif; ?> 
                    
                    <?php if ($this->_attributes['zip_en']) : ?>
                        <div class="wideField rm-semi-field-with-label rm-address-hw">
                                <input type="text" onchange="rm_validate_zipcode('<?php echo $name; ?>')" <?php echo $style; ?> class="field" id="<?php echo $name; ?>_postal_code" 
                                        <?php echo $this->_attributes['zip_req'] ?>
                                        placeholder="<?php echo!empty($this->_attributes['label_as_placeholder']) ? $this->_attributes['zip_label'] : '' ?>"
                                        name="<?php echo $name; ?>[zip]" value="<?php echo $value['zip']; ?>"></input> 
                                <?php if(empty($this->_attributes['label_as_placeholder'])) : ?>
                                    <div class="label label-short"><?php echo $this->_attributes['zip_label']; ?>
                                    
                                           <?php if(!empty($this->_attributes['zip_req'])): ?>    
                                    <sup class="address_req required">&nbsp;*</sup>
                                <?php endif; ?>   
                                    </div>
                                <?php endif; ?>
                          
                        </div>
                    <?php endif; ?> 
                    
            </div>
            
        </div>

        <?php endif; 
        //@ini_set('error_reporting', E_ALL);
    }

}
