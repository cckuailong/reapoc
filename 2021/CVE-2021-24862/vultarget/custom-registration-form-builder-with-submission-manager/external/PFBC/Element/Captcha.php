<?php

class Element_Captcha extends Element {

    public $privateKey = "";
    public $publicKey = "";
    public $version=2;

    public function __construct($label = "", array $properties = null) {
        $this->load_keys();
        parent::__construct($label, "recaptcha_response_field", $properties);
        $this->validation[] = new Validation_Captcha($this->privateKey,'',$this->version);
    }

    public function load_keys(){
        $version =  get_option('rm_option_recaptcha_v');
        $this->version= $version=='v3' ? 3 : 2;
        
        if($this->version==3){
            $this->publicKey = get_option('rm_option_public_key3');
            $this->privateKey = get_option('rm_option_private_key3');
            return;
        }
        $this->publicKey = get_option('rm_option_public_key');
        $this->privateKey = get_option('rm_option_private_key');
    }
    
    public function render() {        
        require_once(dirname(__FILE__) . "/../Resources/recaptchalib.php");
        echo rm_recaptcha_get_html($this->publicKey,$this->version,null,false);
        if($this->version==3){
            echo '<style>.rm_captcha_fieldrow{display:none;}</style>';
        }
    }

    public function getJSFiles() {
        $locale= get_locale();
        $lang= explode('_', $locale);
        $js= array(
            'script_rm_captcha' => RM_BASE_URL . 'public/js/script_rm_captcha.js',
            'google_captcha_api' => "https://www.google.com/recaptcha/api.js?onload=rmInitCaptcha&render=explicit&hl=$lang[0]",
        );
        if($this->version==3){
           $js['google_captcha_api']="https://www.google.com/recaptcha/api.js?onload=rmInitCaptcha&render=".$this->publicKey;
        }
        return $js;
    }

    public function getJSDeps() {
        return array(
            'script_rm_captcha'
        );
    }
    
    public function localizeJS(){
       return array('script_rm_captcha'=>array('name'=>'rm_recap_ver','value'=>array('api'=>$this->version)));
    }
    
}
