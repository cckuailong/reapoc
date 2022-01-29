<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of calss_rm_field_controller
 *
 * @author CMSHelplive
 */
class RM_Editor_Actions_Controller
{

    public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();

        if(!wp_script_is('media-upload', 'enqueued'))
            wp_enqueue_script( 'media-upload' );
    }

    public function add_form($model, $service, $request, $params)
    {
        $data= new stdClass();
        $data->forms= $service->add_form();
        $view = $this->mv_handler->setView('editor_add_form');
        $view->render($data);

    }

   public function add_email($model, $service, $request, $params)
    {
        $data= new stdClass();
        if(isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']))
            $data->emails= $service->add_email($request->req['rm_form_id']);
        $view = $this->mv_handler->setView('editor_add_email');
        $view->render($data);

    }

    public function add_fields_dropdown_invites($model, $service, $request, $params)
    {
        $data= new stdClass();
        if(isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']))
            $data->emails= $service->add_email($request->req['rm_form_id']);
        $data->editor_control_id = 'mce_rm_mail_body';
        $view = $this->mv_handler->setView('editor_add_email');
        $view->render($data);

    }




}
