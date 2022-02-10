"use strict";
jQuery(function($) {

    /**
        CSS Loader
    **/
    $("#nmsf-page-loader").hide();
    $("#nmsf-page").show();


    /**
        Submit Form Data
    **/
    $("form#mainform, .nmsf-form-js").submit(function(e) {
        e.preventDefault();

        jQuery('.nmsf-wrapper').block({
            message: null,
            overlayCSS: {
                background: "#fff",
                opacity: .6,
            }
        });

        var data = $(this).serialize();

        $.post(ajaxurl, data, function(resp) {

            jQuery('.nmsf-wrapper').unblock();

            window.createNotification({
                closeOnClick: true,
                displayCloseButton: false,
                positionClass: 'nfc-bottom-right',
                showDuration: 2000,
                theme: resp.status
            })({
                title: '',
                message: resp.message
            });

        }, 'json');

    });


    /**
        WP Colorpicker
    **/
    $('.nmsf-wp-colorpicker').wpColorPicker();


    /**
        Active First Settings Panel 
    **/
    $('.nmsf-panels-content').each(function(index, item) {
        $(item).find('.nmsf-panel-handler:first').prop('checked', true);
    });


    /**
        Settings Panel Tab
    **/
    $(document).on("click", ".nmsf-tabs-content div", function() {
        var tab_id = $(this).attr('data-tab-id');

        if (!$(this).is("active")) {

            $(".nmsf-tabs-content div").removeClass("active");
            $(".nmsf-panels-content").removeClass("active");

            $(this).addClass("active");
            $(".nmsf-panels-area").find("div[data-panel-id=" + tab_id + "]").addClass("active");
        }
    });


    /**
        Migration Event
    **/
    $(document).on("click", ".nmsf-migrate-back-btn", function(e) {

        if (!confirm(nmsf_vars.migrate_back_msg)) {

            e.preventDefault();
            return false;
        }
        else {
            return true;
        }
    });


    /**
        Add Conditional Settings Fields
    **/
    var ruleset = $.deps.createRuleset();

    $('.nmsf-panel-conditional-field').each(function(index, elem) {
        var conditions = $(elem).attr('data-conditions');
        conditions = JSON.parse(conditions);
        var conditional_rule = ruleset;
        var $this = $(this);

        $.each(conditions, function(index, elements) {

            var element = elements[0];
            var operator = elements[1];
            var input_val = elements[2].join(',');

            conditional_rule = conditional_rule.createRule('[data-rule-id="' + element + '"]', operator, input_val);
            conditional_rule.include($this);
        });
    });

    ruleset.install({ log: false });


    /**
        ToolTip Init
    **/
    $('.nmsf-tooltip').ppom_tooltipster({
        interactive: true,
        theme: 'nmsf_tooltipster-punk',
        tooltipBorderColor: '#32334a',
        tooltipBGColor: '#32334a'
    });


    /**
        Video Popup Init
    **/
    $(".nmsf-ref-video-popup").videoPopup();


    /**
        Select2 Init
    **/
    $('.nmsf-multiselect-js').select2();
});
