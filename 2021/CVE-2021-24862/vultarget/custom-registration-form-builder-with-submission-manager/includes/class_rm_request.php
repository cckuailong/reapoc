<?php


class RM_Request{

    public $req;
    public $xml_loader;
    
    public function __construct($xml_loader){
        $rm_sanitizer = new RM_Sanitizer;
        $this->req= $rm_sanitizer->sanitize_request($_REQUEST);
        $this->xml_loader= $xml_loader;
        $this->setReqSlug();
    }

    public function setReqSlug($rm_slug='',$front=false){
        if (!empty($rm_slug)) {
            $this->req['rm_slug'] = $rm_slug;
        } else if (!isset($_POST['rm_slug']) && isset($_GET['page']))
            $this->req['rm_slug'] = $this->req['page'];
        elseif (!isset($_POST['rm_slug']))
            $this->req['rm_slug'] = 'rm_no_slug';
        if (!isset($_POST['rm_slug']) && $front) {
            $this->req['rm_slug'] = $rm_slug;
        }
    }

    public function isValid(){

        if(!isset($this->req['rm_slug'])){
             return false;
        } 
        $xml= (array)$this->xml_loader->load_data($this->req['rm_slug']);
        
        if(!empty($xml)){
            return true;
       }

       return false;
    }
    
    public function getReq(){
        return $this->req;
    }
}