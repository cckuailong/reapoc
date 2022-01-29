<?php

/**
 * Login field model
 * 
 *
 * @author cmshelplive
 */
class RM_Login_Fields
{
    public $options= null;
    public $field_type= null;
    public $option_values= array();
    

    public function initialize($type){
        $this->field_type= $type;
        $common_options= array(
            'input_selected_icon_codepoint',
            'icon_fg_color',
            'icon_bg_color',
            'icon_bg_alpha',
            'icon_shape',
            'field_css_class',
            'field_label',
            'placeholder',
            'page_no',
            'field_id',
            'is_field_primary',
        );
        switch($this->field_type){
            case 'username': $this->options= array_merge(array('username_accepts'),$common_options); break;
            case 'password': $this->options= array_merge(array(),$common_options); break;
            case 'HTMLH': $this->options= array_merge(array('field_value'),$common_options); break;
            case 'HTMLP': $this->options= array_merge(array('field_value'),$common_options); break;
            case 'Divider': $this->options= array_merge(array(),$common_options); break;
            case 'Spacing': $this->options= array_merge(array(),$common_options); break;
            case 'RichText': $this->options= array_merge(array('field_value'),$common_options); break;
            case 'Timer': $this->options= array_merge(array(),$common_options); break;
            case 'Link': $this->options= array_merge(array('link_type','link_href','link_page','link_same_window'),$common_options); break;
            case 'YouTubeV': $this->options= array_merge(array('field_value','yt_player_width','yt_player_height','yt_auto_play','yt_repeat','yt_related_videos'),$common_options); break;
            case 'Iframe': $this->options= array_merge(array('field_value','if_width','if_height'),$common_options); break;
            default: $this->options= array();
        }
        
    }
    
    public function get_field_type()
    {
        return $this->field_type;
    }

    public function get_field_options()
    {
        return  $this->option_values;
    }

    public function set_field_type($type)
    {
        $this->field_type = $type;
    }

    public function default_option_values(){
        
        foreach($this->options as $option){
            $this->option_values[$option]= '';
        }
    }
    public function set(array $params)
    {   $this->default_option_values();
        foreach ($params as $property => $value)
        {   
            if (in_array($property, $this->options))
            {  
                $this->option_values[$property] = $value;
            }
        }
        $this->option_values['field_type']= $this->field_type;
    }

    public function get_as_field_options($options){
        $option_values= array();
        foreach($this->options as $option){
            if(!isset($options[$option]))
                continue;
            if(is_array($options[$option])){
                 $option_values[$option]= $options[$option][0];
                 continue;
            }
            $option_values[$option]= $options[$option];
           
        }
        return $option_values;
    }
}
