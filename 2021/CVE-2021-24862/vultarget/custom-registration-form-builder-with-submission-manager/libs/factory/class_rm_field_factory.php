<?php

/**
 * Description of RM_Field_Factory
 *
 */
class RM_Field_Factory {
    
    public $db_field;
    public $field_name;
    public $field_options;
    public $gopts;
    public $opts;
    public $x_opts;
    public $prevent_value_update;
            
    public function __construct($db_field,$opts, $prevent_value_update = false){
        $this->prevent_value_update = $prevent_value_update;
        $this->db_field= $db_field;
        $this->gopts= new RM_Options;
        $this->opts= $opts;
        //var_dump(maybe_unserialize($db_field->field_options));
        $this->field_options = maybe_unserialize($db_field->field_options);
        $temp_field = new RM_Fields;
        $valid_field_options = $temp_field->get_valid_options();
        
        if($this->field_options) {
            foreach($valid_field_options as $option_name) {
                if(!isset($this->field_options->$option_name)) {
                    $this->field_options->$option_name = null;
                }
            }
        }
        
        $this->field_name= $db_field->field_type."_".$db_field->field_id;
        $this->db_field->field_value = maybe_unserialize($db_field->field_value);
        if(isset($this->field_options->icon))
            $this->x_opts = (object)array('icon' => $this->field_options->icon);
        else
            $this->x_opts = null;
        
        if(!isset($this->opts['value']))
            $this->opts['value'] = null;
    }
    
     public function create_binfo_field(){
        if(is_user_logged_in() && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_binfo= get_user_meta($current_user->ID, 'description', true);
            $this->opts['value'] = ($user_binfo == '')? $this->opts['value'] : $user_binfo;
        }
       return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
   
     }     
    
    public function create_price_field(){
        $currency_pos = $this->gopts->get_value_of('currency_symbol_position');
        $currency_symbol = $this->gopts->get_currency_symbol();
        return new RM_Frontend_Field_Price($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->field_value, $currency_pos, $currency_symbol, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_file_field(){
        return new RM_Frontend_Field_File($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function get_user_meta_key(){
        if(empty($this->field_options->field_user_profile)){
            if(!empty($this->field_options->field_meta_add)){
                return $this->field_options->field_meta_add;
            }
        }
        else{
            if($this->field_options->field_user_profile=='existing_user_meta'){
                return $this->field_options->existing_user_meta_key;
            }
            else if($this->field_options->field_user_profile=='define_new_user_meta'){
                return $this->field_options->field_meta_add;
            }
        }
        return 'rm_no_meta_key';
    }
    
    public function create_select_field(){
        if(is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page && !empty($this->field_options->field_meta_add)){
            $current_user = wp_get_current_user();  
            $select_field= get_user_meta($current_user->ID, $this->get_user_meta_key(), true);
            $this->opts['value'] = ($select_field == '')? $this->opts['value'] : $select_field;
        }
        return new RM_Frontend_Field_Select($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_multidropdown_field(){
      
        $this->opts['multiple']='multiple';
        return null;
    }
    
    public function create_repeatable_field(){
        return null;
    }
    
    public function create_base_field(){
        if (is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page) {
            $current_user = wp_get_current_user();
            $user_base_info= get_user_meta($current_user->ID,$this->get_user_meta_key(), true);
            $this->opts['value'] = ($user_base_info == '')? $this->opts['value'] : $user_base_info;
        }
        return new RM_Frontend_Field_Base($this->db_field->field_id,'Textbox', '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_phone_field(){
       return null;
    }
    
    public function create_nickname_field(){
        if(is_user_logged_in() && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_nickname= get_user_meta($current_user->ID, 'nickname', true);
            $this->opts['value'] = ($user_nickname == '')? $this->opts['value'] : $user_nickname;
        }
       return new RM_Frontend_Field_Base($this->db_field->field_id,'Nickname', '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_image_field(){
        return null;
    }
    
    public function create_esign_field() {
        $this->opts['accept'] = "image/*";
        $this->opts['class'] = empty($this->opts['class']) ? 'rm_esign_field' : $this->opts['class'].' rm_esign_field';
        return $this->create_file_field();
    }
    
    public function create_facebook_field(){
        //return null;
        $validate = new Validation_RegExp("/(?:https?:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*?(\/)?([\w\-\.]*)/",  RM_UI_Strings::get("FACEBOOK_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(?:https?:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*?(\/)?([\w\-\.]*)";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_website_field(){
        $this->opts['Pattern'] = "((?:https?\:\/\/|[wW][wW][wW]\.)(?:[-a-zA-Z0-9]+\.)*[-a-zA-Z0-9]+.*)";
        if(is_user_logged_in() && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user(); 
            $this->opts['value'] = isset($current_user->user_url)? $current_user->user_url : null;
        }
        return new RM_Frontend_Field_Base($this->db_field->field_id,'Website', '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_twitter_field(){
        //return null;
        $validate = new Validation_RegExp("/(ftp|http|https):\/\/?((www|\w\w)\.)?twitter.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/",  RM_UI_Strings::get("TWITTER_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(ftp|http|https):\/\/?((www|\w\w)\.)?twitter.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_google_field(){
        //return null;
        $validate = new Validation_RegExp("/((http:\/\/(plus\.google\.com\/.*|www\.google\.com\/profiles\/.*|google\.com\/profiles\/.*))|(https:\/\/(plus\.google\.com\/.*)))/i",  RM_UI_Strings::get("GOOGLE_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "((http:\/\/(plus\.google\.com\/.*|www\.google\.com\/profiles\/.*|google\.com\/profiles\/.*))|(https:\/\/(plus\.google\.com\/.*)))";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_instagram_field(){
       //return null;
        $validate = new Validation_RegExp("/(?:^|[^\w])(?:@)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/", RM_UI_Strings::get("INSTAGRAM_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(?:^|[^\w])(?:@)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_linked_field(){
        //return null;
        $validate = new Validation_RegExp("/(ftp|http|https):\/\/?((www|\w\w)\.)?linkedin.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", RM_UI_Strings::get("LINKED_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(ftp|http|https):\/\/?((www|\w\w)\.)?linkedin.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_soundcloud_field(){
        //return null;
        $validate = new Validation_RegExp("/(ftp|http|https):\/\/?((www|\w\w)\.)?soundcloud.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", RM_UI_Strings::get("SOUNDCLOUD_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(ftp|http|https):\/\/?((www|\w\w)\.)?soundcloud.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_youtube_field(){
        //return null;
        $validate = new Validation_RegExp("/(ftp|http|https):\/\/?((www|\w\w)\.)?youtube.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/",  RM_UI_Strings::get("YOUTUBE_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(ftp|http|https):\/\/?((www|\w\w)\.)?youtube.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_vkontacte_field(){
        //return null;
        $validate = new Validation_RegExp("/(ftp|http|https):\/\/?((www|\w\w)\.)?(vkontakte.com|vk.com)(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", RM_UI_Strings::get("VKONTACTE_ERROR"));
        $this->opts['validation'] = $validate;
        $this->opts['Pattern'] = "(ftp|http|https):\/\/?((www|\w\w)\.)?(vkontakte.com|vk.com)(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_skype_field(){
        //return null;
        //$validate = new Validation_RegExp("/[a-zA-Z][a-zA-Z0-9_\-\,\.]{5,31}/", RM_UI_Strings::get("SKYPE_ERROR"));
        //$this->opts['validation'] = $validate;
        //$this->opts['Pattern'] = "[a-zA-Z][a-zA-Z0-9_\-\,\.]{5,31}";
        $field= $this->create_base_field();
        return $field;
    }
    
    public function create_bdate_field(){
        return null;
    }
    
    public function create_secemail_field(){
        return null;
    }
    
    public function create_gender_field(){
        return null;
    }
    
    public function create_terms_field(){
        if (is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page) {
            //These option must be same as specified in gender field analytics calculation.
            $current_user = wp_get_current_user();
            $terms = get_user_meta($current_user->ID,$this->get_user_meta_key(), true);
            $this->opts['value'] = empty($terms) ? $this->db_field->field_value : $terms;
        }
        $this->opts['cb_label'] = isset($this->field_options->tnc_cb_label)?$this->field_options->tnc_cb_label:null;
        return new RM_Frontend_Field_Terms($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_language_field(){
       return null;
    }
    
    public function create_radio_field(){
        if(is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page && !empty($this->field_options->field_meta_add)){
            $current_user = wp_get_current_user();  
            $radio_info= get_user_meta($current_user->ID,$this->get_user_meta_key(), true);
            $this->opts['value'] = ($radio_info == '')? $this->opts['value'] : $radio_info;
        }
        return new RM_Frontend_Field_Radio($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_checkbox_field(){
        
         if(is_user_logged_in() && !$this->prevent_value_update &&  !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page && !empty($this->field_options->field_meta_add)){
                $current_user = wp_get_current_user();  
                $checkbox_info= get_user_meta($current_user->ID,$this->get_user_meta_key(), true);
                $this->opts['value'] = ($checkbox_info == '')? $this->opts['value'] : $checkbox_info;  
        }
      
       
        return new RM_Frontend_Field_Checkbox($this->db_field->field_id, $this->db_field->field_label, '', $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
      
        }
 
    public function create_shortcode_field(){
        return null;
    }
    
    public function create_divider_field(){
       return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts, ' <div class="rmrow rm-full-width"><hr class="rm_divider" width="100%" size="8" align="center"></div>', $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_spacing_field(){
       return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts, '<div class="rmrow rm-full-width"><div class="rm_spacing"></div></div>', $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_htmlh_field(){
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_htmlp_field(){
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->field_value, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_time_field(){
        return null;
    }
    
    public function create_rating_field(){
        return null;
    }
    
    public function create_custom_field(){
               return null;
    }
    
    public function create_email_field(){
        // in this case pre-populate the primary email field with logged-in user's email.
        if($this->db_field->is_field_primary)
        {
            if(is_user_logged_in() && !isset($_GET['form_prev']))
            {
                $current_user = wp_get_current_user();                            
                $this->opts['value'] = $current_user->user_email;
                $this->opts['readonly']= 'readonly';
            }
            else
            {
                // Preparing confirm email options
                $cp_opts = $this->opts;
                $cp_opts['placeholder'] = RM_UI_Strings::get('LABEL_EMAIL_PH_AGAIN');
                $cp_opts['id'] = 'rm_reg_form_email_reentry';
                $cp_opts['label'] = RM_UI_Strings::get('LABEL_EMAIL_AGAIN');

                // Email field ID
                $this->opts['id'] = 'rm_reg_form_email_' . $this->db_field->form_id.'_'.RM_Public::$form_counter;

                // Fetch email field options
                $rm_service = new RM_Services();
                $field = $rm_service->get_primary_field_options('email', $this->db_field->form_id);
                $field_options = maybe_unserialize($field->field_options);

                $form = new RM_Forms();
                $form->load_from_db($this->db_field->form_id);

                if (isset($field_options->en_confirm_email) && $field_options->en_confirm_email) {
                    $fields_array = array();

                    array_push($fields_array, new RM_Frontend_Field_Base($this->db_field->field_id,$this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts));
                    
                    $cp_opts['name']="email_confirmation";
                    array_push($fields_array, new RM_Frontend_Field_Base($this->db_field->field_id,$this->db_field->field_type, '', $this->db_field->field_label, $cp_opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts));
                    
                    return $fields_array;
                }
            }
        }
        else
        {
            if(is_user_logged_in()  && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page && !empty($this->field_options->field_meta_add)){
                $current_user = wp_get_current_user();  
                $user_emailinfo= get_user_meta($current_user->ID,$this->get_user_meta_key(), true);
                $this->opts['value'] = ($user_emailinfo == '')? $this->opts['value'] : $user_emailinfo;
            }
        }
         return new RM_Frontend_Field_Base($this->db_field->field_id,$this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_address_field(){
        $field= $this->create_geo_field();
        return $field;
    }
    
    public function create_wcbilling_field() {
        return new RM_Frontend_Field_WCAddress($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no,'billing', $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_wcshipping_field() {
        return new RM_Frontend_Field_WCAddress($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no,'shipping', $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_wcbillingphone_field() {
        $fields = null;
        if(!class_exists( 'WooCommerce' )){
            return array();
        }
        $user= wp_get_current_user();
        if(!empty($user->ID))
        {
            $this->opts['value']= get_user_meta($user->ID, 'billing_phone', true);
        }
        $field = $this->create_base_field();
        return $field;
    }
    
    public function create_map_field(){ 
        $field= $this->create_geo_field();       
        return $field;
    }
    
    public function create_geo_field(){
        $service = new RM_Front_Form_Service;
        if(is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_address = get_user_meta($current_user->ID, $this->get_user_meta_key(), true);
            $this->opts['value'] = ($user_address == '')? $this->opts['value'] : $user_address;
        }
        return new RM_Frontend_Field_GGeo($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $service->get_setting('google_map_key'), $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    
    public function create_textbox_field(){
        $field= $this->create_base_field();
        return $field;
    }
    
    
    public function create_fname_field(){
       if(is_user_logged_in() && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_fname= get_user_meta($current_user->ID, 'first_name', true);
            $this->opts['value'] = ($user_fname == '')? $this->opts['value'] : $user_fname;
        }
      return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
  
    }
    
    public function create_lname_field(){
        if(is_user_logged_in() && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_lname= get_user_meta($current_user->ID, 'last_name', true);
            $this->opts['value'] = ($user_lname == '')? $this->opts['value'] : $user_lname;
        }
      return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
  
    }
    
    public function create_hidden_field(){
        return new RM_Frontend_Field_Hidden($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_richtext_field(){ 
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, wpautop($this->db_field->field_value), $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_jqueryuidate_field() {
        if(is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']))
        {
            $current_user = wp_get_current_user();  
            $user_date = get_user_meta($current_user->ID, $this->get_user_meta_key(), true);
            $this->opts['value'] = ($user_date == '')? $this->opts['value'] : $user_date;
        }
        return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_default_field(){
        if(is_user_logged_in() && !$this->prevent_value_update && !isset($_GET['form_prev']) && $this->db_field->field_show_on_user_page && !empty($this->field_options->field_meta_add)){
         $current_user = wp_get_current_user();  
         $user_defaultinfo = get_user_meta($current_user->ID,$this->get_user_meta_key(),true);
         $this->opts['value'] = ($user_defaultinfo == '')? $this->opts['value'] : $user_defaultinfo;
        }
        return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_link_field(){
       $href=''; $target='';
      // print_r($this->field_options);
       if($this->field_options->link_type=="url"){
           $href= $this->field_options->link_href;
       } else if($this->field_options->link_type=="page"){
           $href= get_permalink($this->field_options->link_page);
       }
       
       if($this->field_options->link_same_window!=1){
           $target='target="_blank"';
       }
       
       $link_html= '<div class="rmrow"><div class="rm-link-field"><a '.$target.' href="' ;
       $link_html .= $href.'">';
       $link_html.= $this->db_field->field_label.'</a></div></div>';
       return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized','',$this->db_field->field_label, $this->opts,$link_html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    // Special function for YouTube Widgets
    public function create_youtubev_widget(){
        $class=  isset($options['class'])?$options['class'].' rm-full-width':'rm-full-width';
        $width= !empty($this->field_options->yt_player_width)?$this->field_options->yt_player_width:'560';
        $height= !empty($this->field_options->yt_player_height)?$this->field_options->yt_player_height:'315';
        $video_id= RM_Utilities::extract_youtube_embed_src($this->db_field->field_value);
        $src= "https://www.youtube.com/embed/".$video_id."?autoplay=".$this->field_options->yt_auto_play; 
        $src .= $this->field_options->yt_repeat ? "&playlist=".$video_id."&loop=1"  : '';
        $src .= empty($this->field_options->yt_related_videos) ? '&rel=0' : '';
        
        $iframe= "<div class='rmrow'><iframe width='".$width."' height='".$height."' src='".$src."' frameborder='0' ".($this->field_options->yt_auto_play==1?"allow='autoplay'":"")." allowfullscreen></iframe></div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized','',$this->db_field->field_label, $this->opts,$iframe, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_youtubev_field(){
         return $this->create_youtubev_widget();
     }
    
    public function create_iframe_field(){  
        $class=  isset($options['class'])?$options['class'].' rm-full-width':'rm-full-width';
        $width= !empty($this->field_options->if_width)?$this->field_options->if_width:'auto';
        $height= !empty($this->field_options->if_height)?$this->field_options->if_height:'auto';
        $src= $this->db_field->field_value; 
        $link_type= RM_Utilities::check_src_type($this->db_field->field_value);
        
        if($link_type === 'youtube'){
            $video_id= RM_Utilities::extract_youtube_embed_src($this->db_field->field_value);
            $src= "http://www.youtube.com/embed/".$video_id;        
        }
        elseif($link_type === 'vimeo') {
            $video_id= RM_Utilities::extract_vimeo_embed_src($this->db_field->field_value);
            $src= "http://player.vimeo.com/video/".$video_id; 
        }
        $iframe= "<div class='rmrow'><iframe width='".$width."' height='".$height."' src='".$src."' frameborder='0' allowfullscreen></iframe></div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized','',$this->db_field->field_label, $this->opts,$iframe, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    
    public function create_pricev_field(){ 
        $gopts= new RM_Options(); 
        $class=  $this->field_options->field_css_class;
        $total_price_localized_string = RM_UI_Strings::get('FE_FORM_TOTAL_PRICE');
        $curr_symbol = $gopts->get_currency_symbol();
        $curr_pos = $gopts->get_value_of('currency_symbol_position');
        $price_formatting_data = json_encode(array("loc_total_text" => $total_price_localized_string, "symbol" => $curr_symbol, "pos" => $curr_pos));
        $html= "<div class='rmrow'><div class='rm-total-price-widget $class' data-rmpriceformat='$price_formatting_data'></div></div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_subcountv_field(){
        $class=  $this->field_options->field_css_class;
        $exp_str= RM_Utilities::get_form_expiry_message($this->db_field->form_id); 
        $html= "<div class='rmrow rm_expiry_stat_container $class '>$exp_str</div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_mapv_field(){
        $style='';
        $class=  $this->field_options->field_css_class;
        $address= $this->db_field->field_value;
        $zoom= empty($this->field_options->zoom) ? 17 : $this->field_options->zoom;
        $width= empty($this->field_options->width) ? 250 : $this->field_options->width;
        if($width>0)
            $style="width:$width".'px';
        $service= new RM_Services();
        $gmap_api_key= $service->get_setting('google_map_key');
        $element_id='';
      
        if(!empty($address) && !empty($gmap_api_key)){ 
             wp_enqueue_script ('google_map_key', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places');
             wp_enqueue_script("rm_map_widget_script",RM_BASE_URL."public/js/map_widget.js");
             $element_id= 'map'.$this->db_field->field_id;
             echo '<script>jQuery(document).ready(function(){rm_show_map_widget("'.$element_id.'",["'.$address.'"],'.$zoom.')});</script>';
        }
       
        $html= "<div class='rmrow'><div style='$style' class='rm_mapv_container $class '><div id='".$element_id."' class='rm-map-widget'></div></div></div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_imagev_field(){ 
        $class=  $this->field_options->field_css_class;
        $width= $this->field_options->img_size;
        $img='';$styles=array();$href='';$caption='';$title='';$shape_class='';
        $shape_class='';
        $this->field_options->border_width= $this->field_options->border_width=='' ? 5 : $this->field_options->border_width.'px';
        if($this->field_options->img_effects_enabled){
            $styles['border:']= 'solid'; 
            $styles['border-color:']= '#'.$this->field_options->border_color;
            $styles['border-width:']= $this->field_options->border_width;
            if(strtolower($this->field_options->border_shape)=="circle")
                $shape_class= 'imgv_shape_circle';
            else if(strtolower($this->field_options->border_shape)=="square")
                $shape_class= 'imgv_shape_square';  
        }
        
       if(strtolower($width)!='thumbnail'){
            $styles['width:']= $width;
        }

        if($this->field_options->link_type=="url"){
           $href= $this->field_options->link_href;
        } else if($this->field_options->link_type=="page"){
           $href= get_permalink($this->field_options->link_page);
        }
        
        $post = get_post($this->db_field->field_value);
        if(!empty($post)){
            if($this->field_options->img_caption_enabled){
                $caption= $post->post_excerpt; 
            }

            if($this->field_options->img_title_enabled){
                $title= $post->post_title;
            }
        }
       
       $style_str='style="';
       foreach($styles as $key=>$val){
           $style_str .= $key.$val.';';
       }
       $style_str .= '"';
        if(strtolower($width)=='thumbnail'){
            $src_array=wp_get_attachment_image_src($this->db_field->field_value,'thumbnail');
            if(is_array($src_array)){
                $src= $src_array[0];
                $img= "<img title='".$title."' $style_str src='".$src."' />";
            }
        }
        else{
             $src_array=wp_get_attachment_image_src($this->db_field->field_value,'full');
             if(is_array($src_array)){
                 $src= $src_array[0];
                 $img= "<img title='".$title."' $style_str  src='".$src."' />"; 
             }
        }
        
        if(!empty($href)){ 
            if($this->field_options->img_pop_enabled==1){
                add_thickbox();
                $href= esc_url(add_query_arg("TB_iframe",'true',$href));
                $img= '<a class="thickbox" href="'.$href.'">'.$img.'</a>';
            } else
            $img= '<a target="_blank" href="'.$href.'">'.$img.'</a>';
        } else if($this->field_options->img_pop_enabled==1){
            add_thickbox();
            $src_array= wp_get_attachment_image_src($this->db_field->field_value,'full');
            $src= esc_url(add_query_arg("TB_iframe",'true',$src_array[0]));
            $img= '<a class="thickbox" href="'.$src.'">'.$img.'</a>';
        }
       
        
     
        $html = "<div class='rmrow'><figure class='rm-image-widget wp-caption ".$shape_class."' ".$class.">$img"."<figcaption class='wp-caption-text'>".$caption."</figcaption></figure></div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized','',$this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_form_chart_field(){ 
        $class=  $this->field_options->field_css_class;
        $chart_type= $this->db_field->field_value;
        $field_label= $this->db_field->field_label;
        $stats_service= new RM_Analytics_Service();
        if($chart_type=="sot"){
            $time_range= $this->field_options->time_range;
            $chart_html= $stats_service->{$chart_type}($this->db_field->form_id,null,$time_range);
        }
        else
            $chart_html= $stats_service->{$chart_type}($this->db_field->form_id);

        $html= "<div class='rmrow rm-box-graph $class'><div class='rm-box-title'>$field_label</div><div id='rm_".$chart_type."_div'></div></div> $chart_html"; 
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_formdata_field(){
        $html= RM_Utilities::get_formdata_widget_html($this->db_field->field_id);
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_feed_field(){
      $html = RM_Utilities::get_feed_widget_html($this->db_field->field_id); 
      return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized', '', $this->db_field->field_label, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_username_field() {
        $this->opts['name']= 'username';
        return new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
    
    public function create_userpassword_field() {
        // Preparing confirm password options
        $cp_opts = $this->opts;
        $cp_opts['placeholder'] = RM_UI_Strings::get('LABEL_PASSWORD_PH_AGAIN');
        $cp_opts['id'] = 'rm_reg_form_pw_reentry';
        $cp_opts['label'] = RM_UI_Strings::get('LABEL_PASSWORD_AGAIN');
        $this->opts['name']="pwd";
        // Password field ID
        $this->opts['id'] = 'rm_reg_form_pw_' . $this->db_field->form_id.'_'.RM_Public::$form_counter;

        // Fetch password field options
        $rm_service = new RM_Services();
        $field = $rm_service->get_primary_field_options('userpassword', $this->db_field->form_id);
        $field_options = maybe_unserialize($field->field_options);

        $form = new RM_Forms();
        $form->load_from_db($this->db_field->form_id);
        
        $fields_array = array();
        
        $pw_error_msg = array('PWR_UC' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_UC'),
            'PWR_NUM' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_NUM'),
            'PWR_SC' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_SC'),
            'PWR_MINLEN' => RM_UI_Strings::get('LABEL_PW_MINLEN_ERR'),
            'PWR_MAXLEN' => RM_UI_Strings::get('LABEL_PW_MAXLEN_ERR'));

        $pw_rests = $this->gopts->get_value_of('custom_pw_rests');
        //$patt_regex = RM_Utilities::get_password_regex($pw_rests);

        $error_str = RM_UI_Strings::get('ERR_TITLE_CSTM_PW');
        
        $this->opts['style'] = $form->form_options->style_textfield;
        $this->opts['labelStyle'] = $form->form_options->style_label;
        
        
        $this->opts["minlength"] = 7;


        array_push($fields_array, new RM_Frontend_Field_Base($this->db_field->field_id, $this->db_field->field_type, '', $this->db_field->field_label, $this->opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts));

        
        if ($field_options->en_pass_strength) {
            wp_enqueue_script("rm_pwd_strength", RM_BASE_URL . "public/js/password.min.js");
            $password_warnings= array('shortPass'=>$field_options->pwd_short_msg,
                                     'badPass'=>$field_options->pwd_weak_msg,
                                      'goodPass'=>$field_options->pwd_medium_msg,
                                      'strongPass'=>$field_options->pwd_strong_msg);
            wp_add_inline_script('rm_pwd_strength', "var rm_pass_warnings=". json_encode($password_warnings).";");
            wp_add_inline_script('rm_pwd_strength', "jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').password({ shortPass: rm_pass_warnings.shortPass,badPass:rm_pass_warnings.badPass,goodPass:rm_pass_warnings.goodPass,strongPass: rm_pass_warnings.strongPass,});})");
        }
         

        if ($field_options->en_confirm_pwd) {
            $cp_opts['name']="password_confirmation";
            array_push($fields_array, new RM_Frontend_Field_Base($this->db_field->field_id,$this->db_field->field_type, '', $this->db_field->field_label, $cp_opts, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts));
        }

        return $fields_array;
    }
    
    public function create_mobile_field() {
        $this->opts['id']='rm_mobile_'.$this->db_field->field_id.'_'. $this->db_field->form_id . '_' . $this->db_field->page_no;
        $this->opts['data-error-message']= $this->field_options->mobile_err_msg;
        
        $tel_params= "{";
        $error_msg= $this->field_options->mobile_err_msg;
        $embed= false;
        if(isset($_GET['action']) && $_GET['action']=='registrationmagic_embedform'){
            $embed= true; 
        }
        if($this->field_options->format_type=='international'){
            wp_enqueue_script("rm_mobile_data_script", RM_BASE_URL . "public/js/mobile_field/data.js",array('jquery'));
            wp_enqueue_script("rm_mobile_script", RM_BASE_URL . "public/js/mobile_field/intlTelInput.js",array('jquery'));
            wp_enqueue_style("rm_mobile_style", RM_BASE_URL . "public/css/mobile_field/intlTelInput.css");
            
            $util_js= RM_BASE_URL . "public/js/mobile_field/utils.js";
            if(empty($this->field_options->field_placeholder)){
                $tel_params .= "autoPlaceholder:'aggressive',";
              }
            $country_field_name='';
            if(!empty($this->field_options->sync_country) && $this->field_options->country_field!='not_found'){
              $rm_country_field= new RM_Fields();
              $rm_country_field->load_from_db($this->field_options->country_field);
              if($rm_country_field->field_type=='Address'){
                  $country_field_name= $rm_country_field->get_field_type().'_'.$this->field_options->country_field.'[country]';
                  wp_localize_script('rm_mobile_script','rm_country_list', RM_Utilities::get_countries() ); 
              }
              else
              $country_field_name= $rm_country_field->get_field_type().'_'.$this->field_options->country_field;
              
              $force_match_js= '';
              
              if(!empty($this->field_options->country_match)){
                  $force_match_js= "jQuery('[id=" . $this->opts['id'] . "]').closest('.rminput,.rmwc-input').find('.selected-flag').addClass('disable-flag');";
              }
             
              $preferred_countries='';
              if(!empty($this->field_options->preferred_countries)){
                  $countries= explode(',', $this->field_options->preferred_countries);
                  if(is_array($countries)){
                      $preferred_countries= '[';
                      foreach($countries as $country){
                        $preferred_countries .= '"'.strtolower(RM_Utilities::get_country_code($country)).'",';
                      }
                      
                      $tel_params .= 'preferredCountries:'.rtrim($preferred_countries, ',').'],';
                  }
                 
              }
             
              if(!empty($this->field_options->en_geoip)){
                  $tel_params .= 'initialCountry:"auto",geoIpLookup: function(callback) {jQuery.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {var countryCode = (resp && resp.country) ? resp.country : "";callback(countryCode);});},';
              }
              
              if($rm_country_field->field_type=='Address'){
                  if($embed){
                      echo "<script>jQuery(document).ready(function(){jQuery('[name=\'".$country_field_name."\']').change(function(){" 
                      . " var selected_value= jQuery(this).val(); var country=rm_get_country_code_by_name(rm_country_list,selected_value); jQuery('[id=" . $this->opts['id'] . "]').intlTelInput('setCountry',country); ".$force_match_js." "
                      . "})});</script>";
                  }
                  else
                  {
                      wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){jQuery('[name=\'".$country_field_name."\']').change(function(){" 
                      . " var selected_value= jQuery(this).val(); var country=rm_get_country_code_by_name(rm_country_list,selected_value); jQuery('[id=" . $this->opts['id'] . "]').intlTelInput('setCountry',country); ".$force_match_js." "
                      . "})});");
                  }
                  
              }
              else{
                   if($embed){
                       echo "<script>jQuery(document).ready(function(){jQuery('[name=\'".$country_field_name."\']').change(function(){" 
                      . " var selected_value= jQuery(this).val(); var index= selected_value.search(/\[[A-Z]{2}\]/i); if(index>=0) { var country= selected_value.substr(index+1,2).toLowerCase(); jQuery('[id=" . $this->opts['id'] . "]').intlTelInput('setCountry',country); ".$force_match_js."} "
                      . "})});</script>";
                   }
                   else
                   {    
                        wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){jQuery('[name=\'".$country_field_name."\']').change(function(){" 
                      . " var selected_value= jQuery(this).val(); var index= selected_value.search(/\[[A-Z]{2}\]/i); if(index>=0) { var country= selected_value.substr(index+1,2).toLowerCase(); jQuery('[id=" . $this->opts['id'] . "]').intlTelInput('setCountry',country); ".$force_match_js."} "
                      . "})});");
                   }
                 
              }
              
              
            }
            else if(!empty($this->field_options->en_geoip)){
                  $tel_params .= 'initialCountry:"auto",geoIpLookup: function(callback) {jQuery.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {var countryCode = (resp && resp.country) ? resp.country : "";callback(countryCode);});},';
            }
            
            if($tel_params!='{')
                $tel_params.= 'utilsScript:"'.$util_js.'"}';
            else{
                $tel_params='';
            }
            
            $ca_state_type= isset($rm_country_field->field_options->ca_state_type) ? $rm_country_field->field_options->ca_state_type : 'all';
            if($ca_state_type=='america'){
                $preferred_countries= empty($preferred_countries) ? '["us","gb"]' : rtrim($preferred_countries,',').']';
                $placeholder_sett='';
                if(empty($this->field_options->field_placeholder)){
                    $placeholder_sett = "autoPlaceholder:'aggressive',utilsScript:'".$util_js."'";
                }
                if(!empty($this->field_options->country_match)){
                    if($embed){
                         echo "<script>jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form'); form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput({initialCountry:'us',preferredCountries:$preferred_countries,$placeholder_sett}); el.closest('.rminput').find('.selected-flag').addClass('disable-flag'); }, 3000); });</script>";
                    }
                    else
                    {
                         wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form'); form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput({initialCountry:'us',preferredCountries:$preferred_countries,$placeholder_sett}); el.closest('.rminput').find('.selected-flag').addClass('disable-flag'); }, 3000); });");
                    }
                   
                }
                else
                {   
                     if($embed){
                         echo "<script>jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form');  form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput({initialCountry:'us',preferredCountries:$preferred_countries,$placeholder_sett}); }, 3000); });</script>"; 
                     }
                     else
                     {
                         wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form'); form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput({initialCountry:'us',preferredCountries:$preferred_countries,$placeholder_sett}); }, 3000); });");
                     } 
                }
            }
            else{
                if($embed){
                    echo "<script>jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form'); form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput($tel_params); }, 3000);});</script>";   
                }
                else
                {    
                     wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){setTimeout(function(){ var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('form'); form.submit(function(event){if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else { return rm_toggle_tel_wc_error(false,el,''); } el.val(el.intlTelInput('getNumber'))}); el.intlTelInput($tel_params); }, 3000);});");
                }
            }
            $this->opts['data-mobile-intel-field']= '1';
            $meta_value='';
            $mobile_field= new RM_Fields();
            $mobile_field->load_from_db($this->db_field->field_id);
            $form = new RM_Forms();
            $form->load_from_db($this->db_field->form_id);
           
            if($form->get_form_type()==RM_REG_FORM && is_user_logged_in() && $mobile_field->get_field_show_on_user_page()==1){ // Check for existing meta values
                $user_field_meta= $mobile_field->get_field_meta_add();
                if(!empty($user_field_meta)){
                    global $current_user;
                    $meta_value= get_user_meta($current_user->ID,$user_field_meta,true);
                }
            }
           
            if(!empty($this->field_options->country_match) && empty($meta_value)  ){ 
                if($embed){
                     echo "<script>jQuery(document).ready(function(){setTimeout(function(){jQuery('[name=\'".$country_field_name."\']').trigger('change'); },3000);});</script>";
                }
                else
                {
                     wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){setTimeout(function(){jQuery('[name=\'".$country_field_name."\']').trigger('change'); },3000);});");
                }
            }
            else if(!empty($this->field_options->country_match)){ 
                 if($embed){
                     echo "<script>jQuery(document).ready(function(){setTimeout(function(){jQuery('[id=" . $this->opts['id'] . "]').closest('.rminput,.rmwc-input').find('.selected-flag').addClass('disable-flag')},3000)});</script>";
                }
                else
                {
                     wp_add_inline_script('rm_mobile_script',"jQuery(document).ready(function(){setTimeout(function(){jQuery('[id=" . $this->opts['id'] . "]').closest('.rminput,.rmwc-input').find('.selected-flag').addClass('disable-flag')},3000)});");
                }
            }
            
        }
        else if($this->field_options->format_type=='local'){
            if(empty($this->field_options->field_placeholder)){
                $this->opts['placeholder']= '(000)-000-0000';
                $this->opts['pattern']= '.{14}';
            }
            
            wp_enqueue_script("rm_mask_script", RM_BASE_URL . "public/js/jquery.mask.min.js");
            if($embed){
                echo "<script>jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').mask('(000)-000-0000')});</script>";
            }
            else
            {
                wp_add_inline_script('rm_mask_script',"jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').mask('(000)-000-0000')});");
            }
             
        }
        else if($this->field_options->format_type=='custom'){
              if(empty($this->field_options->field_placeholder)){
                $this->opts['placeholder']= $this->field_options->custom_mobile_format;
                $min_length= strlen($this->field_options->custom_mobile_format);
                $this->opts['pattern']= '.{'.$min_length.'}';
            }
            wp_enqueue_script("rm_mask_script", RM_BASE_URL . "public/js/jquery.mask.min.js");
             if($embed){
                 echo "<script>jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').mask('".$this->field_options->custom_mobile_format."')});</script>";
             }
             else
             {
                 wp_add_inline_script("rm_mask_script","jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').mask('".$this->field_options->custom_mobile_format."')});");
             }
            

        }
        else if($this->field_options->format_type=='limited'){
            wp_enqueue_script("rm_mobile_data_script", RM_BASE_URL . "public/js/mobile_field/data.js");
            wp_enqueue_script("rm_mobile_script", RM_BASE_URL . "public/js/mobile_field/intlTelInput.js",array('jquery'));
            wp_enqueue_style("rm_mobile_script", RM_BASE_URL . "public/css/mobile_field/intlTelInput.css");
            $util_js= RM_BASE_URL . "public/js/mobile_field/utils.js";
            $tel_params = '{';
            if(!empty($this->field_options->lim_countries)){
                  $countries= explode(',', $this->field_options->lim_countries);
                  if(is_array($countries)){
                      $limited_countries= '[';
                      foreach($countries as $country){
                        $limited_countries .= '"'.strtolower(RM_Utilities::get_country_code($country)).'",';
                      }
                      $tel_params .= 'onlyCountries:'.rtrim($limited_countries, ',').'],';
                  }
            }
              
            if(!empty($this->field_options->lim_pref_countries)){
                  $countries= explode(',', $this->field_options->lim_pref_countries);
                  if(is_array($countries)){
                      $preferred_countries= '[';
                      foreach($countries as $country){
                        $preferred_countries .= '"'.strtolower(RM_Utilities::get_country_code($country)).'",';
                      }
                      $tel_params .= 'preferredCountries:'.rtrim($preferred_countries, ',').'],';
                  }
            }
            $tel_params.= 'utilsScript:"'.$util_js.'"}';
            if($embed){
                echo "<script>jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').intlTelInput($tel_params);});</script>";
                echo "<script>jQuery(document).ready(function(){var el= jQuery('#" . $this->opts['id'] . "'); var el= jQuery('#" . $this->opts['id'] . "');  var form= el.closest('.rmagic-form'); form.submit(function(event){el.prop('pattern',''); if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else {return rm_toggle_tel_wc_error(true,el,''); } el.val(el.intlTelInput('getNumber'));});});</script>";
            }
            else{
                wp_add_inline_script("rm_mobile_script","jQuery(document).ready(function(){jQuery('#" . $this->opts['id'] . "').intlTelInput($tel_params); var el= jQuery('#" . $this->opts['id'] . "'); var form= el.closest('.rmagic-form'); form.submit(function(event){el.prop('pattern',''); if(!el.intlTelInput('isValidNumber')){ return rm_toggle_tel_wc_error(false,el,'$error_msg'); return false; } else {return rm_toggle_tel_wc_error(true,el,''); } el.val(el.intlTelInput('getNumber'));});});"); 
            }
           
            $this->opts['data-mobile-intel-field']= '1';
        }
        else{
            $validate = new Validation_RegExp("/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/", RM_UI_Strings::get("MOBILE_ERROR"));
            $this->opts['validation'] = $validate;
            $this->opts['Pattern'] = "^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$";
           
        }
        $field = $this->create_base_field();
        
       
        return $field;
    }
    
    public function create_privacy_field(){
        $gopts= new RM_Options();
        $class=  $this->field_options->field_css_class;
        $content=  $this->field_options->privacy_policy_content;
        $url = 'javascript:void(0)';
        if(!empty($this->field_options->privacy_policy_page)){
            $url= get_permalink($this->field_options->privacy_policy_page);
        }
        $content = str_replace('{{privacy_policy}}',"<a target='_blank' href='$url'>".__('Privacy Policy','custom-registration-form-builder-with-submission-manager')."</a>",$content);
        $icon='';
        $field_label='';
        if (isset($this->field_options->icon) && $this->field_options->icon->codepoint)
        {
            if ($this->field_options->icon->shape == 'square')
                $radius = '0px';
            else if ($this->field_options->icon->shape == 'round')
                $radius = '100px';
            else if ($this->field_options->icon->shape == 'sticker')
                $radius = '4px';

            $bg_r = intval(substr($this->field_options->icon->bg_color, 0, 2), 16);
            $bg_g = intval(substr($this->field_options->icon->bg_color, 2, 2), 16);
            $bg_b = intval(substr($this->field_options->icon->bg_color, 4, 2), 16);
            $bg_a = isset($this->field_options->icon->bg_alpha) ? $this->field_options->icon->bg_alpha : 1;
            $icon_style = "style=\"padding:5px;color:#{$this->field_options->icon->fg_color};background-color:rgba({$bg_r},{$bg_g},{$bg_b},{$bg_a});border-radius:{$radius};\"";
            $field_label= '<span><i class="material-icons rm_front_field_icon"' . $icon_style . ' id="id_show_selected_icon">' . $this->field_options->icon->codepoint . ';</i></span>';
        } 
        $checkbox= '';
        
        if(!empty($this->field_options->privacy_display_checkbox)){
            $checkbox = "<input class='rm_privacy_cb' required type='checkbox' />"; 
        }
        $html= "<div class='rmrow rm-privacy-row rm-full-width $class'><span class='rm-privacy-icon'><span>$icon</span><span>$field_label</span><span>$checkbox</span></span>$content</div>";
        return new RM_Frontend_Field_Visible_Only($this->db_field->field_id,'HTMLCustomized','',$this->field_name, $this->opts,$html, $this->db_field->page_no, $this->db_field->is_field_primary, $this->x_opts);
    }
   
}