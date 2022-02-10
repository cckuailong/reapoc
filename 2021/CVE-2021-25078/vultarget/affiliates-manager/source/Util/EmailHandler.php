<?php
/**
 * @author Justin Foell
 */

class WPAM_Util_EmailHandler {

	public function mailAffiliate( $address, $subject, $message ) {
		//#61 override email & name
		add_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );
                add_filter('wp_mail_content_type', 'wpam_filter_mail_content_type');
                WPAM_Logger::log_debug($subject);
                WPAM_Logger::log_debug("Sending an email to ".$address);
		$mail_sent = wp_mail( $address, $subject, $message );
                if($mail_sent==true){
                    WPAM_Logger::log_debug("Email was sent successfully by WordPress");
                }
                else{
                    WPAM_Logger::log_debug("Email could not be sent by WordPress");
                }
                remove_filter('wp_mail_content_type', 'wpam_filter_mail_content_type');
		remove_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );		
	}

	public function mailNewAffiliate( $user_id, $user_pass ) {
		//#62 piggyback onto the username / password email
		add_filter( 'wp_mail', array( $this, 'filterMail' ) );
		add_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );
		wp_new_user_notification( $user_id, $user_pass );
		remove_filter( 'wp_mail', array( $this, 'filterMail' ) );
		remove_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );
	}
        
        public function mailNewApproveAffiliate($user_id, $user_pass, $affiliate)
        {
            add_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
            add_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );
            add_filter('wp_mail_content_type', 'wpam_filter_mail_content_type');
            $user = get_user_by( 'id', $user_id );
            $username = $user->user_login;
            $address = $user->user_email;
            $aff_id = $affiliate->affiliateId;
            $aff_first_name = $affiliate->firstName;
            $aff_last_name = $affiliate->lastName; 
            $aff_email = $affiliate->email;
            $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
            $login_url = get_option(WPAM_PluginConfig::$AffLoginPageURL); //wp_login_url();
            $subject = sprintf(__('Affiliate Application for %s', 'affiliates-manager'), $blogname);
            //$message = "New affiliate registration for {blogname}: has been approved!. \n\nUsername: {affusername} \nPassword: {affpassword} \nLogin URL: {affloginurl}";
            $message = WPAM_MessageHelper::GetMessage('affiliate_application_approved_email');
            $tags = array("{blogname}","{affusername}","{affpassword}","{affloginurl}","{aff_id}","{aff_first_name}","{aff_last_name}","{aff_email}");
            $vals = array($blogname, $username, $user_pass, $login_url, $aff_id, $aff_first_name, $aff_last_name, $aff_email);
            $body = str_replace($tags,$vals,$message);
            WPAM_Logger::log_debug($subject);
            WPAM_Logger::log_debug("Sending an email to ".$address);
            $mail_sent = wp_mail( $address, $subject, $body );
            if($mail_sent==true){
                WPAM_Logger::log_debug("Email was sent successfully by WordPress");
            }
            else{
                WPAM_Logger::log_debug("Email could not be sent by WordPress");
            }
            remove_filter('wp_mail_content_type', 'wpam_filter_mail_content_type');
            remove_filter( 'wp_mail_from', array( $this, 'filterMailAddress' ) );
            remove_filter( 'wp_mail_from_name', array( $this, 'filterMailName' ) );
        }
	
	public function filterMail( $args ) {
		//only add to the user/password email
		if( strpos( $args['subject'], __( 'Your username and password' ) ) === FALSE )
			return $args;
		
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$args['subject'] = sprintf( __( '[%s] Your username and password for Affiliate Manager', 'affiliates-manager' ), $blogname );
		$args['message'] = sprintf( __( 'New affiliate registration for %s: has been approved!', 'affiliates-manager' ), $blogname ) . "\r\n\r\n" . $args['message'];
		return $args;
	}

	public function filterMailAddress( $address ) {
		$addrOverride = get_option( WPAM_PluginConfig::$EmailAddressOption );
		if( ! empty( $addrOverride ) )
			return $addrOverride;
		
		return $address;
	}

	public function filterMailName( $name ) {
		$nameOverride = get_option( WPAM_PluginConfig::$EmailNameOption );
		if( ! empty( $nameOverride ) )
			return $nameOverride;
		
		return $name;
        }
}
