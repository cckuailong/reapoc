<?php

/*
 * Service class to handle Mailchimp operations
 *
 *
 */

class RM_MailChimp_Service {

    public $mailChimp_id;
    public $mailchimp;

    public function __construct() {
        $this->mailChimp_id = get_option('rm_option_mailchimp_key');
         try
       {
   $this->mailchimp = new RM_MailChimp($this->mailChimp_id);
       }
        catch(Exception $e)
             {
                 $this->mailchimp=null;
             } 
    }

    /*
     * list all the mailing lists
     */

    public function get_list() {
        
         if(isset($this->mailchimp))
     {
          $result = $this->mailchimp->get('lists');
        return $result;
     }
     else
         return null;
    }

    public function get_list_field($id) {
        if(isset($this->mailchimp)) {
            $result = $this->mailchimp->get('lists/' . $id . '/merge-fields');
        }
        if(defined('REGMAGIC_ADDON')) {
            return $result;
        }
        if(isset($result['merge_fields']))
        {
            foreach($result['merge_fields'] as $index => $mfield)
            {
                if($mfield['tag'] != 'FNAME' && $mfield['tag'] != 'LNAME')
                unset($result['merge_fields'][$index]);
            }        
        }
        
        return $result;
    }

    /*
     * Subscribe someone to a list (with a post to the list/{listID}/members method):
     */

    public function subscribe($merge, $email, $list_id, $user_status = 'subscribed') {
        if (count($merge) == 0)
            $data = array(
                'email_address' => $email,
                'status' => $user_status
            );
        else
            $data = array(
                'email_address' => $email,
                'status' => $user_status,
                'merge_fields' => $merge
            );
        if( defined( 'ICL_LANGUAGE_CODE' ) ){
            $data['language'] = ICL_LANGUAGE_CODE;
        }
        if(isset($this->mailchimp)) {
            $result = $this->mailchimp->post("lists/$list_id/members", $data);
            return $result;
        } else
         return null;
    }

    public function update_member_info($member, $list_id) {
        /*
          $subscriber_hash = $this->mailchimp->subscriberHash($member->email);

          $result = $this->mailchimp->patch("lists/$list_id/members/$subscriber_hash", [
          'merge_fields' => ['FNAME'=>'Davy', 'LNAME'=>'Jones'],
          'interests'    => ['2s3a384h' => true],
          ]);

          return $result;
         */
    }

    public function delete($member_email, $list_id) {
        $subscriber_hash = $this->mailchimp->subscriberHash($member_email);

        $this->mailchimp->delete("lists/$list_id/members/$subscriber_hash");
    }

    //Maps maichimp fields for a form
    public function get_mailchimp_mapping($options) {
        $details = $this->get_list_field($options['mailchimp_list']);
        $mailchimp_relations = new stdClass();
        if (isset($details['merge_fields'])) {
            foreach ($details['merge_fields'] as $det) {
                $mc_tag = trim($det['tag']);
                $mc_list_id_tag = $options['mailchimp_list'] . '_' . $mc_tag;
                $mc_list_id_tag = trim($mc_list_id_tag);
    
                if(isset($options[$mc_list_id_tag]))
                    $mailchimp_relations->$mc_list_id_tag = $options[$mc_list_id_tag];
            }
                //$form_instance->mailchimp_mapped_email=$request->req['email'];
        }
        return $mailchimp_relations;
    }
    
    public function mc_field_mapping($form, $form_options, $list = null)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_MailChimp_Service_Addon();
            return $addon_service->mc_field_mapping($form, $form_options, $list, $this);
        }
        if(!$list)
        {
            $list = $form_options->mailchimp_list;
        }
        $mailchimp = new RM_MailChimp_Service;
        $details = $this->get_list_field($list);
        $service = new RM_Services();
        $all_field_objects = $service->get_all_form_fields($form);
        if (is_array($all_field_objects) || is_object($all_field_objects))
            $form_fields = '';
        $form_fields_email = '';
        $field_type_array = array();
        $field_type_array['text'] = array();
        $field_type_array['number'] = array();
        $field_type_array['dropdown'] = array();
        $field_type_array['radio'] = array();
        $field_type_array['date'] = array();
        $field_type_array['phone'] = array();
        foreach ($all_field_objects as $obj) {
            if ($obj->field_type == 'Email') {
                $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                if ($form_type_id == $form_options->mailchimp_mapped_email)
                    $form_fields_email .='<option value=' . $form_type_id . ' selected>' . $obj->field_label . '</option>';
                else
                    $form_fields_email .='<option value=' . $form_type_id . '>' . $obj->field_label . '</option>';
                //$data->all_fields[$obj->field_type . '_' . $obj->field_id] = $obj->field_label;
            }
            $field_type = $obj->field_type;


            switch ($field_type) {
                case 'Textbox':
                case 'HTMLP':
                case 'Country':
                case 'Terms':
                case 'Fname':
                case 'Lname':
                case 'BInfo':
                case 'Email':
                    $field_type = 'text';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;

                case 'Select':
                    $field_type = 'dropdown';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Radio':
                    $field_type = 'radio';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'jQueryUIDate':
                    $field_type = 'date';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Number':
                    $field_type = 'number';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Price':
                    $field_type = 'text';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
                case 'Phone':
                    $field_type = 'phone';
                    $form_type_id = $obj->field_type . '_' . $obj->field_id; //
                    $field_type_array[$field_type][$form_type_id] = $obj->field_label;
                    break;
            }
            //$data->all_fields[$obj->field_type . '_' . $obj->field_id] = $obj->field_label;
        }


        $content = '<div class="rmrow">
                            <div class="rmfield">
                                     <label> <b>'.__("Email",'custom-registration-form-builder-with-submission-manager').'</b></label>
                                     </div>
                            <div class="rminput">
                                     <Select name="email" selected="' . $form_options->mailchimp_mapped_email . '"><option value="">' . RM_UI_Strings::get('SELECT_FIELD') . '</option>' . $form_fields_email . '</Select>'
                . '</div>'
                . '</div>';

        if($details && isset($details['merge_fields']))
        foreach ($details['merge_fields'] as $det) {
            $options = '<option value="">' . RM_UI_Strings::get('SELECT_FIELD') . '</option>';
            foreach ($field_type_array as $type => $fld) {

                if ($fld != null && $type == $det['type']) {
                    foreach ($fld as $field_type_id => $field_type_value) {

                        if (is_object($form_options->mailchimp_relations) && $field_type_id == $form_options->mailchimp_relations->{$list . '_' . $det['tag']}) {

                            $options .='<option value="' . $field_type_id . '" selected>' . $field_type_value . '</option>';
                        } else {
                            $options .='<option value="' . $field_type_id . '">' . $field_type_value . '</option>';
                        }
                    }
                }
            }


            $content .='<div class="rmrow">
                            <div class="rmfield">
                                     <label> <b>' . $det['name'] . '</b></label>
                            </div>
                            <div class="rminput">
                                     <Select name="' . $list . '_' . $det['tag'] . '">' . $options . '</Select>'
                    . '</div>'
                    . '</div>';
        }
        return $content;

    }

}
