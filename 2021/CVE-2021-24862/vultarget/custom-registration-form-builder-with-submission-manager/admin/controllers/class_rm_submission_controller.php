<?php

/**
 * Class for submissions controller
 * 
 * Manages the submissions related operations in the backend.
 *
 * @author CMSHelplive
 */
class RM_Submission_Controller
{

    public $mv_handler;

    public function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function manage($model, $service, $request, $params)
    {
        $data = new stdClass();
        
        $filter= new RM_Submission_Filter($request,$service);
        $form_id= $filter->get_form();
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $data->fields = $service->get_all_form_fields($form_id);
      
        $data->filter= $filter;
        $data->rm_slug = $request->req['page'];
        $data->submissions= $filter->get_records();
     
        if(defined('REGMAGIC_ADDON'))
            $data->is_filter_active = $filter->is_active();
      
        $view = $this->mv_handler->setView('submissions_manager');
        $view->render($data);
        
    }

    public function view($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Submission_Controller_Addon();
            return $addon_controller->view($model, $service, $request, $params, $this);
        }
        if (isset($request->req['rm_submission_id']))
        {

            if (!$model->load_from_db($request->req['rm_submission_id']))
            {
                $view = $this->mv_handler->setView('show_notice');
                $data = RM_UI_Strings::get('MSG_DO_NOT_HAVE_ACCESS');
                $view->render($data);
            } else
            {
                $child_id = $model->get_child_id();
                if($child_id != 0){
                    $request->req['rm_submission_id'] = $model->get_last_child();
                    return $this->view($model, $service, $request, $params);
                }
                    
                
                if (isset($request->req['rm_action']) && $request->req['rm_action'] == 'delete')
                {
                    $request->req['rm_form_id'] = $model->get_form_id();
                    $request->req['rm_selected'] = $request->req['rm_submission_id'];
                    $this->remove($model, $service, $request, $params);
                    unset($request->req['rm_selected']);
                } else
                {
                    $settings = new RM_Options;

                    $data = new stdClass();

                    $data->submission = $model;

                    $data->payment = $service->get('PAYPAL_LOGS', array('submission_id' => $service->get_oldest_submission_from_group($model->get_submission_id())), array('%d'), 'row', 0, 99999);

                    if ($data->payment != null)
                    {
                        $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);

                        if ($data->payment->log)
                            $data->payment->log = maybe_unserialize($data->payment->log);
                    }

                    $data->notes = $service->get('NOTES', array('submission_id' => $model->get_submission_id()), array('%d'), 'results', 0, 99999, '*', null, true);
                    $i = 0;
                    if (is_array($data->notes))
                        foreach ($data->notes as $note)
                        {
                            $data->notes[$i]->author = get_userdata($note->published_by)->display_name;
                            if ($note->last_edited_by)
                                $data->notes[$i++]->editor = get_userdata($note->last_edited_by)->display_name;
                            else
                                $data->notes[$i++]->editor = null;
                        }
                    /*
                     * Check submission type
                     */
                    $form = new RM_Forms();
                    $form->load_from_db($model->get_form_id());
                    $data->form_id=$model->get_form_id();
                    $fields= $service->get_all_form_fields($model->get_form_id());
                    $data->email_field_id=$fields['0']->field_id;
                    $form_type = $form->get_form_type() == "1" ? __("Registration",'custom-registration-form-builder-with-submission-manager') : __("Non WP Account",'custom-registration-form-builder-with-submission-manager');
                    $data->form_type = $form_type;
                    $data->form_type_status = $form->get_form_type();
                    //$data->form_name = $form->get_form_name();
                    $data->form_is_unique_token = $form->get_form_is_unique_token();
                    $rm_sr=new RM_Services;
                    $related_subs=$rm_sr->get_submissions_by_email($data->submission->get_user_email());
                    if(is_array($related_subs))
                        $data->related=count($related_subs);
                    if($data->related >0)
                    {
                        $data->related=$data->related-1;
                    }
                    else
                        $data->related=0;
                    /*
                     * User details if form is registration type
                     */
                    if ($form->get_form_type() == "1")
                    {
                        $email = $model->get_user_email();
                        if ($email != "")
                        {
                            $user = get_user_by('email', $email);
                            $data->user = $user;
                        }
                    }
                    $view = $this->mv_handler->setView('view_submission');

                    $view->render($data);
                }
            }
        } else
            throw new InvalidArgumentException(RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID'));
    }

    public function print_pdf($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Submission_Controller_Addon();
            return $addon_controller->print_pdf($model, $service, $request, $params, $this);
        }
        if(is_admin())
            RM_Utilities::redirect('?page=rm_submission_manage&rm_form_id='.$request->req['rm_form_id']);
        else{
            ?>
            <pre class='rm-pre-wrapper-for-script-tags'><script>
                location.reload();
            </script></pre>
            <?php
        }
            
    }

    public function remove($model, RM_Services $service, $request, $params)
    {
       $form_id= (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) ? $request->req['rm_form_id'] : null; 
         $selected = isset($request->req['rm_selected']) ? $request->req['rm_selected'] : null;
        if($selected !=null){
        $service->remove_submissions($selected);
        $service->remove_submission_notes($selected);
        $service->remove_submission_payment_logs($selected);
        }
        RM_Utilities::redirect('?page=rm_submission_manage&rm_form_id='.$form_id);
    }
    public function related($model, RM_Services $service, $request, $params)
    {
         $data=new stdClass();
         $data->submission_id=$request->req['rm_submission_id'];
          $data->user_email=$request->req['rm_user_email'];
         $rm_sr=new RM_Services;
         $data->submissions=$rm_sr->get_submissions_by_email($data->user_email);
        // echo "<pre>",var_dump($submissions);die;
         $view = $this->mv_handler->setView('related_submissions');
         $view->render($data);
    }
    public function export($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Submission_Controller_Addon();
            return $addon_controller->export($model, $service, $request, $params);
        }
        $this->manage($model, $service, $request, $params);
    }

    public function search($model, $service, $request, $params)
    {
        
    }
    
    public function mark_all_read($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Submission_Controller_Addon();
            return $addon_controller->mark_all_read($model, $service, $request, $params);
        }
    }

}