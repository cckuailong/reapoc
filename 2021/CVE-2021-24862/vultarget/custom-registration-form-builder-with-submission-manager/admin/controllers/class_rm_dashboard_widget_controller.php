<?php

/**
 * 
 */

class RM_Dashboard_Widget_Controller
{

     public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function display($model, $service, $request, $params)
    {
        $data = new stdClass;

        $submissions = $service->get('SUBMISSIONS', 1, null, 'results', 0, 10, '*', 'submitted_on', true);

        $sub_data = array();

        if($submissions)
        {
            foreach ($submissions as $submission)
            {
               //echo "<br>ID: ".$submission->form_id." : ".RM_Utilities::localize_time($submission->submitted_on, 'M dS Y, h:ia')." : ";
               $name = $service->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 10, 'form_name');
               $date = RM_Utilities::localize_time($submission->submitted_on, 'd M Y'); //Previously "M dS Y, h:ia".
               $payment_status = $service->get('PAYPAL_LOGS', array('submission_id' => $submission->submission_id), array('%d'), 'var', 0, 10, 'status');

               $sub_data[] = (object)array('submission_id'=>$submission->submission_id, 'name'=>$name, 'date'=>$date, 'payment_status'=>$payment_status);
            }
            
            $data->total_sub = count($submissions);
        }     

        $data->submissions = $sub_data; 
        $data->count = $service->get_count_summary();

        $view = $this->mv_handler->setView("dashboard_widget");
        $view->render($data);
    }
}
