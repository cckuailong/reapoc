jQuery.fn.nextInArray = function(element) {
    var nextId = 0;
    for(var i = 0; i < this.length; i++) {
        if(this[i] == element) {
            nextId = i + 1;
            break;
        }
    }
    if(nextId > this.length-1)
        nextId = 0;
    return this[nextId];
}
jQuery.fn.clearForm = function() {
	return this.each(function() {
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
			return jQuery(':input', this).clearForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')
			this.value = '';
		else if (type == 'checkbox' || type == 'radio')
			this.checked = false;
		else if (tag == 'select') 
			this.selectedIndex = -1;
	});
}
jQuery.fn.tagName = function() {
    return this.get(0).tagName;
}
jQuery.fn.exists = function(){
    return (jQuery(this).length > 0 ? true : false);
}
function isNumber(val) {
    return /^\d+/.test(val);
}
function pushDataToParam(data, pref) {
	pref = pref ? pref : '';
	var res = [];
	for(var key in data) {
		var name = pref && pref != '' ? pref+ '['+ key+ ']' : key;
		if(typeof(data[key]) === 'array' || typeof(data[key]) === 'object') {
			res = jQuery.merge(res, pushDataToParam(data[key], name));
		} else {
			res.push(name+ "="+ data[key]);
		}
	}
	return res;
}
jQuery.fn.serializeAnythingUms = function(addData) {
    var toReturn    = [];
    var els         = jQuery(this).find(':input').get();
    jQuery.each(els, function() {
        if (this.name && !this.disabled && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
            var val = jQuery(this).val();
            toReturn.push( encodeURIComponent(this.name) + "=" + encodeURIComponent( val ) );
        }
    });
    if(typeof(addData) != 'undefined') {
		toReturn = jQuery.merge(toReturn, pushDataToParam(addData));
    }
    return toReturn.join("&").replace(/%20/g, "+");
};
jQuery.fn.serializeAssoc = function() {
	var data = [ ];
	jQuery.each( this.serializeArray(), function( key, obj ) {
	  var a = obj.name.match(/(.*?)\[(.*?)\]/);
	  if(a !== null)
	  {
		var subName = a[1];
		var subKey = a[2];
		if( !data[subName] ) data[subName] = [ ];
		  if( data[subName][subKey] ) {
			if( jQuery.isArray( data[subName][subKey] ) ) {
			  data[subName][subKey].push( obj.value );
			} else {
			  data[subName][subKey] = [ ];
			  data[subName][subKey].push( obj.value );
			};
		  } else {
			data[subName][subKey] = obj.value;
		  };  
		} else {
		  if( data[obj.name] ) {
			if( jQuery.isArray( data[obj.name] ) ) {
			  data[obj.name].push( obj.value );
			} else {
			  data[obj.name] = [ ];
			  data[obj.name].push( obj.value );
			};
		  } else {
			data[obj.name] = obj.value;
		  };
		};
	});
	return data;
};
function str_replace(haystack, needle, replacement) { 
	var temp = haystack.split(needle); 
	return temp.join(replacement); 
}
/**
 * @see php html::nameToClassId($name) method
 **/
function nameToClassId(name) {
    return str_replace(
        str_replace(name, ']', ''), 
            '[', ''
    );
}
function strpos( haystack, needle, offset){
    var i = haystack.indexOf( needle, offset ); // returns -1
    return i >= 0 ? i : false;
}
function extendUms(Child, Parent) {
    var F = function() { };
    F.prototype = Parent.prototype;
    Child.prototype = new F();
    Child.prototype.constructor = Child;
    Child.superclass = Parent.prototype;
}
function toeRedirect(url) {
    document.location.href = url;
}
function toeReload(url) {
	if(url) {
		toeRedirect(url);
	} else
		document.location.reload();
}
jQuery.fn.toeRebuildSelect = function(data, useIdAsValue, val) {
    if(jQuery(this).tagName() == 'SELECT' && typeof(data) == 'object') {
        if(jQuery(data).length > 0) {
            if(typeof(val) == 'undefined')
                val = false;
            if(jQuery(this).children('option').length) {
                jQuery(this).children('option').remove();
            }
            if(typeof(useIdAsValue) == 'undefined')
                useIdAsValue = false;
            var selected = '';
            for(var id in data) {
                selected = '';
                if(val && ((useIdAsValue && id == val) || (data[id] == val)))
                    selected = 'selected';
                jQuery(this).append('<option value="'+ (useIdAsValue ? id : data[id])+ '" '+ selected+ '>'+ data[id]+ '</option>');
            }
        }
    }
}
/**
 * We will not use just jQUery.inArray because it is work incorrect for objects
 * @return mixed - key that was found element or -1 if not
 */
function toeInArray(needle, haystack) {
    if(typeof(haystack) == 'object') {
        for(var k in haystack) {
            if(haystack[ k ] == needle)
                return k;
        }
    } else if(typeof(haystack) == 'array') {
        return jQuery.inArray(needle, haystack);
    }
    return -1;
}
jQuery.fn.setReadonly = function() {
	jQuery(this).addClass('toeReadonly').attr('readonly', 'readonly');
}
jQuery.fn.unsetReadonly = function() {
	jQuery(this).removeClass('toeReadonly').removeAttr('readonly', 'readonly');
}
jQuery.fn.getClassId = function(pref, test) {
	var classId = jQuery(this).attr('class');
	classId = classId.substr( strpos(classId, pref+ '_') );
	if(strpos(classId, ' '))
		classId = classId.substr( 0, strpos(classId, ' ') );
	classId = classId.split('_');
	classId = classId[1];
	return classId;
}
function toeTextIncDec(textFieldId, inc) {
	var value = parseInt(jQuery('#'+ textFieldId).val());
	if(isNaN(value))
		value = 0;
	if(!(inc < 0 && value < 1)) {
		value += inc;
	}
	jQuery('#'+ textFieldId).val(value);
}

/**
 * Make first letter of string in upper case
 * @param str string - string to convert
 * @return string converted string - first letter in upper case
 */
function toeStrFirstUp(str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}
function URLToArray(url) {
	var request = {};
	var pairs = url.substring(url.indexOf('?') + 1).split('&');
	for (var i = 0; i < pairs.length; i++) {
		if(!pairs[i])
			continue;
		var pair = pairs[i].split('=');
		request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
	}
	return request;
}
function ArrayToURL(array) {
	var pairs = [];
	for (var key in array)
		if (array.hasOwnProperty(key))

			pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(array[key]));
	return pairs.join('&');
}
function parseStr (str, array) {
  // http://kevin.vanzonneveld.net
  // +   original by: Cagri Ekin
  // +   improved by: Michael White (http://getsprink.com)
  // +    tweaked by: Jack
  // +   bugfixed by: Onno Marsman
  // +   reimplemented by: stag019
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: stag019
  // +   input by: Dreamer
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
  // +   input by: Zaide (http://zaidesthings.com/)
  // +   input by: David Pesta (http://davidpesta.com/)
  // +   input by: jeicquest
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: When no argument is specified, will put variables in global scope.
  // %        note 1: When a particular argument has been passed, and the returned value is different parse_str of PHP. For example, a=b=c&d====c
  // *     example 1: var arr = {};
  // *     example 1: parse_str('first=foo&second=bar', arr);
  // *     results 1: arr == { first: 'foo', second: 'bar' }
  // *     example 2: var arr = {};
  // *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
  // *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
  // *     example 3: var abc = {3:'a'};
  // *     example 3: parse_str('abc[a][b]["c"]=def&abc[q]=t+5');
  // *     results 3: JSON.stringify(abc) === '{"3":"a","a":{"b":{"c":"def"}},"q":"t 5"}';
	var strArr = String(str).replace(/^&/, '').replace(/&$/, '').split('&'),
	sal = strArr.length,
	i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
	postLeftBracketPos, keys, keysLen,
	fixStr = function (str) {
		return decodeURIComponent(str.replace(/\+/g, '%20'));
	};
	// Comented by Alexey Bolotov
	/*
	if (!array) {
	array = this.window;
	}*/
	if (!array) {
		array = {};
	}

	for (i = 0; i < sal; i++) {
		tmp = strArr[i].split('=');
		key = fixStr(tmp[0]);
		value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

		while (key.charAt(0) === ' ') {
			key = key.slice(1);
		}
		if (key.indexOf('\x00') > -1) {
			key = key.slice(0, key.indexOf('\x00'));
		}
		if (key && key.charAt(0) !== '[') {
			keys = [];
			postLeftBracketPos = 0;
			for (j = 0; j < key.length; j++) {
				if (key.charAt(j) === '[' && !postLeftBracketPos) {
					postLeftBracketPos = j + 1;
				} else if (key.charAt(j) === ']') {
					if (postLeftBracketPos) {
						if (!keys.length) {
							keys.push(key.slice(0, postLeftBracketPos - 1));
						}
						keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
						postLeftBracketPos = 0;
						if (key.charAt(j + 1) !== '[') {
							break;
						}
					}
				}
			}
			if (!keys.length) {
				keys = [key];
			}
			for (j = 0; j < keys[0].length; j++) {
				chr = keys[0].charAt(j);
				if (chr === ' ' || chr === '.' || chr === '[') {
					keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
				}
				if (chr === '[') {
					break;
				}
			}

			obj = array;
			for (j = 0, keysLen = keys.length; j < keysLen; j++) {
				key = keys[j].replace(/^['"]/, '').replace(/['"]$/, '');
				lastIter = j !== keys.length - 1;
				lastObj = obj;
				if ((key !== '' && key !== ' ') || j === 0) {
					if (obj[key] === undef) {
						obj[key] = {};
					}
					obj = obj[key];
				} else { // To insert new dimension
					ct = -1;
					for (p in obj) {
						if (obj.hasOwnProperty(p)) {
							if (+p > ct && p.match(/^\d+$/g)) {
								ct = +p;
							}
						}
					}
					key = ct + 1;
				}
			}
			lastObj[key] = value;
		}
	}
	return array;
}

function toeListableUms(params) {
	this.params			= jQuery.extend({}, params);
	this.table			= jQuery(this.params.table);
	this.paging			= jQuery(this.params.paging);
	this.perPage		= this.params.perPage;
	this.list			= this.params.list;
	this.count			= this.params.count;
	this.page			= this.params.page;
	this.pagingCallback	= this.params.pagingCallback;
	var self			= this;
	
	this.draw = function(list, count) {
		this.table.find('tr').not('.umsExample, .umsTblHeader').remove();
		var exampleRow = this.table.find('.umsExample');
		for(var i in list) {
			var newRow = exampleRow.clone();
			for(var key in list[i]) {
				var element = newRow.find('.'+ key);
				if(element.length) {
					var valueTo = element.attr('valueTo');
					if(valueTo) {
						var newValue = list[i][key];
						var prevValue = element.attr(valueTo);
						if(prevValue)
							newValue = prevValue+ ' '+ newValue;
						element.attr(valueTo, newValue);
					} else
						element.html(list[i][key]);
				}
			}
			newRow.removeClass('umsExample').show();
			this.table.append(newRow);
		}
		if(this.paging) {
			this.paging.html('');
			if(count && count > list.length && this.perPage) {
				for(var i = 1; i <= Math.ceil(count/this.perPage); i++) {
					var newPageId = i-1
					,	newElement = (newPageId == this.page) ? jQuery('<b/>') : jQuery('<a/>');
					if(newPageId != this.page) {
						newElement.attr('href', '#'+ newPageId)
						.click(function(){
							if(self.pagingCallback && typeof(self.pagingCallback) == 'function') {
								self.pagingCallback(parseInt(jQuery(this).attr('href').replace('#', '')));
								return false;
							}
						});
					}
					newElement.addClass('toePagingElement').html(i);
					this.paging.append(newElement);
					if(i%20 == 0 && i)
						this.paging.append('<br />');
				}
			}
		}
	};
	if(this.list)
		this.draw(this.list, this.count);
}

function setCookieUms(c_name, value, exdays) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var value_prepared = '';
	if(typeof(value) == 'array' || typeof(value) == 'object') {
		value_prepared = '_JSON:'+ JSON.stringify( value );
	} else {
		value_prepared = value;
	}
	var c_value = escape(value_prepared)+ ((exdays==null) ? "" : "; expires="+exdate.toUTCString())+ '; path=/';
	document.cookie = c_name+ "="+ c_value;
}

function getCookieUms(name) {
	var parts = document.cookie.split(name + "=");
	if (parts.length == 2) {
		var value = unescape(parts.pop().split(";").shift());
		if(value.indexOf('_JSON:') === 0) {
			value = JSON.parse(value.split("_JSON:").pop());
		}
		return value;
	}
	return null;
}

function delCookieUms( name ) {
  document.cookie = name+ '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function callUserFuncArray(cb, parameters) {
	// http://kevin.vanzonneveld.net
	// +   original by: Thiago Mata (http://thiagomata.blog.com)
	// +   revised  by: Jon Hohle
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: Diplom@t (http://difane.com/)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: call_user_func_array('isNaN', ['a']);
	// *     returns 1: true
	// *     example 2: call_user_func_array('isNaN', [1]);
	// *     returns 2: false
	var func;

	if (typeof cb === 'string') {
		func = (typeof this[cb] === 'function') ? this[cb] : func = (new Function(null, 'return ' + cb))();
	}
	else if (Object.prototype.toString.call(cb) === '[object Array]') {
		func = (typeof cb[0] == 'string') ? eval(cb[0] + "['" + cb[1] + "']") : func = cb[0][cb[1]];
	}
	else if (typeof cb === 'function') {
		func = cb;
	}

	if (typeof func !== 'function') {
		throw new Error(func + ' is not a valid function');
	}

	return (typeof cb[0] === 'string') ? func.apply(eval(cb[0]), parameters) : (typeof cb[0] !== 'object') ? func.apply(null, parameters) : func.apply(cb[0], parameters);
}
jQuery.fn.zoom = function(level) {
	jQuery(this).data('zoom', level);
	return jQuery(this).css({
	/*	'zoom': level	// Didn't worked correctly for mobiles
	,*/	'-moz-transform': 'scale('+ level+ ')'
	,	'-moz-transform-origin': 'center center'
	,	'-o-transform': 'scale('+ level+ ')'
	,	'-o-transform-origin': 'center center'
	,	'-webkit-transform': 'scale('+ level+ ')'
	,	'-webkit-transform-origin': 'center center'
	,	'transform': 'scale('+ level+ ')'
	,	'transform-origin': 'center center'
	});
};
jQuery.fn.rotate = function(deg) {
	return jQuery(this).css({
		'-moz-transform':'rotate('+ deg+ 'deg)'
	,	'-webkit-transform':'rotate('+ deg+ 'deg)'
	,	'-o-transform':'rotate('+ deg+ 'deg)'
	,	'-ms-transform':'rotate('+ deg+ 'deg)'
	,	'transform': 'rotate('+ deg+ 'deg)'
	});
};
jQuery.fn.scrollWidth = function() {
	var inner = document.createElement('p');
	inner.style.width = "100%";
	inner.style.height = "200px";

	var outer = document.createElement('div');
	outer.style.position = "absolute";
	outer.style.top = "0px";
	outer.style.left = "0px";
	outer.style.visibility = "hidden";
	outer.style.width = "200px";
	outer.style.height = "150px";
	outer.style.overflow = "hidden";
	outer.appendChild (inner);

	document.body.appendChild (outer);
	var w1 = inner.offsetWidth;
	outer.style.overflow = 'scroll';
	var w2 = inner.offsetWidth;
	if (w1 == w2) w2 = outer.clientWidth;

	document.body.removeChild (outer);

	return (w1 - w2);
};
/**
 * Retrive worumsess attach ID from image, using img classes
 * @param {htmlObj} img Image to get ID from
 */
function toeGetImgAttachId(img) {
	var classesStr = jQuery(img).attr('class')
	,	aid = 0;
	if(classesStr && classesStr != '') {
		var matches = classesStr.match(/wp-image-(\d+)/);
		if(matches && matches[1]) {
			aid = parseInt(matches[1]);
		}
	}
	return aid;
}
function toeGetHashParams() {
	var hashArr = window.location.hash.split('#')
	,	res = [];
	for(var i in hashArr) {
		if(hashArr[i] && hashArr[i] != '') {
			res.push(hashArr[i]);
		}
	}
	return res;
}
/*Replace text in DOM functions*/
// Reusable generic function
function traverseElement(el, regex, textReplacerFunc, to) {
    // script and style elements are left alone
    if (!/^(script|style)$/.test(el.tagName)) {
        var child = el.lastChild;
        while (child) {
            if (child.nodeType == 1) {
                traverseElement(child, regex, textReplacerFunc, to);
            } else if (child.nodeType == 3) {
                textReplacerFunc(child, regex, to);
            }
            child = child.previousSibling;
        }
    }
}

// This function does the replacing for every matched piece of text
// and can be customized to do what you like
function textReplacerFunc(textNode, regex, to) {
	textNode.data = textNode.data.replace(regex, to);
}

// The main function
function replaceWords(html, words) {
    var container = document.createElement("div");
    container.innerHTML = html;

    // Replace the words one at a time to ensure each one gets matched
	for(var replace in words) {
		traverseElement(container, new RegExp(replace, "g"), textReplacerFunc, words[ replace ]);
	}
    return container.innerHTML;
}
/*****/
function toeSelectText(element) {
    var doc = document
	,	text = jQuery(element).get(0)
	,	range, selection;    
    if (doc.body.createTextRange) { //ms
        range = doc.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) { //all others
        selection = window.getSelection();        
        range = doc.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}
jQuery.fn.animationDuration = function(seconds, isMili) {
	if(isMili) {
		seconds = parseFloat(seconds) / 1000;
	}
	var secondsStr = seconds+ 's';
	return jQuery(this).css({
		'webkit-animation-duration': secondsStr
	,	'-moz-animation-duration': secondsStr
	,	'-o-animation-duration': secondsStr
	,	'animation-duration': secondsStr
	});
};
/**
 * Convert Date string (in common - mm/dd/yyyy) - to miliseconds
 * @param {string} strDate date string
 * @return {int} miliseconds
 */
function umsStrToMs(strDate) {
	var dateHours = strDate.split(' ');
	if(dateHours.length == 2) {
		strDate = dateHours[0]+ ' ';
		var hms = dateHours[1].split(':');
		
		for(var i = 0; i < 3; i++) {
			strDate += hms[ i ] ? hms[ i ] : '00';
			if(i < 2)
				strDate += ':';
		}
	}
	var date = new Date( str_replace(strDate, '-', '/') )
	,	res = 0;
	if(date) {
		res = date.getTime();
	}
	return res;
}
function twoArraysContainSameValue (arr1, arr2) {
	return arr2.some(function (v) {
		return arr1.indexOf(v) >= 0;
	});
}
// Simulates PHP's date function
Date.prototype.format=function(e){var t="";var n=Date.replaceChars;for(var r=0;r<e.length;r++){var i=e.charAt(r);if(r-1>=0&&e.charAt(r-1)=="\\"){t+=i}else if(n[i]){t+=n[i].call(this)}else if(i!="\\"){t+=i}}return t};Date.replaceChars={shortMonths:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],longMonths:["January","February","March","April","May","June","July","August","September","October","November","December"],shortDays:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],longDays:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],d:function(){return(this.getDate()<10?"0":"")+this.getDate()},D:function(){return Date.replaceChars.shortDays[this.getDay()]},j:function(){return this.getDate()},l:function(){return Date.replaceChars.longDays[this.getDay()]},N:function(){return this.getDay()+1},S:function(){return this.getDate()%10==1&&this.getDate()!=11?"st":this.getDate()%10==2&&this.getDate()!=12?"nd":this.getDate()%10==3&&this.getDate()!=13?"rd":"th"},w:function(){return this.getDay()},z:function(){var e=new Date(this.getFullYear(),0,1);return Math.ceil((this-e)/864e5)},W:function(){var e=new Date(this.getFullYear(),0,1);return Math.ceil(((this-e)/864e5+e.getDay()+1)/7)},F:function(){return Date.replaceChars.longMonths[this.getMonth()]},m:function(){return(this.getMonth()<9?"0":"")+(this.getMonth()+1)},M:function(){return Date.replaceChars.shortMonths[this.getMonth()]},n:function(){return this.getMonth()+1},t:function(){var e=new Date;return(new Date(e.getFullYear(),e.getMonth(),0)).getDate()},L:function(){var e=this.getFullYear();return e%400==0||e%100!=0&&e%4==0},o:function(){var e=new Date(this.valueOf());e.setDate(e.getDate()-(this.getDay()+6)%7+3);return e.getFullYear()},Y:function(){return this.getFullYear()},y:function(){return(""+this.getFullYear()).substr(2)},a:function(){return this.getHours()<12?"am":"pm"},A:function(){return this.getHours()<12?"AM":"PM"},B:function(){return Math.floor(((this.getUTCHours()+1)%24+this.getUTCMinutes()/60+this.getUTCSeconds()/3600)*1e3/24)},g:function(){return this.getHours()%12||12},G:function(){return this.getHours()},h:function(){return((this.getHours()%12||12)<10?"0":"")+(this.getHours()%12||12)},H:function(){return(this.getHours()<10?"0":"")+this.getHours()},i:function(){return(this.getMinutes()<10?"0":"")+this.getMinutes()},s:function(){return(this.getSeconds()<10?"0":"")+this.getSeconds()},u:function(){var e=this.getMilliseconds();return(e<10?"00":e<100?"0":"")+e},e:function(){return"Not Yet Supported"},I:function(){var e=null;for(var t=0;t<12;++t){var n=new Date(this.getFullYear(),t,1);var r=n.getTimezoneOffset();if(e===null)e=r;else if(r<e){e=r;break}else if(r>e)break}return this.getTimezoneOffset()==e|0},O:function(){return(-this.getTimezoneOffset()<0?"-":"+")+(Math.abs(this.getTimezoneOffset()/60)<10?"0":"")+Math.abs(this.getTimezoneOffset()/60)+"00"},P:function(){return(-this.getTimezoneOffset()<0?"-":"+")+(Math.abs(this.getTimezoneOffset()/60)<10?"0":"")+Math.abs(this.getTimezoneOffset()/60)+":00"},T:function(){var e=this.getMonth();this.setMonth(0);var t=this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/,"$1");this.setMonth(e);return t},Z:function(){return-this.getTimezoneOffset()*60},c:function(){return this.format("Y-m-d\\TH:i:sP")},r:function(){return this.toString()},U:function(){return this.getTime()/1e3}}