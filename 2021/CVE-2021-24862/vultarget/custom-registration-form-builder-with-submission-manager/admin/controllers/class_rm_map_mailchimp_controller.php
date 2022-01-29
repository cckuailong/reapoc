<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Controller to handle USER related requests
 *
 * @author CMSHelplive
 */
class RM_Map_MailChimp_Controller {

    public $mv_handler;

    function __construct() {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function get_mc_list_field() {
        $list = $_POST['list_id'];
        $form_id = $_POST['form_id'];

        $mailchimp = new RM_MailChimp_Service();       
        
        $form = new RM_Forms;
        $form->load_from_db($form_id);
        
        $content = $mailchimp->mc_field_mapping($form_id, $form->form_options, $list);
        
        echo $content;
        die;
    }   
    
}
