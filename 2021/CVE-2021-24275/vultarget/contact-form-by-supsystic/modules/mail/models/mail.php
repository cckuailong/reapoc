<?php
class mailModelCfs extends modelCfs {
	public function testEmail($email) {
		$email = trim($email);
		if(!empty($email)) {
			if($this->getModule()->send($email, 
				__('Test email functionality', CFS_LANG_CODE), 
				sprintf(__('This is a test email for testing email functionality on your site, %s.', CFS_LANG_CODE), CFS_SITE_URL))
			) {
				return true;
			} else {
				$this->pushError( $this->getModule()->getMailErrors() );
			}
		} else
			$this->pushError (__('Empty email address', CFS_LANG_CODE), 'params[tpl][test_email]');
		return false;
	}
}