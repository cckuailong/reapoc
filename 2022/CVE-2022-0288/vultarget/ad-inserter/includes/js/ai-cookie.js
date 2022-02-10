/*!
 * JavaScript Cookie v2.2.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
  var registeredInModuleLoader;
  if (typeof define === 'function' && define.amd) {
    define(factory);
    registeredInModuleLoader = true;
  }
  if (typeof exports === 'object') {
    module.exports = factory();
    registeredInModuleLoader = true;
  }
  if (!registeredInModuleLoader) {
    var OldCookies = window.Cookies;
    var api = window.Cookies = factory();
    api.noConflict = function () {
      window.Cookies = OldCookies;
      return api;
    };
  }
}(function () {
  function extend () {
    var i = 0;
    var result = {};
    for (; i < arguments.length; i++) {
      var attributes = arguments[ i ];
      for (var key in attributes) {
        result[key] = attributes[key];
      }
    }
    return result;
  }

  function decode (s) {
    return s.replace(/(%[0-9A-Z]{2})+/g, decodeURIComponent);
  }

  function init (converter) {
    function api() {}

    function set (key, value, attributes) {
      if (typeof document === 'undefined') {
        return;
      }

      attributes = extend({
        path: '/',
        sameSite: 'Lax'
      }, api.defaults, attributes);

      if (typeof attributes.expires === 'number') {
        attributes.expires = new Date(new Date() * 1 + attributes.expires * 864e+5);
      }

      // We're using "expires" because "max-age" is not supported by IE
      attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

      try {
        var result = JSON.stringify(value);
        if (/^[\{\[]/.test(result)) {
          value = result;
        }
      } catch (e) {}

      value = converter.write ?
        converter.write(value, key) :
        encodeURIComponent(String(value))
          .replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);

      key = encodeURIComponent(String(key))
        .replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent)
        .replace(/[\(\)]/g, escape);

      var stringifiedAttributes = '';
      for (var attributeName in attributes) {
        if (!attributes[attributeName]) {
          continue;
        }
        stringifiedAttributes += '; ' + attributeName;
        if (attributes[attributeName] === true) {
          continue;
        }

        // Considers RFC 6265 section 5.2:
        // ...
        // 3.  If the remaining unparsed-attributes contains a %x3B (";")
        //     character:
        // Consume the characters of the unparsed-attributes up to,
        // not including, the first %x3B (";") character.
        // ...
        stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
      }

      return (document.cookie = key + '=' + value + stringifiedAttributes);
    }

    function get (key, json) {
      if (typeof document === 'undefined') {
        return;
      }

      var jar = {};
      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all.
      var cookies = document.cookie ? document.cookie.split('; ') : [];
      var i = 0;

      for (; i < cookies.length; i++) {
        var parts = cookies[i].split('=');
        var cookie = parts.slice(1).join('=');

        if (!json && cookie.charAt(0) === '"') {
          cookie = cookie.slice(1, -1);
        }

        try {
          var name = decode(parts[0]);
          cookie = (converter.read || converter)(cookie, name) ||
            decode(cookie);

          if (json) {
            try {
              cookie = JSON.parse(cookie);
            } catch (e) {}
          }

          jar[name] = cookie;

          if (key === name) {
            break;
          }
        } catch (e) {}
      }

      return key ? jar[key] : jar;
    }

    api.set = set;
    api.get = function (key) {
      return get(key, false /* read as raw */);
    };
    api.getJSON = function (key) {
      return get(key, true /* read as json */);
    };
    api.remove = function (key, attributes) {
      set(key, '', extend(attributes, {
        expires: -1
      }));
    };

    api.defaults = {};

    api.withConverter = init;

    return api;
  }

  return init(function () {});
}));



AiCookies = Cookies.noConflict();


ai_check_block = function (block) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

  if (block == null) {
    return true;
  }

  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);
  ai_debug_cookie_status = '';

  if (ai_cookie == null) {
    ai_cookie = {};
  }

  if (typeof ai_delay_showing_pageviews !== 'undefined') {
    if (!ai_cookie.hasOwnProperty (block)) {
      ai_cookie [block] = {};
    }

    if (!ai_cookie [block].hasOwnProperty ('d')) {
      ai_cookie [block]['d'] = ai_delay_showing_pageviews;

      if (ai_debug) console.log ('AI CHECK block', block, 'NO COOKIE DATA d, delayed for', ai_delay_showing_pageviews, 'pageviews');
    }
  }

  if (ai_cookie.hasOwnProperty (block)) {
    for (var cookie_block_property in ai_cookie [block]) {

      if (cookie_block_property == 'x') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'x cookie hash', cookie_code_hash, 'code hash', code_hash);

        var date = new Date();
        var closed_for = ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
        if (closed_for > 0 && cookie_code_hash == code_hash) {
          var message = 'closed for ' + closed_for + ' s = ' + (Math.round (10000 * closed_for / 3600 / 24) / 10000) + ' days';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else {
            if (ai_debug) console.log ('AI CHECK block', block, 'removing x');

            ai_set_cookie (block, 'x', '');
            if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('c')) {
              ai_set_cookie (block, 'h', '');
            }
          }
      } else
      if (cookie_block_property == 'd') {
        if (ai_cookie [block][cookie_block_property] != 0) {
          var message = 'delayed for ' + ai_cookie [block][cookie_block_property] + ' pageviews';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        }
      } else
      if (cookie_block_property == 'i') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'i cookie hash', cookie_code_hash, 'code hash', code_hash);

        if (ai_cookie [block][cookie_block_property] == 0 && cookie_code_hash == code_hash) {
          var message = 'max impressions reached';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else

        if (ai_cookie [block][cookie_block_property] < 0 && cookie_code_hash == code_hash) {
          var date = new Date();
          var closed_for = - ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
          if (closed_for > 0) {
            var message = 'max imp. reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          } else {
              if (ai_debug) console.log ('AI CHECK block', block, 'removing i');

              ai_set_cookie (block, 'i', '');
              if (!ai_cookie [block].hasOwnProperty ('c') && !ai_cookie [block].hasOwnProperty ('x')) {
                if (ai_debug) console.log ('AI CHECK block', block, 'cookie h removed');

                ai_set_cookie (block, 'h', '');
              }
            }
        }
      }
      if (cookie_block_property == 'ipt') {
        if (ai_cookie [block][cookie_block_property] == 0) {

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);
          var closed_for = ai_cookie [block]['it'] - timestamp;

          if (closed_for > 0) {
            var message = 'max imp. per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          }
        }
      }
      if (cookie_block_property == 'c') {

        var code_hash = '';
        var block_object = document.querySelectorAll ('span[data-ai-block="'+block+'"]') [0]
        if ("aiHash" in block_object.dataset) {
          code_hash = block_object.dataset.aiHash;
        }

        var cookie_code_hash = '';
        if (ai_cookie [block].hasOwnProperty ('h')) {
          cookie_code_hash = ai_cookie [block]['h'];
        }
        if (ai_debug) console.log ('AI CHECK block', block, 'c cookie hash', cookie_code_hash, 'code hash', code_hash);

        if (ai_cookie [block][cookie_block_property] == 0 && cookie_code_hash == code_hash) {
          var message = 'max clicks reached';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK block', block, message);
          if (ai_debug) console.log ('');

          return false;
        } else

        if (ai_cookie [block][cookie_block_property] < 0 && cookie_code_hash == code_hash) {
          var date = new Date();
          var closed_for = - ai_cookie [block][cookie_block_property] - Math.round (date.getTime() / 1000);
          if (closed_for > 0) {
            var message = 'max clicks reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          } else {
              if (ai_debug) console.log ('AI CHECK block', block, 'removing c');

              ai_set_cookie (block, 'c', '');
              if (!ai_cookie [block].hasOwnProperty ('i') && !ai_cookie [block].hasOwnProperty ('x')) {
                if (ai_debug) console.log ('AI CHECK block', block, 'cookie h removed');

                ai_set_cookie (block, 'h', '');
              }
            }
        }
      }
      if (cookie_block_property == 'cpt') {
        if (ai_cookie [block][cookie_block_property] == 0) {

          var date = new Date();
          var timestamp = Math.round (date.getTime() / 1000);
          var closed_for = ai_cookie [block]['ct'] - timestamp;

          if (closed_for > 0) {
            var message = 'max clicks per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
            ai_debug_cookie_status = message;
            if (ai_debug) console.log ('AI CHECK block', block, message);
            if (ai_debug) console.log ('');

            return false;
          }
        }
      }
    }

    if (ai_cookie.hasOwnProperty ('G') && ai_cookie ['G'].hasOwnProperty ('cpt')) {
      if (ai_cookie ['G']['cpt'] == 0) {

        var date = new Date();
        var timestamp = Math.round (date.getTime() / 1000);
        var closed_for = ai_cookie ['G']['ct'] - timestamp;

        if (closed_for > 0) {
          var message = 'max global clicks per time reached (' + Math. round (10000 * closed_for / 24 / 3600) / 10000 + ' days = ' + closed_for + ' s)';
          ai_debug_cookie_status = message;
          if (ai_debug) console.log ('AI CHECK GLOBAL', message);
          if (ai_debug) console.log ('');

          return false;
        }
      }
    }
  }

  ai_debug_cookie_status = 'OK';
  if (ai_debug) console.log ('AI CHECK block', block, 'OK');
  if (ai_debug) console.log ('');

  return true;
}

ai_check_and_insert_block = function (block, id) {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

  if (block == null) {
    return true;
  }

  var ai_block_divs = document.getElementsByClassName (id);
  if (ai_block_divs.length) {
    var ai_block_div = ai_block_divs [0];
    var wrapping_div = ai_block_div.closest ('.AI_FUNCT_GET_BLOCK_CLASS_NAME');

    var insert_block = ai_check_block (block);

    if (!insert_block) {
//      if (ai_debug) console.log ('AI CHECK FAILED, !insert_block', block);
      // Check for a fallback block
      if (parseInt (ai_block_div.getAttribute ('limits-fallback')) != 0 && ai_block_div.hasAttribute ('data-fallback-code')) {

        if (ai_debug) console.log ('AI CHECK FAILED, INSERTING FALLBACK BLOCK', ai_block_div.getAttribute ('limits-fallback'));

        ai_block_div.setAttribute ('data-code', ai_block_div.getAttribute ('data-fallback-code'));

        if (wrapping_div.hasAttribute ('data-ai')) {
          if (ai_block_div.hasAttribute ('fallback-tracking') && ai_block_div.hasAttribute ('fallback_level')) {
            wrapping_div.setAttribute ('data-ai-' + ai_block_div.getAttribute ('fallback_level'), ai_block_div.getAttribute ('fallback-tracking'));
          }
        }

        insert_block = true;
      }
    }

    if (insert_block) {
      ai_insert_code (ai_block_div);
      if (wrapping_div) {

        var debug_block = wrapping_div.querySelectorAll ('.ai-debug-block');
        if (wrapping_div && debug_block.length) {
          wrapping_div.classList.remove ('ai-list-block');
          wrapping_div.classList.remove ('ai-list-block-ip');
          wrapping_div.classList.remove ('ai-list-block-filter');
          wrapping_div.style.visibility = '';
          if (wrapping_div.classList.contains ('ai-remove-position')) {
            wrapping_div.style.position = '';
          }
        }

      }
    } else {
        var ai_block_div_data = ai_block_div.closest ('div[data-ai]');
        if (ai_block_div_data != null && typeof ai_block_div_data.getAttribute ("data-ai") != "undefined") {
          var data = JSON.parse (b64d (ai_block_div_data.getAttribute ("data-ai")));
          if (typeof data !== "undefined" && data.constructor === Array) {
            data [1] = "";
            ai_block_div_data.setAttribute ("data-ai", b64e (JSON.stringify (data)));
          }
        }
        var debug_block = wrapping_div.querySelectorAll ('.ai-debug-block');
        if (wrapping_div && debug_block.length) {
          wrapping_div.classList.remove ('ai-list-block');
          wrapping_div.classList.remove ('ai-list-block-ip');
          wrapping_div.classList.remove ('ai-list-block-filter');
          wrapping_div.style.visibility = '';
          if (wrapping_div.classList.contains ('ai-remove-position')) {
            wrapping_div.style.position = '';
          }
        }
      }

    ai_block_div.classList.remove (id);
  }

  var ai_debug_bars = document.querySelectorAll ('.' + id + '-dbg');

//  for (let ai_debug_bar of ai_debug_bars) {
  for (var index = 0, len = ai_debug_bars.length; index < len; index++) {
    var ai_debug_bar = ai_debug_bars [index];
    ai_debug_bar.querySelector ('.ai-status').textContent = ai_debug_cookie_status;
    ai_debug_bar.querySelector ('.ai-cookie-data').textContent = ai_get_cookie_text (block);
    ai_debug_bar.classList.remove (id + '-dbg');
  }
}

function ai_load_cookie () {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//  var ai_debug = false;

  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);

  if (ai_cookie == null) {
    ai_cookie = {};

    if (ai_debug) console.log ('AI COOKIE NOT PRESENT');
  }

  if (ai_debug) console.log ('AI COOKIE LOAD', ai_cookie);

  return ai_cookie;
}

function ai_get_cookie (block, property) {

  var ai_debug = typeof ai_debugging !== 'undefined'; // 4
//  var ai_debug = false;

  var value = '';
  var ai_cookie = ai_load_cookie ();

  if (ai_cookie.hasOwnProperty (block)) {
    if (ai_cookie [block].hasOwnProperty (property)) {
      value = ai_cookie [block][property];
    }
  }

  if (ai_debug) console.log ('AI COOKIE GET block:', block, 'property:', property, 'value:', value);

  return value;
}

function ai_set_cookie (block, property, value) {

  function isEmpty (obj) {
    for (var key in obj) {
        if (obj.hasOwnProperty (key))
          return false;
    }
    return true;
  }

  var ai_cookie_name = 'aiBLOCKS';
  var ai_debug = typeof ai_debugging !== 'undefined'; // 5
//  var ai_debug = false;

  if (ai_debug) console.log ('AI COOKIE SET block:', block, 'property:', property, 'value:', value);

  var ai_cookie = ai_load_cookie ();

  if (value === '') {
    if (ai_cookie.hasOwnProperty (block)) {
      delete ai_cookie [block][property];
      if (isEmpty (ai_cookie [block])) {
        delete ai_cookie [block];
      }
    }
  } else {
      if (!ai_cookie.hasOwnProperty (block)) {
        ai_cookie [block] = {};
      }
      ai_cookie [block][property] = value;
    }

  if (Object.keys (ai_cookie).length === 0 && ai_cookie.constructor === Object) {
    AiCookies.remove (ai_cookie_name);

    if (ai_debug) console.log ('AI COOKIE REMOVED');
  } else {
      AiCookies.set (ai_cookie_name, ai_cookie, {expires: 365, path: '/'});
    }

  if (ai_debug) {
    var ai_cookie_test = AiCookies.getJSON (ai_cookie_name);
    if (typeof (ai_cookie_test) != 'undefined') {
      console.log ('AI COOKIE NEW', ai_cookie_test);

      console.log ('AI COOKIE DATA:');
      for (var cookie_block in ai_cookie_test) {
        for (var cookie_block_property in ai_cookie_test [cookie_block]) {
          if (cookie_block_property == 'x') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'closed for', closed_for, 's = ', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'd') {
            console.log ('  BLOCK', cookie_block, 'delayed for', ai_cookie_test [cookie_block][cookie_block_property], 'pageviews');
          } else
          if (cookie_block_property == 'e') {
            console.log ('  BLOCK', cookie_block, 'show every', ai_cookie_test [cookie_block][cookie_block_property], 'pageviews');
          } else
          if (cookie_block_property == 'i') {
            var i = ai_cookie_test [cookie_block][cookie_block_property];
            if (i >= 0) {
              console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'impressions until limit');
            } else {
                var date = new Date();
                var closed_for = - i - Math.round (date.getTime() / 1000);
                console.log ('  BLOCK', cookie_block, 'max impressions, closed for', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
              }
          } else
          if (cookie_block_property == 'ipt') {
            console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'impressions until limit per time period');
          } else
          if (cookie_block_property == 'it') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'impressions limit expiration in', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'c') {
            var c = ai_cookie_test [cookie_block][cookie_block_property]
            if (c >= 0) {
              console.log ('  BLOCK', cookie_block, c, 'clicks until limit');
            } else {
                var date = new Date();
                var closed_for = - c - Math.round (date.getTime() / 1000);
                console.log ('  BLOCK', cookie_block, 'max clicks, closed for', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
              }
          } else
          if (cookie_block_property == 'cpt') {
            console.log ('  BLOCK', cookie_block, ai_cookie_test [cookie_block][cookie_block_property], 'clicks until limit per time period');
          } else
          if (cookie_block_property == 'ct') {
            var date = new Date();
            var closed_for = ai_cookie_test [cookie_block][cookie_block_property] - Math.round (date.getTime() / 1000);
            console.log ('  BLOCK', cookie_block, 'clicks limit expiration in ', closed_for, 's =', Math.round (10000 * closed_for / 3600 / 24) / 10000, 'days');
          } else
          if (cookie_block_property == 'h') {
            console.log ('  BLOCK', cookie_block, 'hash', ai_cookie_test [cookie_block][cookie_block_property]);
          } else
          console.log ('      ?:', cookie_block, ':', cookie_block_property, ai_cookie_test [cookie_block][cookie_block_property]);
        }
        console.log ('');
      }
    } else console.log ('AI COOKIE NOT PRESENT');
  }

  return ai_cookie;
}

ai_get_cookie_text = function (block) {
  var ai_cookie_name = 'aiBLOCKS';
  var ai_cookie = AiCookies.getJSON (ai_cookie_name);

  if (ai_cookie == null) {
    ai_cookie = {};
  }

  var global_data = '';
  if (ai_cookie.hasOwnProperty ('G')) {
    global_data = 'G[' + JSON.stringify (ai_cookie ['G']).replace (/\"/g, '').replace ('{', '').replace('}', '') + '] ';
  }

  var block_data = '';
  if (ai_cookie.hasOwnProperty (block)) {
    block_data = JSON.stringify (ai_cookie [block]).replace (/\"/g, '').replace ('{', '').replace('}', '');
  }

  return global_data + block_data;
}
