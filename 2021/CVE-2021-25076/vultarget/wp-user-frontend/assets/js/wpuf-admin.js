jQuery(function($) {

    // Collapsable email settings field
    group = [
        '.email-setting',
        '.guest-email-setting',
        '.reset-email-setting',
        '.confirmation-email-setting',
        '.subscription-setting',
        '.admin-new-user-email',
        '.pending-user-email',
        '.denied-user-email',
        '.approved-user-email',
        '.approved-post-email'
    ]
    group.forEach(function(header, index) {
        $(header).addClass("heading");
        $(header+"-option").addClass("hide");

        $("#wpuf_mails "+header).click(function() {
            $(header+"-option").toggleClass("hide");
        });
    })

    // Checked layout radio input field after clicking image
    $(".wpuf-form-layouts li").click(function() {
        $(this.children[0]).attr("checked", "checked");
        $(".wpuf-form-layouts li").removeClass('active');
        $(this).toggleClass('active');
    });

    // Clear schedule lock
    $('#wpuf_clear_schedule_lock').on('click', function(e) {
        e.preventDefault();
        var post_id = $(this).attr('data');

        $.ajax({
            url: wpuf_admin_script.ajaxurl,
            type: 'POST',
            data: {
                'action'    : 'wpuf_clear_schedule_lock',
                'nonce'     : wpuf_admin_script.nonce,
                'post_id'   : post_id
            },
            success:function(data) {
                swal({
                    type: 'success',
                    title: wpuf_admin_script.cleared_schedule_lock,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });
        $(this).closest("p").hide();
    });
});
