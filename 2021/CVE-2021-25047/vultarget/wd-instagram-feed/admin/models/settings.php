<?php

class Settings_model_wdi {

  public $active_tab = 'configure';

  public function __construct() {
    $form_action_get = WDILibrary::get('form_action', '', 'sanitize_text_field', 'GET');
    $form_action_post = WDILibrary::get('form_action', 'configure', 'sanitize_text_field', 'POST');
    $this->activeTab = $form_action_get != '' ? $form_action_get : $form_action_post;
  }
}