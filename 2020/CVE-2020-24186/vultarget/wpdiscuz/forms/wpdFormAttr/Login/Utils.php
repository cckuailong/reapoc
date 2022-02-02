<?php

namespace wpdFormAttr\Login;

use wpdFormAttr\FormConst\wpdFormConst;

class Utils {

    public static function addUser($socialUser, $provider) {
        $userID = 0;
        $userData = [];
        if ($provider === "facebook") {
            $userData = self::sanitizeFacebookUser($socialUser);
        } else if ($provider === "instagram") {
            $userData = self::sanitizeInstagramUser($socialUser);
        } else if ($provider === "twitter") {
            $userData = self::sanitizeTwitterUser($socialUser);
        } else if ($provider === "google") {
            $userData = self::sanitizeGoogleUser($socialUser);
        } else if ($provider === "disqus") {
            $userData = self::sanitizeDisqusUser($socialUser);
        } else if ($provider === "wordpress") {
            $userData = self::sanitizeWordpressUser($socialUser);
        } else if ($provider === "ok") {
            $userData = self::sanitizeOkUser($socialUser);
        } else if ($provider === "vk") {
            $userData = self::sanitizeVkUser($socialUser);
        } else if ($provider === "linkedin") {
            $userData = self::sanitizeLinkedinUser($socialUser);
        } else if ($provider === "yandex") {
            $userData = self::sanitizeYandexUser($socialUser);
        } else if ($provider === "mailru") {
            $userData = self::sanitizeMailruUser($socialUser);
        } else if ($provider === "wechat") {
            $userData = self::sanitizeWeChatUser($socialUser);
        } else if ($provider === "qq") {
            $userData = self::sanitizeQQUser($socialUser);
        } else if ($provider === "weibo") {
            $userData = self::sanitizeWeiboUser($socialUser);
        } else if ($provider === "baidu") {
            $userData = self::sanitizeBaiduUser($socialUser);
        }
        if ($userData) {
            if ($userID = email_exists($userData["user_email"])) {
                $userData["ID"] = $userID;
                $userData["status"] = "update";
            } else {
                $userData["role"] = get_option("default_role");
                $userID = $userData["ID"] = wp_insert_user($userData);
            }
            if ($userID && !is_wp_error($userID)) {
                self::updateUserData($userData);
                update_user_meta($userID, wpdFormConst::WPDISCUZ_SOCIAL_AVATAR_KEY, $userData["avatar"]);
            }
        }
        return $userID;
    }

    private static function updateUserData($userData) {
        $userProvider = get_user_meta($userData["ID"], wpdFormConst::WPDISCUZ_SOCIAL_PROVIDER_KEY, true);
        if ($userProvider !== $userData["provider"]) {
            wp_update_user(["ID" => $userData["ID"], "user_url" => $userData["user_url"]]);
            update_user_meta($userData["ID"], wpdFormConst::WPDISCUZ_SOCIAL_PROVIDER_KEY, $userData["provider"]);
            update_user_meta($userData["ID"], wpdFormConst::WPDISCUZ_SOCIAL_USER_ID_KEY, $userData["social_user_id"]);
        }
    }

    private static function generateLogin($email) {
        $username = str_replace("-", "_", sanitize_title(strstr($email, "@", true)));
        $username = sanitize_user($username);
        return self::saitizeUsername($username);
    }

    private static function saitizeUsername($username) {
        if (mb_strlen($username) > 60) {
            $username = mb_substr($username, 0, 20);
        }
        $suffix = 2;
        $alt_username = $username;
        while (username_exists($alt_username)) {
            $alt_username = $username . "_" . $suffix;
            $suffix++;
        }
        return $alt_username;
    }

    private static function sanitizeFacebookUser($fbUser) {
        $userData = [
            "user_login" => self::generateLogin($fbUser["email"]),
            "first_name" => $fbUser["first_name"],
            "last_name" => $fbUser["last_name"],
            "display_name" => $fbUser["first_name"] . " " . $fbUser["last_name"],
            "user_url" => "",
            "user_email" => $fbUser["email"],
            "provider" => "facebook",
            "social_user_id" => $fbUser["id"],
            "avatar" => "https://graph.facebook.com/" . $fbUser["id"] . "/picture?type=large",
        ];
        return $userData;
    }

    private static function sanitizeInstagramUser($instagramUser) {
        $userData = [
            "user_login" => self::saitizeUsername($instagramUser["username"]),
            "first_name" => '',
            "last_name" => '',
            "display_name" => $instagramUser["username"],
            "user_url" => "https://www.instagram.com/{$instagramUser['username']}",
            "user_email" => $instagramUser["email"],
            "provider" => "instagram",
            "social_user_id" => $instagramUser["id"],
            "avatar" => "",
        ];
        return $userData;
    }

    private static function sanitizeGoogleUser($googleUser) {
        $userData = [
            "user_login" => self::generateLogin($googleUser["email"]),
            "first_name" => $googleUser["given_name"],
            "last_name" => $googleUser["family_name"],
            "display_name" => $googleUser["name"],
            "user_url" => "",
            "user_email" => $googleUser["email"],
            "provider" => "google",
            "social_user_id" => $googleUser["sub"],
            "avatar" => $googleUser["picture"],
        ];
        return $userData;
    }

    private static function sanitizeDisqusUser($disqusUser) {
        $userData = [
            "user_login" => self::saitizeUsername($disqusUser["username"]),
            "first_name" => "",
            "last_name" => "",
            "display_name" => $disqusUser["name"],
            "user_url" => $disqusUser["profileUrl"],
            "user_email" => $disqusUser["email"],
            "provider" => "disqus",
            "social_user_id" => $disqusUser["user_id"],
            "avatar" => isset($disqusUser["avatar"]["permalink"]) ? $disqusUser["avatar"]["permalink"] : "",
        ];
        return $userData;
    }

    private static function sanitizeWordpressUser($wordpressUser) {
        $userData = [
            "user_login" => self::saitizeUsername($wordpressUser["username"]),
            "first_name" => "",
            "last_name" => "",
            "display_name" => $wordpressUser["display_name"],
            "user_url" => $wordpressUser["primary_blog_url"] ? $wordpressUser["primary_blog_url"] : $wordpressUser["profile_URL"],
            "user_email" => $wordpressUser["email"],
            "provider" => "wordpress",
            "social_user_id" => $wordpressUser["ID"],
            "avatar" => "",
        ];
        return $userData;
    }

    private static function sanitizeTwitterUser($socialUser) {
        $userData = [
            "user_login" => self::saitizeUsername($socialUser->screen_name),
            "first_name" => $socialUser->name,
            "last_name" => "",
            "display_name" => $socialUser->name,
            "user_url" => "https://twitter.com/" . $socialUser->screen_name,
            "user_email" => isset($socialUser->email) && $socialUser->email ? $socialUser->email : $socialUser->id . "@twitter.com",
            "provider" => "twitter",
            "social_user_id" => $socialUser->id,
            "avatar" => str_replace("_normal.", "_bigger.", $socialUser->profile_image_url_https),
        ];
        return $userData;
    }

    private static function sanitizeVkUser($socialUser) {
        $userData = [
            "user_login" => self::generateLogin($socialUser["email"]),
            "first_name" => $socialUser["first_name"],
            "last_name" => $socialUser["last_name"],
            "display_name" => $socialUser["first_name"] . " " . $socialUser["last_name"],
            "user_url" => "https://vk.com/" . (!empty($socialUser["screen_name"]) ? $socialUser["screen_name"] : "id" . $socialUser["id"]),
            "user_email" => $socialUser["email"],
            "provider" => "vk",
            "social_user_id" => $socialUser["id"],
            "avatar" => isset($socialUser["photo_100"]) ? $socialUser["photo_100"] : "",
        ];
        return $userData;
    }

    private static function sanitizeLinkedinUser($socialUser) {
        $fname = "";
        $lname = "";
        $email = isset($socialUser["email"]) ? $socialUser["email"] : $socialUser["id"] . "@linkedin.com";
        $dname = $login = self::generateLogin($socialUser["email"]);

        if (isset($socialUser["firstName"]["localized"]) && is_array($socialUser["firstName"]["localized"])) {
            $fname = array_shift($socialUser["firstName"]["localized"]);
        }
        if (isset($socialUser["lastName"]["localized"]) && is_array($socialUser["lastName"]["localized"])) {
            $lname = array_shift($socialUser["lastName"]["localized"]);
        }
        if ($fname || $lname) {
            $dname = trim($fname . " " . $lname);
        }

        if (isset($socialUser["profilePicture"]["displayImage~"]["elements"][0]["identifiers"][0]["identifier"])) {
            $avatar = $socialUser["profilePicture"]["displayImage~"]["elements"][0]["identifiers"][0]["identifier"];
        }

        $userData = [
            "user_login" => $login,
            "first_name" => $fname,
            "last_name" => $lname,
            "display_name" => $dname,
            "user_url" => "",
            "user_email" => $email,
            "provider" => "linkedin",
            "social_user_id" => $socialUser["id"],
            "avatar" => $socialUser['avatar']
        ];
        return $userData;
    }

    private static function sanitizeOkUser($socialUser) {
        $email = !empty($socialUser['email']) ? $socialUser['email'] : $socialUser['uid'] . '_anonymous@ok.ru';
        $userData = [
            "user_login" => self::generateLogin($email),
            "first_name" => $socialUser["first_name"],
            "last_name" => $socialUser["last_name"],
            "display_name" => $socialUser["name"],
            "user_url" => "https://ok.ru/profile/" . $socialUser["uid"],
            "user_email" => $email,
            "provider" => "ok",
            "social_user_id" => $socialUser["uid"],
            "avatar" => $socialUser["pic_2"],
        ];
        return $userData;
    }

    private static function sanitizeYandexUser($socialUser) {
        $userData = [
            "user_login" => self::saitizeUsername($socialUser["login"]),
            "first_name" => $socialUser["first_name"],
            "last_name" => $socialUser["last_name"],
            "display_name" => $socialUser["real_name"],
            "user_url" => "",
            "user_email" => $socialUser["default_email"],
            "provider" => "yandex",
            "social_user_id" => $socialUser["id"],
            "avatar" => "//avatars.mds.yandex.net/get-yapic/" . $socialUser["default_avatar_id"] . "/islands-200"
        ];
        return $userData;
    }

    private static function sanitizeMailruUser($socialUser) {
        $userData = [
            "user_login" => self::generateLogin($socialUser["email"]),
            "first_name" => $socialUser["first_name"],
            "last_name" => $socialUser["last_name"],
            "display_name" => $socialUser["nickname"],
            "user_url" => "",
            "user_email" => $socialUser["email"],
            "provider" => "mailru",
            "social_user_id" => $socialUser["id"],
            "avatar" => $socialUser["image"]
        ];
        return $userData;
    }

    private static function sanitizeWeChatUser($socialUser) {
        $userData = [
            "user_login" => self::saitizeUsername("wechat_" . uniqid()),
            "first_name" => "",
            "last_name" => "",
            "display_name" => $socialUser["nickname"],
            "user_url" => "",
            "user_email" => md5($socialUser["openid"]) . "@wechat.com",
            "provider" => "weixin",
            "social_user_id" => $socialUser["openid"],
            "avatar" => $socialUser["headimgurl"]
        ];
        return $userData;
    }

    private static function sanitizeQQUser($socialUser) {
        $avatar = "";
        if (isset($socialUser['figureurl_qq_2']) && !empty($socialUser['figureurl_qq_2'])) {
            $avatar = $socialUser['figureurl_qq_2'];
        } else if (isset($socialUser['figureurl_qq_1']) && !empty($socialUser['figureurl_qq_1'])) {
            $avatar = $socialUser['figureurl_qq_1'];
        } else {
            $avatar = $socialUser['figureurl_2'];
        }

        $avatar = str_replace("http:", "", $avatar);

        $userData = [
            "user_login" => self::saitizeUsername("qq_" . uniqid()),
            "first_name" => "",
            "last_name" => "",
            "display_name" => $socialUser["nickname"],
            "user_url" => "",
            "user_email" => md5($socialUser["openid"]) . "@qq.com",
            "provider" => "weixin",
            "social_user_id" => $socialUser["openid"],
            "avatar" => $avatar
        ];
        return $userData;
    }

    private static function sanitizeWeiboUser($socialUser) {
        $userData = [
            "user_login" => self::saitizeUsername("weibo_" . $socialUser["idstr"]),
            "first_name" => "",
            "last_name" => "",
            "display_name" => $socialUser["name"],
            "user_url" => "https://www.weibo.com/" . $socialUser["profile_url"],
            "user_email" => $socialUser["idstr"] . "@weibo.com",
            "provider" => "weibo",
            "social_user_id" => $socialUser["idstr"],
            "avatar" => $socialUser["avatar_large"]
        ];
        return $userData;
    }

    private static function sanitizeBaiduUser($socialUser) {
        $login = self::saitizeUsername("baidu_" . $socialUser["uid"]);
        $name = $socialUser["uname"] ? $socialUser["uname"] : $login;
        $avatar = $socialUser["portrait"] ? "https://himg.bdimg.com/sys/portrait/item/" . $socialUser["portrait"] . ".jpg" : "";
        $userData = [
            "user_login" => $login,
            "first_name" => "",
            "last_name" => "",
            "display_name" => $name,
            "user_url" => "",
            "user_email" => $login . "@baidu.com",
            "provider" => "baidu",
            "social_user_id" => $socialUser["uid"],
            "avatar" => $avatar
        ];
        return $userData;
    }

    public static function addOAuthState($provider, $secret, $postID) {
        add_option(wpdFormConst::WPDISCUZ_OAUTH_STATE_TOKEN.md5($secret),[wpdFormConst::WPDISCUZ_OAUTH_STATE_PROVIDER => $provider , wpdFormConst::WPDISCUZ_OAUTH_CURRENT_POSTID => $postID]);
    }

    public static function generateOAuthState($appID) {
        return md5("appID=$appID;date=" . time());
    }

    public static function getProviderByState($state) {
        $option_key = wpdFormConst::WPDISCUZ_OAUTH_STATE_TOKEN.md5($state);
        $providerData = get_option($option_key);
        delete_option($option_key);
        return $providerData;
    }

}
