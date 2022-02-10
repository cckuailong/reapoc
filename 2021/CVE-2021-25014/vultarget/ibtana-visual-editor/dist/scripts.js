jQuery( document ).ready( function( $ ) {

  // Exit Intent for popup code starts here
  // jQuery Exit Intent Plugin
  (function(a){function d(e){0<e.clientY||(b&&clearTimeout(b),0>=a.exitIntent.settings.sensitivity?a.event.trigger("exitintent"):b=setTimeout(function(){b=null;a.event.trigger("exitintent")},a.exitIntent.settings.sensitivity))}function c(){b&&(clearTimeout(b),b=null)}var b;a.exitIntent=function(b,f){a.exitIntent.settings=a.extend(a.exitIntent.settings,f);if("enable"==b)a(window).mouseleave(d),a(window).mouseenter(c);else if("disable"==b)c(),a(window).unbind("mouseleave",d),a(window).unbind("mouseenter",c);else throw"Invalid parameter to jQuery.exitIntent -- should be 'enable'/'disable'";};a.exitIntent.settings={sensitivity:300}})(jQuery);

  function searchStringInArray ( str, strArray ) {
    for (var j=0; j<strArray.length; j++) {
      if (strArray[j].match(str)) return j;
    }
    return -1;
  }

  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  if ( document.querySelectorAll('.ive_exit_intent').length ) {
    $.exitIntent( 'enable' );
    var exit_intents = document.querySelectorAll('.ive_exit_intent');

    var the_trigger_buttons = [];
    for (var i = 0; i < exit_intents.length; i++) {
      var exit_intent = exit_intents[i];
      var exit_intent_classList = exit_intent.classList;
      var index_number_of_class = searchStringInArray( 'ive-popup-open-', exit_intent_classList );
      var the_unique_id_class = exit_intent_classList[index_number_of_class];

      var the_unique_id = the_unique_id_class.split( 'ive-popup-open-' )[1];
      var the_trigger_button = "span.ive-popup-open-" + the_unique_id;

      the_trigger_buttons.push( {
        button_selector: the_trigger_button,
        cookie_based: jQuery( exit_intent ).hasClass( 'ive_cookie_popup' ),
        the_unique_id: the_unique_id
      } );
    }

    $( document ).bind( 'exitintent', function() {
      for (var i = 0; i < the_trigger_buttons.length; i++) {
        var the_trigger_button = the_trigger_buttons[i];
        if ( the_trigger_button.cookie_based === true ) {
          if ( getCookie( the_trigger_button.the_unique_id ) == "" ) {
            setCookie( the_trigger_button.the_unique_id, true, 365 );
            jQuery( the_trigger_button.button_selector ).trigger( 'click' );
          }
          continue;
        }

        if ( jQuery( the_trigger_button.button_selector ).attr( 'already' ) !== "true" ) {
          jQuery( the_trigger_button.button_selector ).attr( 'already', true );
          jQuery( the_trigger_button.button_selector ).trigger( 'click' );
        }
      }
    } );
  }
  // Exit Intent for popup code ends here

  $(".ive-social-media-parent-icon").click(function() {
    var dataLink = $(this).attr('data-link') !== '' ? $(this).attr('data-link') : '#' ;
    window.location.href = dataLink;
  });

  $(".btn_ive_team_main #more_btn_url").click(function(){
    var dataLink = $(this).attr('data-href') !== '' ? $(this).attr('data-href') : '#' ;
    window.location.href = dataLink;
  });

  $( ".social_icon_main .team_member_social" ).each(function( socialIcons ) {
    var dataLink = $(this).attr('data-href') !== '' ? $(this).attr('data-href') : '#' ;
    window.location.href = dataLink;
  });
  var windowSliders={};
  $( ".ive-carousel-content-wrap" ).each(function( index, OwlHscrolls ) {

    var navtextprev = $(this).attr('data-navtextprev');
    var navtextnext = $(this).attr('data-navtextnext');
    var navbtntype = $(this).attr('data-navbtntype');

    if (navbtntype=='icon') {
      var navtextprevicon= `<i class="`+navtextprev+`"></i>`;
      var navtextnexticon= `<i class="`+ navtextnext +`"></i>`;
    }else{
      var navtextprevicon= navtextprev ;
      var navtextnexticon = navtextnext ;
    }

    var settingData={
      nav: true,
      dots: true,
      margin: parseInt($(this).attr('data-margin')),
      stagePadding: parseInt($(this).attr('data-stagepadding')),
      rewind: ($(this).attr('data-rewind') === "true"),
      autoplay: ($(this).attr('data-autoplay') === "true"),
      autoplayTimeout: parseInt($(this).attr('data-autoplaytimeout')),
      autoplayHoverPause: ($(this).attr('data-autoplayhoverpause') === "true"),
      autoplaySpeed: parseInt($(this).attr('data-autoplayspeed')),
      navSpeed: parseInt($(this).attr('data-navspeed')),
      dotsSpeed: parseInt($(this).attr('data-dotsspeed')),
      loop: ($(this).attr('data-loop') === "true"),
      navText: [navtextprevicon, navtextnexticon],
      responsive: {
        0: {
          items: 1
        },
        425: {
          items: parseInt($(this).attr('data-responsive-mob'))
        },
        720: {
          items: parseInt($(this).attr('data-responsive-tab'))
        },
        1024: {
          items: parseInt($(this).attr('data-responsive-desk'))
        }
      }
    };

    var SingleSlider = $(this).owlCarousel(settingData);
    windowSliders[index] = SingleSlider;

  });
  window.windowSliders = windowSliders;

  $('.get-gallery-id').each(function(index,val){
    $id = val.dataset.galleryId;
    $("#"+$id).lightGallery({
      selector: '.light_item'
    });
  });

  $( ".imageaction" ).click(function(){
    var totalmembers = $(this).attr('data-total');
    var blockid = $(this).attr('data-blockid');
    var id = $(this).attr('data-id');
    var heading = $(this).attr('data-head');
    var content = $(this).attr('data-content');
    var url1 = $(this).attr('data-url1');
    var url2 = $(this).attr('data-url2');
    var url3 = $(this).attr('data-url3');
    var btn_url = $(this).attr('data-btn-url');
    var btn = $(this).attr('data-btn');
    var activecolor = $(this).attr('data-active');
    var color = $(this).attr('data-color');
    $( "."+blockid+" .ive_about_title" ).html(heading);
    $( "."+blockid+" .ive_about_content" ).html(content);
    $( "."+blockid+" .btn_about" ).html(btn);
    $("a#social1").attr("href", url1!= '' ? url1 : "#");
    $("a#more_btn_url").attr("href", btn_url!= '' ? btn_url : "#");
    $("a#social2").attr("href", url2!= '' ? url2 : "#");
    $("a#social3").attr("href", url3!= '' ? url3 : "#");

    $( "."+blockid+" .imageaction" ).each(function( index ) {
      if($(this).attr('data-id') === id){
        $("."+blockid+" .memberimg_"+index).css("outline","4px solid "+activecolor);
      }else{
        $("."+blockid+" .memberimg_"+index).css("outline","4px solid "+color);
      }
    });
  });

  // Counter
  function ibtana_visual_editor_ibtana_counter(count) {

      var current = parseInt(count.html(), 10);

      count.html(++current);
      if(current !== count.data('count')){
          setTimeout(function(){ibtana_visual_editor_ibtana_counter(count)}, 50 );
      }
  }

  function ibtana_visual_editor_isScrolledIntoView(elem) {
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();
    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();
    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
  }

  $(window).on('scroll', function() {
    if($(".counter_limit").length) {
      $('.counter_limit').each(function(i, obj) {
        if (ibtana_visual_editor_isScrolledIntoView($(this))) {
          if (!$(obj).attr('is-already-ran')) {
            $(obj).data('count', parseInt($(obj).html(), 10));
            $(obj).html('0');
            ibtana_visual_editor_ibtana_counter($(obj));
          }
          $(obj).attr('is-already-ran', 1);
          // $(window).off('scroll');
        }
      });
    }
    if($('.progress_counter').length){
      $('.progress_counter').each(function(i, obj) {
        if (ibtana_visual_editor_isScrolledIntoView($(this))) {
          var objspan = $(obj).find('.prgress_cnt');

          if (!objspan.attr('is-already-ran')) {
            objspan.data('count', parseInt(objspan.html(), 10));
            objspan.html('0');
            ibtana_visual_editor_progress_counter(objspan,$(obj));
          }
          objspan.attr('is-already-ran', 1);
          // $(window).off('scroll');
        }
      });
    }
    // count.attr('data-sd')

  });

  function ibtana_visual_editor_progress_counter(count, parent_div){
    var current = parseInt(count.html(), 10);
    count.html(++current);
    var count_id = count.attr('data-sd');

    if(parent_div.find('.ibtana_progress-bar-line-path').length){
      var svg_count = parseInt(count_id) - 1;
      parent_div.find('.ibtana_progress-bar-line-path').css('stroke-dashoffset', svg_count + 'px' );
      count.attr('data-sd', svg_count);
    }else{
      var circular_cnt = parseInt(count_id)-3 ;
      parent_div.find('.ibtana_progress-bar-circle-path').css('stroke-dashoffset', circular_cnt + 'px' );
      count.attr('data-sd', circular_cnt);
    }

    if(current !== count.data('count')){
        setTimeout(function(){ibtana_visual_editor_progress_counter(count,parent_div)}, 50 );
    }
  }
  // Counter END

});



// Popup js
setTimeout(function(){jQuery(".wp-block-ive-popup").show()},100),jQuery(document).ready(function(t){function a(){if(""!=window.location.hash&&t(window.location.hash+".ive-title-item").length){var a=window.location.hash.substring(1),i=t("#"+a+" a").attr("data-tab");t("#"+a).closest(".ive-tabs-title-list").find(".ive-tab-title-active").addClass("ive-tab-title-inactive").removeClass("ive-tab-title-active"),t("#"+a).closest(".ive-tabs-wrap").removeClass(function(t,a){return(a.match(/\bive-active-tab-\S+/g)||[]).join(" ")}).addClass("ive-active-tab-"+i),t("#"+a).addClass("ive-tab-title-active"),t("#"+a).closest(".ive-tabs-wrap").find(".ive-tabs-accordion-title.ive-tabs-accordion-title-"+a).addClass("ive-tab-title-active").removeClass("ive-tab-title-inactive")}}jQuery(".ive-pop-title-text").each(function(){t(this).on("click",function(){var a=jQuery(this).attr("dataid");t("#ivepopup-"+a).show()})}),jQuery(".ive-close-popup").each(function(){t(this).on("click",function(){var a=jQuery(this).attr("dataid");t("#ivepopup-"+a).hide()})}),t(".ive-tabs-wrap").each(function(a){var i=t(this).find("> .ive-tabs-title-list .ive-tab-title-active").attr("data-tab"),e=t(this).find("> .ive-tabs-title-list").attr({role:"tablist"});t(this).find("> .ive-tabs-content-wrap > .ive-tab-inner-content").attr({role:"tabpanel","aria-hidden":"true"}),t(this).find("> .ive-tabs-title-list a").each(function(a){var i=t(this).attr("data-tab"),e=t(this).parent().attr("id");t(this).closest(".ive-tabs-wrap").find(".ive-tabs-content-wrap > .ive-inner-tab-"+i).attr("aria-labelledby",e)}),t(this).find(".ive-tabs-content-wrap > .ive-inner-tab-"+i).attr("aria-hidden","false"),t(this).find("> .ive-tabs-title-list li:not(.ive-tab-title-active) a").each(function(){t(this).attr({role:"tab","aria-selected":"false",tabindex:"-1"}).parent().attr("role","presentation")}),t(this).find("> .ive-tabs-title-list li.ive-tab-title-active a").attr({role:"tab","aria-selected":"true",tabindex:"0"}).parent().attr("role","presentation"),t(e).delegate("a","keydown",function(a){switch(a.which){case 37:case 38:0!=t(this).parent().prev().length?t(this).parent().prev().find("> a").click():t(e).find("li:last > a").click();break;case 39:case 40:0!=t(this).parent().next().length?t(this).parent().next().find("> a").click():t(e).find("li:first > a").click()}})}),t(".ive-tabs-title-list li a").click(function(a){a.preventDefault();var i=t(this).attr("data-tab");t(this).closest(".ive-tabs-title-list").find(".ive-tab-title-active").addClass("ive-tab-title-inactive").removeClass("ive-tab-title-active").find("a.ive-tab-title").attr({tabindex:"-1","aria-selected":"false"}),t(this).closest(".ive-tabs-wrap").removeClass(function(t,a){return(a.match(/\bive-active-tab-\S+/g)||[]).join(" ")}).addClass("ive-active-tab-"+i),t(this).parent("li").addClass("ive-tab-title-active").removeClass("ive-tab-title-inactive"),t(this).attr({tabindex:"0","aria-selected":"true"}).focus(),t(this).closest(".ive-tabs-wrap").find(".ive-tabs-content-wrap > .ive-tab-inner-content:not(.ive-inner-tab-"+i+")").attr("aria-hidden","true"),t(this).closest(".ive-tabs-wrap").find(".ive-tabs-content-wrap > .ive-inner-tab-"+i).attr("aria-hidden","false"),t(this).closest(".ive-tabs-wrap").find(".ive-tabs-content-wrap > .ive-tabs-accordion-title:not(.ive-tabs-accordion-title-"+i+")").addClass("ive-tab-title-inactive").removeClass("ive-tab-title-active").attr({tabindex:"-1","aria-selected":"false"}),t(this).closest(".ive-tabs-wrap").find(".ive-tabs-content-wrap > .ive-tabs-accordion-title.ive-tabs-accordion-title-"+i).addClass("ive-tab-title-active").removeClass("ive-tab-title-inactive").attr({tabindex:"0","aria-selected":"true"});var e=window.document.createEvent("UIEvents");e.initUIEvent("resize",!0,!1,window,0),window.dispatchEvent(e);var s=window.document.createEvent("UIEvents");s.initUIEvent("ibtana-tabs-open",!0,!1,window,0),window.dispatchEvent(s)}),t(".ive-create-accordion").find("> .ive-tabs-title-list .ive-title-item").each(function(){var a,i,e,s=t(this).find("a").attr("data-tab");a=t(this).hasClass("ive-tab-title-active")?"ive-tab-title-active":"ive-tab-title-inactive",i=t(this).hasClass("ive-tabs-svg-show-only")?"ive-tabs-svg-show-only":"ive-tabs-svg-show-always",e=t(this).hasClass("ive-tabs-icon-side-top")?"ive-tabs-icon-side-top":"",t(this).closest(".ive-tabs-wrap").find("> .ive-tabs-content-wrap").before('<div class="ive-tabs-accordion-title ive-tabs-accordion-title-'+s+" "+a+" "+i+" "+e+'">'+t(this).html()+"</div>"),t(this).closest(".ive-tabs-wrap").find("> .ive-tabs-content-wrap > .ive-tabs-accordion-title-"+s+"  a").removeAttr("role")}),t(".ive-tabs-accordion-title a").click(function(a){a.preventDefault();var i=t(this).attr("data-tab");
var cntdiv = t(".ive-create-accordion").find("> .ive-tabs-title-list .ive-title-item").length;
for (var q = 1; q <= cntdiv; q++) {
   t(this).parent(".ive-tabs-accordion-title").closest(".ive-tabs-wrap").removeClass("ive-active-tab-" + q);
   t(this).parent(".ive-tabs-accordion-title").closest(".ive-tabs-wrap").find('.ive-tabs-accordion-title').removeClass("ive-tab-title-active");
}
t(this).parent(".ive-tabs-accordion-title").closest(".ive-tabs-wrap").addClass("ive-active-tab-" + i);
t(this).parent(".ive-tabs-accordion-title").closest(".ive-tabs-wrap").find('.ive-tabs-accordion-title-' + i).addClass("ive-tab-title-active");
var e=window.document.createEvent("UIEvents");e.initUIEvent("resize",!0,!1,window,0),window.dispatchEvent(e);var s=window.document.createEvent("UIEvents");s.initUIEvent("ibtana-tabs-open",!0,!1,window,0),window.dispatchEvent(s)}),window.addEventListener("hashchange",a,!1),a()});
// Accordion js
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.IbtanaAccordion=e()}(this,function(){"use strict";function t(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}function e(){return(e=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(t[i]=n[i])}return t}).apply(this,arguments)}function n(t){return function(t){if(Array.isArray(t)){for(var e=0,n=new Array(t.length);e<t.length;e++)n[e]=t[e];return n}}(t)||function(t){if(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t))return Array.from(t)}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}()}var i,a,s,o;return function(){if("function"==typeof window.CustomEvent)return!1;function t(t,e){e=e||{bubbles:!1,cancelable:!1,detail:void 0};var n=document.createEvent("CustomEvent");return n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail),n}t.prototype=window.Event.prototype,window.CustomEvent=t}(),Array.from||(Array.from=(i=Object.prototype.toString,a=function(t){return"function"==typeof t||"[object Function]"===i.call(t)},s=Math.pow(2,53)-1,o=function(t){var e=function(t){var e=Number(t);return isNaN(e)?0:0!==e&&isFinite(e)?(e>0?1:-1)*Math.floor(Math.abs(e)):e}(t);return Math.min(Math.max(e,0),s)},function(t){var e=Object(t);if(null==t)throw new TypeError("Array.from requires an array-like object - not null or undefined");var n,i=arguments.length>1?arguments[1]:void 0;if(void 0!==i){if(!a(i))throw new TypeError("Array.from: when provided, the second argument must be a function");arguments.length>2&&(n=arguments[2])}for(var s,r=o(e.length),l=a(this)?Object(new this(r)):new Array(r),c=0;c<r;)s=e[c],l[c]=i?void 0===n?i(s,c):i.call(n,s,c):s,c+=1;return l.length=r,l})),function(t,e){var n=(t.body||t.documentElement).style,i="",a="";""==n.WebkitAnimation&&(i="-webkit-"),""==n.MozAnimation&&(i="-moz-"),""==n.OAnimation&&(i="-o-"),""==n.WebkitTransition&&(a="-webkit-"),""==n.MozTransition&&(a="-moz-"),""==n.OTransition&&(a="-o-"),Object.defineProperty(Object.prototype,"onCSSAnimationEnd",{value:function(t){var e=function e(n){t(),n.target.removeEventListener(n.type,e)};return this.addEventListener("webkitAnimationEnd",e),this.addEventListener("mozAnimationEnd",e),this.addEventListener("oAnimationEnd",e),this.addEventListener("oanimationend",e),this.addEventListener("animationend",e),(""!=i||"animation"in n)&&"0s"!=getComputedStyle(this)[i+"animation-duration"]||t(),this},enumerable:!1,writable:!0}),Object.defineProperty(Object.prototype,"onCSSTransitionEnd",{value:function(t){var e=function e(n){t(),n.target.removeEventListener(n.type,e)};return this.addEventListener("webkitTransitionEnd",e),this.addEventListener("mozTransitionEnd",e),this.addEventListener("oTransitionEnd",e),this.addEventListener("transitionend",e),this.addEventListener("transitionend",e),(""!=a||"transition"in n)&&"0s"!=getComputedStyle(this)[a+"transition-duration"]||t(),this},enumerable:!1,writable:!0})}(document,window),function(){function i(t,a){var s=this;!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,i);var o="string"==typeof t?document.querySelector(t):t;if(null!=o){var r={headerClass:".ive-blocks-accordion-header",panelClass:".ive-accordion-panel",panelInnerClass:".ive-accordion-panel-inner",hiddenClass:"ive-accordion-panel-hidden",activeClass:"ive-accordion-panel-active",get hidenClass(){return this.hiddenClass},initializedClass:"ive-accordion-initialized",get initalisedClass(){return this.initializedClass},headerDataAttr:"data-ive-accordion-header-id",openMultiplePanels:!1,openHeadersOnLoad:[],headerOpenLabel:"",headerCloseLabel:"",roles:!0};this.settings=e({},r,a),this.container=o;var l=Array.from(this.container.children),c=[];Array.from(l).forEach(function(t){Array.from(t.children).forEach(function(t){c.push(t)})});var d=c.filter(function(t){return!t.classList.contains(s.settings.panelClass.substr(1))});this.headers=d.reduce(function(t,e){var i,a=Array.from(e.children).filter(function(t){return t.classList.contains(s.settings.headerClass.substr(1))});return!a.length&&e.children[0]&&e.children[0].children&&e.children[0].children.length&&(a=Array.from(e.children[0].children).filter(function(t){return t.classList.contains(s.settings.headerClass.substr(1))})),t=(i=[]).concat.apply(i,n(t).concat([a]))},[]),this.panels=c.filter(function(t){return t.classList.contains(s.settings.panelClass.substr(1))}),this.toggleEl=void 0!==this.settings.toggleEl?Array.from(this.container.querySelectorAll(this.settings.toggleEl)):this.headers,this.states=[].map.call(this.headers,function(){return{state:"closed"}}),this.ids=[].map.call(this.headers,function(){return{id:Math.floor(1e6*Math.random()+1)}}),this.toggling=!1,this.container?this.init():console.log("Something is wrong with you markup...")}}var a,s,o;return a=i,(s=[{key:"init",value:function(){this._setupAttributes(),this._initalState(),this.calculateAllPanelsHeight(),this._insertDataAttrs(),this._addListeners(),this._finishInitialization()}},{key:"_setRole",value:function(t,e){("boolean"==typeof this.settings.roles&&this.settings.roles||void 0!==this.settings.roles[t]&&!1!==this.settings.roles[t])&&e.setAttribute("role",t)}},{key:"_initalState",value:function(){var t=this.settings.openHeadersOnLoad;t.length&&(this.toggling=!0,this._openHeadersOnLoad(t)),this._renderDom()}},{key:"_insertDataAttrs",value:function(){var t=this;this.headers.forEach(function(e,n){e.setAttribute(t.settings.headerDataAttr,n)})}},{key:"_finishInitialization",value:function(){this.container.classList.add(this.settings.initializedClass),this._setRole("presentation",this.container);var t=new CustomEvent("initialized");this.container.dispatchEvent(t)}},{key:"_addListeners",value:function(){var t=this;this.headers.forEach(function(e,n){e.addEventListener("click",function(){t.handleClick(e,n)})})}},{key:"handleClick",value:function(t,e){var n=this,i=this.settings.headerClass.substr(1);if(t.classList.contains(i)&&!1===this.toggling)this.toggling=!0,this.setState(e),this._renderDom();else var a=setInterval(function(){!1===n.toggling&&(n.toggling=!0,n.setState(e),n._renderDom(),clearInterval(a))},50)}},{key:"setState",value:function(t){var e=this,n=this.getState();this.settings.openMultiplePanels||n.filter(function(e,n){n!=t&&(e.state="closed")}),n.filter(function(n,i){if(i==t){var a=e.toggleState(n.state);return n.state=a}})}},{key:"_renderDom",value:function(){var t=this;this.states.filter(function(e,n){"open"===e.state&&t.open(n,!1)}),this.states.filter(function(e,n){"closed"===e.state&&t.close(n,!1)})}},{key:"open",value:function(t){(!(arguments.length>1&&void 0!==arguments[1])||arguments[1])&&this.setState(t),this.togglePanel("open",t)}},{key:"close",value:function(t){(!(arguments.length>1&&void 0!==arguments[1])||arguments[1])&&this.setState(t),this.togglePanel("closed",t)}},{key:"openAll",value:function(){var t=this;this.headers.forEach(function(e,n){t.togglePanel("open",n)})}},{key:"closeAll",value:function(){var t=this;this.headers.forEach(function(e,n){t.togglePanel("closed",n)})}},{key:"togglePanel",value:function(t,e){var n=this;if(void 0!==t&&void 0!==e)if("closed"===t){var i=this.headers[e],a=this.panels[e];if(!a.classList.contains(this.settings.hiddenClass)){a.setAttribute("data-panel-height",a.scrollHeight+"px"),a.style.height=a.scrollHeight+"px",a.offsetHeight,a.style.height="",a.classList.add("ive-panel-is-collapsing"),a.classList.remove(this.settings.activeClass),i.classList.remove(this.settings.activeClass),i.setAttribute("aria-expanded",!1);var s=1e3*parseFloat(getComputedStyle(a).transitionDuration);setTimeout(function(){return a.classList.add(n.settings.hiddenClass),a.classList.remove("ive-panel-is-collapsing"),n.toggling=!1},s)}}else if("open"===t){var o=this.headers[e],r=this.panels[e];if(!r.classList.contains(this.settings.activeClass)){r.classList.remove(this.settings.hiddenClass),r.style.height=0,r.offsetHeight,r.classList.add("ive-panel-is-expanding"),r.style.height=r.scrollHeight<parseInt(r.getAttribute("data-panel-height"))?parseInt(r.getAttribute("data-panel-height"))+"px":r.scrollHeight+"px",r.offsetHeight,o.classList.add(this.settings.activeClass),o.setAttribute("aria-expanded",!0);var l=window.document.createEvent("UIEvents");l.initUIEvent("resize",!0,!1,window,0),window.dispatchEvent(l);var c=1e3*parseFloat(getComputedStyle(r).transitionDuration);setTimeout(function(){return r.classList.add(n.settings.activeClass),r.style.height="",r.classList.remove("ive-panel-is-expanding"),n.toggling=!1},c)}}}},{key:"getState",value:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[];return e.length&&Array.isArray(e)?e.map(function(e){return t.states[e]}):this.states}},{key:"toggleState",value:function(t){if(void 0!==t)return"closed"===t?"open":"closed"}},{key:"_openHeadersOnLoad",value:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[];e.length&&Array.isArray(e)&&e.filter(function(t){return null!=t}).forEach(function(e){t.setState(e)})}},{key:"_setupAttributes",value:function(){this._setupHeaders(),this._setupPanels(),this._insertDataAttrs()}},{key:"_setPanelHeight",value:function(){this.calculateAllPanelsHeight()}},{key:"calculatePanelHeight",value:function(t){var e=t.querySelector(this.settings.panelInnerClass).getBoundingClientRect();return t.setAttribute("data-panel-height","".concat(e.height,"px"))}},{key:"calculateAllPanelsHeight",value:function(){var t=this;this.panels.forEach(function(e){t.calculatePanelHeight(e)})}},{key:"_setupHeaders",value:function(){var t=this;this.headers.forEach(function(e,n){e.setAttribute("id","ive-accordion-header-".concat(t.ids[n].id)),e.setAttribute("aria-controls","ive-accordion-panel-".concat(t.ids[n].id))})}},{key:"_setupPanels",value:function(){var t=this;this.panels.forEach(function(e,n){e.setAttribute("id","ive-accordion-panel-".concat(t.ids[n].id)),e.setAttribute("aria-labelledby","ive-accordion-header-".concat(t.ids[n].id)),!0!==t.settings.roles&&!1===t.settings.roles.region||t._setRole("region",e)})}}])&&t(a.prototype,s),o&&t(a,o),i}()}),function(){"use strict";window.IbtanaBlocksAccordion={scroll:function(t,e,n){if(!(n<=0)){var i=(e-t.scrollTop)/n*10;setTimeout(function(){t.scrollTop=t.scrollTop+i,t.scrollTop!==e&&scrollTo(t,e,n-10)},10)}},anchor:function(t){if(""!=window.location.hash){var e,n=location.hash.substring(1);if(!/^[A-z0-9_-]+$/.test(n))return;if((e=document.getElementById(n))&&e.classList.contains("wp-block-ive-pane")){var i=document.querySelectorAll("#"+n+" .ive-blocks-accordion-header")[0];i.classList.contains("ive-accordion-panel-active")||(t.type&&"initialized"===t.type?window.setTimeout(function(){i.click()},50):i.click())}}},init:function(){for(var t=document.querySelectorAll(".ive-accordion-inner-wrap"),e=Array.from(t),n=0,i=e.length;n<i;n++){var a=e[n].getAttribute("data-allow-multiple-open"),s=e[n].getAttribute("data-start-open"),o=parseInt(s);if("none"!==s)for(var r=0,l=e[n].children.length;r<l;r++)e[n].children[r].classList.contains("ive-accordion-pane-"+(1+o))&&(o=r);e[n].addEventListener("initialized",window.IbtanaBlocksAccordion.anchor,!1),new IbtanaAccordion(e[n],{openHeadersOnLoad:"none"===s?[]:[parseInt(o)],headerClass:".ive-blocks-accordion-header",panelClass:".ive-accordion-panel",panelInnerClass:".ive-accordion-panel-inner",hiddenClass:"ive-accordion-panel-hidden",activeClass:"ive-accordion-panel-active",initializedClass:"ive-accordion-initialized",headerDataAttr:"data-ive-accordion-header-id",openMultiplePanels:"true"===a})}window.addEventListener("hashchange",window.IbtanaBlocksAccordion.anchor,!1)}},"loading"===document.readyState?document.addEventListener("DOMContentLoaded",window.IbtanaBlocksAccordion.init):window.IbtanaBlocksAccordion.init()}();
