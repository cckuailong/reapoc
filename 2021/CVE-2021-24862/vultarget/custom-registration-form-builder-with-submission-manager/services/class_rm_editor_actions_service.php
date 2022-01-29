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
class RM_Editor_Actions_Service
{

    public function add_form(){
        $forms = RM_DBManager::get_all(RM_Forms::get_identifier(), $offset = 0, $limit=1000, $column = 'form_name,form_id', $sort_by = '', $descending = false);
        return $forms;
    }

    public function add_email($form_id){
        $where= array("form_id"=>$form_id);
        $data_specifier= array("%s","%d");
        $email_fields= RM_DBManager::get(RM_Fields::get_identifier(),$where, $data_specifier, $result_type = 'results', $offset = 0, $limit = 1000, $column = '*', $sort_by = null, $descending = false);
        $fields= array();
        
        if(is_array($email_fields) || is_object($email_fields))
        foreach($email_fields as $field){
           if(!in_array($field->field_type,array('Price','File','Terms','Divider','Spacing','Image','HTMLH','HTMLP','RichText','Timer','YouTubeV','Link','Iframe'))){
                $fields[]= $field;
            }
        }

        return $fields;
    }
    
    public function add_fields($form_id){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Editor_Actions_Service_Addon;
            return $addon_service->add_fields($form_id, $this);
        }
        
    }

}
