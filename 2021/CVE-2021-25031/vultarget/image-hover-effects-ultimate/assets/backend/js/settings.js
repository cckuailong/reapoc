jQuery.noConflict();
(function ($) {
    $(document).ready(function () {
        var styleid = '';
        var childid = '';

        async function Oxi_Image_Admin_Settings(functionname, rawdata, styleid, childid, callback) {
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
               
                setTimeout(function () {
                    return callback(result);
                }, 2000);
            } catch (error) {
                console.error(error);
            }
        }
        function delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }


        $("input[name=image_hover_ultimate_license_key] ").on("keyup", delay(function (e) {
            var $This = $(this), $value = $This.val();
            if ($value !== $.trim($value)) {
                $value = $.trim($value);
                $This.val($.trim($value));
            }
            var rawdata = JSON.stringify({license: $value});
            var functionname = "oxi_license";
            $('.image_hover_ultimate_license_massage').html('<span class="spinner sa-spinner-open"></span>');
            Oxi_Image_Admin_Settings(functionname, rawdata, styleid, childid, function (callback) {
                $('.image_hover_ultimate_license_massage').html(callback.massage);
                $('.image_hover_ultimate_license_text .oxi-addons-settings-massage').html(callback.text);
            });
        }, 1000));


        $(document.body).on("click", "input", function (e) {
            var $This = $(this), name = $This.attr('name'), $value = $This.val();
            if (name === 'oxi_addons_custom_parent_class') {
                return;
            }
            var rawdata = JSON.stringify({name: name, value: $value});
            var functionname = "oxi_settings";
            $('.' + name).html('<span class="spinner sa-spinner-open"></span>');
            Oxi_Image_Admin_Settings(functionname, rawdata, styleid, childid, function (callback) {
                $('.' + name).html(callback);
                setTimeout(function () {
                    $('.' + name).html('');
                }, 8000);
            });
        });
        $(document.body).on("change", "select", function (e) {
            var $This = $(this), name = $This.attr('name'), $value = $This.val();
            var rawdata = JSON.stringify({name: name, value: $value});
            var functionname = "oxi_settings";
            $('.' + name).html('<span class="spinner sa-spinner-open"></span>');
            Oxi_Image_Admin_Settings(functionname, rawdata, styleid, childid, function (callback) {
                $('.' + name).html(callback);
                setTimeout(function () {
                    $('.' + name).html('');
                }, 8000);
            });
        });

        $("input[name=oxi_addons_custom_parent_class] ").on("keyup", delay(function (e) {
            var $This = $(this), name = $This.attr('name'), $value = $This.val();
            var rawdata = JSON.stringify({name: name, value: $value});
            var functionname = "oxi_settings";
            $('.' + name).html('<span class="spinner sa-spinner-open"></span>');
            Oxi_Image_Admin_Settings(functionname, rawdata, styleid, childid, function (callback) {
                $('.' + name).html(callback);
                setTimeout(function () {
                    $('.' + name).html('');
                }, 8000);
            });
        }, 1500));
    });
})(jQuery)


