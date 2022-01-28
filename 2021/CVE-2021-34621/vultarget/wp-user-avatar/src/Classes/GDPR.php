<?php

namespace ProfilePress\Core\Classes;


use ProfilePress\Core\Classes\PROFILEPRESS_sql as PROFILEPRESS_sql;

class GDPR
{
    public function __construct()
    {
        add_filter('wp_privacy_personal_data_exporters', [$this, 'wp_export_data']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'wp_erase_data']);
    }

    public function wp_erase_data($erasers)
    {
        $erasers['profilepress'] = [
            'eraser_friendly_name' => esc_html__('User Extra Information', 'wp-user-avatar'),
            'callback'             => [$this, 'erase_data']
        ];

        return $erasers;
    }

    public function erase_data($email_address)
    {
        $user          = get_user_by('email', $email_address);
        $user_id       = $user->ID;
        $custom_fields = PROFILEPRESS_sql::get_profile_custom_fields();

        $items_removed  = false;
        $items_retained = false;

        if ( ! empty($custom_fields) && is_array($custom_fields)) {

            foreach ($custom_fields as $custom_field) {

                $get_meta_value = get_user_meta($user_id, $custom_field['field_key']);
                if (empty($get_meta_value)) continue;

                $deleted = delete_user_meta($user_id, $custom_field['field_key']);
                if ($deleted) {
                    $items_removed = true;
                } else {
                    $items_retained = true;
                }
            }
        }

        return [
            'items_removed'  => $items_removed,
            'items_retained' => $items_retained,
            'messages'       => [],
            'done'           => true,
        ];
    }

    public function wp_export_data($exporters)
    {
        $exporters[] = array(
            'exporter_friendly_name' => esc_html__('User Extra Information', 'wp-user-avatar'),
            'callback'               => function ($email_address) {
                $user    = get_user_by('email', $email_address);
                $user_id = $user->ID;

                $data_to_export = [];

                $custom_fields    = PROFILEPRESS_sql::get_profile_custom_fields();
                $db_contact_infos = PROFILEPRESS_sql::get_contact_info_fields();

                if ( ! empty($db_contact_infos) || ! empty($custom_fields)) {

                    $lead_data_to_export = [];

                    if ( ! empty($db_contact_infos) && is_array($db_contact_infos)) {

                        foreach ($db_contact_infos as $key => $value) {

                            $usermeta_value = get_user_meta($user_id, $key, true);

                            if ( ! empty($usermeta_value)) {
                                $lead_data_to_export[] = [
                                    'name'  => $value,
                                    'value' => $usermeta_value
                                ];
                            }
                        }
                    }

                    if ( ! empty($custom_fields) && is_array($custom_fields)) {

                        foreach ($custom_fields as $custom_field) {

                            $usermeta_value = get_user_meta($user_id, $custom_field['field_key'], true);

                            if ( ! empty($usermeta_value)) {
                                $lead_data_to_export[] = [
                                    'name'  => $custom_field['label_name'],
                                    'value' => $usermeta_value
                                ];
                            }
                        }
                    }

                    $data_to_export[] = [
                        'group_id'    => 'profilepress',
                        'group_label' => esc_html__('User Extra Information', 'wp-user-avatar'),
                        'item_id'     => "profilepress-{$user_id}",
                        'data'        => $lead_data_to_export
                    ];
                }

                return [
                    'data' => $data_to_export,
                    'done' => true,
                ];
            }
        );

        return $exporters;
    }

    /**
     * @return GDPR
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}