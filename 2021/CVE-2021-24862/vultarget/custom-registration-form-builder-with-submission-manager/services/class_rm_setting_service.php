<?php

class RM_Setting_Service {

    public $model;

    public function set_model($model){
        $this->model= $model;
    }

    public function get_model(){
        return $this->model;
    }

    public function get_options()
    {
        return $this->model->get_all_options();
    }

    public function save_options($options)
    {
        return $this->model->set_values($options);
    }

}