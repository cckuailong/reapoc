<?php

class Element_Button extends Element
{

    public $_attributes = array("type" => "submit", "value" => "Submit");
    public $icon;

    public function __construct($label = "Submit", $type = "", array $properties = null)
    {
        if (!is_array($properties))
            $properties = array();

        if (!empty($type))
            $properties["type"] = $type;

        $class = "rm-btn";
        if (empty($type) || $type == "submit")
            $class .= " rm-btn-primary";

        if (!empty($properties["class"]))
            $properties["class"] .= " " . $class;
        else
            $properties["class"] = $class;

        if (empty($properties["value"]))
            $properties["value"] = $label;

        parent::__construct("", "", $properties);
    }

    public function render()
    {

        if (isset($this->_attributes['fgcolor']) && isset($this->_attributes['bgcolor']))
        {
            if ($this->_attributes['fgcolor'] == $this->_attributes['bgcolor'])
            {
                unset($this->_attributes['fgcolor']);
                unset($this->_attributes['bgcolor']);                
                echo '<input ', $this->getAttributes(), '/>';
                return;
            }
        }

        $_att_bak = $this->_attributes;
        if (isset($this->_attributes['fgcolor']))
        {
            unset($this->_attributes['fgcolor']);
            $color_s = "color:" . $_att_bak['fgcolor'];
        } else
            $color_s = "";

        if (isset($this->_attributes['bgcolor']))
        {
            unset($this->_attributes['bgcolor']);
            $bgcolor_s = "background-color:" . $_att_bak['bgcolor'];
        } else
            $bgcolor_s = "";

        if ($color_s == "" && $bgcolor_s == "")
            $inline_style = "";
        else
            $inline_style = "style = '$color_s;$bgcolor_s'";

        echo '<input ', $inline_style, ' ', $this->getAttributes(), '/>';

        $this->_attributes = $_att_bak;
    }

}