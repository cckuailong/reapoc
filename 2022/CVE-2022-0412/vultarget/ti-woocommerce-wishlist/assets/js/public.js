"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

// Add to wishlist
(function ($) {
  //Add to wishlist main function.
  $.fn.tinvwl_to_wishlist = function (so) {
    var sd = {
      api_url: window.location.href.split('?')[0],
      text_create: window.tinvwl_add_to_wishlist['text_create'],
      text_already_in: window.tinvwl_add_to_wishlist['text_already_in'],
      class: {
        dialogbox: '.tinvwl_add_to_select_wishlist',
        select: '.tinvwl_wishlist',
        newtitle: '.tinvwl_new_input',
        dialogbutton: '.tinvwl_button_add'
      },
      redirectTimer: null,
      onPrepareList: function onPrepareList() {},
      onGetDialogBox: function onGetDialogBox() {},
      onPrepareDialogBox: function onPrepareDialogBox() {
        if (!$('body > .tinv-wishlist').length) {
          $('body').append($('<div>').addClass('tinv-wishlist'));
        }

        $(this).appendTo('body > .tinv-wishlist');
      },
      onCreateWishList: function onCreateWishList(wishlist) {
        $(this).append($('<option>').html(wishlist.title).val(wishlist.ID).toggleClass('tinv_in_wishlist', wishlist.in));
      },
      onSelectWishList: function onSelectWishList() {},
      onDialogShow: function onDialogShow(modal) {
        $(modal).addClass('tinv-modal-open');
        $(modal).removeClass('ftinvwl-pulse');
      },
      onDialogHide: function onDialogHide(modal) {
        $(modal).removeClass('tinv-modal-open');
        $(modal).removeClass('ftinvwl-pulse');
      },
      onInited: function onInited() {},
      onClick: function onClick() {
        if ($(this).is('.disabled-add-wishlist')) {
          return false;
        }

        if ($(this).is('.ftinvwl-animated')) {
          $(this).addClass('ftinvwl-pulse');
        }

        if (this.tinvwl_dialog) {
          this.tinvwl_dialog.show_list.call(this);
        } else {
          s.onActionProduct.call(this);
        }
      },
      onPrepareDataAction: function onPrepareDataAction(a, data) {
        $('body').trigger('tinvwl_wishlist_button_clicked', [a, data]);
      },
      filterProductAlreadyIn: function filterProductAlreadyIn(WList) {
        var WList = WList || [],
            data = {};
        $('form.cart[method=post], .woocommerce-variation-add-to-cart, form.vtajaxform[method=post]').find('input, select').each(function () {
          var name_elm = $(this).attr('name'),
              type_elm = $(this).attr('type'),
              value_elm = $(this).val();

          if ('checkbox' === type_elm || 'radio' === type_elm) {
            if ($(this).is(':checked')) {
              data['form' + name_elm] = value_elm;
            }
          } else {
            data['form' + name_elm] = value_elm;
          }
        });
        data = data['formvariation_id'];
        return WList.filter(function (wishlist) {
          if ('object' === _typeof(wishlist.in) && 'string' === typeof data) {
            var number = parseInt(data);
            return 0 <= wishlist.in.indexOf(number);
          }

          return wishlist.in;
        });
      },
      onMultiProductAlreadyIn: function onMultiProductAlreadyIn(WList) {
        var WList = WList || [];
        WList = s.onPrepareList.call(WList) || WList;
        WList = s.filterProductAlreadyIn.call(this, WList) || WList;
        $(this).parent().parent().find('.already-in').remove();
        var text = '';

        switch (WList.length) {
          case 0:
            break;

          default:
            var text = $('<ul>');
            $.each(WList, function (k, wishlist) {
              text.append($('<li>').html($('<a>').html(wishlist.title).attr({
                href: wishlist.url
              })).val(wishlist.ID));
            });
            break;
        }

        if (text.length) {
          $(this).closest('.tinv-modal-inner').find('img').after($('<div>').addClass('already-in').html(s.text_already_in + ' ').append(text));
        }
      },
      onAction: {
        redirect: function redirect(url) {
          if (s.redirectTimer) {
            clearTimeout(s.redirectTimer);
          }

          s.redirectTimer = window.setTimeout(function () {
            window.location.href = url;
          }, 4000);
        },
        force_redirect: function force_redirect(url) {
          window.location.href = url;
        },
        wishlists: function wishlists(wishlist) {},
        msg: function msg(html) {
          if (!html) {
            return false;
          }

          var $msg = $(html).eq(0);

          if (!$('body > .tinv-wishlist').length) {
            $('body').append($('<div>').addClass('tinv-wishlist'));
          }

          $('body > .tinv-wishlist').append($msg);
          FocusTrap('body > .tinv-wishlist');
          $msg.on('click', '.tinv-close-modal, .tinvwl_button_close, .tinv-overlay', function (e) {
            e.preventDefault();
            $msg.remove();

            if (s.redirectTimer) {
              clearTimeout(s.redirectTimer);
            }
          });
        },
        status: function status(_status) {
          $('body').trigger('tinvwl_wishlist_added_status', [this, _status]);
        },
        removed: function removed(status) {},
        make_remove: function make_remove(status) {},
        wishlists_data: function wishlists_data(value) {
          set_hash(JSON.stringify(value));
        }
      }
    };

    sd.onActionProduct = function (id, name) {
      var data = {
        form: {},
        tinv_wishlist_id: id || '',
        tinv_wishlist_name: name || '',
        product_type: $(this).attr('data-tinv-wl-producttype'),
        product_id: $(this).attr('data-tinv-wl-product') || 0,
        product_variation: $(this).attr('data-tinv-wl-productvariation') || 0,
        product_action: $(this).attr('data-tinv-wl-action') || 'addto',
        redirect: window.location.href
      },
          a = this,
          formEl = [],
          formData = new FormData();

      if (tinvwl_add_to_wishlist.wpml) {
        data.lang = tinvwl_add_to_wishlist.wpml;
      }

      $('form.cart[method=post][data-product_id="' + $(this).attr('data-tinv-wl-product') + '"], form.vtajaxform[method=post][data-product_id="' + $(this).attr('data-tinv-wl-product') + '"]').each(function () {
        formEl.push($(this));
      });

      if (!formEl.length) {
        $(a).closest('form.cart[method=post], form.vtajaxform[method=post]').each(function () {
          formEl.push($(this));
        });

        if (!formEl.length) {
          formEl.push($('form.cart[method=post]'));
        }
      }

      $('.tinv-wraper[data-product_id="' + $(this).attr('data-tinv-wl-product') + '"]').each(function () {
        formEl.push($(this));
      });
      $.each(formEl, function (index, element) {
        $(element).find('input:not(:disabled), select:not(:disabled), textarea:not(:disabled)').each(function () {
          var name_elm = $(this).attr('name'),
              type_elm = $(this).attr('type'),
              value_elm = $(this).val(),
              count = 10,
              ti_merge_value = function ti_merge_value(o1, o2) {
            if ('object' === _typeof(o2)) {
              if ('undefined' === typeof o1) {
                o1 = {};
              }

              for (var i in o2) {
                if ('' === i) {
                  var j = -1;

                  for (j in o1) {
                    j = j;
                  }

                  j = parseInt(j) + 1;
                  o1[j] = ti_merge_value(o1[i], o2[i]);
                } else {
                  o1[i] = ti_merge_value(o1[i], o2[i]);
                }
              }

              return o1;
            } else {
              return o2;
            }
          };

          if ('button' === type_elm || 'undefined' == typeof name_elm) {
            return;
          }

          while (/^(.+)\[([^\[\]]*?)\]$/.test(name_elm) && 0 < count) {
            var n_name = name_elm.match(/^(.+)\[([^\[\]]*?)\]$/);

            if (3 === n_name.length) {
              var _value_elm = {};
              _value_elm[n_name[2]] = value_elm;
              value_elm = _value_elm;
            }

            name_elm = n_name[1];
            count--;
          }

          if ('file' === type_elm) {
            var file_data = $(this)[0].files;

            if (file_data) {
              formData.append(name_elm, file_data[0]);
            }
          }

          if ('checkbox' === type_elm || 'radio' === type_elm) {
            if ($(this).is(':checked')) {
              if (!value_elm.length && 'object' !== _typeof(value_elm)) {
                value_elm = true;
              }

              data.form[name_elm] = ti_merge_value(data.form[name_elm], value_elm);
            }
          } else {
            data.form[name_elm] = ti_merge_value(data.form[name_elm], value_elm);
          }
        });
      });
      data = s.onPrepareDataAction.call(a, a, data) || data;
      $.each(data, function (key, value) {
        if ('form' === key) {
          $.each(value, function (k, v) {
            if ('object' === _typeof(v)) {
              v = JSON.stringify(v);
            }

            formData.append(key + '[' + k + ']', v);
          });
        } else {
          formData.append(key, value);
        }
      });
      $.ajax({
        url: s.api_url,
        method: 'POST',
        contentType: false,
        processData: false,
        data: formData
      }).done(function (body) {
        s.onDialogHide.call(a.tinvwl_dialog, a);

        if ('object' === _typeof(body)) {
          for (var k in body) {
            if ('function' === typeof s.onAction[k]) {
              s.onAction[k].call(a, body[k]);
            }
          }
        } else {
          if ('function' === typeof s.onAction.msg) {
            s.onAction.msg.call(a, body);
          }
        }
      });
    };

    var s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      if (!$(this).attr('data-tinv-wl-list')) {
        return false;
      }

      if (s.dialogbox) {
        if (s.dialogbox.length) {
          this.tinvwl_dialog = s.dialogbox;
        }
      }

      if (!this.tinvwl_dialog) {
        this.tinvwl_dialog = s.onGetDialogBox.call(this);
      }

      if (!this.tinvwl_dialog) {
        var _tinvwl_dialog = $(this).nextAll(s.class.dialogbox).eq(0);

        if (_tinvwl_dialog.length) {
          this.tinvwl_dialog = _tinvwl_dialog;
        }
      }

      if (this.tinvwl_dialog) {
        s.onPrepareDialogBox.call(this.tinvwl_dialog);

        if ('function' !== typeof this.tinvwl_dialog.update_list) {
          this.tinvwl_dialog.update_list = function (WL) {
            var $select = $(this).find(s.class.select).eq(0);
            $(this).find(s.class.newtitle).hide().val('');
            $select.html('');
            $.each(WL, function (k, v) {
              s.onCreateWishList.call($select, v);
            });

            if (s.text_create) {
              s.onCreateWishList.call($select, {
                ID: '',
                title: s.text_create,
                in: false
              });
            }

            s.onMultiProductAlreadyIn.call($select, WL);
            s.onSelectWishList.call($select, WL);
            $(this).find(s.class.newtitle).toggle('' === $select.val());
          };
        }

        if ('function' !== typeof this.tinvwl_dialog.show_list) {
          this.tinvwl_dialog.show_list = function () {
            var WList = JSON.parse($(this).attr('data-tinv-wl-list')) || [];

            if (WList.length) {
              WList = s.onPrepareList.call(WList) || WList;
              this.tinvwl_dialog.update_list(WList);
              s.onDialogShow.call(this.tinvwl_dialog, this);
            } else {
              s.onActionProduct.call(this);
            }
          };
        }

        var a = this;
        $(this.tinvwl_dialog).find(s.class.dialogbutton).off('click').on('click', function () {
          var b = $(a.tinvwl_dialog).find(s.class.select),
              c = $(a.tinvwl_dialog).find(s.class.newtitle),
              d;

          if (b.val() || c.val()) {
            s.onActionProduct.call(a, b.val(), c.val());
          } else {
            d = c.is(':visible') ? c : b;
            d.addClass('empty-name-wishlist');
            window.setTimeout(function () {
              d.removeClass('empty-name-wishlist');
            }, 1000);
          }
        });
      }

      $(this).off('click').on('click', s.onClick);
      s.onInited.call(this, s);
    });
  };

  $(document).ready(function () {
    // Add to wishlist button click
    $('body').on('click keydown', '.tinvwl_add_to_wishlist_button', function (e) {
      if ('keydown' === e.type) {
        var keyD = e.key !== undefined ? e.key : e.keyCode; // e.key && e.keycode have mixed support - keycode is deprecated but support is greater than e.key
        // I tested within IE11, Firefox, Chrome, Edge (latest) & all had good support for e.key

        if (!('Enter' === keyD || 13 === keyD || 0 <= ['Spacebar', ' '].indexOf(keyD) || 32 === keyD)) {
          return;
        }

        e.preventDefault();
      }

      $('body').trigger('tinvwl_add_to_wishlist_button_click', [this]);

      if ($(this).is('.disabled-add-wishlist')) {
        e.preventDefault();
        window.alert(tinvwl_add_to_wishlist.i18n_make_a_selection_text);
        return;
      }

      if ($(this).is('.inited-add-wishlist')) {
        return;
      }

      $(this).tinvwl_to_wishlist({
        onInited: function onInited(s) {
          $(this).addClass('inited-add-wishlist');
          s.onClick.call(this);
        }
      });
    }); // Disable add to wishlist button if variations not selected

    $(document).on('hide_variation', '.variations_form', function (a) {
      var e = $('.tinvwl_add_to_wishlist_button:not(.tinvwl-loop)[data-tinv-wl-product="' + $(this).data('product_id') + '"]');
      e.attr('data-tinv-wl-productvariation', 0);

      if (e.length && e.attr('data-tinv-wl-list')) {
        var f = JSON.parse(e.attr('data-tinv-wl-list')),
            j = false,
            g = '1' == window.tinvwl_add_to_wishlist['simple_flow'];

        for (var i in f) {
          if (f[i].hasOwnProperty('in') && Array.isArray(f[i].in) && -1 < (f[i].in || []).indexOf(0)) {
            j = true;
          }
        }

        e.toggleClass('tinvwl-product-in-list', j).toggleClass('tinvwl-product-make-remove', j && g).attr('data-tinv-wl-action', j && g ? 'remove' : 'addto');
      }

      if (e.length && !tinvwl_add_to_wishlist.allow_parent_variable) {
        a.preventDefault();
        e.addClass('disabled-add-wishlist');
      }
    });
    $(document).on('show_variation', '.variations_form', function (a, b, d) {
      var e = $('.tinvwl_add_to_wishlist_button:not(.tinvwl-loop)[data-tinv-wl-product="' + $(this).data('product_id') + '"]');
      e.attr('data-tinv-wl-productvariation', b.variation_id);

      if (e.length && e.attr('data-tinv-wl-list')) {
        var f = JSON.parse(e.attr('data-tinv-wl-list')),
            j = false,
            g = '1' == window.tinvwl_add_to_wishlist['simple_flow'];

        for (var i in f) {
          if (f[i].hasOwnProperty('in') && Array.isArray(f[i].in) && -1 < (f[i].in || []).indexOf(b.variation_id)) {
            j = true;
          }
        }

        e.toggleClass('tinvwl-product-in-list', j).toggleClass('tinvwl-product-make-remove', j && g).attr('data-tinv-wl-action', j && g ? 'remove' : 'addto');
      }

      a.preventDefault();
      e.removeClass('disabled-add-wishlist');
    }); // Refresh when storage changes in another tab

    $(window).on('storage onstorage', function (e) {
      if (hash_key === e.originalEvent.key && localStorage.getItem(hash_key) !== sessionStorage.getItem(hash_key)) {
        if (localStorage.getItem(hash_key)) {
          var data = JSON.parse(localStorage.getItem(hash_key));

          if ('object' === _typeof(data) && null !== data && (data.hasOwnProperty('products') || data.hasOwnProperty('counter'))) {
            set_hash(localStorage.getItem(hash_key));
          }
        }
      }
    }); // Get wishlist data from REST API.

    var tinvwl_products = [],
        tinvwl_counter = false;
    $('a.tinvwl_add_to_wishlist_button').each(function () {
      if ('undefined' !== $(this).data('tinv-wl-product') && $(this).data('tinv-wl-product')) {
        tinvwl_products.push($(this).data('tinv-wl-product'));
      }
    });
    $('.wishlist_products_counter_number').each(function () {
      tinvwl_counter = true;
    });

    var rest_request = function rest_request() {
      if (tinvwl_products.length || tinvwl_counter) {
        var params = {
          'ids': tinvwl_products,
          'counter': tinvwl_counter,
          'tinvwl_request': true
        };

        if (tinvwl_add_to_wishlist.wpml) {
          params.lang = tinvwl_add_to_wishlist.wpml;
        }

        $.ajax({
          url: tinvwl_add_to_wishlist.rest_root + 'wishlist/v1/products',
          method: 'POST',
          data: params,
          beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
          }
        }).done(function (response) {
          set_hash(JSON.stringify(response));
          mark_products(response);
        });
      }
    };

    var custom_ajax = function custom_ajax() {
      if (tinvwl_products.length || tinvwl_counter) {
        var params = {};

        if (tinvwl_add_to_wishlist.wpml) {
          params.lang = tinvwl_add_to_wishlist.wpml;
        }

        $.ajax({
          url: tinvwl_add_to_wishlist.plugin_url + 'includes/api/ajax.php',
          method: 'POST',
          data: params,
          beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', tinvwl_add_to_wishlist.nonce);
          }
        }).done(function (response) {
          set_hash(JSON.stringify(response));
          mark_products(response);
        }).fail(function () {
          rest_request();
        });
      }
    };

    var get_wishlist_data = function get_wishlist_data() {
      if ($supports_html5_storage) {
        if (tinvwl_add_to_wishlist.update_wishlists_data) {
          localStorage.setItem(hash_key, '');
        }

        if (localStorage.getItem(hash_key)) {
          var data = JSON.parse(localStorage.getItem(hash_key));

          if ('object' === _typeof(data) && null !== data && (data.hasOwnProperty('products') || data.hasOwnProperty('counter'))) {
            if (!data.hasOwnProperty('lang') && !tinvwl_add_to_wishlist.wpml || tinvwl_add_to_wishlist.wpml && data.lang === tinvwl_add_to_wishlist.wpml) {
              mark_products(data);
              return;
            }
          }
        }
      }

      if (tinvwl_add_to_wishlist.block_ajax_wishlists_data) {
        return;
      }

      custom_ajax();
    };

    get_wishlist_data();
    /* Dynamic buttons */
    // Create an observer instance

    var observer = new MutationObserver(function (mutations) {
      tinvwl_products = [];
      mutations.forEach(function (mutation) {
        var newNodes = mutation.addedNodes; // If there are new nodes added

        if (null !== newNodes) {
          var $nodes = $(newNodes);
          $nodes.each(function () {
            var $node = $(this),
                els = $node.find('.tinvwl_add_to_wishlist_button');

            if (els.length) {
              els.each(function () {
                if ('undefined' !== $(this).data('tinv-wl-product') && $(this).data('tinv-wl-product')) {
                  tinvwl_products.push($(this).data('tinv-wl-product'));
                }
              });
            }
          });
        }
      });

      if (tinvwl_products.length) {
        get_wishlist_data();
      }
    }); // Configuration of the observer:

    var config = {
      childList: true,
      subtree: true
    };
    var targetNode = document.body;
    observer.observe(targetNode, config);
  });
  /* Storage Handling */

  var $supports_html5_storage = true,
      hash_key = tinvwl_add_to_wishlist.hash_key;

  try {
    $supports_html5_storage = 'sessionStorage' in window && null !== window.sessionStorage;
    window.sessionStorage.setItem('ti', 'test');
    window.sessionStorage.removeItem('ti');
    window.localStorage.setItem('ti', 'test');
    window.localStorage.removeItem('ti');
  } catch (err) {
    $supports_html5_storage = false;
  }

  function mark_products(data) {
    var g = '1' == window.tinvwl_add_to_wishlist['simple_flow'];

    if (g) {
      $('a.tinvwl_add_to_wishlist_button').each(function () {
        $(this).removeClass('tinvwl-product-make-remove').removeClass('tinvwl-product-already-on-wishlist').removeClass('tinvwl-product-in-list').attr('data-tinv-wl-action', 'addto').attr('data-tinv-wl-list', '[]');
      });
    }

    $('body').trigger('tinvwl_wishlist_mark_products', [data]);
    $.each(data.products, function (i, item) {
      var id = i,
          e = $('a.tinvwl_add_to_wishlist_button[data-tinv-wl-product="' + id + '"]');
      e.each(function () {
        var vid = parseInt($(this).attr('data-tinv-wl-productvariation')),
            vids = $(this).data('tinv-wl-productvariations') || [],
            j = false;

        for (var i in item) {
          if (item[i].hasOwnProperty('in') && Array.isArray(item[i].in) && (-1 < (item[i].in || []).indexOf(id) || -1 < (item[i].in || []).indexOf(vid) || vids.some(function (r) {
            return 0 <= (item[i].in || []).indexOf(r);
          }))) {
            j = true;
          }
        }

        $('body').trigger('tinvwl_wishlist_product_marked', [this, j]);
        $(this).attr('data-tinv-wl-list', JSON.stringify(item)).toggleClass('tinvwl-product-in-list', j).toggleClass('tinvwl-product-make-remove', j && g).attr('data-tinv-wl-action', j && g ? 'remove' : 'addto');
      });
    });
    update_product_counter(data.counter);
  }
  /** Set the  hash in both session and local storage */


  function set_hash(hash) {
    if ($supports_html5_storage) {
      localStorage.setItem(hash_key, hash);
      sessionStorage.setItem(hash_key, hash);
      mark_products(JSON.parse(hash));
    }
  }

  function update_product_counter(counter) {
    if ('1' == window.tinvwl_add_to_wishlist['hide_zero_counter'] && 0 === counter) {
      counter = 'false';
    }

    jQuery('i.wishlist-icon').addClass('added');

    if ('false' !== counter) {
      jQuery('.wishlist_products_counter_number, body.theme-woostify .wishlist-item-count').html(counter);
      jQuery('i.wishlist-icon').attr('data-icon-label', counter);
    } else {
      jQuery('.wishlist_products_counter_number, body.theme-woostify .wishlist-item-count').html('').closest('span.wishlist-counter-with-products').removeClass('wishlist-counter-with-products');
      jQuery('i.wishlist-icon').removeAttr('data-icon-label');
    }

    var has_products = !('0' == counter || 'false' == counter);
    jQuery('.wishlist_products_counter').toggleClass('wishlist-counter-with-products', has_products);
    setTimeout(function () {
      jQuery('i.wishlist-icon').removeClass('added');
    }, 500);
  }

  function FocusTrap(el) {
    var inputs = $(el).find('select, input, textarea, button, a').filter(':visible');
    var firstInput = inputs.first();
    var lastInput = inputs.last();
    /*set focus on first input*/

    firstInput.focus().blur();
    /*redirect last tab to first input*/

    lastInput.on('keydown', function (e) {
      if (9 === e.which && !e.shiftKey) {
        e.preventDefault();
        firstInput.focus();
      }
    });
    /*redirect first shift+tab to last input*/

    firstInput.on('keydown', function (e) {
      if (9 === e.which && e.shiftKey) {
        e.preventDefault();
        lastInput.focus();
      }
    });
  }
})(jQuery);
"use strict";

// Misc
(function ($) {
  $(document).ready(function () {
    $('#tinvwl_manage_actions, #tinvwl_product_actions').addClass('form-control').parent().wrapInner('<div class="tinvwl-input-group tinvwl-no-full">').find('button').wrap('<span class="tinvwl-input-group-btn">');
    $('.tinv-lists-nav').each(function () {
      if (!$(this).html().trim().length) {
        $(this).remove();
      }
    });
    $('body').on('click', '.social-buttons .social:not(.social-email,.social-whatsapp,.social-clipboard)', function (e) {
      var newWind = window.open($(this).attr('href'), $(this).attr('title'), 'width=420,height=320,resizable=yes,scrollbars=yes,status=yes');

      if (newWind) {
        newWind.focus();
        e.preventDefault();
      }
    });

    if ('undefined' !== typeof ClipboardJS) {
      var clipboard = new ClipboardJS('.social-buttons .social.social-clipboard', {
        text: function text(trigger) {
          return trigger.getAttribute('href');
        }
      });
      clipboard.on('success', function (e) {
        showTooltip(e.trigger, tinvwl_add_to_wishlist.tinvwl_clipboard);
      });
      var btns = document.querySelectorAll('.social-buttons .social.social-clipboard');

      for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener('mouseleave', clearTooltip);
        btns[i].addEventListener('blur', clearTooltip);
      }
    }

    $('body').on('click', '.social-buttons .social.social-clipboard', function (e) {
      e.preventDefault();
    });
    $('body').on('click', '.tinv-wishlist .tinv-overlay, .tinv-wishlist .tinv-close-modal, .tinv-wishlist .tinvwl_button_close', function (e) {
      e.preventDefault();
      $(this).parents('.tinv-modal:first').removeClass('tinv-modal-open');
      $('body').trigger('tinvwl_modal_closed', [this]);
    });
    $('body').on('click', '.tinv-wishlist .tinvwl-btn-onclick', function (e) {
      var url = $(this).data('url');

      if (url) {
        e.preventDefault();
        window.location = $(this).data('url');
      }
    });
    var navigationButton = $('.tinv-wishlist .navigation-button');

    if (navigationButton.length) {
      navigationButton.each(function () {
        var navigationButtons = $(this).find('> li');

        if (5 > navigationButtons.length) {
          navigationButtons.parent().addClass('tinvwl-btns-count-' + navigationButtons.length);
        }
      });
    }

    $('.tinv-login .showlogin').off('click').on('click', function (e) {
      e.preventDefault();
      $(this).closest('.tinv-login').find('.login').toggle();
    });
    $('.tinv-wishlist table.tinvwl-table-manage-list tfoot td').each(function () {
      $(this).toggle(!!$(this).children().not('.look_in').length || !!$(this).children('.look_in').children().length);
    });
  });
})(jQuery);

function showTooltip(elem, msg) {
  elem.setAttribute('class', 'social social-clipboard tooltipped tooltipped-s');
  elem.setAttribute('aria-label', msg);
}

function clearTooltip(e) {
  e.currentTarget.setAttribute('class', 'social social-clipboard ');
  e.currentTarget.removeAttribute('aria-label');
}
"use strict";

// Wishlist table
(function ($) {
  //Prevent to submit wishlist table action
  $.fn.tinvwl_break_submit = function (so) {
    var sd = {
      selector: 'input, select, textarea',
      ifempty: true,
      invert: false,
      validate: function validate() {
        return $(this).val();
      },
      rule: function rule() {
        var form_elements = $(this).parents('form').eq(0).find(s.selector),
            trigger = s.invert;

        if (0 === form_elements.length) {
          return s.ifempty;
        }

        form_elements.each(function () {
          if (trigger && !s.invert || !trigger && s.invert) {
            return;
          }

          trigger = Boolean(s.validate.call($(this)));
        });
        return trigger;
      }
    };
    var s = $.extend(true, {}, sd, so);
    return $(this).each(function () {
      $(this).on('click', function (event) {
        var ss = [];

        if ('undefined' !== typeof $(this).attr('tinvwl_break_submit')) {
          ss = $(this).attr('tinvwl_break_submit').split(',');
        }

        if (-1 !== jQuery.inArray(s.selector, ss)) {
          ss = [];
        }

        if (!s.rule.call($(this)) && 0 === ss.length) {
          alert(window.tinvwl_add_to_wishlist['tinvwl_break_submit']);
          event.preventDefault();
        }

        ss.push(s.selector);
        $(this).attr('tinvwl_break_submit', ss);

        if (s.rule.call($(this))) {
          $(this).removeAttr('tinvwl_break_submit');
        }
      });
    });
  };

  $(document).ready(function () {
    $('.tinvwl-break-input').tinvwl_break_submit({
      selector: '.tinvwl-break-input-filed'
    });
    $('.tinvwl-break-checkbox').tinvwl_break_submit({
      selector: 'table td input[type=checkbox]',
      validate: function validate() {
        return $(this).is(':checked');
      }
    }); // Wishlist table bulk action checkbox

    $('.global-cb').on('click', function () {
      $(this).closest('table').eq(0).find('.product-cb input[type=checkbox], .wishlist-cb input[type=checkbox]').prop('checked', $(this).is(':checked'));
    });
  });
})(jQuery);