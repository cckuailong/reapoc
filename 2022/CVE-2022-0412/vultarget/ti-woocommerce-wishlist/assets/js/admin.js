"use strict";

function TInvWL($, h) {
  this.pf = 'tinvwl';
  this.g = '_';
  this.ho = h || false;
  this.n = 'TInvWL';

  this.aj_act = function (a) {
    return [this.pf, a].join(this.g);
  };

  this._csel = function (a, b) {
    var b = b || '.';
    return '{0}{1}{2}'.format(b, this.pf, a);
  };

  this._tm = function (a) {
    var c = $('script#{0}[type=\'text/template\']'.format(a));

    if (c.length) {
      return c.html();
    }

    return '';
  };

  this.formElm = function () {
    $(this._csel('-form-onoff')).tiwl_onoff();
    $('input[type=checkbox][tiwl-show], input[type=checkbox][tiwl-hide]').tiwl_onoffblock();
    $('[tiwl-value][tiwl-show], [tiwl-value][tiwl-hide]').tiwl_byvalueblock();

    if ('undefined' !== typeof $.fn.wpColorPicker) {
      var calcLuminance = function calcLuminance(rgb) {
        var c = rgb.substring(1);

        var _rgb = parseInt(c, 16);

        var r = _rgb >> 16 & 0xff;
        var g = _rgb >> 8 & 0xff;
        var b = _rgb >> 0 & 0xff;
        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
      };

      var formColor = this._csel('-form-color');

      $(formColor).each(function () {
        var picker = $(this);
        var pickerWrap = $(this).closest('.tinvwl-color-picker');
        var eyedropper = pickerWrap.find('.tinvwl-eyedropper');
        picker.css('background-color', picker.val());

        if (175 < calcLuminance(picker.val())) {
          picker.css('color', '#000000');
        }

        picker.iris({
          mode: 'hsv',
          target: $(this).parent().parent(),
          change: function change(event, ui) {
            if (175 < calcLuminance(ui.color.toCSS())) {
              $(this).css('color', '#000000');
            } else {
              $(this).css('color', '');
            }

            $(this).css('background-color', ui.color.toCSS());
          }
        });
        pickerWrap.on('click', '.iris-square-value', function (e) {
          e.preventDefault();
          picker.iris('toggle');
        });
        eyedropper.on('click', function (e) {
          e.preventDefault();
          picker.iris('show');
        });
        picker.on('focusin', function () {
          picker.iris('show');
        });
      });
      $(document).on('click', function (e) {
        if (!$(e.target).is(formColor + ', .iris-picker, .iris-picker-inner, .iris-slider-offset, .tinvwl-eyedropper, .tinvwl-eyedropper .ftinvwl-eyedropper')) {
          $(formColor).iris('hide');
        } else {
          $(formColor).not($(e.target).closest('.tinvwl-color-picker').find(formColor)).iris('hide');
        }
      });
    }
  };

  this.wizard_page = function (a) {
    $(a).find('select').change(this._wizard_page_ch);
    this.wizard_page_ch($(a).find('select'));
  };

  this.wizard_page_ch = function (a) {
    var a = $(a),
        b = a.parent(this._csel('-page-select')),
        c = b.find('input[type=hidden]').val(),
        d = b.find(this._csel('-error-icon')),
        e = b.find(this._csel('-error-desc'));

    if ('' === a.val()) {
      if (0 == c) {
        b.addClass('tinvwl-error');
        d.show();
        e.show();
      }

      return;
    }

    b.removeClass('tinvwl-error');
    d.hide();
    e.hide();
  };

  this.pageElm = function () {
    $(this._csel('-header', 'div.')).prependTo('#wpbody-content');
    $(this._csel('-page-select')).each(this._wizard_page);
    $('.bulkactions [type=submit]').each(this._control_bulkactions);
    $('.action-search [type=submit]').each(this._control_search);
  };

  this.control_bulkactions = function (a) {
    $(a).on('click', this._control_bulkactions_ck);
  };

  this.control_bulkactions_ck = function (a, b) {
    var a = $(a),
        c = a.parents('.bulkactions').eq(0).find('[name=action]'),
        d = a.parents('form').eq(0);

    if (c) {
      if ('-1' === c.val()) {
        b.preventDefault();
      } else {
        if (!d.find('input[type=checkbox]:checked').length) {
          b.preventDefault();
        }
      }
    }
  };

  this.control_search = function (a) {
    $(a).on('click', this._control_search_ck);
  };

  this.control_search_ck = function (a, b) {
    var a = $(a),
        c = a.parents('.action-search').eq(0).find('[name=s]');

    if (c) {
      if ('' === c.val()) {
        b.preventDefault();
      }
    }
  };

  this.Run = function () {
    this.formElm();
    this.pageElm();
  };

  this.cg = function () {
    var n = this.n;

    if (this.ho) {
      var t = new Date();
      n = n + t.getFullYear() + t.getMonth() + t.getDate();
    }

    window[n] = this;
  };

  this.cg();

  if (!String.prototype.format) {
    String.prototype.format = function () {
      var args = arguments;
      return this.replace(/{(\d+)}/g, function (match, number) {
        return 'undefined' !== typeof args[number] ? args[number] : match;
      });
    };
  }

  (function (o) {
    var n = o.n,
        ho = o.ho,
        c = '';

    if (ho) {
      c = 't=new Date(),n=n+t.getFullYear()+t.getMonth()+t.getDate(),';
    }

    for (var i in o) {
      if ('function' === typeof o[i] && '_' !== i[0] && !o.hasOwnProperty('_' + i)) {
        eval('o._' + i + '=function(a,b,c,d){var n=\'' + n + '\',' + c + 'o=window[n]||null;if (o) {return o.' + i + '(this,a,b,c,d);};};');
      }
    }
  })(this);
}

(function ($) {
  $.fn.tiwl_onoff = function (so) {
    var sd = {
      value: {
        on: '',
        off: ''
      },
      class: 'tiwlform-onoff',
      wrap: 'container',
      button: 'button'
    },
        s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
          b1 = $('<div>').attr({
        class: s.class + '-' + s.button
      }),
          d1c = s.class + '-' + s.wrap,
          d1 = $('<div>').attr({
        id: a.attr('id') + '_' + s.wrap,
        class: d1c
      });

      if (!a.is('input')) {
        return a;
      }

      d1.attr('class', d1.attr('class') + ' ' + a.attr('class'));

      if (a.is(':disabled')) {
        d1.toggleClass('disabled', a.is(':disabled'));
        a.prop('disabled', false);
      }

      d1.toggleClass('checked', a.is(':checked'));
      a.hide().removeAttr('class').wrap(d1).before(b1);
      d1 = a.parent();
      a.on('change', function (e) {
        if (d1.hasClass('disabled')) {
          return e.preventDefault();
        }

        d1.toggleClass('checked', $(this).is(':checked'));
      });
      d1.on('click', function (e) {
        if (d1.hasClass('disabled')) {
          return e.preventDefault();
        }

        if (a.is(':enabled') && d1.hasClass('checked') === a.is(':checked')) {
          a.click();
        }
      });
      return a;
    });
  };

  $.fn.tiwl_onoffblock = function (so) {
    var sd = {
      onEachElm: function onEachElm() {},
      isChecked: function isChecked() {
        return $(this).is(':checked');
      }
    },
        s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
          setAction = function setAction() {
        var o = $(this),
            o_show = o.attr('tiwl-show'),
            o_hide = o.attr('tiwl-hide'),
            o_ch = s.isChecked.call(o),
            doAction = function doAction(o_, on) {
          o_ = o_.match(/[\w\d-\>\.\#\:\=\[\]]+/igm) || [];
          $.each(o_, function (k, v) {
            s.onEachElm.call($(v).toggle(on));
          });
        };

        if ('string' === typeof o_show) {
          doAction(o_show, o_ch);
        }

        if ('string' === typeof o_hide) {
          doAction(o_hide, !o_ch);
        }

        return o;
      };

      if (!a.is('input') || 'checkbox' != a.attr('type')) {
        return a;
      }

      $(this).on('change', setAction);
      return setAction.call(a);
    });
  };

  $.fn.tiwl_byvalueblock = function (so) {
    var sd = {
      onEachElm: function onEachElm() {},
      onClick: function onClick() {
        return $(this).val() == $(this).attr('tiwl-value');
      }
    },
        s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      var a = $(this),
          setAction = function setAction(s) {
        var o = $(this),
            o_show = o.attr('tiwl-show'),
            o_hide = o.attr('tiwl-hide'),
            o_ch = s.onClick.call(o),
            doAction = function doAction(o_, on) {
          o_ = o_.match(/[\w\d-\>\.\#\:\=\[\]]+/igm) || [];
          $.each(o_, function (k, v) {
            s.onEachElm.call($(v).toggle(on));
          });
        };

        if ('string' === typeof o_show) {
          doAction(o_show, o_ch);
        }

        if ('string' === typeof o_hide) {
          doAction(o_hide, !o_ch);
        }

        return o;
      };

      if (!a.is('input') && !a.is('select')) {
        return a;
      }

      $(this).on('change', function () {
        setAction.call(this, s);
      });
      return setAction.call(a, s);
    });
  };

  var a = new TInvWL($);
  $(document).ready(function () {
    a.Run();
    jQuery('input[name="general-show_notice"]').change(function () {
      var o = jQuery(this),
          a = !o.is(':checked'),
          b = jQuery('input[name="general-redirect_require_login"]');

      if (a && !b.is(':checked')) {
        b.click().trigger('change');
      }

      b.closest('.tiwlform-onoff-container').toggleClass('disabled', a);
    }).change();
    $('.tablenav').each(function () {
      var tablenav = $(this);

      if (!$.trim(tablenav.find('.alignleft').html()).length) {
        tablenav.find('.alignleft').remove();
      }

      if (!$.trim(tablenav.find('.alignright').html()).length || tablenav.find('.tablenav-pages').hasClass('one-page')) {
        tablenav.find('.alignright').remove();
        tablenav.find('.tinv-wishlist-clear').remove();
      }

      if (!$.trim(tablenav.html()).length) {
        tablenav.remove();
      }
    });
    $('.tablenav .bulkactions select').addClass('tinvwl-select grey').wrap('<span class="tinvwl-select-wrap">').parent().append('<span class="tinvwl-caret"><span></span></span>');
    $('.tablenav .bulkactions .button.action, .tablenav #search-submit').removeClass('button').addClass('tinvwl-btn grey');
    $('.tinvwl-modal-btn').on('click', function () {
      $(this).next('.tinvwl-modal').addClass('tinvwl-modal-open');
    });
    $('.tinvwl-overlay, .tinvwl-close-modal, .tinvwl_button_close').on('click', function (e) {
      e.preventDefault();
      $(this).parents('.tinvwl-modal:first').removeClass('tinvwl-modal-open');
    });

    if ('undefined' !== typeof $.fn.popover) {
      var popover = $('.tinvwl-help');
      popover.popover({
        content: function content() {
          return $(this).closest('.tinvwl-info-wrap').find('.tinvwl-info-desc').html();
        }
      });
      popover.on('click', function () {
        $(this).popover('toggle');
      });
      popover.on('focusout', function () {
        $(this).popover('hide');
      });
      $(window).on('resize', function () {
        popover.popover('hide');
      });
    }

    $('body').on('click', '.tinvwl-confirm-reset', function (e) {
      e.preventDefault();
      var a = confirm(tinvwl_comfirm.text_comfirm_reset);

      if (a) {
        $(this).removeClass('tinvwl-confirm-reset').trigger('click');
      }
    });
  });
  $(document).on('click', '.tinvwl-chat-notice .notice-dismiss', function (e) {
    $.post(tinvwl_comfirm.ajax_url, {
      action: 'tinvwl_admin_chat_notice'
    });
  });
})(jQuery);