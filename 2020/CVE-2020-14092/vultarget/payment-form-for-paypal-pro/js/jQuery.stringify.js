fbuilderjQuery = (typeof fbuilderjQuery != 'undefined' ) ? fbuilderjQuery : jQuery;

fbuilderjQuery(function(){
    (function($) {
        $.extend({
            
            stringifyXX  : function stringifyXX(obj) {
                var enc  = function(param) {
                    var escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
                        meta = {
                            '\b' : '\\b',
                            '\t' : '\\t',
                            '\n' : '\\n',
                            '\f' : '\\f',
                            '\r' : '\\r',
                            '"' : '\\"',
                            '\\' : '\\\\'
                        };
                                        
                    escapable.lastIndex = 0;
                    return escapable.test(param) ? param.replace(escapable, function (a) {
                        var c = meta[a];
                        return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                    }) : param;
                };
                
                var t = typeof (obj);
                if (t != "object" || obj === null) {
                    // simple data type
                    if (t == "string") obj = '"' + obj + '"';
                    return String(obj);
                } else {
                    // recurse array or object
                    var n, v, json = [], arr = (obj && obj.constructor == Array);

                    for (n in obj) {
                        v = obj[n];
                        t = typeof(v);
                        if (t!="function")
                        {
                            if (t == "string") v = '"' + enc(v) + '"'; else if (t == "object" && v !== null) v = $.stringifyXX(v);
                            json.push((arr ? "" : '"' + n + '":') + String(v));
                        }
                    }
                    return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
                }
            }
        });
    })(fbuilderjQuery);
});