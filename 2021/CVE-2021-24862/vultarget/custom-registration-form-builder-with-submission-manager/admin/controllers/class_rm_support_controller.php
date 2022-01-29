<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 *
 * @author CMSHelplive
 */
class RM_Support_Controller
{
    public $mv_handler;

    function __construct(){
        $this->mv_handler= new RM_Model_View_Handler();
    }

    public function forum($model,$service,$request,$params){
        $view= $this->mv_handler->setView('support');
        $view->render();
    }
    
    public function frontend($model,$service,$request,$params){
        $view= $this->mv_handler->setView('frontend_primer');
        $view->render();
    }
    
    public function whats_new($model,$service,$request,$params){
        $view= $this->mv_handler->setView('whats_new');
        $view->render();
    }
    
    public function premium_page($model,$service,$request,$params){
        $view= $this->mv_handler->setView('premium');
        $view->render();
    }
}
