<?php

namespace ProfilePress\Core\Classes;


use ProfilePress\Core\Themes\Shortcode\MelangeThemeInterface;
use ProfilePress\Core\Themes\Shortcode\LoginThemeInterface;
use ProfilePress\Core\Themes\Shortcode\PasswordResetThemeInterface;
use ProfilePress\Core\Themes\Shortcode\RegistrationThemeInterface;
use ProfilePress\Core\Themes\Shortcode\ThemeInterface;

class ShortcodeThemeFactory
{
    /**
     * @param string $form_type
     * @param string $form_class
     *
     * @return bool|ThemeInterface|PasswordResetThemeInterface|LoginThemeInterface|RegistrationThemeInterface|MelangeThemeInterface
     */
    public static function make($form_type, $form_class)
    {
        $form_type = ucwords(str_replace('-', '', $form_type));

        $class = apply_filters(
            'ppress_register_shortcode_form_class',
            sprintf("ProfilePress\\Core\\Themes\\Shortcode\\%s\\%s", $form_type, $form_class),
            $form_class,
            $form_type
        );

        if ( ! class_exists($class)) {
            return false;
        }

        return new $class;
    }
}