jQuery(document).ready(function($) {
    initGrid();
    function initGrid() {

        var grid = new Muuri('.cmplz-grid', {
            dragEnabled: true,
            dragStartPredicate: function(item, e) {
                return e.target.className === 'cmplz-grid-title';
            },
            dragSortHeuristics: {
                sortInterval: 50,
                minDragDistance: 10,
                minBounceBackAngle: 1
            },
            dragPlaceholder: {
                enabled: false,
                duration: 400,
                createElement: function (item) {
                    return item.getElement().cloneNode(true);
                }
            },
            dragReleaseDuration: 400,
            dragReleaseEasing: 'ease',
            layoutOnInit: true,
            // itemDraggingClass: 'muuri-item-dragging',
        })
        .on('move', function () {
            saveLayout(grid);
        });

        var layout = window.localStorage.getItem('cmplz_layout');
        if (layout) {
            loadLayout(grid, layout);
        } else {
            grid.layout(true);
        }

        // Must save the layout on first load, otherwise filtering the grid won't work on a new install.
        saveLayout(grid);
    }

    function serializeLayout(grid) {
        var itemIds = grid.getItems().map(function (item) {
            return item.getElement().getAttribute('data-id');
        });
        return JSON.stringify(itemIds);
    }

    function saveLayout(grid) {
        var layout = serializeLayout(grid);
        window.localStorage.setItem('cmplz_layout', layout);
    }

    function loadLayout(grid, serializedLayout) {

        var layout = JSON.parse(serializedLayout);
        var currentItems = grid.getItems();
        // // Add or remove the muuri-active class for each checkbox. Class is used in filtering.
        $('.cmplz-grid-item').each(function(){

            var toggle_id = $(this).data('id');
            if ( typeof toggle_id === 'undefined' ) return;

			//if the layout has less blocks then there actually are, we add it here. Otherwise it ends up floating over another block
			if (!layout.includes( toggle_id.toString() ) ) layout.push( toggle_id.toString() );

            if (localStorage.getItem("cmplz_toggle_data_id_"+toggle_id) === null) {
                window.localStorage.setItem('cmplz_toggle_data_id_'+toggle_id, 'checked');
            }

            //Add or remove the active class when the checkbox is checked/unchecked
            if (window.localStorage.getItem('cmplz_toggle_data_id_'+toggle_id) === 'checked') {
                $(this).addClass("muuri-active");
            } else {
                $(this).removeClass("muuri-active");
            }
        });

        var currentItemIds = currentItems.map(function (item) {
            return item.getElement().getAttribute('data-id')
        });
        var newItems = [];
        var itemId;
        var itemIndex;

        for (var i = 0; i < layout.length; i++) {
            itemId = layout[i];
            itemIndex = currentItemIds.indexOf(itemId);
            if (itemIndex > -1) {
                newItems.push(currentItems[itemIndex])
            }
        }

        try {
            // Sort and filter the grid
            grid.sort(newItems, {layout: 'instant'});
            grid.filter('.muuri-active');
			//run a render, to make sure all necessary resizes have been run, in case the scrollbars are visible for example, which would make the screen smaller.
            grid.render();
        }
        catch(err) {
            window.localStorage.removeItem('cmplz_layout');
        }
    }


    // Reload the grid when checkbox value changes
    $('.cmplz-grid-item').each(function(){
        var toggle_id = $(this).data('id');
        // Set defaults for localstorage checkboxes
        if (!window.localStorage.getItem('cmplz_toggle_data_id_'+toggle_id)) {
            window.localStorage.setItem('cmplz_toggle_data_id_'+toggle_id, 'checked');
        }


        $('#cmplz_toggle_data_id_'+toggle_id).change(function() {
            if (document.getElementById("cmplz_toggle_data_id_"+toggle_id).checked ) {
                window.localStorage.setItem('cmplz_toggle_data_id_'+toggle_id, 'checked');
            } else {
                window.localStorage.setItem('cmplz_toggle_data_id_'+toggle_id, 'unchecked');
            }
            initGrid();
        });
    });

    /**
     * Show/hide dashboard items
     */

    //Get the window hash for redirect to #settings after settings save
    var tab = window.location.hash.substr(1).replace('#top','');
    $('ul.tabs li').click(function () {
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $("#" + tab_id).addClass('current');
    });
    var href = $('.tab-'+tab).attr('href');
    if (typeof href !== 'undefined'){
        if (href.indexOf('#'+tab) !== -1 ) {
            $('.tab-'+tab)[0].click();
            window.location.href = href; //causes the browser to refresh and load the requested url
        }
    }


    /**
     * Checkboxes
     */

    // Get grid toggle checkbox values
    var cmplzFormValues = JSON.parse(localStorage.getItem('cmplzFormValues')) || {};
    var checkboxes = $("#cmplz-toggle-dashboard :checkbox");

    // Enable all checkboxes by default to show all grid items. Set localstorage val when set so it only runs once.
    if (localStorage.getItem("cmplzDashboardDefaultsSet") === null) {
        checkboxes.each(function () {
            cmplzFormValues[this.id] = 'checked';
        });
        localStorage.setItem("cmplzFormValues", JSON.stringify(cmplzFormValues));
        localStorage.setItem('cmplzDashboardDefaultsSet', 'set');
    }

    updateStorage();
    // Update storage checkbox value when checkbox value changes
    checkboxes.on("change", function(){
        updateStorage();
    });

    function updateStorage(){
        checkboxes.each(function(){
            cmplzFormValues[this.id] = this.checked;
        });
        localStorage.setItem("cmplzFormValues", JSON.stringify(cmplzFormValues));
    }

    // Get checkbox values on pageload
    $.each(cmplzFormValues, function(key, value) {
        $("#" + key).prop('checked', value);
    });

    // Hide screen options by default
    $("#cmplz-toggle-dashboard").hide();

    // Show/hide screen options on toggle click
    $('#cmplz-show-toggles').click(function(){
        if ($("#cmplz-toggle-dashboard").is(":visible") ){
            $("#cmplz-toggle-dashboard").slideUp();
            $("#cmplz-toggle-arrows").attr('class', 'dashicons dashicons-arrow-down-alt2');
        } else {
            $("#cmplz-toggle-dashboard").slideDown();
            $("#cmplz-toggle-arrows").attr('class', 'dashicons dashicons-arrow-up-alt2');
        }
    });
});
