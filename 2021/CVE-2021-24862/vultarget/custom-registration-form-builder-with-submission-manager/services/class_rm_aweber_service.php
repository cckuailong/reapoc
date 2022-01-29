<?php

class RM_Aweber_Service extends RM_Services {

    private $aw; 
    
    public function __construct(){
       $opt= new RM_Options();
       $options=$opt->get_all_options();
       try
       {
       $this->aw= new RM_Aweber($options);
       }
        catch(Exception $e)
             {
                 $this->aw=null;
             }
    }
    
    public function get_list()
    {
        if(defined('REGMAGIC_ADDON'))
        {
          $addon_service = new RM_Aweber_Service_Addon();
          return $addon_service->get_list($this);
        }
     }
    
     public function subscribe($request,$options)
     {
         if(defined('REGMAGIC_ADDON'))
         {
          $addon_service = new RM_Aweber_Service_Addon();
          return $addon_service->subscribe($request,$options,$this);
         }
     }
    /*
     * list all the mailing lists
     */
    public function aw_field_mapping($form, $form_options, $list = null)
    {
        if(defined('REGMAGIC_ADDON'))
         {
          $addon_service = new RM_Aweber_Service_Addon();
          return $addon_service->aw_field_mapping($form, $form_options, $this, $list);
         }
    }
    
    public function get_aw_mapping($options) {
        if(defined('REGMAGIC_ADDON'))
         {
          $addon_service = new RM_Aweber_Service_Addon();
          return $addon_service->get_aw_mapping($options, $this);
         }
    }
}