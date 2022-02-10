jQuery.noConflict();
(function ($) {
    var styleid = '';
    var childid = '';
    async function Oxi_Image_Admin_Home(functionname, rawdata, styleid, childid, callback) {
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



    $(".addons-pre-check").on("click", function (e) {
        var data = $(this).attr('sub-type');
        if (data === 'premium') {
            alert("Sorry Extension will Works with only Premium Version");
            return false;
        } else {
            return true;
        }

    });

})(jQuery)