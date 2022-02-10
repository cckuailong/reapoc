(function (window) {
    {
        var unknown = '-';
        // screen
        var screenSize = '';
        if (screen.width) {
            width = (screen.width) ? screen.width : '';
            height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }

        // browser
        var nVer = navigator.appVersion;
        var nAgt = navigator.userAgent;
        var browser = navigator.appName;
        var version = '' + parseFloat(navigator.appVersion);
        var majorVersion = parseInt(navigator.appVersion, 10);
        var nameOffset, verOffset, ix;

        // Opera
        if ((verOffset = nAgt.indexOf('Opera')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 6);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Opera Next
        if ((verOffset = nAgt.indexOf('OPR')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 4);
        }
        // Edge
        else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
            browser = 'Microsoft Edge';
            version = nAgt.substring(verOffset + 5);
        }
        // MSIE
        else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(verOffset + 5);
        }
        // Chrome
        else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
            browser = 'Chrome';
            version = nAgt.substring(verOffset + 7);
        }
        // Safari
        else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
            browser = 'Safari';
            version = nAgt.substring(verOffset + 7);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Firefox
        else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
            browser = 'Firefox';
            version = nAgt.substring(verOffset + 8);
        }
        // MSIE 11+
        else if (nAgt.indexOf('Trident/') != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(nAgt.indexOf('rv:') + 3);
        }
        // Other browsers
        else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
            browser = nAgt.substring(nameOffset, verOffset);
            version = nAgt.substring(verOffset + 1);
            if (browser.toLowerCase() == browser.toUpperCase()) {
                browser = navigator.appName;
            }
        }
        // trim the version string
        if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);

        majorVersion = parseInt('' + version, 10);
        if (isNaN(majorVersion)) {
            version = '' + parseFloat(navigator.appVersion);
            majorVersion = parseInt(navigator.appVersion, 10);
        }

        // mobile version
        var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

        // cookie
        var cookieEnabled = (navigator.cookieEnabled) ? true : false;

        if (typeof navigator.cookieEnabled == 'undefined' && !cookieEnabled) {
            document.cookie = 'testcookie';
            cookieEnabled = (document.cookie.indexOf('testcookie') != -1) ? true : false;
        }

        // system
        var os = unknown;
        var clientStrings = [
            {s:'Windows 10', r:/(Windows 10.0|Windows NT 10.0)/},
            {s:'Windows 8.1', r:/(Windows 8.1|Windows NT 6.3)/},
            {s:'Windows 8', r:/(Windows 8|Windows NT 6.2)/},
            {s:'Windows 7', r:/(Windows 7|Windows NT 6.1)/},
            {s:'Windows Vista', r:/Windows NT 6.0/},
            {s:'Windows Server 2003', r:/Windows NT 5.2/},
            {s:'Windows XP', r:/(Windows NT 5.1|Windows XP)/},
            {s:'Windows 2000', r:/(Windows NT 5.0|Windows 2000)/},
            {s:'Windows ME', r:/(Win 9x 4.90|Windows ME)/},
            {s:'Windows 98', r:/(Windows 98|Win98)/},
            {s:'Windows 95', r:/(Windows 95|Win95|Windows_95)/},
            {s:'Windows NT 4.0', r:/(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
            {s:'Windows CE', r:/Windows CE/},
            {s:'Windows 3.11', r:/Win16/},
            {s:'Android', r:/Android/},
            {s:'Open BSD', r:/OpenBSD/},
            {s:'Sun OS', r:/SunOS/},
            {s:'Linux', r:/(Linux|X11)/},
            {s:'iOS', r:/(iPhone|iPad|iPod)/},
            {s:'Mac OS X', r:/Mac OS X/},
            {s:'Mac OS', r:/(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
            {s:'QNX', r:/QNX/},
            {s:'UNIX', r:/UNIX/},
            {s:'BeOS', r:/BeOS/},
            {s:'OS/2', r:/OS\/2/},
            {s:'Search Bot', r:/(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
        ];
        for (var id in clientStrings) {
            var cs = clientStrings[id];
            if (cs.r.test(nAgt)) {
                os = cs.s;
                break;
            }
        }

        var osVersion = unknown;

        if (/Windows/.test(os)) {
            osVersion = /Windows (.*)/.exec(os)[1];
            os = 'Windows';
        }

        switch (os) {
            case 'Mac OS X':
                osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
                break;

            case 'Android':
                osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
                break;

            case 'iOS':
                osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
                break;
        }

        // flash (you'll need to include swfobject)
        /* script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" */
        var flashVersion = 'no check';
        if (typeof swfobject != 'undefined') {
            var fv = swfobject.getFlashPlayerVersion();
            if (fv.major > 0) {
                flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
            }
            else  {
                flashVersion = unknown;
            }
        }
    }
    window.clientInfo = {
        screen: screenSize,
        browser: browser,
        browserVersion: version,
        browserMajorVersion: majorVersion,
        mobile: mobile,
        os: os,
        osVersion: osVersion,
        cookies: cookieEnabled,
        flashVersion: flashVersion
    };
}(this));
var JSON_WSM;
if (typeof JSON_WSM !== 'object' && typeof window.JSON === 'object' && window.JSON.stringify && window.JSON.parse) {
    JSON_WSM = window.JSON;
}
if (typeof _wsm !== 'object') {
    _wsm = [];
}
if (typeof window.Wsm !== 'object') {
    window.Wsm = (function () {       
        'use strict';        
        var expireDateTime,
            eventHandlers = {},
            objPerformance = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance,
            winEncodeWrapper = window.encodeURIComponent,
            winDecodeWrapper = window.decodeURIComponent,
            arrTrackers = [],
            iterator,
            Wsm;            
        function safeDecodeWrapper(url){
            try {
                return winDecodeWrapper(url);
            } catch (e) {
                return unescape(url);
            }
        }
        function isDefined(property) {
            var propertyType = typeof property;
            return propertyType !== 'undefined';
        }
        function isFunction(property) {
            return typeof property === 'function';
        }
        function isObject(property) {
            return typeof property === 'object';
        }
        function isString(property) {
            return typeof property === 'string' || property instanceof String;
        }
        function isObjectEmpty(property){
            if (!property) {
                return true;
            }

            var i;
            var isEmpty = true;
            for (i in property) {
                if (Object.prototype.hasOwnProperty.call(property, i)) {
                    isEmpty = false;
                }
            }

            return isEmpty;
        }
        function logConsoleError(message) {
            if (console !== undefined && console && console.error) {
                console.error(message);
            }
        }
        function apply() {            
            var i, j, f, parameterArray, trackerCall;
            for (i = 0; i < arguments.length; i += 1) {
                trackerCall = null;            
                if (arguments[i] && arguments[i].slice) {
                    trackerCall = arguments[i].slice();
                }
                parameterArray = arguments[i];
                f = parameterArray.shift();
                var fParts, context;
                 if(arrTrackers.length>0) {
                    for (j = 0; j < arrTrackers.length; j++) {
                        if (isString(f)) {
                            context = arrTrackers[j];

                            var isPluginTrackerCall = f.indexOf('.') > 0;
                           
                            if (isPluginTrackerCall) {
                                fParts = f.split('.');
                                if (context && 'object' === typeof context[fParts[0]]) {
                                    context = context[fParts[0]];
                                    f = fParts[1];
                                } 
                            }
                            
                            if (context[f]) {
                                context[f].apply(context, parameterArray);
                            } else {
                                var message = 'The method \'' + f + '\' was not found in "_wsm" variable.';
                                logConsoleError(message);

                                if (!isPluginTrackerCall) {
                                    // do not trigger an error if it is a call to a plugin as the plugin may just not be
                                    // loaded yet etc
                                    throw new TypeError(message);
                                }
                            }

                            if (f === 'newTracker') {                                
                                break;
                            }

                            if (f === 'setTrackerUrl' || f === 'setSiteId') {
                                // these two methods should be only executed on the first tracker
                                break;
                            }
                        } else {
                            
                            f.apply(arrTrackers[j], parameterArray);
                        }
                    }
                }
            }
        }
        function addEventListener(element, eventType, eventHandler, useCapture) {            
            if (element.addEventListener) {
                element.addEventListener(eventType, eventHandler, useCapture);

                return true;
            }

            if (element.attachEvent) {
                return element.attachEvent('on' + eventType, eventHandler);
            }

            element['on' + eventType] = eventHandler;
        }
        function trackCallbackOnLoad(callback)        {
            console.log('load');
            if (document.readyState === 'complete') {
                callback();
            } else if (window.addEventListener) {
                window.addEventListener('load', callback);
            } else if (window.attachEvent) {
                window.attachEvent('onload', callback);
            }
        }
        function trackCallbackOnReady(callback)        {
            console.log('Ready');
            var loaded = false;

            if (document.attachEvent) {
                loaded = document.readyState === 'complete';
            } else {
                loaded = document.readyState !== 'loading';
            }

            if (loaded) {
                callback();
                return;
            }

            var _timer;

            if (document.addEventListener) {
                addEventListener(document, 'DOMContentLoaded', function ready() {
                    document.removeEventListener('DOMContentLoaded', ready, false);
                    if (!loaded) {
                        loaded = true;
                        callback();
                    }
                });
            } else if (document.attachEvent) {
                document.attachEvent('onreadystatechange', function ready() {
                    if (document.readyState === 'complete') {
                        document.detachEvent('onreadystatechange', ready);
                        if (!loaded) {
                            loaded = true;
                            callback();
                        }
                    }
                });

                if (document.documentElement.doScroll && window === window.top) {
                    (function ready() {
                        if (!loaded) {
                            try {
                                document.documentElement.doScroll('left');
                            } catch (error) {
                                setTimeout(ready, 0);

                                return;
                            }
                            loaded = true;
                            callback();
                        }
                    }());
                }
            }

            // fallback
            addEventListener(window, 'load', function () {
                if (!loaded) {
                    loaded = true;
                    callback();
                }
            }, false);
        }
        function fnCallPluginMethod(methodName, callback) {
            var result = '',
                i,
                pluginMethod, value;
            for (i in plugins) {
                if (Object.prototype.hasOwnProperty.call(plugins, i)) {
                    pluginMethod = plugins[i][methodName];

                    if (isFunction(pluginMethod)) {
                        value = pluginMethod(callback);
                        if (value) {
                            result += value;
                        }
                    }
                }
            }
            return result;
        }
        function beforeUnloadHandler() {
            var now;
            //fnCallPluginMethod('unload');
            if (expireDateTime) {
                do {
                    now = new Date();
                } while (now.getTimeAlias() < expireDateTime);
            }
        }        
        function getReferrer() {
            var referrer = '';

            try {
                referrer = window.top.document.referrer;
            } catch (e) {
                if (window.parent) {
                    try {
                        referrer = window.parent.document.referrer;
                    } catch (e2) {
                        referrer = '';
                    }
                }
            }

            if (referrer === '') {
                referrer = document.referrer;
            }

            return referrer;
        }
        function getProtocolScheme(url) {
            var e = new RegExp('^([a-z]+):'),
                matches = e.exec(url);

            return matches ? matches[1] : null;
        }
        function getHostName(url) {
            // scheme : // [username [: password] @] hostame [: port] [/ [path] [? query] [# fragment]]
            var e = new RegExp('^(?:(?:https?|ftp):)/*(?:[^@]+@)?([^:/#]+)'),
                matches = e.exec(url);

            return matches ? matches[1] : url;
        }
        function getParameter(url, name) {
            var regexSearch = "[\\?&#]" + name + "=([^&#]*)";
            var regex = new RegExp(regexSearch);
            var results = regex.exec(url);
            return results ? winDecodeWrapper(results[1]) : '';
        }
        function utf8_encode(argString) {
            return unescape(winEncodeWrapper(argString));
        }
        function sha1(str) {
            // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
            // + namespaced by: Michael White (http://getsprink.com)
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   jslinted by: Anthon Pang (http://wsm.org)
            console.log('Sha1= '+str);
            var
                rotate_left = function (n, s) {
                    return (n << s) | (n >>> (32 - s));
                },

                cvt_hex = function (val) {
                    var strout = '',
                        i,
                        v;

                    for (i = 7; i >= 0; i--) {
                        v = (val >>> (i * 4)) & 0x0f;
                        strout += v.toString(16);
                    }

                    return strout;
                },

                blockstart,
                i,
                j,
                W = [],
                H0 = 0x67452301,
                H1 = 0xEFCDAB89,
                H2 = 0x98BADCFE,
                H3 = 0x10325476,
                H4 = 0xC3D2E1F0,
                A,
                B,
                C,
                D,
                E,
                temp,
                str_len,
                word_array = [];

            str = utf8_encode(str);
            str_len = str.length;

            for (i = 0; i < str_len - 3; i += 4) {
                j = str.charCodeAt(i) << 24 | str.charCodeAt(i + 1) << 16 |
                    str.charCodeAt(i + 2) << 8 | str.charCodeAt(i + 3);
                word_array.push(j);
            }

            switch (str_len & 3) {
            case 0:
                i = 0x080000000;
                break;
            case 1:
                i = str.charCodeAt(str_len - 1) << 24 | 0x0800000;
                break;
            case 2:
                i = str.charCodeAt(str_len - 2) << 24 | str.charCodeAt(str_len - 1) << 16 | 0x08000;
                break;
            case 3:
                i = str.charCodeAt(str_len - 3) << 24 | str.charCodeAt(str_len - 2) << 16 | str.charCodeAt(str_len - 1) << 8 | 0x80;
                break;
            }

            word_array.push(i);

            while ((word_array.length & 15) !== 14) {
                word_array.push(0);
            }

            word_array.push(str_len >>> 29);
            word_array.push((str_len << 3) & 0x0ffffffff);

            for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
                for (i = 0; i < 16; i++) {
                    W[i] = word_array[blockstart + i];
                }

                for (i = 16; i <= 79; i++) {
                    W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
                }

                A = H0;
                B = H1;
                C = H2;
                D = H3;
                E = H4;

                for (i = 0; i <= 19; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 20; i <= 39; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 40; i <= 59; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 60; i <= 79; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                H0 = (H0 + A) & 0x0ffffffff;
                H1 = (H1 + B) & 0x0ffffffff;
                H2 = (H2 + C) & 0x0ffffffff;
                H3 = (H3 + D) & 0x0ffffffff;
                H4 = (H4 + E) & 0x0ffffffff;
            }

            temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);

            return temp.toLowerCase();
        }
        function urlFixup(hostName, href, referrer) {
            if (!hostName) {
                hostName = '';
            }

            if (!href) {
                href = '';
            }

            if (hostName === 'translate.googleusercontent.com') {       // Google
                if (referrer === '') {
                    referrer = href;
                }

                href = getParameter(href, 'u');
                hostName = getHostName(href);
            } else if (hostName === 'cc.bingj.com' ||                   // Bing
                    hostName === 'webcache.googleusercontent.com' ||    // Google
                    hostName.slice(0, 5) === '74.6.') {                 // Yahoo (via Inktomi 74.6.0.0/16)
                href = document.links[0].href;
                hostName = getHostName(href);
            }

            return [hostName, href, referrer];
        }
        function domainFixup(domain) {
            var dl = domain.length;

            // remove trailing '.'
            if (domain.charAt(--dl) === '.') {
                domain = domain.slice(0, dl);
            }

            // remove leading '*'
            if (domain.slice(0, 2) === '*.') {
                domain = domain.slice(1);
            }

            if (domain.indexOf('/') !== -1) {
                domain = domain.substr(0, domain.indexOf('/'));
            }

            return domain;
        }
        function titleFixup(title) {
            title = title && title.text ? title.text : title;
            if (!isString(title)) {
                var tmp = document.getElementsByTagName('title');

                if (tmp && isDefined(tmp[0])) {
                    title = tmp[0].text;
                }
            }

            return title;
        }                
        function indexOfArray(theArray, searchElement) {
            if (theArray && theArray.indexOf) {
                return theArray.indexOf(searchElement);
            }
            if (!isDefined(theArray) || theArray === null) {
                return -1;
            }
            if (!theArray.length) {
                return -1;
            }
            var len = theArray.length;
            if (len === 0) {
                return -1;
            }
            var k = 0;
            while (k < len) {
                if (theArray[k] === searchElement) {
                    return k;
                }
                k++;
            }
            return -1;
        }        
        var wsmQuery={
            isLinkElement: function (node){
                if (!node) {
                    return false;
                }

                var elementName      = String(node.nodeName).toLowerCase();
                var linkElementNames = ['a', 'area'];
                var pos = indexOfArray(linkElementNames, elementName);

                return pos !== -1;
            },
            hasNodeAttribute: function (node, attributeName){
                if (node && node.hasAttribute) {
                    return node.hasAttribute(attributeName);
                }

                if (node && node.attributes) {
                    var typeOfAttr = (typeof node.attributes[attributeName]);
                    return 'undefined' !== typeOfAttr;
                }

                return false;
            },
            getAttributeValueFromNode: function (node, attributeName){
                if (!this.hasNodeAttribute(node, attributeName)) {
                    return;
                }

                if (node && node.getAttribute) {
                    return node.getAttribute(attributeName);
                }

                if (!node || !node.attributes) {
                    return;
                }

                var typeOfAttr = (typeof node.attributes[attributeName]);
                if ('undefined' === typeOfAttr) {
                    return;
                }

                if (node.attributes[attributeName].value) {
                    return node.attributes[attributeName].value; // nodeValue is deprecated ie Chrome
                }

                if (node.attributes[attributeName].nodeValue) {
                    return node.attributes[attributeName].nodeValue;
                }

                var index;
                var attrs = node.attributes;

                if (!attrs) {
                    return;
                }

                for (index = 0; index < attrs.length; index++) {
                    if (attrs[index].nodeName === attributeName) {
                        return attrs[index].nodeValue;
                    }
                }

                return null;
            }
        }       
        function isInsideAnIframe () {
            var frameElement;

            try {
                // If the parent window has another origin, then accessing frameElement
                // throws an Error in IE. see issue #10105.
                frameElement = window.frameElement;
            } catch(e) {
                // When there was an Error, then we know we are inside an iframe.
                return true;
            }

            if (isDefined(frameElement)) {
                return (frameElement && String(frameElement.nodeName).toLowerCase() === 'iframe') ? true : false;
            }

            try {
                return window.self !== window.top;
            } catch (e2) {
                return true;
            }
        }
        function wsmTracker(trackerUrl, siteId) {
            var locationArray = urlFixup(document.domain, window.location.href, getReferrer()),
                domainAlias = domainFixup(locationArray[0]),
                locationHrefAlias = safeDecodeWrapper(locationArray[1]),
                configReferrerUrl = safeDecodeWrapper(locationArray[2]),
                defaultRequestMethod = 'GET',
                configRequestMethod = defaultRequestMethod,
                defaultRequestContentType = 'application/x-www-form-urlencoded; charset=UTF-8',
                configRequestContentType = defaultRequestContentType,
                configTrackerUrl = trackerUrl || '',                
                configAppendToTrackingUrl = '',
                configTrackerSiteId = siteId || '',
                configUserId = '',
                wpUserId = '',
                wpUrlReferrer = '',
                configPageId = '',
                visitorUUID = '',
                configCustomUrl,
                configTitle = '',
                configHostsAlias = [domainAlias],
                configTrackerPause = 500,                
                configHeartBeatDelay,
                heartBeatPingIfActivityAlias,
                configDiscardHashTag,
                configCookieNamePrefix = '_wsm_',
                configCookieDomain,
                configCookiePath,
                configCookiesDisabled = false,
                configDoNotTrack,
                configCountPreRendered,
                configConversionAttributionFirstReferrer,
                configVisitorCookieTimeout = 33955200000, // 13 months (365 days + 28days)
                configSessionCookieTimeout = 1800000, // 30 minutes
                configReferralCookieTimeout = 15768000000, // 6 months
                configPerformanceTrackingEnabled = true,
                configPerformanceGenerationTime = 0,
                browserFeatures = {},
                nextTrackingRequestTime = false,
                linkTrackingInstalled = false,
                linkTrackingEnabled = true,
                heartBeatSetUp = false,
                hadWindowFocusAtLeastOnce = isInsideAnIframe(),
                lastTrackerRequestTime = null,
                heartBeatTimeout,
                lastButton,
                lastTarget,
                hash = sha1,
                domainHash,
                location,
                position='',
                deviceInfo=window.clientInfo,
                configIdPageView;

            try {
                configTitle = document.title;                
            } catch(e) {
                configTitle = '';
            }
            if (navigator.geolocation) {
                //navigator.geolocation.watchPosition(showPosition);
            } 
            function setCookie(cookieName, value, msToExpire, path, domain, secure) {
                if (configCookiesDisabled) {
                    return;
                }

                var expiryDate;

                // relative time to expire in milliseconds
                if (msToExpire) {
                    expiryDate = new Date();
                    expiryDate.setTime(expiryDate.getTime() + msToExpire);
                }

                document.cookie = cookieName + '=' + winEncodeWrapper(value) +
                    (msToExpire ? ';expires=' + expiryDate.toGMTString() : '') +
                    ';path=' + (path || '/') +
                    (domain ? ';domain=' + domain : '') +
                    (secure ? ';secure' : '');
            }
            function getCookie(cookieName) {
                if (configCookiesDisabled) {
                    return 0;
                }

                var cookiePattern = new RegExp('(^|;)[ ]*' + cookieName + '=([^;]*)'),
                    cookieMatch = cookiePattern.exec(document.cookie);

                return cookieMatch ? winDecodeWrapper(cookieMatch[2]) : 0;
            }
            function purify(url) {
                var targetPattern;

                if (configDiscardHashTag) {
                    targetPattern = new RegExp('#.*');

                    return url.replace(targetPattern, '');
                }

                return url;
            }
            function resolveRelativeReference(baseUrl, url) {
                var protocol = getProtocolScheme(url),
                    i;

                if (protocol) {
                    return url;
                }

                if (url.slice(0, 1) === '/') {
                    return getProtocolScheme(baseUrl) + '://' + getHostName(baseUrl) + url;
                }

                baseUrl = purify(baseUrl);

                i = baseUrl.indexOf('?');
                if (i >= 0) {
                    baseUrl = baseUrl.slice(0, i);
                }

                i = baseUrl.lastIndexOf('/');
                if (i !== baseUrl.length - 1) {
                    baseUrl = baseUrl.slice(0, i + 1);
                }

                return baseUrl + url;
            }
            function isSameHost (hostName, alias) {
                var offset;

                hostName = String(hostName).toLowerCase();
                alias = String(alias).toLowerCase();

                if (hostName === alias) {
                    return true;
                }

                if (alias.slice(0, 1) === '.') {
                    if (hostName === alias.slice(1)) {
                        return true;
                    }

                    offset = hostName.length - alias.length;

                    if ((offset > 0) && (hostName.slice(offset) === alias)) {
                        return true;
                    }
                }

                return false;
            }
            function getLocation(){
                var locationAlias = this.location || window.location;

                if (!locationAlias.origin) {
                    locationAlias.origin = locationAlias.protocol + "//" + locationAlias.hostname + (locationAlias.port ? ':' + locationAlias.port: '');
                }

                return locationAlias;
            }
            function getLatitudeLongitude(position){
                this.position= position.coords.latitude + "::"+ position.coords.longitude;
            }
            function toAbsoluteUrl (url){
                if ((!url || String(url) !== url) && url !== '') {
                    // we only handle strings
                    return url;
                }

                if ('' === url) {
                    return getLocation().href;
                }

                // Eg //example.com/test.jpg
                if (url.search(/^\/\//) !== -1) {
                    return getLocation().protocol + url;
                }

                // Eg http://example.com/test.jpg
                if (url.search(/:\/\//) !== -1) {
                    return url;
                }

                // Eg #test.jpg
                if (0 === url.indexOf('#')) {
                    return getLocation().origin + this.getLocation().pathname + url;
                }

                // Eg ?x=5
                if (0 === url.indexOf('?')) {
                    return getLocation().origin + this.getLocation().pathname + url;
                }

                // Eg mailto:x@y.z tel:012345, ... market:... sms:..., javasript:... ecmascript: ... and many more
                if (0 === url.search('^[a-zA-Z]{2,11}:')) {
                    return url;
                }

                // Eg /test.jpg
                if (url.search(/^\//) !== -1) {
                    return getLocation().origin + url;
                }

                // Eg test.jpg
                var regexMatchDir = '(.*\/)';
                var base = getLocation().origin + getLocation().pathname.match(new RegExp(regexMatchDir))[0];
                return base + url;
            }
            function getPathName(url) {
                var parser = document.createElement('a');
                if (url.indexOf('//') !== 0 && url.indexOf('http') !== 0) {
                    if (url.indexOf('*') === 0) {
                        url = url.substr(1);
                    }
                    if (url.indexOf('.') === 0) {
                        url = url.substr(1);
                    }
                    url = 'http://' + url;
                }                
                parser.href = toAbsoluteUrl(url);            
                if (parser.pathname) {
                    return parser.pathname;
                }
                return '';
            }
            function isSiteHostName(hostName) {

                var i,
                    alias,
                    offset;

                for (i = 0; i < configHostsAlias.length; i++) {
                    alias = domainFixup(configHostsAlias[i].toLowerCase());

                    if (hostName === alias) {
                        return true;
                    }

                    if (alias.slice(0, 1) === '.') {
                        if (hostName === alias.slice(1)) {
                            return true;
                        }

                        offset = hostName.length - alias.length;

                        if ((offset > 0) && (hostName.slice(offset) === alias)) {
                            return true;
                        }
                    }
                }

                return false;
            }
            function getImageRequest(request, callback) {
                var image = new Image(1, 1);

                image.onload = function () {
                    iterator = 0; // To avoid JSLint warning of empty block
                    if (typeof callback === 'function') { callback(); }
                };
                // make sure to actually load an image so callback gets invoked
                request = request.replace("send_image=0","send_image=1");
               // console.log('My IMAGE Test :'+configTrackerUrl + (configTrackerUrl.indexOf('?') < 0 ? '?' : '&') + request);
                image.src = configTrackerUrl + (configTrackerUrl.indexOf('?') < 0 ? '?' : '&') + request;
            }
            function sendXmlHttpRequest(request, callback, fallbackToGet) {
                if (!isDefined(fallbackToGet) || null === fallbackToGet) {
                    fallbackToGet = true;
                }

                try {
                    // we use the progid Microsoft.XMLHTTP because
                    // IE5.5 included MSXML 2.5; the progid MSXML2.XMLHTTP
                    // is pinned to MSXML2.XMLHTTP.3.0
                    var xhr = window.XMLHttpRequest
                        ? new window.XMLHttpRequest()
                        : window.ActiveXObject
                        ? new ActiveXObject('Microsoft.XMLHTTP')
                        : null;

                    xhr.open('POST', configTrackerUrl, true);

                    // fallback on error
                    xhr.onreadystatechange = function () {
                        if (this.readyState === 4 && !(this.status >= 200 && this.status < 300) && fallbackToGet) {
                            getImageRequest(request, callback);
                        } else {
                            if (this.readyState === 4 && (typeof callback === 'function')) { callback(); }
                        }
                    };

                    xhr.setRequestHeader('Content-Type', configRequestContentType);
                    console.log('My XHR Test :'+configTrackerUrl + (configTrackerUrl.indexOf('?') < 0 ? '?' : '&') + request);
                    xhr.send(request);
                } catch (e) {
                    if (fallbackToGet) {
                        // fallback
                        getImageRequest(request, callback);
                    }
                }
            }
            function setExpireDateTime(delay) {
                var now  = new Date();
                var time = now.getTime() + delay;

                if (!expireDateTime || time > expireDateTime) {
                    expireDateTime = time;
                }
            }
            function heartBeatUp(delay) {
                if (heartBeatTimeout
                    || !configHeartBeatDelay
                ) {
                    return;
                }

                heartBeatTimeout = setTimeout(function heartBeat() {
                    heartBeatTimeout = null;

                    if (!hadWindowFocusAtLeastOnce) {
                        // if browser does not support .hasFocus (eg IE5), we assume that the window has focus.
                        hadWindowFocusAtLeastOnce = (!document.hasFocus || document.hasFocus());
                    }

                    if (!hadWindowFocusAtLeastOnce) {
                        // only send a ping if the tab actually had focus at least once. For example do not send a ping
                        // if window was opened via "right click => open in new window" and never had focus see #9504
                        heartBeatUp(configHeartBeatDelay);
                        return;
                    }

                    if (heartBeatPingIfActivityAlias()) {
                        return;
                    }

                    var now = new Date(),
                        heartBeatDelay = configHeartBeatDelay - (now.getTime() - lastTrackerRequestTime);
                    // sanity check
                    heartBeatDelay = Math.min(configHeartBeatDelay, heartBeatDelay);
                    heartBeatUp(heartBeatDelay);
                }, delay || configHeartBeatDelay);
            }
            function heartBeatDown() {
                if (!heartBeatTimeout) {
                    return;
                }

                clearTimeout(heartBeatTimeout);
                heartBeatTimeout = null;
            }
            function heartBeatOnFocus() {
                hadWindowFocusAtLeastOnce = true;

                // since it's possible for a user to come back to a tab after several hours or more, we try to send
                // a ping if the page is active. (after the ping is sent, the heart beat timeout will be set)
                if (heartBeatPingIfActivityAlias()) {
                    return;
                }

                heartBeatUp();
            }
            function heartBeatOnBlur() {
                heartBeatDown();
            }
            function setUpHeartBeat() {
                if (heartBeatSetUp
                    || !configHeartBeatDelay
                ) {
                    return;
                }

                heartBeatSetUp = true;

                addEventListener(window, 'focus', heartBeatOnFocus);
                addEventListener(window, 'blur', heartBeatOnBlur);

                heartBeatUp();
            }
            function checkGapAfterFirstRequest(callback) {
                var now     = new Date();
                var timeNow = now.getTime();

                lastTrackerRequestTime = timeNow;

                if (nextTrackingRequestTime && timeNow < nextTrackingRequestTime) {
                    var timeToWait = nextTrackingRequestTime - timeNow;

                    setTimeout(callback, timeToWait);
                    setExpireDateTime(timeToWait + 50); // set timeout is not necessarily executed at timeToWait so delay a bit more
                    nextTrackingRequestTime += 50; // delay next tracking request by further 50ms to next execute them at same time

                    return;
                }

                if (nextTrackingRequestTime === false) {
                    // it is the first request, we want to execute this one directly and delay all the next one(s) within a delay.
                    // All requests after this delay can be executed as usual again
                    var delayInMs = 800;
                    nextTrackingRequestTime = timeNow + delayInMs;
                }

                callback();
            }
            function wsmSendRequest(request, delay, callback) {
                if (!configDoNotTrack && request) {
                    checkGapAfterFirstRequest(function () {
                        if (configRequestMethod === 'POST') {
                            sendXmlHttpRequest(request, callback);
                        } else {
                            getImageRequest(request, callback);
                        }

                        setExpireDateTime(delay);
                    });
                }

                if (!heartBeatSetUp) {
                    setUpHeartBeat(); // setup window events too, but only once
                } else {
                    heartBeatUp();
                }
            }
            function getCookieName(baseName) {            
                return configCookieNamePrefix + baseName + '_' + configTrackerSiteId + '_' + domainHash;
            }
            function hasCookies() {
                if (configCookiesDisabled) {
                    return '0';
                }

                if (!isDefined(navigator.cookieEnabled)) {
                    var testCookieName = getCookieName('testcookie');
                    setCookie(testCookieName, '1');

                    return getCookie(testCookieName) === '1' ? '1' : '0';
                }

                return navigator.cookieEnabled ? '1' : '0';
            }
            function updateDomainHash() {
                domainHash = hash((configCookieDomain || domainAlias) + (configCookiePath || '/')).slice(0, 4); // 4 hexits = 16 bits             
            }
            function generateRandomUuid() {
               var str=(navigator.userAgent || '') + (navigator.platform || '') + (new Date()).getTime() + Math.random();
               var hStr=hash(str).slice(0, 16);               
               return hStr;
            }
            function loadVisitorIdCookie() {
                var now = new Date(),
                    nowTs = Math.round(now.getTime() / 1000),
                    visitorIdCookieName = getCookieName('id'),
                    id = getCookie(visitorIdCookieName),
                    cookieValue,
                    uuid;
                    
                // Visitor ID cookie found                
                if (id) {
                    cookieValue = id.split('.');

                    // returning visitor flag
                    cookieValue.unshift('0');

                    if(visitorUUID.length) {
                        cookieValue[1] = visitorUUID;
                    }
                    return cookieValue;
                }
                
                if(visitorUUID.length) {
                    uuid = visitorUUID;                    
                } else if ('0' === hasCookies()){
                    uuid = '';                    
                } else {
                    console.log('visitorUUID Test='+visitorUUID.length);
                    uuid = generateRandomUuid();
                }
                // No visitor ID cookie, let's create a new one
                cookieValue = [
                    // new visitor
                    '1',

                    // uuid
                    uuid,

                    // creation timestamp - seconds since Unix epoch
                    nowTs,

                    // visitCount - 0 = no previous visit
                    0,

                    // current visit timestamp
                    nowTs,

                    // last visit timestamp - blank = no previous visit
                    ''
                ];                
                return cookieValue;
            }
            /**
             * Loads the Visitor ID cookie and returns a named array of values
             */
            function getValuesFromVisitorIdCookie() {
                var cookieVisitorIdValue = loadVisitorIdCookie(),
                    newVisitor = cookieVisitorIdValue[0],
                    uuid = cookieVisitorIdValue[1],
                    createTs = cookieVisitorIdValue[2],
                    visitCount = cookieVisitorIdValue[3],
                    currentVisitTs = cookieVisitorIdValue[4],
                    lastVisitTs = cookieVisitorIdValue[5];               

                return {
                    newVisitor: newVisitor,
                    uuid: uuid,
                    createTs: createTs,
                    visitCount: visitCount,
                    currentVisitTs: currentVisitTs,
                    lastVisitTs: lastVisitTs
                };
            }
            function getRemainingVisitorCookieTimeout() {
                var now = new Date(),
                    nowTs = now.getTime(),
                    cookieCreatedTs = getValuesFromVisitorIdCookie().createTs;

                var createTs = parseInt(cookieCreatedTs, 10);
                var originalTimeout = (createTs * 1000) + configVisitorCookieTimeout - nowTs;
                return originalTimeout;
            }
            function setVisitorIdCookie(visitorIdCookieValues) {
                if(!configTrackerSiteId) {
                    // when called before Site ID was set
                    return;
                }

                var now = new Date(),
                    nowTs = Math.round(now.getTime() / 1000);

                if(!isDefined(visitorIdCookieValues)) {
                    visitorIdCookieValues = getValuesFromVisitorIdCookie();
                }

                var cookieValue = visitorIdCookieValues.uuid + '.' +
                    visitorIdCookieValues.createTs + '.' +
                    visitorIdCookieValues.visitCount + '.' +
                    nowTs + '.' +
                    visitorIdCookieValues.lastVisitTs 
                setCookie(getCookieName('id'), cookieValue, getRemainingVisitorCookieTimeout(), configCookiePath, configCookieDomain);
            }
            function loadReferrerAttributionCookie() {
                var cookie = getCookie(getCookieName('ref'));
                if (cookie.length) {
                    try {
                        cookie = JSON_WSM.parse(cookie);
                        if (isObject(cookie)) {
                            return cookie;
                        }
                    } catch (ignore) {
                        // Pre 1.3, this cookie was not JSON encoded
                    }
                }

                return [
                    '',
                    '',
                    0,
                    ''
                ];
            }

            function deleteCookie(cookieName, path, domain) {
                setCookie(cookieName, '', -86400, path, domain);
            }

            function isPossibleToSetCookieOnDomain(domainToTest){
                var valueToSet = 'testvalue';
                setCookie('test', valueToSet, 10000, null, domainToTest);

                if (getCookie('test') === valueToSet) {
                    deleteCookie('test', null, domainToTest);

                    return true;
                }

                return false;
            }

            function deleteCookies() {
                var savedConfigCookiesDisabled = configCookiesDisabled;

                // Temporarily allow cookies just to delete the existing ones
                configCookiesDisabled = false;

                var cookiesToDelete = ['id', 'ses',  'ref'];
                var index, cookieName;

                for (index = 0; index < cookiesToDelete.length; index++) {
                    cookieName = getCookieName(cookiesToDelete[index]);
                    if (0 !== getCookie(cookieName)) {
                        deleteCookie(cookieName, configCookiePath, configCookieDomain);
                    }
                }

                configCookiesDisabled = savedConfigCookiesDisabled;
            }

            function setSiteId(siteId) {
                configTrackerSiteId = siteId;
                setVisitorIdCookie();
            }
            function setSessionCookie() {
                setCookie(getCookieName('ses'), '*', configSessionCookieTimeout, configCookiePath, configCookieDomain);
            }

            function generateUniqueId() {
                var id = '';
                var chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var charLen = chars.length;
                var i;

                for (i = 0; i < 6; i++) {
                    id += chars.charAt(Math.floor(Math.random() * charLen));
                }

                return id;
            }
            function wsmGetRequest(request, customData, pluginMethod) {                
                var i,
                    now = new Date(),
                    nowTs = Math.round(now.getTime() / 1000),
                    referralTs,
                    referralUrl,
                    fullRefURL,
                    referralUrlMaxLength = 1024,
                    currentReferrerHostName,
                    originalReferrerHostName,                    
                    cookieSessionName = getCookieName('ses'),
                    cookieReferrerName = getCookieName('ref'),                    
                    cookieSessionValue = getCookie(cookieSessionName),
                    attributionCookie = loadReferrerAttributionCookie(),
                    currentUrl = configCustomUrl || locationHrefAlias,
                    campaignNameDetected,
                    refUrlType,
                    campaignKeywordDetected;
                    refUrlType='nonssl';

                if (configCookiesDisabled) {
                    deleteCookies();
                }

                if (configDoNotTrack) {
                    return '';
                }

                var cookieVisitorIdValues = getValuesFromVisitorIdCookie();
               

                // send charset if document charset is not utf-8. sometimes encoding
                // of urls will be the same as this and not utf-8, which will cause problems
                // do not send charset if it is utf8 since it's assumed by default in Wsm
                var charSet = document.characterSet || document.charset;

                if (!charSet || charSet.toLowerCase() === 'utf-8') {
                    charSet = null;
                }

                campaignNameDetected = attributionCookie[0];
                campaignKeywordDetected = attributionCookie[1];
                referralTs = attributionCookie[2];
                referralUrl = attributionCookie[3];

                if (!cookieSessionValue) {
                    // cookie 'ses' was not found: we consider this the start of a 'session'

                    // here we make sure that if 'ses' cookie is deleted few times within the visit
                    // and so this code path is triggered many times for one visit,
                    // we only increase visitCount once per Visit window (default 30min)
                    var visitDuration = configSessionCookieTimeout / 1000;
                    if (!cookieVisitorIdValues.lastVisitTs
                        || (nowTs - cookieVisitorIdValues.lastVisitTs) > visitDuration) {
                        cookieVisitorIdValues.visitCount++;
                        cookieVisitorIdValues.lastVisitTs = cookieVisitorIdValues.currentVisitTs;
                    }
                    // Store the referrer URL and time in the cookie;
                    // referral URL depends on the first or last referrer attribution
                    currentReferrerHostName = getHostName(configReferrerUrl);
                    originalReferrerHostName = referralUrl.length ? getHostName(referralUrl) : '';

                    if (currentReferrerHostName.length && // there is a referrer
                        !isSiteHostName(currentReferrerHostName) && // domain is not the current domain
                        (!configConversionAttributionFirstReferrer || // attribute to last known referrer
                        !originalReferrerHostName.length || // previously empty
                        isSiteHostName(originalReferrerHostName))) { // previously set but in current domain
                        referralUrl = configReferrerUrl;
                    }

                    // Set the referral cookie if we have either a Referrer URL, or detected a Campaign (or both)
                    if (referralUrl.length
                        || campaignNameDetected.length) {
                        referralTs = nowTs;
                        attributionCookie = [
                            campaignNameDetected,
                            campaignKeywordDetected,
                            referralTs,
                            purify(referralUrl.slice(0, referralUrlMaxLength))
                        ];

                        setCookie(cookieReferrerName, JSON_WSM.stringify(attributionCookie), configReferralCookieTimeout, configCookiePath, configCookieDomain);
                    }
                }
                if((String(referralUrl).length)){
                    var tempA=document.createElement('a');
                    tempA.setAttribute('href',referralUrl);
                    if(tempA.protocol=='https:'){
                        refUrlType='ssl';
                    }
                    referralUrl=purify(referralUrl.slice(0, referralUrlMaxLength))
                    var scheme=tempA.protocol+'//';
                    referralUrl=referralUrl.replace(scheme, "");
                }
                if((String(wpUrlReferrer).length)){
                    var tempA=document.createElement('a');
                    tempA.setAttribute('href',wpUrlReferrer);
                    if(tempA.protocol=='https:'){
                        refUrlType='ssl';
                    }                    
                    var scheme=tempA.protocol+'//';
                    fullRefURL=wpUrlReferrer.replace(scheme, "");
                }
                
                // build out the rest of the request
                request += '&siteId=' + configTrackerSiteId +
                '&rec=1' +
                '&rand=' + String(Math.random()).slice(2, 8) + // keep the string to a minimum
                '&h=' + now.getHours() + '&m=' + now.getMinutes() + '&s=' + now.getSeconds() +
                '&url=' + winEncodeWrapper(purify(currentUrl)) +                
                ((wpUserId && wpUserId.length) ? '&uid=' + winEncodeWrapper(wpUserId) : '') +
                ((configPageId && configPageId.length) ? '&pid=' + winEncodeWrapper(configPageId) : '') +
                '&visitorId=' + cookieVisitorIdValues.uuid + '&fvts=' + cookieVisitorIdValues.createTs + '&vc=' + cookieVisitorIdValues.visitCount +
                '&idn=' + cookieVisitorIdValues.newVisitor + // currently unused
                '&refts=' + referralTs +
                '&lvts=' + cookieVisitorIdValues.lastVisitTs +                
                (String(referralUrl).length ? '&ref=' + winEncodeWrapper(referralUrl) : '') +
                (String(referralUrl).length ? '&refType=' + refUrlType : '') +
                (String(fullRefURL).length ? '&fullRef=' + winEncodeWrapper(fullRefURL) : '') +
                (charSet ? '&cs=' + winEncodeWrapper(charSet) : '') +
                '&send_image=0';                
                // browser features
                for (i in browserFeatures) {
                    if (Object.prototype.hasOwnProperty.call(browserFeatures, i)) {
                        request += '&' + i + '=' + browserFeatures[i];
                    }
                }                
                // performance tracking
                if (configPerformanceTrackingEnabled) {                    
                    if (configPerformanceGenerationTime) {
                        request += '&gtms=' + configPerformanceGenerationTime;
                    } else if (objPerformance && objPerformance.timing
                        && objPerformance.timing.requestStart && objPerformance.timing.responseEnd) {
                        request += '&gtms=' + (objPerformance.timing.responseEnd - objPerformance.timing.requestStart);
                    }
                }
                
                if (configIdPageView) {
                    request += '&pvId=' + configIdPageView;
                }
                if(typeof deviceInfo.browser!=='undefined' && deviceInfo.browser!==''){
                    request +='&browser='+deviceInfo.browser;
                    request += (deviceInfo.browserMajorVersion)?'_'+deviceInfo.browserMajorVersion:'';
                }
                if(typeof deviceInfo.os!=='undefined' && deviceInfo.os!==''){
                    request +='&os='+deviceInfo.os;
                    request += (deviceInfo.osVersion)?'_'+deviceInfo.osVersion:'';
                }                
                if(typeof deviceInfo.mobile!=='undefined' && deviceInfo.mobile==true){                
                    request +='&device=Mobile';
                }else{
                    request += '&device=Desktop';
                } 
                //request += fnCallPluginMethod(pluginMethod);                           
                setVisitorIdCookie(cookieVisitorIdValues);
                setSessionCookie();              

                if (configAppendToTrackingUrl.length) {
                    request += '&' + configAppendToTrackingUrl;
                }                
                return request;
            }
            heartBeatPingIfActivityAlias = function heartBeatPingIfActivity() {
                var now = new Date();
                if (lastTrackerRequestTime + configHeartBeatDelay <= now.getTime()) {
                    var requestPing = wsmGetRequest('ping=1', null, 'ping');
                    wsmSendRequest(requestPing, configTrackerPause);
                    return true;
                }
                return false;
            };
            function showPosition(){
                
            }
            function logPageView(customTitle, customData, callback) {
                configIdPageView = generateUniqueId();
                var request = wsmGetRequest('action_name=' + winEncodeWrapper(titleFixup(customTitle || configTitle)), customData, 'log');
                wsmSendRequest(request, configTrackerPause, callback);
            }            

            function startsUrlWithTrackerUrl(url) {
                return (configTrackerUrl && url && 0 === String(url).indexOf(configTrackerUrl));
            }

            function getSourceElement(sourceElement){
                var parentElement;

                parentElement = sourceElement.parentNode;
                while (parentElement !== null &&
                    /* buggy IE5.5 */
                isDefined(parentElement)) {

                    if (wsmQuery.isLinkElement(sourceElement)) {
                        break;
                    }
                    sourceElement = parentElement;
                    parentElement = sourceElement.parentNode;
                }

                return sourceElement;
            }

            function getLinkIfShouldBeProcessed(sourceElement){
                sourceElement = getSourceElement(sourceElement);

                if (!wsmQuery.hasNodeAttribute(sourceElement, 'href')) {
                    return;
                }

                if (!isDefined(sourceElement.href)) {
                    return;
                }

                var href = wsmQuery.getAttributeValueFromNode(sourceElement, 'href');

                if (startsUrlWithTrackerUrl(href)) {
                    return;
                }

                var originalSourcePath = sourceElement.pathname || getPathName(sourceElement.href);

                // browsers, such as Safari, don't downcase hostname and href
                var originalSourceHostName = sourceElement.hostname || getHostName(sourceElement.href);
                var sourceHostName = originalSourceHostName.toLowerCase();
                var sourceHref = sourceElement.href.replace(originalSourceHostName, sourceHostName);

                // browsers, such as Safari, don't downcase hostname and href
                var scriptProtocol = new RegExp('^(javascript|vbscript|jscript|mocha|livescript|ecmascript|mailto|tel):', 'i');

                if (!scriptProtocol.test(sourceHref)) {                   
                    return {
                            type: 'link',
                            href: sourceHref
                        };
                }
            }
           
            function logLink(url, linkType, customData, callback, sourceElement) {

                var linkParams = linkType + '=' + winEncodeWrapper(purify(url));
                
               
                var request = wsmGetRequest(linkParams, customData, 'link');

                wsmSendRequest(request, configTrackerPause, callback);
            }

            function prefixPropertyName(prefix, propertyName) {
                if (prefix !== '') {
                    return prefix + propertyName.charAt(0).toUpperCase() + propertyName.slice(1);
                }

                return propertyName;
            }
            function trackCallback(callback) {
                var isPreRendered,
                    i,
                    // Chrome 13, IE10, FF10
                    prefixes = ['', 'webkit', 'ms', 'moz'],
                    prefix;
                
                if (!configCountPreRendered) {
                    for (i = 0; i < prefixes.length; i++) {
                        prefix = prefixes[i];
                        // does this browser support the page visibility API?
                        if (Object.prototype.hasOwnProperty.call(document, prefixPropertyName(prefix, 'hidden'))) {
                            // if pre-rendered, then defer callback until page visibility changes
                            if (document[prefixPropertyName(prefix, 'visibilityState')] === 'prerender') {
                                isPreRendered = true;
                            }
                            break;
                        }
                    }
                }

                if (isPreRendered) {
                    // note: the event name doesn't follow the same naming convention as vendor properties
                    addEventListener(document, prefix + 'visibilitychange', function ready() {
                        document.removeEventListener(prefix + 'visibilitychange', ready, false);
                        callback();
                    });

                    return;
                }

                // configCountPreRendered === true || isPreRendered === false
                callback();
            }

            /*
             * Process clicks
             */
            function processClick(sourceElement) {
                var link = getLinkIfShouldBeProcessed(sourceElement);

                if (link && link.type) {
                    link.href = safeDecodeWrapper(link.href);
                    logLink(link.href, link.type, undefined, null, sourceElement);
                }
            }

            function isIE8orOlder(){
                return document.all && !document.addEventListener;
            }

            function getKeyCodeFromEvent(event) {
                // event.which is deprecated https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/which
                var which = event.which;

                /**
                 1 : Left mouse button
                 2 : Wheel button or middle button
                 3 : Right mouse button
                 */

                var typeOfEventButton = (typeof event.button);

                if (!which && typeOfEventButton !== 'undefined' ) {
                    /**
                     -1: No button pressed
                     0 : Main button pressed, usually the left button
                     1 : Auxiliary button pressed, usually the wheel button or themiddle button (if present)
                     2 : Secondary button pressed, usually the right button
                     3 : Fourth button, typically the Browser Back button
                     4 : Fifth button, typically the Browser Forward button

                     IE8 and earlier has different values:
                     1 : Left mouse button
                     2 : Right mouse button
                     4 : Wheel button or middle button

                     For a left-hand configured mouse, the return values are reversed. We do not take care of that.
                     */

                    if (isIE8orOlder()) {
                        if (event.button & 1) {
                            which = 1;
                        } else if (event.button & 2) {
                            which = 3;
                        } else if (event.button & 4) {
                            which = 2;
                        }
                    } else {
                        if (event.button === 0 || event.button === '0') {
                            which = 1;
                        } else if (event.button & 1) {
                            which = 2;
                        } else if (event.button & 2) {
                            which = 3;
                        }
                    }
                }

                return which;
            }

            function getNameOfClickedButton(event) {
                switch (getKeyCodeFromEvent(event)) {
                    case 1:
                        return 'left';
                    case 2:
                        return 'middle';
                    case 3:
                        return 'right';
                }
            }

            function getTargetElementFromEvent(event){
                return event.target || event.srcElement;
            }

            /*
             * Handle click event
             */
            function clickHandler(enable) {

                return function (event) {

                    event = event || window.event;

                    var button = getNameOfClickedButton(event);
                    var target = getTargetElementFromEvent(event);

                    if (event.type === 'click') {

                        var ignoreClick = false;
                        if (enable && button === 'middle') {
                            ignoreClick = true;
                        }

                        if (target && !ignoreClick) {
                            processClick(target);
                        }
                    } else if (event.type === 'mousedown') {
                        if (button === 'middle' && target) {
                            lastButton = button;
                            lastTarget = target;
                        } else {
                            lastButton = lastTarget = null;
                        }
                    } else if (event.type === 'mouseup') {
                        if (button === lastButton && target === lastTarget) {
                            processClick(target);
                        }
                        lastButton = lastTarget = null;
                    } else if (event.type === 'contextmenu') {
                        processClick(target);
                    }
                };
            }

            /*
             * Add click listener to a DOM element
             */
            function addClickListener(element, enable) {                
                var enableType = typeof enable;
                if (enableType === 'undefined') {
                    enable = true;
                }

                addEventListener(element, 'click', clickHandler(enable), false);

                if (enable) {
                    addEventListener(element, 'mouseup', clickHandler(enable), false);
                    addEventListener(element, 'mousedown', clickHandler(enable), false);
                    addEventListener(element, 'contextmenu', clickHandler(enable), false);
                }
            }

            /*
             * Add click handlers to anchor and AREA elements, except those to be ignored
             */
            function addClickListeners(enable) {
                if (!linkTrackingInstalled) {
                    linkTrackingInstalled = true;
                    var i,                        
                        linkElements = document.links;
                        
                    if (linkElements) {
                        for (i = 0; i < linkElements.length; i++) {
                            addClickListener(linkElements[i], enable);
                        }
                    }
                }
            }

            function detectBrowserFeatures() {
                var i,
                    mimeType,
                    pluginMap = {
                        // document types
                        pdf: 'application/pdf',

                        // media players
                        qt: 'video/quicktime',
                        rp: 'audio/x-pn-realaudio-plugin',
                        wma: 'application/x-mplayer2',

                        // interactive multimedia
                        dir: 'application/x-director',
                        fla: 'application/x-shockwave-flash',

                        // RIA
                        java: 'application/x-java-vm',
                        gears: 'application/x-googlegears',
                        ag: 'application/x-silverlight'
                    };
                
                if (!((new RegExp('MSIE')).test(navigator.userAgent))) {
                    if (navigator.mimeTypes && navigator.mimeTypes.length) {
                        for (i in pluginMap) {
                            if (Object.prototype.hasOwnProperty.call(pluginMap, i)) {
                                mimeType = navigator.mimeTypes[pluginMap[i]];
                                browserFeatures[i] = (mimeType && mimeType.enabledPlugin) ? '1' : '0';
                            }
                        }
                    }
                    if (typeof navigator.javaEnabled !== 'unknown' &&
                            isDefined(navigator.javaEnabled) &&
                            navigator.javaEnabled()) {
                        browserFeatures.java = '1';
                    }                    
                    if (isFunction(window.GearsFactory)) {
                        browserFeatures.gears = '1';
                    }
                    browserFeatures.cookie = hasCookies();
                }

                var width = parseInt(screen.width, 10);
                var height = parseInt(screen.height, 10);
                browserFeatures.res = parseInt(width, 10) + 'x' + parseInt(height, 10);
            } 
            detectBrowserFeatures();
            updateDomainHash();
            setVisitorIdCookie();


            this.getQuery = function () {
                return wsmQuery;
            };            
            this.trackCallbackOnLoad = trackCallbackOnLoad;
            this.trackCallbackOnReady = trackCallbackOnReady;            
            this.getDomains = function () {
                return configHostsAlias;
            };
            this.getConfigCookiePath = function () {
                return configCookiePath;
            };
            this.getConfigIdPageView = function () {
                return configIdPageView;
            };
                     
            this.disableLinkTracking = function () {
                linkTrackingInstalled = false;
                linkTrackingEnabled   = false;
            };
            this.getConfigVisitorCookieTimeout = function () {
                return configVisitorCookieTimeout;
            };
            this.removeAllTrackersButFirst = function () {
                var firstTracker = arrTrackers[0];
                arrTrackers = [firstTracker];
            };
            this.getRemainingVisitorCookieTimeout = getRemainingVisitorCookieTimeout;

            this.getVisitorId = function () {
                return getValuesFromVisitorIdCookie().uuid;
            };

            this.getVisitorInfo = function () {              
                return loadVisitorIdCookie();
            };

            this.getAttributionInfo = function () {
                return loadReferrerAttributionCookie();
            };

            this.getAttributionReferrerTimestamp = function () {
                return loadReferrerAttributionCookie()[2];
            };

            this.getAttributionReferrerUrl = function () {
                return loadReferrerAttributionCookie()[3];
            };

            this.setTrackerUrl = function (trackerUrl) {
                configTrackerUrl = trackerUrl;
            };

            this.getTrackerUrl = function () {
                return configTrackerUrl;
            };

            this.newTracker = function (wsmUrl, siteId) {                
                if (!siteId) {
                    
                    throw new Error('A siteId must be given to add a new tracker');
                }

                if (!isDefined(wsmUrl) || null === wsmUrl) {
                    wsmUrl = this.getTrackerUrl();
                }

                var tracker = new wsmTracker(wsmUrl, siteId);

                arrTrackers.push(tracker);

                return tracker;
            };
            this.getSiteId = function() {
                return configTrackerSiteId;
            };
            this.setSiteId = function (siteId) {
                setSiteId(siteId);
            };
            this.setUserId = function (userId) {
                if(!isDefined(userId) || !userId.length) {
                    return;
                }
                configUserId = userId;
                visitorUUID = hash(configUserId).substr(0, 16);
            };
            this.setWpUserId = function (userId) {
                if(!isDefined(userId) || !userId.length) {
                    return;
                }
                wpUserId = userId;                
            };
            this.setUrlReferrer = function (refURL) {
                if(!isDefined(refURL) || !refURL.length) {
                    return;
                }
                wpUrlReferrer = refURL;                
            };
            this.setPageId = function (pageId) {
                if(!isDefined(pageId) || !pageId.length) {
                    return;
                }
                configPageId = pageId;                
            };
            this.getUserId = function() {
                return configUserId;
            };
            this.getPageId = function() {
                return configPageId;
            };
            this.appendToTrackingUrl = function (queryString) {
                configAppendToTrackingUrl = queryString;
            };
            this.getRequest = function (request) {
                return wsmGetRequest(request);
            };            
            this.setLinkTrackingTimer = function (delay) {
                configTrackerPause = delay;
            };
            this.setDomains = function (hostsAlias) {
                configHostsAlias = isString(hostsAlias) ? [hostsAlias] : hostsAlias;

                var hasDomainAliasAlready = false, i = 0, alias;
                for (i; i < configHostsAlias.length; i++) {
                    alias = String(configHostsAlias[i]);

                    if (isSameHost(domainAlias, domainFixup(alias))) {
                        hasDomainAliasAlready = true;
                        break;
                    }

                    var pathName = getPathName(alias);
                    if (pathName && pathName !== '/' && pathName !== '/*') {
                        hasDomainAliasAlready = true;
                        break;
                    }
                }
                if (!hasDomainAliasAlready) {                    
                    configHostsAlias.push(domainAlias);
                }
            };
            this.setRequestMethod = function (method) {
                configRequestMethod = method || defaultRequestMethod;
            };
            this.setRequestContentType = function (requestContentType) {
                configRequestContentType = requestContentType || defaultRequestContentType;
            };
            this.setReferrerUrl = function (url) {
                configReferrerUrl = url;
            };
            this.setCustomUrl = function (url) {
                configCustomUrl = resolveRelativeReference(locationHrefAlias, url);
            };
            this.setPageTitle = function (title) {
                configTitle = title;
            };
            this.discardHashTag = function (enableFilter) {
                configDiscardHashTag = enableFilter;
            };
            this.setCookieNamePrefix = function (cookieNamePrefix) {
                configCookieNamePrefix = cookieNamePrefix;
                // Re-init the Custom Variables cookie                
            };
            this.setCookieDomain = function (domain) {
                var domainFixed = domainFixup(domain);

                if (isPossibleToSetCookieOnDomain(domainFixed)) {
                    configCookieDomain = domainFixed;
                    updateDomainHash();
                }
            };
            this.setCookiePath = function (path) {
                configCookiePath = path;
                updateDomainHash();
            };
            this.setVisitorCookieTimeout = function (timeout) {
                configVisitorCookieTimeout = timeout * 1000;
            };
            this.setSessionCookieTimeout = function (timeout) {
                configSessionCookieTimeout = timeout * 1000;
            };
            this.setReferralCookieTimeout = function (timeout) {
                configReferralCookieTimeout = timeout * 1000;
            };
            this.setConversionAttributionFirstReferrer = function (enable) {
                configConversionAttributionFirstReferrer = enable;
            };
            this.disableCookies = function () {
                configCookiesDisabled = true;
                browserFeatures.cookie = '0';

                if (configTrackerSiteId) {
                    deleteCookies();
                }
            };
            this.deleteCookies = function () {
                deleteCookies();
            };
            this.setDoNotTrack = function (enable) {
                var dnt = navigator.doNotTrack || navigator.msDoNotTrack;
                configDoNotTrack = enable && (dnt === 'yes' || dnt === '1');

                // do not track also disables cookies and deletes existing cookies
                if (configDoNotTrack) {
                    this.disableCookies();
                }
            };
            this.addListener = function (element, enable) {
                addClickListener(element, enable);
            };
            this.enableLinkTracking = function (enable) {
                linkTrackingEnabled = true;

                trackCallback(function () {
                    trackCallbackOnReady(function () {                       
                        addClickListeners(enable);
                    });
                });
            };
            this.disablePerformanceTracking = function () {
                configPerformanceTrackingEnabled = false;
            };
            this.setGenerationTimeMs = function (generationTime) {
                configPerformanceGenerationTime = parseInt(generationTime, 10);
            };
            this.enableHeartBeatTimer = function (heartBeatDelayInSeconds) {
                heartBeatDelayInSeconds = Math.max(heartBeatDelayInSeconds, 1);
                configHeartBeatDelay = (heartBeatDelayInSeconds || 15) * 1000;
                // if a tracking request has already been sent, start the heart beat timeout
                if (lastTrackerRequestTime !== null) {
                    setUpHeartBeat();
                }
            };
            this.disableHeartBeatTimer = function () {
                heartBeatDown();
                if (configHeartBeatDelay || heartBeatSetUp) {
                    if (window.removeEventListener) {
                        window.removeEventListener('focus', heartBeatOnFocus, true);
                        window.removeEventListener('blur', heartBeatOnBlur, true);
                    } else if  (window.detachEvent) {
                        window.detachEvent('onfocus', heartBeatOnFocus);
                        window.detachEvent('onblur', heartBeatOnBlur);
                    }
                }
                configHeartBeatDelay = null;
                heartBeatSetUp = false;
            };
            this.killFrame = function () {
                if (window.location !== window.top.location) {
                    window.top.location = window.location;
                }
            };
            this.redirectFile = function (url) {
                if (window.location.protocol === 'file:') {
                    window.location = url;
                }
            };
            this.setCountPreRendered = function (enable) {
                configCountPreRendered = enable;
            };
            this.trackLink = function (sourceUrl, linkType, customData, callback) {
                trackCallback(function () {
                    logLink(sourceUrl, linkType, customData, callback);
                });
            };
            this.trackPageView = function (customTitle, customData, callback) {
                trackCallback(function () {
                    logPageView(customTitle, customData, callback);
                });
            }; 
            this.trackRequest = function (request, customData, callback) {
                trackCallback(function () {
                    var fullRequest = wsmGetRequest(request, customData);
                    wsmSendRequest(fullRequest, configTrackerPause, callback);
                });
            };

            //Wsm.trigger('TrackerSetup', [this]);
        }
        function wsmTrackerProxy() {
            return {
                push: apply
            };
        }
        function applyMethodsInOrder(wsm, methodsToApply){
            var appliedMethods = {};
            var index, iterator;

            for (index = 0; index < methodsToApply.length; index++) {
                var methodNameToApply = methodsToApply[index];
                appliedMethods[methodNameToApply] = 1;

                for (iterator = 0; iterator < wsm.length; iterator++) {
                    if (wsm[iterator] && wsm[iterator][0]) {
                        var methodName = wsm[iterator][0];

                        if (methodNameToApply === methodName) {
                            apply(wsm[iterator]);
                            delete wsm[iterator];

                            if (appliedMethods[methodName] > 1) {
                                logConsoleError('The method ' + methodName + ' is registered more than once in "_wsm" variable. Only the last call has an effect. Please have a look at the multiple Wsm trackers documentation: http://developer.wsm.org/guides/tracking-javascript-guide#multiple-wms-trackers');
                            }
                            appliedMethods[methodName]++;
                        }
                    }
                }
            }

            return wsm;
        }
        var applyFirst = ['newTracker', 'disableCookies', 'setTrackerUrl',  'setCookiePath', 'setCookieDomain', 'setDomains', 'setWpUserId', 'setPageId','setSiteId', 'enableLinkTracking','setUrlReferrer'];
        function wsmCreateFirstTracker(wsmUrl, siteId){          
            var tracker = new wsmTracker(wsmUrl, siteId);
            arrTrackers.push(tracker);
            
            _wsm = applyMethodsInOrder(_wsm, applyFirst);
            
            // apply the queue of actions
            for (iterator = 0; iterator < _wsm.length; iterator++) {
                if (_wsm[iterator]) {
                    apply(_wsm[iterator]);
                }
            }
            // replace initialization array with proxy object
            _wsm = new wsmTrackerProxy();
            return tracker;
        }
        addEventListener(window, 'beforeunload', beforeUnloadHandler, false);
        Date.prototype.getTimeAlias = Date.prototype.getTime;        
        Wsm = {
            initialized: false,
            JSON: JSON_WSM,
            getTracker: function (wsmUrl, siteId) {
                if (!isDefined(siteId)) {
                    siteId = this.getAsyncTracker().getSiteId();
                }
                if (!isDefined(wsmUrl)) {
                    wsmUrl = this.getAsyncTracker().getTrackerUrl();
                }

                return new wsmTracker(wsmUrl, siteId);
            },
            getAsyncTracker: function (wsmUrl, siteId) {

                var firstTracker;
                if (arrTrackers && arrTrackers.length && arrTrackers[0]) {
                    firstTracker = arrTrackers[0];
                } else {
                    return wsmCreateFirstTracker(wsmUrl, siteId);
                }

                if (!siteId && !wsmUrl) {                 
                    return firstTracker;
                }                
                if ((!isDefined(siteId) || null === siteId) && firstTracker) {
                    siteId = firstTracker.getSiteId();
                }

                if ((!isDefined(wsmUrl) || null === wsmUrl) && firstTracker) {
                    wsmUrl = firstTracker.getTrackerUrl();
                }

                var tracker, i = 0;
                for (i; i < arrTrackers.length; i++) {
                    tracker = arrTrackers[i];
                    if (tracker
                        && String(tracker.getSiteId()) === String(siteId)
                        && tracker.getTrackerUrl() === wsmUrl) {

                        return tracker;
                    }
                }
            },
            getAllTrackers: function () {
                return arrTrackers;
            },
            newTracker: function (wsmUrl, siteId) {              
                if (!arrTrackers.length) {
                    wsmCreateFirstTracker(wsmUrl, siteId);
                } else {
                    arrTrackers[0].newTracker(wsmUrl, siteId);
                }                 
            }
        }; 
        // Expose Wsm as an AMD module
        if (typeof define === 'function' && define.amd) {
            define('wsm', [], function () { return Wsm; });
        }
        return Wsm;
    }());
}
(function () {
    'use strict';    
    function hasWsmConfiguration(){
        if ('object' !== typeof _wsm) {
            return false;
        }        
        var lengthType = typeof _wsm.length;
        if ('undefined' === lengthType) {
            return false;
        }
        return !!_wsm.length;
    }
    if (!window.Wsm.getAllTrackers().length) {       
        if (hasWsmConfiguration()) {
            window.Wsm.newTracker();
        } else {
            _wsm = {push: function (args) {
                if (console !== undefined && console && console.error) {
                    console.error('_wsm.push() was used but Wsm tracker was not initialized before the wsm.js file was loaded.', args);
                }
            }};
        }
    }
   // window.Wsm.trigger('WsmInitialized', []);
    window.Wsm.initialized = true;
}());
if (typeof wsm_log !== 'function') {
    wsm_log = function (wsmPageTitle, wpSiteId, wsmUrl) {
        'use strict';    
        var wsmTracker = window.wsm.getTracker(wsmUrl, wpSiteId);
        // initialize tracker
        wsmTracker.setPageTitle(wsmPageTitle);
        wsmTracker.trackPageView();
    };
}