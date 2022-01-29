<?php

/**
 * 
 */

class RM_Invitations_Controller
{

    public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function manage($model, $service, $request, $params)
    {
        $data = new stdClass;        
        
        $data->forms = RM_Utilities::get_forms_dropdown($service);

        if(isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']))
        {
            $data->current_form_id = $request->req['rm_form_id'];            
        }
        else
        {
            //Get first form's id in this case
             reset($data->forms);
             $data->current_form_id = (string)key($data->forms);

             //Set request parameter manually so wp-editor hook can use it.
             $_REQUEST['rm_form_id'] = $data->current_form_id;
        }

        $data->queue_view = false;
        if(isset($request->req['rm_queues']) && $request->req['rm_queues']=='true')
        {
            $data->queue_view = true;
            $data->queues = $service->get_queues();
            $data->queue_count = count($data->queues);
        }

        if ($this->mv_handler->validateForm("invitation_mail_content"))
        {
            //echo "<pre>", var_dump($request->req),die;
            $form_id = $data->current_form_id;

            if(!isset($data->forms[$form_id]))
                die("Error: INVALID FORM ID, RETRY.");
           
            $res = $service->add_job($form_id, $request->req["rm_mail_subject"], $request->req["rm_mail_body"]);
            
            //Always check via isset for this error in template file
            $data->no_mail_error = ($res == false);// ? true : false;
            
            rm_start_cron();

            //Some return condition maybe?
        }
        
        $data->job = $service->get_job_stat($data->current_form_id);//$job;
        $data->total_resp = $service->get_resp_count($data->current_form_id);
        $view = $this->mv_handler->setView("invitations");
        $view->render($data);
    }

    public function add_job()
    {

    }

}