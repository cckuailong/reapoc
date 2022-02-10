/*
The jQuery UI Month Picker Version 3.0.4
https://github.com/KidSysco/jquery-ui-month-picker/

Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see
<http://www.gnu.org/licenses/gpl-3.0.txt>.
*/

(function ($, window, document, Date) {
    'use strict';

    var _setupErr = 'MonthPicker Error: ';
    // This test must be run before any rererence is made to jQuery.
    // In case the user didn't load jQuery or jQuery UI the plugin
    // will fail before it get's to this test + there is no reason
    // to perform this test for every MonthPicker instance being created.
    if (!$ || !$.ui || !$.ui.button || !$.ui.datepicker) {
        alert(_setupErr + 'The jQuery UI button and datepicker plug-ins must be loaded.');
        return;
    }

    // Creates an alias to jQuery UI's .button() that dosen't
    // conflict with Bootstrap.js button (#35)
    $.widget.bridge('jqueryUIButton', $.ui.button);

    var _speeds = $.fx.speeds;
    var _eventsNs = '.MonthPicker';
    var _textfieldClass = 'month-year-input';
    var _clearHint = 'month-picker-clear-hint';
    var _iconClass = '.ui-button-icon-primary';
    var _disabledClass = 'month-picker-disabled';
    var _todayClass = 'ui-state-highlight';
    var _selectedClass = 'ui-state-active';
    var _defaultClass = 'ui-state-default';
    var _defaultPos = { my: 'left top+1', at: 'left bottom' };
    var _RTL_defaultPos = { my: 'right top+1', at: 'right bottom' };
    var _posErr = _setupErr + 'The jQuery UI position plug-in must be loaded.';
    var _badOptValErr = _setupErr + 'Unsupported % option value, supported values are: ';
    var _badMinMaxVal =  _setupErr + '"_" is not a valid %Month value.';
    var _openedInstance = null;
    var _hasPosition = !!$.ui.position;
    var _animVals = {
        Animation: ['slideToggle', 'fadeToggle', 'none'],
        ShowAnim: ['fadeIn', 'slideDown', 'none'],
        HideAnim: ['fadeOut', 'slideUp', 'none']
    };
    var _setOptionHooks = {
        ValidationErrorMessage: '_createValidationMessage',
        Disabled: '_setDisabledState',
        ShowIcon: '_updateButton',
        Button: '_updateButton',
        ShowOn: '_updateFieldEvents',
        IsRTL: '_setRTL',
        AltFormat: '_updateAlt',
        AltField: '_updateAlt',
        StartYear: '_setPickerYear',
        MinMonth: '_setMinMonth',
        MaxMonth: '_setMaxMonth',
        SelectedMonth: '_setSelectedMonth'
    };
    var $noop = $.noop;
    var $proxy = $.proxy;
    var $datepicker = $.datepicker;
    var click = 'click' + _eventsNs;

    function _toMonth(date) {
        return date.getMonth() + (date.getFullYear() * 12);
    }

    function _toYear(month) {
        return Math.floor(month / 12);
    }

    function _stayActive() {
        $(this).addClass(_selectedClass);
    }

    function _setActive( el, state ) {
        return el[ state ? 'on' : 'off' ]('mousenter mouseout',  _stayActive )
              .toggleClass(_selectedClass, state);
    }

    function _between(month, from, until) {
        return (!from || month >= from) && (!until || month <= until);
    }

    function _encodeMonth(_inst, _val) {
        if (_val === null) {
            return _val;
        } else if (_val instanceof Date) {
            return _toMonth(_val);
        } else if ($.isNumeric(_val)) {
            return _toMonth(new Date) + parseInt(_val, 10);
        }

        var _date = _inst._parseMonth(_val);
        if (_date) {
            return _toMonth(_date);
        }

        return _parsePeriod(_val);
    }

    function _event(_name, _inst) {
        return $proxy(_inst.options[_name] || $noop, _inst.element[0]);
    }

    function _parsePeriod(_val, _initDate) {
        // Parsing is done by replacing tokens in the value to form
        // a JSON object with it's keys and values reversed
        // (example '+1y +2m' will turn into {"+1":"y","+2":"m"})
        // After that we just revers the keys and values.
        var _json = $.trim(_val);
        _json = _json.replace(/y/i, '":"y"');
        _json = _json.replace(/m/i, '":"m"');

        try {
            var _rev = JSON.parse( '{"' + _json.replace(/ /g, ',"') + '}' ), obj = {};

            for (var key in _rev) {
                obj[ _rev[key] ] = key;
            }

            var _month = _toMonth(new Date);
            _month += (parseInt(obj.m, 10) || 0);
            return _month + (parseInt(obj.y, 10) || 0) * 12;
        } catch (e) {
            return false;
        }
    }

    function _makeDefaultButton(options) {
        // this refers to the associated input field.
        return $('<span id="MonthPicker_Button_' + this.id + '" class="month-picker-open-button">' + options.i18n.buttonText + '</span>')
            .jqueryUIButton({
                text: false,
                icons: {
                    // Defaults to 'ui-icon-calculator'.
                    primary: options.ButtonIcon
                }
            });
    }

    function _applyArrowButton($el, dir) {
        $el.jqueryUIButton('option', {
            icons: {
                primary: 'ui-icon-circle-triangle-' + (dir ? 'w' : 'e')
            }
        });
    }

    function _isInline(elem) {
        return !elem.is('input');
    }

    $.MonthPicker = {
        VERSION: '3.0.4', // Added in version 2.4;
        i18n: {
            year: 'Year',
            prevYear: 'Previous Year',
            nextYear: 'Next Year',
            next12Years: 'Jump Forward 12 Years',
            prev12Years: 'Jump Back 12 Years',
            nextLabel: 'Next',
            prevLabel: 'Prev',
            buttonText: 'Open Month Chooser',
            jumpYears: 'Jump Years',
            backTo: 'Back to',
            months: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'June', 'July', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.']
        }
    };

    var _markup =
        '<div class="ui-widget-header month-picker-header ui-corner-all">' +
            '<table class="month-picker-year-table">' +
                '<tr>' +
                    '<td class="month-picker-previous"><a /></td>' +
                    '<td class="month-picker-title"><a /></td>' +
                    '<td class="month-picker-next"><a /></td>' +
                '</tr>' +
            '</table>' +
        '</div>' +
        '<div>' +
            '<table class="month-picker-month-table" />' +
        '</div>';

    // Groups state and functionallity to fade in the jump years hint
    // when the user mouses over the Year 2016 text.
    // NOTE: An invocation of this function:
    // 1: Is an independent instance with it's own unique state.
    // 2: Assumes that there is no previous hint applied to the
    //    button (it dosen't remove the existing hint).
    function _applyButtonHint(_button, _hintText) {
      var _speed = 125, _currentLabel, _startTimeout, _labelElem = $();

      _button.on('mouseenter' + _eventsNs + 'h', _prepareToStart);

      // Setp 1: Wait to make sure the user isn't just mousing over and
      // away from the button.
      // NOTE: If _fadeOutHint() is triggered on mouseleave before the
      // timeout is triggered the animation is canceled.
      function _prepareToStart() {
        _startTimeout = setTimeout(_fadeOutLabel, 175);
      }

      // Setp 2: Fade out the label (Year 2016) text to 45%.
      function _fadeOutLabel() {
        _startTimeout = null;
        _labelElem = $('span', _button).animate({ opacity: 0.45 }, _speed, _fadeInHint);
      }

      // Setp 3: Fade in the hint text (Jump years).
      function _fadeInHint() {
        _currentLabel = _labelElem.text();
        _labelElem.animate({ opacity: 1 }, _speed).text(_hintText);
      }

      _button.on('mouseleave' + _eventsNs + 'h', _fadeOutHint);

      function _fadeOutHint() {
        if (_startTimeout) {
          // If the user is just moving over and away from the button, cancel
          // the animation completely.
          clearTimeout(_startTimeout);
        } else {
          // Setp 4: Fade out the hint text (Jump years) to 45%.
          _labelElem = $('span', _button).animate({ opacity: 0.45 }, _speed, _fadeInLabel);
        }
      }

      // Setp 5: Fade in the label (Year 2016) text.
      function _fadeInLabel() {
        _labelElem.text( _currentLabel ).animate({opacity: 1}, _speed);
      }

      // Adds a function to the button elemene which is called when the
      // user clicks the button (the hint needs to be removed).
      _button.data(_clearHint, function() {
        clearTimeout(_startTimeout);
        _labelElem.stop().css({ opacity: 1 });
        _button.off(_eventsNs + 'h');
      });
    } // End _applyButtonHint()

    function _setDisabled(_button, _value) {
      var _btnWidget = _button.data('ui-button');
      if (_btnWidget.option('disabled') !== _value) {
        _btnWidget.option('disabled', _value);
      }
    }

    $.widget("KidSysco.MonthPicker", {

        /******* Properties *******/

        options: {
            i18n: {},
            IsRTL: false,
            Position: null,
            StartYear: null,
            ShowIcon: true,
            UseInputMask: false,
            ValidationErrorMessage: null,
            Disabled: false,
            MonthFormat: 'mm/yy',
            Animation: 'fadeToggle',
            ShowAnim: null,
            HideAnim: null,
            ShowOn: null,
            MinMonth: null,
            MaxMonth: null,
            Duration: 'normal',
            Button: _makeDefaultButton,
            ButtonIcon: 'ui-icon-calculator'
        },

        _monthPickerButton: $(),
        _validationMessage: $(),
        _selectedBtn: $(),

        /******* jQuery UI Widget Factory Overrides ********/

        _destroy: function () {
            var _elem = this.element;
            if ($.mask && this.options.UseInputMask) {
                _elem.unmask();

                if (!this.GetSelectedDate()) {
                    _elem.val('');
                }
            }

            _elem.removeClass(_textfieldClass).off(_eventsNs);

            $(document).off(_eventsNs + this.uuid);

            this._monthPickerMenu.remove();

            var _button = this._monthPickerButton.off(click);
            if (this._removeOldBtn) {
                _button.remove();
            }

            this._validationMessage.remove();

            if (_openedInstance === this) {
              _openedInstance = null;
            }
        },

        _setOption: function (key, value) {
            switch (key) {
                case 'i18n':
                    // Pass a clone i18n object to the this._super.
                    value = $.extend({}, value);
                    break;
                case 'Position':
                    if (!_hasPosition) {
                        alert(_posErr);
                        return;
                    }
                    break;
                case 'MonthFormat':
                    var date = this.GetSelectedDate();
                    if (date) {
                        this.element.val( this.FormatMonth(date, value) );
                    }
                    break;
            }

            // Make sure the user passed in a valid Animation, ShowAnim and HideAnim options values.
            if (key in _animVals && $.inArray(value, _animVals[key]) === -1) {
                alert(_badOptValErr.replace(/%/, key) + _animVals[key]);
                return;
            }

            // In jQuery UI 1.8, manually invoke the _setOption method from the base widget.
            //$.Widget.prototype._setOption.apply(this, arguments);
            // In jQuery UI 1.9 and above, you use the _super method instead.
            this._super(key, value);

            _setOptionHooks[key] ? this[ _setOptionHooks[key] ](value) : 0;
        },

        _create: function () {
            var _el = this.element, _opts = this.options, _type = _el.attr('type');
            // According to http://www.w3.org/TR/html-markup/input.html#input
            // An input element with no type attribute specified represents the same thing as an
            // input element with its type attribute set to "text".
            // TLDR:
            // http://www.w3.org/TR/html5/forms.html#the-input-element
            // https://api.jquery.com/text-selector/

            // $.inArray(void 0, ['text', 'month', void 0]) returns -1 when searching for undefined in IE8 (#45)
            // This is only noticable in the real version of IE8, emulated versions
            // from the dev tools in modern browsers do not suffer from this issue.
            // if (!_el.is('input,div,span') || $.inArray(_el.attr('type'), ['text', 'month', void 0]) === -1) {
            if (!_el.is('input,div,span') || (_type !== 'text' && _type !== 'month' && _type !==  void 0)) {
                var error = _setupErr + 'MonthPicker can only be called on text or month inputs.';
                // Call alert first so that IE<10 won't trip over console.log and swallow all errors.
                alert(error + ' \n\nSee (developer tools) for more details.');

                console.error(error + '\n Caused by:');
                console.log(_el[0]);
                return false;
            }

            if (!$.mask && _opts.UseInputMask) {
                alert(_setupErr + 'The UseInputMask option requires the Input Mask Plugin. Get it from digitalbush.com');
                return false;
            }

            if (_opts.Position !== null && !_hasPosition) {
                alert(_posErr);
                return false;
            }

            // Make sure the user passed in a valid Animation, ShowAnim and HideAnim options values.
            for (var opt in _animVals) {
                if (_opts[opt] !== null && $.inArray(_opts[opt], _animVals[opt]) === -1) {
                    alert(_badOptValErr.replace(/%/, opt) + _animVals[opt]);
                    return false;
                }
            }

            this._isMonthInputType = _el.attr('type') === 'month';
            if (this._isMonthInputType) {
                this.options.MonthFormat = this.MonthInputFormat;
                _el.css('width', 'auto');
            }

            var _menu = this._monthPickerMenu = $('<div id="MonthPicker_' + _el[0].id + '" class="month-picker ui-widget ui-widget-content ui-corner-all"></div>').hide();
            var isInline = _isInline(_el);

            $(_markup).appendTo(_menu);
            _menu.appendTo( isInline ? _el : document.body );

            this._titleButton =
                $('.month-picker-title', _menu)
                .click($proxy(this._showYearsClickHandler, this))
                .find('a').jqueryUIButton()
                .removeClass(_defaultClass);

            this._applyJumpYearsHint();
            this._createValidationMessage();

            this._prevButton = $('.month-picker-previous>a', _menu)
              .jqueryUIButton({ text: false })
              .removeClass(_defaultClass);

            this._nextButton = $('.month-picker-next>a', _menu)
              .jqueryUIButton({ text: false })
              .removeClass(_defaultClass);

            this._setRTL(_opts.IsRTL); //Assigns icons to the next/prev buttons.

            $(_iconClass, this._nextButton).text(this._i18n('nextLabel'));
            $(_iconClass, this._prevButton).text(this._i18n('prevLabel'));

            var $table = $('.month-picker-month-table', _menu);
            for (var i = 0; i < 12; i++) {
                var $tr = !(i % 3) ? $('<tr />').appendTo($table) : $tr;

                // Use <a> tag instead of <button> to avoid issues
                // only with Google Chrome (#50).
                $tr.append('<td><a class="button-' + (i + 1) + '" /></td>');
            }

            this._buttons = $('a', $table).jqueryUIButton();

            _menu.on('mousedown' + _eventsNs, function (event) {
                event.preventDefault();
            });

            // Checks and initailizes Min/MaxMonth properties
            // (creates _setMinMonth and _setMaxMonth methods).
            var me = this, Month = 'Month';
            $.each(['Min', 'Max'], function(i, type) {
                me["_set" + type + Month] = function(val) {
                    if ((me['_' + type + Month] = _encodeMonth(me, val)) === false) {
                        alert(_badMinMaxVal.replace(/%/, type).replace(/_/, val));
                    }
                };

                me._setOption(type + Month, me.options[type + Month]);
            });

            var _selMonth = _opts.SelectedMonth;
            if (_selMonth !== void 0) {
                var month = _encodeMonth(this, _selMonth);
                _el.val( this._formatMonth(new Date( _toYear(month), month % 12, 1)) );
            }

            this._updateAlt();

            this._setUseInputMask();
            this._setDisabledState();
            this.Destroy = this.destroy;

            if (isInline) {
                this.Open();
            } else {
               // Update the alt field if the user manually changes
               // the input field.
               _el.addClass(_textfieldClass);
               _el.change($proxy(this._updateAlt, this));
            }
        },

        /****** Publicly Accessible API functions ******/

        GetSelectedDate: function () {
            return this._parseMonth();
        },

        GetSelectedYear: function () {
            var date = this.GetSelectedDate();
            return date ? date.getFullYear() : NaN;
        },

        GetSelectedMonth: function () {
            var date = this.GetSelectedDate();
            return date ? date.getMonth()+1 : NaN;
        },

        Validate: function() {
            var _date = this.GetSelectedDate();

            if (this.options.ValidationErrorMessage !== null && !this.options.Disabled) {
                this._validationMessage.toggle(!_date);
            }

            return _date;
        },

        GetSelectedMonthYear: function () {
            var date = this.Validate();
            return date ? (date.getMonth() + 1) + '/' + date.getFullYear() : null;
        },

        Disable: function () {
            this._setOption("Disabled", true);
        },

        Enable: function () {
            this._setOption("Disabled", false);
        },

        ClearAllCallbacks: function () {
            for (var _opt in this.options) {
                if (_opt.indexOf('On') === 0) {
                    this.options[_opt] = $noop;
                }
            }
        },

        Clear: function () {
            this.element.val('');
            $(this.options.AltField).val('');
            this._validationMessage.hide();
        },

        Toggle: function (event) {
            return this._visible ? this.Close(event) : this.Open(event);
        },

        Open: function (event) {
            var _elem = this.element, _opts = this.options;
            if (!_opts.Disabled && !this._visible) {
                // Allow the user to prevent opening the menu.
                event = event || $.Event();
                if (_event('OnBeforeMenuOpen', this)(event) === false || event.isDefaultPrevented()) {
                    return;
                }

                this._visible = true;
                this._ajustYear(_opts);

                var _menu = this._monthPickerMenu;
                this._showMonths();

                if (_isInline(_elem)) {
                    _menu.css('position', 'static').show();
                    _event('OnAfterMenuOpen', this)();
                } else {
                    // If there is an open menu close it first.
                    if (_openedInstance) {
                        _openedInstance.Close(event);
                    }

                    _openedInstance = this;
                    $(document).on('mousedown' + _eventsNs + this.uuid, $proxy(this.Close, this))
                               .on('keydown' + _eventsNs + this.uuid, $proxy(this._keyDown, this));

                    // Trun off validation so that clicking one of the months
                    // won't blur the input field and trogger vlaidation
                    // befroe the month was chosen (click event was triggered).
                    // It is turned back on when Hide() is called.
                    _elem.off('blur' + _eventsNs).focus();

                    var _anim = _opts.ShowAnim || _opts.Animation,
                        _noAnim = _anim === 'none';

                    // jQuery UI overrides jQuery.show and dosen't
                    // call the start callback.
                    // see: http://api.jqueryui.com/show/
                    _menu[ _noAnim ? 'fadeIn' : _anim ]({
                       duration: _noAnim ? 0 : this._duration(),
                       start: $proxy(this._position, this, _menu),
                       complete: _event('OnAfterMenuOpen', this)
                    });
                }
            }
        },

        Close: function (event) {
            var _elem = this.element;
            if (!_isInline(_elem) && this._visible) {
                var _menu = this._monthPickerMenu,
                    _opts = this.options;

                event = event || $.Event();
                if (_event('OnBeforeMenuClose', this)(event) === false || event.isDefaultPrevented()) {
                    return;
                }

                // If the menu is closed while in jump years mode, bring back
                // the jump years hint.
                if (this._backToYear) {
                  this._applyJumpYearsHint();
                  this._backToYear = 0;
                }

                this._visible = false;
                _openedInstance = null;
                $(document).off('keydown' + _eventsNs + this.uuid)
                           .off('mousedown' + _eventsNs + this.uuid);

                this.Validate();
                _elem.on('blur' + _eventsNs, $proxy(this.Validate, this));
                var _callback = _event('OnAfterMenuClose', this);

                var _anim = _opts.HideAnim || _opts.Animation;
                if (_anim === 'none') {
                    _menu.hide(0, _callback);
                } else {
                    _menu[ _anim ](this._duration(), _callback);
                }
            }
        },

        /**
         * Methods the user can override to use a third party library
         * such as http://momentjs.com for parsing and formatting months.
         */
        MonthInputFormat: 'yy-mm',

        ParseMonth: function (str, format) {
            try {
                return $datepicker.parseDate('dd' + format, '01' + str);
            } catch (e) {
                return null;
            }
        },

        FormatMonth: function (date, format) {
            try {
                return $datepicker.formatDate(format, date) || null;
            } catch (e) {
                return null;
            }
        },

        /****** Private and Misc Utility functions ******/

        _setSelectedMonth: function (_selMonth) {
            var month = _encodeMonth(this, _selMonth), _el = this.element;

            if (month) {
                var date = new Date( _toYear(month), month % 12, 1 );
                _el.val( this._formatMonth( date ) );

                this._updateAlt(0, date);
                this._validationMessage.hide();
            } else {
                this.Clear();
            }

            this._ajustYear(this.options);
            this._showMonths();
        },

        _applyJumpYearsHint: function() {
          _applyButtonHint(this._titleButton, this._i18n('jumpYears'));
        },

        _i18n: function(str) {
          var _trans = this.options.i18n[str];

          if (typeof _trans === 'undefined') {
            return $.MonthPicker.i18n[str];
          } else {
            return _trans;
          }
        },

        _parseMonth: function (str, format) {
            return this.ParseMonth(str || this.element.val(), format || this.options.MonthFormat);
        },

        _formatMonth: function (date, format) {
            return this.FormatMonth(date || this._parseMonth(), format || this.options.MonthFormat);
        },

        _updateButton: function () {
            var isDisabled = this.options.Disabled;

            this._createButton();

            // If the button is a jQuery UI button,
            // plain HTML button or an input we support disable it,
            // otherwise the user must handle the diabled state
            // by creating an appropriate button by passing
            // a function. See Button option: Img tag tests for
            // more details.
            var _button = this._monthPickerButton;
            try {
                _button.jqueryUIButton('option', 'disabled', isDisabled);
            } catch (e) {
                _button.filter('button,input').prop('disabled', isDisabled);
            }

            this._updateFieldEvents();
        },

        _createButton: function () {
            var _elem = this.element, _opts = this.options;
            if (_isInline(_elem)) return;

            var _oldButton = this._monthPickerButton.off(_eventsNs);
            var _btnOpt = _opts.ShowIcon ? _opts.Button : false;

            if ($.isFunction(_btnOpt)) {
                var _params = $.extend(true, {i18n: $.extend(true, {}, $.MonthPicker.i18n)}, this.options);
                _btnOpt = _btnOpt.call(_elem[0], _params);
            }

            var _removeOldBtn = false;
            this._monthPickerButton = ( _btnOpt instanceof $ ? _btnOpt : $(_btnOpt) )
                .each(function() {
                    if (!$.contains(document.body, this)) {
                        _removeOldBtn = true;
                        $(this).insertAfter(_elem);
                    }
                })
                .on(click, $proxy(this.Toggle, this))
                .on('mousedown' + _eventsNs, function(r) {
                  r.preventDefault();
                });

            if (this._removeOldBtn) {
                _oldButton.remove();
            }

            this._removeOldBtn = _removeOldBtn;
        },

        _updateFieldEvents: function () {
            var _events = click + ' focus' + _eventsNs;
            this.element.off(_events);
            if (this.options.ShowOn === 'both' || !this._monthPickerButton.length) {
                this.element.on(_events, $proxy(this.Open, this));
            }
        },

        _createValidationMessage: function () {
            var _errMsg = this.options.ValidationErrorMessage, _elem = this.element;
            if ($.inArray(_errMsg, [null, '']) === -1) {
                var _msgEl = $('<span id="MonthPicker_Validation_' + _elem[0].id + '" class="month-picker-invalid-message">' + _errMsg + '</span>');

                var _button = this._monthPickerButton;
                this._validationMessage = _msgEl.insertAfter(_button.length ? _button : _elem);

                _elem.on('blur' + _eventsNs, $proxy(this.Validate, this));
            } else {
                this._validationMessage.remove();
            }
        },

        _setRTL: function(value) {
            _applyArrowButton( this._prevButton.css('float', value ? 'right' : 'left'), !value );
            _applyArrowButton( this._nextButton.css('float', value ? 'left' : 'right'), value );
        },

        _keyDown: function (event) {
            // Don't use $.ui.keyCode to help minification.
            switch (event.keyCode) {
                case 13: // Enter.
                    if (!this.element.val()) {
                        this._chooseMonth(new Date().getMonth() + 1);
                    }
                    this.Close(event);
                    break;
                case 27: // Escape
                case 9: // Tab
                    this.Close(event);
                    break;
            }
        },

        _duration: function() {
            var _dur = this.options.Duration;

            if ($.isNumeric(_dur)) {
                return _dur;
            }

            return _dur in _speeds ? _speeds[ _dur ] : _speeds._default;
        },

        _position: _hasPosition ?
            function($menu) {
                var _defauts = this.options.IsRTL ? _RTL_defaultPos : _defaultPos;
                var _posOpts = $.extend(_defauts, this.options.Position);

                // Only in IE and jQuery 1.12.0 or 2.2.0, .position() will add scrollTop to the top coordinate (#40)
                return $menu.position($.extend({of: this.element}, _posOpts));
            } :
            function($menu) {
                // Only in IE and jQuery 1.12.0 or 2.2.0, .offset() will add scrollTop to the top coordinate (#40)
                var _el = this.element,
                    _css = { top: (_el.offset().top + _el.height() + 7) + 'px' };

                if (this.options.IsRTL) {
                    _css.left = (_el.offset().left-$menu.width()+_el.width() + 7) + 'px';
                } else {
                    _css.left = _el.offset().left + 'px';
                }

                return $menu.css(_css);
            },

        _setUseInputMask: function () {
            if (!this._isMonthInputType) {
                try {
                    if (this.options.UseInputMask) {
                        this.element.mask( this._formatMonth(new Date).replace(/\d/g, 9) );
                    } else {
                        this.element.unmask();
                    }
                } catch (e) {}
            }
        },

        _setDisabledState: function () {
            var isDisabled = this.options.Disabled, _elem = this.element;

            // Disable the associated input field.
            _elem[0].disabled = isDisabled;
            _elem.toggleClass(_disabledClass, isDisabled);

            if (isDisabled) {
                this._validationMessage.hide();
            }

            this.Close();
            this._updateButton();

            _event('OnAfterSetDisabled', this)(isDisabled);
        },

        _getPickerYear: function () {
            return this._pickerYear;
        },

        _setPickerYear: function (year) {
            this._pickerYear = year || new Date().getFullYear();
            this._titleButton.jqueryUIButton({ label: this._i18n('year') + ' ' + this._pickerYear });
        },

        // When calling this method with a falsy (undefined) date
        // value, this.element.val() is used as the date value.
        //
        // Therefore it's important to update the input field
        // before calling this method.
        _updateAlt: function (noop, date) {
            var _field = $(this.options.AltField);
            if (_field.length) {
                _field.val( this._formatMonth(date, this.options.AltFormat) );
            }
        },

        _chooseMonth: function (month) {
            var _year = this._getPickerYear();
            var date = new Date(_year, month-1);
            this.element.val(this._formatMonth( date )).blur();
            this._updateAlt(0, date);

            _setActive( this._selectedBtn, false );
            this._selectedBtn = _setActive( $(this._buttons[month-1]), true );

            _event('OnAfterChooseMonth', this)(date);
        },

        _chooseYear: function (year) {
            this._backToYear = 0;
            this._setPickerYear(year);
            this._buttons.removeClass(_todayClass);
            this._showMonths();
            this._applyJumpYearsHint();

            _event('OnAfterChooseYear', this)();
        },

        _showMonths: function () {
            var _months = this._i18n('months');

            this._prevButton
                .attr('title', this._i18n('prevYear'))
                .off(click)
                .on(click, $proxy(this._addToYear, this, -1));

            this._nextButton
                .attr('title', this._i18n('nextYear'))
                .off(click)
                .on(click, $proxy(this._addToYear, this, 1));

            this._buttons.off(_eventsNs);

            var me = this, _onMonthClick = $proxy(me._onMonthClick, me);
            $.each(_months, function(index, monthName) {
                $(me._buttons[index])
                    .on(click, {month: index+1}, _onMonthClick )
                    .jqueryUIButton('option', 'label', monthName);
            });

            this._decorateButtons();
        },

        _showYearsClickHandler: function () {
            this._buttons.removeClass(_todayClass);
            if (!this._backToYear) {
                this._backToYear = this._getPickerYear();
                this._showYears();

                var _label = this._i18n('backTo') + ' ' + this._getPickerYear();
                this._titleButton.jqueryUIButton({ label: _label }).data( _clearHint )();

                _event('OnAfterChooseYears', this)();
            } else {
                this._setPickerYear(this._backToYear);
                this._applyJumpYearsHint();
                this._showMonths();
                this._backToYear = 0;
            }
        },

        _showYears: function () {
            var _currYear = this._getPickerYear(),
                _yearDifferential = -4,
                _firstYear = (_currYear + _yearDifferential),
                AMOUNT_TO_ADD = 12,
                _thisYear = new Date().getFullYear();

            var _minDate = this._MinMonth;
            var _maxDate = this._MaxMonth;
            var _minYear = _minDate ? _toYear(_minDate) : 0;
            var _maxYear = _maxDate ? _toYear(_maxDate) : 0;
            this._prevButton
                .attr('title', this._i18n('prev12Years'))
                .off(click)
                .on(click, $proxy(this._addToYears, this, -AMOUNT_TO_ADD));

            this._nextButton
                .attr('title', this._i18n('next12Years'))
                .off(click)
                .on(click, $proxy(this._addToYears, this, AMOUNT_TO_ADD));

            _setDisabled(this._prevButton, _minYear && (_firstYear - 1) < _minYear);
            _setDisabled(this._nextButton, _maxYear && (_firstYear + 12) -1 > _maxYear);

            this._buttons.off(_eventsNs);

            _setActive( this._selectedBtn, false );

            var _selYear = this.GetSelectedYear();
            var _onClick = $proxy(this._onYearClick, this);
            var _todayWithinBounds = _between(_thisYear, _minYear, _maxYear);
            var _selWithinBounds = _between(_selYear, _minYear, _maxYear);

            for (var _counter = 0; _counter < 12; _counter++) {
                var _year = _currYear + _yearDifferential;

                var _btn = $( this._buttons[_counter] ).jqueryUIButton({
                        disabled: !_between(_year, _minYear, _maxYear),
                        label: _year
                    })
                    .toggleClass(_todayClass, _year === _thisYear && _todayWithinBounds) // Heighlight the current year.
                    .on(click, { year: _year }, _onClick );

                 // Heighlight the selected year.
                if (_selWithinBounds && _selYear && _selYear === _year) {
                    this._selectedBtn = _setActive( _btn , true );
                }

                _yearDifferential++;
            }
        },

        _onMonthClick: function(event) {
            this._chooseMonth(event.data.month);
            this.Close(event);
        },

        _onYearClick: function(event) {
            this._chooseYear(event.data.year);
        },

        _addToYear: function(amount) {
            this._setPickerYear( this._getPickerYear() + amount );
            this.element.focus();
            this._decorateButtons();

            _event('OnAfter' + (amount > 0 ? 'Next' : 'Previous') + 'Year', this)();
        },

        _addToYears: function(amount) {
            this._pickerYear = this._getPickerYear() + amount;
            this._showYears();
            this.element.focus();

            _event('OnAfter' + (amount > 0 ? 'Next' : 'Previous') + 'Years', this)();
        },

        _ajustYear: function(_opts) {
            var _year = _opts.StartYear || this.GetSelectedYear() || new Date().getFullYear();
            if (this._MinMonth !== null) {
                _year = Math.max(_toYear(this._MinMonth), _year);
            }
            if (this._MaxMonth !== null) {
                _year = Math.min(_toYear(this._MaxMonth), _year);
            }

            this._setPickerYear( _year );
        },

        _decorateButtons: function() {
            var _curYear = this._getPickerYear(), _todaysMonth = _toMonth(new Date),
                _minDate = this._MinMonth, _maxDate = this._MaxMonth;

            // Heighlight the selected month.
            _setActive( this._selectedBtn, false );
            var _sel = this.GetSelectedDate();
            var _withinBounds = _between(_sel ? _toMonth(_sel) : null, _minDate, _maxDate);

            if (_sel && _sel.getFullYear() === _curYear) {
                this._selectedBtn = _setActive( $(this._buttons[_sel.getMonth()]) , _withinBounds );
            }

            // Disable the next/prev button if we've reached the min/max year.
            _setDisabled(this._prevButton,  _minDate && _curYear == _toYear(_minDate));
            _setDisabled(this._nextButton,  _maxDate && _curYear == _toYear(_maxDate));

            for (var i = 0; i < 12; i++) {
                // Disable the button if the month is not between the
                // min and max interval.
                var _month = (_curYear * 12) + i, _isBetween = _between(_month, _minDate, _maxDate);

                $(this._buttons[i])
                    .jqueryUIButton({ disabled: !_isBetween })
                    .toggleClass(_todayClass, _isBetween && _month == _todaysMonth); // Highlights today's month.
            }
        }
    });
}(jQuery, window, document, Date));
