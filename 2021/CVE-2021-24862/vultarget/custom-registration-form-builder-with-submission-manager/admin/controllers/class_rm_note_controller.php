<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_note_controller
 *
 * @author CMSHelplive
 */
class RM_Note_Controller
{

    public $mv_handler;

    public function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function add($model, RM_Note_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Note_Controller_Addon();
            return $addon_controller->add($model, $service, $request, $params, $this);
        }
        return true;
    }

    public function remove($model, /* RM_Services */ $service, $request, $params)
    {
        return true;
    }

    public function delete($model, /* RM_Services */ $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Note_Controller_Addon();
            return $addon_controller->delete($model, $service, $request, $params);
        }
        return true;
    }

}
