<?php
abstract class View extends Base {
	public $_form;

	public function __construct(array $properties = null) {
		$this->configure($properties);
	}

	public function _setForm(RM_PFBC_Form $form) {
		$this->_form = $form;
	}

	/*jQuery is used to apply css entries to the last element.*/
	public function jQueryDocumentReady() {}	

	public function render() {}

	public function renderCSS() {
		echo 'label span.required { color: #B94A48; }';
		echo 'span.help-inline, span.help-block { color: #888; font-size: .9em; font-style: italic; }';
	}	

	public function renderDescriptions($element) {
		$shortDesc = $element->getShortDesc();
		if(!empty($shortDesc)){
			//echo '<span class="help-inline">', $shortDesc, '</span>';;
                        echo '<div class="rmnote"><div class="rmprenote"></div>';
			echo '<div class="rmnote">', $shortDesc, '</div></div>';
                }

		$longDesc = $element->getLongDesc();
		if(!empty($longDesc)){
                        echo '<div class="rmnote"><div class="rmprenote"></div>';
			echo '<div class="rmnotecontent">', $longDesc, '</div></div>';
                }
			//echo '<span class="help-block">', $longDesc, '</span>';;
	}

	public function renderJS() {}

	public function renderLabel(Element $element) {}
}
