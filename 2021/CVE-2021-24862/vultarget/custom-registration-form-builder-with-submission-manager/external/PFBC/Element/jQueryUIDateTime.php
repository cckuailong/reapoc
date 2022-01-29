<?php
class Element_jQueryUIDateTime extends Element_Textbox {
    public $_attributes = array(
        "type" => "text",
		"autocomplete" => "off",
                "readonly" => true
                /*"pattern" => "^((0?[13578]|10|12)(-|\/)(([1-9])|(0[1-9])|([12])([0-9]?)|(3[01]?))(-|\/)((19)([2-9])(\d{1})|(20)([01])(\d{1})|([8901])(\d{1}))|(0?[2469]|11)(-|\/)(([1-9])|(0[1-9])|([12])([0-9]?)|(3[0]?))(-|\/)((19)([2-9])(\d{1})|(20)([01])(\d{1})|([8901])(\d{1})))$"*/
    );
    
    public $jQueryOptions="";
    
    public function __construct($label, $name, array $properties = null) {
        parent::__construct($label, $name, $properties);

        if(!isset($properties['date_format']) || !$properties['date_format'])
            $this->_attributes['data-dateformat'] = 'mm/dd/yy';
        else
            $this->_attributes['data-dateformat'] = $properties['date_format'];
    }

    public function getCSSFiles() {
        
        return array(
            $this->_form->getPrefix() . "://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css"
        );
    }

	public function getJSFiles() {
		return array(
			//$this->_form->getPrefix() . "://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"
		);
	}

    public function jQueryDocumentReady() {
        parent::jQueryDocumentReady();
        $id = $this->_attributes["id"];//var_dump($id);
        $dateformat = explode(" ", $this->_attributes["data-dateformat"]);
        wp_enqueue_style('rm_jquery_ui_timepicker_addon_css');
        wp_enqueue_script('rm_jquery_ui_timepicker_addon_js');
$dpjs = <<<JSDP
        jQuery("#{$id}").datetimepicker({
                                        dateFormat:"{$dateformat[0]}",
                                        timeFormat:"{$dateformat[1]}",
                                        stepMinute: 5,
                                        changeMonth:true,
                                        changeYear:true,
                                        yearRange: '1900:+50',
                                        beforeShow: function(input, inst){
                                                        if(inst.id === "{$id}") {
                                                            jQuery("#ui-datepicker-div").addClass("rm_jqui_element");
                                                        } else {
                                                            jQuery("#ui-datepicker-div").removeClass("rm_jqui_element");
                                                        }
                                                    }
                                    });
JSDP;
        echo $dpjs;
    }

    public function render() {
        $this->validation[] = new Validation_Date;
        parent::render();
    }
}