<?php
class Element_SecEmail extends Element_Textbox {
	public $_attributes = array("type" => "email");
	
	 public function __construct($label, $name, array $properties = null) {
            parent::__construct($label, $name, $properties);
            $this->validation[] = new Validation_Email;
        }
	
	public function render() {
		parent::render();
	}
}
