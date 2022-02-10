/**
 * jquery.simple-combobox v1.1.26 (2015-11-05): jQuery combobox plugin | (c) 2014-2015 Ilya Kremer
 * MIT license http://www.opensource.org/licenses/mit-license.php
 */

// Fill free to use this jQuery plugin in any projects you want
// while keeping the comment above on top of the script.
// Don't forget not to remove it from a minimised version also.
// Thank you!

// TODO consider to use markup when filling combobox from original select options
// TODO consider to add fadeout background for items (checkboxes mode)
// TODO implement items removal (for infinite number of items)

/**
 * Original plugin structure taken and extended from http://stackoverflow.com/a/6871820/837165
 * See and change default options at the end of the code or use
 * $.scombobox.extendDefaults(options) method if you don't feel like
 * touching the original plugin source code.
 * This plugin uses following JS native methods:
 *
 * String.prototype.trim()
 * Array.prototype.indexOf()
 * Object.keys()
 * console object
 *
 * so don't forget to add them to your project for better browser compatibility.
 * You can use missed.js file for that purpose from original GitHub project:
 * https://github.com/ivkremer/jquery-simple-combobox
 *
 * This plugin adds click listener on document, so don't forget to check if events
 * can rich it or use scombobox.close method.
 * @param {Object} $ jQuery reference
 * @param {Object} document (HTMLDocument)
 * @returns {undefined}
 */
(function($, document) {
    'use strict';
    var pname = 'scombobox'; // plugin name, don't forget to change css prefixes if necessary
    var cp = '.' + pname;
    var cdisplay = '-display',
        cvalue = '-value',
        cinvalid = '-invalid',
        cdiv = cdisplay + '-div',
        cditem = cdiv + '-item',
        cdiremove = cditem + '-remove',
        cdholder = cdiv + '-holder',
        clist = '-list',
        cmainspan = '-mainspan',
        chovered = '-hovered',
        csep = '-separator',
        cpheader = '-header',
        cddback = '-dropdown-background',
        cddarr = '-dropdown-arrow',
        cdisabled = '-disabled',
        crequired = '-required';

    function durations(d) {
        return ({
            fast: 200,
            normal: 400,
            slow: 600
        })[d] || d;
    }
    var pInt = parseInt;
    var methods = {
        /**
         * Initializes the combobox.
         * @returns {Object} jQuery object
         */
        init: function() {
            var $div = this.find(cp + clist),
                $select = this.find('select'),
                $dropdownBack = this.find(cp + cddback),
                $dropdownArr = this.find(cp + cddarr);
            var opts = this.data(pname);
            this.addClass(pname);
            if ($select.length == 0) {
                this.append($('<select />'));
            }
            if (this.attr('id')) {
                $select.removeAttr('id');
            }
            if ($select.attr('multiple')) {
                this.data(pname).mode = 'checkboxes';
            }
            if ($dropdownBack.length == 0) {
                this.append('<div class="' + pname + cddback + '" />');
            }
            if ($dropdownArr.length == 0) {
                this.append('<div class="' + pname + cddarr + '" />');
            }
            methods.displayDropdown.call(this, opts.showDropDown);
            if (opts.mode != 'checkboxes') {
                if (this.find(cp + cdisplay).length == 0) {
                    var $inputDisplay = $('<input class="' + pname + cdisplay + '" type="text" />');
                    $inputDisplay.attr('title', $select.attr('title'));
                    $inputDisplay.attr('placeholder', opts.placeholder);
                    this.append($inputDisplay);
                    this.height(+$inputDisplay.css('font-size') +
                        +$inputDisplay.css('padding-top') +
                        +$inputDisplay.css('padding-bottom')
                    );
                }
            }
            if (opts.tabindex != null) {
                this.find(cp + cdisplay).attr('tabindex', opts.tabindex);
            }
            if (this.find(cp + cvalue).length == 0) {
                this.append('<input class="' + pname + cvalue + '" type="hidden" />');
            }
            if (this.find(cp + cdisplay).is(':disabled') || opts.disabled) {
                this.find(cp + cddback + ', ' + cp + cddarr).hide();
            }
            if (opts.disabled) {
                this.find(cp + cdisplay).prop('disabled', true);
                this.addClass(pname + cdisabled);
            }
            if ($select.attr('required') || opts.required) {
                this.find(cp + cdisplay).prop('required', 'required');
                this.addClass(pname + crequired);
            }
            if ($div.length == 0) {
                this.append($div = $('<div class="' + pname + clist + '"></div>'));
            }
            if (opts.mode == 'checkboxes') {
                this.addClass(pname + '-checkboxes');
                this.find(cp + cdisplay).remove();
                var $displayDiv = this.find(cp + cdisplay + '-div');
                if ($displayDiv.length == 0) {
                    $displayDiv = this.append('<div class="' + pname + cdiv + '"><div class="' + pname + cdholder + '" /></div>');
                }
                $displayDiv.attr('title', $select.attr('title'));
                $div.insertAfter(this.find(cp + cdisplay + '-div'));
                var $dholder = this.find(cp + cdholder);
                var $testItem = $('<div class="' + pname + cditem + '" id="' + pname + '-test-item"><div class="' + pname + cditem + '-text">x</div></div>');
                $dholder.append($testItem.css('margin-left', '-9999px').show());
                var height = $testItem.height() + pInt($testItem.css('padding-top')) + pInt($testItem.css('padding-top')) + pInt($testItem.css('margin-top')) + pInt($testItem.css('margin-top')) + pInt($testItem.css('border-top-width')) + pInt($testItem.css('border-top-width')) + pInt($dholder.css('padding-top')) + pInt($dholder.css('padding-top'));
                this.find(cp + cdisplay + '-div').css('min-height', height + 'px');
                $testItem.remove();
            } else {
                this.find(cp + '-display-div').remove();
                $div.insertAfter(this.find(cp + cdisplay));

            }
            $div.css({ 'max-width': opts.listMaxWidth, 'max-height': opts.maxHeight });
            if (opts.wrap == true) {
                $div.css('white-space', 'normal');
            }
            if (opts.autoLoad != $.noop) {
                opts.loopScrolling = false; // there is no way to support this feature when auto loading more items
            }
            addListeners.call(this);
            this.data(pname + '-init', true); // true says that it is right after initialization, it is necessary for callback
            return methods.fill.call(this, opts.data); // (will be set to false after filling)
        },
        /**
         * Fills the combobox with specified data or using options list in select if no data given.
         * @see comments in defaults
         * @param {Array} data array of data objects. See comments in defaults
         * @param {Number} appendMode flag defining if to append (1) or prepend (2) data to existing items
         * @returns {Object} jQuery object
         */
        fill: function(data, appendMode) {
            var $options = this.find('select').children('option, optgroup');
            // don't ever rely on div content, always use select options instead
            var $div = this.find('.' + pname + clist),
                $select = this.find('select');
            data = normalizeData(data);
            var opts = this.data(pname);
            var mode = opts.mode;
            if (!data) { // no data were given; get data from select options
                if (opts.removeDuplicates) {
                    removeDupsjQ($options);
                    purifyOptions($options);
                    $options = this.find('select').children('option, optgroup'); // update after removal
                }
                if ($options.length == 0) {
                    // TODO restore, using $p.data(pname).key if provided instead
                } else { // here are options:
                    $options.each(function() {
                        var $t = $(this);
                        var $p = $('<p />');
                        $p.attr('title', $t.attr('title'));
                        if ($t.hasClass(pname + csep)) { // separator, not an option
                            if ($t.hasClass(pname + cpheader)) { // if header text also given then add only header
                                $div.append($p.addClass(pname + cpheader).text($t.text()));
                            } else { // else add separator itself
                                $p.addClass(pname + csep);
                            }
                        } else if (this.tagName.toLowerCase() == 'optgroup') {
                            var label = $t.attr('label');
                            var $innerOptions = $('option', this);
                            $t.before('<option />'); // don't know why after doesn't work correctly
                            $t.after($innerOptions); // unwrap it
                            $t.remove(); // remove optgroup tag itself
                            $div.append(label ? $p.addClass(pname + cpheader).text(label) : $p.addClass(pname + csep));
                            $innerOptions.each(function() {
                                $div.append($('<p />').attr('title', this.title).append($('<span class="' + pname + cmainspan + '" />').text($(this).text())).data('value', this.value));
                            });
                            return;
                        } else {
                            $p.append($('<span class="' + pname + cmainspan + '" />').text($t.text())).data('value', this.value);
                            if (mode == 'checkboxes') {
                                $p.prepend('<input type="checkbox" />');
                            }
                        }
                        $div.append($p);
                    });
                }
            } else { // fill directly from given data
                if (opts.removeDuplicates) {
                    removeDups(data);
                }
                purifyData(data);
                if (opts.sort) {
                    data.sort(sortF);
                    if (!opts.sortAsc) {
                        data.reverse();
                    }
                }
                if (!appendMode) {
                    $select.empty();
                    $div.empty();
                    this.children(cp + cvalue + ', ' + cp + cdisplay).val('');
                } // TODO consider if appendMode == 2 is not a stupid piece of code
                renderItems.call(this, data, appendMode == 2); // if appendMode == 2, then it is prepend
            }
            if (this.data(pname + '-init')) {
                opts.callback.func.apply(this, opts.callback.args);
                this.data(pname + '-init', false);
            }
            $options = this.find('select').children('option'); // update
            if (!opts.empty) {
                if (mode != 'checkboxes') {
                    this[pname]('val', $options.filter('option:selected:last').val());
                } else {
                    var selectedValues = $options.filter(':selected').map(function() {
                        return $(this).val();
                    }).get();
                    this[pname]('val', selectedValues);
                }
            }
            return this;
        },
        /**
         * Removes all items from combobox (html-based removal)
         * @returns {Object} jQuery object
         */
        clear: function() { // TODO check why to or not to remove data itself
            this.children('select').empty();
            this.children(cp + clist).empty().width('');
            this.children(cp + cdisplay).removeClass(pname + cinvalid);
            this.children(cp + cddback).removeClass(pname + cddback + cinvalid);
            return this;
        },
        /**
         * Updates data without touching html items or gets the data.
         * For updating combobox contents use fill method.
         * @param {string} data
         * @returns {Object} jQuery object
         */
        data: function(data) { // this method is required because after setting new options
            // via options method the data will be merged which probably will be wrong
            if (arguments.length == 0) {
                return this.data(pname).data;
            } else {
                this.data(pname).data = data;
            }
            return this;
        },
        /**
         * Enables and disables combobox.
         * @param {Boolean} b flag
         * @returns {Object|Boolean} jQuery object or boolean desabled status.
         */
        disabled: function(b) {
            var mode = this.data(pname).mode;
            if (arguments.length == 0) {
                if (mode == 'checkboxes') {
                    return this.hasClass(pname + cdisabled);
                } else { // default mode
                    return this.children(cp + cdisplay).prop('disabled');
                }
            }
            b = !!b;
            this.children(cp + cdisplay).prop('disabled', b);
            if (b) {
                this.addClass(pname + cdisabled);
                this.children(cp + cddback + ', ' + cp + cddarr).hide();
            } else {
                this.removeClass(pname + cdisabled);
                this.children(cp + cddback + ', ' + cp + cddarr).show();
            }
            return this;
        },
        /**
         * Sets the tabindex attribute for search input.
         * @param index
         * @returns {Number|Object}
         */
        tabindex: function(index) {
            var $display = this.find(cp + cdisplay);
            if (arguments.length == 0) {
                return $display.attr('tabindex');
            } else {
                $display.attr('tabindex', index);
                return this;
            }
        },
        /**
         * Resets options or see the options. Do not use this for changing data because merging is deep, so
         * data may be merged instead of being replaced.
         * For updating data use data method.
         * @param {Object} options
         * @returns {Object} jQuery object or options object
         */
        options: function(options) {
            if (arguments.length == 0) {
                return this.data(pname);
            }
            $.extend(true, this.data(pname), toCamelCase(options));
            return this;
        },
        /**
         * Combobox value setter and getter.
         * @param {String|Array} v value
         * @returns {Object|String|Array} jQuery object or string/array combobox current value.
         * Value returns as string in the default mode and as an array of values where items were
         * checked in checkboxes mode.
         * If combobox is disabled then empty string is returned.
         */ // TODO add the second parameter: flag if trigger changing the value (now it is triggering by default)
        val: function(v) {
            var opts = this.data(pname),
                mode = opts.mode;
            if (arguments.length == 0) { // get the value
                if (mode == 'default') {
                    var value = this.find(cp + cvalue).val();
                }
                return mode == 'default' ?
                    (this.find(cp + cdisplay).is(':disabled') ? '' : value) :
                    (mode == 'checkboxes' ? getValues.call(this) : null);
            } else { // set the value
                if (mode == 'default') {
                    setValue.call(this, v);
                } else if (mode == 'checkboxes') {
                    setValues.call(this, v);
                }
            }
            return this;
        },
        open: function() {
            slide.call(this.children(cp + clist), 'down');
            return this;
        },
        close: function() {
            slide.call(this.children(cp + clist), 'up');
            return this;
        },
        /*
         * Listeners.
         * Call $('#combo').combobox('keyup', null, 'namespace');
         * to trigger an event of specific namespace.
         */
        change: function(callback, namespace) {
            return bindOrTrig.call(this, 'change', this.children(cp + cvalue), callback, namespace);
        },
        focus: function(callback, namespace) {
            return bindOrTrig.call(this, 'focus', this.children(cp + cdisplay), callback, namespace);
        },
        blur: function(callback, namespace) {
            return bindOrTrig.call(this, 'blur', this.children(cp + cdisplay), callback, namespace);
        },
        keyup: function(callback, namespace) {
            return bindOrTrig.call(this, 'keyup', this.children(cp + cdisplay), callback, namespace);
        },
        keydown: function(callback, namespace) {
            return bindOrTrig.call(this, 'keydown', this.children(cp + cdisplay), callback, namespace);
        },
        keypress: function(callback, namespace) {
            return bindOrTrig.call(this, 'keypress', this.children(cp + cdisplay), callback, namespace);
        },
        click: function(callback, namespace) {
            return bindOrTrig.call(this, 'click', this.children(cp + cdisplay), callback, namespace);
        },
        mousedown: function(callback, namespace) {
            return bindOrTrig.call(this, 'mousedown', this.children(cp + cdisplay), callback, namespace);
        },
        clickDropdown: function(callback, namespace) {
            return bindOrTrig.call(this, 'click', this.children(cp + cddarr), callback, namespace);
        },
        toSelect: function() {
            var $select = this.children('select').insertAfter(this);
            if (this.data(pname).reassignId) {
                $select.attr('id', this.attr('id'));
            }
            this.remove();
            return $select;
        },
        displayDropdown: function(b) {
            if (arguments.length) {
                if (!!b) {
                    this.children(cp + cddarr + ', ' + cp + cddback).show();
                } else {
                    this.children(cp + cddarr + ', ' + cp + cddback).hide();
                }
            } else {
                if (this.data(pname).showDropdown) {
                    this.children(cp + cddarr + ', ' + cp + cddback).show();
                } else {
                    this.children(cp + cddarr + ', ' + cp + cddback).hide();
                }
            }
            return this;
        },
        placeholder: function(text) {
            var $input = this.children(cp + cdisplay);
            if (!arguments.length) {
                return $input.attr('placeholder');
            } else {
                $input.attr('placeholder', text);
                return this;
            }
        }
    };

    function bindOrTrig(type, $element, callback, namespace) {
        if (typeof callback != 'function') { // trigger
            var action = type + (typeof callback == 'string' ? '.' + callback : (typeof namespace == 'string' ? '.' + namespace : ''));
            $element.trigger(action);
        } else { // bind
            addAdditionalListener.call($element, type, callback, namespace);
        }
        return this;
    }

    function addAdditionalListener(type, callback, namespace) {
        var action = type + (typeof namespace == 'string' ? '.' + namespace : '');
        this.bind(action, callback);
    }

    function getValues() { // for checkbox mode
        return JSON.parse(this.find(cp + cvalue).val() || '[]');
    }

    /**
     * Executes after checking a checkbox.
     * this refers to combobox.
     */ // TODO remove duplicate code if possible
    function updateValueInput() { // used for checkboxes mode only
        var $paragraphs = $(this).find(cp + clist + ' p'),
            $vInput = $(this).children(cp + cvalue),
            arrV = [];
        $paragraphs.each(function() {
            var $p = $(this);
            var $check = $p.find(':checkbox');
            if ($check.prop('checked')) {
                arrV.push($p.data('value'));
            }
        });
        $(this).children('select').val(arrV);
        $vInput.val(JSON.stringify(arrV));
    }

    function setValues(values) { // for checkboxes mode; this refers to combobox
        var $paragraphs = $(this).find(cp + clist + ' p'),
            $vInput = $(this).children(cp + cvalue),
            arrV = [];
        var $lastChecked;
        for (var i = 0; i < $paragraphs.length; i++) {
            var $p = $paragraphs.eq(i),
                ind = values.indexOf($p.data('value'));
            if (values.indexOf($p.data('value')) >= 0) {
                $lastChecked = $p.find(':checkbox').prop('checked', true);
                arrV.push(values[ind]);
            } else {
                $p.find(':checkbox').prop('checked', false);
            }
        }
        $(this).children('select').val(values);
        if ($lastChecked) {
            $lastChecked.trigger(pname + '-chupdate', [true]);
            $vInput.val(JSON.stringify(arrV));
        }
    }

    function setValue(value) { // for default mode
        var $t = $(this);
        var O = this.data(pname);
        var $select = $t.children('select'),
            $valueInput = $t.children(cp + cvalue),
            $display = $t.children(cp + cdisplay);
        //find the option whose 'value' is (=) to the given value in the select element
        var $selected = $select
            .find('option')
            .filter(function() {
                return this.value == value;
            });

        $display.removeClass(pname + cinvalid).siblings(cp + cddback).removeClass(pname + cddback + cinvalid)
        if (!$selected.length) { // no such value
            $t.find(cp + clist + ' p').removeClass(pname + chovered);
            $select.children().prop('selected', false);
            if (!O.invalidAsValue) {
                value = ''; // TODO make combobox return null instead of empty string (standard select behavior)
            } else {
                if (O.highlightInvalid || (O.invalidAsValue ? (O.highlightInvalid) : O.highlightInvalid === null)) {
                    $display.addClass(pname + cinvalid).siblings(cp + cddback)
                        .addClass(pname + cddback + cinvalid);
                }
            }
            $valueInput.val(value);
            $display.val(value);
            return;
        }
        $t.find(cp + clist + ' p').eq($selected[0].index).addClass(pname + chovered).siblings().removeClass(pname + chovered);

        $valueInput.val(value).data('changed', true);
        $select.val(value).change();
    }

    /**
     * Add all the combobox logic.
     * @returns {undefined}
     */

    var blurTimer;

    function addListeners() {
        if (this.data('listenersAdded')) { // prevent duplicating listeners
            return;
        }
        var $T = this,
            O = $T.data(pname);

        var typingTimer = null;
        this.on('keyup', cp + cdisplay + ', ' + cp + cdiv, function(e) { // filter
            // Ignore keys that can't alter input field value on their own
            if ([38, //Up arrow
                    40, //Down arrow
                    13, //Enter
                    27, //Escape
                    9, //Tab
                    37, //Left arrow
                    39, //Right arrow
                    17, //Ctrl
                    18, //Alt
                    16, //Shift
                    20, //Caps lock
                    33, //Page up
                    34, //Page down
                    35, //End
                    36 //Home
                ].indexOf(e.which) >= 0) {
                return;
            }

            var doneTyping = function(e) {
                // Some extra cases
                if (!e.ctrlKey && !e.shiftKey && e.which == 45) return; //Insert without modifier
                if (e.ctrlKey && e.which == 65) return; //Ctrl+A; imperfect because sometimes we release the A *after* the Ctrl

                var fullMatch = O.fullMatch,
                    highlight = O.highlight;
                if (fullMatch) {
                    highlight = highlight !== false;
                } else {
                    highlight = !!highlight;
                }
                var $t = $(this),
                    search = this.value.trim();
                if (O.filterIgnoreCase) {
                    search = search.toLowerCase();
                }
                if (O.filterIgnoreAccents && String.prototype.latinize) {
                    search = search.latinize();
                }
                var $div = $t.closest(cp).children(cp + clist);
                slide.call($div, 'down', true);
                var $options = $t.closest(cp).find('select option');
                $(cp + ' ' + cp + clist).each(function() {
                    if ($div[0] != this) {
                        slide.call($(this), 'up');
                    }
                });
                if (!search) {
                    $div.children('p').show().each(function() {
                        $(cp + '-marker', this).contents().unwrap(); // remove selection
                    });
                    return;
                }
                var hideSelector = O.hideSeparatorsOnSearch ? 'p' : 'p:not(' + cp + csep + ', ' + cp + cpheader + ')';
                $div.children(hideSelector).hide();
                $options.each(function() {
                    var text = $(this).text().trim();
                    if (O.filterIgnoreCase) {
                        text = text.toLowerCase();
                    }
                    if (O.filterIgnoreAccents && String.prototype.latinize) {
                        text = text.latinize();
                    }
                    if (fullMatch ? text.indexOf(search) >= 0 : text.indexOf(search) == 0) {
                        // check index and show corresponding paragraph
                        var regexFlags = O.filterIgnoreCase ? 'i' : '';
                        var re = new RegExp("(" + search.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1") + ")", fullMatch ? regexFlags + 'g' : regexFlags);
                        var $ps = $div.children('p:eq(' + $options.index(this) + '):not(' + cp + csep + ', ' + cp + cpheader + ')').show();
                        if (highlight) {
                            $ps.each(function() {
                                $(cp + '-marker', this).contents().unwrap(); // remove previous selection
                                var mainSpan = $(cp + cmainspan, this)[0];
                                mainSpan.innerHTML = mainSpan.innerHTML.replace(re, '<span class="' + pname + '-marker">$1</span>');
                            });
                        }
                    }
                });
            };

            var t = this;
            var delay = O.filterDelay;
            if (!delay) {
                doneTyping.call(t, e);
            } else {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    doneTyping.call(t, e);
                }, delay);
            }
        });
        this.on('keydown', cp + cdisplay, function(e) {
            if ([38, 40, 13, 27, 9].indexOf(e.which) >= 0) {
                if (e.which != 9) {
                    e.preventDefault();
                }
                var $combobox = $(this).closest(cp);
                var $div = $combobox.children(cp + clist);
                var $hovered = $(cp + chovered, $div[0]),
                    $curr, offset;
                var $first = $('p:first', $div[0]);
                var cycle = O.loopScrolling;
                var notHeaderSelector = ':not(' + cp + csep + '):not(' + cp + cpheader + ')'; // don't put both classes in single parenthesis for old jQuery versions
            } else {
                return;
            }
            var fillOnArrow = O.mode == 'default' ? O.fillOnArrowPress : false; // always false for checkboxes mode
            if ($div.is(':animated')) {
                return; // keydown event is only for arrows, enter and escape
            }
            var v = this.value.trim();
            v = (O.filterIgnoreCase) ? v.toLowerCase() : v;
            var scrollTop = $div.scrollTop();
            if (e.which == 40) { // arrdown
                if ($div.is(':hidden')) {
                    slide.call($div, 'down');
                    return;
                }
                if ($hovered.length == 0) {
                    if ($first.is(':visible' + notHeaderSelector)) {
                        $curr = $first.addClass(pname + chovered);
                    } else {
                        $curr = $first.nextAll(':visible' + notHeaderSelector).first().addClass(pname + chovered);
                    }
                } else {
                    if (!cycle) {
                        if (!$hovered.nextAll(':visible' + notHeaderSelector).first().length) {
                            return;
                        }
                    }
                    $curr = $hovered.removeClass(pname + chovered).nextAll(':visible' + notHeaderSelector).first().addClass(pname + chovered);
                    if ($curr.length == 0) {
                        if ($first.is(':visible')) {
                            $curr = $first.addClass(pname + chovered);
                        } else {
                            $curr = $first.nextAll(':visible' + notHeaderSelector).first().addClass(pname + chovered);
                        }
                    }
                    if ($curr.length == 0) {
                        $curr = $first;
                    }
                    offset = $curr.position().top - $div.position().top;
                    var currHeight = $curr.outerHeight();
                    if (offset + currHeight * 6 > $div.height()) { // keep 4 elements ahead
                        if ((offset + currHeight * 6) - $div.height() > currHeight * 1.5) { // $curr is under the visible bottom border
                            $div.scrollTop(scrollTop + offset);
                        } else { // no fix required
                            $div.scrollTop(scrollTop + currHeight); // incremental scrolltop
                        }
                    } else if (offset < 0) {
                        $div.scrollTop(scrollTop - -offset);
                    }
                }
                if (fillOnArrow) {
                    this.value = $curr.find(cp + cmainspan).text();
                    $combobox.children(cp + cdisplay).data('fillonarrow', true);
                }
            } else if (e.which == 38) { // arrup
                if ($div.is(':visible')) {
                    if (!cycle && !$hovered.prevAll(':visible' + notHeaderSelector).first().length) {
                        return;
                    }
                    $curr = $hovered.removeClass(pname + chovered).prevAll(':visible' + notHeaderSelector).first().addClass(pname + chovered);
                    if ($curr.length == 0) {
                        $curr = $('p:visible' + notHeaderSelector + ':last', $div[0]).addClass(pname + chovered);
                    }
                    offset = $curr.position().top - $div.position().top;
                    currHeight = $curr.outerHeight();
                    if (offset < currHeight * 3) {
                        $div.scrollTop(scrollTop - -offset - currHeight * 3);
                    } else if (offset > $div.height() - currHeight * 3) {
                        $div.scrollTop(scrollTop + offset - currHeight * 3); // to the last (was $div[0].scrollHeight)
                    }
                    if (fillOnArrow) {
                        this.value = $curr.find(cp + cmainspan).text();
                        $combobox.children(cp + cdisplay).data('fillonarrow', true);
                    }
                }
            } else if (e.which == 13) { // enter
                if (O.fillOnBlur) {
                    getFirstP($div).click();
                    return;
                }
                $div.children(cp + chovered).click();
                if (O.mode == 'default') {
                    slide.call($div, 'up');
                }
            } else if (e.which == 27) { // escape
                var $t = O.blurOnEscape ? $(this).blur() : $(this);
                // If list is down, escape slides it up and doesn't propagate outward
                if ($div.is(':visible')) {
                    slide.call($div, 'up');
                    e.stopPropagation();
                }
            } else if (e.which == 9) { // tab
                if (O.fillOnTab) {
                    if (v) {
                        // Used to pick the first visible item in the dropdown
                        // Now pick the selected item (if any)
                        var $p = $div.children(cp + chovered);
                        if ($p.length) {
                            // e.preventDefault(); why not to go further?
                            $p.click();
                        }
                    }
                }
            }
        });
        this.on('change', 'select', function(e, checkboxesMode) { // someone triggered combobox select change
            var $combo = $(this).closest(cp);
            var dtext = $('option:selected', this).text();
            $combo.children(cp + cdisplay).val(dtext).data('value', dtext);
            var $valueInput = $combo.children(cp + cvalue);
            if ($valueInput.data('changed')) {
                $valueInput.data('changed', false);
                return;
            }
            if (checkboxesMode) { // no slideup for checkboxes mode
                updateValueInput.call($combo);
                $valueInput.change();
                return;
            }
            $valueInput.change();
            slide.call($combo.children(cp + clist), 'up'); // can be triggered at the page load
        });
        this.on(pname + '-chupdate', cp + clist + ' p :checkbox', function(e, forRefresh) {
            if (forRefresh) {
                e.stopPropagation();
                checkboxesModePClick.call($(this).parent(), e, true);
            }
        });
        this.on('click', cp + clist + ' p', function(e) { // value selected by clicking
            clearTimeout(blurTimer);
            e.stopPropagation();
            if ($(this).is(cp + csep + ', ' + cp + cpheader)) {
                return;
            }
            $T.children(cp + cinvalid).removeClass(pname + cinvalid); // 100% it is not invalid now
            $T.children(cp + cddback).removeClass(pname + cddback + cinvalid);
            var $t = $(this),
                $div = $t.parent(),
                $ps = $div.children();
            var index = $ps.index(this);
            if ($T.data(pname).mode == 'checkboxes') {
                checkboxesModePClick.call(this, e); // process checking
                return;
            }
            var $select = $div.closest(cp).children('select');
            $select.children('option').eq(index).prop('selected', true);
            $select.siblings(cp + cvalue).val($select.val());
            $select.change();
            slide.call($t.parent(), 'up');
            $t.addClass(pname + chovered).siblings().removeClass(pname + chovered);
        });
        this.on('blur', cp + cdisplay, function(e) {
            // Need to do some stuff only when user moves off the scombobox.

            // Try to do nothing in this handler if losing focus to another part of this
            // combobox (e.g. the down/up button, or the list itself).
            // IE needs this technique in addition to the timer one (see below) because
            // clicking on the dropped-down div's scroller (if present) gives a blur
            // but no suitable subsequent event with which to cancel the timer.
            var $t = $(this);
            var rt = $(e.relatedTarget).closest(cp);
            if (rt.length > 0 && rt[0] === $t.closest(cp)[0]) {
                return;
            }

            // The relatedTarget technique doesn't work on Chrome or on Firefox.
            // So we start a 200ms timer when display element loses focus. In click
            // handlers of control's other elements clearTimeout cancels the timer.
            // If the timer isn't cancelled it will fire and do the necessary slide up.
            // We can't defer all the blur processing with this timer as doing so would
            // mean that a click event on a submit button could get an outdated value
            // from the scombobox, because the click would precede the timer event.
            //
            // Note that the timer's function's bind() method is used to supply it with the correct 'this'
            blurTimer = setTimeout(
                function() {
                    var $t = $(this),
                        O = $T.data(pname);
                    if (this === document.activeElement) {
                        // Suppress autoexpand on next focus if this blur was actually the entire window losing focus
                        // rather than this element losing focus to another element on the same window
                        $t.data('silentfocus', true);
                    }
                    $t.data('fillonarrow', false); // Prevent the slide-up from resetting value
                    slide.call($t.closest(cp).children(cp + clist), 'up'); // Make sure the list closes when we're sure we've left the control
                }.bind(this), 200
            );

            //Is this necessary here? Seems to cause issues when on Chrome (cannot select list items correctly.
            //The usefulness of fillOnBlur is debated, see https://github.com/ivkremer/jquery-simple-combobox/issues/25
            //Either remove it entirely, or move it into the setTimeout function above.
            /*
            if (O.fillOnBlur && !O.invalidAsValue) {
                getFirstP($t.parent().children(cp + clist)).click();
                return;
            }
            */

            var vOriginal = $t.val().trim();
            var $valueInput = $t.siblings(cp + cvalue);
            var previousV = $valueInput.val();
            if (!vOriginal) { // if combo was emptied then set its value to '':
                $valueInput.val('');
            } else {
                var value;
                $t.siblings('select').find('option').each(function() {
                    if (O.filterIgnoreCase) {
                        if (vOriginal.toLowerCase() == $(this).text().trim().toLowerCase()) {
                            value = this.value;
                        }
                    } else {
                        if (vOriginal == $(this).text().trim()) {
                            value = this.value;
                        }
                    }
                });
                if (!value) { // value not found (invalid)
                    $valueInput.val(O.invalidAsValue ? vOriginal : '');
                } else {
                    $valueInput.val(value);
                }
            }
            if (previousV !== $valueInput.val()) {
                $valueInput.change().data('changed', true);
            }
        });
        this.on('focus', cp + cdisplay, function() {

            // Check for indicator that focus shouldn't cause expansion
            if ($(this).data('silentfocus')) {
                $(this).data('silentfocus', false);
                return;
            }
            if (!this.value.trim()) { // focusing in empty field
                // should trigger full dropdown:
                if (($T.data(pname).expandOnFocus) || ($(this).data('expandfocus'))) {
                    $(this).keyup();
                }
            } else { // input.display is not empty
                if (($T.data(pname).expandOnFocusWithValue) || ($(this).data('expandfocus'))) {
                    if ($T[pname]('val')) { // if value is valid
                        var $listDiv = $T.children(cp + clist);
                        $listDiv.children().show();
                        slide.call($listDiv, 'down');
                    } else {
                        $(this).keyup(); // else start filtering
                    }
                }
            }
            $(this).data('expandfocus', false);
        });
        this.on('click', cp + cdisplay + '-div', function() {
            if ($T.data(pname).disabled) {
                return;
            }
            slide.call($(this).siblings(cp + clist), 'down');
        });
        this.on('click', cp + cdisplay, function(e) {
            var t = $(this).closest(cp)[0];
            $(cp).each(function() { // close all other comboboxes
                if (this != t) {
                    $(this)[pname]('close');
                }
            });
            e.stopPropagation();
        });
        this.on('click', cp + cddarr, function(e) {
            clearTimeout(blurTimer);
            var $t = $(this),
                $combo = $t.closest(cp);
            var $div = $combo.children(cp + clist);
            if ($div.is(':visible')) {
                slide.call($div, 'up');
                $combo.children(cp + cdisplay).data('silentfocus', true).focus();
            } else {
                $combo.children(cp + cdisplay).data('expandfocus', true).focus();
            }
        });
        this.on('click', cp + cdiremove, function(e) {
            clearTimeout(blurTimer);
            e.stopPropagation();
            var $t = $(this);
            var $item = $t.parent(),
                $div = $T.children(cp + clist);
            $div.children('p').eq($t.data('index')).find(':checkbox').prop('checked', false);
            $item.fadeOut(O.animation.duration);
            $t.closest(cp).children('select').trigger('change', [true]);
        });
        // scroll listener is for ajax loading
        if (O.autoLoad != $.noop) {
            $(cp + clist, this).scroll(function() {
                var $t = $(this),
                    $select = $T.children('select');
                var currentScrollTop = $t.scrollTop();
                var overhead = 50;
                if (currentScrollTop > $t.data('scrollTop')) { // scrolling down
                    if (this.scrollHeight - currentScrollTop - overhead < $t.height()) {
                        if (!$T.data('pending')) {
                            $T.data('pending', true);
                            O.autoLoad.call($T, $select.find('option[value]:last').val(), 'bottom');
                        }
                    }
                } else { // scrolling up
                    if (currentScrollTop < $t.height() / 2) {
                        if (!$T.data('pending')) {
                            $T.data('pending', true);
                            O.autoLoad.call($T, $select.find('option[value]:first').val(), 'top');
                        }
                    }
                }
                $t.data('scrollTop', currentScrollTop);
            }).data('scrollTop', 0);
        }


        $(document).bind('click.' + pname, { thisIs: this }, function(e) {
            slide.call($(e.data.thisIs).children(cp + clist), 'up');
        });

        this.data('listenersAdded', true);
    }

    /**
     * Converts given data to final form in the most convenient way.
     * @param {Array} data data given as options.data param
     * @returns {Array|Boolean} array of data objects or false if no data were given
     */
    function normalizeData(data) {
        if (typeof data == 'string') { // json given
            data = $.parseJSON(data);
            if (data == null) { // null == empty array
                return [];
            }
        }
        if (!data) { // all falsy except empty string
            return false;
        }
        if (!(data instanceof Array)) { // object (probably) was given, convert it to array
            if (typeof data != 'object') {
                return false;
            }
            if (typeof data.length == 'undefined') {
                data.length = Object.keys(data).length;
            }
            data = [].slice.call(data);
        }
        return data; // array was given
    }

    function purifyData(data) {
        for (var i = 0; i < data.length; i++) {
            if ((!data[i].value || !data[i].text) && !(data[i].hasOwnProperty('separator'))) {
                data.splice(i, 1);
            }
        }
    }

    function purifyOptions($options) {
        for (var i = 0; i < $options.length; i++) {
            if (!$options[i].value && !$($options[i]).hasClass(pname + csep) && $options[i].tagName.toLowerCase() != 'optgroup') { // if no value,
                // but if it is a separator, then it is no matter if there is a not empty value
                // if this is an optgroup tag, then it will be used as a separator
                $($options[i]).remove();
            }
        }
    }

    function sortF(a, b) {
        var aT = a.text.trim().toLowerCase(),
            bT = b.text.trim().toLowerCase();
        return aT > bT ? 1 : aT == bT ? 0 : -1;
    }

    function removeDups(a) {
        for (var i = 0; i < a.length; i++) {
            for (var j = i + 1; j < a.length; j++) {
                if (!a[i] || !a[j])
                    continue;
                if (a[i].value == a[j].value)
                    a.splice(i, 1);
            }
        }
    }

    function removeDupsjQ(a) {
        for (var i = 0; i < a.length; i++) {
            for (var j = i + 1; j < a.length; j++) {
                if (!a[i] || !a[j])
                    continue;
                if (a[i].value == a[j].value && a[i].tagName.toLowerCase() != 'optgroup') {
                    $(a[i]).remove();
                }
            }
        }
    }

    /**
     * `this` refers to combobox
     */
    function checkForInvalid() {
        var $display = this.children(cp + cdisplay),
            $select = this.children('select'),
            O = this.data(pname);
        var value, v = $display.val().trim();
        v = (O.filterIgnoreCase) ? v.toLowerCase() : v;
        // check if such value exists in options
        $select.find('option').each(function() {
            var candidate = $(this).text().trim();
            candidate = (O.filterIgnoreCase) ? candidate.toLowerCase() : candidate;
            if (candidate == v) {
                value = this.value;
            }
        });
        var invalid = (!value && v);
        if (invalid) {
            if (O.forbidInvalid) {
                $display.closest(cp).find(cp + cdisplay).val('').data('value', '');
            } else {
                // if highlightInvalid is enabled directly (default is null)
                // or invalidAsValue is on and highlightInvalid is not its default:
                // TODO refactor to make a more readable code:
                if (O.highlightInvalid || (O.invalidAsValue ? (O.highlightInvalid) : O.highlightInvalid === null)) {
                    $display.addClass(pname + cinvalid).siblings(cp + cddback)
                        .addClass(pname + cddback + cinvalid);
                }
            }
            if (!O.invalidAsValue) { // TODO check if this code affects anything
                $display.siblings('select, ' + cp + cvalue).val('');
            }
        } else {
            $display.removeClass(pname + cinvalid).siblings(cp + cddback).removeClass(pname + cddback + cinvalid);
        }
    }

    /**
     * Slides the div with a list. `this` refers to the list
     * @param dir 'up' = collapse, 'down' = expand.
     * @param backspace to fix backspace bug
     */ // TODO rename and comment backspace argument
    function slide(dir, backspace) {
        if (this.is(':animated') || !this.length) {
            return;
        }
        if (dir == 'up' && this.is(':hidden') && this.length == 1) {
            return; // todo put a comment: why? (one reason is probably optimization, but what is this.length == 1 for?)
        }
        var options = this.parent().data(pname).animation;
        if (!$.easing[options.easing]) {
            console.warn('no such easing: ' + options.easing);
            options.easing = 'swing';
        }
        var $combobox = this.parent(),
            O = $combobox.data(pname);
        if (dir == 'up') {
            O.beforeClose.call($combobox);
            options.complete = function() {
                if (O.mode != 'checkboxes') {
                    checkForInvalid.call($combobox);
                }
                O.afterClose.call($combobox);
            };
            this.slideUp(options).data('p-clicked-index', -1);
            $combobox.children(cp + cddarr).removeClass(pname + cddarr + '-up');
        } else {
            O.beforeOpen.call($combobox);
            options.complete = function() { O.afterOpen.call($combobox) };
            this.slideDown(options);
            $combobox.children(cp + cddarr).addClass(pname + cddarr + '-up');

            // Every edit keystroke will call a slide down; use this opportunity to reset the list's display characteristics fully.
            $combobox.find(cp + chovered).removeClass(pname + chovered); // remove previous selection
            $(cp + '-marker', $combobox).contents().unwrap(); // remove previous highlight            

            // Reveal everything whenever we slide down, so that user gets to see all the options.
            // If the slide down was triggered by entry of a character, filtering will immediately reduce the list
            // to matching items. If the slide down was by clicking the down-button, or entry of cursor-down,
            // all entries will remain displayed.
            $combobox.children(cp + clist).children('p').show();
        }
        var $display = $combobox.children(cp + cdisplay); // code for fillOnArrowPress feature
        $display.each(function() {
            var $t = $(this);
            if ($t.data('fillonarrow') && !backspace) { // fix backspace bug
                $t.data('fillonarrow', false).val($t.data('value'));
            }

            // Highlight first full match when dropping down
            if (dir == 'down') {
                var search = this.value.trim();
                if (O.filterIgnoreCase) {
                    search = search.toLowerCase();
                }
                var $selopts = $combobox.find('select option');
                $selopts.each(function() {
                    var text = $(this).text().trim();
                    if (O.filterIgnoreCase) {
                        text = text.toLowerCase();
                    }
                    if (text == search) {
                        $combobox.children(cp + clist).children('p:eq(' + $selopts.index(this) + '):not(' + cp + csep + ', ' + cp + cpheader + ')').first().addClass(pname + chovered);
                        return false;
                    }
                });
            }
        });
    }

    function checkboxesModePClick(e, forRefresh) { // this refers to paragraph dom element
        var $t = $(this),
            $combo = $t.closest(cp),
            $div = $t.parent(),
            $ps = $div.children('p'),
            index = $ps.index(this),
            duration = durations($div.parent().data(pname).animation.duration);
        if (!forRefresh) {
            var $chbox = $t.find(':checkbox');
            // don't toggle prop('checked') if checkbox itself was clicked.
            if (!$(e.target).is(':checkbox')) {
                $chbox.prop('checked', !$chbox.prop('checked')); // avoid clicking, change prop instead
            }
            var choice = $chbox.prop('checked');
            if (e.shiftKey) { // mark between last click and current
                if ($div.data('p-clicked-index') >= 0) { // not for the first time
                    var f = $div.data('p-clicked-index');
                    var from = f < index ? f : index,
                        to = f < index ? index : f;
                    for (var i = from; i <= to; i++) {
                        $($ps[i]).find(':checkbox').prop('checked', choice);
                    }
                }
            }
        }
        var $dispDivHolder = $combo.find(cp + cdholder).prepend('<span />');
        $combo.find(cp + cdholder).fadeOut(duration / 5, function() {
            $dispDivHolder.empty().show();
            // get all selected properties
            $ps.each(function(i) {
                var $t = $(this);
                if ($t.find(':checkbox').prop('checked')) {
                    $dispDivHolder.append(
                        $('<div />').addClass(pname + cditem)
                        .append($('<div />').addClass(pname + cditem + '-text').text($t.find(cp + cmainspan).text()))
                        .append($('<div />').addClass(pname + cdiremove).text('×').data('index', i)).fadeIn(duration * 1.5)
                        .attr('title', $t.attr('title'))
                    );
                }
            });
            $dispDivHolder.append('<div style="clear: both" />');
        });
        $div.data('p-clicked-index', index);
        $t.closest(cp).children('select').trigger('change', [true]); // true for do not slideup the items div
    }

    /**
     * @param items
     * @param prepend flag if prepend instead of appending
     */
    function renderItems(items, prepend) {
        var settings = this.data(pname);
        var $select = this.find('select'),
            $div = this.find(cp + clist);
        for (var i = 0; i < items.length; i++) {
            if (items[i].hasOwnProperty('separator')) { // if separator given then
                if (items[i].hasOwnProperty('header')) { // if header text also given then add only header
                    $p = $('<p class="' + pname + cpheader + '" />').text(items[i].header);
                } else { // else add separator itself
                    var $p = $('<p class="' + pname + csep + '" />');
                }
                var $option = $('<option />');
            } else { // regular item
                $option = $('<option />').val(items[i].value).text(items[i].text).prop('selected', !!items[i].selected);
                $p = settings.pFillFunc.call(this, items[i], settings);
                if (settings.mode == 'checkboxes') {
                    $p.prepend('<input type="checkbox" />');
                }
            }
            $p.data('value', items[i].value);
            if (prepend) {
                $select.prepend($option);
                $div.prepend($p);
            } else {
                $select.append($option);
                $div.append($p);
            }
        }
    }

    function getFirstP($clist) {
        var $closestP = $clist.children(cp + chovered + ':visible');
        if ($closestP.length == 0) {
            $closestP = $clist.children(':visible:first');
        }
        return $closestP;
    }

    function toCamelCase(o) {
        if (o == null) {
            return null;
        }
        var keys = Object.keys(o);
        for (var k = 0; k < keys.length; k++) {
            var key = keys[k].replace(/-([a-z])/g, function(g) {
                return g[1].toUpperCase() });
            if (keys[k] != key) { // hyphened property
                o[key] = o[keys[k]];
                delete o[keys[k]];
            }
            if (typeof o[key] == 'object' && key != 'data') {
                toCamelCase(o[key]);
            }
        }
        return o;
    }

    /**
     * The core.
     * @param {Object|String} actOrOpts action (string) or options (object)
     * @returns {Object|void} jQuery object on success. Throws error on undefined method.
     */
    $.fn[pname] = function(actOrOpts) {
        if (typeof actOrOpts == 'string') {
            if (!this.length) { // method called on empty collection
                $.error('Calling ' + pname + '.' + actOrOpts + '() method on empty collection');
            }
            if (this.data(pname + '-init') == null) { // it can be legally false, but not undefined
                $.error('Calling ' + pname + '.' + actOrOpts + '() method prior to initialization');
            }
            var method = methods[actOrOpts];
            if (!method) {
                $.error('No such method: ' + actOrOpts + ' in jQuery.' + pname + '()');
            }
        } else if (['object', 'undefined'].indexOf(typeof actOrOpts) >= 0) {
            var options = $.extend(true, {}, $.fn[pname].defaults, toCamelCase(actOrOpts));
        } else {
            $.error('Incorrect usage');
            return this;
        }
        if (method) {
            return method.apply(this, Array.prototype.slice.call(arguments, 1));
        }
        return this.each(function() {
            var $t = $(this);
            if ($t.parent().hasClass(pname)) {
                return; // already initialized
            }
            if ($t.is('select')) {
                $t.wrap('<div />');
                if (options.reassignId) {
                    $t.parent().attr('id', $t.attr('id'));
                }
                $t = $t.parent();
            }
            $t.data(pname, $.extend(true, {}, options)); // cloning object is required for cases like:
            // $('multiple targets selector').combobox(settings)
            // $('one of a bunch').combobox('options', propertiesToChange)
            // If the options object is not cloned above,
            // then changing properties will affect every target in the original set.
            methods.init.apply($t);
        });
    };

    $.fn[pname].defaults = {
        /**
         * If no data given combobox is filled relying on $('select option') list.
         * By default (see pMarkup and pFillFunc) the data is an array of objects:
         * {value: '', text: '', additional: '', selected: true/false, anyCustomOption: customValue}
         * You can also provide json or object with enumerated properties:
         * {0: {...}, 1: {...}, ...}
         */
        data: null,
        /**
         * Whether combobox is empty by default (true) or has an original select value (usually it the first value,
         * but can be changed by added a `selected` prop).
         */
        empty: false,
        /**
         * Whether set required attribute.
         */
        required: false,
        /**
         * Whether set combobox disabled.
         */
        disabled: false,
        /**
         * Whether to sort options alphabetically or not
         */
        sort: true,
        /**
         * false to sort descending
         */
        sortAsc: true,
        /**
         * Whether to remove duplicates (regarding to values only).
         * Not removing duplicated may cause an error, so be careful
         */
        removeDuplicates: true,
        /**
         * Whether to match in any part of the option text or only start from the beginning of the text
         */
        fullMatch: false,
        /**
         * By default highlighting is turned on when fullMatch is turned on.
         * Set it strictly to false to disable it anyway or to any truthy value to set it always enabled
         */
        highlight: null,
        /**
         * Whether to ignore case while filtering.
         */
        filterIgnoreCase: true,
        /**
         * Whether to convert a needle and a haystack like 'Cajicá' or 'Hősök' to 'Cajica' and 'Hosok'.
         */
        filterIgnoreAccents: false,
        /**
         * Whether to debounce search function, falsy value for no debounce.
         */
        filterDelay: 0,
        /**
         * Hide separators when typing something in a combo.
         */
        hideSeparatorsOnSearch: false,
        /**
         * When false options list does not drop down on focus (applies on an empty combobox).
         * In this case you have to click on arrow to expand the list or start typing.
         */
        expandOnFocus: true,
        /**
         * When false options list does not drop down on focus (applies on a filled combobox).
         */
        expandOnFocusWithValue: true,
        /**
         * Set tabindex
         */
        tabindex: null,
        /**
         * When true, invalid values are forbidden what means combobox search input empties on blur in case the value
         * was not chosen and search field contained wrong text.
         * When false, incorrect filled combobox search field will has invalid css class.
         */
        forbidInvalid: false,
        /**
         * When true, then value from visible input will be a value returned by `$(combo).scombobox('val');`
         */
        invalidAsValue: false,
        /**
         * Whether to mark a combobox with invalid value with red or not. By default it is turned on.
         * When `invalidAsValue` option is set to true, `highlightInvalid` is considered false by default.
         * If you want to enabled or disable it regardless to `invalidAsValue`, set it to a any truthy value or not null
         * falsy value correspondingly.
         */
        highlightInvalid: null,
        /**
         * If true id from select will be reassigned to the created combobox div when query target was select, like $('select').combobox()
         */
        reassignId: true,
        /**
         * Combobox mode 'default' means it is looking like select box with input for searching.
         * mode 'checkboxes' means every option has a checkbox. In checkboxes mode the value of
         * combobox is an array of values which were checked.
         */
        mode: 'default',
        /**
         * Don't forget to change pFillFunc if necessary when you change the markup.
         * <span class="mainspan"></span> is required to use marker highlighting while typing. Highlighting is only working for the text
         * in this span. That means filter does not apply to additional text. See data parameter.
         */
        pMarkup: '<span class="' + pname + cmainspan + '">${text}</span> <span>${additional}</span>',
        /**
         * Change replacements lines in this function if necessary after changing pMarkup.
         * this refers to combobox
         * @param item {Object} item from data array
         * @param options {Object} plugin instance properties
         */
        pFillFunc: function(item, options) {
            return $('<p />').html(options.pMarkup
                .replace('${text}', item.text)
                .replace('${additional}', item.additional ? item.additional : '')
            );
        },
        /**
         * Animation settings.
         */
        animation: {
            duration: 'fast', // animation speed
            easing: 'swing' // easing effect
        },
        /**
         * Dropdown div max width
         */
        listMaxWidth: window.screen.width / 2,
        /**
         * Use this to handle long text options lists.
         * If true then long text options will take multiple lines. If false, then horizontal slider appears in list.
         */
        wrap: true,
        /**
         * Items list div maximum height (css property)
         */
        maxHeight: '',
        /**
         * Put main text in input while walking though the options with arrow keys
         */
        fillOnArrowPress: true,
        /**
         * Select hovered or first matching option on blur
         */
        fillOnBlur: false,
        /**
         * Blurs the search field on escape keypress
         */
        blurOnEscape: false,
        /**
         * Whether to set the first visible item as a value on tab key press (works only if search input is not empty).
         * If set to false then the default action is working (going to the next input on page).
         */
        fillOnTab: true,
        /**
         * If set to true dropdown arrow appears in the right corner of combobox
         */
        showDropDown: true,
        /**
         * Callback executes after finishing initialization.
         */
        callback: {
            func: $.noop, // this refers to combobox's div holder
            args: [] // arguments
        },
        beforeOpen: $.noop,
        beforeClose: $.noop,
        afterOpen: $.noop,
        afterClose: $.noop,
        /**
         * This option is for ajax loading (appending/prepending items). This function usage is:
         * function(value, direction) {
         *     // value here is the edge value in the list (last for appending or first for prepending).
         *     // direction here is the scrolling direction, which can be either 'top' or 'bottom'
         *     // so you can do something like this:
         *     var $t = $(this);
         *     $.post('your url here' + (direction == 'top' ? '?prepend' : ''), {id: value}, function(res) {
         *         $t.scombobox('fill', res, direction == 'top' ? 2 : 1); // 1 for prepending, 2 for appending
         *         $t.data('pending', false); // this line is compulsory
         *     });
         * }
         */
        autoLoad: $.noop,
        /**
         * Enables infinite scrolling for up and down arrows keys.
         * When autoLoad function provided then loopScrolling is set to false.
         */
        loopScrolling: true,
        /**
         * Placeholder for search input.
         */
        placeholder: ''
    };

    /**
     * This function lets you override the default params without touching original plugin code.
     * Usage: $().scombobox.extendDefaults(yourDefaults);
     * @param options {Object} your custom defaults.
     */
    $.fn[pname].extendDefaults = function(options) {
        $.extend(true, $.fn[pname].defaults, options);
    };
})(jQuery, document);