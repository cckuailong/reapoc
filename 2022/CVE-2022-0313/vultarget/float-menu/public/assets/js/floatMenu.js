/*! ========= INFORMATION ============================
	- author:    Wow-Company
	- url:       https://wow-estore.com/item/float-menu-pro/
==================================================== */

'use strict';

(function($) {

  var defaults = {
    // Main
    position: ['left', 'center'],
    offset: [0, 0],
    buttonShape: 'round',
    buttonColor: 'custom',
    buttonOverColor: 'custom',
    iconColor: 'white',
    iconOverColor: 'white',
    labelColor: 'match',
    labelTextColor: 'match',
    labelEffect: 'slide-out-fade',
    labelAnimate: 'default',
    labelConnected: false,
    labelsOn: true,
    sideSpace: false,
    buttonSpace: false,
    labelSpace: false,
    mobileEnable: false,
    mobileScreen: 768,
    // Subbar
    subPosition: ['circular', 100, -90, 90],
    subEffect: ['slide', 30],
    subAnimate: [400, 'easeOutQuad'],
    subSpace: false,
    subOpen: 'mouseover',
    // Window
    windowPosition: ['center', 'center'],
    windowOffset: [0, 0],
    windowCorners: 'match',
    windowColor: 'match',
    windowShadow: true,
    // Other
    showAfterPosition: false,
    hideAfterPosition: false,
    barAnimate: [250, 'easeOutQuad'],
    hideUnderWidth: false,
    shareTarget: 'default',
  };
  var labelAnimateDefaults = {
    'default': [400, 'easeOutQuad'],
    'fade': [200, 'easeOutQuad'],
    'slide-in-in': {
      show: [400, 'easeOutQuad'],
      hide: [400, 'swing'],
    },
  };

  var methods = {
    'build': build,
    'destroy': destroy,
  };

  $.fn.floatingMenu = function(arg) {
    if (typeof arg === 'object' || !arg) {
      build.apply(this, arguments);
    } else if (methods[arg]) {
      methods[arg].apply(this, Array.prototype.slice.call(arguments, 1));
    } else {
      $.error('The method ' + arg + ' does not exist in Float Menu.');
    }
  };

  function build(options) {
    var settings = createSettings(options);

    return this.each(function() {
      var sidebar = $(this);

      if (sidebar.data('fm-built')) destroy.apply(this);
      sidebar.data('fm-built', true);

      // VARS
      var bar = sidebar.children('.fm-bar');
      var barList = bar.children();
      var sbwindow = sidebar.children('.fm-window');

      var $window = $(window);
      var $document = $(document);

      var barVisible = true;
      var right = false;
      var csub = null;
      var cwindow = null;
      var nextMargins = [];

      // INIT
      if (settings.position[0] === 'right') {
        right = true;
        bar.addClass('fm-right');
      }

      if (settings.buttonShape !== 'square') bar.addClass('fm-' + settings.buttonShape);

      if (settings.labelConnected) bar.addClass('fm-connected');

      if (settings.buttonColor !== 'custom') bar.addClass('fm-' + settings.buttonColor + '-button');
      if (settings.buttonOverColor !== 'custom') bar.addClass('fm-' + settings.buttonOverColor + '-button-over');

      if (settings.iconColor !== 'custom') bar.addClass('fm-' + settings.iconColor + '-icon');
      if (settings.iconOverColor !== 'custom') bar.addClass('fm-' + settings.iconOverColor + '-icon-over');

      if (settings.labelColor !== 'custom') bar.addClass('fm-' + settings.labelColor + '-label');
      if (settings.labelTextColor !== 'custom') bar.addClass('fm-' + settings.labelTextColor + '-label-text');

      if (settings.sideSpace) bar.addClass('fm-side-space');
      if (settings.buttonSpace) bar.addClass('fm-button-space');
      if (settings.labelSpace) bar.addClass('fm-label-space');

      if (settings.windowCorners === 'round') sbwindow.addClass('fm-round');
      if (settings.windowColor !== 'custom') sbwindow.addClass('fm-' + settings.windowColor);
      if (settings.windowShadow) sbwindow.addClass('fm-winshadow');

      buildList(bar);

      // Subbar animate fix.
      barList.each(function(ind) {
        nextMargins[ind] = getInt(barList.eq(ind).css('margin-top'));
      });

      sbwindow.children('.fm-shadow').on('click', closeWindow);

      sbwindow.children('.fm-panel').each(buildPanel);

      resize();

      $window.on('resize.superSidebar', resize);

      if (settings.showAfterPosition) {
        if ($window.scrollTop() < settings.showAfterPosition) {
          bar.css('opacity', 0).addClass('fm-hide');
          barVisible = false;
        }

        $window.on('scroll.superSidebar', function() {
          if ($window.scrollTop() < settings.showAfterPosition) {
            if (barVisible) hideBar();
          } else {
            if (!barVisible) showBar();
          }
        });
      }

      if (settings.hideAfterPosition) {
        if ($window.scrollTop() > settings.hideAfterPosition) {
          bar.css('opacity', 0).addClass('fm-hide');
          barVisible = false;
        }

        $window.on('scroll.superSidebar', function() {
          if ($window.scrollTop() > settings.hideAfterPosition) {
            if (barVisible) hideBar();
          } else {
            if (!barVisible) showBar();
          }
        });
      }

      bar.addClass('fm-css-anim');

      sidebar.addClass('fm-ready');

      // FUNCTIONS
      function buildList(list) {
        list.children().each(function(i) {
          if ($(this).hasClass('fm-sub')) buildSub(this, i);
          else buildButton(this);
        });
      }

      function buildButton(btn) {
        var button = $(btn);
        var link = button.children('a');
        var icon = link.children('.fm-icon');
        var label = link.children('.fm-label');
        var mask, hit = null;

        var linkClick = 0;
        var screen = $(window).width();

        var maskHtml = '<div class="fm-mask"></div>';
        var hitHtml = '<div class="fm-hit"></div>';

        var iw, lw;
        var side = right ? 'right' : 'left';
        var dist = 40;
        var maskOff = false;
        var start = {}, show = {}, end = {};
        var showLabel, hideLabel;

        var showTime = settings.labelAnimate.show[0];
        var showEase = settings.labelAnimate.show[1];
        var hideTime = settings.labelAnimate.hide[0];
        var hideEase = settings.labelAnimate.hide[1];

        if (settings.labelsOn && label.length) {
          iw = getInt(icon.css('width'));
          lw = label.outerWidth(true);

          if (settings.buttonShape === 'round' || settings.buttonShape === 'rounded') maskOff = true;

          if (!settings.labelConnected &&
              (settings.labelSpace || settings.buttonShape === 'round' || settings.buttonShape === 'rounded' ||
                  settings.buttonShape === 'rounded-out'))
            hit = $(hitHtml).appendTo(link);

          switch (settings.labelEffect) {
            case 'fade':
              start = {'opacity': 0};
              show = {'opacity': 1};

              label.css(start);

              showLabel = function() {
                if (hit) hit.addClass('fm-show');
                label.velocity('stop').addClass('fm-show').velocity(show, showTime, showEase);
              };
              hideLabel = function() {
                label.velocity('stop').velocity(start, hideTime, hideEase, function() {
                  label.removeClass('fm-show');
                  if (hit) hit.removeClass('fm-show');
                });
              };
              break;
            case 'slide-out':
            case 'slide-out-fade':
              mask = link.wrap(maskHtml).parent();
              mask.css('width', iw);
              if (maskOff) mask.addClass('fm-off');

              start[side] = -lw + iw;
              if (settings.labelConnected) show[side] = 0;
              else show[side] = iw;

              if (settings.labelEffect === 'slide-out-fade') {
                start['opacity'] = 0;
                show['opacity'] = 1;
              }

              label.css(start);

              showLabel = function() {
                mask.css('width', iw + lw);
                if (maskOff) mask.removeClass('fm-off');
                if (hit) hit.addClass('fm-show');
                label.velocity('stop').addClass('fm-show').velocity(show, showTime, showEase);
              };
              hideLabel = function() {
                label.velocity('stop').velocity(start, hideTime, hideEase, function() {
                  label.removeClass('fm-show');
                  mask.css('width', iw);
                  if (maskOff) mask.addClass('fm-off');
                  if (hit) hit.removeClass('fm-show');
                });
              };
              break;
            case 'slide-in':
              start = {'opacity': 0};
              start[side] = iw + dist;
              show = {'opacity': 1};
              show[side] = iw;

              label.css(start);

              showLabel = function() {
                if (hit) hit.addClass('fm-show');
                label.velocity('stop').addClass('fm-show').velocity(show, showTime, showEase);
              };
              hideLabel = function() {
                label.velocity('stop').velocity(start, hideTime, hideEase, function() {
                  label.removeClass('fm-show');
                  if (hit) hit.removeClass('fm-show');
                });
              };
              break;
            case 'slide-out-out':
            case 'slide-in-in':
              mask = link.wrap(maskHtml).parent();
              mask.css('width', iw);
              if (maskOff) mask.addClass('fm-off');

              if (settings.labelEffect === 'slide-out-out') {
                start[side] = -lw + iw;
                show[side] = iw;
                end[side] = iw + dist;
              } else {
                start[side] = iw + dist;
                show[side] = iw;
                end[side] = -lw + iw;
              }
              start['opacity'] = 0;
              show['opacity'] = 1;
              end['opacity'] = 0;

              showLabel = function() {
                mask.css('width', iw + lw + dist);
                if (maskOff) mask.removeClass('fm-off');
                if (hit) hit.addClass('fm-show');
                label.velocity('stop').css(start).addClass('fm-show').velocity(show, showTime, showEase, function() {
                  mask.css('width', iw + lw);
                });
              };
              hideLabel = function() {
                mask.css('width', iw + lw + dist);
                label.velocity('stop').velocity(end, hideTime, hideEase, function() {
                  label.removeClass('fm-show');
                  mask.css('width', iw);
                  if (maskOff) mask.addClass('fm-off');
                  if (hit) hit.removeClass('fm-show');
                });
              };
              break;
            default:
              showLabel = function() {
                if (hit) hit.addClass('fm-show');
                label.addClass('fm-show');
              };
              hideLabel = function() {
                label.removeClass('fm-show');
                if (hit) hit.removeClass('fm-show');
              };
              break;
          }

          if (settings.mobileEnable == true && settings.mobileScreen >= screen) {

            link.on('touchend', function(e) {

              if (linkClick == 0) {
                e.preventDefault();
                showLabel();
                linkClick = 1;
                setTimeout(function() {
                  hideLabel();
                  linkClick = 0;
                }, 3000);
                return;
              } else if (linkClick == 1) {
                hideLabel();
                linkClick = 0;
              }
            });
          } else {
            link.on('mouseenter', showLabel);
            link.on('mouseleave', hideLabel);
          }
        }

        var shareVal = link.data('share');
        var shareData;
        var shareUrl;
        var shareTarget;
        var href;
        var labelShow;

        if (shareVal) {
          if (shareVal === 'pinterest') {
            link.on('click', pinterestShare);
          } else {
            shareData = settings.shareServices[shareVal];

            if (shareData) {
              shareUrl = shareData.url.replace('{URL}', PAGE_URL).replace('{TITLE}', PAGE_TITLE);

              link.attr('href', shareUrl);

              if (shareData.target === 'app') {
                link.attr('target', '_self');
              } else {
                if (settings.shareTarget === 'default') shareTarget = shareData.target;
                else shareTarget = settings.shareTarget;

                if (shareTarget === 'popup') {
                  link.on('click', {'url': shareUrl, 'params': shareData.popup}, sharePopup);
                } else {
                  link.attr('target', '_blank');
                }
              }
            } else {
              warn('There is no share data for "' + shareVal + '".');
            }
          }
        } else {
          href = link.attr('href');

          if (href && href.indexOf('fm-popup') > -1 && href !== '#') {
            link.on('click', function() {
              openWindow(href);
              return false;
            });
          }
        }

        labelShow = link.attr('data-label');

        if (labelShow === 'show') {
          showLabel();
        }

      }

      function buildSub(sub, ind) {
        var sub = $(sub);
        var icon = sub.children('.fm-icon');
        var list = sub.children('ul');
        var buttonList = list.children();
        var hit = null;
        var nextButton = null;

        var hitHtml = '<div class="fm-subhit"></div>';

        var position = settings.subPosition[0];
        var effect = settings.subEffect[0];
        var side = right ? 'right' : 'left';
        var total = buttonList.length;
        var iw = getInt(icon.css('width'));
        var ih = getInt(icon.css('height'));
        var positions = [];
        var status = null;
        var start = {}, show = {};
        var interval;
        var showList, hideList;
        var i;
        var nextOffset = 0;
        var prevOffset = 0;
        var nextMargin;
        var buttonMargin;
        var barMargin;
        var subOpen = false;

        var showTime = settings.subAnimate.show[0];
        var showEase = settings.subAnimate.show[1];
        var hideTime = settings.subAnimate.hide[0];
        var hideEase = settings.subAnimate.hide[1];

        buildList(list);

        if (position === 'side') sub.addClass('fm-side');
        if (settings.subSpace) sub.addClass('fm-sub-space');

        if (effect === 'linear-slide' || position === 'circular')
          sub.addClass('fm-posabs');

        if ((position === 'under' && effect === 'linear-slide') ||
            (position === 'circular' && effect === 'slide') ||
            (position === 'circular' && effect === 'linear-slide')) {
          buttonList.each(function(i) {
            $(this).css('z-index', 100 - i);
          });
        }

        if (barList[ind + 1]) {
          nextButton = barList.eq(ind + 1);
          //nextMargin = getInt(nextButton.css("margin-top"));
        }

        if (position === 'circular') {
          sub.addClass('fm-circular');

          var r = settings.subPosition[1];
          var sa = settings.subPosition[2];
          var ea = settings.subPosition[3];

          var startRad = sa * Math.PI / 180;
          var endRad = ea * Math.PI / 180;
          var stepRad = (endRad - startRad) / (total - 1);
          var a, s, t, p;

          buttonList.each(function(i) {
            a = i * stepRad + startRad;
            s = Math.round(r * Math.cos(a));
            t = Math.round(r * Math.sin(a));

            p = {'top': t};
            p[side] = s;
            $(this).css(p);
            positions[i] = [s, t];
          });

          hit = $(hitHtml).appendTo(sub);
          hit.css({
            'width': r + iw,
            'height': 2 * r + iw,
            'border-radius': right ? r + 'px 0 0 ' + r + 'px' : '0 ' + r + 'px ' + r + 'px 0',
            'top': -r,
          });

          buttonMargin = getInt(barList.eq(0).css('margin-bottom'));

          if (ind !== 0) {
            prevOffset = r + buttonMargin;
            barMargin = getInt(bar.css('margin-top'));
            sub.css('margin-top', buttonMargin);
          }

          if (nextButton) {
            nextOffset = r + buttonMargin;
          }
        } else {
          if (effect === 'linear-slide') {
            var c = 0;
            buttonList.each(function(i) {
              var btn = $(this);
              btn.css('top', c);
              positions[i] = c;
              c += getInt(btn.css('height')) + getInt(btn.css('margin-bottom'));
            });

            list.css({'width': iw, 'height': c});
          }

          hit = $(hitHtml).appendTo(sub);
          if (position === 'side')
            hit.css({'width': iw + getInt(list.css('margin-' + side)), 'height': ih});
          else
            hit.css({'width': iw, 'height': ih + getInt(list.css('margin-top'))});

          if (position === 'under' && nextButton) {
            nextOffset = list.outerHeight(true) + getInt(nextButton.css('margin-top')) + getInt(list.css('margin-top'));
          }
        }

        list.addClass('fm-hide');

        switch (effect) {
          case 'fade':
            start = {'opacity': 0};
            show = {'opacity': 1};

            list.css(start);

            showList = function() {
              list.velocity('stop').removeClass('fm-hide').velocity(show, showTime, showEase);
            };
            hideList = function() {
              list.velocity('stop').velocity(start, hideTime, hideEase, function() {
                list.addClass('fm-hide');
              });
            };
            break;
          case 'slide':
            if (position === 'circular') {
              start = {'top': 0, 'opacity': 0};
              start[side] = 0;
              buttonList.css(start);

              showList = function() {
                list.removeClass('fm-hide');
                buttonList.each(function(i) {
                  show = {'top': positions[i][1], 'opacity': 1};
                  show[side] = positions[i][0];
                  $(this).velocity('stop').velocity(show, showTime, showEase);
                });
              };
              hideList = function() {
                buttonList.each(function(i) {
                  $(this).velocity('stop').velocity(start, hideTime, hideEase, function() {
                    if (i === total - 1) list.addClass('fm-hide');
                  });
                });
              };
            } else {
              if (position === 'side') {
                start[side] = 0;
                show[side] = iw;
              } else {
                start = {'top': 0};
                show = {'top': 42};
              }
              start['opacity'] = 0;
              show['opacity'] = 1;

              list.css(start);

              showList = function() {
                list.velocity('stop').removeClass('fm-hide').velocity(show, showTime, showEase);
              };
              hideList = function() {
                list.velocity('stop').velocity(start, hideTime, hideEase, function() {
                  list.addClass('fm-hide');
                });
              };
            }
            break;
          case 'linear-fade':
            start = {'opacity': 0};
            show = {'opacity': 1};

            buttonList.css(start);

            showList = function() {
              status = 'show';
              list.removeClass('fm-hide');
              stopInterval();
              i = 0;
              interval = setInterval(function() {
                buttonList.eq(i).velocity('stop').velocity(show, showTime, showEase);
                if (i === total - 1) stopInterval();
                else i++;
              }, settings.subEffect[1]);
            };
            hideList = function() {
              status = 'hide';
              stopInterval();
              i = total - 1;
              interval = setInterval(function() {
                var bi = i;
                buttonList.eq(i).velocity('stop').velocity(start, showTime, showEase, function() {
                  if (status === 'hide' && bi === 0) list.addClass('fm-hide');
                });
                if (i === 0) stopInterval();
                else i--;
              }, settings.subEffect[1]);
            };
            break;
          case 'linear-slide':
            var first, last, step;

            if (position === 'side') start[side] = -iw;
            else if (position === 'circular') {
              start = {'top': 0};
              start[side] = 0;
            } else start = {'top': -ih};
            start['opacity'] = 0;

            buttonList.css(start);

            showList = function() {
              status = 'show';
              list.removeClass('fm-hide');
              stopInterval();
              i = 0;
              interval = setInterval(function() {
                if (position === 'side') show[side] = 0;
                else if (position === 'circular') {
                  show = {'top': positions[i][1]};
                  show[side] = positions[i][0];
                } else show = {'top': positions[i]};
                show['opacity'] = 1;

                buttonList.eq(i).velocity('stop').velocity(show, showTime, showEase);
                if (i === total - 1) stopInterval();
                else i++;
              }, settings.subEffect[1]);
            };
            hideList = function() {
              status = 'hide';

              if (position === 'side' || position === 'circular') {
                first = 0;
                last = total - 1;
                step = 1;
              } else {
                first = total - 1;
                last = 0;
                step = -1;
              }

              stopInterval();
              i = first;
              interval = setInterval(function() {
                var bi = i;
                buttonList.eq(i).velocity('stop').velocity(start, showTime, showEase, function() {
                  if (status === 'hide' && bi === last) list.addClass('fm-hide');
                });
                if (i === last) stopInterval();
                else i += step;
              }, settings.subEffect[1]);
            };
            break;
          default:
            showList = function() {
              list.removeClass('fm-hide');
            };
            hideList = function() {
              list.addClass('fm-hide');
            };
            break;
        }

        function stopInterval() {
          clearInterval(interval);
        }

        function showSub() {
          showList();
          if (hit) hit.addClass('fm-show');

          if (prevOffset) {
            bar.velocity('stop').velocity({'margin-top': barMargin - prevOffset + buttonMargin}, showTime, showEase);
            sub.velocity('stop').velocity({'margin-top': prevOffset}, showTime, showEase);
          }

          if (nextOffset) {
            nextButton.velocity('stop').velocity({'margin-top': nextOffset}, showTime, showEase);
          }

          subOpen = true;
          csub = sub;
        }

        function hideSub() {
          hideList();
          if (hit) hit.removeClass('fm-show');

          if (prevOffset) {
            bar.velocity('stop').velocity({'margin-top': barMargin}, showTime, showEase);
            sub.velocity('stop').velocity({'margin-top': buttonMargin}, showTime, showEase);
          }

          if (nextOffset) {
            nextButton.velocity('stop').velocity({'margin-top': nextMargins[ind + 1]}, showTime, showEase);
          }

          subOpen = false;
          csub = null;
        }

        sub.show = showSub;
        sub.hide = hideSub;

        if (settings.subOpen === 'click') {
          icon.on('click', function(event) {
            if (subOpen) {
              hideSub();
            } else {
              if (csub) {
                csub.hide();
              }
              showSub();
            }
            event.stopPropagation();
          });

          $document.on('click', function(event) {
            if (subOpen && !cwindow && !$(event.target).closest(sub).length) {
              hideSub();
            }
          });
        } else {
          sub.on('mouseenter', showSub);
          sub.on('mouseleave', hideSub);
        }
      }

      function buildPanel() {
        var panel = $(this);
        panel.find('.fm-close').on('click', function(event) {
          closeWindow();
          event.stopPropagation();
        });

      }

      function openWindow(name) {
        sbwindow.addClass('fm-show');
        cwindow = $(name).addClass('fm-show');
        posWindow(cwindow);
      }

      function closeWindow() {
        sbwindow.removeClass('fm-show');
        cwindow.removeClass('fm-show');
        cwindow = null;
      }

      function position() {
        posBar();
        if (cwindow) posWindow(cwindow);
      }

      function resize() {
        position();

        if (settings.hideUnderWidth) {
          if ($window.width() < settings.hideUnderWidth) {
            sidebar.addClass('fm-vhide');
          } else {
            sidebar.removeClass('fm-vhide');
          }
        }
      }

      function posBar() {
        posObject(bar, settings.position, settings.offset);
      }

      function posWindow(win) {
        var pos, off;

        if (win.data('position')) {
          pos = win.data('position').split('-');
          if (!pos[1]) pos[1] = defaults.windowPosition[1];
        } else {
          pos = settings.windowPosition;
        }

        if (win.data('offset')) {
          off = splitOffset(win.data('offset'));
        } else {
          off = settings.windowOffset;
        }

        posObject(win, pos, off);
      }

      function posObject(tar, pos, off) {
        if (pos) {
          var ww = $window.width();
          var wh = $window.height();
          var tw = tar.outerWidth(true);
          var th = tar.outerHeight(true);
          var x, y;
          var p;

          if (typeof pos[0] === 'number') x = {'left': pos[0] + off[0]};
          else if (typeof pos[0] === 'string') {
            if (pos[0].indexOf('%') !== -1) {
              p = getInt(pos[0].split('%')[0]);
              x = {'left': p / 100 * ww + off[0]};
            } else {
              if (pos[0] === 'left') x = {'left': 0 + off[0]};
              else if (pos[0] === 'center') x = {'left': (ww - tw) / 2 + off[0]};
              else if (pos[0] === 'right') x = {'right': 0 + off[0]};

              else x = {'left': getInt(pos[0]) + off[0]};
            }
          }

          if (typeof pos[1] === 'number') y = {'top': pos[1] + off[1]};
          else if (typeof pos[1] === 'string') {
            if (pos[1].indexOf('%') !== -1) {
              p = getInt(pos[1].split('%')[0]);
              y = {'top': p / 100 * wh + off[1]};
            } else {
              if (pos[1] === 'top') y = {'top': 0 + off[1]};
              else if (pos[1] === 'center') y = {'top': (wh - th) / 2 + off[1]};
              else if (pos[1] === 'bottom') y = {'bottom': 0 + off[1]};

              else y = {'top': getInt(pos[1]) + off[1]};
            }
          }

          if (x.left) x.left = Math.round(x.left);
          if (x.right) x.right = Math.round(x.right);

          if (y.top) y.top = Math.round(y.top);
          if (y.bottom) y.bottom = Math.round(y.bottom);

          tar.css($.extend({}, x, y));
        }
      }

      function pinterestShare(event) {
        $('body').
            append('<script src="https://assets.pinterest.com/js/pinmarklet.js" type="text/javascript"></script>');
        event.preventDefault();
      }

      function sharePopup(event) {
        var url = event.data.url;
        var params = event.data.params;
        var winLeft;
        var winParams;

        if (params.left === 'center') {
          winLeft = ($window.width() - params.width) / 2;
        } else {
          winLeft = params.left;
        }

        winParams = 'menubar=no,toolbar=no,location=no,scrollbars=no,status=no,resizable=yes,width=' + params.width +
            ',height=' + params.height + ',top=' + params.top + ',left=' + winLeft;

        window.open(url, 'sbShareWindow', winParams);

        event.preventDefault();
      }

      function showBar() {
        bar.removeClass('fm-hide');
        bar.velocity('stop').velocity({'opacity': 1}, settings.barAnimate.show[0], settings.barAnimate.show[1]);
        barVisible = true;
      }

      function hideBar() {
        bar.velocity('stop').
            velocity({'opacity': 0}, settings.barAnimate.show[0], settings.barAnimate.show[1], function() {
              bar.addClass('fm-hide');
            });
        barVisible = false;
      }
    });
  }

  function destroy() {
    return this.each(function() {
      var sidebar = $(this);
      var bar, sbwindow;

      if (sidebar.data('fm-built')) {
        sidebar.data('fm-built', false);

        bar = sidebar.children('.fm-bar');
        sbwindow = sidebar.children('.fm-window');

        bar.attr('class', 'fm-bar').removeAttr('style');
        sbwindow.attr('class', 'fm-window');

        destroyList(bar);

        sbwindow.removeClass('fm-show');
        sbwindow.children('.fm-shadow').off('click');
        sbwindow.children('.fm-panel').removeClass('fm-show').removeAttr('style').each(destroyPanel);

        $(window).off('resize.superSidebar scroll.superSidebar');

        sidebar.removeClass('fm-ready');
      }

      function destroyList(list) {
        list.children().each(function() {
          if ($(this).hasClass('fm-sub')) destroySub(this);
          else destroyButton(this);
        });
      }

      function destroyButton(btn) {
        var button = $(btn);
        var link = button.find('a');
        var label = link.children('.fm-label');

        if (link.data('share')) link.removeAttr('href target');

        link.children('.fm-hit').remove();

        if (button.children('.fm-mask').length) link.unwrap();

        label.removeAttr('style');

        link.off('mouseenter mouseleave click');
      }

      function destroySub(sub) {
        var sub = $(sub);
        var list = sub.children('ul');

        sub.removeClass('fm-side fm-circular fm-sub-space fm-posabs');

        list.removeClass('fm-hide');
        list.removeAttr('style');
        list.children().removeAttr('style');

        sub.children('.fm-subhit').remove();

        sub.off('mouseenter mouseleave');
      }

      function destroyPanel() {
        var panel = $(this);

        panel.find('.fm-close').off('click');

        var form = panel.find('form');
        if (form.length) destroyForm(form);
      }

      function destroyForm(form) {
        var fieldList = form.find('input, textarea');

        form.find('.fm-submit').off('click');
        form.off('submit');

        fieldList.removeClass('fm-formerror').off('focus');
        fieldList.each(function() {
          $(this).val('');
        });

        form.find('.fm-status').attr('class', 'fm-status');
      }
    });
  }

  function createSettings(options) {
    var settings = $.extend({}, defaults, options);

    if (typeof settings.position === 'string') {
      settings.position = settings.position.split('-');
    }
    if (settings.position[0] === 'center') {
      settings.position[0] = defaults.position[0];
      warn('Bar horizontal position cannot be "center". Horizontal position reset to "left".');
    }
    if (!settings.position[1]) {
      settings.position[1] = defaults.position[1];
    }

    if (settings.offset === 0 || settings.offset === false) {
      settings.offset = [0, 0];
    } else if (typeof settings.offset === 'string') {
      settings.offset = splitOffset(settings.offset);
    }

    if (!options.buttonShape && options.shape) {
      settings.buttonShape = settings.shape;
    }
    if (settings.buttonShape !== 'square') {
      if (settings.buttonShape !== 'round' && settings.buttonShape !== 'rounded' && settings.buttonShape !==
          'rounded-out') {
        settings.buttonShape = 'square';
      }
    }

    if (!options.buttonColor && options.color) {
      settings.buttonColor = settings.color;
    }
    if (settings.buttonColor === 'default') {
      settings.buttonColor = 'custom';
    }

    if (!options.buttonOverColor && options.overColor) {
      settings.buttonOverColor = settings.overColor;
    }
    if (settings.buttonOverColor === 'default') {
      settings.buttonOverColor = 'custom';
    }

    if (settings.labelColor === 'match') {
      settings.labelColor = settings.buttonOverColor;
    }
    if (settings.labelTextColor === 'match') {
      settings.labelTextColor = settings.iconOverColor;
    }

    if (settings.labelEffect === 'slide') settings.labelEffect = 'slide-out';
    if (settings.labelEffect === 'slide-in-fade') settings.labelEffect = 'slide-in';

    if (!options.labelAnimate && options.labelAnim) {
      settings.labelAnimate = settings.labelAnim;
    }
    if (settings.labelAnimate === 'default') {
      if (labelAnimateDefaults[settings.labelEffect]) {
        settings.labelAnimate = labelAnimateDefaults[settings.labelEffect];
      } else {
        settings.labelAnimate = labelAnimateDefaults[0];
      }
    }
    settings.labelAnimate = extendAnimateSetting(settings.labelAnimate);

    if (settings.labelConnected) {
      if (settings.labelEffect === 'slide-in' || settings.labelEffect === 'slide-out-out' || settings.labelEffect ===
          'slide-in-in') {
        settings.labelConnected = false;
        warn('"labelConnected: true" incompatible with "labelEffect: ' + settings.labelEffect +
            '". "labelConnected" reset to false.');
      } else if (settings.labelSpace) {
        settings.labelSpace = false;
        warn('"labelSpace: true" incompatible with "labelConnected: true". "labelSpace" reset to false.');
      }
    }

    if (typeof settings.subPosition === 'string') {
      settings.subPosition = [settings.subPosition];
    }
    if (settings.subPosition[0] === 'circular') {
      if (!settings.subPosition[1]) settings.subPosition[1] = defaults.subPosition[1];
      if (typeof settings.subPosition[2] === 'undefined') settings.subPosition[2] = defaults.subPosition[2];
      if (typeof settings.subPosition[3] === 'undefined') settings.subPosition[3] = defaults.subPosition[3];

      if (settings.subSpace) settings.subSpace = false;
    }

    if (!options.subAnimate && options.subAnim) {
      settings.subAnimate = settings.subAnim;
    }
    if (settings.subAnimate === 'default') settings.subAnimate = defaults.subAnimate;

    if (typeof settings.subEffect === 'string') {
      settings.subEffect = [settings.subEffect];
    }
    if ((settings.subEffect[0] === 'linear-fade' || settings.subEffect[0] === 'linear-slide') &&
        !settings.subEffect[1]) {
      settings.subEffect[1] = defaults.subEffect[1];
    }

    settings.subAnimate = extendAnimateSetting(settings.subAnimate);

    if (typeof settings.windowPosition === 'string') {
      settings.windowPosition = settings.windowPosition.split('-');
    }
    if (!settings.windowPosition[1]) {
      settings.windowPosition[1] = defaults.windowPosition[1];
    }

    if (settings.windowOffset === 0 || settings.windowOffset === false) {
      settings.windowOffset = [0, 0];
    } else if (typeof settings.windowOffset === 'string') {
      settings.windowOffset = splitOffset(settings.windowOffset);
    }

    if (settings.windowCorners === 'match') {
      if (settings.buttonShape === 'round' || settings.buttonShape === 'rounded' || settings.buttonShape ===
          'rounded-out') {
        settings.windowCorners = 'round';
      }
    }

    if (settings.windowColor === 'match') {
      settings.windowColor = settings.buttonColor;
    } else if (settings.windowColor === 'default') {
      settings.windowColor = 'custom';
    }

    if (settings.barAnimate === 'default') {
      settings.barAnimate = defaults.barAnimate;
    }

    settings.barAnimate = extendAnimateSetting(settings.barAnimate);

    if (settings.hideUnder) {
      settings.hideUnderWidth = settings.hideUnder;
    }

    return settings;
  }

  function splitOffset(off) {
    off = off.split('-');
    off[0] = getInt(off[0]);
    if (off[1]) off[1] = getInt(off[1]);
    return off;
  }

  function extendAnimateSetting(animate) {
    if (Object.prototype.toString.call(animate) === '[object Array]') {
      return {
        show: animate,
        hide: animate,
      };
    } else {
      return animate;
    }
  }

  function warn(msg) {
    if (window.console) {
      console.log('(!) Float Menu: ' + msg);
    }
  }

  function getInt(val) {
    return parseInt(val, 10);
  }

}(jQuery));

function scrollToTop() {
  jQuery('body,html').animate({
    scrollTop: 0,
  }, 777);
}

function scrollToBottom() {
  jQuery('html, body').animate({scrollTop: jQuery(document).height()}, 777);
}

function pageprint() {
  window.print();
}

function smoothscroll(section) {
  jQuery('html, body').animate({
    scrollTop: jQuery(section).offset().top,
  }, 777);
}

function goBack() {
  window.history.back();
}

function goForward() {
  window.history.forward();
}

function fmpGetAway(link) {
  // window.open( link, "_newtab" );
  window.location.replace( link );
}

const flTranslate = document.querySelectorAll('[data-google-lang]');
const flDefaultLang = document.documentElement.lang.substr(0, 2);

function flTranslateInit() {

  let code = flTranslateGetCode();

  if (code == flDefaultLang) {
    flTranslateClearCookie();
  }

  new google.translate.TranslateElement({
    pageLanguage: flDefaultLang,
    });

  flTranslate.forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      let lang = el.getAttribute('data-google-lang');
      flTranslateSetCookie(lang);
      window.location.reload();
    });
  });

}

function flTranslateGetCode() {
  let keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)');
  let cookieLang = keyValue ? keyValue[2].split('/')[2] : null;
  let lang = (cookieLang != undefined && cookieLang != 'null') ? cookieLang : flDefaultLang;
  return lang;

}

function flTranslateClearCookie() {
  document.cookie = 'googtrans=null';
  document.cookie = 'googtrans=null; domain=' + document.domain;
}

function flTranslateSetCookie(code) {
  document.cookie = 'googtrans=/auto/' + code;
  document.cookie = 'googtrans=/auto/' + code + '; domain=' + document.domain;
}