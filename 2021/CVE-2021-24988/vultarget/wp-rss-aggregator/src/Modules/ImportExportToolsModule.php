<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Import" and "Export" tools to WP RSS Aggregator.
 *
 * @since 4.17
 */
class ImportExportToolsModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getFactories()
    {
        return [
            /*
             * Information about the "Export" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/export/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Export', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/export.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * Information about the "Import" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/import/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Import', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/import.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The handler that listens to the export request and creates the export file.
             *
             * @since 4.17
             */
            'wpra/admin/tools/export/handler' => function (ContainerInterface $c) {
                return function () {
                    $export = filter_input(INPUT_POST, 'wpra_export', FILTER_DEFAULT);
                    if (empty($export)) {
                        return;
                    }

                    check_admin_referer('wpra_export_settings', 'wpra_export_settings_nonce');

                    $generalSettings = get_option('wprss_settings_general');
                    $fullSettings = ['wprss_settings_general' => $generalSettings];
                    $fullSettings = apply_filters('wprss_fields_export', $fullSettings);

                    $exportData = [];
                    foreach ($fullSettings as $key => $value) {
                        $exportData[$key] = maybe_unserialize($value);
                    }

                    $blogName = str_replace(' ', '', get_option('blogname'));
                    $fileName = sprintf('%s-%s.json', $blogName, date('m-d-Y'));
                    $charset = get_option('blog_charset');

                    header('Content-Description: File Transfer');
                    header("Content-Type: text/json; charset=$charset");
                    header("Content-Disposition: attachment; filename=$fileName");
                    echo json_encode($exportData);

                    exit;
                };
            },
            /*
             * The handler that listens to import upload requests and imports settings from the uploaded file.
             *
             * @since 4.17
             */
            'wpra/admin/tools/import/handler' => function (ContainerInterface $c) {
                return function () {
                    $export = filter_input(INPUT_POST, 'wpra_import', FILTER_DEFAULT);
                    if (empty($export)) {
                        return;
                    }

                    check_admin_referer('wpra_import_settings', 'wpra_import_settings_nonce');

                    $fileInfo = isset($_FILES['wpra_import_file'])
                        ? $_FILES['wpra_import_file']
                        : ['error' => UPLOAD_ERR_NO_FILE];

                    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
                        switch ($fileInfo['error']) {
                            case UPLOAD_ERR_NO_FILE:
                                $message = __('No file was uploaded. Please select a file.', 'wprss');
                                break;
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                $message = __('Upload file is too large.', 'wprss');
                                break;
                            case UPLOAD_ERR_PARTIAL:
                                $message = __('The file was not fully uploaded.', 'wprss');
                                break;
                            default:
                                $message = __('The file upload failed. Please try again.', 'wprss');
                                break;
                        }

                        wp_die($message, __('Upload error', 'wprss'), ['back_link' => true]);

                        exit;
                    }

                    $importFile = $_FILES['wpra_import_file'];
                    $rawContents = file_get_contents($importFile['tmp_name']);
                    $settings = json_decode($rawContents, true);

                    if ($settings === null) {
                        wprss()->getAdminAjaxNotices()->addNotice('settings_import_failed');
                        exit;
                    }

                    foreach ($settings as $key => $value) {
                        update_option($key, $value);
                    }

                    wprss()->getAdminAjaxNotices()->addNotice('settings_import_success');
                    do_action('wprss_settings_imported');
                };
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getExtensions()
    {
        return [
            /*
             * Registers the "Export" and "Import" tools.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + [
                        'export' => $c->get('wpra/admin/tools/export/info'),
                        'import' => $c->get('wpra/admin/tools/import/info'),
                    ];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function run(ContainerInterface $c)
    {
        // Register the Export and Import handlers
        add_action('admin_init', $c->get('wpra/admin/tools/export/handler'));
        add_action('admin_init', $c->get('wpra/admin/tools/import/handler'));
    }
}
