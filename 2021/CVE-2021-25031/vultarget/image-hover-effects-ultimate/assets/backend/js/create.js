jQuery.noConflict();
(function ($) {
    var styleid = '';
    var childid = '';
    async function Image_Hover_Admin_Create(functionname, rawdata, styleid, childid, callback) {
        if (functionname === "") {
            alert('Confirm Function Name');
            return false;
        }
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
    $(".oxi-addons-addons-template-create").on("click", function (e) {
        e.preventDefault();
        $('#style-name').val('');
        $('#oxistyledata').val($(this).attr('effects-data'));
        $("#oxi-addons-style-create-modal").modal("show");
    });



    $(".oxi-addons-addons-web-template").on("click", function (e) {
        e.preventDefault();

        var functionname = "web_template";
        _This = $(this);
        _This.html('<i class="fas fa-spinner fa-pulse"></i>');
        Image_Hover_Admin_Create(functionname, _This.attr('data-effects'), _This.attr('data-value'), childid, function (callback) {
            $('#oxi-addons-style-web-template .modal-body').html(callback);
            $("#oxi-addons-style-web-template").modal("show");
            _This.html('Demos');
        });
        return false;
    });


    $(document.body).on("click", ".oxi-addons-addons-web-template-import-button", function (e) {
        e.preventDefault();
        _This = $(this);
        _This.html('<i class="fas fa-spinner fa-pulse"></i>');
        var functionname = "web_import";
        Image_Hover_Admin_Create(functionname, _This.attr('web-data'), _This.attr('web-template'), childid, function (callback) {
            setTimeout(function () {
                document.location.href = callback;
            }, 1000);
        });

    });


    $("#oxi-addons-style-modal-form").submit(function (e) {
        e.preventDefault();
        $a = $('#oxistyledata').val() + "-data-" + $("input[name='image-hover-box-layouts']:checked").val();

        var data = {
            name: $('#style-name').val(),
            style: $('#' + $a).val()
        };

        var rawdata = JSON.stringify(data);
        var functionname = "create_new";
        $('.modal-footer').prepend('<span class="spinner sa-spinner-open-left"></span>');
        Image_Hover_Admin_Create(functionname, rawdata, styleid, childid, function (callback) {
            setTimeout(function () {
                document.location.href = callback;
            }, 1000);
        });
    });

    $(".oxi-addons-addons-style-btn-warning").on("click", function (e) {
        e.preventDefault();
        var functionname = "shortcode_deactive";
        $This = $(this);
        $This.append('<span class="spinner sa-spinner-open"></span>');
        Image_Hover_Admin_Create(functionname, $This.attr('data-effects'), $This.attr('data-value'), childid, function (callback) {
            setTimeout(function () {
                if (callback === "done") {
                    $This.parents('.oxi-addons-col-1').remove();
                }
            }, 1000);
        });
        return false;
    });

    $(".oxi-addons-addons-style-btn-active").on("click", function (e) {
        e.preventDefault();
        var functionname = "shortcode_active";
        $This = $(this);
        $This.append('<span class="spinner sa-spinner-open"></span>');
        Image_Hover_Admin_Create(functionname, $This.attr('data-effects'), $This.attr('data-value'), childid, function (callback) {
            setTimeout(function () {
                document.location.href = callback;
            }, 1000);
        });
        return false;
    });

})(jQuery)