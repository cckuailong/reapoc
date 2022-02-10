(function ($) {

    $(document).ready(function () {
        $('button#wpra-add-blacklist-btn').click(function () {
            $('div#wpra-add-blacklist-container').slideToggle(200);
        });

        var deleteForm = $('#wpra-delete-blacklist-form');

        $('a.wpra-delete-blacklist-link').click(function () {
            var link = $(this);
            var id = link.data('id');

            deleteForm.find('#wpra-delete-blacklist-id').val(id);
            deleteForm.submit();
        });
    })

})(jQuery);
