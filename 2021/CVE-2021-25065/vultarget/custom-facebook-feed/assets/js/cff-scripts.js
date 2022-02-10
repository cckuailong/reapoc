var cff_js_exists = (typeof cff_js_exists !== 'undefined') ? true : false;
if(!cff_js_exists){

	//Only load the Masonry code if there's a masonry feed on the page
	if( jQuery('#cff.cff-masonry-js').length ){

		//Masonry
		!function(a){function b(){}
			function c(a){function c(b){b.prototype.option||(b.prototype.option=function(b){a.isPlainObject(b)&&(this.options=a.extend(!0,this.options,b))})}
				function e(b,c){a.fn[b]=function(e){if("string"==typeof e){for(var g=d.call(arguments,1),h=0,i=this.length;i>h;h++){var j=this[h],k=a.data(j,b);if(k)if(a.isFunction(k[e])&&"_"!==e.charAt(0)){var l=k[e].apply(k,g);if(void 0!==l)return l}else f("no such method '"+e+"' for "+b+" instance");else f("cannot call methods on "+b+" prior to initialization; attempted to call '"+e+"'")}
					return this}
					return this.each(function(){var d=a.data(this,b);d?(d.option(e),d._init()):(d=new c(this,e),a.data(this,b,d))})}}
				if(a){var f="undefined"==typeof console?b:function(a){console.error(a)};return a.bridget=function(a,b){c(b),e(a,b)},a.bridget}}
			var d=Array.prototype.slice;"function"==typeof define&&define.amd?define("jquery-bridget/jquery.bridget",["jquery"],c):c("object"==typeof exports?require("jquery"):a.jQuery)}(window),function(a){function b(b){var c=a.event;return c.target=c.target||c.srcElement||b,c}
			var c=document.documentElement,d=function(){};c.addEventListener?d=function(a,b,c){a.addEventListener(b,c,!1)}:c.attachEvent&&(d=function(a,c,d){a[c+d]=d.handleEvent?function(){var c=b(a);d.handleEvent.call(d,c)}:function(){var c=b(a);d.call(a,c)},a.attachEvent("on"+c,a[c+d])});var e=function(){};c.removeEventListener?e=function(a,b,c){a.removeEventListener(b,c,!1)}:c.detachEvent&&(e=function(a,b,c){a.detachEvent("on"+b,a[b+c]);try{delete a[b+c]}catch(d){a[b+c]=void 0}});var f={bind:d,unbind:e};"function"==typeof define&&define.amd?define("eventie/eventie",f):"object"==typeof exports?module.exports=f:a.eventie=f}(window),function(){function a(){}
			function b(a,b){for(var c=a.length;c--;)if(a[c].listener===b)return c;return-1}
			function c(a){return function(){return this[a].apply(this,arguments)}}
			var d=a.prototype,e=this,f=e.EventEmitter;d.getListeners=function(a){var b,c,d=this._getEvents();if(a instanceof RegExp){b={};for(c in d)d.hasOwnProperty(c)&&a.test(c)&&(b[c]=d[c])}else b=d[a]||(d[a]=[]);return b},d.flattenListeners=function(a){var b,c=[];for(b=0;b<a.length;b+=1)c.push(a[b].listener);return c},d.getListenersAsObject=function(a){var b,c=this.getListeners(a);return c instanceof Array&&(b={},b[a]=c),b||c},d.addListener=function(a,c){var d,e=this.getListenersAsObject(a),f="object"==typeof c;for(d in e)e.hasOwnProperty(d)&&-1===b(e[d],c)&&e[d].push(f?c:{listener:c,once:!1});return this},d.on=c("addListener"),d.addOnceListener=function(a,b){return this.addListener(a,{listener:b,once:!0})},d.once=c("addOnceListener"),d.defineEvent=function(a){return this.getListeners(a),this},d.defineEvents=function(a){for(var b=0;b<a.length;b+=1)this.defineEvent(a[b]);return this},d.removeListener=function(a,c){var d,e,f=this.getListenersAsObject(a);for(e in f)f.hasOwnProperty(e)&&(d=b(f[e],c),-1!==d&&f[e].splice(d,1));return this},d.off=c("removeListener"),d.addListeners=function(a,b){return this.manipulateListeners(!1,a,b)},d.removeListeners=function(a,b){return this.manipulateListeners(!0,a,b)},d.manipulateListeners=function(a,b,c){var d,e,f=a?this.removeListener:this.addListener,g=a?this.removeListeners:this.addListeners;if("object"!=typeof b||b instanceof RegExp)for(d=c.length;d--;)f.call(this,b,c[d]);else for(d in b)b.hasOwnProperty(d)&&(e=b[d])&&("function"==typeof e?f.call(this,d,e):g.call(this,d,e));return this},d.removeEvent=function(a){var b,c=typeof a,d=this._getEvents();if("string"===c)delete d[a];else if(a instanceof RegExp)for(b in d)d.hasOwnProperty(b)&&a.test(b)&&delete d[b];else delete this._events;return this},d.removeAllListeners=c("removeEvent"),d.emitEvent=function(a,b){var c,d,e,f,g=this.getListenersAsObject(a);for(e in g)if(g.hasOwnProperty(e))for(d=g[e].length;d--;)c=g[e][d],c.once===!0&&this.removeListener(a,c.listener),f=c.listener.apply(this,b||[]),f===this._getOnceReturnValue()&&this.removeListener(a,c.listener);return this},d.trigger=c("emitEvent"),d.emit=function(a){var b=Array.prototype.slice.call(arguments,1);return this.emitEvent(a,b)},d.setOnceReturnValue=function(a){return this._onceReturnValue=a,this},d._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},d._getEvents=function(){return this._events||(this._events={})},a.noConflict=function(){return e.EventEmitter=f,a},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return a}):"object"==typeof module&&module.exports?module.exports=a:e.EventEmitter=a}.call(this),function(a){function b(a){if(a){if("string"==typeof d[a])return a;a=a.charAt(0).toUpperCase()+a.slice(1);for(var b,e=0,f=c.length;f>e;e++)if(b=c[e]+a,"string"==typeof d[b])return b}}
			var c="Webkit Moz ms Ms O".split(" "),d=document.documentElement.style;"function"==typeof define&&define.amd?define("get-style-property/get-style-property",[],function(){return b}):"object"==typeof exports?module.exports=b:a.getStyleProperty=b}(window),function(a){function b(a){var b=parseFloat(a),c=-1===a.indexOf("%")&&!isNaN(b);return c&&b}
			function c(){}
			function d(){for(var a={width:0,height:0,innerWidth:0,innerHeight:0,outerWidth:0,outerHeight:0},b=0,c=g.length;c>b;b++){var d=g[b];a[d]=0}
				return a}
			function e(c){function e(){if(!m){m=!0;var d=a.getComputedStyle;if(j=function(){var a=d?function(a){return d(a,null)}:function(a){return a.currentStyle};return function(b){var c=a(b);return c||f("Style returned "+c+". Are you running this code in a hidden iframe on Firefox? See http://bit.ly/getsizebug1"),c}}(),k=c("boxSizing")){var e=document.createElement("div");e.style.width="200px",e.style.padding="1px 2px 3px 4px",e.style.borderStyle="solid",e.style.borderWidth="1px 2px 3px 4px",e.style[k]="border-box";var g=document.body||document.documentElement;g.appendChild(e);var h=j(e);l=200===b(h.width),g.removeChild(e)}}}
				function h(a){if(e(),"string"==typeof a&&(a=document.querySelector(a)),a&&"object"==typeof a&&a.nodeType){var c=j(a);if("none"===c.display)return d();var f={};f.width=a.offsetWidth,f.height=a.offsetHeight;for(var h=f.isBorderBox=!(!k||!c[k]||"border-box"!==c[k]),m=0,n=g.length;n>m;m++){var o=g[m],p=c[o];p=i(a,p);var q=parseFloat(p);f[o]=isNaN(q)?0:q}
					var r=f.paddingLeft+f.paddingRight,s=f.paddingTop+f.paddingBottom,t=f.marginLeft+f.marginRight,u=f.marginTop+f.marginBottom,v=f.borderLeftWidth+f.borderRightWidth,w=f.borderTopWidth+f.borderBottomWidth,x=h&&l,y=b(c.width);y!==!1&&(f.width=y+(x?0:r+v));var z=b(c.height);return z!==!1&&(f.height=z+(x?0:s+w)),f.innerWidth=f.width-(r+v),f.innerHeight=f.height-(s+w),f.outerWidth=f.width+t,f.outerHeight=f.height+u,f}}
				function i(b,c){if(a.getComputedStyle||-1===c.indexOf("%"))return c;var d=b.style,e=d.left,f=b.runtimeStyle,g=f&&f.left;return g&&(f.left=b.currentStyle.left),d.left=c,c=d.pixelLeft,d.left=e,g&&(f.left=g),c}
				var j,k,l,m=!1;return h}
			var f="undefined"==typeof console?c:function(a){console.error(a)},g=["paddingLeft","paddingRight","paddingTop","paddingBottom","marginLeft","marginRight","marginTop","marginBottom","borderLeftWidth","borderRightWidth","borderTopWidth","borderBottomWidth"];"function"==typeof define&&define.amd?define("get-size/get-size",["get-style-property/get-style-property"],e):"object"==typeof exports?module.exports=e(require("desandro-get-style-property")):a.getSize=e(a.getStyleProperty)}(window),function(a){function b(a){"function"==typeof a&&(b.isReady?a():g.push(a))}
			function c(a){var c="readystatechange"===a.type&&"complete"!==f.readyState;b.isReady||c||d()}
			function d(){b.isReady=!0;for(var a=0,c=g.length;c>a;a++){var d=g[a];d()}}
			function e(e){return"complete"===f.readyState?d():(e.bind(f,"DOMContentLoaded",c),e.bind(f,"readystatechange",c),e.bind(a,"load",c)),b}
			var f=a.document,g=[];b.isReady=!1,"function"==typeof define&&define.amd?define("doc-ready/doc-ready",["eventie/eventie"],e):"object"==typeof exports?module.exports=e(require("eventie")):a.docReady=e(a.eventie)}(window),function(a){function b(a,b){return a[g](b)}
			function c(a){if(!a.parentNode){var b=document.createDocumentFragment();b.appendChild(a)}}
			function d(a,b){c(a);for(var d=a.parentNode.querySelectorAll(b),e=0,f=d.length;f>e;e++)if(d[e]===a)return!0;return!1}
			function e(a,d){return c(a),b(a,d)}
			var f,g=function(){if(a.matches)return"matches";if(a.matchesSelector)return"matchesSelector";for(var b=["webkit","moz","ms","o"],c=0,d=b.length;d>c;c++){var e=b[c],f=e+"MatchesSelector";if(a[f])return f}}();if(g){var h=document.createElement("div"),i=b(h,"div");f=i?b:e}else f=d;"function"==typeof define&&define.amd?define("matches-selector/matches-selector",[],function(){return f}):"object"==typeof exports?module.exports=f:window.matchesSelector=f}(Element.prototype),function(a,b){"function"==typeof define&&define.amd?define("fizzy-ui-utils/utils",["doc-ready/doc-ready","matches-selector/matches-selector"],function(c,d){return b(a,c,d)}):"object"==typeof exports?module.exports=b(a,require("doc-ready"),require("desandro-matches-selector")):a.fizzyUIUtils=b(a,a.docReady,a.matchesSelector)}(window,function(a,b,c){var d={};d.extend=function(a,b){for(var c in b)a[c]=b[c];return a},d.modulo=function(a,b){return(a%b+b)%b};var e=Object.prototype.toString;d.isArray=function(a){return"[object Array]"==e.call(a)},d.makeArray=function(a){var b=[];if(d.isArray(a))b=a;else if(a&&"number"==typeof a.length)for(var c=0,e=a.length;e>c;c++)b.push(a[c]);else b.push(a);return b},d.indexOf=Array.prototype.indexOf?function(a,b){return a.indexOf(b)}:function(a,b){for(var c=0,d=a.length;d>c;c++)if(a[c]===b)return c;return-1},d.removeFrom=function(a,b){var c=d.indexOf(a,b);-1!=c&&a.splice(c,1)},d.isElement="function"==typeof HTMLElement||"object"==typeof HTMLElement?function(a){return a instanceof HTMLElement}:function(a){return a&&"object"==typeof a&&1==a.nodeType&&"string"==typeof a.nodeName},d.setText=function(){function a(a,c){b=b||(void 0!==document.documentElement.textContent?"textContent":"innerText"),a[b]=c}
			var b;return a}(),d.getParent=function(a,b){for(;a!=document.body;)if(a=a.parentNode,c(a,b))return a},d.getQueryElement=function(a){return"string"==typeof a?document.querySelector(a):a},d.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},d.filterFindElements=function(a,b){a=d.makeArray(a);for(var e=[],f=0,g=a.length;g>f;f++){var h=a[f];if(d.isElement(h))if(b){c(h,b)&&e.push(h);for(var i=h.querySelectorAll(b),j=0,k=i.length;k>j;j++)e.push(i[j])}else e.push(h)}
			return e},d.debounceMethod=function(a,b,c){var d=a.prototype[b],e=b+"Timeout";a.prototype[b]=function(){var a=this[e];a&&clearTimeout(a);var b=arguments,f=this;this[e]=setTimeout(function(){d.apply(f,b),delete f[e]},c||100)}},d.toDashed=function(a){return a.replace(/(.)([A-Z])/g,function(a,b,c){return b+"-"+c}).toLowerCase()};var f=a.console;return d.htmlInit=function(c,e){b(function(){for(var b=d.toDashed(e),g=document.querySelectorAll(".js-"+b),h="data-"+b+"-options",i=0,j=g.length;j>i;i++){var k,l=g[i],m=l.getAttribute(h);try{k=m&&JSON.parse(m)}catch(n){f&&f.error("Error parsing "+h+" on "+l.nodeName.toLowerCase()+(l.id?"#"+l.id:"")+": "+n);continue}
			var o=new c(l,k),p=a.jQuery;p&&p.data(l,e,o)}})},d}),function(a,b){"function"==typeof define&&define.amd?define("outlayer/item",["eventEmitter/EventEmitter","get-size/get-size","get-style-property/get-style-property","fizzy-ui-utils/utils"],function(c,d,e,f){return b(a,c,d,e,f)}):"object"==typeof exports?module.exports=b(a,require("wolfy87-eventemitter"),require("get-size"),require("desandro-get-style-property"),require("fizzy-ui-utils")):(a.Outlayer={},a.Outlayer.Item=b(a,a.EventEmitter,a.getSize,a.getStyleProperty,a.fizzyUIUtils))}(window,function(a,b,c,d,e){function f(a){for(var b in a)return!1;return b=null,!0}
			function g(a,b){a&&(this.element=a,this.layout=b,this.position={x:0,y:0},this._create())}
			var h=a.getComputedStyle,i=h?function(a){return h(a,null)}:function(a){return a.currentStyle},j=d("transition"),k=d("transform"),l=j&&k,m=!!d("perspective"),n={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"otransitionend",transition:"transitionend"}[j],o=["transform","transition","transitionDuration","transitionProperty"],p=function(){for(var a={},b=0,c=o.length;c>b;b++){var e=o[b],f=d(e);f&&f!==e&&(a[e]=f)}
				return a}();e.extend(g.prototype,b.prototype),g.prototype._create=function(){this._transn={ingProperties:{},clean:{},onEnd:{}},this.css({position:"absolute"})},g.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},g.prototype.getSize=function(){this.size=c(this.element)},g.prototype.css=function(a){var b=this.element.style;for(var c in a){var d=p[c]||c;b[d]=a[c]}},g.prototype.getPosition=function(){var a=i(this.element),b=this.layout.options,c=b.isOriginLeft,d=b.isOriginTop,e=parseInt(a[c?"left":"right"],10),f=parseInt(a[d?"top":"bottom"],10);e=isNaN(e)?0:e,f=isNaN(f)?0:f;var g=this.layout.size;e-=c?g.paddingLeft:g.paddingRight,f-=d?g.paddingTop:g.paddingBottom,this.position.x=e,this.position.y=f},g.prototype.layoutPosition=function(){var a=this.layout.size,b=this.layout.options,c={},d=b.isOriginLeft?"paddingLeft":"paddingRight",e=b.isOriginLeft?"left":"right",f=b.isOriginLeft?"right":"left",g=this.position.x+a[d];g=b.percentPosition&&!b.isHorizontal?g/a.width*100+"%":g+"px",c[e]=g,c[f]="";var h=b.isOriginTop?"paddingTop":"paddingBottom",i=b.isOriginTop?"top":"bottom",j=b.isOriginTop?"bottom":"top",k=this.position.y+a[h];k=b.percentPosition&&b.isHorizontal?k/a.height*100+"%":k+"px",c[i]=k,c[j]="",this.css(c),this.emitEvent("layout",[this])};var q=m?function(a,b){return"translate3d("+a+"px, "+b+"px, 0)"}:function(a,b){return"translate("+a+"px, "+b+"px)"};g.prototype._transitionTo=function(a,b){this.getPosition();var c=this.position.x,d=this.position.y,e=parseInt(a,10),f=parseInt(b,10),g=e===this.position.x&&f===this.position.y;if(this.setPosition(a,b),g&&!this.isTransitioning)return void this.layoutPosition();var h=a-c,i=b-d,j={},k=this.layout.options;h=k.isOriginLeft?h:-h,i=k.isOriginTop?i:-i,j.transform=q(h,i),this.transition({to:j,onTransitionEnd:{transform:this.layoutPosition},isCleaning:!0})},g.prototype.goTo=function(a,b){this.setPosition(a,b),this.layoutPosition()},g.prototype.moveTo=l?g.prototype._transitionTo:g.prototype.goTo,g.prototype.setPosition=function(a,b){this.position.x=parseInt(a,10),this.position.y=parseInt(b,10)},g.prototype._nonTransition=function(a){this.css(a.to),a.isCleaning&&this._removeStyles(a.to);for(var b in a.onTransitionEnd)a.onTransitionEnd[b].call(this)},g.prototype._transition=function(a){if(!parseFloat(this.layout.options.transitionDuration))return void this._nonTransition(a);var b=this._transn;for(var c in a.onTransitionEnd)b.onEnd[c]=a.onTransitionEnd[c];for(c in a.to)b.ingProperties[c]=!0,a.isCleaning&&(b.clean[c]=!0);if(a.from){this.css(a.from);var d=this.element.offsetHeight;d=null}
				this.enableTransition(a.to),this.css(a.to),this.isTransitioning=!0};var r=k&&e.toDashed(k)+",opacity";g.prototype.enableTransition=function(){this.isTransitioning||(this.css({transitionProperty:r,transitionDuration:this.layout.options.transitionDuration}),this.element.addEventListener(n,this,!1))},g.prototype.transition=g.prototype[j?"_transition":"_nonTransition"],g.prototype.onwebkitTransitionEnd=function(a){this.ontransitionend(a)},g.prototype.onotransitionend=function(a){this.ontransitionend(a)};var s={"-webkit-transform":"transform","-moz-transform":"transform","-o-transform":"transform"};g.prototype.ontransitionend=function(a){if(a.target===this.element){var b=this._transn,c=s[a.propertyName]||a.propertyName;if(delete b.ingProperties[c],f(b.ingProperties)&&this.disableTransition(),c in b.clean&&(this.element.style[a.propertyName]="",delete b.clean[c]),c in b.onEnd){var d=b.onEnd[c];d.call(this),delete b.onEnd[c]}
				this.emitEvent("transitionEnd",[this])}},g.prototype.disableTransition=function(){this.removeTransitionStyles(),this.element.removeEventListener(n,this,!1),this.isTransitioning=!1},g.prototype._removeStyles=function(a){var b={};for(var c in a)b[c]="";this.css(b)};var t={transitionProperty:"",transitionDuration:""};return g.prototype.removeTransitionStyles=function(){this.css(t)},g.prototype.removeElem=function(){this.element.parentNode.removeChild(this.element),this.css({display:""}),this.emitEvent("remove",[this])},g.prototype.remove=function(){if(!j||!parseFloat(this.layout.options.transitionDuration))return void this.removeElem();var a=this;this.once("transitionEnd",function(){a.removeElem()}),this.hide()},g.prototype.reveal=function(){delete this.isHidden,this.css({display:""});var a=this.layout.options,b={},c=this.getHideRevealTransitionEndProperty("visibleStyle");b[c]=this.onRevealTransitionEnd,this.transition({from:a.hiddenStyle,to:a.visibleStyle,isCleaning:!0,onTransitionEnd:b})},g.prototype.onRevealTransitionEnd=function(){this.isHidden||this.emitEvent("reveal")},g.prototype.getHideRevealTransitionEndProperty=function(a){var b=this.layout.options[a];if(b.opacity)return"opacity";for(var c in b)return c},g.prototype.hide=function(){this.isHidden=!0,this.css({display:""});var a=this.layout.options,b={},c=this.getHideRevealTransitionEndProperty("hiddenStyle");b[c]=this.onHideTransitionEnd,this.transition({from:a.visibleStyle,to:a.hiddenStyle,isCleaning:!0,onTransitionEnd:b})},g.prototype.onHideTransitionEnd=function(){this.isHidden&&(this.css({display:"none"}),this.emitEvent("hide"))},g.prototype.destroy=function(){this.css({position:"",left:"",right:"",top:"",bottom:"",transition:"",transform:""})},g}),function(a,b){"function"==typeof define&&define.amd?define("outlayer/outlayer",["eventie/eventie","eventEmitter/EventEmitter","get-size/get-size","fizzy-ui-utils/utils","./item"],function(c,d,e,f,g){return b(a,c,d,e,f,g)}):"object"==typeof exports?module.exports=b(a,require("eventie"),require("wolfy87-eventemitter"),require("get-size"),require("fizzy-ui-utils"),require("./item")):a.Outlayer=b(a,a.eventie,a.EventEmitter,a.getSize,a.fizzyUIUtils,a.Outlayer.Item)}(window,function(a,b,c,d,e,f){function g(a,b){var c=e.getQueryElement(a);if(!c)return void(h&&h.error("Bad element for "+this.constructor.namespace+": "+(c||a)));this.element=c,i&&(this.$element=i(this.element)),this.options=e.extend({},this.constructor.defaults),this.option(b);var d=++k;this.element.outlayerGUID=d,l[d]=this,this._create(),this.options.isInitLayout&&this.layout()}
			var h=a.console,i=a.jQuery,j=function(){},k=0,l={};return g.namespace="outlayer",g.Item=f,g.defaults={containerStyle:{position:"relative"},isInitLayout:!0,isOriginLeft:!0,isOriginTop:!0,isResizeBound:!0,isResizingContainer:!0,transitionDuration:"0.4s",hiddenStyle:{opacity:0,transform:"scale(0.001)"},visibleStyle:{opacity:1,transform:"scale(1)"}},e.extend(g.prototype,c.prototype),g.prototype.option=function(a){e.extend(this.options,a)},g.prototype._create=function(){this.reloadItems(),this.stamps=[],this.stamp(this.options.stamp),e.extend(this.element.style,this.options.containerStyle),this.options.isResizeBound&&this.bindResize()},g.prototype.reloadItems=function(){this.items=this._itemize(this.element.children)},g.prototype._itemize=function(a){for(var b=this._filterFindItemElements(a),c=this.constructor.Item,d=[],e=0,f=b.length;f>e;e++){var g=b[e],h=new c(g,this);d.push(h)}
				return d},g.prototype._filterFindItemElements=function(a){return e.filterFindElements(a,this.options.itemSelector)},g.prototype.getItemElements=function(){for(var a=[],b=0,c=this.items.length;c>b;b++)a.push(this.items[b].element);return a},g.prototype.layout=function(){this._resetLayout(),this._manageStamps();var a=void 0!==this.options.isLayoutInstant?this.options.isLayoutInstant:!this._isLayoutInited;this.layoutItems(this.items,a),this._isLayoutInited=!0},g.prototype._init=g.prototype.layout,g.prototype._resetLayout=function(){this.getSize()},g.prototype.getSize=function(){this.size=d(this.element)},g.prototype._getMeasurement=function(a,b){var c,f=this.options[a];f?("string"==typeof f?c=this.element.querySelector(f):e.isElement(f)&&(c=f),this[a]=c?d(c)[b]:f):this[a]=0},g.prototype.layoutItems=function(a,b){a=this._getItemsForLayout(a),this._layoutItems(a,b),this._postLayout()},g.prototype._getItemsForLayout=function(a){for(var b=[],c=0,d=a.length;d>c;c++){var e=a[c];e.isIgnored||b.push(e)}
				return b},g.prototype._layoutItems=function(a,b){if(this._emitCompleteOnItems("layout",a),a&&a.length){for(var c=[],d=0,e=a.length;e>d;d++){var f=a[d],g=this._getItemLayoutPosition(f);g.item=f,g.isInstant=b||f.isLayoutInstant,c.push(g)}
				this._processLayoutQueue(c)}},g.prototype._getItemLayoutPosition=function(){return{x:0,y:0}},g.prototype._processLayoutQueue=function(a){for(var b=0,c=a.length;c>b;b++){var d=a[b];this._positionItem(d.item,d.x,d.y,d.isInstant)}},g.prototype._positionItem=function(a,b,c,d){d?a.goTo(b,c):a.moveTo(b,c)},g.prototype._postLayout=function(){this.resizeContainer()},g.prototype.resizeContainer=function(){if(this.options.isResizingContainer){var a=this._getContainerSize();a&&(this._setContainerMeasure(a.width,!0),this._setContainerMeasure(a.height,!1))}},g.prototype._getContainerSize=j,g.prototype._setContainerMeasure=function(a,b){if(void 0!==a){var c=this.size;c.isBorderBox&&(a+=b?c.paddingLeft+c.paddingRight+c.borderLeftWidth+c.borderRightWidth:c.paddingBottom+c.paddingTop+c.borderTopWidth+c.borderBottomWidth),a=Math.max(a,0),this.element.style[b?"width":"height"]=a+"px"}},g.prototype._emitCompleteOnItems=function(a,b){function c(){e.emitEvent(a+"Complete",[b])}
				function d(){g++,g===f&&c()}
				var e=this,f=b.length;if(!b||!f)return void c();for(var g=0,h=0,i=b.length;i>h;h++){var j=b[h];j.once(a,d)}},g.prototype.ignore=function(a){var b=this.getItem(a);b&&(b.isIgnored=!0)},g.prototype.unignore=function(a){var b=this.getItem(a);b&&delete b.isIgnored},g.prototype.stamp=function(a){if(a=this._find(a)){this.stamps=this.stamps.concat(a);for(var b=0,c=a.length;c>b;b++){var d=a[b];this.ignore(d)}}},g.prototype.unstamp=function(a){if(a=this._find(a))for(var b=0,c=a.length;c>b;b++){var d=a[b];e.removeFrom(this.stamps,d),this.unignore(d)}},g.prototype._find=function(a){return a?("string"==typeof a&&(a=this.element.querySelectorAll(a)),a=e.makeArray(a)):void 0},g.prototype._manageStamps=function(){if(this.stamps&&this.stamps.length){this._getBoundingRect();for(var a=0,b=this.stamps.length;b>a;a++){var c=this.stamps[a];this._manageStamp(c)}}},g.prototype._getBoundingRect=function(){var a=this.element.getBoundingClientRect(),b=this.size;this._boundingRect={left:a.left+b.paddingLeft+b.borderLeftWidth,top:a.top+b.paddingTop+b.borderTopWidth,right:a.right-(b.paddingRight+b.borderRightWidth),bottom:a.bottom-(b.paddingBottom+b.borderBottomWidth)}},g.prototype._manageStamp=j,g.prototype._getElementOffset=function(a){var b=a.getBoundingClientRect(),c=this._boundingRect,e=d(a),f={left:b.left-c.left-e.marginLeft,top:b.top-c.top-e.marginTop,right:c.right-b.right-e.marginRight,bottom:c.bottom-b.bottom-e.marginBottom};return f},g.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},g.prototype.bindResize=function(){this.isResizeBound||(b.bind(a,"resize",this),this.isResizeBound=!0)},g.prototype.unbindResize=function(){this.isResizeBound&&b.unbind(a,"resize",this),this.isResizeBound=!1},g.prototype.onresize=function(){function a(){b.resize(),delete b.resizeTimeout}
				this.resizeTimeout&&clearTimeout(this.resizeTimeout);var b=this;this.resizeTimeout=setTimeout(a,100)},g.prototype.resize=function(){this.isResizeBound&&this.needsResizeLayout()&&this.layout()},g.prototype.needsResizeLayout=function(){var a=d(this.element),b=this.size&&a;return b&&a.innerWidth!==this.size.innerWidth},g.prototype.addItems=function(a){var b=this._itemize(a);return b.length&&(this.items=this.items.concat(b)),b},g.prototype.appended=function(a){var b=this.addItems(a);b.length&&(this.layoutItems(b,!0),this.reveal(b))},g.prototype.prepended=function(a){var b=this._itemize(a);if(b.length){var c=this.items.slice(0);this.items=b.concat(c),this._resetLayout(),this._manageStamps(),this.layoutItems(b,!0),this.reveal(b),this.layoutItems(c)}},g.prototype.reveal=function(a){this._emitCompleteOnItems("reveal",a);for(var b=a&&a.length,c=0;b&&b>c;c++){var d=a[c];d.reveal()}},g.prototype.hide=function(a){this._emitCompleteOnItems("hide",a);for(var b=a&&a.length,c=0;b&&b>c;c++){var d=a[c];d.hide()}},g.prototype.revealItemElements=function(a){var b=this.getItems(a);this.reveal(b)},g.prototype.hideItemElements=function(a){var b=this.getItems(a);this.hide(b)},g.prototype.getItem=function(a){for(var b=0,c=this.items.length;c>b;b++){var d=this.items[b];if(d.element===a)return d}},g.prototype.getItems=function(a){a=e.makeArray(a);for(var b=[],c=0,d=a.length;d>c;c++){var f=a[c],g=this.getItem(f);g&&b.push(g)}
				return b},g.prototype.remove=function(a){var b=this.getItems(a);if(this._emitCompleteOnItems("remove",b),b&&b.length)for(var c=0,d=b.length;d>c;c++){var f=b[c];f.remove(),e.removeFrom(this.items,f)}},g.prototype.destroy=function(){var a=this.element.style;a.height="",a.position="",a.width="";for(var b=0,c=this.items.length;c>b;b++){var d=this.items[b];d.destroy()}
				this.unbindResize();var e=this.element.outlayerGUID;delete l[e],delete this.element.outlayerGUID,i&&i.removeData(this.element,this.constructor.namespace)},g.data=function(a){a=e.getQueryElement(a);var b=a&&a.outlayerGUID;return b&&l[b]},g.create=function(a,b){function c(){g.apply(this,arguments)}
				return Object.create?c.prototype=Object.create(g.prototype):e.extend(c.prototype,g.prototype),c.prototype.constructor=c,c.defaults=e.extend({},g.defaults),e.extend(c.defaults,b),c.prototype.settings={},c.namespace=a,c.data=g.data,c.Item=function(){f.apply(this,arguments)},c.Item.prototype=new f,e.htmlInit(c,a),i&&i.bridget&&i.bridget(a,c),c},g.Item=f,g}),function(a,b){"function"==typeof define&&define.amd?define(["outlayer/outlayer","get-size/get-size","fizzy-ui-utils/utils"],b):"object"==typeof exports?module.exports=b(require("outlayer"),require("get-size"),require("fizzy-ui-utils")):a.Masonry=b(a.Outlayer,a.getSize,a.fizzyUIUtils)}(window,function(a,b,c){var d=a.create("masonry");return d.prototype._resetLayout=function(){this.getSize(),this._getMeasurement("columnWidth","outerWidth"),this._getMeasurement("gutter","outerWidth"),this.measureColumns();var a=this.cols;for(this.colYs=[];a--;)this.colYs.push(0);this.maxY=0},d.prototype.measureColumns=function(){if(this.getContainerWidth(),!this.columnWidth){var a=this.items[0],c=a&&a.element;this.columnWidth=c&&b(c).outerWidth||this.containerWidth}
			var d=this.columnWidth+=this.gutter,e=this.containerWidth+this.gutter,f=e/d,g=d-e%d,h=g&&1>g?"round":"floor";f=Math[h](f),this.cols=Math.max(f,1)},d.prototype.getContainerWidth=function(){var a=this.options.isFitWidth?this.element.parentNode:this.element,c=b(a);this.containerWidth=c&&c.innerWidth},d.prototype._getItemLayoutPosition=function(a){a.getSize();var b=a.size.outerWidth%this.columnWidth,d=b&&1>b?"round":"ceil",e=Math[d](a.size.outerWidth/this.columnWidth);e=Math.min(e,this.cols);for(var f=this._getColGroup(e),g=Math.min.apply(Math,f),h=c.indexOf(f,g),i={x:this.columnWidth*h,y:g},j=g+a.size.outerHeight,k=this.cols+1-f.length,l=0;k>l;l++)this.colYs[h+l]=j;return i},d.prototype._getColGroup=function(a){if(2>a)return this.colYs;for(var b=[],c=this.cols+1-a,d=0;c>d;d++){var e=this.colYs.slice(d,d+a);b[d]=Math.max.apply(Math,e)}
			return b},d.prototype._manageStamp=function(a){var c=b(a),d=this._getElementOffset(a),e=this.options.isOriginLeft?d.left:d.right,f=e+c.outerWidth,g=Math.floor(e/this.columnWidth);g=Math.max(0,g);var h=Math.floor(f/this.columnWidth);h-=f%this.columnWidth?0:1,h=Math.min(this.cols-1,h);for(var i=(this.options.isOriginTop?d.top:d.bottom)+c.outerHeight,j=g;h>=j;j++)this.colYs[j]=Math.max(i,this.colYs[j])},d.prototype._getContainerSize=function(){this.maxY=Math.max.apply(Math,this.colYs);var a={height:this.maxY};return this.options.isFitWidth&&(a.width=this._getContainerFitWidth()),a},d.prototype._getContainerFitWidth=function(){for(var a=0,b=this.cols;--b&&0===this.colYs[b];)a++;return(this.cols-a)*this.columnWidth-this.gutter},d.prototype.needsResizeLayout=function(){var a=this.containerWidth;return this.getContainerWidth(),a!==this.containerWidth},d})

		function cffAddMasonry($self) {
			var evt = jQuery.Event('cffbeforemasonry');
			evt.$self = $self;
			jQuery(window).trigger(evt);

			if (typeof $self.masonry !== 'function') {
				return;
			}
			var windowWidth = jQuery(window).width(),
				masonryEnabled = false;

			if (windowWidth > 800) {
				if ($self.hasClass('masonry-1-desktop')) {
					$self.addClass('cff-disable-masonry');
				} else {
					masonryEnabled = true;
					$self.addClass('cff-masonry cff-masonry-js').removeClass('cff-disable-masonry');
				}
			} else if (windowWidth > 480) {
				if ($self.hasClass('masonry-2-tablet')
					|| $self.hasClass('masonry-3-tablet')
					|| $self.hasClass('masonry-4-tablet')
					|| $self.hasClass('masonry-5-tablet')
					|| $self.hasClass('masonry-6-tablet')) {
					masonryEnabled = true;
					$self.addClass('cff-masonry cff-masonry-js').removeClass('cff-disable-masonry');
				} else {
					$self.addClass('cff-disable-masonry');
				}
			} else {
				if ($self.hasClass('masonry-2-mobile')
					|| $self.hasClass('masonry-3-mobile')) {
					masonryEnabled = true;
					$self.addClass('cff-masonry cff-masonry-js').removeClass('cff-disable-masonry');
				} else {
					$self.addClass('cff-disable-masonry');
				}
			}

			if (masonryEnabled) {
				if($self.find('.cff-item').length) {

					var itemSelector = {itemSelector:'.cff-new, .cff-item, .cff-likebox'};
					$self.masonry(itemSelector);
					// Add margin to the bottom of each post to give some space
					$self.find('.cff-item').each( function() {
						jQuery(this).css('margin-bottom', '15px');
					});
				}
			}
		}

	} //End Masonry code


	function cff_init(){

		//Set likebox width
		/*
		jQuery('.cff-likebox iframe').each(function(){
			var $likebox = jQuery(this),
				likeboxWidth = $likebox.attr('data-likebox-width'),
				cffFeedWidth = $likebox.parent().width();
			//Default width is 340
			if( likeboxWidth == '' ) likeboxWidth = 340;
			//Change the width dynamically so it's responsive
			if( cffFeedWidth < likeboxWidth ) likeboxWidth = cffFeedWidth;

			$likebox.attr('src', 'https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2F'+$likebox.attr('data-likebox-id')+'%2F&tabs&width='+Math.floor(likeboxWidth)+'&small_header='+$likebox.attr('data-likebox-header')+'&adapt_container_width=true&hide_cover='+$likebox.attr('data-hide-cover')+'&hide_cta='+$likebox.attr('data-hide-cta')+'&show_facepile='+$likebox.attr('data-likebox-faces')+'&locale='+$likebox.attr('data-locale'));
		});
		*/
		jQuery('#cff .cff-item').each(function(){
			var $self = jQuery(this);

			//Wpautop fix
			if( $self.find('.cff-viewpost-facebook').parent('p').length ){
				$self.find('.cff-viewpost-facebook').unwrap('p');
			}
			if( $self.find('.cff-author').parent('p').length ){
				$self.find('.cff-author').eq(1).unwrap('p');
				$self.find('.cff-author').eq(1).remove();
			}
			if( $self.find('#cff .cff-link').parent('p').length ){
				$self.find('#cff .cff-link').unwrap('p');
			}

			//Expand post
			var	expanded = false,
				$post_text = $self.find('.cff-post-text .cff-text'),
				text_limit = $self.closest('#cff').attr('data-char');

			if (typeof text_limit === 'undefined' || text_limit == '') text_limit = 99999;

			//If the text is linked then use the text within the link
			//if ( $post_text.find('a.cff-post-text-link').length ) $post_text = $self.find('.cff-post-text .cff-text .cff-post-text-link');
			var	full_text = $post_text.html();
			if(full_text == undefined) full_text = '';

			//Truncate text taking HTML tags into account
			var cff_trunc_regx = new RegExp(/(<[^>]*>)/g);
			var cff_trunc_counter = 0;

			//convert the string to array using the HTML tags as delimiter and keeping them as array elements
			full_text_arr = full_text.split(cff_trunc_regx);

			for (var i = 0, len = full_text_arr.length; i < len; i++) {
				//ignore the array elements that are HTML tags
				if ( !(cff_trunc_regx.test(full_text_arr[i])) ) {
					//if the counter is 100, remove this element with text
					if (cff_trunc_counter == text_limit) {
						full_text_arr.splice(i, 1);
						continue; //ignore next commands and continue the for loop
					}
					//if the counter != 100, increase the counter with this element length
					cff_trunc_counter = cff_trunc_counter + full_text_arr[i].length;
					//if is over 100, slice the text of this element to match the total of 100 chars and set the counter to 100
					if (cff_trunc_counter > text_limit) {
						var diff = cff_trunc_counter - text_limit;
						full_text_arr[i] = full_text_arr[i].slice(0, -diff);
						cff_trunc_counter = text_limit;

						//Show the 'See More' link if needed
						if (full_text.length > text_limit) $self.find('.cff-expand').show();
					}
				}
			}

			//new string from the array
			var short_text = full_text_arr.join('');

			//remove empty html tags from the array
			short_text = short_text.replace(/(<(?!\/)[^>]+>)+(<\/[^>]+>)/g, "");

			//If the short text cuts off in the middle of a <br> tag then remove the stray '<' which is displayed
			var lastChar = short_text.substr(short_text.length - 1);
			if(lastChar == '<') short_text = short_text.substring(0, short_text.length - 1);

			//Remove any <br> tags from the end of the short_text
			short_text = short_text.replace(/(<br>\s*)+$/,'');
			short_text = short_text.replace(/(<img class="cff-linebreak">\s*)+$/,'');

			//Cut the text based on limits set
			$post_text.html( short_text );


			//Click function
			$self.find('.cff-expand').on('click', function(e){
				e.preventDefault();
				var $expand = jQuery(this),
					$more = $expand.find('.cff-more'),
					$less = $expand.find('.cff-less');
				if (expanded == false){
					$post_text.html( full_text );
					expanded = true;
					$more.hide();
					$less.show();
				} else {
					$post_text.html( short_text );
					expanded = false;
					$more.show();
					$less.hide();
				}
				cffLinkHashtags();
				//Add target to links in text when expanded
				$post_text.find('a').attr('target', '_blank');
				//Re-init masonry for JS
				if( $self.closest('.cff').hasClass('cff-masonry-js') && !$self.closest('.cff').hasClass('cff-masonry-css') ){
                	cffAddMasonry($self.closest('.cff'));
				}
			});
			//Add target attr to post text links via JS so aren't included in char count
			$post_text.find('a').add( $self.find('.cff-post-desc a') ).attr({
				'target' : '_blank',
				'rel' : 'nofollow'
			});

			//Hide the shared link box if it's empty
			$sharedLink = $self.find('.cff-shared-link');
			if( $sharedLink.text() == '' ){
				$sharedLink.remove();
			}

			function cffLinkHashtags(){
				//Link hashtags
				var cffTextStr = $self.find('.cff-text').html(),
					cffDescStr = $self.find('.cff-post-desc').html(),
					regex = /(^|\s)#(\w*[\u0041-\u005A\u0061-\u007A\u00AA\u00B5\u00BA\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u0527\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0\u08A2-\u08AC\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0977\u0979-\u097F\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C33\u0C35-\u0C39\u0C3D\u0C58\u0C59\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D60\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F4\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191C\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19C1-\u19C7\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FCC\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA697\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA78E\uA790-\uA793\uA7A0-\uA7AA\uA7F8-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA80-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]+\w*)/gi,
					// regex = /#(\w*[a-z\u00E0-\u00FC一-龠ぁ-ゔァ-ヴー]+\w*)/gi,
					linkcolor = $self.find('.cff-text').attr('data-color');

				function replacer(hash){
					//Remove white space at beginning of hash
					var replacementString = jQuery.trim(hash);
					//If the hash is a hex code then don't replace it with a link as it's likely in the style attr, eg: "color: #ff0000"
					if ( /^#[0-9A-F]{6}$/i.test( replacementString ) ){
						return replacementString;
					} else {
						return ' <a href="https://www.facebook.com/hashtag/'+ replacementString.substring(1) +'" target="_blank" rel="nofollow" style="color:#' + linkcolor + '">' + replacementString + '</a>';
					}
				}

				//If it's not defined in the source code then set it to be true
				if (typeof cfflinkhashtags == 'undefined') cfflinkhashtags = 'true';

				if(cfflinkhashtags == 'true'){
					//Replace hashtags in text
					var $cffText = $self.find('.cff-text');
					if($cffText.length > 0 && $cffText.find('.cff-post-text-link').length == 0){
						//Add a space after all <br> tags so that #hashtags immediately after them are also converted to hashtag links. Without the space they aren't captured by the regex.
						cffTextStr = cffTextStr.replace(/<br>/g, "<br> ");
						$cffText.html( cffTextStr.replace( regex , replacer ) );
					}
				}

				//Replace hashtags in desc
				if( $self.find('.cff-post-desc').length > 0 ) $self.find('.cff-post-desc').html( cffDescStr.replace( regex , replacer ) );
			}
			cffLinkHashtags();

			//Add target attr to post text links via JS so aren't included in char count
			$self.find('.cff-text a').add( $self.find('.cff-post-desc a') ).attr({
				'target' : '_blank',
				'rel' : 'nofollow noopener noreferrer'
			});

			//Share tooltip function
			$self.find('.cff-share-link').on('click', function(e){

				e.preventDefault();
				var $cffShareTooltip = $self.find('.cff-share-tooltip')

				//Hide tooltip
				if( $cffShareTooltip.is(':visible') ){
					$cffShareTooltip.hide().find('a').removeClass('cff-show');
				} else {
					//Show tooltip
					$cffShareTooltip.show();

					var time = 0;
					$cffShareTooltip.find('a').each(function() {
						var $cffShareIcon = jQuery(this);
						setTimeout( function(){
							$cffShareIcon.addClass('cff-show');
						}, time);
						time += 20;
					});
				}
			});


		}); //End .cff-item each



		jQuery('.cff-wrapper').each(function(){
			var $cff = jQuery(this).find('#cff');
			var $cffElm = jQuery(this);
			setTimeout(function(){
				var consent = checkConsent( $cffElm );
				if(consent){
					addFullFeatures( $cffElm);
				}else{
					jQuery('.cff-gdpr-notice').css({'display':'inline-block'});
					if ($cffElm.find('.cff-visual-header').length) {
	                    $cffElm.find('.cff-header-text').closest('.cff-visual-header').addClass('cff-no-consent');
	                }
				}
			},250)

			//maybe hide
			if (typeof $cff.attr('data-nummobile') !== 'undefined') {
				var num = typeof $cff.attr('data-pag-num') !== 'undefined' && $cff.attr('data-pag-num') !== '' ? parseInt($cff.attr('data-pag-num')) : 1,
					nummobile = typeof $cff.attr('data-nummobile') !== 'undefined' && $cff.attr('data-nummobile') !== '' ? parseInt($cff.attr('data-nummobile')) : num,
					itemSelector = $cff.find('.cff-item').length ? '.cff-item' : '.cff-album-item';
				if (jQuery(window).width() < 480) {
					if (nummobile < $cff.find(itemSelector).length) {
						$cff.find(itemSelector).slice(nummobile - $cff.find(itemSelector).length).addClass('cff-num-diff-hide');
					}
				} else {
					if (num < $cff.find(itemSelector).length) {
						$cff.find(itemSelector).slice(num - $cff.find(itemSelector).length).addClass('cff-num-diff-hide');
					}
				}
				$cff.removeAttr('data-nummobile');
			}

			//Masonry
			if( $cff.hasClass('cff-masonry-js') ){
				cffAddMasonry($cff);
				//Call it again in case post isn't fully loaded
				setTimeout(function(){ cffAddMasonry($cff); }, 500);
				// Resizing the window can affect the masonry feed so it is reset on resize
				jQuery(window).on('resize', function () {
					setTimeout(function(){
						cffAddMasonry($cff);
					}, 500);
				});
				if( $cff.find('.cff-credit').length ) $cff.css('padding-bottom', 30);
			}
		});

		function cffSizeVisualHeader() {
			jQuery('.cff-visual-header.cff-has-cover').each(function() {
				var wrapperHeight = jQuery(this).find('.cff-header-hero').innerHeight(),
					imageHeight = jQuery(this).find('.cff-header-hero img').innerHeight(),
					wrapperWidth = jQuery(this).find('.cff-header-hero').innerWidth(),
					imageWidth = jQuery(this).find('.cff-header-hero img').innerWidth(),
					wrapperAspect = wrapperWidth/wrapperHeight,
					imageAspect = imageWidth/imageHeight,
					width = wrapperAspect < imageAspect ? wrapperHeight * imageAspect + 'px' : '100%',
					difference = imageHeight - wrapperHeight,
					topMargin = Math.max(0,Math.round(difference/2)),
					leftMargin = width !== '100%' ? Math.max(0,Math.round(((wrapperHeight * imageAspect)-wrapperWidth)/2)) : 0;
				jQuery(this).find('.cff-header-hero img').css({
					'opacity' : 1,
					'display' : 'block',
					'visibility' : 'visible',
					'max-width' : 'none',
					'max-height' : 'none',
					'margin-top' : - topMargin + 'px',
					'margin-left' : - leftMargin + 'px',
					'width' : width,
				});
			});
		}setTimeout(cffSizeVisualHeader, 200);

		jQuery(window).on('resize', function () {
			setTimeout(function(){
				cffSizeVisualHeader();
			}, 500);
		});
	}
	cff_init();
	/*
			GDPR & CONSENT Functions
		*/
		function checkConsent( ctn ){
			ctn = ctn.find('.cff-list-container');
			var flags 				= typeof ctn.attr('data-cff-flags') !== 'undefined' ? ctn.attr('data-cff-flags').split(',') : [],
			 	gdpr 				= (flags.indexOf('gdpr') > -1),
                overrideBlockCDN 	= (flags.indexOf('overrideBlockCDN') > -1),
                consentGiven 		= false;
            if (consentGiven || !gdpr) {
                return true;
            }
            if (typeof CLI_Cookie !== "undefined") { // GDPR Cookie Consent by WebToffee
                if (CLI_Cookie.read(CLI_ACCEPT_COOKIE_NAME) !== null)  {

                    // WebToffee no longer uses this cookie but being left here to maintain backwards compatibility
                    if (CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') !== null) {
                        consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-non-necessary') === 'yes';
                    }

                    if (CLI_Cookie.read('cookielawinfo-checkbox-necessary') !== null) {
                        consentGiven = CLI_Cookie.read('cookielawinfo-checkbox-necessary') === 'yes';
                    }
                }

            } else if (typeof window.cnArgs !== "undefined") { // Cookie Notice by dFactory
                var value = "; " + document.cookie,
                    parts = value.split( '; cookie_notice_accepted=' );
                if ( parts.length === 2 ) {
                    var val = parts.pop().split( ';' ).shift();
                    consentGiven = (val === 'true');
                }
            } else if (typeof window.cookieconsent !== 'undefined') { // Complianz by Really Simple Plugins
                consentGiven = cffCmplzGetCookie('cmplz_consent_status') === 'allow';
            } else if (typeof window.Cookiebot !== "undefined") { // Cookiebot by Cybot A/S
                consentGiven = Cookiebot.consented;
            } else if (typeof window.BorlabsCookie !== 'undefined') { // Borlabs Cookie by Borlabs
                consentGiven = window.BorlabsCookie.checkCookieConsent('facebook');
            }
            return consentGiven; // GDPR not enabled
		}

		function cffCmplzGetCookie(cname) {
            var name = cname + "="; //Create the cookie name variable with cookie name concatenate with = sign
            var cArr = window.document.cookie.split(';'); //Create cookie array by split the cookie by ';'

            //Loop through the cookies and return the cookie value if it find the cookie name
            for (var i = 0; i < cArr.length; i++) {
                var c = cArr[i].trim();
                //If the name is the cookie string at position 0, we found the cookie and return the cookie value
                if (c.indexOf(name) == 0)
                    return c.substring(name.length, c.length);
            }

            return "";
        }

		function addFullFeatures( ctn ){
			ctn = jQuery( ctn );
			jQuery('.cff-gdpr-notice').remove();
			ctn.find('.cff-author-img').each(function() {
                jQuery(this).find('img').attr('src',jQuery(this).attr('data-avatar'));
                jQuery(this).removeClass('cff-no-consent');
            });
            ctn.find('.cff-likebox iframe').each(function(){
                var $likebox = jQuery(this),
                    likeboxWidth = $likebox.attr('data-likebox-width'),
                    cffFeedWidth = $likebox.parent().width();
                //Default width is 340
                if( likeboxWidth == '' ) likeboxWidth = 340;
                //Change the width dynamically so it's responsive
                if( cffFeedWidth < likeboxWidth ) likeboxWidth = cffFeedWidth;

                $likebox.attr('src', 'https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2F'+$likebox.attr('data-likebox-id')+'%2F&tabs&width='+Math.floor(likeboxWidth)+'&small_header='+$likebox.attr('data-likebox-header')+'&adapt_container_width=true&hide_cover='+$likebox.attr('data-hide-cover')+'&hide_cta='+$likebox.attr('data-hide-cta')+'&show_facepile='+$likebox.attr('data-likebox-faces')+'&locale='+$likebox.attr('data-locale'));
            });
            if (jQuery('.cff-visual-header').length) {
                jQuery('.cff-visual-header').each(function() {
                    jQuery(this).removeClass('cff-no-consent');
                    if (jQuery(this).find('.cff-header-hero').length) {
                        jQuery(this).find('.cff-header-hero').find('img').attr('src',jQuery(this).find('.cff-header-hero').find('img').attr('data-cover-url'))
                    }
                    if (jQuery(this).find('.cff-header-img').length) {
                        jQuery(this).find('.cff-header-img').find('img').attr('src',jQuery(this).find('.cff-header-img').find('img').attr('data-avatar'))
                    }
                });
            }
		}

		function afterConsentToggled( isConsent, ctn ) {
			if( isConsent ){
				addFullFeatures( ctn );
			}
		}

		//Feed Locator Script
		function cffGetFeedLocatorDataArray(){
			var feedLocatorData = [];
			jQuery('.cff-list-container').each(function(){
				$cffPagUrl = jQuery(this).find('.cff-pag-url');
				var locatorNonce = '';
				if ( typeof $cffPagUrl.attr( 'data-locatornonce' ) !== 'undefined' ) {
					locatorNonce = $cffPagUrl.attr( 'data-locatornonce' );
				}

				var singleFeedLocatorData = {
					feedID : $cffPagUrl.attr('data-feed-id'),
					postID : $cffPagUrl.attr('data-post-id'),
					shortCodeAtts : jQuery.trim($cffPagUrl.attr('data-cff-shortcode')) == '' ? null : JSON.parse($cffPagUrl.attr('data-cff-shortcode')),
					location : locationGuess(jQuery(this)),
					locator_nonce : locatorNonce
				};
				feedLocatorData.push(singleFeedLocatorData);
			});
			return feedLocatorData;
		}

		function locationGuess($cff = false) {
			var $feed = ($cff == false) ? jQuery(this.el) : $cff,
			location = 'content';

			if ($feed.closest('footer').length) {
				location = 'footer';
			} else if ($feed.closest('.header').length
				|| $feed.closest('header').length) {
				location = 'header';
			} else if ($feed.closest('.sidebar').length
				|| $feed.closest('aside').length) {
				location = 'sidebar';
			}

			return location;
		}



	jQuery(document).ready(function(){
		var $ = jQuery;
            $('#cookie-notice a').on('click', function() {
                setTimeout(function() {
                    jQuery('.cff-wrapper').each(function(index){
                        afterConsentToggled( checkConsent( jQuery(this) ), jQuery(this) );
                    });
                },1000);
            });

            // Cookie Notice by dFactory
            $('#cookie-law-info-bar a').on('click', function() {
                setTimeout(function() {
                    jQuery('.cff-wrapper').each(function(index){
                        afterConsentToggled( checkConsent( jQuery(this) ), jQuery(this) );
                    });
                },1000);
            });

            // GDPR Cookie Consent by WebToffee
            $('.cli-user-preference-checkbox').on('click', function(){
                setTimeout(function() {
                    jQuery('.cff-wrapper').each(function(index){
                        afterConsentToggled( false, jQuery(this) );
                    });
                },1000);
            });

            // Cookiebot
            $(window).on('CookiebotOnAccept', function (event) {
                jQuery('.cff-wrapper').each(function(index){
                	afterConsentToggled( true, jQuery(this) );
                });
            });

            // Complianz by Really Simple Plugins
			$(document).on('cmplzEnableScripts', function (event) {
				if ( event.detail === 'marketing' ) {
					jQuery('.cff-wrapper').each(function (index) {
						afterConsentToggled(true, jQuery(this));
					});
				}
            });

            // Complianz by Really Simple Plugins
			$(document).on('cmplzFireCategories', function (event) {
				if ( event.detail.category === 'marketing' ) {
					jQuery('.cff-wrapper').each(function (index) {
						afterConsentToggled(false, jQuery(this));
					});
				}
            });

            // Borlabs Cookie by Borlabs
            $(document).on('borlabs-cookie-consent-saved', function (event) {
                jQuery('.cff-wrapper').each(function(index){
                	afterConsentToggled( true, jQuery(this) );
                });
            });
            if( $('.cff-list-container').length ){
	            var feedLocatorData =  cffGetFeedLocatorDataArray();
	            $.ajax({
                    url: cffajaxurl,
                    type: 'POST',
                    data:{
                        action: 'feed_locator',
                        feedLocatorData : feedLocatorData
                    }
                });
            }
	})

} //End cff_js_exists check