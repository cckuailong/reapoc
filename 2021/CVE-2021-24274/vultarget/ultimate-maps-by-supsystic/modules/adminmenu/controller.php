<?php
class adminmenuControllerUms extends controllerUms {
    public function sendMailToDevelopers() {
        $res = new responseUms();
        $data = reqUms::get('post');
        $fields = array(
            'name' => new fieldUmsUms('name', __('Your name field is required.'), '', '', 'Your name', 0, array(), 'notEmpty', UMS_LANG_CODE),
            'website' => new fieldUmsUms('website', __('Your website field is required.'), '', '', 'Your website', 0, array(), 'notEmpty', UMS_LANG_CODE),
            'email' => new fieldUmsUms('email', __('Your e-mail field is required.'), '', '', 'Your e-mail', 0, array(), 'notEmpty, email', UMS_LANG_CODE),
            'subject' => new fieldUmsUms('subject', __('Subject field is required.'), '', '', 'Subject', 0, array(), 'notEmpty', UMS_LANG_CODE),
            'category' => new fieldUmsUms('category', __('You must select a valid category.'), '', '', 'Category', 0, array(), 'notEmpty', UMS_LANG_CODE),
            'message' => new fieldUmsUms('message', __('Message field is required.'), '', '', 'Message', 0, array(), 'notEmpty', UMS_LANG_CODE),
        );
        foreach($fields as $f) {
            $f->setValue($data[$f->name]);
            $errors = validatorUms::validate($f);
            if(!empty($errors)) {
                $res->addError($errors);
            }
        }
        if(!$res->error) {
            $msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
            foreach($fields as $f) {
                $msg .= '<b>'. $f->label. '</b>: '. nl2br($f->value). '<br />';
            }
			$headers[] = 'From: '. $fields['name']->value. ' <'. $fields['email']->value. '>';
			add_filter('wp_mail_content_type', array(frameUms::_()->getModule('messenger'), 'mailContentType'));
            wp_mail('support@supsystic.team.zendesk.com', 'Supsystic Easy Google Maps', $msg, $headers);
            $res->addMessage(__('Done', UMS_LANG_CODE));
        }
        $res->ajaxExec();
    }
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('sendMailToDevelopers')
			),
		);
	}
}

