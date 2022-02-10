/**
 * A truncated version of essential classes in the Xdn namespace.
 * Requires Xdn.Class.
 * @author Xedin Unknown <xedin.unknown@gmail.com>
 */

;(function($, window, document, undefined) {
    // This is the base, top level namespace
    window.Xdn = window.Xdn || {};
    
    // Allows easy namespacing of classes
    Xdn.assignNamespace = function (object, ns, overwrite) {
        if( !object ) return;
        
        if( (typeof object) === 'string' && !ns ) {
            ns = object;
            object = this;
        }

        ns = ns.split('.');
        var obj, base;
        for( var i=0; i<(ns.length-1); i++ ) {
            base = i ? obj : window;
            base[ns[i]] = base[ns[i]] || {};
            obj = base[ns[i]];
        }
        
        if( obj && !overwrite && obj[ns[i]] && $.isPlainObject(obj[ns[i]]) ) {
            object = $.extend(object, obj[ns[i]]);
        }
        obj[ns[i]] = object;
    };
    
    // Prevents errors in browsers that do not have a `console` global
    !window.console && (window.console = {
        log:            function() {},
        info:           function() {},
        warn:           function() {},
        error:          function() {}
    });
})(jQuery, top, document);

/* Xdn.Object */
;(function($, window, document, undefined) {
    
    var Xdn_Object = Xdn.Class.extend(
    /**
     * @lends Xdn.Object
     */
    {
        _data: {},
        
        init: function(data) {
            this._data = {};
            data && (this._data = data);
        },
        
        getData: function(key) {
            return key ? this._data[key] : this._data;
        },
        
        setData: function(key, value) {
            if( !value ) {
                this._data = key;
                return this;
            }
            
            this._data[key.toString()] = value;
            return this;
        },
        
        unsData: function(key) {
            if( !key ) {
                this._data = {};
                return this;
            }
            
            delete this._data[key];
        },
        
        addData: function(key, value) {
            if( value ) {
                this.setData(key, value);
                return this;
            }
            
            this.setData($.extend({}, this.getData(), key));
        },
        
        clone: function(additionalData) {
            var newObject = new Xdn.Object(this.getData());
            additionalData && newObject.addData(additionalData);
            return newObject;
        },
        
        _beforeMix:             function(mixin) {
            return mixin;
        },
        
        _afterMix:              function(mixin) {
            return this;
        },
        
        mix:                    function(mixin) {
            var self = this;
            mixin = mixin instanceof Array ? mixin : [mixin];
            mixin = this._beforeMix(mixin);
            $.each(mixin, function(i, mixin) {
                if( (/boolean|number|string|array/).test(typeof mixin) ) return true;
                Xdn.Object.augment(self, mixin);
            });
            this._afterMix(mixin);
            
            return this;
        },
        
        // Dummy function for mixin initialization. To be implemented in mixin
        _mix: function() {
        }
    });
    
    Xdn_Object.find = function(object, value, one) {
        one = one && true;
        var result = [];
        $.each(object, function(k, v) {
            var end = v == value && result.push(k) > 1 && one;
            if( end ) return false;
        });
        
        return one ? result : result[0];
    };
    
    Xdn_Object.augment = function(destination, source) {
        for(var prop in source) {
            if( !source.hasOwnProperty(prop) ) continue;
            destination[prop] = typeof(destination[prop]) !== 'undefined' ?
            (function(prop) {
                var fn = source[prop],
                    _super = destination[prop];
                return function() {
                    // Save any _super variable that already existed
                    var tmp = this._super,
                        result;

                    this._super = _super;
                    result = fn.apply(this, arguments);

                    // Restore _super
                    this._super = tmp;
                    return result;
                };
            })(prop) :
            source[prop];
        }

        return destination;
    };
    
    /**
     * @name Xdn.Object
     * @class
     */
    Xdn.assignNamespace(Xdn_Object, 'Xdn.Object');
    
    Xdn.Object.camelize = function(string, separator) {
        separator = separator || '_';
        var ex = new RegExp(separator+'([a-zA-Z])', 'g');
        return string.replace(ex, function (g) { return g[1].toUpperCase(); });
    }
    
})(jQuery, top, document);

/* Xdn.Options */
;(function($, window, document, undefined) {
    
    var Xdn_Options = Xdn.Object.extend({
        read: function(key) {
            return this.getData(key);
        },
        
        write: function(key, value) {
            this.setData(key, value);
            return this;
        },
        
        unset: function(key) {
            this.unsData(key);
            return this;
        },
        
        extend: function(key, value) {
            this.addData(key, value);
            return this;
        },
        
        configure: function(key, value, deep) {
            if( value && !$.isPlainObject(key) ) {
                key = (function(key, value) { var newKey = {}; newKey[key] = value; return newKey; })(key, value);
            }
            
            var args = [{}, key, this.read()];
            deep && args.unshift(true);
            
            this.write($.extend.apply($, args));
        }
    });
    
    Xdn.assignNamespace(Xdn_Options, 'Xdn.Options');    
})(jQuery, top, document);

/* Xdn.Object.Configurable */
;(function($, window, document, undefined) {
    
    var Xdn_Object_Configurable = Xdn.Object.extend({
        _options: null,
        
        init: function(options) {
            this._super();
            this._options = new Xdn.Options();
            $.isPlainObject(options) && this.setOptions(options);
            this._init();
        },
        
        _init: function() {
            
        },
        
        getOptions: function(key) {
            return key ? this._options.read(key) : this._options;
        },
        
        setOptions: function(key, value) {
            this.getOptions().write(key, value);
            return this;
        },
        
        unsetOptions: function(key) {
            this.getOptions().unset(key);
            return this;
        },
        
        mix:            function(mixin) {
            mixin = mixin || this.getOption('mixins');
            this._super(mixin);
            return this;
        }
    });
    
    Xdn.assignNamespace(Xdn_Object_Configurable, 'Xdn.Object.Configurable');    
})(jQuery, top, document);

