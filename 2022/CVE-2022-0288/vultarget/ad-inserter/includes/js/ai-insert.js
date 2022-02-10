ai_insert = function (insertion, selector, insertion_code) {
  if (selector.indexOf (':eq') != - 1) {
    var elements = jQuery (selector);
  } else var elements = document.querySelectorAll (selector);

//  Array.prototype.forEach.call (elements, function (element, index) {
  for (var index = 0, len = elements.length; index < len; index++) {
    var element = elements [index];

    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    if (element.hasAttribute ('id')) {
      selector_string = '#' + element.getAttribute ('id');
    } else
    if (element.hasAttribute ('class')) {
      selector_string = '.' + element.getAttribute ('class').replace (new RegExp (' ', 'g'), '.');
    } else
    selector_string = '';

    if (ai_debug) console.log ('');
    if (ai_debug) console.log ('AI INSERT', insertion, selector, '(' + element.tagName.toLowerCase() + selector_string + ')');

    var template = document.createElement ('div');
    template.innerHTML = insertion_code;

    var ai_selector_counter = template.getElementsByClassName ("ai-selector-counter")[0];
    if (ai_selector_counter != null) {
      ai_selector_counter.innerText = index + 1;
    }

    var ai_debug_name_ai_main = template.getElementsByClassName ("ai-debug-name ai-main")[0];
    if (ai_debug_name_ai_main != null) {
      var insertion_name = '';
      if (insertion == 'before') {
        insertion_name = ai_front.insertion_before;
      } else
      if (insertion == 'after') {
        insertion_name = ai_front.insertion_after;
      } else
      if (insertion == 'prepend') {
        insertion_name = ai_front.insertion_prepend;
      } else
      if (insertion == 'append') {
        insertion_name = ai_front.insertion_append;
      } else
      if (insertion == 'replace-content') {
        insertion_name = ai_front.insertion_replace_content;
      } else
      if (insertion == 'replace-element') {
        insertion_name = ai_front.insertion_replace_element;
      }

      if (selector_string.indexOf ('.ai-viewports') == - 1) {
        ai_debug_name_ai_main.innerText = insertion_name + ' ' + selector + ' (' + element.tagName.toLowerCase() + selector_string + ')';
      }
    }

    var range = document.createRange ();

    var fragment_ok = true;
    try {
      var fragment = range.createContextualFragment (template.innerHTML);
    }
    catch (err) {
      var fragment_ok = false;
      if (ai_debug) console.log ('AI INSERT', 'range.createContextualFragment ERROR:', err.message);
    }

    if (insertion == 'before') {
      if (!fragment_ok) {
        jQuery (template.innerHTML).insertBefore (jQuery (element));
      } else

      element.parentNode.insertBefore (fragment, element);
    } else
    if (insertion == 'after') {
      if (!fragment_ok) {
        jQuery (template.innerHTML).insertBefore (jQuery (element.nextSibling));
      } else

      element.parentNode.insertBefore (fragment, element.nextSibling);
    } else
    if (insertion == 'prepend') {
      if (!fragment_ok) {
        jQuery (template.innerHTML).insertBefore (jQuery (element.firstChild));
      } else

      element.insertBefore (fragment, element.firstChild);
    } else
    if (insertion == 'append') {
      if (!fragment_ok) {
        jQuery (template.innerHTML).appendTo (jQuery (element));
      } else

      element.insertBefore (fragment, null);
    } else
    if (insertion == 'replace-content') {
      element.innerHTML = '';

      if (!fragment_ok) {
        jQuery (template.innerHTML).appendTo (jQuery (element));
      } else

      element.insertBefore (fragment, null);
    } else
    if (insertion == 'replace-element') {
      if (!fragment_ok) {
        jQuery (template.innerHTML).insertBefore (jQuery (element));
      } else

      element.parentNode.insertBefore (fragment, element);

      element.parentNode.removeChild (element);
    }
//  });
  };
}

ai_insert_code = function (element) {

  function hasClass (element, cls) {
    if (element == null) return false;

    if (element.classList) return element.classList.contains (cls); else
      return (' ' + element.className + ' ').indexOf (' ' + cls + ' ') > - 1;
  }

  function addClass (element, cls) {
    if (element == null) return;

    if (element.classList) element.classList.add (cls); else
      element.className += ' ' + cls;
  }

  function removeClass (element, cls) {
    if (element == null) return;

    if (element.classList) element.classList.remove (cls); else
      element.className = element.className.replace (new RegExp ('(^|\\b)' + cls.split (' ').join ('|') + '(\\b|$)', 'gi'), ' ');
  }

  if (typeof element == 'undefined') return;

  var insertion = false;

  var ai_debug = typeof ai_debugging !== 'undefined'; // 2
//  var ai_debug = false;

  if (ai_debug) console.log ('AI INSERT ELEMENT class:', element.getAttribute ('class'));

  if (hasClass (element, 'no-visibility-check')) {
    var visible = true;
  } else var visible = !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);

  if (ai_debug) {
    var block = element.getAttribute ('data-block');
  }

  if (visible) {
    if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block, 'offsetWidth:', element.offsetWidth, 'offsetHeight:', element.offsetHeight, 'getClientRects().length:', element.getClientRects().length);

    var insertion_code = element.getAttribute ('data-code');
    var insertion_type = element.getAttribute ('data-insertion');
    var selector       = element.getAttribute ('data-selector');

    if (insertion_code != null) {
      if (insertion_type != null && selector != null) {
//        var selector_exists = document.querySelectorAll (selector).length;

        if (selector.indexOf (':eq') != - 1) {
          var selector_exists = jQuery (selector).length;
        } else var selector_exists = document.querySelectorAll (selector).length;

        if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block, insertion_type, selector, selector_exists ? '' : 'NOT FOUND');

        if (selector_exists) {
          ai_insert (insertion_type, selector, b64d (insertion_code));
          removeClass (element, 'ai-viewports');
        }
      } else {
          if (ai_debug) console.log ('AI ELEMENT VISIBLE: block', block);

          var range = document.createRange ();

          var fragment_ok = true;
          try {
            var fragment = range.createContextualFragment (b64d (insertion_code));
          }
          catch (err) {
            var fragment_ok = false;
            if (ai_debug) console.log ('AI INSERT NEXT', 'range.createContextualFragment ERROR:', err.message);
          }

          if (!fragment_ok) {
            jQuery (b64d (insertion_code)).insertBefore (jQuery (element.nextSibling));
          } else

          element.parentNode.insertBefore (fragment, element.nextSibling);

          removeClass (element, 'ai-viewports');
        }
    }

    insertion = true;

    // Should not be removed here as it is needed for tracking - removed there
//    var ai_check_block_data = element.getElementsByClassName ('ai-check-block');
//    if (typeof ai_check_block_data [0] != 'undefined') {
//      // Remove span
//      ai_check_block_data [0].parentNode.removeChild (ai_check_block_data [0]);
//    }
  } else {
      if (ai_debug) console.log ('AI ELEMENT NOT VISIBLE: block', block, 'offsetWidth:', element.offsetWidth, 'offsetHeight:', element.offsetHeight, 'getClientRects().length:', element.getClientRects().length);

      var debug_bar = element.previousElementSibling;

      if (hasClass (debug_bar, 'ai-debug-bar') && hasClass (debug_bar, 'ai-debug-script')) {
        removeClass (debug_bar, 'ai-debug-script');
        addClass (debug_bar, 'ai-debug-viewport-invisible');
      }

      removeClass (element, 'ai-viewports');
    }
  return insertion;
}

ai_insert_list_code = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    var inserted = ai_insert_code (ai_block_div);
    var wrapping_div = ai_block_div.closest ('div.AI_FUNCT_GET_BLOCK_CLASS_NAME');
    if (wrapping_div) {
      if (!inserted) {
        wrapping_div.removeAttribute ('data-ai');
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

    if (inserted) ai_process_elements ();
  }
}

ai_insert_viewport_code = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    var inserted = ai_insert_code (ai_block_div);

    ai_block_div.classList.remove (id);

    if (inserted) {
      var wrapping_div = ai_block_div.closest ('div.AI_FUNCT_GET_BLOCK_CLASS_NAME');

      if (wrapping_div != null) {
        var viewport_style = ai_block_div.getAttribute ('style');

        if (viewport_style != null) {
          wrapping_div.setAttribute ('style', wrapping_div.getAttribute ('style') + ' ' + viewport_style);
        }
      }
    }

    setTimeout (function () {
      ai_block_div.removeAttribute ('style');
    }, 2);

    ai_process_elements ();
  }
}

ai_insert_code_by_class = function (id) {
  var ai_block_div = document.getElementsByClassName (id) [0];

  if (typeof ai_block_div != 'undefined') {
    ai_insert_code (ai_block_div);

    ai_block_div.classList.remove (id);
  }
}

ai_insert_client_code = function (id, len) {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 3
//  var ai_debug = false;

  var ai_block_div = document.getElementsByClassName (id) [0];

  if (ai_debug) {
    var block   = ai_block_div.getAttribute ('data-block');
    console.log ('AI INSERT PROTECTED BLOCK', block, '.' + id);
  }

  if (typeof ai_block_div != 'undefined') {
    var insertion_code = ai_block_div.getAttribute ('data-code');

    if (insertion_code != null && ai_check_block () && ai_check_and_insert_block ()) {
      ai_block_div.setAttribute ('data-code', insertion_code.substring (Math.floor (len / 19)));
      ai_insert_code_by_class (id);
      ai_block_div.remove();
    }
  }
}

ai_process_elements_active = false;

function ai_process_elements () {
  if (!ai_process_elements_active)
    setTimeout (function() {
      ai_process_elements_active = false;

      if (typeof ai_process_rotations == 'function') {
        ai_process_rotations ();
      }

      if (typeof ai_process_lists == 'function') {
        ai_process_lists (jQuery (".ai-list-data"));
      }

      if (typeof ai_process_ip_addresses == 'function') {
        ai_process_ip_addresses (jQuery (".ai-ip-data"));
      }

      if (typeof ai_process_filter_hooks == 'function') {
        ai_process_filter_hooks (jQuery (".ai-filter-check"));
      }

      if (typeof ai_adb_process_blocks == 'function') {
        ai_adb_process_blocks ();
      }

      if (typeof ai_process_impressions == 'function' && ai_tracking_finished == true) {
        ai_process_impressions ();
      }
      if (typeof ai_install_click_trackers == 'function' && ai_tracking_finished == true) {
        ai_install_click_trackers ();
      }

      if (typeof ai_install_close_buttons == 'function') {
        ai_install_close_buttons (document);
      }
    }, 5);
  ai_process_elements_active = true;
}


/*globals jQuery,Window,HTMLElement,HTMLDocument,HTMLCollection,NodeList,MutationObserver */
/*exported Arrive*/
/*jshint latedef:false */

/*
 * arrive.js
 * v2.4.1
 * https://github.com/uzairfarooq/arrive
 * MIT licensed
 *
 * Copyright (c) 2014-2017 Uzair Farooq
 */
var Arrive = (function(window, $, undefined) {

  "use strict";

  if(!window.MutationObserver || typeof HTMLElement === 'undefined'){
    return; //for unsupported browsers
  }

  var arriveUniqueId = 0;

  var utils = (function() {
    var matches = HTMLElement.prototype.matches || HTMLElement.prototype.webkitMatchesSelector || HTMLElement.prototype.mozMatchesSelector
                  || HTMLElement.prototype.msMatchesSelector;

    return {
      matchesSelector: function(elem, selector) {
        return elem instanceof HTMLElement && matches.call(elem, selector);
      },
      // to enable function overloading - By John Resig (MIT Licensed)
      addMethod: function (object, name, fn) {
        var old = object[ name ];
        object[ name ] = function(){
          if ( fn.length == arguments.length ) {
            return fn.apply( this, arguments );
          }
          else if ( typeof old == 'function' ) {
            return old.apply( this, arguments );
          }
        };
      },
      callCallbacks: function(callbacksToBeCalled, registrationData) {
        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {
          // as onlyOnce param is true, make sure we fire the event for only one item
          callbacksToBeCalled = [callbacksToBeCalled[0]];
        }

        for (var i = 0, cb; (cb = callbacksToBeCalled[i]); i++) {
          if (cb && cb.callback) {
            cb.callback.call(cb.elem, cb.elem);
          }
        }

        if (registrationData && registrationData.options.onceOnly && registrationData.firedElems.length == 1) {
          // unbind event after first callback as onceOnly is true.
          registrationData.me.unbindEventWithSelectorAndCallback.call(
            registrationData.target, registrationData.selector, registrationData.callback);
        }
      },
      // traverse through all descendants of a node to check if event should be fired for any descendant
      checkChildNodesRecursively: function(nodes, registrationData, matchFunc, callbacksToBeCalled) {
        // check each new node if it matches the selector
        for (var i=0, node; (node = nodes[i]); i++) {
          if (matchFunc(node, registrationData, callbacksToBeCalled)) {
            callbacksToBeCalled.push({ callback: registrationData.callback, elem: node });
          }

          if (node.childNodes.length > 0) {
            utils.checkChildNodesRecursively(node.childNodes, registrationData, matchFunc, callbacksToBeCalled);
          }
        }
      },
      mergeArrays: function(firstArr, secondArr){
        // Overwrites default options with user-defined options.
        var options = {},
            attrName;
        for (attrName in firstArr) {
          if (firstArr.hasOwnProperty(attrName)) {
            options[attrName] = firstArr[attrName];
          }
        }
        for (attrName in secondArr) {
          if (secondArr.hasOwnProperty(attrName)) {
            options[attrName] = secondArr[attrName];
          }
        }
        return options;
      },
      toElementsArray: function (elements) {
        // check if object is an array (or array like object)
        // Note: window object has .length property but it's not array of elements so don't consider it an array
        if (typeof elements !== "undefined" && (typeof elements.length !== "number" || elements === window)) {
          elements = [elements];
        }
        return elements;
      }
    };
  })();


  // Class to maintain state of all registered events of a single type
  var EventsBucket = (function() {
    var EventsBucket = function() {
      // holds all the events

      this._eventsBucket    = [];
      // function to be called while adding an event, the function should do the event initialization/registration
      this._beforeAdding    = null;
      // function to be called while removing an event, the function should do the event destruction
      this._beforeRemoving  = null;
    };

    EventsBucket.prototype.addEvent = function(target, selector, options, callback) {
      var newEvent = {
        target:             target,
        selector:           selector,
        options:            options,
        callback:           callback,
        firedElems:         []
      };

      if (this._beforeAdding) {
        this._beforeAdding(newEvent);
      }

      this._eventsBucket.push(newEvent);
      return newEvent;
    };

    EventsBucket.prototype.removeEvent = function(compareFunction) {
      for (var i=this._eventsBucket.length - 1, registeredEvent; (registeredEvent = this._eventsBucket[i]); i--) {
        if (compareFunction(registeredEvent)) {
          if (this._beforeRemoving) {
              this._beforeRemoving(registeredEvent);
          }

          // mark callback as null so that even if an event mutation was already triggered it does not call callback
          var removedEvents = this._eventsBucket.splice(i, 1);
          if (removedEvents && removedEvents.length) {
            removedEvents[0].callback = null;
          }
        }
      }
    };

    EventsBucket.prototype.beforeAdding = function(beforeAdding) {
      this._beforeAdding = beforeAdding;
    };

    EventsBucket.prototype.beforeRemoving = function(beforeRemoving) {
      this._beforeRemoving = beforeRemoving;
    };

    return EventsBucket;
  })();


  /**
   * @constructor
   * General class for binding/unbinding arrive and leave events
   */
  var MutationEvents = function(getObserverConfig, onMutation) {
    var eventsBucket    = new EventsBucket(),
        me              = this;

    var defaultOptions = {
      fireOnAttributesModification: false
    };

    // actual event registration before adding it to bucket
    eventsBucket.beforeAdding(function(registrationData) {
      var
        target    = registrationData.target,
        observer;

      // mutation observer does not work on window or document
      if (target === window.document || target === window) {
        target = document.getElementsByTagName("html")[0];
      }

      // Create an observer instance
      observer = new MutationObserver(function(e) {
        onMutation.call(this, e, registrationData);
      });

      var config = getObserverConfig(registrationData.options);

      observer.observe(target, config);

      registrationData.observer = observer;
      registrationData.me = me;
    });

    // cleanup/unregister before removing an event
    eventsBucket.beforeRemoving(function (eventData) {
      eventData.observer.disconnect();
    });

    this.bindEvent = function(selector, options, callback) {
      options = utils.mergeArrays(defaultOptions, options);

      var elements = utils.toElementsArray(this);

      for (var i = 0; i < elements.length; i++) {
        eventsBucket.addEvent(elements[i], selector, options, callback);
      }
    };

    this.unbindEvent = function() {
      var elements = utils.toElementsArray(this);
      eventsBucket.removeEvent(function(eventObj) {
        for (var i = 0; i < elements.length; i++) {
          if (this === undefined || eventObj.target === elements[i]) {
            return true;
          }
        }
        return false;
      });
    };

    this.unbindEventWithSelectorOrCallback = function(selector) {
      var elements = utils.toElementsArray(this),
          callback = selector,
          compareFunction;

      if (typeof selector === "function") {
        compareFunction = function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.callback === callback) {
              return true;
            }
          }
          return false;
        };
      }
      else {
        compareFunction = function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector) {
              return true;
            }
          }
          return false;
        };
      }
      eventsBucket.removeEvent(compareFunction);
    };

    this.unbindEventWithSelectorAndCallback = function(selector, callback) {
      var elements = utils.toElementsArray(this);
      eventsBucket.removeEvent(function(eventObj) {
          for (var i = 0; i < elements.length; i++) {
            if ((this === undefined || eventObj.target === elements[i]) && eventObj.selector === selector && eventObj.callback === callback) {
              return true;
            }
          }
          return false;
      });
    };

    return this;
  };


  /**
   * @constructor
   * Processes 'arrive' events
   */
  var ArriveEvents = function() {
    // Default options for 'arrive' event
    var arriveDefaultOptions = {
      fireOnAttributesModification: false,
      onceOnly: false,
      existing: false
    };

    function getArriveObserverConfig(options) {
      var config = {
        attributes: false,
        childList: true,
        subtree: true
      };

      if (options.fireOnAttributesModification) {
        config.attributes = true;
      }

      return config;
    }

    function onArriveMutation(mutations, registrationData) {
      mutations.forEach(function( mutation ) {
        var newNodes    = mutation.addedNodes,
            targetNode = mutation.target,
            callbacksToBeCalled = [],
            node;

        // If new nodes are added
        if( newNodes !== null && newNodes.length > 0 ) {
          utils.checkChildNodesRecursively(newNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);
        }
        else if (mutation.type === "attributes") {
          if (nodeMatchFunc(targetNode, registrationData, callbacksToBeCalled)) {
            callbacksToBeCalled.push({ callback: registrationData.callback, elem: targetNode });
          }
        }

        utils.callCallbacks(callbacksToBeCalled, registrationData);
      });
    }

    function nodeMatchFunc(node, registrationData, callbacksToBeCalled) {
      // check a single node to see if it matches the selector
      if (utils.matchesSelector(node, registrationData.selector)) {
        if(node._id === undefined) {
          node._id = arriveUniqueId++;
        }
        // make sure the arrive event is not already fired for the element
        if (registrationData.firedElems.indexOf(node._id) == -1) {
          registrationData.firedElems.push(node._id);

          return true;
        }
      }

      return false;
    }

    arriveEvents = new MutationEvents(getArriveObserverConfig, onArriveMutation);

    var mutationBindEvent = arriveEvents.bindEvent;

    // override bindEvent function
    arriveEvents.bindEvent = function(selector, options, callback) {

      if (typeof callback === "undefined") {
        callback = options;
        options = arriveDefaultOptions;
      } else {
        options = utils.mergeArrays(arriveDefaultOptions, options);
      }

      var elements = utils.toElementsArray(this);

      if (options.existing) {
        var existing = [];

        for (var i = 0; i < elements.length; i++) {
          var nodes = elements[i].querySelectorAll(selector);
          for (var j = 0; j < nodes.length; j++) {
            existing.push({ callback: callback, elem: nodes[j] });
          }
        }

        // no need to bind event if the callback has to be fired only once and we have already found the element
        if (options.onceOnly && existing.length) {
          return callback.call(existing[0].elem, existing[0].elem);
        }

        setTimeout(utils.callCallbacks, 1, existing);
      }

      mutationBindEvent.call(this, selector, options, callback);
    };

    return arriveEvents;
  };


  /**
   * @constructor
   * Processes 'leave' events
   */
  var LeaveEvents = function() {
    // Default options for 'leave' event
    var leaveDefaultOptions = {};

    function getLeaveObserverConfig() {
      var config = {
        childList: true,
        subtree: true
      };

      return config;
    }

    function onLeaveMutation(mutations, registrationData) {
      mutations.forEach(function( mutation ) {
        var removedNodes  = mutation.removedNodes,
            callbacksToBeCalled = [];

        if( removedNodes !== null && removedNodes.length > 0 ) {
          utils.checkChildNodesRecursively(removedNodes, registrationData, nodeMatchFunc, callbacksToBeCalled);
        }

        utils.callCallbacks(callbacksToBeCalled, registrationData);
      });
    }

    function nodeMatchFunc(node, registrationData) {
      return utils.matchesSelector(node, registrationData.selector);
    }

    leaveEvents = new MutationEvents(getLeaveObserverConfig, onLeaveMutation);

    var mutationBindEvent = leaveEvents.bindEvent;

    // override bindEvent function
    leaveEvents.bindEvent = function(selector, options, callback) {

      if (typeof callback === "undefined") {
        callback = options;
        options = leaveDefaultOptions;
      } else {
        options = utils.mergeArrays(leaveDefaultOptions, options);
      }

      mutationBindEvent.call(this, selector, options, callback);
    };

    return leaveEvents;
  };


  var arriveEvents = new ArriveEvents(),
      leaveEvents  = new LeaveEvents();

  function exposeUnbindApi(eventObj, exposeTo, funcName) {
    // expose unbind function with function overriding
    utils.addMethod(exposeTo, funcName, eventObj.unbindEvent);
    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorOrCallback);
    utils.addMethod(exposeTo, funcName, eventObj.unbindEventWithSelectorAndCallback);
  }

  /*** expose APIs ***/
  function exposeApi(exposeTo) {
    exposeTo.arrive = arriveEvents.bindEvent;
    exposeUnbindApi(arriveEvents, exposeTo, "unbindArrive");

    exposeTo.leave = leaveEvents.bindEvent;
    exposeUnbindApi(leaveEvents, exposeTo, "unbindLeave");
  }

  if ($) {
    exposeApi($.fn);
  }
  exposeApi(HTMLElement.prototype);
  exposeApi(NodeList.prototype);
  exposeApi(HTMLCollection.prototype);
  exposeApi(HTMLDocument.prototype);
  exposeApi(Window.prototype);

  var Arrive = {};
  // expose functions to unbind all arrive/leave events
  exposeUnbindApi(arriveEvents, Arrive, "unbindAllArrive");
  exposeUnbindApi(leaveEvents, Arrive, "unbindAllLeave");

  return Arrive;

})(window, typeof jQuery === 'undefined' ? null : jQuery, undefined);

