<?php
class optionsModelUms extends modelUms {
	private $_values = array();
	private $_valuesLoaded = false;

    public function get($optKey) {
		$this->_loadOptValues();
		return isset($this->_values[ $optKey ]) ? $this->_values[ $optKey ]['value'] : false;
    }
	public function isEmpty($optKey) {
		$value = $this->get($optKey);
		return $value === false;
	}
	public function save($optKey, $val, $ignoreDbUpdate = false) {
		$this->_loadOptValues();
		if(!isset($this->_values[ $optKey ]) || $this->_values[ $optKey ]['value'] !== $val) {
			if(isset($this->_values[ $optKey ]) || !isset($this->_values[ $optKey ]['value']))
				$this->_values[ $optKey ] = array();
			$this->_values[ $optKey ]['value'] = $val;
			$this->_values[ $optKey ]['changed_on'] = time();
			if(!$ignoreDbUpdate) {
				$this->_updateOptsInDb();
			}
			//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('option.'. $optKey);
		}
	}
	public function getAll() {
		$this->_loadOptValues();
		return $this->_values;
	}
	/**
	 * Pass throught refferer - to not lose memory for copy of same opts array
	 */
	public function fillInValues(&$options) {
		$this->_loadOptValues();
		foreach($options as $cKey => $cData) {
			foreach($cData['opts'] as $optKey => $optData) {
				$value = 0;
				$changedOn = 0;
				// Retrive value from saved options
				if(isset($this->_values[ $optKey ])) {
					$value = $this->_values[ $optKey ]['value'];
					$changedOn = isset($this->_values[ $optKey ]['changed_on']) ? $this->_values[ $optKey ]['changed_on'] : '';
				} elseif(isset($optData['def'])) {	// If there were no saved data - set it as default
					$value = $optData['def'];
				}
				$options[ $cKey ]['opts'][ $optKey ]['value'] = $value;
				$options[ $cKey ]['opts'][ $optKey ]['changed_on'] = $changedOn;
				if(!isset($this->_values[ $optKey ]['value'])) {
					$this->_values[ $optKey ]['value'] = $value;
				}
			}
		}
	}
    public function saveGroup($d = array()) {
		if(isset($d['opt_values']) && is_array($d['opt_values']) && !empty($d['opt_values'])) {
			dispatcherUms::doAction('beforeSaveOpts', $d);
			foreach($d['opt_values'] as $code => $val) {
				$this->save($code, $val, true);
			}
			$this->_updateOptsInDb();
			return true;
		} else
			$this->pushError(__('Empty data to save option', UMS_LANG_CODE));
        return false;
    }
	private function _updateOptsInDb() {
		update_option(UMS_CODE. '_opts_data', $this->_values);
	}
	private function _loadOptValues() {
		if(!$this->_valuesLoaded) {
			$this->_values = get_option(UMS_CODE. '_opts_data');
			if(empty($this->_values))
				$this->_values = array();
			$this->_valuesLoaded = true;
			dispatcherUms::doAction('afterOptsLoaded');
		}
	}

	public function isValuesLoaded() {
		return $this->_valuesLoaded;
	}
}
