<?php

namespace ProfilePress\Core\Themes\DragDrop;


use ProfilePress\Core\Classes\FormRepository;

class ThemesRepository
{
    private static $themes;

    public static function defaultThemes()
    {
        if (is_null(self::$themes)) {
            self::$themes = apply_filters('ppress_registered_dragdrop_themes',
                array_merge(self::freeThemes(), self::premiumThemes())
            );
        }
    }

    public static function freeThemes()
    {
        return [
            [
                'name'        => 'Tulip',
                'theme_class' => 'Tulip',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/tulip-registration.png'
            ],
            [
                'name'        => 'Perfecto Lite',
                'theme_class' => 'PerfectoLite',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-registration.png'
            ],

            [
                'name'        => 'Tulip',
                'theme_class' => 'Tulip',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/tulip-edit-profile.png'
            ],

            [
                'name'        => 'Tulip',
                'theme_class' => 'Tulip',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/tulip-login.png'
            ],
            [
                'name'        => 'Perfecto Lite',
                'theme_class' => 'PerfectoLite',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-login.png'
            ],


            [
                'name'        => 'Tulip',
                'theme_class' => 'Tulip',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/tulip-password-reset.png'
            ],

            [
                'name'        => 'Default',
                'theme_class' => 'DefaultTemplate',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/default-user-profile.png'
            ],
            [
                'name'        => 'Dixon',
                'theme_class' => 'Dixon',
                'theme_type'  => FormRepository::USER_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/dixon-profile.png'
            ],

            [
                'name'        => 'Default',
                'theme_class' => 'DefaultTemplate',
                'theme_type'  => FormRepository::MEMBERS_DIRECTORY_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/default-member-directory.png'
            ],
            [
                'name'        => 'Gerbera',
                'theme_class' => 'Gerbera',
                'theme_type'  => FormRepository::MEMBERS_DIRECTORY_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/gerbera-member-directory.png'
            ],
        ];
    }

    public static function premiumThemes()
    {
        return [
            [
                'name'        => 'Daffodil',
                'theme_class' => 'Daffodil',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/daffodil-registration.png'
            ],
            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-registration.png'
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
                'name'        => 'Perfecto Pro',
                'theme_class' => 'PerfectoPro',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::REGISTRATION_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectopro-registration.png'
            ],

            [
                'name'        => 'Daffodil',
                'theme_class' => 'Daffodil',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/daffodil-edit-profile.png'
            ],
            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-edit-profile.png'
            ],
            [
                'name'        => 'Perfecto',
                'theme_class' => 'Perfecto',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-edit-profile.png'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::EDIT_PROFILE_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-edit-profile.png'
            ],

            [
                'name'        => 'Daffodil',
                'theme_class' => 'Daffodil',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/daffodil-login.png'
            ],
            [
                'name'        => 'Bash',
                'theme_class' => 'Bash',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/bash-login.png'
            ],
            [
                'name'        => 'Perfecto Pro',
                'theme_class' => 'PerfectoPro',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectopro-login.png'
            ],
            [
                'name'        => 'Smiley',
                'theme_class' => 'Smiley',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/smiley-login.png'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::LOGIN_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-login-registration.png'
            ],

            [
                'name'        => 'Daffodil',
                'theme_class' => 'Daffodil',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/daffodil-password-reset.png'
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
                'name'        => 'Perfecto',
                'theme_class' => 'Perfecto',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/perfectolite-password-reset.png'
            ],
            [
                'name'        => 'Pinnacle',
                'theme_class' => 'Pinnacle',
                'flag'        => 'premium',
                'theme_type'  => FormRepository::PASSWORD_RESET_TYPE,
                'screenshot'  => PPRESS_ASSETS_URL . '/images/themes/pinnacle-password-reset.png'
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
        }, []);
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