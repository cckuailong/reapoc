<?php
/**
 * Class to generate fiels and output them in attachment by http (https) protocol
 */
class filegeneratorUms {
    static protected $_instances = array();
    protected $_filename = '';
    protected $_data = '';
    protected $_type = '';
    public function __construct($filename, $data, $type) {
        $this->_filename = $filename;
        $this->_data = $data;
        $this->_type = strtolower($type);
    }
    static public function getInstance($filename, $data, $type) {
        $name = md5($filename. $data. $type);
        if(!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new filegeneratorUms($filename, $data, $type);
        }
        return self::$_instances[$name];
    }
    static public function _($filename, $data, $type) {
        return self::getInstance($filename, $data, $type);
    }
    public function generate() {
        switch($this->_type) {
            case 'txt':
                $this->_getTxtHeader();
                break;
            case 'csv':
                $this->_getCsvHeader();
                break;
            default:
                $this->_getDefaultHeader();
                break;
        }
        echo $this->_data;
        exit();
    }
    protected function _getTxtHeader() {
        header('Content-Disposition: attachment; filename="'. $this->_filename. '.txt"');
        header('Content-type: text/plain');
    }
    protected function _getCsvHeader() {
        header('Content-Disposition: attachment; filename="'. $this->_filename. '.csv"');
        header('Content-type: application/csv');
    }
    protected function _getDefaultHeader() {
        header('Content-Disposition: attachment; filename="'. $this->_filename. '"');
        header('Content-type: '. $this->_type);
    }
}
