<?php
class responseCfs {
    public $code = 0;
    public $error = false;
    public $errors = array();
    public $messages = array();
    public $html = '';
    public $data = array();
    /**
	 * Marker to set data not in internal $data var, but set it as object parameters
	 */
	private $_ignoreShellData = false;
	public function getReqType() {
		return reqCfs::getVar('reqType');
	}
	public function isAjax() {
		return $this->getReqType() == 'ajax';
	}
    public function ajaxExec($forceAjax = false) {
        $isAjax = $this->isAjax();
        $redirect = reqCfs::getVar('redirect');
        if(count($this->errors) > 0)
            $this->error = true;
        if($isAjax || $forceAjax)
            exit( json_encode_utf_normal($this) );
        /*if($redirect)
            redirectCfs($redirect);*/
        return $this;
    }
	public function mainRedirect($redirectUrl = '') {
		$redirectUrl = empty($redirectUrl) ? CFS_SITE_URL : $redirectUrl;
		$redirectData = array();
		if(!empty($this->errors)) {
			$redirectData['cfsErrors'] = $this->errors;
		}
		if(!empty($this->messages)) {
			$redirectData['cfsMsgs'] = $this->messages;
		}
        return redirectCfs($redirectUrl. (strpos($redirectUrl, '?') ? '&' : '?'). http_build_query($redirectData));
	}
    public function error() {
        return $this->error;
    }
    public function addError($error, $key = '') {
        if(empty($error)) return;
        $this->error = true;
        if(is_array($error))
            $this->errors = array_merge($this->errors, $error);
        else {
            if(empty($key))
                $this->errors[] = $error;
            else
                $this->errors[$key] = $error;
        }
    }
    /**
     * Alias for responseCfs::addError, @see addError method
     */
    public function pushError($error, $key = '') {
        return $this->addError($error, $key);
    }
    public function addMessage($msg) {
        if(empty($msg)) return;
        if(is_array($msg))
            $this->messages = array_merge($this->messages, $msg);
        else
            $this->messages[] = $msg;
    }
	public function getMessages() {
		return $this->messages;
	}
    public function setHtml($html) {
        $this->html = $html;
    }
    public function addData($data, $value = NULL) {
		if(empty($data)) return;
		if($this->_ignoreShellData) {
			if(!is_array($data))
				$data = array($data => $value);
			foreach($data as $key => $val) {
				$this->{$key} = $val;
			}
		} else {
			if(is_array($data))
				$this->data = array_merge($this->data, $data);
			else
				$this->data[$data] = $value;
		}
    }
	public function setData($data) {
		$this->data = $data;
	}
    public function getErrors() {
        return $this->errors;
    }
	public function ignoreShellData() {
		$this->_ignoreShellData = true;
	}
}

