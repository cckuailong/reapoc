<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_base_model
 *
 * @author CMSHelplive
 */
abstract class RM_Base_Model
{
    public $initialized;
      
    public function set(array $request)
    {
        foreach ($request as $property => $value)
        {
            if (property_exists($this, $property))
            {
                $set_property_method = 'set_' . $property;
                $this->$set_property_method($value);
            }
        }

        return $this->initialized = true;
    }

    abstract public function load_from_db($id);
    //abstract public function insert_into_db(); // Removed due many non-compatible variations of the function in children classes.
    abstract public function update_into_db();
    abstract public function remove_from_db();
}
