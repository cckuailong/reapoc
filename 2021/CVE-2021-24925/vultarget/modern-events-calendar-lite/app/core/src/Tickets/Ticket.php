<?php

namespace MEC\Tickets;

class Ticket{

    public $data;

    public function __construct($data){

        $this->data = $data;
    }

    /**     
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get_data($key,$default = null){
        
        $v = isset($this->data[$key]) ? $this->data[$key] : $default;

        return apply_filters('mec_ticket_get_data',$v,$key,$this->data,$default);
    }

    /**
     * @param string $type start|end
     * @return array
     */
    private function _get_time($type){
        
        return array(
            'h' => isset($this->data['ticket_'.$type.'_time_hour']) ? sprintf('%02d',$this->data['ticket_'.$type.'_time_hour']) : '',
            'm' => isset($this->data['ticket_'.$type.'_time_minute']) ? sprintf('%02d',$this->data['ticket_'.$type.'_time_minute']) : '',
            'ampm' => isset($this->data['ticket_'.$type.'_time_ampm']) ? $this->data['ticket_'.$type.'_time_ampm'] : '',
        );
    }

    /**
     * @param string $type all|start|end
     * @param string $format
     * @return string
     */
    public function get_time($type = 'all',$format = 'H:i A'){

        $start = $this->_get_time('start');
        $end = $this->_get_time('end');

        $start_time = "{$start['h']}:{$start['m']} {$start['ampm']}";
        $end_time = "{$end['h']}:{$end['m']} {$end['ampm']}";

        $start = date($format,strtotime($start_time));
        $end = date($format,strtotime($end_time));

        switch($type){
            case 'start':
                return $start_time;
                break;
            case 'end':
                return $end_time;
                break;
            default:
                return "$start_time $end_time";
                break;
        }
    }
}