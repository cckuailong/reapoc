jQuery (document).ready (function ($) {

  function findParent (tagname, element) {
    while (element) {
      if ((element.nodeName || element.tagName).toLowerCase() === tagname.toLowerCase ()) {
        return element;
      }
      element = element.parentNode;
    }
    return null;
  }

  function interceptClick (e) {
    e = e || event;
    var element = findParent ('a', e.target || e.srcElement);
    if (element) {
      e.preventDefault ();

      if (!ctrl_pressed) {
        var param = {
          'html_element_selection': block,
          'selector':               $('#ai-selector').val (),
          'input':                  settings_input
        };

        var form = document.createElement ("form");
        form.setAttribute ("method", "post");
        form.setAttribute ("action", element.href);
        form.setAttribute ("target", '_self');
        for (var i in param) {
           if (param.hasOwnProperty (i)) {
             var input = document.createElement ('input');
             input.type = 'hidden';
             input.name = i;
             input.value = encodeURI (param [i]);
             form.appendChild (input);
           }
        }
        document.body.appendChild (form);
        form.submit();
        document.body.removeChild (form);
      }
    }
  }

  function getElementSelector (el) {
    var selector = el.nodeName.toLowerCase ();

    if (el.hasAttribute ('id') && el.id != '') {
      selector = selector + '#' + el.id;
    }

    if (el.className) {
      classes = el.className.replace(/ai-selected|ai-highlighted/g, '').trim();
      if (classes) {
        selector = selector + '.' + classes.replace(/\s{2,}/g, ' ').trim().replace (/ /g, '.');
      }
    }

    return selector;
  }

  function getDomPath (el) {
    var stack = [];
    while (el.parentNode != null) {

      var sibCount = 0;
      var sibIndex = 0;
      for (var i = 0; i < el.parentNode.childNodes.length; i++) {
        var sib = el.parentNode.childNodes [i];
        if (sib.nodeName == el.nodeName) {
          if (sib === el) {
            sibIndex = sibCount;
          }
          sibCount++;
        }
      }
      if (el.hasAttribute ('id') && el.id != '') {
        stack.unshift (el.nodeName.toLowerCase () + '#' + el.id);
      } else if (sibCount > 1) {
        stack.unshift (el.nodeName.toLowerCase () + ':eq(' + sibIndex + ')');
      } else {
        stack.unshift (el.nodeName.toLowerCase ());
      }
      el = el.parentNode;
    }

    return stack.slice (1); // removes the html element
  }

  function getShortestPath (elements) {
    var stack = [];
    var found = false;
    elements.reverse ().forEach (function (element) {
      if (!found) stack.unshift (element);
      found = element.indexOf ('#') != -1;
    });
    return stack;
  }

  function cleanSelectors (selectors) {
    selectors = selectors.trim ();

    if (selectors.slice (0, 1) == ',') {
      selectors = selectors.slice (1, selectors.length);
    }

    if (selectors.slice (-1) == ',') {
      selectors = selectors.slice (0, selectors.length - 1);
    }

    return (selectors.trim ());
  }

  function wrapElement (element) {
    return '<kbd class="ai-html-element">' + element + '</kbd>';
  }

  function wrapElements (elements) {
    var html_elements = [];
    elements.forEach (function (element) {
      html_elements.push (wrapElement (element));
    });

    return html_elements;
  }

  function createClickableElements () {
    $(".ai-html-element").click (function () {
      var element_selector = $(this).text ();

      $('#ai-selector-element').html (wrapElement (element_selector));

      $('.ai-highlighted').removeClass ('ai-highlighted');
      $('.ai-selected').removeClass ('ai-selected');

      $(element_selector).addClass ('ai-selected');

      $('#ai-selector-data ' + element_selector).removeClass ('ai-selected');

      $('#ai-selector').val (element_selector);
    });
  }

  function loadFromSettings () {
    if (window.opener != null && !window.opener.closed) {
      $("#ai-selector").val (cleanSelectors (settings_selector));
      $("#ai-selector").trigger ("input");
    }
  }

  function applyToSettings (add) {
    if (window.opener != null && !window.opener.closed) {
      var settings = $(window.opener.document).contents ();
      var selector  = $("#ai-selector").val ();

      if (add) {
        var existing_selectors = settings.find (settings_input).val ().trim ();
        existing_selectors = cleanSelectors (existing_selectors);
        if (existing_selectors != '') {
          existing_selectors = existing_selectors + ', ';
        }
        selector = existing_selectors + selector;
      }

      settings.find (settings_input).val (selector);
    }
  }

  function changeAction () {
    if (ctrl_pressed) {
      $("#ai-use-button").hide ();
      $("#ai-add-button").show ();
    } else {
        $("#ai-use-button").show ();
        $("#ai-add-button").hide ();
      }
  }

  var block              = "AI_POST_HTML_ELEMENT_SELECTION";
  var settings_selector  = "AI_POST_SELECTOR";
  var settings_input     = "AI_POST_INPUT";
  var ctrl_pressed = false;
  var selected_element = null;
  var current_element = null;

  document.onclick = interceptClick;

//  var elements = $("a");
//  elements.click (function (event) {
//    console.log ('AI event', event);
//    interceptClick (event);
//  });

//    console.log ('AI event', document.getElementsByTagName ("A"));

//  var a_elements = document.getElementsByTagName ("A");
//  for (i = 0; i < a_elements.length; i++) {
////     console.log ('AI event', a_elements [i], event);
//   a_elements [i].addEventListener ("click", function (event){
////    interceptClick (event);
//    var element = $(event.target);
//    console.log ('AI CLICK', element.prop ("tagName"));
//   });
//  }

  $(document).keydown (function (event) {
    if (event.which == "17") {
      ctrl_pressed = true;
      changeAction ();

      if (current_element != null && current_element.prop ("tagName") == 'A') {
        $(current_element).trigger ('mouseover');
      }
    }
  });

  $(document).keyup (function() {
      ctrl_pressed = false;
      changeAction ();

      if (current_element != null && current_element.prop ("tagName") == 'A') {
        $(current_element).trigger ('mouseout');
      }
  });

  $('body').css ({'user-select': 'none', 'margin-top': '140px'});

  $('body').prepend ('<section id="ai-selector-data">' +
'<table>' +
'  <tbody>' +
'    <tr>' +
'      <td class="data-name">' + ai_front.element + '</td>' +
'      <td class="data-value"><section id="ai-selector-element"></section></td>' +
'      <td><button type="button" id="ai-cancel-button" style="min-width: 110px;" title="' + ai_front.cancel_element_selection + '"> ' + ai_front.cancel + ' </button></td>' +
'    </tr>' +
'    <tr>' +
'      <td>' + ai_front.path + '</td>' +
'      <td><section id="ai-selector-path"></section></td>' +
'      <td><button type="button" id="ai-parent-button" style="min-width: 110px;" title="' + ai_front.select_parent_element + '"> ' + ai_front.parent + ' </button></td>' +
'    </tr>' +
'    <tr>' +
'      <td>' + ai_front.selector + '</td>' +
'      <td style="width: 100%;"><input id="ai-selector" type="text" value="" maxlength="500" title="' + ai_front.css_selector + '" /></td>' +
'      <td><button type="button" id="ai-use-button" style="min-width: 110px;" title="' + ai_front.use_current_selector + '"> ' + ai_front.use + ' </button>' +
'          <button type="button" id="ai-add-button" style="min-width: 110px; display: none;" title="' + ai_front.add_current_selector + '"> ' + ai_front.add + ' </button></td>' +
'    </tr>' +
'  </tbody>' +
'</table>' +
'</section>');

  $('body').bind ('mouseover mouseout click', function (event) {
    var element = $(event.target);
    var elements = getDomPath (element [0]);
    var path = elements.join (' > ');

    if (path.indexOf ('ai-selector-data') != -1) {
      return;
    }

    if (element.hasClass ('ai-html-element')) {
      return;
    }

    switch (event.type) {
      case 'click':
        if (element.prop ("tagName") != 'A' || ctrl_pressed) {
          selected_element = element;

          $('#ai-selector-element').html (wrapElement (getElementSelector (element [0])));
          $('#ai-selector-path').html (wrapElements (elements).join (' > '));

          createClickableElements ();

          $('.ai-highlighted').removeClass ('ai-highlighted');
          $('.ai-selected').removeClass ('ai-selected');

          element.addClass ('ai-selected');

          $('#ai-selector').val (getShortestPath (elements).join (' > '));
        }
        break;
      case 'mouseover':
        current_element = element;
        if (element.prop ("tagName") != 'A' || ctrl_pressed) {
          element.addClass ('ai-highlighted');
        }
        break;
      case 'mouseout':
        element.removeClass ('ai-highlighted');
        break;
    }
  });

  $("#ai-selector").on ('input', function() {

    $('.ai-highlighted').removeClass ('ai-highlighted');
    $('.ai-selected').removeClass ('ai-selected');

    var selectors = cleanSelectors ($("#ai-selector").val ());
    $(selectors).addClass ('ai-selected');

    var elements = selectors.split (',');
    elements.forEach (function (element) {
      $('#ai-selector-data ' + element).removeClass ('ai-selected');
    });

    if (elements.length == 1 && $(selectors).length == 1) {
      selected_element = $(elements [0]);

      $('#ai-selector-element').html (wrapElement (getElementSelector (selected_element [0])));
      $('#ai-selector-path').html (wrapElements (getDomPath (selected_element [0])).join (' > '));

      createClickableElements ();
    } else {
        selected_element = null;
        $('#ai-selector-element').text ('');
        $('#ai-selector-path').text ('');
      }
  });

  window.onkeydown = function (event) {
    if (event.keyCode === 27 ) {
      window.close();
    }
  };

  loadFromSettings ();

  $("#ai-cancel-button").button ({
  }).click (function () {
    window.close();
  });

  $("#ai-parent-button").button ({
  }).click (function () {
    if (selected_element.prop ("tagName") != 'BODY') {
      selected_element = selected_element.parent ();
      selected_element.click ();
    }
  });

  $("#ai-use-button").button ({
  }).click (function () {
    applyToSettings (false);
    window.close();
  });

  $("#ai-add-button").button ({
  }).click (function () {
    applyToSettings (true);
    window.close();
  });

});
