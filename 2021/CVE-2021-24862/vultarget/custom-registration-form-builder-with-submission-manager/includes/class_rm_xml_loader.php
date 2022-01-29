<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Singleton class to load all the XML entries in the initial load of the plugin
 *
 * @author CMSHelplive
 */
class RM_XML_Loader
{

    public $xml_obj;
    public $xml_path;
    public static $instance;
    public $request_tree= null;
    public $current_action;

    public function __construct($xml_path)
    {
        $this->xml_path = $xml_path;
        $this->xml_obj = array();
        $this->request_tree= new stdClass();
    }

    static function getInstance($xml_path){
        if(self::$instance===null){
            self::$instance = new RM_XML_Loader($xml_path);
        }
        return self::$instance;
    }

    public function load_data($name)
    {
        $xml = simplexml_load_file($this->xml_path);

        foreach ($xml->children() as $node) { //print_r($node);

            if ($node->getName() == "controllers") {
                foreach ($node->children() as $controller) {
                    $this->xml_obj['controllers'][(string)$controller['name']] = (string)$controller['class'];
                }
            }

            // Check if <Model> tag exists
            if ($node->getName() == "models") {
                foreach ($node->children() as $model) {
                    $this->xml_obj['models'][(string)$model['name']] = (string)$model['class'];
                }
            }

            if ($node->getName() == "services") {
                foreach ($node->children() as $service) {
                    $this->xml_obj['services'][(string)$service['name']] = (string)$service['class'];
                }
            }

        }


        foreach ($xml->children() as $node) {
            if ($node->getName() == "requests") {
                foreach ($node->children() as $request) {

                    /*
                     * Get all the allowed actions for the controller. Then concatenate the allowed method to match with
                     * the current request slug
                     */
                    $all_actions= explode(',',$request['allowed_actions']);

                    $allowed_actions= array();

                    foreach($all_actions as $a){
                        $allowed_actions[]= $request['slug'].$a;
                        if($name==$request['slug'].$a){
                            $this->current_action= $a;
                        }
                    }


                    /*
                     * Check if the controller has a matching slug from request slug
                     */
                    if(in_array($name,$allowed_actions)){
                        if(isset($this->xml_obj['controllers'][(string)$request['controller']])){
                            $this->request_tree->controller= $this->xml_obj['controllers'][(string)$request['controller']];
                        }else{
                            echo 'No Controller found';
                            die;
                        }

                    /*
                     * Get all the children tags in matching request tag
                     */
                        foreach ($request->children() as $actions) {

                            // Checking if <action> tag exists
                            if($actions->getName()=="actions"){
                                foreach($actions as $action){
                                    if((string)$action['name']==$this->current_action)
                                    {
                                        if(isset($action['success-action'])){
                                            $this->request_tree->success=  (string)$action['success-action'];
                                        }
                                        foreach($action->children() as $actionElements){
                                            if($actionElements->getName()=="model"){

                                                $this->request_tree->model= $this->xml_obj['models'][(string)$actionElements['ref']];
                                            }
                                            if($actionElements->getName()=="service"){

                                                $this->request_tree->service= $this->xml_obj['services'][(string)$actionElements['ref']];
                                            }
                                        }

                                        $this->request_tree->action= $this->current_action;
                                        $this->request_tree->context= "admin";
                                        $this->request_tree->type= (string)$action['type'];

                                    }
                                }
                            }else{
                                echo 'No action tag declared corresponding to controller class <b>'.$this->request_tree->controller."</b>";
                                die;
                            }
                        }
                    }

                }
            }
        }


            // $this->xml_obj->target= (string)$request->target;
            //  $this->xml_obj->dependencies= explode('|', $request->dependencies);
            // $this->xml_obj->type= (string) strtolower($request['type']);
            // $this->xml_obj->address= (string) strtolower($request['address']);




        return $this->request_tree;

    }



}
