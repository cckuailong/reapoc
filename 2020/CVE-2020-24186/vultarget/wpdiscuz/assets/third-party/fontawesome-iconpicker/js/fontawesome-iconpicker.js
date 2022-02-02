(function (a) {
    if (typeof define === "function" && define.amd) {
        define(["jquery"], a);
    } else {
        a(jQuery);
    }
})(function (a) {
    a.ui = a.ui || {};
    var b = a.ui.version = "1.12.1";
    (function () {
        var b, c = Math.max, d = Math.abs, e = /left|center|right/, f = /top|center|bottom/, g = /[\+\-]\d+(\.[\d]+)?%?/, h = /^\w+/, i = /%$/, j = a.fn.pos;
        function k(a, b, c) {
            return [parseFloat(a[0]) * (i.test(a[0]) ? b / 100 : 1), parseFloat(a[1]) * (i.test(a[1]) ? c / 100 : 1)];
        }
        function l(b, c) {
            return parseInt(a.css(b, c), 10) || 0;
        }
        function m(b) {
            var c = b[0];
            if (c.nodeType === 9) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: 0,
                        left: 0
                    }
                };
            }
            if (a.isWindow(c)) {
                return {
                    width: b.width(),
                    height: b.height(),
                    offset: {
                        top: b.scrollTop(),
                        left: b.scrollLeft()
                    }
                };
            }
            if (c.preventDefault) {
                return {
                    width: 0,
                    height: 0,
                    offset: {
                        top: c.pageY,
                        left: c.pageX
                    }
                };
            }
            return {
                width: b.outerWidth(),
                height: b.outerHeight(),
                offset: b.offset()
            };
        }
        a.pos = {
            scrollbarWidth: function () {
                if (b !== undefined) {
                    return b;
                }
                var c, d, e = a("<div " + "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" + "<div style='height:100px;width:auto;'></div></div>"), f = e.children()[0];
                a("body").append(e);
                c = f.offsetWidth;
                e.css("overflow", "scroll");
                d = f.offsetWidth;
                if (c === d) {
                    d = e[0].clientWidth;
                }
                e.remove();
                return b = c - d;
            },
            getScrollInfo: function (b) {
                var c = b.isWindow || b.isDocument ? "" : b.element.css("overflow-x"), d = b.isWindow || b.isDocument ? "" : b.element.css("overflow-y"), e = c === "scroll" || c === "auto" && b.width < b.element[0].scrollWidth, f = d === "scroll" || d === "auto" && b.height < b.element[0].scrollHeight;
                return {
                    width: f ? a.pos.scrollbarWidth() : 0,
                    height: e ? a.pos.scrollbarWidth() : 0
                };
            },
            getWithinInfo: function (b) {
                var c = a(b || window), d = a.isWindow(c[0]), e = !!c[0] && c[0].nodeType === 9, f = !d && !e;
                return {
                    element: c,
                    isWindow: d,
                    isDocument: e,
                    offset: f ? a(b).offset() : {
                        left: 0,
                        top: 0
                    },
                    scrollLeft: c.scrollLeft(),
                    scrollTop: c.scrollTop(),
                    width: c.outerWidth(),
                    height: c.outerHeight()
                };
            }
        };
        a.fn.pos = function (b) {
            if (!b || !b.of) {
                return j.apply(this, arguments);
            }
            b = a.extend({}, b);
            var i, n, o, p, q, r, s = a(b.of), t = a.pos.getWithinInfo(b.within), u = a.pos.getScrollInfo(t), v = (b.collision || "flip").split(" "), w = {};
            r = m(s);
            if (s[0].preventDefault) {
                b.at = "left top";
            }
            n = r.width;
            o = r.height;
            p = r.offset;
            q = a.extend({}, p);
            a.each(["my", "at"], function () {
                var a = (b[this] || "").split(" "), c, d;
                if (a.length === 1) {
                    a = e.test(a[0]) ? a.concat(["center"]) : f.test(a[0]) ? ["center"].concat(a) : ["center", "center"];
                }
                a[0] = e.test(a[0]) ? a[0] : "center";
                a[1] = f.test(a[1]) ? a[1] : "center";
                c = g.exec(a[0]);
                d = g.exec(a[1]);
                w[this] = [c ? c[0] : 0, d ? d[0] : 0];
                b[this] = [h.exec(a[0])[0], h.exec(a[1])[0]];
            });
            if (v.length === 1) {
                v[1] = v[0];
            }
            if (b.at[0] === "right") {
                q.left += n;
            } else if (b.at[0] === "center") {
                q.left += n / 2;
            }
            if (b.at[1] === "bottom") {
                q.top += o;
            } else if (b.at[1] === "center") {
                q.top += o / 2;
            }
            i = k(w.at, n, o);
            q.left += i[0];
            q.top += i[1];
            return this.each(function () {
                var e, f, g = a(this), h = g.outerWidth(), j = g.outerHeight(), m = l(this, "marginLeft"), r = l(this, "marginTop"), x = h + m + l(this, "marginRight") + u.width, y = j + r + l(this, "marginBottom") + u.height, z = a.extend({}, q), A = k(w.my, g.outerWidth(), g.outerHeight());
                if (b.my[0] === "right") {
                    z.left -= h;
                } else if (b.my[0] === "center") {
                    z.left -= h / 2;
                }
                if (b.my[1] === "bottom") {
                    z.top -= j;
                } else if (b.my[1] === "center") {
                    z.top -= j / 2;
                }
                z.left += A[0];
                z.top += A[1];
                e = {
                    marginLeft: m,
                    marginTop: r
                };
                a.each(["left", "top"], function (c, d) {
                    if (a.ui.pos[v[c]]) {
                        a.ui.pos[v[c]][d](z, {
                            targetWidth: n,
                            targetHeight: o,
                            elemWidth: h,
                            elemHeight: j,
                            collisionPosition: e,
                            collisionWidth: x,
                            collisionHeight: y,
                            offset: [i[0] + A[0], i[1] + A[1]],
                            my: b.my,
                            at: b.at,
                            within: t,
                            elem: g
                        });
                    }
                });
                if (b.using) {
                    f = function (a) {
                        var e = p.left - z.left, f = e + n - h, i = p.top - z.top, k = i + o - j, l = {
                            target: {
                                element: s,
                                left: p.left,
                                top: p.top,
                                width: n,
                                height: o
                            },
                            element: {
                                element: g,
                                left: z.left,
                                top: z.top,
                                width: h,
                                height: j
                            },
                            horizontal: f < 0 ? "left" : e > 0 ? "right" : "center",
                            vertical: k < 0 ? "top" : i > 0 ? "bottom" : "middle"
                        };
                        if (n < h && d(e + f) < n) {
                            l.horizontal = "center";
                        }
                        if (o < j && d(i + k) < o) {
                            l.vertical = "middle";
                        }
                        if (c(d(e), d(f)) > c(d(i), d(k))) {
                            l.important = "horizontal";
                        } else {
                            l.important = "vertical";
                        }
                        b.using.call(this, a, l);
                    };
                }
                g.offset(a.extend(z, {
                    using: f
                }));
            });
        };
        a.ui.pos = {
            _trigger: function (a, b, c, d) {
                if (b.elem) {
                    b.elem.trigger({
                        type: c,
                        position: a,
                        positionData: b,
                        triggered: d
                    });
                }
            },
            fit: {
                left: function (b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitLeft");
                    var e = d.within, f = e.isWindow ? e.scrollLeft : e.offset.left, g = e.width, h = b.left - d.collisionPosition.marginLeft, i = f - h, j = h + d.collisionWidth - g - f, k;
                    if (d.collisionWidth > g) {
                        if (i > 0 && j <= 0) {
                            k = b.left + i + d.collisionWidth - g - f;
                            b.left += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.left = f;
                        } else {
                            if (i > j) {
                                b.left = f + g - d.collisionWidth;
                            } else {
                                b.left = f;
                            }
                        }
                    } else if (i > 0) {
                        b.left += i;
                    } else if (j > 0) {
                        b.left -= j;
                    } else {
                        b.left = c(b.left - h, b.left);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitLeft");
                },
                top: function (b, d) {
                    a.ui.pos._trigger(b, d, "posCollide", "fitTop");
                    var e = d.within, f = e.isWindow ? e.scrollTop : e.offset.top, g = d.within.height, h = b.top - d.collisionPosition.marginTop, i = f - h, j = h + d.collisionHeight - g - f, k;
                    if (d.collisionHeight > g) {
                        if (i > 0 && j <= 0) {
                            k = b.top + i + d.collisionHeight - g - f;
                            b.top += i - k;
                        } else if (j > 0 && i <= 0) {
                            b.top = f;
                        } else {
                            if (i > j) {
                                b.top = f + g - d.collisionHeight;
                            } else {
                                b.top = f;
                            }
                        }
                    } else if (i > 0) {
                        b.top += i;
                    } else if (j > 0) {
                        b.top -= j;
                    } else {
                        b.top = c(b.top - h, b.top);
                    }
                    a.ui.pos._trigger(b, d, "posCollided", "fitTop");
                }
            },
            flip: {
                left: function (b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipLeft");
                    var e = c.within, f = e.offset.left + e.scrollLeft, g = e.width, h = e.isWindow ? e.scrollLeft : e.offset.left, i = b.left - c.collisionPosition.marginLeft, j = i - h, k = i + c.collisionWidth - g - h, l = c.my[0] === "left" ? -c.elemWidth : c.my[0] === "right" ? c.elemWidth : 0, m = c.at[0] === "left" ? c.targetWidth : c.at[0] === "right" ? -c.targetWidth : 0, n = -2 * c.offset[0], o, p;
                    if (j < 0) {
                        o = b.left + l + m + n + c.collisionWidth - g - f;
                        if (o < 0 || o < d(j)) {
                            b.left += l + m + n;
                        }
                    } else if (k > 0) {
                        p = b.left - c.collisionPosition.marginLeft + l + m + n - h;
                        if (p > 0 || d(p) < k) {
                            b.left += l + m + n;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipLeft");
                },
                top: function (b, c) {
                    a.ui.pos._trigger(b, c, "posCollide", "flipTop");
                    var e = c.within, f = e.offset.top + e.scrollTop, g = e.height, h = e.isWindow ? e.scrollTop : e.offset.top, i = b.top - c.collisionPosition.marginTop, j = i - h, k = i + c.collisionHeight - g - h, l = c.my[1] === "top", m = l ? -c.elemHeight : c.my[1] === "bottom" ? c.elemHeight : 0, n = c.at[1] === "top" ? c.targetHeight : c.at[1] === "bottom" ? -c.targetHeight : 0, o = -2 * c.offset[1], p, q;
                    if (j < 0) {
                        q = b.top + m + n + o + c.collisionHeight - g - f;
                        if (q < 0 || q < d(j)) {
                            b.top += m + n + o;
                        }
                    } else if (k > 0) {
                        p = b.top - c.collisionPosition.marginTop + m + n + o - h;
                        if (p > 0 || d(p) < k) {
                            b.top += m + n + o;
                        }
                    }
                    a.ui.pos._trigger(b, c, "posCollided", "flipTop");
                }
            },
            flipfit: {
                left: function () {
                    a.ui.pos.flip.left.apply(this, arguments);
                    a.ui.pos.fit.left.apply(this, arguments);
                },
                top: function () {
                    a.ui.pos.flip.top.apply(this, arguments);
                    a.ui.pos.fit.top.apply(this, arguments);
                }
            }
        };
        (function () {
            var b, c, d, e, f, g = document.getElementsByTagName("body")[0], h = document.createElement("div");
            b = document.createElement(g ? "div" : "body");
            d = {
                visibility: "hidden",
                width: 0,
                height: 0,
                border: 0,
                margin: 0,
                background: "none"
            };
            if (g) {
                a.extend(d, {
                    position: "absolute",
                    left: "-1000px",
                    top: "-1000px"
                });
            }
            for (f in d) {
                b.style[f] = d[f];
            }
            b.appendChild(h);
            c = g || document.documentElement;
            c.insertBefore(b, c.firstChild);
            h.style.cssText = "position: absolute; left: 10.7432222px;";
            e = a(h).offset().left;
            a.support.offsetFractions = e > 10 && e < 11;
            b.innerHTML = "";
            c.removeChild(b);
        })();
    })();
    var c = a.ui.position;
});

(function (a) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery"], a);
    } else if (window.jQuery && !window.jQuery.fn.iconpicker) {
        a(window.jQuery);
    }
})(function (a) {
    "use strict";
    var b = {
        isEmpty: function (a) {
            return a === false || a === "" || a === null || a === undefined;
        },
        isEmptyObject: function (a) {
            return this.isEmpty(a) === true || a.length === 0;
        },
        isElement: function (b) {
            return a(b).length > 0;
        },
        isString: function (a) {
            return typeof a === "string" || a instanceof String;
        },
        isArray: function (b) {
            return a.isArray(b);
        },
        inArray: function (b, c) {
            return a.inArray(b, c) !== -1;
        },
        throwError: function (a) {
            throw "Font Awesome Icon Picker Exception: " + a;
        }
    };
    var c = function (d, e) {
        this._id = c._idCounter++;
        this.element = a(d).addClass("iconpicker-element");
        this._trigger("iconpickerCreate");
        this.options = a.extend({}, c.defaultOptions, this.element.data(), e);
        this.options.templates = a.extend({}, c.defaultOptions.templates, this.options.templates);
        this.options.originalPlacement = this.options.placement;
        this.container = b.isElement(this.options.container) ? a(this.options.container) : false;
        if (this.container === false) {
            if (this.element.is(".dropdown-toggle")) {
                this.container = a("~ .dropdown-menu:first", this.element);
            } else {
                this.container = this.element.is("input,textarea,button,.btn") ? this.element.parent() : this.element;
            }
        }
        this.container.addClass("iconpicker-container");
        if (this.isDropdownMenu()) {
            this.options.templates.search = false;
            this.options.templates.buttons = false;
            this.options.placement = "inline";
        }
        this.input = this.element.is("input,textarea") ? this.element.addClass("iconpicker-input") : false;
        if (this.input === false) {
            this.input = this.container.find(this.options.input);
            if (!this.input.is("input,textarea")) {
                this.input = false;
            }
        }
        this.component = this.isDropdownMenu() ? this.container.parent().find(this.options.component) : this.container.find(this.options.component);
        if (this.component.length === 0) {
            this.component = false;
        } else {
            this.component.find("i").addClass("iconpicker-component");
        }
        this._createPopover();
        this._createIconpicker();
        if (this.getAcceptButton().length === 0) {
            this.options.mustAccept = false;
        }
        if (this.isInputGroup()) {
            this.container.parent().append(this.popover);
        } else {
            this.container.append(this.popover);
        }
        this._bindElementEvents();
        this._bindWindowEvents();
        this.update(this.options.selected);
        if (this.isInline()) {
            this.show();
        }
        this._trigger("iconpickerCreated");
    };
    c._idCounter = 0;
    c.defaultOptions = {
        title: false,
        selected: false,
        defaultValue: false,
        placement: "bottom",
        collision: "none",
        animation: true,
        hideOnSelect: false,
        showFooter: false,
        searchInFooter: false,
        mustAccept: false,
        selectedCustomClass: "bg-primary",
        icons: [],
        fullClassFormatter: function (a) {
            return "fa " + a;
        },
        input: "input,.iconpicker-input",
        inputSearch: false,
        container: false,
        component: ".input-group-addon,.iconpicker-component",
        templates: {
            popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
            footer: '<div class="popover-footer"></div>',
            buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
            search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
            iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
            iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>'
        }
    };
    c.batch = function (b, c) {
        var d = Array.prototype.slice.call(arguments, 2);
        return a(b).each(function () {
            var b = a(this).data("iconpicker");
            if (!!b) {
                b[c].apply(b, d);
            }
        });
    };
    c.prototype = {
        constructor: c,
        options: {},
        _id: 0,
        _trigger: function (b, c) {
            c = c || {};
            this.element.trigger(a.extend({
                type: b,
                iconpickerInstance: this
            }, c));
        },
        _createPopover: function () {
            this.popover = a(this.options.templates.popover);
            var c = this.popover.find(".popover-title");
            if (!!this.options.title) {
                c.append(a('<div class="popover-title-text">' + this.options.title + "</div>"));
            }
            if (this.hasSeparatedSearchInput() && !this.options.searchInFooter) {
                c.append(this.options.templates.search);
            } else if (!this.options.title) {
                c.remove();
            }
            if (this.options.showFooter && !b.isEmpty(this.options.templates.footer)) {
                var d = a(this.options.templates.footer);
                if (this.hasSeparatedSearchInput() && this.options.searchInFooter) {
                    d.append(a(this.options.templates.search));
                }
                if (!b.isEmpty(this.options.templates.buttons)) {
                    d.append(a(this.options.templates.buttons));
                }
                this.popover.append(d);
            }
            if (this.options.animation === true) {
                this.popover.addClass("fade");
            }
            return this.popover;
        },
        _createIconpicker: function () {
            var b = this;
            this.iconpicker = a(this.options.templates.iconpicker);
            var c = function (c) {
                var d = a(this);
                if (d.is("i")) {
                    d = d.parent();
                }
                b._trigger("iconpickerSelect", {
                    iconpickerItem: d,
                    iconpickerValue: b.iconpickerValue
                });
                if (b.options.mustAccept === false) {
                    b.update(d.data("iconpickerValue"));
                    b._trigger("iconpickerSelected", {
                        iconpickerItem: this,
                        iconpickerValue: b.iconpickerValue
                    });
                } else {
                    b.update(d.data("iconpickerValue"), true);
                }
                if (b.options.hideOnSelect && b.options.mustAccept === false) {
                    b.hide();
                }
                c.preventDefault();
                return false;
            };
            for (var d in this.options.icons) {
                if (typeof this.options.icons[d] === "string") {
                    var e = a(this.options.templates.iconpickerItem);
                    e.find("i").addClass(this.options.fullClassFormatter(this.options.icons[d]));
                    e.data("iconpickerValue", this.options.icons[d]).on("click.iconpicker", c);
                    this.iconpicker.find(".iconpicker-items").append(e.attr("title", "." + this.options.icons[d]));
                }
            }
            this.popover.find(".popover-content").append(this.iconpicker);
            return this.iconpicker;
        },
        _isEventInsideIconpicker: function (b) {
            var c = a(b.target);
            if ((!c.hasClass("iconpicker-element") || c.hasClass("iconpicker-element") && !c.is(this.element)) && c.parents(".iconpicker-popover").length === 0) {
                return false;
            }
            return true;
        },
        _bindElementEvents: function () {
            var c = this;
            this.getSearchInput().on("keyup.iconpicker", function () {
                c.filter(a(this).val().toLowerCase());
            });
            this.getAcceptButton().on("click.iconpicker", function () {
                var a = c.iconpicker.find(".iconpicker-selected").get(0);
                c.update(c.iconpickerValue);
                c._trigger("iconpickerSelected", {
                    iconpickerItem: a,
                    iconpickerValue: c.iconpickerValue
                });
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.getCancelButton().on("click.iconpicker", function () {
                if (!c.isInline()) {
                    c.hide();
                }
            });
            this.element.on("focus.iconpicker", function (a) {
                c.show();
                a.stopPropagation();
            });
            if (this.hasComponent()) {
                this.component.on("click.iconpicker", function () {
                    c.toggle();
                });
            }
            if (this.hasInput()) {
                this.input.on("keyup.iconpicker", function (d) {
                    if (!b.inArray(d.keyCode, [38, 40, 37, 39, 16, 17, 18, 9, 8, 91, 93, 20, 46, 186, 190, 46, 78, 188, 44, 86])) {
                        c.update();
                    } else {
                        c._updateFormGroupStatus(c.getValid(this.value) !== false);
                    }
                    if (c.options.inputSearch === true) {
                        c.filter(a(this).val().toLowerCase());
                    }
                });
            }
        },
        _bindWindowEvents: function () {
            var b = a(window.document);
            var c = this;
            var d = ".iconpicker.inst" + this._id;
            a(window).on("resize.iconpicker" + d + " orientationchange.iconpicker" + d, function (a) {
                if (c.popover.hasClass("in")) {
                    c.updatePlacement();
                }
            });
            if (!c.isInline()) {
                b.on("mouseup" + d, function (a) {
                    if (!c._isEventInsideIconpicker(a) && !c.isInline()) {
                        c.hide();
                    }
                    a.stopPropagation();
                    a.preventDefault();
                    return false;
                });
            }
            return false;
        },
        _unbindElementEvents: function () {
            this.popover.off(".iconpicker");
            this.element.off(".iconpicker");
            if (this.hasInput()) {
                this.input.off(".iconpicker");
            }
            if (this.hasComponent()) {
                this.component.off(".iconpicker");
            }
            if (this.hasContainer()) {
                this.container.off(".iconpicker");
            }
        },
        _unbindWindowEvents: function () {
            a(window).off(".iconpicker.inst" + this._id);
            a(window.document).off(".iconpicker.inst" + this._id);
        },
        updatePlacement: function (b, c) {
            b = b || this.options.placement;
            this.options.placement = b;
            c = c || this.options.collision;
            c = c === true ? "flip" : c;
            var d = {
                at: "right bottom",
                my: "right top",
                of: this.hasInput() && !this.isInputGroup() ? this.input : this.container,
                collision: c === true ? "flip" : c,
                within: window
            };
            this.popover.removeClass("inline topLeftCorner topLeft top topRight topRightCorner " + "rightTop right rightBottom bottomRight bottomRightCorner " + "bottom bottomLeft bottomLeftCorner leftBottom left leftTop");
            if (typeof b === "object") {
                return this.popover.pos(a.extend({}, d, b));
            }
            switch (b) {
                case "inline":
                    {
                        d = false;
                    }
                    break;

                case "topLeftCorner":
                    {
                        d.my = "right bottom";
                        d.at = "left top";
                    }
                    break;

                case "topLeft":
                    {
                        d.my = "left bottom";
                        d.at = "left top";
                    }
                    break;

                case "top":
                    {
                        d.my = "center bottom";
                        d.at = "center top";
                    }
                    break;

                case "topRight":
                    {
                        d.my = "right bottom";
                        d.at = "right top";
                    }
                    break;

                case "topRightCorner":
                    {
                        d.my = "left bottom";
                        d.at = "right top";
                    }
                    break;

                case "rightTop":
                    {
                        d.my = "left bottom";
                        d.at = "right center";
                    }
                    break;

                case "right":
                    {
                        d.my = "left center";
                        d.at = "right center";
                    }
                    break;

                case "rightBottom":
                    {
                        d.my = "left top";
                        d.at = "right center";
                    }
                    break;

                case "bottomRightCorner":
                    {
                        d.my = "left top";
                        d.at = "right bottom";
                    }
                    break;

                case "bottomRight":
                    {
                        d.my = "right top";
                        d.at = "right bottom";
                    }
                    break;

                case "bottom":
                    {
                        d.my = "center top";
                        d.at = "center bottom";
                    }
                    break;

                case "bottomLeft":
                    {
                        d.my = "left top";
                        d.at = "left bottom";
                    }
                    break;

                case "bottomLeftCorner":
                    {
                        d.my = "right top";
                        d.at = "left bottom";
                    }
                    break;

                case "leftBottom":
                    {
                        d.my = "right top";
                        d.at = "left center";
                    }
                    break;

                case "left":
                    {
                        d.my = "right center";
                        d.at = "left center";
                    }
                    break;

                case "leftTop":
                    {
                        d.my = "right bottom";
                        d.at = "left center";
                    }
                    break;

                default:
                    {
                        return false;
                    }
                    break;
            }
            this.popover.css({
                display: this.options.placement === "inline" ? "" : "block"
            });
            if (d !== false) {
                this.popover.pos(d).css("maxWidth", a(window).width() - this.container.offset().left - 5);
            } else {
                this.popover.css({
                    top: "auto",
                    right: "auto",
                    bottom: "auto",
                    left: "auto",
                    maxWidth: "none"
                });
            }
            this.popover.addClass(this.options.placement);
            return true;
        },
        _updateComponents: function () {
            this.iconpicker.find(".iconpicker-item.iconpicker-selected").removeClass("iconpicker-selected " + this.options.selectedCustomClass);
            if (this.iconpickerValue) {
                this.iconpicker.find("." + this.options.fullClassFormatter(this.iconpickerValue).replace(/ /g, ".")).parent().addClass("iconpicker-selected " + this.options.selectedCustomClass);
            }
            if (this.hasComponent()) {
                var a = this.component.find("i");
                if (a.length > 0) {
                    a.attr("class", this.options.fullClassFormatter(this.iconpickerValue));
                } else {
                    this.component.html(this.getHtml());
                }
            }
        },
        _updateFormGroupStatus: function (a) {
            if (this.hasInput()) {
                if (a !== false) {
                    this.input.parents(".form-group:first").removeClass("has-error");
                } else {
                    this.input.parents(".form-group:first").addClass("has-error");
                }
                return true;
            }
            return false;
        },
        getValid: function (c) {
            if (!b.isString(c)) {
                c = "";
            }
            var d = c === "";
            c = a.trim(c);
            if (b.inArray(c, this.options.icons) || d) {
                return c;
            }
            return false;
        },
        setValue: function (a) {
            var b = this.getValid(a);
            if (b !== false) {
                this.iconpickerValue = b;
                this._trigger("iconpickerSetValue", {
                    iconpickerValue: b
                });
                return this.iconpickerValue;
            } else {
                this._trigger("iconpickerInvalid", {
                    iconpickerValue: a
                });
                return false;
            }
        },
        getHtml: function () {
            return '<i class="' + this.options.fullClassFormatter(this.iconpickerValue) + '"></i>';
        },
        setSourceValue: function (a) {
            a = this.setValue(a);
            if (a !== false && a !== "") {
                if (this.hasInput()) {
                    this.input.val(this.iconpickerValue);
                } else {
                    this.element.data("iconpickerValue", this.iconpickerValue);
                }
                this._trigger("iconpickerSetSourceValue", {
                    iconpickerValue: a
                });
            }
            return a;
        },
        getSourceValue: function (a) {
            a = a || this.options.defaultValue;
            var b = a;
            if (this.hasInput()) {
                b = this.input.val();
            } else {
                b = this.element.data("iconpickerValue");
            }
            if (b === undefined || b === "" || b === null || b === false) {
                b = a;
            }
            return b;
        },
        hasInput: function () {
            return this.input !== false;
        },
        isInputSearch: function () {
            return this.hasInput() && this.options.inputSearch === true;
        },
        isInputGroup: function () {
            return this.container.is(".input-group");
        },
        isDropdownMenu: function () {
            return this.container.is(".dropdown-menu");
        },
        hasSeparatedSearchInput: function () {
            return this.options.templates.search !== false && !this.isInputSearch();
        },
        hasComponent: function () {
            return this.component !== false;
        },
        hasContainer: function () {
            return this.container !== false;
        },
        getAcceptButton: function () {
            return this.popover.find(".iconpicker-btn-accept");
        },
        getCancelButton: function () {
            return this.popover.find(".iconpicker-btn-cancel");
        },
        getSearchInput: function () {
            return this.popover.find(".iconpicker-search");
        },
        filter: function (c) {
            if (b.isEmpty(c)) {
                this.iconpicker.find(".iconpicker-item").show();
                return a(false);
            } else {
                var d = [];
                this.iconpicker.find(".iconpicker-item").each(function () {
                    var b = a(this);
                    var e = b.attr("title").toLowerCase();
                    var f = false;
                    try {
                        f = new RegExp(c, "g");
                    } catch (a) {
                        f = false;
                    }
                    if (f !== false && e.match(f)) {
                        d.push(b);
                        b.show();
                    } else {
                        b.hide();
                    }
                });
                return d;
            }
        },
        show: function () {
            if (this.popover.hasClass("in")) {
                return false;
            }
            a.iconpicker.batch(a(".iconpicker-popover.in:not(.inline)").not(this.popover), "hide");
            this._trigger("iconpickerShow");
            this.updatePlacement();
            this.popover.addClass("in");
            setTimeout(a.proxy(function () {
                this.popover.css("display", this.isInline() ? "" : "block");
                this._trigger("iconpickerShown");
            }, this), this.options.animation ? 300 : 1);
        },
        hide: function () {
            if (!this.popover.hasClass("in")) {
                return false;
            }
            this._trigger("iconpickerHide");
            this.popover.removeClass("in");
            setTimeout(a.proxy(function () {
                this.popover.css("display", "none");
                this.getSearchInput().val("");
                this.filter("");
                this._trigger("iconpickerHidden");
            }, this), this.options.animation ? 300 : 1);
        },
        toggle: function () {
            if (this.popover.is(":visible")) {
                this.hide();
            } else {
                this.show(true);
            }
        },
        update: function (a, b) {
            a = a ? a : this.getSourceValue(this.iconpickerValue);
            this._trigger("iconpickerUpdate");
            if (b === true) {
                a = this.setValue(a);
            } else {
                a = this.setSourceValue(a);
                this._updateFormGroupStatus(a !== false);
            }
            if (a !== false) {
                this._updateComponents();
            }
            this._trigger("iconpickerUpdated");
            return a;
        },
        destroy: function () {
            this._trigger("iconpickerDestroy");
            this.element.removeData("iconpicker").removeData("iconpickerValue").removeClass("iconpicker-element");
            this._unbindElementEvents();
            this._unbindWindowEvents();
            a(this.popover).remove();
            this._trigger("iconpickerDestroyed");
        },
        disable: function () {
            if (this.hasInput()) {
                this.input.prop("disabled", true);
                return true;
            }
            return false;
        },
        enable: function () {
            if (this.hasInput()) {
                this.input.prop("disabled", false);
                return true;
            }
            return false;
        },
        isDisabled: function () {
            if (this.hasInput()) {
                return this.input.prop("disabled") === true;
            }
            return false;
        },
        isInline: function () {
            return this.options.placement === "inline" || this.popover.hasClass("inline");
        }
    };
    a.iconpicker = c;
    a.fn.iconpicker = function (b) {
        return this.each(function () {
            var d = a(this);
            if (!d.data("iconpicker")) {
                d.data("iconpicker", new c(this, typeof b === "object" ? b : {}));
            }
        });
    };
    c.defaultOptions.fullClassFormatter = function (val) {
        return  val;
    };
    c.defaultOptions.icons = ["fab fa-500px", "fab fa-accessible-icon", "fab fa-accusoft", "fab fa-acquisitions-incorporated", "fab fa-adn", "fab fa-adobe", "fab fa-adversal", "fab fa-affiliatetheme", "fab fa-airbnb", "fab fa-algolia", "fab fa-alipay", "fab fa-amazon-pay", "fab fa-amazon", "fab fa-amilia", "fab fa-android", "fab fa-angellist", "fab fa-angrycreative", "fab fa-angular", "fab fa-app-store-ios", "fab fa-app-store", "fab fa-apper", "fab fa-apple-pay", "fab fa-apple", "fab fa-artstation", "fab fa-asymmetrik", "fab fa-atlassian", "fab fa-audible", "fab fa-autoprefixer", "fab fa-avianex", "fab fa-aviato", "fab fa-aws", "fab fa-bandcamp", "fab fa-battle-net", "fab fa-behance-square", "fab fa-behance", "fab fa-bimobject", "fab fa-bitbucket", "fab fa-bitcoin", "fab fa-bity", "fab fa-black-tie", "fab fa-blackberry", "fab fa-blogger-b", "fab fa-blogger", "fab fa-bluetooth-b", "fab fa-bluetooth", "fab fa-bootstrap", "fab fa-btc", "fab fa-buffer", "fab fa-buromobelexperte", "fab fa-buy-n-large", "fab fa-buysellads", "fab fa-canadian-maple-leaf", "fab fa-cc-amazon-pay", "fab fa-cc-amex", "fab fa-cc-apple-pay", "fab fa-cc-diners-club", "fab fa-cc-discover", "fab fa-cc-jcb", "fab fa-cc-mastercard", "fab fa-cc-paypal", "fab fa-cc-stripe", "fab fa-cc-visa", "fab fa-centercode", "fab fa-centos", "fab fa-chrome", "fab fa-chromecast", "fab fa-cloudscale", "fab fa-cloudsmith", "fab fa-cloudversify", "fab fa-codepen", "fab fa-codiepie", "fab fa-confluence", "fab fa-connectdevelop", "fab fa-contao", "fab fa-cotton-bureau", "fab fa-cpanel", "fab fa-creative-commons-by", "fab fa-creative-commons-nc-eu", "fab fa-creative-commons-nc-jp", "fab fa-creative-commons-nc", "fab fa-creative-commons-nd", "fab fa-creative-commons-pd-alt", "fab fa-creative-commons-pd", "fab fa-creative-commons-remix", "fab fa-creative-commons-sa", "fab fa-creative-commons-sampling-plus", "fab fa-creative-commons-sampling", "fab fa-creative-commons-share", "fab fa-creative-commons-zero", "fab fa-creative-commons", "fab fa-critical-role", "fab fa-css3-alt", "fab fa-css3", "fab fa-cuttlefish", "fab fa-d-and-d-beyond", "fab fa-d-and-d", "fab fa-dailymotion", "fab fa-dashcube", "fab fa-delicious", "fab fa-deploydog", "fab fa-deskpro", "fab fa-dev", "fab fa-deviantart", "fab fa-dhl", "fab fa-diaspora", "fab fa-digg", "fab fa-digital-ocean", "fab fa-discord", "fab fa-discourse", "fab fa-dochub", "fab fa-docker", "fab fa-draft2digital", "fab fa-dribbble-square", "fab fa-dribbble", "fab fa-dropbox", "fab fa-drupal", "fab fa-dyalog", "fab fa-earlybirds", "fab fa-ebay", "fab fa-edge", "fab fa-elementor", "fab fa-ello", "fab fa-ember", "fab fa-empire", "fab fa-envira", "fab fa-erlang", "fab fa-ethereum", "fab fa-etsy", "fab fa-evernote", "fab fa-expeditedssl", "fab fa-facebook-f", "fab fa-facebook-messenger", "fab fa-facebook-square", "fab fa-facebook", "fab fa-fantasy-flight-games", "fab fa-fedex", "fab fa-fedora", "fab fa-figma", "fab fa-firefox-browser", "fab fa-firefox", "fab fa-first-order-alt", "fab fa-first-order", "fab fa-firstdraft", "fab fa-flickr", "fab fa-flipboard", "fab fa-fly", "fab fa-font-awesome-alt", "fab fa-font-awesome-flag", "fab fa-font-awesome", "fab fa-fonticons-fi", "fab fa-fonticons", "fab fa-fort-awesome-alt", "fab fa-fort-awesome", "fab fa-forumbee", "fab fa-foursquare", "fab fa-free-code-camp", "fab fa-freebsd", "fab fa-fulcrum", "fab fa-galactic-republic", "fab fa-galactic-senate", "fab fa-get-pocket", "fab fa-gg-circle", "fab fa-gg", "fab fa-git-alt", "fab fa-git-square", "fab fa-git", "fab fa-github-alt", "fab fa-github-square", "fab fa-github", "fab fa-gitkraken", "fab fa-gitlab", "fab fa-gitter", "fab fa-glide-g", "fab fa-glide", "fab fa-gofore", "fab fa-goodreads-g", "fab fa-goodreads", "fab fa-google-drive", "fab fa-google-play", "fab fa-google-plus-g", "fab fa-google-plus-square", "fab fa-google-plus", "fab fa-google-wallet", "fab fa-google", "fab fa-gratipay", "fab fa-grav", "fab fa-gripfire", "fab fa-grunt", "fab fa-gulp", "fab fa-hacker-news-square", "fab fa-hacker-news", "fab fa-hackerrank", "fab fa-hips", "fab fa-hire-a-helper", "fab fa-hooli", "fab fa-hornbill", "fab fa-hotjar", "fab fa-houzz", "fab fa-html5", "fab fa-hubspot", "fab fa-ideal", "fab fa-imdb", "fab fa-instagram-square", "fab fa-instagram", "fab fa-intercom", "fab fa-internet-explorer", "fab fa-invision", "fab fa-ioxhost", "fab fa-itch-io", "fab fa-itunes-note", "fab fa-itunes", "fab fa-java", "fab fa-jedi-order", "fab fa-jenkins", "fab fa-jira", "fab fa-joget", "fab fa-joomla", "fab fa-js-square", "fab fa-js", "fab fa-jsfiddle", "fab fa-kaggle", "fab fa-keybase", "fab fa-keycdn", "fab fa-kickstarter-k", "fab fa-kickstarter", "fab fa-korvue", "fab fa-laravel", "fab fa-lastfm-square", "fab fa-lastfm", "fab fa-leanpub", "fab fa-less", "fab fa-line", "fab fa-linkedin-in", "fab fa-linkedin", "fab fa-linode", "fab fa-linux", "fab fa-lyft", "fab fa-magento", "fab fa-mailchimp", "fab fa-mandalorian", "fab fa-markdown", "fab fa-mastodon", "fab fa-maxcdn", "fab fa-mdb", "fab fa-medapps", "fab fa-medium-m", "fab fa-medium", "fab fa-medrt", "fab fa-meetup", "fab fa-megaport", "fab fa-mendeley", "fab fa-microblog", "fab fa-microsoft", "fab fa-mix", "fab fa-mixcloud", "fab fa-mixer", "fab fa-mizuni", "fab fa-modx", "fab fa-monero", "fab fa-napster", "fab fa-neos", "fab fa-nimblr", "fab fa-node-js", "fab fa-node", "fab fa-npm", "fab fa-ns8", "fab fa-nutritionix", "fab fa-odnoklassniki-square", "fab fa-odnoklassniki", "fab fa-old-republic", "fab fa-opencart", "fab fa-openid", "fab fa-opera", "fab fa-optin-monster", "fab fa-orcid", "fab fa-osi", "fab fa-page4", "fab fa-pagelines", "fab fa-palfed", "fab fa-patreon", "fab fa-paypal", "fab fa-penny-arcade", "fab fa-periscope", "fab fa-phabricator", "fab fa-phoenix-framework", "fab fa-phoenix-squadron", "fab fa-php", "fab fa-pied-piper-alt", "fab fa-pied-piper-hat", "fab fa-pied-piper-pp", "fab fa-pied-piper-square", "fab fa-pied-piper", "fab fa-pinterest-p", "fab fa-pinterest-square", "fab fa-pinterest", "fab fa-playstation", "fab fa-product-hunt", "fab fa-pushed", "fab fa-python", "fab fa-qq", "fab fa-quinscape", "fab fa-quora", "fab fa-r-project", "fab fa-raspberry-pi", "fab fa-ravelry", "fab fa-react", "fab fa-reacteurope", "fab fa-readme", "fab fa-rebel", "fab fa-red-river", "fab fa-reddit-alien", "fab fa-reddit-square", "fab fa-reddit", "fab fa-redhat", "fab fa-renren", "fab fa-replyd", "fab fa-researchgate", "fab fa-resolving", "fab fa-rev", "fab fa-rocketchat", "fab fa-rockrms", "fab fa-safari", "fab fa-salesforce", "fab fa-sass", "fab fa-schlix", "fab fa-scribd", "fab fa-searchengin", "fab fa-sellcast", "fab fa-sellsy", "fab fa-servicestack", "fab fa-shirtsinbulk", "fab fa-shopify", "fab fa-shopware", "fab fa-simplybuilt", "fab fa-sistrix", "fab fa-sith", "fab fa-sketch", "fab fa-skyatlas", "fab fa-skype", "fab fa-slack-hash", "fab fa-slack", "fab fa-slideshare", "fab fa-snapchat-ghost", "fab fa-snapchat-square", "fab fa-snapchat", "fab fa-soundcloud", "fab fa-sourcetree", "fab fa-speakap", "fab fa-speaker-deck", "fab fa-spotify", "fab fa-squarespace", "fab fa-stack-exchange", "fab fa-stack-overflow", "fab fa-stackpath", "fab fa-staylinked", "fab fa-steam-square", "fab fa-steam-symbol", "fab fa-steam", "fab fa-sticker-mule", "fab fa-strava", "fab fa-stripe-s", "fab fa-stripe", "fab fa-studiovinari", "fab fa-stumbleupon-circle", "fab fa-stumbleupon", "fab fa-superpowers", "fab fa-supple", "fab fa-suse", "fab fa-swift", "fab fa-symfony", "fab fa-teamspeak", "fab fa-telegram-plane", "fab fa-telegram", "fab fa-tencent-weibo", "fab fa-the-red-yeti", "fab fa-themeco", "fab fa-themeisle", "fab fa-think-peaks", "fab fa-trade-federation", "fab fa-trello", "fab fa-tripadvisor", "fab fa-tumblr-square", "fab fa-tumblr", "fab fa-twitch", "fab fa-twitter-square", "fab fa-twitter", "fab fa-typo3", "fab fa-uber", "fab fa-ubuntu", "fab fa-uikit", "fab fa-umbraco", "fab fa-uniregistry", "fab fa-unity", "fab fa-untappd", "fab fa-ups", "fab fa-usb", "fab fa-usps", "fab fa-ussunnah", "fab fa-vaadin", "fab fa-viacoin", "fab fa-viadeo-square", "fab fa-viadeo", "fab fa-viber", "fab fa-vimeo-square", "fab fa-vimeo-v", "fab fa-vimeo", "fab fa-vine", "fab fa-vk", "fab fa-vnv", "fab fa-vuejs", "fab fa-waze", "fab fa-weebly", "fab fa-weibo", "fab fa-weixin", "fab fa-whatsapp-square", "fab fa-whatsapp", "fab fa-whmcs", "fab fa-wikipedia-w", "fab fa-windows", "fab fa-wix", "fab fa-wizards-of-the-coast", "fab fa-wolf-pack-battalion", "fab fa-wordpress-simple", "fab fa-wordpress", "fab fa-wpbeginner", "fab fa-wpexplorer", "fab fa-wpforms", "fab fa-wpressr", "fab fa-xbox", "fab fa-xing-square", "fab fa-xing", "fab fa-y-combinator", "fab fa-yahoo", "fab fa-yammer", "fab fa-yandex-international", "fab fa-yandex", "fab fa-yarn", "fab fa-yelp", "fab fa-yoast", "fab fa-youtube-square", "fab fa-youtube", "fab fa-zhihu", "far fa-address-book", "far fa-address-card", "far fa-angry", "far fa-arrow-alt-circle-down", "far fa-arrow-alt-circle-left", "far fa-arrow-alt-circle-right", "far fa-arrow-alt-circle-up", "far fa-bell-slash", "far fa-bell", "far fa-bookmark", "far fa-building", "far fa-calendar-alt", "far fa-calendar-check", "far fa-calendar-minus", "far fa-calendar-plus", "far fa-calendar-times", "far fa-calendar", "far fa-caret-square-down", "far fa-caret-square-left", "far fa-caret-square-right", "far fa-caret-square-up", "far fa-chart-bar", "far fa-check-circle", "far fa-check-square", "far fa-circle", "far fa-clipboard", "far fa-clock", "far fa-clone", "far fa-closed-captioning", "far fa-comment-alt", "far fa-comment-dots", "far fa-comment", "far fa-comments", "far fa-compass", "far fa-copy", "far fa-copyright", "far fa-credit-card", "far fa-dizzy", "far fa-dot-circle", "far fa-edit", "far fa-envelope-open", "far fa-envelope", "far fa-eye-slash", "far fa-eye", "far fa-file-alt", "far fa-file-archive", "far fa-file-audio", "far fa-file-code", "far fa-file-excel", "far fa-file-image", "far fa-file-pdf", "far fa-file-powerpoint", "far fa-file-video", "far fa-file-word", "far fa-file", "far fa-flag", "far fa-flushed", "far fa-folder-open", "far fa-folder", "far fa-frown-open", "far fa-frown", "far fa-futbol", "far fa-gem", "far fa-grimace", "far fa-grin-alt", "far fa-grin-beam-sweat", "far fa-grin-beam", "far fa-grin-hearts", "far fa-grin-squint-tears", "far fa-grin-squint", "far fa-grin-stars", "far fa-grin-tears", "far fa-grin-tongue-squint", "far fa-grin-tongue-wink", "far fa-grin-tongue", "far fa-grin-wink", "far fa-grin", "far fa-hand-lizard", "far fa-hand-paper", "far fa-hand-peace", "far fa-hand-point-down", "far fa-hand-point-left", "far fa-hand-point-right", "far fa-hand-point-up", "far fa-hand-pointer", "far fa-hand-rock", "far fa-hand-scissors", "far fa-hand-spock", "far fa-handshake", "far fa-hdd", "far fa-heart", "far fa-hospital", "far fa-hourglass", "far fa-id-badge", "far fa-id-card", "far fa-image", "far fa-images", "far fa-keyboard", "far fa-kiss-beam", "far fa-kiss-wink-heart", "far fa-kiss", "far fa-laugh-beam", "far fa-laugh-squint", "far fa-laugh-wink", "far fa-laugh", "far fa-lemon", "far fa-life-ring", "far fa-lightbulb", "far fa-list-alt", "far fa-map", "far fa-meh-blank", "far fa-meh-rolling-eyes", "far fa-meh", "far fa-minus-square", "far fa-money-bill-alt", "far fa-moon", "far fa-newspaper", "far fa-object-group", "far fa-object-ungroup", "far fa-paper-plane", "far fa-pause-circle", "far fa-play-circle", "far fa-plus-square", "far fa-question-circle", "far fa-registered", "far fa-sad-cry", "far fa-sad-tear", "far fa-save", "far fa-share-square", "far fa-smile-beam", "far fa-smile-wink", "far fa-smile", "far fa-snowflake", "far fa-square", "far fa-star-half", "far fa-star", "far fa-sticky-note", "far fa-stop-circle", "far fa-sun", "far fa-surprise", "far fa-thumbs-down", "far fa-thumbs-up", "far fa-times-circle", "far fa-tired", "far fa-trash-alt", "far fa-user-circle", "far fa-user", "far fa-window-close", "far fa-window-maximize", "far fa-window-minimize", "far fa-window-restore", "fas fa-ad", "fas fa-address-book", "fas fa-address-card", "fas fa-adjust", "fas fa-air-freshener", "fas fa-align-center", "fas fa-align-justify", "fas fa-align-left", "fas fa-align-right", "fas fa-allergies", "fas fa-ambulance", "fas fa-american-sign-language-interpreting", "fas fa-anchor", "fas fa-angle-double-down", "fas fa-angle-double-left", "fas fa-angle-double-right", "fas fa-angle-double-up", "fas fa-angle-down", "fas fa-angle-left", "fas fa-angle-right", "fas fa-angle-up", "fas fa-angry", "fas fa-ankh", "fas fa-apple-alt", "fas fa-archive", "fas fa-archway", "fas fa-arrow-alt-circle-down", "fas fa-arrow-alt-circle-left", "fas fa-arrow-alt-circle-right", "fas fa-arrow-alt-circle-up", "fas fa-arrow-circle-down", "fas fa-arrow-circle-left", "fas fa-arrow-circle-right", "fas fa-arrow-circle-up", "fas fa-arrow-down", "fas fa-arrow-left", "fas fa-arrow-right", "fas fa-arrow-up", "fas fa-arrows-alt-h", "fas fa-arrows-alt-v", "fas fa-arrows-alt", "fas fa-assistive-listening-systems", "fas fa-asterisk", "fas fa-at", "fas fa-atlas", "fas fa-atom", "fas fa-audio-description", "fas fa-award", "fas fa-baby-carriage", "fas fa-baby", "fas fa-backspace", "fas fa-backward", "fas fa-bacon", "fas fa-bahai", "fas fa-balance-scale-left", "fas fa-balance-scale-right", "fas fa-balance-scale", "fas fa-ban", "fas fa-band-aid", "fas fa-barcode", "fas fa-bars", "fas fa-baseball-ball", "fas fa-basketball-ball", "fas fa-bath", "fas fa-battery-empty", "fas fa-battery-full", "fas fa-battery-half", "fas fa-battery-quarter", "fas fa-battery-three-quarters", "fas fa-bed", "fas fa-beer", "fas fa-bell-slash", "fas fa-bell", "fas fa-bezier-curve", "fas fa-bible", "fas fa-bicycle", "fas fa-biking", "fas fa-binoculars", "fas fa-biohazard", "fas fa-birthday-cake", "fas fa-blender-phone", "fas fa-blender", "fas fa-blind", "fas fa-blog", "fas fa-bold", "fas fa-bolt", "fas fa-bomb", "fas fa-bone", "fas fa-bong", "fas fa-book-dead", "fas fa-book-medical", "fas fa-book-open", "fas fa-book-reader", "fas fa-book", "fas fa-bookmark", "fas fa-border-all", "fas fa-border-none", "fas fa-border-style", "fas fa-bowling-ball", "fas fa-box-open", "fas fa-box-tissue", "fas fa-box", "fas fa-boxes", "fas fa-braille", "fas fa-brain", "fas fa-bread-slice", "fas fa-briefcase-medical", "fas fa-briefcase", "fas fa-broadcast-tower", "fas fa-broom", "fas fa-brush", "fas fa-bug", "fas fa-building", "fas fa-bullhorn", "fas fa-bullseye", "fas fa-burn", "fas fa-bus-alt", "fas fa-bus", "fas fa-business-time", "fas fa-calculator", "fas fa-calendar-alt", "fas fa-calendar-check", "fas fa-calendar-day", "fas fa-calendar-minus", "fas fa-calendar-plus", "fas fa-calendar-times", "fas fa-calendar-week", "fas fa-calendar", "fas fa-camera-retro", "fas fa-camera", "fas fa-campground", "fas fa-candy-cane", "fas fa-cannabis", "fas fa-capsules", "fas fa-car-alt", "fas fa-car-battery", "fas fa-car-crash", "fas fa-car-side", "fas fa-car", "fas fa-caravan", "fas fa-caret-down", "fas fa-caret-left", "fas fa-caret-right", "fas fa-caret-square-down", "fas fa-caret-square-left", "fas fa-caret-square-right", "fas fa-caret-square-up", "fas fa-caret-up", "fas fa-carrot", "fas fa-cart-arrow-down", "fas fa-cart-plus", "fas fa-cash-register", "fas fa-cat", "fas fa-certificate", "fas fa-chair", "fas fa-chalkboard-teacher", "fas fa-chalkboard", "fas fa-charging-station", "fas fa-chart-area", "fas fa-chart-bar", "fas fa-chart-line", "fas fa-chart-pie", "fas fa-check-circle", "fas fa-check-double", "fas fa-check-square", "fas fa-check", "fas fa-cheese", "fas fa-chess-bishop", "fas fa-chess-board", "fas fa-chess-king", "fas fa-chess-knight", "fas fa-chess-pawn", "fas fa-chess-queen", "fas fa-chess-rook", "fas fa-chess", "fas fa-chevron-circle-down", "fas fa-chevron-circle-left", "fas fa-chevron-circle-right", "fas fa-chevron-circle-up", "fas fa-chevron-down", "fas fa-chevron-left", "fas fa-chevron-right", "fas fa-chevron-up", "fas fa-child", "fas fa-church", "fas fa-circle-notch", "fas fa-circle", "fas fa-city", "fas fa-clinic-medical", "fas fa-clipboard-check", "fas fa-clipboard-list", "fas fa-clipboard", "fas fa-clock", "fas fa-clone", "fas fa-closed-captioning", "fas fa-cloud-download-alt", "fas fa-cloud-meatball", "fas fa-cloud-moon-rain", "fas fa-cloud-moon", "fas fa-cloud-rain", "fas fa-cloud-showers-heavy", "fas fa-cloud-sun-rain", "fas fa-cloud-sun", "fas fa-cloud-upload-alt", "fas fa-cloud", "fas fa-cocktail", "fas fa-code-branch", "fas fa-code", "fas fa-coffee", "fas fa-cog", "fas fa-cogs", "fas fa-coins", "fas fa-columns", "fas fa-comment-alt", "fas fa-comment-dollar", "fas fa-comment-dots", "fas fa-comment-medical", "fas fa-comment-slash", "fas fa-comment", "fas fa-comments-dollar", "fas fa-comments", "fas fa-compact-disc", "fas fa-compass", "fas fa-compress-alt", "fas fa-compress-arrows-alt", "fas fa-compress", "fas fa-concierge-bell", "fas fa-cookie-bite", "fas fa-cookie", "fas fa-copy", "fas fa-copyright", "fas fa-couch", "fas fa-credit-card", "fas fa-crop-alt", "fas fa-crop", "fas fa-cross", "fas fa-crosshairs", "fas fa-crow", "fas fa-crown", "fas fa-crutch", "fas fa-cube", "fas fa-cubes", "fas fa-cut", "fas fa-database", "fas fa-deaf", "fas fa-democrat", "fas fa-desktop", "fas fa-dharmachakra", "fas fa-diagnoses", "fas fa-dice-d20", "fas fa-dice-d6", "fas fa-dice-five", "fas fa-dice-four", "fas fa-dice-one", "fas fa-dice-six", "fas fa-dice-three", "fas fa-dice-two", "fas fa-dice", "fas fa-digital-tachograph", "fas fa-directions", "fas fa-disease", "fas fa-divide", "fas fa-dizzy", "fas fa-dna", "fas fa-dog", "fas fa-dollar-sign", "fas fa-dolly-flatbed", "fas fa-dolly", "fas fa-donate", "fas fa-door-closed", "fas fa-door-open", "fas fa-dot-circle", "fas fa-dove", "fas fa-download", "fas fa-drafting-compass", "fas fa-dragon", "fas fa-draw-polygon", "fas fa-drum-steelpan", "fas fa-drum", "fas fa-drumstick-bite", "fas fa-dumbbell", "fas fa-dumpster-fire", "fas fa-dumpster", "fas fa-dungeon", "fas fa-edit", "fas fa-egg", "fas fa-eject", "fas fa-ellipsis-h", "fas fa-ellipsis-v", "fas fa-envelope-open-text", "fas fa-envelope-open", "fas fa-envelope-square", "fas fa-envelope", "fas fa-equals", "fas fa-eraser", "fas fa-ethernet", "fas fa-euro-sign", "fas fa-exchange-alt", "fas fa-exclamation-circle", "fas fa-exclamation-triangle", "fas fa-exclamation", "fas fa-expand-alt", "fas fa-expand-arrows-alt", "fas fa-expand", "fas fa-external-link-alt", "fas fa-external-link-square-alt", "fas fa-eye-dropper", "fas fa-eye-slash", "fas fa-eye", "fas fa-fan", "fas fa-fast-backward", "fas fa-fast-forward", "fas fa-faucet", "fas fa-fax", "fas fa-feather-alt", "fas fa-feather", "fas fa-female", "fas fa-fighter-jet", "fas fa-file-alt", "fas fa-file-archive", "fas fa-file-audio", "fas fa-file-code", "fas fa-file-contract", "fas fa-file-csv", "fas fa-file-download", "fas fa-file-excel", "fas fa-file-export", "fas fa-file-image", "fas fa-file-import", "fas fa-file-invoice-dollar", "fas fa-file-invoice", "fas fa-file-medical-alt", "fas fa-file-medical", "fas fa-file-pdf", "fas fa-file-powerpoint", "fas fa-file-prescription", "fas fa-file-signature", "fas fa-file-upload", "fas fa-file-video", "fas fa-file-word", "fas fa-file", "fas fa-fill-drip", "fas fa-fill", "fas fa-film", "fas fa-filter", "fas fa-fingerprint", "fas fa-fire-alt", "fas fa-fire-extinguisher", "fas fa-fire", "fas fa-first-aid", "fas fa-fish", "fas fa-fist-raised", "fas fa-flag-checkered", "fas fa-flag-usa", "fas fa-flag", "fas fa-flask", "fas fa-flushed", "fas fa-folder-minus", "fas fa-folder-open", "fas fa-folder-plus", "fas fa-folder", "fas fa-font", "fas fa-football-ball", "fas fa-forward", "fas fa-frog", "fas fa-frown-open", "fas fa-frown", "fas fa-funnel-dollar", "fas fa-futbol", "fas fa-gamepad", "fas fa-gas-pump", "fas fa-gavel", "fas fa-gem", "fas fa-genderless", "fas fa-ghost", "fas fa-gift", "fas fa-gifts", "fas fa-glass-cheers", "fas fa-glass-martini-alt", "fas fa-glass-martini", "fas fa-glass-whiskey", "fas fa-glasses", "fas fa-globe-africa", "fas fa-globe-americas", "fas fa-globe-asia", "fas fa-globe-europe", "fas fa-globe", "fas fa-golf-ball", "fas fa-gopuram", "fas fa-graduation-cap", "fas fa-greater-than-equal", "fas fa-greater-than", "fas fa-grimace", "fas fa-grin-alt", "fas fa-grin-beam-sweat", "fas fa-grin-beam", "fas fa-grin-hearts", "fas fa-grin-squint-tears", "fas fa-grin-squint", "fas fa-grin-stars", "fas fa-grin-tears", "fas fa-grin-tongue-squint", "fas fa-grin-tongue-wink", "fas fa-grin-tongue", "fas fa-grin-wink", "fas fa-grin", "fas fa-grip-horizontal", "fas fa-grip-lines-vertical", "fas fa-grip-lines", "fas fa-grip-vertical", "fas fa-guitar", "fas fa-h-square", "fas fa-hamburger", "fas fa-hammer", "fas fa-hamsa", "fas fa-hand-holding-heart", "fas fa-hand-holding-medical", "fas fa-hand-holding-usd", "fas fa-hand-holding-water", "fas fa-hand-holding", "fas fa-hand-lizard", "fas fa-hand-middle-finger", "fas fa-hand-paper", "fas fa-hand-peace", "fas fa-hand-point-down", "fas fa-hand-point-left", "fas fa-hand-point-right", "fas fa-hand-point-up", "fas fa-hand-pointer", "fas fa-hand-rock", "fas fa-hand-scissors", "fas fa-hand-sparkles", "fas fa-hand-spock", "fas fa-hands-helping", "fas fa-hands-wash", "fas fa-hands", "fas fa-handshake-alt-slash", "fas fa-handshake-slash", "fas fa-handshake", "fas fa-hanukiah", "fas fa-hard-hat", "fas fa-hashtag", "fas fa-hat-cowboy-side", "fas fa-hat-cowboy", "fas fa-hat-wizard", "fas fa-hdd", "fas fa-head-side-cough-slash", "fas fa-head-side-cough", "fas fa-head-side-mask", "fas fa-head-side-virus", "fas fa-heading", "fas fa-headphones-alt", "fas fa-headphones", "fas fa-headset", "fas fa-heart-broken", "fas fa-heart", "fas fa-heartbeat", "fas fa-helicopter", "fas fa-highlighter", "fas fa-hiking", "fas fa-hippo", "fas fa-history", "fas fa-hockey-puck", "fas fa-holly-berry", "fas fa-home", "fas fa-horse-head", "fas fa-horse", "fas fa-hospital-alt", "fas fa-hospital-symbol", "fas fa-hospital-user", "fas fa-hospital", "fas fa-hot-tub", "fas fa-hotdog", "fas fa-hotel", "fas fa-hourglass-end", "fas fa-hourglass-half", "fas fa-hourglass-start", "fas fa-hourglass", "fas fa-house-damage", "fas fa-house-user", "fas fa-hryvnia", "fas fa-i-cursor", "fas fa-ice-cream", "fas fa-icicles", "fas fa-icons", "fas fa-id-badge", "fas fa-id-card-alt", "fas fa-id-card", "fas fa-igloo", "fas fa-image", "fas fa-images", "fas fa-inbox", "fas fa-indent", "fas fa-industry", "fas fa-infinity", "fas fa-info-circle", "fas fa-info", "fas fa-italic", "fas fa-jedi", "fas fa-joint", "fas fa-journal-whills", "fas fa-kaaba", "fas fa-key", "fas fa-keyboard", "fas fa-khanda", "fas fa-kiss-beam", "fas fa-kiss-wink-heart", "fas fa-kiss", "fas fa-kiwi-bird", "fas fa-landmark", "fas fa-language", "fas fa-laptop-code", "fas fa-laptop-house", "fas fa-laptop-medical", "fas fa-laptop", "fas fa-laugh-beam", "fas fa-laugh-squint", "fas fa-laugh-wink", "fas fa-laugh", "fas fa-layer-group", "fas fa-leaf", "fas fa-lemon", "fas fa-less-than-equal", "fas fa-less-than", "fas fa-level-down-alt", "fas fa-level-up-alt", "fas fa-life-ring", "fas fa-lightbulb", "fas fa-link", "fas fa-lira-sign", "fas fa-list-alt", "fas fa-list-ol", "fas fa-list-ul", "fas fa-list", "fas fa-location-arrow", "fas fa-lock-open", "fas fa-lock", "fas fa-long-arrow-alt-down", "fas fa-long-arrow-alt-left", "fas fa-long-arrow-alt-right", "fas fa-long-arrow-alt-up", "fas fa-low-vision", "fas fa-luggage-cart", "fas fa-lungs-virus", "fas fa-lungs", "fas fa-magic", "fas fa-magnet", "fas fa-mail-bulk", "fas fa-male", "fas fa-map-marked-alt", "fas fa-map-marked", "fas fa-map-marker-alt", "fas fa-map-marker", "fas fa-map-pin", "fas fa-map-signs", "fas fa-map", "fas fa-marker", "fas fa-mars-double", "fas fa-mars-stroke-h", "fas fa-mars-stroke-v", "fas fa-mars-stroke", "fas fa-mars", "fas fa-mask", "fas fa-medal", "fas fa-medkit", "fas fa-meh-blank", "fas fa-meh-rolling-eyes", "fas fa-meh", "fas fa-memory", "fas fa-menorah", "fas fa-mercury", "fas fa-meteor", "fas fa-microchip", "fas fa-microphone-alt-slash", "fas fa-microphone-alt", "fas fa-microphone-slash", "fas fa-microphone", "fas fa-microscope", "fas fa-minus-circle", "fas fa-minus-square", "fas fa-minus", "fas fa-mitten", "fas fa-mobile-alt", "fas fa-mobile", "fas fa-money-bill-alt", "fas fa-money-bill-wave-alt", "fas fa-money-bill-wave", "fas fa-money-bill", "fas fa-money-check-alt", "fas fa-money-check", "fas fa-monument", "fas fa-moon", "fas fa-mortar-pestle", "fas fa-mosque", "fas fa-motorcycle", "fas fa-mountain", "fas fa-mouse-pointer", "fas fa-mouse", "fas fa-mug-hot", "fas fa-music", "fas fa-network-wired", "fas fa-neuter", "fas fa-newspaper", "fas fa-not-equal", "fas fa-notes-medical", "fas fa-object-group", "fas fa-object-ungroup", "fas fa-oil-can", "fas fa-om", "fas fa-otter", "fas fa-outdent", "fas fa-pager", "fas fa-paint-brush", "fas fa-paint-roller", "fas fa-palette", "fas fa-pallet", "fas fa-paper-plane", "fas fa-paperclip", "fas fa-parachute-box", "fas fa-paragraph", "fas fa-parking", "fas fa-passport", "fas fa-pastafarianism", "fas fa-paste", "fas fa-pause-circle", "fas fa-pause", "fas fa-paw", "fas fa-peace", "fas fa-pen-alt", "fas fa-pen-fancy", "fas fa-pen-nib", "fas fa-pen-square", "fas fa-pen", "fas fa-pencil-alt", "fas fa-pencil-ruler", "fas fa-people-arrows", "fas fa-people-carry", "fas fa-pepper-hot", "fas fa-percent", "fas fa-percentage", "fas fa-person-booth", "fas fa-phone-alt", "fas fa-phone-slash", "fas fa-phone-square-alt", "fas fa-phone-square", "fas fa-phone-volume", "fas fa-phone", "fas fa-photo-video", "fas fa-piggy-bank", "fas fa-pills", "fas fa-pizza-slice", "fas fa-place-of-worship", "fas fa-plane-arrival", "fas fa-plane-departure", "fas fa-plane-slash", "fas fa-plane", "fas fa-play-circle", "fas fa-play", "fas fa-plug", "fas fa-plus-circle", "fas fa-plus-square", "fas fa-plus", "fas fa-podcast", "fas fa-poll-h", "fas fa-poll", "fas fa-poo-storm", "fas fa-poo", "fas fa-poop", "fas fa-portrait", "fas fa-pound-sign", "fas fa-power-off", "fas fa-pray", "fas fa-praying-hands", "fas fa-prescription-bottle-alt", "fas fa-prescription-bottle", "fas fa-prescription", "fas fa-print", "fas fa-procedures", "fas fa-project-diagram", "fas fa-pump-medical", "fas fa-pump-soap", "fas fa-puzzle-piece", "fas fa-qrcode", "fas fa-question-circle", "fas fa-question", "fas fa-quidditch", "fas fa-quote-left", "fas fa-quote-right", "fas fa-quran", "fas fa-radiation-alt", "fas fa-radiation", "fas fa-rainbow", "fas fa-random", "fas fa-receipt", "fas fa-record-vinyl", "fas fa-recycle", "fas fa-redo-alt", "fas fa-redo", "fas fa-registered", "fas fa-remove-format", "fas fa-reply-all", "fas fa-reply", "fas fa-republican", "fas fa-restroom", "fas fa-retweet", "fas fa-ribbon", "fas fa-ring", "fas fa-road", "fas fa-robot", "fas fa-rocket", "fas fa-route", "fas fa-rss-square", "fas fa-rss", "fas fa-ruble-sign", "fas fa-ruler-combined", "fas fa-ruler-horizontal", "fas fa-ruler-vertical", "fas fa-ruler", "fas fa-running", "fas fa-rupee-sign", "fas fa-sad-cry", "fas fa-sad-tear", "fas fa-satellite-dish", "fas fa-satellite", "fas fa-save", "fas fa-school", "fas fa-screwdriver", "fas fa-scroll", "fas fa-sd-card", "fas fa-search-dollar", "fas fa-search-location", "fas fa-search-minus", "fas fa-search-plus", "fas fa-search", "fas fa-seedling", "fas fa-server", "fas fa-shapes", "fas fa-share-alt-square", "fas fa-share-alt", "fas fa-share-square", "fas fa-share", "fas fa-shekel-sign", "fas fa-shield-alt", "fas fa-shield-virus", "fas fa-ship", "fas fa-shipping-fast", "fas fa-shoe-prints", "fas fa-shopping-bag", "fas fa-shopping-basket", "fas fa-shopping-cart", "fas fa-shower", "fas fa-shuttle-van", "fas fa-sign-in-alt", "fas fa-sign-language", "fas fa-sign-out-alt", "fas fa-sign", "fas fa-signal", "fas fa-signature", "fas fa-sim-card", "fas fa-sitemap", "fas fa-skating", "fas fa-skiing-nordic", "fas fa-skiing", "fas fa-skull-crossbones", "fas fa-skull", "fas fa-slash", "fas fa-sleigh", "fas fa-sliders-h", "fas fa-smile-beam", "fas fa-smile-wink", "fas fa-smile", "fas fa-smog", "fas fa-smoking-ban", "fas fa-smoking", "fas fa-sms", "fas fa-snowboarding", "fas fa-snowflake", "fas fa-snowman", "fas fa-snowplow", "fas fa-soap", "fas fa-socks", "fas fa-solar-panel", "fas fa-sort-alpha-down-alt", "fas fa-sort-alpha-down", "fas fa-sort-alpha-up-alt", "fas fa-sort-alpha-up", "fas fa-sort-amount-down-alt", "fas fa-sort-amount-down", "fas fa-sort-amount-up-alt", "fas fa-sort-amount-up", "fas fa-sort-down", "fas fa-sort-numeric-down-alt", "fas fa-sort-numeric-down", "fas fa-sort-numeric-up-alt", "fas fa-sort-numeric-up", "fas fa-sort-up", "fas fa-sort", "fas fa-spa", "fas fa-space-shuttle", "fas fa-spell-check", "fas fa-spider", "fas fa-spinner", "fas fa-splotch", "fas fa-spray-can", "fas fa-square-full", "fas fa-square-root-alt", "fas fa-square", "fas fa-stamp", "fas fa-star-and-crescent", "fas fa-star-half-alt", "fas fa-star-half", "fas fa-star-of-david", "fas fa-star-of-life", "fas fa-star", "fas fa-step-backward", "fas fa-step-forward", "fas fa-stethoscope", "fas fa-sticky-note", "fas fa-stop-circle", "fas fa-stop", "fas fa-stopwatch-20", "fas fa-stopwatch", "fas fa-store-alt-slash", "fas fa-store-alt", "fas fa-store-slash", "fas fa-store", "fas fa-stream", "fas fa-street-view", "fas fa-strikethrough", "fas fa-stroopwafel", "fas fa-subscript", "fas fa-subway", "fas fa-suitcase-rolling", "fas fa-suitcase", "fas fa-sun", "fas fa-superscript", "fas fa-surprise", "fas fa-swatchbook", "fas fa-swimmer", "fas fa-swimming-pool", "fas fa-synagogue", "fas fa-sync-alt", "fas fa-sync", "fas fa-syringe", "fas fa-table-tennis", "fas fa-table", "fas fa-tablet-alt", "fas fa-tablet", "fas fa-tablets", "fas fa-tachometer-alt", "fas fa-tag", "fas fa-tags", "fas fa-tape", "fas fa-tasks", "fas fa-taxi", "fas fa-teeth-open", "fas fa-teeth", "fas fa-temperature-high", "fas fa-temperature-low", "fas fa-tenge", "fas fa-terminal", "fas fa-text-height", "fas fa-text-width", "fas fa-th-large", "fas fa-th-list", "fas fa-th", "fas fa-theater-masks", "fas fa-thermometer-empty", "fas fa-thermometer-full", "fas fa-thermometer-half", "fas fa-thermometer-quarter", "fas fa-thermometer-three-quarters", "fas fa-thermometer", "fas fa-thumbs-down", "fas fa-thumbs-up", "fas fa-thumbtack", "fas fa-ticket-alt", "fas fa-times-circle", "fas fa-times", "fas fa-tint-slash", "fas fa-tint", "fas fa-tired", "fas fa-toggle-off", "fas fa-toggle-on", "fas fa-toilet-paper-slash", "fas fa-toilet-paper", "fas fa-toilet", "fas fa-toolbox", "fas fa-tools", "fas fa-tooth", "fas fa-torah", "fas fa-torii-gate", "fas fa-tractor", "fas fa-trademark", "fas fa-traffic-light", "fas fa-trailer", "fas fa-train", "fas fa-tram", "fas fa-transgender-alt", "fas fa-transgender", "fas fa-trash-alt", "fas fa-trash-restore-alt", "fas fa-trash-restore", "fas fa-trash", "fas fa-tree", "fas fa-trophy", "fas fa-truck-loading", "fas fa-truck-monster", "fas fa-truck-moving", "fas fa-truck-pickup", "fas fa-truck", "fas fa-tshirt", "fas fa-tty", "fas fa-tv", "fas fa-umbrella-beach", "fas fa-umbrella", "fas fa-underline", "fas fa-undo-alt", "fas fa-undo", "fas fa-universal-access", "fas fa-university", "fas fa-unlink", "fas fa-unlock-alt", "fas fa-unlock", "fas fa-upload", "fas fa-user-alt-slash", "fas fa-user-alt", "fas fa-user-astronaut", "fas fa-user-check", "fas fa-user-circle", "fas fa-user-clock", "fas fa-user-cog", "fas fa-user-edit", "fas fa-user-friends", "fas fa-user-graduate", "fas fa-user-injured", "fas fa-user-lock", "fas fa-user-md", "fas fa-user-minus", "fas fa-user-ninja", "fas fa-user-nurse", "fas fa-user-plus", "fas fa-user-secret", "fas fa-user-shield", "fas fa-user-slash", "fas fa-user-tag", "fas fa-user-tie", "fas fa-user-times", "fas fa-user", "fas fa-users-cog", "fas fa-users", "fas fa-utensil-spoon", "fas fa-utensils", "fas fa-vector-square", "fas fa-venus-double", "fas fa-venus-mars", "fas fa-venus", "fas fa-vial", "fas fa-vials", "fas fa-video-slash", "fas fa-video", "fas fa-vihara", "fas fa-virus-slash", "fas fa-virus", "fas fa-viruses", "fas fa-voicemail", "fas fa-volleyball-ball", "fas fa-volume-down", "fas fa-volume-mute", "fas fa-volume-off", "fas fa-volume-up", "fas fa-vote-yea", "fas fa-vr-cardboard", "fas fa-walking", "fas fa-wallet", "fas fa-warehouse", "fas fa-water", "fas fa-wave-square", "fas fa-weight-hanging", "fas fa-weight", "fas fa-wheelchair", "fas fa-wifi", "fas fa-wind", "fas fa-window-close", "fas fa-window-maximize", "fas fa-window-minimize", "fas fa-window-restore", "fas fa-wine-bottle", "fas fa-wine-glass-alt", "fas fa-wine-glass", "fas fa-won-sign", "fas fa-wrench", "fas fa-x-ray", "fas fa-yen-sign", "fas fa-yin-yang"];
    c.defaultOptions.templates = {
        popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' +
                '<div class="popover-title"></div><div class="popover-content"></div></div>',
        footer: '<div class="popover-footer"></div>',
        buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' +
                ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
        search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
        iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
        iconpickerItem: '<a role="button" href="#" class="iconpicker-item"><i></i></a>',
    };
});