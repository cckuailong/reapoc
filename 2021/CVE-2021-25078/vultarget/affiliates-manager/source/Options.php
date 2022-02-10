<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/3/11
 * Time: 11:01 PM
 */


/**
 * New wrapper for wp_options
 * 
 * TODO: setters, with internal validation
 */
class WPAM_Options
{
	public function getPaypalAPIUser() { return get_option( WPAM_PluginConfig::$PaypalAPIUserOption ); }
	public function getPaypalAPIPassword() { return get_option( WPAM_PluginConfig::$PaypalAPIPasswordOption ); }
	public function getPaypalAPISignature() { return get_option( WPAM_PluginConfig::$PaypalAPISignatureOption ); }
	public function getPaypalAPIEndPoint() { return get_option( WPAM_PluginConfig::$PaypalAPIEndPointOption ); }
	
	public function getPaypalAPIEndPointURL() {
		switch ( get_option( WPAM_PluginConfig::$PaypalAPIEndPointOption ) )
		{
			case 'dev': return WPAM_PayPal_Service::PAYPAL_API_ENDPOINT_SANDBOX;
			case 'live': return WPAM_PayPal_Service::PAYPAL_API_ENDPOINT_LIVE;
			default: throw new Exception( __( 'Invalid PayPal API value', 'affiliates-manager' ) );
		}
	}

	public function getPaypalMassPayEnabled() { return (int)get_option( WPAM_PluginConfig::$PaypalMassPayEnabledOption ); }
 	
	public function initOptions()
	{
		add_option( WPAM_PluginConfig::$CookieExpireOption,                30,    NULL, 'no' );
		add_option( WPAM_PluginConfig::$EmailNameOption,                   NULL, NULL, 'no' );
		add_option( WPAM_PluginConfig::$EmailAddressOption,                NULL, NULL, 'no' );
                add_option( WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption,  true, NULL, 'no' );
                add_option( WPAM_PluginConfig::$AffBountyType, 'percent' );
                add_option( WPAM_PluginConfig::$AffBountyAmount, 25 );
                add_option( WPAM_PluginConfig::$AffCurrencySymbol, '$' );
                add_option( WPAM_PluginConfig::$AffCurrencyCode, 'USD' );
                add_option( WPAM_PluginConfig::$AffEnableImpressions, 0, NULL, 'no' );
		add_option( WPAM_PluginConfig::$PayoutMethodCheckIsEnabledOption,  true, NULL, 'no' );
		add_option( WPAM_PluginConfig::$PayoutMethodPaypalIsEnabledOption, true, NULL, 'no' );
		add_option( WPAM_PluginConfig::$TNCOptionOption, file_get_contents( WPAM_RESOURCES_DIR . "default_tnc.txt" ) );
		add_option( WPAM_PluginConfig::$MinPayoutAmountOption, 20 );
		add_option( WPAM_PluginConfig::$PaypalAPIEndPointOption, 'dev' );
                add_option( WPAM_PluginConfig::$EmailType, 'plain' );
                add_option( WPAM_PluginConfig::$SendAdminRegNotification, true );
                add_option( WPAM_PluginConfig::$AdminRegNotificationEmail, get_option('admin_email') );
                add_option( WPAM_PluginConfig::$AutoDeleteWPUserAccount, true );
                update_option('wpam_options_version', WPAM_OPTIONS_VERSION);
	}
}
