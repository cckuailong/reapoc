<?php

namespace ProfilePress\Core\Themes\Shortcode;


interface PasswordResetThemeInterface extends ThemeInterface
{
    public function success_message();

    public function password_reset_handler();
}