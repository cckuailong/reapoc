<?php
class Validation_Captcha extends Validation {
	public $message = '';
	public $publicKey;
        public $version;

	public function __construct($publicKey, $message = "",$version=2) {
		$this->publicKey = $publicKey;
                $this->version=$version;
		//if(!empty($message))
		$this->message = RM_UI_Strings::get('ERROR_INVALID_RECAPTCHA');
	}

	public function isValid($value) {
		require_once(dirname(__FILE__) . "/../Resources/recaptchalib.php");
                
                if(!isset($_POST["g-recaptcha-response"])){
                    return false;
                }

		$resp = rm_recaptcha_check_answer ($this->publicKey, $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
		if($resp->is_valid)
			return true;
		else{
                    if(!empty($_POST['rm_slug']) && $_POST['rm_slug']=='rm_login_form'){
                        $login_service= new RM_Login_Service();
                        $username= sanitize_text_field($_POST['username']);
                        $user= get_user_by('login', $username);
                        if(empty($user)){
                            $user= get_user_by('email', $username);
                        }
                        $args= array('ip'=>$_SERVER["REMOTE_ADDR"],'time'=> current_time('timestamp'),'type'=>'normal','result'=>'failure','failure_reason'=>'incorrect_reCAPCTCHA');
                        if(!empty($user)){
                          $args['email']= $user->user_email;
                        }
                        else
                        {
                            $args['username_used']= $username;
                        }
                        if(empty($_POST['rm_captcha_form_processed'])){
                            $_POST['rm_captcha_form_processed']=1;
                            $login_service->insert_login_log($args);
                        }
                        
                    }
                    
                    
                    return false;
                }
	}
    
    public function getMessage() {
		return $this->message;
	}
}
