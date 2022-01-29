<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Map
 *
 * @author CMSHelplive
 */
class Element_Rating extends Element
{

    public $_attributes = array(
        'type' => 'text'
    );
    
    public $jQueryOptions = "";

    /* public function getCSSFiles()
      {
      return array(
      );
      } */

    public function __construct($label, $name, array $properties = null)
    {
        parent::__construct($label, $name, $properties);
        $this->_attributes['id'] = $name;
        
        if(isset($properties['required']))
        $this->_attributes['required']=$properties['required'];

    }

    public function getJSFiles()
    {
        return array('script_rm_rating' => RM_ADDON_BASE_URL . 'public/js/rating3/jquery.rateit.js');
    }
    
    public function getJSDeps()
    {
        return array(
            'script_rm_rating'
        );
    }
    
    public function render()
    {   
        if(isset($this->_attributes['required']))
            $req_attr = "required";
        else
            $req_attr = "";
//        /var_dump($this->_attributes);
        $id = "rm_hidden_rate_".$this->_attributes['id'];
        $max_stars = isset($this->_attributes['max_stars']) ? $this->_attributes['max_stars'] : 5;
        $star_width = isset($this->_attributes['star_width']) ? $this->_attributes['star_width'] : 36;
        $star_face = "star";
        $star_icon = "&#xE838;";
        $is_read_only = isset($this->_attributes['readonly']);
        
        if(isset($this->_attributes['star_face'])){
            
            $star_face = $this->_attributes['star_face'];
            
            switch($this->_attributes['star_face']) {
                case 'heart':
                    $star_icon = "&#xE87D;";
                    break;
                
                case 'face':
                    $star_icon = "&#xE420;";
                    break;
                
                case 'brush':
                    $star_icon = "&#xE3AE;";
                    break;
                
                case 'sun':
                    $star_icon = "&#xE430;";
                    break;
                
                case 'flag':
                    $star_icon = "&#xE153;";
                    break;
                
                case 'snowflake':
                    $star_icon = "&#xEB3B;";
                    break;
                
                case 'bag':
                    $star_icon = "&#xEB3F;";
                    break;
                
                case 'circle':
                    $star_icon = "&#xE061;";
                    break;
                
                case 'thumbup':
                    $star_icon = "&#xE8DC;";
                    break;
            }
        }
        
        $step_size = isset($this->_attributes['step_size']) ? $this->_attributes['step_size'] : 'half';
        $step_size = ($step_size === "half") ? "0.5" : "1";
        $star_color = isset($this->_attributes['star_color']) ? $this->_attributes['star_color'] : 'FBC326';
        ?>  
    <div
         class="rateit rm_rating_face_<?php echo $star_face; ?>"
         id="rm_rateit5"
         data-rateit-min="0"
         data-rateit-max="<?php echo $max_stars; ?>"
         data-rateit-value="<?php echo $this->getAttribute('value'); ?>"
         data-rateit-step="<?php echo $step_size; ?>"
         data-rateit-ispreset="true"
         data-rateit-mode="font"
         data-rateit-icon="<?php echo $star_icon; ?>"
         data-rateit-starwidth="<?php echo $star_width; ?>"
         data-rateit-forcestarwidth="true"
         data-rateit-resetable="false"         
         <?php if($is_read_only): ?>            
            data-rateit-readonly="true"
         <?php else: ?>
            data-rateit-backingfld="#<?php echo $id; ?>"
         <?php endif; ?>
         style="font-family:Material Icons;word-wrap:normal;color:#<?php echo $star_color; ?>"
    >
    <?php if(!$is_read_only): ?>
        <input type="hidden" class="rateitbackend <?php echo $this->getAttribute('class'); ?>" id="<?php echo $id; ?>" name="<?php echo $this->_attributes['id']; ?>" <?php echo $req_attr; ?> value="<?php echo $this->getAttribute('value'); ?>"/>
    <?php endif; ?>
</div>

 
  <?php
    }
    
}
