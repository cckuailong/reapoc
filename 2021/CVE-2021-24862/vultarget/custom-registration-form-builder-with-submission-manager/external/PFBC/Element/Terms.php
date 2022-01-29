<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Terms
 *
 * @author CMSHelplive
 */
class Element_Terms extends Element {
    public $terms;
    public $_attributes = array("rows" => "5");
    public $required_scroll;

    public function __construct($label, $name, $terms_text, array $properties = null) {
        $configuration = array(
            "label" => $label,
            "name" => $name
        );

        /* Merge any properties provided with an associative array containing the label
          and name properties. */
        if (is_array($properties))
            $configuration = array_merge($configuration, $properties);

        $this->configure($configuration);
        $this->_attributes["default_value"] = $terms_text;
        $this->required_scroll = isset($properties['required_scroll']) ? $properties['required_scroll'] : null;
    }

   public function render()
    {       
       if(isset($this->_attributes["style"])){
           $style = "style='".$this->_attributes["style"]."'";
           unset($this->_attributes["style"]);
       }else
           $style = '';
          
       if(isset($this->_attributes['cb_label'])){
             $cb_label = $this->_attributes["cb_label"];
            unset($this->_attributes["cb_label"]);
         }
         else
             $cb_label = '';
         
       $scroll = '';
       $disabled = '';
       if($this->required_scroll==1){
           $scroll = "onscroll='scroll_down_end(this);' ";
           $disabled='disabled';
       }
       
       $checked = '';
        if($this->getAttribute('value') == 'on')
            $checked = "checked";
       
       if(isset($this->_attributes["check_above_tc"]) && $this->_attributes["check_above_tc"] == 1)
           echo "<div class='rm_terms_checkbox'><input ",$checked," ",$disabled," value='on' type='checkbox'", $this->getAttributes(array("default_value", "value")),  " class='rm_check_box'>".$cb_label."</div>";
       
       echo "<div id='rm_terms_textarea' class='rm_terms_textarea'><textarea ", $style ," ",$scroll," readonly  id='rm_terms_area_", $this->_attributes['name'], "' class='rm_terms_area'>";
       
       if (!empty($this->_attributes["default_value"]))
           echo $this->filter($this->_attributes["default_value"]);
       echo "</textarea></div>";
       
       if(!isset($this->_attributes["check_above_tc"]) || $this->_attributes["check_above_tc"] == 0)
           echo "<div class='rm_terms_checkbox'><input ",$checked," ",$disabled," value='on' type='checkbox'", $this->getAttributes(array("default_value", "value")),  " class='rm_check_box'>".$cb_label."</div>";
   }


    public function getAttributes($ignore = "") {

        $str = "";
        if (!empty($this->_attributes)) {
            if (!is_array($ignore))
                $ignore = array($ignore);
            $attributes = array_diff(array_keys($this->_attributes), $ignore);
            foreach ($attributes as $attribute) {
                $str .= ' ' . $attribute;
                if ($this->_attributes[$attribute] !== "" && !is_array($this->_attributes[$attribute] && $attribute === "default_value"))
                    $str .= '="' . $this->filter($this->_attributes[$attribute]) . '"';
            }
        }
        return $str;
    }

}
