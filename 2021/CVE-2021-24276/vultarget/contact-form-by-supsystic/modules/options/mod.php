<?php
class optionsCfs extends moduleCfs {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search
	
	public function init() {
		//dispatcherCfs::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		add_action('init', array($this, 'initAllOptValues'), 99);	// It should be init after all languages was inited (frame::connectLang)
		dispatcherCfs::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
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
		$allowKeys = array('add_love_link', 'disable_autosave');
		$res = array();
		foreach($allowKeys as $k) {
			$res[ $k ] = $this->get($k);
		}
		return $res;
	}
	public function getAdminPage() {
		if(installerCfs::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			installerCfs::setUsed();	// Show this welcome page - only one time
			return frameCfs::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', CFS_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 30,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherCfs::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', CFS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0), 
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
		$reqTab = reqCfs::getVar('tab');
		return empty($reqTab) ? 'forms' : $reqTab;
	}
	public function getTabUrl($tab = '', $params = array()) {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameCfs::_()->getModule('adminmenu')->getMainLink();
		}
		$url = empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
		if(!empty($params)) {
			$url .= '&'. http_build_query($params);
		}
		return $url;
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
			$defSendmailPath = function_exists('ini_get') ? @ini_get('sendmail_path') : false;
			if (empty($defSendmailPath) && !stristr($defSendmailPath, 'sendmail')) {
				$defSendmailPath = '/usr/sbin/sendmail';
			}
			$this->_options = dispatcherCfs::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', CFS_LANG_CODE),
					'opts' => array(
						'send_stats' => array('label' => __('Send usage statistics', CFS_LANG_CODE), 'desc' => __('Send information about what plugin options you prefer to use, this will help us make our solution better for You.', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'enable_submit_ip_antispam' => array('label' => __('Enable blocking Contact from same IP', CFS_LANG_CODE), 'desc' => __('Our plugin have feature to block form submission from same IP more then one time per hour - to avoid spam. You can enable this feature here.', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'disable_autosave' => array('label' => __('Disable autosave on Form Edit', CFS_LANG_CODE), 'desc' => __('By default our plugin will make autosave all your changes that you do in Form edit screen, but you can disable this feature here. Just don\'t forget to save your Form each time you make any changes in it.', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'add_love_link' => array('label' => __('Enable promo link', CFS_LANG_CODE), 'desc' => __('We are trying to make our plugin better for you, and you can help us with this. Just check this option - and small promotion link will be added in the bottom of your Form. This is easy for you - but very helpful for us!', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'access_roles' => array('label' => __('User role can use plugin', CFS_LANG_CODE), 'desc' => __('User with next roles will have access to whole plugin from admin area.', CFS_LANG_CODE), 'def' => 'administrator', 'html' => 'selectlist', 'options' => array($this, 'getAvailableUserRolesSelect'), 'pro' => ''),
						'foot_assets' => array('label' => __('Load Assets in Footer', CFS_LANG_CODE), 'desc' => __('Force load all plugin CSS and JavaScript files in footer - to increase page load speed. Please make sure that you have correct footer.php file in your WordPress theme with wp_footer() function call in it.', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'disable_email_html_type' => array('label' => __('Disable HTML Emails content type', CFS_LANG_CODE), 'desc' => __('Some servers fail send emails with HTML content type: content-type = "text/html", so if you have problems with sending emails from our plugn - try to disable this feature here.', CFS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						
						'mail_send_engine' => array('label' => __('Send With', CFS_LANG_CODE), 'desc' => __('You can send your emails with different email sendng engines.', CFS_LANG_CODE), 'def' => 'wp_mail', 'html' => 'selectbox',
							'options' => array('wp_mail' => __('WordPress PHP Mail', CFS_LANG_CODE), 'smtp' => __('Third party providers (SMTP)', CFS_LANG_CODE), 'sendmail' => __('Sendmail', CFS_LANG_CODE))),
						
						'smtp_host' => array('label' => __('SMTP Hostname', CFS_LANG_CODE), 'desc' => __('e.g. smtp.mydomain.com', CFS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_login' => array('label' => __('SMTP Login', CFS_LANG_CODE), 'desc' => __('Your email login', CFS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_pass' => array('label' => __('SMTP Password', CFS_LANG_CODE), 'desc' => __('Your email password', CFS_LANG_CODE), 'html' => 'password', 'connect' => 'mail_send_engine:smtp'),
						'smtp_port' => array('label' => __('SMTP Port', CFS_LANG_CODE), 'desc' => __('Port for your SMTP provider', CFS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:smtp'),
						'smtp_secure' => array('label' => __('SMTP Secure', CFS_LANG_CODE), 'desc' => __('Use secure SMTP connection. If you enable this option - make sure that your server support such secure connections.', CFS_LANG_CODE), 'html' => 'selectbox', 'connect' => 'mail_send_engine:smtp',
							'options' => array('' => __('No', CFS_LANG_CODE), 'ssl' => 'SSL', 'tls' => 'TLS'), 'def' => ''),
						
						'sendmail_path' => array('label' => __('Sendmail Path', CFS_LANG_CODE), 'desc' => __('You can check it on your server, or ask about it - in your hosting provider.', CFS_LANG_CODE), 'html' => 'text', 'connect' => 'mail_send_engine:sendmail', 'def' => $defSendmailPath),
						
						'remove_expire_contacts' => array('label' => __('Remove Contact Forms data after days passed', CFS_LANG_CODE), 'desc' => __('CAUTION: Do Not use this feature if you do not exactly know hat it is doing! It can delete contact form data!<br />You may want not to store all of your contact form data in your database - to make it cleaner, or for example due to some laws (in some countries you can\'t store users data all time, only few days). For this reason you can set here number of days to store users contact forms data. After those days passed (starting from user submit form) - his \ her data will be removed. Leave this field with zero value to disable this functionality.', CFS_LANG_CODE), 'def' => '0', 'html' => 'number'),
						'expire_contacts_last_check' => array('label' => 'Internal option', 'desc' => 'Just to not bussy database too much', 'def' => ''),
					),
				),
			));
			$isPro = frameCfs::_()->getModule('supsystic_promo')->isPro();
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = frameCfs::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=forms');
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
}

