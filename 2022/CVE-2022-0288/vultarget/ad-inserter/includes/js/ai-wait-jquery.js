function ai_run_scripts () {
AI_JS_CODE=1
}

//function ai_load_translations () {
//AI_JS_CODE=2
//}

function ai_wait_for_jquery () {
  var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//  var ai_debug = false;

  function ai_get_script (url, action) {
    var script = document.createElement ('script');
    script.src = url;

    var head = document.getElementsByTagName ('head')[0],
        done = false;

    // Attach handlers for all browsers
    script.onload = script.onreadystatechange = function () {
      if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
        done = true;

        if (ai_debug) console.log ('AI jQuery LOADED');

        if (action) {
          action ();
        }

        script.onload = script.onreadystatechange = null;
        head.removeChild (script);
      };
    };

    head.appendChild (script);
  };

//  if (window.jQuery) {
  if (window.jQuery && window.jQuery.fn) {
    if (ai_debug) console.log ('AI jQuery READY');

    ai_run_scripts ();
  } else {
      ai_jquery_waiting_counter ++;

      if (ai_debug) console.log ('AI jQuery NOT READY:', ai_jquery_waiting_counter);

      if (ai_jquery_waiting_counter == 4) {

        if (ai_debug) console.log ('AI jQuery LOADING...');

        ai_get_script ('AI_JS_JQUERY0', function () {
          ai_get_script ('AI_JS_JQUERY1', null);
        });

//        ai_load_translations ();
      }

      if (ai_jquery_waiting_counter < 30) {
        setTimeout (function () {ai_wait_for_jquery ()}, 50);
      } else if (ai_debug) console.log ('AI jQuery NOT LOADED');
    }
  }
ai_jquery_waiting_counter = 0;
ai_wait_for_jquery ();
