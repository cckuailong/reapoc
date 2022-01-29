<?php
class View_Vertical extends View {
	public function render() {
		echo '<form', $this->_form->getAttributes(), '>';
		$this->_form->getErrorView()->render();

		$elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if($element instanceof Element_Button) {
                if($e == 0 || !$elements[($e - 1)] instanceof Element_Button)
                    echo '<div class="form-actions">';
				else
					echo ' ';
                $element->render();
                if(($e + 1) == $elementSize || !$elements[($e + 1)] instanceof Element_Button)
                    echo '</div>';
            }
            else {
                $this->renderLabel($element);
                $element->render();
				$this->renderDescriptions($element);
                ++$elementCount;
            }
        }

		echo '</form>';
    }

	public function renderLabel(Element $element) {
        $label = $element->getLabel();
		echo '<label for="', $element->getAttribute("id"), '">';
        if(!empty($label)) {
			if($element->isRequired())
				echo '<span class="required">* </span>';
			echo $label;	
        }
		echo '</label>'; 
    }
}	
