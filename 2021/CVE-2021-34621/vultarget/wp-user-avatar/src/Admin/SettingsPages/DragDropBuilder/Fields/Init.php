<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;

class Init
{
    public static function init()
    {
        $form_type = sanitize_text_field($_GET['form-type']);

        do_action('ppress_drag_drop_builder_field_init_before', $form_type);

        if (in_array($form_type, [FR::EDIT_PROFILE_TYPE, FR::REGISTRATION_TYPE])) {
            new Username();
            new Email();
            new ConfirmEmail();
            new Password();
            new ConfirmPassword();
            new PasswordStrengthMeter();
            new Website();
            new Nickname();
            new DisplayName();
            new FirstName();
            new LastName();
            new ProfilePicture();
            new CoverImage();
            new Bio();
            new TextBox();
            new Textarea();
            new SelectDropdown();
            new CheckboxList();
            new RadioButtons();
            new SingleCheckbox();
            new CFPassword();
            new Number();
            new Country();
            new Date();
            new DefinedFieldTypes\Agreeable();
            new DefinedFieldTypes\Checkbox();
            new DefinedFieldTypes\Input();
            new DefinedFieldTypes\Password();
            new DefinedFieldTypes\Date();
            new DefinedFieldTypes\Radio();
            new DefinedFieldTypes\Select();
        }

        if ($form_type == FR::EDIT_PROFILE_TYPE) {
            new EditProfile\ShowProfilePicture();
            new EditProfile\ShowCoverImage();
        }

        if ($form_type == FR::REGISTRATION_TYPE) {
            new SelectRole();
        }

        if ($form_type == FR::LOGIN_TYPE) {
            new Login\Userlogin();
            new Login\Password();
            new Login\RememberLogin();
        }

        if ($form_type == FR::PASSWORD_RESET_TYPE) {
            new PasswordReset\Userlogin();
        }

        if (in_array($form_type, [FR::USER_PROFILE_TYPE, FR::MEMBERS_DIRECTORY_TYPE])) {
            new UserProfile\Username();
            new UserProfile\Email();
            new UserProfile\FirstName();
            new UserProfile\LastName();
            new UserProfile\Website();
            new UserProfile\DisplayName();
            new UserProfile\Nickname();
            new UserProfile\Bio();

            if (EM::is_enabled(EM::CUSTOM_FIELDS)) {
                new UserProfile\CustomField();
            }
        }

        new HTML();

        do_action('ppress_drag_drop_builder_field_init_after', $form_type);
    }
}