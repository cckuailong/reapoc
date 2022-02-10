<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 27, 2010
 * Time: 5:09:21 PM
 */

class WPAM_Tracking_TrackingLinkBuilder
{
	private $affiliate;
	private $creative;

	public function __construct(WPAM_Data_Models_AffiliateModel $affiliate, WPAM_Data_Models_CreativeModel $creative) {
		$this->affiliate = $affiliate;
		$this->creative = $creative;
	}

	public function getTrackingKey() {
		$trackingKey = new WPAM_Tracking_TrackingKey();

		$trackingKey->setAffiliateRefKey($this->affiliate->uniqueRefKey);
		$trackingKey->setCreativeId($this->creative->creativeId);

		return $trackingKey;
	}

	public function getUrl() {
                $aff_landing_page = get_option(WPAM_PluginConfig::$AffLandingPageURL);
                if(isset($aff_landing_page) && !empty($aff_landing_page)){
                    $aff_landing_page = trailingslashit($aff_landing_page);
                    $aff_landing_page = $aff_landing_page.trim($this->creative->slug);
                    return add_query_arg( array( WPAM_PluginConfig::$wpam_id => $this->affiliate->affiliateId ), $aff_landing_page);
                }
		return add_query_arg( array( WPAM_PluginConfig::$wpam_id => $this->affiliate->affiliateId ),
							  home_url( '/'.trim( $this->creative->slug ) ) );
	}

	public function getHtmlSnippet() {
		switch ($this->creative->type) {
			case 'image':
				$html = "<a href=\"" . $this->getUrl() . "\">";
                                $img_url = '';
                                if(isset($this->creative->image) && !empty($this->creative->image)){  //new way of retrieving an image URL
                                    $img_url = $this->creative->image;
                                }
                                else if(isset($this->creative->imagePostId) && !empty($this->creative->imagePostId)){  //old way for backwards compatiblity
                                    $img_url = wp_get_attachment_url($this->creative->imagePostId);
                                }
				$html .= "<img src=\"" . $img_url . "\" style=\"border: 0;\" title=\"{$this->creative->altText}\"/>";
				$html .= "</a>";
				return $html;
			case 'text':
				$html = "<a href=\"" . $this->getUrl() . "\" title=\"{$this->creative->altText}\">";
				$html .= $this->creative->linkText;
				$html .= "</a>";
				return $html;

			default:
				return NULL;
		}
	}

	public function getImpressionHtmlSnippet() {
		if (get_option (WPAM_PluginConfig::$AffEnableImpressions) ) {
			$impurl = add_query_arg( array( WPAM_PluginConfig::$RefKey => $this->getTrackingKey()->pack() ), WPAM_URL . "/imp.php" );
			$html = "<img src=\"$impurl\" width=\"0\" height=\"0\" />";
		}
		else $html = "";

		return $html . $this->getHtmlSnippet();
	}
}
