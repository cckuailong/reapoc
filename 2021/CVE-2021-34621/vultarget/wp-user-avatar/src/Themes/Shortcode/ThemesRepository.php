<?php

namespace ProfilePress\Core\Themes\Shortcode;


use ProfilePress\Core\Classes\FormRepository;

class ThemesRepository
{
    private static $themes;

    public static function defaultThemes()
    {
        if (is_null(self::$themes)) {
            self::$themes = apply_filters('ppress_registered_shortcode_themes',
                array_merge(self::freeThemes(), self::premiumThemes())
            );
        }
    }

    public static function freeThemes()
    {
        return [
            [
                'name'        => 'Perfecto Lite',
                'theme_class' => 'PerfectoLite',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-login.png'
            ],
            [
                'name'        => 'Fobuk',
                'theme_class' => 'Fzbuk',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/fzbuk-login.png'
            ],

            [
                'name'        => 'Perfecto Lite',
                'theme_class' => 'PerfectoLite',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-registration.png'
            ],
            [
                'name'        => 'Fzbuk',
                'theme_class' => 'Fzbuk',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/fzbuk-registration.png'
            ],
            [
                'name'        => 'Boson',
                'theme_class' => 'Boson',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/boson-registration.png'
            ],

            [
                'name'        => 'Fzbuk',
                'theme_class' => 'Fzbuk',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/fzbuk-password-reset.png'
            ],
            [
                'name'        => 'Perfecto',
                'theme_class' => 'Perfecto',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-password-reset.png'
            ],

            [
                'name'        => 'Boson',
                'theme_class' => 'Boson',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/boson-edit-profile.png'
            ],
            [
                'name'        => 'Perfecto',
                'theme_class' => 'Perfecto',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-edit-profile.png'
            ],

            [
                'name'        => 'Dixon',
                'theme_class' => 'Dixon',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/dixon-profile.png'
            ],
            [
                'name'        => 'Daisy',
                'theme_class' => 'Daisy',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/daisy-profile.png'
            ],

            [
                'name'        => 'Lucid Tab Widget',
                'theme_class' => 'Lucid',
                'theme_type'  => FormRepository::MELANGE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/lucid-tab-widget.png'
            ]
        ];
    }

    public static function premiumThemes()
    {
        return [
            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-login.png'
            ],
            [
                'name'        => 'Perfecto Pro',
                'theme_class' => 'PerfectoPro',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectopro-login.png'
            ],
            [
                'name'        => 'Parallel',
                'theme_class' => 'Parallel',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/parallel-login.gif'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-login-registration.png'
            ],
            [
                'name'        => 'Bash',
                'theme_class' => 'Bash',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/bash-login.png'
            ],
            [
                'name'        => 'Jakhu',
                'theme_class' => 'Jakhu',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/jakhu-login.png'
            ],
            [
                'name'        => 'Sukan',
                'theme_class' => 'Sukan',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/sukan-login.png'
            ],

            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-registration.png'
            ],
            [
                'name'        => 'Perfecto Pro',
                'theme_class' => 'PerfectoPro',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectopro-registration.png'
            ],
            [
                'name'        => 'Lavender',
                'theme_class' => 'Lavender',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/lavender-registration.png'
            ],
            [
                'name'        => 'Parallel',
                'theme_class' => 'Parallel',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/parallel-registration.gif'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-login-registration.png'
            ],
            [
                'name'        => 'Bash',
                'theme_class' => 'Bash',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/bash-registration.png'
            ],
            [
                'name'        => 'Jakhu',
                'theme_class' => 'Jakhu',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/jakhu-registration.png'
            ],

            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-password-reset.png'
            ],
            [
                'name'        => 'Bash',
                'theme_class' => 'Bash',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/bash-password-reset.png'
            ],
            [
                'name'        => 'Parallel',
                'theme_class' => 'Parallel',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/parallel-password-reset.gif'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-password-reset.png'
            ],
            [
                'name'        => 'Jakhu',
                'theme_class' => 'Jakhu',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/jakhu-password-reset.png'
            ],

            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-edit-profile.png'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-edit-profile.png'
            ],

            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-profile.png'
            ],
            [
                'name'        => 'Monochrome',
                'theme_class' => 'Monochrome',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/monochrome-profile.png'
            ],

            [
                'name'        => 'Montserrat',
                'theme_class' => 'Montserrat',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::MELANGE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/montserrat-form.gif'
            ],
            [
                'name'        => 'Bash',
                'theme_class' => 'Bash',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::MELANGE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/bash-account-form.gif'
            ],
            [
                'name'        => 'Stride',
                'theme_class' => 'Stride',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::MELANGE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/stride.gif'
            ]
        ];
    }

    /**
     * All Optin themes available.
     *
     * @return mixed
     */
    public static function get_all()
    {
        self::defaultThemes();

        return self::$themes;
    }

    /**
     * Get form themes of a given type.
     *
     * @param string $form_type
     *
     * @return mixed
     */
    public static function get_by_type($form_type)
    {
        $all = self::get_all();

        return array_reduce($all, function ($carry, $item) use ($form_type) {

            // remove leading & trailing whitespace.
            $form_type_array = array_map('trim', explode(',', $item['theme_type']));

            if (in_array($form_type, $form_type_array)) {
                $carry[] = $item;
            }

            return $carry;
        });
    }

    /**
     * Get form theme by name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get_by_name($name)
    {
        $all = self::get_all();

        return array_reduce($all, function ($carry, $item) use ($name) {

            if ($item['name'] == $name) {
                $carry = $item;
            }

            return $carry;
        });
    }

    /**
     * Add form theme to theme repository.
     *
     * @param mixed $data
     *
     * @return void
     */
    public static function add($data)
    {
        self::defaultThemes();
        self::$themes[] = $data;
    }

    /**
     * Delete form theme from stack.
     *
     * @param mixed $theme_name
     *
     * @return void
     */
    public static function delete_by_name($theme_name)
    {
        self::defaultThemes();

        foreach (self::$themes as $index => $theme) {
            if ($theme['name'] == $theme_name) {
                unset(self::$themes[$index]);
            }
        }
    }
}