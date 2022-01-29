<?php

/**
 * Controller to handle attachment related work
 *
 * @author CMSHelplive
 */
class RM_Attachment_Controller
{

    public $mv_handler;

    public function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function manage($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Attachment_Controller_Addon();
            return $addon_controller->manage($model, $service, $request, $params, $this);
        }
        $data = new stdClass();
        $view = $this->mv_handler->setView('attachment_manage');
        $view->render($data);
    }

    public function download_all($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Attachment_Controller_Addon();
            return $addon_controller->download_all($model, $service, $request, $params);
        }
        return true;
    }

    public function download_selected($model, $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Attachment_Controller_Addon();
            return $addon_controller->download_selected($model, $service, $request, $params);
        }
        return $true;
    }

    public function download($model, RM_Attachment_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Attachment_Controller_Addon();
            return $addon_controller->download($model, $service, $request, $params);
        }
        return true;
    }

}