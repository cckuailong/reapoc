<?php
abstract class viewUms extends baseObjectUms {
    /* 
     * @deprecated
     */
    protected $_tpl = UMS_DEFAULT;
    /*
     * @var string name of theme to load from templates, if empty - default values will be used
     */
    protected $_theme = '';
    /*
     * @var string module code for this view
     */
    protected $_code = '';

    public function display($tpl = '') {
        $tpl = (empty($tpl)) ? $this->_tpl : $tpl;

        if(($content = $this->getContent($tpl)) !== false) {
            echo $content;
        }
    }
	public function getPath($tpl) {
		$path = '';
		$code = $this->_code;
		$parentModule = frameUms::_()->getModule( $this->_code );
		$plTemplate = frameUms::_()->getModule('options')->get('template');		// Current plugin template
		if(empty($plTemplate) || !frameUms::_()->getModule($plTemplate))
			$plTemplate = '';
		if(file_exists(utilsUms::getCurrentWPThemeDir(). 'ums'. DS. $code. DS. $tpl. '.php')) {
            $path = utilsUms::getCurrentWPThemeDir(). 'ums'. DS. $code. DS. $tpl. '.php';
        } elseif($plTemplate && file_exists(frameUms::_()->getModule($plTemplate)->getModDir(). 'templates'. DS. $code. DS. $tpl. '.php')) {
			$path = frameUms::_()->getModule($plTemplate)->getModDir(). 'templates'. DS. $code. DS. $tpl. '.php';
		} elseif(file_exists($parentModule->getModDir(). 'views'. DS. 'tpl'. DS. $tpl. '.php')) { //Then try to find it in module directory
            $path = $parentModule->getModDir(). DS. 'views'. DS. 'tpl'. DS. $tpl. '.php';
        }
		return $path;
	}
	public function getModule() {
		return frameUms::_()->getModule( $this->_code );
	}
	public function getModel($code = '') {
		return frameUms::_()->getModule( $this->_code )->getController()->getModel($code);
	}
    public function getContent($tpl = '') {
        $tpl = (empty($tpl)) ? $this->_tpl : $tpl;
        $path = $this->getPath($tpl);
        if($path) {
            $content = '';
            ob_start();
            require($path);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        return false;
    }
    public function setTheme($theme) {
        $this->_theme = $theme;
    }
    public function getTheme() {
        return $this->_theme;
    }
    public function setTpl($tpl) {
        $this->_tpl = $tpl;
    }
    public function getTpl() {
        return $this->_tpl;
    }
    public function init() {

    }
    public function assign($name, $value) {
        $this->$name = $value;
    }
    public function setCode($code) {
        $this->_code = $code;
    }
    public function getCode() {
        return $this->_code;
    }
	
	/**
	 * This will display form for our widgets
	 */
	public function displayWidgetForm($data = array(), $widget = array(), $formTpl = 'form') {
		$this->assign('data', $data);
        $this->assign('widget', $widget);
		if(frameUms::_()->isTplEditor()) {
			if($this->getPath($formTpl. '_ext')) {
				$formTpl .= '_ext';
			}
		}
		self::display($formTpl);
	}
	public function sizeToPxPt($size) {
		if(!strpos($size, 'px') && !strpos($size, '%'))
			$size .= 'px';
		return $size;
	}
	public function getInlineContent($tpl = '') {
		return preg_replace('/\s+/', ' ', $this->getContent($tpl));
	}
}
