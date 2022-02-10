jQuery (function ($) {

  $(document).ready(function($) {

    function show_review_notice () {
      $('.ai-notice').fadeIn ("fast", function() {
        $(this).css ('display', 'table');
      });
    }

    if (typeof ajaxurl !== 'undefined') {
//      var nonce = $('.ai-notice[nonce]').attr ('nonce');
      var nonce = $('.ai-notice[data-value]').attr ('data-value');

      if (typeof nonce !== 'undefined') {
        nonce = atob (nonce);
        $.ajax (ajaxurl, {
          type: 'POST',
          data: {
            action:         'ai_ajax_backend',
            ai_check:       nonce,
            'notice-check': nonce
          }
        }).done (function (data) {

//          console.log ('AI NOTICE CHECK', nonce, data);

          if (data == nonce) {
            setTimeout (show_review_notice, 500);
          }
        });
      }
    }
  });

  $(document).on ('click', '.ai-notice .ai-notice-dismiss', function () {
    var ai_debug = parseInt ($('#ai-data').attr ('js_debugging'));
    var notice_div = $(this).closest ('.ai-notice');
    var nonce = atob (notice_div.attr ('data-value'));
    var notice = notice_div.data ('notice');
    var action = $(this).data ('notice');

    if (ai_debug) console.log ('AI NOTICE CLICK', notice, action);

    notice_div.hide ();

    // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    $.ajax (ajaxurl, {
      type: 'POST',
      data: {
        action:   'ai_ajax_backend',
        ai_check: nonce,
        notice:   notice,
        click:    action,
      }
    });

  });
});
