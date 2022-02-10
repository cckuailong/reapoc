jQuery(function ($) {
    // Check if the Role field is available. If not, abort.
    if (!$('.user-role-wrap select#role, #createuser select#role').length) {
        return;
    }

    var $field = $('.user-role-wrap select#role, #createuser select#role'),
        $newField = $field.clone();

    $newField.attr('name', 'pp_roles[]');
    $newField.attr('id', 'pp_roles');
    $field.after($newField);
    $field.hide();

    // Convert the roles field into multiselect
    $newField.prop('multiple', true);

    // $newField.attr('name', 'role[]');

    // Select additional roles
    $newField.find('option').each(function (i, option) {
        $option = $(option);

        $.each(ppCapabilitiesProfileData.selected_roles, function (i, role) {
            if ($option.val() === role) {
                $option.prop('selected', true);
            }
        });
    });

    $newField.chosen({
        'width': '25em'
    });
});
