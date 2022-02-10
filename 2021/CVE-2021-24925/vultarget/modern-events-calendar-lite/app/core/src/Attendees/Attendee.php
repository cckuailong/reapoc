<?php

namespace MEC\Attendees;

class Attendee{

    /**
     * Attendee Data
     *
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     */
    public function __construct( $data ){

        $this->set_data( $data );
    }

    /**
     * @param array $data
     */
    public function set_data( $data ){

        $this->data = [
            'attendee_id' => isset($data['attendee_id']) ? $data['attendee_id'] : '',
            'post_id' => isset($data['post_id']) ? $data['post_id'] : '',
            'event_id' => isset($data['event_id']) ? $data['event_id'] : '',
            'occurrence' => isset($data['occurrence']) ? $data['occurrence'] : '',
            'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
            'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
            'email' => isset($data['email']) ? $data['email'] : '',
            'count' => isset($data['count']) && $data['count'] ? $data['count'] : 1,
            'reg' => isset($data['reg']) && is_array($data['reg']) ? $data['reg'] : [],
        ];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get_data( $key = null ){

        return !is_null( $key ) ? $this->data[$key] : $key;
    }

    /**
     * @return int
     */
    public function get_id(){

        return $this->get_data( 'attendee_id' );
    }

    /**
     * @return int
     */
    public function get_event_id(){

        return $this->get_data( 'event_id' );
    }

    /**
     * @return int
     */
    public function get_occurrence_id(){

        return $this->get_data( 'occurrence' );
    }

    /**
     * @return string
     */
    public function get_email(){

        return $this->get_data( 'email' );
    }

    /**
     * @return string
     */
    public function get_first_name(){

        return $this->get_data( 'first_name' );
    }

    /**
     * @return string
     */
    public function get_last_name(){

        return $this->get_data( 'last_name' );
    }

    /**
     * @return string
     */
    public function get_name(){

        return $this->get_first_name().' '.$this->get_last_name();
    }

    /**
     * @return int
     */
    public function get_count(){

        return $this->get_data( 'count' );
    }

    /**
     * @return int
     */
    public function get_reg_data(){

        return $this->get_data( 'reg' );
    }
}