<?php
/**
 * @author John Hargrove
 * 
 * Date: May 24, 2010
 * Time: 12:04:03 AM
 */

abstract class WPAM_Pages_PublicPage
{
	protected $name;
	protected $title;
	protected $parentPage;
	
	public function getName() { return $this->name; }
	public function getTitle() { return $this->title; }
	public function getParentPage() { return $this->parentPage; }

	public function __construct($name, $title, WPAM_Pages_PublicPage $parentPage = NULL)
	{
		$this->name = $name;
		$this->title = $title;
		$this->parentPage = $parentPage;
	}

	public function getLink($args = array())
	{
		$page_id = $this->getPageId();
		if( !isset( $args['page_id'] ) && isset( $page_id ) ) {
			$args['page_id'] = $page_id;
		}

		$baseUrl = home_url();
		
		return $baseUrl . "/?" .  http_build_query($args);
	}

	public function install()
	{
            $postHelper = new WPAM_PostHelper();
            if($this->parentPage == NULL) {
                return $postHelper->createPage($this->name, $this->title, '[AffiliatesHome]');
            }
            if($this->name == "affiliate-register") {
                return $postHelper->createPage($this->name, $this->title, '[AffiliatesRegister]', $this->parentPage->getPageId() );
            }
            if($this->name == "affiliate-login"){
                return $postHelper->createPage($this->name, $this->title, '[AffiliatesLogin]', $this->parentPage->getPageId() );
            }
	}

	/*
	public function uninstall()
	{
	}

	public function isInstalled()
	{
		$postHelper = new WPAM_PostHelper();
		return $postHelper->postExists($this->name);
	}
	*/

	public function process($request)
	{
		$outputCleaner = new WPAM_OutputCleaner();
		$response = $this->processRequest($outputCleaner->cleanRequest($request));
		return $response->render();
	}
	
	public function isActiveAffiliate($wpUser)
	{
		$db = new WPAM_Data_DataAccess();
		$affiliate = $db->getAffiliateRepository()->loadByUserId($wpUser->ID);

		if ($affiliate !== NULL)
		{
			$status = $affiliate->status;
			if ($status != 'declined' && $status != 'pending')
			{
				return true;
			}
		}
		return false;
	}

	public abstract function processRequest($request);
	public abstract function isAvailable($wpUser);
	public static function getPageId(){return;}

	public function doShortcode() {
		if ($this->isAvailable(wp_get_current_user())) {
			return $this->process($_REQUEST);
		}
		return __( 'Access denied.', 'affiliates-manager' );
	}
}
