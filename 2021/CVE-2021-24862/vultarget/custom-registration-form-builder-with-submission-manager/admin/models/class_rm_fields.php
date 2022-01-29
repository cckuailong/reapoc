<?php

/**
 * Form field model
 * 
 * This class represents the model for a form's fields and has the properties 
 * of a field and also have the DB operations for the model 
 *
 * @author cmshelplive
 */
class RM_Fields extends RM_Base_Model
{

    public $field_id;
    public $form_id;
    public $page_no;
    public $field_label;
    public $field_type;
    public $field_value;
    public $field_order;
    public $field_options;
    public $is_field_primary;
    public $field_show_on_user_page;
    public $is_deletion_allowed=1;
    public $field_is_editable;
    public $valid_options;

    //public $initialized;
    //errors of field data validation
    public $errors;

    public function __construct()
    {
        $this->initialized = false;
        $this->field_id = NULL;
        $this->field_order = 99999999;
        $this->field_is_editable = 0;
        $this->valid_options = array('field_is_multiline','field_placeholder', 'field_timezone', 'field_max_length', 'field_is_required_range', 'field_is_required_max_range', 'field_is_required_min_range', 'field_is_required_scroll', 'field_check_above_tc', 'field_default_value', 'field_css_class', 'field_textarea_columns', 'field_textarea_rows', 'field_is_required','field_is_show_asterix', /*'field_is_required_scroll',*/'field_is_read_only', 'field_is_other_option', 'help_text', 'icon','field_validation','custom_validation', 'tnc_cb_label', 'date_format','field_is_unique','un_err_msg', 'rating_conf','conditions','field_meta_add','conditions','link_same_window','link_page','link_href','link_type','yt_auto_play','yt_repeat','yt_related_videos','yt_player_width','rm_widget','yt_player_height','if_width','if_height',
                                     'field_address_type','field_ca_address1_label','field_ca_address2_label','field_ca_city_label','field_ca_state_label','field_ca_country_label','field_ca_zip_label','field_ca_city_en','field_ca_state_en','field_ca_zip_en','field_ca_country_en','field_ca_address1_en','field_ca_address2_en','ca_state_type',
                                    'field_ca_address1_req','field_ca_city_req','field_ca_zip_req','field_ca_country_req','field_ca_state_req','field_ca_address2_req','field_ca_label_as_placeholder','field_ca_lmark_label','field_ca_lmark_en','field_ca_lmark_req','field_ca_state_codes','field_ca_country_america_can','field_ca_country_limited','field_ca_en_country_search','img_caption_enabled','img_title_enabled','img_link_enabled','img_effects_enabled','border_color','border_width','border_shape','img_pop_enabled','img_size','lat','long','zoom','width','nu_form_views','nu_views_text_before','nu_views_text_after','nu_sub_text_after','nu_sub_text_before','nu_submissions','sub_limits','sub_limit_text_before','sub_limit_text_after','sub_date_limits','sub_date_limit_text_before','sub_date_limit_text_after','last_sub_rec','ls_text_before','ls_text_after','show_form_name','form_desc','sub_limit_ind','custom_value','hide_country','hide_date','show_gravatar','max_items','time_range',
                                    'user_exists_error','username_characters','invalid_username_format','en_confirm_pwd','pass_mismatch_err','en_confirm_email','email_mismatch_err','en_pass_strength','pwd_strength_type','pwd_short_msg','pwd_weak_msg','pwd_medium_msg','pwd_strong_msg','format_type','preferred_countries','en_geoip','custom_mobile_format','lim_countries','lim_pref_countries','mobile_err_msg','country_field','sync_country','country_match',
                                    'privacy_policy_page','privacy_policy_content','privacy_display_checkbox','field_user_profile','existing_user_meta_key',
                                    'field_wcb_email_as_primary','field_wcb_firstname_label','field_wcb_lastname_label','field_wcb_company_label','field_wcb_address1_label','field_wcb_address2_label','field_wcb_phone_label','field_wcb_email_label','field_wcb_city_label','field_wcb_state_label','field_wcb_country_label','field_wcb_zip_label','field_wcb_city_en','field_wcb_state_en','field_wcb_zip_en','field_wcb_country_en','field_wcb_firstname_en','field_wcb_lastname_en','field_wcb_company_en','field_wcb_address1_en','field_wcb_address2_en','field_wcb_phone_en','field_wcb_email_en','wcb_state_type','field_wcb_firstname_req','field_wcb_lastname_req','field_wcb_company_req','field_wcb_address1_req','field_wcb_phone_req','field_wcb_email_req','field_wcb_city_req','field_wcb_zip_req','field_wcb_country_req','field_wcb_state_req','field_wcb_address2_req','field_wcb_label_as_placeholder','field_wcb_lmark_label','field_wcb_lmark_en','field_wcb_lmark_req','field_wcb_state_codes','field_wcb_country_ameriwcb_can','field_wcb_country_limited','field_wcb_en_country_search',
                                    'field_wcs_firstname_label','field_wcs_lastname_label','field_wcs_company_label','field_wcs_address1_label','field_wcs_address2_label','field_wcs_city_label','field_wcs_state_label','field_wcs_country_label','field_wcs_zip_label','field_wcs_city_en','field_wcs_state_en','field_wcs_zip_en','field_wcs_country_en','field_wcs_address1_en','field_wcs_address2_en','wcs_state_type','field_wcs_firstname_en','field_wcs_lastname_en','field_wcs_company_en','field_wcs_address1_req','field_wcs_city_req','field_wcs_zip_req','field_wcs_country_req','field_wcs_state_req','field_wcs_firstname_req','field_wcs_lastname_req','field_wcs_company_req','field_wcs_address2_req','field_wcs_label_as_placeholder','field_wcs_lmark_label','field_wcs_lmark_en','field_wcs_lmark_req','field_wcs_state_codes','field_wcs_country_ameriwcs_can','field_wcs_country_limited','field_wcs_en_country_search');
        $this->field_options = new stdClass;
        $this->initialize_options();
    }
    
    public function get_valid_options() {
        return $this->valid_options;
    }
    
    public function initialize_options(){
        foreach ($this->valid_options as $valid_option)
            $this->field_options->$valid_option = null;
    }
    /*     * *getters** */
    
    public static function get_identifier()
    {
        return 'FIELDS';
    }

    public function get_is_field_primary()
    {
        return $this->is_field_primary;
    }

    public function get_field_id()
    {
        return $this->field_id;
    }

    public function get_form_id()
    {
        return $this->form_id;
    }

    public function get_field_label()
    {
        return $this->field_label;
    }
    
    public function get_field_is_editable(){
        return $this->field_is_editable;
    }
    
    public function set_field_is_editable($field_is_editable){
        if(is_array($field_is_editable))
            $this->field_is_editable = count($field_is_editable);
        else
            $this->field_is_editable = $field_is_editable;
    }

    public function get_field_type()
    {
        return $this->field_type;
    }

    public function get_field_value()
    {
        return maybe_unserialize($this->field_value);
    }

    public function get_field_order()
    {
        return $this->field_order;
    }

    public function get_field_options()
    {
        $options_serialized = maybe_serialize($this->field_options);
        return $options_serialized;
    }

    public function get_field_default_value()
    {
        return maybe_unserialize($this->field_options->field_default_value);
    }
    
    public function get_field_show_on_user_page(){
        return $this->field_show_on_user_page;
    }
    
    public function get_page_no()
    {
        return $this->page_no;
    }
    
    public function get_field_meta_add(){   
             return $this->field_options->field_meta_add;
     }

    
    /*     * *setters** */
    
    public function set_page_no($page_no)
    {
        $this->page_no = $page_no;
    }
        
    public function set_field_default_value($field_default_value)
    {
        $this->field_options->field_default_value = maybe_serialize($field_default_value);
    }

    public function set_field_show_on_user_page($field_show_on_user_page)
    {
        if(is_array($field_show_on_user_page))
            $this->field_show_on_user_page = count($field_show_on_user_page);
        else
            $this->field_show_on_user_page = $field_show_on_user_page;
            
    }
    
    public function set_is_deletion_allowed($allowed)
    {
        $this->is_deletion_allowed= $allowed;
    }
    
    public function set_is_field_primary($is_field_primary)
    {
        $this->is_field_primary = $is_field_primary;
    }

    public function set_field_id($field_id)
    {
        $this->field_id = $field_id;
    }

    public function set_form_id($form_id)
    {
        $this->form_id = $form_id;
    }

    public function set_field_label($label)
    {
        $this->field_label = $label;
    }

    public function set_field_type($type)
    {
        $this->field_type = $type;
    }

    public function set_field_value($value)
    {
        $this->field_value = maybe_serialize($value);
    }

    public function set_field_order($order)
    {
        $this->field_order = $order;
    }
 
    public function set_field_meta_add($meta_key)
     {       
           $this->field_options->field_meta_add= sanitize_key(wp_unslash($meta_key));
     }
    
    public function set_field_options($options)
    {
        $field_options = maybe_unserialize($options);
        $this->field_options = RM_Utilities::merge_object($field_options, $this->field_options);
    }

    public function set(array $request)
    {   
        foreach ($request as $property => $value)
        {
            $set_property_method = 'set_' . $property;

            if (method_exists($this, $set_property_method))
            {
                $this->$set_property_method($value);
            } elseif (in_array($property, $this->valid_options, true))
            {
                //checkbox type options are returned as array by PFBC, convert to boolean.
                if (is_array($value)){
                     $value= count($value);
                }
                   
                $this->field_options->$property = $value;
            }
        }
        
        return $this->initialized = true;
    }

    /*     * **Validations*** */

    public function validate_form_id()
    {
        if (empty($this->form_id))
        {
            $this->errors['FORM_ID'] = __("No Form ID defined",'custom-registration-form-builder-with-submission-manager');
        }
        if (!is_int($this->form_id))
        {
            $this->errors['FORM_ID'] = __("Not a valid Form ID",'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function validate_label()
    {
        if (empty($this->field_label))
        {
            $this->errors['LABEL'] =__("Label can not be empty.",'custom-registration-form-builder-with-submission-manager');
        }
        if (!is_string($this->field_label))
        {
            $this->errors['LABEL'] = __("Label must be a string.",'custom-registration-form-builder-with-submission-manager');
        }
        if (preg_match('/[^a-zA-Z0-9_\-\.]/', $this->field_label))
        {
            $this->errors['LABEL'] = __("Label can not contain special characters.",'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function validate_type()
    {

        if (empty($this->field_type))
        {
            $this->errors['TYPE'] = __("Field type can not be empty.",'custom-registration-form-builder-with-submission-manager');
        }

        //validation of field_type data
    }

    public function validate_value()
    {
        //validations for value of field; 
    }

    public function validate_order()
    {
        if (empty($this->field_order))
        {
            $this->errors['ORDER'] = __("Field order can not be empty.",'custom-registration-form-builder-with-submission-manager');
        }
        if (is_int($this->field_order))
        {
            $this->errors['ORDER'] = __("Invalid order.",'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function is_valid()
    {
        $this->validate_form_id();
        $this->validate_label();
        $this->validate_type();

        return count($this->errors) === 0;
    }

    public function errors()
    {
        return $this->errors;
    }

    /*     * **Database Operations*** */

    public function insert_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }

        if ($this->field_id)
        {
            return false;
        }

        $data = array(
            'form_id' => $this->form_id,
            'page_no' => $this->page_no,
            'field_label' => $this->field_label,
            'field_type' => $this->field_type,
            'field_value' => $this->field_value,
            'field_order' => $this->field_order,
            'field_show_on_user_page' => $this->field_show_on_user_page,
            'is_deletion_allowed'=>$this->is_deletion_allowed,
            'field_is_editable' => $this->field_is_editable,
            'is_field_primary' => $this->is_field_primary?$this->is_field_primary:0,
            'field_options' => $this->get_field_options(),
        );

        $data_specifiers = array(
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%s'
        );

        $result = RM_DBManager::insert_row('FIELDS', $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        $this->field_id = $result;

        return $result;
    }

    public function update_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }
        if (!$this->field_id)
        {
            return false;
        }
        
        $data = array(
            'form_id' => $this->form_id,
            'page_no' => $this->page_no,
            'field_label' => $this->field_label,
            'field_type' => $this->field_type,
            'field_value' => $this->field_value,
            'field_is_editable' => $this->field_is_editable,
            'is_deletion_allowed'=>$this->is_deletion_allowed,
            'field_show_on_user_page' => $this->field_show_on_user_page,
            'field_options' => $this->get_field_options()
        );

        $data_specifiers = array(
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%s'
        );

        $result = RM_DBManager::update_row('FIELDS', $this->field_id, $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        return true;
    }

    public function load_from_db($field_id, $should_set_id = true)
    {

        $result = RM_DBManager::get_row('FIELDS', $field_id);

        if (null !== $result)
        {
            if ($should_set_id)
                $this->field_id = $field_id;
            else
                $this->field_id = null;
            $this->form_id = $result->form_id;
            $this->page_no = $result->page_no;
            $this->field_label = $result->field_label;
            $this->field_type = $result->field_type;
            $this->field_value = $result->field_value;
            $this->field_order = $result->field_order;
            $this->is_field_primary = $result->is_field_primary;
            $this->field_is_editable = $result->field_is_editable;
            $this->field_show_on_user_page = $result->field_show_on_user_page;
            $this->is_deletion_allowed= isset($result->is_deletion_allowed) ? $result->is_deletion_allowed : 1;
            $this->set_field_options($result->field_options);
        } else
        {
            return false;
        }
        $this->initialized = true;
        return true;
    }

    public function remove_from_db()
    {
        return RM_DBManager::remove_row('FIELDS', $this->field_id);
    }
    
    public function remove_conditions(){
        if(isset($this->field_options->conditions)){
            unset($this->field_options->conditions);
            $this->update_into_db();
        }
    }
    
    public function get_field_conditions(){
        if(isset($this->field_options->conditions))
            return $this->field_options->conditions;
        return array("rules"=>array(),"settings"=>array());
    }
    
    public function set_conditions($value){
        $this->field_options->conditions= $value;
    }
    
    public function set_username_characters($value){
        $this->field_options->username_characters= $value;
    }

}
