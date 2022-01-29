<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class responsible for User and Roles related operations
 *
 * @author CMSHelplive
 */
class RM_User_Services extends RM_Services
{

    public $default_user_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');

    public function get_user_roles() {
        $roles = get_editable_roles();
        $role_names = array();
        foreach ($roles as $key => $role) {
            $role_names[$key] = $role['name'];
        }
        return $role_names;
    }
    
    public function add_default_form($form=null,$role=null) {
        $role =isset($_POST['role'])? $_POST['role'] : null;
        $form =isset($_POST['form'])? $_POST['form'] : null;
        if(isset($role) && isset($form)) {
            $gopts= new RM_Options;
            $default_forms=array();
            $opt_default_forms=$gopts->get_value_of('rm_option_default_forms');
            $default_forms= maybe_unserialize($opt_default_forms);
            if(defined('REGMAGIC_ADDON')) {
                $def = $default_forms;
                foreach ($def as $key => $val) {
                    if ($val == $form) {
                        $default_forms[$key] = null;
                    }
                }
            }
            if($form == '') {
                $default_forms[$role]=null;
                $opt_default_forms=  maybe_serialize($default_forms);
                $gopts->set_value_of('rm_option_default_forms',$opt_default_forms);
                echo "";
                die;
            }  
            $default_forms[$role]=$form;
            $opt_default_forms=  maybe_serialize($default_forms);
            $gopts->set_value_of('rm_option_default_forms',$opt_default_forms);
            $forms_options=new RM_Forms;
            $forms_options->load_from_db($form);
            $form_name=$forms_options->get_form_name();
            echo $form_name;
            die;
        }
        echo "";
        die;
    }

    // This function creates a copy of the role with a different name
    public function create_role($role_name, $display_name, $capability, $additional_data = null) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->create_role($role_name, $display_name, $capability, $additional_data, $this);
        }
        $role = get_role($capability);
        if (add_role($role_name, $display_name, $role->capabilities) !== null)
            return true;
        else
            return false;
    }

    public function get_roles_by_status() {
        $roles_data = new stdClass();
        $roles = $this->get_user_roles();
        $custom = array();
        $default = array();
        $linked_form = array();
        foreach ($roles as $key => $role) {
            if (in_array($key, $this->default_user_roles)) {
                $default[$key] = $role;
                $linked_form[$key]=$this->get_linked_forms($key);
            } else {
                $custom[$key] = $role;
                $linked_form[$key]=$this->get_linked_forms($key);
            }
        }
        $roles_data->default = $default;
        $roles_data->custom = $custom;
        $roles_data->linked_forms=$linked_form;
        return $roles_data;
    }
    
    public function get_linked_forms($role) {
      $forms= RM_DBManager::get('FORMS', array("default_user_role" => $role), array("%s"));
      $linked_form=array();
      if($forms != null) {
          foreach($forms as $form) {
              $linked_form[$form->form_id] = $form->form_name;
          }
      }
      return $linked_form;
    }
    
    public function delete($users,$reassign=null) {
        if (is_array($users) && !empty($users)) {
            $curr_user = wp_get_current_user();
            if (isset($curr_user->ID))
                $curr_user_id = $curr_user->ID;
            else
                $curr_user_id = null;
            foreach ($users as $id) {
                if ($curr_user_id != $id){
                    wp_delete_user($id,$reassign);
                } 
            }
        }
    }

    public function activate($users) {
        $user_model= new RM_User;
        if (is_array($users) && !empty($users)) {
            foreach ($users as $id) {
                $user_model->activate_user($id);
            }
        }
    }

    public function notify_users($users, $type) {
        if (is_array($users) && !empty($users)) {
            $front_form_service = new RM_Front_Form_Service;
            foreach ($users as $id) {
                $user = get_user_by('id', $id);
                $params = new stdClass;
                $params->email = $user->user_email;                
                $params->sub_id = get_user_meta($id, 'RM_UMETA_SUB_ID', true);
                $params->form_id = get_user_meta($id, 'RM_UMETA_FORM_ID', true);
                RM_Email_Service::notify_user_on_activation($params);
            }
        }
    }
    
    public static function send_email_ajax()
    {
        check_ajax_referer( 'rm_send_email_user_view', 'rm_ajaxnonce' );
        if (current_user_can('manage_options'))
        {
            $to = $_POST['to'];
            $sub = $_POST['sub'];
            $body = $_POST['body'];

            RM_Utilities::quick_email($to, $sub, $body);
        }
        wp_die();
    }

    public function deactivate_user_by_id($user_id) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->deactivate_user_by_id($user_id, $this);
        }
        $user_model= new RM_User;
        $curr_user = wp_get_current_user();
        if (isset($curr_user->ID))
            $curr_user_id = $curr_user->ID;
        else
            $curr_user_id = null;
        if ($curr_user_id != $user_id)
            $user_model->deactivate_user ($user_id);
    }

    public function activate_user_by_id($user_id)
    {
        $user_model= new RM_User;
        return $user_model->activate_user($user_id);
    }

    public function deactivate($users) {
        $user_model= new RM_User;
        if (is_array($users) && !empty($users)) {
            $curr_user = wp_get_current_user();
            if (isset($curr_user->ID))
                $curr_user_id = $curr_user->ID;
            else
                $curr_user_id = null;
            foreach ($users as $id) {
                if ($curr_user_id != $id)
                    $user_model->deactivate_user($id);
            }
        }
    }

    public function delete_roles($roles) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->delete_roles($roles, $this);
        }
        if (is_array($roles) && !empty($roles)) {
            foreach ($roles as $name) {
                $users = $this->get_users_by_role($name);
                foreach ($users as $user) {
                    $user->add_role('subscriber');
                }

                remove_role($name);
            }
        }
    }

    public function get_users_by_role($role_name) {
        $args = array('role' => $role_name);
        $users = get_users($args);
        return $users;
    }

    public function get_user_count() {
        $result = count_users();
        $total_users = $result['total_users'];
        return $total_users;
    }

    public function get_users($offset = '', $number = '', $search_str = '', $user_status = 'all', $interval = 'all', $user_ids = array(), $fields_to_return = 'all') {
        $args = array('number' => $number, 'offset' => $offset, 'include' => $user_ids, 'search' => '*' . $search_str . '*');

        if(defined('REGMAGIC_ADDON')) {
            $args['fields'] = $fields_to_return;
        }
        
        switch ($user_status) {
            case 'active':
                $args['meta_query'] = array('relation' => 'OR',
                    array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => '!='
                    ),
                    array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => 'NOT EXISTS'
                ));
                break;

            case 'pending':
                $args['meta_query'] = array(array(
                        'key' => 'rm_user_status',
                        'value' => '1',
                        'compare' => '='
                ));
                break;
        }

        switch ($interval) {
            case 'today':
                $args['date_query'] = array(array('after' => date('Y-m-d', strtotime('today')), 'inclusive' => true));
                break;

            case 'week':
                $args['date_query'] = array(array('after' => date('Y-m-d', strtotime('this week')), 'inclusive' => true));
                break;

            case 'month':
                $args['date_query'] = array(array('after' => 'first day of this month', 'inclusive' => true));
                break;

            case 'year':
                $args['date_query'] = array(array('year' => date('Y'), 'inclusive' => true));
                break;
        }
        //echo "Args:<pre>", var_dump($args), "</pre>";
        $users = get_users($args);

        return $users;
    }

    public function get_total_user_per_pagination() {
        $total = $this->get_user_count();
        return (int) ($total / 2) + (($total % 2) == 0 ? 0 : 1);
    }

    public function get_all_user_data($page = '1', $number = '20', $search_str = '', $user_status = 'all', $interval = 'all', $user_ids = array()) {
        $offset = ($page * $number) - $number;
        $all_user_info = $this->get_users($offset, $number, $search_str, $user_status, $interval, $user_ids);
        $all_user_data = array();

        foreach ($all_user_info as $user) {

            $tmpuser = new stdClass();
            $user_info = get_userdata($user->ID);
            $is_disabled = (int) get_user_meta($user->ID, 'rm_user_status', true);
            $tmpuser->ID = $user->ID;

            if (empty($user_info->display_name))
                $tmpuser->first_name = $user_info->first_name;
            else
                $tmpuser->first_name = $user_info->display_name;

            if (isset($user_info->user_email))
                $tmpuser->user_email = $user_info->user_email;
            else
                $tmpuser->user_email = '';

            if ($is_disabled == 1)
                $tmpuser->user_status = RM_UI_Strings::get('LABEL_DEACTIVATED');
            else
                $tmpuser->user_status = RM_UI_Strings::get('LABEL_ACTIVATED');

            $tmpuser->date = $user_info->user_registered;

            $all_user_data[] = $tmpuser;
        }

        return $all_user_data;
    }

    public function get_user_by($field, $value) {
        $user = get_user_by($field, $value);
        return $user;
    }

    public function login($request) {
        global $user;
        $credentials = array();
        $credentials['user_login'] = $request->req['username'];
        $credentials['user_password'] = $request->req['pwd'];
        if (isset($request->req['remember']))
            $credentials['remember'] = true;
        else
            $credentials['remember'] = false;

        require_once(ABSPATH . 'wp-load.php');
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        $user = wp_signon($credentials, is_ssl());
        if(!is_wp_error($user)){
             do_action('rm_user_signon',$user);
        }
        else
        {
              do_action('rm_user_signon_failure',$credentials);
        }
        return $user;
    }
    
    public function google_login_html() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->google_login_html($this);
        }
    }
    
    public function linkedin_login_html() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->linkedin_login_html($this);
        }
    }
    
    public function instagarm_login_html() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->instagarm_login_html($this);
        }
    }
    
    public function twitter_login_html() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->twitter_login_html($this);
        }
    }
    
    public function windows_login_html() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->windows_login_html($this);
        }
    }

    public function facebook_login_html() {
        if(!RM_Utilities::is_ssl()){
            return;
        }


        $gopts = new RM_Options;
        if ($gopts->get_value_of('enable_facebook') == 'yes') {
            $fb_app_id = $gopts->get_value_of('facebook_app_id');
            if (!$fb_app_id)
                return;
            
            return "<pre class='rm-pre-wrapper-for-script-tags'><script>
  function checkLoginState() {
   FB.getLoginStatus(function(response) {
  if (response.status === 'connected') {
   greet();
  }
  else {
  FB.login(function(response) {
FB.api('/me',{fields: 'first_name,email'}, function (response) {
	handle_data(response.email,response.first_name,'facebook');


});
}, {scope: 'email'});
  }
});
  }
function greet() {
FB.api('/me',{fields: 'first_name,email'}, function (response) {
	handle_data(response.email,response.first_name,'facebook');


});
}
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '" . $fb_app_id . "',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.5' // use graph api version 2.5
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = '//connect.facebook.net/en_US/sdk.js';
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
 
</script></pre>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->
<div class='rm-facebook-login rm-third-party-login'><input class='rm-third-party-login-btn' type='button' onclick='checkLoginState()' value='".__('Sign in with Facebook','custom-registration-form-builder-with-submission-manager')."' /><span><svg aria-hidden='true' data-prefix='fab' data-icon='facebook-f' class='svg-inline--fa fa-facebook-f fa-w-9' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 264 512'><path fill='#fff' d='M76.7 512V283H0v-91h76.7v-71.7C76.7 42.4 124.3 0 193.8 0c33.3 0 61.9 2.5 70.2 3.6V85h-48.2c-37.8 0-45.1 18-45.1 44.3V192H256l-11.7 91h-73.6v229'></path></svg></span> </div>";
            //  return '<div class="facebook_login"><a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a></div>';
        }
    }

    public function facebook_login_callback()
    {
        global $rm_env_requirements;

        if (!($rm_env_requirements & RM_REQ_EXT_CURL))
            return;

        global $rm_fb_sdk_req;
        $gopts = new RM_Options;

        $fb_app_id = $gopts->get_value_of('facebook_app_id');
        $fb_app_secret = $gopts->get_value_of('facebook_app_secret');

        if (!$fb_app_id || !$fb_app_secret)
            return;

        if ($rm_fb_sdk_req === RM_FB_SDK_REQ_OK)
        {
            $fb = new Facebook\Facebook(array(
                'app_id' => $fb_app_id,
                'app_secret' => $fb_app_secret,
                'default_graph_version' => 'v2.2',
            ));

            $helper = $fb->getRedirectLoginHelper();

            try
            {
                $accessToken = $helper->getAccessToken();
            } catch (Facebook\Exceptions\FacebookResponseException $e)
            {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e)
            {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            if (!isset($accessToken))
            {
                if ($helper->getError())
                {
                    header('HTTP/1.0 401 Unauthorized');
                    echo "Error: " . $helper->getError() . "\n";
                    echo "Error Code: " . $helper->getErrorCode() . "\n";
                    echo "Error Reason: " . $helper->getErrorReason() . "\n";
                    echo "Error Description: " . $helper->getErrorDescription() . "\n";
                } else
                {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Bad request';
                }
                exit;
            }

            // Logged in
            // echo '<h3>Access Token</h3>';
            //var_dump($accessToken->getValue());
            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);

            //echo '<h3>Metadata</h3>';
            //var_dump($tokenMetadata);
            // Validation (these will throw FacebookSDKException's when they fail)

            $tokenMetadata->validateAppId($fb_app_id); // Replace {app-id} with your app id
            // If you know the user ID this access token belongs to, you can validate it here
            //$tokenMetadata->validateUserId('123');
            $tokenMetadata->validateExpiration();

            if (!$accessToken->isLongLived())
            {
                // Exchanges a short-lived access token for a long-lived one
                try
                {
                    $accessToken2 = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e)
                {
                    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                    exit;
                }

                //echo '<h3>Long-lived</h3>';
                //var_dump($accessToken2->getValue());
            }



            //$_SESSION['fb_access_token'] = (string) $accessToken;



            try
            {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->get('/me?fields=id,name,email,first_name,last_name', (string) $accessToken);
            } catch (Facebook\Exceptions\FacebookResponseException $e)
            {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e)
            {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            $user = $response->getGraphUser();

            //var_dump($user->getFirstName());
            $user_name = $user->getName();
            $user_email = $user->getEmail();
            $user_name = $user->getName();
            $user_fname = $user->getFirstName();
            $user_lname = $user->getLastName();
            $redirection_post = $gopts->get_value_of('post_submission_redirection_url');

            if (email_exists($user_email))
            { // user is a member
                $user = get_user_by('email', $user_email);

                $user_id = $user->ID;
                
                $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);
                        
                if(!$is_disabled)
                    wp_set_auth_cookie($user_id, true);
                
            } else
            { // this user is a guest
                $random_password = wp_generate_password(10, false);

                $user_id = wp_create_user($user_email, $random_password, $user_email);

                if (!is_wp_error($user_id))
                {
                    if (function_exists('is_multisite') && is_multisite())
                        add_user_to_blog(get_current_blog_id(), $user_id, get_option('default_role'));

                    update_user_meta($user_id, 'avatar_image', 'https://graph.facebook.com/' . $user->getId() . '/picture?type=large');

                    wp_update_user(array(
                        'ID' => $user_id,
                        'display_name' => $user_name,
                        'first_name' => $user_fname,
                        'last_name' => $user_lname
                    ));
                    
                    //varify auto approval setting
                    $auto_approval = $gopts->get_value_of('user_auto_approval');

                    if($auto_approval == 'yes')
                    {
                        wp_set_auth_cookie($user_id, true);
                    }
                    else  //Deactivate the user
                    {
                       update_user_meta($user_id, 'rm_user_status', '1');
                    }
                }
            }
        } else
        {
            $fb = new Facebook(array(
                'appId' => $fb_app_id,
                'secret' => $fb_app_secret
            ));

            $user = $fb->getUser();

            if ($user)
            {
                $user_profile = $fb->api('/me?fields=id,name,email,first_name,last_name');
                if (isset($user_profile['email']))
                {
                    $user_email = $user_profile['email'];
                    $redirection_post = $gopts->get_value_of('post_submission_redirection_url');

                    if (email_exists($user_email))
                    { // user is a member
                        $user = get_user_by('email', $user_email);
                        $user_id = $user->ID;
                        $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);
                        if(!$is_disabled)
                            wp_set_auth_cookie($user_id, true);
                    } else
                    { // this user is a guest
                        $random_password = wp_generate_password(10, false);

                        $user_id = wp_create_user($user_email, $random_password, $user_email);
                        if (!is_wp_error($user_id))
                        { 

                            if (function_exists('is_multisite') && is_multisite())
                                add_user_to_blog(get_current_blog_id(), $user_id, 'subscriber');

                            update_user_meta($user_id, 'avatar_image', 'https://graph.facebook.com/' . $user_profile['id'] . '/picture?type=large');

                            wp_update_user(array(
                                'ID' => $user_id,
                                'display_name' => $user_profile['name'],
                                'first_name' => $user_profile['first_name'],
                                'last_name' => $user_profile['last_name']
                            ));
                            //varify auto approval setting
                            $auto_approval = $gopts->get_value_of('user_auto_approval');

                            if($auto_approval == 'yes')
                            {
                                wp_set_auth_cookie($user_id, true);
                            }
                            else  //Deactivate the user
                            {
                               update_user_meta($user_id, 'rm_user_status', '1');
                            }
                        }
                    }
                } else
                    die('Error: Unable to fetch email address from Facebbok.');
            }
        }

        $rdrto = RM_Utilities::after_login_redirect($user);
            
        if(!$rdrto)
            $rdrto = apply_filters( 'login_redirect', $redirect_to, "", $user );

        if(!$rdrto || $rdrto == "__current_url") {
            $rdrto = "";
        }
        
        $after_login_url = $rdrto;
        RM_Utilities::redirect($after_login_url);
    }

    public function set_user_role($user_id, $role)
    {
        $user = new WP_User($user_id);
        $user->set_role($role);
    }

    public function reset_user_password($pass, $conf, $user_id) {
        if ($pass && $conf && $user_id) {
            if ($pass === $conf) {
                wp_set_password($pass, $user_id);
            }
        } else {
            throw new InvalidArgumentException("Invalid Argument Supplied in " . __CLASS__ . '::' . __FUNCTION__);
        }
    }

    public function create_user_activation_link($user_id) {
        if ((int) $user_id) {
            $pass = wp_generate_password(10, false);
            $activation_code = md5($pass);

            if (!update_user_meta($user_id, 'rm_activation_code', $activation_code))
                return false;

            $user_data_obj = new stdClass();
            $user_data_obj->user_id = $user_id;
            $user_data_obj->activation_code = $activation_code;

            $user_data_json = json_encode($user_data_obj);

            $user_data_enc = urlencode(RM_Utilities::enc_str($user_data_json));

            $user_activation_link = admin_url('admin-ajax.php') . '?action=rm_activate_user&user=' . $user_data_enc;

            return $user_activation_link;
        }

        return false;
    }
    
    public function social_login_using_email($user_email = null, $user_fname = null,$type=null) {
        $ajax_check = check_ajax_referer('rm-social-login-security', 'security'); // Check referer validity
        if($ajax_check == false) {
            $resp = array('code' => 'denied', 'msg' => __('Request denied','custom-registration-form-builder-with-submission-manager'));
            echo json_encode($resp);
            die;
        }
        if (isset($_POST['email']))
            $user_email = $_POST['email'];
        if (isset($_POST['first_name']))
            $user_fname = $_POST['first_name'];
        else
            $user_fname = null;
        $type= isset($_POST['type']) ? $_POST['type'] : '';
        $user_model= new RM_User;
        $gopts = new RM_Options;
        $resp = array('code' => 'allowed', 'msg' => '');
        $login_service= new RM_Login_Service();
        // error_log($user_email); error_log($user_fname);
        $user = $user_email;
        if ($user_email != null) {
            if (email_exists($user_email)) { // user is a member
                $user = get_user_by('email', $user_email);
                $user_id = (int) $user->data->ID;
                $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);

                if (!$is_disabled){
                    //$login_service->insert_login_log(array('email'=>$user->user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>1,'type'=>'social:'.$type,'result'=>'success'));
                    $login_service->insert_login_log(array('email'=>$user->user_email,'username_used'=>$user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>1,'type'=>'social','result'=>'success','social_type'=>$type));
                    wp_set_auth_cookie($user_id, true);
                }
                else {
                    $login_service->insert_login_log(array('email'=>$user->user_email,'username_used'=>$user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>0,'type'=>'social','result'=>'failure','social_type'=>$type));
                    $resp['code'] = 'denied';
                    $resp['msg'] = RM_UI_Strings::get("RM_SOCIAL_ERR_ACC_UNAPPROVED"); //"Please wait for admin's approval before you can log in";
                }
            } else if (username_exists($user_email)) {
                $user = get_user_by('login', $user_email);
                $user_id = (int) $user->data->ID;
                $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);
                $username_used='';
                if($type=='instagram'){
                    $username_used= $user_email;
                }
                if (!$is_disabled){
                    $login_service->insert_login_log(array('email'=>$user->user_email,'username_used'=>$user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>1,'type'=>'social','result'=>'success','social_type'=>$type,'username_used'=>$username_used));
                    wp_set_auth_cookie($user_id, true);
                }
                else {
                    $login_service->insert_login_log(array('email'=>$user->user_email,'username_used'=>$user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>0,'type'=>'social','result'=>'failure','social_type'=>$type,'username_used'=>$username_used));
                    $resp['code'] = 'denied';
                    $resp['msg'] = RM_UI_Strings::get("RM_SOCIAL_ERR_ACC_UNAPPROVED"); //"Please wait for admin's approval before you can log in";
                }
            } else { // this user is a guest
                $random_password = wp_generate_password(10, false);

                $user_id = wp_create_user($user_email, $random_password, $user_email);
                if (!is_wp_error($user_id)) {
                    if (function_exists('is_multisite') && is_multisite())
                        add_user_to_blog(get_current_blog_id(), $user_id, 'subscriber');

                    wp_update_user(array(
                        'ID' => $user_id,
                        'display_name' => $user_fname,
                        'first_name' => $user_fname
                    ));

                    //varify auto approval setting
                    $auto_approval = $gopts->get_value_of('user_auto_approval');

                    if ($auto_approval == 'yes') {
                        wp_set_auth_cookie($user_id, true);
                    } else {  //Deactivate the user
                        $user_model->deactivate_user($user_id);
                        $user_service = new RM_User_Services;
                        $link = $user_service->create_user_activation_link($user_id);
                        $user_info = get_userdata($user_id);
                        $required_params = new stdClass();
                        $required_params->email = $user_email;
                        $required_params->username = $user_info->display_name;
                        //required_params->form_id= $form_id;             

                        $required_params->link = $link;

                        // ob_start(); var_dump('datas',$auto_approval,$link,$required_params->email, $required_params->link); $out=ob_get_clean(); error_log($out);    

                        RM_Email_Service::notify_admin_to_activate_user($required_params);



                        $resp['code'] = 'denied';
                        $resp['msg'] = RM_UI_Strings::get("RM_SOCIAL_ERR_NEW_ACC_UNAPPROVED"); //"Account has been created. Please wait for admin's approval before you can log in";
                    }

                    /*       if ($auto_approval != "yes") {
                      $this->deactivate_user_by_id($user_id);
                      }
                      else{
                      $this->activate_user_by_id($user_id);} */

                    /*
                      error_log('niku');
                      if($auto_approval != "yes"){

                      $link = $this->create_user_activation_link($user_id);
                      $required_params = new stdClass();
                      $required_params->email = $user;
                      // $required_params->form_id= $form_id;

                      $required_params->link = $link;

                      ob_start(); var_dump('datas',$auto_approval,$link,$required_params->email, $required_params->link); $out=ob_get_clean(); error_log($out);

                      RM_Email_Service::notify_admin_to_activate_user($required_params);
                      } */
                }
            }

            $rdrto = RM_Utilities::after_login_redirect($user);

            if (!$rdrto)
                $rdrto = apply_filters('login_redirect', $redirect_to, "", $user);

            if (!$rdrto || $rdrto == "__current_url") {
                $rdrto = "";
            }

            $after_login_url = $rdrto;


            if ($resp['code'] == 'allowed')
                $resp['msg'] = $after_login_url;

            echo json_encode($resp);

            die;
        }
    }
    
    public function get_twitter_keys() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->get_twitter_keys($this);
        }
    }

    public function get_instagram_user() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->get_instagram_user($this);
        }
    }
    
    public function auto_login_by_id($user_id){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_User_Services_Addon();
            return $addon_service->auto_login_by_id($user_id, $this);
        }
    }
    
    public function get_user_meta_dropdown(){
        $metas= array();
        $rows= RM_DBManager::get_all_user_meta();
        foreach($rows as $row){
            array_push($metas,$row->meta_key);
        }
        return $metas;
    }

}