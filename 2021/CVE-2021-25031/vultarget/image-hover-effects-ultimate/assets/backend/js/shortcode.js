jQuery.noConflict();
(function ($) {
    var styleid = '';
    var childid = '';

    async function Oxi_Image_Admin_Shortcode(
            functionname,
            rawdata,
            styleid,
            childid,
            callback
            ) {
        if (functionname === '') {
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
                    rawdata: rawdata,
                },
            });
           
            return callback(result);
        } catch (error) {
            console.error(error);
        }
    }

    $('#oxi-import-style').on('click', function () {
        $('#oxi-addons-style-import-modal').modal('show');
    });

    $('.oxi-addons-style-clone').on('click', function (e) {
        e.preventDefault();
        $('#oxistyleid').val($(this).attr('datavalue'));
        $('#oxi-addons-style-clone-modal').modal('show');
    });


    $("#oxi-addons-style-clone-modal-form").submit(function (e) {
        e.preventDefault();
        var rawdata = $('#addons-style-name').val();
        var styleid = $('#oxistyleid').val();
        var functionname = "layouts_clone";
        $('.modal-footer').prepend('<span class="spinner sa-spinner-open-left"></span>');
        Oxi_Image_Admin_Shortcode(functionname, rawdata, styleid, childid, function (callback) {
            setTimeout(function () {
                document.location.href = callback;
            }, 1000);
        });
    });




    $('.oxi-addons-style-delete').on('click', function (e) {
        e.preventDefault();

        var status = confirm("Do you want to Delete this Shortcode? Before delete kindly confirm that you don't use or already replaced this Shortcode. If deleted will never Restored.");
        if (status == false) {
            return false;
        }
        var $This = $(this);
        var rawdata = 'deleting';
        var styleid = $This.val();
        var functionname = 'shortcode_delete';
        $(this).parents('td').append('<span class="spinner sa-spinner-open"></span>');
        Oxi_Image_Admin_Shortcode(
                functionname,
                rawdata,
                styleid,
                childid,
                function (callback) {
                 
                    setTimeout(function () {
                        if (callback === 'done') {
                            $This.parents('tr').remove();
                        }
                    }, 1000);
                }
        );
    });




    setTimeout(function () {
        if ($('.table').hasClass('oxi_addons_table_data')) {
            $('.oxi_addons_table_data').DataTable({
                aLengthMenu: [
                    [7, 25, 50, -1],
                    [7, 25, 50, 'All'],
                ],
                initComplete: function (settings, json) {
                    $('.oxi-addons-row.table-responsive')
                            .css('opacity', '1')
                            .animate(
                                    {
                                        height: $('.oxi-addons-row.table-responsive').get(0)
                                                .scrollHeight,
                                    },
                                    1000
                                    );
                },
            });
        }
    }, 500);

})(jQuery);
