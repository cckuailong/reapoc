/**
 * Contains logic for deleting and adding roles.
 *
 * For deleting roles it makes a request to the server to delete the tag.
 * For adding roles it makes a request to the server to add the tag.
 *
 */

/* global ajaxurl, validateForm */

jQuery(document).ready(function ($) {

    /**
     * Adds an event handler to the delete role link on the role overview page.
     *
     * Cancels default event handling and event bubbling.
     *
     *
     * @returns boolean Always returns false to cancel the default event handling.
     */
    $('#the-list').on('click', '.delete-role', function () {

        if (confirm(pp_roles_i18n.confirm_delete)) {
            var t = $(this), tr = t.parents('tr'), r = true, data;

            data = t.attr('href').replace(/[^?]*\?/, '');

            /**
             * Makes a request to the server to delete the role that corresponds to the delete role button.
             *
             * @param {string} r The response from the server.
             *
             * @returns {void}
             */
            $.post(ajaxurl, data, function (r) {
                if (r) {
                    if (r.success === true) {
                        $('#ajax-response').empty();
                        tr.fadeOut('normal', function () {
                            tr.remove();
                        });
                    } else {
                        $('#ajax-response').empty().append(r.data);
                        tr.children().css('backgroundColor', '');
                    }
                }
            });

            tr.children().css('backgroundColor', '#f33');
        }

        return false;
    });

    /**
     * Adds an event handler to the form submit on the role overview page.
     *
     * Cancels default event handling and event bubbling.
     *
     *
     * @returns boolean Always returns false to cancel the default event handling.
     */
    $('#submit').click(function () {
        var form = $(this).parents('form');

        if (!validateForm(form))
            return false;

        /**
         * Does a request to the server to add a new role to the system
         *
         * @param {string} r The response from the server.
         *
         * @returns {void}
         */

        $.post(ajaxurl, $('#addrole').serialize(), function (r) {
            var res, parent, role, indent, i;

            $('#ajax-response').empty();
            res = typeof r !== 'undefined' ? r : null;
            if (res) {
                if (res.success === false) {
                    $('#ajax-response').append(res.data);

                } else if (res.success === true) {

                    $('.roles').prepend(res.data); // add to the table

                    $('.roles .no-items').remove();

                    $('input[type="text"]:visible, textarea:visible', form).val('');
                }
            }
        });

        return false;
    });

});
