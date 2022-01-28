<?php
class mailCfs extends moduleCfs {
	private $_smtpMailer = null;
	private $_sendMailMailer = null;

	public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$type = frameCfs::_()->getModule('options')->get('mail_send_engine');
		$res = false;
		switch($type) {
			case 'smtp':
				$res = $this->sendSmtpMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				break;
			case 'sendmail':
				$res = $this->sendSendMailMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				break;
			case 'wp_mail': default:
				$res = $this->sendWpMail( $to, $subject, $message, $fromName, $fromEmail, $replyToName, $replyToEmail, $additionalHeaders, $additionalParameters );
				if(!$res) {
					// Sometimes it return false, but email was sent, and in such cases
					// - in errors array there are only one - empty string - value.
					// Let's count this for now as Success sending
					$mailErrors = array_filter( $this->getMailErrors() );
					if(empty($mailErrors)) {
						$res = true;
					}
				}
				break;
		}
		return $res;
	}
	private function _getSmtpMailer() {
		if(!$this->_smtpMailer) {
			$this->_connectPhpMailer();
			$this->_smtpMailer = new PHPMailer\PHPMailer\PHPMailer( true );  // create a new object
			$this->_smtpMailer->IsSMTP(); // enable SMTP
			$this->_smtpMailer->Debugoutput = array($this, 'pushPhpMailerError');
			$this->_smtpMailer->SMTPDebug = 2;  // debugging: 1 = errors and messages, 2 = messages only
			$this->_smtpMailer->SMTPAuth = true;  // authentication enabled
			$smtpSecure = frameCfs::_()->getModule('options')->get('smtp_secure');
			if(!empty($smtpSecure)) {
				$this->_smtpMailer->SMTPSecure = $smtpSecure; // secure transfer enabled REQUIRED for GMail
			}
			$this->_smtpMailer->Host = trim(frameCfs::_()->getModule('options')->get('smtp_host'));
			$this->_smtpMailer->Port = trim(frameCfs::_()->getModule('options')->get('smtp_port'));

			$login = trim(frameCfs::_()->getModule('options')->get('smtp_login'));
			$this->_smtpMailer->Username = $login;
			$this->_smtpMailer->Password = trim(frameCfs::_()->getModule('options')->get('smtp_pass'));
			if(strpos($login, '@') > 0) {
				$this->_smtpMailer->From = $login;
			}
		}
		return $this->_smtpMailer;
	}
	public function pushPhpMailerError( $errorStr ) {
		if(strpos($errorStr, 'SMTP ERROR') !== false) {
			$this->pushError( $errorStr );
		}
	}
	private function _getSendMailMailer() {
		if(!$this->_sendMailMailer) {
			$this->_connectPhpMailer();
			$this->_sendMailMailer = new PHPMailer\PHPMailer\PHPMailer( true );  // create a new object
			$this->_sendMailMailer->isSendmail(); // enable SendMail
			$sendMailPath = trim(frameCfs::_()->getModule('options')->get('sendmail_path'));
			if(!empty($sendMailPath)) {
				$this->_sendMailMailer->Sendmail = $sendMailPath;
			}
		}
		return $this->_sendMailMailer;
	}
	private function _connectPhpMailer() {
      global $wp_version;
			if (!class_exists('PHPMailer', false)) {
				 require_once ( ABSPATH . WPINC . '/PHPMailer/PHPMailer.php' );
				 require_once ( ABSPATH . WPINC . '/PHPMailer/Exception.php' );
				 require_once ( ABSPATH . WPINC . '/PHPMailer/SMTP.php' );
				 $phpMailer = new PHPMailer\PHPMailer\PHPMailer( true );
			}
	}
	public function sendSendMailMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$this->_getSendMailMailer();
		if($fromEmail && $fromName) {
			$this->_sendMailMailer->setFrom($fromEmail, $fromName);
		}
		if($replyToName || $replyToEmail) {
			$this->_sendMailMailer->addReplyTo($replyToEmail, $replyToName);
        }
		/*if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_sendMailMailer->ReturnPath = $params['return_path_email'];
        }*/
		$this->_sendMailMailer->Subject = $subject;
		$this->_sendMailMailer->addAddress($to);
		if(frameCfs::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_sendMailMailer->Body = $message;
		} else {
			$this->_sendMailMailer->msgHTML( $message );
		}
		if($this->_sendMailMailer->send()) {
			return true;
		} else {
			$this->pushError( 'Mail error: '.$this->_sendMailMailer->ErrorInfo );
		}
		return false;
	}
	public function sendSmtpMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$this->_getSmtpMailer();
		// Clear all prev. data - to not collect them
		$this->_smtpMailer->clearAddresses();
		$this->_smtpMailer->clearReplyTos();
		$this->_smtpMailer->clearAllRecipients();
		$this->_smtpMailer->clearAttachments();
		$this->_smtpMailer->clearCustomHeaders();

		if($fromEmail && $fromName) {
			$this->_smtpMailer->setFrom($fromEmail, $fromName);
		}
		if($replyToName || $replyToEmail) {
			$this->_smtpMailer->addReplyTo($replyToEmail, $replyToName);
    }
		/*if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_smtpMailer->ReturnPath = $params['return_path_email'];
        }*/
		$this->_smtpMailer->Subject = $subject;
		$to = explode(',', $to);
		if (!empty($to) && is_array($to)){
			foreach ($to as $email) {
				$this->_smtpMailer->addAddress($email);
			}
		} else {
			$this->_smtpMailer->addAddress($to);
		}
		if(frameCfs::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_smtpMailer->Body = $message;
		} else {
			$this->_smtpMailer->msgHTML( $message );
		}
		if($this->_smtpMailer->send()) {
			return true;
		} else {
			$this->pushError( 'Mail error: '.$this->_smtpMailer->ErrorInfo );
		}
		return false;
	}
	public function sendWpMail($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {
		$headersArr = array();
		$eol = "\r\n";
		$replyToExists = $fromNameExists = false;
		if(!empty($additionalHeaders)) {
			foreach($additionalHeaders as $addH) {
				if(strpos($addH, 'From:') !== false) {
					$fromNameExists = true;
				} elseif(strpos($addH, 'Reply-To:') !== false) {
					$replyToExists = true;
				}
			}
		}
        if(!empty($fromName) && !empty($fromEmail) && !$fromNameExists) {
            $headersArr[] = 'From: '. $fromName. ' <'. $fromEmail. '>';
        }
		if(!empty($replyToName) && !empty($replyToEmail) && !$replyToExists) {
            $headersArr[] = 'Reply-To: '. $replyToName. ' <'. $replyToEmail. '>';
        }
		if(!empty($additionalHeaders)) {
			$headersArr = array_merge($headersArr, $additionalHeaders);
		}
		if(!function_exists('wp_mail'))
			frameCfs::_()->loadPlugins();
		if(!frameCfs::_()->getModule('options')->get('disable_email_html_type')) {
			add_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}

		$attach = null;
		if(isset($additionalParameters['attach']) && !empty($additionalParameters['attach'])) {
			$attach = $additionalParameters['attach'];
		}
		if(empty($attach)) {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		} else {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr), $attach);
		}
		if(!frameCfs::_()->getModule('options')->get('disable_email_html_type')) {
			remove_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}

		return $result;
	}

	public function getMailErrors() {
		global $ts_mail_errors;
		global $phpmailer;
		$type = frameCfs::_()->getModule('options')->get('mail_send_engine');
		switch($type) {
			case 'smtp': case 'sendmail':
				return $this->getErrors();
			case 'wp_mail': default:
				// Clear prev. send errors at first
				$ts_mail_errors = array();

				// Let's try to get errors about mail sending from WP
				if (!isset($ts_mail_errors)) $ts_mail_errors = array();
				if (isset($phpmailer)) {
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
				if(empty($ts_mail_errors)) {
					$ts_mail_errors[] = __('Cannot send email - problem with send server', CFS_LANG_CODE);
				}
				return $ts_mail_errors;
		}
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Mail', CFS_LANG_CODE),
			'opts' => array(
				'mail_function_work' => array('label' => __('Mail function tested and work', CFS_LANG_CODE), 'desc' => ''),
				'notify_email' => array('label' => __('Notify Email', CFS_LANG_CODE), 'desc' => __('Email address used for all email notifications from plugin', CFS_LANG_CODE), 'html' => 'text', 'def' => get_option('admin_email')),
			),
		);
		return $opts;
	}
}
