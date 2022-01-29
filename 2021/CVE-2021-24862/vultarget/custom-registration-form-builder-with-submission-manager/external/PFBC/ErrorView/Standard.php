<?php
class ErrorView_Standard extends ErrorView {
	public function applyAjaxErrorResponse() {
		$id = $this->_form->getAttribute("id");
                $error_header = RM_UI_Strings::get('LABEL_FORM_SUB_ERROR_HEADER');
		echo <<<JS

		var errorHTML = '<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a><strong class="alert-heading">{$error_header}</strong><ul>';
		for(e = 0; e < errorSize; ++e)
			errorHTML += '<li>' + response.errors[e] + '</li>';
		errorHTML += '</ul></div>';
		jQuery("#$id").prepend(errorHTML);
JS;

	}

	public function parse($errors) {
		$list = array();
		if(!empty($errors)) {
			$keys = array_keys($errors);
			$keySize = sizeof($keys);
			for($k = 0; $k < $keySize; ++$k) 
				$list = array_merge($list, $errors[$keys[$k]]);
		}
		return $list;
	}

    public function render() {
        $errors = $this->parse($this->_form->getErrors());
        if(!empty($errors)) {
            $errors = implode("</li><li>", $errors);

            $error_header = RM_UI_Strings::get('LABEL_FORM_SUB_ERROR_HEADER');

			echo <<<HTML
			<div class="rm-response-message alert alert-error rm-alret-box-wrap">
				<span class="close" data-dismiss="alert" onclick="jQuery(this).parents('.alert-error').hide()">×</span>
				<ul><li>$errors</li></ul>
			</div>
HTML;
        }
    }

    public function renderAjaxErrorResponse() {
        $errors = $this->parse($this->_form->getErrors());
        if(!empty($errors)) {
            header("Content-type: application/json");
            echo json_encode(array("errors" => $errors));
        }
    }
}
