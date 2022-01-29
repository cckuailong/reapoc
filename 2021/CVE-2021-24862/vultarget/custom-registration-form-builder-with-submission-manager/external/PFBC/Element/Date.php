<?php
class Element_Date extends Element_Textbox {
	public $_attributes = array(
		"type" => "date",
		"pattern" => "\d{4}-\d{2}-\d{2}"
	);

	public function __construct($label, $name, array $properties = null) {
		$this->_attributes["placeholder"] =__('YYYY-MM-DD (e.g. '.date("Y-m-d"). ')','custom-registration-form-builder-with-submission-manager') ;
		$this->_attributes["title"] = $this->_attributes["placeholder"];

		parent::__construct($label, $name, $properties);
    }

	public function render() {
		$this->validation[] = new Validation_RegExp("/" . $this->_attributes["pattern"] . "/", __('Error: The %element% field must match the following date format:','custom-registration-form-builder-with-submission-manager') . $this->_attributes["title"]);
		parent::render();
	}
}
