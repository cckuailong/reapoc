<?php
abstract class Element extends Base {
	public $_errors = array();
	public $_attributes = array();
	public $_form;

	public $label;
	public $shortDesc;
	public $longDesc;
	public $validation = array();
        public $advance_opts = array('exclass_row' => '', //This class will be added along with rmrow.
                                        'exclass_field' => '', //This class will be added along with rmfield.
                                        'exclass_input' => '', //This class will be added along with rminput.
                                        'sub_element' => null); //A child element nested within.
        
	public function __construct($label, $name, array $properties = null, array $advance_opts = null) {
		$configuration = array(
			"label" => $label,
			"name" => $name
		);
                
                //Setup advanced options for the field.
                if(is_array($advance_opts))
                    $this->advance_opts = array_merge($this->advance_opts, $advance_opts);
                
                if(isset($properties['class'],$this->_attributes['class'])){
                     $properties['class'] .= ' '.$this->_attributes['class'];
                }
		/*Merge any properties provided with an associative array containing the label
		and name properties.*/
		if(is_array($properties))
			$configuration = array_merge($configuration, $properties);
		
		$this->configure($configuration);
	}

	/*When an element is serialized and stored in the session, this method prevents any non-essential
	information from being included.*/
	public function __sleep() {
		return array("_attributes", "label", "validation");
	}

	/*If an element requires external stylesheets, this method is used to return an
	array of entries that will be applied before the form is rendered.*/
	public function getCSSFiles() {}

	public function getErrors() {
		return $this->_errors;
	}	

	/*If an element requires external javascript file, this method is used to return an
	array of entries that will be applied after the form is rendered.*/
	public function getJSFiles() {}
        
        //Function to load dependency of a script
        public function getJSDeps() {return false;}

	public function getLabel() {
		return $this->label;
	}

	public function getLongDesc() {
		return $this->longDesc;
	}
        
        public function getAdvanceAttr($att_name = null) {
		return !$att_name ? $this->advance_opts :
                        (isset($this->advance_opts[$att_name]) ? $this->advance_opts[$att_name] : null);
	}

	/*This method provides a shortcut for checking if an element is required.*/
	public function isRequired() {
		if(!empty($this->validation)) {
			foreach($this->validation as $validation) {
				if($validation instanceof Validation_Required)
					return true;
			}
		}
		return false;
	}

	public function getShortDesc() {
		return $this->shortDesc;
	}
         public function show_asterix() {
            $options=new RM_Options;
	    return $options->get_value_of('show_asterix');
           
	}
	/*The isValid method ensures that the provided value satisfies each of the 
	element's validation rules.*/
	public function isValid($value,$form_id) {
		$valid = true;
		if(!empty($this->validation)) {
			if(!empty($this->label))
				$element = $this->label;
			elseif(!empty($this->_attributes["placeholder"]))
				$element = $this->_attributes["placeholder"];
			else
				$element = $this->_attributes["name"];

			if(substr($element, -1) == ":")
				$element = substr($element, 0, -1);

			//Make element label bold and quoted
			$element = "<b>'".$element."'</b>";

			foreach($this->validation as $validation) {
				// Check if it is related to file upload operation
				if($validation instanceof Validation_File){
					$validation->setName($this->getAttribute('name'));

                    $validation->setFormId($form_id);
				}
				if(!$validation->isValid($value)) {
					/*In the error message, %element% will be replaced by the element's label (or 
					name if label is not provided).*/
					$this->_errors[] = str_replace("%element%", $element, $validation->getMessage());
					$valid = false;
				}	
			}
		}
		return $valid;
	}

	/*If an element requires jQuery, this method is used to include a section of javascript
	that will be applied within the jQuery(document).ready(function() {}); section after the 
	form has been rendered.*/
	public function jQueryDocumentReady() {}

	/*Elements that have the jQueryOptions property included (Date, Sort, Checksort, and Color)
	can make use of this method to render out the element's appropriate jQuery options.*/
	public function jQueryOptions() {
		if(!empty($this->jQueryOptions)) {
            $options = "changeMonth:true";
            foreach($this->jQueryOptions as $option => $value) {
                if(!empty($options))
                    $options .= ", ";
                $options .= $option . ': ';
				/*When javascript needs to be applied as a jQuery option's value, no quotes are needed.*/
                if(is_string($value) && substr($value, 0, 3) == "js:")
                    $options .= substr($value, 3);
                else
                    $options .= var_export($value, true);
            }
            echo "{ ", $options, " }";
        }
	}

	/*Many of the included elements make use of the <input> tag for display.  These include the Hidden, Textbox, 
	Password, Date, Color, Button, Email, and File element classes.  The project's other element classes will
	override this method with their own implementation.*/
	public function render() {
		echo '<input', $this->getAttributes(), '/>';
	}

	/*If an element requires inline stylesheet definitions, this method is used send them to the browser before
	the form is rendered.*/
	public function renderCSS() {}

	/*If an element requires javascript to be loaded, this method is used send them to the browser after
	the form is rendered.*/
	public function renderJS() {}

	public function _setForm(RM_PFBC_Form $form) {
		$this->_form = $form;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	/*This method provides a shortcut for applying the Required validation class to an element.*/
	public function setRequired($required) {
		if(!empty($required))
			$this->validation[] = new Validation_Required;
		$this->_attributes["required"] = "";	
	}

	/*This method applies one or more validation rules to an element.  If can accept a single concrete 
	validation class or an array of entries.*/
	public function setValidation($validation) {
		/*If a single validation class is provided, an array is created in order to reuse the same logic.*/
		if(!is_array($validation))
			$validation = array($validation);
		foreach($validation as $object) {
			/*Ensures $object contains a existing concrete validation class.*/
			if($object instanceof Validation) {
				$this->validation[] = $object;
				if($object instanceof Validation_Required)
					$this->_attributes["required"] = "";	
			}	
		}	
	}
        
        public function addValidation($validation)
        {
            $this->validation[]= $validation;
        }
        
        public function localizeJS(){ return array(); }
}
