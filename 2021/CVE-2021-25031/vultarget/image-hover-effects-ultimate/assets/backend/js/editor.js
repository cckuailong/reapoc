jQuery.noConflict();
(function ($) {


    var urlParams = new URLSearchParams(window.location.search);
    var styleid = urlParams.get("styleid");
    var childid = "";
    var WRAPPER = $('#oxi-addons-preview-data').attr('template-wrapper');
    function NEWRegExp(par = '') {
        return new RegExp(par, "g");
    }

    function replaceStr(str, find, replace) {
        for (var i = 0; i < find.length; i++) {
            str = str.replace(new RegExp(find[i], 'gi'), replace[i]);
        }
        return str;
    }
    async function OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, callback) {
        if (functionname === "") {
            alert('Confirm Function Name');
            return false;
        }
        rawdata = rawdata.replace(/  /g, '&nbsp;');
        rawdata = rawdata.replace(/\\r/g, '');
        rawdata = rawdata.replace(/\r/g, '');
        rawdata = rawdata.replace(/\n/g, '<br>');
        rawdata = rawdata.replace(/\\n/g, '<br>');

        let result;
        try {
            result = await $.ajax({
                url: ImageHoverUltimate.root + 'ImageHoverUltimate/v1/' + functionname,
                method: 'POST',

                data: {
                    _wpnonce: ImageHoverUltimate.nonce,
                    styleid: styleid,
                    childid: childid,
                    rawdata: rawdata
                }
            });
         
            return callback(result);

        } catch (error) {
            console.error(error);
        }
    }


    function OxiAddonsPreviewDataLoader() {
        OxiAddonsTemplateSettings('elements_template_render_data', JSON.stringify($("#oxi-addons-form-submit").serializeJSON({checkboxUncheckedValue: "0"})), styleid, childid, function (callback) {
            $("#oxi-addons-preview-data").html(callback);
        });
    }

    function OxiAddonsModalConfirm(id, data) {
        if (($("#OXIAADDONSCHANGEDPOPUP").data("bs.modal") || {})._isShown) {
            setTimeout(function () {
                $("#OXIAADDONSCHANGEDPOPUP").modal("hide");
            }, 3000);
            $(id).html(data);
        }
    }

    $("#addonsstylenamechange").on("click", function (e) {
        e.preventDefault();
        var rawdata = JSON.stringify($("#shortcode-addons-name-change-submit").serializeJSON({checkboxUncheckedValue: "0"}));
        var functionname = "template_name";
        $(this).html('<span class="dashicons dashicons-admin-generic"></span>');
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
            if (callback === "success") {
                $("#OXIAADDONSCHANGEDPOPUP .icon-box").html('<span class="dashicons dashicons-yes"></span>');
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center h4").html("Superb!");
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center p").html("Shortcode name has been saved successfully.");
                $("#OXIAADDONSCHANGEDPOPUP").modal("show");
                OxiAddonsModalConfirm("#addonsstylenamechange", "Save");
            }
        });
    });
    $("#oxi-addons-modal-rearrange").sortable({
        axis: 'y',
        update: function (event, ui) {
            var list_sortable = $(this).sortable('toArray').toString();
            $('#oxi-addons-list-rearrange-data').val(list_sortable);
        }
    });
    $("#oxi-addons-rearrange-data-modal-open").on("click", function () {
        var rawdata = 'rearrange_modal_data';
        var functionname = "elements_rearrange_modal_data";
        $("#modal-rearrange-store-file").hide();
        $("#oxi-addons-list-rearrange-saving").show();
        $("#oxi-addons-modal-rearrange").hide();
        $("#oxi-addons-list-rearrange-modal").modal("show");
        $("#oxi-addons-modal-rearrange").html('');
        var d = $("#modal-rearrange-store-file").html();
        $("#oxi-addons-list-rearrange-data").val('');
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
            $.each($.parseJSON(callback), function (key, value) {
                data = d.replace(NEWRegExp("{{id}}"), key);
                $.each(value, function (k, v) {
                    data = data.replace(NEWRegExp('{{' + k + '}}'), v);
                });
                $("#oxi-addons-modal-rearrange").append(data);
                $("#oxi-addons-list-rearrange-saving").hide();
                $("#oxi-addons-modal-rearrange").show();
            });
        });
    });
    $("#oxi-addons-setting-rebuild").on("click", function (e) {
        e.preventDefault();
        $(this).html('Wait..');
        var rawdata = 'rebuild';
        var styleid = $(this).data('value');
        var functionname = "elements_template_rebuild_data";
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
        
            if (callback === "success") {
                setTimeout(function () {
                    location.reload();
                }, 1500);
            }
        });
    });
    $("#oxi-addons-list-rearrange-submit").on("click", function (e) {
        e.preventDefault();
        $(this).val('Savings..');
        var rawdata = $('#oxi-addons-list-rearrange-data').val();
        if (rawdata === '') {
            alert('Kindly Rearrange, Then  Click to saved');
            return false;
        }
        var functionname = "elements_template_rearrange_save_data";
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
         
            if (callback === "success") {
                $("#OXIAADDONSCHANGEDPOPUP .icon-box").html('<span class="dashicons dashicons-yes"></span>');
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center h4").html("Great!");
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center p").html("Rearrange Flipbox has been saved successfully.");
                $("#OXIAADDONSCHANGEDPOPUP").modal("show");
                OxiAddonsModalConfirm("#oxi-addons-list-rearrange-submit", "Save");
                $("#oxi-addons-list-rearrange-submit").val('Save');
                $("#oxi-addons-list-rearrange-modal").modal("hide");
                OxiAddonsPreviewDataLoader();
            }
        });
    });
    $("#oxi-addons-templates-submit").on("click", function (e) {
        e.preventDefault();
        $(".oxi-addons-minicolor").each(function (index, value) {
            var datavalue = $(this).attr("oxilabvalue");
            if (typeof datavalue !== typeof undefined && datavalue !== false) {
                $(this).val(datavalue);
            }
        });
        $(".oxi-addons-gradient-color").each(function (index, value) {
            var datavalue = $(this).attr("oxilabvalue");
            if (typeof datavalue !== typeof undefined && datavalue !== false) {
                $(this).val(datavalue);
            }
        });
        var rawdata = JSON.stringify($("#oxi-addons-form-submit").serializeJSON({checkboxUncheckedValue: "0"}));
        var functionname = "elements_template_style";
        $(this).html('<span class="dashicons dashicons-admin-generic"></span>');
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
           
            if (callback === "success") {
              
                $("#OXIAADDONSCHANGEDPOPUP .icon-box").html('<span class="dashicons dashicons-yes"></span>');
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center h4").html("Great!");
                $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center p").html("Layouts data has been saved successfully.");
                $("#OXIAADDONSCHANGEDPOPUP").modal("show");
                OxiAddonsModalConfirm("#oxi-addons-templates-submit", "Save");
                OxiAddonsPreviewDataLoader();
            }
        });
    });
    $("#oxi-template-modal-submit").on("click", function (e) {
        e.preventDefault();
        var rawdata = JSON.stringify($("#oxi-template-modal-form").serializeJSON({checkboxUncheckedValue: "0"}));
        var functionname = "elements_template_modal_data";
        var childid = $("#shortcodeitemid").val();
        $(this).html('<span class="dashicons dashicons-admin-generic"></span>');
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
            $("#oxi-addons-list-data-modal").modal("hide");
            $("#OXIAADDONSCHANGEDPOPUP .icon-box").html('<span class="dashicons dashicons-yes"></span>');
            $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center h4").html("Great!");
            $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center p").html("Data has been saved successfully.");
            $("#OXIAADDONSCHANGEDPOPUP").modal("show");
            OxiAddonsModalConfirm("#oxi-template-modal-submit", "Submit");
            OxiAddonsPreviewDataLoader();
        });
    });
    $("#shortcode-addons-style-change-submit-button").on("click", function (e) {
        e.preventDefault();
        var tr = $(this).attr('premium');
        if (tr !== 'ache') {
            alert("Sorry Template Changer Works with only Premium Version :( ")
            return false;
        }
        var status = confirm("Do you Want to Change Template? If you change Template sometimes style not work properlly and you need to customize it.");
        if (status === false) {
            return false;
        } else {
            var functionname = "template_change";
            var rawdata = $("#shortcode-current-style-name").val();
            var styleid = $("#shortcode-addons-style-change-submit-id").val();
            $(this).html('Wait');
            OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
                location.reload();
            });
        }
    });
    function ShortCodeMultipleSelector_Handler($value) {
        return $value.replace(/{{[0-9a-zA-Z.?:_-]+}}/g, function (match, contents, offset, input_string) {
            var m = match.replace(/{{/g, "").replace(/}}/g, "");
            var arr = m.split('.');
            if (m.indexOf("SIZE") != -1) {
                m = m.replace(/.SIZE/g, $("#" + arr[0] + '-size').val());
            }
            if (m.indexOf("UNIT") != -1) {
                m = m.replace(/.UNIT/g, $("#" + arr[0] + '-choices').val());
            }
            if (m.indexOf("VALUE") != -1) {
                m = m.replace(/.VALUE/g, $("#" + arr[0]).val());
            }
            m = m.replace(NEWRegExp(arr[0]), '');
            return m;
        });
    }
    $("body").on("click", ".shortcode-addons-template-item-edit", function (e) {
        e.preventDefault();
        $('#oxi-template-modal-form')[0].reset();
        var rawdata = "edit";
        var functionname = "elements_template_modal_data_edit";
        var childid = $(this).attr("value");
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
            if (callback === "Go to hell") {
                alert("Data Error");
            } else {
                $("#shortcode-addons-template-modal-form input[type='checkbox']").attr('checked', false);
                $.each($.parseJSON(callback), function (key, value) {
                    var tp = $('input[name="' + key + '"]').attr("type");
                    if (typeof tp !== 'undefined') {
                        if (tp === 'radio') {
                            $('input[name=' + key + ']').val([value]);
                        } else if (tp === 'checkbox') {
                            if (value != '0') {
                                $('input[name=' + key + ']').attr('checked', 'true');
                            } else {
                                $('input[name=' + key + ']').prop('checked', false).removeAttr('checked');
                            }
                        } else if (tp === 'hidden') {
                           
                            $('input[name=' + key + ']').val(value);
                            if ($('input[name=' + key + ']').hasClass('shortcode-addons-media-control-link')) {
                                $('#' + key).siblings('.shortcode-addons-media-control').removeClass('shortcode-addons-media-control-hidden-button');
                                $('input[name=' + key + ']').siblings('.shortcode-addons-media-control').children('.shortcode-addons-media-control-image-load').css({'background-image': 'url(' + value + ')'});
                            }
                        } else {
                            $("#" + key).val(value);
                        }
                    } else {
                        $("#" + key).val(value);
                    }
                });
                $("[data-condition]").each(function (index, value) {
                    $(this).addClass('shortcode-addons-form-conditionize');
                });
                $('.shortcode-addons-form-conditionize').conditionize();
                $('.shortcode-control-type-select .shortcode-addons-select-input').each(function (e) {
                    $id = $(this).attr('id');
                    $('#' + $id).select2({width: '100%'});
                });
                $("#oxi-template-modal-submit").html("Submit");
                $("#oxi-addons-list-data-modal").modal("show");
            }
        });
    });


    $("body").on("click", ".shortcode-addons-template-item-clone", function (e) {
        e.preventDefault();
        var rawdata = "delete";
        var functionname = "elements_template_modal_data_clone";
        var childid = $(this).attr("value");
        OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
            if (callback === "done") {
                OxiAddonsPreviewDataLoader();
            } else {
                alert("Data Error");
            }
        });

    });
    $("body").on("click", ".shortcode-addons-template-item-delete", function (e) {
        e.preventDefault();
        var rawdata = "delete";
        var functionname = "elements_template_modal_data_delete";
        var childid = $(this).attr("value");
        var status = confirm("Do you Want to Delete this?");
        if (status === false) {
            return false;
        } else {
            OxiAddonsTemplateSettings(functionname, rawdata, styleid, childid, function (callback) {
                if (callback === "done") {
                    $("#OXIAADDONSCHANGEDPOPUP .icon-box").html('<span class="dashicons dashicons-trash"></span>');
                    $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center h4").html("Deleted :(");
                    $("#OXIAADDONSCHANGEDPOPUP .modal-body.text-center p").html("Your data has been delete successfully.");
                    $("#OXIAADDONSCHANGEDPOPUP").modal("show");
                    OxiAddonsModalConfirm(".shortcode-addons-template-item-delete", "Delete")
                    OxiAddonsPreviewDataLoader();
                } else {
                    alert("Data Error")
                }
            });
        }
    });


    $(document.body).on("keyup", ".shortcode-control-type-text input", function (e) {
        $input = $(this);
        if ($input.attr("retundata") !== '') {
            e.preventDefault();
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (el, obj) {
                if (el.indexOf('{{KEY}}') != -1) {
                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                $(cls).html($input.val());
            });
        }
    });
    $(document.body).on("keyup", ".shortcode-control-type-textarea textarea", function (e) {
        $input = $(this);
        if ($input.attr("retundata") !== '') {
            e.preventDefault();
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (el, obj) {
                if (el.indexOf('{{KEY}}') != -1) {
                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                $(cls).html($input.val());
            });
        }
    });
    $(".shortcode-control-type-number input").on("keyup", function () {
        $input = $(this);
        if ($input.attr("retundata") !== '') {
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (el, obj) {
                if (el.indexOf('{{KEY}}') != -1) {
                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                var Cval = obj.replace(NEWRegExp("{{VALUE}}"), $input.val());
                if (Cval.indexOf("{{") != -1) {
                    Cval = ShortCodeMultipleSelector_Handler(Cval);
                }
                if ($input.attr('responsive') === 'tab') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else if ($input.attr('responsive') === 'mobile') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else {
                    $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                }
            });
            if ($input.val() === '') {
                OxiAddonsPreviewDataLoader();
            }
        }
    });
    $(".shortcode-control-type-select select").on("change", function (e) {
        $input = $(this);
        if ($input.attr("retundata") !== '') {
            id = $(this).attr('id');
            var arr = [];
            $("#" + id + " option").each(function () {
                arr.push($(this).val());
            });
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (key, obj) {
                if (key.indexOf('{{KEY}}') != -1) {
                    key = key.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                $.each(obj, function (k, o) {
                    var cls = key.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    if (o.type === 'CSS') {
                        var Cval = o.value.replace(NEWRegExp("{{VALUE}}"), $input.val());
                        if ($input.attr('responsive') === 'tab') {
                            $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                        } else if ($input.attr('responsive') === 'mobile') {
                            $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                        } else {
                            $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                        }
                    } else {
                        $.each(arr, function (i, v) {
                            $(cls).removeClass(v);
                        });
                        $(cls).addClass($input.val());
                    }

                });
            });
            if ($input.val() === '') {
                OxiAddonsPreviewDataLoader();
            }
        }

    });
    $(document.body).on("click", ".shortcode-control-type-choose input", function (e) {
        name = $(this).attr('name');
        $value = $(this).val();
        if ($(this).parent('.shortcode-form-choices').attr("retundata") !== '') {
            var arr = [];
            $("input[name=" + name + "]").each(function () {
                arr.push($(this).val());
            });
            $input = $(this).parent('.shortcode-form-choices');
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (key, obj) {
                if (key.indexOf('{{KEY}}') != -1) {
                    key = key.replace(NEWRegExp("{{KEY}}"), name.split('saarsa')[1]);
                }
                $.each(obj, function (k, o) {
                    var cls = key.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    if (o.type === 'CSS') {
                        var Cval = o.value.replace(NEWRegExp("{{VALUE}}"), $value);
                        if ($input.attr('responsive') === 'tab') {
                            $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                        } else if ($input.attr('responsive') === 'mobile') {
                            $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                        } else {
                            $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                        }
                    } else {
                        $.each(arr, function (i, v) {
                            $(cls).removeClass(v);
                        });
                        $(cls).addClass($value);
                    }
                });
            });
            if ($(this).val() === '') {
                OxiAddonsPreviewDataLoader();
            }
        }

    });
    $('.oxi-addons-minicolor').each(function () {
        $(this).minicolors({
            control: $(this).attr('data-control') || 'hue',
            defaultValue: $(this).attr('data-defaultValue') || '',
            format: $(this).attr('data-format') || 'hex',
            keywords: $(this).attr('data-keywords') || 'transparent' || 'initial' || 'inherit',
            inline: $(this).attr('data-inline') === 'true',
            letterCase: $(this).attr('data-letterCase') || 'lowercase',
            opacity: $(this).attr('data-opacity'),
            position: $(this).attr('data-position') || 'bottom left',
            swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
            change: function (value, opacity) {
                if (!value)
                    return;
                if (opacity)
                    value += ', ' + opacity;
                if (typeof console === 'object') {
                    // console.log(value);
                }
            },
            theme: 'bootstrap'
        });
    });
    $(".shortcode-control-type-color input").on("keyup, change", function () {
        $input = $(this);
        $custom = $input.attr("custom");
        if ($input.attr("retundata") !== '') {
            if ($custom === '') {
                var $data = JSON.parse($input.attr("retundata"));
                $.each($data, function (el, obj) {
                    if (el.indexOf('{{KEY}}') != -1) {
                        el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                    }
                    var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    var Cval = obj.replace(NEWRegExp("{{VALUE}}"), $input.val());
                    if ($input.attr('responsive') === 'tab') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else if ($input.attr('responsive') === 'mobile') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else {
                        $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                    }
                });
                if ($input.val() === '') {
                    $input.siblings('.minicolors-swatch').children('.minicolors-swatch-color').css('background-color', 'transparent');
                    OxiAddonsPreviewDataLoader();
                }
            } else {
                var $data = JSON.parse($input.attr("retundata"));
                $custom = $custom.split("|||||");
                $id = $custom[0];
                $VALUE = '';
                if ($custom[1] === 'text-shadow') {
                    $color = $('#' + $id + "-color").val();
                    $blur = $('#' + $id + "-blur-size").val();
                    $horizontal = $('#' + $id + "-horizontal-size").val();
                    $vertical = $('#' + $id + "-vertical-size").val();
                    $true = (($blur === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$horizontal || !$vertical) ? true : false;
                    if ($true === false) {
                        $VALUE = 'text-shadow: ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $color + ';';
                    } else {
                        OxiAddonsPreviewDataLoader();
                    }
                } else if ($custom[1] === 'box-shadow') {
                    $type = $('input[name="' + $id + '-type"]:checked').val();
                    $color = $('#' + $id + "-color").val();
                    $blur = $('#' + $id + "-blur-size").val();
                    $spread = $('#' + $id + "-spread-size").val();
                    $horizontal = $('#' + $id + "-horizontal-size").val();
                    $vertical = $('#' + $id + "-vertical-size").val();
                    $true = (($blur === '0' && $spread === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$spread || !$horizontal || !$vertical) ? true : false;
                    if ($true === false) {
                        $VALUE = 'box-shadow: ' + $type + ' ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $spread + 'px ' + $color + ';';
                    } else {
                        OxiAddonsPreviewDataLoader();
                    }
                }

                $.each($data, function (el, obj) {
                    if (el.indexOf('{{KEY}}') != -1) {
                        el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                    }
                    var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    var Cval = obj.replace(NEWRegExp("{{VALUE}}"), $VALUE);
                    if ($input.attr('responsive') === 'tab') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else if ($input.attr('responsive') === 'mobile') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else {
                        $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                    }
                });
            }
        }
    });
    $(".shortcode-control-type-font input").on("change", function () {
        $input = $(this);
        if ($(this).attr("retundata") !== '') {
            var font = $input.val().replace(/\+/g, ' ');
            font = font.split(':');
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (el, obj) {
                if (el.indexOf('{{KEY}}') != -1) {
                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                var Cval = obj.replace(NEWRegExp("{{VALUE}}"), font[0]);
                if ($input.attr('responsive') === 'tab') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else if ($input.attr('responsive') === 'mobile') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else {
                    $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                }
            });
        }
    });


    function ShortCodeFormSliderINT(ID = '') {
        $this = $('.shortcode-form-slider');
        if (ID !== '') {
            $this = ID.find('.shortcode-form-slider');
        }
        $this.each(function (key, value) {
            var $This = $(this);
            if (!$This.parents('.shortcode-addons-form-repeater-store').length) {

                var $input = $This.siblings('.shortcode-form-slider-input').children('input');
                var step = parseFloat($(this).siblings('.shortcode-form-slider-input').children('input').attr('step'));
                var min = parseFloat($(this).siblings('.shortcode-form-slider-input').children('input').attr('min'));
                var max = parseFloat($(this).siblings('.shortcode-form-slider-input').children('input').attr('max'));
                if (step % 1 == 0) {
                    decimals = 0;
                } else if (step % 0.1 == 0) {
                    decimals = 1;
                } else if (step % 0.01 == 0) {
                    decimals = 2;
                } else {
                    decimals = 3;
                }
                noUiSlider.create(value, {
                    animate: true,
                    start: $input.val(),
                    step: step,
                    connect: 'lower',
                    range: {
                        'min': min,
                        'max': max
                    },
                    format: wNumb({
                        decimals: decimals
                    })
                });
                value.noUiSlider.on('slide', function (v) {
                    $input.val(v);
                    $custom = $input.attr("custom");
                    if ($input.attr("retundata") !== '') {
                        if ($custom === '') {
                            var $data = JSON.parse($input.attr("retundata"));
                            $.each($data, function (el, obj) {
                                if (el.indexOf('{{KEY}}') != -1) {
                                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                                }
                                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                                var Cval = obj.replace(NEWRegExp("{{SIZE}}"), v);
                                Cval = Cval.replace(NEWRegExp("{{UNIT}}"), $input.attr('unit'));
                                if (Cval.indexOf("{{") != -1) {
                                    Cval = ShortCodeMultipleSelector_Handler(Cval);
                                }
                                if ($input.attr('responsive') === 'tab') {
                                    $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                                } else if ($input.attr('responsive') === 'mobile') {
                                    $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                                } else {
                                    $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                                }
                            });
                        } else {
                            var $data = JSON.parse($input.attr("retundata"));
                            $custom = $custom.split("|||||");
                            $id = $custom[0];
                            $VALUE = '';
                            if ($custom[1] === 'text-shadow') {
                                $color = $('#' + $id + "-color").val();
                                $blur = $('#' + $id + "-blur-size").val();
                                $horizontal = $('#' + $id + "-horizontal-size").val();
                                $vertical = $('#' + $id + "-vertical-size").val();
                                $true = (($blur === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$horizontal || !$vertical) ? true : false;
                                if ($true === false) {
                                    $VALUE = 'text-shadow: ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $color + ';';
                                } else {
                                    OxiAddonsPreviewDataLoader();
                                }
                            } else if ($custom[1] === 'box-shadow') {
                                $type = $('input[name="' + $id + '-type"]:checked').val();
                                $color = $('#' + $id + "-color").val();
                                $blur = $('#' + $id + "-blur-size").val();
                                $spread = $('#' + $id + "-spread-size").val();
                                $horizontal = $('#' + $id + "-horizontal-size").val();
                                $vertical = $('#' + $id + "-vertical-size").val();
                                $true = (($blur === '0' && $spread === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$spread || !$horizontal || !$vertical) ? true : false;
                                if ($true === false) {
                                    $VALUE = 'box-shadow: ' + $type + ' ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $spread + 'px ' + $color + ';';
                                } else {
                                    OxiAddonsPreviewDataLoader();
                                }
                            }
                            $.each($data, function (el, obj) {
                                if (el.indexOf('{{KEY}}') != -1) {
                                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                                }
                                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                                var Cval = obj.replace(NEWRegExp("{{VALUE}}"), $VALUE);
                                if (Cval.indexOf("{{") != -1) {
                                    Cval = ShortCodeMultipleSelector_Handler(Cval);
                                }
                                if ($input.attr('responsive') === 'tab') {
                                    $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                                } else if ($input.attr('responsive') === 'mobile') {
                                    $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                                } else {
                                    $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                                }
                            });
                        }
                    }
                });
            }
        });
    }
    ShortCodeFormSliderINT();
    $(".shortcode-form-slider-input input").on("keyup", function () {
        $input = $(this);
        $custom = $input.attr("custom");
        var html5Slider = $(this).parent().siblings('.shortcode-form-slider');
        html5Slider = html5Slider[0];
        var val = $(this).val();
        html5Slider.noUiSlider.set(val);
        if ($input.attr("retundata") !== '') {

            if ($custom === '') {
                var $data = JSON.parse($input.attr("retundata"));
                $.each($data, function (el, obj) {
                    if (el.indexOf('{{KEY}}') != -1) {
                        el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                    }
                    var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    var Cval = obj.replace(NEWRegExp("{{SIZE}}"), $input.val());
                    Cval = Cval.replace(NEWRegExp("{{UNIT}}"), $input.attr('unit'));
                    if (Cval.indexOf("{{") != -1) {
                        Cval = ShortCodeMultipleSelector_Handler(Cval);
                    }
                    if ($input.attr('responsive') === 'tab') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else if ($input.attr('responsive') === 'mobile') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else {
                        $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                    }
                });
            } else {
                var $data = JSON.parse($input.attr("retundata"));
                $custom = $custom.split("|||||");
                $id = $custom[0];
                $VALUE = '';
                if ($custom[1] === 'text-shadow') {
                    $color = $('#' + $id + "-color").val();
                    $blur = $('#' + $id + "-blur-size").val();
                    $horizontal = $('#' + $id + "-horizontal-size").val();
                    $vertical = $('#' + $id + "-vertical-size").val();
                    $true = (($blur === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$horizontal || !$vertical) ? true : false;
                    if ($true === false) {
                        $VALUE = 'text-shadow: ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $color + ';';
                    } else {
                        OxiAddonsPreviewDataLoader();
                    }
                } else if ($custom[1] === 'box-shadow') {
                    $type = $('input[name="' + $id + '-type"]:checked').val();
                    $color = $('#' + $id + "-color").val();
                    $blur = $('#' + $id + "-blur-size").val();
                    $spread = $('#' + $id + "-spread-size").val();
                    $horizontal = $('#' + $id + "-horizontal-size").val();
                    $vertical = $('#' + $id + "-vertical-size").val();
                    $true = (($blur === '0' && $spread === '0' && $horizontal === '0' && $vertical === '0') || !$blur || !$spread || !$horizontal || !$vertical) ? true : false;
                    if ($true === false) {
                        $VALUE = 'box-shadow: ' + $type + ' ' + $horizontal + 'px ' + $vertical + 'px ' + $blur + 'px ' + $spread + 'px ' + $color + ';';
                    } else {
                        OxiAddonsPreviewDataLoader();
                    }
                }
                $.each($data, function (el, obj) {
                    if (el.indexOf('{{KEY}}') != -1) {
                        el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                    }
                    var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    var Cval = obj.replace(NEWRegExp("{{VALUE}}"), $VALUE);
                    if (Cval.indexOf("{{") != -1) {
                        Cval = ShortCodeMultipleSelector_Handler(Cval);
                    }
                    if ($input.attr('responsive') === 'tab') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else if ($input.attr('responsive') === 'mobile') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else {
                        $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                    }
                });
            }
            if ($input.val() === '') {
                OxiAddonsPreviewDataLoader();
            }
        }



    });
    $(".shortcode-control-type-slider .shortcode-form-units-choices-label").click(function () {
        var id = "#" + $(this).attr('for');
        var input = $(this).parent().siblings('.shortcode-form-control-input-wrapper').children('.shortcode-form-slider-input').children('input');
        input.attr('min', $(id).attr('min'));
        input.attr('max', $(id).attr('max'));
        input.attr('step', $(id).attr('step'));
        input.attr('unit', $(id).val());
        var step = parseFloat($(id).attr('step'));
        var min = parseFloat($(id).attr('min'));
        var max = parseFloat($(id).attr('max'));
        var start = input.attr('default-value');
        if (step % 1 == 0) {
            decimals = 0;
        } else if (step % 0.1 == 0) {
            decimals = 1;
        } else if (step % 0.01 == 0) {
            decimals = 2;
        } else {
            decimals = 3;
        }

        var html5Slider = $(this).parent().siblings('.shortcode-form-control-input-wrapper').children('.shortcode-form-slider');
        html5Slider = html5Slider[0];
        html5Slider.noUiSlider.updateOptions({
            start: start,
            step: step,
            range: {
                'min': min,
                'max': max
            },
            format: wNumb({
                decimals: decimals
            })
        });
    });
    $(".shortcode-form-link-dimensions").click(function () {
        $(this).toggleClass("link-dimensions-unlink");
    });
    $(".shortcode-control-type-dimensions .shortcode-form-units-choices-label").click(function () {
        var id = "#" + $(this).attr('for');
        var input = $(this).parent().siblings('.shortcode-form-control-input-wrapper').children('.shortcode-form-control-dimensions').children('li').children('input');
        input.attr('min', $(id).attr('min'));
        input.attr('max', $(id).attr('max'));
        input.attr('step', $(id).attr('step'));

    });
    $(".shortcode-control-type-dimensions input").on("input", function () {
        $this = $(this);
        if ($this.parent().siblings('.shortcode-form-control-dimension').children('.shortcode-form-link-dimensions').hasClass('link-dimensions-unlink')) {
            if ($this.val() === '') {
                $this.val(0);
            }
            $siblings = $this.parent().siblings('.shortcode-form-control-dimension').children('input');
            $.each($siblings, function (e, o) {
                if ($(this).val() === '') {
                    $(this).val(0);
                }
            });
        } else {
            var value = $this.val();
            $this.parent().siblings('.shortcode-form-control-dimension').children('input').val(value);
        }
        $input = $this;
        $InputID = $input.attr('input-id');
        UNIT = $InputID + '-choices';
        TOP = $InputID + '-top';
        RIGHT = $InputID + '-right';
        BOTTOM = $InputID + '-bottom';
        LEFT = $InputID + '-left';
        if ($input.attr("retundata") !== '' && $input.attr('type') !== 'radio') {
         
            var $data = JSON.parse($input.attr("retundata"));
            $.each($data, function (el, obj) {
                if (el.indexOf('{{KEY}}') != -1) {
                    el = el.replace(NEWRegExp("{{KEY}}"), $input.attr('name').split('saarsa')[1]);
                }
                var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                var type = $("input[name=\"" + UNIT + "\"]").attr('type');
                if (type === 'hidden') {
                    Cval = obj.replace(NEWRegExp("{{UNIT}}"), $('#' + UNIT).val());
                } else {
                    var Cval = obj.replace(NEWRegExp("{{UNIT}}"), $("input[name=\"" + UNIT + "\"]:checked").val());
                }
                Cval = Cval.replace(NEWRegExp("{{TOP}}"), $('#' + TOP).val());
                Cval = Cval.replace(NEWRegExp("{{RIGHT}}"), $('#' + RIGHT).val());
                Cval = Cval.replace(NEWRegExp("{{BOTTOM}}"), $('#' + BOTTOM).val());
                Cval = Cval.replace(NEWRegExp("{{LEFT}}"), $('#' + LEFT).val());
                if ($input.attr('responsive') === 'tab') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else if ($input.attr('responsive') === 'mobile') {
                    $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                } else {
                    $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                }
            });
            if ($input.val() === '') {
                OxiAddonsPreviewDataLoader();
            }
        }

    });
    $(".oxi-addons-gradient-color").each(function (i, v) {
        $(this).coloringPick({
            "show_input": true,
            "theme": "dark",
            'destroy': false,
            change: function (val) {
                $data = [];
                var $This = $(this).children('input');
              
                var _VALUE = $This.val();
                $id = $This.attr('background');
                $imagecheck = $("#" + $id + "-img").is(":checked");
                $imagesource = $('input[name="' + $id + '-select"]:checked').val();
                $Image = ($imagecheck === true ? ($imagesource === 'media-library' ? $("#" + $id + "-image").val() : $("#" + $id + "-url").val()) : '');
                var wordcount = val.split(/\b[\s,\.-:;]*/).length;
                var limitWord = 23;
                if ($Image === '') {
                    if (wordcount < limitWord) {
                        $BACKGROUND = 'background: ' + _VALUE + ';';
                    } else {
                        $BACKGROUND = ' background:-ms-' + _VALUE + '; background:-webkit-' + _VALUE + '; background:-moz-' + _VALUE + '; background:-o-' + _VALUE + '; background:' + _VALUE + ';';
                    }
                } else {
                    if (wordcount < limitWord) {
                        $BACKGROUND = 'background:linear-gradient(0deg, ' + _VALUE + ' 0%, ' + _VALUE + ' 100%), url(\'' + $Image + '\') ' + $("#" + $id + "-repeat").val() + ' ' + $("#" + $id + "-position").val() + ';';
                    } else {
                        $BACKGROUND = 'background:' + _VALUE + ', url(\'' + $Image + '\' ) ' + $("#" + $id + "-repeat").val() + ' ' + $("#" + $id + "-position-lap").val() + ';';
                    }
                }
               
                var $data = ($This.attr("retundata") !== '' ? JSON.parse($This.attr("retundata")) : []);
                $.each($data, function (el, obj) {
                    if (el.indexOf('{{KEY}}') != -1) {
                        el = el.replace(NEWRegExp("{{KEY}}"), $This.attr('name').split('saarsa')[1]);
                    }
                    var cls = el.replace(NEWRegExp("{{WRAPPER}}"), WRAPPER);
                    Cval = $BACKGROUND;
                    if ($This.attr('responsive') === 'tab') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (min-width : 669px) and (max-width : 993px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else if ($This.attr('responsive') === 'mobile') {
                        $("#oxi-addons-preview-data").append('<style>@media only screen and (max-width : 668px){#oxi-addons-preview-data ' + cls + '{' + Cval + '}} < /style>');
                    } else {
                        $("#oxi-addons-preview-data").append('<style>#oxi-addons-preview-data ' + cls + '{' + Cval + '} < /style>');
                    }
                });
                if (_VALUE === '') {
                    OxiAddonsPreviewDataLoader();
                }
            },
        });
    });
    $('.oxi-admin-icon-selector').iconpicker();
    $('.shortcode-addons-form-conditionize').conditionize();
    $(document.body).on("change", ".shortcode-addons-control-loader  input", function () {
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("change", ".shortcode-addons-control-loader  select", function () {
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("keyup", ".shortcode-addons-control-loader input", function () {
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("keyup", ".shortcode-addons-control-loader textarea", function () {
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("keyup", ".shortcode-addons-control-loader wysiwyg", function () {
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("change", ".shortcode-control-type-icon input", function () {
        OxiAddonsPreviewDataLoader();
    });
    $("#oxi-addons-2-0-color").on("change", function (e) {
        $input = $(this).val();
        $("#oxi-addons-preview-data").css('background', $input);
        $("#image-hover-preview-color").val($input);
    });
    if ($('div').hasClass('shortcode-form-repeater-fields-wrapper')) {
        $(".shortcode-form-repeater-fields-wrapper").sortable({
            axis: 'y',
            handle: ".shortcode-form-repeater-controls-title"
        });
    }
    if ($('div').hasClass('shortcode-form-rearrange-fields-wrapper')) {
        $(".shortcode-form-rearrange-fields-wrapper").sortable({
            axis: 'y',
            handle: ".shortcode-form-repeater-controls-title",
            update: function (event, ui) {
                var list_sortable = jQuery(this).sortable('toArray').toString();
                jQuery(jQuery(this).attr('vlid')).val(list_sortable);
            }
        });
    }

    function RepeaterTitle() {
        $(".shortcode-form-repeater-controls-title").each(function (index, value) {
            $patent = $(this).parents('.shortcode-form-repeater-fields');
            $t = $patent.attr('tab-title');
            $value = $patent.find("input").filter('[id$=\'' + $t + '\']').val();
            $(this).html($value);
        });
    }
    $(document.body).on("click", ".shortcode-form-repeater-fields-wrapper .shortcode-form-repeater-controls-remove", function () {
        event.preventDefault();
        $(this).parents('.shortcode-form-repeater-fields').remove();
        $(this).parents('.shortcode-form-repeater-fields-wrapper').trigger('sortupdate');
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("click", ".shortcode-form-repeater-controls-title", function () {
        event.preventDefault();
        RepeaterTitle();
        $(this).parents('.shortcode-form-repeater-fields').toggleClass('shortcode-form-repeater-controls-open');
        OxiAddonsPreviewDataLoader();
    });
    function childRecursive(element, func) {
        func(element);
        var children = element.children();
        if (children.length > 0) {
            children.each(function () {
                childRecursive($(this), func);
            });
        }
    }
    function getNewAttr(str, newNum, REP) {
        return str.replace(REP, 'saarsarep' + newNum);
    }
    function setCloneAttr(element, value, REP) {

        if (element.attr('id') !== undefined) {
            element.attr('id', getNewAttr(element.attr('id'), value, REP));
        }
        if (element.attr('name') !== undefined) {
            element.attr('name', getNewAttr(element.attr('name'), value, REP));
        }
        if (element.attr('for') !== undefined) {
            element.attr('for', getNewAttr(element.attr('for'), value, REP));
        }
        if (element.attr('data-condition') !== undefined) {
            element.attr('data-condition', getNewAttr(element.attr('data-condition'), value, REP));
        }
    }
    $(document.body).on("click", ".shortcode-form-repeater-button", function () {
        event.preventDefault();
        $This = $(this);
        $inputVAL = $This.parent().siblings('.shortcode-control-type-hidden').children().find('input').val(parseInt($This.parent().siblings('.shortcode-control-type-hidden').children().find('input').val()) + 1).val();
        var REP = 'saarsarepidrep';
        var newItem = $('#repeater-' + $This.attr('parent-id') + '-initial-data').children().clone();
        childRecursive(newItem, function (e) {
            setCloneAttr(e, $inputVAL, REP);
        });
        newItem.appendTo($This.parent().siblings('.shortcode-form-repeater-fields-wrapper'));
        $Current = $This.parent().siblings('.shortcode-form-repeater-fields-wrapper').children(':last');
        OxiAddonsPreviewDataLoader();
    });
    $(document.body).on("click", ".shortcode-form-repeater-fields-wrapper .shortcode-form-repeater-controls-duplicate", function () {
        event.preventDefault();
        $patent = $(this).parents('.shortcode-form-repeater-fields');
        var REP = 'saarsa' + $patent.find('*').filter(':input').attr('name').split('saarsa')[1];
        $inputVAL = $patent.parent().siblings('.shortcode-control-type-hidden').children().find('input').val(parseInt($patent.parent().siblings('.shortcode-control-type-hidden').children().find('input').val()) + 1).val();
        var newItem = $patent.clone();
        childRecursive(newItem, function (e) {
            setCloneAttr(e, $inputVAL, REP);
        });
        newItem.insertAfter($patent);
        $(this).parents('.shortcode-form-repeater-fields-wrapper').trigger('sortupdate');
        OxiAddonsPreviewDataLoader();
    });
})(jQuery);