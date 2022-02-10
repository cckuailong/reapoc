<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 6, 2010
 * Time: 11:47:58 PM
 */

class WPAM_TermsCompiler
{
	private $template;
	public function __construct($template)
	{
		$this->template = $template;
	}

	public function build()
	{
		$siteName = get_option('blogname');
		$siteUrl = get_option('home');
		$termsUrl = WPAM_URL . "/tnc.php";
		$payoutMinimum = get_option(WPAM_PluginConfig::$MinPayoutAmountOption);

		$tnc = $this->template;

		$tnc = str_replace("[site name]", $siteName, $tnc);
		$tnc = str_replace("[site url]", $siteUrl, $tnc);
		$tnc = str_replace("[terms url]", $termsUrl, $tnc);
		$tnc = str_replace("[payout minimum]", $payoutMinimum, $tnc);

		return $tnc;
	}
}
