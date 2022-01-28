<?php
class supsystic_promoUms extends moduleUms {
	private $_mainLink = '';
	private $_specSymbols = array(
		'from'	=> array('?', '&'),
		'to'	=> array('%', '^'),
	);
	private $_minDataInStatToSend = 20;	// At least 20 points in table shuld be present before send stats
	public function __construct($d) {
		parent::__construct($d);
		$this->getMainLink();
	}
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		if(is_admin()) {
			$this->checkStatisticStatus();
		}
		$this->weLoveYou();
		dispatcherUms::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherUms::addAction('beforeSaveOpts', array($this, 'checkSaveOpts'));
		dispatcherUms::addAction('addMapBottomControls', array($this, 'checkWeLoveYou'), 99);
		dispatcherUms::addAction('discountMsg', array($this, 'getDiscountMsg'));
		add_action('admin_notices', array($this, 'checkAdminPromoNotices'));
		add_action('admin_notices', array($this, 'showUserApiKeyAdminNotice'));
	}
	function showUserApiKeyAdminNotice() {
		// Check each active engine and it's API key
		// TODO: Add on main site - articles about each engine - and setup here links to it's documentation
		$settingsLink = frameUms::_()->getModule('options')->getTabUrl('settings');
		$engines = frameUms::_()->getModule('maps')->getEngines();
		$defEngine = frameUms::_()->getModule('options')->get('def_engine');
		$usedEngines = frameUms::_()->getModule('maps')->getModel()->getUsedEngines();
		$allRequiredEngines = array($defEngine);
		$errorsForEngines = array();
		if(!empty($usedEngines) && is_array($usedEngines)) {
			foreach($usedEngines as $ed) {
				if(!empty($ed['engine']) && !in_array($ed['engine'], $allRequiredEngines)) {
					$allRequiredEngines[] = $ed['engine'];
				}
			}
		}
		foreach($allRequiredEngines as $eKey) {
			if(isset($engines[ $eKey ]['key_name']) && !empty($engines[ $eKey ]['key_name'])) {
				$savedKey = trim(frameUms::_()->getModule('options')->get( $engines[ $eKey ]['key_name'] ));
				if(empty($savedKey)) {
					$errorsForEngines[ $eKey ] = $engines[ $eKey ];
				}
			}
		}
		if(!empty($errorsForEngines)) {
			foreach($errorsForEngines as $eKey => $ed) {
				printf('<div class="%1$s" data-code=""><p>%2$s</p></div>',
						'updated notice is-dismissible supsystic-admin-notice',
						sprintf(__('Please, set your API key for %s in Ultimate Maps by Supsystic plugin <a href="%s">Settings</a>!', UMS_LANG_CODE), $ed['label'], $settingsLink));
			}
		}
	}
	public function checkAdminPromoNotices() {
		if(!frameUms::_()->isAdminPlugOptsPage())	// Our notices - only for our plugin pages for now
			return;
		$notices = array();
		// Start usage
		$startUsage = (int) frameUms::_()->getModule('options')->get('start_usage');
		$currTime = time();
		$day = 24 * 3600;
		if($startUsage) {	// Already saved
			$rateMsg = sprintf(__("<h3>Hey, I noticed you just use %s over a week - that's awesome!</h3><p>Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.</p>", UMS_LANG_CODE), UMS_WP_PLUGIN_NAME);
			$rateMsg .= '<p><a href="https://wordpress.org/support/view/plugin-reviews/'. UMS_WP_NAME. '?rate=5#postform" target="_blank" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', UMS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', UMS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('I already did', UMS_LANG_CODE). '</a></p>';
			$enbPromoLinkMsg = sprintf(__("<h3>More then eleven days with our %s plugin - Congratulations!</h3>", UMS_LANG_CODE), UMS_WP_PLUGIN_NAME);
			$enbPromoLinkMsg .= __("<p>On behalf of the entire <a href='https://supsystic.com/' target='_blank'>supsystic.com</a> company I would like to thank you for been with us, and I really hope that our software helped you.</p>", UMS_LANG_CODE);
			$enbPromoLinkMsg .= __("<p>And today, if you want, - you can help us. This is really simple - you can just add small promo link to our site under your maps. This is small step for you, but a big help for us! Sure, if you don't want - just skip this and continue enjoy our software!</p>", UMS_LANG_CODE);
			$enbPromoLinkMsg .= '<p><a href="#" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', UMS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', UMS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('Skip', UMS_LANG_CODE). '</a></p>';
			$checkOtherPlugins = '<p>'
				. sprintf(__("Check out <a href='%s' target='_blank' class='button button-primary' data-statistic-code='hide'>our other Plugins</a>! Years of experience in WordPress plugins developers made those list unbreakable!", UMS_LANG_CODE), frameUms::_()->getModule('options')->getTabUrl('featured-plugins'))
			. '</p>';
			$needGoogleMapsMsg = '<p>'
					. sprintf(__('Need <b>Google Maps</b>? Find it in our <a target="_blank" href="%s">Easy Google Maps</a> plugin or directly on <a href="%s" target="_blank">WordPress.org</a>!', UMS_LANG_CODE), admin_url('plugin-install.php?tab=search&type=term&s=Google+Maps+Easy'), 'https://wordpress.org/plugins/google-maps-easy/')
					. '</p>';
			$notices = array(
				'rate_msg' => array('html' => $rateMsg, 'show_after' => 7 * $day),
				'enb_promo_link_msg' => array('html' => $enbPromoLinkMsg, 'show_after' => 11 * $day),
				'check_other_plugs_msg' => array('html' => $checkOtherPlugins, 'show_after' => 1 * $day),
				'need_google_maps' => array('html' => $needGoogleMapsMsg, 'show_after' => 0),
			);
			foreach($notices as $nKey => $n) {
				if($currTime - $startUsage <= $n['show_after']) {
					unset($notices[ $nKey ]);
					continue;
				}
				$done = (int) frameUms::_()->getModule('options')->get('done_'. $nKey);
				if($done) {
					unset($notices[ $nKey ]);
					continue;
				}
				$hide = (int) frameUms::_()->getModule('options')->get('hide_'. $nKey);
				if($hide) {
					unset($notices[ $nKey ]);
					continue;
				}
				$later = (int) frameUms::_()->getModule('options')->get('later_'. $nKey);
				if($later && ($currTime - $later) <= 2 * $day) {	// remember each 2 days
					unset($notices[ $nKey ]);
					continue;
				}
				if($nKey == 'enb_promo_link_msg' && (int)frameUms::_()->getModule('options')->get('add_love_link')) {
					unset($notices[ $nKey ]);
					continue;
				}
			}
		} else {
			frameUms::_()->getModule('options')->getModel()->save('start_usage', $currTime);
		}
		if(!empty($notices)) {
			if(isset($notices['rate_msg']) && isset($notices['enb_promo_link_msg']) && !empty($notices['enb_promo_link_msg'])) {
				unset($notices['rate_msg']);	// Show only one from those messages
			}
			$html = '';
			foreach($notices as $nKey => $n) {
				$this->getModel()->saveUsageStat($nKey. '.'. 'show', true);
				$html .= '<div class="updated notice is-dismissible supsystic-admin-notice" data-code="'. $nKey. '">'. $n['html']. '</div>';
			}
			echo $html;
		}
	}
	public function addAdminTab($tabs) {
		$tabs['overview'] = array(
			'label' => __('Overview', UMS_LANG_CODE), 'callback' => array($this, 'getOverviewTabContent'), 'fa_icon' => 'fa-info', 'sort_order' => 5,
		);
		$tabs['featured-plugins'] = array(
			'label' => __('Featured Plugins', UMS_LANG_CODE), 'callback' => array($this, 'showFeaturedPluginsPage'), 'fa_icon' => 'fa-heart', 'sort_order' => 99,
		);
		return $tabs;
	}
	public function getOverviewTabContent() {
		return $this->getView()->getOverviewTabContent();
	}
	// We used such methods - _encodeSlug() and _decodeSlug() - as in slug wp don't understand urlencode() functions
	private function _encodeSlug($slug) {
		return str_replace($this->_specSymbols['from'], $this->_specSymbols['to'], $slug);
	}
	private function _decodeSlug($slug) {
		return str_replace($this->_specSymbols['to'], $this->_specSymbols['from'], $slug);
	}
	public function decodeSlug($slug) {
		return $this->_decodeSlug($slug);
	}
	public function modifyMainAdminSlug($mainSlug) {
		$firstTimeLookedToPlugin = !installerUms::isUsed();
		if($firstTimeLookedToPlugin) {
			$mainSlug = $this->_getNewAdminMenuSlug($mainSlug);
		}
		return $mainSlug;
	}
	private function _getWelcomMessageMenuData($option, $modifySlug = true) {
		return array_merge($option, array(
			'page_title'	=> __('Welcome to Supsystic Secure', UMS_LANG_CODE),
			'menu_slug'		=> ($modifySlug ? $this->_getNewAdminMenuSlug( $option['menu_slug'] ) : $option['menu_slug'] ),
			'function'		=> array($this, 'showWelcomePage'),
		));
	}
	public function addWelcomePageToMenus($options) {
		$firstTimeLookedToPlugin = !installerUms::isUsed();
		if($firstTimeLookedToPlugin) {
			foreach($options as $i => $opt) {
				$options[$i] = $this->_getWelcomMessageMenuData( $options[$i] );
			}
		}
		return $options;
	}
	private function _getNewAdminMenuSlug($menuSlug) {
		// We can't use "&" symbol in slug - so we used "|" symbol
		$newSlug = $this->_encodeSlug(str_replace('admin.php?page=', '', $menuSlug));
		return 'welcome-to-'. frameUms::_()->getModule('adminmenu')->getMainSlug(). '|return='. $newSlug;
	}
	public function addWelcomePageToMainMenu($option) {
		$firstTimeLookedToPlugin = !installerUms::isUsed();
		if($firstTimeLookedToPlugin) {
			$option = $this->_getWelcomMessageMenuData($option, false);
		}
		return $option;
	}
	public function showWelcomePage() {
		$this->getView()->showWelcomePage();
	}
	public function displayAdminFooter() {
		if(frameUms::_()->isAdminPlugPage()) {
			$this->getView()->displayAdminFooter();
		}
	}
	private function _preparePromoLink($link, $ref = '') {
		if(empty($ref))
			$ref = 'user';
		$link .= '?ref='. $ref;
		return $link;
	}
	public function weLoveYou() {
		if(!frameUms::_()->getModule(implode('', array('l','ic','e','ns','e')))) {
			//
		}
	}
	/**
	 * Public shell for private method
	 */
	public function preparePromoLink($link, $ref = '') {
		return $this->_preparePromoLink($link, $ref);
	}
	public function checkStatisticStatus(){
		$canSend = (int) frameUms::_()->getModule('options')->get('send_stats');
		if($canSend) {
			$this->getModel()->checkAndSend();
		}
	}
	public function getMinStatSend() {
		return $this->_minDataInStatToSend;
	}
	public function getMainLink() {
		if(empty($this->_mainLink)) {
			$affiliateQueryString = '';
			$this->_mainLink = 'https://supsystic.com/plugins/ultimate-maps/' . $affiliateQueryString;
		}
		return $this->_mainLink ;
	}
	public function getContactFormFields() {
		$fields = array(
            'name' => array('label' => __('Name', UMS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
			'email' => array('label' => __('Email', UMS_LANG_CODE), 'html' => 'email', 'valid' => array('notEmpty', 'email'), 'placeholder' => 'example@mail.com', 'def' => get_bloginfo('admin_email')),
			'website' => array('label' => __('Website', UMS_LANG_CODE), 'html' => 'text', 'placeholder' => 'http://example.com', 'def' => get_bloginfo('url')),
			'subject' => array('label' => __('Subject', UMS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
            'category' => array('label' => __('Topic', UMS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'selectbox', 'options' => array(
				'plugins_options' => __('Plugin options', UMS_LANG_CODE),
				'bug' => __('Report a bug', UMS_LANG_CODE),
				'functionality_request' => __('Require a new functionallity', UMS_LANG_CODE),
				'other' => __('Other', UMS_LANG_CODE),
			)),
			'message' => array('label' => __('Message', UMS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'textarea', 'placeholder' => __('Hello Supsystic Team!', UMS_LANG_CODE)),
        );
		foreach($fields as $k => $v) {
			if(isset($fields[ $k ]['valid']) && !is_array($fields[ $k ]['valid']))
				$fields[ $k ]['valid'] = array( $fields[ $k ]['valid'] );
		}
		return $fields;
	}
	public function isPro() {
		return frameUms::_()->getModule('add_map_options') ? true : false;
	}
	public function generateMainLink($params = '') {
		$mainLink = $this->getMainLink();
		if(!empty($params)) {
			return $mainLink. (strpos($mainLink , '?') ? '&' : '?'). $params;
		}
		return $mainLink;
	}
	public function getLoveLink() {
		$title = 'WordPress Google Maps Plugin';
		return '<a title="'. $title. '" style="border: none; color: #26bfc1 !important; font-size: 9px; display: block; float: right;" href="'. $this->generateMainLink('utm_source=plugin&utm_medium=love_link&utm_campaign=ultimatemaps'). '" target="_blank">'
			. $title
			. '</a>';
	}
	public function checkSaveOpts($newValues) {
		$loveLinkEnb = (int) frameUms::_()->getModule('options')->get('add_love_link');
		$loveLinkEnbNew = isset($newValues['opt_values']['add_love_link']) ? (int) $newValues['opt_values']['add_love_link'] : 0;
		if($loveLinkEnb != $loveLinkEnbNew) {
			$this->getModel()->saveUsageStat('love_link.'. ($loveLinkEnbNew ? 'enb' : 'dslb'));
		}
	}
	public function checkWeLoveYou() {
		if(frameUms::_()->getModule('options')->get('add_love_link')) {
			echo $this->getLoveLink();
		}
	}
	public function addPromoMapTabs() {
		$tabs = array();
		if(!$this->isPro()) {
			$tabs['umsShapeTab'] = array(
				'label' => __('Figures', UMS_LANG_CODE),
				'content' => $this->getView()->getPromoTabContent('shapes'),
				'promo' => true,
			);
			$tabs['umsHeatmapTab'] = array(
				'label' => __('Heatmap', UMS_LANG_CODE),
				'content' => $this->getView()->getPromoTabContent('heatmap'),
				'promo' => true,
			);
		}
		return $tabs;
	}
	public function showFeaturedPluginsPage() {
		return $this->getView()->showFeaturedPluginsPage();
	}
	public function getDiscountMsg() {
		if($this->isPro()
			&& frameUms::_()->getModule('options')->getActiveTab() == 'license'
			&& frameUms::_()->getModule('license')
			&& frameUms::_()->getModule('license')->getModel()->isActive()
		) {
			$proPluginsList = array(
				'ultimate-maps-by-supsystic-pro', 'newsletters-by-supsystic-pro', 'contact-form-by-supsystic-pro', 'live-chat-pro',
				'digital-publications-supsystic-pro', 'coming-soon-supsystic-pro', 'price-table-supsystic-pro', 'tables-generator-pro',
				'social-share-pro', 'popup-by-supsystic-pro', 'supsystic_slider_pro', 'supsystic-gallery-pro', 'google-maps-easy-pro',
				'backup-supsystic-pro',
			);
			$activePluginsList = get_option('active_plugins', array());
			$activeProPluginsCount = 0;
			foreach($activePluginsList as $actPl) {
				foreach($proPluginsList as $proPl) {
					if(strpos($actPl, $proPl) !== false) {
						$activeProPluginsCount++;
					}
				}
			}
			if($activeProPluginsCount === 1) {
				$buyLink = $this->getDiscountBuyUrl();
				$this->getView()->getDiscountMsg($buyLink);
			}
		}
	}
	public function getDiscountBuyUrl() {
		$license = frameUms::_()->getModule('license')->getModel()->getCredentials();
		$license['key'] = md5($license['key']);
		$license = urlencode(base64_encode(implode('|', $license)));
		$plugin_code = 'ultimate_maps_pro';
		return 'http://supsystic.com/?mod=manager&pl=lms&action=applyDiscountBuyUrl&plugin_code='. $plugin_code. '&lic='. $license;
	}
}
