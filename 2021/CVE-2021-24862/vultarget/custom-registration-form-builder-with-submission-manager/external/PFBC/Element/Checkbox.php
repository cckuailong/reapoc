<?php
class Element_Checkbox extends OptionElement {
	public $_attributes = array("type" => "checkbox");
	public $inline;
        
         public function jQueryDocumentReady(){
            if(isset($this->_attributes["rm_is_other_option"]) && $this->_attributes["rm_is_other_option"] == 1){
                echo <<<JS
            
                   
                   jQuery("input[name='{$this->_attributes['name']}']").change(function(){
                   var obj_op = jQuery("#{$this->_attributes['id']}_other_section");
                    if(jQuery(this).attr('id')=='{$this->_attributes["id"]}_other')
                    {
                        if(jQuery(this).prop('checked')){
                             obj_op.slideDown();
                             obj_op.children("input[type=text]").attr('disabled', false);
                             obj_op.children("input[type=text]").attr('required', true);
                            }
                        else{
                            obj_op.slideUp();
                            obj_op.children("input[type=text]").attr('disabled', true);
                            obj_op.children("input[type=text]").attr('required', false);
                        }  
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
		if(isset($this->_attributes["value"])) {
			if(!is_array($this->_attributes["value"]))
				$this->_attributes["value"] = array($this->_attributes["value"]);
		}
		else
			$this->_attributes["value"] = array();

		if(substr($this->_attributes["name"], -2) != "[]")
			$this->_attributes["name"] .= "[]";

		$labelClass = 'rmradio';//'rm'. $this->_attributes["type"];
		if(!empty($this->inline))
			$labelClass .= " inline";

		$count = 0;//Extract color attribute so that can be applied to text as well.
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
                
                //Get base name of the sub element (if specified) to process inside the loop
                $sub_ele = $this->getAdvanceAttr('sub_element');
                $sub_ele_name = ($sub_ele && $sub_ele instanceof Element) ? $sub_ele->getAttribute('name') : '';
                                 
		foreach($this->options as $value => $text) {
			$value = $this->getOptionValue($value);
                        
                        echo '<li><span class="rm-pricefield-wrap"> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
			//echo '<label class="', $labelClass, '"> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked", "required")), ' value="', $this->filter($value), '"';
			if($value && in_array($value, $this->_attributes["value"]))
				echo ' checked="checked"';
			//echo '/> ', $text, ' </label> ';
			echo '/><label for="',$this->_attributes["id"], '-', $count,'"><span>', $text, '</span></label>';
                        ////Render sub element if specified
                        if($sub_ele && $sub_ele instanceof Element)
                        {
                            if($sub_ele_name)
                                $sub_ele->setAttribute("name",$sub_ele_name."[$value]");
                            echo '</span><div class="rmrow">', $this->renderLabel($sub_ele), '<div class="rminput">', $sub_ele->render(), '</div></div>';
                            //$sub_ele->render();
                        }
                        //////End sub element render.
                        echo ' </li> ';
			++$count;
		}
                if(isset($this->_attributes["rm_is_other_option"]) && $this->_attributes["rm_is_other_option"] == 1){                    
                    $other_val = '';
                    if(isset($this->_attributes["value"])):
                    //get value of "other" field to be prefilled if provided.
                    $diff = array_diff($this->_attributes["value"], array_keys($this->options));                    
                    if(count($diff)===1)
                    {
                        $other_val = array_values($diff);
                        $other_val = $other_val[0];
                    }
                    endif;
                   echo '<li>';
                   if(!$other_val)
                   {
                   echo '<input type="checkbox" value="" id="'.$this->_attributes["id"].'_other" name="'.$this->getAttribute("name").'" style="'.$this->getAttribute("style").'"><label for="'.$this->_attributes["id"].'_other">'.__('Other','custom-registration-form-builder-with-submission-manager').'</label></li>'.
                        '<li id="'.$this->_attributes["id"].'_other_section" style="display:none">'.
                        '<input style="'.$this->getAttribute("style").'" type="text" id="'.$this->_attributes["id"].'_other_input" disabled>';
                   }
                   else
                   {
                    echo '<input type="checkbox" value="" id="'.$this->_attributes["id"].'_other" name="'.$this->getAttribute("name").'" style="'.$this->getAttribute("style").'" checked><label for="'.$this->_attributes["id"].'_other">'.__('Other','custom-registration-form-builder-with-submission-manager').'</label></li>'.
                        '<li id="'.$this->_attributes["id"].'_other_section">'.
                        '<input style="'.$this->getAttribute("style").'" type="text" id="'.$this->_attributes["id"].'_other_input" value="'.$other_val.'">';   
                   }
                   echo  '</li>';
                }
                    echo '</ul>';
	}
        
        //Function taken from userform. This renders sub-element's label.
        public function renderLabel(Element $element)
        {

            $label = $element->getLabel();

            if (!empty($label))
            {
                //echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
                $field_class = trim("rmfield ".$element->getAdvanceAttr('exclass_field'));
                echo '<div class="'.$field_class.'" for="', $element->getAttribute("id"), '" style="',$element->getAttribute("labelstyle"),'"><label>';


                if ($element->isRequired()  && ($element->show_asterix()=='yes'))
                {
                echo '<sup class="required">*</sup>';
                }
                else
                {
                  echo '<sup class="required">&nbsp;&nbsp;</sup>';
                }
                echo $label, '</label></div>';
            }
        }
}
