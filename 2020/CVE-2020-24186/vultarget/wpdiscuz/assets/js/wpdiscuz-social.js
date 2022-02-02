//============================== FACEBOOK API INIT  ========================== //
if ((wpdiscuzAjaxObj.enableFbLogin || wpdiscuzAjaxObj.enableFbShare) && wpdiscuzAjaxObj.facebookAppID) {
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


    window.fbAsyncInit = function () {
        FB.init({
            appId: wpdiscuzAjaxObj.facebookAppID,
            cookie: true,
            xfbml: true,
            version: 'v7.0'
        });
    };
}
function wpcShareCommentFB(url, quote) {
    FB.ui({
        method: 'share',
        href: url,
        quote: quote,
    }, function (response) {});
}

//============================== GOOGLE API INIT  ========================== //

jQuery(document).ready(function ($) {
    wpdDisplayErrorMessage();
    if (Cookies.get('wpdiscuz_scroll_to_comments')) {
        Cookies.remove('wpdiscuz_scroll_to_comments', {path: '/'});
        $('html, body').animate({
            scrollTop: $('#comments').offset().top - 32
        }, 1000);
    }
    $(document).delegate('.wpd-comment-share .fa-facebook-f', 'click', function () {
        if (wpdiscuzAjaxObj.enableFbShare != 1) {
            return;
        }
        var commentID = $(this).parents('.wpd-comment').find('.wpd-comment-right').attr('id');
        var postUrl = window.location.href;
        if (postUrl.indexOf('#') !== -1) {
            postUrl = postUrl.substring(0, postUrl.indexOf('#'));
        }
        postUrl += '#' + commentID;
        var commentContent = $(this).parents('.wpd-comment-right').find('.wpd-comment-text').text();
        wpcShareCommentFB(postUrl, commentContent);
    });

    var socialLoginProvider = '';
    $(document).delegate('#wpdcom .wpd-social-login .wpdiscuz-login-button', 'click', function () {
        var socialLoginContainer = $(this).parents('.wpd-social-login');
        socialLoginProvider = wpdInitProvider($(this));
        wpdSocialLoginIsConfirmAgreement(socialLoginProvider, socialLoginContainer);
    });

    $(document).delegate('#wpdcom .wpd-agreement-buttons-right .wpd-agreement-button', 'click', function () {
        var socialLoginContainer = $(this).parents('.wpd-form-wrap, .wpd-form').find('.wpd-social-login-agreement').slideUp(700);
        if ($(this).hasClass('wpd-agreement-button-agree')) {
            if (wpdiscuzAjaxObj.isCookiesEnabled) {
                Cookies.set('socialLoginAgreementConfirmed', 1, {expires: 30, path: '/'});
            }
            wpdCallSocialLogin(socialLoginProvider, socialLoginContainer);
        }
    });

    function wpdSocialLoginIsConfirmAgreement(provider, container) {
        if (parseInt(wpdiscuzAjaxObj.socialLoginAgreementCheckbox) != 1 || Cookies.get('socialLoginAgreementConfirmed') == 1) {
            wpdCallSocialLogin(provider, container);
        } else {
            container.parents('.wpd-form-wrap, .wpd-form').find('.wpd-social-login-agreement').first().slideDown(700);
        }
        return false;
    }


    function wpdCallSocialLogin(provider, container) {
        var token, userID = '';
        wpdSocialLoginLoadingBar(container, 1);
        Cookies.set('wpdiscuz_scroll_to_comments', 1, {path: '/'});
        if (provider === 'facebook' && wpdiscuzAjaxObj.facebookUseOAuth2 == 0) {
            FB.getLoginStatus(function (response) {
                if (response.status === 'connected') {
                    token = response.authResponse.accessToken;
                    userID = response.authResponse.userID;
                    wpdSendRequest(provider, token, userID, container);
                } else {
                    FB.login(function (response) {
                        if (response.status === 'connected') {
                            token = response.authResponse.accessToken;
                            userID = response.authResponse.userID;
                            wpdSendRequest(provider, token, userID, container);
                        }
                    }, {scope: 'public_profile,email'});
                }
            });
        } else {
            wpdSendRequest(provider, token, userID, container);
        }
    }

    function wpdSendRequest(provider, token, userID, container) {
        var response = '';
        $.ajax({
            type: 'POST',
            url: wpdiscuzAjaxObj.url,
            data: {
                action: 'wpd_social_login',
                provider: provider,
                token: token,
                userID: userID,
                postID: wpdiscuzAjaxObj.wc_post_id
            }
        }).done(function (wpdiscuz_response) {
            wpdHandleResponse(wpdiscuz_response, container);
        });
        return response;
    }

    function wpdHandleResponse(respons, container) {
        try {
            var obj = $.parseJSON(respons);
            var code = obj.code;
            var message = obj.message;
            var url = obj.url;
            if (parseInt(code) === 200) {
                location.assign(url);
            } else {
                wpdiscuzAjaxObj.setCommentMessage(message, 'error');
            }
        } catch (e) {
            console.log(e);
        }
        wpdSocialLoginLoadingBar(container, 0);
    }

    function wpdDisplayErrorMessage() {
        var errorMessage = Cookies.get('wpdiscuz_social_login_message');
        if (errorMessage && errorMessage !== 'undefined') {
            Cookies.remove('wpdiscuz_social_login_message');
            wpdiscuzAjaxObj.setCommentMessage(decodeURIComponent(errorMessage.replace(/\+/g, '%20')), 'error');
        }
    }

    function wpdInitProvider($obj) {
        var provider = '';
        if ($obj.hasClass('wpdsn-fb')) {
            provider = 'facebook';
        }
        if ($obj.hasClass('wpdsn-insta')) {
            provider = 'instagram';
        }
        if ($obj.hasClass('wpdsn-gg')) {
            provider = 'google';
        }
        if ($obj.hasClass('wpdsn-ds')) {
            provider = 'disqus';
        }
        if ($obj.hasClass('wpdsn-wp')) {
            provider = 'wordpress';
        }
        if ($obj.hasClass('wpdsn-tw')) {
            provider = 'twitter';
        }
        if ($obj.hasClass('wpdsn-vk')) {
            provider = 'vk';
        }
        if ($obj.hasClass('wpdsn-ok')) {
            provider = 'ok';
        }
        if ($obj.hasClass('wpdsn-linked')) {
            provider = 'linkedin';
        }
        if ($obj.hasClass('wpdsn-yandex')) {
            provider = 'yandex';
        }
        if ($obj.hasClass('wpdsn-mailru')) {
            provider = 'mailru';
        }
        if ($obj.hasClass('wpdsn-weixin')) {
            provider = 'wechat';
        }
        if ($obj.hasClass('wpdsn-weibo')) {
            provider = 'weibo';
        }
        if ($obj.hasClass('wpdsn-qq')) {
            provider = 'qq';
        }
        if ($obj.hasClass('wpdsn-baidu')) {
            provider = 'baidu';
        }
        return provider;
    }

    function wpdSocialLoginLoadingBar(container, show) {
        if (show === 1) {
            container.find('.wpdiscuz-social-login-spinner').show();
        } else {
            container.find('.wpdiscuz-social-login-spinner').hide();
        }
    }
});
