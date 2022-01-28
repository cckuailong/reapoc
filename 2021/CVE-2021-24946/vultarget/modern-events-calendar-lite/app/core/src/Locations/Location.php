<?php

namespace MEC\Locations;

use MEC\Singleton;

/**
 * Not completed
 */
class Locations extends Singleton {

    public $ID;
    public $data;
    /**
     * @param \WP_Post|int $location
     */
    public function __construct($location){


    }


    public function get_locations($query){
        $term_id = $this->ID;
        if(!$term_id){
            return array();
        }

        return array(
            'id'=>$term_id,
            'name'=>$term->name,
            'address'=> get_term_meta( $term_id, 'address', true),
            'latitude'=>get_term_meta($term_id, 'latitude', true),
            'longitude'=>get_term_meta($term_id, 'longitude', true),
            'url'=>get_term_meta($term_id, 'url', true),
            'thumbnail'=>get_term_meta($term_id, 'thumbnail', true)
        );
    }
}