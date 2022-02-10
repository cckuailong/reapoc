<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 6:06:19 PM
 */

class WPAM_Pages_AffiliatesManageAccount extends WPAM_Pages_PublicPage
{

	public function processRequest($request)
	{
		// TODO: Implement processRequest() method.
	}

	public function isAvailable($wpUser)
	{
		return $this->isActiveAffiliate($wpUser);
	}
}
