<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_login extends MEC_base
{
    public $factory;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();
    }
    
    /**
     * Initialize search feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // login form shortcode
        $this->factory->shortcode('MEC_login', array($this, 'login'));

        $this->factory->action('wp_ajax_mec_ajax_login_data', array($this, 'mec_ajax_login_data'));
        $this->factory->action('wp_ajax_nopriv_mec_ajax_login_data', array($this, 'mec_ajax_login_data'));
    }

    public function mec_ajax_login_data()
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_login_nonce'])) return;
        
        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['mec_login_nonce']), 'mec-ajax-login-nonce')) return;
        
        $info = array();
        $info['user_login'] = $_POST['username'];
        $info['user_password'] = $_POST['password'];
        $info['remember'] = true;

        $user_signon = wp_signon($info, true); // secure_cookie set true.
        if(is_wp_error($user_signon))
        {
            echo json_encode(array('loggedin'=>false, 'message'=>__('<strong>'.esc_html__('Wrong username or password, reloading...', 'modern-events-calendar-lite').'</strong>')));
        }
        else
        {
            echo json_encode(array('loggedin'=>true, 'message'=>__('<strong>'.esc_html__('Login successful, redirecting...', 'modern-events-calendar-lite').'</strong>')));
        }

        die();
    }

    /**
     * Show user login form
     * @return string
     */
    public function login()
    {
        $path = MEC::import('app.features.login.login', true, true);

        ob_start();
        include $path;
        return ob_get_clean();
    }
}