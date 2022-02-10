<?php
namespace NotificationX\Admin;

use NotificationX\Core\Database;
use NotificationX\Core\PostType;
use NotificationX\Core\Rules;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;

class ImportExport{
    use GetInstance;

    public function __construct(){
        add_filter('nx_settings_tab_miscellaneous', [$this, 'settings_tab_help']);
        add_filter('upload_mimes', [$this, 'cc_mime_types']);
        add_filter('nx_settings', [$this, 'save_settings']);
    }

    public function save_settings($settings) {
        $remove_before_save = [
            'export-notification',
            'export-analytics',
            'export-status',
            'export-settings',
            'run_export',
            'import',
            'run_import',
        ];
        foreach ($remove_before_save as $key) {
            if(isset($settings[$key])){
                unset($settings[$key]);
            }
        }
        return $settings;
    }

    public function cc_mime_types($mimes) {
        $mimes['json'] = 'text/plain';
        return $mimes;
    }

    public function settings_tab_help($tabs) {

        $tabs['fields']['import-section'] = array(
            'name'     => 'import-section',
            'type'     => "section",
            'label'    => __('Import/Export', 'notificationx'),
            'priority' => 30,
            'fields'   => array(
                'export-notification' => [
                    'name'     => "export-notification",
                    'type'     => 'checkbox',
                    'label'    => __('Export Notifications', 'notificationx'),
                    'default'  => 0,
                    'priority' => 10,
                ],
                'export-analytics' => [
                    'name'     => "export-analytics",
                    'type'     => 'checkbox',
                    'label'    => __('Analytics', 'notificationx'),
                    'default'  => 0,
                    'priority' => 15,
                    'rules'    => Rules::is( 'export-notification', true ),
                    // 'description' => __('Click, if you want to disable powered by text from notification', 'notificationx'),
                ],
                'export-status' => array(
                    'name'     => 'export-status',
                    'type'     => 'select',
                    'label'    => __('Status', 'notificationx'),
                    'priority' => 20,
                    'rules'    => Rules::is( 'export-notification', true ),
                    'default'  => ['all'],
                    'options'  => GlobalFields::get_instance()->normalize_fields([
                        'all' => 'ALL',
                        'enabled' => 'Enabled',
                        'disabled' => 'Disabled',
                    ]),
                ),
                'export-settings' => [
                    'name'     => "export-settings",
                    'type'     => 'checkbox',
                    'label'    => __('Export Settings', 'notificationx'),
                    'default'  => 0,
                    'priority' => 30,
                ],
                'run_export' => array(
                    'name'     => 'run_export',
                    // 'label'    => __('Import', 'notificationx'),
                    'text'    => [
                        'normal'  => __('Export', 'notificationx'),
                        'saved'   => __('Export', 'notificationx'),
                        'loading' => __('Exporting...', 'notificationx'),
                    ],
                    'type'     => 'button',
                    'priority' => 40,
                    // 'rules'    => Rules::is( 'import', null, true ),
                    'rules'    => Rules::logicalRule([
                        Rules::is( 'export-notification', true ),
                        Rules::is( 'export-settings', true ),
                    ], 'or'),
                    'ajax'     => [
                        'on'   => 'click',
                        'api'  => '/notificationx/v1/export',
                        'data' => [
                            'export-notification' => '@export-notification',
                            'export-settings'     => '@export-settings',
                            'export-analytics'    => '@export-analytics',
                            'export-status'       => '@export-status',
                        ],
                        'swal' => [
                            'text'      => __('Export completed successfully.', 'notificationx'),
                            'icon'      => 'success',
                            'autoClose' => 2000
                        ],
                    ],
                ),

                'import' => array(
                    'name'         => 'import',
                    'type'         => 'jsonuploader',
                    'label'        => __('Import', 'notificationx'),
                    'reset'        => __('Change', 'notificationx'),
                    'priority'     => 60,
                    'notImage'     => true,
                ),
                'run_import' => array(
                    'name'     => 'run_import',
                    // 'label'    => __('Import', 'notificationx'),
                    'text'    => [
                        'normal'  => __('Import', 'notificationx'),
                        'saved'   => __('Import', 'notificationx'),
                        'loading' => __('Importing...', 'notificationx'),
                    ],
                    'type'     => 'button',
                    'priority' => 70,
                    'rules'    => Rules::is( 'import', null, true ),
                    'ajax'     => [
                        'on'   => 'click',
                        'api'  => '/notificationx/v1/import',
                        'data' => [
                            'import'   => '@import',
                        ],
                        'swal' => [
                            'text'      => __('Import completed successfully.', 'notificationx'),
                            'icon'      => 'success',
                            'autoClose' => 2000
                        ],
                    ],
                ),
            ),
        );

        return $tabs;
    }

    public function import($request){
        @set_time_limit(0);
        $params = $request->get_params();
        $status = 'error';
        if(!empty($params['import'])){
            try {
                $data = json_decode($params['import'], true);

                if(!empty($data['settings'])){
                    Settings::get_instance()->set('settings', $data['settings']);
                    $status = 'success';
                }

                if(!empty($data['notifications'])){
                    $analytics = [];
                    if(!empty($data['analytics'])){
                        $analytics = $this->group_stats_by_nx_id($data['analytics']);
                    }
                    foreach ($data['notifications'] as $key => $post) {
                        $nx_id = $post['nx_id'];
                        unset($post['nx_id']);
                        unset($post['id']);

                        if($post['source'] == 'press_bar' && !empty($post['elementor_id'])){
                            $elementor_data = $data['elementor'][$post['elementor_id']];
                            unset($elementor_data['post']['ID']);

                            $el_id = wp_insert_post($elementor_data['post']);
                            foreach ($elementor_data['meta'] as $key => $value) {
                                if($key == '_elementor_css') continue;
                                foreach ($value as $s_value) {
                                    if($key == '_elementor_data'){
                                        $s_value = wp_slash( wp_json_encode(json_decode($s_value)));
                                    }
                                    add_post_meta($el_id, $key, $s_value);
                                }
                            }
                            $post['elementor_id'] = $el_id;


                        }


                        $notification = PostType::get_instance()->save_post($post); //, ['no_hooks' => true]
                        $nx_id_new    = $notification['nx_id'];

                        if(!empty($analytics[$nx_id])){
                            foreach ($analytics[$nx_id] as $key => $value) {
                                $value['nx_id'] = $nx_id_new;
                                $analytics[$nx_id][$key] = $value;
                            }
                            // Database::get_instance()->insert_posts(Database::$table_stats, array_values($analytics[$nx_id]));
                        }
                    }
                    if(!empty($analytics)){
                        $_analytics = [];
                        foreach ($analytics as $key => $value) {
                            $_analytics = array_merge($_analytics, $value);
                        }
                        Database::get_instance()->insert_posts(Database::$table_stats, array_values($_analytics));
                    }

                    $status = 'success';
                }

            } catch (\Throwable $th) {
                //throw $th;
                $status = 'error';
            }
        }

        return [
            'status'  => $status,
            'data'    => [
                'context' => [
                    'import' => null,
                ]
            ]
        ];
    }

    public function export($request){
        @set_time_limit(0);
        $params = $request->get_params();
        $export = [];
        if(!empty($params['export-settings'])){
            $file_name = 'nx-settings-export.json';
            $export['settings'] = Settings::get_instance()->get('settings');
        }
        if(!empty($params['export-notification'])){
            $where = [];
            $file_name = 'nx-notification-export.json';
            if(!empty($params['export-status']) && ($params['export-status'] == 'enabled' || $params['export-status'] == 'disabled')){
                $where = [
                    'enabled' => $params['export-status'] == 'enabled',
                ];
            }
            $export['notifications'] = PostType::get_instance()->get_posts($where);
            if(!empty($params['export-analytics']) && !empty($export['notifications'])){
                $nx_ids = array_column($export['notifications'], 'nx_id');
                $export['analytics'] = Database::get_instance()->get_posts(Database::$table_stats, '*', [
                    'nx_id' => [
                        'IN',
                        '(' . implode(', ', $nx_ids) . ')'
                    ]
                ]);
            }

            if(!empty($export['notifications'])){
                foreach ($export['notifications'] as $key => $post) {
                    if($post['source'] == 'press_bar' && !empty($post['elementor_id'])){
                        $export['elementor'][$post['elementor_id']]['post'] = get_post($post['elementor_id']);
                        $meta = get_post_meta($post['elementor_id']);
                        foreach ($meta as $key => $value) {
                            $export['elementor'][$post['elementor_id']]['meta'][$key] = array_map('maybe_unserialize', $value);
                        }
                    }
                }
            }
        }
        if(!empty($params['export-settings']) && !empty($params['export-notification'])){
            $file_name = 'nx-export.json';
        }
        return [
            'success' => true,
            'data'    => [
                'filename'  => $file_name,
                'download'  => $export,
                'context' => [
                    'export-notification' => false,
                    'export-settings'     => false,
                    'export-analytics'    => false,
                    'export-status'       => 'all',
                ]
            ]
        ];
    }

    public function group_stats_by_nx_id($stats){
        $new_stats = [];
        if(!empty($stats)){
            foreach ($stats as $key => $value) {
                unset($value['stat_id']);
                $new_stats[$value['nx_id']][] = $value;
            }
        }

        return $new_stats;
    }
}
