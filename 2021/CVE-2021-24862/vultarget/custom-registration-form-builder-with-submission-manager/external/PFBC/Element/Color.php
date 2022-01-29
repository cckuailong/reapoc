<?php
class Element_Color extends Element_Textbox {
	public $_attributes = array("type" => "text","class"=>'jscolor'/*,"readonly"=>"readonly"*/);

	public function getJSFiles() {

	}
	public function render() {
		$this->_attributes["pattern"] = "[A-Fa-f0-9]{6}";
		$this->_attributes["title"] = __('6-digit hexidecimal color (e.g. #000000)','custom-registration-form-builder-with-submission-manager');
		$this->validation[] = new Validation_RegExp("/" . $this->_attributes["pattern"] . "/", __('Error: The %element% field must contain a ','custom-registration-form-builder-with-submission-manager') . $this->_attributes["title"]);
		parent::render();
	}
}
