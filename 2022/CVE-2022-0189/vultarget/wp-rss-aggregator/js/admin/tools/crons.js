(function ($, Config, undefined) {

    // The length of a day in seconds
    var DAY_IN_SECONDS = 24 * 60 * 60;

    // Update the table only when the tool is shown
    $(document).on('wpra/tools/on_loaded', function (event, tool) {
        if (tool === 'crons') {
            if (Store.isLoaded) {
                init();
            } else {
                $(document).ready(init);
            }
        }

        $(document).on('wpra/tools/on_switched_to_crons', init);
    });

    /**
     * Initializes the crons tool.
     */
    function init() {
        Loading.init();
        Pagination.init();
        Table.init();
        Timeline.init();
        Info.init();

        Store.init();
    }

    /**
     * The loading component.
     */
    var Loading = {
        element: null,
        wrapper: null,
        shown: false,
        progress: null,
        maxWidth: 100,
        init: function () {
            Loading.element = $('.wpra-crons-loading');
            Loading.wrapper = $('.wpra-crons-wrap');
            Loading.bar = Loading.element.find('.wpra-crons-loading-bar');

            Loading.hide().update();
        },
        update: function () {
            Loading.element.toggle(Loading.shown);
            Loading.wrapper.toggle(!Loading.shown);

            Loading.bar.css({
                width: (Loading.progress * Loading.maxWidth) + '%'
            });
        },
        setProgress: function (progress) {
            Loading.progress = progress;

            return Loading;
        },
        show: function () {
            Loading.shown = true;

            return Loading;
        },
        hide: function () {
            Loading.shown = false;
            Loading.progress = 0;

            return Loading;
        },
    };

    /**
     * The data store.
     */
    var Store = {
        feeds: [],
        groups: {},
        count: 0,
        isLoaded: false,
        page: 1,
        numPages: 1,

        init: function () {
            // Show the loading message with an empty progress bar
            Loading.setProgress(0).show().update();

            var currPage = 1;
            var loadNextPage = function () {
                // Update the loading
                Loading.setProgress(currPage / Store.numPages).update();

                // If reached the last page, hide the progress bar
                if (currPage >= Store.numPages) {
                    // Generate the groups
                    Store.groupFeeds();

                    // Update the components
                    setTimeout(function () {
                        Loading.hide().update();
                        Pagination.update();
                        Table.update();
                        Timeline.update();
                        Info.update();
                    }, 500);

                    return;
                }

                // Increment the page
                currPage++;

                // Fetch the page
                Store.fetchSources(currPage, loadNextPage);
            };

            // Load the first page
            Store.fetchSources(1, loadNextPage);
        },

        update: function (delegateUpate) {
            // Re-group the feeds
            Store.groupFeeds();

            // Update the table and timeline
            if (delegateUpate !== false) {
                Table.update();
                Timeline.update();
            }
        },

        fetchSources: function (page, callback) {
            page = (page === null || page === undefined) ? Store.page : page;

            $.ajax({
                url: Config.restUrl + 'wpra/v1/sources',
                method: 'GET',
                data: {
                    num: Config.perPage,
                    page: page
                },
                beforeSend: function (request) {
                    request.setRequestHeader("X-WP-NONCE", Config.restApiNonce);
                },
                success: function (response) {
                    if (response && response.items) {
                        Store.count = response.count;
                        Store.feeds = Store.feeds.concat(response.items);

                        if (!Store.isLoaded) {
                            Store.numPages = Math.ceil(Store.count / Store.feeds.length);
                        }

                        // Save the original cron information
                        Store.feeds = Store.feeds.map(function (feed) {
                            feed.original = {
                                active: feed.active,
                                update_time: feed.update_time,
                                update_interval: feed.update_interval,
                            };

                            return feed;
                        });

                        // Sort the feeds
                        Store.feeds = Store.feeds.sort(function (a, b) {
                            return Time.compare(Feed.getUpdateTime(a), Feed.getUpdateTime(b));
                        });

                        Store.isLoaded = true;

                        Store.update();
                    }

                    if (callback) {
                        callback();
                    }
                },
                error: function (response) {
                    console.error(response);
                },
            });
        },

        getIntervalName: function (interval) {
            return Config.schedules[interval]
                ? Config.schedules[interval]['display']
                : interval;
        },

        groupFeeds: function () {
            Store.groups = {};

            for (var i in Store.feeds) {
                var feed = Store.feeds[i];
                var time = Feed.getUpdateTime(feed),
                    timeStr = Time.format(time);

                if (!Store.groups[timeStr]) {
                    Store.groups[timeStr] = [];
                }

                Store.groups[timeStr].push(feed);

                // Get the interval time in seconds
                var interval = Config.schedules[Feed.getUpdateInterval(feed)]['interval'];
                var intervalObj = Time.fromSeconds(interval);

                // Add recurrences for feeds that fetch more than once a day
                if (interval < DAY_IN_SECONDS) {
                    var t = {
                        hours: time.hours,
                        minutes: time.minutes,
                    };

                    var numRepeats = Math.floor(DAY_IN_SECONDS / interval) - 1;
                    for (var i = 0; i < numRepeats; ++i) {
                        // Add the interval to the temporary time
                        t = Time.add(t, intervalObj);

                        // Convert to seconds, clamp to 24 hours and convert back to an object
                        // This lets us format it into a time string without having 24+ hours
                        var t2 = Time.fromSeconds(Time.toSeconds(t) % DAY_IN_SECONDS);
                        // Get the time string for the clamped time
                        var str = Time.format(t2);

                        // Add the recurrence to the groups
                        if (!Store.groups[str]) {
                            Store.groups[str] = [];
                        }
                        Store.groups[str].push(feed);
                    }
                }
            }

            var collapsed = {};
            for (var timeStr in Store.groups) {
                // Get the time object and string for the previous minute
                var group = Store.groups[timeStr],
                    time = Time.parse(timeStr),
                    prevTime = Time.add(time, {hours: 0, minutes: -1}),
                    prevTimeStr = Time.format(prevTime);

                // The key to use - either this group's time string or a time string for 1 minute less
                var key = Store.groups.hasOwnProperty(prevTimeStr)
                    ? prevTimeStr
                    : timeStr;

                // Create the array for the key if needed
                if (!Array.isArray(collapsed[key])) {
                    collapsed[key] = [];
                }

                // Add the group to the array for the key
                collapsed[key] = collapsed[key].concat(group);
            }

            Store.groups = Object.keys(collapsed).sort().reduce((acc, key) => (acc[key] = collapsed[key], acc), {});
        },
    };

    /**
     * Functions related to feed sources and their data.
     */
    var Feed = {
        getState: function (feed) {
            return feed.active ? 'active' : 'paused';
        },
        getUpdateInterval: function (feed) {
            return feed.update_interval;
        },
        getUpdateTime: function (feed) {
            return (feed.update_time)
                ? Time.parse(feed.update_time)
                : Time.parse(Config.globalTime);
        },
    };

    /**
     * The feed sources table.
     */
    var Table = {
        element: null,
        body: null,
        page: 1,
        numPerPage: Config.perPage,
        highlighted: null,
        init: function () {
            if (Table.element === null) {
                Table.element = $('#wpra-crons-tool-table');
                Table.body = Table.element.find('tbody');
            }
        },
        createRow: function (feed) {
            var id = feed.id,
                state = Feed.getState(feed),
                name = feed.name,
                interval = Feed.getUpdateInterval(feed),
                timeStr = Time.format(Feed.getUpdateTime(feed));

            var elRow = $('<tr></tr>').addClass('wpra-crons-feed-' + Feed.getState(feed));

            var idCol = $('<td></td>').appendTo(elRow).addClass('wpra-crons-feed-id-col').text('#' + id);
            var nameCol = $('<td></td>').appendTo(elRow).addClass('wpra-crons-feed-name-col').text(name);
            var intervalCol = $('<td></td>').appendTo(elRow).addClass('wpra-crons-interval-col');
            var timeCol = $('<td></td>').appendTo(elRow).addClass('wpra-crons-time-col');

            {
                // The interval selector
                var intervalSelect = $('<select>').appendTo(intervalCol);
                // The reset time button
                var resetIntervalBtn = $('<a>').appendTo(intervalCol)
                    .attr('href', 'javascript:void(0)')
                    .addClass('wpra-crons-reset-interval')
                    .text('Reset')
                    .toggle(feed.update_interval !== feed.original.update_interval);

                // Add the options to the interval selector
                for (var i in Config.schedules) {
                    var option = $('<option>')
                        .val(i)
                        .text(Config.schedules[i]['display'])
                        .prop('selected', i === interval);

                    intervalSelect.append(option);
                }

                // Event for when the selected interval changes
                intervalSelect.on('change', function () {
                    var newInterval = $(this).val();

                    // Show the reset button if the value is different from the original
                    resetIntervalBtn.toggle(newInterval !== feed.original.update_interval);

                    // Update the time in the store
                    feed.update_interval = newInterval;
                    Store.update(false);
                    Timeline.update();
                });

                // Event for when the reset interval button is clicked
                resetIntervalBtn.click(function () {
                    feed.update_interval = feed.original.update_interval;
                    Store.update();
                });
            }

            {
                // The time field
                var timeField = $('<input />').appendTo(timeCol).attr({type: 'time', value: timeStr});
                // The reset time button
                var resetTimeBtn = $('<a>').appendTo(timeCol)
                    .attr('href', 'javascript:void(0)')
                    .addClass('wpra-crons-reset-time')
                    .text('Reset')
                    .toggle(feed.update_time !== feed.original.update_time);


                // When the time field's value changes, update the store and timeline
                // (But not the table, otherwise the field will lose focus)
                timeField.on('change', function (e) {
                    var newTime = $(this).val();

                    // Show the reset button if the value is different from the original
                    resetTimeBtn.toggle(newTime !== feed.original.update_time);

                    // Update the time in the store
                    feed.update_time = newTime;
                    Store.update(false);
                    Timeline.update();
                });

                // Event for when the reset time button is clicked
                resetTimeBtn.click(function () {
                    feed.update_time = feed.original.update_time;
                    Store.update();
                });
            }

            elRow.on('hover', function (e) {
                if (e.type === "mouseenter") {
                    Table.body.find('.wpra-crons-highlighted-feed').removeClass('wpra-crons-highlighted-feed');

                    $(this).addClass('wpra-crons-highlighted-feed');
                    Table.highlighted = id;

                    Timeline.update();
                } else {
                    if (Table.highlighted === id) {
                        $(this).removeClass('wpra-crons-highlighted-feed');
                        Table.highlighted = null;

                        Timeline.update();
                    }
                }
            });

            return elRow;
        },
        update: function () {
            Table.body.empty();

            var pagedFeeds = Store.feeds.slice(
                Table.numPerPage * (Table.page - 1),
                Table.numPerPage * Table.page
            );

            for (var i in pagedFeeds) {
                Table.body.append(Table.createRow(pagedFeeds[i]));
            }
        },
    };

    /**
     * The pagination component.
     */
    var Pagination = {
        numFeeds: null,
        nextBtn: null,
        prevBtn: null,
        firstPageBtn: null,
        lastPageBtn: null,
        currPageSpan: null,
        numPagesSpan: null,
        // Initializes the pagination
        init: function () {
            Pagination.nextBtn = $('#wpra-crons-next-page');
            Pagination.prevBtn = $('#wpra-crons-prev-page');
            Pagination.firstPageBtn = $('#wpra-crons-first-page');
            Pagination.lastPageBtn = $('#wpra-crons-last-page');
            Pagination.currPageSpan = $('.wpra-crons-curr-page');
            Pagination.numPagesSpan = $('.wpra-crons-num-pages');
            Pagination.numFeeds = $('.wpra-crons-num-feeds');

            // Hide the feed counter until the component updates
            Pagination.numFeeds.parent().hide();

            Pagination.nextBtn.click(Pagination.nextPage);
            Pagination.prevBtn.click(Pagination.prevPage);
            Pagination.firstPageBtn.click(Pagination.firstPage);
            Pagination.lastPageBtn.click(Pagination.lastPage);
        },
        // Updates the pagination component
        update: function () {
            Pagination.currPageSpan.text(Table.page);
            Pagination.numPagesSpan.text(Store.numPages);

            Pagination.nextBtn.prop('disabled', Table.page >= Store.numPages);
            Pagination.prevBtn.prop('disabled', Table.page <= 1);

            Pagination.firstPageBtn.prop('disabled', Table.page <= 1);
            Pagination.lastPageBtn.prop('disabled', Table.page === Store.numPages);

            Pagination.numFeeds.text(Store.count);
            Pagination.numFeeds.parent().toggle(Store.count > 0);
        },
        // Switches to a specific page
        changePage: function (page) {
            Table.page = page;

            Table.update();
            Pagination.update();
        },
        // Switches to the next page
        nextPage: function () {
            Pagination.changePage(Math.min(Table.page + 1, Store.numPages));
        },
        // Switches to the previous page
        prevPage: function () {
            Pagination.changePage(Math.max(Table.page - 1, 1));
        },
        // Switches to the first page
        firstPage: function () {
            Pagination.changePage(1);
        },
        // Switches to the last page
        lastPage: function () {
            Pagination.changePage(Store.numPages);
        },
    };

    /**
     * The info component.
     */
    var Info = {
        elGlobalInterval: null,
        elGlobalTime: null,
        elDownloadTimeline: null,
        init: function () {
            Info.elGlobalInterval = $('.wpra-crons-global-interval');
            Info.elGlobalTime = $('.wpra-crons-global-time');
            Info.elDownloadTimeline = $('.wpra-crons-download-timeline');

            Info.elDownloadTimeline.click(function () {
                var imageUrl = Timeline.canvas.toDataURL();
                window.open(imageUrl, '_wpraTimelineDL');
                window.focus();
            });
        },
        update: function () {
            Info.elGlobalInterval.text(Util.getIntervalName(Config.globalInterval));
            Info.elGlobalTime.text(Config.globalTime);
        }
    };

    /*
     * The timeline diagram.
     */
    var Timeline = {
        element: null,
        canvas: null,
        minWidth: 1280,

        init: function () {
            Timeline.element = document.getElementById('wpra-crons-timeline');
            Timeline.canvas = document.getElementById('wpra-crons-timeline-canvas');

            Timeline.update();
            window.addEventListener('resize', Timeline.update, false);
        },

        update: function () {
            // Update the width of the canvas to match its parent (-2 for the border of the parent)
            Timeline.canvas.width = Math.max(Timeline.minWidth, Timeline.element.offsetWidth - 2);

            // Get canvas properties
            var canvas = Timeline.canvas,
                rWidth = canvas.width,
                rHeight = canvas.height,
                hPadding = 10,
                vPadding = 10,
                width = rWidth - (hPadding * 2),
                height = rHeight - (vPadding * 2),
                ctx = canvas.getContext("2d"),
                axisOffset = 10,
                textHeight = 30,
                textSpacing = 20,
                lineY = height - textSpacing - textHeight,
                lineColor = "#555",
                hourGuideColor = "#666",
                minsGuideColor = "#999",
                lineWidth = 2,
                evenTextColor = "#444",
                oddTextColor = "#888",
                bubbleColor = "#317596",
                bubbleWarningColor = "#b97d50",
                bubbleSeriousColor = "#9b3832",
                bubbleBlurColor = "#ccc",
                bubbleRadius = 12,
                bubbleTopOffset = 5,
                bubbleTop = (bubbleRadius * 2) + bubbleTopOffset;

            // Clear the canvas
            ctx.clearRect(0, 0, width, height);
            ctx.translate(hPadding, vPadding);

            // Draw the bottom line
            {
                ctx.save();
                ctx.beginPath();
                ctx.moveTo(0, lineY);
                ctx.lineTo(width, lineY);
                ctx.lineWidth = lineWidth;
                ctx.strokeStyle = lineColor;
                ctx.stroke();
                ctx.restore();
            }

            // Pad along the x-axis so that the numbers are not exactly at the edges
            ctx.translate(axisOffset, 0);
            var availWidth = width - (axisOffset * 2);

            // Draw the numbers and dotted lines
            {
                var hourWidth = availWidth / 24,
                    minFontSize = 12,
                    maxFontSize = 18,
                    fontSizeRatio = 0.011,
                    fontSize = Math.max(Math.min(availWidth * fontSizeRatio, maxFontSize), minFontSize);

                ctx.font = fontSize + "px sans-serif";
                ctx.textBaseline = "hanging";
                for (var hour = 0; hour <= 24; ++hour) {
                    var hourStr = (hour < 10) ? "0" + hour : hour,
                        text = hourStr + ":00",
                        even = (hour % 2 === 0),
                        x = hour * hourWidth,
                        y = height - textHeight - (textSpacing / 2),
                        tx = x,
                        ty = y + 3,
                        color = (even) ? evenTextColor : oddTextColor;

                    // Do not draw the hour text for 24:00 or later
                    if (hour < 24) {
                        ctx.save();
                        ctx.translate(tx, ty);
                        ctx.rotate(Math.PI / 5);
                        ctx.fillStyle = color;
                        ctx.textAlign = "left";
                        ctx.fillText(text, 0, 0);
                        ctx.restore();
                    }

                    // The hour guide lines
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                    ctx.lineTo(x, 0);
                    ctx.setLineDash([4, 4]);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = hourGuideColor;
                    ctx.stroke();
                    ctx.moveTo(0, 0);
                    ctx.restore();

                    // The half-hour guide lines
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x + (hourWidth / 2), y);
                    ctx.lineTo(x + (hourWidth / 2), 0);
                    ctx.setLineDash([2, 2]);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = minsGuideColor;
                    ctx.stroke();
                    ctx.moveTo(0, 0);
                    ctx.restore();
                }
            }

            // Draw the indicators
            {
                var minuteWidth = availWidth / (24 * 60),
                    fetchDuration = 5, // in minutes
                    fetchWidth = fetchDuration * minuteWidth;

                // The function for drawing a group
                var drawFn = function (group, timeStr, highlighted) {
                    var time = Time.parse(timeStr),
                        groupX = (time.hours * hourWidth) + (time.minutes / 60 * hourWidth),
                        count = group.length,
                        color = bubbleColor,
                        bgColor = "#fff",
                        textColor = color;

                    if (count > 10) {
                        textColor = color = bubbleSeriousColor;
                    } else if (count > 5) {
                        textColor = color = bubbleWarningColor;
                    }

                    // If highlighted is `true`, draw highlighted group
                    // If highlighted is `false`, draw blurred group
                    // If highlighted is anything else, draw normally
                    if (highlighted === true) {
                        bgColor = color;
                        textColor = "#fff";
                    } else if (highlighted === false) {
                        color = bubbleBlurColor;
                        textColor = bubbleBlurColor;
                    }

                    // Draw the indicator line
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(groupX, lineY);
                    ctx.lineTo(groupX, bubbleTop);
                    ctx.lineCap = "square";
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = color;
                    ctx.stroke();
                    ctx.restore();

                    // Draw the bubble
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(groupX, bubbleRadius + bubbleTopOffset, bubbleRadius, 0, 2 * Math.PI);
                    ctx.fillStyle = bgColor;
                    ctx.fill();
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = color;
                    ctx.stroke();
                    ctx.restore();

                    // Draw the feed count
                    ctx.save();
                    ctx.font = "12px sans-serif";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = textColor;
                    ctx.fillText(count, groupX, bubbleRadius + bubbleTopOffset + 1);
                    ctx.restore();
                };

                // Stores groups to be drawn later, for a higher "z index"
                var drawLater = {};

                // Draw the groups
                for (var timeStr in Store.groups) {
                    var group = Store.groups[timeStr];

                    // If no group is highlighted, draw normally
                    if (Table.highlighted === null) {
                        drawFn(group, timeStr);

                        continue;
                    }

                    // Check if the group contains the highlighted feed
                    var hasHighlightedFeed = group.find(function (feed) {
                        return feed.id === Table.highlighted;
                    });

                    // If so, draw it later
                    if (hasHighlightedFeed) {
                        drawLater[timeStr] = group;

                        continue;
                    }

                    // If not, draw it as blurred
                    drawFn(group, timeStr, false);
                }

                for (var timeStr in drawLater) {
                    drawFn(drawLater[timeStr], timeStr, true);
                }
            }

            ctx.translate(0, 0);
        },
    };


    /**
     * Time related functions.
     */
    var Time = {
        create: function (h, m) {
            return {
                hours: h,
                minutes: m
            };
        },
        format: function (time) {
            if (!time) {
                return "";
            }

            var hours = time.hours < 10 ? "0" + time.hours : time.hours;
            var minutes = time.minutes < 10 ? "0" + time.minutes : time.minutes;

            return hours + ":" + minutes;
        },
        parse: function (str) {
            var parts = str.split(':');
            var hours = parseInt(parts[0]);
            var mins = parseInt(parts[1]);

            return Time.create(hours, mins);
        },
        toSeconds: function (time) {
            return time.hours * 3600 + time.minutes * 60;
        },
        fromSeconds: function (seconds) {
            var hours = Math.floor(seconds / 3600);
            var minutes = (seconds - (hours * 3600)) / 60;

            return Time.create(hours, minutes);
        },
        add: function (time1, time2, clamp) {
            var newObj = {
                hours: time1.hours + time2.hours,
                minutes: time1.minutes + time2.minutes
            };

            // Add overflowing minutes to the hours
            newObj.hours += Math.floor(newObj.minutes / 60);
            // Clamp the minutes
            newObj.minutes = newObj.minutes % 60;

            // If clamping is on, clamp the hours to 24
            if (clamp !== false) {
                newObj.hours = newObj.hours % 24;
            }

            return newObj;
        },
        diff: function (time1, time2, clamp) {
            return Time.add(time1, {
                hours: -(time2.hours),
                minutes: -(time2.minutes),
            }, clamp);
        },
        compare(a, b) {
            var an = Time.toSeconds(a);
            var bn = Time.toSeconds(b);

            if (an === bn) {
                return 0;
            }

            return (an < bn) ? -1 : 1;
        },
    };

    /**
     * Utility functions.
     */
    var Util = {
        getIntervalName: function (interval) {
            return Config.schedules[interval]
                ? Config.schedules[interval]['display']
                : interval;
        },
    };

})(jQuery, WpraCronsTool);
