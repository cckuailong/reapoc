<?php

namespace ProfilePress\Core\Classes;

use ProfilePress\Core\Base;
use ProfilePress\Core\Themes\DragDrop\AbstractTheme;

class FormRepository
{
    const SHORTCODE_BUILDER_TYPE = 'shortcode';
    const DRAG_DROP_BUILDER_TYPE = 'dragdrop';

    const BUILD_FROM_SCRATCH_THEME = 'BuildScratch';

    const LOGIN_TYPE = 'login';
    const REGISTRATION_TYPE = 'registration';
    const PASSWORD_RESET_TYPE = 'password-reset';
    const EDIT_PROFILE_TYPE = 'edit-profile';
    const MELANGE_TYPE = 'melange';
    const USER_PROFILE_TYPE = 'user-profile';
    const MEMBERS_DIRECTORY_TYPE = 'member-directory';


    const FORM_CLASS = 'form_class';
    const FORM_STRUCTURE = 'form_structure';
    const FORM_CSS = 'form_css';
    const SUCCESS_MESSAGE = 'success_message';
    const PROCESSING_LABEL = 'processing_label';

    const MELANGE_REGISTRATION_SUCCESS_MESSAGE = 'melange_registration_success_msg';
    const MELANGE_PASSWORD_RESET_SUCCESS_MESSAGE = 'melange_password_reset_success_msg';
    const MELANGE_EDIT_PROFILE_SUCCESS_MESSAGE = 'melange_edit_profile_success_msg';

    const PASSWORDLESS_LOGIN = 'passwordless_login';
    const REGISTRATION_USER_ROLE = 'registration_user_role';
    const DISABLE_USERNAME_REQUIREMENT = 'disable_username_requirement';
    const PASSWORD_RESET_HANDLER = 'password_reset_handler';

    const FORM_BUILDER_FIELDS_SETTINGS = 'form_builder_fields_settings';
    const METABOX_FORM_BUILDER_SETTINGS = 'form_builder_settings';

    public static function wpdb()
    {
        global $wpdb;

        return $wpdb;
    }

    public static function get_forms($form_type = false)
    {
        $table = Base::form_db_table();
        $sql   = "SELECT * FROM $table";
        $args  = [];

        if ( ! empty($form_type)) {
            $sql    .= " WHERE form_type = %s";
            $args[] = $form_type;

            $sql = self::wpdb()->prepare($sql, $args);
        }

        return self::wpdb()->get_results($sql, 'ARRAY_A');
    }

    /**
     * Check if an form name already exist.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function name_exist($name)
    {
        $campaign_name = sanitize_text_field($name);
        $table         = Base::form_db_table();
        $result        = self::wpdb()->get_var(self::wpdb()->prepare("SELECT name FROM $table WHERE name = '%s'", $campaign_name));

        return ! empty($result);
    }

    public static function form_id_exist($id, $form_type)
    {
        $table  = Base::form_db_table();
        $result = self::wpdb()->get_var(self::wpdb()->prepare("SELECT form_id FROM $table WHERE form_id = %d AND form_type = '%s'", $id, $form_type));

        return ! empty($result);
    }

    public static function add_form_meta($form_id, $form_type, $key, $value)
    {
        $value = serialize($value);

        $result = self::wpdb()->insert(
            Base::form_meta_db_table(),
            [
                'form_id'    => $form_id,
                'form_type'  => $form_type,
                'meta_key'   => $key,
                'meta_value' => $value
            ],
            [
                '%d',
                '%s',
                '%s',
                '%s'
            ]
        );

        return $result !== false;
    }

    public static function update_form_meta($form_id, $form_type, $key, $value)
    {
        $check = self::get_form_meta($form_id, $form_type, $key, false);

        if (empty($check)) return self::add_form_meta($form_id, $form_type, $key, $value);

        $value = serialize($value);

        $result = self::wpdb()->update(
            Base::form_meta_db_table(),
            ['meta_value' => $value],
            [
                'form_id'   => $form_id,
                'form_type' => $form_type,
                'meta_key'  => $key,
            ],
            ['%s'],
            [
                '%d',
                '%s',
                '%s'
            ]
        );

        return $result !== false;
    }

    public static function delete_form_meta($form_id, $form_type, $key = '')
    {
        $where = [
            'form_id'   => $form_id,
            'form_type' => $form_type,
        ];

        $where_format = [
            '%d',
            '%s'
        ];

        if ( ! empty($key)) {
            $where['meta_key'] = $key;
            $where_format[]    = '%s';
        }

        $result = self::wpdb()->delete(Base::form_meta_db_table(), $where, $where_format);

        return $result !== false;
    }

    public static function get_processing_label($form_id, $form_type)
    {
        $processing_label = self::get_form_meta($form_id, $form_type, self::PROCESSING_LABEL);

        if ($processing_label === false || empty($processing_label)) {
            $processing_label = esc_html__('Processing', 'wp-user-avatar');
        }

        return $processing_label;
    }

    public static function get_form_meta($form_id, $form_type, $key, $single = true)
    {
        $table   = Base::form_meta_db_table();
        $form_id = absint($form_id);

        $result = self::wpdb()->get_results(
            self::wpdb()->prepare(
                "SELECT  meta_value FROM $table WHERE form_id = %d AND form_type = %s AND meta_key = %s",
                $form_id, $form_type, $key
            ),
            'ARRAY_A'
        );

        if (empty($result)) return $single == true ? false : [];

        $output = [];
        foreach ($result as $value) {
            $output[] = unserialize($value['meta_value']);
        }

        if ($single && is_array($output)) return $output[0];

        return $output;
    }

    public static function get_form_first_id($form_type)
    {
        $table  = Base::form_db_table();
        $result = self::wpdb()->get_var(
            self::wpdb()->prepare(
                "SELECT form_id FROM $table WHERE form_type = '%s' ORDER BY form_id ASC",
                $form_type
            )
        );

        return absint($result);
    }

    public static function get_form_last_id($form_type)
    {
        $table  = Base::form_db_table();
        $result = self::wpdb()->get_var(
            self::wpdb()->prepare(
                "SELECT form_id FROM $table WHERE form_type = '%s' ORDER BY form_id DESC",
                $form_type
            )
        );

        return absint($result);
    }

    /**
     * Add new form to database.
     *
     * @param string $name
     * @param string $form_type
     * @param string $form_theme_class
     * @param string $builder_type
     *
     * @return false|int
     */
    public static function add_form($name, $form_type, $form_theme_class, $builder_type = 'shortcode')
    {
        $form_id = self::get_form_last_id($form_type) + 1;

        self::wpdb()->insert(
            Base::form_db_table(),
            [
                'name'         => $name,
                'form_type'    => $form_type,
                'builder_type' => $builder_type,
                'date'         => date("Y-m-d H:i:s"),
                'form_id'      => $form_id
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            ]
        );

        if (is_int(self::wpdb()->insert_id) && self::wpdb()->insert_id > 0) {

            if ($builder_type == self::SHORTCODE_BUILDER_TYPE) {
                if (empty($form_theme_class)) {
                    $theme_instance = new FormShortcodeDefaults($form_type);
                } else {
                    $theme_instance = ShortcodeThemeFactory::make($form_type, $form_theme_class);

                    if ( ! $theme_instance) {
                        self::delete_form($form_id, $form_type);

                        return false;
                    };
                }

                $structure = str_replace('{{form_id}}', $form_id, $theme_instance->get_structure());
                $css       = str_replace('{{form_id}}', $form_id, $theme_instance->get_css());

                self::add_form_meta($form_id, $form_type, self::FORM_CLASS, $form_theme_class);

                self::add_form_meta($form_id, $form_type, self::FORM_STRUCTURE, $structure);
                self::add_form_meta($form_id, $form_type, self::FORM_CSS, $css);

                if (method_exists($theme_instance, self::PASSWORD_RESET_HANDLER)) {
                    self::add_form_meta($form_id, $form_type, self::PASSWORD_RESET_HANDLER, $theme_instance->password_reset_handler());
                }

                if (method_exists($theme_instance, self::SUCCESS_MESSAGE)) {
                    self::add_form_meta($form_id, $form_type, self::SUCCESS_MESSAGE, $theme_instance->success_message());
                }

                if (method_exists($theme_instance, 'edit_profile_success_message')) {
                    self::add_form_meta($form_id, $form_type, self::MELANGE_EDIT_PROFILE_SUCCESS_MESSAGE, $theme_instance->edit_profile_success_message());
                }

                if (method_exists($theme_instance, 'password_reset_success_message')) {
                    self::add_form_meta($form_id, $form_type, self::MELANGE_PASSWORD_RESET_SUCCESS_MESSAGE, $theme_instance->password_reset_success_message());
                }

                if (method_exists($theme_instance, 'registration_success_message')) {
                    self::add_form_meta($form_id, $form_type, self::MELANGE_REGISTRATION_SUCCESS_MESSAGE, $theme_instance->registration_success_message());
                }
            }

            if ($builder_type == self::DRAG_DROP_BUILDER_TYPE) {

                if (empty($form_theme_class)) {
                    $form_theme_class = self::BUILD_FROM_SCRATCH_THEME;
                }

                /**
                 * Let Init class below knows the form type we are creating
                 * @see \ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\Init
                 */
                $_GET['form-type'] = $form_type;

                self::add_form_meta($form_id, $form_type, self::FORM_CLASS, $form_theme_class);

                $theme_class = self::make_class($form_theme_class, $form_type);

                if (method_exists($theme_class, 'default_field_listing')) {

                    $field_listing = call_user_func([$theme_class, 'default_field_listing']);

                    if ( ! empty($field_listing)) {
                        self::add_form_meta($form_id, $form_type, self::FORM_BUILDER_FIELDS_SETTINGS, json_encode($field_listing));
                    }
                }

                $theme_instance = self::forge_class($form_id, $form_theme_class, $form_type);

                if (method_exists($theme_instance, 'default_metabox_settings')) {
                    $default_metabox_settings = call_user_func([$theme_instance, 'default_metabox_settings']);
                    if (is_array($default_metabox_settings) && ! empty($default_metabox_settings)) {
                        foreach ($default_metabox_settings as $key => $value) {
                            self::add_form_meta($form_id, $form_type, $key, $value);
                        }
                    }
                }
            }
        }

        return $form_id;
    }

    public static function clone_form($form_id, $form_type)
    {
        $old_form_id = $form_id;

        $name = self::get_name($old_form_id, $form_type) . ' - Copy';

        $builder_type = self::SHORTCODE_BUILDER_TYPE;
        if (self::is_drag_drop($old_form_id, $form_type)) {
            $builder_type = self::DRAG_DROP_BUILDER_TYPE;
        }

        $new_form_id = self::get_form_last_id($form_type) + 1;

        self::wpdb()->insert(
            Base::form_db_table(),
            [
                'name'         => $name,
                'form_type'    => $form_type,
                'builder_type' => $builder_type,
                'date'         => date("Y-m-d H:i:s"),
                'form_id'      => $new_form_id
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            ]
        );

        $table = Base::form_meta_db_table();
        $metas = self::wpdb()->get_results(
            self::wpdb()->prepare(
                "SELECT meta_key, meta_value FROM $table WHERE form_id = %d AND form_type = %s",
                $old_form_id,
                $form_type
            ),
            'ARRAY_A'
        );

        foreach ($metas as $meta) {
            self::add_form_meta($new_form_id, $form_type, $meta['meta_key'], unserialize($meta['meta_value']));
        }
    }

    public static function delete_form($form_id, $form_type)
    {
        $response = self::wpdb()->delete(
            Base::form_db_table(),
            ['form_id' => $form_id, 'form_type' => $form_type],
            ['%d', '%s']
        );

        if ($response) {
            self::delete_form_meta($form_id, $form_type);
        }
    }

    public static function update_form($form_id, $form_type, $name = '', $data = [])
    {
        $data = array_filter($data, function ($value) {
            return is_bool($value) || ! empty($value) || is_null($value);
        });

        if ( ! empty($name)) {
            self::wpdb()->update(
                Base::form_db_table(),
                ['name' => $name],
                ['form_id' => $form_id, 'form_type' => $form_type],
                ['%s'],
                ['%d', '%s']
            );
        }


        foreach ($data as $key => $value) {
            self::update_form_meta($form_id, $form_type, $key, $value);
        }

        return true;
    }

    /**
     * Form name.
     *
     * @param int $form_id
     * @param string $form_type
     *
     * @return string
     */
    public static function get_name($form_id, $form_type)
    {
        $table = Base::form_db_table();

        return self::wpdb()->get_var(
            self::wpdb()->prepare(
                "SELECT name FROM $table WHERE form_id = %d AND form_type = %s",
                $form_id,
                $form_type
            )
        );
    }

    public static function get_form_ids($form_type)
    {
        $table = Base::form_db_table();

        return self::wpdb()->get_col(
            self::wpdb()->prepare(
                "SELECT form_id FROM $table WHERE form_type = %s",
                $form_type
            )
        );
    }

    /**
     * Get form class
     *
     * @param int $form_id
     * @param string $form_type
     *
     * @return string
     */
    public static function get_form_class($form_id, $form_type)
    {
        return self::get_form_meta($form_id, $form_type, self::FORM_CLASS, true);
    }

    public static function is_login_passwordless($form_id)
    {
        $data = FormRepository::get_form_meta($form_id, FormRepository::LOGIN_TYPE, FormRepository::PASSWORDLESS_LOGIN, true);

        return $data === true;
    }

    public static function is_drag_drop($form_id, $form_type)
    {
        $table = Base::form_db_table();

        return self::wpdb()->get_var(
                self::wpdb()->prepare(
                    "SELECT builder_type FROM $table WHERE form_type = '%s' AND form_id = %d",
                    $form_type,
                    $form_id
                )
            ) == self::DRAG_DROP_BUILDER_TYPE;
    }

    /**
     * PHP namespaced class of the form theme.
     *
     * @param $form_theme_class
     * @param $form_type
     *
     * @return string
     */
    public static function make_class($form_theme_class, $form_type)
    {
        $form_type = str_replace('-', '', ucwords($form_type, '-'));

        return apply_filters(
            'ppress_register_dnd_form_class',
            sprintf('ProfilePress\Core\Themes\DragDrop\%s\%s', $form_type, $form_theme_class),
            $form_theme_class,
            $form_type
        );
    }

    /**
     * @param $form_id
     * @param $form_theme_class
     * @param $form_type
     *
     * @return AbstractTheme|bool
     */
    public static function forge_class($form_id, $form_theme_class, $form_type)
    {
        /** @var AbstractTheme $class */
        $class = self::make_class($form_theme_class, $form_type);

        if (class_exists($class)) {
            return $class::get_instance($form_id, $form_type);
        }

        return false;
    }

    public static function form_builder_fields_settings($form_id, $form_type)
    {
        $field_settings = self::get_form_meta($form_id, $form_type, self::FORM_BUILDER_FIELDS_SETTINGS);

        if ( ! empty($field_settings)) {
            return json_decode($field_settings, true);
        }

        return [];
    }

    public static function dnd_form_fields_json($form_id, $form_type, $defaults)
    {
        $field_settings = self::form_builder_fields_settings($form_id, $form_type);

        if ( ! empty($field_settings)) {

            foreach ($field_settings as $key => $field_setting) {

                if (isset($field_setting['fieldType'])) {
                    $field_type = $field_setting['fieldType'];
                    if (isset($defaults[$field_type])) {
                        $field_settings[$key] = wp_parse_args($field_setting, $defaults[$field_type]);
                    }
                }
            }
        }

        return json_encode($field_settings);
    }


    public static function dnd_class_instance($id, $form_type)
    {
        $form_class    = self::get_form_class($id, $form_type);
        $form_instance = self::forge_class($id, $form_class, $form_type);

        if ( ! $form_class || ! $form_instance) return false;

        return $form_instance;
    }
}