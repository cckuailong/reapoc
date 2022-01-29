<?php
class Element_UserEmail extends Element_Textbox {
	public $_attributes = array("type" => "email");

	public function render() {
		$this->validation[] = new Validation_Email;
		parent::render();
	}
        
        public function jQueryDocumentReady() {
          $form_id_array= explode('_', $this->_form->form_slug); 
          
          // Form int ID will always be on scond index eg: form_52_1
          $form_id= (int) $form_id_array[1];
          echo <<<JS
            
                   
                   jQuery("#{$this->_attributes['id']}").change(function(){
                   var data = {
                           'action': 'rm_user_exists',
                           'rm_slug': 'rm_user_exists',
                           'email': jQuery(this).val(),
                           'attr': 'data-rm-valid-email',
                           'form_id':"{$form_id}"
                   };
                   
                   rm_user_exists(this,rm_ajax_url,data);
                  
                 });
           
JS;
            
            $is_wc_fields = 0;
            $email_as_primary = 0;
            $service = new RM_Services();
            $fields = $service->get_all_form_fields($form_id);
            foreach($fields as $field){
                if($field->field_type=='WCBilling'){
                    $is_wc_fields = 1;
                    
                    $f_model= new RM_Fields();
                    $f_model->load_from_db($field->field_id);
                    $f_options= maybe_unserialize($f_model->get_field_options());
                    if($f_options->field_wcb_email_as_primary){
                        $email_as_primary = 1;
                    }
                }else if($field->field_type=='WCShipping'){
                    $is_wc_fields = 1;
                }else if($field->field_type=='WCBillingPhone'){
                    $is_wc_fields = 1;
                }
            }
            
            if($is_wc_fields==1 && $email_as_primary==1){
                $form = new RM_Forms();
                $form->load_from_db($form_id);
                
                if(!is_user_logged_in()){
                    echo <<<JS
                       jQuery("#{$this->_attributes['id']}").parent('.rminput').parent('.rmrow').hide();
                       jQuery("#{$this->_attributes['id']}").addClass('rm_wc_hidden_email');
JS;
                }else if(is_user_logged_in() && $form->get_form_type()!=1){
                    echo <<<JS
                       jQuery("#{$this->_attributes['id']}").parent('.rminput').parent('.rmrow').hide();
                       jQuery("#{$this->_attributes['id']}").addClass('rm_wc_hidden_email');
JS;
                }
            }
        
        }
}
