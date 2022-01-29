<?php
class Element_Radio extends OptionElement {
	public $_attributes = array("type" => "radio");
	public $inline;
        
        
        public function jQueryDocumentReady(){
            if(isset($this->_attributes["rm_is_other_option"]) && $this->_attributes["rm_is_other_option"] == 1){
                echo <<<JS
            
                   
                   jQuery("input[name='{$this->_attributes['name']}']").change(function(){
                   var obj_op = jQuery("#{$this->_attributes['id']}_other_section");
                    if(jQuery(this).attr('id')=='{$this->_attributes["id"]}_other')
                    {
                        obj_op.slideDown();
                        obj_op.children("input[type=text]").attr('disabled', false);
                        obj_op.children("input[type=text]").attr('required', true);
                    } 
                    else
                    {
                         obj_op.slideUp();
                         obj_op.children("input[type=text]").attr('disabled', true);
                         obj_op.children("input[type=text]").attr('required', false);
                    }
                  
                 });
                    
                jQuery('#{$this->_attributes["id"]}_other_input').change(function(){
                    jQuery('#{$this->_attributes["id"]}_other').val(jQuery(this).val());
                    if(jQuery(".data-conditional").length>0){
                        jQuery(".data-conditional").conditionize({});
                    }
                }) ;     
           
JS;
            }
              
             
        }
	public function render() { 
		$labelClass = 'rmradio';//$this->_attributes["type"];
		if(!empty($this->inline))
			$labelClass .= " inline";

		$count = 0;
                //Extract color attribute so that can be applied to text as well.
                $style_str = "";
                if(isset($this->_attributes["style"]))
                {
                    $al = explode(';',$this->_attributes["style"]);                    
                    foreach($al as $a)
                    {
                        if(strpos(trim($a),"color:")=== 0)
                        {
                            $style_str ='style="'.$a.'";'; 
                            break;
                        }
                    }
                }
                echo '<ul class="' .$labelClass. '" '.$style_str.'">';
		foreach($this->options as $value => $text) {
			$value = $this->getOptionValue($value);

			//echo '<label class="', $labelClass . '"> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
			echo '<li> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
			if(isset($this->_attributes["value"]) && $this->_attributes["value"] == $value)
				echo ' checked="checked"';
			//echo '/> ', $text, ' </label> ';
			echo '/><label for="', $this->_attributes["id"], '-', $count,'"> ', $text, '</label> </li> ';
			++$count;
		}                
                 
                if(isset($this->_attributes["rm_is_other_option"]) && $this->_attributes["rm_is_other_option"] == 1){                       //get value of "other" field to be prefilled if provided.
                    $other_val = '';
                    if(isset($this->_attributes["value"]) && !in_array($this->_attributes["value"], array_values($this->options)))
                            $other_val = $this->_attributes["value"];
                   echo '<li>';
                   if($other_val){
                     echo      '<input id="'.$this->_attributes["id"].'_other" type="radio" value="" name="'.$this->getAttribute("name").'" style="'.$this->getAttribute("style").'" checked><label for="'.$this->_attributes["id"].'_other">'.__('Other','custom-registration-form-builder-with-submission-manager').'</label></li>'.
                        '<li id="'.$this->_attributes["id"].'_other_section">'.
                        '<input style="'.$this->getAttribute("style").'" type="text" id="'.$this->_attributes["id"].'_other_input" name="'.$this->getAttribute("name").'" value="'.$other_val.'">';
                   }
                   else
                   {
                     echo  '<input id="'.$this->_attributes["id"].'_other" type="radio" value="" name="'.$this->getAttribute("name").'" style="'.$this->getAttribute("style").'"><label for="'.$this->_attributes["id"].'_other">'.__('Other','custom-registration-form-builder-with-submission-manager').'</label></li>'.
                        '<li id="'.$this->_attributes["id"].'_other_section" style="display:none">'.
                        '<input style="'.$this->getAttribute("style").'" type="text" id="'.$this->_attributes["id"].'_other_input" name="'.$this->getAttribute("name").'" disabled>';

                   }
                    echo   '</li>';
                }
            echo '</ul>';
	}
}
