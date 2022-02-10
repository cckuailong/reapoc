(function ($) {
    'use strict';

    $(document).on('click', '[data-slug="secure-copy-content-protection"] .deactivate a', function () {
        swal({
            html: "<h2>Do you want to upgrade to Pro version or permanently delete the plugin?</h2><ul><li>Upgrade: Your data will be saved for upgrade.</li><li>Deactivate: Your data will be deleted completely.</li></ul>",
            footer: '<a href="" class="ays-poll-temporary-deactivation">Temporary deactivation</a>',
            type: 'question',
            showCloseButton: true,
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Upgrade',
            cancelButtonText: 'Deactivate',
            confirmButtonClass: "ays-sccp-upgrade-button"
        }).then((result) => {

            if( result.dismiss && result.dismiss == 'close' ){
                return false;
            }

            var upgrade_plugin = false;
            if (result.value) upgrade_plugin = true;
            var data = {action: 'deactivate_sccp_option_sccp', upgrade_plugin: upgrade_plugin};
            $.ajax({
                url: sccp_admin_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function () {
                    window.location = $(document).find('[data-slug="secure-copy-content-protection"]').find('.deactivate').find('a').attr('href');
                }
            });
        });
        return false;
    });

    $(document).on('click', '.ays-poll-temporary-deactivation', function (e) {
        e.preventDefault();

        $(document).find('.ays-sccp-upgrade-button').trigger('click');

    });

})(jQuery);