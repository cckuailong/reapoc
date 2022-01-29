<?php

/**
 * Class to handle Model validation along with set view operation
 */

class RM_Model_View_Handler
{
    /*
     * This function validates the submitted for all the POST requests.
     * It clear all the errors in case of any GET requests.
     */
    public function validateForm($form_slug="default", $form_object = null){
        $valid= false;
        
           if($_SERVER['REQUEST_METHOD']=="POST" && RM_PFBC_Form::isValid($form_slug, false, $form_object))
           {
               $valid= true;
               RM_PFBC_Form::clearValues($form_slug);
           }
           else
           {
             if($_SERVER['REQUEST_METHOD']=="GET" || (isset($_POST['RM_CLEAR_ERROR'])&& $_POST['RM_CLEAR_ERROR'] === 'true'))
               {
                   RM_PFBC_Form::clearErrors($form_slug);
                   RM_PFBC_Form::clearValues($form_slug);
               }
           }
            return $valid;
    }

    public function setView($view_name,$front=false){
        if($front)
            $view= new RM_View_Public($view_name);
        else
            $view= new RM_View_Admin($view_name);

        return $view;
    }
    
    public function clearFormErrors($form_slug){
        RM_PFBC_Form::clearErrors($form_slug);
    }

}