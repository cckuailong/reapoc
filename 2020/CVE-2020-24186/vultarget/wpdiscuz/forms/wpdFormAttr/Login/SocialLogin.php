<?php

namespace wpdFormAttr\Login;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Login\twitter\TwitterOAuthException;
use wpdFormAttr\Login\twitter\TwitterOAuth;
use wpdFormAttr\Login\Utils;

class SocialLogin {

    private static $_instance = null;
    private $generalOptions;

    private function __construct($options) {
        $this->generalOptions = $options;
        add_action("init", [&$this, "requestHandler"]);
        add_action("wpdiscuz_front_scripts", [&$this, "socialScripts"]);
        add_action("comment_main_form_bar_top", [&$this, "getButtons"]);
        add_action("comment_main_form_after_head", [&$this, "getAgreement"]);
        add_action("comment_reply_form_bar_top", [&$this, "getReplyFormButtons"], 1);
        add_action("comment_reply_form_bar_top", [&$this, "getAgreement"], 2);
        add_action("wp_ajax_wpd_social_login", [&$this, "login"]);
        add_action("wp_ajax_nopriv_wpd_social_login", [&$this, "login"]);
        add_action("wp_ajax_wpd_login_callback", [&$this, "loginCallBack"]);
        add_action("wp_ajax_nopriv_wpd_login_callback", [&$this, "loginCallBack"]);
        add_filter("get_avatar", [&$this, "userAvatar"], 999, 6);
    }

    public function requestHandler() {
        if ($this->generalOptions->social["enableInstagramLogin"] && (strpos($_SERVER['REQUEST_URI'], "wpdiscuz_auth/instagram") !== false)) {
            $this->instagramLoginCallBack();
        }
        if ($this->generalOptions->social["enableLinkedinLogin"] && (strpos($_SERVER['REQUEST_URI'], "wpdiscuz_auth/linkedin") !== false)) {
            $this->linkedinLoginCallBack();
        }
    }

    public function login() {
        $postID = filter_input(INPUT_POST, "postID", FILTER_SANITIZE_NUMBER_INT);
        $provider = filter_input(INPUT_POST, "provider", FILTER_SANITIZE_STRING);
        $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_STRING);
        $userID = filter_input(INPUT_POST, "userID", FILTER_SANITIZE_NUMBER_INT);
        $response = ["code" => "error", "message" => esc_html__("Authentication failed.", "wpdiscuz"), "url" => ""];
        if ($provider === "facebook") {
            if ($this->generalOptions->social["fbUseOAuth2"]) {
                $response = $this->facebookLoginPHP($postID, $response);
            } else {
                $response = $this->facebookLogin($token, $userID, $response);
            }
        } else if ($provider === "instagram") {
            $response = $this->instagramLogin($postID, $response);
        } else if ($provider === "google") {
            $response = $this->googleLogin($postID, $response);
        } else if ($provider === "disqus") {
            $response = $this->disqusLogin($postID, $response);
        } else if ($provider === "wordpress") {
            $response = $this->wordpressLogin($postID, $response);
        } else if ($provider === "twitter") {
            $response = $this->twitterLogin($postID, $response);
        } else if ($provider === "vk") {
            $response = $this->vkLogin($postID, $response);
        } else if ($provider === "ok") {
            $response = $this->okLogin($postID, $response);
        } else if ($provider === "yandex") {
            $response = $this->yandexLogin($postID, $response);
        } else if ($provider === "mailru") {
            $response = $this->mailruLogin($postID, $response);
        } else if ($provider === "linkedin") {
            $response = $this->linkedinLogin($postID, $response);
        } else if ($provider === "wechat") {
            $response = $this->wechatLogin($postID, $response);
        } else if ($provider === "qq") {
            $response = $this->qqLogin($postID, $response);
        } else if ($provider === "weibo") {
            $response = $this->weiboLogin($postID, $response);
        } else if ($provider === "baidu") {
            $response = $this->baiduLogin($postID, $response);
        }
        if (!$response["url"]) {
            $response["url"] = $this->getPostLink($postID);
        }
        wp_die(json_encode(apply_filters("wpdiscuz_social_login_response", $response, $provider, $postID, $token, $userID)));
    }

    public function loginCallBack() {
        $this->deleteCookie();
        $provider = filter_input(INPUT_GET, "provider", FILTER_SANITIZE_STRING);
        if ($provider === "facebook") {
            $response = $this->facebookLoginPHPCallBack();
        } else if ($provider === "google") {
            $response = $this->googleLoginCallBack();
        } else if ($provider === "twitter") {
            $response = $this->twitterLoginCallBack();
        } else if ($provider === "disqus") {
            $response = $this->disqusLoginCallBack();
        } else if ($provider === "wordpress") {
            $response = $this->wordpressLoginCallBack();
        } else if ($provider === "vk") {
            $response = $this->vkLoginCallBack();
        } else if ($provider === "ok") {
            $response = $this->okLoginCallBack();
        } else if ($provider === "yandex") {
            $response = $this->yandexLoginCallBack();
        } else if ($provider === "mailru") {
            $response = $this->mailruLoginCallBack();
        } else if ($provider === "wechat") {
            $response = $this->wechatLoginCallBack();
        } else if ($provider === "qq") {
            $response = $this->qqLoginCallBack();
        } else if ($provider === "weibo") {
            $response = $this->weiboLoginCallBack();
        } else if ($provider === "baidu") {
            $response = $this->baiduLoginCallBack();
        }
    }

    private function getPostLink($postID) {
        $url = home_url();
        if ($postID) {
            $url = get_permalink($postID);
        }
        return esc_url_raw($url);
    }

    // https://developers.facebook.com/docs/apps/register
    public function facebookLogin($token, $userID, $response) {
        if (!$token || !$userID) {
            $response["message"] = esc_html__("Facebook access token or user ID invalid.", "wpdiscuz");
            return $response;
        }
        if (!$this->generalOptions->social["fbAppSecret"]) {
            $response["message"] = esc_html__("Facebook App Secret is required.", "wpdiscuz");
            return $response;
        }
        $appsecret_proof = hash_hmac("sha256", $token, trim($this->generalOptions->social["fbAppSecret"]));
        $url = add_query_arg(["fields" => "id,first_name,last_name,picture,email", "access_token" => $token, "appsecret_proof" => $appsecret_proof], "https://graph.facebook.com/v2.8/" . $userID);
        $fb_response = wp_remote_get(esc_url_raw($url), ["timeout" => 30]);

        if (is_wp_error($fb_response)) {
            $response["message"] = $fb_response->get_error_message();
            return $response;
        }

        $fb_user = json_decode(wp_remote_retrieve_body($fb_response), true);

        if (isset($fb_user["error"])) {
            $response["message"] = "Error code: " . $fb_user["error"]["code"] . " - " . $fb_user["error"]["message"];
            return $response;
        }
        if (empty($fb_user["email"]) && $fb_user["id"]) {
            $fb_user["email"] = $fb_user["id"] . "@facebook.com";
        }
        $this->setCurrentUser(Utils::addUser($fb_user, "facebook"));
        $uID = Utils::addUser($fb_user, "facebook");
        if (is_wp_error($uID)) {
            $response["message"] = $uID->get_error_message();
        }else{
            $response = ["code" => 200];
        }
        $this->setCurrentUser($uID);
        return $response;
    }

    public function facebookLoginPHP($postID, $response) {
        if (!$this->generalOptions->social["fbAppID"] || !$this->generalOptions->social["fbAppSecret"]) {
            $response["message"] = esc_html__("Facebook Application ID and Application Secret  required.", "wpdiscuz");
            return $response;
        }
        $fbAuthorizeURL = "https://www.facebook.com/v7.0/dialog/oauth";
        $fbCallBack = $this->createCallBackURL("facebook");
        $state = Utils::generateOAuthState($this->generalOptions->social["fbAppID"]);
        Utils::addOAuthState("facebook", $state, $postID);
        $oautAttributs = [
            "client_id" => $this->generalOptions->social["fbAppID"],
            "redirect_uri" => urlencode($fbCallBack),
            "response_type" => "code",
            "scope" => "email,public_profile",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $fbAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function facebookLoginPHPCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "facebook")) {
            $this->redirect($postID, esc_html__("Facebook authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Facebook authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $fbCallBack = $this->createCallBackURL("facebook");
        $fbAccessTokenURL = "https://graph.facebook.com/v7.0/oauth/access_token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["fbAppID"],
            "client_secret" => $this->generalOptions->social["fbAppSecret"],
            "redirect_uri" => urlencode($fbCallBack),
            "code" => $code];
        $fbAccessTokenURL = add_query_arg($accessTokenArgs, $fbAccessTokenURL);
        $fbAccesTokenResponse = wp_remote_get($fbAccessTokenURL);

        if (is_wp_error($fbAccesTokenResponse)) {
            $this->redirect($postID, $fbAccesTokenResponse->get_error_message());
        }
        $fbAccesTokenData = json_decode(wp_remote_retrieve_body($fbAccesTokenResponse), true);
        if (isset($fbAccesTokenData["error"])) {
            $this->redirect($postID, $fbAccesTokenData["error"]["message"]);
        }
        $token = $fbAccesTokenData["access_token"];
        $appsecret_proof = hash_hmac("sha256", $token, trim($this->generalOptions->social["fbAppSecret"]));
        $fbGetUserDataURL = add_query_arg(["fields" => "id,first_name,last_name,picture,email", "access_token" => $token, "appsecret_proof" => $appsecret_proof], "https://graph.facebook.com/v7.0/me");
        $getFbUserResponse = wp_remote_get($fbGetUserDataURL);
        if (is_wp_error($getFbUserResponse)) {
            $this->redirect($postID, $getFbUserResponse->get_error_message());
        }
        $fbUserData = json_decode(wp_remote_retrieve_body($getFbUserResponse), true);
        if (isset($fbUserData["error"])) {
            $this->redirect($postID, $fbUserData["error"]["message"]);
        }
        if (empty($fbUserData["email"]) && $fbUserData["id"]) {
            $fbUserData["email"] = $fbUserData["id"] . "@facebook.com";
        }
        $uID = Utils::addUser($fbUserData, "facebook");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    // https://developers.facebook.com/docs/instagram-basic-display-api/getting-started
    public function instagramLogin($postID, $response) {
        if (!$this->generalOptions->social["instagramAppID"] || !$this->generalOptions->social["instagramAppSecret"]) {
            $response["message"] = esc_html__("Instagram Application ID and Application Secret  required.", "wpdiscuz");
            return $response;
        }
        $instagramAuthorizeURL = "https://api.instagram.com/oauth/authorize";
        $instagramCallBack = site_url('/wpdiscuz_auth/instagram/');
        $state = Utils::generateOAuthState($this->generalOptions->social["instagramAppID"]);
        Utils::addOAuthState("instagram", $state, $postID);
        $oautAttributs = [
            "client_id" => $this->generalOptions->social["instagramAppID"],
            "redirect_uri" => $instagramCallBack,
            "response_type" => "code",
            "scope" => "user_profile,user_media",
            "state" => $state
        ];
        $oautURL = add_query_arg($oautAttributs, $instagramAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function instagramLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];

        if (!$state || ($provider != "instagram")) {
            $this->redirect($postID, esc_html__("Instagram authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Instagram authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $instagramCallBack = site_url('/wpdiscuz_auth/instagram/');
        $instagramAccessTokenURL = "https://api.instagram.com/oauth/access_token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["instagramAppID"],
            "client_secret" => $this->generalOptions->social["instagramAppSecret"],
            "grant_type" => "authorization_code",
            "redirect_uri" => $instagramCallBack,
            "code" => $code];
        $instagramAccesTokenResponse = wp_remote_post($instagramAccessTokenURL, ['body' => $accessTokenArgs]);

        if (is_wp_error($instagramAccesTokenResponse)) {
            $this->redirect($postID, $instagramAccesTokenResponse->get_error_message());
        }
        $instagramAccesTokenData = json_decode(wp_remote_retrieve_body($instagramAccesTokenResponse), true);
        if (isset($instagramAccesTokenData["error"])) {
            $this->redirect($postID, $instagramAccesTokenData["error"]["message"]);
        }
        $token = $instagramAccesTokenData["access_token"];
        $userID = $instagramAccesTokenData["user_id"];
        $appsecret_proof = hash_hmac("sha256", $token, trim($this->generalOptions->social["instagramAppSecret"]));
        $instagramGetUserDataURL = add_query_arg(["fields" => "id,username", "access_token" => $token, "appsecret_proof" => $appsecret_proof], "https://graph.instagram.com/$userID");
        $getInstagramUserResponse = wp_remote_get($instagramGetUserDataURL);

        if (is_wp_error($getInstagramUserResponse)) {
            $this->redirect($postID, $getInstagramUserResponse->get_error_message());
        }
        $instagramUserData = json_decode(wp_remote_retrieve_body($getInstagramUserResponse), true);
        if (isset($instagramUserData["error"])) {
            $this->redirect($postID, $instagramUserData["error"]["message"]);
        }
        if (empty($instagramUserData["email"]) && $userID) {
            $instagramUserData["email"] = $userID . "@instagram.com";
        }
        $uID = Utils::addUser($instagramUserData, "instagram");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    // https://console.developers.google.com/
    public function googleLogin($postID, $response) {
        if (!$this->generalOptions->social["googleClientID"] || !$this->generalOptions->social["googleClientSecret"]) {
            $response["message"] = esc_html__("Google Client ID and Client Secret  required.", "wpdiscuz");
            return $response;
        }

        $googleAuthorizeURL = "https://accounts.google.com/o/oauth2/v2/auth";
        $googleCallBack = $this->createCallBackURL("google");
        $state = Utils::generateOAuthState($this->generalOptions->social["googleClientID"]);
        Utils::addOAuthState("google", $state, $postID);
        $oautAttributs = [
            "client_id" => urlencode($this->generalOptions->social["googleClientID"]),
            "scope" => "openid email profile",
            "response_type" => "code",
            "state" => $state,
            "redirect_uri" => urlencode($googleCallBack)
        ];
        $oautURL = add_query_arg($oautAttributs, $googleAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function googleLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "google")) {
            $this->redirect($postID, esc_html__("Google authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Google authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $googleCallBack = $this->createCallBackURL("google");
        $googleAccessTokenURL = "https://www.googleapis.com/oauth2/v4/token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["googleClientID"],
            "client_secret" => $this->generalOptions->social["googleClientSecret"],
            "redirect_uri" => $googleCallBack,
            "code" => $code,
            "grant_type" => 'authorization_code'];
        $googleAccesTokenResponse = wp_remote_post($googleAccessTokenURL, ['body' => $accessTokenArgs]);
        if (is_wp_error($googleAccesTokenResponse)) {
            $this->redirect($postID, $googleAccesTokenResponse->get_error_message());
        }
        $googleAccesTokenData = json_decode(wp_remote_retrieve_body($googleAccesTokenResponse), true);
        if (isset($googleAccesTokenData["error"])) {
            $this->redirect($postID, $googleAccesTokenData["error_description"]);
        }
        $idToken = $googleAccesTokenData["id_token"];
        $getGoogleUserRataURL = add_query_arg(["id_token" => $idToken], 'https://oauth2.googleapis.com/tokeninfo');
        $googleUserDataResponse = wp_remote_get($getGoogleUserRataURL);
        if (is_wp_error($googleUserDataResponse)) {
            $this->redirect($postID, $googleUserDataResponse->get_error_message());
        }
        $googleUserData = json_decode(wp_remote_retrieve_body($googleUserDataResponse), true);
        if (isset($googleUserData["error"])) {
            $this->redirect($postID, $googleUserData["error_description"]);
        }
        $uID = Utils::addUser($googleUserData, "google");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    // https://docs.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow?context=linkedin/context
    public function linkedinLogin($postID, $response) {
        if (!$this->generalOptions->social["linkedinClientID"] || !$this->generalOptions->social["linkedinClientSecret"]) {
            $response["message"] = esc_html__("Linkedin Client ID and Client Secret  required.", "wpdiscuz");
            return $response;
        }
        $linkedinAuthorizeURL = "https://www.linkedin.com/oauth/v2/authorization";
        $linkedinCallBack = site_url('/wpdiscuz_auth/linkedin/');
        $state = Utils::generateOAuthState($this->generalOptions->social["linkedinClientID"]);
        Utils::addOAuthState("linkedin", $state, $postID);
        $oautAttributs = [
            "client_id" => $this->generalOptions->social["linkedinClientID"],
            "redirect_uri" => urlencode($linkedinCallBack),
            "response_type" => "code",
            "scope" => "r_liteprofile r_emailaddress",
            "state" => $state
        ];
        $oautURL = add_query_arg($oautAttributs, $linkedinAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function linkedinLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];

        if (!$state || ($provider != "linkedin")) {
            $this->redirect($postID, esc_html__("Linkedin authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Linkedin authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $linkedinCallBack = site_url('/wpdiscuz_auth/linkedin/');
        $linkedinAccessTokenURL = "https://www.linkedin.com/oauth/v2/accessToken";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["linkedinClientID"],
            "client_secret" => $this->generalOptions->social["linkedinClientSecret"],
            "grant_type" => "authorization_code",
            "redirect_uri" => $linkedinCallBack,
            "code" => $code];
        $linkedinAccesTokenResponse = wp_remote_post($linkedinAccessTokenURL, ['body' => $accessTokenArgs]);

        if (is_wp_error($linkedinAccesTokenResponse)) {
            $this->redirect($postID, $linkedinAccesTokenResponse->get_error_message());
        }
        $linkedinAccesTokenData = json_decode(wp_remote_retrieve_body($linkedinAccesTokenResponse), true);
        if (isset($linkedinAccesTokenData["error"])) {
            $this->redirect($postID, $linkedinAccesTokenData["error_description"]);
        }
        $token = $linkedinAccesTokenData["access_token"];

        $linkedinGetUserEmailURL = 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))';
        $linkedinGetUserAvatarURL = 'https://api.linkedin.com/v2/me?projection=(id,profilePicture(displayImage~:playableStreams))';
        $linkedinGetUserDataURL = 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,emailAddress,profilePicture(displayImage~:playableStreams))';
        $email = '';
        $avatar = '';
        $getLinkedinRequestArgs = [
            'timeout' => 120,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers' => 'Authorization:Bearer ' . $token
        ];

        $getLinkedinEmailResponse = wp_remote_get($linkedinGetUserEmailURL, $getLinkedinRequestArgs);
        $getLinkedinAvatarResponse = wp_remote_get($linkedinGetUserAvatarURL, $getLinkedinRequestArgs);

        if (!is_wp_error($getLinkedinEmailResponse)) {
            $linkedinUserEmailData = json_decode(wp_remote_retrieve_body($getLinkedinEmailResponse), true);
            if (!isset($linkedinUserEmailData["error"]) && isset($linkedinUserEmailData['elements']['0']['handle~']['emailAddress'])) {
                $email = $linkedinUserEmailData['elements']['0']['handle~']['emailAddress'];
            }
        }

        if (!is_wp_error($getLinkedinAvatarResponse)) {
            $linkedinUserAvatarData = json_decode(wp_remote_retrieve_body($getLinkedinAvatarResponse), true);
            if (!isset($linkedinUserAvatarData["error"]) && isset($linkedinUserAvatarData['profilePicture']['displayImage~']['elements']['0']['identifiers'][0]['identifier'])) {
                $avatar = $linkedinUserAvatarData['profilePicture']['displayImage~']['elements']['0']['identifiers'][0]['identifier'];
            }
        }



        $getLinkedinUserResponse = wp_remote_get($linkedinGetUserDataURL, $getLinkedinRequestArgs);
        if (is_wp_error($getLinkedinUserResponse)) {
            $this->redirect($postID, $getLinkedinUserResponse->get_error_message());
        }
        $linkedinUserData = json_decode(wp_remote_retrieve_body($getLinkedinUserResponse), true);

        if (isset($linkedinUserData["error"])) {
            $this->redirect($postID, $linkedinUserData["error_description"]);
        }
        if ($email) {
            $linkedinUserData["email"] = $email;
        }

        if ($avatar) {
            $linkedinUserData["avatar"] = $avatar;
        }

        $uID = Utils::addUser($linkedinUserData, "linkedin");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    public function disqusLogin($postID, $response) {
        if (!$this->generalOptions->social["disqusPublicKey"] || !$this->generalOptions->social["disqusSecretKey"]) {
            $response["message"] = esc_html__("Disqus Public Key and Secret Key  required.", "wpdiscuz");
            return $response;
        }
        $disqusAuthorizeURL = "https://disqus.com/api/oauth/2.0/authorize";
        $disqusCallBack = $this->createCallBackURL("disqus");
        $state = Utils::generateOAuthState($this->generalOptions->social["disqusPublicKey"]);
        Utils::addOAuthState("disqus", $state, $postID);
        $oautAttributs = [
            "client_id" => urlencode($this->generalOptions->social["disqusPublicKey"]),
            "scope" => "read,email",
            "response_type" => "code",
            "state" => $state,
            "redirect_uri" => urlencode($disqusCallBack)
        ];
        $oautURL = add_query_arg($oautAttributs, $disqusAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function disqusLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "disqus")) {
            $this->redirect($postID, esc_html__("Disqus authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Disqus authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $disqusCallBack = $this->createCallBackURL("disqus");
        $disqusAccessTokenURL = "https://disqus.com/api/oauth/2.0/access_token";
        $accessTokenArgs = [
            "grant_type" => "authorization_code",
            "client_id" => $this->generalOptions->social["disqusPublicKey"],
            "client_secret" => $this->generalOptions->social["disqusSecretKey"],
            "redirect_uri" => $disqusCallBack,
            "code" => $code
        ];
        $disqusAccesTokenResponse = wp_remote_post($disqusAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($disqusAccesTokenResponse)) {
            $this->redirect($postID, $disqusAccesTokenResponse->get_error_message());
        }
        $disqusAccesTokenData = json_decode(wp_remote_retrieve_body($disqusAccesTokenResponse), true);
        if (isset($disqusAccesTokenData["error"])) {
            $this->redirect($postID, $disqusAccesTokenData["error_description"]);
        }
        if (!isset($disqusAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Disqus authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        if (!isset($disqusAccesTokenData["user_id"])) {
            $this->redirect($postID, esc_html__("Disqus authentication failed (user_id does not exist).", "wpdiscuz"));
        }
        $userID = $disqusAccesTokenData["user_id"];
        $accesToken = $disqusAccesTokenData["access_token"];
        $disqusGetUserDataURL = "https://disqus.com/api/3.0/users/details.json";
        $disqusGetUserDataAttr = [
            "access_token" => $accesToken,
            "api_key" => $this->generalOptions->social["disqusPublicKey"],
        ];

        $getDisqusUserResponse = wp_remote_get($disqusGetUserDataURL, ["body" => $disqusGetUserDataAttr]);
        if (is_wp_error($getDisqusUserResponse)) {
            $this->redirect($postID, $getDisqusUserResponse->get_error_message());
        }
        $disqusUserData = json_decode(wp_remote_retrieve_body($getDisqusUserResponse), true);
        if (isset($disqusUserData["code"]) && $disqusUserData["code"] != 0) {
            $this->redirect($postID, $disqusUserData["response"]);
        }
        $disqusUser = $disqusUserData["response"];
        $disqusUser["user_id"] = $userID;
        $uID = Utils::addUser($disqusUser, "disqus");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://developer.wordpress.com/docs/oauth2/  https://developer.wordpress.com/docs/wpcc/
    public function wordpressLogin($postID, $response) {
        if (!$this->generalOptions->social["wordpressClientID"] || !$this->generalOptions->social["wordpressClientSecret"]) {
            $response["message"] = esc_html__("Wordpress Client ID and Client Secret required.", "wpdiscuz");
            return $response;
        }
        $wordpressAuthorizeURL = "https://public-api.wordpress.com/oauth2/authorize";
        $wordpressCallBack = $this->createCallBackURL("wordpress");
        $state = Utils::generateOAuthState($this->generalOptions->social["wordpressClientID"]);
        Utils::addOAuthState("wordpress", $state, $postID);
        $oautAttributs = [
            "client_id" => $this->generalOptions->social["wordpressClientID"],
            "scope" => "auth",
            "response_type" => "code",
            "state" => $state,
            "redirect_uri" => urlencode($wordpressCallBack)
        ];
        $oautURL = add_query_arg($oautAttributs, $wordpressAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function wordpressLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "wordpress")) {
            $this->redirect($postID, esc_html__("Wordpress.com authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Wordpress.com authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $wordpressCallBack = $this->createCallBackURL("wordpress");
        $wordpressAccessTokenURL = "https://public-api.wordpress.com/oauth2/token";
        $accessTokenArgs = [
            "grant_type" => "authorization_code",
            "client_id" => $this->generalOptions->social["wordpressClientID"],
            "client_secret" => $this->generalOptions->social["wordpressClientSecret"],
            "redirect_uri" => $wordpressCallBack,
            "code" => $code
        ];
        $wordpressAccesTokenResponse = wp_remote_post($wordpressAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($wordpressAccesTokenResponse)) {
            $this->redirect($postID, $wordpressAccesTokenResponse->get_error_message());
        }
        $wordpressAccesTokenData = json_decode(wp_remote_retrieve_body($wordpressAccesTokenResponse), true);
        if (isset($wordpressAccesTokenData["error"])) {
            $this->redirect($postID, $wordpressAccesTokenData["error_description"]);
        }
        if (!isset($wordpressAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Wordpress.com authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accesToken = $wordpressAccesTokenData["access_token"];
        $wordpressAccesTokenValidateURL = "https://public-api.wordpress.com/oauth2/token-info";
        $accesTokenValidateArgs = ["client_id" => $this->generalOptions->social["wordpressClientID"], "token" => urlencode($accesToken)];
        $wordpressAccesTokenValidateURL = add_query_arg($accesTokenValidateArgs, $wordpressAccesTokenValidateURL);
        $accesTokenValidateResponse = wp_remote_get($wordpressAccesTokenValidateURL, $accesTokenValidateArgs);
        if (is_wp_error($accesTokenValidateResponse)) {
            $this->redirect($postID, $accesTokenValidateResponse->get_error_message());
        }
        $accesTokenValidateData = json_decode(wp_remote_retrieve_body($accesTokenValidateResponse), true);
        if (!isset($accesTokenValidateData["user_id"]) || !$accesTokenValidateData["user_id"]) {
            $this->redirect($postID, esc_html__("Wordpress.com authentication failed (user_id does not exist).", "wpdiscuz"));
        }

        $wordpressGetUserDataURL = "https://public-api.wordpress.com/rest/v1/me/";
        $wordpressGetUserDataAttr = ["Authorization" => "Bearer " . $accesToken];

        $getWordpressUserResponse = wp_remote_get($wordpressGetUserDataURL, ["headers" => $wordpressGetUserDataAttr]);

        if (is_wp_error($getWordpressUserResponse)) {
            $this->redirect($postID, $getWordpressUserResponse->get_error_message());
        }
        $wordpressUserData = json_decode(wp_remote_retrieve_body($getWordpressUserResponse), true);
        if (isset($wordpressUserData["error"])) {
            $this->redirect($postID, $wordpressUserData["message"]);
        }
        $uID = Utils::addUser($wordpressUserData, "wordpress");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    // https://apps.twitter.com/
    public function twitterLogin($postID, $response) {
        if ($this->generalOptions->social["twitterAppID"] && $this->generalOptions->social["twitterAppSecret"]) {
            $twitter = new TwitterOAuth($this->generalOptions->social["twitterAppID"], $this->generalOptions->social["twitterAppSecret"]);
            $twitterCallBack = $this->createCallBackURL("twitter");
            try {
                $requestToken = $twitter->oauth("oauth/request_token", ["oauth_callback" => $twitterCallBack]);
                Utils::addOAuthState($requestToken["oauth_token_secret"], $requestToken["oauth_token"], $postID);
                $url = $twitter->url("oauth/authorize", ["oauth_token" => $requestToken["oauth_token"]]);
                $response["code"] = 200;
                $response["message"] = "";
                $response["url"] = $url;
            } catch (TwitterOAuthException $e) {
                $response["message"] = $e->getOAuthMessage();
            }
        } else {
            $response["message"] = esc_html__("Twitter Consumer Key and Consumer Secret  required.", "wpdiscuz");
        }
        return $response;
    }

    public function twitterLoginCallBack() {
        $oauthToken = filter_input(INPUT_GET, "oauth_token", FILTER_SANITIZE_STRING);
        $oauthVerifier = filter_input(INPUT_GET, "oauth_verifier", FILTER_SANITIZE_STRING);
        $oauthSecretData = Utils::getProviderByState($oauthToken);
        $oauthSecret = $oauthSecretData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $oauthSecretData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$oauthVerifier || !$oauthSecret) {
            $this->redirect($postID, esc_html__("Twitter authentication failed (OAuth secret does not exist).", "wpdiscuz"));
        }
        $twitter = new TwitterOAuth($this->generalOptions->social["twitterAppID"], $this->generalOptions->social["twitterAppSecret"], $oauthToken, $oauthSecret);
        try {
            $accessToken = $twitter->oauth("oauth/access_token", ["oauth_verifier" => $oauthVerifier]);
            $connection = new TwitterOAuth($this->generalOptions->social["twitterAppID"], $this->generalOptions->social["twitterAppSecret"], $accessToken["oauth_token"], $accessToken["oauth_token_secret"]);
            $twitterUser = $connection->get("account/verify_credentials", ["include_email" => "true"]);
            if (!empty($twitterUser->id)) {
                $uID = Utils::addUser($twitterUser, "twitter");
                if (is_wp_error($uID)) {
                    $this->redirect($postID, $uID->get_error_message());
                }
                $this->setCurrentUser($uID);
                $this->redirect($postID);
            } else {
                $this->redirect($postID, esc_html__("Twitter connection failed.", "wpdiscuz"));
            }
        } catch (TwitterOAuthException $e) {
            $this->redirect($postID, $e->getOAuthMessage());
        }
    }

    // https://vk.com/editapp?act=create
    public function vkLogin($postID, $response) {
        if (!$this->generalOptions->social["vkAppID"] || !$this->generalOptions->social["vkAppSecret"]) {
            $response["message"] = esc_html__("VK Client ID and Client Secret  required.", "wpdiscuz");
            return $response;
        }
        $vkAuthorizeURL = "https://oauth.vk.com/authorize";
        $vkCallBack = $this->createCallBackURL("vk");
        $state = Utils::generateOAuthState($this->generalOptions->social["vkAppID"]);
        Utils::addOAuthState("vk", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["vkAppID"],
            "client_secret" => $this->generalOptions->social["vkAppSecret"],
            "redirect_uri" => urlencode($vkCallBack),
            "response_type" => "code",
            "scope" => "email",
            "state" => $state,
            "v" => "5.78"];
        $oautURL = add_query_arg($oautAttributs, $vkAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function vkLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "vk")) {
            $this->redirect($postID, esc_html__("VK authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("VK authentication failed (OAuth code does not exist).", "wpdiscuz"));
        }
        $vkCallBack = $this->createCallBackURL("vk");
        $vkAccessTokenURL = "https://oauth.vk.com/access_token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["vkAppID"],
            "client_secret" => $this->generalOptions->social["vkAppSecret"],
            "redirect_uri" => $vkCallBack,
            "code" => $code];
        $vkAccesTokenResponse = wp_remote_post($vkAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($vkAccesTokenResponse)) {
            $this->redirect($postID, $vkAccesTokenResponse->get_error_message());
        }
        $vkAccesTokenData = json_decode(wp_remote_retrieve_body($vkAccesTokenResponse), true);
        if (isset($vkAccesTokenData["error"])) {
            $this->redirect($postID, $vkAccesTokenData["error_description"]);
        }
        if (!isset($vkAccesTokenData["user_id"])) {
            $this->redirect($postID, esc_html__("VK authentication failed (user_id does not exist).", "wpdiscuz"));
        }
        $userID = $vkAccesTokenData["user_id"];
        $email = isset($vkAccesTokenData["email"]) ? $vkAccesTokenData["email"] : $userID . "@vk.com";
        $vkGetUserDataURL = "https://api.vk.com/method/users.get";
        $vkGetUserDataAttr = ["user_ids" => $userID,
            "access_token" => $vkAccesTokenData["access_token"],
            "fields" => "first_name,last_name,screen_name,photo_100",
            "v" => "5.78"];
        $getVkUserResponse = wp_remote_post($vkGetUserDataURL, ["body" => $vkGetUserDataAttr]);
        if (is_wp_error($getVkUserResponse)) {
            $this->redirect($postID, $getVkUserResponse->get_error_message());
        }
        $vkUserData = json_decode(wp_remote_retrieve_body($getVkUserResponse), true);
        if (isset($vkUserData["error"])) {
            $this->redirect($postID, $vkUserData["error_msg"]);
        }
        $vkUser = $vkUserData["response"][0];
        $vkUser["email"] = $email;
        $uID = Utils::addUser($vkUser, "vk");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://apiok.ru/dev/app/create
    public function okLogin($postID, $response) {
        if (!$this->generalOptions->social["okAppID"] || !$this->generalOptions->social["okAppSecret"] || !$this->generalOptions->social["okAppKey"]) {
            $response["message"] = esc_html__("OK Application ID, Application Key  and Application Secret  required.", "wpdiscuz");
            return $response;
        }
        $okAuthorizeURL = "https://connect.ok.ru/oauth/authorize";
        $okCallBack = $this->createCallBackURL("ok");
        $state = Utils::generateOAuthState($this->generalOptions->social["okAppID"]);
        Utils::addOAuthState("ok", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["okAppID"],
            "redirect_uri" => urlencode($okCallBack),
            "response_type" => "code",
            "scope" => "VALUABLE_ACCESS;GET_EMAIL",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $okAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function okLoginCallBack() {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if (!$state || ($provider != "ok")) {
            $this->redirect($postID, esc_html__("OK authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("OK authentication failed (code does not exist).", "wpdiscuz"));
        }
        $okCallBack = $this->createCallBackURL("ok");
        $okAccessTokenURL = "https://api.ok.ru/oauth/token.do";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["okAppID"],
            "client_secret" => $this->generalOptions->social["okAppSecret"],
            "redirect_uri" => $okCallBack,
            "grant_type" => "authorization_code",
            "code" => $code];
        $okAccesTokenResponse = wp_remote_post($okAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($okAccesTokenResponse)) {
            $this->redirect($postID, $okAccesTokenResponse->get_error_message());
        }
        $okAccesTokenData = json_decode(wp_remote_retrieve_body($okAccesTokenResponse), true);
        if (isset($okAccesTokenData["error_code"])) {
            $this->redirect($postID, $okAccesTokenData["error_msg"]);
        }
        if (!isset($okAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("OK authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $okAccesTokenData["access_token"];
        $secretKey = md5($accessToken . $this->generalOptions->social["okAppSecret"]);
        $sig = md5("application_key={$this->generalOptions->social["okAppKey"]}format=jsonmethod=users.getCurrentUser$secretKey");
        $okGetUserDataURL = "https://api.ok.ru/fb.do";
        $okGetUserDataAttr = ["application_key" => $this->generalOptions->social["okAppKey"],
            "format" => "json",
            "method" => "users.getCurrentUser",
            "sig" => $sig,
            "access_token" => $accessToken];
        $getOkUserResponse = wp_remote_post($okGetUserDataURL, ["body" => $okGetUserDataAttr]);
        if (is_wp_error($getOkUserResponse)) {
            $this->redirect($postID, $getOkUserResponse->get_error_message());
        }
        $okUserData = json_decode(wp_remote_retrieve_body($getOkUserResponse), true);
        if (isset($okUserData["error_code"])) {
            $this->redirect($postID, $okUserData["error_msg"]);
        }
        $uID = Utils::addUser($okUserData, "ok");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://yandex.ru/dev/oauth/doc/dg/reference/auto-code-client-docpage/#auto-code-client
    public function yandexLogin($postID, $response) {
        if (!$this->generalOptions->social["yandexID"] || !$this->generalOptions->social["yandexPassword"]) {
            $response["message"] = esc_html__("Yandex ID and Password  required.", "wpdiscuz");
            return $response;
        }
        $yandexAuthorizeURL = "https://oauth.yandex.ru/authorize";
        $yandexCallBack = $this->createCallBackURL("yandex");
        $state = Utils::generateOAuthState($this->generalOptions->social["yandexID"]);
        Utils::addOAuthState("yandex", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["yandexID"],
            "redirect_uri" => urlencode($yandexCallBack),
            "response_type" => "code",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $yandexAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function yandexLoginCallBack() {
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "error_description", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "yandex")) {
            $this->redirect($postID, esc_html__("Yandex authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Yandex authentication failed (code does not exist).", "wpdiscuz"));
        }
        $yandexCallBack = $this->createCallBackURL("yandex");
        $yandexAccessTokenURL = "https://oauth.yandex.ru/token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["yandexID"],
            "client_secret" => $this->generalOptions->social["yandexPassword"],
            "redirect_uri" => $yandexCallBack,
            "grant_type" => "authorization_code",
            "code" => $code];
        $yandexAccesTokenResponse = wp_remote_post($yandexAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($yandexAccesTokenResponse)) {
            $this->redirect($postID, $yandexAccesTokenResponse->get_error_message());
        }
        $yandexAccesTokenData = json_decode(wp_remote_retrieve_body($yandexAccesTokenResponse), true);

        if (isset($yandexAccesTokenData["error"])) {
            $this->redirect($postID, $yandexAccesTokenData["error_description"]);
        }
        if (!isset($yandexAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Yandex authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $yandexAccesTokenData["access_token"];
        $yandexGetUserDataURL = "https://login.yandex.ru/info?format=json";

        $yandexGetUserDataAttr = [
            'timeout' => 120,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers' => 'Authorization: OAuth ' . $accessToken
        ];

        $getYandexUserResponse = wp_remote_post($yandexGetUserDataURL, $yandexGetUserDataAttr);

        if (is_wp_error($getYandexUserResponse)) {
            $this->redirect($postID, $getYandexUserResponse->get_error_message());
        }
        $yandexUserData = json_decode(wp_remote_retrieve_body($getYandexUserResponse), true);
        if (isset($yandexUserData["error"])) {
            $this->redirect($postID, $yandexUserData["error_description"]);
        }

        $uID = Utils::addUser($yandexUserData, "yandex");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://o2.mail.ru/docs/
    public function mailruLogin($postID, $response) {
        if (!$this->generalOptions->social["mailruClientID"] || !$this->generalOptions->social["mailruClientSecret"]) {
            $response["message"] = esc_html__("Mail.ru  Client ID  and Client Secret  required.", "wpdiscuz");
            return $response;
        }
        $mailruAuthorizeURL = "https://oauth.mail.ru/login";
        $mailruCallBack = $this->createCallBackURL("mailru");
        $state = Utils::generateOAuthState($this->generalOptions->social["mailruClientID"]);
        Utils::addOAuthState("mailru", $state, $postID);
        $oautAttributs = [
            "client_id" => $this->generalOptions->social["mailruClientID"],
            "response_type" => "code",
            "scope" => "userinfo",
            "redirect_uri" => urlencode($mailruCallBack),
            "state" => $state
        ];
        $oautURL = add_query_arg($oautAttributs, $mailruAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function mailruLoginCallBack() {
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "error_description", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];

        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "mailru")) {
            $this->redirect($postID, esc_html__("Mail.ru authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Mail.ru authentication failed (code does not exist).", "wpdiscuz"));
        }
        $mailruCallBack = $this->createCallBackURL("mailru");
        $mailruAccessTokenURL = "https://oauth.mail.ru/token";
        $accessTokenArgs = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => urlencode($mailruCallBack)
        ];

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $curl = curl_init();
        $header[] = 'Host: oauth.mail.ru';
        $header[] = 'Authorization: Basic ' . base64_encode($this->generalOptions->social["mailruClientID"] . ':' . $this->generalOptions->social["mailruClientSecret"]);
        $header[] = 'Content-Type: application/x-www-form-urlencoded';

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_URL, $mailruAccessTokenURL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($accessTokenArgs)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $mailruAccesTokenResponse = curl_exec($curl);
        curl_close($curl);

        $mailruAccesTokenData = json_decode($mailruAccesTokenResponse, true);

        if (isset($mailruAccesTokenData["error"])) {
            $this->redirect($postID, $mailruAccesTokenData["error_description"]);
        }
        if (!isset($mailruAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Mail.ru authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $mailruAccesTokenData["access_token"];

        $mailruGetUserDataURL = 'https://oauth.mail.ru/userinfo' . '?access_token=' . $accessToken;
        $mailruUserData = json_decode(file_get_contents($mailruGetUserDataURL), true);

        if (isset($mailruUserData["error"])) {
            $this->redirect($postID, $mailruUserData["error_description"]);
        }

        $uID = Utils::addUser($mailruUserData, "mailru");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://developers.weixin.qq.com/doc/oplatform/en/Website_App/WeChat_Login/Wechat_Login.html
    public function wechatLogin($postID, $response) {
        if (!$this->generalOptions->social["wechatAppID"] || !$this->generalOptions->social["wechatSecret"]) {
            $response["message"] = esc_html__("WeChat AppKey and AppSecret  required.", "wpdiscuz");
            return $response;
        }

        $wechatAuthorizeURL = "https://open.weixin.qq.com/connect/qrconnect";
        $wechatCallBack = $this->createCallBackURL("wechat");
        $state = Utils::generateOAuthState($this->generalOptions->social["wechatAppID"]);
        Utils::addOAuthState("wechat", $state, $postID);
        $oautAttributs = ["appid" => $this->generalOptions->social["wechatAppID"],
            "redirect_uri" => urlencode($wechatCallBack),
            "response_type" => "code",
            "scope" => "snsapi_login",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $wechatAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL . "#wechat_redirect";
        return $response;
    }

    public function wechatLoginCallBack() {
        $error = filter_input(INPUT_GET, "errcode", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "errmsg", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "wechat")) {
            $this->redirect($postID, esc_html__("WeChat authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("WeChat authentication failed (code does not exist).", "wpdiscuz"));
        }
        $wechatAccessTokenURL = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $accessTokenArgs = ["appid" => $this->generalOptions->social["wechatAppID"],
            "secret" => $this->generalOptions->social["wechatSecret"],
            "grant_type" => "authorization_code",
            "code" => $code];
        $wechatAccesTokenResponse = wp_remote_post($wechatAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($wechatAccesTokenResponse)) {
            $this->redirect($postID, $wechatAccesTokenResponse->get_error_message());
        }
        $wechatAccesTokenData = json_decode(wp_remote_retrieve_body($wechatAccesTokenResponse), true);

        if (isset($wechatAccesTokenData["errcode"])) {
            $this->redirect($postID, $wechatAccesTokenData["errmsg"]);
        }
        if (!isset($wechatAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("WeChat authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $wechatAccesTokenData["access_token"];
        $uid = $wechatAccesTokenData["openid"];

        $wechatGetUserDataAttributs = ["appid" => $this->generalOptions->social["wechatAppID"],
            "access_token" => $accessToken,
            "openid" => $uid
        ];

        $wechatGetUserDataURL = add_query_arg($wechatGetUserDataAttributs, "https://api.weixin.qq.com/sns/userinfo");

        $getWechatUserResponse = wp_remote_get($wechatGetUserDataURL);

        if (is_wp_error($getWechatUserResponse)) {
            $this->redirect($postID, $getWechatUserResponse->get_error_message());
        }
        $wechatUserData = json_decode(wp_remote_retrieve_body($getWechatUserResponse), true);
        if (isset($wechatUserData["errcode"])) {
            $this->redirect($postID, $wechatUserData["errmsg"]);
        }
        $uID = Utils::addUser($wechatUserData, "wechat");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://wiki.connect.qq.com/%E5%BC%80%E5%8F%91%E6%94%BB%E7%95%A5_server-side
    public function qqLogin($postID, $response) {
        if (!$this->generalOptions->social["qqAppID"] || !$this->generalOptions->social["qqSecret"]) {
            $response["message"] = esc_html__("QQ AppKey and AppSecret  required.", "wpdiscuz");
            return $response;
        }

        $qqAuthorizeURL = "https://graph.qq.com/oauth2.0/authorize";
        $qqCallBack = $this->createCallBackURL("qq");
        $state = Utils::generateOAuthState($this->generalOptions->social["qqAppID"]);
        Utils::addOAuthState("qq", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["qqAppID"],
            "redirect_uri" => urlencode($qqCallBack),
            "response_type" => "code",
            "scope" => "get_user_info",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $qqAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function qqLoginCallBack() {
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "error_description", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "qq")) {
            $this->redirect($postID, esc_html__("QQ authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("QQ authentication failed (code does not exist).", "wpdiscuz"));
        }
        $qqCallBack = $this->createCallBackURL("qq");
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["qqAppID"],
            "client_secret" => $this->generalOptions->social["qqSecret"],
            "redirect_uri" => urlencode($qqCallBack),
            "grant_type" => "authorization_code",
            "code" => $code];
        $qqAccessTokenURL = add_query_arg($accessTokenArgs, "https://graph.qq.com/oauth2.0/token");
        $qqAccesTokenResponse = wp_remote_get($qqAccessTokenURL);
        if (is_wp_error($qqAccesTokenResponse)) {
            $this->redirect($postID, $qqAccesTokenResponse->get_error_message());
        }
        $qqAccesTokenResponseBody = wp_remote_retrieve_body($qqAccesTokenResponse);
        if (strpos($qqAccesTokenResponseBody, "callback") !== false) {
            $lpos = strpos($qqAccesTokenResponseBody, "(");
            $rpos = strrpos($qqAccesTokenResponseBody, ")");
            $qqAccesTokenResponseBody = substr($qqAccesTokenResponseBody, $lpos + 1, $rpos - $lpos - 1);
            $qqAccesTokenResponseMsg = json_decode($qqAccesTokenResponseBody, true);
            if (isset($qqAccesTokenResponseMsg["error"])) {
                $this->redirect($postID, $qqAccesTokenResponseMsg["error_description"]);
            }
            $qqAccesTokenData = array();
            parse_str($qqAccesTokenResponseBody, $qqAccesTokenData);
            if (!isset($qqAccesTokenData["access_token"])) {
                $this->redirect($postID, esc_html__("QQ authentication failed (access_token does not exist).", "wpdiscuz"));
            }
            $accessToken = $qqAccesTokenData["access_token"];
            $qqOpenIdResponse = wp_remote_get("https://graph.qq.com/oauth2.0/me?access_token=" . $accessToken);
            if (is_wp_error($qqOpenIdResponse)) {
                $this->redirect($postID, $qqOpenIdResponse->get_error_message());
            }
            $qqOpenIdResponseBody = wp_remote_retrieve_body($qqAccesTokenResponse);
            if (strpos($qqOpenIdResponseBody, "callback") !== false) {
                $lpos = strpos($qqOpenIdResponseBody, "(");
                $rpos = strrpos($qqOpenIdResponseBody, ")");
                $qqOpenIdResponseBody = substr($qqOpenIdResponseBody, $lpos + 1, $rpos - $lpos - 1);
            }
            $qqOpenIdResponseMsg = json_decode($qqOpenIdResponseBody, true);
            if (isset($qqOpenIdResponseMsg["error"])) {
                $this->redirect($postID, $qqOpenIdResponseMsg["error_description"]);
            }
            $openid = $qqOpenIdResponseMsg["openid"];
            $qqGetUserDataAttributs = ["oauth_consumer_key" => $this->generalOptions->social["qqAppID"],
                "access_token" => $accessToken,
                "openid" => $openid
            ];
            $qqGetUserDataURL = add_query_arg($qqGetUserDataAttributs, "https://graph.qq.com/user/get_user_info");
            $getQQUserResponse = wp_remote_get($qqGetUserDataURL);
            if (is_wp_error($getQQUserResponse)) {
                $this->redirect($postID, $getQQUserResponse->get_error_message());
            }
            $qqUserData = json_decode(wp_remote_retrieve_body($getQQUserResponse), true);
            if (isset($qqUserData["error"])) {
                $this->redirect($postID, $qqUserData["error_description"]);
            }
            $qqUserData["openid"] = $openid;
            $uID = Utils::addUser($qqUserData, "qq");
            if (is_wp_error($uID)) {
                $this->redirect($postID, $uID->get_error_message());
            }
            $this->setCurrentUser($uID);
            $this->redirect($postID);
        } else {
            $this->redirect($postID, esc_html__("QQ authentication failed (access_token does not exist).", "wpdiscuz"));
        }
    }

    //https://gwu-libraries.github.io/sfm-ui/posts/2016-04-26-weibo-api-guide
    //https://open.weibo.com/wiki/Connect/login
    public function weiboLogin($postID, $response) {
        if (!$this->generalOptions->social["weiboKey"] || !$this->generalOptions->social["weiboSecret"]) {
            $response["message"] = esc_html__("Weibo App Key and App Secret  required.", "wpdiscuz");
            return $response;
        }

        $weiboAuthorizeURL = "https://api.weibo.com/oauth2/authorize";
        $weiboCallBack = $this->createCallBackURL("weibo");
        $state = Utils::generateOAuthState($this->generalOptions->social["weiboKey"]);
        Utils::addOAuthState("weibo", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["weiboKey"],
            "redirect_uri" => urlencode($weiboCallBack),
            "response_type" => "code",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $weiboAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function weiboLoginCallBack() {
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "error_description", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "weibo")) {
            $this->redirect($postID, esc_html__("Weibo authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Weibo authentication failed (code does not exist).", "wpdiscuz"));
        }
        $weiboCallBack = $this->createCallBackURL("weibo");
        $weiboAccessTokenURL = "https://api.weibo.com/oauth2/access_token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["weiboKey"],
            "client_secret" => $this->generalOptions->social["weiboSecret"],
            "redirect_uri" => $weiboCallBack,
            "grant_type" => "authorization_code",
            "code" => $code];
        $weiboAccesTokenResponse = wp_remote_post($weiboAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($weiboAccesTokenResponse)) {
            $this->redirect($postID, $weiboAccesTokenResponse->get_error_message());
        }
        $weiboAccesTokenData = json_decode(wp_remote_retrieve_body($weiboAccesTokenResponse), true);

        if (isset($weiboAccesTokenData["error"])) {
            $this->redirect($postID, $weiboAccesTokenData["error_description"]);
        }
        if (!isset($weiboAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Weibo authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $weiboAccesTokenData["access_token"];
        $uid = $weiboAccesTokenData["uid"];

        $weiboGetUserDataURL = "https://api.weibo.com/2/users/show.json?uid=" . $uid;

        $weiboGetUserDataAttr = [
            'httpversion' => '1.1',
            'headers' => 'Authorization:OAuth2 ' . $accessToken
        ];

        $getWeiboUserResponse = wp_remote_get($weiboGetUserDataURL, $weiboGetUserDataAttr);
        if (is_wp_error($getWeiboUserResponse)) {
            $this->redirect($postID, $getWeiboUserResponse->get_error_message());
        }
        $weiboUserData = json_decode(wp_remote_retrieve_body($getWeiboUserResponse), true);
        if (isset($weiboUserData["error"])) {
            $this->redirect($postID, $weiboUserData["error_description"]);
        }
        $uID = Utils::addUser($weiboUserData, "weibo");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    //https://developer.baidu.com/wiki/index.php?title=docs/oauth/application
    //https://developer.baidu.com/wiki/index.php?title=docs/oauth/showcase
    public function baiduLogin($postID, $response) {
        if (!$this->generalOptions->social["baiduAppID"] || !$this->generalOptions->social["baiduSecret"]) {
            $response["message"] = esc_html__("Baidu Client ID and Client Secret  required.", "wpdiscuz");
            return $response;
        }

        $baiduAuthorizeURL = "https://openapi.baidu.com/oauth/2.0/authorize";
        $baiduCallBack = $this->createCallBackURL("baidu");
        $state = Utils::generateOAuthState($this->generalOptions->social["baiduAppID"]);
        Utils::addOAuthState("baidu", $state, $postID);
        $oautAttributs = ["client_id" => $this->generalOptions->social["baiduAppID"],
            "redirect_uri" => urlencode($baiduCallBack),
            "response_type" => "code",
            "scope" => "basic",
            //'page', 'popup', 'touch' or 'mobile'
            "display" => wp_is_mobile() ? "mobile" : "page",
            "state" => $state];
        $oautURL = add_query_arg($oautAttributs, $baiduAuthorizeURL);
        $response["code"] = 200;
        $response["message"] = "";
        $response["url"] = $oautURL;
        return $response;
    }

    public function baiduLoginCallBack() {
        $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);
        $errorDesc = filter_input(INPUT_GET, "error_description", FILTER_SANITIZE_STRING);
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_STRING);
        $providerData = Utils::getProviderByState($state);
        $provider = $providerData[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER];
        $postID = $providerData[wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID];
        if ($error) {
            $this->redirect($postID, esc_html($errorDesc));
        }
        if (!$state || ($provider != "baidu")) {
            $this->redirect($postID, esc_html__("Baidu authentication failed (OAuth state does not exist).", "wpdiscuz"));
        }
        if (!$code) {
            $this->redirect($postID, esc_html__("Baidu authentication failed (code does not exist).", "wpdiscuz"));
        }
        $baiduCallBack = $this->createCallBackURL("baidu");
        $baiduAccessTokenURL = "https://openapi.baidu.com/oauth/2.0/token";
        $accessTokenArgs = ["client_id" => $this->generalOptions->social["baiduAppID"],
            "client_secret" => $this->generalOptions->social["baiduSecret"],
            "redirect_uri" => $baiduCallBack,
            "grant_type" => "authorization_code",
            "code" => $code];
        $baiduAccesTokenResponse = wp_remote_post($baiduAccessTokenURL, ["body" => $accessTokenArgs]);

        if (is_wp_error($baiduAccesTokenResponse)) {
            $this->redirect($postID, $baiduAccesTokenResponse->get_error_message());
        }
        $baiduAccesTokenData = json_decode(wp_remote_retrieve_body($baiduAccesTokenResponse), true);

        if (isset($baiduAccesTokenData["error"])) {
            $this->redirect($postID, $baiduAccesTokenData["error_description"]);
        }
        if (!isset($baiduAccesTokenData["access_token"])) {
            $this->redirect($postID, esc_html__("Baidu authentication failed (access_token does not exist).", "wpdiscuz"));
        }
        $accessToken = $baiduAccesTokenData["access_token"];

        $getBaiduUserResponse = wp_remote_get("https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser?access_token=" . $accessToken);
        if (is_wp_error($getBaiduUserResponse)) {
            $this->redirect($postID, $getBaiduUserResponse->get_error_message());
        }
        $baiduUserData = json_decode(wp_remote_retrieve_body($getBaiduUserResponse), true);
        if (isset($baiduUserData["error_code"])) {
            $this->redirect($postID, $baiduUserData["error_msg"]);
        }
        $uID = Utils::addUser($baiduUserData, "baidu");
        if (is_wp_error($uID)) {
            $this->redirect($postID, $uID->get_error_message());
        }
        $this->setCurrentUser($uID);
        $this->redirect($postID);
    }

    private function redirect($postID, $message = "") {
        if ($message) {
            setcookie('wpdiscuz_social_login_message', $message, time() + 3600, '/');
        }
        do_action("wpdiscuz_clean_post_cache", $postID, "social_login");
        wp_redirect($this->getPostLink($postID), 302);
        exit();
    }

    private function createCallBackURL($provider) {
        $adminAjaxURL = admin_url("admin-ajax.php");
        $urlAttributs = ["action" => "wpd_login_callback", "provider" => $provider];
        return add_query_arg($urlAttributs, $adminAjaxURL);
    }

    private function deleteCookie() {
        unset($_COOKIE["wpdiscuz_social_login_message"]);
        setcookie("wpdiscuz_social_login_message", "", time() - ( 15 * 60 ));
    }

    private function setCurrentUser($userID) {
        $user = get_user_by("id", $userID);
        wp_set_current_user($userID, $user->user_login);
        wp_set_auth_cookie($userID);
        do_action("wp_login", $user->user_login, $user);
    }

    public function getButtons() {
        global $post;
        if (!is_user_logged_in() && wpDiscuz()->helper->isLoadWpdiscuz($post) && $this->generalOptions->isShowLoginButtons()) {
            echo "<div class='wpd-social-login'>";
            echo "<span class='wpd-connect-with'>" . esc_html($this->generalOptions->phrases["wc_connect_with"]) . "</span>";
            $this->facebookButton();
            $this->instagramButton();
            $this->twitterButton();
            $this->googleButton();
            $this->disqusButton();
            $this->wordpressButton();
            $this->linkedinButton();
            $this->yandexButton();
            $this->vkButton();
            $this->okButton();
            $this->mailruButton();
            $this->wechatButton();
            $this->weiboButton();
            $this->qqButton();
            $this->baiduButton();
            echo "<div class='wpdiscuz-social-login-spinner'><i class='fas fa-spinner fa-pulse'></i></div><div class='wpd-clear'></div>";
            echo "</div>";
        }
    }

    public function getReplyFormButtons() {
        if ($this->generalOptions->social["socialLoginInSecondaryForm"]) {
            $this->getButtons();
        }
    }

    public function getAgreement() {
        global $post;
        if (!is_user_logged_in() && wpDiscuz()->helper->isLoadWpdiscuz($post) && $this->generalOptions->isShowLoginButtons() && $this->generalOptions->social["socialLoginAgreementCheckbox"]) {
            ?>
            <div class="wpd-social-login-agreement" style="display: none;">
                <div class="wpd-agreement-title"><?php echo $this->generalOptions->phrases["wc_social_login_agreement_label"]; ?></div>
                <div class="wpd-agreement"><?php echo $this->generalOptions->phrases["wc_social_login_agreement_desc"]; ?></div>
                <div class="wpd-agreement-buttons">
                    <div class="wpd-agreement-buttons-right"><span class="wpd-agreement-button wpd-agreement-button-disagree"><?php echo $this->generalOptions->phrases["wc_agreement_button_disagree"]; ?></span><span class="wpd-agreement-button wpd-agreement-button-agree"><?php echo $this->generalOptions->phrases["wc_agreement_button_agree"]; ?></span></div>
                    <div class="wpd-clear"></div>
                </div>
            </div>
            <?php
        }
    }

    private function facebookButton() {
        if ($this->generalOptions->social["enableFbLogin"] && $this->generalOptions->social["fbAppID"] && $this->generalOptions->social["fbAppSecret"]) {
            echo "<span class='wpdsn wpdsn-fb wpdiscuz-login-button' wpd-tooltip='Facebook'><i class='fab fa-facebook'></i></span>";
        }
    }

    private function instagramButton() {
        if ($this->generalOptions->social["enableInstagramLogin"] && $this->generalOptions->social["instagramAppID"] && $this->generalOptions->social["instagramAppSecret"]) {
            echo "<span class='wpdsn wpdsn-insta wpdiscuz-login-button' wpd-tooltip='Instagram'><i class='fab fa-instagram'></i></span>";
        }
    }

    private function linkedinButton() {
        if ($this->generalOptions->social["enableLinkedinLogin"] && $this->generalOptions->social["linkedinClientID"] && $this->generalOptions->social["linkedinClientSecret"]) {
            echo "<span class='wpdsn wpdsn-linked wpdiscuz-login-button' wpd-tooltip='Linkedin'><i class='fab fa-linkedin-in'></i></span>";
        }
    }

    private function twitterButton() {
        if ($this->generalOptions->social["enableTwitterLogin"] && $this->generalOptions->social["twitterAppID"] && $this->generalOptions->social["twitterAppSecret"]) {
            echo "<span class='wpdsn wpdsn-tw wpdiscuz-login-button' wpd-tooltip='Twitter'><i class='fab fa-twitter'></i></span>";
        }
    }

    private function googleButton() {
        if ($this->generalOptions->social["enableGoogleLogin"] && $this->generalOptions->social["googleClientID"] && $this->generalOptions->social["googleClientSecret"]) {
            echo "<span class='wpdsn wpdsn-gg wpdiscuz-login-button' wpd-tooltip='Google'><i class='fab fa-google'></i></span>";
        }
    }

    private function disqusButton() {
        if ($this->generalOptions->social["enableDisqusLogin"] && $this->generalOptions->social["disqusPublicKey"] && $this->generalOptions->social["disqusSecretKey"]) {
            echo "<span class='wpdsn wpdsn-ds wpdiscuz-login-button' wpd-tooltip='Disqus'><i class='wpd-disqus'>D</i></span>";
        }
    }

    private function wordpressButton() {
        if ($this->generalOptions->social["enableWordpressLogin"] && $this->generalOptions->social["wordpressClientID"] && $this->generalOptions->social["wordpressClientSecret"]) {
            echo "<span class='wpdsn wpdsn-wp wpdiscuz-login-button' wpd-tooltip='WordPress'><i class='fab fa-wordpress-simple'></i></span>";
        }
    }

    private function okButton() {
        if ($this->generalOptions->social["enableOkLogin"] && $this->generalOptions->social["okAppID"] && $this->generalOptions->social["okAppSecret"]) {
            echo "<span class='wpdsn wpdsn-ok wpdiscuz-login-button' wpd-tooltip='Odnoklassniki'><i class='fab fa-odnoklassniki'></i></span>";
        }
    }

    private function vkButton() {
        if ($this->generalOptions->social["enableVkLogin"] && $this->generalOptions->social["vkAppID"] && $this->generalOptions->social["vkAppSecret"]) {
            echo "<span class='wpdsn wpdsn-vk wpdiscuz-login-button' wpd-tooltip='VKontakte'><i class='fab fa-vk'></i></span>";
        }
    }

    private function yandexButton() {
        if ($this->generalOptions->social["enableYandexLogin"] && $this->generalOptions->social["yandexID"] && $this->generalOptions->social["yandexPassword"]) {
            echo "<span class='wpdsn wpdsn-yandex wpdiscuz-login-button' wpd-tooltip='Yandex'><i class='fab fa-yandex-international'></i></span>";
        }
    }

    private function mailruButton() {
        if ($this->generalOptions->social["enableMailruLogin"] && $this->generalOptions->social["mailruClientID"] && $this->generalOptions->social["mailruClientSecret"]) {
            echo "<span class='wpdsn wpdsn-mailru wpdiscuz-login-button' wpd-tooltip='Mail.ru'><i class='fas fa-at'></i></span>";
        }
    }

    private function wechatButton() {
        if ($this->generalOptions->social["enableWechatLogin"] && $this->generalOptions->social["wechatAppID"] && $this->generalOptions->social["wechatSecret"]) {
            echo "<span class='wpdsn wpdsn-weixin wpdiscuz-login-button' wpd-tooltip='WeChat'><i class='fab fa-weixin'></i></span>";
        }
    }

    private function baiduButton() {
        if ($this->generalOptions->social["enableBaiduLogin"] && $this->generalOptions->social["baiduAppID"] && $this->generalOptions->social["baiduSecret"]) {
            echo "<span class='wpdsn wpdsn-baidu wpdiscuz-login-button' wpd-tooltip='Baidu'><i class='fas fa-paw'></i></span>";
        }
    }

    private function qqButton() {
        if ($this->generalOptions->social["enableQQLogin"] && $this->generalOptions->social["qqAppID"] && $this->generalOptions->social["qqSecret"]) {
            echo "<span class='wpdsn wpdsn-qq wpdiscuz-login-button' wpd-tooltip='Tencent QQ'><i class='fab fa-qq'></i></span>";
        }
    }

    private function weiboButton() {
        if ($this->generalOptions->social["enableWeiboLogin"] && $this->generalOptions->social["weiboKey"] && $this->generalOptions->social["weiboSecret"]) {
            echo "<span class='wpdsn wpdsn-weibo wpdiscuz-login-button' wpd-tooltip='Sina Weibo'><i class='fab fa-weibo'></i></span>";
        }
    }

    public function userAvatar($avatar, $id_or_email, $size, $default, $alt, $args = []) {
        if (strpos($avatar, "gravatar.com") === false) {
            return $avatar;
        }
        $userID = false;
        if (isset($args["wpdiscuz_current_user"])) {
            if ($args["wpdiscuz_current_user"]) {
                $userID = $args["wpdiscuz_current_user"]->ID;
            }
        } else {
            if (is_numeric($id_or_email)) {
                $userID = (int) $id_or_email;
            } elseif (is_object($id_or_email)) {
                if (!empty($id_or_email->user_id)) {
                    $userID = (int) $id_or_email->user_id;
                }
            } else {
                $user = get_user_by("email", $id_or_email);
                $userID = isset($user->ID) ? $user->ID : 0;
            }
        }

        if ($userID && $avatarURL = get_user_meta($userID, wpdFormConst::WPDISCUZ_SOCIAL_AVATAR_KEY, true)) {
//            $avatarURL = apply_filters("get_avatar_url", $avatarURL, $id_or_email, $args);
            $class = ["avatar", "avatar-" . (int) $args["size"], "photo"];
            if (is_array($args["class"])) {
                $class = array_merge($class, $args["class"]);
            } else {
                $class[] = $args["class"];
            }
            $avatar = "<img alt='" . esc_attr($alt) . "' src='" . esc_attr($avatarURL) . "' class='" . esc_attr(join(" ", $class)) . " wpdiscuz-social-avatar' height='" . intval($size) . "' width='" . intval($size) . "' " . $args["extra_attr"] . "/>";
        }
        return $avatar;
    }

    public function socialScripts() {
        if (!$this->generalOptions->general["loadComboVersion"] && ($this->generalOptions->social["enableFbShare"] || $this->generalOptions->isShowLoginButtons())) {
            $suf = $this->generalOptions->general["loadMinVersion"] ? ".min" : "";
            wp_register_script("wpdiscuz-social-js", plugins_url(WPDISCUZ_DIR_NAME . "/assets/js/wpdiscuz-social$suf.js"), ["wpdiscuz-ajax-js"], get_option("wc_plugin_version", "1.0.0"), true);
            wp_enqueue_script("wpdiscuz-social-js");
        }
    }

    public static function getInstance($options) {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($options);
        }
        return self::$_instance;
    }

}
