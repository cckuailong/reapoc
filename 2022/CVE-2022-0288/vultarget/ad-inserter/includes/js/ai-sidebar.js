jQuery(document).ready(function($) {

  var ai_set_sidebars = function ($) {
    var sticky_widget_mode   = AI_FUNC_GET_STICKY_WIDGET_MODE;
    var sticky_widget_margin = AI_FUNC_GET_STICKY_WIDGET_MARGIN;
    var document_width = $(document).width();

    var ai_debug = typeof ai_debugging !== 'undefined'; // 1
//    var ai_debug = false;

    $(".ai-sticky-widget").each (function () {
      var widget = $(this);
      var widget_width = widget.width();

      if (ai_debug) console.log ('');
      if (ai_debug) console.log ("WIDGET:", widget.width (), widget.prop ("tagName"), widget.attr ("id"));

      var already_sticky_js = false;
      var sidebar = widget.parent ();
      while (sidebar.prop ("tagName") != "BODY") {

        if (sidebar.hasClass ('theiaStickySidebar')) {
          already_sticky_js = true;
          break;
        }

        if (ai_debug) console.log ("SIDEBAR:", sidebar.width (), sidebar.prop ("tagName"), sidebar.attr ("id"));

        var parent_element = sidebar.parent ();
        var parent_element_width = parent_element.width();
        if (parent_element_width > widget_width * 1.2 || parent_element_width > document_width / 2) break;
        sidebar = parent_element;
      }
      if (already_sticky_js) {
        if (ai_debug) console.log ("JS STICKY SIDEBAR ALREADY SET");
        return;
      }

      var new_sidebar_top = sidebar.offset ().top - widget.offset ().top + sticky_widget_margin;

      if (ai_debug) console.log ("NEW SIDEBAR TOP:", new_sidebar_top);

      if (sticky_widget_mode == 0) {
        // CSS
        if (sidebar.css ("position") != "sticky" || isNaN (parseInt (sidebar.css ("top"))) || sidebar.css ("top") < new_sidebar_top) {
          sidebar.css ("position", "sticky").css ("position", "-webkit-sticky").css ("top", new_sidebar_top);

          if (ai_debug) console.log ("CSS STICKY SIDEBAR, TOP:", new_sidebar_top);
        }
        else if (ai_debug) console.log ("CSS STICKY SIDEBAR ALREADY SET");
      } else {
          // Javascript
          sidebar.theiaStickySidebar({
            additionalMarginTop: new_sidebar_top,
            sidebarBehavior: 'stick-to-top',
          });

          if (ai_debug) console.log ("JS STICKY SIDEBAR, TOP:", new_sidebar_top);
        }
    });

  };

  if (typeof ai_sticky_sidebar_delay == 'undefined') {
    ai_sticky_sidebar_delay = 200;
  }

  setTimeout (function() {
    ai_set_sidebars ($);
  }, ai_sticky_sidebar_delay);
});


