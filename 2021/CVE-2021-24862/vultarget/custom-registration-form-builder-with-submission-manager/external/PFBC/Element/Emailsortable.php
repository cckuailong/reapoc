<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Emailsortable
 *
 * @author CMSHelplive
 */
class Element_Emailsortable extends Element_Textboxsortable
{
   public $_attributes = array("type" => "email");

	public function render() {
		$this->validation[] = new Validation_Email;
		parent::render();
	}
}
