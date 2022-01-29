<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class adds a repeatable type of field which can contain multiple values and textboxes can be appanded
 * multiple times 
 * 
 * @internal You must have to enter an id to this element as it will make sure the appending will work correctly.
 *
 * @author CMSHelplive
 */
class Element_Repeatable extends Element
{

    public $_attributes = array("type" => "text");
    public $prepend;
    public $append;

    public function __construct($label, $name, array $properties = null) {
		
                $configuration = array(
			"label" => $label,
			"name" => $name
		);

		/*Merge any properties provided with an associative array containing the label
		and name properties.*/
		if(is_array($properties))
			$configuration = array_merge($configuration, $properties);
		
		$this->configure($configuration);
                
    }
        
    public function render()
    {
        $addons = array();
        if (!empty($this->prepend))
            $addons[] = "input-prepend";
        if (!empty($this->append))
            $addons[] = "input-append";
        if (!empty($addons))
            echo '<div class="', implode(" ", $addons), '">';

        if(substr($this->_attributes["name"], -2) != "[]")
			$this->_attributes["name"] .= "[]";
        
        $this->renderRepeatable('start');
        $counter = isset($this->_attributes["value"])?count($this->_attributes["value"]):1;
        for ($i = 0; $i <= $counter-1; $i++)
        {
            $this->renderRepeatable("prepend");
            $this->renderAddOn("prepend");
            $this->customRender($i);
            $this->renderAddOn("append");
            $this->renderRepeatable("append");
        }
        $this->renderRepeatable('close');

        if (!empty($addons))
            echo '</div>';
    }
    
    public function customRender($i) {
        if(isset($this->_attributes['field_is_multiline']) && (int)$this->_attributes['field_is_multiline']==1 ):
            echo "<textarea", $this->getAttributes("value"), ">";
            if(!empty($this->_attributes["value"])){
                if(is_array($this->_attributes["value"]))
                    echo $this->_attributes["value"][$i];
                else
                   echo $this->getAttribute("value");
            }
            echo "</textarea>";
        else:
            echo '<input', $this->getAttributes(), '/>';
        endif;
		
	}

    public function renderAddOn($type = "prepend")
    {
        if (!empty($this->$type))
        {
            $span = true;
            if (strpos($this->$type, "<button") !== false)
                $span = false;

            if ($span)
                echo '<span class="add-on">';

            echo $this->$type;

            if ($span)
                echo '</span>';
        }
    }

    public function renderRepeatable($type = "prepend")
    {
        if ($type === "start")
            echo '<div class="rm_field_type_repeatable_container" id="rm_field_type_repeatable_container_'.$this->_attributes['id'].'">';
        if ($type === "prepend")
            echo '<div class="appendable_options">';
        if ($type === "append")
            echo '<div class="rm_actions" id="rm_add_repeatable_field" onClick="rm_append_field(\'div\',\'rm_field_type_repeatable_container_'.$this->_attributes["id"].'\')"><a>' . RM_UI_Strings::get("LABEL_ADD") . '</a></div><div class="rm_actions" onClick="rm_delete_appended_field(this,\'rm_field_type_repeatable_container_'.$this->_attributes["id"].'\')"><a href="javascript:void(0)">' . RM_UI_Strings::get("LABEL_DELETE") . '</a></div></div>';
        if ($type === "close")
            echo '</div>';
    }

}
