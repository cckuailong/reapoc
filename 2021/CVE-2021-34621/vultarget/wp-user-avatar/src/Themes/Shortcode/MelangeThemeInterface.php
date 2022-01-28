<?php

namespace ProfilePress\Core\Themes\Shortcode;

interface MelangeThemeInterface extends ThemeInterface
{
    public function registration_success_message();

    public function password_reset_success_message();

    public function edit_profile_success_message();
}