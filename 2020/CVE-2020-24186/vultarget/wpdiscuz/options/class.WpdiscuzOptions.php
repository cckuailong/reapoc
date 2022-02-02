<?php

class WpdiscuzOptions implements WpDiscuzConstants {

    public $form = [];
    public $recaptcha = [];
    public $login = [];
    public $social = [];
    public $rating = [];
    public $thread_display = [];
    public $thread_layouts = [];
    public $thread_styles = [];
    public $subscription = [];
    public $labels = [];
    public $moderation = [];
    public $content = [];
    public $live = [];
    public $inline = [];
    public $general = [];
    public $wp = [];
    public $wmuUploadMaxFileSize;
    public $wmuPostMaxSize;

    /**
     * Type - HTML elements array
     * Available Values - Text
     * Description - Phrases for form elements texts
     * Default Value -
     */
    public $phrases;

    /**
     * helper class for database operations
     */
    public $dbManager;

    /* === CACHE === */
    public $isFileFunctionsExists;
    /* === CACHE === */
    public $isGoodbyeCaptchaActive;
    public $goodbyeCaptchaTocken;
    public $formContentTypeRel;
    public $formPostRel;
    private $addons;
    private $tips;

    public function __construct($dbManager) {
        $this->dbManager = $dbManager;
        $this->wmuUploadMaxFileSize = $this->getSizeInBytes(ini_get('upload_max_filesize'));
        $this->wmuPostMaxSize = $this->getSizeInBytes(ini_get('post_max_size'));
        $this->initAddons();
        $this->initTips();
        add_option(self::OPTION_SLUG_HASH_KEY, md5(time() . uniqid()), "", "no");
        $this->initPhrases();
        $this->addOptions();
        $this->initOptions(get_option(self::OPTION_SLUG_OPTIONS));
        $this->wp["dateFormat"] = get_option("date_format");
        $this->wp["timeFormat"] = get_option("time_format");
        $this->wp["threadComments"] = get_option("thread_comments");
        $this->wp["threadCommentsDepth"] = get_option("thread_comments_depth");
        $this->wp["isPaginate"] = get_option("page_comments");
        $wordpressCommentOrder = strtolower(get_option("comment_order"));
        $this->wp["commentOrder"] = in_array($wordpressCommentOrder, ["asc", "desc"]) ? $wordpressCommentOrder : "desc";
        $this->wp["commentPerPage"] = get_option("comments_per_page");
        $this->wp["showAvatars"] = get_option("show_avatars");
        $this->wp["defaultCommentsPage"] = get_option("default_comments_page");
        $this->isFileFunctionsExists = function_exists("file_get_contents") && function_exists("file_put_contents");
        $this->initFormRelations();
        $this->initGoodbyeCaptchaField();
        add_action("init", [&$this, "initPhrasesOnLoad"], 2126);
        add_action("admin_init", [&$this, "saveAndResetOptionsAndPhrases"], 1);
        add_action("wp_ajax_dismiss_wpdiscuz_addon_note", [&$this, "dismissAddonNote"]);
        add_action("admin_notices", [&$this, "adminNotices"]);
    }

    public function initOptions($serialize_options) {
        $options = maybe_unserialize($serialize_options);
        $defaultOptions = $this->getDefaultOptions();
        /* form */
        $this->form["commentFormView"] = isset($options[self::TAB_FORM]["commentFormView"]) ? $options[self::TAB_FORM]["commentFormView"] : $defaultOptions[self::TAB_FORM]["commentFormView"];
        $this->form["enableDropAnimation"] = isset($options[self::TAB_FORM]["enableDropAnimation"]) ? $options[self::TAB_FORM]["enableDropAnimation"] : $defaultOptions[self::TAB_FORM]["enableDropAnimation"];
        $this->form["richEditor"] = isset($options[self::TAB_FORM]["richEditor"]) ? $options[self::TAB_FORM]["richEditor"] : $defaultOptions[self::TAB_FORM]["richEditor"];
        $this->form["boldButton"] = isset($options[self::TAB_FORM]["boldButton"]) ? $options[self::TAB_FORM]["boldButton"] : $defaultOptions[self::TAB_FORM]["boldButton"];
        $this->form["italicButton"] = isset($options[self::TAB_FORM]["italicButton"]) ? $options[self::TAB_FORM]["italicButton"] : $defaultOptions[self::TAB_FORM]["italicButton"];
        $this->form["underlineButton"] = isset($options[self::TAB_FORM]["underlineButton"]) ? $options[self::TAB_FORM]["underlineButton"] : $defaultOptions[self::TAB_FORM]["underlineButton"];
        $this->form["strikeButton"] = isset($options[self::TAB_FORM]["strikeButton"]) ? $options[self::TAB_FORM]["strikeButton"] : $defaultOptions[self::TAB_FORM]["strikeButton"];
        $this->form["olButton"] = isset($options[self::TAB_FORM]["olButton"]) ? $options[self::TAB_FORM]["olButton"] : $defaultOptions[self::TAB_FORM]["olButton"];
        $this->form["ulButton"] = isset($options[self::TAB_FORM]["ulButton"]) ? $options[self::TAB_FORM]["ulButton"] : $defaultOptions[self::TAB_FORM]["ulButton"];
        $this->form["blockquoteButton"] = isset($options[self::TAB_FORM]["blockquoteButton"]) ? $options[self::TAB_FORM]["blockquoteButton"] : $defaultOptions[self::TAB_FORM]["blockquoteButton"];
        $this->form["codeblockButton"] = isset($options[self::TAB_FORM]["codeblockButton"]) ? $options[self::TAB_FORM]["codeblockButton"] : $defaultOptions[self::TAB_FORM]["codeblockButton"];
        $this->form["linkButton"] = isset($options[self::TAB_FORM]["linkButton"]) ? $options[self::TAB_FORM]["linkButton"] : $defaultOptions[self::TAB_FORM]["linkButton"];
        $this->form["sourcecodeButton"] = isset($options[self::TAB_FORM]["sourcecodeButton"]) ? $options[self::TAB_FORM]["sourcecodeButton"] : $defaultOptions[self::TAB_FORM]["sourcecodeButton"];
        $this->form["spoilerButton"] = isset($options[self::TAB_FORM]["spoilerButton"]) ? $options[self::TAB_FORM]["spoilerButton"] : $defaultOptions[self::TAB_FORM]["spoilerButton"];
        $this->form["enableQuickTags"] = isset($options[self::TAB_FORM]["enableQuickTags"]) ? $options[self::TAB_FORM]["enableQuickTags"] : $defaultOptions[self::TAB_FORM]["enableQuickTags"];
        $this->form["commenterNameMinLength"] = isset($options[self::TAB_FORM]["commenterNameMinLength"]) ? $options[self::TAB_FORM]["commenterNameMinLength"] : $defaultOptions[self::TAB_FORM]["commenterNameMinLength"];
        $this->form["commenterNameMaxLength"] = isset($options[self::TAB_FORM]["commenterNameMaxLength"]) ? $options[self::TAB_FORM]["commenterNameMaxLength"] : $defaultOptions[self::TAB_FORM]["commenterNameMaxLength"];
        $this->form["storeCommenterData"] = isset($options[self::TAB_FORM]["storeCommenterData"]) ? $options[self::TAB_FORM]["storeCommenterData"] : $defaultOptions[self::TAB_FORM]["storeCommenterData"];
        /* recaptcha */
        $this->recaptcha["version"] = "2.0";
        $this->recaptcha["score"] = "";
        $this->recaptcha["siteKey"] = isset($options[self::TAB_RECAPTCHA]["siteKey"]) ? $options[self::TAB_RECAPTCHA]["siteKey"] : $defaultOptions[self::TAB_RECAPTCHA]["siteKey"];
        $this->recaptcha["secretKey"] = isset($options[self::TAB_RECAPTCHA]["secretKey"]) ? $options[self::TAB_RECAPTCHA]["secretKey"] : $defaultOptions[self::TAB_RECAPTCHA]["secretKey"];
        $this->recaptcha["theme"] = isset($options[self::TAB_RECAPTCHA]["theme"]) ? $options[self::TAB_RECAPTCHA]["theme"] : $defaultOptions[self::TAB_RECAPTCHA]["theme"];
        $this->recaptcha["lang"] = isset($options[self::TAB_RECAPTCHA]["lang"]) ? $options[self::TAB_RECAPTCHA]["lang"] : $defaultOptions[self::TAB_RECAPTCHA]["lang"];
        $this->recaptcha["requestMethod"] = isset($options[self::TAB_RECAPTCHA]["requestMethod"]) ? $options[self::TAB_RECAPTCHA]["requestMethod"] : $defaultOptions[self::TAB_RECAPTCHA]["requestMethod"];
        $this->recaptcha["showForGuests"] = isset($options[self::TAB_RECAPTCHA]["showForGuests"]) ? $options[self::TAB_RECAPTCHA]["showForGuests"] : $defaultOptions[self::TAB_RECAPTCHA]["showForGuests"];
        $this->recaptcha["showForUsers"] = isset($options[self::TAB_RECAPTCHA]["showForUsers"]) ? $options[self::TAB_RECAPTCHA]["showForUsers"] : $defaultOptions[self::TAB_RECAPTCHA]["showForUsers"];
        $this->recaptcha["isShowOnSubscribeForm"] = isset($options[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"]) ? $options[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"] : $defaultOptions[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"];
        $lang = $this->recaptcha["lang"] ? "&hl=" . $this->recaptcha["lang"] : "";
        $this->recaptcha["reCaptchaUrl"] = "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit$lang";
        /* login */
        $this->login["showLoggedInUsername"] = isset($options[self::TAB_LOGIN]["showLoggedInUsername"]) ? $options[self::TAB_LOGIN]["showLoggedInUsername"] : $defaultOptions[self::TAB_LOGIN]["showLoggedInUsername"];
        $this->login["showLoginLinkForGuests"] = isset($options[self::TAB_LOGIN]["showLoginLinkForGuests"]) ? $options[self::TAB_LOGIN]["showLoginLinkForGuests"] : $defaultOptions[self::TAB_LOGIN]["showLoginLinkForGuests"];
        $this->login["showActivityTab"] = isset($options[self::TAB_LOGIN]["showActivityTab"]) ? $options[self::TAB_LOGIN]["showActivityTab"] : $defaultOptions[self::TAB_LOGIN]["showActivityTab"];
        $this->login["showSubscriptionsTab"] = isset($options[self::TAB_LOGIN]["showSubscriptionsTab"]) ? $options[self::TAB_LOGIN]["showSubscriptionsTab"] : $defaultOptions[self::TAB_LOGIN]["showSubscriptionsTab"];
        $this->login["showFollowsTab"] = isset($options[self::TAB_LOGIN]["showFollowsTab"]) ? $options[self::TAB_LOGIN]["showFollowsTab"] : $defaultOptions[self::TAB_LOGIN]["showFollowsTab"];
        $this->login["enableProfileURLs"] = isset($options[self::TAB_LOGIN]["enableProfileURLs"]) ? $options[self::TAB_LOGIN]["enableProfileURLs"] : $defaultOptions[self::TAB_LOGIN]["enableProfileURLs"];
        $this->login["websiteAsProfileUrl"] = isset($options[self::TAB_LOGIN]["websiteAsProfileUrl"]) ? $options[self::TAB_LOGIN]["websiteAsProfileUrl"] : $defaultOptions[self::TAB_LOGIN]["websiteAsProfileUrl"];
        $this->login["isUserByEmail"] = isset($options[self::TAB_LOGIN]["isUserByEmail"]) ? $options[self::TAB_LOGIN]["isUserByEmail"] : $defaultOptions[self::TAB_LOGIN]["isUserByEmail"];
        $this->login["loginUrl"] = isset($options[self::TAB_LOGIN]["loginUrl"]) ? $options[self::TAB_LOGIN]["loginUrl"] : $defaultOptions[self::TAB_LOGIN]["loginUrl"];
        /* social */
        $this->social["socialLoginAgreementCheckbox"] = isset($options[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"]) ? $options[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"] : $defaultOptions[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"];
        $this->social["socialLoginInSecondaryForm"] = isset($options[self::TAB_SOCIAL]["socialLoginInSecondaryForm"]) ? $options[self::TAB_SOCIAL]["socialLoginInSecondaryForm"] : $defaultOptions[self::TAB_SOCIAL]["socialLoginInSecondaryForm"];
        $this->social["displayIconOnAvatar"] = isset($options[self::TAB_SOCIAL]["displayIconOnAvatar"]) ? $options[self::TAB_SOCIAL]["displayIconOnAvatar"] : $defaultOptions[self::TAB_SOCIAL]["displayIconOnAvatar"];
        // fb
        $this->social["enableFbLogin"] = isset($options[self::TAB_SOCIAL]["enableFbLogin"]) ? $options[self::TAB_SOCIAL]["enableFbLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableFbLogin"];
        $this->social["enableFbShare"] = isset($options[self::TAB_SOCIAL]["enableFbShare"]) ? $options[self::TAB_SOCIAL]["enableFbShare"] : $defaultOptions[self::TAB_SOCIAL]["enableFbShare"];
        $this->social["fbAppID"] = isset($options[self::TAB_SOCIAL]["fbAppID"]) ? $options[self::TAB_SOCIAL]["fbAppID"] : $defaultOptions[self::TAB_SOCIAL]["fbAppID"];
        $this->social["fbAppSecret"] = isset($options[self::TAB_SOCIAL]["fbAppSecret"]) ? $options[self::TAB_SOCIAL]["fbAppSecret"] : $defaultOptions[self::TAB_SOCIAL]["fbAppSecret"];
        $this->social["fbUseOAuth2"] = isset($options[self::TAB_SOCIAL]["fbUseOAuth2"]) ? $options[self::TAB_SOCIAL]["fbUseOAuth2"] : $defaultOptions[self::TAB_SOCIAL]["fbUseOAuth2"];
        // twitter
        $this->social["enableTwitterLogin"] = isset($options[self::TAB_SOCIAL]["enableTwitterLogin"]) ? $options[self::TAB_SOCIAL]["enableTwitterLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableTwitterLogin"];
        $this->social["enableTwitterShare"] = isset($options[self::TAB_SOCIAL]["enableTwitterShare"]) ? $options[self::TAB_SOCIAL]["enableTwitterShare"] : $defaultOptions[self::TAB_SOCIAL]["enableTwitterShare"];
        $this->social["twitterAppID"] = isset($options[self::TAB_SOCIAL]["twitterAppID"]) ? $options[self::TAB_SOCIAL]["twitterAppID"] : $defaultOptions[self::TAB_SOCIAL]["twitterAppID"];
        $this->social["twitterAppSecret"] = isset($options[self::TAB_SOCIAL]["twitterAppSecret"]) ? $options[self::TAB_SOCIAL]["twitterAppSecret"] : $defaultOptions[self::TAB_SOCIAL]["twitterAppSecret"];
        // google
        $this->social["enableGoogleLogin"] = isset($options[self::TAB_SOCIAL]["enableGoogleLogin"]) ? $options[self::TAB_SOCIAL]["enableGoogleLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableGoogleLogin"];
        $this->social["googleClientID"] = isset($options[self::TAB_SOCIAL]["googleClientID"]) ? $options[self::TAB_SOCIAL]["googleClientID"] : $defaultOptions[self::TAB_SOCIAL]["googleClientID"];
        $this->social["googleClientSecret"] = isset($options[self::TAB_SOCIAL]["googleClientSecret"]) ? $options[self::TAB_SOCIAL]["googleClientSecret"] : $defaultOptions[self::TAB_SOCIAL]["googleClientSecret"];
        // disqus
        $this->social["enableDisqusLogin"] = isset($options[self::TAB_SOCIAL]["enableDisqusLogin"]) ? $options[self::TAB_SOCIAL]["enableDisqusLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableDisqusLogin"];
        $this->social["disqusPublicKey"] = isset($options[self::TAB_SOCIAL]["disqusPublicKey"]) ? $options[self::TAB_SOCIAL]["disqusPublicKey"] : $defaultOptions[self::TAB_SOCIAL]["disqusPublicKey"];
        $this->social["disqusSecretKey"] = isset($options[self::TAB_SOCIAL]["disqusSecretKey"]) ? $options[self::TAB_SOCIAL]["disqusSecretKey"] : $defaultOptions[self::TAB_SOCIAL]["disqusSecretKey"];
        // wordpress
        $this->social["enableWordpressLogin"] = isset($options[self::TAB_SOCIAL]["enableWordpressLogin"]) ? $options[self::TAB_SOCIAL]["enableWordpressLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableWordpressLogin"];
        $this->social["wordpressClientID"] = isset($options[self::TAB_SOCIAL]["wordpressClientID"]) ? $options[self::TAB_SOCIAL]["wordpressClientID"] : $defaultOptions[self::TAB_SOCIAL]["wordpressClientID"];
        $this->social["wordpressClientSecret"] = isset($options[self::TAB_SOCIAL]["wordpressClientSecret"]) ? $options[self::TAB_SOCIAL]["wordpressClientSecret"] : $defaultOptions[self::TAB_SOCIAL]["wordpressClientSecret"];
        // instagram
        $this->social["enableInstagramLogin"] = isset($options[self::TAB_SOCIAL]["enableInstagramLogin"]) ? $options[self::TAB_SOCIAL]["enableInstagramLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableInstagramLogin"];
        $this->social["instagramAppID"] = isset($options[self::TAB_SOCIAL]["instagramAppID"]) ? $options[self::TAB_SOCIAL]["instagramAppID"] : $defaultOptions[self::TAB_SOCIAL]["instagramAppID"];
        $this->social["instagramAppSecret"] = isset($options[self::TAB_SOCIAL]["instagramAppSecret"]) ? $options[self::TAB_SOCIAL]["instagramAppSecret"] : $defaultOptions[self::TAB_SOCIAL]["instagramAppSecret"];
        // linkedin
        $this->social["enableLinkedinLogin"] = isset($options[self::TAB_SOCIAL]["enableLinkedinLogin"]) ? $options[self::TAB_SOCIAL]["enableLinkedinLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableLinkedinLogin"];
        $this->social["linkedinClientID"] = isset($options[self::TAB_SOCIAL]["linkedinClientID"]) ? $options[self::TAB_SOCIAL]["linkedinClientID"] : $defaultOptions[self::TAB_SOCIAL]["linkedinClientID"];
        $this->social["linkedinClientSecret"] = isset($options[self::TAB_SOCIAL]["linkedinClientSecret"]) ? $options[self::TAB_SOCIAL]["linkedinClientSecret"] : $defaultOptions[self::TAB_SOCIAL]["linkedinClientSecret"];
        // whatsapp
        $this->social["enableWhatsappShare"] = isset($options[self::TAB_SOCIAL]["enableWhatsappShare"]) ? $options[self::TAB_SOCIAL]["enableWhatsappShare"] : $defaultOptions[self::TAB_SOCIAL]["enableWhatsappShare"];
        // yandex
        $this->social["enableYandexLogin"] = isset($options[self::TAB_SOCIAL]["enableYandexLogin"]) ? $options[self::TAB_SOCIAL]["enableYandexLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableYandexLogin"];
        $this->social["yandexID"] = isset($options[self::TAB_SOCIAL]["yandexID"]) ? $options[self::TAB_SOCIAL]["yandexID"] : $defaultOptions[self::TAB_SOCIAL]["yandexID"];
        $this->social["yandexPassword"] = isset($options[self::TAB_SOCIAL]["yandexPassword"]) ? $options[self::TAB_SOCIAL]["yandexPassword"] : $defaultOptions[self::TAB_SOCIAL]["yandexPassword"];
        // mail.ru
        $this->social["enableMailruLogin"] = isset($options[self::TAB_SOCIAL]["enableMailruLogin"]) ? $options[self::TAB_SOCIAL]["enableMailruLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableMailruLogin"];
        $this->social["mailruClientID"] = isset($options[self::TAB_SOCIAL]["mailruClientID"]) ? $options[self::TAB_SOCIAL]["mailruClientID"] : $defaultOptions[self::TAB_SOCIAL]["mailruClientID"];
        $this->social["mailruClientSecret"] = isset($options[self::TAB_SOCIAL]["mailruClientSecret"]) ? $options[self::TAB_SOCIAL]["mailruClientSecret"] : $defaultOptions[self::TAB_SOCIAL]["mailruClientSecret"];
        // weibo
        $this->social["enableWeiboLogin"] = isset($options[self::TAB_SOCIAL]["enableWeiboLogin"]) ? $options[self::TAB_SOCIAL]["enableWeiboLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableWeiboLogin"];
        $this->social["weiboKey"] = isset($options[self::TAB_SOCIAL]["weiboKey"]) ? $options[self::TAB_SOCIAL]["weiboKey"] : $defaultOptions[self::TAB_SOCIAL]["weiboKey"];
        $this->social["weiboSecret"] = isset($options[self::TAB_SOCIAL]["weiboSecret"]) ? $options[self::TAB_SOCIAL]["weiboSecret"] : $defaultOptions[self::TAB_SOCIAL]["weiboSecret"];
        // wechat
        $this->social["enableWechatLogin"] = isset($options[self::TAB_SOCIAL]["enableWechatLogin"]) ? $options[self::TAB_SOCIAL]["enableWechatLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableWechatLogin"];
        $this->social["wechatAppID"] = isset($options[self::TAB_SOCIAL]["wechatAppID"]) ? $options[self::TAB_SOCIAL]["wechatAppID"] : $defaultOptions[self::TAB_SOCIAL]["wechatAppID"];
        $this->social["wechatSecret"] = isset($options[self::TAB_SOCIAL]["wechatSecret"]) ? $options[self::TAB_SOCIAL]["wechatSecret"] : $defaultOptions[self::TAB_SOCIAL]["wechatSecret"];
        // qq
        $this->social["enableQQLogin"] = isset($options[self::TAB_SOCIAL]["enableQQLogin"]) ? $options[self::TAB_SOCIAL]["enableQQLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableQQLogin"];
        $this->social["qqAppID"] = isset($options[self::TAB_SOCIAL]["qqAppID"]) ? $options[self::TAB_SOCIAL]["qqAppID"] : $defaultOptions[self::TAB_SOCIAL]["qqAppID"];
        $this->social["qqSecret"] = isset($options[self::TAB_SOCIAL]["qqSecret"]) ? $options[self::TAB_SOCIAL]["qqSecret"] : $defaultOptions[self::TAB_SOCIAL]["qqSecret"];
        // baidu
        $this->social["enableBaiduLogin"] = isset($options[self::TAB_SOCIAL]["enableBaiduLogin"]) ? $options[self::TAB_SOCIAL]["enableBaiduLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableBaiduLogin"];
        $this->social["baiduAppID"] = isset($options[self::TAB_SOCIAL]["baiduAppID"]) ? $options[self::TAB_SOCIAL]["baiduAppID"] : $defaultOptions[self::TAB_SOCIAL]["baiduAppID"];
        $this->social["baiduSecret"] = isset($options[self::TAB_SOCIAL]["baiduSecret"]) ? $options[self::TAB_SOCIAL]["baiduSecret"] : $defaultOptions[self::TAB_SOCIAL]["baiduSecret"];
        // vk
        $this->social["enableVkLogin"] = isset($options[self::TAB_SOCIAL]["enableVkLogin"]) ? $options[self::TAB_SOCIAL]["enableVkLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableVkLogin"];
        $this->social["enableVkShare"] = isset($options[self::TAB_SOCIAL]["enableVkShare"]) ? $options[self::TAB_SOCIAL]["enableVkShare"] : $defaultOptions[self::TAB_SOCIAL]["enableVkShare"];
        $this->social["vkAppID"] = isset($options[self::TAB_SOCIAL]["vkAppID"]) ? $options[self::TAB_SOCIAL]["vkAppID"] : $defaultOptions[self::TAB_SOCIAL]["vkAppID"];
        $this->social["vkAppSecret"] = isset($options[self::TAB_SOCIAL]["vkAppSecret"]) ? $options[self::TAB_SOCIAL]["vkAppSecret"] : $defaultOptions[self::TAB_SOCIAL]["vkAppSecret"];
        // ok
        $this->social["enableOkLogin"] = isset($options[self::TAB_SOCIAL]["enableOkLogin"]) ? $options[self::TAB_SOCIAL]["enableOkLogin"] : $defaultOptions[self::TAB_SOCIAL]["enableOkLogin"];
        $this->social["enableOkShare"] = isset($options[self::TAB_SOCIAL]["enableOkShare"]) ? $options[self::TAB_SOCIAL]["enableOkShare"] : $defaultOptions[self::TAB_SOCIAL]["enableOkShare"];
        $this->social["okAppID"] = isset($options[self::TAB_SOCIAL]["okAppID"]) ? $options[self::TAB_SOCIAL]["okAppID"] : $defaultOptions[self::TAB_SOCIAL]["okAppID"];
        $this->social["okAppKey"] = isset($options[self::TAB_SOCIAL]["okAppKey"]) ? $options[self::TAB_SOCIAL]["okAppKey"] : $defaultOptions[self::TAB_SOCIAL]["okAppKey"];
        $this->social["okAppSecret"] = isset($options[self::TAB_SOCIAL]["okAppSecret"]) ? $options[self::TAB_SOCIAL]["okAppSecret"] : $defaultOptions[self::TAB_SOCIAL]["okAppSecret"];
        /* rating */
        $this->rating["enablePostRatingSchema"] = isset($options[self::TAB_RATING]["enablePostRatingSchema"]) ? $options[self::TAB_RATING]["enablePostRatingSchema"] : $defaultOptions[self::TAB_RATING]["enablePostRatingSchema"];
        $this->rating["displayRatingOnPost"] = isset($options[self::TAB_RATING]["displayRatingOnPost"]) ? $options[self::TAB_RATING]["displayRatingOnPost"] : $defaultOptions[self::TAB_RATING]["displayRatingOnPost"];
        $this->rating["ratingCssOnNoneSingular"] = isset($options[self::TAB_RATING]["ratingCssOnNoneSingular"]) ? $options[self::TAB_RATING]["ratingCssOnNoneSingular"] : $defaultOptions[self::TAB_RATING]["ratingCssOnNoneSingular"];
        $this->rating["ratingHoverColor"] = isset($options[self::TAB_RATING]["ratingHoverColor"]) ? $options[self::TAB_RATING]["ratingHoverColor"] : $defaultOptions[self::TAB_RATING]["ratingHoverColor"];
        $this->rating["ratingInactiveColor"] = isset($options[self::TAB_RATING]["ratingInactiveColor"]) ? $options[self::TAB_RATING]["ratingInactiveColor"] : $defaultOptions[self::TAB_RATING]["ratingInactiveColor"];
        $this->rating["ratingActiveColor"] = isset($options[self::TAB_RATING]["ratingActiveColor"]) ? $options[self::TAB_RATING]["ratingActiveColor"] : $defaultOptions[self::TAB_RATING]["ratingActiveColor"];
        /* thread_display */
        $this->thread_display["firstLoadWithAjax"] = isset($options[self::TAB_THREAD_DISPLAY]["firstLoadWithAjax"]) ? $options[self::TAB_THREAD_DISPLAY]["firstLoadWithAjax"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["firstLoadWithAjax"];
        $this->thread_display["commentListLoadType"] = isset($options[self::TAB_THREAD_DISPLAY]["commentListLoadType"]) ? $options[self::TAB_THREAD_DISPLAY]["commentListLoadType"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["commentListLoadType"];
        $this->thread_display["isLoadOnlyParentComments"] = isset($options[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"]) ? $options[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"];
        $this->thread_display["showReactedFilterButton"] = isset($options[self::TAB_THREAD_DISPLAY]["showReactedFilterButton"]) ? $options[self::TAB_THREAD_DISPLAY]["showReactedFilterButton"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["showReactedFilterButton"];
        $this->thread_display["showHottestFilterButton"] = isset($options[self::TAB_THREAD_DISPLAY]["showHottestFilterButton"]) ? $options[self::TAB_THREAD_DISPLAY]["showHottestFilterButton"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["showHottestFilterButton"];
        $this->thread_display["showSortingButtons"] = isset($options[self::TAB_THREAD_DISPLAY]["showSortingButtons"]) ? $options[self::TAB_THREAD_DISPLAY]["showSortingButtons"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["showSortingButtons"];
        $this->thread_display["mostVotedByDefault"] = isset($options[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"]) ? $options[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"];
        $this->thread_display["reverseChildren"] = isset($options[self::TAB_THREAD_DISPLAY]["reverseChildren"]) ? $options[self::TAB_THREAD_DISPLAY]["reverseChildren"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["reverseChildren"];
        $this->thread_display["highlightUnreadComments"] = isset($options[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"]) ? $options[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"];
        $this->thread_display["scrollToComment"] = isset($options[self::TAB_THREAD_DISPLAY]["scrollToComment"]) ? $options[self::TAB_THREAD_DISPLAY]["scrollToComment"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["scrollToComment"];
        $this->thread_display["orderCommentsBy"] = isset($options[self::TAB_THREAD_DISPLAY]["orderCommentsBy"]) ? $options[self::TAB_THREAD_DISPLAY]["orderCommentsBy"] : $defaultOptions[self::TAB_THREAD_DISPLAY]["orderCommentsBy"];
        /* thread_layouts */
        $this->thread_layouts["showCommentLink"] = isset($options[self::TAB_THREAD_LAYOUTS]["showCommentLink"]) ? $options[self::TAB_THREAD_LAYOUTS]["showCommentLink"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["showCommentLink"];
        $this->thread_layouts["showCommentDate"] = isset($options[self::TAB_THREAD_LAYOUTS]["showCommentDate"]) ? $options[self::TAB_THREAD_LAYOUTS]["showCommentDate"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["showCommentDate"];
        $this->thread_layouts["showVotingButtons"] = isset($options[self::TAB_THREAD_LAYOUTS]["showVotingButtons"]) ? $options[self::TAB_THREAD_LAYOUTS]["showVotingButtons"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["showVotingButtons"];
        $this->thread_layouts["votingButtonsIcon"] = isset($options[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"]) ? $options[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"];
        $this->thread_layouts["votingButtonsStyle"] = isset($options[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"]) ? $options[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"];
        $this->thread_layouts["enableDislikeButton"] = isset($options[self::TAB_THREAD_LAYOUTS]["enableDislikeButton"]) ? $options[self::TAB_THREAD_LAYOUTS]["enableDislikeButton"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["enableDislikeButton"];
        $this->thread_layouts["isGuestCanVote"] = isset($options[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"]) ? $options[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"];
        $this->thread_layouts["highlightVotingButtons"] = isset($options[self::TAB_THREAD_LAYOUTS]["highlightVotingButtons"]) ? $options[self::TAB_THREAD_LAYOUTS]["highlightVotingButtons"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["highlightVotingButtons"];
        $this->thread_layouts["showAvatars"] = isset($options[self::TAB_THREAD_LAYOUTS]["showAvatars"]) ? $options[self::TAB_THREAD_LAYOUTS]["showAvatars"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["showAvatars"];
        $this->thread_layouts["defaultAvatarUrlForUser"] = isset($options[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForUser"]) ? $options[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForUser"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForUser"];
        $this->thread_layouts["defaultAvatarUrlForGuest"] = isset($options[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForGuest"]) ? $options[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForGuest"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForGuest"];
        $this->thread_layouts["changeAvatarsEverywhere"] = isset($options[self::TAB_THREAD_LAYOUTS]["changeAvatarsEverywhere"]) ? $options[self::TAB_THREAD_LAYOUTS]["changeAvatarsEverywhere"] : $defaultOptions[self::TAB_THREAD_LAYOUTS]["changeAvatarsEverywhere"];
        /* thread_styles */
        $this->thread_styles["theme"] = isset($options[self::TAB_THREAD_STYLES]["theme"]) ? $options[self::TAB_THREAD_STYLES]["theme"] : $defaultOptions[self::TAB_THREAD_STYLES]["theme"];
        $this->thread_styles["primaryColor"] = isset($options[self::TAB_THREAD_STYLES]["primaryColor"]) ? $options[self::TAB_THREAD_STYLES]["primaryColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["primaryColor"];
        $this->thread_styles["newLoadedCommentBGColor"] = isset($options[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"]) ? $options[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"];
        $this->thread_styles["primaryButtonColor"] = isset($options[self::TAB_THREAD_STYLES]["primaryButtonColor"]) ? $options[self::TAB_THREAD_STYLES]["primaryButtonColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["primaryButtonColor"];
        $this->thread_styles["primaryButtonBG"] = isset($options[self::TAB_THREAD_STYLES]["primaryButtonBG"]) ? $options[self::TAB_THREAD_STYLES]["primaryButtonBG"] : $defaultOptions[self::TAB_THREAD_STYLES]["primaryButtonBG"];
        $this->thread_styles["bubbleColors"] = isset($options[self::TAB_THREAD_STYLES]["bubbleColors"]) ? $options[self::TAB_THREAD_STYLES]["bubbleColors"] : $defaultOptions[self::TAB_THREAD_STYLES]["bubbleColors"];
        $this->thread_styles["inlineFeedbackColors"] = isset($options[self::TAB_THREAD_STYLES]["inlineFeedbackColors"]) ? $options[self::TAB_THREAD_STYLES]["inlineFeedbackColors"] : $defaultOptions[self::TAB_THREAD_STYLES]["inlineFeedbackColors"];
        $this->thread_styles["defaultCommentAreaBG"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentAreaBG"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentAreaBG"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentAreaBG"];
        $this->thread_styles["defaultCommentTextColor"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentTextColor"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentTextColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentTextColor"];
        $this->thread_styles["defaultCommentFieldsBG"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentFieldsBG"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentFieldsBG"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentFieldsBG"];
        $this->thread_styles["defaultCommentFieldsBorderColor"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentFieldsBorderColor"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentFieldsBorderColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentFieldsBorderColor"];
        $this->thread_styles["defaultCommentFieldsTextColor"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentFieldsTextColor"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentFieldsTextColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentFieldsTextColor"];
        $this->thread_styles["defaultCommentFieldsPlaceholderColor"] = isset($options[self::TAB_THREAD_STYLES]["defaultCommentFieldsPlaceholderColor"]) ? $options[self::TAB_THREAD_STYLES]["defaultCommentFieldsPlaceholderColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["defaultCommentFieldsPlaceholderColor"];
        $this->thread_styles["darkCommentAreaBG"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentAreaBG"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentAreaBG"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentAreaBG"];
        $this->thread_styles["darkCommentTextColor"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentTextColor"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentTextColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentTextColor"];
        $this->thread_styles["darkCommentFieldsBG"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentFieldsBG"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentFieldsBG"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentFieldsBG"];
        $this->thread_styles["darkCommentFieldsBorderColor"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentFieldsBorderColor"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentFieldsBorderColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentFieldsBorderColor"];
        $this->thread_styles["darkCommentFieldsTextColor"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentFieldsTextColor"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentFieldsTextColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentFieldsTextColor"];
        $this->thread_styles["darkCommentFieldsPlaceholderColor"] = isset($options[self::TAB_THREAD_STYLES]["darkCommentFieldsPlaceholderColor"]) ? $options[self::TAB_THREAD_STYLES]["darkCommentFieldsPlaceholderColor"] : $defaultOptions[self::TAB_THREAD_STYLES]["darkCommentFieldsPlaceholderColor"];
        $this->thread_styles["commentTextSize"] = isset($options[self::TAB_THREAD_STYLES]["commentTextSize"]) ? $options[self::TAB_THREAD_STYLES]["commentTextSize"] : $defaultOptions[self::TAB_THREAD_STYLES]["commentTextSize"];
        $this->thread_styles["enableFontAwesome"] = isset($options[self::TAB_THREAD_STYLES]["enableFontAwesome"]) ? $options[self::TAB_THREAD_STYLES]["enableFontAwesome"] : $defaultOptions[self::TAB_THREAD_STYLES]["enableFontAwesome"];
        $this->thread_styles["customCss"] = isset($options[self::TAB_THREAD_STYLES]["customCss"]) ? $options[self::TAB_THREAD_STYLES]["customCss"] : $defaultOptions[self::TAB_THREAD_STYLES]["customCss"];
        /* subscription */
        $this->subscription["enableUserMentioning"] = isset($options[self::TAB_SUBSCRIPTION]["enableUserMentioning"]) ? $options[self::TAB_SUBSCRIPTION]["enableUserMentioning"] : $defaultOptions[self::TAB_SUBSCRIPTION]["enableUserMentioning"];
        $this->subscription["sendMailToMentionedUsers"] = isset($options[self::TAB_SUBSCRIPTION]["sendMailToMentionedUsers"]) ? $options[self::TAB_SUBSCRIPTION]["sendMailToMentionedUsers"] : $defaultOptions[self::TAB_SUBSCRIPTION]["sendMailToMentionedUsers"];
        $this->subscription["isNotifyOnCommentApprove"] = isset($options[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"]) ? $options[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"] : $defaultOptions[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"];
        $this->subscription["enableMemberConfirm"] = isset($options[self::TAB_SUBSCRIPTION]["enableMemberConfirm"]) ? $options[self::TAB_SUBSCRIPTION]["enableMemberConfirm"] : $defaultOptions[self::TAB_SUBSCRIPTION]["enableMemberConfirm"];
        $this->subscription["enableGuestsConfirm"] = isset($options[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"]) ? $options[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"] : $defaultOptions[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"];
        $this->subscription["subscriptionType"] = isset($options[self::TAB_SUBSCRIPTION]["subscriptionType"]) ? $options[self::TAB_SUBSCRIPTION]["subscriptionType"] : $defaultOptions[self::TAB_SUBSCRIPTION]["subscriptionType"];
        $this->subscription["showReplyCheckbox"] = isset($options[self::TAB_SUBSCRIPTION]["showReplyCheckbox"]) ? $options[self::TAB_SUBSCRIPTION]["showReplyCheckbox"] : $defaultOptions[self::TAB_SUBSCRIPTION]["showReplyCheckbox"];
        $this->subscription["isReplyDefaultChecked"] = isset($options[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"]) ? $options[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"] : $defaultOptions[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"];
        $this->subscription["usePostmaticForCommentNotification"] = isset($options[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"]) ? $options[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"] : $defaultOptions[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"];
        $this->subscription["isFollowActive"] = isset($options[self::TAB_SUBSCRIPTION]["isFollowActive"]) ? $options[self::TAB_SUBSCRIPTION]["isFollowActive"] : $defaultOptions[self::TAB_SUBSCRIPTION]["isFollowActive"];
        $this->subscription["disableFollowConfirmForUsers"] = isset($options[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"]) ? $options[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"] : $defaultOptions[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"];
        /* labels */
        $this->labels["blogRoleLabels"] = isset($options[self::TAB_LABELS]["blogRoleLabels"]) ? $options[self::TAB_LABELS]["blogRoleLabels"] : $defaultOptions[self::TAB_LABELS]["blogRoleLabels"];
        $this->labels["blogRoles"] = isset($options[self::TAB_LABELS]["blogRoles"]) ? $options[self::TAB_LABELS]["blogRoles"] : $defaultOptions[self::TAB_LABELS]["blogRoles"];
        /* moderation */
        $this->moderation["commentEditableTime"] = isset($options[self::TAB_MODERATION]["commentEditableTime"]) ? $options[self::TAB_MODERATION]["commentEditableTime"] : $defaultOptions[self::TAB_MODERATION]["commentEditableTime"];
        $this->moderation["enableEditingWhenHaveReplies"] = isset($options[self::TAB_MODERATION]["enableEditingWhenHaveReplies"]) ? $options[self::TAB_MODERATION]["enableEditingWhenHaveReplies"] : $defaultOptions[self::TAB_MODERATION]["enableEditingWhenHaveReplies"];
        $this->moderation["displayEditingInfo"] = isset($options[self::TAB_MODERATION]["displayEditingInfo"]) ? $options[self::TAB_MODERATION]["displayEditingInfo"] : $defaultOptions[self::TAB_MODERATION]["displayEditingInfo"];
        $this->moderation["enableStickButton"] = isset($options[self::TAB_MODERATION]["enableStickButton"]) ? $options[self::TAB_MODERATION]["enableStickButton"] : $defaultOptions[self::TAB_MODERATION]["enableStickButton"];
        $this->moderation["enableCloseButton"] = isset($options[self::TAB_MODERATION]["enableCloseButton"]) ? $options[self::TAB_MODERATION]["enableCloseButton"] : $defaultOptions[self::TAB_MODERATION]["enableCloseButton"];
        $this->moderation["restrictCommentingPerUser"] = isset($options[self::TAB_MODERATION]["restrictCommentingPerUser"]) ? $options[self::TAB_MODERATION]["restrictCommentingPerUser"] : $defaultOptions[self::TAB_MODERATION]["restrictCommentingPerUser"];
        $this->moderation["commentRestrictionType"] = isset($options[self::TAB_MODERATION]["commentRestrictionType"]) ? $options[self::TAB_MODERATION]["commentRestrictionType"] : $defaultOptions[self::TAB_MODERATION]["commentRestrictionType"];
        $this->moderation["userCommentsLimit"] = isset($options[self::TAB_MODERATION]["userCommentsLimit"]) ? $options[self::TAB_MODERATION]["userCommentsLimit"] : $defaultOptions[self::TAB_MODERATION]["userCommentsLimit"];
        /* content */
        $this->content["commentTextMinLength"] = isset($options[self::TAB_CONTENT]["commentTextMinLength"]) ? $options[self::TAB_CONTENT]["commentTextMinLength"] : $defaultOptions[self::TAB_CONTENT]["commentTextMinLength"];
        $this->content["commentTextMaxLength"] = isset($options[self::TAB_CONTENT]["commentTextMaxLength"]) ? $options[self::TAB_CONTENT]["commentTextMaxLength"] : $defaultOptions[self::TAB_CONTENT]["commentTextMaxLength"];
        $this->content["enableImageConversion"] = isset($options[self::TAB_CONTENT]["enableImageConversion"]) ? $options[self::TAB_CONTENT]["enableImageConversion"] : $defaultOptions[self::TAB_CONTENT]["enableImageConversion"];
        $this->content["enableShortcodes"] = isset($options[self::TAB_CONTENT]["enableShortcodes"]) ? $options[self::TAB_CONTENT]["enableShortcodes"] : $defaultOptions[self::TAB_CONTENT]["enableShortcodes"];
        $this->content["commentReadMoreLimit"] = isset($options[self::TAB_CONTENT]["commentReadMoreLimit"]) ? $options[self::TAB_CONTENT]["commentReadMoreLimit"] : $defaultOptions[self::TAB_CONTENT]["commentReadMoreLimit"];
        $this->content["wmuIsEnabled"] = isset($options[self::TAB_CONTENT]["wmuIsEnabled"]) ? $options[self::TAB_CONTENT]["wmuIsEnabled"] : $defaultOptions[self::TAB_CONTENT]["wmuIsEnabled"];
        $this->content["wmuIsGuestAllowed"] = isset($options[self::TAB_CONTENT]["wmuIsGuestAllowed"]) ? $options[self::TAB_CONTENT]["wmuIsGuestAllowed"] : $defaultOptions[self::TAB_CONTENT]["wmuIsGuestAllowed"];
        $this->content["wmuIsLightbox"] = isset($options[self::TAB_CONTENT]["wmuIsLightbox"]) ? $options[self::TAB_CONTENT]["wmuIsLightbox"] : $defaultOptions[self::TAB_CONTENT]["wmuIsLightbox"];
        $this->content["wmuMimeTypes"] = isset($options[self::TAB_CONTENT]["wmuMimeTypes"]) ? $options[self::TAB_CONTENT]["wmuMimeTypes"] : $defaultOptions[self::TAB_CONTENT]["wmuMimeTypes"];
        $this->content["wmuMaxFileSize"] = isset($options[self::TAB_CONTENT]["wmuMaxFileSize"]) ? $options[self::TAB_CONTENT]["wmuMaxFileSize"] : $defaultOptions[self::TAB_CONTENT]["wmuMaxFileSize"];
        $this->content["wmuIsShowFilesDashboard"] = isset($options[self::TAB_CONTENT]["wmuIsShowFilesDashboard"]) ? $options[self::TAB_CONTENT]["wmuIsShowFilesDashboard"] : $defaultOptions[self::TAB_CONTENT]["wmuIsShowFilesDashboard"];
        $this->content["wmuSingleImageWidth"] = isset($options[self::TAB_CONTENT]["wmuSingleImageWidth"]) ? $options[self::TAB_CONTENT]["wmuSingleImageWidth"] : $defaultOptions[self::TAB_CONTENT]["wmuSingleImageWidth"];
        $this->content["wmuSingleImageHeight"] = isset($options[self::TAB_CONTENT]["wmuSingleImageHeight"]) ? $options[self::TAB_CONTENT]["wmuSingleImageHeight"] : $defaultOptions[self::TAB_CONTENT]["wmuSingleImageHeight"];
        $this->content["wmuImageSizes"] = isset($options[self::TAB_CONTENT]["wmuImageSizes"]) ? array_filter($options[self::TAB_CONTENT]["wmuImageSizes"]) : $defaultOptions[self::TAB_CONTENT]["wmuImageSizes"];
        /* live */
        $this->live["enableBubble"] = isset($options[self::TAB_LIVE]["enableBubble"]) ? $options[self::TAB_LIVE]["enableBubble"] : $defaultOptions[self::TAB_LIVE]["enableBubble"];
        $this->live["bubbleLiveUpdate"] = isset($options[self::TAB_LIVE]["bubbleLiveUpdate"]) ? $options[self::TAB_LIVE]["bubbleLiveUpdate"] : $defaultOptions[self::TAB_LIVE]["bubbleLiveUpdate"];
        $this->live["bubbleLocation"] = isset($options[self::TAB_LIVE]["bubbleLocation"]) ? $options[self::TAB_LIVE]["bubbleLocation"] : $defaultOptions[self::TAB_LIVE]["bubbleLocation"];
        $this->live["bubbleShowNewCommentMessage"] = isset($options[self::TAB_LIVE]["bubbleShowNewCommentMessage"]) ? $options[self::TAB_LIVE]["bubbleShowNewCommentMessage"] : $defaultOptions[self::TAB_LIVE]["bubbleShowNewCommentMessage"];
        $this->live["bubbleHintTimeout"] = isset($options[self::TAB_LIVE]["bubbleHintTimeout"]) ? $options[self::TAB_LIVE]["bubbleHintTimeout"] : $defaultOptions[self::TAB_LIVE]["bubbleHintTimeout"];
        $this->live["bubbleHintHideTimeout"] = isset($options[self::TAB_LIVE]["bubbleHintHideTimeout"]) ? $options[self::TAB_LIVE]["bubbleHintHideTimeout"] : $defaultOptions[self::TAB_LIVE]["bubbleHintHideTimeout"];
        $this->live["commentListUpdateType"] = isset($options[self::TAB_LIVE]["commentListUpdateType"]) ? $options[self::TAB_LIVE]["commentListUpdateType"] : $defaultOptions[self::TAB_LIVE]["commentListUpdateType"];
        $this->live["liveUpdateGuests"] = isset($options[self::TAB_LIVE]["liveUpdateGuests"]) ? $options[self::TAB_LIVE]["liveUpdateGuests"] : $defaultOptions[self::TAB_LIVE]["liveUpdateGuests"];
        $this->live["commentListUpdateTimer"] = isset($options[self::TAB_LIVE]["commentListUpdateTimer"]) ? $options[self::TAB_LIVE]["commentListUpdateTimer"] : $defaultOptions[self::TAB_LIVE]["commentListUpdateTimer"];
        /* inline */
        $this->inline["showInlineFilterButton"] = isset($options[self::TAB_INLINE]["showInlineFilterButton"]) ? $options[self::TAB_INLINE]["showInlineFilterButton"] : $defaultOptions[self::TAB_INLINE]["showInlineFilterButton"];
        $this->inline["inlineFeedbackAttractionType"] = isset($options[self::TAB_INLINE]["inlineFeedbackAttractionType"]) ? $options[self::TAB_INLINE]["inlineFeedbackAttractionType"] : $defaultOptions[self::TAB_INLINE]["inlineFeedbackAttractionType"];
        /* general */
        $this->general["isEnableOnHome"] = isset($options[self::TAB_GENERAL]["isEnableOnHome"]) ? $options[self::TAB_GENERAL]["isEnableOnHome"] : $defaultOptions[self::TAB_GENERAL]["isEnableOnHome"];
        $this->general["isNativeAjaxEnabled"] = isset($options[self::TAB_GENERAL]["isNativeAjaxEnabled"]) ? $options[self::TAB_GENERAL]["isNativeAjaxEnabled"] : $defaultOptions[self::TAB_GENERAL]["isNativeAjaxEnabled"];
        $this->general["loadComboVersion"] = isset($options[self::TAB_GENERAL]["loadComboVersion"]) ? $options[self::TAB_GENERAL]["loadComboVersion"] : $defaultOptions[self::TAB_GENERAL]["loadComboVersion"];
        $this->general["loadMinVersion"] = isset($options[self::TAB_GENERAL]["loadMinVersion"]) ? $options[self::TAB_GENERAL]["loadMinVersion"] : $defaultOptions[self::TAB_GENERAL]["loadMinVersion"];
        $this->general["commentLinkFilter"] = isset($options[self::TAB_GENERAL]["commentLinkFilter"]) ? $options[self::TAB_GENERAL]["commentLinkFilter"] : $defaultOptions[self::TAB_GENERAL]["commentLinkFilter"];
        $this->general["redirectPage"] = isset($options[self::TAB_GENERAL]["redirectPage"]) ? $options[self::TAB_GENERAL]["redirectPage"] : $defaultOptions[self::TAB_GENERAL]["redirectPage"];
        $this->general["simpleCommentDate"] = isset($options[self::TAB_GENERAL]["simpleCommentDate"]) ? $options[self::TAB_GENERAL]["simpleCommentDate"] : $defaultOptions[self::TAB_GENERAL]["simpleCommentDate"];
        $this->general["dateDiffFormat"] = isset($options[self::TAB_GENERAL]["dateDiffFormat"]) ? $options[self::TAB_GENERAL]["dateDiffFormat"] : $defaultOptions[self::TAB_GENERAL]["dateDiffFormat"];
        $this->general["isUsePoMo"] = isset($options[self::TAB_GENERAL]["isUsePoMo"]) ? $options[self::TAB_GENERAL]["isUsePoMo"] : $defaultOptions[self::TAB_GENERAL]["isUsePoMo"];
        $this->general["showPluginPoweredByLink"] = isset($options[self::TAB_GENERAL]["showPluginPoweredByLink"]) ? $options[self::TAB_GENERAL]["showPluginPoweredByLink"] : $defaultOptions[self::TAB_GENERAL]["showPluginPoweredByLink"];
        $this->general["isGravatarCacheEnabled"] = isset($options[self::TAB_GENERAL]["isGravatarCacheEnabled"]) ? $options[self::TAB_GENERAL]["isGravatarCacheEnabled"] : $defaultOptions[self::TAB_GENERAL]["isGravatarCacheEnabled"];
        $this->general["gravatarCacheMethod"] = isset($options[self::TAB_GENERAL]["gravatarCacheMethod"]) ? $options[self::TAB_GENERAL]["gravatarCacheMethod"] : $defaultOptions[self::TAB_GENERAL]["gravatarCacheMethod"];
        $this->general["gravatarCacheTimeout"] = isset($options[self::TAB_GENERAL]["gravatarCacheTimeout"]) ? $options[self::TAB_GENERAL]["gravatarCacheTimeout"] : $defaultOptions[self::TAB_GENERAL]["gravatarCacheTimeout"];
        do_action("wpdiscuz_init_options", $this);
    }

    /**
     * initialize default phrases
     */
    public function initPhrases() {
        $this->phrases = [
            "wc_be_the_first_text" => esc_html__("Be the First to Comment!", "wpdiscuz"),
            "wc_comment_start_text" => esc_html__("Start the discussion", "wpdiscuz"),
            "wc_comment_join_text" => esc_html__("Join the discussion", "wpdiscuz"),
            "wc_most_reacted_comment" => esc_html__("Most reacted comment", "wpdiscuz"),
            "wc_hottest_comment_thread" => esc_html__("Hottest comment thread", "wpdiscuz"),
            "wc_inline_comments" => esc_html__("Inline Comments", "wpdiscuz"),
            "wc_email_text" => esc_html__("Email", "wpdiscuz"),
            "wc_subscribe_anchor" => esc_html__("Subscribe", "wpdiscuz"),
            "wc_notify_of" => esc_html__("Notify of", "wpdiscuz"),
            "wc_notify_on_new_comment" => esc_html__("new follow-up comments", "wpdiscuz"),
            "wc_notify_on_all_new_reply" => esc_html__("new replies to my comments", "wpdiscuz"),
            "wc_notify_on_new_reply" => esc_html__("Notify of new replies to this comment", "wpdiscuz"),
            "wc_sort_by" => esc_html__("Sort by", "wpdiscuz"),
            "wc_newest" => esc_html__("Newest", "wpdiscuz"),
            "wc_oldest" => esc_html__("Oldest", "wpdiscuz"),
            "wc_most_voted" => esc_html__("Most Voted", "wpdiscuz"),
            "wc_load_more_submit_text" => esc_html__("Load More Comments", "wpdiscuz"),
            "wc_load_rest_comments_submit_text" => esc_html__("Load Rest of Comments", "wpdiscuz"),
            "wc_reply_text" => esc_html__("Reply", "wpdiscuz"),
            "wc_share_text" => esc_html__("Share", "wpdiscuz"),
            "wc_edit_text" => esc_html__("Edit", "wpdiscuz"),
            "wc_share_facebook" => esc_html__("Share On Facebook", "wpdiscuz"),
            "wc_share_twitter" => esc_html__("Share On Twitter", "wpdiscuz"),
            "wc_share_whatsapp" => esc_html__("Share On WhatsApp", "wpdiscuz"),
            "wc_share_vk" => esc_html__("Share On VKontakte", "wpdiscuz"),
            "wc_share_ok" => esc_html__("Share On Odnoklassniki", "wpdiscuz"),
            "wc_hide_replies_text" => esc_html__("Hide Replies", "wpdiscuz"),
            "wc_show_replies_text" => esc_html__("View Replies", "wpdiscuz"),
            "wc_email_subject" => esc_html__("New Comment", "wpdiscuz"),
            "wc_email_message" => __("Hi [SUBSCRIBER_NAME],<br/><br/> new comment have been posted by the <em><strong>[COMMENT_AUTHOR]</em></strong> on the discussion section you've been interested in<br/><br/><a href='[COMMENT_URL]'>[COMMENT_URL]</a><br/><br/>[COMMENT_CONTENT]<br/><br/><a href='[UNSUBSCRIBE_URL]'>Unsubscribe</a>", "wpdiscuz"),
            "wc_all_comment_new_reply_subject" => esc_html__("New Reply", "wpdiscuz"),
            "wc_all_comment_new_reply_message" => __("Hi [SUBSCRIBER_NAME],<br/><br/> new reply have been posted by the <em><strong>[COMMENT_AUTHOR]</em></strong> on the discussion section you've been interested in<br/><br/><a href='[COMMENT_URL]'>[COMMENT_URL]</a><br/><br/>[COMMENT_CONTENT]<br/><br/><a href='[UNSUBSCRIBE_URL]'>Unsubscribe</a>", "wpdiscuz"),
            "wc_new_reply_email_subject" => esc_html__("New Reply", "wpdiscuz"),
            "wc_new_reply_email_message" => __("Hi [SUBSCRIBER_NAME],<br/><br/> new reply have been posted by the <em><strong>[COMMENT_AUTHOR]</em></strong> on the discussion section you've been interested in<br/><br/><a href='[COMMENT_URL]'>[COMMENT_URL]</a><br/><br/>[COMMENT_CONTENT]<br/><br/><a href='[UNSUBSCRIBE_URL]'>Unsubscribe</a>", "wpdiscuz"),
            "wc_subscribed_on_comment" => esc_html__("You're subscribed for new replies on this comment", "wpdiscuz"),
            "wc_subscribed_on_all_comment" => esc_html__("You're subscribed for new replies on all your comments", "wpdiscuz"),
            "wc_subscribed_on_post" => esc_html__("You're subscribed for new follow-up comments on this post", "wpdiscuz"),
            "wc_unsubscribe" => esc_html__("Unsubscribe", "wpdiscuz"),
            "wc_ignore_subscription" => esc_html__("Cancel subscription", "wpdiscuz"),
            "wc_unsubscribe_message" => esc_html__("You've successfully unsubscribed.", "wpdiscuz"),
            "wc_subscribe_message" => esc_html__("You've successfully subscribed.", "wpdiscuz"),
            "wc_confirm_email" => esc_html__("Confirm your subscription", "wpdiscuz"),
            "wc_comfirm_success_message" => esc_html__("You've successfully confirmed your subscription.", "wpdiscuz"),
            "wc_confirm_email_subject" => esc_html__("Subscription Confirmation", "wpdiscuz"),
            "wc_confirm_email_message" => __("Hi, <br/> You just subscribed for new comments on our website. This means you will receive an email when new comments are posted according to subscription option you've chosen. <br/> To activate, click confirm below. If you believe this is an error, ignore this message and we'll never bother you again. <br/><br/><a href='[POST_URL]'>[POST_TITLE]</a><br/><br/><a href='[CONFIRM_URL]'>Confirm Your Subscrption</a><br/><br/><a href='[CANCEL_URL]'>Cancel Subscription</a>", "wpdiscuz"),
            "wc_error_empty_text" => esc_html__("please fill out this field to comment", "wpdiscuz"),
            "wc_error_email_text" => esc_html__("email address is invalid", "wpdiscuz"),
            "wc_error_url_text" => esc_html__("url is invalid", "wpdiscuz"),
            "wc_year_text" => esc_html__("year", "wpdiscuz"),
            "wc_year_text_plural" => esc_html__("years", "wpdiscuz"), // PLURAL
            "wc_month_text" => esc_html__("month", "wpdiscuz"),
            "wc_month_text_plural" => esc_html__("months", "wpdiscuz"), // PLURAL
            "wc_day_text" => esc_html__("day", "wpdiscuz"),
            "wc_day_text_plural" => esc_html__("days", "wpdiscuz"), // PLURAL
            "wc_hour_text" => esc_html__("hour", "wpdiscuz"),
            "wc_hour_text_plural" => esc_html__("hours", "wpdiscuz"), // PLURAL
            "wc_minute_text" => esc_html__("minute", "wpdiscuz"),
            "wc_minute_text_plural" => esc_html__("minutes", "wpdiscuz"), // PLURAL
            "wc_second_text" => esc_html__("second", "wpdiscuz"),
            "wc_second_text_plural" => esc_html__("seconds", "wpdiscuz"), // PLURAL
            "wc_right_now_text" => esc_html__("right now", "wpdiscuz"),
            "wc_ago_text" => esc_html__("ago", "wpdiscuz"),
            "wc_you_must_be_text" => esc_html__("You must be", "wpdiscuz"),
            "wc_logged_in_as" => esc_html__("You are logged in as", "wpdiscuz"),
            "wc_log_in" => esc_html__("Login", "wpdiscuz"),
            "wc_login_please" => esc_html__("Please %s to comment", "wpdiscuz"),
            "wc_log_out" => esc_html__("Log out", "wpdiscuz"),
            "wc_logged_in_text" => esc_html__("logged in", "wpdiscuz"),
            "wc_to_post_comment_text" => esc_html__("to post a comment.", "wpdiscuz"),
            "wc_vote_up" => esc_html__("Vote Up", "wpdiscuz"),
            "wc_vote_down" => esc_html__("Vote Down", "wpdiscuz"),
            "wc_vote_counted" => esc_html__("Vote Counted", "wpdiscuz"),
            "wc_vote_only_one_time" => esc_html__("You've already voted for this comment", "wpdiscuz"),
            "wc_voting_error" => esc_html__("Voting Error", "wpdiscuz"),
            "wc_login_to_vote" => esc_html__("You Must Be Logged In To Vote", "wpdiscuz"),
            "wc_self_vote" => esc_html__("You cannot vote for your comment", "wpdiscuz"),
            "wc_deny_voting_from_same_ip" => esc_html__("You are not allowed to vote for this comment", "wpdiscuz"),
            "wc_invalid_captcha" => esc_html__("Invalid Captcha Code", "wpdiscuz"),
            "wc_invalid_field" => esc_html__("Some of field value is invalid", "wpdiscuz"),
            "wc_awaiting_for_approval" => esc_html__("Awaiting for approval", "wpdiscuz"),
            "wc_comment_not_updated" => esc_html__("Sorry, the comment was not updated", "wpdiscuz"),
            "wc_comment_edit_not_possible" => esc_html__("Sorry, this comment is no longer possible to edit", "wpdiscuz"),
            "wc_comment_not_edited" => esc_html__("You've not made any changes", "wpdiscuz"),
            "wc_comment_edit_save_button" => esc_html__("Save", "wpdiscuz"),
            "wc_comment_edit_cancel_button" => esc_html__("Cancel", "wpdiscuz"),
            "wc_msg_input_min_length" => esc_html__("Input is too short", "wpdiscuz"),
            "wc_msg_input_max_length" => esc_html__("Input is too long", "wpdiscuz"),
            "wc_read_more" => esc_html__("Read more &raquo;", "wpdiscuz"),
            "wc_anonymous" => esc_html__("Anonymous", "wpdiscuz"),
            "wc_msg_required_fields" => esc_html__("Please fill out required fields", "wpdiscuz"),
            "wc_connect_with" => esc_html__("Connect with", "wpdiscuz"),
            "wc_subscribed_to" => esc_html__("You're subscribed to", "wpdiscuz"),
            "wc_postmatic_subscription_label" => esc_html__("Participate in this discussion via email", "wpdiscuz"),
            "wc_form_subscription_submit" => esc_html__("&rsaquo;", "wpdiscuz"),
            "wc_comment_approved_email_subject" => esc_html__("Your comment is approved!", "wpdiscuz"),
            "wc_comment_approved_email_message" => __('Hi [COMMENT_AUTHOR],<br/><br/>your comment was approved.<br/><br/><a href="[COMMENT_URL]">[COMMENT_URL]</a><br/><br/>[COMMENT_CONTENT]', "wpdiscuz"),
            "wc_roles_cannot_comment_message" => esc_html__("Comments are closed.", "wpdiscuz"),
            "wc_stick_comment_btn_title" => esc_html__("Stick this comment", "wpdiscuz"),
            "wc_stick_comment" => esc_html__("Stick", "wpdiscuz"),
            "wc_unstick_comment" => esc_html__("Unstick", "wpdiscuz"),
            "wc_sticky_comment_icon_title" => esc_html__("Sticky Comment Thread", "wpdiscuz"),
            "wc_close_comment_btn_title" => esc_html__("Close this thread", "wpdiscuz"),
            "wc_close_comment" => esc_html__("Close", "wpdiscuz"),
            "wc_open_comment" => esc_html__("Open", "wpdiscuz"),
            "wc_closed_comment_icon_title" => esc_html__("Closed Comment Thread", "wpdiscuz"),
            "wc_social_login_agreement_label" => esc_html__("I allow to create an account", "wpdiscuz"),
            "wc_social_login_agreement_desc" => esc_html__("When you login first time using a Social Login button, we collect your account public profile information shared by Social Login provider, based on your privacy settings. We also get your email address to automatically create an account for you in our website. Once your account is created, you'll be logged-in to this account.", "wpdiscuz"),
            "wc_agreement_button_disagree" => esc_html__("Disagree", "wpdiscuz"),
            "wc_agreement_button_agree" => esc_html__("Agree", "wpdiscuz"),
            "wc_content_and_settings" => esc_html__("My content and settings", "wpdiscuz"),
            "wc_user_settings_activity" => esc_html__("Activity", "wpdiscuz"),
            "wc_user_settings_subscriptions" => esc_html__("Subscriptions", "wpdiscuz"),
            "wc_user_settings_follows" => esc_html__("Follows", "wpdiscuz"),
            "wc_user_settings_response_to" => esc_html__("In response to:", "wpdiscuz"),
            "wc_user_settings_email_me_delete_links" => esc_html__("Bulk management via email", "wpdiscuz"),
            "wc_user_settings_email_me_delete_links_desc" => esc_html__("Click the button above to get an email with bulk delete and unsubscribe links.", "wpdiscuz"),
            "wc_user_settings_no_data" => esc_html__("No data found!", "wpdiscuz"),
            "wc_user_settings_request_deleting_comments" => esc_html__("Delete all my comments", "wpdiscuz"),
            "wc_user_settings_cancel_subscriptions" => esc_html__("Cancel all comment subscriptions", "wpdiscuz"),
            "wc_user_settings_clear_cookie" => esc_html__("Clear cookies with my personal data", "wpdiscuz"),
            "wc_user_settings_delete_links" => esc_html__("Bulk management via email", "wpdiscuz"),
            "wc_user_settings_delete_all_comments" => esc_html__("Delete all my comments", "wpdiscuz"),
            "wc_user_settings_delete_all_comments_message" => __('Please use this link to delete all your comments. Please note, that this action cannot be undone.<br/><br/><a href="[DELETE_COMMENTS_URL]" target="_blank">Delete all my comments</a><br/><br/>', "wpdiscuz"),
            "wc_user_settings_delete_all_subscriptions" => esc_html__("Delete all my subscriptions", "wpdiscuz"),
            "wc_user_settings_delete_all_subscriptions_message" => __('Please use this link to cancel all subscriptions for new comments. Please note, that this action cannot be undone.<br/><br/><a href="[DELETE_SUBSCRIPTIONS_URL]" target="_blank">Delete all my subscriptions</a><br/><br/>', "wpdiscuz"),
            "wc_user_settings_delete_all_follows" => esc_html__("Delete all my follows", "wpdiscuz"),
            "wc_user_settings_delete_all_follows_message" => __('Please use this link to cancel all follows for new comments. Please note, that this action cannot be undone.<br/><br/><a href="[DELETE_FOLLOWS_URL]" target="_blank">Delete all my follows</a><br/><br/>', "wpdiscuz"),
            "wc_user_settings_subscribed_to_replies" => esc_html__("subscribed to this comment", "wpdiscuz"),
            "wc_user_settings_subscribed_to_replies_own" => esc_html__("subscribed to my comments", "wpdiscuz"),
            "wc_user_settings_subscribed_to_all_comments" => esc_html__("subscribed to all follow-up comments of this post", "wpdiscuz"),
            "wc_user_settings_check_email" => esc_html__("Please check your email."),
            "wc_user_settings_email_error" => esc_html__("Error : Can't send email.", "wpdiscuz"),
            "wc_confirm_comment_delete" => esc_html__("Are you sure you want to delete this comment?", "wpdiscuz"),
            "wc_confirm_cancel_subscription" => esc_html__("Are you sure you want to cancel this subscription?", "wpdiscuz"),
            "wc_confirm_cancel_follow" => esc_html__("Are you sure you want to cancel this follow?", "wpdiscuz"),
            "wc_follow_user" => esc_html__("Follow this user", "wpdiscuz"),
            "wc_unfollow_user" => esc_html__("Unfollow this user", "wpdiscuz"),
            "wc_follow_success" => esc_html__("You started following this comment author", "wpdiscuz"),
            "wc_follow_canceled" => esc_html__("You stopped following this comment author.", "wpdiscuz"),
            "wc_follow_email_confirm" => esc_html__("Please check your email and confirm the user following request.", "wpdiscuz"),
            "wc_follow_email_confirm_fail" => esc_html__("Sorry, we couldn't send confirmation email.", "wpdiscuz"),
            "wc_follow_login_to_follow" => esc_html__("Please login to follow users.", "wpdiscuz"),
            "wc_follow_impossible" => esc_html__("We are sorry, but you can't follow this user.", "wpdiscuz"),
            "wc_follow_not_added" => esc_html__("Following failed. Please try again later.", "wpdiscuz"),
            "wc_follow_confirm" => esc_html__("Confirm user following request", "wpdiscuz"),
            "wc_follow_cancel" => esc_html__("Cancel user following request", "wpdiscuz"),
            "wc_follow_confirm_email_subject" => esc_html__("User Following Confirmation", "wpdiscuz"),
            "wc_follow_confirm_email_message" => __('Hi, <br/> You just started following a new user. You\'ll get email notification once new comment is posted by this user. <br/> Please click on "user following confirmation" link to confirm your request. If you believe this is an error, ignore this message and we\'ll never bother you again. <br/><br/><a href="[POST_URL]">[POST_TITLE]</a><br/><br/><a href="[CONFIRM_URL]">' . __("Confirm Follow", "wpdiscuz") . '</a><br/><br/><a href="[CANCEL_URL]">' . esc_html__("Unfollow", "wpdiscuz") . "</a>", "wpdiscuz"),
            "wc_follow_email_subject" => esc_html__("New Comment", "wpdiscuz"),
            "wc_follow_email_message" => __('Hi [FOLLOWER_NAME],<br/><br/> new comment have been posted by the <em><strong>[COMMENT_AUTHOR]</em></strong> you are following<br/><br/><a href="[COMMENT_URL]">[COMMENT_URL]</a><br/><br/>[COMMENT_CONTENT]<br/><br/><a href="[CANCEL_URL]">' . esc_html__("Unfollow", "wpdiscuz") . '</a>', "wpdiscuz"),
            "wc_mentioned_email_subject" => esc_html__("You have been mentioned in comment", "wpdiscuz"),
            "wc_mentioned_email_message" => __('Hi [MENTIONED_USER_NAME]!<br/>You have been mentioned in a comment posted on "[POST_TITLE]" post by [COMMENT_AUTHOR].<br/><br/>Comment URL: <a href="[COMMENT_URL]">[COMMENT_URL]</a>', "wpdiscuz"),
            "wc_copied_to_clipboard" => esc_html__("Copied to clipboard!", "wpdiscuz"),
            "wc_feedback_shortcode_tooltip" => esc_html__("Select a part of text and ask readers for feedback (inline commenting)", "wpdiscuz"),
            "wc_feedback_popup_title" => esc_html__("Ask for Feedback", "wpdiscuz"),
            "wc_please_leave_feebdack" => esc_html__("Please leave a feedback on this", "wpdiscuz"),
            "wc_feedback_content_text" => esc_html__("", "wpdiscuz"),
            "wc_feedback_comment_success" => esc_html__("Thank you for your feedback!", "wpdiscuz"),
            "wc_commenting_is_closed" => esc_html__("Commenting is closed!", "wpdiscuz"),
            "wc_closed_comment_thread" => esc_html__("This is closed comment thread", "wpdiscuz"),
            "wc_bubble_invite_message" => esc_html__("Would love your thoughts, please comment.", "wpdiscuz"),
            "wc_vote_phrase" => esc_html__("vote", "wpdiscuz"),
            "wc_votes_phrase" => esc_html__("votes", "wpdiscuz"),
            "wc_comment_link" => esc_html__("Comment Link", "wpdiscuz"),
            "wc_not_allowed_to_comment_more_than" => esc_html__("We are sorry, you are not allowed to comment more than %d time(s)!", "wpdiscuz"),
            "wc_not_allowed_to_create_comment_thread_more_than" => esc_html__("We are sorry, you are not allowed to comment more than %d time(s)!", "wpdiscuz"),
            "wc_not_allowed_to_reply_more_than" => esc_html__("We are sorry, you are not allowed to reply more than %d time(s)!", "wpdiscuz"),
            "wc_inline_form_comment" => esc_html__("Your comment here...", "wpdiscuz"),
            "wc_inline_form_notify" => esc_html__("Notify me via email when a new reply is posted", "wpdiscuz"),
            "wc_inline_form_name" => esc_html__("Your Name*", "wpdiscuz"),
            "wc_inline_form_email" => esc_html__("Your Email", "wpdiscuz"),
            "wc_inline_form_comment_button" => esc_html__("COMMENT", "wpdiscuz"),
            "wc_inline_comments_view_all" => esc_html__("View all comments", "wpdiscuz"),
            "wc_inline_feedbacks" => esc_html__("Inline Feedbacks", "wpdiscuz"),
            "wc_unable_sent_email" => esc_html__("Unable to send an email", "wpdiscuz"),
            "wc_subscription_fault" => esc_html__("Subscription Fault", "wpdiscuz"),
            "wc_comments_are_deleted" => esc_html__("Your comments have been deleted from database", "wpdiscuz"),
            "wc_cancel_subs_success" => esc_html__("You cancel all your subscriptions successfully", "wpdiscuz"),
            "wc_cancel_follows_success" => esc_html__("You cancel all your follows successfully", "wpdiscuz"),
            "wc_follow_confirm_success" => esc_html__("Follow has been confirmed successfully", "wpdiscuz"),
            "wc_follow_cancel_success" => esc_html__("Follow has been canceled successfully", "wpdiscuz"),
            "wc_login_to_comment" => esc_html__("Please login to comment", "wpdiscuz"),
            "wc_view_comments" => esc_html__("View Comments", "wpdiscuz"),
            "wc_spoiler" => esc_html__("Spoiler", "wpdiscuz"),
            "wc_last_edited" => esc_html__('Last edited %1$s by %2$s', "wpdiscuz"),
            "wc_reply_to" => esc_html__("Reply to", "wpdiscuz"),
            "wc_manage_comment" => esc_html__("Manage Comment", "wpdiscuz"),
            "wc_spoiler_title" => esc_html__("Spoiler Title", "wpdiscuz"),
            "wc_cannot_rate_again" => esc_html__("You cannot rate again", "wpdiscuz"),
            "wc_not_allowed_to_rate" => esc_html__("You're not allowed to rate here", "wpdiscuz"),
            // Media Upload
            "wmuPhraseConfirmDelete" => esc_html__("Are you sure you want to delete this attachment?", "wpdiscuz"),
            "wmuPhraseNotAllowedFile" => esc_html__("Not allowed file type", "wpdiscuz"),
            "wmuPhraseMaxFileCount" => esc_html__("Maximum number of uploaded files is", "wpdiscuz"),
            "wmuPhraseMaxFileSize" => esc_html__("Maximum upload file size is", "wpdiscuz"),
            "wmuPhrasePostMaxSize" => esc_html__("Maximum post size is", "wpdiscuz"),
            "wmuAttachImage" => esc_html__("Attach an image to this comment", "wpdiscuz"),
            "wmuChangeImage" => esc_html__("Change the attached image", "wpdiscuz"),
        ];
    }

    public function toArray() {
        $options = [
            self::TAB_FORM => [
                "commentFormView" => $this->form["commentFormView"],
                "enableDropAnimation" => $this->form["enableDropAnimation"],
                "richEditor" => $this->form["richEditor"],
                "boldButton" => $this->form["boldButton"],
                "italicButton" => $this->form["italicButton"],
                "underlineButton" => $this->form["underlineButton"],
                "strikeButton" => $this->form["strikeButton"],
                "olButton" => $this->form["olButton"],
                "ulButton" => $this->form["ulButton"],
                "blockquoteButton" => $this->form["blockquoteButton"],
                "codeblockButton" => $this->form["codeblockButton"],
                "linkButton" => $this->form["linkButton"],
                "sourcecodeButton" => $this->form["sourcecodeButton"],
                "spoilerButton" => $this->form["spoilerButton"],
                "enableQuickTags" => $this->form["enableQuickTags"],
                "commenterNameMinLength" => $this->form["commenterNameMinLength"],
                "commenterNameMaxLength" => $this->form["commenterNameMaxLength"],
                "storeCommenterData" => $this->form["storeCommenterData"],
            ],
            self::TAB_RECAPTCHA => [
                "siteKey" => $this->recaptcha["siteKey"],
                "secretKey" => $this->recaptcha["secretKey"],
                "theme" => $this->recaptcha["theme"],
                "lang" => $this->recaptcha["lang"],
                "requestMethod" => $this->recaptcha["requestMethod"],
                "showForGuests" => $this->recaptcha["showForGuests"],
                "showForUsers" => $this->recaptcha["showForUsers"],
                "isShowOnSubscribeForm" => $this->recaptcha["isShowOnSubscribeForm"],
            ],
            self::TAB_LOGIN => [
                "showLoggedInUsername" => $this->login["showLoggedInUsername"],
                "showLoginLinkForGuests" => $this->login["showLoginLinkForGuests"],
                "showActivityTab" => $this->login["showActivityTab"],
                "showSubscriptionsTab" => $this->login["showSubscriptionsTab"],
                "showFollowsTab" => $this->login["showFollowsTab"],
                "enableProfileURLs" => $this->login["enableProfileURLs"],
                "websiteAsProfileUrl" => $this->login["websiteAsProfileUrl"],
                "isUserByEmail" => $this->login["isUserByEmail"],
                "loginUrl" => $this->login["loginUrl"],
            ],
            self::TAB_SOCIAL => [
                "socialLoginAgreementCheckbox" => $this->social["socialLoginAgreementCheckbox"],
                "socialLoginInSecondaryForm" => $this->social["socialLoginInSecondaryForm"],
                "displayIconOnAvatar" => $this->social["displayIconOnAvatar"],
                // fb
                "enableFbLogin" => $this->social["enableFbLogin"],
                "enableFbShare" => $this->social["enableFbShare"],
                "fbAppID" => $this->social["fbAppID"],
                "fbAppSecret" => $this->social["fbAppSecret"],
                "fbUseOAuth2" => $this->social["fbUseOAuth2"],
                // twitter
                "enableTwitterLogin" => $this->social["enableTwitterLogin"],
                "enableTwitterShare" => $this->social["enableTwitterShare"],
                "twitterAppID" => $this->social["twitterAppID"],
                "twitterAppSecret" => $this->social["twitterAppSecret"],
                // google
                "enableGoogleLogin" => $this->social["enableGoogleLogin"],
                "googleClientID" => $this->social["googleClientID"],
                "googleClientSecret" => $this->social["googleClientSecret"],
                // disqus
                "enableDisqusLogin" => $this->social["enableDisqusLogin"],
                "disqusPublicKey" => $this->social["disqusPublicKey"],
                "disqusSecretKey" => $this->social["disqusSecretKey"],
                // wordpress
                "enableWordpressLogin" => $this->social["enableWordpressLogin"],
                "wordpressClientID" => $this->social["wordpressClientID"],
                "wordpressClientSecret" => $this->social["wordpressClientSecret"],
                // instagram
                "enableInstagramLogin" => $this->social["enableInstagramLogin"],
                "instagramAppID" => $this->social["instagramAppID"],
                "instagramAppSecret" => $this->social["instagramAppSecret"],
                // linkedin
                "enableLinkedinLogin" => $this->social["enableLinkedinLogin"],
                "linkedinClientID" => $this->social["linkedinClientID"],
                "linkedinClientSecret" => $this->social["linkedinClientSecret"],
                // whatsapp
                "enableWhatsappShare" => $this->social["enableWhatsappShare"],
                // yandex
                "enableYandexLogin" => $this->social["enableYandexLogin"],
                "yandexID" => $this->social["yandexID"],
                "yandexPassword" => $this->social["yandexPassword"],
                // mail.ru
                "enableMailruLogin" => $this->social["enableMailruLogin"],
                "mailruClientID" => $this->social["mailruClientID"],
                "mailruClientSecret" => $this->social["mailruClientSecret"],
                // weibo
                "enableWeiboLogin" => $this->social["enableWeiboLogin"],
                "weiboKey" => $this->social["weiboKey"],
                "weiboSecret" => $this->social["weiboSecret"],
                // wechat
                "enableWechatLogin" => $this->social["enableWechatLogin"],
                "wechatAppID" => $this->social["wechatAppID"],
                "wechatSecret" => $this->social["wechatSecret"],
                // qq
                "enableQQLogin" => $this->social["enableQQLogin"],
                "qqAppID" => $this->social["qqAppID"],
                "qqSecret" => $this->social["qqSecret"],
                // baidu
                "enableBaiduLogin" => $this->social["enableBaiduLogin"],
                "baiduAppID" => $this->social["baiduAppID"],
                "baiduSecret" => $this->social["baiduSecret"],
                // vk
                "enableVkLogin" => $this->social["enableVkLogin"],
                "enableVkShare" => $this->social["enableVkShare"],
                "vkAppID" => $this->social["vkAppID"],
                "vkAppSecret" => $this->social["vkAppSecret"],
                // ok
                "enableOkLogin" => $this->social["enableOkLogin"],
                "enableOkShare" => $this->social["enableOkShare"],
                "okAppID" => $this->social["okAppID"],
                "okAppKey" => $this->social["okAppKey"],
                "okAppSecret" => $this->social["okAppSecret"],
            ],
            self::TAB_RATING => [
                "enablePostRatingSchema" => $this->rating["enablePostRatingSchema"],
                "displayRatingOnPost" => $this->rating["displayRatingOnPost"],
                "ratingCssOnNoneSingular" => $this->rating["ratingCssOnNoneSingular"],
                "ratingHoverColor" => $this->rating["ratingHoverColor"],
                "ratingInactiveColor" => $this->rating["ratingInactiveColor"],
                "ratingActiveColor" => $this->rating["ratingActiveColor"],
            ],
            self::TAB_THREAD_DISPLAY => [
                "firstLoadWithAjax" => $this->thread_display["firstLoadWithAjax"],
                "commentListLoadType" => $this->thread_display["commentListLoadType"],
                "isLoadOnlyParentComments" => $this->thread_display["isLoadOnlyParentComments"],
                "showReactedFilterButton" => $this->thread_display["showReactedFilterButton"],
                "showHottestFilterButton" => $this->thread_display["showHottestFilterButton"],
                "showSortingButtons" => $this->thread_display["showSortingButtons"],
                "mostVotedByDefault" => $this->thread_display["mostVotedByDefault"],
                "reverseChildren" => $this->thread_display["reverseChildren"],
                "highlightUnreadComments" => $this->thread_display["highlightUnreadComments"],
                "scrollToComment" => $this->thread_display["scrollToComment"],
                "orderCommentsBy" => $this->thread_display["orderCommentsBy"],
            ],
            self::TAB_THREAD_LAYOUTS => [
                "showCommentLink" => $this->thread_layouts["showCommentLink"],
                "showCommentDate" => $this->thread_layouts["showCommentDate"],
                "showVotingButtons" => $this->thread_layouts["showVotingButtons"],
                "votingButtonsIcon" => $this->thread_layouts["votingButtonsIcon"],
                "votingButtonsStyle" => $this->thread_layouts["votingButtonsStyle"],
                "enableDislikeButton" => $this->thread_layouts["enableDislikeButton"],
                "isGuestCanVote" => $this->thread_layouts["isGuestCanVote"],
                "highlightVotingButtons" => $this->thread_layouts["highlightVotingButtons"],
                "showAvatars" => $this->thread_layouts["showAvatars"],
                "defaultAvatarUrlForUser" => $this->thread_layouts["defaultAvatarUrlForUser"],
                "defaultAvatarUrlForGuest" => $this->thread_layouts["defaultAvatarUrlForGuest"],
                "changeAvatarsEverywhere" => $this->thread_layouts["changeAvatarsEverywhere"],
            ],
            self::TAB_THREAD_STYLES => [
                "theme" => $this->thread_styles["theme"],
                "primaryColor" => $this->thread_styles["primaryColor"],
                "newLoadedCommentBGColor" => $this->thread_styles["newLoadedCommentBGColor"],
                "primaryButtonColor" => $this->thread_styles["primaryButtonColor"],
                "primaryButtonBG" => $this->thread_styles["primaryButtonBG"],
                "bubbleColors" => $this->thread_styles["bubbleColors"],
                "inlineFeedbackColors" => $this->thread_styles["inlineFeedbackColors"],
                "defaultCommentAreaBG" => $this->thread_styles["defaultCommentAreaBG"],
                "defaultCommentTextColor" => $this->thread_styles["defaultCommentTextColor"],
                "defaultCommentFieldsBG" => $this->thread_styles["defaultCommentFieldsBG"],
                "defaultCommentFieldsBorderColor" => $this->thread_styles["defaultCommentFieldsBorderColor"],
                "defaultCommentFieldsTextColor" => $this->thread_styles["defaultCommentFieldsTextColor"],
                "defaultCommentFieldsPlaceholderColor" => $this->thread_styles["defaultCommentFieldsPlaceholderColor"],
                "darkCommentAreaBG" => $this->thread_styles["darkCommentAreaBG"],
                "darkCommentTextColor" => $this->thread_styles["darkCommentTextColor"],
                "darkCommentFieldsBG" => $this->thread_styles["darkCommentFieldsBG"],
                "darkCommentFieldsBorderColor" => $this->thread_styles["darkCommentFieldsBorderColor"],
                "darkCommentFieldsTextColor" => $this->thread_styles["darkCommentFieldsTextColor"],
                "darkCommentFieldsPlaceholderColor" => $this->thread_styles["darkCommentFieldsPlaceholderColor"],
                "commentTextSize" => $this->thread_styles["commentTextSize"],
                "enableFontAwesome" => $this->thread_styles["enableFontAwesome"],
                "customCss" => $this->thread_styles["customCss"],
            ],
            self::TAB_SUBSCRIPTION => [
                "enableUserMentioning" => $this->subscription["enableUserMentioning"],
                "sendMailToMentionedUsers" => $this->subscription["sendMailToMentionedUsers"],
                "isNotifyOnCommentApprove" => $this->subscription["isNotifyOnCommentApprove"],
                "enableMemberConfirm" => $this->subscription["enableMemberConfirm"],
                "enableGuestsConfirm" => $this->subscription["enableGuestsConfirm"],
                "subscriptionType" => $this->subscription["subscriptionType"],
                "showReplyCheckbox" => $this->subscription["showReplyCheckbox"],
                "isReplyDefaultChecked" => $this->subscription["isReplyDefaultChecked"],
                "usePostmaticForCommentNotification" => $this->subscription["usePostmaticForCommentNotification"],
                "isFollowActive" => $this->subscription["isFollowActive"],
                "disableFollowConfirmForUsers" => $this->subscription["disableFollowConfirmForUsers"],
            ],
            self::TAB_LABELS => [
                "blogRoleLabels" => $this->labels["blogRoleLabels"],
                "blogRoles" => $this->labels["blogRoles"],
            ],
            self::TAB_MODERATION => [
                "commentEditableTime" => $this->moderation["commentEditableTime"],
                "enableEditingWhenHaveReplies" => $this->moderation["enableEditingWhenHaveReplies"],
                "displayEditingInfo" => $this->moderation["displayEditingInfo"],
                "enableStickButton" => $this->moderation["enableStickButton"],
                "enableCloseButton" => $this->moderation["enableCloseButton"],
                "restrictCommentingPerUser" => $this->moderation["restrictCommentingPerUser"],
                "commentRestrictionType" => $this->moderation["commentRestrictionType"],
                "userCommentsLimit" => $this->moderation["userCommentsLimit"],
            ],
            self::TAB_CONTENT => [
                "commentTextMinLength" => $this->content["commentTextMinLength"],
                "commentTextMaxLength" => $this->content["commentTextMaxLength"],
                "enableImageConversion" => $this->content["enableImageConversion"],
                "enableShortcodes" => $this->content["enableShortcodes"],
                "commentReadMoreLimit" => $this->content["commentReadMoreLimit"],
                "wmuIsEnabled" => $this->content["wmuIsEnabled"],
                "wmuIsGuestAllowed" => $this->content["wmuIsGuestAllowed"],
                "wmuIsLightbox" => $this->content["wmuIsLightbox"],
                "wmuMimeTypes" => $this->content["wmuMimeTypes"],
                "wmuMaxFileSize" => $this->content["wmuMaxFileSize"],
                "wmuIsShowFilesDashboard" => $this->content["wmuIsShowFilesDashboard"],
                "wmuSingleImageWidth" => $this->content["wmuSingleImageWidth"],
                "wmuSingleImageHeight" => $this->content["wmuSingleImageHeight"],
                "wmuImageSizes" => $this->content["wmuImageSizes"],
            ],
            self::TAB_LIVE => [
                "enableBubble" => $this->live["enableBubble"],
                "bubbleLiveUpdate" => $this->live["bubbleLiveUpdate"],
                "bubbleLocation" => $this->live["bubbleLocation"],
                "bubbleShowNewCommentMessage" => $this->live["bubbleShowNewCommentMessage"],
                "bubbleHintTimeout" => $this->live["bubbleHintTimeout"],
                "bubbleHintHideTimeout" => $this->live["bubbleHintHideTimeout"],
                "commentListUpdateType" => $this->live["commentListUpdateType"],
                "liveUpdateGuests" => $this->live["liveUpdateGuests"],
                "commentListUpdateTimer" => $this->live["commentListUpdateTimer"],
            ],
            self::TAB_INLINE => [
                "showInlineFilterButton" => $this->inline["showInlineFilterButton"],
                "inlineFeedbackAttractionType" => $this->inline["inlineFeedbackAttractionType"],
            ],
            self::TAB_GENERAL => [
                "isEnableOnHome" => $this->general["isEnableOnHome"],
                "isNativeAjaxEnabled" => $this->general["isNativeAjaxEnabled"],
                "loadComboVersion" => $this->general["loadComboVersion"],
                "loadMinVersion" => $this->general["loadMinVersion"],
                "commentLinkFilter" => $this->general["commentLinkFilter"],
                "redirectPage" => $this->general["redirectPage"],
                "simpleCommentDate" => $this->general["simpleCommentDate"],
                "dateDiffFormat" => $this->general["dateDiffFormat"],
                "isUsePoMo" => $this->general["isUsePoMo"],
                "showPluginPoweredByLink" => $this->general["showPluginPoweredByLink"],
                "isGravatarCacheEnabled" => $this->general["isGravatarCacheEnabled"],
                "gravatarCacheMethod" => $this->general["gravatarCacheMethod"],
                "gravatarCacheTimeout" => $this->general["gravatarCacheTimeout"],
            ],
        ];
        return $options;
    }

    public function updateOptions() {
        update_option(self::OPTION_SLUG_OPTIONS, $this->toArray());
    }

    public function addOptions() {
        add_option(self::OPTION_SLUG_OPTIONS, $this->getDefaultOptions());
    }

    public function getDefaultOptions() {
        return [
            self::TAB_FORM => [
                "commentFormView" => "collapsed",
                "enableDropAnimation" => 1,
                "richEditor" => "desktop",
                "boldButton" => 1,
                "italicButton" => 1,
                "underlineButton" => 1,
                "strikeButton" => 1,
                "olButton" => 1,
                "ulButton" => 1,
                "blockquoteButton" => 1,
                "codeblockButton" => 1,
                "linkButton" => 1,
                "sourcecodeButton" => 1,
                "spoilerButton" => 1,
                "enableQuickTags" => 0,
                "commenterNameMinLength" => 3,
                "commenterNameMaxLength" => 50,
                "storeCommenterData" => -1,
            ],
            self::TAB_RECAPTCHA => [
                "siteKey" => "",
                "secretKey" => "",
                "theme" => "light",
                "lang" => "",
                "requestMethod" => "auto",
                "showForGuests" => 0,
                "showForUsers" => 0,
                "isShowOnSubscribeForm" => 0,
            ],
            self::TAB_LOGIN => [
                "showLoggedInUsername" => 1,
                "showLoginLinkForGuests" => 1,
                "showActivityTab" => 1,
                "showSubscriptionsTab" => 1,
                "showFollowsTab" => 1,
                "enableProfileURLs" => 1,
                "websiteAsProfileUrl" => 1,
                "isUserByEmail" => 0,
                "loginUrl" => "",
            ],
            self::TAB_SOCIAL => [
                "socialLoginAgreementCheckbox" => 1,
                "socialLoginInSecondaryForm" => 0,
                "displayIconOnAvatar" => 1,
                "enableFbLogin" => 0,
                "enableFbShare" => 0,
                "fbUseOAuth2" => 0,
                "fbAppID" => "",
                "fbAppSecret" => "",
                "enableTwitterLogin" => 0,
                "enableTwitterShare" => 1,
                "twitterAppID" => "",
                "twitterAppSecret" => "",
                "enableGoogleLogin" => 0,
                "googleClientID" => "",
                "googleClientSecret" => "",
                "enableDisqusLogin" => 0,
                "disqusPublicKey" => "",
                "disqusSecretKey" => "",
                "enableWordpressLogin" => 0,
                "wordpressClientID" => "",
                "wordpressClientSecret" => "",
                "enableInstagramLogin" => 0,
                "instagramAppID" => "",
                "instagramAppSecret" => "",
                "enableLinkedinLogin" => 0,
                "linkedinClientID" => "",
                "linkedinClientSecret" => "",
                "enableWhatsappShare" => 0,
                "enableYandexLogin" => 0,
                "yandexID" => "",
                "yandexPassword" => "",
                "enableMailruLogin" => 0,
                "mailruClientID" => "",
                "mailruClientSecret" => "",
                "enableWeiboLogin" => 0,
                "weiboKey" => "",
                "weiboSecret" => "",
                "enableWechatLogin" => 0,
                "wechatAppID" => "",
                "wechatSecret" => "",
                "enableQQLogin" => 0,
                "qqAppID" => "",
                "qqSecret" => "",
                "enableBaiduLogin" => 0,
                "baiduAppID" => "",
                "baiduSecret" => "",
                "enableVkLogin" => 0,
                "enableVkShare" => 1,
                "vkAppID" => "",
                "vkAppSecret" => "",
                "enableOkLogin" => 0,
                "enableOkShare" => 1,
                "okAppID" => "",
                "okAppKey" => "",
                "okAppSecret" => "",
            ],
            self::TAB_RATING => [
                "enablePostRatingSchema" => 0,
                "displayRatingOnPost" => ["before_comment_form"],
                "ratingCssOnNoneSingular" => 0,
                "ratingHoverColor" => "#FFED85",
                "ratingInactiveColor" => "#DDDDDD",
                "ratingActiveColor" => "#FFD700",
            ],
            self::TAB_THREAD_DISPLAY => [
                "firstLoadWithAjax" => 0,
                "commentListLoadType" => 0,
                "isLoadOnlyParentComments" => 0,
                "showReactedFilterButton" => 1,
                "showHottestFilterButton" => 1,
                "showSortingButtons" => 1,
                "mostVotedByDefault" => 0,
                "reverseChildren" => 0,
                "highlightUnreadComments" => 0,
                "scrollToComment" => 1,
                "orderCommentsBy" => "comment_ID",
            ],
            self::TAB_THREAD_LAYOUTS => [
                "showCommentLink" => 1,
                "showCommentDate" => 1,
                "showVotingButtons" => 1,
                "votingButtonsIcon" => "fa-plus|fa-minus",
                "votingButtonsStyle" => 0,
                "enableDislikeButton" => 1,
                "isGuestCanVote" => 1,
                "highlightVotingButtons" => 1,
                "showAvatars" => 1,
                "defaultAvatarUrlForUser" => "",
                "defaultAvatarUrlForGuest" => "",
                "changeAvatarsEverywhere" => 1,
            ],
            self::TAB_THREAD_STYLES => [
                "theme" => "wpd-default",
                "primaryColor" => "#00B38F",
                "newLoadedCommentBGColor" => "#FFFAD6",
                "primaryButtonColor" => "#FFFFFF",
                "primaryButtonBG" => "#07B290",
                "bubbleColors" => "#1DB99A",
                "inlineFeedbackColors" => "#1DB99A",
                "defaultCommentAreaBG" => "",
                "defaultCommentTextColor" => "#777777",
                "defaultCommentFieldsBG" => "",
                "defaultCommentFieldsBorderColor" => "#DDDDDD",
                "defaultCommentFieldsTextColor" => "#777777",
                "defaultCommentFieldsPlaceholderColor" => "",
                "darkCommentAreaBG" => "#111111",
                "darkCommentTextColor" => "#CCCCCC",
                "darkCommentFieldsBG" => "#999999",
                "darkCommentFieldsBorderColor" => "#D1D1D1",
                "darkCommentFieldsTextColor" => "#000000",
                "darkCommentFieldsPlaceholderColor" => "#DDDDDD",
                "commentTextSize" => "14px",
                "enableFontAwesome" => 1,
                "customCss" => ".comments-area{width:auto;}",
            ],
            self::TAB_SUBSCRIPTION => [
                "enableUserMentioning" => 1,
                "sendMailToMentionedUsers" => 1,
                "isNotifyOnCommentApprove" => 1,
                "enableMemberConfirm" => 0,
                "enableGuestsConfirm" => 1,
                "subscriptionType" => 1,
                "showReplyCheckbox" => 1,
                "isReplyDefaultChecked" => 0,
                "usePostmaticForCommentNotification" => 0,
                "isFollowActive" => 1,
                "disableFollowConfirmForUsers" => 1,
            ],
            self::TAB_LABELS => [
                "blogRoleLabels" => isset($this->labels["blogRoleLabels"]) ? $this->labels["blogRoleLabels"] : [],
                "blogRoles" => isset($this->labels["blogRoles"]) ? $this->labels["blogRoles"] : [],
            ],
            self::TAB_MODERATION => [
                "commentEditableTime" => 900,
                "enableEditingWhenHaveReplies" => 0,
                "displayEditingInfo" => 1,
                "enableStickButton" => 1,
                "enableCloseButton" => 1,
                "restrictCommentingPerUser" => "disable",
                "commentRestrictionType" => "both",
                "userCommentsLimit" => 1,
            ],
            self::TAB_CONTENT => [
                "commentTextMinLength" => 1,
                "commentTextMaxLength" => "",
                "enableImageConversion" => 1,
                "enableShortcodes" => 0,
                "commentReadMoreLimit" => 0,
                "wmuIsEnabled" => 1,
                "wmuIsGuestAllowed" => 1,
                "wmuIsLightbox" => 1,
                "wmuMimeTypes" => $this->getDefaultFileTypes(),
                "wmuMaxFileSize" => 2,
                "wmuIsShowFilesDashboard" => 1,
                "wmuSingleImageWidth" => "auto",
                "wmuSingleImageHeight" => 200,
                "wmuImageSizes" => $this->getAllImageSizes(),
            ],
            self::TAB_LIVE => [
                "enableBubble" => 0,
                "bubbleLiveUpdate" => 0,
                "bubbleLocation" => "content_left",
                "bubbleShowNewCommentMessage" => 1,
                "bubbleHintTimeout" => 45,
                "bubbleHintHideTimeout" => 10,
                "commentListUpdateType" => 0,
                "liveUpdateGuests" => 0,
                "commentListUpdateTimer" => 30,
            ],
            self::TAB_INLINE => [
                "showInlineFilterButton" => 1,
                "inlineFeedbackAttractionType" => "blink",
            ],
            self::TAB_GENERAL => [
                "isEnableOnHome" => 1,
                "isNativeAjaxEnabled" => 1,
                "loadComboVersion" => 1,
                "loadMinVersion" => 1,
                "commentLinkFilter" => 1,
                "redirectPage" => 0,
                "simpleCommentDate" => 0,
                "dateDiffFormat" => "[number] [time_unit] [adjective]",
                "isUsePoMo" => 0,
                "showPluginPoweredByLink" => 0,
                "isGravatarCacheEnabled" => 0,
                "gravatarCacheMethod" => "cronjob",
                "gravatarCacheTimeout" => 10,
            ],
        ];
    }

    public function initPhrasesOnLoad() {
        if (!$this->general["isUsePoMo"] && $this->dbManager->isPhraseExists("wc_be_the_first_text")) {
            $this->phrases = $this->dbManager->getPhrases();
        } else {
            $this->initPhrases();
        }
    }

    private function initFormRelations() {
        $this->formContentTypeRel = get_option("wpdiscuz_form_content_type_rel", []);
        $this->formPostRel = get_option("wpdiscuz_form_post_rel", []);
    }

    public function isShareEnabled() {
        return ($this->social["enableFbShare"] || $this->social["enableTwitterShare"] || $this->social["enableVkShare"] || $this->social["enableOkShare"]);
    }

    public function getOptionsForJs() {
        $jsArgs = [];
        $jsArgs["wc_hide_replies_text"] = $this->phrases["wc_hide_replies_text"];
        $jsArgs["wc_show_replies_text"] = $this->phrases["wc_show_replies_text"];
        $jsArgs["wc_msg_required_fields"] = $this->phrases["wc_msg_required_fields"];
        $jsArgs["wc_invalid_field"] = $this->phrases["wc_invalid_field"];
        $jsArgs["wc_error_empty_text"] = $this->phrases["wc_error_empty_text"];
        $jsArgs["wc_error_url_text"] = $this->phrases["wc_error_url_text"];
        $jsArgs["wc_error_email_text"] = $this->phrases["wc_error_email_text"];
        $jsArgs["wc_invalid_captcha"] = $this->phrases["wc_invalid_captcha"];
        $jsArgs["wc_login_to_vote"] = $this->phrases["wc_login_to_vote"];
        $jsArgs["wc_deny_voting_from_same_ip"] = $this->phrases["wc_deny_voting_from_same_ip"];
        $jsArgs["wc_self_vote"] = $this->phrases["wc_self_vote"];
        $jsArgs["wc_vote_only_one_time"] = $this->phrases["wc_vote_only_one_time"];
        $jsArgs["wc_voting_error"] = $this->phrases["wc_voting_error"];
        $jsArgs["wc_comment_edit_not_possible"] = $this->phrases["wc_comment_edit_not_possible"];
        $jsArgs["wc_comment_not_updated"] = $this->phrases["wc_comment_not_updated"];
        $jsArgs["wc_comment_not_edited"] = $this->phrases["wc_comment_not_edited"];
        $jsArgs["wc_msg_input_min_length"] = $this->phrases["wc_msg_input_min_length"];
        $jsArgs["wc_msg_input_max_length"] = $this->phrases["wc_msg_input_max_length"];
        $jsArgs["wc_spoiler_title"] = $this->phrases["wc_spoiler_title"];
        $jsArgs["wc_cannot_rate_again"] = $this->phrases["wc_cannot_rate_again"];
        $jsArgs["wc_not_allowed_to_rate"] = $this->phrases["wc_not_allowed_to_rate"];
        //<!-- follow phrases
        $jsArgs["wc_follow_user"] = $this->phrases["wc_follow_user"];
        $jsArgs["wc_unfollow_user"] = $this->phrases["wc_unfollow_user"];
        $jsArgs["wc_follow_success"] = $this->phrases["wc_follow_success"];
        $jsArgs["wc_follow_canceled"] = $this->phrases["wc_follow_canceled"];
        $jsArgs["wc_follow_email_confirm"] = $this->phrases["wc_follow_email_confirm"];
        $jsArgs["wc_follow_email_confirm_fail"] = $this->phrases["wc_follow_email_confirm_fail"];
        $jsArgs["wc_follow_login_to_follow"] = $this->phrases["wc_follow_login_to_follow"];
        $jsArgs["wc_follow_impossible"] = $this->phrases["wc_follow_impossible"];
        $jsArgs["wc_follow_not_added"] = $this->phrases["wc_follow_not_added"];
        //follow phrases -->
        $jsArgs["is_user_logged_in"] = is_user_logged_in();
        $jsArgs["commentListLoadType"] = $this->thread_display["commentListLoadType"];
        $jsArgs["commentListUpdateType"] = $this->live["commentListUpdateType"];
        $jsArgs["commentListUpdateTimer"] = $this->live["commentListUpdateTimer"];
        $jsArgs["liveUpdateGuests"] = $this->live["liveUpdateGuests"];
        $jsArgs["wordpressThreadCommentsDepth"] = $this->wp["threadCommentsDepth"];
        $jsArgs["wordpressIsPaginate"] = $this->wp["isPaginate"];
        $jsArgs["commentTextMaxLength"] = $this->content["commentTextMaxLength"] ? $this->content["commentTextMaxLength"] : null;
        $jsArgs["commentTextMinLength"] = $this->content["commentTextMinLength"];
        if ($this->form["storeCommenterData"] < 0) {
            $jsArgs["storeCommenterData"] = 100000;
        } else if ($this->form["storeCommenterData"] == 0) {
            $jsArgs["storeCommenterData"] = null;
        } else {
            $jsArgs["storeCommenterData"] = $this->form["storeCommenterData"];
        }
        if (function_exists("zerospam_get_key")) {
            $jsArgs["wpdiscuz_zs"] = md5(zerospam_get_key());
        }
        $jsArgs["socialLoginAgreementCheckbox"] = $this->social["socialLoginAgreementCheckbox"];
        $jsArgs["enableFbLogin"] = $this->social["enableFbLogin"];
        $jsArgs["enableFbShare"] = $this->social["enableFbShare"];
        $jsArgs["facebookAppID"] = $this->social["fbAppID"];
        $jsArgs["facebookUseOAuth2"] = $this->social["fbUseOAuth2"];
        $jsArgs["enableGoogleLogin"] = $this->social["enableGoogleLogin"];
        $jsArgs["googleClientID"] = $this->social["googleClientID"];
        $jsArgs["googleClientSecret"] = $this->social["googleClientSecret"];
        $jsArgs["cookiehash"] = COOKIEHASH;
        $jsArgs["isLoadOnlyParentComments"] = $this->thread_display["isLoadOnlyParentComments"];
        $jsArgs["scrollToComment"] = $this->thread_display["scrollToComment"];
        $jsArgs["commentFormView"] = $this->form["commentFormView"];
        $jsArgs["enableDropAnimation"] = $this->form["enableDropAnimation"];
        $jsArgs["isNativeAjaxEnabled"] = $this->general["isNativeAjaxEnabled"];
        $jsArgs["enableBubble"] = $this->live["enableBubble"];
        $jsArgs["bubbleLiveUpdate"] = $this->live["bubbleLiveUpdate"];
        $jsArgs["bubbleHintTimeout"] = $this->live["bubbleHintTimeout"];
        $jsArgs["bubbleHintHideTimeout"] = $this->live["bubbleHintHideTimeout"];
        $jsArgs["cookieHideBubbleHint"] = self::COOKIE_HIDE_BUBBLE_HINT;
        $jsArgs["bubbleShowNewCommentMessage"] = $this->live["bubbleShowNewCommentMessage"];
        $jsArgs["bubbleLocation"] = $this->live["bubbleLocation"];
        $jsArgs["firstLoadWithAjax"] = $this->thread_display["firstLoadWithAjax"];
        $jsArgs["wc_copied_to_clipboard"] = $this->phrases["wc_copied_to_clipboard"];
        $jsArgs["inlineFeedbackAttractionType"] = $this->inline["inlineFeedbackAttractionType"];
        $jsArgs["loadRichEditor"] = intval($this->form["richEditor"] === "both" || (!wp_is_mobile() && $this->form["richEditor"] === "desktop"));
        //**reCaptcha**//
        $jsArgs["wpDiscuzReCaptchaSK"] = apply_filters("wpdiscuz_recaptcha_site_key", $this->recaptcha["siteKey"]);
        $jsArgs["wpDiscuzReCaptchaTheme"] = $this->recaptcha["theme"];
        $jsArgs["wpDiscuzReCaptchaVersion"] = apply_filters("wpdiscuz_recaptcha_version", $this->recaptcha["version"]);
        $jsArgs["wc_captcha_show_for_guest"] = $this->recaptcha["showForGuests"];
        $jsArgs["wc_captcha_show_for_members"] = $this->recaptcha["showForUsers"];
        $jsArgs["wpDiscuzIsShowOnSubscribeForm"] = $this->recaptcha["isShowOnSubscribeForm"];
        // Media Upload //
        $jsArgs["wmuEnabled"] = $this->content["wmuIsEnabled"];
        $jsArgs["wmuInput"] = self::INPUT_NAME;
        $jsArgs["wmuMaxFileCount"] = 1;
        $jsArgs["wmuMaxFileSize"] = $this->content["wmuMaxFileSize"] * 1024 * 1024;
        $jsArgs["wmuPostMaxSize"] = $this->wmuPostMaxSize;
        $jsArgs["wmuIsLightbox"] = $this->content["wmuIsLightbox"];
        $jsArgs["wmuMimeTypes"] = $this->content["wmuMimeTypes"];
        $jsArgs["wmuPhraseConfirmDelete"] = $this->phrases["wmuPhraseConfirmDelete"];
        $jsArgs["wmuPhraseNotAllowedFile"] = $this->phrases["wmuPhraseNotAllowedFile"];
        $jsArgs["wmuPhraseMaxFileCount"] = preg_replace("#(\d+$)#is", "", $this->phrases["wmuPhraseMaxFileCount"]) . " " . apply_filters("wpdiscuz_mu_file_count", 1);
        $jsArgs["wmuPhraseMaxFileSize"] = $this->phrases["wmuPhraseMaxFileSize"] . " " . $this->content["wmuMaxFileSize"] . "MB";
        $jsArgs["wmuPhrasePostMaxSize"] = $this->phrases["wmuPhrasePostMaxSize"] . " " . ($this->wmuPostMaxSize / (1024 * 1024)) . "MB";
        $jsArgs["msgEmptyFile"] = esc_html__("File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.");
        $jsArgs["msgPostIdNotExists"] = esc_html__("Post ID not exists", "wpdiscuz");
        $jsArgs["msgUploadingNotAllowed"] = esc_html__("Sorry, uploading not allowed for this post", "wpdiscuz");
        $jsArgs["msgPermissionDenied"] = esc_html__("You do not have sufficient permissions to perform this action", "wpdiscuz");
        $nonceKey = ($key = get_home_url()) ? md5($key) : "wmu-nonce";
        $jsArgs["wmuSecurity"] = wp_create_nonce($nonceKey);
        $jsArgs["wmuKeyImages"] = self::KEY_IMAGES;
        $jsArgs["wmuSingleImageWidth"] = $this->content["wmuSingleImageWidth"];
        $jsArgs["wmuSingleImageHeight"] = $this->content["wmuSingleImageHeight"];
        return $jsArgs;
    }

    private function initGoodbyeCaptchaField() {
        $this->isGoodbyeCaptchaActive = is_callable(["GdbcWordPressPublicModule", "isCommentsProtectionActivated"]) && GdbcWordPressPublicModule::isCommentsProtectionActivated();
        if ($this->isGoodbyeCaptchaActive) {
            $this->goodbyeCaptchaTocken = GdbcWordPressPublicModule::getInstance()->getTokenFieldHtml();
        }
    }

    public function editorOptions() {
        ob_start();
        ?>
        var wpdiscuzEditorOptions = {
        modules: {
        toolbar: "",
        counter: {
        uniqueID: "",
        maxcount : <?php echo $this->content["commentTextMaxLength"] ? $this->content["commentTextMaxLength"] : 0; ?>,
        mincount : <?php echo $this->content["commentTextMinLength"]; ?>,
        },
        <?php do_action("wpdiscuz_editor_modules"); ?>
        },
        placeholder: '<?php echo get_comments_number() ? $this->phrases["wc_comment_join_text"] : $this->phrases["wc_be_the_first_text"]; ?>',
        theme: 'snow',
        debug: '<?php echo $this->general["loadComboVersion"] || $this->general["loadMinVersion"] ? 'error' : 'warn'; ?>'
        };
        <?php
        return ob_get_clean();
    }

    public function saveAndResetOptionsAndPhrases() {
        if (!empty($_GET["wpd_wizard"]) && ($wizard = absint($_GET["wpd_wizard"])) && !empty($_POST)) {
            check_admin_referer("wpd_wizard_form");
            if ($wizard === 2) {
                $forms = get_posts(["post_type" => "wpdiscuz_form", "post_status" => "publish", "posts_per_page" => -1,]);
                foreach ($forms as $k => $form) {
                    $formMeta = get_post_meta($form->ID, "wpdiscuz_form_general_options", true);
                    $formMeta["layout"] = isset($_POST["layout"]) ? absint($_POST["layout"]) : 1;
                    update_post_meta($form->ID, "wpdiscuz_form_general_options", $formMeta);
                }
                $this->thread_styles["theme"] = isset($_POST["theme"]) ? trim($_POST["theme"]) : "wpd-default";
                $this->updateOptions();
            } else if ($wizard === 3) {
                $this->live["enableBubble"] = isset($_POST["enableBubble"]) ? absint($_POST["enableBubble"]) : 0;
                $this->live["bubbleLiveUpdate"] = isset($_POST["bubbleLiveUpdate"]) ? absint($_POST["bubbleLiveUpdate"]) : 0;
                $this->live["bubbleLocation"] = isset($_POST["bubbleLocation"]) ? trim($_POST["bubbleLocation"]) : "content_left";
                $this->updateOptions();
            } else if ($wizard === 4) {
                $forms = get_posts(["post_type" => "wpdiscuz_form", "post_status" => "publish", "posts_per_page" => -1,]);
                foreach ($forms as $k => $form) {
                    $formMeta = get_post_meta($form->ID, "wpdiscuz_form_general_options", true);
                    $formMeta["enable_post_rating"] = isset($_POST["enable_post_rating"]) ? absint($_POST["enable_post_rating"]) : 1;
                    update_post_meta($form->ID, "wpdiscuz_form_general_options", $formMeta);
                }
            }
        } else {
            $this->resetOptions();
            $this->saveOptions();
            $this->savePhrases();
        }
    }

    public function saveOptions() {
        if (isset($_POST["wc_submit_options"]) && !empty($_POST["wpd_tab"])) {
            if (!current_user_can("manage_options")) {
                die(esc_html_e("Hacker?", "wpdiscuz"));
            }
            check_admin_referer("wc_options_form-" . $_POST["wpd_tab"]);
            if (self::TAB_FORM === $_POST["wpd_tab"]) {
                $this->form["commentFormView"] = isset($_POST[self::TAB_FORM]["commentFormView"]) ? trim($_POST[self::TAB_FORM]["commentFormView"]) : "collapsed";
                $this->form["enableDropAnimation"] = isset($_POST[self::TAB_FORM]["enableDropAnimation"]) ? absint($_POST[self::TAB_FORM]["enableDropAnimation"]) : 0;
                $this->form["richEditor"] = isset($_POST[self::TAB_FORM]["richEditor"]) ? trim($_POST[self::TAB_FORM]["richEditor"]) : "desktop";
                $this->form["boldButton"] = isset($_POST[self::TAB_FORM]["boldButton"]) ? intval($_POST[self::TAB_FORM]["boldButton"]) : 0;
                $this->form["italicButton"] = isset($_POST[self::TAB_FORM]["italicButton"]) ? intval($_POST[self::TAB_FORM]["italicButton"]) : 0;
                $this->form["underlineButton"] = isset($_POST[self::TAB_FORM]["underlineButton"]) ? intval($_POST[self::TAB_FORM]["underlineButton"]) : 0;
                $this->form["strikeButton"] = isset($_POST[self::TAB_FORM]["strikeButton"]) ? intval($_POST[self::TAB_FORM]["strikeButton"]) : 0;
                $this->form["olButton"] = isset($_POST[self::TAB_FORM]["olButton"]) ? intval($_POST[self::TAB_FORM]["olButton"]) : 0;
                $this->form["ulButton"] = isset($_POST[self::TAB_FORM]["ulButton"]) ? intval($_POST[self::TAB_FORM]["ulButton"]) : 0;
                $this->form["blockquoteButton"] = isset($_POST[self::TAB_FORM]["blockquoteButton"]) ? intval($_POST[self::TAB_FORM]["blockquoteButton"]) : 0;
                $this->form["codeblockButton"] = isset($_POST[self::TAB_FORM]["codeblockButton"]) ? intval($_POST[self::TAB_FORM]["codeblockButton"]) : 0;
                $this->form["linkButton"] = isset($_POST[self::TAB_FORM]["linkButton"]) ? intval($_POST[self::TAB_FORM]["linkButton"]) : 0;
                $this->form["sourcecodeButton"] = isset($_POST[self::TAB_FORM]["sourcecodeButton"]) ? intval($_POST[self::TAB_FORM]["sourcecodeButton"]) : 0;
                $this->form["spoilerButton"] = isset($_POST[self::TAB_FORM]["spoilerButton"]) ? intval($_POST[self::TAB_FORM]["spoilerButton"]) : 0;
                $this->form["enableQuickTags"] = isset($_POST[self::TAB_FORM]["enableQuickTags"]) ? intval($_POST[self::TAB_FORM]["enableQuickTags"]) : 0;
                $this->form["commenterNameMinLength"] = isset($_POST[self::TAB_FORM]["commenterNameMinLength"]) && absint($_POST[self::TAB_FORM]["commenterNameMinLength"]) >= 1 ? absint($_POST[self::TAB_FORM]["commenterNameMinLength"]) : 1;
                $this->form["commenterNameMaxLength"] = isset($_POST[self::TAB_FORM]["commenterNameMaxLength"]) && absint($_POST[self::TAB_FORM]["commenterNameMaxLength"]) >= 3 && absint($_POST[self::TAB_FORM]["commenterNameMaxLength"]) <= 50 ? absint($_POST[self::TAB_FORM]["commenterNameMaxLength"]) : 50;
                $this->form["storeCommenterData"] = isset($_POST[self::TAB_FORM]["storeCommenterData"]) && (intval($_POST[self::TAB_FORM]["storeCommenterData"]) || $_POST[self::TAB_FORM]["storeCommenterData"] == 0) ? intval($_POST[self::TAB_FORM]["storeCommenterData"]) : -1;
            } else if (self::TAB_RECAPTCHA === $_POST["wpd_tab"]) {
                $this->recaptcha["siteKey"] = isset($_POST[self::TAB_RECAPTCHA]["siteKey"]) ? trim($_POST[self::TAB_RECAPTCHA]["siteKey"]) : "";
                $this->recaptcha["secretKey"] = isset($_POST[self::TAB_RECAPTCHA]["secretKey"]) ? trim($_POST[self::TAB_RECAPTCHA]["secretKey"]) : "";
                $this->recaptcha["theme"] = isset($_POST[self::TAB_RECAPTCHA]["theme"]) ? trim($_POST[self::TAB_RECAPTCHA]["theme"]) : "light";
                $this->recaptcha["lang"] = isset($_POST[self::TAB_RECAPTCHA]["lang"]) ? trim($_POST[self::TAB_RECAPTCHA]["lang"]) : "";
                $this->recaptcha["requestMethod"] = isset($_POST[self::TAB_RECAPTCHA]["requestMethod"]) ? trim($_POST[self::TAB_RECAPTCHA]["requestMethod"]) : "auto";
                if (empty($_POST[self::TAB_RECAPTCHA]["useV3"])) {
                    if ($this->recaptcha["siteKey"] && $this->recaptcha["secretKey"]) {
                        $this->recaptcha["showForGuests"] = isset($_POST[self::TAB_RECAPTCHA]["showForGuests"]) ? absint($_POST[self::TAB_RECAPTCHA]["showForGuests"]) : 0;
                        $this->recaptcha["showForUsers"] = isset($_POST[self::TAB_RECAPTCHA]["showForUsers"]) ? absint($_POST[self::TAB_RECAPTCHA]["showForUsers"]) : 0;
                        $this->recaptcha["isShowOnSubscribeForm"] = isset($_POST[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"]) ? absint($_POST[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"]) : 0;
                    } else {
                        $this->recaptcha["showForGuests"] = 0;
                        $this->recaptcha["showForUsers"] = 0;
                        $this->recaptcha["isShowOnSubscribeForm"] = 0;
                    }
                } else {
                    $this->recaptcha["showForGuests"] = isset($_POST[self::TAB_RECAPTCHA]["showForGuests"]) ? absint($_POST[self::TAB_RECAPTCHA]["showForGuests"]) : 0;
                    $this->recaptcha["showForUsers"] = isset($_POST[self::TAB_RECAPTCHA]["showForUsers"]) ? absint($_POST[self::TAB_RECAPTCHA]["showForUsers"]) : 0;
                    $this->recaptcha["isShowOnSubscribeForm"] = isset($_POST[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"]) ? absint($_POST[self::TAB_RECAPTCHA]["isShowOnSubscribeForm"]) : 0;
                }
            } else if (self::TAB_LOGIN === $_POST["wpd_tab"]) {
                $this->login["showLoggedInUsername"] = isset($_POST[self::TAB_LOGIN]["showLoggedInUsername"]) ? absint($_POST[self::TAB_LOGIN]["showLoggedInUsername"]) : 0;
                $this->login["showLoginLinkForGuests"] = isset($_POST[self::TAB_LOGIN]["showLoginLinkForGuests"]) ? absint($_POST[self::TAB_LOGIN]["showLoginLinkForGuests"]) : 0;
                $this->login["showActivityTab"] = isset($_POST[self::TAB_LOGIN]["showActivityTab"]) ? absint($_POST[self::TAB_LOGIN]["showActivityTab"]) : 0;
                $this->login["showSubscriptionsTab"] = isset($_POST[self::TAB_LOGIN]["showSubscriptionsTab"]) ? absint($_POST[self::TAB_LOGIN]["showSubscriptionsTab"]) : 0;
                $this->login["showFollowsTab"] = isset($_POST[self::TAB_LOGIN]["showFollowsTab"]) ? absint($_POST[self::TAB_LOGIN]["showFollowsTab"]) : 0;
                $this->login["enableProfileURLs"] = isset($_POST[self::TAB_LOGIN]["enableProfileURLs"]) ? absint($_POST[self::TAB_LOGIN]["enableProfileURLs"]) : 0;
                $this->login["websiteAsProfileUrl"] = isset($_POST[self::TAB_LOGIN]["websiteAsProfileUrl"]) ? absint($_POST[self::TAB_LOGIN]["websiteAsProfileUrl"]) : 0;
                $this->login["isUserByEmail"] = isset($_POST[self::TAB_LOGIN]["isUserByEmail"]) ? absint($_POST[self::TAB_LOGIN]["isUserByEmail"]) : 0;
                $this->login["loginUrl"] = isset($_POST[self::TAB_LOGIN]["loginUrl"]) && ($l = trim($_POST[self::TAB_LOGIN]["loginUrl"])) ? $l : "";
            } else if (self::TAB_SOCIAL === $_POST["wpd_tab"]) {
                $this->social["socialLoginAgreementCheckbox"] = isset($_POST[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"]) ? absint($_POST[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"]) : 0;
                $this->social["socialLoginInSecondaryForm"] = isset($_POST[self::TAB_SOCIAL]["socialLoginInSecondaryForm"]) ? absint($_POST[self::TAB_SOCIAL]["socialLoginInSecondaryForm"]) : 0;
                $this->social["displayIconOnAvatar"] = isset($_POST[self::TAB_SOCIAL]["displayIconOnAvatar"]) ? absint($_POST[self::TAB_SOCIAL]["displayIconOnAvatar"]) : 0;
                // fb
                $this->social["enableFbLogin"] = isset($_POST[self::TAB_SOCIAL]["enableFbLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableFbLogin"]) : 0;
                $this->social["enableFbShare"] = isset($_POST[self::TAB_SOCIAL]["enableFbShare"]) ? absint($_POST[self::TAB_SOCIAL]["enableFbShare"]) : 0;
                $this->social["fbAppID"] = isset($_POST[self::TAB_SOCIAL]["fbAppID"]) ? trim($_POST[self::TAB_SOCIAL]["fbAppID"]) : "";
                $this->social["fbAppSecret"] = isset($_POST[self::TAB_SOCIAL]["fbAppSecret"]) ? trim($_POST[self::TAB_SOCIAL]["fbAppSecret"]) : "";
                $this->social["fbUseOAuth2"] = isset($_POST[self::TAB_SOCIAL]["fbUseOAuth2"]) ? absint($_POST[self::TAB_SOCIAL]["fbUseOAuth2"]) : 0;
                // twitter
                $this->social["enableTwitterLogin"] = isset($_POST[self::TAB_SOCIAL]["enableTwitterLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableTwitterLogin"]) : 0;
                $this->social["enableTwitterShare"] = isset($_POST[self::TAB_SOCIAL]["enableTwitterShare"]) ? absint($_POST[self::TAB_SOCIAL]["enableTwitterShare"]) : 0;
                $this->social["twitterAppID"] = isset($_POST[self::TAB_SOCIAL]["twitterAppID"]) ? trim($_POST[self::TAB_SOCIAL]["twitterAppID"]) : "";
                $this->social["twitterAppSecret"] = isset($_POST[self::TAB_SOCIAL]["twitterAppSecret"]) ? trim($_POST[self::TAB_SOCIAL]["twitterAppSecret"]) : "";
                // google
                $this->social["enableGoogleLogin"] = isset($_POST[self::TAB_SOCIAL]["enableGoogleLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableGoogleLogin"]) : 0;
                $this->social["googleClientID"] = isset($_POST[self::TAB_SOCIAL]["googleClientID"]) ? trim($_POST[self::TAB_SOCIAL]["googleClientID"]) : "";
                $this->social["googleClientSecret"] = isset($_POST[self::TAB_SOCIAL]["googleClientSecret"]) ? trim($_POST[self::TAB_SOCIAL]["googleClientSecret"]) : "";
                // disqus
                $this->social["enableDisqusLogin"] = isset($_POST[self::TAB_SOCIAL]["enableDisqusLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableDisqusLogin"]) : 0;
                $this->social["disqusPublicKey"] = isset($_POST[self::TAB_SOCIAL]["disqusPublicKey"]) ? trim($_POST[self::TAB_SOCIAL]["disqusPublicKey"]) : "";
                $this->social["disqusSecretKey"] = isset($_POST[self::TAB_SOCIAL]["disqusSecretKey"]) ? trim($_POST[self::TAB_SOCIAL]["disqusSecretKey"]) : "";
                // wordpress
                $this->social["enableWordpressLogin"] = isset($_POST[self::TAB_SOCIAL]["enableWordpressLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableWordpressLogin"]) : 0;
                $this->social["wordpressClientID"] = isset($_POST[self::TAB_SOCIAL]["wordpressClientID"]) ? trim($_POST[self::TAB_SOCIAL]["wordpressClientID"]) : "";
                $this->social["wordpressClientSecret"] = isset($_POST[self::TAB_SOCIAL]["wordpressClientSecret"]) ? trim($_POST[self::TAB_SOCIAL]["wordpressClientSecret"]) : "";
                // instagram
                $this->social["enableInstagramLogin"] = isset($_POST[self::TAB_SOCIAL]["enableInstagramLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableInstagramLogin"]) : 0;
                $this->social["instagramAppID"] = isset($_POST[self::TAB_SOCIAL]["instagramAppID"]) ? trim($_POST[self::TAB_SOCIAL]["instagramAppID"]) : "";
                $this->social["instagramAppSecret"] = isset($_POST[self::TAB_SOCIAL]["instagramAppSecret"]) ? trim($_POST[self::TAB_SOCIAL]["instagramAppSecret"]) : "";
                // linkedin
                $this->social["enableLinkedinLogin"] = isset($_POST[self::TAB_SOCIAL]["enableLinkedinLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableLinkedinLogin"]) : 0;
                $this->social["linkedinClientID"] = isset($_POST[self::TAB_SOCIAL]["linkedinClientID"]) ? trim($_POST[self::TAB_SOCIAL]["linkedinClientID"]) : "";
                $this->social["linkedinClientSecret"] = isset($_POST[self::TAB_SOCIAL]["linkedinClientSecret"]) ? trim($_POST[self::TAB_SOCIAL]["linkedinClientSecret"]) : "";
                // whatsapp
                $this->social["enableWhatsappShare"] = isset($_POST[self::TAB_SOCIAL]["enableWhatsappShare"]) ? absint($_POST[self::TAB_SOCIAL]["enableWhatsappShare"]) : 0;
                // yandex
                $this->social["enableYandexLogin"] = isset($_POST[self::TAB_SOCIAL]["enableYandexLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableYandexLogin"]) : 0;
                $this->social["yandexID"] = isset($_POST[self::TAB_SOCIAL]["yandexID"]) ? trim($_POST[self::TAB_SOCIAL]["yandexID"]) : "";
                $this->social["yandexPassword"] = isset($_POST[self::TAB_SOCIAL]["yandexPassword"]) ? trim($_POST[self::TAB_SOCIAL]["yandexPassword"]) : "";
                // mail.ru
                $this->social["enableMailruLogin"] = isset($_POST[self::TAB_SOCIAL]["enableMailruLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableMailruLogin"]) : 0;
                $this->social["mailruClientID"] = isset($_POST[self::TAB_SOCIAL]["mailruClientID"]) ? trim($_POST[self::TAB_SOCIAL]["mailruClientID"]) : "";
                $this->social["mailruClientSecret"] = isset($_POST[self::TAB_SOCIAL]["mailruClientSecret"]) ? trim($_POST[self::TAB_SOCIAL]["mailruClientSecret"]) : "";
                // weibo
                $this->social["enableWeiboLogin"] = isset($_POST[self::TAB_SOCIAL]["enableWeiboLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableWeiboLogin"]) : 0;
                $this->social["weiboKey"] = isset($_POST[self::TAB_SOCIAL]["weiboKey"]) ? trim($_POST[self::TAB_SOCIAL]["weiboKey"]) : "";
                $this->social["weiboSecret"] = isset($_POST[self::TAB_SOCIAL]["weiboSecret"]) ? trim($_POST[self::TAB_SOCIAL]["weiboSecret"]) : "";
                // wechat
                $this->social["enableWechatLogin"] = isset($_POST[self::TAB_SOCIAL]["enableWechatLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableWechatLogin"]) : 0;
                $this->social["wechatAppID"] = isset($_POST[self::TAB_SOCIAL]["wechatAppID"]) ? trim($_POST[self::TAB_SOCIAL]["wechatAppID"]) : "";
                $this->social["wechatSecret"] = isset($_POST[self::TAB_SOCIAL]["wechatSecret"]) ? trim($_POST[self::TAB_SOCIAL]["wechatSecret"]) : "";
                // qq
                $this->social["enableQQLogin"] = isset($_POST[self::TAB_SOCIAL]["enableQQLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableQQLogin"]) : 0;
                $this->social["qqAppID"] = isset($_POST[self::TAB_SOCIAL]["qqAppID"]) ? trim($_POST[self::TAB_SOCIAL]["qqAppID"]) : "";
                $this->social["qqSecret"] = isset($_POST[self::TAB_SOCIAL]["qqSecret"]) ? trim($_POST[self::TAB_SOCIAL]["qqSecret"]) : "";
                // baidu
                $this->social["enableBaiduLogin"] = isset($_POST[self::TAB_SOCIAL]["enableBaiduLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableBaiduLogin"]) : 0;
                $this->social["baiduAppID"] = isset($_POST[self::TAB_SOCIAL]["baiduAppID"]) ? trim($_POST[self::TAB_SOCIAL]["baiduAppID"]) : "";
                $this->social["baiduSecret"] = isset($_POST[self::TAB_SOCIAL]["baiduSecret"]) ? trim($_POST[self::TAB_SOCIAL]["baiduSecret"]) : "";
                // vk
                $this->social["enableVkLogin"] = isset($_POST[self::TAB_SOCIAL]["enableVkLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableVkLogin"]) : 0;
                $this->social["enableVkShare"] = isset($_POST[self::TAB_SOCIAL]["enableVkShare"]) ? absint($_POST[self::TAB_SOCIAL]["enableVkShare"]) : 0;
                $this->social["vkAppID"] = isset($_POST[self::TAB_SOCIAL]["vkAppID"]) ? trim($_POST[self::TAB_SOCIAL]["vkAppID"]) : "";
                $this->social["vkAppSecret"] = isset($_POST[self::TAB_SOCIAL]["vkAppSecret"]) ? trim($_POST[self::TAB_SOCIAL]["vkAppSecret"]) : "";
                // ok
                $this->social["enableOkLogin"] = isset($_POST[self::TAB_SOCIAL]["enableOkLogin"]) ? absint($_POST[self::TAB_SOCIAL]["enableOkLogin"]) : 0;
                $this->social["enableOkShare"] = isset($_POST[self::TAB_SOCIAL]["enableOkShare"]) ? absint($_POST[self::TAB_SOCIAL]["enableOkShare"]) : 0;
                $this->social["okAppID"] = isset($_POST[self::TAB_SOCIAL]["okAppID"]) ? trim($_POST[self::TAB_SOCIAL]["okAppID"]) : "";
                $this->social["okAppKey"] = isset($_POST[self::TAB_SOCIAL]["okAppKey"]) ? trim($_POST[self::TAB_SOCIAL]["okAppKey"]) : "";
                $this->social["okAppSecret"] = isset($_POST[self::TAB_SOCIAL]["okAppSecret"]) ? trim($_POST[self::TAB_SOCIAL]["okAppSecret"]) : "";
            } else if (self::TAB_RATING === $_POST["wpd_tab"]) {
                $this->rating["enablePostRatingSchema"] = isset($_POST[self::TAB_RATING]["enablePostRatingSchema"]) ? absint($_POST[self::TAB_RATING]["enablePostRatingSchema"]) : 0;
                $this->rating["displayRatingOnPost"] = isset($_POST[self::TAB_RATING]["displayRatingOnPost"]) ? $_POST[self::TAB_RATING]["displayRatingOnPost"] : [];
                $this->rating["ratingCssOnNoneSingular"] = isset($_POST[self::TAB_RATING]["ratingCssOnNoneSingular"]) ? absint($_POST[self::TAB_RATING]["ratingCssOnNoneSingular"]) : 0;
                $this->rating["ratingHoverColor"] = isset($_POST[self::TAB_RATING]["ratingHoverColor"]) ? $_POST[self::TAB_RATING]["ratingHoverColor"] : "#FFED85";
                $this->rating["ratingInactiveColor"] = isset($_POST[self::TAB_RATING]["ratingInactiveColor"]) ? $_POST[self::TAB_RATING]["ratingInactiveColor"] : "#DDDDDD";
                $this->rating["ratingActiveColor"] = isset($_POST[self::TAB_RATING]["ratingActiveColor"]) ? $_POST[self::TAB_RATING]["ratingActiveColor"] : "#FFD700";
            } else if (self::TAB_THREAD_DISPLAY === $_POST["wpd_tab"]) {
                $this->thread_display["firstLoadWithAjax"] = isset($_POST[self::TAB_THREAD_DISPLAY]["firstLoadWithAjax"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["firstLoadWithAjax"]) : 0;
                $this->thread_display["commentListLoadType"] = isset($_POST[self::TAB_THREAD_DISPLAY]["commentListLoadType"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["commentListLoadType"]) : 0;
                $this->thread_display["isLoadOnlyParentComments"] = isset($_POST[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"]) : 0;
                $this->thread_display["showReactedFilterButton"] = isset($_POST[self::TAB_THREAD_DISPLAY]["showReactedFilterButton"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["showReactedFilterButton"]) : 0;
                $this->thread_display["showHottestFilterButton"] = isset($_POST[self::TAB_THREAD_DISPLAY]["showHottestFilterButton"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["showHottestFilterButton"]) : 0;
                $this->thread_display["showSortingButtons"] = isset($_POST[self::TAB_THREAD_DISPLAY]["showSortingButtons"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["showSortingButtons"]) : 0;
                $this->thread_display["mostVotedByDefault"] = isset($_POST[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"]) : 0;
                $this->thread_display["reverseChildren"] = isset($_POST[self::TAB_THREAD_DISPLAY]["reverseChildren"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["reverseChildren"]) : 0;
                $this->thread_display["highlightUnreadComments"] = isset($_POST[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"]) : 0;
                $this->thread_display["scrollToComment"] = isset($_POST[self::TAB_THREAD_DISPLAY]["scrollToComment"]) ? absint($_POST[self::TAB_THREAD_DISPLAY]["scrollToComment"]) : 0;
                $this->thread_display["orderCommentsBy"] = isset($_POST[self::TAB_THREAD_DISPLAY]["orderCommentsBy"]) && ($o = trim($_POST[self::TAB_THREAD_DISPLAY]["orderCommentsBy"])) && in_array($o, ["comment_ID", "comment_date_gmt"]) ? $o : "comment_ID";
            } else if (self::TAB_THREAD_LAYOUTS === $_POST["wpd_tab"]) {
                $this->thread_layouts["showCommentLink"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["showCommentLink"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["showCommentLink"]) : 0;
                $this->thread_layouts["showCommentDate"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["showCommentDate"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["showCommentDate"]) : 0;
                $this->thread_layouts["showVotingButtons"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["showVotingButtons"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["showVotingButtons"]) : 0;
                $this->thread_layouts["votingButtonsIcon"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"]) ? $_POST[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"] : "fa-plus|fa-minus";
                $this->thread_layouts["votingButtonsStyle"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"]) : 0;
                $this->thread_layouts["enableDislikeButton"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["enableDislikeButton"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["enableDislikeButton"]) : 0;
                $this->thread_layouts["isGuestCanVote"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"]) : 0;
                $this->thread_layouts["highlightVotingButtons"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["highlightVotingButtons"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["highlightVotingButtons"]) : 0;
                $this->thread_layouts["showAvatars"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["showAvatars"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["showAvatars"]) : 0;
                $this->thread_layouts["defaultAvatarUrlForUser"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForUser"]) ? trim($_POST[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForUser"]) : "";
                $this->thread_layouts["defaultAvatarUrlForGuest"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForGuest"]) ? trim($_POST[self::TAB_THREAD_LAYOUTS]["defaultAvatarUrlForGuest"]) : "";
                $this->thread_layouts["changeAvatarsEverywhere"] = isset($_POST[self::TAB_THREAD_LAYOUTS]["changeAvatarsEverywhere"]) ? absint($_POST[self::TAB_THREAD_LAYOUTS]["changeAvatarsEverywhere"]) : 0;
            } else if (self::TAB_THREAD_STYLES === $_POST["wpd_tab"]) {
                $this->thread_styles["theme"] = isset($_POST[self::TAB_THREAD_STYLES]["theme"]) ? trim($_POST[self::TAB_THREAD_STYLES]["theme"]) : "wpd-default";
                $this->thread_styles["primaryColor"] = isset($_POST[self::TAB_THREAD_STYLES]["primaryColor"]) ? $_POST[self::TAB_THREAD_STYLES]["primaryColor"] : "#00B38F";
                $this->thread_styles["newLoadedCommentBGColor"] = isset($_POST[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"]) ? $_POST[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"] : "#FFFAD6";
                $this->thread_styles["primaryButtonColor"] = isset($_POST[self::TAB_THREAD_STYLES]["primaryButtonColor"]) ? $_POST[self::TAB_THREAD_STYLES]["primaryButtonColor"] : "#FFFFFF";
                $this->thread_styles["primaryButtonBG"] = isset($_POST[self::TAB_THREAD_STYLES]["primaryButtonBG"]) ? $_POST[self::TAB_THREAD_STYLES]["primaryButtonBG"] : "#07B290";
                $this->thread_styles["bubbleColors"] = isset($_POST[self::TAB_THREAD_STYLES]["bubbleColors"]) ? $_POST[self::TAB_THREAD_STYLES]["bubbleColors"] : "#1DB99A";
                $this->thread_styles["inlineFeedbackColors"] = isset($_POST[self::TAB_THREAD_STYLES]["inlineFeedbackColors"]) ? $_POST[self::TAB_THREAD_STYLES]["inlineFeedbackColors"] : "#1DB99A";
                $this->thread_styles["defaultCommentAreaBG"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentAreaBG"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentAreaBG"] : "";
                $this->thread_styles["defaultCommentTextColor"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentTextColor"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentTextColor"] : "#777777";
                $this->thread_styles["defaultCommentFieldsBG"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsBG"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsBG"] : "";
                $this->thread_styles["defaultCommentFieldsBorderColor"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsBorderColor"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsBorderColor"] : "#DDDDDD";
                $this->thread_styles["defaultCommentFieldsTextColor"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsTextColor"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsTextColor"] : "#777777";
                $this->thread_styles["defaultCommentFieldsPlaceholderColor"] = isset($_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsPlaceholderColor"]) ? $_POST[self::TAB_THREAD_STYLES]["defaultCommentFieldsPlaceholderColor"] : "";
                $this->thread_styles["darkCommentAreaBG"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentAreaBG"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentAreaBG"] : "#111111";
                $this->thread_styles["darkCommentTextColor"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentTextColor"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentTextColor"] : "#CCCCCC";
                $this->thread_styles["darkCommentFieldsBG"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsBG"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsBG"] : "#999999";
                $this->thread_styles["darkCommentFieldsBorderColor"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsBorderColor"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsBorderColor"] : "#D1D1D1";
                $this->thread_styles["darkCommentFieldsTextColor"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsTextColor"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsTextColor"] : "#000000";
                $this->thread_styles["darkCommentFieldsPlaceholderColor"] = isset($_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsPlaceholderColor"]) ? $_POST[self::TAB_THREAD_STYLES]["darkCommentFieldsPlaceholderColor"] : "#DDDDDD";
                $this->thread_styles["commentTextSize"] = isset($_POST[self::TAB_THREAD_STYLES]["commentTextSize"]) ? $_POST[self::TAB_THREAD_STYLES]["commentTextSize"] : "14px";
                $this->thread_styles["enableFontAwesome"] = isset($_POST[self::TAB_THREAD_STYLES]["enableFontAwesome"]) ? absint($_POST[self::TAB_THREAD_STYLES]["enableFontAwesome"]) : 0;
                $this->thread_styles["customCss"] = isset($_POST[self::TAB_THREAD_STYLES]["customCss"]) ? $_POST[self::TAB_THREAD_STYLES]["customCss"] : ".comments-area{width:auto;}";
            } else if (self::TAB_SUBSCRIPTION === $_POST["wpd_tab"]) {
                $this->subscription["enableUserMentioning"] = isset($_POST[self::TAB_SUBSCRIPTION]["enableUserMentioning"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["enableUserMentioning"]) : 0;
                $this->subscription["sendMailToMentionedUsers"] = isset($_POST[self::TAB_SUBSCRIPTION]["sendMailToMentionedUsers"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["sendMailToMentionedUsers"]) : 0;
                $this->subscription["isNotifyOnCommentApprove"] = isset($_POST[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"]) : 0;
                $this->subscription["enableMemberConfirm"] = isset($_POST[self::TAB_SUBSCRIPTION]["enableMemberConfirm"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["enableMemberConfirm"]) : 0;
                $this->subscription["enableGuestsConfirm"] = isset($_POST[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"]) : 0;
                $this->subscription["subscriptionType"] = isset($_POST[self::TAB_SUBSCRIPTION]["subscriptionType"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["subscriptionType"]) : 1;
                $this->subscription["showReplyCheckbox"] = isset($_POST[self::TAB_SUBSCRIPTION]["showReplyCheckbox"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["showReplyCheckbox"]) : 0;
                $this->subscription["isReplyDefaultChecked"] = isset($_POST[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"]) : 0;
                $this->subscription["usePostmaticForCommentNotification"] = isset($_POST[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"]) : 0;
                $this->subscription["isFollowActive"] = isset($_POST[self::TAB_SUBSCRIPTION]["isFollowActive"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["isFollowActive"]) : 0;
                $this->subscription["disableFollowConfirmForUsers"] = isset($_POST[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"]) ? absint($_POST[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"]) : 0;
            } else if (self::TAB_LABELS === $_POST["wpd_tab"]) {
                $emptyRolesArray = array_combine(array_keys($this->labels["blogRoleLabels"]), array_pad([], count($this->labels["blogRoleLabels"]), 0));
                $this->labels["blogRoleLabels"] = isset($_POST[self::TAB_LABELS]["blogRoleLabels"]) ? wp_parse_args($_POST[self::TAB_LABELS]["blogRoleLabels"], $emptyRolesArray) : $emptyRolesArray;
                $this->labels["blogRoles"] = isset($_POST[self::TAB_LABELS]["blogRoles"]) ? wp_parse_args($_POST[self::TAB_LABELS]["blogRoles"], $this->labels["blogRoles"]) : $this->labels["blogRoles"];
            } else if (self::TAB_MODERATION === $_POST["wpd_tab"]) {
                $this->moderation["commentEditableTime"] = isset($_POST[self::TAB_MODERATION]["commentEditableTime"]) ? $_POST[self::TAB_MODERATION]["commentEditableTime"] : 900;
                $this->moderation["enableEditingWhenHaveReplies"] = isset($_POST[self::TAB_MODERATION]["enableEditingWhenHaveReplies"]) ? absint($_POST[self::TAB_MODERATION]["enableEditingWhenHaveReplies"]) : 0;
                $this->moderation["displayEditingInfo"] = isset($_POST[self::TAB_MODERATION]["displayEditingInfo"]) ? absint($_POST[self::TAB_MODERATION]["displayEditingInfo"]) : 0;
                $this->moderation["enableStickButton"] = isset($_POST[self::TAB_MODERATION]["enableStickButton"]) ? absint($_POST[self::TAB_MODERATION]["enableStickButton"]) : 0;
                $this->moderation["enableCloseButton"] = isset($_POST[self::TAB_MODERATION]["enableCloseButton"]) ? absint($_POST[self::TAB_MODERATION]["enableCloseButton"]) : 0;
                $this->moderation["restrictCommentingPerUser"] = isset($_POST[self::TAB_MODERATION]["restrictCommentingPerUser"]) ? trim($_POST[self::TAB_MODERATION]["restrictCommentingPerUser"]) : "disable";
                $this->moderation["commentRestrictionType"] = isset($_POST[self::TAB_MODERATION]["commentRestrictionType"]) ? trim($_POST[self::TAB_MODERATION]["commentRestrictionType"]) : "both";
                $this->moderation["userCommentsLimit"] = isset($_POST[self::TAB_MODERATION]["userCommentsLimit"]) ? absint($_POST[self::TAB_MODERATION]["userCommentsLimit"]) : 1;
            } else if (self::TAB_CONTENT === $_POST["wpd_tab"]) {
                $this->content["commentTextMinLength"] = isset($_POST[self::TAB_CONTENT]["commentTextMinLength"]) && absint($_POST[self::TAB_CONTENT]["commentTextMinLength"]) > 0 ? absint($_POST[self::TAB_CONTENT]["commentTextMinLength"]) : 1;
                $this->content["commentTextMaxLength"] = isset($_POST[self::TAB_CONTENT]["commentTextMaxLength"]) && absint($_POST[self::TAB_CONTENT]["commentTextMaxLength"]) > 0 ? absint($_POST[self::TAB_CONTENT]["commentTextMaxLength"]) : "";
                $this->content["enableImageConversion"] = isset($_POST[self::TAB_CONTENT]["enableImageConversion"]) ? absint($_POST[self::TAB_CONTENT]["enableImageConversion"]) : 0;
                $this->content["enableShortcodes"] = isset($_POST[self::TAB_CONTENT]["enableShortcodes"]) ? absint($_POST[self::TAB_CONTENT]["enableShortcodes"]) : 0;
                $this->content["commentReadMoreLimit"] = isset($_POST[self::TAB_CONTENT]["commentReadMoreLimit"]) && absint($_POST[self::TAB_CONTENT]["commentReadMoreLimit"]) >= 0 ? absint($_POST[self::TAB_CONTENT]["commentReadMoreLimit"]) : 0;
                $this->content["wmuIsEnabled"] = isset($_POST[self::TAB_CONTENT]["wmuIsEnabled"]) ? absint($_POST[self::TAB_CONTENT]["wmuIsEnabled"]) : 0;
                $this->content["wmuIsGuestAllowed"] = isset($_POST[self::TAB_CONTENT]["wmuIsGuestAllowed"]) ? absint($_POST[self::TAB_CONTENT]["wmuIsGuestAllowed"]) : 0;
                $this->content["wmuIsLightbox"] = isset($_POST[self::TAB_CONTENT]["wmuIsLightbox"]) ? absint($_POST[self::TAB_CONTENT]["wmuIsLightbox"]) : 0;
                $this->content["wmuMimeTypes"] = isset($_POST[self::TAB_CONTENT]["wmuMimeTypes"]) ? $_POST[self::TAB_CONTENT]["wmuMimeTypes"] : [];
                $this->content["wmuMaxFileSize"] = isset($_POST[self::TAB_CONTENT]["wmuMaxFileSize"]) ? $_POST[self::TAB_CONTENT]["wmuMaxFileSize"] : $this->wmuUploadMaxFileSize / (1024 * 1024);
                $this->content["wmuIsShowFilesDashboard"] = isset($_POST[self::TAB_CONTENT]["wmuIsShowFilesDashboard"]) ? absint($_POST[self::TAB_CONTENT]["wmuIsShowFilesDashboard"]) : 0;
                $this->content["wmuSingleImageWidth"] = isset($_POST[self::TAB_CONTENT]["wmuSingleImageWidth"]) && ($v = trim($_POST[self::TAB_CONTENT]["wmuSingleImageWidth"])) && ($v == "auto" || ($v = absint($v))) ? $v : 320;
                $this->content["wmuSingleImageHeight"] = isset($_POST[self::TAB_CONTENT]["wmuSingleImageHeight"]) && ($v = trim($_POST[self::TAB_CONTENT]["wmuSingleImageHeight"])) && ($v == "auto" || ($v = absint($v))) ? $v : 200;
                $this->content["wmuImageSizes"] = isset($_POST[self::TAB_CONTENT]["wmuImageSizes"]) && is_array($_POST[self::TAB_CONTENT]["wmuImageSizes"]) && ($sizes = array_filter($_POST[self::TAB_CONTENT]["wmuImageSizes"])) ? $sizes : [];
            } else if (self::TAB_LIVE === $_POST["wpd_tab"]) {
                $this->live["enableBubble"] = isset($_POST[self::TAB_LIVE]["enableBubble"]) ? absint($_POST[self::TAB_LIVE]["enableBubble"]) : 0;
                $this->live["bubbleLiveUpdate"] = isset($_POST[self::TAB_LIVE]["bubbleLiveUpdate"]) ? absint($_POST[self::TAB_LIVE]["bubbleLiveUpdate"]) : 0;
                $this->live["bubbleLocation"] = isset($_POST[self::TAB_LIVE]["bubbleLocation"]) ? trim($_POST[self::TAB_LIVE]["bubbleLocation"]) : "content_left";
                $this->live["bubbleShowNewCommentMessage"] = isset($_POST[self::TAB_LIVE]["bubbleShowNewCommentMessage"]) ? absint($_POST[self::TAB_LIVE]["bubbleShowNewCommentMessage"]) : 0;
                $this->live["bubbleHintTimeout"] = isset($_POST[self::TAB_LIVE]["bubbleHintTimeout"]) ? absint($_POST[self::TAB_LIVE]["bubbleHintTimeout"]) : 0;
                $this->live["bubbleHintHideTimeout"] = isset($_POST[self::TAB_LIVE]["bubbleHintHideTimeout"]) ? absint($_POST[self::TAB_LIVE]["bubbleHintHideTimeout"]) : 0;
                $this->live["commentListUpdateType"] = isset($_POST[self::TAB_LIVE]["commentListUpdateType"]) ? absint($_POST[self::TAB_LIVE]["commentListUpdateType"]) : 0;
                $this->live["liveUpdateGuests"] = isset($_POST[self::TAB_LIVE]["liveUpdateGuests"]) ? absint($_POST[self::TAB_LIVE]["liveUpdateGuests"]) : 0;
                $this->live["commentListUpdateTimer"] = isset($_POST[self::TAB_LIVE]["commentListUpdateTimer"]) ? absint($_POST[self::TAB_LIVE]["commentListUpdateTimer"]) : 30;
            } else if (self::TAB_INLINE === $_POST["wpd_tab"]) {
                $this->inline["showInlineFilterButton"] = isset($_POST[self::TAB_INLINE]["showInlineFilterButton"]) ? absint($_POST[self::TAB_INLINE]["showInlineFilterButton"]) : 0;
                $this->inline["inlineFeedbackAttractionType"] = isset($_POST[self::TAB_INLINE]["inlineFeedbackAttractionType"]) ? trim($_POST[self::TAB_INLINE]["inlineFeedbackAttractionType"]) : "disable";
            } else if (self::TAB_GENERAL === $_POST["wpd_tab"]) {
                $this->general["isEnableOnHome"] = isset($_POST[self::TAB_GENERAL]["isEnableOnHome"]) ? absint($_POST[self::TAB_GENERAL]["isEnableOnHome"]) : 0;
                $this->general["isNativeAjaxEnabled"] = isset($_POST[self::TAB_GENERAL]["isNativeAjaxEnabled"]) ? absint($_POST[self::TAB_GENERAL]["isNativeAjaxEnabled"]) : 0;
                $this->general["loadComboVersion"] = isset($_POST[self::TAB_GENERAL]["loadComboVersion"]) ? absint($_POST[self::TAB_GENERAL]["loadComboVersion"]) : 0;
                $this->general["loadMinVersion"] = isset($_POST[self::TAB_GENERAL]["loadMinVersion"]) ? absint($_POST[self::TAB_GENERAL]["loadMinVersion"]) : 0;
                $this->general["commentLinkFilter"] = isset($_POST[self::TAB_GENERAL]["commentLinkFilter"]) ? absint($_POST[self::TAB_GENERAL]["commentLinkFilter"]) : 1;
                $this->general["simpleCommentDate"] = isset($_POST[self::TAB_GENERAL]["simpleCommentDate"]) ? absint($_POST[self::TAB_GENERAL]["simpleCommentDate"]) : 0;
                $this->general["dateDiffFormat"] = isset($_POST[self::TAB_GENERAL]["dateDiffFormat"]) ? trim($_POST[self::TAB_GENERAL]["dateDiffFormat"]) : "";
                $this->general["isUsePoMo"] = isset($_POST[self::TAB_GENERAL]["isUsePoMo"]) ? absint($_POST[self::TAB_GENERAL]["isUsePoMo"]) : 0;
                $this->general["showPluginPoweredByLink"] = isset($_POST[self::TAB_GENERAL]["showPluginPoweredByLink"]) ? absint($_POST[self::TAB_GENERAL]["showPluginPoweredByLink"]) : 0;
                $this->general["isGravatarCacheEnabled"] = isset($_POST[self::TAB_GENERAL]["isGravatarCacheEnabled"]) ? absint($_POST[self::TAB_GENERAL]["isGravatarCacheEnabled"]) : 0;
                $this->general["gravatarCacheMethod"] = isset($_POST[self::TAB_GENERAL]["gravatarCacheMethod"]) ? trim($_POST[self::TAB_GENERAL]["gravatarCacheMethod"]) : "cronjob";
                $this->general["gravatarCacheTimeout"] = isset($_POST[self::TAB_GENERAL]["gravatarCacheTimeout"]) ? absint($_POST[self::TAB_GENERAL]["gravatarCacheTimeout"]) : 10;
                $this->general["redirectPage"] = isset($_POST[self::TAB_GENERAL]["redirectPage"]) ? absint($_POST[self::TAB_GENERAL]["redirectPage"]) : 0;
            }
            do_action("wpdiscuz_save_options");
            $this->updateOptions();
        }
    }

    public function savePhrases() {
        if (isset($_POST["wc_submit_phrases"])) {
            if (!current_user_can("manage_options")) {
                die(esc_html_e("Hacker?", "wpdiscuz"));
            }
            check_admin_referer("wc_phrases_form");
            $this->phrases["wc_be_the_first_text"] = esc_attr($_POST["wc_be_the_first_text"]);
            $this->phrases["wc_comment_start_text"] = esc_attr($_POST["wc_comment_start_text"]);
            $this->phrases["wc_comment_join_text"] = esc_attr($_POST["wc_comment_join_text"]);
            $this->phrases["wc_content_and_settings"] = esc_attr($_POST["wc_content_and_settings"]);
            $this->phrases["wc_hottest_comment_thread"] = esc_attr($_POST["wc_hottest_comment_thread"]);
            $this->phrases["wc_most_reacted_comment"] = esc_attr($_POST["wc_most_reacted_comment"]);
            $this->phrases["wc_inline_comments"] = esc_attr($_POST["wc_inline_comments"]);
            $this->phrases["wc_email_text"] = esc_attr($_POST["wc_email_text"]);
            $this->phrases["wc_subscribe_anchor"] = esc_attr($_POST["wc_subscribe_anchor"]);
            $this->phrases["wc_notify_of"] = esc_attr($_POST["wc_notify_of"]);
            $this->phrases["wc_notify_on_new_comment"] = esc_attr($_POST["wc_notify_on_new_comment"]);
            $this->phrases["wc_notify_on_all_new_reply"] = esc_attr($_POST["wc_notify_on_all_new_reply"]);
            $this->phrases["wc_notify_on_new_reply"] = esc_attr($_POST["wc_notify_on_new_reply"]);
            $this->phrases["wc_sort_by"] = esc_attr($_POST["wc_sort_by"]);
            $this->phrases["wc_newest"] = esc_attr($_POST["wc_newest"]);
            $this->phrases["wc_oldest"] = esc_attr($_POST["wc_oldest"]);
            $this->phrases["wc_most_voted"] = esc_attr($_POST["wc_most_voted"]);
            $this->phrases["wc_load_more_submit_text"] = esc_attr($_POST["wc_load_more_submit_text"]);
            $this->phrases["wc_load_rest_comments_submit_text"] = esc_attr($_POST["wc_load_rest_comments_submit_text"]);
            $this->phrases["wc_reply_text"] = esc_attr($_POST["wc_reply_text"]);
            $this->phrases["wc_share_text"] = esc_attr($_POST["wc_share_text"]);
            $this->phrases["wc_edit_text"] = esc_attr($_POST["wc_edit_text"]);
            $this->phrases["wc_share_facebook"] = esc_attr($_POST["wc_share_facebook"]);
            $this->phrases["wc_share_twitter"] = esc_attr($_POST["wc_share_twitter"]);
            $this->phrases["wc_share_whatsapp"] = esc_attr($_POST["wc_share_whatsapp"]);
            $this->phrases["wc_share_vk"] = esc_attr($_POST["wc_share_vk"]);
            $this->phrases["wc_share_ok"] = esc_attr($_POST["wc_share_ok"]);
            $this->phrases["wc_hide_replies_text"] = esc_attr($_POST["wc_hide_replies_text"]);
            $this->phrases["wc_show_replies_text"] = esc_attr($_POST["wc_show_replies_text"]);
            $this->phrases["wc_email_subject"] = esc_attr($_POST["wc_email_subject"]);
            $this->phrases["wc_email_message"] = wpautop($_POST["wc_email_message"]);
            $this->phrases["wc_all_comment_new_reply_subject"] = esc_attr($_POST["wc_all_comment_new_reply_subject"]);
            $this->phrases["wc_all_comment_new_reply_message"] = wpautop($_POST["wc_all_comment_new_reply_message"]);
            $this->phrases["wc_new_reply_email_subject"] = esc_attr($_POST["wc_new_reply_email_subject"]);
            $this->phrases["wc_new_reply_email_message"] = wpautop($_POST["wc_new_reply_email_message"]);
            $this->phrases["wc_subscribed_on_comment"] = esc_attr($_POST["wc_subscribed_on_comment"]);
            $this->phrases["wc_subscribed_on_all_comment"] = esc_attr($_POST["wc_subscribed_on_all_comment"]);
            $this->phrases["wc_subscribed_on_post"] = esc_attr($_POST["wc_subscribed_on_post"]);
            $this->phrases["wc_unsubscribe"] = esc_attr($_POST["wc_unsubscribe"]);
            $this->phrases["wc_ignore_subscription"] = esc_attr($_POST["wc_ignore_subscription"]);
            $this->phrases["wc_unsubscribe_message"] = esc_attr($_POST["wc_unsubscribe_message"]);
            $this->phrases["wc_subscribe_message"] = esc_attr($_POST["wc_subscribe_message"]);
            $this->phrases["wc_confirm_email"] = esc_attr($_POST["wc_confirm_email"]);
            $this->phrases["wc_comfirm_success_message"] = esc_attr($_POST["wc_comfirm_success_message"]);
            $this->phrases["wc_confirm_email_subject"] = esc_attr($_POST["wc_confirm_email_subject"]);
            $this->phrases["wc_confirm_email_message"] = wpautop($_POST["wc_confirm_email_message"]);
            $this->phrases["wc_error_empty_text"] = esc_attr($_POST["wc_error_empty_text"]);
            $this->phrases["wc_error_email_text"] = esc_attr($_POST["wc_error_email_text"]);
            $this->phrases["wc_error_url_text"] = esc_attr($_POST["wc_error_url_text"]);
            $this->phrases["wc_year_text"] = esc_attr($_POST["wc_year_text"]);
            $this->phrases["wc_year_text_plural"] = esc_attr($_POST["wc_year_text_plural"]);
            $this->phrases["wc_month_text"] = esc_attr($_POST["wc_month_text"]);
            $this->phrases["wc_month_text_plural"] = esc_attr($_POST["wc_month_text_plural"]);
            $this->phrases["wc_day_text"] = esc_attr($_POST["wc_day_text"]);
            $this->phrases["wc_day_text_plural"] = esc_attr($_POST["wc_day_text_plural"]);
            $this->phrases["wc_hour_text"] = esc_attr($_POST["wc_hour_text"]);
            $this->phrases["wc_hour_text_plural"] = esc_attr($_POST["wc_hour_text_plural"]);
            $this->phrases["wc_minute_text"] = esc_attr($_POST["wc_minute_text"]);
            $this->phrases["wc_minute_text_plural"] = esc_attr($_POST["wc_minute_text_plural"]);
            $this->phrases["wc_second_text"] = esc_attr($_POST["wc_second_text"]);
            $this->phrases["wc_second_text_plural"] = esc_attr($_POST["wc_second_text_plural"]);
            $this->phrases["wc_right_now_text"] = esc_attr($_POST["wc_right_now_text"]);
            $this->phrases["wc_ago_text"] = esc_attr($_POST["wc_ago_text"]);
            $this->phrases["wc_you_must_be_text"] = esc_attr($_POST["wc_you_must_be_text"]);
            $this->phrases["wc_logged_in_as"] = esc_attr($_POST["wc_logged_in_as"]);
            $this->phrases["wc_log_out"] = esc_attr($_POST["wc_log_out"]);
            $this->phrases["wc_log_in"] = esc_attr($_POST["wc_log_in"]);
            $this->phrases["wc_login_please"] = esc_attr($_POST["wc_login_please"]);
            $this->phrases["wc_logged_in_text"] = esc_attr($_POST["wc_logged_in_text"]);
            $this->phrases["wc_to_post_comment_text"] = esc_attr($_POST["wc_to_post_comment_text"]);
            $this->phrases["wc_vote_counted"] = esc_attr($_POST["wc_vote_counted"]);
            $this->phrases["wc_vote_up"] = esc_attr($_POST["wc_vote_up"]);
            $this->phrases["wc_vote_down"] = esc_attr($_POST["wc_vote_down"]);
            $this->phrases["wc_awaiting_for_approval"] = esc_attr($_POST["wc_awaiting_for_approval"]);
            $this->phrases["wc_vote_only_one_time"] = esc_attr($_POST["wc_vote_only_one_time"]);
            $this->phrases["wc_voting_error"] = esc_attr($_POST["wc_voting_error"]);
            $this->phrases["wc_self_vote"] = esc_attr($_POST["wc_self_vote"]);
            $this->phrases["wc_deny_voting_from_same_ip"] = esc_attr($_POST["wc_deny_voting_from_same_ip"]);
            $this->phrases["wc_login_to_vote"] = esc_attr($_POST["wc_login_to_vote"]);
            $this->phrases["wc_invalid_captcha"] = esc_attr($_POST["wc_invalid_captcha"]);
            $this->phrases["wc_invalid_field"] = esc_attr($_POST["wc_invalid_field"]);
            $this->phrases["wc_comment_not_updated"] = esc_attr($_POST["wc_comment_not_updated"]);
            $this->phrases["wc_comment_edit_not_possible"] = esc_attr($_POST["wc_comment_edit_not_possible"]);
            $this->phrases["wc_comment_not_edited"] = esc_attr($_POST["wc_comment_not_edited"]);
            $this->phrases["wc_comment_edit_save_button"] = esc_attr($_POST["wc_comment_edit_save_button"]);
            $this->phrases["wc_comment_edit_cancel_button"] = esc_attr($_POST["wc_comment_edit_cancel_button"]);
            $this->phrases["wc_msg_input_min_length"] = esc_attr($_POST["wc_msg_input_min_length"]);
            $this->phrases["wc_msg_input_max_length"] = esc_attr($_POST["wc_msg_input_max_length"]);
            $this->phrases["wc_read_more"] = esc_attr($_POST["wc_read_more"]);
            $this->phrases["wc_anonymous"] = esc_attr($_POST["wc_anonymous"]);
            $this->phrases["wc_msg_required_fields"] = esc_attr($_POST["wc_msg_required_fields"]);
            $this->phrases["wc_connect_with"] = esc_attr($_POST["wc_connect_with"]);
            $this->phrases["wc_subscribed_to"] = esc_attr($_POST["wc_subscribed_to"]);
            $this->phrases["wc_form_subscription_submit"] = esc_attr($_POST["wc_form_subscription_submit"]);
            $this->phrases["wc_comment_approved_email_subject"] = esc_attr($_POST["wc_comment_approved_email_subject"]);
            $this->phrases["wc_comment_approved_email_message"] = wpautop($_POST["wc_comment_approved_email_message"]);
            $this->phrases["wc_roles_cannot_comment_message"] = esc_attr($_POST["wc_roles_cannot_comment_message"]);
            $this->phrases["wc_stick_comment_btn_title"] = esc_attr($_POST["wc_stick_comment_btn_title"]);
            $this->phrases["wc_stick_comment"] = esc_attr($_POST["wc_stick_comment"]);
            $this->phrases["wc_unstick_comment"] = esc_attr($_POST["wc_unstick_comment"]);
            $this->phrases["wc_sticky_comment_icon_title"] = esc_attr($_POST["wc_sticky_comment_icon_title"]);
            $this->phrases["wc_close_comment_btn_title"] = esc_attr($_POST["wc_close_comment_btn_title"]);
            $this->phrases["wc_close_comment"] = esc_attr($_POST["wc_close_comment"]);
            $this->phrases["wc_open_comment"] = esc_attr($_POST["wc_open_comment"]);
            $this->phrases["wc_closed_comment_icon_title"] = esc_attr($_POST["wc_closed_comment_icon_title"]);
            $this->phrases["wc_social_login_agreement_label"] = esc_attr($_POST["wc_social_login_agreement_label"]);
            $this->phrases["wc_social_login_agreement_desc"] = esc_attr($_POST["wc_social_login_agreement_desc"]);
            $this->phrases["wc_agreement_button_disagree"] = esc_attr($_POST["wc_agreement_button_disagree"]);
            $this->phrases["wc_agreement_button_agree"] = esc_attr($_POST["wc_agreement_button_agree"]);
            $this->phrases["wc_content_and_settings"] = esc_attr($_POST["wc_content_and_settings"]);
            $this->phrases["wc_user_settings_activity"] = esc_attr($_POST["wc_user_settings_activity"]);
            $this->phrases["wc_user_settings_subscriptions"] = esc_attr($_POST["wc_user_settings_subscriptions"]);
            $this->phrases["wc_user_settings_follows"] = esc_attr($_POST["wc_user_settings_follows"]);
            $this->phrases["wc_user_settings_response_to"] = esc_attr($_POST["wc_user_settings_response_to"]);
            $this->phrases["wc_user_settings_email_me_delete_links"] = esc_attr($_POST["wc_user_settings_email_me_delete_links"]);
            $this->phrases["wc_user_settings_email_me_delete_links_desc"] = esc_attr($_POST["wc_user_settings_email_me_delete_links_desc"]);
            $this->phrases["wc_user_settings_no_data"] = esc_attr($_POST["wc_user_settings_no_data"]);
            $this->phrases["wc_user_settings_request_deleting_comments"] = esc_attr($_POST["wc_user_settings_request_deleting_comments"]);
            $this->phrases["wc_user_settings_cancel_subscriptions"] = esc_attr($_POST["wc_user_settings_cancel_subscriptions"]);
            $this->phrases["wc_user_settings_clear_cookie"] = esc_attr($_POST["wc_user_settings_clear_cookie"]);
            $this->phrases["wc_user_settings_delete_links"] = esc_attr($_POST["wc_user_settings_delete_links"]);
            $this->phrases["wc_user_settings_delete_all_comments"] = esc_attr($_POST["wc_user_settings_delete_all_comments"]);
            $this->phrases["wc_user_settings_delete_all_comments_message"] = wpautop($_POST["wc_user_settings_delete_all_comments_message"]);
            $this->phrases["wc_user_settings_delete_all_subscriptions"] = esc_attr($_POST["wc_user_settings_delete_all_subscriptions"]);
            $this->phrases["wc_user_settings_delete_all_subscriptions_message"] = wpautop($_POST["wc_user_settings_delete_all_subscriptions_message"]);
            $this->phrases["wc_user_settings_delete_all_follows"] = esc_attr($_POST["wc_user_settings_delete_all_follows"]);
            $this->phrases["wc_user_settings_delete_all_follows_message"] = wpautop($_POST["wc_user_settings_delete_all_follows_message"]);
            $this->phrases["wc_user_settings_subscribed_to_replies"] = esc_attr($_POST["wc_user_settings_subscribed_to_replies"]);
            $this->phrases["wc_user_settings_subscribed_to_replies_own"] = esc_attr($_POST["wc_user_settings_subscribed_to_replies_own"]);
            $this->phrases["wc_user_settings_subscribed_to_all_comments"] = esc_attr($_POST["wc_user_settings_subscribed_to_all_comments"]);
            $this->phrases["wc_user_settings_check_email"] = esc_attr($_POST["wc_user_settings_check_email"]);
            $this->phrases["wc_user_settings_email_error"] = esc_attr($_POST["wc_user_settings_email_error"]);
            $this->phrases["wc_confirm_comment_delete"] = esc_attr($_POST["wc_confirm_comment_delete"]);
            $this->phrases["wc_confirm_cancel_subscription"] = esc_attr($_POST["wc_confirm_cancel_subscription"]);
            $this->phrases["wc_confirm_cancel_follow"] = esc_attr($_POST["wc_confirm_cancel_follow"]);
            $this->phrases["wc_follow_user"] = esc_attr($_POST["wc_follow_user"]);
            $this->phrases["wc_unfollow_user"] = esc_attr($_POST["wc_unfollow_user"]);
            $this->phrases["wc_follow_success"] = esc_attr($_POST["wc_follow_success"]);
            $this->phrases["wc_follow_canceled"] = esc_attr($_POST["wc_follow_canceled"]);
            $this->phrases["wc_follow_email_confirm"] = esc_attr($_POST["wc_follow_email_confirm"]);
            $this->phrases["wc_follow_email_confirm_fail"] = esc_attr($_POST["wc_follow_email_confirm_fail"]);
            $this->phrases["wc_follow_login_to_follow"] = esc_attr($_POST["wc_follow_login_to_follow"]);
            $this->phrases["wc_follow_impossible"] = esc_attr($_POST["wc_follow_impossible"]);
            $this->phrases["wc_follow_not_added"] = esc_attr($_POST["wc_follow_not_added"]);
            $this->phrases["wc_follow_confirm"] = esc_attr($_POST["wc_follow_confirm"]);
            $this->phrases["wc_follow_cancel"] = esc_attr($_POST["wc_follow_cancel"]);
            $this->phrases["wc_follow_confirm_email_subject"] = esc_attr($_POST["wc_follow_confirm_email_subject"]);
            $this->phrases["wc_follow_confirm_email_message"] = wpautop($_POST["wc_follow_confirm_email_message"]);
            $this->phrases["wc_follow_email_subject"] = esc_attr($_POST["wc_follow_email_subject"]);
            $this->phrases["wc_follow_email_message"] = wpautop($_POST["wc_follow_email_message"]);
            $this->phrases["wc_mentioned_email_subject"] = esc_attr($_POST["wc_mentioned_email_subject"]);
            $this->phrases["wc_mentioned_email_message"] = wpautop($_POST["wc_mentioned_email_message"]);
            $this->phrases["wc_copied_to_clipboard"] = esc_attr($_POST["wc_copied_to_clipboard"]);
            $this->phrases["wc_feedback_shortcode_tooltip"] = esc_attr($_POST["wc_feedback_shortcode_tooltip"]);
            $this->phrases["wc_feedback_popup_title"] = esc_attr($_POST["wc_feedback_popup_title"]);
            $this->phrases["wc_please_leave_feebdack"] = esc_attr($_POST["wc_please_leave_feebdack"]);
            $this->phrases["wc_feedback_content_text"] = esc_attr($_POST["wc_feedback_content_text"]);
            $this->phrases["wc_feedback_comment_success"] = esc_attr($_POST["wc_feedback_comment_success"]);
            $this->phrases["wc_commenting_is_closed"] = esc_attr($_POST["wc_commenting_is_closed"]);
            $this->phrases["wc_closed_comment_thread"] = esc_attr($_POST["wc_closed_comment_thread"]);
            $this->phrases["wc_bubble_invite_message"] = esc_attr($_POST["wc_bubble_invite_message"]);
            $this->phrases["wc_vote_phrase"] = esc_attr($_POST["wc_vote_phrase"]);
            $this->phrases["wc_votes_phrase"] = esc_attr($_POST["wc_votes_phrase"]);
            $this->phrases["wc_comment_link"] = esc_attr($_POST["wc_comment_link"]);
            $this->phrases["wc_not_allowed_to_comment_more_than"] = esc_attr($_POST["wc_not_allowed_to_comment_more_than"]);
            $this->phrases["wc_not_allowed_to_create_comment_thread_more_than"] = esc_attr($_POST["wc_not_allowed_to_create_comment_thread_more_than"]);
            $this->phrases["wc_not_allowed_to_reply_more_than"] = esc_attr($_POST["wc_not_allowed_to_reply_more_than"]);
            $this->phrases["wc_inline_form_comment"] = esc_attr($_POST["wc_inline_form_comment"]);
            $this->phrases["wc_inline_form_notify"] = esc_attr($_POST["wc_inline_form_notify"]);
            $this->phrases["wc_inline_form_name"] = esc_attr($_POST["wc_inline_form_name"]);
            $this->phrases["wc_inline_form_email"] = esc_attr($_POST["wc_inline_form_email"]);
            $this->phrases["wc_inline_form_comment_button"] = esc_attr($_POST["wc_inline_form_comment_button"]);
            $this->phrases["wc_inline_comments_view_all"] = esc_attr($_POST["wc_inline_comments_view_all"]);
            $this->phrases["wc_inline_feedbacks"] = esc_attr($_POST["wc_inline_feedbacks"]);
            $this->phrases["wc_unable_sent_email"] = esc_attr($_POST["wc_unable_sent_email"]);
            $this->phrases["wc_subscription_fault"] = esc_attr($_POST["wc_subscription_fault"]);
            $this->phrases["wc_comments_are_deleted"] = esc_attr($_POST["wc_comments_are_deleted"]);
            $this->phrases["wc_cancel_subs_success"] = esc_attr($_POST["wc_cancel_subs_success"]);
            $this->phrases["wc_cancel_follows_success"] = esc_attr($_POST["wc_cancel_follows_success"]);
            $this->phrases["wc_follow_confirm_success"] = esc_attr($_POST["wc_follow_confirm_success"]);
            $this->phrases["wc_follow_cancel_success"] = esc_attr($_POST["wc_follow_cancel_success"]);
            $this->phrases["wc_login_to_comment"] = esc_attr($_POST["wc_login_to_comment"]);
            $this->phrases["wc_view_comments"] = esc_attr($_POST["wc_view_comments"]);
            $this->phrases["wc_spoiler"] = esc_attr($_POST["wc_spoiler"]);
            $this->phrases["wc_last_edited"] = esc_attr($_POST["wc_last_edited"]);
            $this->phrases["wc_reply_to"] = esc_attr($_POST["wc_reply_to"]);
            $this->phrases["wc_manage_comment"] = esc_attr($_POST["wc_manage_comment"]);
            $this->phrases["wc_spoiler_title"] = esc_attr($_POST["wc_spoiler_title"]);
            $this->phrases["wc_cannot_rate_again"] = esc_attr($_POST["wc_cannot_rate_again"]);
            $this->phrases["wc_not_allowed_to_rate"] = esc_attr($_POST["wc_not_allowed_to_rate"]);
            // Media Upload //            
            $this->phrases["wmuPhraseConfirmDelete"] = esc_attr($_POST["wmuPhraseConfirmDelete"]);
            $this->phrases["wmuPhraseNotAllowedFile"] = esc_attr($_POST["wmuPhraseNotAllowedFile"]);
            $this->phrases["wmuPhraseMaxFileCount"] = esc_attr($_POST["wmuPhraseMaxFileCount"]);
            $this->phrases["wmuPhraseMaxFileSize"] = esc_attr($_POST["wmuPhraseMaxFileSize"]);
            $this->phrases["wmuPhrasePostMaxSize"] = esc_attr($_POST["wmuPhrasePostMaxSize"]);
            $this->phrases["wmuAttachImage"] = esc_attr($_POST["wmuAttachImage"]);
            $this->phrases["wmuChangeImage"] = esc_attr($_POST["wmuChangeImage"]);

            if (class_exists("Prompt_Comment_Form_Handling") && $this->usePostmaticForCommentNotification) {
                $this->phrases["wc_postmatic_subscription_label"] = esc_attr($_POST["wc_postmatic_subscription_label"]);
            }
            foreach ($this->labels["blogRoles"] as $roleName => $roleVal) {
                $this->phrases["wc_blog_role_" . $roleName] = esc_attr($_POST["wc_blog_role_" . $roleName]);
            }
            $this->dbManager->updatePhrases($this->phrases);
        }
    }

    public function resetOptions() {
        if (isset($_GET["_wpnonce"]) && isset($_GET["wpdiscuz_reset_options"]) && !empty($_GET["page"]) && !empty($_GET["wpd_tab"]) && ($resetTab = sanitize_key($_GET["wpd_tab"])) && $_GET["wpdiscuz_reset_options"] == 1 && $_GET["page"] == WpdiscuzCore::PAGE_SETTINGS && current_user_can("manage_options") && wp_verify_nonce($_GET["_wpnonce"], "wpdiscuz_reset_options_nonce-" . $resetTab)) {

            $roleColors = ["guest" => "#898989", "post_author" => "#07B290", "administrator" => "#ff451f", "editor" => "#d36000", "author" => "#327324", "contributor" => "#a240cd", "subscriber" => "#31839e"];

            if ($resetTab === "all") {
                delete_option(self::OPTION_SLUG_OPTIONS);
                $this->addOptions();
                $this->initOptions(get_option(self::OPTION_SLUG_OPTIONS));
                $this->general["showPluginPoweredByLink"] = 1;
                $blogRoles = get_editable_roles();
                $this->labels["blogRoles"]["guest"] = $roleColors["guest"];
                $this->labels["blogRoles"]["post_author"] = $roleColors["post_author"];
                $this->labels["blogRoleLabels"]["post_author"] = 1;
                $this->labels["blogRoleLabels"]["guest"] = 0;
                foreach ($blogRoles as $roleName => $roleInfo) {
                    $this->labels["blogRoles"][$roleName] = isset($roleColors[$roleName]) ? $roleColors[$roleName] : "#31839e";
                    $this->labels["blogRoleLabels"][$roleName] = $roleName === "editor" || $roleName === "administrator" ? 1 : 0;
                }
                $this->updateOptions();
            } else if (isset($this->{$resetTab})) {
                $defaultOptions = $this->getDefaultOptions();
                $this->{$resetTab} = $defaultOptions[$_GET["wpd_tab"]];
                if ($resetTab === WpdiscuzCore::TAB_GENERAL) {
                    $this->general["showPluginPoweredByLink"] = 1;
                } else if ($resetTab === WpdiscuzCore::TAB_LABELS) {
                    $blogRoles = get_editable_roles();
                    $this->labels["blogRoles"]["guest"] = $roleColors["guest"];
                    $this->labels["blogRoles"]["post_author"] = $roleColors["post_author"];
                    $this->labels["blogRoleLabels"]["post_author"] = 1;
                    $this->labels["blogRoleLabels"]["guest"] = 0;
                    foreach ($blogRoles as $roleName => $roleInfo) {
                        $this->labels["blogRoles"][$roleName] = isset($roleColors[$roleName]) ? $roleColors[$roleName] : "#31839e";
                        $this->labels["blogRoleLabels"][$roleName] = $roleName === "editor" || $roleName === "administrator" ? 1 : 0;
                    }
                }
                $this->updateOptions();
            }
            do_action("wpdiscuz_reset_options", $resetTab);
        }
    }

    public function mainOptionsForm() {
        if (isset($_POST["wc_submit_options"])) {
            add_settings_error("wpdiscuz", "settings_updated", esc_html__("Settings updated", "wpdiscuz"), "updated");
        }
        include_once WPDISCUZ_DIR_PATH . "/options/html-options.php";
    }

    public function phrasesOptionsForm() {
        if (isset($_POST["wc_submit_phrases"])) {
            add_settings_error("wpdiscuz", "phrases_updated", esc_html__("Phrases updated", "wpdiscuz"), "updated");
        }
        $this->initPhrasesOnLoad();
        include_once WPDISCUZ_DIR_PATH . "/options/html-phrases.php";
    }

    public function tools() {
        if (current_user_can("manage_options")) {

            $wpUploadsDir = wp_upload_dir();
            $wpdiscuzOptionsDir = $wpUploadsDir["basedir"] . self::OPTIONS_DIR;
            $wpdiscuzOptionsUrl = $wpUploadsDir["baseurl"] . self::OPTIONS_DIR;

            if (isset($_POST["tools-action"])) {

                $action = $_POST["tools-action"];

                if ($action === "export-options") {
                    check_admin_referer("wc_tools_form", "wpd-options-export");
                    wp_mkdir_p($wpdiscuzOptionsDir);
                    $options = @maybe_unserialize(get_option(self::OPTION_SLUG_OPTIONS));
                    if ($options) {
                        $json = json_encode($options);
                        if (file_put_contents($wpdiscuzOptionsDir . self::OPTIONS_FILENAME . ".txt", $json)) {
                            add_settings_error("wpdiscuz", "settings_updated", esc_html__("Options were backed up!", "wpdiscuz"), "updated");
                        } else {
                            add_settings_error("wpdiscuz", "settings_updated", esc_html__("Cannot back up the options!", "wpdiscuz"), "error");
                        }
                    }
                } else if ($action === "import-options") {
                    check_admin_referer("wc_tools_form", "wpd-options-import");
                    $file = isset($_FILES["wpdiscuz-options-file"]) ? $_FILES["wpdiscuz-options-file"] : "";
                    if ($file && is_array($file) && isset($file["tmp_name"])) {
                        if ($data = file_get_contents($file["tmp_name"])) {
                            $options = json_decode($data, true);
                            if ($options && is_array($options)) {
                                update_option(self::OPTION_SLUG_OPTIONS, $this->replaceOldOptions($options, false));
                                add_settings_error("wpdiscuz", "settings_updated", esc_html__("Options Imported Successfully!", "wpdiscuz"), "updated");
                            } else {
                                add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! File content is empty or data is not valid!", "wpdiscuz"), "error");
                            }
                        } else {
                            add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! Can not get file content!", "wpdiscuz"), "error");
                        }
                    } else {
                        add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! Please choose file!", "wpdiscuz"), "error");
                    }
                } else if ($action === "export-phrases") {
                    check_admin_referer("wc_tools_form", "wpd-phrases-export");
                    wp_mkdir_p($wpdiscuzOptionsDir);
                    $phrases = $this->dbManager->getPhrases();
                    if ($phrases) {
                        $json = json_encode($phrases);
                        if (file_put_contents($wpdiscuzOptionsDir . self::PHRASES_FILENAME . ".txt", $json)) {
                            add_settings_error("wpdiscuz", "settings_updated", esc_html__("Phrases were backed up!", "wpdiscuz"), "updated");
                        } else {
                            add_settings_error("wpdiscuz", "settings_updated", esc_html__("Cannot back up the phrases!", "wpdiscuz"), "error");
                        }
                    }
                } else if ($action === "import-phrases") {
                    check_admin_referer("wc_tools_form", "wpd-phrases-import");
                    $file = isset($_FILES["wpdiscuz-phrases-file"]) ? $_FILES["wpdiscuz-phrases-file"] : "";
                    if ($file && is_array($file) && isset($file["tmp_name"])) {
                        if ($data = file_get_contents($file["tmp_name"])) {
                            $phrases = json_decode($data, true);
                            if ($phrases && is_array($phrases)) {
                                $this->dbManager->updatePhrases($phrases);
                                add_settings_error("wpdiscuz", "settings_updated", esc_html__("Phrases Imported Successfully!", "wpdiscuz"), "updated");
                            } else {
                                add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! File content is empty or data is not valid!", "wpdiscuz"), "error");
                            }
                        } else {
                            add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! Can not get file content!", "wpdiscuz"), "error");
                        }
                    } else {
                        add_settings_error("wpdiscuz", "settings_error", esc_html__("Error occured! Please choose file!", "wpdiscuz"), "error");
                    }
                }
            }
        } else {
            die(esc_html_e("Hacker?", "wpdiscuz"));
        }
        include_once WPDISCUZ_DIR_PATH . "/options/html-tools.php";
    }

    public function addons() {
        include_once WPDISCUZ_DIR_PATH . "/options/html-addons.php";
    }

    private function initAddons() {
        $this->addons = [
            "bundle" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "Bundle", "title" => "Addons Bundle", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/bundle/header.png"), "desc" => esc_html__("All 16 addons in one bundle. Save 90% and get Unlimited Site License with one year premium support.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-addons-bundle/"],
            "uploader" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzMediaUploader", "title" => "Media Uploader", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/uploader/header.png"), "desc" => esc_html__("Extended comment attachment system. Allows to upload images, videos, audios and other file types.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-media-uploader/"],
            "embeds" => ["version" => "1.0.0", "requires" => "7.0.0", "class" => "WpdiscuzEmbeds", "title" => "Embeds", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/embeds/header.png"), "desc" => esc_html__("Allows to embed lots of video, social network, audio and photo content providers URLs in comment content.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-embeds/"],
            "syntax" => ["version" => "1.0.0", "requires" => "7.0.0", "class" => "wpDiscuzSyntaxHighlighter", "title" => "wpDiscuzSyntaxHighlighter", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/syntax/header.png"), "desc" => esc_html__("Syntax highlighting for comments, automatic language detection and multi-language code highlighting.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-syntax-highlighter/"],
            "frontend-moderation" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzFrontEndModeration", "title" => "Front-end Moderation", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/frontend-moderation/header.png"), "desc" => esc_html__("All in one powerful yet simple admin toolkit to moderate comments on front-end.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-frontend-moderation/"],
            "emoticons" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzSmile", "title" => "Emoticons", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/emoticons/header.png"), "desc" => esc_html__("Brings an ocean of emotions to your comments. It comes with an awesome smile package.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-emoticons/"],
            "recaptcha" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzRecaptcha", "title" => "Invisible reCAPTCHA v3", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/recaptcha/header.png"), "desc" => esc_html__("Adds Invisible reCAPTCHA on all comment forms. Stops spam and bot comments with reCAPTCHA version 3", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-recaptcha/"],
            "author-info" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzCommentAuthorInfo", "title" => "Comment Author Info", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/author-info/header.png"), "desc" => esc_html__("Extended information about comment author with Profile, Activity, Votes and Subscriptions Tabs on pop-up window.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-comment-author-info/"],
            "report-flagging" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzFlagComment", "title" => "Report and Flagging", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/report/header.png"), "desc" => esc_html__("Comment reporting tools. Auto-moderates comments based on number of flags and dislikes.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-report-flagging/"],
            "online-users" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzOnlineUsers", "title" => "Online Users", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/online-users/header.png"), "desc" => esc_html__("Real-time online user checking, pop-up notification of new online users and online/offline badges.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-online-users/"],
            "private" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzPrivateComment", "title" => "Private Comments", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/private/header.png"), "desc" => esc_html__("Allows to create private comment threads. Rich management options in dashboard by user roles.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-private-comments/"],
            "subscriptions" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpdSubscribeManager", "title" => "Subscription Manager", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/subscriptions/header.png"), "desc" => esc_html__("Total control over comment subscriptions. Full list, monitor, manage, filter, unsubscribe, confirm...", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-subscribe-manager/"],
            "ads-manager" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzAdsManager", "title" => "Ads Manager", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/ads-manager/header.png"), "desc" => esc_html__("A full-fledged tool-kit for advertising in comment section of your website. Separate banner and ad managment.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-ads-manager/"],
            "user-mention" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzUCM", "title" => "User &amp; Comment Mentioning", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/user-mention/header.png"), "desc" => esc_html__("Allows to mention comments and users in comment text using #comment-id and @username tags.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-user-comment-mentioning/"],
            "likers" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpdiscuzVoters", "title" => "Advanced Likers", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/likers/header.png"), "desc" => esc_html__("See comment likers and voters of each comment. Adds user reputation and badges based on received likes.", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-advanced-likers/"],
            "translate" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "WpDiscuzTranslate", "title" => "Comment Translate", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/translate/header.png"), "desc" => esc_html__('Adds a smart and intuitive AJAX "Translate" button with 60 language options. Uses free translation API.', "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-comment-translation/"],
            "search" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzCommentSearch", "title" => "Comment Search", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/search/header.png"), "desc" => esc_html__("AJAX powered front-end comment search. It starts searching while you type search words. ", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-comment-search/"],
            "widgets" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "wpDiscuzWidgets", "title" => "wpDiscuz Widgets", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/widgets/header.png"), "desc" => esc_html__("Most voted comments, Active comment threads, Most commented posts, Active comment authors", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-widgets/"],
            "mycred" => ["version" => "7.0.0", "requires" => "7.0.0", "class" => "myCRED_Hook_wpDiscuz_Vote", "title" => "myCRED Integration", "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/mycred/header.png"), "desc" => esc_html__("Integrates myCRED Badges and Ranks. Converts wpDiscuz comment votes/likes to myCRED points. ", "wpdiscuz"), "url" => "https://gvectors.com/product/wpdiscuz-mycred/"],
        ];
    }

    private function initTips() {
        $this->tips = [
            "custom-form" => ["title" => esc_html__("Custom Comment Forms", "wpdiscuz"),
                "text" => esc_html__("You can create custom comment forms with wpDiscuz. wpDiscuz 4 comes with custom comment forms and fields. You can create custom comment forms for each post type, each form can beceated with different form fields, for eaxample: text, dropdown, rating, checkboxes, etc...", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/tips/custom-form.png"),
                "url" => admin_url() . "edit.php?post_type=wpdiscuz_form"],
            "emoticons" => ["title" => esc_html__("Emoticons", "wpdiscuz"),
                "text" => esc_html__("You can add more emotions to your comments using wpDiscuz Emoticons addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/emoticons/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-emoticons/"],
            "ads-manager" => ["title" => esc_html__("Ads Manager", "wpdiscuz"),
                "text" => esc_html__("Increase your income using ad banners. Comment area is the most active sections for advertising. wpDiscuz Ads Manager addon is designed to help you add banners and control ads in this section.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/ads-manager/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-ads-manager/"],
            "user-mention" => ["title" => esc_html__("User and Comment Mentioning", "wpdiscuz"),
                "text" => esc_html__("Using wpDiscuz User &amp; Comment Mentioning addon you can allow commenters mention comments and users in comment text using #comment-id and @username tags.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/user-mention/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-user-comment-mentioning/"],
            "likers" => ["title" => esc_html__("Advanced Likers", "wpdiscuz"),
                "text" => esc_html__("wpDiscuz Advanced Likers addon displays likers and voters of each comment. Adds user reputation and badges based on received likes.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/likers/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-advanced-likers/"],
            "report-flagging" => ["title" => esc_html__("Report and Flagging", "wpdiscuz"),
                "text" => esc_html__("Let your commenters help you to determine and remove spam comments. wpDiscuz Report and Flagging addon comes with comment reporting tools. Automaticaly auto-moderates comments based on number of flags and dislikes.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/report/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-report-flagging/"],
            "translate" => ["title" => esc_html__("Comment Translate", "wpdiscuz"),
                "text" => esc_html__("In most cases the big part of your visitors are not a native speakers of your language. Make your comments comprehensible for all visitors using wpDiscuz Comment Translation addon. It adds smart and intuitive AJAX 'Translate' button with 60 language translation options. Uses free translation API.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/translate/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-translation/"],
            "search" => ["title" => esc_html__("Comment Search", "wpdiscuz"),
                "text" => esc_html__("You can let website visitor search in comments. It's always more attractive to find a comment about something that interest you. Using wpDiscuz Comment Search addon you'll get a nice, AJAX powered front-end comment search form above comment list.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/search/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-comment-search/"],
            "widgets" => ["title" => esc_html__("wpDiscuz Widgets", "wpdiscuz"),
                "text" => esc_html__("More Comment Widgets! Most voted comments, Active comment threads, Most commented posts, Active comment authors widgets are available in wpDiscuz Widgets Addon", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/widgets/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-widgets/"],
            "frontend-moderation" => ["title" => esc_html__("Front-end Moderation", "wpdiscuz"),
                "text" => esc_html__("You can moderate comments on front-end using all in one powerful yet simple wpDiscuz Frontend Moderation addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/frontend-moderation/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-frontend-moderation/"],
            "uploader" => ["title" => esc_html__("Media Uploader", "wpdiscuz"),
                "text" => esc_html__("You can let website visitors attach images and files to comments and embed video/audio content using wpDiscuz Media Uploader addon.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/uploader/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-media-uploader/"],
            "recaptcha" => ["title" => esc_html__("Google ReCaptcha", "wpdiscuz"),
                "text" => esc_html__("Advanced spam protection with wpDiscuz Google reCAPTCHA addon. This addon adds No-CAPTCHA reCAPTCHA on all comment forms. Stops spam and bot comments.", "wpdiscuz"),
                "thumb" => plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/recaptcha/header.png"),
                "url" => "https://gvectors.com/product/wpdiscuz-recaptcha/"],
        ];
    }

    private function addonNote() {
        if ((!empty($_GET["page"]) && in_array($_GET["page"], [self::PAGE_WPDISCUZ, self::PAGE_SETTINGS, self::PAGE_PHRASES, self::PAGE_TOOLS, self::PAGE_ADDONS])) || strpos($_SERVER["REQUEST_URI"], "edit.php?post_type=wpdiscuz_form") !== FALSE) {
            $lastHash = get_option("wpdiscuz-addon-note-dismissed");
            $lastHashArray = explode(",", $lastHash);
            $currentHash = "wpDiscuz Addon Bundle";
            if ($lastHash != $currentHash && !in_array("Addons Bundle", $lastHashArray)) {
                ?>
                <div class="updated notice wpdiscuz_addon_note is-dismissible" style="margin-top:10px;">
                    <p style="font-weight:normal; font-size:15px; border-bottom:1px dotted #DCDCDC; padding-bottom:10px; clear: both;">
                        <?php //esc_html_e("New Addons are available for wpDiscuz Comments Plugin");         ?>
                        <?php esc_html_e("Finally wpDiscuz Addons Bundle is ready for You!", "wpdiscuz"); ?>
                    </p>
                    <div style="font-size:14px;">
                        <?php
                        foreach ($this->addons as $key => $addon) {
                            if ($addon["class"] != "Bundle")
                                continue;
                            if (in_array($addon["title"], $lastHashArray))
                                continue;
                            ?>
                            <div style="display:inline-block; min-width:27%; padding-right:10px; margin-bottom:10px;"><img src="<?php echo esc_url_raw($addon["thumb"]); ?>" style="height:40px; width:auto; vertical-align:middle; margin:0px 10px; text-decoration:none;" />  <a href="<?php echo esc_url_raw($addon["url"]) ?>" target="_blank" style="color:#444; text-decoration:none;" title="<?php esc_attr_e("View Addons Bundle", "wpdiscuz"); ?>"><?php echo esc_html($addon["title"]); ?></a></div>
                            <?php
                        }
                        ?>
                        <div style="clear:both;"></div>
                    </div>
                    <p>&nbsp;&nbsp;&nbsp;<a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_ADDONS)); ?>"><?php esc_html_e("Go to wpDiscuz Addons subMenu"); ?> &raquo;</a></p>
                </div>
                <?php
            }
        }
    }

    public function dismissAddonNote() {
        $hash = $this->addonHash();
        update_option("wpdiscuz-addon-note-dismissed", $hash);
        exit();
    }

    public function dismissTipNote() {
        $hash = $this->tipHash();
        update_option("wpdiscuz-tip-note-dismissed", $hash);
        exit();
    }

    public function dismissAddonNoteOnPage() {
        $hash = $this->addonHash();
        update_option("wpdiscuz-addon-note-dismissed", $hash);
    }

    public function addonHash() {
        $viewed = "";
        foreach ($this->addons as $key => $addon) {
            $viewed .= $addon["title"] . ",";
        }
        $hash = $viewed;
        return $hash;
    }

    public function tipHash() {
        $viewed = "";
        foreach ($this->tips as $key => $tip) {
            $viewed .= $tip["title"] . ",";
        }
        $hash = $viewed;
        return $hash;
    }

    public function tipDisplayed() {
        $tipTtile = substr(strip_tags($_GET["tip"]), 0, 100);
        $lastHash = get_option("wpdiscuz-tip-note-dismissed");
        if ($lastHash) {
            $lastHashArray = explode(",", $lastHash);
        } else {
            $lastHashArray = [];
        }
        $lastHashArray[] = $tipTtile;
        $hash = implode(",", $lastHashArray);
        return $hash;
    }

    public function refreshAddonPage() {
        $lastHash = get_option("wpdiscuz-addon-note-dismissed");
        $currentHash = $this->addonHash();
        if ($lastHash != $currentHash) {
            ?>
            <script language="javascript">jQuery(document).ready(function () {
                    location.reload();
                });</script>
            <?php
        }
    }

    public function adminNotices() {
        if (current_user_can("manage_options")) {
            //$this->addonNote(); //temporary disabled.
            $this->regenerateMessage();
        }
    }

    private function regenerateMessage() {
        global $pagenow;
        $notWpdiscuzSettingsPage = $pagenow !== "admin.php" || ($pagenow === "admin.php" && (!isset($_GET["page"]) || (isset($_GET["page"]) && $_GET["page"] !== self::PAGE_SETTINGS)));
        $wizardCompleted = intval(get_option(self::OPTION_SLUG_WIZARD_COMPLETED));
        if (!$wizardCompleted && $notWpdiscuzSettingsPage) {
            ?>
            <div class='notice notice-warning'>
                <p style="font-size: 14px; font-weight: 600;">
                    <?php esc_html_e("Please complete required steps to start using wpDiscuz 7", "wpdiscuz"); ?> &nbsp;
                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_SETTINGS . "&wpd_wizard=1")); ?>" class="button button-primary"><?php intval(get_option(self::OPTION_SLUG_WIZARD_AFTER_UPDATE)) ? esc_html_e("Go to Update Wizard &raquo;", "wpdiscuz") : esc_html_e("Go to Installation Wizard &raquo;", "wpdiscuz"); ?></a>
                </p>
            </div>
            <?php
        }
        if ($wizardCompleted && $notWpdiscuzSettingsPage && class_exists("Jetpack") && Jetpack::is_module_active("comments")) {
            ?>
            <div class='notice notice-warning'>
                <p>
                    <?php esc_html_e("Jetpack Comments are active.", "wpdiscuz"); ?>
                </p>
            </div>
            <?php
        }
        if ($wizardCompleted && intval(get_option(self::OPTION_SLUG_SHOW_VOTE_REG_MESSAGE))) {
            ?>
            <div class='notice notice-warning'>
                <p>
                    <?php esc_html_e("Comment votes meta data need to be regenerated", "wpdiscuz"); ?>&nbsp;
                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_TOOLS . "#wpdtool-regenerate")); ?>" class="button button-primary"><?php esc_html_e("Regenerate Vote Metas", "wpdiscuz"); ?></a>
                </p>
            </div>
            <?php
        }
        if ($wizardCompleted && intval(get_option(self::OPTION_SLUG_SHOW_CLOSED_REG_MESSAGE))) {
            ?>
            <div class='notice notice-warning'>
                <p>
                    <?php esc_html_e("Closed Comments data need be regenerated", "wpdiscuz"); ?>&nbsp;
                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_TOOLS . "#wpdtool-regenerate")); ?>" class="button button-primary"><?php esc_html_e("Regenerate Closed Comments", "wpdiscuz"); ?></a>
                </p>
            </div>
            <?php
        }
        if ($wizardCompleted && intval(get_option(self::OPTION_SLUG_SHOW_VOTE_DATA_REG_MESSAGE))) {
            ?>
            <div class='notice notice-warning'>
                <p>
                    <?php esc_html_e("Comments votes data need to be regenerated", "wpdiscuz"); ?>&nbsp;
                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_TOOLS . "#wpdtool-regenerate")); ?>" class="button button-primary"><?php esc_html_e("Regenerate Vote Data", "wpdiscuz"); ?></a>
                </p>
            </div>
            <?php
        }
        if ($wizardCompleted && intval(get_option(self::OPTION_SLUG_SHOW_SYNC_COMMENTERS_MESSAGE))) {
            ?>
            <div class='notice notice-warning'>
                <p>
                    <?php esc_html_e("Please synchronize comment data for the best performance and fastest experience", "wpdiscuz"); ?>&nbsp;
                    <a href="<?php echo esc_url_raw(admin_url("admin.php?page=" . self::PAGE_TOOLS . "#wpdtool-regenerate")); ?>" class="button button-primary"><?php esc_html_e("Synchronize Commenters Data", "wpdiscuz"); ?></a>
                </p>
            </div>
            <?php
        }
    }

    public function getDefaultFileTypes() {
        $types = [
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "jpe" => "image/jpeg",
            "gif" => "image/gif",
            "png" => "image/png",
            "bmp" => "image/bmp",
            "tiff" => "image/tiff",
            "tif" => "image/tiff",
            "ico" => "image/x-icon",
        ];
        $types = apply_filters("wpdiscuz_mu_file_types", $types);
        return $types;
    }

    private function getSizeInBytes($size) {
        $value = trim($size);
        if (is_numeric($value)) {
            return $value;
        }

        $lastChar = strtolower($value[strlen($value) - 1]);
        $value = substr($value, 0, -1);

        switch ($lastChar) {
            case 'g': $value *= 1024 * 1024 * 1024;
                break;
            case 'm': $value *= 1024 * 1024;
                break;
            case 'k': $value *= 1024;
                break;
        }
        return intval($value);
    }

    private function getAllImageSizes() {
        return ["thumbnail", "medium", "medium_large", "large"];
    }

    public function dashboard() {
        include_once WPDISCUZ_DIR_PATH . "/options/html-dashboard.php";
    }

    public function isShowLoginButtons() {
        return $this->social["enableFbLogin"] || $this->social["enableTwitterLogin"] || $this->social["enableGoogleLogin"] || $this->social["enableDisqusLogin"] || $this->social["enableWordpressLogin"] || $this->social["enableVkLogin"] || $this->social["enableOkLogin"] || $this->social["enableInstagramLogin"] || $this->social["enableLinkedinLogin"] || $this->social["enableYandexLogin"] || $this->social["enableMailruLogin"] || $this->social["enableWeiboLogin"] || $this->social["enableWechatLogin"] || $this->social["enableQQLogin"] || $this->social["enableBaiduLogin"];
    }

    public function showEditorToolbar() {
        return $this->form["boldButton"] || $this->form["italicButton"] || $this->form["underlineButton"] || $this->form["strikeButton"] || $this->form["olButton"] || $this->form["ulButton"] || $this->form["blockquoteButton"] || $this->form["codeblockButton"] || $this->form["linkButton"] || $this->form["sourcecodeButton"] || $this->form["spoilerButton"];
    }

    public function replaceOldOptions($oldOptions, $update = true) {
        $newOptions = $this->getDefaultOptions();
        if (!$update && isset($oldOptions[self::TAB_GENERAL])) {
            foreach ($newOptions as $key => $value) {
                foreach ($value as $k => $val) {
                    if (isset($oldOptions[$key][$k])) {
                        $newOptions[$key][$k] = $oldOptions[$key][$k];
                    }
                }
            }
            return $newOptions;
        }
        if (isset($oldOptions["enableDropAnimation"])) {
            $newOptions[self::TAB_FORM]["enableDropAnimation"] = $oldOptions["enableDropAnimation"];
        } else if (isset($oldOptions[self::TAB_FORM]["enableDropAnimation"])) {
            $newOptions[self::TAB_FORM]["enableDropAnimation"] = $oldOptions[self::TAB_FORM]["enableDropAnimation"];
        }
        if (isset($oldOptions["commenterNameMinLength"])) {
            $newOptions[self::TAB_FORM]["commenterNameMinLength"] = $oldOptions["commenterNameMinLength"];
        } else if (isset($oldOptions[self::TAB_FORM]["commenterNameMinLength"])) {
            $newOptions[self::TAB_FORM]["commenterNameMinLength"] = $oldOptions[self::TAB_FORM]["commenterNameMinLength"];
        }
        if (isset($oldOptions["commenterNameMaxLength"])) {
            $newOptions[self::TAB_FORM]["commenterNameMaxLength"] = $oldOptions["commenterNameMaxLength"];
        } else if (isset($oldOptions[self::TAB_FORM]["commenterNameMaxLength"])) {
            $newOptions[self::TAB_FORM]["commenterNameMaxLength"] = $oldOptions[self::TAB_FORM]["commenterNameMaxLength"];
        }
        if (isset($oldOptions["storeCommenterData"])) {
            $newOptions[self::TAB_FORM]["storeCommenterData"] = $oldOptions["storeCommenterData"];
        } else if (isset($oldOptions[self::TAB_FORM]["storeCommenterData"])) {
            $newOptions[self::TAB_FORM]["storeCommenterData"] = $oldOptions[self::TAB_FORM]["storeCommenterData"];
        }
        if (isset($oldOptions["wc_show_hide_loggedin_username"])) {
            $newOptions[self::TAB_LOGIN]["showLoggedInUsername"] = $oldOptions["wc_show_hide_loggedin_username"];
        } else if (isset($oldOptions[self::TAB_LOGIN]["showLoggedInUsername"])) {
            $newOptions[self::TAB_LOGIN]["showLoggedInUsername"] = $oldOptions[self::TAB_LOGIN]["showLoggedInUsername"];
        }
        if (isset($oldOptions["hideLoginLinkForGuests"])) {
            $newOptions[self::TAB_LOGIN]["showLoginLinkForGuests"] = (int) !$oldOptions["hideLoginLinkForGuests"];
        } else if (isset($oldOptions[self::TAB_LOGIN]["showLoginLinkForGuests"])) {
            $newOptions[self::TAB_LOGIN]["showLoginLinkForGuests"] = $oldOptions[self::TAB_LOGIN]["showLoginLinkForGuests"];
        }
        if (isset($oldOptions["hideUserSettingsButton"])) {
            $settingsButton = (int) !$oldOptions["hideUserSettingsButton"];
            $newOptions[self::TAB_LOGIN]["showActivityTab"] = $settingsButton;
            $newOptions[self::TAB_LOGIN]["showSubscriptionsTab"] = $settingsButton;
            $newOptions[self::TAB_LOGIN]["showFollowsTab"] = $settingsButton;
        } else {
            if (isset($oldOptions[self::TAB_LOGIN]["showActivityTab"])) {
                $newOptions[self::TAB_LOGIN]["showActivityTab"] = $oldOptions[self::TAB_LOGIN]["showActivityTab"];
            }
            if (isset($oldOptions[self::TAB_LOGIN]["showSubscriptionsTab"])) {
                $newOptions[self::TAB_LOGIN]["showSubscriptionsTab"] = $oldOptions[self::TAB_LOGIN]["showSubscriptionsTab"];
            }
            if (isset($oldOptions[self::TAB_LOGIN]["showFollowsTab"])) {
                $newOptions[self::TAB_LOGIN]["showFollowsTab"] = $oldOptions[self::TAB_LOGIN]["showFollowsTab"];
            }
        }
        if (isset($oldOptions["disableProfileURLs"])) {
            $newOptions[self::TAB_LOGIN]["enableProfileURLs"] = (int) !$oldOptions["disableProfileURLs"];
        } else if (isset($oldOptions[self::TAB_LOGIN]["enableProfileURLs"])) {
            $newOptions[self::TAB_LOGIN]["enableProfileURLs"] = $oldOptions[self::TAB_LOGIN]["enableProfileURLs"];
        }
        if (isset($oldOptions["isUserByEmail"])) {
            $newOptions[self::TAB_LOGIN]["isUserByEmail"] = $oldOptions["isUserByEmail"];
        } else if (isset($oldOptions[self::TAB_LOGIN]["isUserByEmail"])) {
            $newOptions[self::TAB_LOGIN]["isUserByEmail"] = $oldOptions[self::TAB_LOGIN]["isUserByEmail"];
        }
        if (isset($oldOptions["socialLoginAgreementCheckbox"])) {
            $newOptions[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"] = $oldOptions["socialLoginAgreementCheckbox"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"])) {
            $newOptions[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"] = $oldOptions[self::TAB_SOCIAL]["socialLoginAgreementCheckbox"];
        }
        if (isset($oldOptions["socialLoginInSecondaryForm"])) {
            $newOptions[self::TAB_SOCIAL]["socialLoginInSecondaryForm"] = $oldOptions["socialLoginInSecondaryForm"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["socialLoginInSecondaryForm"])) {
            $newOptions[self::TAB_SOCIAL]["socialLoginInSecondaryForm"] = $oldOptions[self::TAB_SOCIAL]["socialLoginInSecondaryForm"];
        }
        if (isset($oldOptions["enableFbLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableFbLogin"] = $oldOptions["enableFbLogin"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableFbLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableFbLogin"] = $oldOptions[self::TAB_SOCIAL]["enableFbLogin"];
        }
        if (isset($oldOptions["enableFbShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableFbShare"] = $oldOptions["enableFbShare"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableFbShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableFbShare"] = $oldOptions[self::TAB_SOCIAL]["enableFbShare"];
        }
        if (isset($oldOptions["fbAppID"])) {
            $newOptions[self::TAB_SOCIAL]["fbAppID"] = $oldOptions["fbAppID"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["fbAppID"])) {
            $newOptions[self::TAB_SOCIAL]["fbAppID"] = $oldOptions[self::TAB_SOCIAL]["fbAppID"];
        }
        if (isset($oldOptions["fbAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["fbAppSecret"] = $oldOptions["fbAppSecret"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["fbAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["fbAppSecret"] = $oldOptions[self::TAB_SOCIAL]["fbAppSecret"];
        }
        if (isset($oldOptions["fbUseOAuth2"])) {
            $newOptions[self::TAB_SOCIAL]["fbUseOAuth2"] = $oldOptions["fbUseOAuth2"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["fbUseOAuth2"])) {
            $newOptions[self::TAB_SOCIAL]["fbUseOAuth2"] = $oldOptions[self::TAB_SOCIAL]["fbUseOAuth2"];
        }
        if (isset($oldOptions["enableTwitterLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableTwitterLogin"] = $oldOptions["enableTwitterLogin"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableTwitterLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableTwitterLogin"] = $oldOptions[self::TAB_SOCIAL]["enableTwitterLogin"];
        }
        if (isset($oldOptions["enableTwitterShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableTwitterShare"] = $oldOptions["enableTwitterShare"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableTwitterShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableTwitterShare"] = $oldOptions[self::TAB_SOCIAL]["enableTwitterShare"];
        }
        if (isset($oldOptions["twitterAppID"])) {
            $newOptions[self::TAB_SOCIAL]["twitterAppID"] = $oldOptions["twitterAppID"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["twitterAppID"])) {
            $newOptions[self::TAB_SOCIAL]["twitterAppID"] = $oldOptions[self::TAB_SOCIAL]["twitterAppID"];
        }
        if (isset($oldOptions["twitterAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["twitterAppSecret"] = $oldOptions["twitterAppSecret"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["twitterAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["twitterAppSecret"] = $oldOptions[self::TAB_SOCIAL]["twitterAppSecret"];
        }
        if (isset($oldOptions["enableGoogleLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableGoogleLogin"] = $oldOptions["enableGoogleLogin"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableGoogleLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableGoogleLogin"] = $oldOptions[self::TAB_SOCIAL]["enableGoogleLogin"];
        }
        if (isset($oldOptions["enableVkLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableVkLogin"] = $oldOptions["enableVkLogin"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableVkLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableVkLogin"] = $oldOptions[self::TAB_SOCIAL]["enableVkLogin"];
        }
        if (isset($oldOptions["enableVkShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableVkShare"] = $oldOptions["enableVkShare"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableVkShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableVkShare"] = $oldOptions[self::TAB_SOCIAL]["enableVkShare"];
        }
        if (isset($oldOptions["vkAppID"])) {
            $newOptions[self::TAB_SOCIAL]["vkAppID"] = $oldOptions["vkAppID"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["vkAppID"])) {
            $newOptions[self::TAB_SOCIAL]["vkAppID"] = $oldOptions[self::TAB_SOCIAL]["vkAppID"];
        }
        if (isset($oldOptions["vkAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["vkAppSecret"] = $oldOptions["vkAppSecret"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["vkAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["vkAppSecret"] = $oldOptions[self::TAB_SOCIAL]["vkAppSecret"];
        }
        if (isset($oldOptions["enableOkLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableOkLogin"] = $oldOptions["enableOkLogin"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableOkLogin"])) {
            $newOptions[self::TAB_SOCIAL]["enableOkLogin"] = $oldOptions[self::TAB_SOCIAL]["enableOkLogin"];
        }
        if (isset($oldOptions["enableOkShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableOkShare"] = $oldOptions["enableOkShare"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["enableOkShare"])) {
            $newOptions[self::TAB_SOCIAL]["enableOkShare"] = $oldOptions[self::TAB_SOCIAL]["enableOkShare"];
        }
        if (isset($oldOptions["okAppID"])) {
            $newOptions[self::TAB_SOCIAL]["okAppID"] = $oldOptions["okAppID"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["okAppID"])) {
            $newOptions[self::TAB_SOCIAL]["okAppID"] = $oldOptions[self::TAB_SOCIAL]["okAppID"];
        }
        if (isset($oldOptions["okAppKey"])) {
            $newOptions[self::TAB_SOCIAL]["okAppKey"] = $oldOptions["okAppKey"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["okAppKey"])) {
            $newOptions[self::TAB_SOCIAL]["okAppKey"] = $oldOptions[self::TAB_SOCIAL]["okAppKey"];
        }
        if (isset($oldOptions["okAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["okAppSecret"] = $oldOptions["okAppSecret"];
        } else if (isset($oldOptions[self::TAB_SOCIAL]["okAppSecret"])) {
            $newOptions[self::TAB_SOCIAL]["okAppSecret"] = $oldOptions[self::TAB_SOCIAL]["okAppSecret"];
        }
        if (isset($oldOptions["displayRatingOnPost"])) {
            $newOptions[self::TAB_RATING]["displayRatingOnPost"] = $oldOptions["displayRatingOnPost"];
        } else if (isset($oldOptions[self::TAB_RATING]["displayRatingOnPost"])) {
            $newOptions[self::TAB_RATING]["displayRatingOnPost"] = $oldOptions[self::TAB_RATING]["displayRatingOnPost"];
        }
        if (isset($oldOptions["ratingCssOnNoneSingular"])) {
            $newOptions[self::TAB_RATING]["ratingCssOnNoneSingular"] = $oldOptions["ratingCssOnNoneSingular"];
        } else if (isset($oldOptions[self::TAB_RATING]["ratingCssOnNoneSingular"])) {
            $newOptions[self::TAB_RATING]["ratingCssOnNoneSingular"] = $oldOptions[self::TAB_RATING]["ratingCssOnNoneSingular"];
        }
        if (isset($oldOptions["wc_comment_rating_hover_color"])) {
            $newOptions[self::TAB_RATING]["ratingHoverColor"] = $oldOptions["wc_comment_rating_hover_color"];
        } else if (isset($oldOptions[self::TAB_RATING]["ratingHoverColor"])) {
            $newOptions[self::TAB_RATING]["ratingHoverColor"] = $oldOptions[self::TAB_RATING]["ratingHoverColor"];
        }
        if (isset($oldOptions["wc_comment_rating_inactiv_color"])) {
            $newOptions[self::TAB_RATING]["ratingInactiveColor"] = $oldOptions["wc_comment_rating_inactiv_color"];
        } else if (isset($oldOptions[self::TAB_RATING]["ratingInactiveColor"])) {
            $newOptions[self::TAB_RATING]["ratingInactiveColor"] = $oldOptions[self::TAB_RATING]["ratingInactiveColor"];
        }
        if (isset($oldOptions["wc_comment_rating_activ_color"])) {
            $newOptions[self::TAB_RATING]["ratingActiveColor"] = $oldOptions["wc_comment_rating_activ_color"];
        } else if (isset($oldOptions[self::TAB_RATING]["ratingActiveColor"])) {
            $newOptions[self::TAB_RATING]["ratingActiveColor"] = $oldOptions[self::TAB_RATING]["ratingActiveColor"];
        }
        if (isset($oldOptions["commentListLoadType"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["commentListLoadType"] = $oldOptions["commentListLoadType"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["commentListLoadType"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["commentListLoadType"] = $oldOptions[self::TAB_THREAD_DISPLAY]["commentListLoadType"];
        }
        if (isset($oldOptions["isLoadOnlyParentComments"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"] = $oldOptions["isLoadOnlyParentComments"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"] = $oldOptions[self::TAB_THREAD_DISPLAY]["isLoadOnlyParentComments"];
        }
        if (isset($oldOptions["show_sorting_buttons"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["showSortingButtons"] = $oldOptions["show_sorting_buttons"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["showSortingButtons"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["showSortingButtons"] = $oldOptions[self::TAB_THREAD_DISPLAY]["showSortingButtons"];
        }
        if (isset($oldOptions["mostVotedByDefault"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"] = $oldOptions["mostVotedByDefault"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"] = $oldOptions[self::TAB_THREAD_DISPLAY]["mostVotedByDefault"];
        }
        if (isset($oldOptions["reverseChildren"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["reverseChildren"] = $oldOptions["reverseChildren"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["reverseChildren"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["reverseChildren"] = $oldOptions[self::TAB_THREAD_DISPLAY]["reverseChildren"];
        }
        if (isset($oldOptions["enableLastVisitCookie"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"] = $oldOptions["enableLastVisitCookie"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"] = $oldOptions[self::TAB_THREAD_DISPLAY]["highlightUnreadComments"];
        }
        if (isset($oldOptions["showHideCommentLink"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["showCommentLink"] = (int) !$oldOptions["showHideCommentLink"];
        } else if (isset($oldOptions[self::TAB_THREAD_DISPLAY]["showCommentLink"])) {
            $newOptions[self::TAB_THREAD_DISPLAY]["showCommentLink"] = $oldOptions[self::TAB_THREAD_DISPLAY]["showCommentLink"];
        }
        if (isset($oldOptions["hideCommentDate"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["showCommentDate"] = (int) !$oldOptions["hideCommentDate"];
        } else if (isset($oldOptions[self::TAB_THREAD_LAYOUTS]["showCommentDate"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["showCommentDate"] = $oldOptions[self::TAB_THREAD_LAYOUTS]["showCommentDate"];
        }
        if (isset($oldOptions["wc_voting_buttons_show_hide"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["showVotingButtons"] = (int) !$oldOptions["wc_voting_buttons_show_hide"];
        } else if (isset($oldOptions[self::TAB_THREAD_LAYOUTS]["showVotingButtons"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["showVotingButtons"] = $oldOptions[self::TAB_THREAD_LAYOUTS]["showVotingButtons"];
        }
        if (isset($oldOptions["votingButtonsIcon"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"] = $oldOptions["votingButtonsIcon"];
        } else if (isset($oldOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"] = $oldOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsIcon"];
        }
        if (isset($oldOptions["votingButtonsStyle"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"] = $oldOptions["votingButtonsStyle"];
        } else if (isset($oldOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"] = $oldOptions[self::TAB_THREAD_LAYOUTS]["votingButtonsStyle"];
        }
        if (isset($oldOptions["wc_is_guest_can_vote"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"] = $oldOptions["wc_is_guest_can_vote"];
        } else if (isset($oldOptions[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"])) {
            $newOptions[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"] = $oldOptions[self::TAB_THREAD_LAYOUTS]["isGuestCanVote"];
        }
        if (isset($oldOptions["theme"])) {
            $newOptions[self::TAB_THREAD_STYLES]["theme"] = $oldOptions["theme"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["theme"])) {
            $newOptions[self::TAB_THREAD_STYLES]["theme"] = $oldOptions[self::TAB_THREAD_STYLES]["theme"];
        }
        if (isset($oldOptions["wc_comment_username_color"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryColor"] = $oldOptions["wc_comment_username_color"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["primaryColor"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryColor"] = $oldOptions[self::TAB_THREAD_STYLES]["primaryColor"];
        }
        if (isset($oldOptions["wc_new_loaded_comment_bg_color"])) {
            $newOptions[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"] = $oldOptions["wc_new_loaded_comment_bg_color"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"])) {
            $newOptions[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"] = $oldOptions[self::TAB_THREAD_STYLES]["newLoadedCommentBGColor"];
        }
        if (isset($oldOptions["wc_link_button_color"]["primary_button_color"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryButtonColor"] = $oldOptions["wc_link_button_color"]["primary_button_color"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["primaryButtonColor"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryButtonColor"] = $oldOptions[self::TAB_THREAD_STYLES]["primaryButtonColor"];
        }
        if (isset($oldOptions["wc_link_button_color"]["primary_button_bg"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryButtonBG"] = $oldOptions["wc_link_button_color"]["primary_button_bg"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["primaryButtonBG"])) {
            $newOptions[self::TAB_THREAD_STYLES]["primaryButtonBG"] = $oldOptions[self::TAB_THREAD_STYLES]["primaryButtonBG"];
        }
        if (isset($oldOptions["wc_comment_text_size"])) {
            $newOptions[self::TAB_THREAD_STYLES]["commentTextSize"] = $oldOptions["wc_comment_text_size"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["commentTextSize"])) {
            $newOptions[self::TAB_THREAD_STYLES]["commentTextSize"] = $oldOptions[self::TAB_THREAD_STYLES]["commentTextSize"];
        }
        if (isset($oldOptions["disableFontAwesome"])) {
            $newOptions[self::TAB_THREAD_STYLES]["enableFontAwesome"] = (int) !$oldOptions["disableFontAwesome"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["enableFontAwesome"])) {
            $newOptions[self::TAB_THREAD_STYLES]["enableFontAwesome"] = $oldOptions[self::TAB_THREAD_STYLES]["enableFontAwesome"];
        }
        if (isset($oldOptions["wc_custom_css"])) {
            $newOptions[self::TAB_THREAD_STYLES]["customCss"] = $oldOptions["wc_custom_css"];
        } else if (isset($oldOptions[self::TAB_THREAD_STYLES]["customCss"])) {
            $newOptions[self::TAB_THREAD_STYLES]["customCss"] = $oldOptions[self::TAB_THREAD_STYLES]["customCss"];
        }
        if (isset($oldOptions["isNotifyOnCommentApprove"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"] = $oldOptions["isNotifyOnCommentApprove"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"] = $oldOptions[self::TAB_SUBSCRIPTION]["isNotifyOnCommentApprove"];
        }
        if (isset($oldOptions["wc_disable_member_confirm"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["enableMemberConfirm"] = (int) !$oldOptions["wc_disable_member_confirm"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["enableMemberConfirm"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["enableMemberConfirm"] = $oldOptions[self::TAB_SUBSCRIPTION]["enableMemberConfirm"];
        }
        if (isset($oldOptions["disableGuestsConfirm"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"] = (int) !$oldOptions["disableGuestsConfirm"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"] = $oldOptions[self::TAB_SUBSCRIPTION]["enableGuestsConfirm"];
        }
        if (isset($oldOptions["subscriptionType"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["subscriptionType"] = $oldOptions["subscriptionType"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["subscriptionType"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["subscriptionType"] = $oldOptions[self::TAB_SUBSCRIPTION]["subscriptionType"];
        }
        if (isset($oldOptions["wc_show_hide_reply_checkbox"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["showReplyCheckbox"] = $oldOptions["wc_show_hide_reply_checkbox"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["showReplyCheckbox"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["showReplyCheckbox"] = $oldOptions[self::TAB_SUBSCRIPTION]["showReplyCheckbox"];
        }
        if (isset($oldOptions["isReplyDefaultChecked"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"] = $oldOptions["isReplyDefaultChecked"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"] = $oldOptions[self::TAB_SUBSCRIPTION]["isReplyDefaultChecked"];
        }
        if (isset($oldOptions["wc_use_postmatic_for_comment_notification"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"] = $oldOptions["wc_use_postmatic_for_comment_notification"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"] = $oldOptions[self::TAB_SUBSCRIPTION]["usePostmaticForCommentNotification"];
        }
        if (isset($oldOptions["isFollowActive"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isFollowActive"] = $oldOptions["isFollowActive"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["isFollowActive"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["isFollowActive"] = $oldOptions[self::TAB_SUBSCRIPTION]["isFollowActive"];
        }
        if (isset($oldOptions["disableFollowConfirmForUsers"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"] = $oldOptions["disableFollowConfirmForUsers"];
        } else if (isset($oldOptions[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"])) {
            $newOptions[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"] = $oldOptions[self::TAB_SUBSCRIPTION]["disableFollowConfirmForUsers"];
        }
        if (isset($oldOptions["wc_blog_roles"])) {
            $newOptions[self::TAB_LABELS]["blogRoles"] = $oldOptions["wc_blog_roles"];
        } else if (isset($oldOptions[self::TAB_LABELS]["blogRoles"])) {
            $newOptions[self::TAB_LABELS]["blogRoles"] = $oldOptions[self::TAB_LABELS]["blogRoles"];
        }
        if (isset($oldOptions["wc_comment_editable_time"])) {
            $newOptions[self::TAB_MODERATION]["commentEditableTime"] = $oldOptions["wc_comment_editable_time"];
        } else if (isset($oldOptions[self::TAB_MODERATION]["commentEditableTime"])) {
            $newOptions[self::TAB_MODERATION]["commentEditableTime"] = $oldOptions[self::TAB_MODERATION]["commentEditableTime"];
        }
        if (isset($oldOptions["enableStickButton"])) {
            $newOptions[self::TAB_MODERATION]["enableStickButton"] = $oldOptions["enableStickButton"];
        } else if (isset($oldOptions[self::TAB_MODERATION]["enableStickButton"])) {
            $newOptions[self::TAB_MODERATION]["enableStickButton"] = $oldOptions[self::TAB_MODERATION]["enableStickButton"];
        }
        if (isset($oldOptions["enableCloseButton"])) {
            $newOptions[self::TAB_MODERATION]["enableCloseButton"] = $oldOptions["enableCloseButton"];
        } else if (isset($oldOptions[self::TAB_MODERATION]["enableCloseButton"])) {
            $newOptions[self::TAB_MODERATION]["enableCloseButton"] = $oldOptions[self::TAB_MODERATION]["enableCloseButton"];
        }
        if (isset($oldOptions["wc_comment_text_min_length"])) {
            $newOptions[self::TAB_CONTENT]["commentTextMinLength"] = $oldOptions["wc_comment_text_min_length"];
        } else if (isset($oldOptions[self::TAB_CONTENT]["commentTextMinLength"])) {
            $newOptions[self::TAB_CONTENT]["commentTextMinLength"] = $oldOptions[self::TAB_CONTENT]["commentTextMinLength"];
        }
        if (isset($oldOptions["wc_comment_text_max_length"])) {
            $newOptions[self::TAB_CONTENT]["commentTextMaxLength"] = $oldOptions["wc_comment_text_max_length"];
        } else if (isset($oldOptions[self::TAB_CONTENT]["commentTextMaxLength"])) {
            $newOptions[self::TAB_CONTENT]["commentTextMaxLength"] = $oldOptions[self::TAB_CONTENT]["commentTextMaxLength"];
        }
        if (isset($oldOptions["enableImageConversion"])) {
            $newOptions[self::TAB_CONTENT]["enableImageConversion"] = $oldOptions["enableImageConversion"];
        } else if (isset($oldOptions[self::TAB_CONTENT]["enableImageConversion"])) {
            $newOptions[self::TAB_CONTENT]["enableImageConversion"] = $oldOptions[self::TAB_CONTENT]["enableImageConversion"];
        }
        if (isset($oldOptions["commentWordsLimit"])) {
            $newOptions[self::TAB_CONTENT]["commentReadMoreLimit"] = $oldOptions["commentWordsLimit"];
        } else if (isset($oldOptions[self::TAB_CONTENT]["commentReadMoreLimit"])) {
            $newOptions[self::TAB_CONTENT]["commentReadMoreLimit"] = $oldOptions[self::TAB_CONTENT]["commentReadMoreLimit"];
        }
        if (isset($oldOptions["wc_comment_list_update_type"])) {
            $newOptions[self::TAB_LIVE]["commentListUpdateType"] = $oldOptions["wc_comment_list_update_type"] == 2 ? 0 : $oldOptions["wc_comment_list_update_type"];
        } else if (isset($oldOptions[self::TAB_LIVE]["commentListUpdateType"])) {
            $newOptions[self::TAB_LIVE]["commentListUpdateType"] = $oldOptions[self::TAB_LIVE]["commentListUpdateType"];
        }
        if (isset($oldOptions["wc_live_update_guests"])) {
            $newOptions[self::TAB_LIVE]["liveUpdateGuests"] = (int) !$oldOptions["wc_live_update_guests"];
        } else if (isset($oldOptions[self::TAB_LIVE]["liveUpdateGuests"])) {
            $newOptions[self::TAB_LIVE]["liveUpdateGuests"] = $oldOptions[self::TAB_LIVE]["liveUpdateGuests"];
        }
        if (isset($oldOptions["wc_comment_list_update_timer"])) {
            $newOptions[self::TAB_CONTENT]["commentListUpdateTimer"] = $oldOptions["wc_comment_list_update_timer"];
        } else if (isset($oldOptions[self::TAB_CONTENT]["commentListUpdateTimer"])) {
            $newOptions[self::TAB_CONTENT]["commentListUpdateTimer"] = $oldOptions[self::TAB_CONTENT]["commentListUpdateTimer"];
        }
        if (isset($oldOptions["isEnableOnHome"])) {
            $newOptions[self::TAB_GENERAL]["isEnableOnHome"] = $oldOptions["isEnableOnHome"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["isEnableOnHome"])) {
            $newOptions[self::TAB_GENERAL]["isEnableOnHome"] = $oldOptions[self::TAB_GENERAL]["isEnableOnHome"];
        }
        if (isset($oldOptions["isNativeAjaxEnabled"])) {
            $newOptions[self::TAB_GENERAL]["isNativeAjaxEnabled"] = $oldOptions["isNativeAjaxEnabled"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["isNativeAjaxEnabled"])) {
            $newOptions[self::TAB_GENERAL]["isNativeAjaxEnabled"] = $oldOptions[self::TAB_GENERAL]["isNativeAjaxEnabled"];
        }
        if (isset($oldOptions["commentLinkFilter"])) {
            $newOptions[self::TAB_GENERAL]["commentLinkFilter"] = $oldOptions["commentLinkFilter"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["commentLinkFilter"])) {
            $newOptions[self::TAB_GENERAL]["commentLinkFilter"] = $oldOptions[self::TAB_GENERAL]["commentLinkFilter"];
        }
        if (isset($oldOptions["wpdiscuz_redirect_page"])) {
            $newOptions[self::TAB_GENERAL]["redirectPage"] = $oldOptions["wpdiscuz_redirect_page"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["redirectPage"])) {
            $newOptions[self::TAB_GENERAL]["redirectPage"] = $oldOptions[self::TAB_GENERAL]["redirectPage"];
        }
        if (isset($oldOptions["wc_simple_comment_date"])) {
            $newOptions[self::TAB_GENERAL]["simpleCommentDate"] = $oldOptions["wc_simple_comment_date"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["simpleCommentDate"])) {
            $newOptions[self::TAB_GENERAL]["simpleCommentDate"] = $oldOptions[self::TAB_GENERAL]["simpleCommentDate"];
        }
        if (isset($oldOptions["wc_is_use_po_mo"])) {
            $newOptions[self::TAB_GENERAL]["isUsePoMo"] = $oldOptions["wc_is_use_po_mo"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["isUsePoMo"])) {
            $newOptions[self::TAB_GENERAL]["isUsePoMo"] = $oldOptions[self::TAB_GENERAL]["isUsePoMo"];
        }
        if (isset($oldOptions["wc_show_plugin_powerid_by"])) {
            $newOptions[self::TAB_GENERAL]["showPluginPoweredByLink"] = $oldOptions["wc_show_plugin_powerid_by"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["showPluginPoweredByLink"])) {
            $newOptions[self::TAB_GENERAL]["showPluginPoweredByLink"] = $oldOptions[self::TAB_GENERAL]["showPluginPoweredByLink"];
        }
        if (isset($oldOptions["isGravatarCacheEnabled"])) {
            $newOptions[self::TAB_GENERAL]["isGravatarCacheEnabled"] = $oldOptions["isGravatarCacheEnabled"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["isGravatarCacheEnabled"])) {
            $newOptions[self::TAB_GENERAL]["isGravatarCacheEnabled"] = $oldOptions[self::TAB_GENERAL]["isGravatarCacheEnabled"];
        }
        if (isset($oldOptions["gravatarCacheMethod"])) {
            $newOptions[self::TAB_GENERAL]["gravatarCacheMethod"] = $oldOptions["gravatarCacheMethod"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["gravatarCacheMethod"])) {
            $newOptions[self::TAB_GENERAL]["gravatarCacheMethod"] = $oldOptions[self::TAB_GENERAL]["gravatarCacheMethod"];
        }
        if (isset($oldOptions["gravatarCacheTimeout"])) {
            $newOptions[self::TAB_GENERAL]["gravatarCacheTimeout"] = $oldOptions["gravatarCacheTimeout"];
        } else if (isset($oldOptions[self::TAB_GENERAL]["gravatarCacheTimeout"])) {
            $newOptions[self::TAB_GENERAL]["gravatarCacheTimeout"] = $oldOptions[self::TAB_GENERAL]["gravatarCacheTimeout"];
        }
        if ($update) {
            $this->initOptions($newOptions);
            $this->updateOptions();
        }
        return $newOptions;
    }

    public function printDocLink($docUrl) {
        if ($docUrl && $docUrl !== "#") {
            echo "<a href='" . esc_url_raw($docUrl) . "' title='" . esc_attr("Read the documentation", "wpdiscuz") . "' target='_blank'><i class='far fa-question-circle'></i></a>";
        }
    }

    public function settingsArray() {
        $settings = [
            "core" => [
                WpdiscuzCore::TAB_FORM => [
                    "title" => esc_html__("Comment Form Settings", "wpdiscuz"),
                    "title_original" => "Comment Form Settings",
                    "icon" => "box-forms.png",
                    "icon-height" => "50px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_FORM . ".php",
                    "options" => [
                        "storeCommenterData" => [
                            "label" => esc_html__("Keep guest commenter credentials in browser cookies for X days", "wpdiscuz"),
                            "label_original" => "Keep guest commenter credentials in browser cookies for X days",
                            "description" => esc_html__("wpDiscuz uses WordPress functions to keep guest Name, Email and Website information in cookies. Those are used to fill according fields of comment form on next commenting time. Set this option value -1 to make it unlimited. Set this option value 0 to clear those data when user closes browser.", "wpdiscuz"),
                            "description_original" => "wpDiscuz uses WordPress functions to keep guest Name, Email and Website information in cookies. Those are used to fill according fields of comment form on next commenting time. Set this option value -1 to make it unlimited. Set this option value 0 to clear those data when user closes browser.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/#keep-guest-commenter-credentials-in-browser-cookies-for-x-days"
                        ],
                        "commenterNameLength" => [
                            "label" => esc_html__("Comment author name length (for guests only)", "wpdiscuz"),
                            "label_original" => "Comment author name length (for guests only)",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/#comment-author-name-length-for-guests-only"
                        ],
                        "commentFormView" => [
                            "label" => esc_html__("Comment Form View", "wpdiscuz"),
                            "label_original" => "Comment Form View",
                            "description" => esc_html__('By default, only the comment text field is visible. When you click on the comment text field it opens all other fields (Name, Email, Website, etc...). If you want to keep all fields open, please set this option "expended".', "wpdiscuz"),
                            "description_original" => 'By default, only the comment text field is visible. When you click on the comment text field it opens all other fields (Name, Email, Website, etc...). If you want to keep all fields open, please set this option "expended".',
                            "docurl" => ""
                        ],
                        "enableDropAnimation" => [
                            "label" => esc_html__("Enable drop animation for comment form and subscription bar", "wpdiscuz"),
                            "label_original" => "Enable drop animation for comment form and subscription bar",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/#enable-drop-animation-for-comment-form-and-subscription-bar"
                        ],
                        "richEditor" => [
                            "label" => esc_html__("Load Rich Editor", "wpdiscuz"),
                            "label_original" => "Load Rich Editor for",
                            "description" => esc_html__("Search engines rank web pages for mobile devices totally different. For the mobile devices, there are more restrictions for JS and CSS files loading. This is the main reason why wpDiscuz disables the Rich Editor for mobile devices by default. It's only enabled for desktop. If you have good cache and website optimizer plugins you can enable the rich editor for mobile devices as well.", "wpdiscuz"),
                            "description_original" => "Search engines rank web pages for mobile devices totally different. For the mobile devices, there are more restrictions for JS and CSS files loading. This is the main reason why wpDiscuz disables the Rich Editor for mobile devices by default. It's only enabled for desktop. If you have good cache and website optimizer plugins you can enable the rich editor for mobile devices as well.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/#load-rich-editor"
                        ],
                        "editorToolbar" => [
                            "label" => esc_html__("Rich Editor Toolbar Buttons", "wpdiscuz"),
                            "label_original" => "Rich Editor Toolbar Buttons",
                            "description" => esc_html__("Please click on buttons to disable or enable. The enabled buttons are colored green, the disabled buttons are gray. If you want to disable the whole formatting toolbar, please click on the [Disable formatting buttons] button. Options to manage Image Attachment button are located in 'Comment Content and Media' setting page.", "wpdiscuz"),
                            "description_original" => "Please click on buttons to disable or enable. The enabled buttons are colored green, the disabled buttons are gray. If you want to disable the whole formatting toolbar, please click on the [Disable formatting buttons] button. Options to manage Image Attachment button are located in 'Comment Content and Media' setting page.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-form/#rich-editor-toolbar-buttons"
                        ],
                        "enableQuickTags" => [
                            "label" => esc_html__("Enable Quicktags", "wpdiscuz"),
                            "label_original" => "Enable Quicktags",
                            "description" => esc_html__('Quicktag is a on-click button that inserts HTML in to comment textarea. For example the "b" Quicktag will insert the HTML bold tags < b > < /b >.', "wpdiscuz"),
                            "description_original" => 'Quicktag is a on-click button that inserts HTML in to comment textarea. For example the "b" Quicktag will insert the HTML bold tags < b > < /b >.',
                            "docurl" => ""
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_RECAPTCHA => [
                    "title" => esc_html__("Google reCAPTCHA", "wpdiscuz"),
                    "title_original" => "Google reCAPTCHA",
                    "icon" => "box-recaptcha.png",
                    "icon-height" => "56px",
                    "status" => $this->recaptcha["siteKey"] && $this->recaptcha["secretKey"] ? "ok" : "note",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_RECAPTCHA . ".php",
                    "options" => [
                        "siteKey" => [
                            "label" => esc_html__("Site Key", "wpdiscuz"),
                            "label_original" => "Site Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#site-keys"
                        ],
                        "secretKey" => [
                            "label" => esc_html__("Secret Key", "wpdiscuz"),
                            "label_original" => "Secret Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#recaptcha-version-2-%E2%80%93-site-key-and-secret-key"
                        ],
                        "theme" => [
                            "label" => esc_html__("reCAPTCHA Theme", "wpdiscuz"),
                            "label_original" => "reCAPTCHA Theme",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#recaptcha-theme"
                        ],
                        "lang" => [
                            "label" => esc_html__("reCAPTCHA Language", "wpdiscuz"),
                            "label_original" => "reCAPTCHA Language",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#recaptcha-language"
                        ],
                        "requestMethod" => [
                            "label" => esc_html__("Request Method", "wpdiscuz"),
                            "label_original" => "Request Method",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#request-method"
                        ],
                        "showForGuests" => [
                            "label" => esc_html__("Enable for Guests", "wpdiscuz"),
                            "label_original" => "Enable for Guests",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#enable-for-guests"
                        ],
                        "showForUsers" => [
                            "label" => esc_html__("Enable for Logged-in Users", "wpdiscuz"),
                            "label_original" => "Enable for Logged-in Users",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#enable-for-logged-in-users"
                        ],
                        "isShowOnSubscribeForm" => [
                            "label" => esc_html__("Display on Subscription Form", "wpdiscuz"),
                            "label_original" => "Display on Subscription Form",
                            "description" => "",
                            "label_original" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/google-recaptcha/#display-on-subscription-form"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_LOGIN => [
                    "title" => esc_html__("User Authorization and Profile Data", "wpdiscuz"),
                    "title_original" => "User Authorization and Profile Data",
                    "icon" => "box-login.png",
                    "icon-height" => "65px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_LOGIN . ".php",
                    "options" => [
                        "showLoggedInUsername" => [
                            "label" => esc_html__("Display logged-in user name and logout link on comment form", "wpdiscuz"),
                            "label_original" => "Display logged-in user name and logout link on comment form",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/#display-logged-in-user-name-and-logout-link-on-comment-form"
                        ],
                        "showLoginLinkForGuests" => [
                            "label" => esc_html__('Show "Login" link on comment form', "wpdiscuz"),
                            "label_original" => 'Show "Login" link on comment form',
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/#show-%E2%80%9Clogin%E2%80%9D-link-on-comment-form"
                        ],
                        "myContentSettings" => [
                            "label" => esc_html__('"My Content and Settings" button', "wpdiscuz"),
                            "label_original" => '"My Content and Settings" button',
                            "description" => esc_html__('The "My Content & Settings" button is located in comment filter panel on top of all comments, right after the [X Comments] phrase. This button opens a pop-up window allowing commenters manage their content and settings.', "wpdiscuz"),
                            "description_original" => 'The "My Content & Settings" button is located in comment filter panel on top of all comments, right after the [X Comments] phrase. This button opens a pop-up window allowing commenters manage their content and settings.',
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/#%E2%80%9Cmy-content-and-settings%E2%80%9D-button"
                        ],
                        "enableProfileURLs" => [
                            "label" => esc_html__("Enable Profiles URL", "wpdiscuz"),
                            "label_original" => "Enable Profiles URL",
                            "description" => sprintf(esc_html__("By default wpDiscuz adds a link with comment author avatar to the author profile page, you can disable this link using this option. However in case you use some plugin with User Profile page, you should keep this option enabled. wpDiscuz is well integrated with %s, BuddyPress and Ultimate Member profile builder plugins.", "wpdiscuz"), "<a href='https://wordpress.org/plugins/wpforo/' target='_blank'>wpForo Forum</a>"),
                            "description_original" => "By default wpDiscuz adds a link with comment author avatar to the author profile page, you can disable this link using this option. However in case you use some plugin with User Profile page, you should keep this option enabled. wpDiscuz is well integrated with <a href='https://wordpress.org/plugins/wpforo/' target='_blank'>wpForo Forum</a>, BuddyPress and Ultimate Member profile builder plugins.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/#enable-profiles-url"
                        ],
                        "websiteAsProfileUrl" => [
                            "label" => esc_html__("Use Website URL as Profile URL", "wpdiscuz"),
                            "label_original" => "Use Website URL as Profile URL",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "isUserByEmail" => [
                            "label" => esc_html__("Use guest email to detect registered account", "wpdiscuz"),
                            "label_original" => "Use guest email to detect registered account",
                            "description" => esc_html__("Sometimes registered users comment as guest using the same email address. wpDiscuz can detect the account role using guest email and display commenter label correctly.", "wpdiscuz"),
                            "description_original" => "Sometimes registered users comment as guest using the same email address. wpDiscuz can detect the account role using guest email and display commenter label correctly.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-authorization-and-profile-data/#use-guest-email-to-detect-registered-account"
                        ],
                        "loginUrl" => [
                            "label" => esc_html__("Login URL", "wpdiscuz"),
                            "label_original" => "Login URL",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_SOCIAL => [
                    "title" => esc_html__("Social Login and Share", "wpdiscuz"),
                    "title_original" => "Social Login and Share",
                    "icon" => "box-social.png",
                    "icon-height" => "80px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_SOCIAL . ".php",
                    "options" => [
                        "socialLoginAgreementCheckbox" => [
                            "label" => esc_html__("User agreement prior to a social login action", "wpdiscuz"),
                            "label_original" => "User agreement prior to a social login action",
                            "description" => esc_html__("If this option is enabled, all Social Login buttons become not-clickable until user accept automatic account creation process based on his/her Social Network Account shared information (email, name). This checkbox and appropriate information will be displayed when user click on a social login button, prior to the login process. This extra step is added to comply with the GDPR", "wpdiscuz") . " <a href='https://gdpr-info.eu/art-22-gdpr/' target='_blank' rel='noreferrer'>(Article 22).</a> <br>" . esc_html__("The note text and the label of this checkbox can be managed in Comments > Phrases > Social Login tab.", "wpdiscuz"),
                            "description_original" => "If this option is enabled, all Social Login buttons become not-clickable until user accept automatic account creation process based on his/her Social Network Account shared information (email, name). This checkbox and appropriate information will be displayed when user click on a social login button, prior to the login process. This extra step is added to comply with the GDPR <a href='https://gdpr-info.eu/art-22-gdpr/' target='_blank' rel='noreferrer'>(Article 22).</a> <br>The note text and the label of this checkbox can be managed in Comments > Phrases > Social Login tab.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/#user-agreement-prior-to-a-social-login-action"
                        ],
                        "socialLoginInSecondaryForm" => [
                            "label" => esc_html__("Display social login buttons on reply forms", "wpdiscuz"),
                            "label_original" => "Display social login buttons on reply forms",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/#display-social-login-buttons-on-reply-forms"
                        ],
                        "displayIconOnAvatar" => [
                            "label" => esc_html__("Display Social Network Icon on User Avatars", "wpdiscuz"),
                            "label_original" => "Display Social Network Icon on User Avatars",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableFbLogin" => [
                            "label" => esc_html__("Facebook Login Button", "wpdiscuz"),
                            "label_original" => "Facebook Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "fbUseOAuth2" => [
                            "label" => esc_html__("Use Facebook OAuth2", "wpdiscuz"),
                            "label_original" => "Use Facebook OAuth2",
                            "description" => esc_html__("If you enable this option, please make sure you've inserted the Valid OAuth Redirect URI in according field when you create Facebook Login App. Your website OAuth Redirect URI is displayed above.", "wpdiscuz"),
                            "description_original" => "If you enable this option, please make sure you've inserted the Valid OAuth Redirect URI in according field when you create Facebook Login App. Your website OAuth Redirect URI is displayed above.",
                            "docurl" => ""
                        ],
                        "enableFbShare" => [
                            "label" => esc_html__("Facebook Share Button", "wpdiscuz"),
                            "label_original" => "Facebook Share Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "fbAppID" => [
                            "label" => esc_html__("Facebook Application ID", "wpdiscuz"),
                            "label_original" => "Facebook Application ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/facebook-app-configuration/"
                        ],
                        "fbAppSecret" => [
                            "label" => esc_html__("Facebook Application Secret", "wpdiscuz"),
                            "label_original" => "Facebook Application Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/facebook-app-configuration/"
                        ],
                        "enableTwitterLogin" => [
                            "label" => esc_html__("Twitter Login Button", "wpdiscuz"),
                            "label_original" => "Twitter Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableTwitterShare" => [
                            "label" => esc_html__("Twitter Share Button", "wpdiscuz"),
                            "label_original" => "Twitter Share Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "twitterAppID" => [
                            "label" => esc_html__("Twitter - Consumer Key (API Key)", "wpdiscuz"),
                            "label_original" => "Twitter - Consumer Key (API Key)",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/twitter-app-configuration/"
                        ],
                        "twitterAppSecret" => [
                            "label" => esc_html__("Twitter - Consumer Secret (API Secret)", "wpdiscuz"),
                            "label_original" => "Twitter - Consumer Secret (API Secret)",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/twitter-app-configuration/"
                        ],
                        "enableGoogleLogin" => [
                            "label" => esc_html__("Google Login Button", "wpdiscuz"),
                            "label_original" => "Google Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "googleClientID" => [
                            "label" => esc_html__("Google Client ID", "wpdiscuz"),
                            "label_original" => "Google Client ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/google-app-configuration/"
                        ],
                        "googleClientSecret" => [
                            "label" => esc_html__("Google Client Secret", "wpdiscuz"),
                            "label_original" => "Google Client Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/google-app-configuration/"
                        ],
                        "enableDisqusLogin" => [
                            "label" => esc_html__("Disqus Login Button", "wpdiscuz"),
                            "label_original" => "Disqus Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "disqusPublicKey" => [
                            "label" => esc_html__("Disqus Public Key", "wpdiscuz"),
                            "label_original" => "Disqus Public Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/disqus-app-configuration/"
                        ],
                        "disqusSecretKey" => [
                            "label" => esc_html__("Disqus Secret Key", "wpdiscuz"),
                            "label_original" => "Disqus Secret Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/disqus-app-configuration/"
                        ],
                        "enableWordpressLogin" => [
                            "label" => esc_html__("WordPress Login Button", "wpdiscuz"),
                            "label_original" => "WordPress Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "wordpressClientID" => [
                            "label" => esc_html__("WordPress Client ID", "wpdiscuz"),
                            "label_original" => "WordPress Client ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/wordpress-com-app-configuration/"
                        ],
                        "wordpressClientSecret" => [
                            "label" => esc_html__("WordPress Client Secret", "wpdiscuz"),
                            "label_original" => "WordPress Client Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/wordpress-com-app-configuration/"
                        ],
                        "enableInstagramLogin" => [
                            "label" => esc_html__("Instagram Login Button", "wpdiscuz"),
                            "label_original" => "Instagram Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "instagramAppID" => [
                            "label" => esc_html__("Instagram App ID", "wpdiscuz"),
                            "label_original" => "Instagram App ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/instagram-app-configuration/"
                        ],
                        "instagramAppSecret" => [
                            "label" => esc_html__("Instagram App Secret", "wpdiscuz"),
                            "label_original" => "Instagram App Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/instagram-app-configuration/"
                        ],
                        "enableLinkedinLogin" => [
                            "label" => esc_html__("LinkedIn Login Button", "wpdiscuz"),
                            "label_original" => "LinkedIn Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "linkedinClientID" => [
                            "label" => esc_html__("LinkedIn Client ID", "wpdiscuz"),
                            "label_original" => "LinkedIn Client ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/linkedin-app-configuration/"
                        ],
                        "linkedinClientSecret" => [
                            "label" => esc_html__("LinkedIn Client Secret", "wpdiscuz"),
                            "label_original" => "LinkedIn Client Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/linkedin-app-configuration/"
                        ],
                        "enableWhatsappShare" => [
                            "label" => esc_html__("WhatsApp Share Button", "wpdiscuz"),
                            "label_original" => "WhatsApp Share Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableYandexLogin" => [
                            "label" => esc_html__("Yandex Login Button", "wpdiscuz"),
                            "label_original" => "Yandex Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "yandexID" => [
                            "label" => esc_html__("Yandex ID", "wpdiscuz"),
                            "label_original" => "Yandex ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/yandex-app-configuration/"
                        ],
                        "yandexPassword" => [
                            "label" => esc_html__("Yandex Password", "wpdiscuz"),
                            "label_original" => "Yandex Password",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/yandex-app-configuration/"
                        ],
                        "enableMailruLogin" => [
                            "label" => esc_html__("Mail.ru Login Button", "wpdiscuz"),
                            "label_original" => "Mail.ru Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "mailruClientID" => [
                            "label" => esc_html__("Mail.ru Client ID", "wpdiscuz"),
                            "label_original" => "Mail.ru Client ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/mail-ru-app-configuration/"
                        ],
                        "mailruClientSecret" => [
                            "label" => esc_html__("Mail.ru Client Secret", "wpdiscuz"),
                            "label_original" => "Mail.ru Client Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/mail-ru-app-configuration/"
                        ],
                        "enableWeiboLogin" => [
                            "label" => esc_html__("Weibo Login Button", "wpdiscuz"),
                            "label_original" => "Weibo Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "weiboKey" => [
                            "label" => esc_html__("Weibo App Key", "wpdiscuz"),
                            "label_original" => "Weibo App Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "weiboSecret" => [
                            "label" => esc_html__("Weibo App Secret", "wpdiscuz"),
                            "label_original" => "Weibo App Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableWechatLogin" => [
                            "label" => esc_html__("WeChat Login Button", "wpdiscuz"),
                            "label_original" => "WeChat Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "wechatAppID" => [
                            "label" => esc_html__("WeChat App ID", "wpdiscuz"),
                            "label_original" => "WeChat App ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "wechatSecret" => [
                            "label" => esc_html__("WeChat Secret", "wpdiscuz"),
                            "label_original" => "WeChat Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableQQLogin" => [
                            "label" => esc_html__("QQ Login Button", "wpdiscuz"),
                            "label_original" => "QQ Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "qqAppID" => [
                            "label" => esc_html__("QQ AppID", "wpdiscuz"),
                            "label_original" => "QQ AppID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "qqSecret" => [
                            "label" => esc_html__("QQ AppKey", "wpdiscuz"),
                            "label_original" => "QQ AppKey",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableBaiduLogin" => [
                            "label" => esc_html__("Baidu Login Button", "wpdiscuz"),
                            "label_original" => "Baidu Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "baiduAppID" => [
                            "label" => esc_html__("Baidu Client ID", "wpdiscuz"),
                            "label_original" => "Baidu Client ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "baiduSecret" => [
                            "label" => esc_html__("Baidu Client Secret", "wpdiscuz"),
                            "label_original" => "Baidu Client Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableVkLogin" => [
                            "label" => esc_html__("VK Login Button", "wpdiscuz"),
                            "label_original" => "VK Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableVkShare" => [
                            "label" => esc_html__("VK Share Button", "wpdiscuz"),
                            "label_original" => "VK Share Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "vkAppID" => [
                            "label" => esc_html__("VK Application ID", "wpdiscuz"),
                            "label_original" => "VK Application ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/vk-app-configuration/"
                        ],
                        "vkAppSecret" => [
                            "label" => esc_html__("VK Secure Key", "wpdiscuz"),
                            "label_original" => "VK Secure Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/social-login-and-share/vk-app-configuration/"
                        ],
                        "enableOkLogin" => [
                            "label" => esc_html__("OK Login Button", "wpdiscuz"),
                            "label_original" => "OK Login Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableOkShare" => [
                            "label" => esc_html__("OK Share Button", "wpdiscuz"),
                            "label_original" => "OK Share Button",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "okAppID" => [
                            "label" => esc_html__("OK Application ID", "wpdiscuz"),
                            "label_original" => "OK Application ID",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://apiok.ru/en/dev/app/create"
                        ],
                        "okAppKey" => [
                            "label" => esc_html__("OK Application Key", "wpdiscuz"),
                            "label_original" => "OK Application Key",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://apiok.ru/en/dev/app/create"
                        ],
                        "okAppSecret" => [
                            "label" => esc_html__("OK Application Secret", "wpdiscuz"),
                            "label_original" => "OK Application Secret",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://apiok.ru/en/dev/app/create"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_RATING => [
                    "title" => esc_html__("Article and Comment Rating", "wpdiscuz"),
                    "title_original" => "Article and Comment Rating",
                    "icon" => "box-rating.png",
                    "icon-height" => "46px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_RATING . ".php",
                    "options" => [
                        "enablePostRatingSchema" => [
                            "label" => esc_html__("Enable Aggregate Rating Schema", "wpdiscuz"),
                            "label_original" => "Enable Aggregate Rating Schema",
                            "description" => esc_html__("Aggregate rating schema is a code integrated with post rating HTML. This enables Google to feature your post ratings and attract customers with it. When searching the internet, people will see your posts search results with star ratings. Even though those results are not at the top of search engine results page, those sites caught people attention first.", "wpdiscuz"),
                            "description_original" => "Aggregate rating schema is a code integrated with post rating HTML. This enables Google to feature your post ratings and attract customers with it. When searching the internet, people will see your posts search results with star ratings. Even though those results are not at the top of search engine results page, those sites caught people attention first.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/article-and-comment-rating/#enable-aggregate-rating-schema"
                        ],
                        "displayRatingOnPost" => [
                            "label" => esc_html__("Display Ratings", "wpdiscuz"),
                            "label_original" => "Display Ratings",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/article-and-comment-rating/#display-ratings"
                        ],
                        "ratingStarColors" => [
                            "label" => esc_html__("Rating Star Colors", "wpdiscuz"),
                            "label_original" => "Rating Star Colors",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ]
                    ]
                ],
                WpdiscuzCore::TAB_THREAD_DISPLAY => [
                    "title" => esc_html__("Comment Thread Displaying", "wpdiscuz"),
                    "title_original" => "Comment Thread Displaying",
                    "icon" => "box-threads.png",
                    "icon-height" => "58px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_THREAD_DISPLAY . ".php",
                    "options" => [
                        "firstLoadWithAjax" => [
                            "label" => esc_html__("Comment List Loading Type", "wpdiscuz"),
                            "label_original" => "Comment List Loading Type",
                            "description" => esc_html__("Keep your page loading speed high by disabling comments loading. Once the page loading is complete, this option will initiate AJAX request and load comments without affecting page loading speed. Also, you can select the [View Comments] button option to allow visitors load comments manually whenever they want.", "wpdiscuz"),
                            "description_original" => "Keep your page loading speed high by disabling comments loading. Once the page loading is complete, this option will initiate AJAX request and load comments without affecting page loading speed. Also, you can select the [View Comments] button option to allow visitors load comments manually whenever they want.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#comment-list-loading-type"
                        ],
                        "isLoadOnlyParentComments" => [
                            "label" => __("Display only parent comments and <u>view replies &or;</u> button", "wpdiscuz"),
                            "label_original" => "Display only parent comments and <u>view replies &or;</u> button",
                            "description" => esc_html__("If this option is enabled only parent comments will be displayed. This increases page load speed and keeps pages light. If visitor wants to read replies he/she just need to click on [view replies (12)] button located on all parent comments which have replies.", "wpdiscuz"),
                            "description_original" => "If this option is enabled only parent comments will be displayed. This increases page load speed and keeps pages light. If visitor wants to read replies he/she just need to click on [view replies (12)] button located on all parent comments which have replies.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#display-only-parent-comments-and-view-replies-%E2%88%A8-button"
                        ],
                        "showReactedFilterButton" => [
                            "label" => esc_html__('Display "Most Reacted Comments" filter button', "wpdiscuz"),
                            "label_original" => 'Display "Most Reacted Comments" filter button',
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#display-%E2%80%9Cmost-reacted-comments%E2%80%9D-filter-button"
                        ],
                        "showHottestFilterButton" => [
                            "label" => esc_html__('Display "Hottest Comment Threads" filter button', "wpdiscuz"),
                            "label_original" => 'Display "Hottest Comment Threads" filter button',
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#display-%E2%80%9Chottest-comment-threads%E2%80%9D-filter-button"
                        ],
                        "showSortingButtons" => [
                            "label" => esc_html__("Display Comment Sorting Options", "wpdiscuz"),
                            "label_original" => "Display Comment Sorting Options",
                            "description" => esc_html__("This option enables comment sorting buttons (newest | oldest | most voted). Sorting buttons are not available for the default comments pagination type [1][2][3]... It's only active for [Load more] and other AJAX pagination types.", "wpdiscuz"),
                            "description_original" => "This option enables comment sorting buttons (newest | oldest | most voted). Sorting buttons are not available for the default comments pagination type [1][2][3]... It's only active for [Load more] and other AJAX pagination types.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#display-comment-sorting-options"
                        ],
                        "mostVotedByDefault" => [
                            "label" => esc_html__('Set comments order to "Most voted" by default', "wpdiscuz"),
                            "label_original" => 'Set comments order to "Most voted" by default',
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#set-comments-order-to-%E2%80%9Cmost-voted%E2%80%9D-by-default"
                        ],
                        "reverseChildren" => [
                            "label" => esc_html__("Reverse Child Comments Order", "wpdiscuz"),
                            "label_original" => "Reverse Child Comments Order",
                            "description" => esc_html__("By default child comments are sorted by oldest on top. Using this option you can revers child comments order and sort them by newest on top.", "wpdiscuz"),
                            "description_original" => "By default child comments are sorted by oldest on top. Using this option you can revers child comments order and sort them by newest on top.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#reverse-child-comments-order"
                        ],
                        "commentListLoadType" => [
                            "label" => esc_html__("Comments Pagination Type", "wpdiscuz"),
                            "label_original" => "Comments Pagination Type",
                            "description" => esc_html__('You can manage the number of comments for [Load more] option in Settings > Discussion page, using "Break comments into pages with [X] top level comments per page" option. To show the default Wordpress comment pagination you should enable the checkbox on beginning of the same option.', "wpdiscuz"),
                            "description_original" => 'You can manage the number of comments for [Load more] option in Settings > Discussion page, using "Break comments into pages with [X] top level comments per page" option. To show the default Wordpress comment pagination you should enable the checkbox on beginning of the same option.',
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#comments-pagination-type"
                        ],
                        "highlightUnreadComments" => [
                            "label" => esc_html__("Highlight Unread Comments", "wpdiscuz"),
                            "label_original" => "Highlight Unread Comments",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-displaying/#highlight-unread-comments"
                        ],
                        "scrollToComment" => [
                            "label" => __("Scroll to the comment after posting", "wpdiscuz"),
                            "label_original" => "Scroll to the comment after posting",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "orderCommentsBy" => [
                            "label" => __("Newest and oldest comment ordering by", "wpdiscuz"),
                            "label_original" => "Newest and oldest comment ordering by",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_THREAD_LAYOUTS => [
                    "title" => esc_html__("Comment Thread Features", "wpdiscuz"),
                    "title_original" => "Comment Thread Features",
                    "icon" => "box-layouts.png",
                    "icon-height" => "50px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_THREAD_LAYOUTS . ".php",
                    "options" => [
                        "showCommentLink" => [
                            "label" => esc_html__("Show Comment Link", "wpdiscuz"),
                            "label_original" => "Show Comment Link",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#show-comment-link"
                        ],
                        "showCommentDate" => [
                            "label" => esc_html__("Show Comment Date", "wpdiscuz"),
                            "label_original" => "Show Comment Date",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "showVotingButtons" => [
                            "label" => esc_html__("Show Voting Buttons", "wpdiscuz"),
                            "label_original" => "Show Voting Buttons",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#voting-liking-buttons"
                        ],
                        "votingButtonsIcon" => [
                            "label" => esc_html__("Voting Buttons Icon", "wpdiscuz"),
                            "label_original" => "Voting Buttons Icon",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#voting-liking-buttons"
                        ],
                        "votingButtonsStyle" => [
                            "label" => esc_html__("Comment Voting Result Mode", "wpdiscuz"),
                            "label_original" => "Comment Voting Result Mode",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#voting-liking-buttons"
                        ],
                        "enableDislikeButton" => [
                            "label" => esc_html__("Enable down vote button (dislike)", "wpdiscuz"),
                            "label_original" => "Enable down vote button (dislike)",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "isGuestCanVote" => [
                            "label" => esc_html__("Allow Guests to Vote for Comments", "wpdiscuz"),
                            "label_original" => "Allow Guests to Vote for Comments",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#voting-liking-buttons"
                        ],
                        "highlightVotingButtons" => [
                            "label" => esc_html__("Highlight Voting Buttons for Voters", "wpdiscuz"),
                            "label_original" => "Highlight Voting Buttons for Voters",
                            "description" => esc_html__("This allows users to see own voted comments.", "wpdiscuz"),
                            "description_original" => "This allows users to see own voted comments.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#voting-liking-buttons"
                        ],
                        "showAvatars" => [
                            "label" => esc_html__("Display Avatars", "wpdiscuz"),
                            "label_original" => "Display Avatars",
                            "description" => esc_html__("This option only related to avatars in comment system. For sitewide avatar control, please use WordPress native avatar settings in Dashboard > Settings > Discussions admin page.", "wpdiscuz"),
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#display-avatars"
                        ],
                        "defaultAvatarUrlForUser" => [
                            "label" => esc_html__("Default Avatar Source URL for Users", "wpdiscuz"),
                            "label_original" => "Default Avatar Source URL for Users",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#display-avatars"
                        ],
                        "defaultAvatarUrlForGuest" => [
                            "label" => esc_html__("Default Avatar Source URL for Guests", "wpdiscuz"),
                            "label_original" => "Default Avatar Source URL for Guests",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#display-avatars"
                        ],
                        "changeAvatarsEverywhere" => [
                            "label" => esc_html__("Enable Sitewide Usage of Default Avatars", "wpdiscuz"),
                            "label_original" => "Enable Sitewide Usage of Default Avatars",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-thread-features/#display-avatars"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_THREAD_STYLES => [
                    "title" => esc_html__("Styles and Colors", "wpdiscuz"),
                    "title_original" => "Styles and Colors",
                    "icon" => "box-styles.png",
                    "icon-height" => "56px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_THREAD_STYLES . ".php",
                    "options" => [
                        "theme" => [
                            "label" => esc_html__("Comment Form and Comment List Style", "wpdiscuz"),
                            "label_original" => "Comment Form and Comment List Style",
                            "description" => esc_html__("Starting from wpDiscuz 7 you can choose the [ Off ] option of comment style. It'll remove most of wpDiscuz CSS code and allow you write your own CSS for custom comment styling.", "wpdiscuz"),
                            "description_original" => "Starting from wpDiscuz 7 you can choose the [ Off ] option of comment style. It'll remove most of wpDiscuz CSS code and allow you write your own CSS for custom comment styling.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/styles-and-colors/#comment-form-and-comment-list-style"
                        ],
                        "styleSpecificColors" => [
                            "label" => esc_html__("Style Specific Colors", "wpdiscuz"),
                            "label_original" => "Style Specific Colors",
                            "description" => esc_html__("These options allows you manage comment section colors individaly for the Default and Dark Styles", "wpdiscuz"),
                            "description_original" => "These options allows you manage comment section colors individaly for the Default and Dark Styles",
                            "docurl" => ""
                        ],
                        "colors" => [
                            "label" => esc_html__("General Colors", "wpdiscuz"),
                            "label_original" => "General Colors",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/styles-and-colors/#colors"
                        ],
                        "commentTextSize" => [
                            "label" => esc_html__("Comment Text Size", "wpdiscuz"),
                            "label_original" => "Comment Text Size",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "enableFontAwesome" => [
                            "label" => esc_html__("Load Font Awesome css lib", "wpdiscuz"),
                            "label_original" => "Load Font Awesome css lib",
                            "description" => esc_html__("IMPORTANT: In case your theme uses old versions of Font-Awesome lib, you should not disable this this option. The theme old version doesn't support new version icons, thus some wpDiscuz icons might be lost.", "wpdiscuz"),
                            "description_original" => "IMPORTANT: In case your theme uses old versions of Font-Awesome lib, you should not disable this this option. The theme old version doesn't support new version icons, thus some wpDiscuz icons might be lost.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/styles-and-colors/#load-font-awesome-css-lib"
                        ],
                        "customCss" => [
                            "label" => esc_html__("Custom CSS Code", "wpdiscuz"),
                            "label_original" => "Custom CSS Code",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/styles-and-colors/#custom-css-code"
                        ]
                    ]
                ],
                WpdiscuzCore::TAB_SUBSCRIPTION => [
                    "title" => esc_html__("Subscription and User Following", "wpdiscuz"),
                    "title_original" => "Subscription and User Following",
                    "icon" => "box-email.png",
                    "icon-height" => "58px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_SUBSCRIPTION . ".php",
                    "options" => [
                        "enableUserMentioning" => [
                            "label" => esc_html__("Enable User Mentioning", "wpdiscuz"),
                            "label_original" => "Enable User Mentioning",
                            "description" => sprintf(__("This option allows mentioning users in comments using @nickname method. Mentioned users will get notification via email if the next option is enabled. To get an advanced user mentioning features and to be able mention comments by #CommentID, we recommend the %s addon.", "wpdiscuz"), '<a href="https://gvectors.com/product/wpdiscuz-user-comment-mentioning/" target="_blank">' . "wpDiscuz User & Comment Mentioning" . '</a>'),
                            "description_original" => sprintf("This option allows mentioning users in comments using @nickname method. Mentioned users will get notification via email if the next option is enabled. To get an advanced user mentioning features and to be able mention comments by #CommentID, we recommend the %s addon.", '<a href="https://gvectors.com/product/wpdiscuz-user-comment-mentioning/" target="_blank">' . "wpDiscuz User & Comment Mentioning" . '</a>'),
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#enable-user-mentioning"
                        ],
                        "sendMailToMentionedUsers" => [
                            "label" => esc_html__("Send E-Mail Notification to Mentioned Users", "wpdiscuz"),
                            "label_original" => "Send E-Mail Notification to Mentioned Users",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#send-e-mail-notification-to-mentioned-users"
                        ],
                        "isNotifyOnCommentApprove" => [
                            "label" => esc_html__("Notify comment author once comment is approved", "wpdiscuz"),
                            "label_original" => "Notify comment author once comment is approved",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#notify-comment-author-once-comment-is-approved"
                        ],
                        "enableMemberConfirm" => [
                            "label" => esc_html__("Enable subscription confirmation for registered users", "wpdiscuz"),
                            "label_original" => "Enable subscription confirmation for registered users",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#enable-subscription-confirmation-for-registered-users"
                        ],
                        "enableGuestsConfirm" => [
                            "label" => esc_html__("Enable subscription confirmation for guests", "wpdiscuz"),
                            "label_original" => "Enable subscription confirmation for guests",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#enable-subscription-confirmation-for-guests"
                        ],
                        "subscriptionType" => [
                            "label" => esc_html__("Subscription types in Subscription Bar drop-down", "wpdiscuz"),
                            "label_original" => "Subscription types in Subscription Bar drop-down",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#subscription-types-in-subscription-bar-drop-down"
                        ],
                        "showReplyCheckbox" => [
                            "label" => esc_html__('Display "Notify of new replies to this comment" option in comment form', "wpdiscuz"),
                            "label_original" => 'Display "Notify of new replies to this comment" option in comment form',
                            "description" => esc_html__("wpDiscuz is the only comment plugin which allows you to subscribe to certain comment replies. This option is located above [Post Comment] button in comment form. You can disable this subscription way by unchecking this option.", "wpdiscuz"),
                            "description_original" => "wpDiscuz is the only comment plugin which allows you to subscribe to certain comment replies. This option is located above [Post Comment] button in comment form. You can disable this subscription way by unchecking this option.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#display-%E2%80%9Cnotify-of-new-replies-to-this-comment%E2%80%9D-option-in-commen"
                        ],
                        "isReplyDefaultChecked" => [
                            "label" => esc_html__('Keep checked the "Notify of new replies to this comment" option by default', "wpdiscuz"),
                            "label_original" => 'Keep checked the "Notify of new replies to this comment" option by default',
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "usePostmaticForCommentNotification" => [
                            "label" => esc_html__("Use Postmatic for subscriptions and commenting by email", "wpdiscuz"),
                            "label_original" => "Use Postmatic for subscriptions and commenting by email",
                            "description" => esc_html__("Postmatic allows your users subscribe to comments. Instead of just being notified, they add a reply right from their inbox.", "wpdiscuz"),
                            "description_original" => "Postmatic allows your users subscribe to comments. Instead of just being notified, they add a reply right from their inbox.",
                            "docurl" => ""
                        ],
                        "isFollowActive" => [
                            "label" => esc_html__("Enable User Following Feature", "wpdiscuz"),
                            "label_original" => "Enable User Following Feature",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#user-subscription-follow-users"
                        ],
                        "disableFollowConfirmForUsers" => [
                            "label" => esc_html__("Follow users without email confirmation", "wpdiscuz"),
                            "label_original" => "Follow users without email confirmation",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/subscription-and-user-following/#follow-users-without-email-confirmation"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_LABELS => [
                    "title" => esc_html__("User Labels and Badges", "wpdiscuz"),
                    "title_original" => "User Labels and Badges",
                    "icon" => "box-badges.png",
                    "icon-height" => "56px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_LABELS . ".php",
                    "options" => [
                        "blogRoleLabels" => [
                            "label" => esc_html__("Display Comment Author Labels", "wpdiscuz"),
                            "label_original" => "Display Comment Author Labels",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-labels-and-badges/#display-comment-author-labels"
                        ],
                        "commenterLabelColors" => [
                            "label" => esc_html__("Comment Author Label Colors by User Role", "wpdiscuz"),
                            "label_original" => "Comment Author Label Colors by User Role",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-labels-and-badges/#comment-author-label-colors-by-user-role"
                        ]
                    ]
                ],
                WpdiscuzCore::TAB_MODERATION => [
                    "title" => esc_html__("Comment Moderation", "wpdiscuz"),
                    "title_original" => "Comment Moderation",
                    "icon" => "box-moderation.png",
                    "icon-height" => "50px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_MODERATION . ".php",
                    "options" => [
                        "commentEditableTime" => [
                            "label" => esc_html__("Edit Button - Allow comment editing for", "wpdiscuz"),
                            "label_original" => "Edit Button - Allow comment editing for",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#edit-button-%E2%80%93-allow-comment-editing-for"
                        ],
                        "enableEditingWhenHaveReplies" => [
                            "label" => esc_html__("Enable editing for replied comments", "wpdiscuz"),
                            "label_original" => "Enable editing for replied comments",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#enable-editing-for-replied-comments"
                        ],
                        "displayEditingInfo" => [
                            "label" => esc_html__("Display comment editing Information", "wpdiscuz"),
                            "label_original" => "Display comment editing Information",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#display-comment-editing-information"
                        ],
                        "enableStickButton" => [
                            "label" => esc_html__("Stick Button - Stick a comment thread", "wpdiscuz"),
                            "label_original" => "Stick Button - Stick a comment thread",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#stick-button-%E2%80%93-stick-a-comment-thread"
                        ],
                        "enableCloseButton" => [
                            "label" => esc_html__("Close Button - Close a comment thread", "wpdiscuz"),
                            "label_original" => "Close Button - Close a comment thread",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#close-button-%E2%80%93-close-a-comment-thread"
                        ],
                        "userCommentsLimit" => [
                            "label" => esc_html__("Limit Comments per User", "wpdiscuz"),
                            "label_original" => "Limit Comments per User",
                            "description" => esc_html__("This option allows control commenting activity per user. You can set maximum number of comments users can leave per post or sitewide. It also allow to set restriction for comments or for replies only.", "wpdiscuz"),
                            "description_original" => "This option allows control commenting activity per user. You can set maximum number of comments users can leave per post or sitewide. It also allow to set restriction for comments or for replies only.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-moderation/#limit-comments-per-user"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_CONTENT => [
                    "title" => esc_html__("Comment Content and Media", "wpdiscuz"),
                    "title_original" => "Comment Content and Media",
                    "icon" => "box-content.png",
                    "icon-height" => "50px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_CONTENT . ".php",
                    "options" => [
                        "commentTextLength" => [
                            "label" => esc_html__("Comment Text Length", "wpdiscuz"),
                            "label_original" => "Comment Text Length",
                            "description" => esc_html__("Allows to set minimum and maximum number of chars can be inserted in comment textarea. Leave the max value empty to remove the limit.", "wpdiscuz"),
                            "description_original" => "Allows to set minimum and maximum number of chars can be inserted in comment textarea. Leave the max value empty to remove the limit.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#comment-text-length"
                        ],
                        "enableImageConversion" => [
                            "label" => esc_html__("Image Source URL to Image Conversion", "wpdiscuz"),
                            "label_original" => "Image Source URL to Image Conversion",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#image-source-url-to-image-conversion"
                        ],
                        "enableShortcodes" => [
                            "label" => esc_html__("Enable WordPress Shortcodes in Comment Content", "wpdiscuz"),
                            "label_original" => "Enable WordPress Shortcodes in Comment Content",
                            "description" => esc_html__("This option allows embedding other plugins shortcodes in comment content. Some plugin shortcodes work very slow, so this may affect your page load speed if the shortcode provider plugin is not well optimized.", "wpdiscuz"),
                            "description_original" => "This option allows embedding other plugins shortcodes in comment content. Some plugin shortcodes work very slow, so this may affect your page load speed if the shortcode provider plugin is not well optimized.",
                            "docurl" => ""
                        ],
                        "commentReadMoreLimit" => [
                            "label" => esc_html__("The number of words before breaking comment text (Read more)", "wpdiscuz"),
                            "label_original" => "The number of words before breaking comment text (Read more)",
                            "description" => esc_html__("Set this option value 0, to turn off comment text breaking function.", "wpdiscuz"),
                            "description_original" => "Set this option value 0, to turn off comment text breaking function.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#the-number-of-words-before-breaking-comment-text-read-more"
                        ],
                        "wmuIsEnabled" => [
                            "label" => esc_html__("Enable Media Uploading", "wpdiscuz"),
                            "label_original" => "Enable Media Uploading",
                            "description" => esc_html__("This option allows commenters to attach an image with comments.", "wpdiscuz"),
                            "description_original" => "This option allows commenters to attach an image with comments.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#enable-media-uploading"
                        ],
                        "wmuIsGuestAllowed" => [
                            "label" => esc_html__("Allow Media Uploading for Guests", "wpdiscuz"),
                            "label_original" => "Allow Media Uploading for Guests",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "wmuIsLightbox" => [
                            "label" => esc_html__("Enable Lightbox for Attached Images", "wpdiscuz"),
                            "label_original" => "Enable Lightbox for Attached Images",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#enable-lightbox-for-attached-images"
                        ],
                        "wmuMimeTypes" => [
                            "label" => esc_html__("Allowed File Types", "wpdiscuz"),
                            "label_original" => "Allowed File Types",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#allowed-file-types"
                        ],
                        "wmuMaxFileSize" => [
                            "label" => esc_html__("Max Uploaded Size", "wpdiscuz"),
                            "label_original" => "Max Uploaded Size",
                            "description" => esc_html__("You can not set this value more than 'upload_max_filesize' and 'post_max_size'. If you want to increase server parameters please contact to your hosting service support.", "wpdiscuz"),
                            "description_original" => "You can not set this value more than 'upload_max_filesize' and 'post_max_size'. If you want to increase server parameters please contact to your hosting service support.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#max-uploaded-size"
                        ],
                        "wmuIsShowFilesDashboard" => [
                            "label" => esc_html__("Show Comments Media in Dashboard", "wpdiscuz"),
                            "label_original" => "Show Comments Media in Dashboard",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#show-comments-media-in-dashboard"
                        ],
                        "wmuSingleImageSize" => [
                            "label" => esc_html__("Single Image Sizes in Comments", "wpdiscuz"),
                            "label_original" => "Single Image Sizes in Comments",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#single-image-sizes-in-comments"
                        ],
                        "wmuImageSizes" => [
                            "label" => esc_html__("Generate Thumbnail Sizes", "wpdiscuz"),
                            "label_original" => "Generate Thumbnail Sizes",
                            "description" => esc_html__("Once image is uploaded, it'll generate thumbnails according to your selected sizes. When you set up a new WordPress website, the platform gives you three image sizes to play with: thumbnail, medium, and large (plus the file's original resolution). You may have other options and sizes which are registered by current active theme and by other plugins.", "wpdiscuz"),
                            "description_original" => "Once image is uploaded, it'll generate thumbnails according to your selected sizes. When you set up a new WordPress website, the platform gives you three image sizes to play with: thumbnail, medium, and large (plus the file's original resolution). You may have other options and sizes which are registered by current active theme and by other plugins.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/comment-content-and-media/#generate-thumbnail-sizes"
                        ]
                    ]
                ],
                WpdiscuzCore::TAB_LIVE => [
                    "title" => esc_html__("Live Commenting and Notifications", "wpdiscuz"),
                    "title_original" => "Live Commenting and Notifications",
                    "icon" => "box-bubble.png",
                    "icon-height" => "70px",
                    "status" => "new",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_LIVE . ".php",
                    "options" => [
                        "bubble" => [
                            "label" => esc_html__("Comment Bubble", "wpdiscuz"),
                            "label_original" => "Comment Bubble",
                            "description" => esc_html__("Comment Bubble is a real-time updating sticky comment icon on your web pages. It invites people to comment, displays current comments information and notifies current page viewers about new comments.", "wpdiscuz"),
                            "description_original" => "Comment Bubble is a real-time updating sticky comment icon on your web pages. It invites people to comment, displays current comments information and notifies current page viewers about new comments.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#comment-bubble"
                        ],
                        "bubbleLiveUpdate" => [
                            "label" => esc_html__("Comment Bubble Live Update", "wpdiscuz"),
                            "label_original" => "Comment Bubble Live Update",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#comment-bubble-live-update"
                        ],
                        "bubbleLocation" => [
                            "label" => esc_html__("Comment Bubble Location", "wpdiscuz"),
                            "label_original" => "Comment Bubble Location",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#comment-bubble-location"
                        ],
                        "bubbleShowNewCommentMessage" => [
                            "label" => esc_html__("Bubble - Notify on New Comments", "wpdiscuz"),
                            "label_original" => "Bubble - Notify on New Comments",
                            "description" => esc_html__("If the Bubble live update is enabled, it shows new comments excerpts as a pop-up information to article reads in real-time. This keeps website visitors up to date and engages them join to the discussion.", "wpdiscuz"),
                            "description_original" => "If the Bubble live update is enabled, it shows new comments excerpts as a pop-up information to article reads in real-time. This keeps website visitors up to date and engages them join to the discussion.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#bubble-%E2%80%93-notify-on-new-comments"
                        ],
                        "bubbleHintTimeout" => [
                            "label" => esc_html__("Bubble - Invite to comment in X seconds", "wpdiscuz"),
                            "label_original" => "Bubble - Invite to comment in X seconds",
                            "description" => esc_html__("In most cases article readers don't even think about leaving some comment. Using this option you can enable Bubble &laquo;Invite to Comment&raquo; message. Once page is loaded and visitor has read some content, it reminds about comments and calls to leave a reply.", "wpdiscuz"),
                            "description_original" => "In most cases article readers don't even think about leaving some comment. Using this option you can enable Bubble &laquo;Invite to Comment&raquo; message. Once page is loaded and visitor has read some content, it reminds about comments and calls to leave a reply.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#bubble-%E2%80%93-invite-to-comment-in-x-seconds"
                        ],
                        "bubbleHintHideTimeout" => [
                            "label" => esc_html__("Bubble - Hide the invitation message in X seconds", "wpdiscuz"),
                            "label_original" => "Bubble - Hide the invitation message in X seconds",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#bubble-%E2%80%93-hide-the-invitation-message-in-x-seconds"
                        ],
                        "commentListUpdateType" => [
                            "label" => esc_html__("Live Update", "wpdiscuz"),
                            "label_original" => "Live Update",
                            "description" => esc_html__("wpDiscuz live update is very light and doesn't overload your server. However we recommend to monitor your server resources if you're on a Shared hosting plan. There are some very weak hosting plans which may not be able to perform very frequently live update requests. If you found some issue you can set the option below 30 seconds or more.", "wpdiscuz"),
                            "description_original" => "wpDiscuz live update is very light and doesn't overload your server. However we recommend to monitor your server resources if you're on a Shared hosting plan. There are some very weak hosting plans which may not be able to perform very frequently live update requests. If you found some issue you can set the option below 30 seconds or more.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#live-update"
                        ],
                        "liveUpdateGuests" => [
                            "label" => esc_html__("Enable Live Update for Guests", "wpdiscuz"),
                            "label_original" => "Enable Live Update for Guests",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#enable-live-update-for-guests"
                        ],
                        "commentListUpdateTimer" => [
                            "label" => esc_html__("Update Comment List Every", "wpdiscuz"),
                            "label_original" => "Update Comment List Every",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/live-commenting-and-notifications/#update-comment-list-every-x-seconds"
                        ]
                    ]
                ],
                WpdiscuzCore::TAB_INLINE => [
                    "title" => esc_html__("Inline Commenting", "wpdiscuz"),
                    "title_original" => "Inline Commenting",
                    "icon" => "box-feedback.png",
                    "icon-height" => "56px",
                    "status" => "new",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_INLINE . ".php",
                    "options" => [
                        "showInlineFilterButton" => [
                            "label" => esc_html__("Display filter button to load inline feedbacks", "wpdiscuz"),
                            "label_original" => "Display filter button to load inline feedbacks",
                            "description" => esc_html__("This filter button appears next to all filter buttons and comment sorting options. It allows to filter and display article inline feedbacks (comments made while reading current article).", "wpdiscuz"),
                            "description_original" => "This filter button appears next to all filter buttons and comment sorting options. It allows to filter and display article inline feedbacks (comments made while reading current article).",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/inline-commenting/#display-filter-button-to-load-inline-feedbacks"
                        ],
                        "inlineFeedbackAttractionType" => [
                            "label" => esc_html__('Animation for "Leave a Feedback" button in article content', "wpdiscuz"),
                            "label_original" => 'Animation for "Leave a Feedback" button in article content',
                            "description" => esc_html__('Once a question is added in article editor (backend), readers will see a small comment icon (call to leave a feedback) next to the text part you\'ve selected for your question on article (front-end). This icon calls people to leave a feedback, using the type you\'ve selected in this option. For example, if you\'ve chosen the "Blink" option, once reader scrolled  and reached to the article text with question, it animates with comment button size and color changes attracting readers attention.', "wpdiscuz"),
                            "description_original" => 'Once a question is added in article editor (backend), readers will see a small comment icon (call to leave a feedback) next to the text part you\'ve selected for your question on article (front-end). This icon calls people to leave a feedback, using the type you\'ve selected in this option. For example, if you\'ve chosen the "Blink" option, once reader scrolled  and reached to the article text with question, it animates with comment button size and color changes attracting readers attention.',
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/inline-commenting/#animation-for-%E2%80%9Cleave-a-feedback%E2%80%9D-button-in-article-content"
                        ],
                    ]
                ],
                WpdiscuzCore::TAB_GENERAL => [
                    "title" => esc_html__("General Settings", "wpdiscuz"),
                    "title_original" => "General Settings",
                    "icon" => "box-general.png",
                    "icon-height" => "56px",
                    "status" => "ok",
                    "file_path" => WPDISCUZ_DIR_PATH . "/options/options-layouts/html-" . WpdiscuzCore::TAB_GENERAL . ".php",
                    "options" => [
                        "isEnableOnHome" => [
                            "label" => esc_html__("Enable wpDiscuz on Home Page", "wpdiscuz"),
                            "label_original" => "Enable wpDiscuz on Home Page",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#enable-wpdiscuz-on-home-page"
                        ],
                        "isNativeAjaxEnabled" => [
                            "label" => esc_html__("Use WordPress native AJAX functions", "wpdiscuz"),
                            "label_original" => "Use WordPress native AJAX functions",
                            "description" => esc_html__("By disabling this option you're automatically enabling wpDiscuz custom AJAX functions, which are many times faster that the default WordPress functions. Just make sure it doesn't conflict with your plugins.", "wpdiscuz"),
                            "description_original" => "By disabling this option you're automatically enabling wpDiscuz custom AJAX functions, which are many times faster that the default WordPress functions. Just make sure it doesn't conflict with your plugins.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#use-wordpress-native-ajax-functions"
                        ],
                        "loadComboVersion" => [
                            "label" => esc_html__("Combine JS and CSS Files to Optimize Page Loading Speed", "wpdiscuz"),
                            "label_original" => "Combine JS and CSS Files to Optimize Page Loading Speed",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "loadMinVersion" => [
                            "label" => esc_html__("Minify JS and CSS Files to Optimize Page Loading Speed", "wpdiscuz"),
                            "label_original" => "Minify JS and CSS Files to Optimize Page Loading Speed",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#load-min-versions"
                        ],
                        "commentLinkFilter" => [
                            "label" => esc_html__("Secure comment content in HTTPS protocol.", "wpdiscuz"),
                            "label_original" => "Secure comment content in HTTPS protocol.",
                            "description" => esc_html__("This option detects images and other contents with non-https source URLs and fix according to your selected logic.", "wpdiscuz"),
                            "description_original" => "This option detects images and other contents with non-https source URLs and fix according to your selected logic.",
                            "docurl" => ""
                        ],
                        "redirectPage" => [
                            "label" => esc_html__("Redirect First Comment to", "wpdiscuz"),
                            "label_original" => "Redirect First Comment to",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#redirect-first-comment-to"
                        ],
                        "simpleCommentDate" => [
                            "label" => esc_html__("Use WordPress Date/Time Format", "wpdiscuz"),
                            "label_original" => "Use WordPress Date/Time Format",
                            "description" => esc_html__("wpDiscuz shows Human Readable date format. If you check this option it'll show the date/time format set in WordPress General Settings.", "wpdiscuz"),
                            "description_original" => "wpDiscuz shows Human Readable date format. If you check this option it'll show the date/time format set in WordPress General Settings.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#use-wordpress-date-time-format"
                        ],
                        "dateDiffFormat" => [
                            "label" => esc_html__("Structure of Human Readable Date Format", "wpdiscuz"),
                            "label_original" => "Structure of Human Readable Date Format",
                            "description" => esc_html__("By default, comment date is displayed with the human readable format, such as [X days ago]. For some languages, you may need to change the sequence of words in this date. This option provides shordcodes for each word allowing you manage the order. [number] is the 'X', [time_unit] is the 'days', [adjective] is the 'ago'.", "wpdiscuz"),
                            "description_original" => "By default, comment date is displayed with the human readable format, such as [X days ago]. For some languages, you may need to change the sequence of words in this date. This option provides shordcodes for each word allowing you manage the order. [number] is the 'X', [time_unit] is the 'days', [adjective] is the 'ago'.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#structure-of-human-readable-date-format"
                        ],
                        "isUsePoMo" => [
                            "label" => esc_html__("Use Plugin .PO/.MO Files", "wpdiscuz"),
                            "label_original" => "Use Plugin .PO/.MO Files",
                            "description" => esc_html__("wpDiscuz phrase system allows you to translate all front-end phrases. However if you have a multi-language website it'll not allow you to add more than one language translation. The only way to get it is the plugin translation files (.PO / .MO). If wpDiscuz has the languages you need you should check this option to disable phrase system and it'll automatically translate all phrases based on language files according to current language.", "wpdiscuz"),
                            "description_original" => "wpDiscuz phrase system allows you to translate all front-end phrases. However if you have a multi-language website it'll not allow you to add more than one language translation. The only way to get it is the plugin translation files (.PO / .MO). If wpDiscuz has the languages you need you should check this option to disable phrase system and it'll automatically translate all phrases based on language files according to current language.",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#use-plugin-po-mo-files"
                        ],
                        "showPluginPoweredByLink" => [
                            "label" => esc_html__("Help wpDiscuz to grow allowing people to recognize which comment plugin you use", "wpdiscuz"),
                            "label_original" => "Help wpDiscuz to grow allowing people to recognize which comment plugin you use",
                            "description" => esc_html__("Please check this option on to help wpDiscuz get more popularity as your thank to the hard work we do for you totally free. This option adds a very small (16x16px) icon under the comment section which will allow your site visitors recognize the name of comment solution you use.", "wpdiscuz"),
                            "description_original" => "Please check this option on to help wpDiscuz get more popularity as your thank to the hard work we do for you totally free. This option adds a very small (16x16px) icon under the comment section which will allow your site visitors recognize the name of comment solution you use.",
                            "docurl" => ""
                        ],
                        "isGravatarCacheEnabled" => [
                            "label" => esc_html__("Enable Gravatar caching", "wpdiscuz"),
                            "label_original" => "Enable Gravatar caching",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#enable-gravatar-caching"
                        ],
                        "gravatarCacheMethod" => [
                            "label" => esc_html__("Caching Method", "wpdiscuz"),
                            "label_original" => "Caching Method",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#caching-method"
                        ],
                        "gravatarCacheTimeout" => [
                            "label" => esc_html__("Reset Avatar Cache Frequency", "wpdiscuz"),
                            "label_original" => "Reset Avatar Cache Frequency",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => "https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/general-settings/#cache-avatars-for-%E2%80%9Cx%E2%80%9D-days"
                        ],
                        "removeVoteData" => [
                            "label" => esc_html__("Remove Vote Data", "wpdiscuz"),
                            "label_original" => "Remove Vote Data",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "purgeAvatarCache" => [
                            "label" => esc_html__("Purge Expired Avatar Caches", "wpdiscuz"),
                            "label_original" => "Purge Expired Avatar Caches",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ],
                        "purgeAllCaches" => [
                            "label" => esc_html__("Purge All Avatar Caches", "wpdiscuz"),
                            "label_original" => "Purge All Avatar Caches",
                            "description" => "",
                            "description_original" => "",
                            "docurl" => ""
                        ]
                    ]
                ],
            ],
            "addons" => [],
        ];
        return apply_filters("wpdiscuz_settings", $settings);
    }

}
