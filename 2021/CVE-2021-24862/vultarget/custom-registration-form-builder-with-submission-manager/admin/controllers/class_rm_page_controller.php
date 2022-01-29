<?php

/**
 * Default Views(when no parameter is set in request) loader functionality controller
 * 
 * This class loads default views for menu pages defined when no additional request parameter 
 * is set to load a perticular view 
 */
class RM_Page_Controller extends RM_FormHandler
{

    public $model;
    public $service;

    public function __construct($service,$model)
    {
        $this->model= $model;
        $this->service= $service;
    }

    public function save_form(){

        if(parent::validateForm("add_form"))
        {
            $view= new RM_View_Admin('add_form');
        }else{
            $view= new RM_View_Admin('add_form');
        }
        $view->render();
    }
}
