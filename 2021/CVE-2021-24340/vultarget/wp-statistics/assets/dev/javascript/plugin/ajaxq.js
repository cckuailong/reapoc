// AjaxQ jQuery Plugin
// Copyright (c) 2012 Foliotek Inc.
// MIT License
// https://github.com/Foliotek/ajaxq
// Uses CommonJS, AMD or browser globals to create a jQuery plugin.
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var queues = {};
    var activeReqs = {};

    // Register an $.ajaxq function, which follows the $.ajax interface, but allows a queue name which will force only one request per queue to fire.
    // opts can be the regular $.ajax settings plainObject, or a callback returning the settings object, to be evaluated just prior to the actual call to $.ajax.
    $.ajaxq = function(qname, opts) {

        if (typeof opts === "undefined") {
            throw ("AjaxQ: queue name is not provided");
        }

        // Will return a Deferred promise object extended with success/error/callback, so that this function matches the interface of $.ajax
        var deferred = $.Deferred(),
            promise = deferred.promise();

        promise.success = promise.done;
        promise.error = promise.fail;
        promise.complete = promise.always;

        // Check whether options are to be evaluated at call time or not.
        var deferredOpts = typeof opts === 'function';
        // Create a deep copy of the arguments, and enqueue this request.
        var clonedOptions = !deferredOpts ? $.extend(true, {}, opts) : null;
        enqueue(function() {
            // Send off the ajax request now that the item has been removed from the queue
            var jqXHR = $.ajax.apply(window, [deferredOpts ? opts() : clonedOptions]);

            // Notify the returned deferred object with the correct context when the jqXHR is done or fails
            // Note that 'always' will automatically be fired once one of these are called: http://api.jquery.com/category/deferred-object/.
            jqXHR.done(function() {
                deferred.resolve.apply(this, arguments);
            });
            jqXHR.fail(function() {
                deferred.reject.apply(this, arguments);
            });

            jqXHR.always(dequeue); // make sure to dequeue the next request AFTER the done and fail callbacks are fired

            return jqXHR;
        });

        return promise;


        // If there is no queue, create an empty one and instantly process this item.
        // Otherwise, just add this item onto it for later processing.
        function enqueue(cb) {
            if (!queues[qname]) {
                queues[qname] = [];
                var xhr = cb();
                activeReqs[qname] = xhr;
            }
            else {
                queues[qname].push(cb);
            }
        }

        // Remove the next callback from the queue and fire it off.
        // If the queue was empty (this was the last item), delete it from memory so the next one can be instantly processed.
        function dequeue() {
            if (!queues[qname]) {
                return;
            }
            var nextCallback = queues[qname].shift();
            if (nextCallback) {
                var xhr = nextCallback();
                activeReqs[qname] = xhr;
            }
            else {
                delete queues[qname];
                delete activeReqs[qname];
            }
        }
    };

    // Register a $.postq and $.getq method to provide shortcuts for $.get and $.post
    // Copied from jQuery source to make sure the functions share the same defaults as $.get and $.post.
    $.each( [ "getq", "postq" ], function( i, method ) {
        $[ method ] = function( qname, url, data, callback, type ) {

            if ( $.isFunction( data ) ) {
                type = type || callback;
                callback = data;
                data = undefined;
            }

            return $.ajaxq(qname, {
                type: method === "postq" ? "post" : "get",
                url: url,
                data: data,
                success: callback,
                dataType: type
            });
        };
    });

    var isQueueRunning = function(qname) {
        return (queues.hasOwnProperty(qname) && queues[qname].length > 0) || activeReqs.hasOwnProperty(qname);
    };

    var isAnyQueueRunning = function() {
        for (var i in queues) {
            if (isQueueRunning(i)) return true;
        }
        return false;
    };

    $.ajaxq.isRunning = function(qname) {
        if (qname) return isQueueRunning(qname);
        else return isAnyQueueRunning();
    };

    $.ajaxq.getActiveRequest = function(qname) {
        if (!qname) throw ("AjaxQ: queue name is required");

        return activeReqs[qname];
    };

    $.ajaxq.abort = function(qname) {
        if (!qname) throw ("AjaxQ: queue name is required");
        
        var current = $.ajaxq.getActiveRequest(qname);
        delete queues[qname];
        delete activeReqs[qname];
        if (current) current.abort();
    };

    $.ajaxq.clear = function(qname) {
        if (!qname) {
            for (var i in queues) {
                if (queues.hasOwnProperty(i)) {
                    queues[i] = [];
                }
            }
        }
        else {
            if (queues[qname]) {
                queues[qname] = [];
            }
        }
    };

}));
