<?php
class Element_File extends Element_FileNative {
	public $_attributes = array("type" => "file");
        
   public function __construct($label, $name, array $properties = null) {
       parent::__construct($label, $name, $properties);
       if($this->isRequired())
            $this->validation[] = new Validation_File("", true);
       else
            $this->validation[] = new Validation_File;
   }    
   
    public function jQueryDocumentReady(){  
        
        // Set form encryption type to enable the file upload
        echo <<<JS
            
                   
                 var formInput= jQuery("#{$this->_attributes['id']}");
                 var form = jQuery(formInput[0].form);
                 var enctype= jQuery(form).prop('enctype','multipart/form-data');
           
JS;
    }     
	
    public function render() {
        $multiple= get_option('rm_option_allow_multiple_file_uploads');
        if($multiple=="yes" && !isset($this->_attributes['multiple'])){
            $this->_attributes['multiple']= "multiple";
            $this->_attributes['name']= $this->_attributes['name'].'[]';
        }
        elseif(isset($this->_attributes['multiple']))
        {
            $this->_attributes['name']= $this->_attributes['name'].'[]'; 
        }
        else
        {
            
        }
//        if($this->isRequired())
//            $this->validation[] = new Validation_File("", true);
//        else
//            $this->validation[] = new Validation_File;
        
        parent::render();
    }
}
