(function ($) {
    var DATA_TOOL = 'wpra-tool';
    var URL_TOOL_PARAM = 'tool';

    // The tool tabs and pages
    var tabs, pages;
    // Get the current tool from the URL
    var currTool = getUrlParam(window.location, URL_TOOL_PARAM);

    // When a state is popped, navigate to the corresponding tool
    window.onpopstate = function (event) {
        if (event.state) {
            setCurrentTool(event.state.tool);
        } else {
            setCurrentTool();
        }
    };

    // Initialize elements and events
    $(document).ready(function () {
        tabs = $('.nav-tab-wrapper > .wpra-tool-tab');
        pages = $('.wpra-tools-container > .wpra-tool');

        setCurrentTool(currTool);
        pushHistoryTool(currTool, true);

        // Add click handler for tabs
        tabs.click(onTabClicked);

        // Initialize links
        pages.find('a').each(function () {
            var el = $(this);
            var href = el.attr('href');
            var tool = getUrlParam(href, URL_TOOL_PARAM);

            if (!tool) {
                return;
            }

            // If the link points to a tab, add a click handler for navigation to that tab
            if (rebuildToolUrl(href, '') === rebuildToolUrl(window.location.href, '')) {
                el.click(function (e) {
                    navigate(tool);
                    e.preventDefault();
                });
            }
        });

        $(document).trigger('wpra/tools/on_loaded', [currTool]);
    });

    // Get the tab for a given tool key
    function getTab(key) {
        return tabs.filter(function () {
            return $(this).data('wpra-tool') === key;
        });
    }

    // Get the page for a given tool key
    function getPage(key) {
        return pages.filter(function () {
            return $(this).data('wpra-tool') === key;
        });
    }

    // Event handler for when a tab is clicked
    function onTabClicked(e) {
        let target = $(e.target);
        let tool = target.data('wpra-tool');

        navigate(tool);
    }

    // Navigates to a particular tool.
    // Preferred over `setCurrentTool()`
    function navigate(tool)
    {
        if (tool === currTool) {
            return;
        }

        setCurrentTool(tool);
        pushHistoryTool(currTool);
    }

    // Set the current tool and updates the DOM
    function setCurrentTool(tool)
    {
        $(document).trigger('wpra/tools/on_leaving_tool', [currTool]);
        $(document).trigger('wpra/tools/on_leaving_from_' + currTool);

        showTool(currTool = tool);

        $(document).trigger('wpra/tools/on_switched_to_' + currTool);
        $(document).trigger('wpra/tools/on_switched_tool', [currTool]);
    }

    // Updates the DOM to show a particular tool
    function showTool(tool)
    {
        // Default to first tab
        if (!tool) {
            tool = tabs.first().data('wpra-tool');
        }

        let tab = getTab(tool);
        let page = getPage(tool);

        pages.hide();
        tabs.removeClass('nav-tab-active');

        page.show();
        tab.addClass('nav-tab-active');
    }

    // Utility function that pushes a tool navigation entry to the browser's history
    function pushHistoryTool(tool, replace) {
        if (!tool) {
            return;
        }

        var newUrl = rebuildToolUrl(window.location.href, tool);

        if (replace) {
            history.replaceState({tool: currTool}, window.document.title, newUrl);
        } else {
            history.pushState({tool: currTool}, window.document.title, newUrl);
        }
    }

    // Utility function that rebuilds a URL for a given tool
    function rebuildToolUrl(url, tool)
    {
        var urlSplit = url.split('?', 2);
        var params = parseQueryString(urlSplit[1]);
        params[URL_TOOL_PARAM] = tool;
        var newParams = stringifyQuery(params);

        return urlSplit[0] + '?' + newParams;
    }

    // Utility function to get a URL param
    function getUrlParam(url, name, def) {
        name = name.replace(/[\[\]]/g, '\\$&');

        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
        var results = regex.exec(url);

        if (!results) {
            return def;
        }

        if (!results[2]) {
            return def;
        }

        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    function parseQueryString(str) {
        if (typeof str !== 'string') {
            return {};
        }

        str = str.trim().replace(/^\?/, '');

        if (!str) {
            return {};
        }

        return str.trim().split('&').reduce(function (ret, param) {
            var parts = param.replace(/\+/g, ' ').split('=');
            var key = parts[0];
            var val = parts[1];

            key = decodeURIComponent(key);
            // missing `=` should be `null`:
            // http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
            val = val === undefined ? null : decodeURIComponent(val);

            if (!ret.hasOwnProperty(key)) {
                ret[key] = val;
            } else if (Array.isArray(ret[key])) {
                ret[key].push(val);
            } else {
                ret[key] = [ret[key], val];
            }

            return ret;
        }, {});
    };

    function stringifyQuery(obj) {
        return obj ? Object.keys(obj).map(function (key) {
            var val = obj[key];

            if (Array.isArray(val)) {
                return val.map(function (val2) {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(val2);
                }).join('&');
            }

            return encodeURIComponent(key) + '=' + encodeURIComponent(val);
        }).join('&') : '';
    };
})(jQuery);
