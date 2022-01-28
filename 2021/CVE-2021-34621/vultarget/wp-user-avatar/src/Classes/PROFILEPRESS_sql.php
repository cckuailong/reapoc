<?php

namespace ProfilePress\Core\Classes;

use ProfilePress\Core\Base;

class PROFILEPRESS_sql
{
    /** @param $meta_key
     * @param $meta_value
     *
     * @return bool|int
     */
    public static function add_meta_data($meta_key, $meta_value)
    {
        global $wpdb;

        $insert = $wpdb->insert(
            Base::meta_data_db_table(),
            [
                'meta_key'   => $meta_key,
                'meta_value' => serialize($meta_value),
            ],
            [
                '%s',
                '%s',
            ]
        );

        return ! $insert ? false : $wpdb->insert_id;
    }

    /**
     * @param $meta_id
     * @param $meta_key
     * @param $meta_value
     *
     * @return false|int
     */
    public static function update_meta_value($meta_id, $meta_key, $meta_value)
    {
        global $wpdb;

        return $wpdb->update(
            Base::meta_data_db_table(),
            ['meta_value' => serialize($meta_value)],
            ['id' => $meta_id, 'meta_key' => $meta_key],
            ['%s'],
            ['%d', '%s']
        );
    }

    /**
     * @param $meta_id
     * @param $meta_key
     *
     * @return bool|mixed
     */
    public static function get_meta_value($meta_id, $meta_key)
    {
        global $wpdb;

        $table = Base::meta_data_db_table();

        $sql = "SELECT meta_value FROM $table WHERE id= %d AND meta_key = %s";

        // get the profile fields row for the id and save as array
        $result = $wpdb->get_var($wpdb->prepare($sql, $meta_id, $meta_key));

        return ! is_null($result) ? unserialize($result) : false;
    }

    public static function get_meta_data_by_key($meta_key)
    {
        global $wpdb;

        $table = Base::meta_data_db_table();

        $sql = "SELECT * FROM $table WHERE meta_key = %s";

        $result = $wpdb->get_results($wpdb->prepare($sql, $meta_key), 'ARRAY_A');

        if (empty($result)) return false;

        $output = [];
        foreach ($result as $key => $meta) {
            $output[$key] = array_reduce(array_keys($meta), function ($carry, $item) use ($meta) {
                $carry[$item] = ($item == 'meta_value') ? unserialize($meta[$item]) : $meta[$item];

                return $carry;
            });
        }

        return $output;
    }

    public static function delete_meta_data($meta_id)
    {
        global $wpdb;

        $result = $wpdb->delete(Base::meta_data_db_table(), ['id' => $meta_id], ['%d']);

        return $result !== false;
    }

    public static function delete_meta_data_by_flag($flag)
    {
        global $wpdb;

        $result = $wpdb->delete(Base::meta_data_db_table(), ['flag' => $flag], ['%s']);

        return $result !== false;
    }

    /**
     * Query for profile placement if user can view the his profile
     *
     * @return mixed
     */
    public static function get_profile_custom_fields()
    {
        static $cache = false;

        if (false === $cache) {

            global $wpdb;

            $cache = $wpdb->get_results(
                sprintf("SELECT * FROM %s ORDER BY id", Base::profile_fields_db_table()),
                'ARRAY_A'
            );
        }

        return $cache;
    }

    /**
     * Retrieve the profile field row of an ID
     *
     * @param int $id
     *
     * @return array
     */
    public static function get_profile_custom_field_by_id($id)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id),
            'ARRAY_A'
        );
    }

    /**
     * Retrieve the profile custom field by field key
     *
     * @param $field_key
     *
     * @return array
     */
    public static function get_profile_custom_field_by_key($field_key)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE field_key = %s", $field_key),
            'ARRAY_A'
        );
    }

    public static function get_profile_custom_fields_by_types($types)
    {
        global $wpdb;

        $sql = sprintf("SELECT * FROM %s", Base::profile_fields_db_table());

        $sql .= " WHERE type IN(" . implode(', ', array_fill(0, count($types), '%s')) . ")";

        $sql = call_user_func_array([$wpdb, 'prepare'], array_merge([$sql], $types));

        return $wpdb->get_results($sql);
    }

    public static function delete_profile_custom_field($id)
    {
        global $wpdb;

        $delete_sql = $wpdb->delete(
            Base::profile_fields_db_table(),
            ['id' => $id],
            ['%d']
        );

        return $delete_sql;
    }

    /**
     * Return a list of created custom profile IDs.
     *
     * @return array
     */
    public static function get_profile_field_ids()
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_col("SELECT id FROM $table ORDER BY id");
    }

    /**
     * Check if a profile field's key exist in the database.
     *
     * @param int $field_key
     *
     * @return bool
     */
    public static function is_profile_field_key_exist($field_key)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        $response = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM $table WHERE field_key = %s", $field_key)
        );

        return ! is_null($response);
    }

    /**
     * Add custom field to DB
     *
     * @param string $label_name
     * @param string $key
     * @param string $description
     * @param string $type
     * @param string $options
     *
     * @return bool|int
     */
    public static function add_profile_field($label_name, $key, $description, $type, $options)
    {
        global $wpdb;

        $insert = $wpdb->insert(
            Base::profile_fields_db_table(),
            array(
                'label_name'  => $label_name,
                'field_key'   => $key,
                'description' => $description,
                'type'        => $type,
                'options'     => $options,
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );

        return ! $insert ? false : $wpdb->insert_id;
    }


    /**
     * Update custom field in DB
     *
     * @param $id
     * @param string $label_name
     * @param string $key
     * @param string $description
     * @param string $type
     * @param string $options
     *
     * @return bool|int
     */
    public static function update_profile_field($id, $label_name, $key, $description, $type, $options)
    {
        global $wpdb;

        return $wpdb->update(
            Base::profile_fields_db_table(),
            [
                'label_name'  => $label_name,
                'field_key'   => $key,
                'description' => $description,
                'type'        => $type,
                'options'     => $options,
            ],
            ['id' => $id],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',

            ],
            ['%d']
        );
    }


    /***
     * Mark a select field as multi selectable.
     *
     * @param string $key
     *
     * @param int $id must have a value.
     *
     * @return bool
     */
    public static function add_multi_selectable($key, $id = 0)
    {
        $old_data = get_option('ppress_cpf_select_multi_selectable', array());
        $new_data = [$key => $id];

        return update_option(
            'ppress_cpf_select_multi_selectable',
            array_merge($old_data, $new_data)
        );
    }

    /***
     * Remove a select field as multi selectable.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function delete_multi_selectable($key)
    {
        $old_data = get_option('ppress_cpf_select_multi_selectable', array());
        unset($old_data[$key]);

        return update_option('ppress_cpf_select_multi_selectable', array_unique($old_data));
    }

    /**
     * get radio buttons options of an added custom field
     */
    public static function get_field_option_values($field_key)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_var(
            $wpdb->prepare("SELECT options FROM $table WHERE field_key = %s", $field_key)
        );
    }

    /**
     * Get radio buttons options of an added custom field
     */
    public static function get_field_label($field_key)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_var($wpdb->prepare("SELECT label_name FROM $table WHERE field_key = %s", $field_key));
    }

    public static function get_field_type($field_key)
    {
        global $wpdb;

        $table = Base::profile_fields_db_table();

        return $wpdb->get_var($wpdb->prepare("SELECT type FROM $table WHERE field_key = %s", $field_key));
    }

    public static function get_contact_info_fields()
    {
        return get_option(PPRESS_CONTACT_INFO_OPTION_NAME, []);
    }

    public static function get_contact_info_field_label($field_key)
    {
        return ppress_var(self::get_contact_info_fields(), $field_key);
    }

    /** One time Passwordless login */
    public static function passwordless_insert_record($user_id, $token, $expiration)
    {
        global $wpdb;

        $table = Base::passwordless_login_db_table();

        // check if a passwordless record already exist for the user
        // if true update the row else add a new row record.
        $id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE user_id = %d", $user_id));

        if (is_null($id)) {

            $prepared_statement = $wpdb->prepare(
                "
		INSERT INTO $table
		( user_id, token, expires )
		VALUES ( %d, %s, %d )
	",
                array(
                    $user_id,
                    $token,
                    $expiration,
                )
            );
        } else {
            $prepared_statement = $wpdb->prepare(
                "
		UPDATE $table
		SET token = %s, expires = %d
		WHERE user_id = %d
	",
                array(
                    $token,
                    $expiration,
                    $user_id,
                )
            );
        }

        return $wpdb->query($prepared_statement);
    }

    /**
     * Delete OTP record for a user.
     *
     * @param int $user_id
     *
     * @return false|int
     */
    public static function passwordless_delete_record($user_id)
    {
        global $wpdb;

        return $wpdb->delete(Base::passwordless_login_db_table(), array('user_id' => $user_id), array('%d'));
    }

    /**
     * Get the passwordless token of a user by ID
     *
     * @param int $user_id ID of user
     *
     * @return null|string
     */
    public static function passwordless_get_user_token($user_id)
    {
        global $wpdb;

        $table = Base::passwordless_login_db_table();

        return $wpdb->get_var($wpdb->prepare("SELECT token FROM $table WHERE user_id = %d", $user_id));
    }

    /**
     * Get the expiration time
     *
     * @param int $user_id
     *
     * @return null|string
     */
    public static function passwordless_get_expiration($user_id)
    {
        global $wpdb;

        $table = Base::passwordless_login_db_table();

        return $wpdb->get_var($wpdb->prepare("SELECT expires FROM $table WHERE user_id = %d", $user_id));
    }
}
