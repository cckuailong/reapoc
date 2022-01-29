<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author CMSHelplive
 */
class Element_Timer extends Element implements Widget
{

    public function __construct($value,$class=null)
    {
        $properties = array("value" => $value, "class" => $class);
        parent::__construct("", "", $properties);
    }
    
    public function getJSFiles()
    { 
        return array(
            'script_rm_flipclock' => RM_BASE_URL . 'public/js/script_rm_flipclock.js'
        );
    }
    
    public function getCSSFiles() {
        return array(
                RM_BASE_URL . 'public/css/rm_field_flipclock.css'
        );
    }

    public function getJSDeps()
    {  
        return array(
            'script_rm_flipclock'
        );
    }
    
    public function jQueryDocumentReady() {
      echo "jQuery('#".$this->_attributes["id"]."').FlipClock({clockFace: 'MinuteCounter'});";
    }
    
    public function render()
    {
        ?>
        <div id="<?php echo $this->_attributes["id"]; ?>" class="rm_clock <?php echo $this->_attributes["class"] ? ''.$this->_attributes["class"]:null; ?>" style="margin:2em;"></div>
        <?php
    }
    
  
}
