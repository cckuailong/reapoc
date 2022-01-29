<?php
class Validation_File extends Validation {
    public  $name;
    public $formId;
    public $isRequiredFileField;
    public static $attachment_ids= array();
    
    public function __construct($message = "", $is_required=false) {
		if(!empty($message))
			$this->message = $message;
        else
            $this->message = RM_UI_Strings::get('FORM_ERR_FILE_TYPE'); 

       $this->isRequiredFileField = $is_required;
                
	}

    public function setFormId($formId){
        $this->formId= $formId;

    }

    public function setName($name){

        if(stristr($name,'[') && stristr($name,']')){
            $this->name= substr($name,0,strlen($name)-2);
        }else{
            $this->name= $name;
        }

    }

    public function isValid($value) {
        $valid= true;
        $field_id = '';
        $name_explode = explode("_", $this->name);
        if(is_array($name_explode) && isset($name_explode[1]))
        {
            $field_id = $name_explode[1];
        }
        
        //var_dump($name_explode);die;
        $field = new RM_Fields();
        $gopts = new RM_Options();
        $field->load_from_db($field_id);
        

        //Sanitize value before use, remove white space, trim extra pipes.
        $fv = explode("|",trim(preg_replace('/\s+/', '', strtolower($field->get_field_value())),'|'));
        $allowed_types = array();
        
        $multiple = $gopts->get_value_of('allow_multiple_file_uploads');

        if(!$fv || (count($fv)===1 && $fv[0]==""))
            $allowed_types = explode("|",  strtolower ($gopts->get_value_of('allowed_file_types')));
        else
        {            
            foreach ($fv as $key => $value) 
                $fv[$key] = strtolower($value);
            
            $allowed_types = $fv;
        }

        //die;
        if($multiple=="yes")
        {
            if(isset($_FILES[$this->name]) && !empty($_FILES[$this->name]))
            {
                foreach($_FILES[$this->name]['name'] as $filename)
                {
                    if($filename == "")
                    {
                        if(!$this->isRequiredFileField)
                            return true;
                        else
                        {
                            $this->message = " %element% ".RM_UI_Strings::get('ERROR_REQUIRED');
                            return false;
                        }
                    }
                    // ".files" type names (starting with a dot) not allowed.
                    elseif(strpos( $filename, '.') === 0)
                        return false;
                    //"files" type names (without extension) not allowed.
                    elseif(strpos( $filename, '.') === false)
                        return false;
                    else 
                    {
                        $arrx = explode('.',$filename);
                        $ext = $arrx[count($arrx)-1];

                        if($ext == "")
                            return false;

                        $ext = strtolower($ext);

                        if(!in_array($ext, $allowed_types))
                            return false;
                    }                    

                }

                return true;               
            }
            return true;
        }
        else
        {
            if(isset($_FILES[$this->name]) && !empty($_FILES[$this->name]))
            {
                
                $filename = $_FILES[$this->name]['name'];
                
                if($filename == "")
                    {
                        if(!$this->isRequiredFileField)
                            return true;
                        else
                        {
                            $this->message = " %element% ".RM_UI_Strings::get('ERROR_REQUIRED');
                            return false;
                        }
                    }
                // ".files" type names (starting with a dot) not allowed.
                elseif(strpos( $filename, '.') === 0)
                    return false;
                //"files" type names (without extension) not allowed.
                elseif(strpos( $filename, '.') === false)
                    return false;
                else 
                {
                    $arrx = explode('.',$filename);
                    $ext = $arrx[count($arrx)-1];

                    if($ext == "")
                        return false;
                    
                    $ext = strtolower($ext);

                    if(!in_array($ext, $allowed_types))
                        return false;    
                }                      
                return true;
            }
            return true;
        }
        return false;
    }

    function set_error($error){
        if(isset($error) && !empty($error)){
            foreach($error->errors as $error){
                RM_PFBC_Form::setError($this->formId, $error[0]);
            }
        }
    }

    public function isTypeAllowed($type){
        $valid= false;
        $allowed_types= explode('|',get_option('rm_option_allowed_file_types'));
        foreach($allowed_types as $ex){
            if(!empty($ex)){
                if(stristr($type,$ex))
                {
                    $valid= true;
                }
            }

        }
        return $valid;
    }
    
    public function getMessage() {
		return $this->message;
	}
}
