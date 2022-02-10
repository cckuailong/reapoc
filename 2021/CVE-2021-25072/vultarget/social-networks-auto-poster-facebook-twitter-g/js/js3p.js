/*================================================================================
 * @name: bPopup * @author: (c)Bjoern Klinggaard (twitter@bklinggaard) * @version: 0.11.0.min
 ================================================================================*/
  (function(c){c.fn.bPopup=function(A,E){function L(){a.contentContainer=c(a.contentContainer||b);switch(a.content){case "iframe":var d=c('<iframe class="b-iframe" '+a.iframeAttr+"></iframe>");d.appendTo(a.contentContainer);t=b.outerHeight(!0);u=b.outerWidth(!0);B();d.attr("src",a.loadUrl);l(a.loadCallback);break;case "image":B();c("<img />").load(function(){l(a.loadCallback);F(c(this))}).attr("src",a.loadUrl).hide().appendTo(a.contentContainer);break;default:B(),c('<div class="b-ajax-wrapper"></div>').load(a.loadUrl,a.loadData,function(d,b,e){l(a.loadCallback,b);F(c(this))}).hide().appendTo(a.contentContainer)}}function B(){a.modal&&c('<div class="b-modal '+e+'"></div>').css({backgroundColor:a.modalColor,position:"fixed",top:0,right:0,bottom:0,left:0,opacity:0,zIndex:a.zIndex+v}).appendTo(a.appendTo).fadeTo(a.speed,a.opacity);C();b.data("bPopup",a).data("id",e).css({left:"slideIn"==a.transition||"slideBack"==a.transition?"slideBack"==a.transition?f.scrollLeft()+w:-1*(x+u):m(!(!a.follow[0]&&n||g)),position:a.positionStyle||"absolute",top:"slideDown"==a.transition||"slideUp"==a.transition?"slideUp"==a.transition?f.scrollTop()+y:z+-1*t:p(!(!a.follow[1]&&q||g)),"z-index":a.zIndex+v+1}).each(function(){a.appending&&c(this).appendTo(a.appendTo)});G(!0)}function r(){a.modal&&c(".b-modal."+b.data("id")).fadeTo(a.speed,0,function(){c(this).remove()});a.scrollBar||c("html").css("overflow","auto");c(".b-modal."+e).unbind("click");f.unbind("keydown."+e);k.unbind("."+e).data("bPopup",0<k.data("bPopup")-1?k.data("bPopup")-1:null);b.undelegate(".bClose, ."+a.closeClass,"click."+e,r).data("bPopup",null);clearTimeout(H);G();return!1}function I(d){y=k.height();w=k.width();h=D();if(h.x||h.y)clearTimeout(J),J=setTimeout(function(){C();d=d||a.followSpeed;var e={};h.x&&(e.left=a.follow[0]?m(!0):"auto");h.y&&(e.top=a.follow[1]?p(!0):"auto");b.dequeue().each(function(){g?c(this).css({left:x,top:z}):c(this).animate(e,d,a.followEasing)})},50)}function F(d){var c=d.width(),e=d.height(),f={};a.contentContainer.css({height:e,width:c});e>=b.height()&&(f.height=b.height());c>=b.width()&&(f.width=b.width());t=b.outerHeight(!0);u=b.outerWidth(!0);C();a.contentContainer.css({height:"auto",width:"auto"});f.left=m(!(!a.follow[0]&&n||g));f.top=p(!(!a.follow[1]&&q||g));b.animate(f,250,function(){d.show();h=D()})}function M(){k.data("bPopup",v);b.delegate(".bClose, ."+a.closeClass,"click."+e,r);a.modalClose&&c(".b-modal."+e).css("cursor","pointer").bind("click",r);N||!a.follow[0]&&!a.follow[1]||k.bind("scroll."+e,function(){if(h.x||h.y){var d={};h.x&&(d.left=a.follow[0]?m(!g):"auto");h.y&&(d.top=a.follow[1]?p(!g):"auto");b.dequeue().animate(d,a.followSpeed,a.followEasing)}}).bind("resize."+e,function(){I()});a.escClose&&f.bind("keydown."+e,function(a){27==a.which&&r()})}function G(d){function c(e){b.css({display:"block",opacity:1}).animate(e,a.speed,a.easing,function(){K(d)})}switch(d?a.transition:a.transitionClose||a.transition){case "slideIn":c({left:d?m(!(!a.follow[0]&&n||g)):f.scrollLeft()-(u||b.outerWidth(!0))-200});break;case "slideBack":c({left:d?m(!(!a.follow[0]&&n||g)):f.scrollLeft()+w+200});break;case "slideDown":c({top:d?p(!(!a.follow[1]&&q||g)):f.scrollTop()-(t||b.outerHeight(!0))-200});break;case "slideUp":c({top:d?p(!(!a.follow[1]&&q||g)):f.scrollTop()+y+200});break;default:b.stop().fadeTo(a.speed,d?1:0,function(){K(d)})}}function K(d){d?(M(),l(E),a.autoClose&&(H=setTimeout(r,a.autoClose))):(b.hide(),l(a.onClose),a.loadUrl&&(a.contentContainer.empty(),b.css({height:"auto",width:"auto"})))}function m(a){return a?x+f.scrollLeft():x}function p(a){return a?z+f.scrollTop():z}function l(a,e){c.isFunction(a)&&a.call(b,e)}function C(){z=q?a.position[1]:Math.max(0,(y-b.outerHeight(!0))/2-a.amsl);x=n?a.position[0]:(w-b.outerWidth(!0))/2;h=D()}function D(){return{x:w>b.outerWidth(!0),y:y>b.outerHeight(!0)}}c.isFunction(A)&&(E=A,A=null);var a=c.extend({},c.fn.bPopup.defaults,A);a.scrollBar||c("html").css("overflow","hidden");var b=this,f=c(document),k=c(window),y=k.height(),w=k.width(),N=/OS 6(_\d)+/i.test(navigator.userAgent),v=0,e,h,q,n,g,z,x,t,u,J,H;b.close=function(){r()};b.reposition=function(a){I(a)};return b.each(function(){c(this).data("bPopup")||(l(a.onOpen),v=(k.data("bPopup")||0)+1,e="__b-popup"+v+"__",q="auto"!==a.position[1],n="auto"!==a.position[0],g="fixed"===a.positionStyle,t=b.outerHeight(!0),u=b.outerWidth(!0),a.loadUrl?L():B())})};c.fn.bPopup.defaults={amsl:50,appending:!0,appendTo:"body",autoClose:!1,closeClass:"b-close",content:"ajax",contentContainer:!1,easing:"swing",escClose:!0,follow:[!0,!0],followEasing:"swing",followSpeed:500,iframeAttr:'scrolling="no" frameborder="0"',loadCallback:!1,loadData:!1,loadUrl:!1,modal:!0,modalClose:!0,modalColor:"#000",onClose:!1,onOpen:!1,opacity:.7,position:["auto","auto"],positionStyle:"absolute",scrollBar:!0,speed:250,transition:"fadeIn",transitionClose:!1,zIndex:9997}})(jQuery);
  
   /*! iCheck v1.0.2 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
(function(f){function A(a,b,d){var c=a[0],g=/er/.test(d)?_indeterminate:/bl/.test(d)?n:k,e=d==_update?{checked:c[k],disabled:c[n],indeterminate:"true"==a.attr(_indeterminate)||"false"==a.attr(_determinate)}:c[g];if(/^(ch|di|in)/.test(d)&&!e)x(a,g);else if(/^(un|en|de)/.test(d)&&e)q(a,g);else if(d==_update)for(var f in e)e[f]?x(a,f,!0):q(a,f,!0);else if(!b||"toggle"==d){if(!b)a[_callback]("ifClicked");e?c[_type]!==r&&q(a,g):x(a,g)}}function x(a,b,d){var c=a[0],g=a.parent(),e=b==k,u=b==_indeterminate,
v=b==n,s=u?_determinate:e?y:"enabled",F=l(a,s+t(c[_type])),B=l(a,b+t(c[_type]));if(!0!==c[b]){if(!d&&b==k&&c[_type]==r&&c.name){var w=a.closest("form"),p='input[name="'+c.name+'"]',p=w.length?w.find(p):f(p);p.each(function(){this!==c&&f(this).data(m)&&q(f(this),b)})}u?(c[b]=!0,c[k]&&q(a,k,"force")):(d||(c[b]=!0),e&&c[_indeterminate]&&q(a,_indeterminate,!1));D(a,e,b,d)}c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"default");g[_add](B||l(a,b)||"");g.attr("role")&&!u&&g.attr("aria-"+(v?n:k),"true");
g[_remove](F||l(a,s)||"")}function q(a,b,d){var c=a[0],g=a.parent(),e=b==k,f=b==_indeterminate,m=b==n,s=f?_determinate:e?y:"enabled",q=l(a,s+t(c[_type])),r=l(a,b+t(c[_type]));if(!1!==c[b]){if(f||!d||"force"==d)c[b]=!1;D(a,e,s,d)}!c[n]&&l(a,_cursor,!0)&&g.find("."+C).css(_cursor,"pointer");g[_remove](r||l(a,b)||"");g.attr("role")&&!f&&g.attr("aria-"+(m?n:k),"false");g[_add](q||l(a,s)||"")}function E(a,b){if(a.data(m)){a.parent().html(a.attr("style",a.data(m).s||""));if(b)a[_callback](b);a.off(".i").unwrap();
f(_label+'[for="'+a[0].id+'"]').add(a.closest(_label)).off(".i")}}function l(a,b,f){if(a.data(m))return a.data(m).o[b+(f?"":"Class")]}function t(a){return a.charAt(0).toUpperCase()+a.slice(1)}function D(a,b,f,c){if(!c){if(b)a[_callback]("ifToggled");a[_callback]("ifChanged")[_callback]("if"+t(f))}}var m="iCheck",C=m+"-helper",r="radio",k="checked",y="un"+k,n="disabled";_determinate="determinate";_indeterminate="in"+_determinate;_update="update";_type="type";_click="click";_touch="touchbegin.i touchend.i";
_add="addClass";_remove="removeClass";_callback="trigger";_label="label";_cursor="cursor";_mobile=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);f.fn[m]=function(a,b){var d='input[type="checkbox"], input[type="'+r+'"]',c=f(),g=function(a){a.each(function(){var a=f(this);c=a.is(d)?c.add(a):c.add(a.find(d))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(a))return a=a.toLowerCase(),g(this),c.each(function(){var c=
f(this);"destroy"==a?E(c,"ifDestroyed"):A(c,!0,a);f.isFunction(b)&&b()});if("object"!=typeof a&&a)return this;var e=f.extend({checkedClass:k,disabledClass:n,indeterminateClass:_indeterminate,labelHover:!0},a),l=e.handle,v=e.hoverClass||"hover",s=e.focusClass||"focus",t=e.activeClass||"active",B=!!e.labelHover,w=e.labelHoverClass||"hover",p=(""+e.increaseArea).replace("%","")|0;if("checkbox"==l||l==r)d='input[type="'+l+'"]';-50>p&&(p=-50);g(this);return c.each(function(){var a=f(this);E(a);var c=this,
b=c.id,g=-p+"%",d=100+2*p+"%",d={position:"absolute",top:g,left:g,display:"block",width:d,height:d,margin:0,padding:0,background:"#fff",border:0,opacity:0},g=_mobile?{position:"absolute",visibility:"hidden"}:p?d:{position:"absolute",opacity:0},l="checkbox"==c[_type]?e.checkboxClass||"icheckbox":e.radioClass||"i"+r,z=f(_label+'[for="'+b+'"]').add(a.closest(_label)),u=!!e.aria,y=m+"-"+Math.random().toString(36).substr(2,6),h='<div class="'+l+'" '+(u?'role="'+c[_type]+'" ':"");u&&z.each(function(){h+=
'aria-labelledby="';this.id?h+=this.id:(this.id=y,h+=y);h+='"'});h=a.wrap(h+"/>")[_callback]("ifCreated").parent().append(e.insert);d=f('<ins class="'+C+'"/>').css(d).appendTo(h);a.data(m,{o:e,s:a.attr("style")}).css(g);e.inheritClass&&h[_add](c.className||"");e.inheritID&&b&&h.attr("id",m+"-"+b);"static"==h.css("position")&&h.css("position","relative");A(a,!0,_update);if(z.length)z.on(_click+".i mouseover.i mouseout.i "+_touch,function(b){var d=b[_type],e=f(this);if(!c[n]){if(d==_click){if(f(b.target).is("a"))return;
A(a,!1,!0)}else B&&(/ut|nd/.test(d)?(h[_remove](v),e[_remove](w)):(h[_add](v),e[_add](w)));if(_mobile)b.stopPropagation();else return!1}});a.on(_click+".i focus.i blur.i keyup.i keydown.i keypress.i",function(b){var d=b[_type];b=b.keyCode;if(d==_click)return!1;if("keydown"==d&&32==b)return c[_type]==r&&c[k]||(c[k]?q(a,k):x(a,k)),!1;if("keyup"==d&&c[_type]==r)!c[k]&&x(a,k);else if(/us|ur/.test(d))h["blur"==d?_remove:_add](s)});d.on(_click+" mousedown mouseup mouseover mouseout "+_touch,function(b){var d=
b[_type],e=/wn|up/.test(d)?t:v;if(!c[n]){if(d==_click)A(a,!1,!0);else{if(/wn|er|in/.test(d))h[_add](e);else h[_remove](e+" "+t);if(z.length&&B&&e==v)z[/ut|nd/.test(d)?_remove:_add](w)}if(_mobile)b.stopPropagation();else return!1}})})}})(window.jQuery||window.Zepto);
  
/* jquery.quickselect.min.js - https://github.com/dcparker/jquery_plugins/tree/master/quickselect */
function object(d){var s=function(){};s.prototype=d;return new s();}var QuickSelect;(function($){QuickSelect=function(d,f){var self=this;d=$(d);d.attr('autocomplete','off');self.options=f;self.AllItems={};var g=false,h=-1,j=false,k,l,m,n=false,o,p;if(/MSIE (\d+\.\d+);/.test(navigator.userAgent)){if(Number(RegExp.$1)<=7)n=true;}o=$('<div class="'+f.resultsClass+'" style="display:block;position:absolute;z-index:9999;"></div>').hide();p=$('<iframe />');p.css({border:'none',position:'absolute'});if(f.width>0){o.css("width",f.width);p.css("width",f.width);}$('body').append(o);o.hide();if(n)$('body').append(p);self.getLabel=function(A){return A.label||(typeof(A)==='string'?A:A[0])||'';};var r=function(A){return A.values||(A.value?[A.value]:(typeof(A)==='string'?[A]:A))||[];};var t=function(A){var B=$('li',o);if(!B)return;if(typeof(A)==="number")h=h+A;else h=B.index(A);if(h<0)h=0;else if(h>=B.size())h=B.size()-1;B.removeClass(f.selectedClass);$(B[h]).addClass(f.selectedClass);if(f.autoFill&&self.last_keyCode!=8){d.val(l+$(B[h]).text().substring(l.length));var C=l.length,D=d.val().length,E=d.get(0);if(E.createTextRange){var F=E.createTextRange();F.collapse(true);F.moveStart("character",C);F.moveEnd("character",D);F.select();}else if(E.setSelectionRange){E.setSelectionRange(C,D);}else if(E.selectionStart){E.selectionStart=C;E.selectionEnd=D;}E.focus();}};var u=function(){if(m){clearTimeout(m);}d.removeClass(f.loadingClass);if(o.is(":visible"))o.hide();if(p.is(":visible"))p.hide();h=-1;};self.selectItem=function(A,B){if(!A){A=document.createElement("li");A.item='';}var C=self.getLabel(A.item),D=r(A.item);d.lastSelected=C;d.val(C);l=C;o.empty();$(f.additionalFields).each(function(i,E){$(E).val(D[i+1]);});if(!B)u();if(f.onItemSelect)setTimeout(function(){f.onItemSelect(A);},1);return true;};var v=function(){var A=$("li."+f.selectedClass,o).get(0);if(A){return self.selectItem(A);}else{if(f.exactMatch){d.val('');$(f.additionalFields).each(function(i,B){$(B).val('');});}return false;}};var w=function(A){o.empty();if(!j||A===null||A.length===0)return u();var B=document.createElement("ul"),C=A.length,D=function(){t(this);},E=function(){},F=function(e){e.preventDefault();e.stopPropagation();self.selectItem(this);};o.append(B);if(f.maxVisibleItems>0&&f.maxVisibleItems<C)C=f.maxVisibleItems;for(var i=0;i<C;i++){var G=A[i],H=document.createElement("li");o.append(H);$(H).text(f.formatItem?f.formatItem(G,i,C):self.getLabel(G));H.item=G;if(G.className)H.className=G.className;B.appendChild(H);$(H).hover(D,E).click(F);}d.removeClass(f.loadingClass);return true;};var x=function(q,A){f.finderFunction.apply(self,[q,function(B){w(f.matchMethod.apply(self,[q,B]));A();}]);};var y=function(){var A=d.offset(),B=(f.width>0?f.width:d.width()),C=$('li',o);o.css({width:parseInt(B,10)+"px",top:A.top+d.height()+5+"px",left:A.left+"px"});if(n){p.css({width:parseInt(B,10)-2+"px",top:A.top+d.height()+6+"px",left:A.left+1+"px",height:o.height()-2+'px'}).show();}o.show();if(f.autoSelectFirst||(f.selectSingleMatch&&C.length==1))t(C.get(0));};var z=function(){if(k>=9&&k<=45){return;}var q=d.val();if(q==l)return;l=q;if(q.length>=f.minChars){d.addClass(f.loadingClass);x(q,y);}else{if(q.length===0&&(f.onBlank?f.onBlank():true))$(f.additionalFields).each(function(i,A){A.value='';});d.removeClass(f.loadingClass);o.hide();p.hide();}};o.mousedown(function(e){if(e.srcElement)g=e.srcElement.tagName!='DIV';});d.keydown(function(e){k=e.keyCode;switch(e.keyCode){case 38:e.preventDefault();t(-1);break;case 40:e.preventDefault();if(!o.is(":visible")){y();t(0);}else{t(1);}break;case 13:if(v()){e.preventDefault();d.select();}break;case 9:break;case 27:if(h>-1&&f.exactMatch&&d.val()!=$($('li',o).get(h)).text()){h=-1;}$('li',o).removeClass(f.selectedClass);u();e.preventDefault();break;default:if(m){clearTimeout(m);}m=setTimeout(z,f.delay);break;}}).focus(function(){j=true;}).blur(function(e){if(h>-1){v();}j=false;if(m){clearTimeout(m);}m=setTimeout(function(){u();if(f.exactMatch&&d.val()!=d.lastSelected){self.selectItem(null,true);}},200);});};QuickSelect.matchers={quicksilver:function(q,d){var f,g,self=this;f=(self.options.matchCase?q:q.toLowerCase());self.AllItems[f]=[];for(var i=0;i<d.length;i++){g=(self.options.matchCase?self.getLabel(d[i]):self.getLabel(d[i]).toLowerCase());if(g.score(f)>0){self.AllItems[f].push(d[i]);}}return self.AllItems[f].sort(function(a,b){a=(self.options.matchCase?self.getLabel(a):self.getLabel(a).toLowerCase());b=(self.options.matchCase?self.getLabel(b):self.getLabel(b).toLowerCase());a=a.score(f);b=b.score(f);return(a>b?-1:(b>a?1:0));});},contains:function(q,d){var f,g,self=this;f=(self.options.matchCase?q:q.toLowerCase());self.AllItems[f]=[];for(var i=0;i<d.length;i++){g=(self.options.matchCase?self.getLabel(d[i]):self.getLabel(d[i]).toLowerCase());if(g.indexOf(f)>-1){self.AllItems[f].push(d[i]);}}return self.AllItems[f].sort(function(a,b){a=(self.options.matchCase?self.getLabel(a):self.getLabel(a).toLowerCase());b=(self.options.matchCase?self.getLabel(b):self.getLabel(b).toLowerCase());var h=a.indexOf(f);var j=b.indexOf(f);return(h>j?-1:(h<j?1:(a>b?-1:(b>a?1:0))));});},startsWith:function(q,d){var f,g,self=this;f=(self.options.matchCase?q:q.toLowerCase());self.AllItems[f]=[];for(var i=0;i<d.length;i++){g=(self.options.matchCase?self.getLabel(d[i]):self.getLabel(d[i]).toLowerCase());if(g.indexOf(f)===0){self.AllItems[f].push(d[i]);}}return self.AllItems[f].sort(function(a,b){a=(self.options.matchCase?self.getLabel(a):self.getLabel(a).toLowerCase());b=(self.options.matchCase?self.getLabel(b):self.getLabel(b).toLowerCase());return(a>b?-1:(b>a?1:0));});}};QuickSelect.finders={data:function(q,d){d(this.options.data);},ajax:function(q,d){var f=this.options.ajax+"?q="+encodeURI(q);for(var i in this.options.ajaxParams){if(this.options.ajaxParams.hasOwnProperty(i)){f+="\x26"+i+"\x3d"+encodeURI(this.options.ajaxParams[i]);}}$.getJSON(f,d);}};$.fn.quickselect=function(d,f){if(d=='instance'&&$(this).data('quickselect'))return $(this).data('quickselect');d=d||{};d.data=(typeof(d.data)==="object"&&d.data.constructor==Array)?d.data:undefined;d.ajaxParams=d.ajaxParams||{};d.delay=d.delay||400;if(!d.delay)d.delay=(!d.ajax?400:10);d.minChars=d.minChars||1;d.cssFlavor=d.cssFlavor||'quickselect';d.inputClass=d.inputClass||d.cssFlavor+"_input";d.loadingClass=d.loadingClass||d.cssFlavor+"_loading";d.resultsClass=d.resultsClass||d.cssFlavor+"_results";d.selectedClass=d.selectedClass||d.cssFlavor+"_selected";d.finderFunction=d.finderFunction||QuickSelect.finders[!d.data?'ajax':'data'];if(d.finderFunction==='data'||d.finderFunction==='ajax')d.finderFunction=QuickSelect.finders[d.finderFunction];d.matchMethod=d.matchMethod||QuickSelect.matchers[(typeof(''.score)==='function'&&'\x6c'.score('\x6c')==1?'quicksilver':'contains')];if(d.matchMethod==='quicksilver'||d.matchMethod==='contains'||d.matchMethod==='startsWith')d.matchMethod=QuickSelect.matchers[d.matchMethod];if(d.matchCase===undefined)d.matchCase=false;if(d.exactMatch===undefined)d.exactMatch=false;if(d.autoSelectFirst===undefined)d.autoSelectFirst=true;if(d.selectSingleMatch===undefined)d.selectSingleMatch=true;if(d.additionalFields===undefined)d.additionalFields=$('nothing');d.maxVisibleItems=d.maxVisibleItems||-1;if(d.autoFill===undefined||d.matchMethod!='startsWith'){d.autoFill=false;}d.width=parseInt(d.width,10)||0;return this.each(function(){var g=this,h=object(d);if(g.tagName=='INPUT'){var j=new QuickSelect(g,h);$(g).data('quickselect',j);}else if(g.tagName=='SELECT'){h.delay=h.delay||10;h.finderFunction='data';var name=g.name,k=g.id,l=g.className,m=$(g).attr('accesskey'),n=$(g).attr('tabindex'),o=$("option:selected",g).get(0);h.data=[];$('option',g).each(function(i,t){h.data.push({label:$(t).text(),values:[t.value,t.value],className:t.className});});var p=$("<input type='text' class='"+l+"' id='"+k+"_quickselect' accesskey='"+m+"' tabindex='"+n+"' />");if(o){p.val($(o).text());}var r=$("<input type='hidden' id='"+k+"' name='"+g.name+"' />");if(o){r.val(o.value);}h.additionalFields=r;$(g).after(p).after(r).remove();p.quickselect(h);}});};})(jQuery);

// String Scoring Algorithm 0.1.22 | (c) 2009-2015 Joshaven Potter <yourtech@gmail.com>
// MIT License: http://opensource.org/licenses/MIT | https://github.com/joshaven/string_score
String.prototype.score=function(e,f){if(this===e)return 1;if(""===e)return 0;var d=0,a,g=this.toLowerCase(),n=this.length,h=e.toLowerCase(),k=e.length,b;a=0;var l=1,m,c;f&&(m=1-f);if(f)for(c=0;c<k;c+=1)b=g.indexOf(h[c],a),-1===b?l+=m:(a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8)),this[b]===e[c]&&(a+=.1),d+=a,a=b+1);else for(c=0;c<k;c+=1){b=g.indexOf(h[c],a);if(-1===b)return 0;a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8));this[b]===e[c]&&(a+=.1);d+=a;a=b+1}d=.5*(d/n+d/k)/l;h[0]===g[0]&&.85>d&&(d+=.15);return d};

!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports&&"function"==typeof require?require("jquery"):jQuery)}(function(t,e){function i(e,i){this._name=s,this.el=e,this.$el=t(e),this.$el.prop("multiple")||(this.settings=t.extend({},o,i,this.$el.data()),this._defaults=o,this.$options=this.$el.find("option, optgroup"),this.init(),t.fn[s].instances.push(this))}var s="comboSelect",n="comboselect",o={comboClass:"combo-select",comboArrowClass:"combo-arrow",comboDropDownClass:"combo-dropdown",inputClass:"combo-input text-input",disabledClass:"option-disabled",hoverClass:"option-hover",selectedClass:"option-selected",markerClass:"combo-marker",themeClass:"",maxHeight:200,extendStyle:!0,focusInput:!0},r={ESC:27,TAB:9,RETURN:13,LEFT:37,UP:38,RIGHT:39,DOWN:40,ENTER:13,SHIFT:16},l=/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase());t.extend(i.prototype,{init:function(){this._construct(),this._events()},_construct:function(){this.$el.data("plugin_"+n+"_tabindex",this.$el.prop("tabindex")),!l&&this.$el.prop("tabIndex",-1),this.$container=this.$el.wrapAll('<div class="'+this.settings.comboClass+" "+this.settings.themeClass+'" />').parent(),this.settings.extendStyle&&this.$el.attr("style")&&this.$container.attr("style",this.$el.attr("style")),this.$arrow=t('<div class="'+this.settings.comboArrowClass+'" />').appendTo(this.$container),this.$dropdown=t('<ul class="'+this.settings.comboDropDownClass+'" />').appendTo(this.$container),this._build(),this.$input=t('<input type="text"'+(l?'tabindex="-1"':"")+' placeholder="'+this.getPlaceholder()+'" class="'+this.settings.inputClass+'">').appendTo(this.$container),this._updateInput()},getPlaceholder:function(){var t="";return this.$options.filter(function(t,e){return"OPTION"==e.nodeName}).each(function(e,i){""==i.value&&(t=i.innerHTML)}),t},_build:function(){var t=this,e="",i=0;this.$options.each(function(s,n){return"optgroup"==n.nodeName.toLowerCase()?e+='<li class="option-group">'+this.label+"</li>":(e+='<li class="'+(this.disabled?t.settings.disabledClass:"option-item")+" "+(i==t.$el.prop("selectedIndex")?t.settings.selectedClass:"")+'" data-index="'+i+'" data-value="'+this.value+'">'+this.innerHTML+"</li>",void i++)}),this.$dropdown.html(e),this.$items=this.$dropdown.children()},_events:function(){this.$container.on("focus.input","input",t.proxy(this._focus,this)),this.$container.on("mouseup.input","input",function(t){t.preventDefault()}),this.$container.on("blur.input","input",t.proxy(this._blur,this)),this.$el.on("change.select",t.proxy(this._change,this)),this.$el.on("focus.select",t.proxy(this._focus,this)),this.$el.on("blur.select",t.proxy(this._blurSelect,this)),this.$container.on("click.arrow","."+this.settings.comboArrowClass,t.proxy(this._toggle,this)),this.$container.on("comboselect:close",t.proxy(this._close,this)),this.$container.on("comboselect:open",t.proxy(this._open,this)),this.$container.on("comboselect:update",t.proxy(this._update,this)),t("html").off("click.comboselect").on("click.comboselect",function(){t.each(t.fn[s].instances,function(t,e){e.$container.trigger("comboselect:close")})}),this.$container.on("click.comboselect",function(t){t.stopPropagation()}),this.$container.on("keydown","input",t.proxy(this._keydown,this)),this.$container.on("keyup","input",t.proxy(this._keyup,this)),this.$container.on("click.item",".option-item",t.proxy(this._select,this))},_keydown:function(t){switch(t.which){case r.UP:this._move("up",t);break;case r.DOWN:this._move("down",t);break;case r.TAB:this._enter(t);break;case r.RIGHT:this._autofill(t);break;case r.ENTER:this._enter(t)}},_keyup:function(t){switch(t.which){case r.ESC:this.$container.trigger("comboselect:close");break;case r.ENTER:case r.UP:case r.DOWN:case r.LEFT:case r.RIGHT:case r.TAB:case r.SHIFT:break;default:this._filter(t.target.value)}},_enter:function(t){var e=this._getHovered();if(e.length&&this._select(e),t&&t.which==r.ENTER){if(!e.length)return this._blur(),!0;t.preventDefault()}},_move:function(t){var e=this._getVisible(),i=this._getHovered(),s=i.prevAll(".option-item").filter(":visible").length,n=e.length;switch(t){case"up":s--,0>s&&(s=n-1);break;case"down":s++,s>=n&&(s=0)}e.removeClass(this.settings.hoverClass).eq(s).addClass(this.settings.hoverClass),this.opened||this.$container.trigger("comboselect:open"),this._fixScroll()},_select:function(e){var i=t(e.currentTarget?e.currentTarget:e);if(i.length){var s=i.data("index");this._selectByIndex(s),this.$input.focus(),this.$container.trigger("comboselect:close")}},_selectByIndex:function(t){"undefined"==typeof t&&(t=0),this.$el.prop("selectedIndex")!=t&&this.$el.prop("selectedIndex",t).trigger("change")},_autofill:function(){var t=this._getHovered();if(t.length){var e=t.data("index");this._selectByIndex(e)}},_filter:function(e){var i=this,s=this._getAll();needle=t.trim(e).toLowerCase(),reEscape=new RegExp("(\\"+["/",".","*","+","?","|","(",")","[","]","{","}","\\"].join("|\\")+")","g"),pattern="("+e.replace(reEscape,"\\$1")+")",t("."+i.settings.markerClass,s).contents().unwrap(),needle?(this.$items.filter(".option-group, .option-disabled").hide(),s.hide().filter(function(){var e=t(this),s=t.trim(e.text()).toLowerCase();return-1!=s.toString().indexOf(needle)?(e.html(function(t,e){return e.replace(new RegExp(pattern,"gi"),'<span class="'+i.settings.markerClass+'">$1</span>')}),!0):void 0}).show()):this.$items.show(),this.$container.trigger("comboselect:open")},_highlight:function(){var t=this._getVisible().removeClass(this.settings.hoverClass),e=t.filter("."+this.settings.selectedClass);e.length?e.addClass(this.settings.hoverClass):t.removeClass(this.settings.hoverClass).first().addClass(this.settings.hoverClass)},_updateInput:function(){var e=this.$el.prop("selectedIndex");return this.$el.val()?(text=this.$el.find("option").eq(e).text(),this.$input.val(text)):this.$input.val(""),this._getAll().removeClass(this.settings.selectedClass).filter(function(){return t(this).data("index")==e}).addClass(this.settings.selectedClass)},_blurSelect:function(){this.$container.removeClass("combo-focus")},_focus:function(t){this.$container.toggleClass("combo-focus",!this.opened),l||(this.opened||this.$container.trigger("comboselect:open"),this.settings.focusInput&&t&&t.currentTarget&&"INPUT"==t.currentTarget.nodeName&&t.currentTarget.select())},_blur:function(){var e=t.trim(this.$input.val().toLowerCase()),i=!isNaN(e),s=this.$options.filter(function(){return"OPTION"==this.nodeName}).filter(function(){var s=this.innerText||this.textContent;return i?parseInt(t.trim(s).toLowerCase())==e:t.trim(s).toLowerCase()==e}).prop("index");this._selectByIndex(s)},_change:function(){this._updateInput()},_getAll:function(){return this.$items.filter(".option-item")},_getVisible:function(){return this.$items.filter(".option-item").filter(":visible")},_getHovered:function(){return this._getVisible().filter("."+this.settings.hoverClass)},_open:function(){var e=this;this.$container.addClass("combo-open"),this.opened=!0,this.settings.focusInput&&setTimeout(function(){!e.$input.is(":focus")&&e.$input.focus()}),this._highlight(),this._fixScroll(),t.each(t.fn[s].instances,function(t,i){i!=e&&i.opened&&i.$container.trigger("comboselect:close")})},_toggle:function(){this.opened?this._close.call(this):this._open.call(this)},_close:function(){this.$container.removeClass("combo-open combo-focus"),this.$container.trigger("comboselect:closed"),this.opened=!1,this.$items.show()},_fixScroll:function(){if(!this.$dropdown.is(":hidden")){var t=this._getHovered();if(t.length){var e,i,s,n=t.outerHeight();e=t[0].offsetTop,i=this.$dropdown.scrollTop(),s=i+this.settings.maxHeight-n,i>e?this.$dropdown.scrollTop(e):e>s&&this.$dropdown.scrollTop(e-this.settings.maxHeight+n)}}},_update:function(){this.$options=this.$el.find("option, optgroup"),this.$dropdown.empty(),this._build()},dispose:function(){this.$arrow.remove(),this.$input.remove(),this.$dropdown.remove(),this.$el.removeAttr("tabindex"),this.$el.data("plugin_"+n+"_tabindex")&&this.$el.prop("tabindex",this.$el.data("plugin_"+n+"_tabindex")),this.$el.unwrap(),this.$el.removeData("plugin_"+n),this.$el.removeData("plugin_"+n+"_tabindex"),this.$el.off("change.select focus.select blur.select")}}),t.fn[s]=function(e,s){return this.each(function(){var o=t(this),r=o.data("plugin_"+n);"string"==typeof e?r&&"function"==typeof r[e]&&r[e](s):(r&&r.dispose&&r.dispose(),t.data(this,"plugin_"+n,new i(this,e)))}),this},t.fn[s].instances=[]});

// DDslick
(function (factory) {

    if (typeof define === "function" && define.amd) {
        /** AMD. Register as an anonymous module. */
        define(["jquery"], factory);
    } else if (typeof module === "object" && module.exports) {
        /** Node/CommonJS */
        module.exports = factory(require("jquery"));
    } else {
        /** Browser globals */
        factory(window.jQuery);
    }

}(function ($) {

    $.fn.ddslick = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === "object" || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error("Method " + method + " does not exists.");
        }
    };

    var methods = {};
    var settingsMap = {};
    var defaults = {
        data: [],
        keepJSONItemsOnTop: false,
        animationTime: 50,
        width: 260,
        height: null,
        background: "#eee",
        selectText: "",
        defaultSelectedIndex: null,
        truncateDescription: true,
        imagePosition: "left",
        showSelectedHTML: true,
        clickOffToClose: true,
        embedCSS: true,
        onSelected: function() { }
    };

    var closeListenerInitialized = false;
    var ddSelectHtml = "<div class='dd-select'><input class='dd-selected-value' type='hidden' /><a class='dd-selected'></a><span class='dd-pointer dd-pointer-down'></span></div>";
    var ddOptionsHtml = "<ul class='dd-options'></ul>";

    //CSS for ddSlick
    var ddslickCSS = "<style id='css-ddslick' type='text/css'>" +
        ".dd-select{ border-radius:2px; border:solid 1px #ccc; position:relative; cursor:pointer;}" +
        ".dd-desc { color:#aaa; display:block; overflow: hidden; font-weight:normal; line-height: 1.4em; }" +
        ".dd-selected{ overflow:hidden; display:block; padding:10px;  font-weight:bold;}" +
        ".dd-pointer{ width:0; height:0; position:absolute; right:10px; top:50%; margin-top:-3px;}" +
        ".dd-pointer-down{ border:solid 5px transparent; border-top:solid 5px #000; }" +
        ".dd-pointer-up{border:solid 5px transparent !important; border-bottom:solid 5px #000 !important; margin-top:-8px;}" +
        ".dd-options{ border:solid 1px #ccc; border-top:none; list-style:none; box-shadow:0px 1px 5px #ddd; display:none; position:absolute; z-index:2000; margin:0; padding:0;background:#fff; overflow:auto;}" +
        ".dd-option, .dd-title{ padding:10px; display:block; border-bottom:solid 1px #ddd; overflow:hidden; text-decoration:none; color:#333; cursor:pointer;-webkit-transition: all 0.25s ease-in-out; -moz-transition: all 0.25s ease-in-out;-o-transition: all 0.25s ease-in-out;-ms-transition: all 0.25s ease-in-out; }" +
        ".dd-options > li:last-child > .dd-option{ border-bottom:none;}" +
        ".dd-option:hover{ background:#f3f3f3; color:#000;}" +
        ".dd-selected-description-truncated { text-overflow: ellipsis; white-space:nowrap; }" +
        ".dd-option-selected { background:#f6f6f6; }" +
        ".dd-option { padding-left:30px} .dd-option-image, .dd-selected-image { vertical-align:middle; float:left; margin-right:5px; max-width:64px;}" +
        ".dd-image-right { float:right; margin-right:15px; margin-left:5px;}" +
        ".dd-container{ position:relative;} .dd-selected-text { font-weight:bold}</style>";

    //Public methods
    methods.init = function (userOptions) {
        //Preserve the original defaults by passing an empty object as the target
        //The object is used to get global flags like embedCSS.
        var options = $.extend({}, defaults, userOptions);

        //CSS styles are only added once.
        if ($("#css-ddslick").length <= 0 && options.embedCSS) {
            $(ddslickCSS).appendTo("head");
        }

        //Apply on all selected elements
        return this.each(function() {
            //Preserve the original defaults by passing an empty object as the target
            //The object is used to save drop-down"s corresponding settings and data.
            var options = $.extend({}, defaults, userOptions);

            var obj = $(this),
                data = obj.data("ddslick");
            //If the plugin has not been initialized yet
            if (!data) {

                var ddSelect = [];

                //Get data from HTML select options
                obj.find("option").each(function() {
                    var $this = $(this), thisData = $this.data();
                    ddSelect.push({
                        text: $.trim($this.text()),
                        value: $this.val(),
                        title: thisData.title,
                        selected: $this.is(":selected"),
                        description: thisData.description,
                        imageSrc: thisData.imagesrc //keep it lowercase for HTML5 data-attributes
                    });
                });

                //Update Plugin data merging both HTML select data and JSON data for the dropdown
                if (options.keepJSONItemsOnTop)
                    $.merge(options.data, ddSelect);
                else options.data = $.merge(ddSelect, options.data);

                //Replace HTML select with empty placeholder, keep the original
                var original = obj, placeholder = $("<div>").attr("id", obj.attr("id"));
                obj.replaceWith(placeholder);
                obj = placeholder;

                // Save options
                var settingsId = "ID_" + (new Date()).getTime();
                $(obj).attr("data-settings-id", settingsId);
                settingsMap[settingsId] = {};
                $.extend(settingsMap[settingsId], options);

                //Add classes and append ddSelectHtml & ddOptionsHtml to the container
                obj.addClass("dd-container").append(ddSelectHtml).append(ddOptionsHtml);

                // Inherit name attribute from original element
                obj.find("input.dd-selected-value")
                    .attr("id", $(original).attr("id"))
                    .attr("name", $(original).attr("name"));

                //Get newly created ddOptions and ddSelect to manipulate
                var ddOptions = obj.find(".dd-options");
                ddSelect = obj.find(".dd-select");

                //Set widths
                ddOptions.css({ width: options.width });
                ddSelect.css({ width: options.width, background: options.background });
                obj.css({ width: options.width });

                //Set height
                if (options.height !== null)
                    ddOptions.css({ height: options.height, overflow: "auto" });

                //Add ddOptions to the container. Replace with template engine later.
                $.each(options.data, function (index, item) {
                    if (item.selected) options.defaultSelectedIndex = index;
                    
                    if (item.title) ddOptions.append('<li class="dd-title"><strong>'+item.text+'</strong></li>'); else {
                    
                    var ddList = $("<li>").append($("<a>").addClass("dd-option"));
                    var ddOption = ddList.find("a");
                    if(item.value) ddOption.append($("<input>").addClass("dd-option-value").attr("type", "hidden").val(item.value));
                    if(item.imageSrc) ddOption.append($("<img>").attr("src", item.imageSrc).addClass("dd-option-image" + (options.imagePosition === "right" ? " dd-image-right" : "")));
                    if(item.text) ddOption.append($("<label>").addClass("dd-option-text").text(item.text));
                    if(item.description) ddOption.append($("<small>").addClass("dd-option-description dd-desc").text(item.description));
                    ddOptions.append(ddList);
                    
                   }
                });

                //Save plugin data.
                var pluginData = {
                    settings: options,
                    original: original,
                    selectedIndex: -1,
                    selectedItem: null,
                    selectedData: null
                };

                obj.data("ddslick", pluginData);

                //Check if needs to show the select text, otherwise show selected or default selection
                if (options.selectText.length > 0 && options.defaultSelectedIndex === null) {
                    obj.find(".dd-selected").html(options.selectText);
                }
                else {
                    var index = (options.defaultSelectedIndex != null && options.defaultSelectedIndex >= 0 && options.defaultSelectedIndex < options.data.length)
                                ? options.defaultSelectedIndex
                                : 0;
                    selectIndex(obj, index, false);
                }

                //EVENTS
                //Displaying options
                obj.find(".dd-select").on("click.ddslick", function() {
                    open(obj);
                });

                //Selecting an option
                obj.find(".dd-option").on("click.ddslick", function() {
                    selectIndex(obj, $(this).closest("li").index(), true);
                });

                //Click anywhere to close
                if (options.clickOffToClose) {
                    ddOptions.addClass("dd-click-off-close");
                    obj.on("click.ddslick", function (e) { e.stopPropagation(); });
                    // Close listener needs to be added only once
                    if(!closeListenerInitialized) {
                        closeListenerInitialized = true;
                        $("body").on("click", function () {
                            $(".dd-open").removeClass("dd-open");
                            $(".dd-click-off-close").slideUp(options.animationTime).siblings(".dd-select").find(".dd-pointer").removeClass("dd-pointer-up");
                        });
                    }
                }
            }
        });
    };

    //Public method to select an option by its index
    methods.select = function (options) {
        return this.each(function() {
            if (options.index !== undefined)
                selectIndex($(this), options.index);
            if (options.value !== undefined)
                selectValue($(this), options.value);
            if (options.id !== undefined)
                selectValue($(this), options.id);
        });
    };

    //Public method to open drop down
    methods.open = function() {
        return this.each(function() {
            var $this = $(this),
                pluginData = $this.data("ddslick");

            //Check if plugin is initialized
            if (pluginData)
                open($this);
        });
    };

    //Public method to close drop down
    methods.close = function() {
        return this.each(function() {
            var $this = $(this),
                pluginData = $this.data("ddslick");

            //Check if plugin is initialized
            if (pluginData)
                close($this);
        });
    };

    //Public method to destroy. Unbind all events and restore the original Html select/options
    methods.destroy = function() {
        return this.each(function() {
            var $this = $(this),
                pluginData = $this.data("ddslick");

            //Check if already destroyed
            if (pluginData) {
                var originalElement = pluginData.original;
                $this.removeData("ddslick").unbind(".ddslick").replaceWith(originalElement);
            }
        });
    };

    //Private: Select by value
    function selectValue(obj, value) {
        var index = obj.find(".dd-option-value[value= '" + value + "']").parents("li").prevAll().length;
        selectIndex(obj, index);
    }

    //Private: Select index
    function selectIndex(obj, index, callbackOnSelection) {

        //Get plugin data
        var pluginData = obj.data("ddslick");

        //Get required elements
        var ddSelected = obj.find(".dd-selected"),
            ddSelectedValue = ddSelected.siblings(".dd-selected-value"),
            selectedOption = obj.find(".dd-option").eq(index),
            selectedLiItem = selectedOption.closest("li"),
            settings = pluginData.settings,
            selectedData = pluginData.settings.data[index];

        //Highlight selected option
        obj.find(".dd-option").removeClass("dd-option-selected");
        selectedOption.addClass("dd-option-selected");

        //Update or Set plugin data with new selection
        pluginData.selectedIndex = index;
        pluginData.selectedItem = selectedLiItem;
        pluginData.selectedData = selectedData;

        //If set to display to full html, add html
        if (settings.showSelectedHTML) {
            var ddSelectedData = $("<div>");
            if(selectedData.imageSrc) ddSelectedData.append($("<img>").addClass("dd-selected-image" + (settings.imagePosition === "right" ? " dd-image-right" : "")).attr("src", selectedData.imageSrc));
            if(selectedData.text) ddSelectedData.append($("<label>").addClass("dd-selected-text").text(selectedData.text));
            if(selectedData.description) ddSelectedData.append($("<small>").addClass("dd-selected-description dd-desc" + (settings.truncateDescription ? " dd-selected-description-truncated" : "")).text(selectedData.description));
            ddSelected.html(ddSelectedData.html());
        }
        //Else only display text as selection
        else ddSelected.html(selectedData.text);

        //Updating selected option value
        ddSelectedValue.val(selectedData.value);

        //BONUS! Update the original element attribute with the new selection
        pluginData.original.val(selectedData.value);
        obj.data("ddslick", pluginData);

        //Close options on selection
        close(obj);

        //Adjust appearence for selected option
        adjustSelectedHeight(obj);

        //Callback function on selection
        if (callbackOnSelection && typeof settings.onSelected == "function") {
            settings.onSelected.call(this, pluginData);
        }
    }

    //Private: Close the drop down options
    function open(obj) {

        var $this = obj.find(".dd-select"),
            ddOptions = $this.siblings(".dd-options"),
            ddPointer = $this.find(".dd-pointer"),
            wasOpen = ddOptions.is(":visible"),
            settings = settingsMap[obj.attr("data-settings-id")];

        //Close all open options (multiple plugins) on the page
        $(".dd-click-off-close").not(ddOptions).slideUp(settings.animationTime);
        $(".dd-pointer").removeClass("dd-pointer-up");
        $this.removeClass("dd-open");

        if (wasOpen) {
            ddOptions.slideUp(settings.animationTime);
            ddPointer.removeClass("dd-pointer-up");
            $this.removeClass("dd-open");
        }
        else {
            $this.addClass("dd-open");
            ddOptions.slideDown(settings.animationTime);
            ddPointer.addClass("dd-pointer-up");
        }

        //Fix text height (i.e. display title in center), if there is no description
        adjustOptionsHeight(obj);
    }

    //Private: Close the drop down options
    function close(obj) {
        //Close drop down and adjust pointer direction
        var settings = settingsMap[obj.attr("data-settings-id")];
        obj.find(".dd-select").removeClass("dd-open");
        obj.find(".dd-options").slideUp(settings.animationTime);
        obj.find(".dd-pointer").removeClass("dd-pointer-up").removeClass("dd-pointer-up");
    }

    //Private: Adjust appearence for selected option (move title to middle), when no desripction
    function adjustSelectedHeight(obj) { return;

        //Get height of dd-selected
        var lSHeight = obj.find(".dd-select").css("height");

        //Check if there is selected description
        var descriptionSelected = obj.find(".dd-selected-description");
        var imgSelected = obj.find(".dd-selected-image");
        if (descriptionSelected.length <= 0 && imgSelected.length > 0) {
            obj.find(".dd-selected-text").css("lineHeight", lSHeight);
        }
    }

    //Private: Adjust appearence for drop down options (move title to middle), when no desripction
    function adjustOptionsHeight(obj) {
        obj.find(".dd-option").each(function() {
            var $this = $(this);
            var lOHeight = $this.css("height");
            var descriptionOption = $this.find(".dd-option-description");
            var imgOption = obj.find(".dd-option-image");
            if (descriptionOption.length <= 0 && imgOption.length > 0) {
                $this.find(".dd-option-text").css("lineHeight", lOHeight);
            }
        });
    }

}));