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
class Element_WCAddress extends Element {

    public $_attributes = array();
    public $jQueryOptions = "";
    public $properties = array();
    public $field_type;

    public function __construct($label, $name, $field_type, array $properties = null) {
        parent::__construct($label, $name, $properties);
        $this->field_type = $field_type;
    }

    public function render() {
        $name = $this->_attributes['name'];
        $values = $this->_attributes['value'];
        $submission_id= isset($_REQUEST['submission_id']) ? absint($_REQUEST['submission_id']) : '';
        $user= wp_get_current_user();
        if(!empty($submission_id)){
            $submission= new RM_Submissions();
            $submission->load_from_db($submission_id);
            $u_email= $submission->get_user_email();
            $user= get_user_by('email',$u_email); 
        }
        global $woocommerce;
        global $post;
        $WC_Session = array();
        if(function_exists('WC') && WC()->session){
            $WC_Session = WC()->session->get( 'customer' );
        }
        
        if(!empty($post)){
            if($post->ID==get_option('woocommerce_myaccount_page_id') || $post->ID==get_option('woocommerce_checkout_page_id')){
                $this->_attributes['textfield_style'] = '';
            }
        }
        
        if ($this->field_type == "billing") {
            $field_name = 'wcbilling_' . $this->_attributes['field_id'];
            ?>
   <div class="rm-wcbilling">
       <div class="rm-wc-wrap">
            <?php if ($this->_attributes['field_wcb_firstname_en'] == 1):
                ?>
       
            
       
                <div class="rm-wc-hw">
                  
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['firstname']= get_user_meta($user->ID, 'billing_first_name', true);
                              } 
                        ?>
                        <input type="text" style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['firstname']) ? '' : $values['firstname']; ?>" name="<?php echo $field_name . '[firstname]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_firstname_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_firstname_req']) ? '' : 'required'; ?>>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_firstname_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcb_firstname_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>                    
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_lastname_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['lastname']= get_user_meta($user->ID, 'billing_last_name', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['lastname']) ? '' : $values['lastname']; ?>" name="<?php echo $field_name . '[lastname]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_lastname_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_lastname_req']) ? '' : 'required'; ?>>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_lastname_label']; ?>
                                <?php if (!empty($this->_attributes['field_wcb_lastname_req'])) : ?>
                                    <sup class="required">&nbsp;*</sup>
                                <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>                    
                </div>
       
       
            <?php endif; ?>
           </div>

            <?php if ($this->_attributes['field_wcb_company_en'] == 1):
                ?>
                <div class="rm-wc-wrap">
                    <div class="rm-wc-fw">
                    
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['company']= get_user_meta($user->ID, 'billing_company', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['company']) ? '' : $values['company']; ?>" name="<?php echo $field_name . '[company]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_company_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_company_req']) ? '' : 'required'; ?>>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_company_label']; ?>
                                <?php if (!empty($this->_attributes['field_wcb_company_req'])) : ?>
                                    <sup class="required">&nbsp;*</sup>
                                <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?> 
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_address1_en'] == 1):
                ?>
        <div class="rm-wc-wrap">
                <div class="rm-wc-fw">
                   
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['add1']= get_user_meta($user->ID, 'billing_address_1', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['add1']) ? '' : $values['add1']; ?>" name="<?php echo $field_name . '[add1]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_address1_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_address1_req']) ? '' : 'required'; ?>>
                        
                        <div class="rm-wc-label">
                           <?php echo $this->_attributes['field_wcb_address1_label']; ?>
                               <?php if (!empty($this->_attributes['field_wcb_address1_req'])) : ?>
                                   <sup class="required">&nbsp;*</sup>
                               <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>                    
                </div>
               </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_address2_en'] == 1):
                ?>
       <div class="rm-wc-wrap">
                <div class="rm-wc-fw">
                  
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['add2']= get_user_meta($user->ID, 'billing_address_2', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['add2']) ? '' : $values['add2']; ?>" type="text" name="<?php echo $field_name . '[add2]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_address2_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_address2_req']) ? '' : 'required'; ?>>
                    
                        <div class="rm-wc-label">
                          <?php echo $this->_attributes['field_wcb_address2_label']; ?>
                              <?php if (!empty($this->_attributes['field_wcb_address2_req'])) : ?>
                                  <sup class="required">&nbsp;*</sup>
                              <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_city_en'] == 1):
                ?>
       <div class="rm-wc-wrap">
           <div class="rm-wc-hw">
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['city']= get_user_meta($user->ID, 'billing_city', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['city']) ? '' : $values['city']; ?>" type="text" name="<?php echo $field_name . '[city]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_city_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_city_req']) ? '' : 'required'; ?> >
                        <div class="rm-wc-label">
                           <?php echo $this->_attributes['field_wcb_city_label']; ?>
                                <?php if (!empty($this->_attributes['field_wcb_city_req'])) : ?>
                                    <sup class="required">&nbsp;*</sup>
                                <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
           </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_state_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                     <?php if(!empty($user->ID)){
                         $values['state']= get_user_meta($user->ID, 'billing_state', true);
                     }
                     ?>
                    <div class="rm-wc-field">
                        <span id="<?php echo $field_name . '_state'; ?>"><input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" name="<?php echo $field_name . '[state]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_state_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_state_req']) ? '' : 'required'; ?> value="<?php echo empty($values['state']) ? '' : $values['state']; ?>"></span>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_state_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcb_state_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div data-style="<?php echo $this->_attributes['textfield_style']; ?>" id="<?php echo $field_name . '_state_attrs'; ?>" data-name="<?php echo $field_name . '[state]'; ?>" data-placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_state_label']; ?>" data-required="<?php echo empty($this->_attributes['field_wcb_state_req']) ? '' : 'required'; ?>" data-value="<?php echo empty($values['state']) ? '' : $values['state']; ?>" data-class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>"></div>
                    <script>
                    jQuery(document).ready(function () {
                        jQuery("[name='<?php echo $field_name . '[country]'; ?>']").change(function () {
                            if(jQuery(this).val()!=''){
                                jQuery("#<?php echo $field_name; ?>_state").html('Please wait...');
                                
                                var data = {
                                    "action": "rm_get_state",
                                    "rm_slug": "rm_get_state",
                                    "country": jQuery(this).val(),
                                    "attr": "data-rm-state-val",
                                    "form_id": "<?php echo $this->_attributes['form_id'] ?>",
                                    'state_field_id': '<?php echo $field_name.'_state' ?>',
                                    'type': 'billing' 
                                };
                                rm_get_state(this, rm_ajax_url, data);
                            }
                        });
                        jQuery("[name='<?php echo $field_name . '[country]'; ?>']").trigger('change');
                    });
                    </script>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
       </div>
       
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_country_en'] == 1):
                ?>
       
       <div class="rm-wc-wrap">
                <div class="rm-wc-hw">
                  
                    <div class="rm-wc-field">
                        <?php
                        $gew_setting = get_option('woocommerce_default_customer_address');
                        $country_code = '';
                        if(!empty($values['country'])){
                            $country_code = $values['country'];
                        }
                        if (!empty($gew_setting) && in_array($gew_setting, array('geolocation', 'geolocation_ajax'))) {
                            if(!empty($user->ID)){
                                $c_code= get_user_meta($user->ID, 'billing_country', true);
                                if(!empty($c_code)){
                                    $country_code= $c_code;
                                }
                            }
                            $geo_add = WC_Geolocation::geolocate_ip( '', true, false );
                            if(!empty($geo_add['country'])){
                                $country_code = $geo_add['country'];
                            }
                            if(empty($country_code)){
                                $country_code = get_option('woocommerce_default_country');
                            }
                            if(!empty($WC_Session['country'])){
                                $country_code = $WC_Session['country'];
                            }
                            if(!empty($country_code)){
                                foreach (RM_Utilities::get_countries() as $key => $val) {
                                    $pos = strpos($key, '[' . $country_code . ']');
                                    if ($pos !== false) {
                                        $country_code = $key;
                                        break;
                                    }
                                }
                            }
                        }else{
                            if(!empty($user->ID)){
                                $c_code= get_user_meta($user->ID, 'billing_country', true);
                            }
                            
                            if(!empty($WC_Session['country'])){
                                $c_code = $WC_Session['country'];
                            }
                            if(!empty($c_code)){
                                foreach (RM_Utilities::get_countries() as $key => $val) {
                                    $pos = strpos($key, '[' . $c_code . ']');
                                    if ($pos !== false) {
                                        $country_code = $key;
                                        break;
                                    }
                                }
                            }
                        }                        
                        ?>
                        <select style="<?php echo $this->_attributes['textfield_style']; ?>" <?php echo empty($this->_attributes['field_wcb_country_req']) ? '' : 'required'; ?> name="<?php echo $field_name . '[country]'; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>">
                            <?php foreach (RM_Utilities::get_countries() as $key => $val): ?>
                                <option <?php echo $country_code == $key ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_country_label']; ?>
                                <?php if (!empty($this->_attributes['field_wcb_country_req'])) : ?>
                                    <sup class="required">&nbsp;*</sup>
                                <?php endif; ?>

                        </div>
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_zip_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                  
                    <div class="rm-wc-field">
                        <?php if(!empty($user->ID)){
                                $values['zip']= get_user_meta($user->ID, 'billing_postcode', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['zip']) ? '' : $values['zip']; ?>" type="text" name="<?php echo $field_name . '[zip]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_zip_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_zip_req']) ? '' : 'required'; ?>>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcb_zip_label']; ?>
                            
                              <?php if (!empty($this->_attributes['field_wcb_zip_req'])) : ?>
                                  <sup class="required">&nbsp;*</sup>
                              <?php endif; ?>

                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
       </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_phone_en'] == 1):
                ?>
       <div class="rm-wc-wrap">
                <div class="rm-wc-fw">

                    <div class="rm-wc-field">
                        <?php $pattern = "^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$"; 
                              if(!empty($user->ID)){
                                $values['phone']= get_user_meta($user->ID, 'billing_phone', true);
                              } 
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['phone']) ? '' : $values['phone']; ?>" type="text" pattern="<?php //echo $pattern; ?>" name="<?php echo $field_name . '[phone]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_phone_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcb_phone_req']) ? '' : 'required'; ?> >
                    
                        <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcb_phone_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcb_phone_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                       
                    </div>
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
       </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcb_email_en'] == 1):
                ?>
            <div class="rm-wc-wrap">
                <div class="rm-wc-fw">
                 
                    <div class="rm-wc-field">
                        <?php
                        $email_required = empty($this->_attributes['field_wcb_email_req'])?false:true;
                        $field_value='';
                        $read_only='';
                        if ($this->_attributes['field_wcb_email_as_primary'] == 1) {
                            $email_required = true;
                            $form = new RM_Forms();
                            $form->load_from_db($this->_attributes['form_id']);
                            if (is_user_logged_in() && $form->get_form_type() == 1) {
                                $current_user = wp_get_current_user();
                                $field_value = $current_user->user_email;
                                $read_only = 'readonly';
                            }
                            else if (!is_user_logged_in()) {
                                if($form->get_form_type() == 1){
                            ?>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery("[name='<?php echo $field_name . '[email]'; ?>']").change(function () {
                                        var data = {
                                            "action": "rm_user_exists",
                                            "rm_slug": "rm_user_exists",
                                            "email": jQuery(this).val(),
                                            "attr": "data-rm-valid-email",
                                            "form_id": "<?php echo $this->_attributes['form_id'] ?>"
                                        };
                                        jQuery('.rm_wc_hidden_email').val(jQuery(this).val());
                                        rm_user_exists(this, rm_ajax_url, data);
                                    });
                                });
                            </script>
                            <?php   } else { ?>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery("[name='<?php echo $field_name . '[email]'; ?>']").change(function () {
                                        jQuery('.rm_wc_hidden_email').val(jQuery(this).val());
                                    });
                                });
                            </script>
                            <?php }
                            } else if (is_user_logged_in() && $form->get_form_type() != 1) { ?>
                            <script>
                                jQuery(document).ready(function () {
                                    jQuery("[name='<?php echo $field_name.'[email]'; ?>']").change(function () {
                                        jQuery('.rm_wc_hidden_email').val(jQuery(this).val());
                                    });
                                });
                            </script>
                            <?php
                            }
                        }
                        if(!empty($user->ID)){
                            $get_field_value = get_user_meta($user->ID, 'billing_email', true);
                            if(!empty($get_field_value)){
                                $field_value= $get_field_value;
                            }
                        }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="email" <?php echo $read_only; ?> value="<?php echo $field_value; ?>"  name="<?php echo $field_name . '[email]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcb_label_as_placeholder']) ? '' : $this->_attributes['field_wcb_email_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo ($email_required) ? 'required' : ''; ?> >
                    
                        <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcb_email_label']; ?>
                            <?php if ($email_required) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        
                        </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
       <?php endif; ?>
        <?php if(!empty($this->_attributes['help_text'])):?>
            <div class="rmnote rm-wc-hover-text">
                <div class="rmprenote"></div>
                <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
            </div>
            <script>
               
            jQuery(document).ready(function () {
                jQuery(".rm-wcbilling .rm-wc-field input").focus(function () {
                    jQuery('.rm-wcbilling .rm-wc-hover-text').show();
                });
                jQuery(".rm-wcbilling .rm-wc-field input").mouseout(function () {
                    jQuery('.rm-wcbilling .rm-wc-hover-text').hide();
                });
            });

          
            </script>            
            <?php endif; ?>
</div>
           
            

        <?php } else{
            $field_name = 'wcshipping_' . $this->_attributes['field_id'];
            ?>
  <div class="rm-wcshipping">
      <div class="rm-wc-wrap">
            <?php if ($this->_attributes['field_wcs_firstname_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                  
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['firstname']= get_user_meta($user->ID, 'shipping_first_name', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['firstname']) ? '' : $values['firstname']; ?>" name="<?php echo $field_name . '[firstname]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_firstname_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_firstname_req']) ? '' : 'required'; ?>>
                      <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_firstname_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_firstname_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        
                    </div>
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>                    
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_lastname_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                 
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['lastname']= get_user_meta($user->ID, 'shipping_last_name', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['lastname']) ? '' : $values['lastname']; ?>" name="<?php echo $field_name . '[lastname]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_lastname_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_lastname_req']) ? '' : 'required'; ?>>
                       <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_lastname_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_lastname_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                      
                    </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>                    
                </div>
            <?php endif; ?>
      
      </div>     

            <?php if ($this->_attributes['field_wcs_company_en'] == 1):
                ?>
                <div class="rm-wc-wrap">
                    <div class="rm-wc-fw">
                   
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['company']= get_user_meta($user->ID, 'shipping_company', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['company']) ? '' : $values['company']; ?>" name="<?php echo $field_name . '[company]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_company_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_company_req']) ? '' : 'required'; ?>>
                     <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_company_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_company_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                       
                    </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?> 
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_address1_en'] == 1):
                ?>
                <div class="rm-wc-wrap">
                   <div class="rm-wc-fw">
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['add1']= get_user_meta($user->ID, 'shipping_address_1', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" value="<?php echo empty($values['add1']) ? '' : $values['add1']; ?>" name="<?php echo $field_name . '[add1]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_address1_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_address1_req']) ? '' : 'required'; ?>>
                     <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_address1_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_address1_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                       
                    </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_address2_en'] == 1):
                ?>
                <div class="rm-wc-wrap">
                    <div class="rm-wc-fw">
                   
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['add2']= get_user_meta($user->ID, 'shipping_address_2', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['add2']) ? '' : $values['add2']; ?>" type="text" name="<?php echo $field_name . '[add2]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_address2_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_address2_req']) ? '' : 'required'; ?>>
                     <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_address2_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_address2_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                       
                    </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_city_en'] == 1):
                ?>
      <div class="rm-wc-wrap">

                <div class="rm-wc-hw">
                  
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['city']= get_user_meta($user->ID, 'shipping_city', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['city']) ? '' : $values['city']; ?>" type="text" name="<?php echo $field_name . '[city]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_city_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_city_req']) ? '' : 'required'; ?> >
                    
                      <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_city_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_city_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        
                    </div>
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
      
      
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_state_en'] == 1):
                ?>
                <div class="rm-wc-hw">
                  <?php if(!empty($user->ID)){
                         $values['state']= get_user_meta($user->ID, 'shipping_state', true);
                     }
                     ?>
                    <div class="rm-wc-field">
                        <span id="<?php echo $field_name . '_state'; ?>"><input style="<?php echo $this->_attributes['textfield_style']; ?>" type="text" name="<?php echo $field_name . '[state]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_state_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_state_req']) ? '' : 'required'; ?> value="<?php echo empty($values['state']) ? '' : $values['state']; ?>"></span>
                        <div class="rm-wc-label">
                            <?php echo $this->_attributes['field_wcs_state_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_state_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div data-style="<?php echo $this->_attributes['textfield_style']; ?>" id="<?php echo $field_name . '_state_attrs'; ?>" data-name="<?php echo $field_name . '[state]'; ?>" data-placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_state_label']; ?>" data-required="<?php echo empty($this->_attributes['field_wcs_state_req']) ? '' : 'required'; ?>" data-value="<?php echo empty($values['state']) ? '' : $values['state']; ?>" data-class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" ></div>
                    <script>
                    jQuery(document).ready(function () {
                        jQuery("[name='<?php echo $field_name . '[country]'; ?>']").change(function () {
                            if(jQuery(this).val()!=''){
                                jQuery("#<?php echo $field_name; ?>_state").html('Please wait...');
                                var data = {
                                    "action": "rm_get_state",
                                    "rm_slug": "rm_get_state",
                                    "country": jQuery(this).val(),
                                    "attr": "data-rm-state-val",
                                    "form_id": "<?php echo $this->_attributes['form_id'] ?>",
                                    'state_field_id': '<?php echo $field_name.'_state' ?>',
                                    'type': 'shipping'
                                };
                                rm_get_state(this, rm_ajax_url, data);
                            }
                        });
                        jQuery("[name='<?php echo $field_name . '[country]'; ?>']").trigger('change');
                    });
                    </script>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
          
      </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_country_en'] == 1):
                ?>
      <div class="rm-wc-wrap">
                <div class="rm-wc-hw">
                 
                    <div class="rm-wc-field">
                        <?php
                        $gew_setting = get_option('woocommerce_default_customer_address');
                        $country_code = '';
                        if(!empty($values['country'])){
                            $country_code = $values['country'];
                        }
                        if (!empty($gew_setting) && in_array($gew_setting, array('geolocation', 'geolocation_ajax'))) {
                            if(!empty($user->ID)){
                                $c_code= get_user_meta($user->ID, 'shipping_country', true);
                                if(!empty($c_code)){
                                    $country_code= $c_code;
                                }
                            }
                            $geo_add = WC_Geolocation::geolocate_ip( '', true, false );
                            if(!empty($geo_add['country'])){
                                $country_code = $geo_add['country'];
                            }
                            if(empty($country_code)){
                                $country_code = get_option('woocommerce_default_country');
                            }
                            if(!empty($WC_Session['shipping_country'])){
                                $country_code = $WC_Session['shipping_country'];
                            }
                            if(!empty($country_code)){
                                foreach (RM_Utilities::get_countries() as $key => $val) {
                                    $pos = strpos($key, '[' . $country_code . ']');
                                    if ($pos !== false) {
                                        $country_code = $key;
                                        break;
                                    }
                                }
                            }
                        }else{
                            if(!empty($user->ID)){
                                $c_code= get_user_meta($user->ID, 'shipping_country', true);
                            }
                            
                            if(!empty($WC_Session['shipping_country'])){
                                $c_code = $WC_Session['shipping_country'];
                            }
                            if(!empty($c_code)){
                                foreach (RM_Utilities::get_countries() as $key => $val) {
                                    $pos = strpos($key, '[' . $c_code . ']');
                                    if ($pos !== false) {
                                        $country_code = $key;
                                        break;
                                    }
                                }
                            }
                        }
                        ?>
                        <select style="<?php echo $this->_attributes['textfield_style']; ?>" <?php echo empty($this->_attributes['field_wcs_country_req']) ? '' : 'required'; ?> name="<?php echo $field_name . '[country]'; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>">
                            <?php foreach (RM_Utilities::get_countries() as $key => $val): ?>
                                <option <?php echo $country_code == $key ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="rm-wc-label">
                                            <?php echo $this->_attributes['field_wcs_country_label']; ?>
                                            <?php if (!empty($this->_attributes['field_wcs_country_req'])) : ?>
                                                <sup class="required">&nbsp;*</sup>
                                            <?php endif; ?>

                         </div>
                        
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->_attributes['field_wcs_zip_en'] == 1):
                ?>
                <div class="rm-wc-hw">
               
                    <div class="rm-wc-field">
                        <?php 
                            if(!empty($user->ID)){
                                $values['zip']= get_user_meta($user->ID, 'shipping_postcode', true);
                            }
                        ?>
                        <input style="<?php echo $this->_attributes['textfield_style']; ?>" value="<?php echo empty($values['zip']) ? '' : $values['zip']; ?>" type="text" name="<?php echo $field_name . '[zip]'; ?>" placeholder="<?php echo empty($this->_attributes['field_wcs_label_as_placeholder']) ? '' : $this->_attributes['field_wcs_zip_label']; ?>" class="<?php echo empty($this->_attributes['field_css_class']) ? '' : $this->_attributes['field_css_class']; ?>" <?php echo empty($this->_attributes['field_wcs_zip_req']) ? '' : 'required'; ?>>
                        <div class="rm-wc-label">
                        <?php echo $this->_attributes['field_wcs_zip_label']; ?>
                            <?php if (!empty($this->_attributes['field_wcs_zip_req'])) : ?>
                                <sup class="required">&nbsp;*</sup>
                            <?php endif; ?>
                       
                    </div>
                    
                    </div>
                    <?php if(!empty($this->_attributes['help_text'])):?>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
                    </div>
                    <?php endif; ?>
                </div>
      </div>
      <?php endif; ?>
        <?php if(!empty($this->_attributes['help_text'])):?>
            <div class="rmnote rm-wc-hover-text">
                <div class="rmprenote"></div>
                <div class="rmnotecontent"><?php echo $this->_attributes['help_text'] ?></div>
            </div>
            <script>
            jQuery(document).ready(function () {
                jQuery(".rm-wcshipping .rm-wc-field input, .rm-wcshipping .rm-wc-field select").focus(function () {
                    jQuery('.rm-wcshipping .rm-wc-hover-text').show();
                });
                jQuery(".rm-wcshipping .rm-wc-field input, .rm-wcshipping .rm-wc-field select").mouseout(function () {
                    jQuery('.rm-wcshipping .rm-wc-hover-text').hide();
                });
            });
            </script>
            <?php endif; ?>
    </div>
        <?php } ?>


        <?php
    }

}
