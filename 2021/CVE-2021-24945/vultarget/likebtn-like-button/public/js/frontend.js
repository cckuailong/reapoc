// Init LikeBtn
(function(d, e, s) {
    /// Load script if it has not been loaded in footer
    if (typeof(likebtn_wl) == "undefined") {
        a = d.createElement(e);
        m = d.getElementsByTagName(e)[0];
        a.async = 1;
        a.src = s;
        m.parentNode.insertBefore(a, m);
    }
    likebtn_init();
})(document, 'script', '//w.likebtn.com/js/w/widget.js');

function likebtn_init()
{
    if (typeof(LikeBtn) != "undefined") {
        LikeBtn.init();
    }
    setTimeout(likebtn_init, 250);
}

// Call ajax handler and custom event handler if set
function likebtn_eh(event)
{
    var old_type = 0;

    // Call custom event_handler if set
    if (event.wrapper) {
        var custom_eh = event.wrapper.getAttribute('data-custom_eh');
        if (custom_eh) {
            var callback = window[custom_eh];
            if (typeof(callback) === 'function') {
                try {
                    callback(event);
                } catch(e) {
                    likebtn_log("Error occured calling event handler function '" + custom_eh + "': " + e.message);
                }
            }
        }
    }

    var modal_content = event.wrapper.getAttribute('data-clk_modal');
    if (event.type === "likebtn.click" && modal_content) {
        likebtn_modal(modal_content);
        return;
    }

    // Do not send ajax request if proxy request is being sent
    if (typeof(event.settings.prx) !== "undefined" && event.settings.prx) {
        return;
    }

    var type = 0;

    if (event.type === "likebtn.like") {
        type = 1;
    } else if (event.type === "likebtn.dislike") {
        type = -1;
    } else if (event.type === "likebtn.unlike") {
        old_type = 1;
        type = 0;
    } else if (event.type === "likebtn.undislike") {
        old_type = -1;
        type = 0;
    } else {
        return;
    }

    // Check if ajax data is set using wp_localize_script
    if (typeof(likebtn_eh_data) === "undefined") {
        likebtn_log('likebtn_eh_data not set');
        return;
    }

    // Ajax
    var data = {
        action: 'likebtn_event_handler',
        old_type: old_type,
        type: type,
        identifier: event.settings.identifier,
        security: likebtn_eh_data.security
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    likebtn_ajax(likebtn_eh_data.ajaxurl, data, function() {
        if (this.readyState == XMLHttpRequest.DONE ) {
            if (this.status == 200) {
               var response = JSON.parse(this.responseText);

               if (response) {
                    if (response.result && response.result == 'success') {

                    } else {
                        var msg = 'Error sending ajax request';
                        if (response.message) {
                            msg += ': '+response.message;
                        }
                        likebtn_log(msg);
                    }
                } else {
                    likebtn_log('Error parsing ajax response');
                }
            } else {
                likebtn_log('Error sending ajax request');
           }
        }
    });
}

// Send ajax request to the server
function likebtn_ajax(url, data, callback, method)
{
    var xmlhttp;

    if (typeof(method) === "undefined") {
        method = "POST";
    }

    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = callback;

    xmlhttp.open(method, url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(likebtn_http_build_query(data));
}

// Log a message
function likebtn_log(msg)
{
    if (typeof(console) !== "undefined") {
        console.log(msg);
    }
}

// Convert array to params string
function likebtn_http_build_query(params) {
    var lst = [];
    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            lst.push(encodeURIComponent(key)+"="+encodeURIComponent(params[key]));
        }
    }
    return lst.join("&");
}

// Display modal window
function likebtn_modal(content)
{
    if (typeof(window['likebtn_modal_popup']) !== "undefined") {
        window['likebtn_modal_popup'].open();
        return;
    }
    var html = 
    '<div class="likebtn-mdl-wrapper">'+
      '<div class="likebtn-mdl-content">'+
        '<div class="likebtn-mdl-title">'+
          '<button type="button" class="likebtn-mdl-close">&times;</button>'+
          //'<h3>&nbsp;</h3>'+
        '</div>'+
        '<div class="likebtn-mdl-body">'+
          '<p>'+content+'</p>'+
        '</div>'+
      '</div>'+
    '</div>';

    var popup_el = document.createElement('div');
    popup_el.innerHTML = html;
    if (popup_el.childNodes[0]) {
        popup_el = popup_el.childNodes[0];
    }
    document.body.appendChild(popup_el);

    // As a native plugin
    window['likebtn_modal_popup'] = new Popup(popup_el, {
        width: 400,
        height: 300,
        closeBtnClass: 'likebtn-mdl-close'
    });

    window['likebtn_modal_popup'].open();
}

// Simple Popup
// http://www.cssscript.com/simple-clean-popup-window-with-pure-javascript-simple-popup/
!function(t,e){e(0,t)}(this,function(t,e){"use strict";function n(t,e){return this instanceof n?(this.opts=f({},f(c,e)),this.el=t,void this.init()):new n(t,e)}var i=window,o=document,s=o.documentElement,c={width:500,height:400,offsetX:0,offsetY:0,zIndex:999,closeBtnClass:"popup-close"},f=function(t,e){for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n]);return t},u=function(){return i.innerWidth||s.clientWidth},r=function(){return i.innerHeight||s.clientHeight},h=function(){return i.pageXOffset||s.scrollLeft},l=function(){return i.pageYOffset||s.scrollTop},a=function(t,e){if(e=e||o,e.getElementsByClassName)return e.getElementsByClassName(t);for(var n=[],i=new RegExp("^|\\s+"+t+"\\s+|$"),s=e.getElementsByTagName("*"),c=0,f=s.length;f>c;c++)i.test(s[c].className)&&n.push(s[c]);return n},p=function(t,e,n){t.addEventListener?t.addEventListener(e,n,!1):t.attachEvent("on"+e,n)};return f(n.prototype,{init:function(){var t=this.opts;f(this.el.style,{position:"absolute",width:t.width+"px",height:'auto',zIndex:t.zIndex}),this.bindEvent()},bindEvent:function(){var t=a(this.opts.closeBtnClass)[0],e=this;p(t,"click",function(){e.close()}),p(o,"keydown",function(t){t=t||window.event;var n=t.which||t.keyCode;27===n&&e.close()}),p(i,"resize",function(){e.setPosition()})},open:function(){this.el.style.display="block",this.setPosition()},close:function(){this.el.style.display="none"},setPosition:function(){var t=this.opts,e=l()+Math.max(0,(r()-t.height)/2),n=h()+Math.max(0,(u()-t.width)/2);f(this.el.style,{top:e+t.offsetY+"px",left:n+t.offsetX+"px"})}}),t&&t.fn?t.fn.popup=function(e){var i=[];return this.each(function(t,o){i.push(new n(o,e))}),{open:function(){t.each(i,function(t,e){e.open()})},close:function(){t.each(i,function(t,e){e.close()})}}}:e&&(e.Popup=n),n});