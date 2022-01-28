<?php
class supsystic_promoViewUms extends viewUms {
    public function displayAdminFooter() {
        parent::display('adminFooter');
    }
	public function showWelcomePage() {
		$this->assign('askOptions', array(
			1 => array('label' => 'Google'),
			2 => array('label' => 'Worumsess.org'),
			3 => array('label' => 'Refer a friend'),
			4 => array('label' => 'Find on the web'),
			5 => array('label' => 'Other way...'),
		));
		$this->assign('originalPage', uriUms::getFullUrl());
		parent::display('welcomePage');
	}
	public function getOverviewTabContent() {
		frameUms::_()->getModule('templates')->loadJqueryUi();

		frameUms::_()->getModule('templates')->loadSlimscroll();
		frameUms::_()->addScript('admin.overview', $this->getModule()->getModPath(). 'js/admin.overview.js');
		frameUms::_()->addStyle('admin.overview', $this->getModule()->getModPath(). 'css/admin.overview.css');
		$this->assign('mainLink', $this->getModule()->getMainLink());
		$this->assign('faqList', $this->getFaqList());
		$this->assign('serverSettings', $this->getServerSettings());
		$this->assign('news', $this->getNewsContent());
		$this->assign('contactFields', $this->getModule()->getContactFormFields());
		return parent::getContent('overviewTabContent');
	}
	public function getFaqList() {
		return array(

		);
	}
	public function getNewsContent() {
		$getData = wp_remote_get('http://supsystic.com/news/main.html');
		$content = '';
		if($getData
			&& is_array($getData)
			&& isset($getData['response'])
			&& isset($getData['response']['code'])
			&& $getData['response']['code'] == 200
			&& isset($getData['body'])
			&& !empty($getData['body'])
		) {
			$content = $getData['body'];
		} else {
			$content = sprintf(__("There were some problem while trying to retrive our news, but you can always check all list <a target='_blank' href='%s'>here</a>.", UMS_LANG_CODE), 'http://supsystic.com/news');
		}
		return $content;
	}
	public function getServerSettings() {
		global $wpdb;
		return array(
			'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
			'MySQL' => array('value' => $wpdb->db_version()),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? __('Yes', UMS_LANG_CODE) : __('No', UMS_LANG_CODE)),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? __('Yes', UMS_LANG_CODE) : __('No', UMS_LANG_CODE)),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? __('Yes', UMS_LANG_CODE) : __('No', UMS_LANG_CODE), 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? __('Yes', UMS_LANG_CODE) : __('No', UMS_LANG_CODE), 'error' => !extension_loaded('curl')),
		);
	}
	public function getPromoTabContent($tabCode) {
		$this->assign('promoLink', $this->getModule()->getMainLink());
		$this->assign('tabCode', $tabCode);
		return parent::getContent('adminPromoTabContent');
	}
	public function showFeaturedPluginsPage() {
		frameUms::_()->getModule('templates')->loadSupTablesUi();
		frameUms::_()->addStyle('admin.featured-plugins', $this->getModule()->getModPath(). 'css/admin.featured-plugins.css');
		frameUms::_()->getModule('templates')->loadGoogleFont('Montserrat');
		$siteUrl = 'https://supsystic.com/';
		$pluginsUrl = $siteUrl. 'plugins/';
		$downloadsUrl = 'https://downloads.wordpress.org/plugin/';
		$imgUrl = frameUms::_()->getModule('supsystic_promo')->getModPath(). 'img/';
		$promoCampaign = 'ultimatemaps';
		$this->assign('pluginsList', array(
      array('label' => __('Popup Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'popup-plugin/','external' => true, 'img' => $imgUrl. 'Popup_256.png', 'desc' => __('The Best WordPress PopUp option plugin to help you gain more subscribers, social followers or advertisement. Responsive pop-ups with friendly options.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'popup-by-supsystic.zip'),
			array('label' => __('Photo Gallery Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'photo-gallery/','external' => true, 'img' => $imgUrl. 'Gallery_256.png', 'desc' => __('Photo Gallery Plugin with a great number of layouts will help you to create quality respectable portfolios and image galleries.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'gallery-by-supsystic.zip'),
			array('label' => __('Contact Form Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'contact-form-plugin/','external' => true, 'img' => $imgUrl. 'Contact_Form_256.png', 'desc' => __('One of the best plugin for creating Contact Forms on your WordPress site. Changeable fonts, backgrounds, an option for adding fields etc.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'contact-form-by-supsystic.zip'),
			array('label' => __('Newsletter Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'newsletter-plugin/','external' => true, 'img' => $imgUrl. 'icon-256x256.png', 'desc' => __('Supsystic Newsletter plugin for automatic mailing of your letters. You will have no need to control it or send them manually. No coding, hard skills or long hours of customizing are required.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'newsletter-by-supsystic.zip'),
			array('label' => __('Membership by Supsystic', UMS_LANG_CODE), 'url' => $pluginsUrl. 'membership-plugin/','external' => true, 'img' => $imgUrl. '256.png', 'desc' => __('Create online membership community with custom user profiles, roles, FrontEnd registration and login. Members Directory, activity, groups, messages.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'membership-by-supsystic.zip'),
			array('label' => __('Data Tables Generator', UMS_LANG_CODE), 'url' => $pluginsUrl. 'data-tables-generator-plugin/','external' => true, 'img' => $imgUrl. 'Data_Tables_256.png', 'desc' => __('Create and manage beautiful data tables with custom design. No HTML knowledge is required.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'data-tables-generator-by-supsystic.zip'),
			array('label' => __('Slider Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'slider/','external' => true, 'img' => $imgUrl. 'Slider_256.png', 'desc' => __('Creating slideshows with Slider plugin is fast and easy. Simply select images from your WordPress Media Library, Flickr, Instagram or Facebook, set slide captions, links and SEO fields all from one page.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'slider-by-supsystic.zip'),
			array('label' => __('Social Share Buttons', UMS_LANG_CODE), 'url' => $pluginsUrl. 'social-share-plugin/','external' => true, 'img' => $imgUrl. 'Social_Buttons_256.png', 'desc' => __('Social share buttons to increase social traffic and popularity. Social sharing to Facebook, Twitter and other social networks.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'social-share-buttons-by-supsystic.zip'),
			array('label' => __('Live Chat Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'live-chat/','external' => true, 'img' => $imgUrl. 'Live_Chat_256.png', 'desc' => __('Be closer to your visitors and customers with Live Chat Support by Supsystic. Help you visitors, support them in real-time with exceptional Live Chat WordPress plugin by Supsystic.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'live-chat-by-supsystic.zip'),
			array('label' => __('Pricing Table', UMS_LANG_CODE), 'url' => $pluginsUrl. 'pricing-table/','external' => true, 'img' => $imgUrl. 'Pricing_Table_256.png', 'desc' => __('It’s never been so easy to create and manage pricing and comparison tables with table builder. Any element of the table can be customise with mouse click.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'pricing-table-by-supsystic.zip'),
			array('label' => __('Coming Soon Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'coming-soon-plugin/', 'external' => true, 'img' => $imgUrl. 'Coming_Soon_256.png', 'desc' => __('Coming soon page with drag-and-drop builder or under construction | maintenance mode to notify visitors and collects emails.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'coming-soon-by-supsystic.zip'),
			array('label' => __('Backup Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'backup-plugin/', 'external' => true, 'img' => $imgUrl. 'Backup_256.png', 'desc' => __('Backup and Restore WordPress Plugin by Supsystic provides quick and unhitched DropBox, FTP, Amazon S3, Google Drive backup for your WordPress website.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'backup-by-supsystic.zip'),
			array('label' => __('Google Maps Easy', UMS_LANG_CODE), 'url' => $pluginsUrl. 'google-maps-plugin/', 'external' => true, 'img' => $imgUrl. 'Google_Maps_256.png', 'desc' => __('Display custom Google Maps. Set markers and locations with text, images, categories and links. Customize google map in a simple and intuitive way.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'google-maps-easy.zip'),
			array('label' => __('Digital Publication Plugin', UMS_LANG_CODE), 'url' => $pluginsUrl. 'digital-publication-plugin/', 'external' => true, 'img' => $imgUrl. 'Digital_Publication_256.png', 'desc' => __('Digital Publication WordPress Plugin by Supsystic for Magazines, Catalogs, Portfolios. Convert images, posts, PDF to the page flip book.', UMS_LANG_CODE), 'download' => $downloadsUrl. 'digital-publications-by-supsystic.zip'),
			array('label' => __('Kinsta Hosting', UMS_LANG_CODE), 'url' => 'https://kinsta.com?kaid=MNRQQASUYJRT', 'external' => true, 'img' => $imgUrl. 'kinsta_banner.png', 'desc' => __('If you want to host a business site or a blog, Kinsta managed WordPress hosting is the best place to stop on. Without any hesitation, we can say Kinsta is incredible when it comes to uptime and speed.', UMS_LANG_CODE)),
		));
		foreach($this->pluginsList as $i => $p) {
			if (empty($p['external'])) {
				$this->pluginsList[$i]['url'] = $this->pluginsList[$i]['url'] . '?utm_source=plugin&utm_medium=featured_plugins&utm_campaign=' . $promoCampaign;
			}
		}
		$this->assign('bundleUrl', $siteUrl. 'product/plugins-bundle/'. '?utm_source=plugin&utm_medium=featured_plugins&utm_campaign='. $promoCampaign);
		return parent::getContent('featuredPlugins');
	}
	public function getDiscountMsg($buyLink = '#') {
		$this->assign('bundlePageLink', '//supsystic.com/all-plugins/');
		$this->assign('buyLink', $buyLink);
		parent::display('discountMsg');
	}
}
