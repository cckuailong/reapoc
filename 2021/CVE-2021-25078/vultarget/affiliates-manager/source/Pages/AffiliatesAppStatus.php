<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 6:04:41 PM
 */

class WPAM_Pages_AffiliatesAppStatus extends WPAM_Pages_PublicPage
{
	public function processRequest($request)
	{
		
	}

	public function isAvailable($wpUser)
	{
		$db = new WPAM_Data_DataAccess();
		$affiliate = $db->getAffiliateRepository()->loadByUserId($wpUser->ID);
		return $affiliate !== NULL && $affiliate->isPending();
	}
}
