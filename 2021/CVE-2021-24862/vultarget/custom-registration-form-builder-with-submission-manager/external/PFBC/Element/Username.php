<?php
class Element_Username extends Element_Textbox {
	public function getJSFiles() {

	}
	public function render() {
		
		parent::render();              
	}
        
        
        public function jQueryDocumentReady() {    
          $form_id_array= explode('_', $this->_form->form_slug); 
          
          // Form int ID will always be on scond index eg: form_52_1
          $form_id= (int) $form_id_array[1];  
          $validation_msg= RM_UI_Strings::get("USERNAME_EXISTS");  
          echo <<<JS
            
                   
                   jQuery("#{$this->_attributes['id']}").change(function(){
                   var data = {
                           'action': 'rm_user_exists',
                           'rm_slug': 'rm_user_exists',
                           'username': jQuery(this).val(),
                           'attr': 'data-rm-valid-username',
                           'form_id':"{$form_id}"
                   };
                   
                   rm_user_exists(this,rm_ajax_url,data,"{$validation_msg}");
                  
                 });
           
JS;
            
        
        }
       
}
