<?php
abstract class baseObjectUms {
    protected $_internalErrors = array();
    protected $_haveErrors = false;
    public function pushError($error, $key = '') {
        if(is_array($error)) {
            $this->_internalErrors = array_merge ($this->_internalErrors, $error);
        } elseif(empty($key)) {
            $this->_internalErrors[] = $error;
        } else {
            $this->_internalErrors[ $key ] = $error;
        }
        $this->_haveErrors = true;
    }
    public function getErrors() {
        return $this->_internalErrors;
    }
    public function haveErrors() {
        return $this->_haveErrors;
    }
}

