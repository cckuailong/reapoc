<?php

class WPAM_Pages_AffiliatesLogin extends WPAM_Pages_PublicPage
{
	private $response;
	
	public function isAvailable($wpUser)
	{
		return true;
	}
        public function processRequest($request)
	{
            
        }
        public static function getPageId() {
		return get_option( WPAM_PluginConfig::$AffLoginPageURL );
	}
	public static function getPageURL() {
		return get_option( WPAM_PluginConfig::$AffLoginPageURL );
	}
}

