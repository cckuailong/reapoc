<?php


class WPAM_PluginConfig
{
	/***** WP Capabilities *****/
	public static $AdminCap = 'wpam_admin';
	public static $AffiliateCap = 'wpam_affiliate';
	public static $AffiliateActiveCap = 'wpam_affiliate_active';

	/***** WP Options *****/
	//public static $ReferralEncryptKeyOption = 'wpam_refkey_encrypt_key';
	public static $TNCOptionOption = 'wpam_tnc_body';
	public static $MinPayoutAmountOption = 'wpam_min_payout';
	public static $CookieExpireOption = 'wpam_cookie_expire';
	public static $EmailNameOption = 'wpam_email_name';
	public static $EmailAddressOption = 'wpam_email_address';
        public static $AutoAffiliateApproveIsEnabledOption = 'wpam_auto_aff_approve_enabled';
        public static $AffBountyType = 'wpam_affbounty_type';
        public static $AffBountyAmount = 'wpam_affbounty_amount';
        public static $AffCurrencySymbol = 'wpam_affcurrency_symbol';
        public static $AffCurrencyCode = 'wpam_affcurrency_code';
        public static $AffdoNotRecordZeroAmtCommission = 'wpam_donot_record_zero_amt_commission';
        public static $AffEnableImpressions = 'wpam_enable_impressions';
        public static $AffEnableDebug = 'wpam_enable_debug';
	public static $PayoutMethodCheckIsEnabledOption = 'wpam_payout_check_enabled';
	public static $PayoutMethodPaypalIsEnabledOption = 'wpam_payout_paypal_enabled';
        public static $PayoutMethodManualIsEnabledOption = 'wpam_payout_manual_enabled';
	public static $HomePageId = 'wpam_home_page_id';
	public static $RegPageId = 'wpam_register_page_id';
        public static $AffHomePageURL = 'wpam_aff_home_page';
        public static $AffRegPageURL = 'wpam_aff_reg_page';
        public static $AffLoginPageURL = 'wpam_aff_login_page';
        public static $AffHomeMsg = 'wpam_aff_home_msg';
        public static $AffHomeMsgNotRegistered = 'wpam_aff_home_msg_not_registered';
        public static $AffLandingPageURL = 'wpam_landing_page';
        public static $AffTncPageURL = 'wpam_aff_tnc_page';
        public static $DisableOwnReferrals = 'wpam_disable_own_referrals';
        public static $EmailType = 'wpam_email_type';
        public static $SendAdminRegNotification = 'wpam_send_admin_reg_notification';
        public static $AdminRegNotificationEmail = 'wpam_admin_reg_notification_email';
        public static $SendAffCommissionNotification = 'wpam_send_aff_commission_notification';
        public static $SendAdminAffCommissionNotification = 'wpam_send_admin_aff_commission_notification';
        public static $AdminAffCommissionNotificationEmail = 'wpam_admin_aff_commission_notification_email';
        public static $AutoDeleteWPUserAccount = 'wpam_auto_delete_wp_user_account';
	// paypal
	public static $PaypalMassPayEnabledOption = 'wpam_paypal_mass_pay_enabled';
	public static $PaypalAPIUserOption = 'wpam_paypal_api_user';
	public static $PaypalAPIPasswordOption = 'wpam_paypal_api_password';
	public static $PaypalAPISignatureOption = 'wpam_paypal_api_signature';
	public static $PaypalAPIEndPointOption = 'wpam_paypal_api_endpoint';

	/***** MISC *****/
	public static $RefKey = 'wpam_refkey';
        public static $wpam_id = 'wpam_id';
        public static $DefaultCreativeId = 'wpam_default_creative_id';

	/***** short codes *****/
	public static $ShortCodeHome = '[AffiliatesHome]';
	public static $ShortCodeRegister = '[AffiliatesRegister]';
        public static $ShortCodeLogin = '[AffiliatesLogin]';
	
}

