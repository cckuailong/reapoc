<?php
class Element_Email extends Element_Textbox {
	public $_attributes = array("type" => "email");

        public function __construct($label, $name, array $properties = null, array $advance_opts = null) {
            parent::__construct($label, $name, $properties, $advance_opts);
            $this->validation[] = new Validation_Email;
        } 
	public function render() {
		parent::render();
	}
}
