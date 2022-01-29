<?php

interface RM_Controller_Abstract
{

    public function set_controller($controller);

    public function set_action($action);

    public function set_params($params);

    public function run();
}
