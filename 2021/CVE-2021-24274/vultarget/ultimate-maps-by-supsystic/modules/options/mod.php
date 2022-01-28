<?php
class optionsUms extends moduleUms {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search
	
	public function init() {
		dispatcherUms::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		dispatcherUms::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
        dispatcherUms::addAction('adminMenuAccessCap', array($this, 'adminMenuAccessRoles'), 10, 1);
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', UMS_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 60,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		// empty for now
		return array();
	}
	public function getAdminPage() {
		if(installerUms::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			return frameUms::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherUms::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', UMS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0), 
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = reqUms::getVar('tab');
		return empty($reqTab) ? 'maps' : $reqTab;
	}
	public function getTabUrl($tab = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameUms::_()->getModule('adminmenu')->getMainLink();
		}
		return empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
	}
	public function getRolesList() {
		if(!function_exists('get_editable_roles')) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		return get_editable_roles();
	}
	public function getAvailableUserRolesSelect() {
		$rolesList = $this->getRolesList();
		$rolesListForSelect = array();
		foreach($rolesList as $rKey => $rData) {
			$rolesListForSelect[ $rKey ] = $rData['name'];
		}
		return $rolesListForSelect;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$this->_options = dispatcherUms::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', UMS_LANG_CODE),
					'opts' => array(
						'def_engine' => array('label' => __('Maps Engine', UMS_LANG_CODE), 'desc' => __('Select required Maps Engine to drive your maps.', UMS_LANG_CODE), 'def' => 'leaflet', 'html' => 'selectbox', 
							'options' => frameUms::_()->getModule('maps')->getEnginesForSelect(), 
							'attrs' => 'style="width: 300px;"'),
						'bing_key' => array('label' => __('Bing Maps API Key', UMS_LANG_CODE), 'desc' => sprintf(__('Get your Bing Maps API Key under your Microsoft Bing account - check it <a href="%s" target="_blank">here</a>', UMS_LANG_CODE), 'https://www.bingmapsportal.com/Application'), 'def' => '', 'html' => 'text'/*, 'connect' => 'def_engine:bing'*/),
						'mapbox_key' => array('label' => __('MapBox Access Token', UMS_LANG_CODE), 'desc' => sprintf(__('Get your MapBox Access Token under your MapBox account - check it <a href="%s" target="_blank">here</a>', UMS_LANG_CODE), 'https://www.mapbox.com/account/'), 'def' => '', 'html' => 'text'/*, 'connect' => 'def_engine:mapbox'*/),
						'thunderforest_key' => array('label' => __('Thunderforest API Key', UMS_LANG_CODE), 'desc' => sprintf(__('Get your Thunderforest API Key under your Thunderforest account - check it <a href="%s" target="_blank">here</a>', UMS_LANG_CODE), 'https://manage.thunderforest.com/dashboard'), 'def' => '', 'html' => 'text'/*, 'connect' => 'def_engine:mapbox'*/),
						
						//'api_domain' => array('label' => __('API Domain', UMS_LANG_CODE), 'desc' => __('Sets domain for google API scripts', UMS_LANG_CODE), 'def' => '', 'html' => 'selectbox', 'options' => array('https://maps.googleapis.com/' => 'https://maps.googleapis.com/', 'https://maps.google.cn/' => 'https://maps.google.cn/'), 'attrs' => 'style="width: 300px;"'),
						//'user_api_key' => array('label' => __('User API key', UMS_LANG_CODE), 'desc' => __("You can use your own Google API key, check the <a href='//supsystic.com/google-maps-api-key/' target='_blank'>instruction</a> how to create it. To use plugin's default API key leave this field blank.", UMS_LANG_CODE), 'def' => '', 'html' => 'text', 'attrs' => 'style="width: 300px;"'),
						//'send_stats' => array('label' => __('Send usage statistics', UMS_LANG_CODE), 'desc' => __('Send information about what plugin options you prefer to use, this will help us make our solution better for You.', UMS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'add_love_link' => array('label' => __('Enable promo link', UMS_LANG_CODE), 'desc' => __('We are trying to make our plugin better for you, and you can help us with this. Just check this option - and small promotion link will be added in the bottom of your Google Maps. This is easy for you - but very helpful for us!', UMS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'access_roles' => array('label' => __('User role can use plugin', UMS_LANG_CODE), 'desc' => __('User with next roles will have access to whole plugin from admin area.', UMS_LANG_CODE), 'def' => 'administrator', 'html' => 'selectlist', 'options' => array($this, 'getAvailableUserRolesSelect'), 'attrs' => 'style="width: 300px;"', 'pro' => ''),
					),
				),
			));
			$isPro = frameUms::_()->getModule('supsystic_promo')->isPro();
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = frameUms::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=ultimatemaps');
					}
				}
			}
			$this->getModel()->fillInValues( $this->_options );
		}
		return $this->_options;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}

    /**
     * @param $caps string
     * @return string
     */
	public function adminMenuAccessRoles($caps) {
	    $options = $this->getAll();
        if (frameUms::_()->getModule('supsystic_promo')->isPro() && isset($options['general']['opts']['access_roles']['value'])) {
            $roles = $options['general']['opts']['access_roles']['value'];
            if(!is_array($roles)) {
            	$roles = array(0 => $roles);
            }
            if (in_array('subscriber', $roles)) {
                $caps = 'read';
            }elseif (in_array('contributor', $roles)) {
                $caps = 'edit_posts';
            }elseif (in_array('author', $roles)) {
                $caps = 'publish_posts';
            }elseif (in_array('editor', $roles)) {
                $caps = 'delete_others_pages';
            }
        }

        return $caps;
    }
}

