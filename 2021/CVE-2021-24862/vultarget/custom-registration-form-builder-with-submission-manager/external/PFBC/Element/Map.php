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
class Element_Map extends Element
{

    public $_attributes = array(
        'type' => 'text',
        'class' => 'rm-map-controls rm_map_autocomplete rm-map-controls-uninitialized',
        'onkeydown' => 'rm_prevent_submission(event)'

    );
    public $jQueryOptions = "";
    public $api_key;

    /* public function getCSSFiles()
      {
      return array(
      );
      } */

    public function __construct($label, $name, $api_key, array $properties = null)
    {
        parent::__construct($label, $name, $properties);
        $this->_attributes['id'] = $name;
        $this->api_key = $api_key;
    }

    public function getJSFiles()
    {
        return array(
            'script_rm_map' => RM_BASE_URL . 'public/js/script_rm_map.js',
            'google_map_api' => $this->_form->getPrefix() . "://maps.googleapis.com/maps/api/js?key=".$this->api_key."&libraries=places&callback=rmInitGoogleApi",
        );
    }

    public function getJSDeps()
    {
        return array(
            'script_rm_map'
        );
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        //echo 'initMap();';
    }

    public function render()
    {
        ?>  
        <div class="rmmap_container">
            <input <?php echo $this->getAttributes(); ?>>
            <div style="height:350px" class="map" id="map<?php echo $this->_attributes['id']; ?>"></div></div>
        <?php
    }

}
