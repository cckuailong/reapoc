<?php
class mailUms extends moduleUms {
	public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = null, $additionalParameters = null) {
		$headersArr = array();
		$eol = "\r\n";
        if(!empty($fromName) && !empty($fromEmail)) {
            $headersArr[] = 'From: '. $fromName. ' <'. $fromEmail. '>';
        }
		if(!empty($replyToName) && !empty($replyToEmail)) {
            $headersArr[] = 'Reply-To: '. $replyToName. ' <'. $replyToEmail. '>';
        }
		if(!function_exists('wp_mail'))
			frameUms::_()->loadPlugins();
		add_filter('wp_mail_content_type', array($this, 'mailContentType'));

        $result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		remove_filter('wp_mail_content_type', array($this, 'mailContentType'));

		return $result;
	}
	public function getMailErrors() {
		global $ts_mail_errors;

		// Clear prev. send errors at first
		$ts_mail_errors = array();

		// Let's try to get errors about mail sending from WP
		if (!isset($ts_mail_errors)) $ts_mail_errors = array();
		
		if(empty($ts_mail_errors)) {
			$ts_mail_errors[] = __('Can not send email - problem with send server');
		}
		return $ts_mail_errors;
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
}
