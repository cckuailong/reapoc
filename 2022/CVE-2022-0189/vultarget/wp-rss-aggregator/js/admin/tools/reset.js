(function ($, Config) {
    $(document).ready(function () {
        // Double confirmation for the reset operations
        $('#wpra-delete-items-form, #wpra-reset-settings-form').on('submit', onResetSubmit);

        function onResetSubmit(e) {
            e.preventDefault();

            var confirmation = confirm(Config.message);
            if (confirmation == true) {
                // Unhook this function to prevent self-triggering infinite recursion
                $(this).off('submit', onResetSubmit);

                // Submit the form
                $(this).submit();

                // Re-attach this function
                $(this).on('submit', onResetSubmit);

                return false;
            }
        }
    });
})(jQuery, WpraResetTool);
