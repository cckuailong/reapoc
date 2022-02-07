/* ========= INFORMATION ============================
- document:  Button Generator!
- author:    Wow-Company
- copyright: 2019 Wow-Company
- version:   1.0
- email:     support@wow-company.com
==================================================== */

'use strict';

(function ($) {
  $(document).on('click', '.wow-plugin-notice .notice-dismiss', function () {
    $.ajax({
      url: ajaxurl,
      data: {
        action: 'float_menu_notice_action'
      }
    })
  })
})(jQuery);