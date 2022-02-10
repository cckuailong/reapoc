<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "System Info" tool to WP RSS Aggregator.
 *
 * @since 4.17
 */
class SysInfoToolModule implements ModuleInterface
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
             * Information about the "System Info" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/sys_info/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('System Info', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/sys_info.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The handler that listens to the system info download request.
             *
             * @since 4.17
             */
            'wpra/admin/tools/sys_info/dl_handler' => function (ContainerInterface $c) {
                return function () {
                    $dlSysInfo = filter_input(INPUT_POST, 'wpra_dl_sysinfo', FILTER_DEFAULT);
                    if (empty($dlSysInfo)) {
                        return;
                    }

                    // Check nonce
                    check_admin_referer('wpra_dl_sys_info', 'wpra_dl_sys_info_nonce');

                    nocache_headers();
                    header("Content-type: text/plain");
                    header('Content-Disposition: attachment; filename="wprss-system-info.txt"');
                    echo wp_strip_all_tags(wpra_get_sys_info());
                    exit;
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
             * Registers the "System Info" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['sys_info' => $c->get('wpra/admin/tools/sys_info/info')];
            },
            /*
             * Adds the system information to the render context on the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/context' => function (ContainerInterface $c, $ctx) {
                return $ctx + ['sys_info' => wpra_get_sys_info()];
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
        // Register the system info download handler
        add_action('admin_init', $c->get('wpra/admin/tools/sys_info/dl_handler'));
    }
}
