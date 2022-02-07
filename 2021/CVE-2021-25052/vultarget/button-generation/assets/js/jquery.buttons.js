/* ========= INFORMATION ============================
- document:  Button Generator!
- author:    Wow-Company
- copyright: 2019 Wow-Company
- version:   1.0
- email:     support@wow-company.com
==================================================== */
'use strict';

function btnaction(id) {
  let data = 'action=btg_count&count_type=action&tool_id=' + id;
  jQuery.post(btg_count.ajaxurl, data, function (response) {
    const button = jQuery('.btg-button-' + id);
    let action = button.attr('data-action');
    let url = button.attr('data-url');

    if (action === 'link') {
      if (url !== undefined) {
        window.location.href = url;
      }
    } else if (action === 'share') {
      if (url !== undefined) {
        let params = 'width=550, height=450, top=' + ((screen.height - 450) / 2) + ', left=' + ((screen.width - 550) / 2) + ' scrollbars=0, resizable=1, menubar=0, toolbar=0, status=0';
        window.open(url, '_blank', params);
      }
    } else if (action === 'totop') {
      jQuery('body,html').animate({
        scrollTop: 0
      }, 777);
    } else if (action === 'totop') {
      jQuery('body,html').animate({
        scrollTop: 0
      }, 777);
    } else if (action === 'print') {
      window.print();
    } else if (action === 'smoothscroll') {
      jQuery('html, body').animate({
        scrollTop: jQuery(url).offset().top,
      }, 777);
    }

    if (jQuery('.btg-button-' + id + ' .badge').hasClass('btg-counter')) {
      if (response === 'OK') {
        let button_counter = jQuery('.btg-button-' + id + ' .badge').text();
        jQuery('.btg-button-' + id + ' .btg-counter').text(button_counter * 1 + 1);
      }
    }

  });
}
